<?php
include 'header.php';
echo'<h3>Referral System</h3>';
	echo'<hr>';
echo'<div class="floaty" style="width:75%;">';
	echo'<b>Your Referer Link:</b> ';
	echo'<input type="text" size="50" value="https://s2.TheMafiaLife.com/register.php?referer=' . $user_class->id . '" /><br />';
	echo'<br />';
	echo'<b>Reward:</b> <span style="color:yellow;">50 Credits + 100 Points</span> per referral.<br />';
	echo'<br />';
//	echo'You will also gain 10% of what your referred player spends, so you gain points if they spend credits.';
//	echo'<br />';
	echo'<br />';
	echo'You will receive your points only after we filter out multis. This is due to too many people abusing the referral system. ';
	echo'Because we have to do this manually now, this could take anywhere from an hour to 2 days, but rest assured that you will recieve your points.';
	echo'<br />';
echo'</div>';
echo'<h3>Players You Have Referred to TML</h3>';
	echo'<hr>';
echo'<div class="floaty" style="width:75%;">';
	
	echo'<table id="newtables" style="width:100%;">';
		echo'<tr>';
			echo'<th>Mobster</th>';
			echo'<th>State</th>';
			echo'<th>Reward [pts]</th>';
		echo'</tr>';
		$db->query("SELECT * FROM referrals WHERE referrer = ? ORDER BY id DESC");
		$db->execute(array(
			$user_class->id
		));
	$rows = $db->fetch_row();
	if(!count($rows)){
		echo'<tr>';
			echo'<td colspan="3">You have no referrals</td>';
		echo'</tr>';
	} else {
		foreach($rows as $row){
			$credited = ($row['credited'] == 0) ? "Pending" : "Approved";
			$points = ($row['credited'] == 0) ? "0" : "100 + 50 Credits";
			echo'<tr>';
				echo'<td>' . formatName($row['referred']) . '</td>';
				echo'<td>' . $credited . '</td>';
				echo'<td>' . $points . '</td>';
			echo'</tr>';
		}
	}
	echo'</table>';
echo'</div>';
echo'<h3>Top 10 Referrers</h3>';
	echo'<hr>';
echo'<div class="floaty" style="width:75%;">';
	
	echo'<table id="newtables" style="width:100%;">';
		echo'<tr>';
			echo'<th>Rank</th>';
			echo'<th>Username</th>';
			echo'<th>Referrals</th>';
		echo'</tr>';
		$db->query("SELECT COUNT(*) count, referrer FROM referrals r LEFT JOIN bans b ON r.referrer = b.id WHERE b.id IS NULL AND credited = 1 GROUP BY referrer ORDER BY count DESC LIMIT 10");
		$db->execute();
		$rows = $db->fetch_row();
		$r = 0;
		foreach($rows as $row){
			echo'<tr>';
				echo'<td width="10%">' . ++$r . '.</td>';
				echo'<td width="32%">' . formatName($row['referrer']) . '</td>';
				echo'<td width="14%">' . prettynum($row['count']) . '</td>';
			echo'</tr>';
		}
	echo'</table>';
