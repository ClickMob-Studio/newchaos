<?php
include 'header.php';
if ($user_class->crew == 0) {
    if ($_POST['create'] != "") { // if they are wanting to start a new crew
        $_POST['tag'] = str_replace('"', '', $_POST['tag']);
        $_POST['name'] = str_replace('"', '', $_POST['name']);
        $crewname = strip_tags($_POST['name']);
        $crewtag = strip_tags($_POST['tag']);
        $error .= ($user_class->money < 500000) ? "<div>You don't have enough money to start a crew. You need at least $500,000</div>" : $error;
        $error .= ($user_class->crew != 0) ? "<div>You have to leave your crew to start a new crew.</div>" : "";
        $error .= (strlen($crewname) < 3) ? "<div>Your crew's name has to be at least 3 characters long.</div>" : "";
        $error .= (strlen($crewname) > 25) ? "<div>Your crew's name can only be a max of 25 characters long.</div>" : "";
        $error .= (strlen($crewtag) < 1) ? "<div>Your crew's tag has to be at least 1 character long.</div>" : "";
        $error .= (strlen($crewtag) > 3) ? "<div>Your crew's tag can only be a max of 3 characters long.</div>" : "";
        $crewname = addslashes($crewname);
        $crewtag = addslashes($crewtag);
        //check if name is taken yet
        $check = mysql_query("SELECT * FROM `crews` WHERE `name`='" . $crewname . "'");
        $exist = mysql_num_rows($check);
        $error .= ($exist > 0) ? "<div>The crew name you chose is already taken.</div>" : "";
        //check if tag is taken yet
        $check = mysql_query("SELECT * FROM `crews` WHERE `tag`='" . $crewtag . "'");
        $exist = mysql_num_rows($check);
        $error .= ($exist > 0) ? "<div>The crew tag you chose is already taken.</div>" : "";
        if ($error == "") { // if there are no errors, make the crew
            perform_query("INSERT INTO `crews` (name, tag, leader) VALUES (?, ?, ?)", [$crewname, $crewtag, $user_class->id]);
            $newmoney = $user_class->money - 500000; //deduct the cost of the money
            $result = mysql_query("SELECT * FROM `crews` WHERE `leader` = '" . $user_class->id . "'");
            $worked = mysql_fetch_array($result);
            $crewid = $worked['id'];

            perform_query("UPDATE `grpgusers` SET `crew` = ?, `money` = ?, `crewleader` = '1', `crank` = '1' WHERE `id` = ?", [$crewid, $newmoney, $_SESSION['id']]);
            perform_query("DELETE FROM `crewinvites` WHERE `playerid` = ?", [$user_class->id]);

            echo Message("You have successfully created a crew!");
        } else {
            echo Message($error);
        }
    }
    ?>


    <div class="contenthead floaty">
        <span
            style="margin: 0; line-height: 27px; text-transform: uppercase; font-size: 20px; text-align: left; text-indent: 25px;">
            <h4>Create Crew</h4>
        </span>
        <hr>
        <table id="newtables" style="width:100%;">


            <tr>
                <form method='post'>
                    Well, it looks like you haven't join or created a crew yet.<br><br>
                    To create a crew it costs $500,000. If you don't have enough, or would like to join someone elses crew,
                    check out the <a href="crew_list.php">crew List</a> for other crews to join.<br><br>
                    <table width='100%'>
                        <tr>
                            <td width='15%'>crew Name:</td>
                            <td width='35%'><input type='text' name='name' value='' maxlength='20' size='16'></td>
                            <td width='15%'>crew Tag</td>
                            <td width='35%'><input type='text' name='tag' value='' maxlength='3' size='4'></td>
                        </tr>
                        <tr>
                            <td colspan='4' align='center'><input type='submit' name='create' value='Create'></td>
                        </tr>
                    </table>
                </form>
                </td>
            </tr>
            <?php
}
include 'footer.php';
?>