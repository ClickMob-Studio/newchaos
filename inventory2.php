<?php

include 'header.php';

// $resultlimit = $mysql->query("SELECT * FROM (SELECT SUM(inventory.quantity) as totalinv FROM inventory WHERE inventory.userid = '".$user_class->id."') as t1, (SELECT SUM(inventorycp.quantity) as totalinvcp FROM inventorycp WHERE inventorycp.userid = '".$user_class->id."') as t2");
// $workedlimit = mysql_fetch_array($resultlimit);
// $totalinventory = $workedlimit['totalinv'] + $workedlimit['totalinvcp'];
// if ($user_class->eqweapon != 0) { $totalinventory += 1; }
// if ($user_class->eqarmor != 0) { $totalinventory += 1; }
// if ($user_class->eqshoe != 0) { $totalinventory += 1; }
// if ($user_class->eqgloves != 0) { $totalinventory += 1; }
// if ($user_class->eqhat != 0) { $totalinventory += 1; }

// print_r($user_class);

?>
<div class="contenthead">Your Inventory</div><!--contenthead-->
<div class="contentcontent">Everything you have collected. <a href='mastery.php'>Click here to visit the Mastery Room.</a></div><!--contentcontent--><div class="contentfoot"></div><!--contentfoot-->
<div class="contenthead">Equipped</div><!--contenthead-->
<div class="contentcontent">
<center>
<!-- <b>Inventory Usage: -->
<?php
    // if ($totalinventory >= $user_class->inventorylimit) { echo "<font color='#FF0000'>" . $totalinventory . "</font>/" . $user_class->inventorylimit; }
    //     else { echo $totalinventory . "/" . $user_class->inventorylimit; }
