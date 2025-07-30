<?php
include 'header.php';
?>
<div class='box_top'>Pray</div>
<div class='box_middle'>
    <div class='pad'>
        <?php
        if ($user_class->prayer == 0)
            error("You have already prayed today.");
        $whereone = '<img src="images/prayer.png"></tr></td><tr><td class="contentcontent">';
        if ($user_class->jail > 0)
            error("You can't access this page if you are in jail.");
        if ($user_class->hospital > 0)
            error("You can't access this page if you are in the hospital.");
        if ($user_class->prayer < 1)
            error("<center>You have already prayed recently. Come back tomorrow [<a href='prayer.php'>Back</a>].</center>");
        function error($msg)
        {
            echo Message($msg);
            include 'footer.php';
            die();
        }
        $newp = $user_class->prayer - 1;
        $qweq = "You pray at the altar and ";
        $asda = "</tr></td><tr><td class='contentcontent'>";
        if (isset($_POST['prayer']) && $_POST['prayer'] == "1") {
            $where = rand(1, 3);
            $amount = rand(5000, 25000);
            $reward = " exp";
            $newexp = $user_class->exp + $amount;
            perform_query("UPDATE grpgusers SET exp = ?, prayer = ? WHERE id = ?", [$newexp, $newp, $user_class->id]);
            $user_class = new User($_SESSION['id']);
        }
        if (isset($_POST['prayer']) && $_POST['prayer'] == "2") {
            $where = rand(11, 13);
            $amount = rand(100000, 2500000);
            $reward = " dollars";
            $newmoney = $user_class->money + $amount;
            perform_query("UPDATE grpgusers SET money = ?, prayer = ? WHERE id = ?", [$newmoney, $newp, $user_class->id]);
            $user_class = new User($_SESSION['id']);
        }
        if (isset($_POST['prayer']) && $_POST['prayer'] == "3") {
            $where = rand(21, 23);
            $amount = rand(1000, 3000);
            $reward = " points";
            $newiq = $user_class->points + $amount;
            perform_query("UPDATE grpgusers SET points = ?, prayer = ? WHERE id = ?", [$newiq, $newp, $user_class->id]);
            $user_class = new User($_SESSION['id']);
        }

        if (isset($where)) {

            if ($where == "1")
                $whereone = "you smacked a random man's head. You gained ";
            if ($where == "2")
                $whereone = "suddenly, you started dancing randomly, your dancing was much better than usual,<br>it got the crowd going! You gained ";
            if ($where == "3")
                $whereone = "you went outside, a bird almost took a crap on your head,<br>but your dodging skills were awesome and dodged it, and gained ";
            if ($where == "11")
                $whereone = "you found a dead man lying on the floor,<br>you open the wallet from his left coat pocket. You found $";
            if ($where == "12")
                $whereone = "you saw Bill Gates driving, he crashed into some windows.<br>You found on the back of his car $";
            if ($where == "13")
                $whereone = "a big wad of cash landed in your head, you recieved $";
            if ($where == "21")
                $whereone = "an mysterious old man handed you a strange book.<br>It printed 'The Book of Knowledge'. You gained ";
            if ($where == "22")
                $whereone = "you saw your former teacher walk by.<br>You decided to have a chat with the teacher. You gained ";
            if ($where == "23")
                $whereone = "you found a dictionary on the floor.<br>You decided to read 5 random pages from it. You gained ";
        }
        ?>

        <div class="contenthead floaty">
            <center>
                <?php
                if (isset($_POST['prayer']) && $_POST['prayer'] > "0") {
                    echo $qweq;
                    echo $whereone;
                    echo $amount;
                    echo $reward;
                    echo $asda;
                } else
                    echo $whereone;
                ?>
                <br>Here you can pray once a day, each prayer affects on what you can get.
            </center>
            </tr>
            </td>
            <tr>
                <td class="contentcontent">
                    <form method='post'>
                        <table width='100%'>
                            <tr>
                                <td class='contenthead' colspan='3'>Select a prayer.</td>
                            </tr>
                            <tr>
                                <td class='contentcontent' width='33%' align='center'><input type='radio' name='prayer'
                                        value='1'><b>EXP</b></td>
                                <td class='contentcontent' align='center'><input type='radio' name='prayer'
                                        value='2'><b>Money</b></td>
                                <td class='contentcontent' width='33%' align='center'><input type='radio' name='prayer'
                                        value='3' /><b>Points</b></td>
                            </tr>
                            <tr>
                                <td class='contentcontent' align='center'>O Lord, in this time of need, strengthen me.
                                    You are my strength and my shield; You are my refuge and strength, a very present
                                    help in trouble. I know, Father, that Your eyes go to and fro throughout the earth
                                    to strengthen those whose hearts long for You. The body grows weary, but my hope is
                                    in You to renew my strength.</td>
                                <td class='contentcontent' align='center'>Angels of the living God, search the land of
                                    the living and the dead and recover all my stolen wealth in Jesus' name. Let the
                                    resurrection power of the Lord Jesus Christ come upon the works of my hands in
                                    Jesus' name, let my angel of blessing locate me today in the name of Jesus.</td>
                                <td class='contentcontent' align='center'>Give me a penetrating mind to discover, firm
                                    to judge, open to understand, free to serve the truth; an honest mind in telling
                                    what it sees rather than what it wants to see; a tolerant mind which does not
                                    dictate to other people, but which explain what it sees clearly; a mind infused by
                                    the light and the truth of your Son Jesus, patient in faith, while waiting for the
                                    vision of eternal life.</td>
                            </tr>
                            <tr>
                                <td colspan='3' align='center'>
                                    <center><input type='submit' value='Pray' class='button' /></center>
                                </td>
                            </tr>
                        </table>
                    </form>
                </td>
            </tr>
            <?php
            include 'footer.php';
            ?>