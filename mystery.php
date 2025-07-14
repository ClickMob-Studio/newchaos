<?php
include "header.php";
?>
<style>
    .contenthead {
        text-align: center;
    }

    .doors-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 20px;
        max-width: 600px;
        margin: auto;
    }

    .door {
        background-color: #333;
        /* Grey background */
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.7);
        /* Dark glow effect */
        padding: 10px;
        /* Padding around the door image */
        border-radius: 5px;
        /* Optional: adds rounded corners */
    }

    .door a {
        display: inline-block;
        /* Ensure the anchor tag respects padding */
    }

    .door img {
        display: block;
        /* Remove default image inline spacing */
        width: 150px;
        height: 250px;
    }
</style>

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
if ($_GET['open'] == x) {
    $chance = rand(1, 9);
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
        perform_query("UPDATE grpgusers SET  doors=doors-1 WHERE id = ?", [$user_class->id]);
        echo Message("You opened the door and tripped a rigged explosion......<br>
	<a href='thedoors.php'>Back</a>");
        include 'footer.php';
        die();
    }
    if ($chance == 4) {
        $stole1 = rand(1, 1);
        perform_query("UPDATE grpgusers SET raidtokens = raidtokens + ?, doors = doors - 1 WHERE id = ?", [$stole1, $user_class->id]);
        echo Message("You opened the door and found " . prettynum($stole1) . " Raid Token.<br>
	<a href='thedoors.php'>Back</a>");
        include 'footer.php';
        die();
    }
    if ($chance == 5) {
        $stole1 = rand(1, 1);
        perform_query("UPDATE grpgusers SET raidtokens = raidtokens + ?, doors = doors - 1 WHERE id = ?", [$stole1, $user_class->id]);
        echo Message("You opened the door and found " . prettynum($stole1) . " Raid Token.<br>
	<a href='thedoors.php'>Back</a>");
        include 'footer.php';
        die();
    }
    if ($chance == 6) {
        $stole1 = rand(1, 1);
        perform_query("UPDATE grpgusers SET raidtokens = raidtokens + ?, doors = doors - 1 WHERE id = ?", [$stole1, $user_class->id]);
        echo Message("You opened the door and found " . prettynum($stole1) . " Raid Token.<br>
	<a href='thedoors.php'>Back</a>");
        include 'footer.php';
        die();
    }
    if ($chance == 7) {
        $stole1 = rand(1, 1);
        perform_query("UPDATE grpgusers SET raidtokens = raidtokens + ?, doors = doors - 1 WHERE id = ?", [$stole1, $user_class->id]);
        echo Message("You opened the door and found " . prettynum($stole1) . " Raid Token.<br>
	<a href='thedoors.php'>Back</a>");
        include 'footer.php';
        die();
    }
    if ($chance == 8) {
        $stole1 = rand(1, 1);
        perform_query("UPDATE grpgusers SET raidtokens = raidtokens + ?, doors = doors - 1 WHERE id = ?", [$stole1, $user_class->id]);
        echo Message("You opened the door and found " . prettynum($stole1) . " Raid Token.<br>
	<a href='thedoors.php'>Back</a>");
        include 'footer.php';
        die();
    }
    if ($chance == 9) {
        $stole1 = rand(1, 1);
        perform_query("UPDATE grpgusers SET raidtokens = raidtokens + ?, doors = doors - 1 WHERE id = ?", [$stole1, $user_class->id]);
        echo Message("You opened the door and found " . prettynum($stole1) . " Raid Token.<br>
	<a href='thedoors.php'>Back</a>");
        include 'footer.php';
        die();
    }
}
?>

<div class="contenthead floaty">
    <span
        style="margin: 0; line-height: 27px; text-transform: uppercase; font-size: 20px; text-align: left; text-indent: 25px;">
        <h4>The Doors</h4>
    </span>
    <p style="text-align: center;">Make your choice of which door to open<br>It may be nice....it may be
        nasty....<br>The only way to find out is to open it up</p>
    <div class="doors-container"
        style="display: flex; flex-wrap: wrap; justify-content: center; gap: 20px; max-width: 600px; margin: auto;">
        <div class="door"><a href='thedoors.php?open=x'><img src="images/018-copy.jpg" width="150" height="250"
                    alt="door" /></a></div>
        <div class="door"><a href='thedoors.php?open=x'><img src="images/018-copy.jpg" width="150" height="250"
                    alt="door" /></a></div>
        <div class="door"><a href='thedoors.php?open=x'><img src="images/018-copy.jpg" width="150" height="250"
                    alt="door" /></a></div>
        <div class="door"><a href='thedoors.php?open=x'><img src="images/018-copy.jpg" width="150" height="250"
                    alt="door" /></a></div>
        <div class="door"><a href='thedoors.php?open=x'><img src="images/018-copy.jpg" width="150" height="250"
                    alt="door" /></a></div>
        <div class="door"><a href='thedoors.php?open=x'><img src="images/018-copy.jpg" width="150" height="250"
                    alt="door" /></a></div>
        <div class="door"><a href='thedoors.php?open=x'><img src="images/018-copy.jpg" width="150" height="250"
                    alt="door" /></a></div>
        <div class="door"><a href='thedoors.php?open=x'><img src="images/018-copy.jpg" width="150" height="250"
                    alt="door" /></a></div>
        <div class="door"><a href='thedoors.php?open=x'><img src="images/018-copy.jpg" width="150" height="250"
                    alt="door" /></a></div>
    </div>
</div>


<?php
include 'footer.php';
?>