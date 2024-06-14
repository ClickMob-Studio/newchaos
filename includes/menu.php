<h1>Poker navigation menu</h1>
<div class="contentBox">
    <table width="100%">
        <Tr>
            <td width="50%" align="left"><a href='poker.php?view=index'><?php echo $view->MENU_HOME; ?></a>&nbsp;
                <?php if ($view->valid == true) { ?>
                    |&nbsp;<a href='poker.php?view=lobby'><?php echo $view->MENU_LOBBY; ?></a>&nbsp;
                                                                                              |&nbsp;<a
                            href='poker.php?view=myplayer'><?php echo $view->MENU_MYPLAYER; ?></a>&nbsp;
                <?php } ?>
                |&nbsp;<a href='poker.php?view=faq'><?php echo $view->MENU_FAQ; ?></a>&nbsp;
                <?php if ($view->ADMIN == true) { ?>
                    |&nbsp;<a href='pokerAdmin.php'><?php echo $view->MENU_ADMIN; ?></a>
                <?php } ?></td>
            <td width="50%" align="right"><?php echo $view->MENU_POKERMONEY .
                    ':  ' .
                    $view->userWinpot; ?></td>
        </Tr>
    </table>
</div>

<h1>ScoreBoard</h1>
<div class="contentBox">
    <table class="cleanTable" align="center" width="60%">
        <tr>
            <th class="headerCell" align="right" width="20%">Rank</th>
            <th class="headerCell" align="center" width="60%">Name</th>
            <th class="headerCell" align="right" width="20%">Win</th>
        </tr>
        <?php
        $arrScore = $view->arrScore;

        $intTotalScore = count($arrScore);
        for ($intM = 0; $intM < $intTotalScore; ++$intM) { ?>
            <tr>
                <td align="right">
                    <?php if ($intM == 0): ?>
                        <img width="16" height="16" src="images/buttons/rank1.png">
                    <?php elseif ($intM == 1): ?>
                        <img width="16" height="16" src="images/buttons/rank2.png">
                    <?php elseif ($intM == 2): ?>
                        <img width="16" height="16" src="images/buttons/rank3.png">
                    <?php endif; ?>
                    #<?php echo $intM + 1; ?>
                </td>
                <td class="dottedColumn" align="center"><a
                            href="profiles.php?id=<?php echo $arrScore[$intM]['user_id']; ?>"><?php echo ucfirst($arrScore[$intM]['name']); ?></a>
                </td>
                <td class="dottedColumn" align="right">  <?php echo $arrScore[$intM]['win']; ?></td>
            </tr>
        <?php }
        ?>

    </table>
</div>