?>
</b><br>
</center>
<br />
    <center>
    <div style="background-image:url('/images/inventory2.png');width:350px;height:438px;position:relative;">
        <?php if ($user_class->eqweapon != 0) { ?>
            <div style='position:absolute;left:17px;top:142px;filter:alpha(opacity=85);-moz-opacity:0.85;opacity:0.85;'><a href='equip.php?unequip=weapon'><img src="<?= $user_class->weaponimg ?>" alt="<?= $user_class->weaponname ?>" title="<?= $user_class->weaponname ?>" border="1"></a><br />[<a href='equip.php?unequip=weapon'>unequip</a>]</div>
        <?php } else { ?>
            <div style='position:absolute;left:63px;top:182px;filter:alpha(opacity=85);-moz-opacity:0.85;opacity:0.85;'><b>x</b></div>
        <?php } ?>

        <?php if ($user_class->eqarmor != 0) { ?>
            <div style='position:absolute;left:126px;top:142px;filter:alpha(opacity=85);-moz-opacity:0.85;opacity:0.85;'><a href='equip.php?unequip=armor'><img src="<?= $user_class->armorimg ?>" alt="<?= $user_class->armorimg ?>" title="<?= $user_class->armorname ?>" border="1"></a><br />[<a href='equip.php?unequip=armor'>unequip</a>]</div>
        <?php } else { ?>
            <div style='position:absolute;left:173px;top:182px;filter:alpha(opacity=85);-moz-opacity:0.85;opacity:0.85;'><b>x</b></div>
        <?php } ?>

        <?php if ($user_class->eqshoe != 0) { ?>
            <div style='position:absolute;left:126px;top:326px;filter:alpha(opacity=85);-moz-opacity:0.85;opacity:0.85;'><a href='equip.php?unequip=shoes'><img src="<?= $user_class->shoeimg ?>" alt="<?= $user_class->shoename ?>" title="<?= $user_class->shoename ?>" border="1"></a><br />[<a href='equip.php?unequip=shoes'>unequip</a>]</div>
        <?php } else { ?>
            <div style='position:absolute;left:173px;top:363px;filter:alpha(opacity=85);-moz-opacity:0.85;opacity:0.85;'><b>x</b></div>
        <?php } ?>

        <?php if ($user_class->eqgloves != 0) { ?>
            <div style='position:absolute;left:236px;top:142px;filter:alpha(opacity=85);-moz-opacity:0.85;opacity:0.85;'><a href='equip.php?unequip=gloves'><img src="<?= $user_class->glovesimg ?>" alt="<?= $user_class->glovesname ?>" title="<?= $user_class->glovesname ?>" border="1"></a><br />[<a href='equip.php?unequip=gloves'>unequip</a>]</div>
        <?php } else { ?>
            <div style='position:absolute;left:283px;top:182px;filter:alpha(opacity=85);-moz-opacity:0.85;opacity:0.85;'><b>x</b></div>
        <?php } ?>

        <?php if ($user_class->eqhat != 0) { ?>
            <div style='position:absolute;left:126px;top:18px;filter:alpha(opacity=85);-moz-opacity:0.85;opacity:0.85;'><a href='equip.php?unequip=hat'><img src="<?= $user_class->hatimg ?>" alt="<?= $user_class->hatname ?>" title="<?= $user_class->hatname ?>" border="1"></a><br />[<a href='equip.php?unequip=hat'>unequip</a>]</div>
        <?php } else { ?>
            <div style='position:absolute;left:175px;top:58px;filter:alpha(opacity=85);-moz-opacity:0.85;opacity:0.85;'><b>x</b></div>
        <?php } ?>

        <?php if (($user_class->eqweapon == 12) && ($user_class->eqarmor == 70) && ($user_class->eqshoe == 110) && ($user_class->eqgloves == 150) && ($user_class->eqhat == 134)) { ?>
            <div style='position:absolute;left:240px;top:30px;filter:alpha(opacity=85);-moz-opacity:0.85;opacity:0.85;'><img src="images/set_street_punk.png" alt="Street Punk FULL Set" title="Street Punk FULL Set" border="0"></div>
        <?php } elseif (($user_class->eqweapon == 17) && ($user_class->eqarmor == 89) && ($user_class->eqshoe == 116) && ($user_class->eqgloves == 156) && ($user_class->eqhat == 130)) { ?>
            <div style='position:absolute;left:240px;top:30px;filter:alpha(opacity=85);-moz-opacity:0.85;opacity:0.85;'><img src="images/set_silent_assassin.png" alt="Silent Assassin FULL Set" title="Silent Assassin FULL Set" border="0"></div>
        <?php } elseif (($user_class->eqweapon == 29) && ($user_class->eqarmor == 80) && ($user_class->eqshoe == 113) && ($user_class->eqgloves == 159) && ($user_class->eqhat == 133)) { ?>
            <div style='position:absolute;left:240px;top:30px;filter:alpha(opacity=85);-moz-opacity:0.85;opacity:0.85;'><img src="images/set_classic_gangster.png" alt="Classic Gangster FULL Set" title="Classic Gangster FULL Set" border="0"></div>
        <?php } elseif (($user_class->eqweapon == 27) && ($user_class->eqarmor == 82) && ($user_class->eqshoe == 119) && ($user_class->eqgloves == 158) && ($user_class->eqhat == 138)) { ?>
            <div style='position:absolute;left:240px;top:30px;filter:alpha(opacity=85);-moz-opacity:0.85;opacity:0.85;'><img src="images/set_combat_marine.png" alt="Combat Marine FULL Set" title="Combat Marine FULL Set" border="0"></div>
        <?php } elseif (($user_class->eqweapon == 48) && ($user_class->eqarmor == 90) && ($user_class->eqshoe == 120) && ($user_class->eqgloves == 160) && ($user_class->eqhat == 140)) { ?>
            <div style='position:absolute;left:240px;top:30px;filter:alpha(opacity=85);-moz-opacity:0.85;opacity:0.85;'><img src="images/set_halloween.png" alt="Halloween FULL Set" title="Halloween FULL Set" border="0"></div>
        <?php } elseif (($user_class->eqweapon == 49) && ($user_class->eqarmor == 91) && ($user_class->eqshoe == 121) && ($user_class->eqgloves == 161) && ($user_class->eqhat == 141)) { ?>
            <div style='position:absolute;left:240px;top:30px;filter:alpha(opacity=85);-moz-opacity:0.85;opacity:0.85;'><img src="images/set_valentines_day.png" alt="Valentine's Day FULL Set" title="Valentine's Day FULL Set" border="0"></div>
        <?php } elseif (($user_class->eqweapon == 50) && ($user_class->eqarmor == 92) && ($user_class->eqshoe == 122) && ($user_class->eqgloves == 162) && ($user_class->eqhat == 142)) { ?>
            <div style='position:absolute;left:240px;top:30px;filter:alpha(opacity=85);-moz-opacity:0.85;opacity:0.85;'><img src="images/set_christmas.png" alt="Christmas FULL Set" title="Christmas FULL Set" border="0"></div>
        <?php } ?>

        <?php

            $resultcar = $mysql->query("SELECT carid FROM `cars` WHERE `userid` = '".$user_class->id."' and `isonmarket` = '0' ORDER BY `carid` DESC LIMIT 1");
            $workedcar = mysql_fetch_array($resultcar);
            if ($workedcar['carid'] != "") {
                $resultcar2 = $mysql->query("SELECT * FROM carlot WHERE id = '".$workedcar['carid']."'");
                $workedcar2 = mysql_fetch_array($resultcar2);
        ?>
            <div style='position:absolute;left:236px;top:326px;filter:alpha(opacity=85);-moz-opacity:0.85;opacity:0.85;'><img src="<?= $workedcar2['image'] ?>" alt="<?= $workedcar2['name'] ?>" title="<?= $workedcar2['name'] ?>" border="0"></div>
        <?php  } else { ?>
            <div style='position:absolute;left:283px;top:357px;filter:alpha(opacity=85);-moz-opacity:0.85;opacity:0.85;'><b>x</b></div>
        <?php } ?>

        <?php

            $resultpet = $mysql->query("SELECT type FROM `grpgpets` WHERE `userid` = '".$user_class->id."' and `isonmarket` = '0' and `leashed` = '1'");
            $workedpet = mysql_fetch_array($resultpet);
            if ($workedpet['type'] != "") {
                $resultpet2 = $mysql->query("SELECT * FROM petstore WHERE name = '".$workedpet['type']."'");
                $workedpet2 = mysql_fetch_array($resultpet2);
        ?>
            <div style='position:absolute;left:18px;top:326px;filter:alpha(opacity=85);-moz-opacity:0.85;opacity:0.85;'><img src="<?= $workedpet2['image'] ?>" alt="<?= $workedpet2['name'] ?>" title="<?= $workedpet2['name'] ?>" border="0"></div>
        <?php  } else { ?>
            <div style='position:absolute;left:63px;top:357px;filter:alpha(opacity=85);-moz-opacity:0.85;opacity:0.85;'><b>x</b></div>
        <?php } ?>

    </div>
    <br />
    &nbsp;
    <form method='post' id="theForm">
    <table width='80%' id='cleanTable' align='center'>
        <tr>
            <td width='15%' style="text-align:left;"><b>Strength:</b></td>
            <td width='40%' style="text-align:left;"><?php echo number_format($user_class->strength);?> [<?php echo number_format($user_class->moddedstrength);?>]</td>
            <td style="text-align:center;">
            <?php

                    $result11 = $mysql->query("SELECT * FROM `inventory_save` WHERE `userid` = '".$_SESSION['id']."' LIMIT 1");
                    $worked1 = mysql_fetch_array($result11);
            ?>
                    <b><a href="equip.php?unequip=uall">[Unequip All]</a></b>
            </td>
        </tr>
        <tr>
            <td style="text-align:left;"><b>Defense:</b></td>
            <td style="text-align:left;"><?php echo number_format($user_class->defense);?> [<?php echo number_format($user_class->moddeddefense);?>]</td>
            <td style="text-align:center;">
            <?php

                    if ($worked1['slot1name'] != "") {
                        echo '<a href="equip.php?equipsave=1">'.$worked1['slot1name'].'</a>';
                    } else {
                        echo '<a href="#">[empty]</a>';
                    }
            ?>
            </td>
        </tr>
        <tr>
            <td style="text-align:left;"><b>Speed</b>:</td>
            <td style="text-align:left;"><?php echo number_format($user_class->speed);?> [<?php echo number_format($user_class->moddedspeed);?>]</td>
            <td style="text-align:center;">
            <?php

                    if ($worked1['slot2name'] != "") {
                        echo '<a href="equip.php?equipsave=2">'.$worked1['slot2name'].'</a>';
                    } else {
                        echo '<a href="#">[empty]</a>';
                    }
            ?>
            </td>
        </tr>
        <tr>
            <td style="text-align:left;"><b>Dexterity:</b></td>
            <td style="text-align:left;"><?php echo number_format($user_class->dexterity);?> [<?php echo number_format($user_class->moddeddexterity);?>]</td>
            <td style="text-align:center;">
            <?php

                    if ($worked1['slot3name'] != "") {
                        echo '<a href="equip.php?equipsave=3">'.$worked1['slot3name'].'</a>';
                    } else {
                        echo '<a href="#">[empty]</a>';
                    }
            ?>
            </td>
        </tr>
        <tr>
            <td style="text-align:left;"><b><i>Total</i></b>:</td>
            <td style="text-align:left;"><i><?php echo number_format($user_class->totalattrib);?> [<?php echo number_format($user_class->totalmodded);?>]</i></td>
            <td style="text-align:center;">
            <?php

                    if ($worked1['slot4name'] != "") {
                        echo '<a href="equip.php?equipsave=4">'.$worked1['slot4name'].'</a>';
                    } else {
                        echo '<a href="#">[empty]</a>';
                    }
            ?>
            </td>
        </tr>
        <tr>
            <td style="text-align:left;"><b>HP:</b></td>
            <td style="text-align:left;"><?php echo number_format($user_class->originalmaxhp);?> [<?php echo number_format($user_class->maxhp);?>]</td>
            <td style="text-align:center;">
                Save
                <select name="numberSlot" style="font-size: 10px" class="field_box">
                    <option <?php if ($_POST['numberSlot'] == "1") { echo "selected"; } ?> value ="1">1</option>
                    <option <?php if ($_POST['numberSlot'] == "2") { echo "selected"; } ?> value ="2">2</option>
                    <option <?php if ($_POST['numberSlot'] == "3") { echo "selected"; } ?> value ="3">3</option>
                    <option <?php if ($_POST['numberSlot'] == "4") { echo "selected"; } ?> value ="4">4</option>
                </select>, name:
                <input style="font-size: 10px" type="text" value="<?php echo $_POST['saveName']; ?>" maxlength="11" size="10" name="saveName" />&nbsp;&nbsp;
                <input type="submit" name='saveSlot' value='Go' />
            </td>
        </tr>
    </table>
    </center>
    </form>