echo'</div>';
<ul id="mainnav"><li><table style="top: -23px; position: relative; height: 60px;"><tr style="vertical-align: bottom;"><td><a href="index.php?mod=premium&sh=f16505f156f9d98227413fc6076fb1dc" class="awesome-tabs">Jeweller<div class="navBG"></div></a></td><td><a href="index.php?mod=premium&submod=inventory&sh=f16505f156f9d98227413fc6076fb1dc" class="awesome-tabs">Inventory<div class="navBG"></div></a></td><td><a href="index.php?mod=premium&submod=centurio&sh=f16505f156f9d98227413fc6076fb1dc" class="awesome-tabs">Centurion<div class="navBG"></div></a></td><td><a href="index.php?mod=powerups&sh=f16505f156f9d98227413fc6076fb1dc" class="awesome-tabs current">Pacts</a></td></tr></table></li></ul>
        </div>
                    <a id="chat_icon"  data-tooltip="[[[&quot;Chat&quot;,&quot;white&quot;]]]" target="_parent"></a>
        
        <div id="main">
            <div id="main_inner" class="pngfix">
                <div id="sidebar">
                    <div id="sidebar_inner">
                        <div id="mainmenu">
                                                                                    <a class="menuitem" href="index.php?mod=overview&sh=f16505f156f9d98227413fc6076fb1dc" title="Overview" target="_self">Overview</a>
                            <a class="menuitem" href="index.php?mod=quests&sh=f16505f156f9d98227413fc6076fb1dc" title="Pantheon" target="_self">Pantheon</a>
                            <a class="menuitem" href="index.php?mod=guild&sh=f16505f156f9d98227413fc6076fb1dc" title="Guild" target="_self">Guild</a>
                            <a class="menuitem" href="index.php?mod=highscore&sh=f16505f156f9d98227413fc6076fb1dc" title="Highscore" target="_self">Highscore</a>
                            <a class="menuitem" href="index.php?mod=recruiting&sh=f16505f156f9d98227413fc6076fb1dc" title="Recruiting" target="_self">Recruiting</a>
                                                                                    <a class="menuitem premium" href="index.php?mod=premium&sh=f16505f156f9d98227413fc6076fb1dc" target="_self">Premium</a>
                                                                                                                                                <div id="submenuhead1" style="display:none">
                                    <div class="menutab_city"><a href="index.php?mod=map&submod=city&sh=f16505f156f9d98227413fc6076fb1dc" class="submenuswitch" target="_self">&nbsp;</a></div>
                                    <div class="menutab_country" onmouseover="switchMenu(2)"><a href="index.php?mod=map&submod=country&sh=f16505f156f9d98227413fc6076fb1dc" class="submenuswitch" target="_self">&nbsp;</a></div>
                                </div>
                                <div id="submenu1" class="submenu" style="display:none">
                                                                                                                                                                    <a href="index.php?mod=work&sh=f16505f156f9d98227413fc6076fb1dc"
                                               class="menuitem "
                                               target="_self">Work</a>
                                                                                                                                                                                                            <a href="index.php?mod=arena&sh=f16505f156f9d98227413fc6076fb1dc"
                                               class="menuitem "
                                               target="_self">Arena</a>
                                                                                                                                                                                                            <a href="index.php?mod=training&sh=f16505f156f9d98227413fc6076fb1dc"
                                               class="menuitem "
                                               target="_self">Training</a>
                                                                                                                                                                                                            <a href="index.php?mod=inventory&sub=1&sh=f16505f156f9d98227413fc6076fb1dc"
                                               class="menuitem "
                                               target="_self">Weapon smith</a>
                                                                                                                                                                                                            <a href="index.php?mod=inventory&sub=2&sh=f16505f156f9d98227413fc6076fb1dc"
                                               class="menuitem "
                                               target="_self">Armour smith</a>
                                                                                                                                                                                                            <a href="index.php?mod=inventory&sub=3&sh=f16505f156f9d98227413fc6076fb1dc"
                                               class="menuitem "
                                               target="_self">General goods</a>
                                                                                                                                                                                                            <a href="index.php?mod=inventory&sub=4&sh=f16505f156f9d98227413fc6076fb1dc"
                                               class="menuitem "
                                               target="_self">Alchemist</a>
                                                                                                                                                                                                            <a href="index.php?mod=inventory&sub=5&sh=f16505f156f9d98227413fc6076fb1dc"
                                               class="menuitem "
                                               target="_self">Mercenary</a>
                                                                                                                                                                                                            <a href="index.php?mod=inventory&sub=6&sh=f16505f156f9d98227413fc6076fb1dc"
                                               class="menuitem "
                                               target="_self">Malefica</a>
                                                                                                                                                                                                            <a href="index.php?mod=forge&submod=forge&sh=f16505f156f9d98227413fc6076fb1dc"
                                               class="menuitem "
                                               target="_self">Forge</a>
                                                                                                                                                                                                            <a href="index.php?mod=forge&submod=smeltery&sh=f16505f156f9d98227413fc6076fb1dc"
                                               class="menuitem "
                                               target="_self">Smelter</a>
                                                                                                                                                                                                            <a href="index.php?mod=forge&submod=workbench&sh=f16505f156f9d98227413fc6076fb1dc"
                                               class="menuitem "
                                               target="_self">Workbench</a>
                                                                                                                                                                                                            <a href="index.php?mod=forge&submod=storage&sh=f16505f156f9d98227413fc6076fb1dc"
                                               class="menuitem "
                                               target="_self">Horreum</a>
                                                                                                                                                                                                            <a href="index.php?mod=magus&sh=f16505f156f9d98227413fc6076fb1dc"
                                               class="menuitem "
                                               target="_self">Magus Hermeticus</a>
                                                                                                                                                                                                            <a href="index.php?mod=auction&sh=f16505f156f9d98227413fc6076fb1dc"
                                               class="menuitem "
                                               target="_self">Auction house</a>
                                                                                                                                                                                                            <a href="index.php?mod=market&sh=f16505f156f9d98227413fc6076fb1dc"
                                               class="menuitem "
                                               target="_self">Market</a>
                                                                                                                                                                                                            <a href="index.php?mod=map&submod=country&sh=f16505f156f9d98227413fc6076fb1dc"
                                               class="menuitem "
                                               target="_self">City gate</a>
                                                                                                            </div>
                                                                <div id="submenuhead2">
                                    <div class="menutab_city" onmouseover="switchMenu(1)"><a href="index.php?mod=map&submod=city&sh=f16505f156f9d98227413fc6076fb1dc" class="submenuswitch" target="_self">&nbsp;</a></div>
                                    <div class="menutab_country"><a href="index.php?mod=map&submod=country&sh=f16505f156f9d98227413fc6076fb1dc" class="submenuswitch" target="_self">&nbsp;</a></div>
                                </div>
                                <div id="submenu2" class="submenu">
                                                                            <a href="index.php?mod=hermit&sh=f16505f156f9d98227413fc6076fb1dc" class="menuitem" target="_self">Hermit</a>
                                    
                                                                                                                                                                                                                                                                                                                <a href="index.php?mod=location&loc=0&sh=f16505f156f9d98227413fc6076fb1dc" class="menuitem " target="_self">
                                                Grimwood                                            </a>
                                                                                                                                                                                                        <a href="index.php?mod=location&loc=1&sh=f16505f156f9d98227413fc6076fb1dc" class="menuitem " target="_self">
                                                Pirate Harbour                                            </a>
                                                                                                                                                                                                        <a href="index.php?mod=location&loc=2&sh=f16505f156f9d98227413fc6076fb1dc" class="menuitem " target="_self">
                                                Misty Mountains                                            </a>
                                                                                                                                                                                                        <a href="index.php?mod=location&loc=3&sh=f16505f156f9d98227413fc6076fb1dc" class="menuitem " target="_self">
                                                Wolf Cave                                            </a>
                                                                                                                                                                                                                                                    <span id="location_inactive_4" class="menuitem inactive"  data-tooltip="[[[&quot;from level 60&quot;,&quot;white&quot;]]]">Ancient Temple</span>
                                                                                                                                                                                                                                                    <span id="location_inactive_5" class="menuitem inactive"  data-tooltip="[[[&quot;from level 65&quot;,&quot;white&quot;]]]">Barbarian Village</span>
                                                                                                                                                                                                                                                    <span id="location_inactive_6" class="menuitem inactive"  data-tooltip="[[[&quot;from level 70&quot;,&quot;white&quot;]]]">Bandit Camp</span>
                                                                            
                                                                                                        </div>
                                <div id="submenufooter"></div>
                                                    </div>
                    </div>
                    <div id="sidebar_footer"></div>
                </div>
                <div id="content">
                                

    <h2 class="section-header contentItem">Description</h2>
    <section>
        <p>You will become mightier than you ever dreamed of if you choose to make a pact with either the earthly or divine powers! But you will always have to keep in mind that you an only activate one pact from each category. We wouldn`t want the gods to argue about whose favourite you are; this would cause terrible chaos.</p>
    </section>

            <div id="changeRune1_1" class="rune_confirm section-header">
            <table width="100%" border="0" cellspacing="0" cellpading="0">
                <tr>
                    <td colspan="2">
                        <div style="text-align: center; height: 27px; font-size: large;">Blessing from Apollo</div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 85px; padding: 5px;"><img alt="" src="//gf2.geo.gfsrv.net/cdn73/eb21690aec1d96fba3aa215e96c90d.jpg" /></td>
                    <td>
                        <p style="margin-top: 0;">The God of the poetry is fond of your talents and gives you his blessing - that your epic may go down in history.</p>
                        <p>You have a 10% chance of not using up any expedition points.<br/> Maximum expedition points are increased by 50%. (+12) &nbsp;</p>
                    </td>
                </tr>
            </table>
            <table cellspacing="0" cellpadding="0" style="border-spacing:0;width:100%">
                <tr>
                    <td style="width:50%">
                        <div class="al" style="padding:5px">
                                                            <a class="cancel_confirm_link" href="index.php?mod=powerups&submod=changePowerUp&rune=1&nr=1&sh=f16505f156f9d98227413fc6076fb1dc">Yes, I want to change!</a>
                                                    </div>
                    </td>
                    <td style="width:50%">
                        <div class="ar" style="padding:5px">
                            <a class="cancel_confirm_link" href="javascript:blackoutDialog(false)">No!</a>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
            <div id="changeRune1_2" class="rune_confirm section-header">
            <table width="100%" border="0" cellspacing="0" cellpading="0">
                <tr>
                    <td colspan="2">
                        <div style="text-align: center; height: 27px; font-size: large;">Blessing from Ceres</div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 85px; padding: 5px;"><img alt="" src="//gf2.geo.gfsrv.net/cdn4c/cf358c538e5803536977846125c979.jpg" /></td>
                    <td>
                        <p style="margin-top: 0;">The mother of the earth is bestowing you with her mercy and with divine health.</p>
                        <p>The regeneration of life points is increased by 50% (+575) &nbsp;</p>
                    </td>
                </tr>
            </table>
            <table cellspacing="0" cellpadding="0" style="border-spacing:0;width:100%">
                <tr>
                    <td style="width:50%">
                        <div class="al" style="padding:5px">
                                                            <a class="cancel_confirm_link" href="index.php?mod=powerups&submod=changePowerUp&rune=1&nr=2&sh=f16505f156f9d98227413fc6076fb1dc">Yes, I want to change!</a>
                                                    </div>
                    </td>
                    <td style="width:50%">
                        <div class="ar" style="padding:5px">
                            <a class="cancel_confirm_link" href="javascript:blackoutDialog(false)">No!</a>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
            <div id="changeRune1_5" class="rune_confirm section-header">
            <table width="100%" border="0" cellspacing="0" cellpading="0">
                <tr>
                    <td colspan="2">
                        <div style="text-align: center; height: 27px; font-size: large;">Blessing of Jupiter</div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 85px; padding: 5px;"><img alt="" src="//gf1.geo.gfsrv.net/cdn6c/b495b3f244395094d4fe7df2b69ec2.jpg" /></td>
                    <td>
                        <p style="margin-top: 0;">The father of God is looking after you - Your life energy is brimming over with divine energy.</p>
                        <p>The life points from constitution are increased by 50% (+925) &nbsp;</p>
                    </td>
                </tr>
            </table>
            <table cellspacing="0" cellpadding="0" style="border-spacing:0;width:100%">
                <tr>
                    <td style="width:50%">
                        <div class="al" style="padding:5px">
                                                            <a class="cancel_confirm_link" href="index.php?mod=powerups&submod=changePowerUp&rune=1&nr=5&sh=f16505f156f9d98227413fc6076fb1dc">Yes, I want to change!</a>
                                                    </div>
                    </td>
                    <td style="width:50%">
                        <div class="ar" style="padding:5px">
                            <a class="cancel_confirm_link" href="javascript:blackoutDialog(false)">No!</a>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
            <div id="changeRune1_3" class="rune_confirm section-header">
            <table width="100%" border="0" cellspacing="0" cellpading="0">
                <tr>
                    <td colspan="2">
                        <div style="text-align: center; height: 27px; font-size: large;">Blessing of Venus</div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 85px; padding: 5px;"><img alt="" src="//gf3.geo.gfsrv.net/cdn84/1ed81ac9053fc5bf70d962f637db45.jpg" /></td>
                    <td>
                        <p style="margin-top: 0;">The touch of the Goddess of love and beauty gives your charisma a Godly trait.</p>
                        <p>The maximum charisma is increased by 38 &nbsp;</p>
                    </td>
                </tr>
            </table>
            <table cellspacing="0" cellpadding="0" style="border-spacing:0;width:100%">
                <tr>
                    <td style="width:50%">
                        <div class="al" style="padding:5px">
                                                            <a class="cancel_confirm_link" href="index.php?mod=powerups&submod=changePowerUp&rune=1&nr=3&sh=f16505f156f9d98227413fc6076fb1dc">Yes, I want to change!</a>
                                                    </div>
                    </td>
                    <td style="width:50%">
                        <div class="ar" style="padding:5px">
                            <a class="cancel_confirm_link" href="javascript:blackoutDialog(false)">No!</a>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
            <div id="changeRune1_4" class="rune_confirm section-header">
            <table width="100%" border="0" cellspacing="0" cellpading="0">
                <tr>
                    <td colspan="2">
                        <div style="text-align: center; height: 27px; font-size: large;">Mercury`s Blessing</div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 85px; padding: 5px;"><img alt="" src="//gf3.geo.gfsrv.net/cdne3/acb4dd8d0a07f7ea274b840c1600b7.jpg" /></td>
                    <td>
                        <p style="margin-top: 0;">From now on, the god of travellers will be watching over your expeditions.</p>
                        <p>The effects of all expedition bonuses are doubled.</p>
                    </td>
                </tr>
            </table>
            <table cellspacing="0" cellpadding="0" style="border-spacing:0;width:100%">
                <tr>
                    <td style="width:50%">
                        <div class="al" style="padding:5px">
                                                            <a class="cancel_confirm_link" href="index.php?mod=powerups&submod=changePowerUp&rune=1&nr=4&sh=f16505f156f9d98227413fc6076fb1dc">Yes, I want to change!</a>
                                                    </div>
                    </td>
                    <td style="width:50%">
                        <div class="ar" style="padding:5px">
                            <a class="cancel_confirm_link" href="javascript:blackoutDialog(false)">No!</a>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    
    <h2 class="section-header powerup_title">
        <div id="powerup1" class="powerup_cat_icon" style="background-image: url(//gf1.geo.gfsrv.net/cdn62/908aa633848089ce281dc81659b4bf.gif)"></div>
        <span id="runeTitle1" class="rune_title">Blessing from Apollo</span>
    </h2>
    <section>
                                <span class="powerup_duration" style="color: green;">7 Days, 9 Minutes remaining</span>
        
        <div class="powerup_box">
                            <div
                    id="rune1_1"
                    class="powerUpImg1"
                                            data-tooltip="[[[&quot;Blessing from Apollo&quot;,&quot;#DDDDDD&quot;],[&quot;The God of the poetry is fond of your talents and gives you his blessing - that your epic may go down in history.&quot;,&quot;#808080&quot;,400],[&quot;You have a 10% chance of not using up any expedition points.&lt;br\/&gt; Maximum expedition points are increased by 50%. (+12) &amp;nbsp;&quot;,&quot;#00B712&quot;]]]"                                                                style="background-image: url(//gf3.geo.gfsrv.net/cdn25/426d3dcc24513c31815de00e9176c6.jpg);"
                                    ></div>
                            <div
                    id="rune1_2"
                    class="powerUpImg2"
                                            data-tooltip="[[[&quot;Blessing from Ceres&quot;,&quot;#DDDDDD&quot;],[&quot;The mother of the earth is bestowing you with her mercy and with divine health.&quot;,&quot;#808080&quot;,400],[&quot;The regeneration of life points is increased by 50% (+575) &amp;nbsp;&quot;,&quot;#00B712&quot;]]]"                                                                onClick="blackoutDialog(true, 'changeRune1_2', 400)"
                        style="cursor: pointer; background-image: url(//gf2.geo.gfsrv.net/cdn4c/cf358c538e5803536977846125c979.jpg);"
                                    ></div>
                            <div
                    id="rune1_5"
                    class="powerUpImg5"
                                            data-tooltip="[[[&quot;Blessing of Jupiter&quot;,&quot;#DDDDDD&quot;],[&quot;The father of God is looking after you - Your life energy is brimming over with divine energy.&quot;,&quot;#808080&quot;,400],[&quot;The life points from constitution are increased by 50% (+925) &amp;nbsp;&quot;,&quot;#00B712&quot;],[&quot;Needs level 60&quot;,&quot;#FF0000&quot;]]]"                                                                onClick="blackoutDialog(true, 'changeRune1_5', 400)"
                        style="cursor: pointer; background-image: url(//gf1.geo.gfsrv.net/cdn6c/b495b3f244395094d4fe7df2b69ec2.jpg);"
                                    ></div>
                            <div
                    id="rune1_3"
                    class="powerUpImg3"
                                            data-tooltip="[[[&quot;Blessing of Venus&quot;,&quot;#DDDDDD&quot;],[&quot;The touch of the Goddess of love and beauty gives your charisma a Godly trait.&quot;,&quot;#808080&quot;,400],[&quot;The maximum charisma is increased by 38 &amp;nbsp;&quot;,&quot;#00B712&quot;]]]"                                                                onClick="blackoutDialog(true, 'changeRune1_3', 400)"
                        style="cursor: pointer; background-image: url(//gf3.geo.gfsrv.net/cdn84/1ed81ac9053fc5bf70d962f637db45.jpg);"
                                    ></div>
                            <div
                    id="rune1_4"
                    class="powerUpImg4"
                                            data-tooltip="[[[&quot;Mercury`s Blessing&quot;,&quot;#DDDDDD&quot;],[&quot;From now on, the god of travellers will be watching over your expeditions.&quot;,&quot;#808080&quot;,400],[&quot;The effects of all expedition bonuses are doubled.&quot;,&quot;#00B712&quot;],[&quot;Needs level 40&quot;,&quot;#FF0000&quot;]]]"                                                                onClick="blackoutDialog(true, 'changeRune1_4', 400)"
                        style="cursor: pointer; background-image: url(//gf3.geo.gfsrv.net/cdne3/acb4dd8d0a07f7ea274b840c1600b7.jpg);"
                                    ></div>
                    </div>

        <div class="powerup_buy_extend">
            <form method="POST" id="buyForm1" action="index.php?mod=powerups&submod=activatePowerUp&sh=f16505f156f9d98227413fc6076fb1dc" style="margin: 0;">
                <input type="hidden" id="hiddenPowerUp1Nr" name="activatePowerUpNr" value="1" />
                <input type="hidden" name="activatePowerUpCatNr" value="1" />
            </form>
                            <a href="#" onClick="buyRune('1', '1')">
                                            Further 14 days for only 15 <a href="index.php?mod=premium&submod=rubies&sh=f16505f156f9d98227413fc6076fb1dc"><img alt="" src="//gf1.geo.gfsrv.net/cdn92/03d0e8e4718d33a582132485433bb7.gif" title="Rubies" align="absmiddle" border="0" /></a>                                    </a>
                    </div>
    </section>
            <div id="changeRune2_1" class="rune_confirm section-header">
            <table width="100%" border="0" cellspacing="0" cellpading="0">
                <tr>
                    <td colspan="2">
                        <div style="text-align: center; height: 27px; font-size: large;">Seal of praetor</div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 85px; padding: 5px;"><img alt="" src="//gf3.geo.gfsrv.net/cdn5c/8bfc623c715506494fd98740c7f32e.jpg" /></td>
                    <td>
                        <p style="margin-top: 0;">The seal of praetor allows you to receive goods which are not meant for you.</p>
                        <p>You can use items which are available 2 levels above yours.</p>
                    </td>
                </tr>
            </table>
            <table cellspacing="0" cellpadding="0" style="border-spacing:0;width:100%">
                <tr>
                    <td style="width:50%">
                        <div class="al" style="padding:5px">
                                                            <a class="cancel_confirm_link" href="index.php?mod=powerups&submod=changePowerUp&rune=2&nr=1&sh=f16505f156f9d98227413fc6076fb1dc">Yes, I want to change!</a>
                                                    </div>
                    </td>
                    <td style="width:50%">
                        <div class="ar" style="padding:5px">
                            <a class="cancel_confirm_link" href="javascript:blackoutDialog(false)">No!</a>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
            <div id="changeRune2_2" class="rune_confirm section-header">
            <table width="100%" border="0" cellspacing="0" cellpading="0">
                <tr>
                    <td colspan="2">
                        <div style="text-align: center; height: 27px; font-size: large;">Seal of patrician</div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 85px; padding: 5px;"><img alt="" src="//gf1.geo.gfsrv.net/cdn0e/9346dfd28d29f24e6928590e41d49f.jpg" /></td>
                    <td>
                        <p style="margin-top: 0;">From now on, the patrician seal grants you access to the secret warehouses of the merchant.</p>
                        <p>The waiting time for new goods at the merchant`s is reduced by 50%</p>
                    </td>
                </tr>
            </table>
            <table cellspacing="0" cellpadding="0" style="border-spacing:0;width:100%">
                <tr>
                    <td style="width:50%">
                        <div class="al" style="padding:5px">
                                                            <a class="cancel_confirm_link" href="index.php?mod=powerups&submod=changePowerUp&rune=2&nr=2&sh=f16505f156f9d98227413fc6076fb1dc">Yes, I want to change!</a>
                                                    </div>
                    </td>
                    <td style="width:50%">
                        <div class="ar" style="padding:5px">
                            <a class="cancel_confirm_link" href="javascript:blackoutDialog(false)">No!</a>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
            <div id="changeRune2_3" class="rune_confirm section-header">
            <table width="100%" border="0" cellspacing="0" cellpading="0">
                <tr>
                    <td colspan="2">
                        <div style="text-align: center; height: 27px; font-size: large;">Seal of consulate</div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 85px; padding: 5px;"><img alt="" src="//gf1.geo.gfsrv.net/cdn32/34fed8f05bfad9069e22da7156cb77.jpg" /></td>
                    <td>
                        <p style="margin-top: 0;">As messenger of the consulate, the merchants are going to offer you fair prices from now on.</p>
                        <p>Items can be sold/bought at a reduced rate of 5% at the merchant`s</p>
                    </td>
                </tr>
            </table>
            <table cellspacing="0" cellpadding="0" style="border-spacing:0;width:100%">
                <tr>
                    <td style="width:50%">
                        <div class="al" style="padding:5px">
                                                            <a class="cancel_confirm_link" href="index.php?mod=powerups&submod=changePowerUp&rune=2&nr=3&sh=f16505f156f9d98227413fc6076fb1dc">Yes, I want to change!</a>
                                                    </div>
                    </td>
                    <td style="width:50%">
                        <div class="ar" style="padding:5px">
                            <a class="cancel_confirm_link" href="javascript:blackoutDialog(false)">No!</a>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
            <div id="changeRune2_4" class="rune_confirm section-header">
            <table width="100%" border="0" cellspacing="0" cellpading="0">
                <tr>
                    <td colspan="2">
                        <div style="text-align: center; height: 27px; font-size: large;">Seal of quaestor</div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 85px; padding: 5px;"><img alt="" src="//gf2.geo.gfsrv.net/cdn42/bad676b578c377b9251d9319b5a9e3.jpg" /></td>
                    <td>
                        <p style="margin-top: 0;">With the seal from Quaestor you are going to get a higher fee for regular work.</p>
                        <p>The pay for the work is increased by 3%</p>
                    </td>
                </tr>
            </table>
            <table cellspacing="0" cellpadding="0" style="border-spacing:0;width:100%">
                <tr>
                    <td style="width:50%">
                        <div class="al" style="padding:5px">
                                                            <a class="cancel_confirm_link" href="index.php?mod=powerups&submod=changePowerUp&rune=2&nr=4&sh=f16505f156f9d98227413fc6076fb1dc">Yes, I want to change!</a>
                                                    </div>
                    </td>
                    <td style="width:50%">
                        <div class="ar" style="padding:5px">
                            <a class="cancel_confirm_link" href="javascript:blackoutDialog(false)">No!</a>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
            <div id="changeRune2_5" class="rune_confirm section-header">
            <table width="100%" border="0" cellspacing="0" cellpading="0">
                <tr>
                    <td colspan="2">
                        <div style="text-align: center; height: 27px; font-size: large;">Seal of Aedil</div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 85px; padding: 5px;"><img alt="" src="//gf3.geo.gfsrv.net/cdnb9/438ecfd74b9464549f5b9e97a3d950.jpg" /></td>
                    <td>
                        <p style="margin-top: 0;">With the seal of Aedile you can avoid a lot of bribes at the market.</p>
                        <p>The market fee at the market is reduced by 50% <br/> Allows sales at the market which last 48h</p>
                    </td>
                </tr>
            </table>
            <table cellspacing="0" cellpadding="0" style="border-spacing:0;width:100%">
                <tr>
                    <td style="width:50%">
                        <div class="al" style="padding:5px">
                                                            <a class="cancel_confirm_link" href="index.php?mod=powerups&submod=changePowerUp&rune=2&nr=5&sh=f16505f156f9d98227413fc6076fb1dc">Yes, I want to change!</a>
                                                    </div>
                    </td>
                    <td style="width:50%">
                        <div class="ar" style="padding:5px">
                            <a class="cancel_confirm_link" href="javascript:blackoutDialog(false)">No!</a>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    
    <h2 class="section-header powerup_title">
        <div id="powerup2" class="powerup_cat_icon" style="background-image: url(//gf2.geo.gfsrv.net/cdn1d/fb4f2b64dcdaec0b6bf94ced8b091e.gif)"></div>
        <span id="runeTitle2" class="rune_title">Seal of praetor</span>
    </h2>
    <section>
                                <span class="powerup_duration" style="color: green;">7 Days, 9 Minutes remaining</span>
        
        <div class="powerup_box">
                            <div
                    id="rune2_1"
                    class="powerUpImg1"
                                            data-tooltip="[[[&quot;Seal of praetor&quot;,&quot;#DDDDDD&quot;],[&quot;The seal of praetor allows you to receive goods which are not meant for you.&quot;,&quot;#808080&quot;,400],[&quot;You can use items which are available 2 levels above yours.&quot;,&quot;#00B712&quot;]]]"                                                                style="background-image: url(//gf1.geo.gfsrv.net/cdn34/4691fc5eb70d1beaa6e3f21c793315.jpg);"
                                    ></div>
                            <div
                    id="rune2_2"
                    class="powerUpImg2"
                                            data-tooltip="[[[&quot;Seal of patrician&quot;,&quot;#DDDDDD&quot;],[&quot;From now on, the patrician seal grants you access to the secret warehouses of the merchant.&quot;,&quot;#808080&quot;,400],[&quot;The waiting time for new goods at the merchant`s is reduced by 50%&quot;,&quot;#00B712&quot;]]]"                                                                onClick="blackoutDialog(true, 'changeRune2_2', 500)"
                        style="cursor: pointer; background-image: url(//gf1.geo.gfsrv.net/cdn0e/9346dfd28d29f24e6928590e41d49f.jpg);"
                                    ></div>
                            <div
                    id="rune2_3"
                    class="powerUpImg3"
                                            data-tooltip="[[[&quot;Seal of consulate&quot;,&quot;#DDDDDD&quot;],[&quot;As messenger of the consulate, the merchants are going to offer you fair prices from now on.&quot;,&quot;#808080&quot;,400],[&quot;Items can be sold\/bought at a reduced rate of 5% at the merchant`s&quot;,&quot;#00B712&quot;]]]"                                                                onClick="blackoutDialog(true, 'changeRune2_3', 500)"
                        style="cursor: pointer; background-image: url(//gf1.geo.gfsrv.net/cdn32/34fed8f05bfad9069e22da7156cb77.jpg);"
                                    ></div>
                            <div
                    id="rune2_4"
                    class="powerUpImg4"
                                            data-tooltip="[[[&quot;Seal of quaestor&quot;,&quot;#DDDDDD&quot;],[&quot;With the seal from Quaestor you are going to get a higher fee for regular work.&quot;,&quot;#808080&quot;,400],[&quot;The pay for the work is increased by 3%&quot;,&quot;#00B712&quot;],[&quot;Needs level 40&quot;,&quot;#FF0000&quot;]]]"                                                                onClick="blackoutDialog(true, 'changeRune2_4', 500)"
                        style="cursor: pointer; background-image: url(//gf2.geo.gfsrv.net/cdn42/bad676b578c377b9251d9319b5a9e3.jpg);"
                                    ></div>
                            <div
                    id="rune2_5"
                    class="powerUpImg5"
                                            data-tooltip="[[[&quot;Seal of Aedil&quot;,&quot;#DDDDDD&quot;],[&quot;With the seal of Aedile you can avoid a lot of bribes at the market.&quot;,&quot;#808080&quot;,400],[&quot;The market fee at the market is reduced by 50% &lt;br\/&gt; Allows sales at the market which last 48h&quot;,&quot;#00B712&quot;],[&quot;Needs level 60&quot;,&quot;#FF0000&quot;]]]"                                                                onClick="blackoutDialog(true, 'changeRune2_5', 500)"
                        style="cursor: pointer; background-image: url(//gf3.geo.gfsrv.net/cdnb9/438ecfd74b9464549f5b9e97a3d950.jpg);"
                                    ></div>
                    </div>

        <div class="powerup_buy_extend">
            <form method="POST" id="buyForm2" action="index.php?mod=powerups&submod=activatePowerUp&sh=f16505f156f9d98227413fc6076fb1dc" style="margin: 0;">
                <input type="hidden" id="hiddenPowerUp2Nr" name="activatePowerUpNr" value="1" />
                <input type="hidden" name="activatePowerUpCatNr" value="2" />
            </form>
                            <a href="#" onClick="buyRune('2', '1')">
                                            Further 14 days for only 15 <a href="index.php?mod=premium&submod=rubies&sh=f16505f156f9d98227413fc6076fb1dc"><img alt="" src="//gf1.geo.gfsrv.net/cdn92/03d0e8e4718d33a582132485433bb7.gif" title="Rubies" align="absmiddle" border="0" /></a>                                    </a>
                    </div>
    </section>
            <div id="changeRune3_1" class="rune_confirm section-header">
            <table width="100%" border="0" cellspacing="0" cellpading="0">
                <tr>
                    <td colspan="2">
                        <div style="text-align: center; height: 27px; font-size: large;">Honour of the berserker</div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 85px; padding: 5px;"><img alt="" src="//gf2.geo.gfsrv.net/cdnd4/fd582e194918709f858c6a702d77bb.jpg" /></td>
                    <td>
                        <p style="margin-top: 0;">The berserker honour is only given to gladiators who have became famous through their fatal attacks.</p>
                        <p>The total damage is increased by 25% of the playing level or by a minimum of 2 (+10) &nbsp;</p>
                    </td>
                </tr>
            </table>
            <table cellspacing="0" cellpadding="0" style="border-spacing:0;width:100%">
                <tr>
                    <td style="width:50%">
                        <div class="al" style="padding:5px">
                                                            <a class="cancel_confirm_link" href="index.php?mod=powerups&submod=changePowerUp&rune=3&nr=1&sh=f16505f156f9d98227413fc6076fb1dc">Yes, I want to change!</a>
                                                    </div>
                    </td>
                    <td style="width:50%">
                        <div class="ar" style="padding:5px">
                            <a class="cancel_confirm_link" href="javascript:blackoutDialog(false)">No!</a>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
            <div id="changeRune3_3" class="rune_confirm section-header">
            <table width="100%" border="0" cellspacing="0" cellpading="0">
                <tr>
                    <td colspan="2">
                        <div style="text-align: center; height: 27px; font-size: large;">Honour of the veteran</div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 85px; padding: 5px;"><img alt="" src="//gf1.geo.gfsrv.net/cdn3a/72528761f17603446461a4b189108e.jpg" /></td>
                    <td>
                        <p style="margin-top: 0;">The veteran honour is given to those gladiators who have perfected their martial art.</p>
                        <p>Gives a 10% chance of causing a critical hit.</p>
                    </td>
                </tr>
            </table>
            <table cellspacing="0" cellpadding="0" style="border-spacing:0;width:100%">
                <tr>
                    <td style="width:50%">
                        <div class="al" style="padding:5px">
                                                            <a class="cancel_confirm_link" href="index.php?mod=powerups&submod=changePowerUp&rune=3&nr=3&sh=f16505f156f9d98227413fc6076fb1dc">Yes, I want to change!</a>
                                                    </div>
                    </td>
                    <td style="width:50%">
                        <div class="ar" style="padding:5px">
                            <a class="cancel_confirm_link" href="javascript:blackoutDialog(false)">No!</a>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
            <div id="changeRune3_4" class="rune_confirm section-header">
            <table width="100%" border="0" cellspacing="0" cellpading="0">
                <tr>
                    <td colspan="2">
                        <div style="text-align: center; height: 27px; font-size: large;">Honours of the hero</div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 85px; padding: 5px;"><img alt="" src="//gf1.geo.gfsrv.net/cdn39/864cdef231f43ac06956a82f8abecf.jpg" /></td>
                    <td>
                        <p style="margin-top: 0;">The honour of a hero is given to fighters whose physical strength is much higher than those of ordinary mortals.</p>
                        <p>The maximum strength is increased by 36 &nbsp;</p>
                    </td>
                </tr>
            </table>
            <table cellspacing="0" cellpadding="0" style="border-spacing:0;width:100%">
                <tr>
                    <td style="width:50%">
                        <div class="al" style="padding:5px">
                                                            <a class="cancel_confirm_link" href="index.php?mod=powerups&submod=changePowerUp&rune=3&nr=4&sh=f16505f156f9d98227413fc6076fb1dc">Yes, I want to change!</a>
                                                    </div>
                    </td>
                    <td style="width:50%">
                        <div class="ar" style="padding:5px">
                            <a class="cancel_confirm_link" href="javascript:blackoutDialog(false)">No!</a>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
            <div id="changeRune3_2" class="rune_confirm section-header">
            <table width="100%" border="0" cellspacing="0" cellpading="0">
                <tr>
                    <td colspan="2">
                        <div style="text-align: center; height: 27px; font-size: large;">Honour of the armourer</div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 85px; padding: 5px;"><img alt="" src="//gf1.geo.gfsrv.net/cdnf2/b419dd9d84737720b3911e0e16ab74.jpg" /></td>
                    <td>
                        <p style="margin-top: 0;">Armourers are warriors that are so skilled in the use of their favoured weapon that they are rendered untouchable.</p>
                        <p>The maximum dexterity is increased by 40 &nbsp;</p>
                    </td>
                </tr>
            </table>
            <table cellspacing="0" cellpadding="0" style="border-spacing:0;width:100%">
                <tr>
                    <td style="width:50%">
                        <div class="al" style="padding:5px">
                                                            <a class="cancel_confirm_link" href="index.php?mod=powerups&submod=changePowerUp&rune=3&nr=2&sh=f16505f156f9d98227413fc6076fb1dc">Yes, I want to change!</a>
                                                    </div>
                    </td>
                    <td style="width:50%">
                        <div class="ar" style="padding:5px">
                            <a class="cancel_confirm_link" href="javascript:blackoutDialog(false)">No!</a>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
            <div id="changeRune3_5" class="rune_confirm section-header">
            <table width="100%" border="0" cellspacing="0" cellpading="0">
                <tr>
                    <td colspan="2">
                        <div style="text-align: center; height: 27px; font-size: large;">Honour of the destroyer</div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 85px; padding: 5px;"><img alt="" src="//gf3.geo.gfsrv.net/cdn8d/9d06ce9ce47e15fe2b3deb72e0904d.jpg" /></td>
                    <td>
                        <p style="margin-top: 0;">The warrior whose strength and technique cannot be beaten by any type of armour or shield gets the honour of a destroyer.</p>
                        <p>Lowers the armour value of the opponent (at Level 39: 585 points)</p>
                    </td>
                </tr>
            </table>
            <table cellspacing="0" cellpadding="0" style="border-spacing:0;width:100%">
                <tr>
                    <td style="width:50%">
                        <div class="al" style="padding:5px">
                                                            <a class="cancel_confirm_link" href="index.php?mod=powerups&submod=changePowerUp&rune=3&nr=5&sh=f16505f156f9d98227413fc6076fb1dc">Yes, I want to change!</a>
                                                    </div>
                    </td>
                    <td style="width:50%">
                        <div class="ar" style="padding:5px">
                            <a class="cancel_confirm_link" href="javascript:blackoutDialog(false)">No!</a>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    
    <h2 class="section-header powerup_title">
        <div id="powerup3" class="powerup_cat_icon" style="background-image: url(//gf1.geo.gfsrv.net/cdn92/fa7b458af4e2bfee9c3f1bcefa3b19.gif)"></div>
        <span id="runeTitle3" class="rune_title">Honour of the berserker</span>
    </h2>
    <section>
                                <span class="powerup_duration" style="color: green;">7 Days, 9 Minutes remaining</span>
        
        <div class="powerup_box">
                            <div
                    id="rune3_1"
                    class="powerUpImg1"
                                            data-tooltip="[[[&quot;Honour of the berserker&quot;,&quot;#DDDDDD&quot;],[&quot;The berserker honour is only given to gladiators who have became famous through their fatal attacks.&quot;,&quot;#808080&quot;,400],[&quot;The total damage is increased by 25% of the playing level or by a minimum of 2 (+10) &amp;nbsp;&quot;,&quot;#00B712&quot;]]]"                                                                style="background-image: url(//gf2.geo.gfsrv.net/cdn7b/22d8c1a35d33b3fd39ffd997da18e1.jpg);"
                                    ></div>
                            <div
                    id="rune3_3"
                    class="powerUpImg3"
                                            data-tooltip="[[[&quot;Honour of the veteran&quot;,&quot;#DDDDDD&quot;],[&quot;The veteran honour is given to those gladiators who have perfected their martial art.&quot;,&quot;#808080&quot;,400],[&quot;Gives a 10% chance of causing a critical hit.&quot;,&quot;#00B712&quot;]]]"                                                                onClick="blackoutDialog(true, 'changeRune3_3', 600)"
                        style="cursor: pointer; background-image: url(//gf1.geo.gfsrv.net/cdn3a/72528761f17603446461a4b189108e.jpg);"
                                    ></div>
                            <div
                    id="rune3_4"
                    class="powerUpImg4"
                                            data-tooltip="[[[&quot;Honours of the hero&quot;,&quot;#DDDDDD&quot;],[&quot;The honour of a hero is given to fighters whose physical strength is much higher than those of ordinary mortals.&quot;,&quot;#808080&quot;,400],[&quot;The maximum strength is increased by 36 &amp;nbsp;&quot;,&quot;#00B712&quot;],[&quot;Needs level 40&quot;,&quot;#FF0000&quot;]]]"                                                                onClick="blackoutDialog(true, 'changeRune3_4', 600)"
                        style="cursor: pointer; background-image: url(//gf1.geo.gfsrv.net/cdn39/864cdef231f43ac06956a82f8abecf.jpg);"
                                    ></div>
                            <div
                    id="rune3_2"
                    class="powerUpImg2"
                                            data-tooltip="[[[&quot;Honour of the armourer&quot;,&quot;#DDDDDD&quot;],[&quot;Armourers are warriors that are so skilled in the use of their favoured weapon that they are rendered untouchable.&quot;,&quot;#808080&quot;,400],[&quot;The maximum dexterity is increased by 40 &amp;nbsp;&quot;,&quot;#00B712&quot;]]]"                                                                onClick="blackoutDialog(true, 'changeRune3_2', 600)"
                        style="cursor: pointer; background-image: url(//gf1.geo.gfsrv.net/cdnf2/b419dd9d84737720b3911e0e16ab74.jpg);"
                                    ></div>
                            <div
                    id="rune3_5"
                    class="powerUpImg5"
                                            data-tooltip="[[[&quot;Honour of the destroyer&quot;,&quot;#DDDDDD&quot;],[&quot;The warrior whose strength and technique cannot be beaten by any type of armour or shield gets the honour of a destroyer.&quot;,&quot;#808080&quot;,400],[&quot;Lowers the armour value of the opponent (at Level 39: 585 points)&quot;,&quot;#00B712&quot;],[&quot;Needs level 60&quot;,&quot;#FF0000&quot;]]]"                                                                onClick="blackoutDialog(true, 'changeRune3_5', 600)"
                        style="cursor: pointer; background-image: url(//gf3.geo.gfsrv.net/cdn8d/9d06ce9ce47e15fe2b3deb72e0904d.jpg);"
                                    ></div>
                    </div>

        <div class="powerup_buy_extend">
            <form method="POST" id="buyForm3" action="index.php?mod=powerups&submod=activatePowerUp&sh=f16505f156f9d98227413fc6076fb1dc" style="margin: 0;">
                <input type="hidden" id="hiddenPowerUp3Nr" name="activatePowerUpNr" value="1" />
                <input type="hidden" name="activatePowerUpCatNr" value="3" />
            </form>
                            <a href="#" onClick="buyRune('3', '1')">
                                            Further 14 days for only 15 <a href="index.php?mod=premium&submod=rubies&sh=f16505f156f9d98227413fc6076fb1dc"><img alt="" src="//gf1.geo.gfsrv.net/cdn92/03d0e8e4718d33a582132485433bb7.gif" title="Rubies" align="absmiddle" border="0" /></a>                                    </a>
                    </div>
    </section>
            <div id="changeRune4_1" class="rune_confirm section-header">
            <table width="100%" border="0" cellspacing="0" cellpading="0">
                <tr>
                    <td colspan="2">
                        <div style="text-align: center; height: 27px; font-size: large;">Secret Knowledge of the Ancients</div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 85px; padding: 5px;"><img alt="" src="//gf3.geo.gfsrv.net/cdn8e/76cf549a92ff7a43f80771659170f5.jpg" /></td>
                    <td>
                        <p style="margin-top: 0;">Those who master the techniques of the ancestors can get, evaluate and complete quests more quickly.</p>
                        <p>The cooldown time for accepting quests is reduced by 50%. <br /> The Ruby costs for shortening quest cooldown time is reduced to 1 Ruby.</p>
                    </td>
                </tr>
            </table>
            <table cellspacing="0" cellpadding="0" style="border-spacing:0;width:100%">
                <tr>
                    <td style="width:50%">
                        <div class="al" style="padding:5px">
                                                            <a class="cancel_confirm_link" href="index.php?mod=powerups&submod=changePowerUp&rune=4&nr=1&sh=f16505f156f9d98227413fc6076fb1dc">Yes, I want to change!</a>
                                                    </div>
                    </td>
                    <td style="width:50%">
                        <div class="ar" style="padding:5px">
                            <a class="cancel_confirm_link" href="javascript:blackoutDialog(false)">No!</a>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
            <div id="changeRune4_2" class="rune_confirm section-header">
            <table width="100%" border="0" cellspacing="0" cellpading="0">
                <tr>
                    <td colspan="2">
                        <div style="text-align: center; height: 27px; font-size: large;">Secret knowledge of the beast master</div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 85px; padding: 5px;"><img alt="" src="//gf3.geo.gfsrv.net/cdn20/894322e9d42ffe611e32facf9e38d3.jpg" /></td>
                    <td>
                        <p style="margin-top: 0;">The ancient secrets of the beast master not only include the high art of tracking, but also a wide knowledge about the creatures of the wilderness.</p>
                        <p>+5% Learning chance for expedition bonuses</p>
                    </td>
                </tr>
            </table>
            <table cellspacing="0" cellpadding="0" style="border-spacing:0;width:100%">
                <tr>
                    <td style="width:50%">
                        <div class="al" style="padding:5px">
                                                            <a class="cancel_confirm_link" href="index.php?mod=powerups&submod=changePowerUp&rune=4&nr=2&sh=f16505f156f9d98227413fc6076fb1dc">Yes, I want to change!</a>
                                                    </div>
                    </td>
                    <td style="width:50%">
                        <div class="ar" style="padding:5px">
                            <a class="cancel_confirm_link" href="javascript:blackoutDialog(false)">No!</a>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
            <div id="changeRune4_3" class="rune_confirm section-header">
            <table width="100%" border="0" cellspacing="0" cellpading="0">
                <tr>
                    <td colspan="2">
                        <div style="text-align: center; height: 27px; font-size: large;">Secret knowledge of the artefact collector</div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 85px; padding: 5px;"><img alt="" src="//gf3.geo.gfsrv.net/cdne1/073b551a230f249a3a4fa083b4fcff.jpg" /></td>
                    <td>
                        <p style="margin-top: 0;">The secret cult of the artefact collector has been collecting information about all occult and mystic items since the beginning of the Roman empire.</p>
                        <p>+15% Chance of finding an item</p>
                    </td>
                </tr>
            </table>
            <table cellspacing="0" cellpadding="0" style="border-spacing:0;width:100%">
                <tr>
                    <td style="width:50%">
                        <div class="al" style="padding:5px">
                                                            <a class="cancel_confirm_link" href="index.php?mod=powerups&submod=changePowerUp&rune=4&nr=3&sh=f16505f156f9d98227413fc6076fb1dc">Yes, I want to change!</a>
                                                    </div>
                    </td>
                    <td style="width:50%">
                        <div class="ar" style="padding:5px">
                            <a class="cancel_confirm_link" href="javascript:blackoutDialog(false)">No!</a>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
            <div id="changeRune4_4" class="rune_confirm section-header">
            <table width="100%" border="0" cellspacing="0" cellpading="0">
                <tr>
                    <td colspan="2">
                        <div style="text-align: center; height: 27px; font-size: large;">Secret knowledge of the assassins</div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 85px; padding: 5px;"><img alt="" src="//gf3.geo.gfsrv.net/cdn5e/234e0c2663d8e28a45c5b9c3241006.jpg" /></td>
                    <td>
                        <p style="margin-top: 0;">The secret society of assassins teaches strictly protected techniques of sneaking, acrobatics and evasion.</p>
                        <p>The maximum agility is increased by 54 &nbsp;</p>
                    </td>
                </tr>
            </table>
            <table cellspacing="0" cellpadding="0" style="border-spacing:0;width:100%">
                <tr>
                    <td style="width:50%">
                        <div class="al" style="padding:5px">
                                                            <a class="cancel_confirm_link" href="index.php?mod=powerups&submod=changePowerUp&rune=4&nr=4&sh=f16505f156f9d98227413fc6076fb1dc">Yes, I want to change!</a>
                                                    </div>
                    </td>
                    <td style="width:50%">
                        <div class="ar" style="padding:5px">
                            <a class="cancel_confirm_link" href="javascript:blackoutDialog(false)">No!</a>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
            <div id="changeRune4_5" class="rune_confirm section-header">
            <table width="100%" border="0" cellspacing="0" cellpading="0">
                <tr>
                    <td colspan="2">
                        <div style="text-align: center; height: 27px; font-size: large;">Secret knowledge of the immortals</div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 85px; padding: 5px;"><img alt="" src="//gf2.geo.gfsrv.net/cdna3/7236fdf07b3079e56f909a67dec917.jpg" /></td>
                    <td>
                        <p style="margin-top: 0;">The box of the immortals is said to possess an elixir which gives eternal life.</p>
                        <p>The maximum constitution is increased by 32 &nbsp;</p>
                    </td>
                </tr>
            </table>
            <table cellspacing="0" cellpadding="0" style="border-spacing:0;width:100%">
                <tr>
                    <td style="width:50%">
                        <div class="al" style="padding:5px">
                                                            <a class="cancel_confirm_link" href="index.php?mod=powerups&submod=changePowerUp&rune=4&nr=5&sh=f16505f156f9d98227413fc6076fb1dc">Yes, I want to change!</a>
                                                    </div>
                    </td>
                    <td style="width:50%">
                        <div class="ar" style="padding:5px">
                            <a class="cancel_confirm_link" href="javascript:blackoutDialog(false)">No!</a>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    
    <h2 class="section-header powerup_title">
        <div id="powerup4" class="powerup_cat_icon" style="background-image: url(//gf2.geo.gfsrv.net/cdn49/c422ef306d0633183b7331b4ee6012.gif)"></div>
        <span id="runeTitle4" class="rune_title">Secret knowledge of the beast master</span>
    </h2>
    <section>
                                <span class="powerup_duration" style="color: green;">7 Days, 9 Minutes remaining</span>
        
        <div class="powerup_box">
                            <div
                    id="rune4_1"
                    class="powerUpImg1"
                                            data-tooltip="[[[&quot;Secret Knowledge of the Ancients&quot;,&quot;#DDDDDD&quot;],[&quot;Those who master the techniques of the ancestors can get, evaluate and complete quests more quickly.&quot;,&quot;#808080&quot;,400],[&quot;The cooldown time for accepting quests is reduced by 50%. &lt;br \/&gt; The Ruby costs for shortening quest cooldown time is reduced to 1 Ruby.&quot;,&quot;#00B712&quot;]]]"                                                                onClick="blackoutDialog(true, 'changeRune4_1', 700)"
                        style="cursor: pointer; background-image: url(//gf3.geo.gfsrv.net/cdn8e/76cf549a92ff7a43f80771659170f5.jpg);"
                                    ></div>
                            <div
                    id="rune4_2"
                    class="powerUpImg2"
                                            data-tooltip="[[[&quot;Secret knowledge of the beast master&quot;,&quot;#DDDDDD&quot;],[&quot;The ancient secrets of the beast master not only include the high art of tracking, but also a wide knowledge about the creatures of the wilderness.&quot;,&quot;#808080&quot;,400],[&quot;+5% Learning chance for expedition bonuses&quot;,&quot;#00B712&quot;]]]"                                                                style="background-image: url(//gf2.geo.gfsrv.net/cdn74/f3cfbac37f35502acc79cf46535a41.jpg);"
                                    ></div>
                            <div
                    id="rune4_3"
                    class="powerUpImg3"
                                            data-tooltip="[[[&quot;Secret knowledge of the artefact collector&quot;,&quot;#DDDDDD&quot;],[&quot;The secret cult of the artefact collector has been collecting information about all occult and mystic items since the beginning of the Roman empire.&quot;,&quot;#808080&quot;,400],[&quot;+15% Chance of finding an item&quot;,&quot;#00B712&quot;]]]"                                                                onClick="blackoutDialog(true, 'changeRune4_3', 700)"
                        style="cursor: pointer; background-image: url(//gf3.geo.gfsrv.net/cdne1/073b551a230f249a3a4fa083b4fcff.jpg);"
                                    ></div>
                            <div
                    id="rune4_4"
                    class="powerUpImg4"
                                            data-tooltip="[[[&quot;Secret knowledge of the assassins&quot;,&quot;#DDDDDD&quot;],[&quot;The secret society of assassins teaches strictly protected techniques of sneaking, acrobatics and evasion.&quot;,&quot;#808080&quot;,400],[&quot;The maximum agility is increased by 54 &amp;nbsp;&quot;,&quot;#00B712&quot;],[&quot;Needs level 40&quot;,&quot;#FF0000&quot;]]]"                                                                onClick="blackoutDialog(true, 'changeRune4_4', 700)"
                        style="cursor: pointer; background-image: url(//gf3.geo.gfsrv.net/cdn5e/234e0c2663d8e28a45c5b9c3241006.jpg);"
                                    ></div>
                            <div
                    id="rune4_5"
                    class="powerUpImg5"
                                            data-tooltip="[[[&quot;Secret knowledge of the immortals&quot;,&quot;#DDDDDD&quot;],[&quot;The box of the immortals is said to possess an elixir which gives eternal life.&quot;,&quot;#808080&quot;,400],[&quot;The maximum constitution is increased by 32 &amp;nbsp;&quot;,&quot;#00B712&quot;],[&quot;Needs level 60&quot;,&quot;#FF0000&quot;]]]"                                                                onClick="blackoutDialog(true, 'changeRune4_5', 700)"
                        style="cursor: pointer; background-image: url(//gf2.geo.gfsrv.net/cdna3/7236fdf07b3079e56f909a67dec917.jpg);"
                                    ></div>
                    </div>

        <div class="powerup_buy_extend">
            <form method="POST" id="buyForm4" action="index.php?mod=powerups&submod=activatePowerUp&sh=f16505f156f9d98227413fc6076fb1dc" style="margin: 0;">
                <input type="hidden" id="hiddenPowerUp4Nr" name="activatePowerUpNr" value="2" />
                <input type="hidden" name="activatePowerUpCatNr" value="4" />
            </form>
                            <a href="#" onClick="buyRune('4', '2')">
                                            Further 14 days for only 15 <a href="index.php?mod=premium&submod=rubies&sh=f16505f156f9d98227413fc6076fb1dc"><img alt="" src="//gf1.geo.gfsrv.net/cdn92/03d0e8e4718d33a582132485433bb7.gif" title="Rubies" align="absmiddle" border="0" /></a>                                    </a>
                    </div>
    </section>

