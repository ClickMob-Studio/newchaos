<?php
include 'header.php';
?>
<div class='box_top'>Gang Wars</div>
<div class='box_middle'>
    <div class='pad'>
        <?php

        if ($user_class->gang != 0) {
            $gang_class = new Gang($user_class->gang);
            $wars = CheckGangWar($user_class->gang);

            $db->query("SELECT * FROM gangwars WHERE (gang1 = ? OR gang2 = ?) AND accepted = 1");
            $db->execute([$user_class->gang, $user_class->gang]);
            $war = $db->fetch_row(true);
            ?>
            <center><a href="gangwarguide.php"><button class="ycbutton">Click here to view the Gang War guide.</button></a>
            </center>
            <br />
            <br />
            <?php
            if ($wars == 1) {
                if ($war['gang1'] == $user_class->gang) {
                    $yourgang = $war['gang1'];
                    $yourscore = $war['gang1score'];
                    $theirgang = $war['gang2'];
                    $theirscore = $war['gang2score'];
                } else {
                    $yourgang = $war['gang2'];
                    $yourscore = $war['gang2score'];
                    $theirgang = $war['gang1'];
                    $theirscore = $war['gang1score'];
                }
                $war_gang = new Gang($theirgang);
                if ($_GET['surrender'] == "true" && $user_class->id == $gang_class->leader) {
                    $db->query("SELECT * FROM gangwars WHERE (gang1 = ? OR gang2 = ?) AND accepted = 1 LIMIT 1");
                    $db->execute([$user_class->gang, $user_class->gang]);
                    $query = $db->fetch_row(true);
                    $atwar = count($query);
                    if ($user_class->gang != 0 && $atwar > 0) {
                        $winner_gang = new Gang($theirgang);
                        $loser_gang = new Gang($user_class->gang);
                        $newvault = $winner_gang->moneyvault + ($query['bet'] * 2);

                        perform_query("UPDATE gangs SET moneyvault = ? WHERE id = ? LIMIT 1", [$newvault, $winner_gang->id]);

                        Send_Event($winner_gang->leader, "Your rival gang has surrendered! You finished with the total score of $yourscore and [-_GANGID_-] finished with the total score of $theirscore. You have been granted the bet of " . prettynum($query['bet'] * 2, 1) . ".", $theirgang);
                        Send_Event($loser_gang->leader, "You have surrendered so unfortunately you lost the gang war. You finished with the total score of $theirscore and [-_GANGID_-] finished with the total score of $yourscore.", $yourgang);

                        perform_query("DELETE FROM gangwars WHERE (gang1 = ? OR gang2 = ?) AND accepted = 1 LIMIT 1", [$user_class->gang, $user_class->gang]);
                        echo Message("You have surrendered in your gang war.");
                    }
                }
                ?>
                <script type="text/javascript">
                    function surrender() {
                        input_box = confirm("Are you sure you want to surrender to your enemy gang?");
                        if (input_box == true) {
                            window.location.href = "viewwar.php?surrender=true";
                        }
                    }
                </script>
                <?php
                echo ($user_class->id == $gang_class->leader) ? '<center><b><a href="javascript:surrender();">Surrender Gang War</a></b></center><br /><br />' : "";
                print "
    <table id='newtables' style='width:100%;'>
        <tr>
            <th>War With</th>
            <th>Your Score</th>
            <th>Their Score</th>
            <th>Bet</th>
            <th>Time Left</th>
        </tr>
        <tr>
            <td>$war_gang->formattedname</td>
            <td>" . prettynum($yourscore) . "</td>
            <td>" . prettynum($theirscore) . "</td>
            <td>" . prettynum($war['bet'] * 2, 1) . "</td>
            <td>" . howlongleft($war['timeending']) . "</td>
        </tr>
</table>
";
            } else
                echo "You don't have any active gang wars.";
            print "</td></tr>";
        } else
            echo Message("You aren't in a gang.");
        include("gangheaders.php");
        include 'footer.php';
        ?>