</div><!--contentcontent--><div class="contentfoot"></div><!--contentfoot-->

<?php

//$result = $mysql->query("SELECT * FROM `inventory` WHERE `userid` = '".$user_class->id."' ORDER BY `itemid` ASC");
$result = $mysql->query("SELECT inventory.itemid, inventory.quantity, inventory.loaned, items.id, items.itemname, items.image, items.cost, items.description, items.tradable, mastery.mastery_points, mastery_list.mastery_points_required from inventory left join items on items.id=inventory.itemid left join mastery on (mastery.itemid=inventory.itemid and mastery.userid='".$user_class->id."') left join mastery_list on mastery_list.itemid=inventory.itemid where inventory.userid='".$user_class->id."' ORDER BY inventory.itemid ASC");
$howmanyweps = 0;
$howmanyarmors = 0;
$howmanygloves = 0;
$howmanyhats = 0;
$howmanyshoes = 0;
$howmanymisc = 0;
$howmanydrugs = 0;
$howmanyspecials = 0;

while($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
//	    $result2 = $mysql->query("SELECT * FROM `items` WHERE `id`='".$line['itemid']."' LIMIT 1");
//        $worked2 = mysql_fetch_array($result2);

        if ((($line['id'] >= 10) && ($line['id'] <= 50)) || (($line['id'] >= 70) && ($line['id'] <= 92)) || (($line['id'] >= 110) && ($line['id'] <= 122)) || (($line['id'] >= 150) && ($line['id'] <= 162)) || (($line['id'] >= 130) && ($line['id'] <= 142))) {
//            $result4 = $mysql->query("SELECT mastery.userid, mastery.itemid, mastery.mastery_points, mastery_list.mastery_points_required FROM `mastery` LEFT JOIN `mastery_list` ON mastery.itemid = mastery_list.itemid where mastery.userid = '".$user_class->id."' and mastery.itemid = '".$line['id']."' LIMIT 1");
            if ($line['mastery_points'] >= 0) {
//                $workedmastery = mysql_fetch_array($result4);
                if ($line['mastery_points'] == 0) { $itemmastery = 0; }
                    else { $itemmastery = round(($line['mastery_points'] * 100) / $line['mastery_points_required']); }
            }
            if (is_null($line['mastery_points'])) {
                $result5 = $mysql->query("INSERT INTO `mastery` VALUES ('', '".$user_class->id."', '".$line['id']."', '0')");
                $itemmastery = 0;
            }
        }

		if (($line['id'] >= 10) && ($line['id'] <= 50)) {
            $howmanyweps++;
		    $sell = ($line['cost'] > 0) ? "<a href='sellitem.php?id=".$line['id']."'>[Sell]</a>" : "";
            if ($line['loaned'] == 1) {
                $loanweapons .= "
                <tr>
                    <td align='left' id='dottedRow'>
                        <table id='cleanTable' width='100%' style='margin: 1px; padding: 2px;'>
                            <tr>
                                <td rowspan='2' valign='top' width='80'><img src='". $line['image']."' style='border: 1px solid #333333' title='".$line['itemname']."' width='50px' height='50px'></td>
                                <td valign='middle' width='400'>". item_popup($line['itemname'], $line['id']) ." [x".$line['quantity']."]</td>
                                <td valign='middle'><a href='inventory.php?action=return&id=".$line['id']."'>[Return]</a> <a href='equip.php?eq=weapon&loaned=1&id=".$line['id']."'>[Equip]</a></td>
                            </tr>
                            <tr>
                                <td valign='top'>Quality: 100%</td>
                                <td valign='top'><a href='mastery.php'>Mastery</a>: ".$itemmastery."%</td>
                            </tr>
                        </table>
                    </td>
                </tr>";
            } elseif ($line['tradable'] == 1) {
                $weapons .= "
                <tr>
                    <td align='left' id='dottedRow'>
                        <table id='cleanTable' width='100%' style='margin: 1px; padding: 2px;'>
                            <tr>
                                <td rowspan='2' valign='top' width='80'><img src='". $line['image']."' style='border: 1px solid #333333' title='".$line['itemname']."' width='50px' height='50px'></td>
                                <td valign='middle' width='400'>". item_popup($line['itemname'], $line['id']) ." [x".$line['quantity']."]</td>
                                <td valign='middle'>$sell <a href='putonmarket.php?id=".$line['id']."'>[Market]</a> <a href='senditem.php?id=".$line['id']."'>[Send]</a> <a href='equip.php?eq=weapon&id=".$line['id']."'>[Equip]</a></td>
                            </tr>
                            <tr>
                                <td valign='top'>Quality: 100%</td>
                                <td valign='top'><a href='mastery.php'>Mastery</a>: ".$itemmastery."%</td>
                            </tr>
                        </table>
                    </td>
                </tr>";
            } elseif ($line['tradable'] == 0) {
                $weapons .= "
                <tr>
                    <td align='left' id='dottedRow'>
                        <table id='cleanTable' width='100%' style='margin: 1px; padding: 2px;'>
                            <tr>
                                <td rowspan='2' valign='top' width='80'><img src='". $line['image']."' style='border: 1px solid #333333' title='".$line['itemname']."' width='50px' height='50px'></td>
                                <td valign='middle' width='400'>". item_popup($line['itemname'], $line['id']) ." [x".$line['quantity']."]</td>
                                <td valign='middle'><a href='equip.php?eq=weapon&id=".$line['id']."'>[Equip]</a></td>
                            </tr>
                            <tr>
                                <td valign='top'>Quality: 100%</td>
                                <td valign='top'><a href='mastery.php'>Mastery</a>: ".$itemmastery."%</td>
                            </tr>
                        </table>
                    </td>
                </tr>";
            }
        }

		if ((($line['id'] >= 70) && ($line['id'] <= 92)) || ($line['id'] == '178')) {
            $howmanyarmors++;
		    $sell = ($line['cost'] > 0) ? "<a href='sellitem.php?id=".$line['id']."'>[Sell]</a>" : "";
            if($line['loaned'] == 1) {
                $loanarmors .= "
                <tr>
                    <td align='left' id='dottedRow'>
                        <table id='cleanTable' width='100%' style='margin: 1px; padding: 2px;'>
                            <tr>
                                <td rowspan='2' valign='top' width='80'><img src='". $line['image']."' style='border: 1px solid #333333' title='".$line['itemname']."' width='50px' height='50px'></td>
                                <td valign='middle' width='400'>". item_popup($line['itemname'], $line['id']) ." [x".$line['quantity']."]</td>
                                <td valign='middle'><a href='inventory.php?action=return&id=".$line['id']."'>[Return]</a> <a href='equip.php?eq=armor&loaned=1&id=".$line['id']."'>[Equip]</a></td>
                            </tr>
                            <tr>
                                <td valign='top'>Quality: 100%</td>
                                <td valign='top'><a href='mastery.php#tabs-2'>Mastery</a>: ".$itemmastery."%</td>
                            </tr>
                        </table>
                    </td>
                </tr>";
            } elseif ($line['tradable'] == 1) {
                $armors .= "
                <tr>
                    <td align='left' id='dottedRow'>
                        <table id='cleanTable' width='100%' style='margin: 1px; padding: 2px;'>
                            <tr>
                                <td rowspan='2' valign='top' width='80'><img src='". $line['image']."' style='border: 1px solid #333333' title='".$line['itemname']."' width='50px' height='50px'></td>
                                <td valign='middle' width='400'>". item_popup($line['itemname'], $line['id']) ." [x".$line['quantity']."]</td>
                                <td valign='middle'>$sell <a href='putonmarket.php?id=".$line['id']."'>[Market]</a> <a href='senditem.php?id=".$line['id']."'>[Send]</a> <a href='equip.php?eq=armor&id=".$line['id']."'>[Equip]</a></td>
                            </tr>
                            <tr>
                                <td valign='top'>Quality: 100%</td>
                                <td valign='top'><a href='mastery.php#tabs-2'>Mastery</a>: ".$itemmastery."%</td>
                            </tr>
                        </table>
                    </td>
                </tr>";
            } elseif ($line['tradable'] == 0) {
                $armors .= "
                <tr>
                    <td align='left' id='dottedRow'>
                        <table id='cleanTable' width='100%' style='margin: 1px; padding: 2px;'>
                            <tr>
                                <td rowspan='2' valign='top' width='80'><img src='". $line['image']."' style='border: 1px solid #333333' title='".$line['itemname']."' width='50px' height='50px'></td>
                                <td valign='middle' width='400'>". item_popup($line['itemname'], $line['id']) ." [x".$line['quantity']."]</td>
                                <td valign='middle'><a href='equip.php?eq=armor&id=".$line['id']."'>[Equip]</a></td>
                            </tr>
                            <tr>
                                <td valign='top'>Quality: 100%</td>
                                <td valign='top'><a href='mastery.php#tabs-2'>Mastery</a>: ".$itemmastery."%</td>
                            </tr>
                        </table>
                    </td>
                </tr>";
            }
		}

		if (($line['id'] >= 110) && ($line['id'] <= 122)) {
            $howmanyshoes++;
		    $sell = ($line['cost'] > 0) ? "<a href='sellitem.php?id=".$line['id']."'>[Sell]</a>" : "";
            if($line['loaned'] == 1) {
                $loanshoes .= "
                <tr>
                    <td align='left' id='dottedRow'>
                        <table id='cleanTable' width='100%' style='margin: 1px; padding: 2px;'>
                            <tr>
                                <td rowspan='2' valign='top' width='80'><img src='". $line['image']."' style='border: 1px solid #333333' title='".$line['itemname']."' width='50px' height='50px'></td>
                                <td valign='middle' width='400'>". item_popup($line['itemname'], $line['id']) ." [x".$line['quantity']."]</td>
                                <td valign='middle'><a href='inventory.php?action=return&id=".$line['id']."'>[Return]</a> <a href='equip.php?eq=shoe&loaned=1&id=".$line['id']."'>[Equip]</a></td>
                            </tr>
                            <tr>
                                <td valign='top'>Quality: 100%</td>
                                <td valign='top'><a href='mastery.php#tabs-3'>Mastery</a>: ".$itemmastery."%</td>
                            </tr>
                        </table>
                    </td>
                </tr>";
            } elseif ($line['tradable'] == 1) {
                $shoes .= "
                <tr>
                    <td align='left' id='dottedRow'>
                        <table id='cleanTable' width='100%' style='margin: 1px; padding: 2px;'>
                            <tr>
                                <td rowspan='2' valign='top' width='80'><img src='". $line['image']."' style='border: 1px solid #333333' title='".$line['itemname']."' width='50px' height='50px'></td>
                                <td valign='middle' width='400'>". item_popup($line['itemname'], $line['id']) ." [x".$line['quantity']."]</td>
                                <td valign='middle'>$sell <a href='putonmarket.php?id=".$line['id']."'>[Market]</a> <a href='senditem.php?id=".$line['id']."'>[Send]</a> <a href='equip.php?eq=shoe&id=".$line['id']."'>[Equip]</a></td>
                            </tr>
                            <tr>
                                <td valign='top'>Quality: 100%</td>
                                <td valign='top'><a href='mastery.php#tabs-3'>Mastery</a>: ".$itemmastery."%</td>
                            </tr>
                        </table>
                    </td>
                </tr>";
            } elseif ($line['tradable'] == 0) {
                $shoes .= "
                <tr>
                    <td align='left' id='dottedRow'>
                        <table id='cleanTable' width='100%' style='margin: 1px; padding: 2px;'>
                            <tr>
                                <td rowspan='2' valign='top' width='80'><img src='". $line['image']."' style='border: 1px solid #333333' title='".$line['itemname']."' width='50px' height='50px'></td>
                                <td valign='middle' width='400'>". item_popup($line['itemname'], $line['id']) ." [x".$line['quantity']."]</td>
                                <td valign='middle'><a href='equip.php?eq=shoe&id=".$line['id']."'>[Equip]</a></td>
                            </tr>
                            <tr>
                                <td valign='top'>Quality: 100%</td>
                                <td valign='top'><a href='mastery.php#tabs-3'>Mastery</a>: ".$itemmastery."%</td>
                            </tr>
                        </table>
                    </td>
                </tr>";
            }
		}

        if (($line['id'] >= 150) && ($line['id'] <= 162)) {
            $howmanygloves++;
            $sell = ($line['cost'] > 0) ? "<a href='sellitem.php?id=".$line['id']."'>[Sell]</a>" : "";
            if($line['loaned'] == 1) {
                $loangloves .= "
                <tr>
                    <td align='left' id='dottedRow'>
                        <table id='cleanTable' width='100%' style='margin: 1px; padding: 2px;'>
                            <tr>
                                <td rowspan='2' valign='top' width='80'><img src='". $line['image']."' style='border: 1px solid #333333' title='".$line['itemname']."' width='50px' height='50px'></td>
                                <td valign='middle' width='400'>". item_popup($line['itemname'], $line['id']) ." [x".$line['quantity']."]</td>
                                <td valign='middle'><a href='inventory.php?action=return&id=".$line['id']."'>[Return]</a> <a href='equip.php?eq=gloves&loaned=1&id=".$line['id']."'>[Equip]</a></td>
                            </tr>
                            <tr>
                                <td valign='top'>Quality: 100%</td>
                                <td valign='top'><a href='mastery.php#tabs-4'>Mastery</a>: ".$itemmastery."%</td>
                            </tr>
                        </table>
                    </td>
                </tr>";
            } elseif ($line['tradable'] == 1) {
                $gloves .= "
                <tr>
                    <td align='left' id='dottedRow'>
                        <table id='cleanTable' width='100%' style='margin: 1px; padding: 2px;'>
                            <tr>
                                <td rowspan='2' valign='top' width='80'><img src='". $line['image']."' style='border: 1px solid #333333' title='".$line['itemname']."' width='50px' height='50px'></td>
                                <td valign='middle' width='400'>". item_popup($line['itemname'], $line['id']) ." [x".$line['quantity']."]</td>
                                <td valign='middle'>$sell <a href='putonmarket.php?id=".$line['id']."'>[Market]</a> <a href='senditem.php?id=".$line['id']."'>[Send]</a> <a href='equip.php?eq=gloves&id=".$line['id']."'>[Equip]</a></td>
                            </tr>
                            <tr>
                                <td valign='top'>Quality: 100%</td>
                                <td valign='top'><a href='mastery.php#tabs-4'>Mastery</a>: ".$itemmastery."%</td>
                            </tr>
                        </table>
                    </td>
                </tr>";
            } elseif ($line['tradable'] == 0) {
                $gloves .= "
                <tr>
                    <td align='left' id='dottedRow'>
                        <table id='cleanTable' width='100%' style='margin: 1px; padding: 2px;'>
                            <tr>
                                <td rowspan='2' valign='top' width='80'><img src='". $line['image']."' style='border: 1px solid #333333' title='".$line['itemname']."' width='50px' height='50px'></td>
                                <td valign='middle' width='400'>". item_popup($line['itemname'], $line['id']) ." [x".$line['quantity']."]</td>
                                <td valign='middle'><a href='equip.php?eq=gloves&id=".$line['id']."'>[Equip]</a></td>
                            </tr>
                            <tr>
                                <td valign='top'>Quality: 100%</td>
                                <td valign='top'><a href='mastery.php#tabs-4'>Mastery</a>: ".$itemmastery."%</td>
                            </tr>
                        </table>
                    </td>
                </tr>";
            }
        }

        if (($line['id'] >= 130) && ($line['id'] <= 142)) {
            $howmanyhats++;
            $sell = ($line['cost'] > 0) ? "<a href='sellitem.php?id=".$line['id']."'>[Sell]</a>" : "";
            if($line['loaned'] == 1) {
                $loanhats .= "
                <tr>
                    <td align='left' id='dottedRow'>
                        <table id='cleanTable' width='100%' style='margin: 1px; padding: 2px;'>
                            <tr>
                                <td rowspan='2' valign='top' width='80'><img src='". $line['image']."' style='border: 1px solid #333333' title='".$line['itemname']."' width='50px' height='50px'></td>
                                <td valign='middle' width='400'>". item_popup($line['itemname'], $line['id']) ." [x".$line['quantity']."]</td>
                                <td valign='middle'><a href='inventory.php?action=return&id=".$line['id']."'>[Return]</a> <a href='equip.php?eq=hat&loaned=1&id=".$line['id']."'>[Equip]</a></td>
                            </tr>
                            <tr>
                                <td valign='top'>Quality: 100%</td>
                                <td valign='top'><a href='mastery.php#tabs-5'>Mastery</a>: ".$itemmastery."%</td>
                            </tr>
                        </table>
                    </td>
                </tr>";
            } elseif ($line['tradable'] == 1) {
                $hats .= "
                <tr>
                    <td align='left' id='dottedRow'>
                        <table id='cleanTable' width='100%' style='margin: 1px; padding: 2px;'>
                            <tr>
                                <td rowspan='2' valign='top' width='80'><img src='". $line['image']."' style='border: 1px solid #333333' title='".$line['itemname']."' width='50px' height='50px'></td>
                                <td valign='middle' width='400'>". item_popup($line['itemname'], $line['id']) ." [x".$line['quantity']."]</td>
                                <td valign='middle'>$sell <a href='putonmarket.php?id=".$line['id']."'>[Market]</a> <a href='senditem.php?id=".$line['id']."'>[Send]</a> <a href='equip.php?eq=hat&id=".$line['id']."'>[Equip]</a></td>
                            </tr>
                            <tr>
                                <td valign='top'>Quality: 100%</td>
                                <td valign='top'><a href='mastery.php#tabs-5'>Mastery</a>: ".$itemmastery."%</td>
                            </tr>
                        </table>
                    </td>
                </tr>";
            } elseif ($line['tradable'] == 0) {
                $hats .= "
                <tr>
                    <td align='left' id='dottedRow'>
                        <table id='cleanTable' width='100%' style='margin: 1px; padding: 2px;'>
                            <tr>
                                <td rowspan='2' valign='top' width='80'><img src='". $line['image']."' style='border: 1px solid #333333' title='".$line['itemname']."' width='50px' height='50px'></td>
                                <td valign='middle' width='400'>". item_popup($line['itemname'], $line['id']) ." [x".$line['quantity']."]</td>
                                <td valign='middle'><a href='equip.php?eq=hat&id=".$line['id']."'>[Equip]</a></td>
                            </tr>
                            <tr>
                                <td valign='top'>Quality: 100%</td>
                                <td valign='top'><a href='mastery.php#tabs-5'>Mastery</a>: ".$itemmastery."%</td>
                            </tr>
                        </table>
                    </td>
                </tr>";
            }
        }

		if ($line['id'] == 170 || // Akake Pill
            $line['id'] == 190 || $line['id'] == 191 || $line['id'] == 192 || $line['id'] == 193 || // Medkits
            $line['id'] == 200 || $line['id'] == 201 || $line['id'] == 202 || $line['id'] == 203 || // Med Certs
            $line['id'] == 180 || // House Bomb
            $line['id'] == 186 || // Prison Key
            $line['id'] == 255 || $line['id'] == 257 || $line['id'] == 258 || // Infected Mobster Event
            $line['id'] == 93 || $line['id'] == 210 ||
            $line['id'] == 204 || $line['id'] == 205
            ) {
                if ($line['tradable'] == 1) {
                    $howmanymisc++;
                    $misc .= "
                    <tr>
                        <td align='left' id='dottedRow'>
                            <table id='cleanTable' width='100%' style='margin: 1px; padding: 2px;'>
                                <tr>
                                    <td rowspan='2' valign='top' width='80'><img src='". $line['image']."' style='border: 1px solid #333333' title='".$line['itemname']."' width='50px' height='50px'></td>
                                    <td valign='middle' width='400'>". item_popup($line['itemname'], $line['id']) ." [x".$line['quantity']."]</td>
                                    <td valign='middle'><a href='putonmarket.php?id=".$line['id']."'>[Market]</a> <a href='senditem.php?id=".$line['id']."'>[Send]</a> <a href='inventory.php?use=".$line['id']."'>[Use]</a></td>
                                </tr>
                                <tr>
                                    <td colspan='2'>".$line['description']."</td>
                                </tr>
                            </table>
                        </td>
                    </tr>";
                } elseif ($line['tradable'] == 0) {
                    $howmanymisc++;
                    $misc .= "
                    <tr>
                        <td align='left' id='dottedRow'>
                            <table id='cleanTable' width='100%' style='margin: 1px; padding: 2px;'>
                                <tr>
                                    <td rowspan='2' valign='top' width='80'><img src='". $line['image']."' style='border: 1px solid #333333' title='".$line['itemname']."' width='50px' height='50px'></td>
                                    <td valign='middle' width='400'>". item_popup($line['itemname'], $line['id']) ." [x".$line['quantity']."]</td>
                                    <td valign='middle'><a href='inventory.php?use=".$line['id']."'>[Use]</a></td>
                                </tr>
                                <tr>
                                    <td colspan='2'>".$line['description']."</td>
                                </tr>
                            </table>
                        </td>
                    </tr>";
                }
		}

        if ($line['id'] == 210) { // Fertilizer
            $howmanymisc++;
            $misc .= "
            <tr>
                <td align='left' id='dottedRow'>
                    <table id='cleanTable' width='100%' style='margin: 1px; padding: 2px;'>
                        <tr>
                            <td rowspan='2' valign='top' width='80'><img src='". $line['image']."' style='border: 1px solid #333333' title='".$line['itemname']."' width='50px' height='50px'></td>
                            <td valign='middle' width='400'>". item_popup($line['itemname'], $line['id']) ." [x".$line['quantity']."]</td>
                            <td valign='middle'><a href='putonmarket.php?id=".$line['id']."'>[Market]</a> <a href='senditem.php?id=".$line['id']."'>[Send]</a></td>
                        </tr>
                        <tr>
                            <td colspan='2'>".$line['description']."</td>
                        </tr>
                    </table>
                </td>
            </tr>";
        }

		if ($line['id'] == 171 || $line['id'] == 172 || $line['id'] == 173 || $line['id'] == 174 || $line['id'] == 175 || $line['id'] == 176 || $line['id'] == 177 || $line['id'] == 256) { // Drugs
            if ($line['tradable'] == 1) {
                $howmanydrugs++;
                $drugs .= "
                <tr>
                    <td align='left' id='dottedRow'>
                        <table id='cleanTable' width='100%' style='margin: 1px; padding: 2px;'>
                            <tr>
                                <td rowspan='2' valign='top' width='80'><img src='". $line['image']."' style='border: 1px solid #333333' title='".$line['itemname']."' width='50px' height='50px'></td>
                                <td valign='middle' width='400'>". item_popup($line['itemname'], $line['id']) ." [x".$line['quantity']."]</td>
                                <td valign='middle'><a href='putonmarket.php?id=".$line['id']."'>[Market]</a> <a href='senditem.php?id=".$line['id']."'>[Send]</a> <a href='inventory.php?use=".$line['id']."'>[Use]</a></td>
                            </tr>
                            <tr>
                                <td colspan='2'></td>
                            </tr>
                        </table>
                    </td>
                </tr>";
            } elseif ($line['tradable'] == 0) {
                $howmanydrugs++;
                $drugs .= "
                <tr>
                    <td align='left' id='dottedRow'>
                        <table id='cleanTable' width='100%' style='margin: 1px; padding: 2px;'>
                            <tr>
                                <td rowspan='2' valign='top' width='80'><img src='". $line['image']."' style='border: 1px solid #333333' title='".$line['itemname']."' width='50px' height='50px'></td>
                                <td valign='middle' width='400'>". item_popup($line['itemname'], $line['id']) ." [x".$line['quantity']."]</td>
                                <td valign='middle'><a href='inventory.php?use=".$line['id']."'>[Use]</a></td>
                            </tr>
                            <tr>
                                <td colspan='2'></td>
                            </tr>
                        </table>
                    </td>
                </tr>";
            }
		}

        if ($line['id'] == 182 || $line['id'] == 183 || $line['id'] == 184 || $line['id'] == 195 || $line['id'] == 196 || $line['id'] == 181 || $line['id'] == 185 || $line['id'] == 187 || $line['id'] == 188) { // Special Items
            if ($line['tradable'] == 1) {
                $howmanyspecials++;
                $special .= "
                <tr>
                    <td align='left' id='dottedRow'>
                        <table id='cleanTable' width='100%' style='margin: 1px; padding: 2px;'>
                            <tr>
                                <td rowspan='2' valign='top' width='80'><img src='". $line['image']."' style='border: 1px solid #333333' title='".$line['itemname']."' width='50px' height='50px'></td>
                                <td valign='middle' width='400'>". item_popup($line['itemname'], $line['id']) ." [x".$line['quantity']."]</td>
                                <td valign='middle'><a href='putonmarket.php?id=".$line['id']."'>[Market]</a> <a href='senditem.php?id=".$line['id']."'>[Send]</a> <a href='inventory.php?use=".$line['id']."' onclick='return confirm(\"Are you sure you would like to use the ".$line['itemname']." ?\")'>[Use]</a></td>
                            </tr>
                            <tr>
                                <td colspan='2'>".$line['description']."</td>
                            </tr>
                        </table>
                    </td>
                </tr>";
            } elseif ($line['tradable'] == 0) {
                $howmanyspecials++;
                $special .= "
                <tr>
                    <td align='left' id='dottedRow'>
                        <table id='cleanTable' width='100%' style='margin: 1px; padding: 2px;'>
                            <tr>
                                <td rowspan='2' valign='top' width='80'><img src='". $line['image']."' style='border: 1px solid #333333' title='".$line['itemname']."' width='50px' height='50px'></td>
                                <td valign='middle' width='400'>". item_popup($line['itemname'], $line['id']) ." [x".$line['quantity']."]</td>
                                <td valign='middle'><a href='inventory.php?use=".$line['id']."' onclick='return confirm(\"Are you sure you would like to use the ".$line['itemname']." ?\")'>[Use]</a></td>
                            </tr>
                            <tr>
                                <td colspan='2'>".$line['description']."</td>
                            </tr>
                        </table>
                    </td>
                </tr>";
            }
        }

        if ($line['id'] == 220 || $line['id'] == 221 || $line['id'] == 222 || $line['id'] == 223 || $line['id'] == 224) {
            if ($line['tradable'] == 1) {
                $howmanyspecials++;
                $special .= "
                <tr>
                    <td align='left' id='dottedRow'>
                        <table id='cleanTable' width='100%' style='margin: 1px; padding: 2px;'>
                            <tr>
                                <td rowspan='2' valign='top' width='80'><img src='". $line['image']."' style='border: 1px solid #333333' title='".$line['itemname']."' width='50px' height='50px'></td>
                                <td valign='middle' width='400'>". item_popup($line['itemname'], $line['id']) ." [x".$line['quantity']."]</td>
                                <td valign='middle'><a href='putonmarket.php?id=".$line['id']."'>[Market]</a> <a href='senditem.php?id=".$line['id']."'>[Send]</a></td>
                            </tr>
                            <tr>
                                <td colspan='2'>".$line['description']."</td>
                            </tr>
                        </table>
                    </td>
                </tr>";
            } elseif ($line['tradable'] == 0) {
                $howmanyspecials++;
                $special .= "
                <tr>
                    <td align='left' id='dottedRow'>
                        <table id='cleanTable' width='100%' style='margin: 1px; padding: 2px;'>
                            <tr>
                                <td rowspan='2' valign='top' width='80'><img src='". $line['image']."' style='border: 1px solid #333333' title='".$line['itemname']."' width='50px' height='50px'></td>
                                <td valign='middle' width='400'>". item_popup($line['itemname'], $line['id']) ." [x".$line['quantity']."]</td>
                                <td valign='middle'>
                            </tr>
                            <tr>
                                <td colspan='2'>".$line['description']."</td>
                            </tr>
                        </table>
                    </td>
                </tr>";
            }
        }

}