<script type="text/javascript" src="//gf2.geo.gfsrv.net/cdn77/5dd33c67566e59410ba1ab536495c5.js"></script>
<script type="text/javascript">
                        objektAusgrauen(false, 'powerup1', false, false);
        
                                    objektAusgrauen(false, 'rune1_1', true, false);
                                                objektAusgrauen(false, 'rune1_2', true, true);
                                                objektAusgrauen(true, 'rune1_5', true, false);
                                                objektAusgrauen(false, 'rune1_3', true, true);
                                                objektAusgrauen(true, 'rune1_4', true, false);
                                            objektAusgrauen(false, 'powerup2', false, false);
        
                                    objektAusgrauen(false, 'rune2_1', true, false);
                                                objektAusgrauen(false, 'rune2_2', true, true);
                                                objektAusgrauen(false, 'rune2_3', true, true);
                                                objektAusgrauen(true, 'rune2_4', true, false);
                                                objektAusgrauen(true, 'rune2_5', true, false);
                                            objektAusgrauen(false, 'powerup3', false, false);
        
                                    objektAusgrauen(false, 'rune3_1', true, false);
                                                objektAusgrauen(false, 'rune3_3', true, true);
                                                objektAusgrauen(true, 'rune3_4', true, false);
                                                objektAusgrauen(false, 'rune3_2', true, true);
                                                objektAusgrauen(true, 'rune3_5', true, false);
                                            objektAusgrauen(false, 'powerup4', false, false);
        
                                    objektAusgrauen(false, 'rune4_1', true, true);
                                                objektAusgrauen(false, 'rune4_2', true, false);
                                                objektAusgrauen(false, 'rune4_3', true, true);
                                                objektAusgrauen(true, 'rune4_4', true, false);
                                                objektAusgrauen(true, 'rune4_5', true, false);
                        </script>

