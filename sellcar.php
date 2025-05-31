<?php
include 'header.php';



if ($_GET['id'] == "") {

    echo Message("No car picked.");

    include 'footer.php';

    die();
}



$howmany = Check_Car($_GET['id'], $user_class->id);//check how many they have


$result2 = mysql_query("SELECT * FROM `carlot` WHERE `id`='" . $_GET['id'] . "'");

$worked = mysql_fetch_array($result2);



$price = $worked['cost'] * .60;



if ($_GET['confirm'] == "true") { //if they confirm they want to sell it
    $error = ($howmany == 0) ? "You don't have any of those." : $error;

    if (isset($error)) {
        echo Message($error);
        include 'footer.php';
        die();
    }

    $newmoney = $user_class->money + $price;

    perform_query("UPDATE `grpgusers` SET `money` = ? WHERE `id` = ?", [$newmoney, $_SESSION['id']]);
    Take_Car($_GET['id'], $user_class->id);
    echo Message("You have sold your " . $worked['name'] . " for $" . prettynum($price) . ".<br /><br /><a href='garage.php'>Back to Garage</a>");

    include 'footer.php';
    die();
}
?>

<tr>
    <td class="contentspacer"></td>
</tr>
<tr>
    <td class="contenthead">Sell Car</td>
</tr>

<tr>
    <td class="contentcontent">

        <?php echo "Are you sure that you want to sell your " . $worked['name'] . " for $" . prettynum($price) . "?<br /><br /><a href='sellcar.php?id=" . $_GET['id'] . "&confirm=true'>Yes</a>"; ?>&nbsp;&nbsp;|&nbsp;&nbsp;<a
            href="garage.php">No</a>

    </td>
</tr>

<?php
include 'footer.php';
?>