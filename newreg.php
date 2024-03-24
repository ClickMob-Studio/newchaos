<?php
session_start();
error_reporting(0);
include 'dbcon.php';
ini_set('session.save_path', '/home/yobcity/tmp/sessions');
ini_set('session.gc_probability', 1);
function badmailhost($str) {
    return filter_var($str, FILTER_VALIDATE_EMAIL);
}
if (isset($_POST['username'])) {
    $username = strip_tags($_POST['username']);
    $username = addslashes($username);
    $loginname = strip_tags($_POST['username']);
    $loginname = addslashes($loginname);
    $signuptime = time();
    $password = $_POST['password'];
    $password2 = $_POST['password_confirm'];
    $email = strip_tags($_POST['email']);
    $email22 = strip_tags($_POST['email']);
    $email = addslashes($email);
    $referer = addslashes($referer);
    $checkuser = mysql_query("SELECT * FROM `grpgusers` WHERE `loginame`='$username'");
    if ($_POST['gender'] != 'male' && $_POST['gender'] != 'female') {
        $_POST['gender'] = 'male';
    }
    $gender = $_POST["gender"];
    $activation = md5(uniqid(rand(), true));
    $username_exist = mysql_num_rows($checkuser);
    if ($username_exist > 0) {
        $_SESSION['reg_failmessage'] .= 'Username is already taken <br />';
        header('Location: home.php');
    }
    if (strlen($username) < 1 or strlen($username) > 20) {
        $_SESSION['reg_failmessage'] .= 'Username needs to be between 1-20 characters <br />';
        header('Location: index.php');
    }
    if (strlen($password) < 6 or strlen($username) > 20) {
        $_SESSION['reg_failmessage'] .= 'Password needs to be between 6-20 characters <br />';
        header('Location: index.php');
    }
    if ($password != $password2) {
        $_SESSION['reg_failmessage'] .= 'Passwords do not match <br />';
        header('Location: index.php');
    }
    if (!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email)) {
        $_SESSION['reg_failmessage'] .= 'Invalid email <br />';
        header('Location: index.php');
    }
    $checkemail = mysql_query("SELECT * FROM `grpgusers` WHERE `email` = '$email'");
    $email_exist = mysql_num_rows($checkemail);
    if ($email_exist > 0) {
        $_SESSION['reg_failmessage'] .= 'Email is already in use <br />';
        header('Location: index.php');
    }
    if (!badmailhost($email)) {
        $_SESSION['reg_failmessage'] .= 'Invalid email<br />';
        header('Location: index.php');
    }
    if (!isset($_SESSION['reg_failmessage'])) {
        $result = mysql_query("INSERT INTO `grpgusers` (`signupip`, `username`, `password`, `email`, `signuptime`, `loginame`, `gender`, `activate`) VALUES ('" . $_SERVER['REMOTE_ADDR'] . "', '" . $username . "', '" . sha1(mysql_real_escape_string($password)) . "', '" . $email . "', '" . $signuptime . "', '" . $loginname . "', '" . $gender . "', '" . $activation . "')");
        $newid = mysql_insert_id();
        $result = mysql_query("UPDATE `grpgusers` SET `activate` = '0' WHERE `activate`='" . $activation . "'");
        $_SESSION['reg_failmessage'] = 'Registered Successfully<br />';
        $_SESSION['id'] = $newid;
        echo"<meta http-equiv='refresh' content='0;url=index.php'/>";
        $msgtext = "
[center][img]https://i.imgsafe.org/a66719e.png[/img]
Welcome to [b][color=cyan]Yob City[/color][/b]. 
This is a Mafia Text Based Game, made by the players for the players, with the aim being, to provide you with an unforgettable experience. 
You can be whatever you want and your goals are determined by you. Be the best mobster around, or be the richest, or simply have a laugh with your friends. This game is for all!
Below you will find the components to the guide, which will be full of information and relevant links. If you find information missing or need further assistance? Simply message a staff member with your query, they will get back to you ASAP. 
You can find the staff list [url=http://yobcity.com/viewstaff.php][size=4][b][color=cyan]Here[/color][/b][/size][/url], and online staff will be located at the bottom of the [b][color=cyan]Navigation Panel[/color][/b], found on the left hand side of the game.
Remember that the best way to learn the game is to play it.
Look around, click things and read stuff and talk to other players. 
I assure you that once you do? It will not be long before you are kicking ass, or spanking mine :haha:.
[url=http://yobcity.com/profiles.php?id=496][size=3][b][color=purple][496] Dark Angel[/color][/b][/size][/url]
[/center]
[img]https://i.imgsafe.org/e2a70e4.png[/img]
[center]These are the game dailies. They refresh at rollover so you can do them again. These are simply actions you can complete to make free cash, points, exp and items.
If you want to keep going as a mobster and you want to compete? This is a must, it is free stuff, so please remember: dalies dalies dalies.
Please remember that some of these you can do multiple times, and not just once. 
If the dalies name has a [b][color=cyan]*[/color][/b] next to it? you can do it more than once.
The list of Dailies on Yob City is below.
[size=6][b][color=red]1.[/color][/b][/size] [url=http://yobcity.com/vote.php][b][size=6]Vote for Points[/size][/b][/url]
[size=6][b][color=red]2.[/color][/b][/size] [url=http://yobcity.com/downtown.php][b][size=6]Search Downtown[/size][/b][/url]
[size=6][b][color=red]3.[/color][/b][/size] [url=http://yobcity.com/prayer.php][b][size=6]Pray for Points, Exp or Cash[/size][/b][/url]
[size=6][b][color=red]4.[/color][/b][/size] [url=http://yobcity.com/thedoors.php][b][size=6]The Mystery Doors[/size][/b][/url][size=6][b][color=cyan]*[/color][/b][/size] 
[size=6][b][color=red]5.[/color][/b][/size] [url=http://yobcity.com/roulettespin.php][b][size=6]Russian Roulette[/size][/b][/url][size=6][b][color=cyan]*[/color][/b][/size] 
[size=6][b][color=red]6.[/color][/b][/size] [url=http://yobcity.com/numbergame.php][b][size=6]Number Game[/size][/b][/url][size=6][b][color=cyan]*[/color][/b][/size] 
[size=6][b][color=red]7.[/color][/b][/size] [url=http://yobcity.com/lucky_boxes.php][b][size=6]Lucky Boxes[/size][/b][/url]
[size=6][b][color=red]8.[/color][/b][/size] [url=http://yobcity.com/FruitMachine.php][b][size=6]Fruit Machine[/size][/b][/url][size=6][b][color=cyan]*[/color][/b][/size][/center]
[img]https://i.imgsafe.org/8db8143.png[/img]
[center]Missions is a simple concept, you complete tasks and you receive prizes in return. This is vital, because this is how you can get more points and cash, simply by being active and doing what you need to, to progress in this game.
People that mission get ahead, you can even keep up with big spenders...Time is everything people!
You will find a link to the Missions page on the [b][color=yellow]Navigation Panel[/color][/b] on the left hand side, in [b][color=orange]orange[/color][/b]. If you are having trouble? 
Please click [url=http://yobcity.com/missions.php][size=4][b][color=lime]HERE[/color][/b][/size][/url] now.[/center]
[img]https://i.imgsafe.org/49094da.png[/img]
[center]This is exactly what is sounds like. You will get points for being active, these points can then be traded in for set prizes. 
You will find the link that takes you to this page, in your user details in the header. 
Click the button called [USE] next to where it says Activity.
Otherwise, to see what i mean please click [url=http://yobcity.com/spendactivity.php][size=4][b][color=#FF4400]HERE[/color][/b][/size][/url] now.[/center]
[img]https://i.imgsafe.org/e18f774.png[/img]
[center]This section will explain what each stat means and how they work together in combat.
[b][color=#CC0000]Strength:[/color][/b] You require strength to do damage to your opponent, the strength attribute is what decides how much damage you do to.
[b][color=cyan]Speed:[/color][/b] The speed attribute decides who attacks first, if you have a higher speed than your opponent you will hit him or her first. However speed is useless without strength, you need a balance between the two.
[b][color=green]Defense:[/color][/b] This attribute is there to allow you to defend yourself against attacks from other members, this attributes reduces the amount of damage you take in a battle.
There are three kind of players on mafia games: 
[size=4][b][color=cyan]Speed players[/color] | [color=green]Defense players[/color] | [color=#FF4400]Balanced players[/color][/b][/size]
[b][color=cyan]Speed Players:[/color][/b] Speed based players are people which like training the speed attribute, this is the cheapest form of training. To be a speed based player you need to make sure your speed is high, this will allow you to hit first. However even though this is the case speed based players must make sure there strength is equally as high, even though you hit first you need the damage power to beat your opponent.
Therefore if your speed is higher than your opponents speed and your strength is higher than their defense you will win, but if you strength is not greater than their defense you will not win. 
Remember to be a good speed based player you need to have a good balance between speed and strength.
[b][color=green]Defense Players:[/color][/b] Defense based training is a more expensive form of training compared to speed based. This is because to have a great defense account you require to do a little bit more. Defense players should train there defense as much as possible however this is not the only component, if you want a good defense whore you need to make sure your level is HIGH, increasing your level also increases your Health (HP) which will then allow you to take more damage. Make sure your defense is higher than your opponent�s strength to cancel the damage out. This would be enough to have a decent defense whore. 
If you want a better defense whore, train your strength a little, enough to make sure that it is higher than any speed based accounts defense. This will insure you win every time, you will not get the first hit however your defense will cancel their attack and then your attack will wipe them out with no problem.
[b][color=#FF4400]Balanced Players:[/color][/b] A balanced account is the most expensive but the most effective. A balanced account should have a decent level, a very high strength, a good speed and a high defense.
A balanced account is able to hit first some of the time and defend most of the time but always hit with great damage.
This type of account might be the best but it costs the most and takes a lot of time to develop.
[b][color=red]*REMEMBER*[/color][/b] that equips (Items) change your stats, depending on the % of the item will depend on how much it adds on to your specific stats. So be prepared it helps in combats.
Items can be bought in the city stores, located on the explore page, under the [b][color=red]Market Place[/color][/b] header. Items cost cash so please keep saving. Different items can be found in different cities.[/center]
[img]https://i.imgsafe.org/f1d1d7c.png[/img]
[center]Levelling is an important part to the game and players who do level with compensation.
Levelling unlocks aspects in the game, but also helps you gain money, more NRG, Nerve and HP.
The higher your level the better in the long run.
[b][color=yellow]1) [/color][/b] Levels unlock cities in the game. Cities have level restrictions, meaning unless you are not that level you cannot enter, this allows you to hide away if you need to from bullies in the game, so level. More levels more cities.
[b][color=yellow]2) [/color][/b] Every level you gain, your NRG, Nerve and HP bar grows. More health means better chance in winning a fight, more nerve means more crimes you can do which means more money you can get from crimes, which you can put forward to improving your account.
[b][color=yellow]3)[/color][/b] Each time you level means more you get per train in the gym. This is good because it allows you to gain more for your points. You gain more because each time you level your NRG bar grows, increasing the amount you use and gain per train.
The way you can level is simple.
To gain levels you need to gain exp, this exp can be seen at your home page.
Ways in which you can gain levels is simple.
[b][color=yellow]1)[/color][/b]  Attack people with a higher level than yourself. You gain 100 exp per level difference between you and your opponent. However remember if they are the same level as you, you still gain [b][color=yellow]100 exp[/color][/b], therefore if they are 26 levels higher than yourself, you will gain [b][color=yellow]2,700 exp[/color][/b].
[b][color=red][u]REMEMBER[/u]: You can only make a max of 12,000exp per attack.[/color][/b]
It is also good to remember that too attack you need NRG (25% min), but also you need to have better stats than the other person. Read above to understand how stats work. 
When you kill someone they go to Hospital for 5mins and lose their HP. 
Players can buy out of hospital with certs, or cash. 
The cash option link will be shown on the [url=http://yobcity.com/hospital.php][size=3][b][color=red]Hospital Page[/color][/size][/b][/url], at the top, in [color=red]Red[/color]. Cash will come out of your bank or hand.
[b][color=yellow]2)[/color][/b]  Another way in gaining levels is by doing crimes. Crimes give you exp and also money. Depending on the nerve required to do the crime will depend on how much exp and money you gain from it.
[b][color=red][u]Example[/u] | Nerve 22 Crime[/b]:[/color] 
You successfully managed to Sell some weed
You received [b][color=yellow]608 exp[/color][/b] and [b][color=green]$1415[/color].[/b][/center]
[img]https://i.imgsafe.org/477b5de.png[/img]
[center]When training your [u]Stats[/u] you must remember specific key points,
that will effect the amount you gain per train and other aspects.
Click [url=http://yobcity.com/gym.php][size=4][b][color=purple]HERE[/color][/size][/b][/url] for the GYM.
[b][color=#FF0044][u]Remember[/u] NRG refills are always 10 points.[/color][/b]
[color=#FF0099][b]Level:[/b][/color] Level plays a big role in training. The higher your level means the higher your NRG / NRG bar this will improve the amount you gain per train in the gym dramatically. Remember level as much as you can because every time you do is every time the amount you gain per train increases, which will allow you to gain more from your credits.
You will find the [b][color=purple]Levelling Guide[/color][/b] above.
[color=#FF0099][b]House:[/b][/color] Depending on your house depends on how much awake you have, the more awake the better you gain per train, this makes a big impact on how much you gain per train. Houses cost money, the better the house the more it costs, this is a big part in training, without a good house your training will be pointless and a waste of credits, so for more for your money get a good house.
You will find the Housing listing on the [b][u]Explore Page[/b][/u], under the title [b][color=#FF0044]Real Estate[/color][/b], under the header [b][color=#FF0044]City Hall[/color][/b] .
Otherwise, Click [url=http://yobcity.com/house.php][size=4][b][color=purple]HERE[/color][/size][/b][/url] to see the listings.
To train in the gym you require NRG. 
Each time you train make sure to [u]ALWAYS[/u] refill your awake and energy to get the most you can get for your train.
Lucky for you it is made easy here, follow instructions on the [url=http://yobcity.com/gym.php][size=4][b][color=purple]GYM Page[/color][/size][/b][/url].[/center]
[img]https://i.imgsafe.org/e6c5f90.png[/img]
[center]There has been a new feature added to the game.
You can now rob another yobsters house, simply go to their profile to do this.
It will cost you: $10,000 and 100% energy to commit a robbery.
You will gain:
- All the money the player and their spouse, if any, has on hand.
- The player will lose a random % of awake, between 15-45%.
- They will also be sent to hospital for 10 minutes.
There is a item called Security Systems which will help protect you. 
It does the following:
- Allows you to know who robbed you. 
- Gives a 25% chance of a failed robbery against you.
Without a Security System:
- You will never know who robbed you.
- No extra protections
[b][color=red]You can buy a security system on the [url=http://yobcity.com/rmstore.php][size=3]Upgrade Store[/size][/url].[/color][/b]
Chances that you will succeed in robbing another yobster:
- 5% chance by default
- otherwise you will have to rely on your speed.
- Your modded Speed (with shoes) vs their normal Speed (without shoes)[/center]
[img]https://i.imgsafe.org/76f131f.png[/img]
[center]Marriage is more important on here than on other games.
The way marriage is set up is, if you go to another players profile you will find a function where you can propose.
Unlike other games where this simply means you are married, a cosmetic add. 
Here being married means you share the same house. So if your partner has a better house than you? you will adopt and share that better house.
If by some chance you have the same house? you will gain an addition 5% on awake.
When you divorce the player, both parties return to the houses they had before.
So if you are a gold digger? This is the game for you :haha:[/center]
Staff.";
        $to = $newid;
        $from = 1;
        $bomb = 0;
        $parent = ($_POST['parent'] != 0) ? $_POST['parent'] : floor(time() / (uniqid(rand(1, 20), true) + uniqid(rand(1, 200))) - rand(100, 1000));
        $timesent = time();
        $subject = "Welcome to the game! - <font color=red>Must Read</font>";
        $msgtext = strip_tags($msgtext);
        $msgtext = nl2br($msgtext);
        $msgtext = addslashes($msgtext);
        $result = mysql_query("INSERT INTO `pms` (`id`,`to`, `from`, `timesent`, `subject`, `msgtext`, `bomb`)" . "VALUES ('','$to', '$from', '$timesent', '" . $subject . "', '" . $msgtext . "', '" . $bomb . "')");
        if ($_POST['referer'] != "")
            $result = mysql_query("INSERT INTO referrals (`when`, referrer, referred) VALUES ($signuptime, {$_POST['referer']}, $newid)");
        Send_Event($user_class->id, "Welcome To Yob City![br]To get you started we are giving you:[br]&bull;&nbsp;[color=lime]3 Donator Days[br]&bull;&nbsp;$10,000 Cash[br]&bull;&nbsp;50 Points[br]Before you get started please read the [b][a href='gamerules.php']Game Rules[/a][/b] ", $user_class->id);
        //END AUTO MAIL
        die();
    }
}
?>