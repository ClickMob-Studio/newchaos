<?php
include("header.php");
?>
<div class="box_top">Request Relationship</div>
<div class="box_middle">
    <div class="pad">
        <?php
        if ($_GET['player'] == "") {
            echo Message("Oops, looks like you've followed an incorrect link! Please <a href='home.php'>click here</a> to go home.");
            include("footer.php");
            die();
        }
        if ($_GET['action'] == "") {
            echo Message("Oops, looks like you've followed an incorrect link! Please <a href='home.php'>click here</a> to go home.");
            include("footer.php");
            die();
        }
        $player = new User($_GET['player']);
        if (isset($_POST['send'])) {
            if ($user_class->relationshipended > (time() - 432000)) {
                $times = time() - $user_class->relationshipended;
                $timess = time() + $times;

                echo Message("You can only marry once every 5 days");
                include("footer.php");
                die();
            }
            echo Message("You have requested to start a relationship.");
            Relationship_Req($_POST['player'], $_POST['status'], $user_class->id);
        }
        if (isset($_POST['end'])) {
            echo Message("You have ended your relationship.");
            Send_Event($user_class->relplayer, "[-_USERID_-] has ended your relationship.", $user_class->id);
            $end = mysql_query("UPDATE `grpgusers` SET `relationship` = '0', `shared_bank` = '0', `relationshipdays` = '0', relationshipended = " . time() . ", `relplayer` = '0' WHERE `id` = '" . $user_class->id . "' OR `id` = '" . $user_class->relplayer . "'");
        }
        if ($_GET['action'] == "new") {
            if ($_GET['player'] == $user_class->id) {
                echo Message("You can't date yourself.");
                include("footer.php");
                die();
            }
            if ($user_class->relationship != 0) {
                echo Message("You already have a relationship with someone.");
                include("footer.php");
                die();
            }
            if ($player->relationship != 0) {
                echo Message("This player already has a relationship with someone.");
                include("footer.php");
                die();
            }
            ?>
            <b>You are requesting a relationship with</b> <?php echo $player->formattedname; ?><b>.</b><br /><br />
            <form method="post">
                <table width="40%">
                    <tr>
                        <td width="30%">
                            <b>Relationship Status:</b>
                        </td>
                        <td width="10%">
                            <select name="status">
                                <option value="1">Dating</option>
                                <option value="2">Engaged</option>
                                <option value="3">Married</option>
                            </select>
                        </td>
                    </tr>
                </table>
                <br />
                <input type="hidden" name="player" value="<?php echo $_GET['player']; ?>" />
                <input type="submit" name="send" value="Send Request" />
            </form>
        </div>
    </div>
    <?php
        } else {
            ?>
    <div class="box_top">Ending Your Relationship</div>
    <div class="box_middle">
        <div class="pad">
            <?php
            if ($user_class->relationship == 0) {
                echo Message("You don't have a relationship to end.");
                include("footer.php");
                die();
            }
            if ($user_class->relplayer != $_GET['player']) {
                echo Message("You don't have a relationship with that player.");
                include("footer.php");
                die();
            }
            ?>
            <b>Are you sure you want to end your relationship with</b> <?php echo $player->formattedname; ?><b>?</b><br />
            Please ensure that there is no money left in the Family Vault. It WILL Be LOST!<br />
            <form method="post">
                <input type="submit" name="end" value="End Relationship" />&nbsp;&nbsp;&nbsp;
            </form>
        </div>
    </div>

    <?php
        }
        include("footer.php");
        ?>