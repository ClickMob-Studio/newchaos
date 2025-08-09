<?php
include "header.php";
?>

<div class='box_top'>The Doors</div>
<div class='box_middle'>
    <div class='pad'>
        <?php
        if ($user_class->jail > 0) {
            echo Message("You cant do the doors  while in prison.");
            include 'footer.php';
            die();
        }
        if ($user_class->hospital > 0) {
            echo Message("You cant do the doors hospital.");
            include 'footer.php';
            die();
        }
        if ($user_class->doors < 1) {
            echo Message("You have already been here today.");
            include 'footer.php';
            die();
        }
        if (isset($_GET['open']) && $_GET['open'] == 'x') {
            $chance = rand(1, 5);
            if ($chance == 1) {
                $stole = rand(20, 150);
                perform_query("UPDATE grpgusers SET points = points + ?, doors = doors - 1 WHERE id = ?", [$stole, $user_class->id]);
                echo Message("You opened the door and found " . prettynum($stole) . " points.<br>
	<a href='thedoors.php'>Back</a>");
                include 'footer.php';
                die();
            }
            if ($chance == 2) {
                $stole1 = rand(100000, 1000000);
                perform_query("UPDATE grpgusers SET money = money + ?, doors = doors - 1 WHERE id = ?", [$stole1, $user_class->id]);
                echo Message("You opened the door and found $" . prettynum($stole1) . ".<br>
	<a href='thedoors.php'>Back</a>");
                include 'footer.php';
                die();
            }
            if ($chance == 3) {
                $hosp = 120;
                perform_query("UPDATE `grpgusers` SET hospital = ?, `hhow` = 'door' WHERE `id` = ?", [$hosp, $user_class->id]);
                perform_query("UPDATE grpgusers SET doors = doors - 1 WHERE id = ?", [$user_class->id]);
                echo Message("You opened the door and tripped a rigged explosion......<br>
	<a href='thedoors.php'>Back</a>");
                include 'footer.php';
                die();
            }
            if ($chance == 4) {
                $stole1 = rand(1, 5);
                perform_query("UPDATE grpgusers SET raidtokens = raidtokens + ?, doors = doors - 1 WHERE id = ?", [$stole1, $user_class->id]);
                echo Message("You opened the door and found " . prettynum($stole1) . " Raid Tokens.<br>
	<a href='thedoors.php'>Back</a>");
                include 'footer.php';
                die();
            }
            if ($chance == 5) {
                $stole1 = rand(2, 7);
                perform_query("UPDATE grpgusers SET raidtokens = raidtokens + ?, doors = doors - 1 WHERE id = ?", [$stole1, $user_class->id]);
                echo Message("You opened the door and found " . prettynum($stole1) . " Raid Tokens.<br>
        <a href='thedoors.php'>Back</a>");
                include 'footer.php';
                die();
            }
        }

        ?>

        <div class="contenthead floaty">

            <center>Make your choice of which door to open<br />It may be nice....it may be nasty....<br />The only way
                to find out is to open it up. You can search behind the doors 5 times each day<br /> <br /><br></center>
            <center>
                <table width="600" border="0">
                    <tr>
                        <td><a href='thedoors.php?open=x'><img src="images/018-copy.jpg" width="150" height="250"
                                    alt="door" BORDER='0' /></a></td>
                        <td><a href='thedoors.php?open=x'><img src="images/018-copy.jpg" width="150" height="250"
                                    alt="door" BORDER='0' /></a></td>
                        <td><a href='thedoors.php?open=x'><img src="images/018-copy.jpg" width="150" height="250"
                                    alt="door" BORDER='0' /></a></td>
                        <td><a href='thedoors.php?open=x'><img src="images/018-copy.jpg" width="150" height="250"
                                    alt="door" BORDER='0' /></a></td>
                    </tr>
                </table>
                <br /><br />
            </center>
            </td>
            </tr>
        </div>
        <?php
        include 'footer.php';
        ?>