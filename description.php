<?php
include_once 'dbcon.php';
function prettynum($num, $dollar = "0")
{
    $out = strrev((string) preg_replace('/(\d{3})(?=\d)(?!\d*\.)/', '$1,', strrev($num)));
    if ($dollar && is_numeric($num)) {
        $out = "$" . $out;
    }
    return $out;
}

$worked = Get_Item(filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT));
?>
<html>

<head>

    <title><?php echo $worked['itemname'] ?></title>

    <style>
        * {
            font-family: tahoma;
            font-size: 12px;
            color: #FFFFFF;
        }

        body {
            font: 1.4rem 'Montserrat', sans-serif;
            color: #fff;
            background: url(/asset/img/background.png) top center / cover no-repeat #21201c;
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
                        <td>
                            <p style='color:white;font-size:16px;font-weight:bold;'>
                                <center><?php echo $worked['itemname'] ?></center>
                            </p>
                        </td>
                    </tr>
                </table>

                <br>

                <table width='100%' cellpadding='4' cellspacing='0'>

                    <tr>
                        <td colspan='2' class='style1'>Description</td>
                    </tr>
                    <tr>
                        <td class='textl' align='center'><img src='<?php echo $worked['image'] ?>' width='100'
                                height='100' style='border: 1px solid #000000'></td>
                        <td class='textm2'><?php echo $worked['description'] ?></td>
                    </tr>
                </table>


                <br>

                <table width='100%' cellpadding='4' cellspacing='0'>
                    <tr>
                        <td colspan='4' class='style2'>Details</td>
                    </tr>
                    <tr>
                        <td class='textm'>Name: </td>
                        <td class='textr'><?php echo $worked['itemname'] ?></td>

                    </tr>
                    <tr>
                        <td class='textm'>Sell Value: </td>
                        <td class='textr'>$<?php echo prettynum($worked['cost'] * .6) ?></td>
                    </tr>
                    <tr>
                        <td class='textm'>Shop Cost: </td>

                        <td class='textr'>$<?php echo prettynum($worked['cost']) ?></td>
                    </tr>
                    <tr>
                        <td class='textm' valign='top'>Attack Bonus: </td>
                        <td class='textr'>
                            <?php echo $worked['offense'] ?>%<br>
                        </td>
                    </tr>
                    <tr>
                        <td class='textm' valign='top'>Defense Bonus: </td>
                        <td class='textr'>
                            <?php echo $worked['defense'] ?>%<br>
                        </td>
                    </tr>
                    <tr>
                        <td class='textm' valign='top'>Speed Bonus: </td>
                        <td class='textr'><?php echo $worked['speed'] ?>%</td>
                    </tr>
                    <tr>
                        <td class='textm' valign='top'>Required Level: </td>
                        <td class='textr'><?php echo $worked['level'] ?></td>
                    </tr>
                </table>

                </br>

                <table width='100%' cellpadding='4' cellspacing='0'>
                    <tr>
                        <td colspan='4' class='style2'>Shareability</td>
                    </tr>
                    <tr>
                        <td class='textm' valign='top'>Gang vault: </td>
                        <td class='textr'><?php echo ($worked['shareable'] ? 'Yes' : 'No') ?></td>
                    </tr>
                    <tr>
                        <td class='textm' valign='top'>Tradeable on Market: </td>
                        <td class='textr'>Yes</td>
                    </tr>
                    <tr>
                        <td class='textm' valign='top'>Sendable: </td>
                        <td class='textr'>Yes</td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>

</body>
</table>