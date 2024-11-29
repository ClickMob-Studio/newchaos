<?php
include 'dbcon.php';

function prettynum($num, $dollar = "0") {
    $out = strrev((string) preg_replace('/(\d{3})(?=\d)(?!\d*\.)/', '$1,', strrev($num)));
    if ($dollar && is_numeric($num)) {
        $out = "$" . $out;
    }
    return $out;
}

$result = mysql_query("SELECT * FROM `items` WHERE `id` = '" . $_GET['id'] . "'");
$worked = mysql_fetch_array($result);
?>		
<html>
    <head>
        <title><?php echo $worked['itemname'] ?></title>

        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
        
        <!-- Bootstrap 5 -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

        <style>
            body {
                background: url('/asset/img/background.png') top center / cover no-repeat #21201c;
                font-family: 'Montserrat', sans-serif;
                color: #fff;
                padding: 85px 0;
            }

            * {
                font-size: 1.4rem;
            }

            .container {
                width: 90%; /* Ensures content does not stretch too wide */
                max-width: 1200px; /* Keeps a max width for large screens */
            }

            .wrap {
                background-color: #000;
                border: 1px solid #333;
            }

            .header {
                background-color: #1A1A1A;
                border: 1px solid #333;
            }

            .header p {
                color: white;
                font-size: 16px;
                font-weight: bold;
                text-align: center;
            }

            .style1, .style2 {
                padding: 5px;
                border: 1px solid #333;
                font-weight: bold;
                color: #FFFFFF;
                background-color: #1A1A1A;
            }

            .textl, .textr, .text, .textm, .text2, .textl2, .textr2, .textm2 {
                padding: 5px;
                border: 1px solid #333;
            }

            .textr {
                background-color: #111111;
            }

            .textl2, .textr2 {
                background-color: #333333;
            }

            /* Maintain fixed table width */
            table {
                width: 100%;
                table-layout: fixed;
            }

            /* Ensure content is centered within the container */
            .wrap td {
                text-align: center;
            }

        </style>

    </head>
    <body>

        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="header">
                        <p><?php echo $worked['itemname'] ?></p>
                    </div>

                    <table class='wrap table table-bordered table-dark'>
                        <tr>
                            <td colspan='2' class='style1'>Description</td>
                        </tr>
                        <tr>
                            <td class='textl' align='center'>
                                <img src='<?php echo $worked['image'] ?>' width='100' height='100' class="img-fluid" style='border: 1px solid #000'>
                            </td>
                            <td class='textm2'><?php echo $worked['description'] ?></td>
                        </tr>
                    </table>

                    <br>

                    <table class='table table-bordered table-dark'>
                        <tr>
                            <td colspan='4' class='style2'>Details</td>
                        </tr>
                        <tr>
                            <td class='textm'>Name:</td>
                            <td class='textr'><?php echo $worked['itemname'] ?></td>
                        </tr>
                        <tr>
                            <td class='textm'>Sell Value:</td>
                            <td class='textr'><?php echo prettynum($worked['cost'] * .6) ?></td>
                        </tr>
                        <tr>
                            <td class='textm'>Shop Cost:</td>
                            <td class='textr'><?php echo prettynum($worked['cost']) ?></td>
                        </tr>
                        <tr>
                            <td class='textm'>Attack Bonus:</td>
                            <td class='textr'><?php echo $worked['offense'] ?>%</td>
                        </tr>
                        <tr>
                            <td class='textm'>Defense Bonus:</td>
                            <td class='textr'><?php echo $worked['defense'] ?>%</td>
                        </tr>
                        <tr>
                            <td class='textm'>Speed Bonus:</td>
                            <td class='textr'><?php echo $worked['speed'] ?>%</td>
                        </tr>
                        <tr>
                            <td class='textm'>Required Level:</td>
                            <td class='textr'><?php echo $worked['level'] ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Bootstrap 5 JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    </body>
</html>
