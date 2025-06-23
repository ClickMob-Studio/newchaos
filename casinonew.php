<?php

include 'header.php';

?>

<div class='box_top'>Casino</div>
<div class='box_middle'>
    <div class='pad'>
        <?php
        $db->query("SELECT COUNT(*) FROM fiftyfifty");
        $db->execute();
        $fiftyfifty_live_bets = $db->fetch_single();
        ?>

        <div class="contenthead floaty">


            <script language="javascript">
                function wo(url, w, h) {
                    width = screen.width;
                    height = screen.height;
                    window.open(url + "?w=" + w + "&h=" + h, 'showpicture', 'width=' + (w) + ',height=' + (h) + ',left=' + ((width - w) / 2) + ',top=0,fullscreen=0,location=0,menubar=0,scrol lbars=yes,status=0,to olbar=0,resizable=yes');
                }
            </script>

            <table cellspacing='4' width='90%' align='center'>
                <tr>
                    <td width='110' height='110' valign='middle' align='center' bgcolor="#28282A">
                        <a href="5050.php"
                            title="Place a bet and wait for someone to match it. One will win 90% of the money!"
                            onmouseover="document.game5050.src='images/casino/5050.png'"
                            onmouseout="document.game5050.src='images/casino/5050_b.png'">
                            <img src="images/casino/5050_b.png" name="game5050" style="border-color: #28282A"
                                border="2"></a>
                        <br>50/50
                        <?php echo "<div>Total live bets: " . $fiftyfifty_live_bets . "</div>"; ?>
                    <td width='110' height='110' valign='middle' align='center' bgcolor="#28282A">
                        <a href="luckydip.php" title="Pay $1,000 and try your luck to win more!"
                            onmouseover="document.luckydip.src='images/casino/luckydip.png'"
                            onmouseout="document.luckydip.src='images/casino/luckydip_b.png'">
                            <img src="images/casino/luckydip_b.png" name="luckydip" style="border-color: #28282A"
                                border="2"></a>
                        <br>LUCKY DIP
                    </td>

                    <td width='110' height='110' valign='top' align='center' bgcolor="#28282A">
                        <a href="hilo.php" title="Guess if the next card is higher or lower!"
                            onmouseover="document.higher_lower.src='images/casino/higher_lower.png'"
                            onmouseout="document.higher_lower.src='images/casino/higher_lower_b.png'">
                            <img src="images/casino/higher_lower_b.png" name="higher_lower"
                                style="border-color: #28282A" border="2"></a>
                        <br>HIGHER/LOWER
                    </td>
                </tr>

                <tr>
                    <td><br><br></td>
                    <td></td>
                    <td></td>
                </tr>

                <tr>
                    <td width='110' height='110' valign='middle' align='center' bgcolor="#28282A">
                        <a href="ptslottery.php" title="Buy a lottery ticket for a chance to win the pot!"
                            onmouseover="document.lottery.src='images/casino/craps.png'"
                            onmouseout="document.lottery.src='images/casino/craps_b.png'">
                            <img src="images/casino/lottery_b.png" name="lottery" style="border-color: #28282A"
                                border="2"></a>
                        <br>POINTS LOTTERY
                    </td>
                    <td width='110' height='110' valign='middle' align='center' bgcolor="#28282A">
                        <a href="cashlottery.php" title="Try your luck for a chance to win the Jackpot!"
                            onmouseover="document.lottery_jackpot.src='images/casino/lottery_jackpot.png'"
                            onmouseout="document.lottery_jackpot.src='images/casino/lottery_jackpot_b.png'">
                            <img src="images/casino/lottery_jackpot_b.png" name="Points Lottery"
                                style="border-color: #28282A" border="2"></a>
                        <br>MONEY LOTTERY
                    </td>
                    <td width='110' height='110' valign='middle' align='center' bgcolor="#28282A">
                        <a href="numbergame.php" title="Numbers Game Place a bet and wait for someone to match it!"
                            onmouseover="document.pss.src='images/casino/lottery.png'"
                            onmouseout="document.pss.src='images/casino/lottery_b.png'">
                            <img src="images/casino/lottery_b.png" name="pss" style="border-color: #28282A"
                                border="2"></a>
                        <br>Numbers Game
                    </td>

                </tr>

                <tr>
                    <td><br><br></td>
                    <td></td>
                    <td></td>
                </tr>


            </table>


            <br><br><br>

            <a href='city.php'>
                <font color=red>Back to town</font>
            </a>
        </div><!--contentcontent-->
        <div class="contentfoot"></div><!--contentfoot-->

        <?php

        include 'footer.php';
        ?>