<div id="blackoutDialognotification" class="cancel_confirm">
    <div class="blackoutDialog_header pngfix">
        <div id="header_notification" style="font-size:large;text-align:center"></div>
    </div>
    <div class="blackoutDialog_body pngfix">
        <div id="picturenotification" class="blackoutDialog_icon"></div>
        <div id="dialogTxtnotification" class="blackoutDialog_text"><p></p></div>
        <br class="clearfloat" />
        <table class="blackoutDialog_buttons">
            <tr>
                <td id="buttonleftnotification">
                    <input type="submit" class="awesome-button big" value="" id="linknotification" />
                </td>
                <td id="buttonrightnotification">
                    <input type="submit" class="awesome-button big" value="Cancel" onclick="blackoutDialog(false)" id="linkcancelnotification" />
                </td>
            </tr>
        </table>
    </div>
    <div class="blackoutDialog_footer pngfix">
    </div>
</div>    <script type="text/javascript">
        var tutorialData = {"forge":{"settingsTitle":"Show forge tutorial","steps":[{"settingsName":"Select your item","text":"<br>Welcome to the forge! It`s great that you found your way here.<br><br>You can produce your own equipment here. Simply choose the item you wish to create, select a suitable prefix and\/or suffix and the process starts.<br><br>Please note however, that you can only forge prefixes and suffixes that you have already learned. Complete `expeditions` and find scrolls to learn these.<br><br>You can recognise which prefix or suffix a scroll teaches you by the scroll name: <br>For example, `Tinuviel`s Scroll` enables you to learn the prefix `Tinuviel`; the `Scroll of Love` instead unlocks the `of Love` suffix.<br><br>The info box on the right-hand side reveals all the latest information regarding your forging process: forge duration, resource requirements and success rate for the process are all listed.<br><br>Once you start the rental you receive a forge store, in which you can place all required resources for your item bit by bit. <br><br>Note: if you change your mind and cancel your forging request, you will receive a large portion of your resources back but the rental costs will not be refunded!<br><br>","visibility":true},{"settingsName":"Hunter and Collector","text":"<br>You are currently in your forge store. You can add all the required resources for your desired item here little by little. Drag the required resource into the store inventory and they will automatically be added to your store. The rate of success and the quality level of your item will naturally depend on the resources that you use to create it.<br><br>A rule of thumb: better resources make better items! <br><br>If you have everything you need then you can start the forge process by clicking on `Forge`.<br><br>","visibility":true},{"settingsName":"EUREKA!","text":"<br>Congratulations, you have successfully forged an item. Now you can drag the item from the forge box to your inventory.<br><br>","visibility":true},{"settingsName":"What a disaster!","text":"<br>Unfortunately a catastrophic error occurred during the forging and you are now stood before the pitiful remains of your forging work.<br><br>Despite your failure, you receive some of your resources back and can now decide what your next move is: <br>Have them wrapped into a package and delivered to you or, for a fee, store them directly into a new forging space for the same item to have the forging process attempted once again.<br><br>","visibility":true}]}};

        function updateTutorialState(category, step, visibility) {
            return function() {
                var option               = new Array();
                option['spinnerVisible'] = false;
                sendAjax(
                    jQuery(this), 
                    'ajax.php', 
                    'mod=settings&submod=updateTutorialState&category=' + category + '&step=' + step + '&visibility=' + !jQuery('#tutorialDoNotShowAgain').prop('checked'), 
                    null, 
                    null, 
                    option
                );
                
                tutorialData[category].steps[step].visibility = !jQuery('#tutorialDoNotShowAgain').prop('checked');
            }
        }
        
        function showTutorial(data)
        {
            if (tutorialData[data.category] !== undefined && 
                tutorialData[data.category].steps[data.step] !== undefined && 
                tutorialData[data.category].steps[data.step].visibility === true
            ) {
                var tutorialContainer = jQuery('.tutorial');
                var content           = tutorialData[data.category].steps[data.step];
                
                tutorialContainer.find('.title').html(content.settingsName);
                tutorialContainer.find('.text').html(content.text);
                
                tutorialContainer.fadeIn("normal");
                jQuery('#tutorialDoNotShowAgain').on('click', updateTutorialState(data.category, data.step, jQuery(this)));
            }
        }
        
        jQuery(document).ready(function() {
            jQuery('.tutorial .awesome-button').on('click', function(){
                jQuery('.tutorial').fadeOut('normal');
            })
        });
    </script>
    <div class="tutorial" style="display: none;">
        <div class="title">
        </div>
        <div class="text">
        </div>
        <div class="footer">
            <input type="checkbox" name="tutorialDoNotShowAgain" id="tutorialDoNotShowAgain"  />
            <label for="tutorialDoNotShowAgain">Don`t show this tutorial step again</label>
            <br />
            <div class="awesome-button">Close</div>
        </div>
    </div>

                        </div>
                    </div>
                </div>

                <div id="footer">
                    <div id="footer_background" class="pngfix"></div>
                    <div id="footer_inner">
                        <div id="footer_logos">
                            <p id="safeplay"><a href="http://corporate.gameforge.com/en/products/safe-play/" target="_blank"></a></p>
                                                        <p id="logoGameforge"><a href="//en.gameforge.com" target="_blank">Gameforge</a></p>
                        </div>
                        <div class="footer_links">
                            <a href="http://agbserver.gameforge.com/rewrite.php?game=gladiatus&page=imprint&lang=en" target="_blank">Legal notice</a>&nbsp;|&nbsp;
                            <a href="http://agbserver.gameforge.com/rewrite.php?game=gladiatus&page=terms&lang=en" target="_blank">T&C</a>&nbsp;|&nbsp;
                            <a href="http://agbserver.gameforge.com/rewrite.php?game=gladiatus&page=privacy&lang=en" target="_blank">Privacy Policy</a>&nbsp;|&nbsp;
                            <a href="index.php?mod=stuff&submod=gameRules&sh=f16505f156f9d98227413fc6076fb1dc">Rules</a> |
                            <a target="_blank" href="https://forum.gladiatus.gameforge.com/forum/thread/1101-changelogs-game-updates/?action=firstNew">4.6.1-pl6</a>
                        </div>                            
                        <p id="copyright">© 2007 <a href="http://gameforge.com" target="_blank" class="copyright">Gameforge 4D GmbH</a>. All rights reserved.</p>
                    </div>
                </div>
            </div>
        </div>

        <script type="text/javascript">
    var lang          = {"DAY":"Day","DAYS":"Days","COOLDOWN":"Cooldown","CAST_TIME":"Incubation time","SAVED":"Entry saved.","CANNOT_SAVE":"Could not be saved!","SHOW_ACTIVE_BUFFS":"Active bonuses"};
    var _translations = {"recipe":"Scroll","white":"White","green":"Green","blue":"Blue","purple":"Purple","orange":"Orange","red":"Red"};
    var secureHash    = "f16505f156f9d98227413fc6076fb1dc";
    var playerId      = 23177;
    var dollId        = 1;
</script>                        <script type="text/javascript">
        var currentMousePos = { x: -1, y: -1 };
        
        jQuery(function() {
            jQuery("#draggable").draggable().resizable();
            jQuery(document).mousemove(function(event) {
                currentMousePos.x = event.pageX;
                currentMousePos.y = event.pageY;
            });
        });
        </script>
        
<!-- #MMO:NETBAR# -->
<div id="pagefoldtarget"></div>
<script type="text/javascript" data-cookieconsent="ignore">
    var mmoCSS = ' body {margin:0; padding:0;} div.openX_interstitial div.openX_int_closeButton a { text-indent:-4000px; float:right; height:23px; width:23px; display:block; background:transparent url(//gf2.geo.gfsrv.net/cdn14/7618d1159940178a2e53a8be22710a.png) repeat-x; } #mmonetbar { background:transparent url(//gf2.geo.gfsrv.net/cdna4/36f7db7ac887ed3ab4242fb7286211.bg) repeat-x; font:normal 11px Tahoma, Arial, Helvetica, sans-serif; height:32px; left:0; padding:0; position:absolute; text-align:center; top:0; width:100%; z-index:3000; } #mmonetbar #mmoContent { height:32px; margin:0 auto; width:1024px; position: relative; } #mmonetbar .mmosmallbar {width:585px !important;} #mmonetbar .mmosmallbar div.mmoBoxMiddle { width: 290px; } #mmonetbar .mmonewsout {width:800px !important;} #mmonetbar .mmouseronlineout {width:768px !important;} #mmonetbar .mmolangout {width:380px !important;} #mmonetbar .mmolangout .mmoGame { width: 265px; } #mmonetbar #mmoContent.mmoingame { width: 533px; } #mmonetbar #mmoContent.mmoingame .mmoGame { width: auto; } #mmonetbar a, #mmoNetbarSubmenu a { color:#666; font:normal 11px Tahoma, Arial, Helvetica, sans-serif; outline: none; text-decoration:none; white-space:nowrap; } #mmonetbar select { background-color:#452317 !important; border:1px solid #000 !important; color:#dcbb96 !important; font:normal 11px Verdana, Arial, Helvetica, sans-serif; height:18px; margin-top:3px; width:100px; } #mmonetbar .mmoGames select {width:80px;} #mmonetbar option { background-color:#452317 !important; color:#dcbb96 !important; } #mmonetbar option:hover { background-color:#5b3021 !important; } #mmonetbar select#mmoCountry {width:120px;} #mmonetbar .mmoSelectbox { background-color:#452317; float:left; margin:3px 0 0 3px; position:relative; } * html #mmonetbar .mmoSelectbox {position:static;} *+html #mmonetbar .mmoSelectbox {position:static;} #mmonetbar #mmoOneGame {cursor:default; height:14px; margin-top:3px; padding-left:5px; width:80px;} #mmonetbar .label {float:left; font-weight:bold; margin-right:4px; overflow:hidden !important;} #mmonetbar #mmoUsers .label {font-size:10px;} #mmonetbar .mmoBoxLeft, #mmonetbar .mmoBoxRight { background:transparent url(//gf2.geo.gfsrv.net/cdnae/c326c1586bef7a0a9d7ef57b64648c.sprites) no-repeat -109px -4px; float:left; width:5px; height:24px; } #mmonetbar .mmoBoxRight {background-position:-126px -4px;} #mmonetbar .mmoBoxMiddle { background:transparent url(//gf2.geo.gfsrv.net/cdna4/36f7db7ac887ed3ab4242fb7286211.bg) repeat-x 0 -36px; color:#dcbb96 !important; float:left; height:24px; line-height:22px; text-align:left; white-space:nowrap; position: relative; z-index: 10000; } #mmonetbar #mmoGames, #mmonetbar #mmoLangs {margin:0px 4px 0 0;} #mmonetbar #mmoNews, #mmonetbar #mmoUsers, #mmonetbar #mmoGame, #mmonetbar .nojsGame {margin:4px 4px 0 0;} #mmonetbar #mmoLogo { background:transparent url(//gf2.geo.gfsrv.net/cdnae/c326c1586bef7a0a9d7ef57b64648c.sprites) no-repeat top left; float:left; display:block; height:32px; width:108px; text-indent: -9999px; position: relative; z-index: 1 } #mmonetbar #mmoNews {float:left; width:252px;} #mmonetbar #mmoNews #mmoNewsContent {text-align:left; width:200px;} #mmonetbar #mmoNews #mmoNewsticker {overflow:hidden; width:240px;} #mmonetbar #mmoNews #mmoNewsticker ul { margin: 0; padding: 0; list-style: none; } #mmonetbar #mmoNews #mmoNewsticker ul li { font:normal 11px/22px Tahoma, Arial, Helvetica, sans-serif !important; color:#dcbb96 !important; padding: 0; margin: 0; background: none; display: none; } #mmonetbar #mmoNews #mmoNewsticker ul li.mmoTickShow { display: block; } #mmonetbar #mmoNews #mmoNewsticker ul li a img {border:0;} #mmonetbar #mmoNews #mmoNewsticker ul li a {color:#dcbb96 !important;display:block;height:24px;line-height:23px;} #mmonetbar #mmoNews #mmoNewsticker ul li a:hover {text-decoration:underline;} #mmonetbar #mmoUsers {float:left; width:178px;} #mmonetbar #mmoUsers .mmoBoxLeft {width:17px;} #mmonetbar #mmoUsers .mmoBoxMiddle {padding-left:3px; width:150px;} #mmonetbar .mmoGame {display:none; float:left; width:432px;} #mmonetbar .mmoGame #mmoGames {float:left; width:206px;} #mmonetbar .mmoGame #mmoLangs {float:left; margin:0; width:252px;} #mmonetbar .mmoGame label { color:#dcbb96 !important; float:left; font-weight:400 !important; line-height:22px; margin:0px; text-align:right !important; width:110px; font-size: 11px !important; } #mmonetbar .nojsGame {display:block; width:470px;} #mmonetbar .nojsGame .mmoBoxMiddle {width:450px;} #mmonetbar .nojsGame .mmoSelectbox {margin:0px 0 0 3px;} *+html #mmonetbar .nojsGame .mmoSelectbox {margin:2px 0 0 3px;} * html #mmonetbar .nojsGame .mmoSelectbox {margin:2px 0 0 3px;} #mmonetbar .nojsGame .mmoGameBtn { background:transparent url(//gf2.geo.gfsrv.net/cdnae/c326c1586bef7a0a9d7ef57b64648c.sprites) no-repeat -162px -7px; border:none; cursor:pointer; float:left; height:18px; margin:3px 0 0 7px; padding:0; width:18px; } #mmonetbar .mmoSelectArea { border:1px solid #000; color:#dcbb96 !important; display:block !important; float:none; font-weight:400 !important; font-size:11px; height:16px; line-height:13px; -moz-box-sizing: content-box; overflow:hidden !important; width:90px; } #mmonetbar #mmoLangSelect .mmoSelectArea {width:129px;} #mmonetbar #mmoLangSelect .mmoOptionsDivVisible {min-width:129px;} #mmonetbar .mmoSelectArea .mmoSelectButton { background: url(//gf2.geo.gfsrv.net/cdnae/c326c1586bef7a0a9d7ef57b64648c.sprites) no-repeat -141px -8px; float:right; width:17px; height:16px; } #mmonetbar .mmoSelectText {cursor:pointer; float:left; overflow:hidden; padding:1px 2px; width:68px;} #mmonetbar #mmoLangSelect .mmoSelectText {width:107px;} #mmonetbar #mmoOneLang {cursor:default; height:14px;} #mmonetbar div.mmoOneLang { background: none; } #mmonetbar div.mmoOneLang #mmoOneLang { border: none; padding: 2px 3px; } #mmonetbar .mmoOptionsDivInvisible, #mmonetbar .mmoOptionsDivVisible { background-color: #452317 !important; border: 1px solid #000; position: absolute; min-width:90px; z-index: 3100; } * html #mmonetbar .mmoOptionsDivVisible .highlight {background-color:#5b3021 !important} #mmonetbar .mmoOptionsDivInvisible {display: none;} #mmonetbar .mmoOptionsDivVisible ul { border:0; font:normal 11px Tahoma, Arial, Helvetica, sans-serif; list-style: none; margin:0; padding:2px; overflow:auto; overflow-x:hidden; } #mmonetbar #mmoLangs .mmoOptionsDivVisible ul {min-width:125px;} #mmonetbar .mmoOptionsDivVisible ul li { background-color: #452317; height:14px; padding:2px 0; } #mmonetbar .mmoOptionsDivVisible a { color: #dcbb96 !important; display: block; font-weight:400 !important; height:16px !important; min-width:80px; text-decoration: none; white-space:nowrap; width:100%; } #mmonetbar #mmoContent .mmoLangList a {min-width:102px;} #mmonetbar .mmoOptionsDivVisible li:hover {background-color: #5b3021;} #mmonetbar .mmoOptionsDivVisible li a:hover {color: #dcbb96 !important;} #mmonetbar .mmoOptionsDivVisible li.mmoActive {background-color: #5b3021 !important;} #mmonetbar .mmoOptionsDivVisible li.mmoActive a {color: #dcbb96 !important;} #mmonetbar .mmoOptionsDivVisible ul.mmoListHeight {height:240px} #mmonetbar .mmoOptionsDivVisible ul.mmoLangList.mmoListHeight li {padding-right:15px !important; width:100%;} #mmonetbar #mmoGameSelect ul.mmoListHeight a {min-width:85px;} #mmonetbar #mmoLangSelect ul.mmoListHeight a {min-width:105px;} #mmonetbar #mmoFocus {position:absolute;left:-2000px;top:-2000px;} #mmonetbar #mmoLangs .mmoSelectText span, #mmonetbar #mmoLangs .mmoflag { background: transparent url(//gf3.geo.gfsrv.net/cdn28/71fe874d78b03e38e06a3b471f6224.png) no-repeat; height:14px !important; padding-left:23px; } .mmo_AE {background-position:left 0px !important} .mmo_AR {background-position:left -14px !important} .mmo_BE {background-position:left -28px !important} .mmo_BG {background-position:left -42px !important} .mmo_BR {background-position:left -56px !important} .mmo_BY {background-position:left -70px !important} .mmo_CA {background-position:left -84px !important} .mmo_CH {background-position:left -98px !important} .mmo_CL {background-position:left -112px !important} .mmo_CN {background-position:left -126px !important} .mmo_CO {background-position:left -140px !important} .mmo_CZ {background-position:left -154px !important} .mmo_DE {background-position:left -168px !important} .mmo_DK {background-position:left -182px !important} .mmo_EE {background-position:left -196px !important} .mmo_EG {background-position:left -210px !important} .mmo_EN {background-position:left -224px !important} .mmo_ES {background-position:left -238px !important} .mmo_EU {background-position:left -252px !important} .mmo_FI {background-position:left -266px !important} .mmo_FR {background-position:left -280px !important} .mmo_GR {background-position:left -294px !important} .mmo_HK {background-position:left -308px !important} .mmo_HR {background-position:left -322px !important} .mmo_HU {background-position:left -336px !important} .mmo_ID {background-position:left -350px !important} .mmo_IL {background-position:left -364px !important} .mmo_IN {background-position:left -378px !important} .mmo_INTL {background-position:left -392px !important} .mmo_IR {background-position:left -406px !important} .mmo_IT {background-position:left -420px !important} .mmo_JP {background-position:left -434px !important} .mmo_KE {background-position:left -448px !important} .mmo_KR {background-position:left -462px !important} .mmo_LT {background-position:left -476px !important} .mmo_LV {background-position:left -490px !important} .mmo_ME {background-position:left -504px !important} .mmo_MK {background-position:left -518px !important} .mmo_MX {background-position:left -532px !important} .mmo_NL {background-position:left -546px !important} .mmo_NO {background-position:left -560px !important} .mmo_PE {background-position:left -574px !important} .mmo_PH {background-position:left -588px !important} .mmo_PK {background-position:left -602px !important} .mmo_PL {background-position:left -616px !important} .mmo_PT {background-position:left -630px !important} .mmo_RO {background-position:left -644px !important} .mmo_RS {background-position:left -658px !important} .mmo_RU {background-position:left -672px !important} .mmo_SE {background-position:left -686px !important} .mmo_SI {background-position:left -700px !important} .mmo_SK {background-position:left -714px !important} .mmo_TH {background-position:left -728px !important} .mmo_TR {background-position:left -742px !important} .mmo_TW {background-position:left -756px !important} .mmo_UA {background-position:left -770px !important} .mmo_UK {background-position:left -784px !important} .mmo_US {background-position:left -798px !important} .mmo_VE {background-position:left -812px !important} .mmo_VN {background-position:left -826px !important} .mmo_YU {background-position:left -840px !important} .mmo_ZA {background-position:left -854px !important} .mmo_WW {background-position:left -392px !important} .mmo_AU {background-position:left -868px !important} div#mmonetbar a:active { top: 0; } div#mmoGamesOverviewPanel { width: 582px; position: absolute; top: 0; right: 0; font: 12px Arial, sans-serif; } div#mmoGamesOverviewPanel h4, div#mmoGamesOverviewPanel h5 { margin: 0; font-size: 12px; font-weight: bold; text-align: left; } div#mmoGamesOverviewPanel a { text-decoration: none; } div#mmoGamesOverviewPanel a img { border: none; } div#mmoGamesOverviewToggle { width: 168px; padding: 4px 0 4px 414px; } div#mmoGamesOverviewToggle h4 { height: 18px; position: relative; background: url(//gf2.geo.gfsrv.net/cdna4/36f7db7ac887ed3ab4242fb7286211.bg) repeat-x 0 -36px; top: 0px; padding: 3px 20px; -moz-box-sizing: content-box; } div#mmoGamesOverviewToggle h4 a { display: block; width: 116px; height: 16px; line-height: 14px; text-align: left; font-weight: normal; outline: none; color: #dcbb96 !important; font-size: 11px !important; position: relative; border: 1px solid #000; padding: 0 0 0 10px; background: #452317; -moz-box-sizing: content-box; } div#mmoGamesOverviewToggle h4 a.gameCountZero { cursor: default; text-align: center; padding: 0; width: 126px; } div#mmoGamesOverviewToggle h4 a span.mmoNbPseudoSelect_icon { display: block; position: absolute; top: 0; right: 0; width: 17px; height: 16px; background: url(//gf2.geo.gfsrv.net/cdnae/c326c1586bef7a0a9d7ef57b64648c.sprites) no-repeat -141px -8px; } span.iconTriangle { display: block; position: absolute; top: 5px; right: 10px; width: 0px; border: 5px solid transparent; border-bottom-color: #dcbb96; } div#mmoGamesOverviewToggle h4 a.toggleHidden { } div#mmoGamesOverviewToggle h4 a.toggleHidden span.iconTriangle { top: 10px; border: 5px solid transparent; border-top-color: #dcbb96; } div#mmoGamesOverviewToggle h4 span.mmoNbBoxEdge { display: block; width: 5px; height: 24px; background: url(//gf2.geo.gfsrv.net/cdnae/c326c1586bef7a0a9d7ef57b64648c.sprites) no-repeat -109px -4px; position: absolute; top: 0; } div#mmoGamesOverviewToggle h4 span.mmoNbBoxEdge_left { left: 0; } div#mmoGamesOverviewToggle h4 span.mmoNbBoxEdge_right { right: 0; background-position: -126px -4px; } div#mmoGamesOverviewLists { clear: both; background: #452317; width: 580px; border: 1px solid #000; float: left; position: relative; top: 0px; -moz-box-sizing: content-box; } div#mmoGamesOverviewLists h5 { clear: both; width: 544px; margin: 0; padding: 0 18px; height: 27px; line-height: 27px; color: #dcbb96; border-bottom: 1px solid #000; background: url(//gf2.geo.gfsrv.net/cdna4/36f7db7ac887ed3ab4242fb7286211.bg) repeat-x 0 -3px; font-family: inherit; -moz-box-sizing: content-box; } div#mmoGamesOverviewLists h5 a { color: #dcbb96; font-weight: bold; line-height: 27px; } #mmoGamesOverviewLists #mmoGamesOverview_featured li { width: auto; } #mmoGamesOverviewLists #mmoGamesOverview_featured span.gameImgTarget { display: block; width: 560px; height: 180px; margin: 0; } #mmoGamesOverviewLists #mmoGamesOverview_featured span.gameName { display: none; } #mmoGamesOverview_featured img { display: block; } div#mmoGamesOverviewLists ul { margin: 0; padding: 5px 5px; list-style: none; width: 570px; float: left; text-align: left; -moz-box-sizing: content-box; } div#mmoGamesOverviewLists ul li { margin: 0; padding: 0; list-style: none; width: 190px; float: left; background: none; } div#mmoGamesOverviewLists ul li a, #mmoNetbarSubmenu .submenu_item a { display: block; padding: 5px; font-weight: bold; line-height: 1; color: #dcbb96 !important; font-size: 11px !important; } div#mmoGamesOverviewLists ul li a:focus, div#mmoGamesOverviewLists ul li a:hover, #mmoNetbarSubmenu .submenu_item a:focus, #mmoNetbarSubmenu .submenu_item a:hover { background-color: #5b3021; } div#mmoGamesOverviewLists ul li a span.gameImgTarget { display: block; width: 180px; height: 90px; background: none; margin: 0 0 4px 0; } div#mmoGamesOverviewLists ul li a span img { display: block; } #mmoNetbarSubmenu .submenu { background-color: #452317; display: none; position: absolute; z-index: 11111; } div#mmoGamesOverviewLists div#mmoGamesOverviewCountry { width: 20px; height: 14px; position: absolute; top: 6px; right: 12px; background-image: url(//gf3.geo.gfsrv.net/cdn28/71fe874d78b03e38e06a3b471f6224.png); background-repeat: no-repeat; } #mmonetbar div.nojsGame { width: 432px !important; } #mmonetbar div.nojsGame div.mmoBoxMiddle { width: 422px; } #mmonetbar div.nojsGame label { width: 105px; } #pagefoldtarget .nbPF { position: absolute; top: 0; z-index: 999999; text-indent: -9999px; width: 125px; height: 120px; } #pagefoldtarget .nbPFLeft { left: 0px; } #pagefoldtarget .nbPF.nbPFRight { right: 0px; background-position: right 0px } #pagefoldtarget .nbPFDark.nbPFRight { background-image: url(//gf2.geo.gfsrv.net/cdn75/98954a5b65ea8ac2b5472017426515.png); _background-image: url(//gf1.geo.gfsrv.net/cdn9d/ca3b68a0f2fc9b5fd4f4e9acc1aa9f.gif); } #pagefoldtarget .nbPFDark.nbPFLeft { background-image: url(//gf2.geo.gfsrv.net/cdndd/f3329ffdb5f66db6930cd98f547da7.png); _background-image: url(//gf1.geo.gfsrv.net/cdn37/470d765043864d857eb6ffdc30bc4d.gif); } #pagefoldtarget .nbPFLight.nbPFRight { background-image: url(//gf1.geo.gfsrv.net/cdn34/8ae6ba8194f659bc3784e01b457749.png); _background-image: url(//gf2.geo.gfsrv.net/cdn46/2634bb44de90d88b10e3fe8cf940ff.gif); } #pagefoldtarget .nbPFLight.nbPFLeft { background: url(//gf1.geo.gfsrv.net/cdn38/d4718fc349f75778ee051b4cc76824.png) no-repeat; _background-image: url(//gf1.geo.gfsrv.net/cdn01/3dc42ed780058a74a17220804afda1.gif); } #pagefoldtarget .nbPF a{ text-indent: -9999px; display: block; width: 110px; height: 95px; } #pagefoldtarget .nbPF.nbPFRight a{ float:right; } #pagefoldtarget .nbPF.nbPFHover a{ width:358px; height: 320px; } #pagefoldtarget .nbPF.nbPFHover { background-position: left -129px !important; width:400px; height: 400px; } #pagefoldtarget .nbPF.nbPFRight.nbPFHover { background-position: right -129px !important; } ';
    var mmostyle = document.createElement('style');
    if (navigator.appName == "Microsoft Internet Explorer") {
        mmostyle.setAttribute("type", "text/css");
        mmostyle.styleSheet.cssText = mmoCSS;
    } else {
        var mmostyleTxt = document.createTextNode(mmoCSS);
        mmostyle.type = 'text/css';
        mmostyle.appendChild(mmostyleTxt);
    }
    document.getElementsByTagName('head')[0].appendChild(mmostyle);
</script>

<noscript>
    <style type="text/css">

         body {margin:0; padding:0;} #mmonetbar { background:transparent url(//gf2.geo.gfsrv.net/cdna4/36f7db7ac887ed3ab4242fb7286211.bg) repeat-x; font:normal 11px Tahoma, Arial, Helvetica, sans-serif; height:32px; left:0; padding:0; position:absolute; text-align:center; top:0; width:100%; z-index:3000; } #mmonetbar #mmoContent { height:32px; margin:0 auto; width:1024px; position: relative; } #mmonetbar #mmoLogo { background:transparent url(//gf2.geo.gfsrv.net/cdnae/c326c1586bef7a0a9d7ef57b64648c.sprites) no-repeat top left; float:left; display:block; height:32px; width:108px; text-indent: -9999px; } #mmonetbar #mmoNews, #mmonetbar #mmoGame, #mmonetbar #mmoFocus, #pagefoldtarget { display:none !important; } 
    </style>
</noscript>

<div id="mmoNetbarSubmenu"><div id='submenu_LittleGames' class='submenu' onmouseenter='mmoHoverMenu("LittleGames")' onmouseleave='mmoLeaveMenu("LittleGames")'><div class='submenu_item'><a href='https://gameforge.com/en-US/littlegames/snake-games/' target='_blank'>Snake Games</a></div><div class='submenu_item'><a href='https://gameforge.com/en-US/littlegames/online-games/' target='_blank'>Online Games</a></div><div class='submenu_item'><a href='https://gameforge.com/en-US/littlegames/io-games/' target='_blank'>.io Games</a></div><div class='submenu_item'><a href='https://gameforge.com/en-US/littlegames/dinosaur-games/' target='_blank'>Dinosaur Games</a></div><div class='submenu_item'><a href='https://gameforge.com/en-US/littlegames/car-games/' target='_blank'>Car Games</a></div><div class='submenu_item'><a href='https://gameforge.com/en-US/littlegames/new-games/' target='_blank'>New Games</a></div><div class='submenu_item'><a href='https://gameforge.com/en-US/littlegames/2-players-games/' target='_blank'>2 Player Games</a></div><div class='submenu_item'><a href='https://gameforge.com/en-US/littlegames/arcade-games/' target='_blank'>Arcade Games</a></div><div class='submenu_item'><a href='https://gameforge.com/en-US/littlegames/boys-games/' target='_blank'>Games for boys</a></div><div class='submenu_item'><a href='https://gameforge.com/en-US/littlegames/war-games/' target='_blank'>War Games</a></div><div class='submenu_item'><a href='https://gameforge.com/en-US/littlegames/basketball-games/' target='_blank'>Basketball Games</a></div><div class='submenu_item'><a href='https://gameforge.com/en-US/littlegames/dress-up-games/' target='_blank'>Dress Up Games</a></div><div class='submenu_item'><a href='https://gameforge.com/en-US/littlegames/shooting-games/' target='_blank'>Shooting games</a></div><div class='submenu_item'><a href='https://gameforge.com/en-US/littlegames/puzzle-games/' target='_blank'>Puzzle Games</a></div><div class='submenu_item'><a href='https://gameforge.com/en-US/littlegames/multiplayer-games/' target='_blank'>Multiplayer Games</a></div><div class='submenu_item'><a href='https://gameforge.com/en-US/littlegames/strategy-games/' target='_blank'>Strategy Games</a></div><div class='submenu_item'><a href='https://gameforge.com/en-US/littlegames/racing-games/' target='_blank'>Racing Games</a></div><div class='submenu_item'><a href='https://gameforge.com/en-US/littlegames/fighting-games/' target='_blank'>Fighting Games</a></div><div class='submenu_item'><a href='https://gameforge.com/en-US/littlegames/cooking-games/' target='_blank'>Cooking Games</a></div><div class='submenu_item'><a href='https://gameforge.com/en-US/littlegames/driving-games/' target='_blank'>Driving games</a></div></div></div>
<script type="text/javascript" data-cookieconsent="ignore">
    var mmoTilehovered = false;
    var mmoMenuHovered = false;

    function mmoHoverTile(game) {
        mmoTilehovered = true;
        mmoShowSubmenu(game);
    }

    function mmoLeaveTile(game) {
        mmoTilehovered = false;
        mmoHideSubmenu(game);
    }

    function mmoHoverMenu(game) {
        mmoMenuHovered = true;
        mmoShowSubmenu(game);
    }

    function mmoLeaveMenu(game) {
        mmoMenuHovered = false;
        mmoHideSubmenu(game);
    }

    function mmoShowSubmenu(game) {
        let submenu = document.getElementById('submenu_' + game);
        if (submenu !== null) {
            let rect = document.getElementById('mmoTile_' + game).getBoundingClientRect();
            let left = rect.left + 120;
            let bottom = window.innerHeight - window.scrollY - rect.bottom + 30;
            let newStyle = {
                'display': 'block',
                'left': left + 'px',
                'bottom': bottom + 'px'
            };
            Object.assign(submenu.style, newStyle);
        }
    }

    function mmoHideSubmenu(game) {
        if (!mmoTilehovered && !mmoMenuHovered) {
            let submenu = document.getElementById('submenu_' + game);
            if (submenu !== null) {
                submenu.style.display = 'none';
            }
        }
    }
</script>

<div id="mmonetbar" class="mmogladiatus">
    <script type="text/javascript" data-cookieconsent="ignore">
function mmoEl(name){if(document.getElementById){return document.getElementById(name);}else if(document.all){return document.all[name];}else if(document.layers){return document.layers[name];}
return false;}
function mmoJump(el){window.location.href=el.options[el.selectedIndex].value;}
var mmo_tickDly=3000;var mmo_tickFadeDly=50;var mmo_tickFadeTicks=10;var mmoTickEl=null;var mmoTickItems=null;var mmoTickIdx=0;var mmoTickState=0;var mmoTickFade=1;var mmoTickHalt=false;function mmoTicker(){var f=0;try{mmoTickEl=mmoEl('mmoNewsticker');if(mmoTickEl){mmoTickItems=mmoTickEl.getElementsByTagName("li");if(mmoTickItems){f=1;}}}catch(e){f=0;}
if(!f){setTimeout(mmoTicker,10);return;}
setTimeout(mmoTicknext,0);}
function mmoTicknext(){if(mmoTickHalt){mmoTickAlphaFor(mmoTickEl,100);setTimeout(mmoTicknext,500);return;}
if(mmoTickState===0){mmoTickFade=mmoTickFade-1;mmoTickAlpha();if(mmoTickFade<=0){mmoTickState=1;setTimeout(mmoTicknext,0);return;}
setTimeout(mmoTicknext,mmo_tickFadeDly);return;}
if(mmoTickState===1){mmoTickItems[mmoTickIdx].className="";mmoTickIdx++;if(mmoTickIdx>=mmoTickItems.length)mmoTickIdx=0;mmoTickItems[mmoTickIdx].className="mmoTickShow";setTimeout(mmoTicknext,mmo_tickFadeDly);mmoTickState=2;return;}
if(mmoTickState===2){mmoTickFade=mmoTickFade+1;mmoTickAlpha();if(mmoTickFade>=mmo_tickFadeTicks){if(mmoTickItems.length<2)return;mmoTickState=0;setTimeout(mmoTicknext,mmo_tickDly);return;}
setTimeout(mmoTicknext,mmo_tickFadeDly);return;}}
function mmoTickAlpha(){var a=(100/mmo_tickFadeTicks)*mmoTickFade;mmoTickAlphaFor(mmoTickEl,a);}
function mmoTickAlphaFor(el,a){el.style.filter='Alpha(opacity='+a+')';el.style.opacity=a/100;el.style.MozOpacity=a/100;el.style.KhtmlOpacity=a/100;}
var mmoActive_select=null;function mmoInitSelect(){if(!document.getElementById)return false;document.getElementById('mmonetbar').style.display='block';document.getElementById('mmoGame').style.display='block';document.getElementById('mmoFocus').onkeyup=function(e){mmo_selid=mmoActive_select.id.replace('mmoOptionsDiv','');if(!e){e=window.event;}
if(e.keyCode)var thecode=e.keyCode;else if(e.which)var thecode=e.which;mmoSelectMe(mmo_selid,thecode);}}
function mmoSelectMe(selid,thecode){var mmolist=document.getElementById('mmoList'+selid);var mmoitems=mmolist.getElementsByTagName('li');switch(thecode){case 13:mmoShowOptions(selid);window.location=mmoActive_select.url;break;case 38:mmoActive_select.activeit.className='';var minus=((mmoActive_select.activeid-1)<=0)?'0':(mmoActive_select.activeid-1);mmoActive_select=mmoSetActive(selid,minus);break;case 40:mmoActive_select.activeit.className='';var plus=((mmoActive_select.activeid+1)>=mmoitems.length)?(mmoitems.length-1):(mmoActive_select.activeid+1);mmoActive_select=mmoSetActive(selid,plus);break;default:thecode=String.fromCharCode(thecode);var found=false;for(var i=0;i<mmoitems.length;i++){var _a=mmoitems[i].getElementsByTagName('a');if(navigator.appName.indexOf("Explorer")>-1){}else{txtContent=_a[0].textContent;}
if(!found&&(thecode.toLowerCase()===txtContent.charAt(0).toLowerCase())){mmoActive_select.activeit.className='';mmoActive_select=mmoSetActive(selid,i);found=true;}}
break;}}
function mmoSetActive(selid,itemid){mmoActive_select=null;var mmolist=document.getElementById('mmoList'+selid);var mmoitems=mmolist.getElementsByTagName('li');mmoActive_select=document.getElementById('mmoOptionsDiv'+selid);mmoActive_select.selid=selid;if(itemid!==undefined){var _a=mmoitems[itemid].getElementsByTagName('a');var textVar=document.getElementById("mmoMySelectText"+selid);textVar.innerHTML=_a[0].innerHTML;if(selid===1)textVar.className=_a[0].className;mmoitems[itemid].className='mmoActive';}
for(var i=0;i<mmoitems.length;i++){if(mmoitems[i].className==='mmoActive'){mmoActive_select.activeit=mmoitems[i];mmoActive_select.activeid=i;mmoActive_select.url=(mmoitems[i].getElementsByTagName('a'))?mmoitems[i].getElementsByTagName('a')[0].href:null;}}
return mmoActive_select;}
function mmoShowOptions(g){var _elem=document.getElementById("mmoOptionsDiv"+g);if((mmoActive_select)&&(mmoActive_select!==_elem)){mmoActive_select.className="mmoOptionsDivInvisible";document.getElementById('mmonetbar').focus();}
if(_elem.className==="mmoOptionsDivInvisible"){document.getElementById('mmoFocus').focus();mmoActive_select=mmoSetActive(g);if(document.documentElement){document.documentElement.onclick=mmoHideOptions;}else{window.onclick=mmoHideOptions;}
_elem.className="mmoOptionsDivVisible";}else if(_elem.className==="mmoOptionsDivVisible"){_elem.className="mmoOptionsDivInvisible";document.getElementById('mmonetbar').focus();}}
function mmoHideOptions(e){if(mmoActive_select){if(!e)e=window.event;var _target=(e.target||e.srcElement);if((_target.id.indexOf('mmoOptionsDiv')!==-1))return false;if(mmoisElementBefore(_target,'mmoSelectArea')===0&&(mmoisElementBefore(_target,'mmoOptionsDiv')===0)){mmoActive_select.className="mmoOptionsDivInvisible";mmoActive_select=null;}}else{if(document.documentElement)document.documentElement.onclick=function(){};else window.onclick=null;}}
function mmoisElementBefore(_el,_class){var _parent=_el;do _parent=_parent.parentNode;while(_parent&&(_parent.className!=null)&&(_parent.className.indexOf(_class)===-1))
return(_parent.className&&(_parent.className.indexOf(_class)!==-1))?1:0;}
var ua=navigator.userAgent.toLowerCase();var ie6browser=(ua.indexOf("msie 6")>-1)&&(ua.indexOf("opera")<0);function highlight(el,mod){if(ie6browser){if(mod===1&&!el.className.match(/highlight/))el.className=el.className+' highlight';else if(mod===0)el.className=el.className.replace(/highlight/g,'');}}
var mmoToggleDisplay={init:function(wrapperId){var wrapper=document.getElementById(wrapperId);if(!wrapper)return;var headline=wrapper.getElementsByTagName("h4")[0],link=headline.getElementsByTagName("a")[0];if(link.className.indexOf("gameCountZero")!==-1)return false;var panel=document.getElementById(link.hash.substr(1));mmoToggleDisplay.hidePanel(panel,link);link.onclick=function(e){mmoToggleDisplay.loadImages();mmoToggleDisplay.toggle(this,panel);return false;};mmoToggleDisplay.outerClick(wrapper,link,panel);var timeoutID=null,delay=5000;wrapper.onmouseout=function(e){if(!e){e=window.event;}
var reltg=e.relatedTarget?e.relatedTarget:e.toElement;if(reltg===wrapper||mmoToggleDisplay.isChildOf(reltg,wrapper)){return;}
timeoutID=setTimeout(function(){mmoToggleDisplay.hidePanel(panel,link);},delay);};wrapper.onmouseover=function(e){if(timeoutID){clearTimeout(timeoutID);}};},isChildOf:function(child,parent){while(child&&child!==parent){child=child.parentNode;}
return child===parent;},hidePanel:function(panel,link){panel.style.display="none";link.className="toggleHidden";},toggle:function(link,panel){panel.style.display=panel.style.display==="none"?"block":"none";link.className=link.className==="toggleHidden"?"":"toggleHidden";},outerClick:function(wrapper,link,panel){document.body.onclick=function(e){if(!e){e=window.event}
if(!(mmoToggleDisplay.isChildOf((e.target||e.srcElement),wrapper))&&panel.style.display!=="none"){mmoToggleDisplay.toggle(link,panel);}}},loadImages:function(){var script=document.createElement("script");script.type="text/javascript";var jsonGameData_browser='{"ogame":"\/\/gf3.geo.gfsrv.net\/cdn53\/79cd6fb6cbf4dd1b2c82362701f28f.jpg","ikariam":"\/\/gf1.geo.gfsrv.net\/cdn6a\/6a39169629249ac6c33ad5c6209a8e.jpg","battleknight":"\/\/gf3.geo.gfsrv.net\/cdn88\/1078f8c8b702f6c00bd80540a15de4.png","bitefight":"\/\/gf1.geo.gfsrv.net\/cdn3f\/d53efd82d430eaa71b708336af9624.png","kingsage":"\/\/gf1.geo.gfsrv.net\/cdncd\/48d4d41c64ce8cd6d180828935ef80.png","LittleGames":"\/\/gf1.geo.gfsrv.net\/cdn38\/66b5d81c98a38def3fab97a3e3e6bd.jpg","GMag":"\/\/gf1.geo.gfsrv.net\/cdnf0\/88646a3b18e92d232bad5b8391cec7.jpg"}',jsonGameData_client='{"aionclassic":"\/\/gf3.geo.gfsrv.net\/cdnbc\/158941a97e65dcf5842ce48b69713f.jpg","metin2":"\/\/gf1.geo.gfsrv.net\/cdn31\/42e645397ef450be0886499f765855.jpg","nostale":"\/\/gf2.geo.gfsrv.net\/cdnd0\/874807298a036f7ce415d59919a608.png","tss":"\/\/gf2.geo.gfsrv.net\/cdna4\/ecef59183e07625f936f8a12687668.jpg","aion":"\/\/gf1.geo.gfsrv.net\/cdn07\/a155657608cd0cdff7417c8d18aac1.jpg","elsword":"\/\/gf3.geo.gfsrv.net\/cdn28\/f1d511fc6386d1242f9928eac92079.jpg","4story":"\/\/gf1.geo.gfsrv.net\/cdn9f\/35e42e0330b32d00feda51fefb72cd.png","runesofmagic":"\/\/gf1.geo.gfsrv.net\/cdn69\/35877003ccc87e5e1c9d1c31e3f8ae.jpg"}',jsonGameData_featured='{"tinythor":"\/\/gf2.geo.gfsrv.net\/cdnd7\/66c2d8c4a22cad0a670989003da5b5.teaser"}';script.text='';script.text+=' mmoToggleDisplay.callback('+jsonGameData_featured+', "featured");';script.text+=' mmoToggleDisplay.callback('+jsonGameData_client+', "client");';script.text+='mmoToggleDisplay.callback('+jsonGameData_browser+', "browser");';document.getElementsByTagName("head")[0].appendChild(script);mmoToggleDisplay.loadImages=function(){};},callback:function(data,gamesCat){for(var gameName in data){var gameSpan=document.getElementById("gameImgTarget_"+gameName);if(!gameSpan){return false;}
var gameImg=document.createElement("img");gameImg.src=""+data[gameName];gameImg.alt="";gameSpan.appendChild(gameImg);}}};    </script>
    <div id="mmoContent" class="mmonewsout">

        <a id="mmoLogo" target="_blank"
           href="https://en.gameforge.com/games/gladiatus?kid=5-29807-03707-1105-101121f3"
           title="Gameforge.com &ndash; Feel free to play">Gameforge.com &ndash; Feel free to play</a>

                    <!-- news -->
            <div id="mmoNews">
                <div class="mmoBoxLeft"></div>
                <div class="mmoBoxMiddle" onmouseover="mmoTickHalt=true;" onmouseout="mmoTickHalt=false;">
                    <div class="mmoNewsContent">
                        <div id="mmoNewsticker">
                            <ul>
                                                                    <li class="mmoTickShow">
                                        <a target="_blank" href="https://www.nintendo.co.uk/Games/Nintendo-Switch-download-software/Tiny-Thor-2422298.html?kid=5-ab806-09006-2308-12027198">Hammer Time! Tiny Thor Now on Nintendo Switch</a>
                                    </li>
                                                            </ul>
                        </div>
                    </div>
                </div>
                <div class="mmoBoxRight"></div>
            </div>
        
        <div id="mmoGame" class="mmoGame">
            <div class="mmoBoxLeft"></div>
            <div class="mmoBoxMiddle">

                <!--<div id="mmoGames"></div>-->

                <div id="mmoLangs">
                                            <label>Select country:</label>
                        <div id="mmoLangSelect" class="mmoSelectbox">
                            <div id="mmoSarea1" onclick="mmoShowOptions(1)" class="mmoSelectArea">
                                <div class="mmoSelectText" id="mmoMySelectContent1">
                                    <div id="mmoMySelectText1" class="mmoflag mmo_EN">United Kingdom</div>                                </div>
                                <div class="mmoSelectButton"></div>
                            </div>
                            <div class="mmoOptionsDivInvisible" id="mmoOptionsDiv1">
                                <ul class="mmoLangList mmoListHeight" id="mmoList1">
                                    <li><a href="https://lobby.gladiatus.gameforge.com/?kid=5-03740-03707-1105-12012139" target="_blank" rel="nofollow" class="mmoflag mmo_AR">Argentina</a></li>
<li><a href="https://lobby.gladiatus.gameforge.com/?kid=5-03719-03707-1105-12012158" target="_blank" rel="nofollow" class="mmoflag mmo_BR">Brasil</a></li>
<li><a href="https://lobby.gladiatus.gameforge.com/?kid=5-03720-03707-1105-1201212e" target="_blank" rel="nofollow" class="mmoflag mmo_DK">Danmark</a></li>
<li><a href="https://lobby.gladiatus.gameforge.com/?kid=5-03706-03707-1105-120121c7" target="_blank" rel="nofollow" class="mmoflag mmo_DE">Deutschland</a></li>
<li><a href="https://lobby.gladiatus.gameforge.com/?kid=5-03752-03707-1105-1201213d" target="_blank" rel="nofollow" class="mmoflag mmo_EE">Eesti</a></li>
<li><a href="https://lobby.gladiatus.gameforge.com/?kid=5-03709-03707-1105-1201211e" target="_blank" rel="nofollow" class="mmoflag mmo_ES">España</a></li>
<li><a href="https://lobby.gladiatus.gameforge.com/?kid=5-03708-03707-1105-120121db" target="_blank" rel="nofollow" class="mmoflag mmo_FR">France</a></li>
<li><a href="https://lobby.gladiatus.gameforge.com/?kid=5-03710-03707-1105-1201217d" target="_blank" rel="nofollow" class="mmoflag mmo_IT">Italia</a></li>
<li><a href="//ba.gladiatus.gameforge.com/?kid=5-03712-03707-1105-1201218b" target="_blank" rel="nofollow" class="mmoflag mmo_YU">Jugoslavija</a></li>
<li><a href="https://lobby.gladiatus.gameforge.com/?kid=5-03743-03707-1105-1201215e" target="_blank" rel="nofollow" class="mmoflag mmo_LV">Latvija</a></li>
<li><a href="https://lobby.gladiatus.gameforge.com/?kid=5-03744-03707-1105-1201218d" target="_blank" rel="nofollow" class="mmoflag mmo_LT">Lietuva</a></li>
<li><a href="https://lobby.gladiatus.gameforge.com/?kid=5-03732-03707-1105-1201216a" target="_blank" rel="nofollow" class="mmoflag mmo_HU">Magyarország</a></li>
<li><a href="https://mx.gladiatus.gameforge.com/game/?kid=5-03739-03707-1105-12012193" target="_blank" rel="nofollow" class="mmoflag mmo_MX">México</a></li>
<li><a href="https://lobby.gladiatus.gameforge.com/?kid=5-03713-03707-1105-120121bb" target="_blank" rel="nofollow" class="mmoflag mmo_NL">Nederland</a></li>
<li><a href="https://no.gladiatus.gameforge.com/game/?kid=5-03734-03707-1105-12012119" target="_blank" rel="nofollow" class="mmoflag mmo_NO">Norge</a></li>
<li><a href="https://pl.gladiatus.gameforge.com/game/?kid=5-03711-03707-1105-12012169" target="_blank" rel="nofollow" class="mmoflag mmo_PL">Polska</a></li>
<li><a href="https://lobby.gladiatus.gameforge.com/?kid=5-03717-03707-1105-120121f2" target="_blank" rel="nofollow" class="mmoflag mmo_PT">Portugal</a></li>
<li><a href="https://lobby.gladiatus.gameforge.com/?kid=5-03733-03707-1105-120121ec" target="_blank" rel="nofollow" class="mmoflag mmo_RO">Romania</a></li>
<li><a href="https://lobby.gladiatus.gameforge.com/?kid=5-03735-03707-1105-120121b4" target="_blank" rel="nofollow" class="mmoflag mmo_SK">Slovensko</a></li>
<li><a href="https://lobby.gladiatus.gameforge.com/?kid=5-03737-03707-1105-12012141" target="_blank" rel="nofollow" class="mmoflag mmo_FI">Suomi</a></li>
<li><a href="https://se.gladiatus.gameforge.com/game/?kid=5-03723-03707-1105-120121e2" target="_blank" rel="nofollow" class="mmoflag mmo_SE">Sverige</a></li>
<li><a href="https://lobby.gladiatus.gameforge.com/?kid=5-03714-03707-1105-1201219a" target="_blank" rel="nofollow" class="mmoflag mmo_TR">Türkiye</a></li>
<li><a href="https://lobby.gladiatus.gameforge.com/?kid=5-03745-03707-1105-120121f7" target="_blank" rel="nofollow" class="mmoflag mmo_US">USA</a></li>
<li class="mmoActive"><a href="https://lobby.gladiatus.gameforge.com/?kid=5-03707-03707-1105-1201212f" target="_blank" rel="nofollow" class="mmoflag mmo_EN">United Kingdom</a></li>
<li><a href="https://lobby.gladiatus.gameforge.com/?kid=5-03731-03707-1105-12012194" target="_blank" rel="nofollow" class="mmoflag mmo_CZ">Ceská Republika</a></li>
<li><a href="https://gr.gladiatus.gameforge.com/game/?kid=5-03727-03707-1105-12012104" target="_blank" rel="nofollow" class="mmoflag mmo_GR">????da</a></li>
<li><a href="https://lobby.gladiatus.gameforge.com/?kid=5-03736-03707-1105-12012170" target="_blank" rel="nofollow" class="mmoflag mmo_BG">????????</a></li>
<li><a href="https://lobby.gladiatus.gameforge.com/?kid=5-03715-03707-1105-120121dd" target="_blank" rel="nofollow" class="mmoflag mmo_RU">?????????? ?????????</a></li>
<li><a href="https://il.gladiatus.gameforge.com/game/?kid=5-03750-03707-1105-1201214c" target="_blank" rel="nofollow" class="mmoflag mmo_IL">?????</a></li>
<li><a href="https://lobby.gladiatus.gameforge.com/?kid=5-03749-03707-1105-120121cf" target="_blank" rel="nofollow" class="mmoflag mmo_AE">???????? ??????? ???????</a></li>
<li><a href="https://lobby.gladiatus.gameforge.com/?kid=5-03716-03707-1105-120121ba" target="_blank" rel="nofollow" class="mmoflag mmo_TW">??</a></li>
                                </ul>
                            </div>
                        </div>
                                        </div>
            </div>
            <div class="mmoBoxRight"></div>

                            <div id="mmoGamesOverviewPanel">
                <div id="mmoGamesOverviewToggle">
                    <h4>
                        <a href="#mmoGamesOverviewLists">More games<span class="mmoNbPseudoSelect_icon"></span></a>
                        <span class="mmoNbBoxEdge mmoNbBoxEdge_left"></span>
                        <span class="mmoNbBoxEdge mmoNbBoxEdge_right"></span>
                    </h4>
                </div>
                                    <div id="mmoGamesOverviewLists">
                        <div id="mmoGamesOverviewCountry" class="mmo_EN"></div>

                                                    <!-- Section: Featured Game -->
                            <h5>Featured game</h5>
                            <ul id="mmoGamesOverview_featured">
                                                                    <li>
                                        <a id="mmoTile_tinythor" href="https://store.steampowered.com/app/541310/Tiny_Thor/?utm_source=gf&utm_medium=netbar&utm_campaign=5-ab807-09007-2302-120281cf&kid=5-ab807-03707-1105-1202812f" title="NOW ON STEAM" target="_blank"
                                           onmouseenter="mmoHoverTile('tinythor')" onmouseleave="mmoLeaveTile('tinythor')">
                                            <span id="gameImgTarget_tinythor" class="gameImgTarget"></span>
                                            <span class="gameName">Tiny Thor</span>
                                        </a>
                                    </li>
                                                            </ul>
                        
                                                    <!-- Section: Client Games -->
                            <h5><a href="https://gameforge.com/en-GB/mmorpg/?origin=netbar" target="_blank">Client Games</a></h5>
                            <ul id="mmoGamesOverview_client">
                                                                    <li class="mmoGameIcon mmoGameIcon_aionclassic mmoGameIcon_aionclassic_en">
                                        <a id="mmoTile_aionclassic" href="https://join.aionclassic.gameforge.com?kid=5-aba07-03707-1105-12028103" title="Back to the Roots" target="_blank"
                                           onmouseenter="mmoHoverTile('aionclassic')" onmouseleave="mmoLeaveTile('aionclassic')">
                                            <span id="gameImgTarget_aionclassic" class="gameImgTarget"></span>
                                            Aion Classic Europe                                        </a>
                                    </li>
                                                                        <li class="mmoGameIcon mmoGameIcon_metin2 mmoGameIcon_metin2_en">
                                        <a id="mmoTile_metin2" href="https://en.metin2.gameforge.com/landing?kid=5-02007-03707-1105-1202814b" title="Sharpen your blade and your mind" target="_blank"
                                           onmouseenter="mmoHoverTile('metin2')" onmouseleave="mmoLeaveTile('metin2')">
                                            <span id="gameImgTarget_metin2" class="gameImgTarget"></span>
                                            Metin2                                        </a>
                                    </li>
                                                                        <li class="mmoGameIcon mmoGameIcon_nostale mmoGameIcon_nostale_en">
                                        <a id="mmoTile_nostale" href="https://en.nostale.gameforge.com/landing/?kid=5-09107-03707-1105-12028104" title="Live the legend" target="_blank"
                                           onmouseenter="mmoHoverTile('nostale')" onmouseleave="mmoLeaveTile('nostale')">
                                            <span id="gameImgTarget_nostale" class="gameImgTarget"></span>
                                            NosTale                                        </a>
                                    </li>
                                                                        <li class="mmoGameIcon mmoGameIcon_tss mmoGameIcon_tss_en">
                                        <a id="mmoTile_tss" href="https://gameforge.com/en-GB/play/trigon_space_story?kid=5-99907-03707-1105-1202817c" title="Roguelike, Sci-Fi, Strategy" target="_blank"
                                           onmouseenter="mmoHoverTile('tss')" onmouseleave="mmoLeaveTile('tss')">
                                            <span id="gameImgTarget_tss" class="gameImgTarget"></span>
                                            Trigon: Space Story                                        </a>
                                    </li>
                                                                        <li class="mmoGameIcon mmoGameIcon_aion mmoGameIcon_aion_en">
                                        <a id="mmoTile_aion" href="https://en.aion.gameforge.com/website/7-0?kid=5-62007-03707-1105-120281f3" title="Earn your wings" target="_blank"
                                           onmouseenter="mmoHoverTile('aion')" onmouseleave="mmoLeaveTile('aion')">
                                            <span id="gameImgTarget_aion" class="gameImgTarget"></span>
                                            AION free-to-play                                        </a>
                                    </li>
                                                                        <li class="mmoGameIcon mmoGameIcon_elsword mmoGameIcon_elsword_en">
                                        <a id="mmoTile_elsword" href="https://en.elsword.gameforge.com/landing?kid=5-48807-03707-1105-1202812c" title="Mean monsters, cool heroes" target="_blank"
                                           onmouseenter="mmoHoverTile('elsword')" onmouseleave="mmoLeaveTile('elsword')">
                                            <span id="gameImgTarget_elsword" class="gameImgTarget"></span>
                                            Elsword                                        </a>
                                    </li>
                                                                        <li class="mmoGameIcon mmoGameIcon_4story mmoGameIcon_4story_en">
                                        <a id="mmoTile_4story" href="http://en.4story.gameforge.com/landing?kid=5-23307-03707-1105-120281f3" title="For the light of truth" target="_blank"
                                           onmouseenter="mmoHoverTile('4story')" onmouseleave="mmoLeaveTile('4story')">
                                            <span id="gameImgTarget_4story" class="gameImgTarget"></span>
                                            4Story                                        </a>
                                    </li>
                                                                        <li class="mmoGameIcon mmoGameIcon_runesofmagic mmoGameIcon_runesofmagic_en">
                                        <a id="mmoTile_runesofmagic" href="https://en.runesofmagic.gameforge.com/landing/?kid=5-46807-03707-1105-12028193" title="THE AWARD WINNING MMORPG" target="_blank"
                                           onmouseenter="mmoHoverTile('runesofmagic')" onmouseleave="mmoLeaveTile('runesofmagic')">
                                            <span id="gameImgTarget_runesofmagic" class="gameImgTarget"></span>
                                            Runes of Magic                                        </a>
                                    </li>
                                                                </ul>
                            
                                                    <!-- Section: Browser Games -->
                            <h5><a href="https://gameforge.com/en-GB/browser-games/?origin=netbar" target="_blank">Browser Games</a></h5>
                            <ul id="mmoGamesOverview_browser">
                                                                    <li class="mmoGameIcon mmoGameIcon_ogame mmoGameIcon_ogame_en">
                                        <a id="mmoTile_ogame" href="https://play.ogame.gameforge.com/?kid=5-00107-03707-1105-12028121" title="Colonies of the Future" target="_blank"
                                           onmouseenter="mmoHoverTile('ogame')" onmouseleave="mmoLeaveTile('ogame')">
                                            <span id="gameImgTarget_ogame" class="gameImgTarget"></span>
                                            OGame                                        </a>
                                    </li>
                                                                        <li class="mmoGameIcon mmoGameIcon_ikariam mmoGameIcon_ikariam_en">
                                        <a id="mmoTile_ikariam" href="https://lobby.ikariam.gameforge.com/en_GB/?kid=5-03807-03707-1105-120281f8" title="The future of antiquity" target="_blank"
                                           onmouseenter="mmoHoverTile('ikariam')" onmouseleave="mmoLeaveTile('ikariam')">
                                            <span id="gameImgTarget_ikariam" class="gameImgTarget"></span>
                                            Ikariam                                        </a>
                                    </li>
                                                                        <li class="mmoGameIcon mmoGameIcon_battleknight mmoGameIcon_battleknight_en">
                                        <a id="mmoTile_battleknight" href="https://en.battleknight.gameforge.com//?kid=5-01907-03707-1105-12028192" title="For honour and glory" target="_blank"
                                           onmouseenter="mmoHoverTile('battleknight')" onmouseleave="mmoLeaveTile('battleknight')">
                                            <span id="gameImgTarget_battleknight" class="gameImgTarget"></span>
                                            BattleKnight                                        </a>
                                    </li>
                                                                        <li class="mmoGameIcon mmoGameIcon_bitefight mmoGameIcon_bitefight_en">
                                        <a id="mmoTile_bitefight" href="https://en.bitefight.gameforge.com/?kid=5-00207-03707-1105-120281fe" title="Rivals of the night" target="_blank"
                                           onmouseenter="mmoHoverTile('bitefight')" onmouseleave="mmoLeaveTile('bitefight')">
                                            <span id="gameImgTarget_bitefight" class="gameImgTarget"></span>
                                            BiteFight                                        </a>
                                    </li>
                                                                        <li class="mmoGameIcon mmoGameIcon_kingsage mmoGameIcon_kingsage_en">
                                        <a id="mmoTile_kingsage" href="https://en.kingsage.gameforge.com/?kid=5-31107-03707-1105-12028117" title="Long live the king!" target="_blank"
                                           onmouseenter="mmoHoverTile('kingsage')" onmouseleave="mmoLeaveTile('kingsage')">
                                            <span id="gameImgTarget_kingsage" class="gameImgTarget"></span>
                                            KingsAge                                        </a>
                                    </li>
                                                                        <li class="mmoGameIcon mmoGameIcon_LittleGames mmoGameIcon_LittleGames_en">
                                        <a id="mmoTile_LittleGames" href="https://gameforge.com/en-US/littlegames/?origin=portal/?kid=5-99907-03707-1105-1202817c" title="Free Games" target="_blank"
                                           onmouseenter="mmoHoverTile('LittleGames')" onmouseleave="mmoLeaveTile('LittleGames')">
                                            <span id="gameImgTarget_LittleGames" class="gameImgTarget"></span>
                                            Free Games                                        </a>
                                    </li>
                                                                        <li class="mmoGameIcon mmoGameIcon_GMag mmoGameIcon_GMag_en">
                                        <a id="mmoTile_GMag" href="https://gameforge.com/en-US/gmag/?origin=netbar&kid=5-99907-03707-1105-1202817c" title="GMag" target="_blank"
                                           onmouseenter="mmoHoverTile('GMag')" onmouseleave="mmoLeaveTile('GMag')">
                                            <span id="gameImgTarget_GMag" class="gameImgTarget"></span>
                                            GMag                                        </a>
                                    </li>
                                                                </ul>
                                                </div><!-- /mmoGamesOverviewLists -->
                    </div><!-- /mmoGamesOverviewPanel -->
                                            </div><!-- /mmoGame -->
        <input id="mmoFocus" type="text" size="5"/>
    </div><!-- /mmoContent -->
</div><!-- /mmonetbar -->

<!-- gladiatus / en / ingame / 06.08.2023 20:10 -->
<script type="text/javascript" data-cookieconsent="ignore">
    mmoInitSelect();
    mmoTicker();    mmoToggleDisplay.init("mmoGamesOverviewPanel");
</script>


    <!--/* OpenX Interstitial or Floating DHTML Tag v2.8.8 */-->
    <div id="openXHackFoo">
        <script type="text/javascript" data-cookieconsent="ignore">
            var HTTP_GET_VARS = new Array();
            var strGET = document.location.search.substr(1, document.location.search.length);
            if (strGET !== '') {
                var gArr = strGET.split('&');
                for (i = 0; i < gArr.length; ++i) {
                    var v = '';
                    var vArr = gArr[i].split('=');
                    if (vArr.length > 1) {
                        v = vArr[1];
                    }
                    HTTP_GET_VARS[unescape(vArr[0])] = unescape(v);
                }
            }

            function GET(v) {
                if (!HTTP_GET_VARS[v]) {
                    return '';
                }
                return HTTP_GET_VARS[v];
            }

            function openxDetectDeviceOS() {
                return (function (ua) {
                    if (/iPhone/i.test(ua) || /iPad/.test(ua) || /iPod/.test(ua)) {
                        return 'ios';
                    } else if (/Android/.test(ua)) {
                        return 'android';
                    } else if (/Windows Phone OS 7\.0/.test(ua)) {
                        return 'winphone7';
                    } else if (/BlackBerry/.test(ua)) {
                        return 'rim';
                    } else {
                        return 'desktop';
                    }
                })(navigator.userAgent);
            }

            function escapeHtml(str) {
                return str.match(new RegExp('^[0-9a-zA-Z-]+$')) ? str : '';
            }

            function hasMarketingConsent() {
                return (typeof window.gfCookieConsent !== 'undefined') && window.gfCookieConsent.consent.marketing === true;
            }

            if (openxDetectDeviceOS() === 'desktop') {
                var params = 'zoneid=1400&source=Quelle&cb=INSERT_RANDOM_NUMBER_HERE&layerstyle=simple&align=right&valign=top&padding=2&shifth=30&shiftv=20&closebutton=t&backcolor=E1D4A7&bordercolor=FDED65';
                params = params.replace(/INSERT_RANDOM_NUMBER_HERE/g, Math.floor(Math.random() * 99999999999));
                params = params + '&zindex=9999999&layerstyle=gameforge&kid=' + escapeHtml(GET('kid'));
                var m3_u = hasMarketingConsent() ? 'https://ads-delivery.gameforge.com/al.php' : 'https://ads-deliverync.gameforge.com/al.php';
                document.write('<scr' + 'ipt type="text/javascript" src="' + m3_u + '?' + params + '"></scr' + 'ipt>');
            }
        </script>
    </div>

<!-- #/MMO:NETBAR# -->
    </body>
</html>

include 'footer.php';
?>