?>

<div class="contentcontent">

<br /><br />

<link rel="stylesheet" href="css/inventory.css" type="text/css">

<script>
    $(function() {
        $( "#tabs" ).tabs();
    });
</script>

<div class="demo">
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">Weapons [<?php if ($howmanyweps == 0) { echo "-"; } else { echo $howmanyweps; } ?>]</a></li>
        <li><a href="#tabs-2">Armors [<?php if ($howmanyarmors == 0) { echo "-"; } else { echo $howmanyarmors; } ?>]</a></li>
        <li><a href="#tabs-3">Shoes [<?php if ($howmanyshoes == 0) { echo "-"; } else { echo $howmanyshoes; } ?>]</a></li>
        <li><a href="#tabs-4">Gloves [<?php if ($howmanygloves == 0) { echo "-"; } else { echo $howmanygloves; } ?>]</a></li>
        <li><a href="#tabs-5">Hats [<?php if ($howmanyhats == 0) { echo "-"; } else { echo $howmanyhats; } ?>]</a></li>
        <li><a href="#tabs-6">Misc [<?php if ($howmanymisc == 0) { echo "-"; } else { echo $howmanymisc; } ?>]</a></li>
        <li><a href="#tabs-7">Drugs [<?php if ($howmanydrugs == 0) { echo "-"; } else { echo $howmanydrugs; } ?>]</a></li>
        <li><a href="#tabs-8">Special Items [<?php if ($howmanyspecials == 0) { echo "-"; } else { echo $howmanyspecials; } ?>]</a></li>
    </ul>
    <div id="tabs-1">
        <table width='100%'>
            <?php echo $loanweapons; ?>
            <?php echo $weapons; ?>
        </table>
    </div>
    <div id="tabs-2">
        <table width='100%'>
            <?php echo $loanarmors; ?>
            <?php echo $armors; ?>
        </table>
    </div>
    <div id="tabs-3">
        <table width='100%'>
            <?php echo $loanshoes; ?>
            <?php echo $shoes; ?>
        </table>
    </div>
    <div id="tabs-4">
        <table width='100%'>
            <?php echo $loangloves; ?>
            <?php echo $gloves; ?>
        </table>
    </div>
    <div id="tabs-5">
        <table width='100%'>
            <?php echo $loanhats; ?>
            <?php echo $hats; ?>
        </table>
    </div>
    <div id="tabs-6">
        <table width='100%'>
            <?php echo $misc; ?>
        </table>
    </div>
    <div id="tabs-7">
        <table width='100%'>
            <?php echo $drugs; ?>
        </table>
    </div>
    <div id="tabs-8">
        <table width='100%'>
            <?php echo $special; ?>
        </table>
    </div>
</div>
</div><!-- End demo -->
<br /><br />
</div><!--contentcontent--><div class="contentfoot"></div><!--contentfoot-->

<?php

include 'footer.php'
?>