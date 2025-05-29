<?php
include 'header.php';

$result = mysql_query("SELECT * FROM `hangar` WHERE `userid`='" . $user_class->id . "'");

$howmany = mysql_num_rows($result);

if ($howmany == 0) {

    echo Message("You don't have an airplane. You can't fly privately without an airplane.");

    include 'footer.php';

    die();
}





if ($_GET['go'] != "") {

    $result = mysql_query("SELECT * FROM `hangar` WHERE `userid`='" . $user_class->id . "'");

    $howmany = mysql_num_rows($result);



    $error = ($howmany == 0) ? "You don't have an airplane. You can't fly privately without an airplane." : $error;

    $error = ($user_class->jail > 0) ? "You can't fly somewhere if you are in jail." : $error;

    $error = ($_GET['go'] == $user_class->country) ? "You are already there." : $error;



    $result = mysql_query("SELECT * FROM `countries` WHERE `id`='" . $_GET['go'] . "'");

    $worked = mysql_fetch_array($result);



    $cost = 10000;



    $error = ($worked['name'] == "") ? "That country doesn't exist." : $error;

    $error = ($_GET['go'] == 1 && $user_class->admin != 1) ? "That country is admin only." : $error;

    $error = ($user_class->level < $worked['levelreq']) ? "You are not a high enough level to fly there." : $error;

    $error = ($user_class->money < $cost) ? "You dont have enough money to fly there." : $error;

    $error = ($worked['rmonly'] == 1 && $user_class->rmdays < 1) ? "You need to be a Respected Mobster to go there." : $error;





    if (!isset($error)) {

        $cost = 10000;

        $newmoney = $user_class->money - $cost;

        $getcity1 = mysql_query("SELECT * FROM `cities` WHERE `country` = '" . $worked['id'] . "' ORDER BY `levelreq` LIMIT 1");

        $getcity = mysql_fetch_array($getcity1);

        perform_query("UPDATE `grpgusers` SET `country` = ?, `money` = ?, `city` = ? WHERE `id` = ?", [$_GET['go'], $newmoney, $getcity['id'], $user_class->id]);

        echo Message("You successfully paid $10,000 to fly privately to your destination.");
    } else {

        echo Message($error);
    }
}
?>



<style type="text/css">
    .rm {

        color: #FFCC00;

        font-size: 8px;

        vertical-align: 3px;

        font-weight: 600;

    }
</style>



<tr>
    <td class="contentspacer"></td>
</tr>
<tr>
    <td class="contenthead">Fly [Currently In: <?php echo $user_class->countryname; ?>]</td>
</tr>

<tr>
    <td class="contentcontent">

        <center>

            Here you can fly to your destination using your airplane for only $10,000.<br /><br />

            [<a href="airport.php">Airport</a>]&nbsp;&nbsp;[<a href="bus.php">Bus Station</a>]&nbsp;&nbsp;[<a
                href="drive.php">Drive</a>]

        </center>

        <br />

        <table width="100%">

            <tr>

                <td><b>Country Name</b></td>

                <td><b>Level Req.</b></td>

                <td><b>Population</b></td>

            </tr>

            <?php
            if ($user_class->admin == 1) {

                $result = mysql_query("SELECT * FROM `countries` WHERE `show` = '1' ORDER BY `levelreq` ASC");

                while ($line = mysql_fetch_array($result, mysql_ASSOC)) {

                    $population1 = mysql_query("SELECT * FROM `grpgusers` WHERE `country` = '" . $line['id'] . "'");

                    $population = mysql_num_rows($population1);

                    if ($line['rmonly'] == 1) {

                        echo "<tr><td><a href='fly.php?go=" . $line['id'] . "'>" . $line['name'] . "</a>&nbsp;<span class='rm'><a href='rmstore.php'>RM ONLY</a></span></td><td>" . $line['levelreq'] . "</td><td>" . $population . "</td></tr>";
                    } else {

                        echo "<tr><td><a href='fly.php?go=" . $line['id'] . "'>" . $line['name'] . "</a></td><td>" . $line['levelreq'] . "</td><td>" . $population . "</td></tr>";
                    }
                }
            } else {

                $result = mysql_query("SELECT * FROM `countries` WHERE `show` = '1' ORDER BY `levelreq` ASC");

                while ($line = mysql_fetch_array($result, mysql_ASSOC)) {

                    $population1 = mysql_query("SELECT * FROM `grpgusers` WHERE `country` = '" . $line['id'] . "'");

                    $population = mysql_num_rows($population1);

                    if ($line['id'] != 1) {

                        if ($line['rmonly'] == 1) {

                            echo "<tr><td><a href='fly.php?go=" . $line['id'] . "'>" . $line['name'] . "</a>&nbsp;<span class='rm'><a href='rmstore.php'>RM ONLY</a></span></td><td>" . $line['levelreq'] . "</td><td>" . $population . "</td></tr>";
                        } else {

                            echo "<tr><td><a href='fly.php?go=" . $line['id'] . "'>" . $line['name'] . "</a></td><td>" . $line['levelreq'] . "</td><td>" . $population . "</td></tr>";
                        }
                    }
                }
            }
            ?>

        </table>

        <?php
        include 'footer.php';
        ?>