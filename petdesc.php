<?php
require_once __DIR__ . '/includes/dbcon.php';
require_once __DIR__ . '/includes/class_main.php';
require_once __DIR__ . '/includes/class_users.php';
$_GET['id'] = isset($_GET['id']) && ctype_digit($_GET['id']) ? $_GET['id'] : null;
if (empty($_GET['id']))
    $mtg->error("You didn't select a valid pet");
$db->query("SELECT * FROM petshop WHERE id = ?");
$db->execute([$_GET['id']]);
if (!$db->num_rows())
    $mtg->error("That pet doesn't exist");
$row = $db->fetch_row(true);
?>
<html>
    <head>
        <title><?php echo $row['name'] ?></title>
        <style>
            * {
                font-family: tahoma;
                font-size: 12px;
                color: #FFFFFF;
            }
            body {
                background-color: #000000;
                margin: 15px;
            }
            .wrap {
                background-color: #000000;
                border: 1px solid #333;
            }
            .header {
                background-color: #1A1A1A;
                border: 1px solid #333;
            }
            .head_text {
                padding: 5px;
                border: 1px solid #333;
                background-color: #1A1A1A;
                color: #999999;
                font-weight: bold;
            }
            .head_text2l {
                padding: 5px;
                border-left: 1px solid #333;
                border-bottom: 1px solid #333;
                background-color: #555555;
                color: #999999;
                font-weight: bold;
            }
            .head_text2r {
                padding: 5px;
                border-right: 1px solid #333;
                border-bottom: 1px solid #333;
                background-color: #555555;
                color: #999999;
                font-weight: bold;
            }
            .head_text2 {
                padding: 5px;
                border-bottom: 1px solid #333;
                background-color: #555555;
                color: #999999;
                font-weight: bold;
            }
            .head_text3 {
                padding: 5px;
                border-left: 1px solid #333;
                border-right: 1px solid #333;
                border-bottom: 1px solid #333;
                background-color: #111111;
                color: #111111;
            }
            .textl {
                padding: 5px;
                border-left: 1px solid #333;
                border-bottom: 1px solid #333;
                background-color: #111111;
            }
            .textr {
                padding: 5px;
                border-right: 1px solid #333;
                border-bottom: 1px solid #333;
                background-color: #111111;
            }
            .text {
                padding: 5px;
                border-bottom: 1px solid #333;
                background-color: #111111;
            }
            .textl2 {
                padding: 5px;
                border-left: 1px solid #333;
                border-bottom: 1px solid #333;
                background-color: #333333;
            }
            .textr2 {
                padding: 5px;
                border-right: 1px solid #333;
                border-bottom: 1px solid #333;
                background-color: #333333;
            }
            .text2 {
                padding: 5px;
                border-bottom: 1px solid #333;
                background-color: #333333;
            }
            .textm {
                padding: 5px;
                background-color: #111111;
                border-left: 1px solid #333;
                border-right: 1px solid #333;
                border-bottom: 1px solid #333;
            }
            .textm2 {
                padding: 5px;
                background-color: #111111;
                border-left: 1px solid #333;
                border-right: 1px solid #333;
                border-bottom: 1px solid #333;
            }
            .style1 {
                padding: 5px;
                border: 1px solid #333333;
                background-color: #1A1A1A;
                color: #FFFFFF;
                font-weight: bold;
            }
            .style2 {
                padding: 5px;
                border: 1px solid #333;
                background-color: #1A1A1A;
                color: #FFFFFF;
                font-weight: bold;
            }
        </style>
    </head>
    <body background="images/background.gif">
        <table class='wrap' width='100%' height='100%' cellpadding='5' cellspacing='0' align='center'>
            <tr>
                <td valign='top'>
                    <table class='header' width='100%' cellpadding='5' cellspacing='0' align='center'>
                        <tr>
                            <td><p style='color:white;font-size:16px;font-weight:bold;'><center><?php echo $row['name'] ?></center></p></td>
            </tr>
        </table>
        <br>
        <table width='100%' cellpadding='4' cellspacing='0'>
            <tr>
                <td colspan='2' class='style1'>Description</td>
            </tr>
            <tr>
                <td class='textl' align='center'><img src='<?php echo $row['picture'] ?>' width='100' height='100' style='border: 1px solid #000000'></td>
                <td class='textm2'><?php echo $row['description'] ?></td>
            </tr>
        </table>
        <br>
        <table width='100%' cellpadding='4' cellspacing='0'>
            <tr>
                <td colspan='4' class='style2'>Details</td>
            </tr>
            <tr>
                <td class='textm'>Pet Type:</td>
                <td class='textr'><?php echo $row['name'] ?></td>
            </tr>
            <tr>
                <td class='textm'>Cost:</td>
                <td class='textr'>$<?php echo $row['cost'] ?></td>
            </tr>
            <tr>
                <td class='textm'>Base Strength:</td>
                <td class='textr'><?php echo $row['str'] ?></td>
            </tr>
            <tr>
                <td class='textm'valign='top'>Base Defense:</td>
                <td class='textr'><?php echo $row['def'] ?><br></td>
            </tr>
            <tr>
                <td class='textm'valign='top'>Base Speed:</td>
                <td class='textr'><?php echo $row['spe'] ?><br></td>
            </tr>
        </table>
    </td>
</tr>
</table>
</body>
</html>