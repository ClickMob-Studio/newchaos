<?php
include 'header.php';
if(isset($_GET['jailbreak'])){
    $jailbreak = $_GET['jailbreak'];
}else{
    $jailbreak = '';
}

if ($jailbreak != ""){
    if(empty($_GET['token'])){
        echo Message("There has been a issue");
    }
    if($_GET['token'] != $_SESSION['token']){
        $_SESSION['message'] = "F5 use on jail is not allowed";
        header('Location: jail.php');
    }else{
        unset($_SESSION['token']);
    }

    $jailed_person = new User($jailbreak);
    if ($jailed_person->formattedname == ""){
        echo Message("That person does not exist.");
        include 'footer.php';
        die();
    }
    if ($jailed_person->id === $user_class->id) {
        echo Message("You can't break yourself out of jail.");
        include 'footer.php';
        die();
    }
    if ($jailed_person->jail == "0"){
        echo Message("That person is not in jail.");
        include 'footer.php';
        die();
    }
    $chance = rand(1,(100 * $crime - ($user_class->speed / 25)));
    //$money = 785;
    $nerve = 10;
    $exp = 2500;
    if ($user_class->nerve >= $nerve) {
        if($chance <= 75) {
            $_SESSION['message'] = "Success! You receive ".$exp." exp and 3 points";
            $exp = $exp + $user_class->exp;
            $crimesucceeded = 1 + $user_class->crimesucceeded;
            $crimemoney = $money + $user_class->crimemoney;
            $money = $money + $user_class->money;
            $nerve = $user_class->nerve - $nerve;
            if ($user_class->gang != 0) {
                mysql_query("UPDATE gangs SET dailyBusts = dailyBusts + 1 WHERE id = ".$user_class->gang);
            }
            mysql_query("UPDATE grpgusers SET `both` = `both` + 1, `epoints` = `epoints` + `eventbusts`, `bustcomp` = `bustcomp` + 1, exp =  ".$exp.", busts = busts + 1, points = points + 3, nerve = nerve - ".$nerve." WHERE id = ".$user_class->id);
            mission('b');
            newmissions('busts');
            gangContest(array(
                'busts' => 1,
                'exp' => $exp
            ));
            $toadd = array('botd' => 1);
            ofthes($user_class->id, $toadd);
            bloodbath('busts', $user_class->id);
            $result = mysql_query("UPDATE `grpgusers` SET `jail` = '0' WHERE `id`='".$jailed_person->id."'");
            //send even to that person
            Send_Event($jailed_person->id, "You have been busted out of Jail by [-_USERID_-].", $user_class->id);
        }elseif ($chance >= 150) {
            $_SESSION['message'] = "You were caught. You were hauled off to jail for 10  minutes.";
            $crimefailed = 1 + $user_class->crimefailed;
            $jail = 10800;
            $nerve = $user_class->nerve - $nerve;
            $result = mysql_query("UPDATE grpgusers SET crimefailed = crimefailed + 1, caught = caught + 1, jail = 600, nerve = nerve - ".$nerve." WHERE id =".$user_class->id);
        }else{
            $_SESSION['message'] ="You failed.";
            $crimefailed = 1 + $user_class->crimefailed;
            $nerve = $user_class->nerve - $nerve;
            $result = mysql_query("UPDATE grpgusers SET crimefailed = crimefailed + 1, nerve = nerve - ".$nerve." WHERE id = '".$_SESSION['id']."'");
        }
    } else {
        echo Message("You don't have enough nerve for that crime.");
        include 'footer.php';
        die();
    }

}

if(isset($_GET['action']) && $_GET['action'] == 'bail'){
    if($user_class->jail < 1){
        $_SESSION['message'] = 'You are currently not in jail';
    }else{
        $cost = ceil($user_class->jail / 60);
        if($user_class->points < $cost){
            $_SESSION['message'] = 'You do not have enough points';
        }else{
            $_SESSION['message'] = 'You have bailed you self out of jail for '.$cost.' points';
            mysql_query("UPDATE grpgusers SET jail = 0, points = points - ".$cost." WHERE id = ".$user_class->id);
        }
    }
}

$cost = ceil($user_class->jail / 60);
?>
    <h1>Jail</h1>
<?php
if(isset($_SESSION['message'])){
    echo Message($_SESSION['message']);
    unset($_SESSION['message']);
}
if($user_class->jail > 0){
    echo "<span style='color:red'>You are currently in jail click<a href='jail.php?action=bail' style='color:white'>here</a> to bail your self out this will cost you ".$cost." points</span>";
}
?>
    <tr><td class="contentcontent">
            <table id='jail-table' width='100%' cellpadding='4' cellspacing='0'>
                <tr>

                    <td>Mobster</td>

                    <td>Time Left</td>

                    <td>Actions</td>

                </tr>
                <?php
                $ignore = array($user_class->id);
                $ignore = implode(',', $ignore);

                $result = mysql_query("SELECT * FROM `grpgusers` ORDER BY `jail` DESC");
                function generateRandomString($length = 10) {
                    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                    $randomString = '';
                    for ($i = 0; $i < $length; $i++) {
                        $index = mt_rand(0, strlen($characters) - 1);
                        $randomString .= $characters[$index];
                    }
                    return $randomString;
                }
                $token = generateRandomString(10);
                $_SESSION['token'] = $token;
                if(mysql_num_rows($result)){
                    while($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
                        $secondsago = time()-$line['lastactive'];
                        $user_jail = new User($line['id']);
                        if (floor($user_jail->jail / 60) != 1) {
                            $plural = "s";
                        }

                        if($user_jail->jail != 0){
                            echo "<tr class='jail-cell-row'><td>".$user_jail->formattedname."</td><td>".floor($user_jail->jail / 60)." minute".$plural."</td><td><a href = '?jailbreak=".$user_jail->id."&token=".$token."'>Break Out</a></td></tr>";
                        }
                    }
                }else{
                    echo "<tr class='jail-cell-row'><td colspan='3'>There are currently no jailbreaks</td></tr>";
                }
                ?>
            </table>
        </td></tr>

<script type="text/javascript">
    jailInterval = setInterval(() => {
        $.get("ajax_jail_new.php?action=fetch_users", {}, (jailers) => {
            console.log('interval');
            $('.jail-cell-row').remove();

            console.log(jailers);
            if (jailers != false) {
                jailers.forEach((data, index) => {

                    $('#jail-table tr:last').after('' +
                        '<tr class="jail-cell-row">' +
                            '<td>' + data.username + '</td>' +
                            '<td>' + data.time + '</td>' +
                            '<td><a href="?jailbreak=' + data.id + '&token= <?php echo $token ?>" data-user-id="' + data.id + '" class="break-out-link">Break Out</a></td>' +
                        '</tr>'
                    );
                })
            }
        }, "json")
    }, 2000);
</script>
<?
include 'footer.php';
?>