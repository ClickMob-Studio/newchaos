<?php
include "header.php";
if (!isset($_SESSION['deal_ok'])) {
    $db->query("SELECT uid FROM dond WHERE uid = ?");
    $db->execute([$user_class->id]);
    $res = $db->fetch_row(true);
    if (empty($res)) {
        $_SESSION['deal_ok'] = true;
        perform_query("INSERT INTO dond VALUES(?)", [$user_class->id]);
    } else
        $_SESSION['deal_ok'] = false;
}

?>

<div class="box_top">Lucky Boxes</div>
<div class="box_middle">
    <div class="pad">

        <?php
        $boxes = array(
            1,
            2,
            3,
            4,
            5,
            6,
            7,
            8,
            9,
            10,
            11,
            12,
            13,
            14,
            15,
            16,
            17,
            18,
            19,
            20,
            21,
            22,
            23,
            24,
            25,
            26
        );
        $amount = array(
            10,
            20,
            30,
            50,
            100,
            250,
            500,
            750,
            1000,
            2000,
            3000,
            4000,
            5000,
            7500,
            10000,
            30000,
            50000,
            100000,
            150000,
            180000,
            200000,
            350000,
            500000,
            750000,
            1000000,
            2500000
        );
        $bank = array(
            5,
            8,
            11,
            14,
            17,
            21,
            24
        );
        if (empty($_SESSION['deal'])) {
            if (isset($_GET['play'])) {
                if ($_SESSION['deal_ok']) {
                    if ($user_class->money >= 10000) {
                        $_SESSION['deal'] = true;
                        shuffle($amount);
                        $_SESSION['deal_box'] = array_combine($boxes, $amount);
                        $_SESSION['deal_boxes'] = array();
                        $_SESSION['deal_bank'] = true;
                        echo '<p>Okay, we have taken $10,000 and you can now play the game.</p>';
                        echo '<p><a href="lucky_boxes.php">continue</a></p>';
                        $user_class->money -= 10000;
                        perform_query("UPDATE grpgusers SET money = ? WHERE id = ?", [$user_class->money, $user_class->id]);
                    } else {
                        echo '<p>Sorry, you don\'t seem to have $10,000</p>';
                        echo '<p><a href="lucky_boxes.php">back</a></p>';
                    }
                } else {
                    echo '<p>Sorry, you have already played today.</p>';
                    echo '<p><a href="city.php">back</a></p>';
                    unset($_SESSION['deal_ok']);
                }
            } else {
                echo '<p>Welcome to Deal or No Deal! Here you have the chance to win up to $2,500,000 every day.</p>';
                echo '<p>It costs $10,000 to play - and you can win as little as $1</p>';
                echo '<p><input type="button" value="Play" onclick=\'document.location="lucky_boxes.php?play"\' /></p>';
            }
        } else if ($_SESSION['deal_ok']) {
            if (isset($_GET['end'])) {
                gameOver();
            } else if ((in_array(count($_SESSION['deal_boxes']), $bank)) && ($_SESSION['deal_bank'])) {
                $sum = 0;
                for ($x = 1; $x < 27; $x++)
                    if (!in_array($x, $_SESSION['deal_boxes']))
                        $sum += $_SESSION['deal_box'][$x];
                $amount = floor($sum / (26 - count($_SESSION['deal_boxes'])));
                if (isset($_GET['deal'])) {
                    gameOver();
                    echo '<p>You have accepted the banker\'s offer of $' . number_format($amount) . '!</p>';
                    echo '<p><a href="city.php">back</a></p>';
                    $user_class->money += $amount;
                    perform_query("UPDATE grpgusers SET money = ? WHERE id = ?", [$user_class->money, $user_class->id]);
                    die();
                } else if (isset($_GET['nodeal'])) {
                    echo '<p>You have declined the banker\'s offer of $' . number_format($amount) . '. You can now continue playing.</p>';
                    $_SESSION['deal_bank'] = 0;
                } else {
                    echo '<p>The banker has offered you $' . number_format($amount) . '</p>';
                    echo '<p><input type="button" value="Deal" onclick=\'document.location="lucky_boxes.php?deal"\' /> or <input type="button" value="No Deal" onclick=\'document.location="lucky_boxes.php?nodeal"\' class="x" /></p>';
                }
            } else if (
                isset($_GET['box']) &&
                $_GET['box'] > 0 &&
                $_GET['box'] < 27 &&
                (!isset($_SESSION['deal_mybox']) || $_SESSION['deal_mybox'] < 1)
            ) {
                $_SESSION['deal_mybox'] = $_GET['box'];
                echo '<p>You select box ' . $_GET['box'] . ' as your box - you can now select 5 other boxes before the banker makes an offer for your box</p>';
            } else if (
                isset($_GET['box'], $_SESSION['deal_mybox']) &&
                $_GET['box'] > 0 &&
                $_GET['box'] < 27 &&
                $_GET['box'] != $_SESSION['deal_mybox']
            ) {
                if (!in_array($_GET['box'], $_SESSION['deal_boxes'])) {
                    echo '<p>You open box ' . $_GET['box'] . ' and see $' . number_format($_SESSION['deal_box'][$_GET['box']]) . '</p>';
                    $_SESSION['deal_boxes'][] = $_GET['box'];
                    $_SESSION['deal_bank'] = true;
                }
            } else if (!isset($_SESSION['deal_mybox']) || $_SESSION['deal_mybox'] < 1) {
                echo '<p>Start off by selecting the box that you will keep until the end.</p>';
            }
            if (isset($_SESSION['deal_boxes']) && count($_SESSION['deal_boxes']) == 25) {
                $mybox = $_SESSION['deal_box'][$_SESSION['deal_mybox']];
                echo '<p>You open your box and get $' . number_format($mybox) . '!</p>';
                $user_class->money += $mybox;
                perform_query("UPDATE grpgusers SET money = ? WHERE id = ?", [$user_class->money, $user_class->id]);
                unset($_SESSION['deal_ok']);
                gameOver();
            }
            echo '<table cellpadding="5" align="center">';
            echo '<tr>';
            echo '<td>' . showBox(1, $_SESSION['deal_box'][1]) . '</td>';
            echo '<td>' . showBox(2, $_SESSION['deal_box'][2]) . '</td>';
            echo '<td>' . showBox(3, $_SESSION['deal_box'][3]) . '</td>';
            echo '<td>' . showBox(4, $_SESSION['deal_box'][4]) . '</td>';
            echo '<td>' . showBox(5, $_SESSION['deal_box'][5]) . '</td>';
            echo '</tr>';
            echo '<tr>';
            echo '<td>' . showBox(6, $_SESSION['deal_box'][6]) . '</td>';
            echo '<td>' . showBox(7, $_SESSION['deal_box'][7]) . '</td>';
            echo '<td>' . showBox(8, $_SESSION['deal_box'][8]) . '</td>';
            echo '<td>' . showBox(9, $_SESSION['deal_box'][9]) . '</td>';
            echo '<td>' . showBox(10, $_SESSION['deal_box'][10]) . '</td>';
            echo '</tr>';
            echo '<tr>';
            echo '<td>' . showBox(11, $_SESSION['deal_box'][11]) . '</td>';
            echo '<td>&nbsp;</td>';
            echo '<td>' . showBox(12, $_SESSION['deal_box'][12]) . '</td>';
            echo '<td>&nbsp;</td>';
            echo '<td>' . showBox(13, $_SESSION['deal_box'][13]) . '</td>';
            echo '</tr>';
            echo '<tr>';
            echo '<td>' . showBox(14, $_SESSION['deal_box'][14]) . '</td>';
            echo '<td>' . showBox(15, $_SESSION['deal_box'][15]) . '</td>';
            echo '<td>' . showBox(16, $_SESSION['deal_box'][16]) . '</td>';
            echo '<td>' . showBox(17, $_SESSION['deal_box'][17]) . '</td>';
            echo '<td>' . showBox(18, $_SESSION['deal_box'][18]) . '</td>';
            echo '</tr>';
            echo '<tr>';
            echo '<td>' . showBox(19, $_SESSION['deal_box'][19]) . '</td>';
            echo '<td>' . showBox(20, $_SESSION['deal_box'][20]) . '</td>';
            echo '<td>' . showBox(21, $_SESSION['deal_box'][21]) . '</td>';
            echo '<td>' . showBox(22, $_SESSION['deal_box'][22]) . '</td>';
            echo '<td>' . showBox(23, $_SESSION['deal_box'][23]) . '</td>';
            echo '</tr>';
            echo '<tr>';
            echo '<td>&nbsp;</td>';
            echo '<td>' . showBox(24, $_SESSION['deal_box'][24]) . '</td>';
            echo '<td>' . showBox(25, $_SESSION['deal_box'][25]) . '</td>';
            echo '<td>' . showBox(26, $_SESSION['deal_box'][26]) . '</td>';
            echo '<td>&nbsp;</td>';
            echo '</tr>';
            echo '</table>';
        } else {
            echo '<p>You have already played today.</p>';
            echo '<p><a href="city.php">back</a></p>';
            unset($_SESSION['deal_ok']);
        }
        function showBox($num, $amount)
        {
            if (isset($_SESSION['deal_box'])) {
                $open = in_array($num, $_SESSION['deal_boxes']);
                $rtn = '<table onclick=\'document.location="lucky_boxes.php?box=' . $num . '"\' onmouseover="this.style.border=\'solid 2px #999\';" onmouseout="this.style.border=\'solid 2px #000\';" style="background-color:#69f;border:solid 2px #000;" width="100">';
                if (($amount > 0) && ($open)) {
                    $rtn .= '<tr style="background-color:#ddd;">';
                    $rtn .= '<td align="center" height="18" style="color:red;"><b class="text-black">$' . number_format($amount) . '</b>';
                } else if (isset($_SESSION['deal_mybox']) && $num == $_SESSION['deal_mybox']) {
                    $rtn .= '<tr style="background-color:#296321;">';
                    $rtn .= '<td align="center" height="18"><b class="text-white">Your Box</b>';
                } else {
                    $rtn .= '<tr style="background-color:#b40929;">';
                    $rtn .= '<td align="center" height="18" style="color:green;"><b class="text-black">Not Opened</b>';
                }
                $rtn .= '</td></tr>';
                $rtn .= '<tr><td align="center" style="font-size:18pt;">' . $num . '<br /></td></tr></table>';
            } else {
                echo '&nbsp;';
            }
            return $rtn;
        }
        function gameOver()
        {
            unset($_SESSION['deal']);
            unset($_SESSION['deal_box']);
            unset($_SESSION['deal_mybox']);
            unset($_SESSION['deal_boxes']);
            unset($_SESSION['deal_bank']);
            unset($_SESSION['deal_ok']);
        }
        ?>

    </div>
</div>
<?php
include "footer.php";
?>