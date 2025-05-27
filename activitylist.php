<?php
include("header.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_POST['resetref'])) {
    $result = $db->query("UPDATE `grpgusers` SET `killcomp` = '0'");
    if ($result) {
        echo Message("The kill counts have been reset.");
    } else {
        echo "Error: " . $db->error;
    }
}

if (isset($_POST['resetexp'])) {
    $result = $db->query("UPDATE `grpgusers` SET `expcount` = '0'");
    if ($result) {
        echo Message("The exp counts have been reset.");
    } else {
        echo "Error: " . $db->error;
    }
}
?>

<tr>
    <td class="contentspacer"></td>
</tr>
<tr>
    <td class="contenthead">Kill Contest</td>
</tr>
<tr>
    <td class="contentcontent">

<tr>
    <td>
        <center>
            <font color=red><b>Welcome to The Kill contest</b></font>
        </center><br>This is your chance to win some amazing prizes! All you need to do is win more attacks than
        your fellow players. Here are the prizes.</br></br>

        - <font color=gold>1st Place</font> : 90 Day Gradient & RM Days + 1 Point Per Kill</br>

        - <font color=silver>2nd Place</font> : 60 Day Gradient & RM Days + 10,000 Points.</br>

        - <font color=bronze>3rd Place</font> : 30 Day Gradient & RM Days + 5,000 Points.</br>
        </br>

        Along with the top 3 killers recieving these prizes. All users will also receive a prize if they hit these Kill Thresholds </br>
        </br>

        - <font color=bronze>100 Kills</font> : 5,000 Points</br>

        - <font color=silver>1,000 Kills</font> : 10,000 Points</br>

        - <font color=gold>10,000 Kills</font> : 25,000 Points</br>

        </br>

        This Competition Will end on the 14th of March at 23:59am (Game Time)
        </br></br>
    </td>
</tr>

<table width="100%" style="border: 1px solid #444444;" cellpadding="4" cellspacing="0">
    <tr>
        <td style="border-right: 1px solid #444444;">
            <center><b><u>Time Online</u></b></center><br />
            <table width="100%">
                <tr>





                    <td><b>#</b></td>
                    <td><b>Username</b></td>
                    <td><b>Time online</b></td>
                </tr>

                <?php
                $db->query("SELECT * FROM `grpgusers` ORDER BY `dailytime` DESC LIMIT 25");
                $db->execute();
               // $rank = 0;
                
                foreach ( $db->fetch_row() as $index => $line ) {

                   $user_name = new User($line['id']);
                    $minutes = ($line['dailytime'] > 0) ? $line['dailytime'] : 0;
                    $activeStr = calctime($minutes * 60);
                    echo '<tr><td width="10%">' . $index+1 . '.</td><td width="55%">' . $user_name->formattedname . '</td><td width="35%">' . $activeStr . '</td></tr>';
               

                }

                ?>


            </table>


    </tr>
</table>

</td>
</tr>
<?php
include("footer.php");
?>