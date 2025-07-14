<?php

include 'header.php';
?>

<script>
    $(document).ready(function () {
        $(".sec_view").click(function () {
            var linkedObj = "#" + $(this).attr("link");
            var open = 0;
            if ($(this).hasClass("sec_close")) {
                $(linkedObj).show('slow');
                $(this).removeClass("sec_close");
                open = 1;
            } else {
                $(linkedObj).hide('slow');
                $(this).addClass("sec_close");
            }

            $.post("secview.php", { "do": "saveshd", "section": $(this).attr("link"), "open": open }, function (data) {
                ;//alert(data);
            });
        });
    });
</script>

<?php

$_GET['id'] = check_number($_GET['id']);
$resultcheck = $mysql->query("SELECT * FROM `grpgusers` WHERE `id` = '" . $_GET['id'] . "' LIMIT 1");

if (isset($_POST['save_notes'])) {
    if ($user_class->rmdays > 0) {
        $result00 = $mysql->query("INSERT INTO `notes` VALUES ('', '" . $user_class->id . "', '" . $_GET['id'] . "', '" . $_POST['text'] . "') ON DUPLICATE KEY UPDATE `notes`='" . $_POST['text'] . "'");
        echo Message('Your notes have been saved.');
    }
}

if (mysql_num_rows($resultcheck) > 0) {
    $profile_class = new User($_GET['id']);

    $getsig = mysql_fetch_array($mysql->query("SELECT `signature` FROM `grpgusers_extra` WHERE `userid` = '" . $profile_class->id . "' LIMIT 1"));
    $profile_class->signature = $getsig['signature'];

    if ($user_class->gender == "Female") {
        $himher = "her";
    } else {
        $himher = "him";
    }

    // Check if The Infected Mobster Event is Active
    $currenttime = time();
    $result911 = $mysql->query("SELECT * FROM `infected_mobster`");
    $worked911 = mysql_fetch_array($result911);
    if (($currenttime >= $worked911['starts']) && ($currenttime <= $worked911['ends'])) {
        $infected_mobster = true;
    } else {
        $infected_mobster = false;
    }

    // Admin Stuff

    if ($_POST['del_avatar'] && $user_class->accesslevel == 1) {
        if (file_exists('avatars/' . $profile_class->id . '.jpg')) {
            unlink('avatars/' . $profile_class->id . '.jpg');
        } elseif (file_exists('avatars/' . $profile_class->id . '.gif')) {
            unlink('avatars/' . $profile_class->id . '.gif');
        } elseif (file_exists('avatars/' . $profile_class->id . '.png')) {
            unlink('avatars/' . $profile_class->id . '.png');
        }
    }

    if ($_POST[update] && $user_class->accesslevel == 1) {
        if ($_POST['flagtype'] == 0) {
            $result_no = $mysql->query("update grpgusers set `scammer`='no' where id = '" . $profile_class->id . "' LIMIT 1");
        } elseif ($_POST['flagtype'] == 1) {
            $result_yes = $mysql->query("update grpgusers set `scammer`='YES' where id = '" . $profile_class->id . "' LIMIT 1");
        }
    }

    if ($user_class->accesslevel == 1) {
        $del_avatar = "<br /><form method='POST'><input type='submit' name='del_avatar' value='Delete Avatar' onclick='return confirm(\"Are you sure you would like to delete this avatar ?\")'></form>";
    }

    // End Admin Stuff

    if (($_GET['rate'] != "") && ($_GET['rate'] == "up") && ($user_class->id != $profile_class->id)) {
        $result3 = $mysql->query("INSERT IGNORE INTO `ratelog` (`from`, `to`) VALUES ('" . $user_class->id . "', '" . $profile_class->id . "')");
        if (mysql_affected_rows() > 0) {
            $result2 = $mysql->query("UPDATE `grpgusers` SET `rating`=`rating`+'1' WHERE `id` = '" . $profile_class->id . "'");
            Send_Event($profile_class->id, $user_class->formattedname . " rated you <b><font color=blue>UP</font></b>. You can return the favor by rating " . $himher . " <a href='profiles.php?id=" . $user_class->id . "&rate=up'>UP</a> or <a href='profiles.php?id=" . $user_class->id . "&rate=down'>DOWN</a>.");
            $profile_class->rating += 1;
            echo Message($profile_class->formattedname . " You Rated Them Up.");
        } else {
            echo Message($profile_class->formattedname . " You Already Rated Them Up.");
        }





    } elseif (($_GET['rate'] != "") && ($_GET['rate'] == "down") && ($user_class->id != $profile_class->id)) {
        $result3 = $mysql->query("INSERT IGNORE INTO `ratelog` (`from`, `to`) VALUES ('" . $user_class->id . "', '" . $profile_class->id . "')");
        if (mysql_affected_rows() > 0) {
            $result2 = $mysql->query("UPDATE `grpgusers` SET `rating`=`rating`-'1' WHERE `id` = '" . $profile_class->id . "'");
            Send_Event($profile_class->id, $user_class->formattedname . " rated you <b><font color=red>Down</font></b>. You can return the favor by rating " . $himher . " <a href='profiles.php?id=" . $user_class->id . "&rate=up'>UP</a> or <a href='profiles.php?id=" . $user_class->id . "&rate=down'>DOWN</a>.");
            $profile_class->rating -= 1;
            echo Message($profile_class->formattedname . " You Rated Them Down.");
        } else {
            echo Message($profile_class->formattedname . " You Already Rated Them Today.");
        }





    }

    if ($_GET['action'] == "addfriend") {
        $result = $mysql->query("SELECT * from `contactlist_friends` WHERE `whoslist` = '" . $user_class->id . "' and `userid` = '" . $profile_class->id . "'");
        $numcontacts = mysql_num_rows($result);
        if ($numcontacts == 0) {
            $result3 = $mysql->query("INSERT INTO `contactlist_friends` (`whoslist`, `userid`) VALUES ('" . $user_class->id . "', '" . $profile_class->id . "')");
            echo Message($profile_class->formattedname . " added to the Friend list.");
        }
    }

    if ($_GET['action'] == "addenemy") {
        $result = $mysql->query("SELECT * from `contactlist_enemies` WHERE `whoslist` = '" . $user_class->id . "' and `userid` = '" . $profile_class->id . "'");
        $numcontacts = mysql_num_rows($result);
        if ($numcontacts == 0) {
            $result3 = $mysql->query("INSERT INTO `contactlist_enemies` (`whoslist`, `userid`) VALUES ('" . $user_class->id . "', '" . $profile_class->id . "')");
            echo Message($profile_class->formattedname . " added to the Enemy list.");
        }
    }

    if ($_GET['action'] == "addignore") {
        $result = $mysql->query("SELECT * from `contactlist_ignore` WHERE `whoslist` = '" . $user_class->id . "' and `userid` = '" . $profile_class->id . "'");
        $numcontacts = mysql_num_rows($result);
        if ($numcontacts == 0) {
            if ($profile_class->id != "1") {
                if ($profile_class->id != $user_class->id) {
                    $result3 = $mysql->query("INSERT INTO `contactlist_ignore` (`whoslist`, `userid`) VALUES ('" . $user_class->id . "', '" . $profile_class->id . "')");
                    echo Message($profile_class->formattedname . " added to the Ignore list.");
                } else {
                    echo Message("You can't ignore yourself!");
                    include 'footer.php';
                    exit;
                }
            } else {
                echo Message("You can't ignore the KING!");
                include 'footer.php';
                exit;
            }
        }
    }

    if (file_exists('avatars/' . $profile_class->id . '.jpg')) {
        $avatar = 'avatars/' . $profile_class->id . '.jpg';
    } elseif (file_exists('avatars/' . $profile_class->id . '.gif')) {
        $avatar = 'avatars/' . $profile_class->id . '.gif';
    } elseif (file_exists('avatars/' . $profile_class->id . '.png')) {
        $avatar = 'avatars/' . $profile_class->id . '.png';
    } else {
        $avatar = 'avatars/0.jpg';
    }
    ;
    ?>
    <script type="text/javascript" src="https://worldofmobsters.com/includes/wz_tooltip.js"></script>
    <div class="contenthead">Profile: <?php echo $profile_class->gamename ?> [<?php echo $profile_class->id; ?>]</div>
    <!--contenthead-->
    <div class="contentcontent">
        <table width='100%'>
            <tr>
                <td><b>Favorite Quote</b>:</td>
                <td colspan='3'>"<?php echo cleaner($profile_class->quote); ?>"</td>
            </tr>
            <tr>
                <td colspan='4'>
                    <table width='100%' height='100%' cellpadding='5' cellspacing='2'>
                        <tr>
                            <td width='120' align='center'><img height="100" width="100"
                                    src="<?php echo $profile_class->avatar; ?>"><?php echo $del_avatar; ?></td>
                            <td align='center'>
                                <b>Status:</b> <?php echo $profile_class->mobsterstatus; ?><br><br>
                                <?php

                                if ($profile_class->id > 1) {
                                    if ($profile_class->scammer == "YES") {
                                        $profile_class->scammer = "<b><font color=red>YES</font></b>";
                                    }
                                    ?>
                                    <b>Rating:</b> <?php echo number_format($profile_class->rating); ?> [<a
                                        href="profiles.php?id=<?php echo $profile_class->id; ?>&rate=up">up</a> &bull; <a
                                        href="profiles.php?id=<?php echo $profile_class->id; ?>&rate=down">down</a>]
                                    &nbsp;&nbsp; <b>Scammer:</b> <?php echo $profile_class->scammer; ?>
                                    <?php

                                    if ($profile_class->pmban == 1) {
                                        ?>
                                        <br><i>
                                            <font color=red>User BANNED from the mailing system.</font>
                                        </i>
                                        <?php

                                    }
                                }
                                ?>
                            </td>
                            <td align='right' valign='bottom'><img
                                    src='images/class<?php echo $profile_class->charclassid; ?>.jpg' width='70' height='70'
                                    alt='<?php echo $profile_class->charclass; ?>'
                                    title='<?php echo $profile_class->charclass; ?>'></td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr>
                <td width='20%'><b>Name</b>:</td>
                <td width='35%'><?php echo $profile_class->formattedname; ?> [<b onmouseover="TagToTip('formH')"
                        onmouseout="UnTip()">?</b>]&nbsp;</td>
                <td width='20%'><b>HP</b>:</td>
                <td width='25%'><?php echo $profile_class->formattedhp; ?></td>
            </tr>

            <tr>
                <td><b>Type</b>:</td>
                <td><?php echo $profile_class->type ?></td>
                <td><b>Crimes</b>:</td>
                <td><?php echo number_format($profile_class->crimetotal); ?></td>
            </tr>

            <tr>
                <td><b>Gender</b>:</td>
                <td><?php echo $profile_class->gender ?></td>
                <td><b>Forum Posts</b>:</td>
                <td><?php echo number_format($profile_class->forum_post_count); ?></td>
            </tr>

            <tr>
                <td><b>Level</b>:</td>
                <td><?php echo $profile_class->level; ?></td>
                <td><b>Money</b>:</td>
                <td>$<?php echo number_format($profile_class->money); ?></td>
            </tr>

            <tr>
                <td><b>Prison (Busts)</b>:</td>
                <td><?php echo number_format($profile_class->prison_busts); ?></td>
                <td><b>Prison (Caught)</b>:</td>
                <td><?php echo number_format($profile_class->prison_caught); ?></td>
            </tr>

            <tr>
                <td><b>Age</b>:</td>
                <td><?php echo $profile_class->age; ?></td>
                <td><b>Online</b>:</td>
                <td><?php echo $profile_class->formattedonline; ?></td>
            </tr>

            <tr>
                <td><b>Last Active</b>:</td>
                <td><?php echo $profile_class->formattedlastactive; ?></td>
                <td><b>Total Active</b>:</td>
                <td><?php echo ActiveTime($profile_class->totalactive); ?></td>
            </tr>

            <tr>
                <td><b>Gang</b>:</td>
                <td><?php echo $profile_class->formattedgang; ?></td>
                <td><b>Gang History</b>:</td>
                <td><a href='ganghistory.php?id=<?php echo $profile_class->id; ?>'> View</a></td>
            </tr>

            <tr>
                <td><b>City</b>:</td>
                <td><a href='travel.php'><?php echo $profile_class->cityname; ?></a></td>
                <td><b>House</b>:</td>
                <td><?php if ($profile_class->housename != "Homeless") { ?><a
                            href="housedesc.php?id=<?php echo $profile_class->id; ?>"><?php echo $profile_class->housename; ?></a>
                    </td>
                <? } else {
                    echo $profile_class->housename; ?></td><? } ?>
            </tr>

            <?php

            if ($profile_class->hospital > time()) {
                if ((floor($profile_class->hospital - time()) / 60) != 1) {
                    $plural = "s";
                }
                $inhospital = 1;
                $formattedhospital = ceil(($profile_class->hospital - time()) / 60) . " minute" . $plural;
            }
            if ($inhospital != 1) {
                $formattedhospital = "Not in Hospital";
            }

            if ($profile_class->jail > time()) {
                if ((floor($profile_class->jail - time()) / 60) != 1) {
                    $plural = "s";
                }
                $injail = 1;
                $formattedjail = ceil(($profile_class->jail - time()) / 60) . " minute" . $plural;
            }
            if ($injail != 1) {
                $formattedjail = "Not in Jail";
            }
            ?>
            <tr>
                <td><b>Hospital Status</b>:</td>
                <td><?php echo $formattedhospital; ?></td>
                <td><b>Prison Status</b>:</td>
                <td><?php echo $formattedjail; ?></td>
            </tr>

            <?php

            $ifreferred = $mysql->query("SELECT referrals.referrer, grpgusers.gamename FROM referrals left join grpgusers on grpgusers.id=referrals.referrer WHERE referrals.referred='$profile_class->id'");
            $chkifreferred = mysql_fetch_array($ifreferred);
            if ($chkifreferred['referrer'] != "") {
                $wasreffered = $chkifreferred['gamename'];
            } else {
                $wasreffered = "Nobody";
            }

            $checkref = $mysql->query("SELECT count(*) as totalrefs FROM `referrals` WHERE `referrer`='$profile_class->id'");
            $numref = mysql_fetch_assoc($checkref);
            ?>

            <tr>
                <td><b>Referred By</b>:</td>
                <td><?php echo $wasreffered; ?></td>
                <td><b>Referred</b>:</td>
                <td><?php echo $numref['totalrefs']; ?></td>
            </tr>
            <?php

            if ($profile_class->familyid == '0') {
                $familymembers = 1;
            } else {
                $resultfm = $mysql->query("SELECT COUNT(*) as familymembers FROM `grpgusers` WHERE `familyid` = '" . $profile_class->familyid . "' AND `familyid` != '0'");
                $workedfm = mysql_fetch_assoc($resultfm);
                if ($profile_class->familyid == 0) {
                    $familymembers = $workedfm['familymembers'];
                } else {
                    $familymembers = "<a href='familymembers_view.php?id=" . $profile_class->id . "'>" . $workedfm['familymembers'] . "</a>";
                }
            }
            ?>
            <tr>
                <td><b>Family Members</b>:</td>
                <td><?php echo $familymembers; ?></td>
                <td><b>Street Level</b>:</td>
                <td><?php echo $profile_class->street_level; ?></td>
            </tr>

        </table>

        <?php

        if ($infected_mobster)
            echo "
    <br />
    <table width='65%' align='center'>
        <tr>
            <td align='right'><img src='images/infected_kills.png' border='0' style='vertical-align:middle;'></td>
            <td align='center' width='10%'><b>" . $profile_class->infectedkills . "</b></td>
            <td width='10%'>&nbsp;</td>
            <td align='center' width='10%'><b>" . $profile_class->mobstersinfected . "</b></td>
            <td align='left'><img src='images/mobsters_infected.png' border='0' style='vertical-align:middle;'></td>
        </tr>
    </table>
    ";
        ?>
    </div><!--contentcontent-->
    <div class="contentfoot"></div><!--contentfoot-->

    <?php

    if ($user_class->id != $profile_class->id) {
        $is_friend = false;
        $is_enemy = false;
        $is_ignored = false;
        $resultisf = $mysql->query("SELECT * from `contactlist_friends` WHERE `userid` = '" . $profile_class->id . "' and `whoslist` = '" . $user_class->id . "'");
        if (mysql_num_rows($resultisf) > 0) {
            $is_friend = true;
        }
        $resultise = $mysql->query("SELECT * from `contactlist_enemies` WHERE `userid` = '" . $profile_class->id . "' and `whoslist` = '" . $user_class->id . "'");
        if (mysql_num_rows($resultise) > 0) {
            $is_enemy = true;
        }
        $resultisi = $mysql->query("SELECT * from `contactlist_ignore` WHERE `userid` = '" . $profile_class->id . "' and `whoslist` = '" . $user_class->id . "'");
        if (mysql_num_rows($resultisi) > 0) {
            $is_ignored = true;
        }

        $codedate = time();
        $newcode = getUniqueCode(32);
        $result2 = $mysql->query("INSERT INTO `randomids_mug` (`date`, `code`) VALUES ('" . $codedate . "', '" . $newcode . "')");

        ?>

        <div class="contenthead">Actions</div><!--contenthead-->
        <div class="contentcontent">
            <table width='100%'>
                <tr>
                    <td width='25%' align='center'><a href='pms.php?action=new&to=<?php echo $profile_class->id ?>'>Message</a>
                    </td>
                    <td width='25%' align='center'><a href='attack.php?attack=<?php echo $profile_class->id ?>'>Attack</a></td>
                    <td width='25%' align='center'><a
                            href='mug.php?mug=<?php echo $profile_class->id ?>&r=<?php echo $newcode; ?>'>Mug</a></td>
                    <td width='25%' align='center'><a href='spy.php?id=<?php echo $profile_class->id; ?>'>Spy</a></td>
                </tr>

                <tr>
                    <td width='25%' align='center'><a href='sendmoney.php?person=<?php echo $profile_class->id; ?>'>Send
                            Money</a></td>
                    <td width='25%' align='center'><a href='sendpoints.php?person=<?php echo $profile_class->id ?>'>Send
                            Points</a></td>
                    <td width='25%' align='center'><a href='sendmulti.php?id=<?php echo $profile_class->id ?>'>Send Items</a>
                    </td>
                    <td width='25%' align='center'><a href='sendcredit.php?person=<?php echo $profile_class->id ?>'>Send
                            Credits</a></td>
                </tr>

                <tr>
                    <td width='25%' align='center'><?php if ($is_friend) { ?> <a
                                href='contactlist.php?action=friends&remove=<?php echo $profile_class->id ?>'>Remove Friend</a>
                        <?php } else { ?><a href='profiles.php?action=addfriend&id=<?php echo $profile_class->id ?>'>Add
                                Friend</a> <?php } ?></td>
                    <td width='25%' align='center'><?php if ($is_enemy) { ?> <a
                                href='contactlist.php?action=enemies&remove=<?php echo $profile_class->id ?>'>Remove Enemy</a>
                        <?php } else { ?><a href='profiles.php?action=addenemy&id=<?php echo $profile_class->id ?>'>Add
                                Enemy</a> <?php } ?></td>
                    <td width='25%' align='center'><?php if ($is_ignored) { ?> <a
                                href='contactlist.php?action=ignore&remove=<?php echo $profile_class->id ?>'>Remove Ignore</a>
                        <?php } else { ?><a href='profiles.php?action=addignore&id=<?php echo $profile_class->id; ?>'>Add
                                Ignore</a> <?php } ?></td>
                    <td width='25%' align='center'><a href='hitman.php?userid=<?php echo $profile_class->id; ?>'>Add Bounty</a>
                    </td>
                </tr>
                <?php

                list($rankedit, $rankvault, $rankmember, $rankinvites, $rankmembershiprequests, $rankmail, $rankcrime, $rankjointattack, $rankupgrade) = str_split($user_class->gangrank);
                ?>
                <td width='25%' align='center'>
                    <?php if ((($user_class->gangleader == $user_class->id) || ($rankinvites == 1)) && ($profile_class->gang == 0)) { ?>
                        <a href='gang.php?action=invite&userid=<?php echo $profile_class->id ?>'>Invite to Gang</a> <?php } ?></td>
                <td width='25%' align='center'></td>
                <td width='25%' align='center'></td>
                <td width='25%' align='center'></td>
                <tr>

                </tr>
            </table>
        </div><!--contentcontent-->
        <div class="contentfoot"></div><!--contentfoot-->
    <?php } ?>


    <?php

    // Medals
    $result_medals = $mysql->query("SELECT * FROM `medals` WHERE `userid` = '" . $profile_class->id . "' order by `medallevel` DESC");
    //$result_medals = $mysql->query("SELECT * FROM (SELECT * FROM `medals` WHERE `userid` = '".$profile_class->id."' order by `medallevel` DESC) as t1 GROUP by `whatmedal`");
    if (mysql_num_rows($result_medals) > 0) {
        ?>
        <div class="contenthead">Medals<span class="sec_view sec_open" link="div_ach"></span></div><!--contenthead-->
        <div class="contentcontent">
            <div id="div_ach">
                <div style="display:block; width:750px; overflow:hidden;">
                    <center>
                        <?php

                        while ($row = mysql_fetch_array($result_medals)) {
                            echo "<img src='images_medals/" . $row['whatmedal'] . $row['medallevel'] . ".jpg' title='" . $row['desc'] . ": " . $row['desc2'] . " [Awarded " . date(d . " " . M . ", " . Y, $row['timewhen']) . "]'>&nbsp;";
                        }
                        ?>
                    </center>
                </div>
            </div>
        </div><!--contentcontent-->
        <div class="contentfoot"></div><!--contentfoot-->
        <?php

    }
    ?>

    <?php

    if ($user_class->id != $profile_class->id) {
        if ($user_class->rmdays > 0) {
            $result007 = $mysql->query("SELECT * FROM `notes` WHERE `userid`='" . $user_class->id . "' AND `aboutuserid`='" . $_GET['id'] . "' LIMIT 1");
            $worked007 = mysql_fetch_array($result007);
            ?>
            <div class="contenthead">Your Notes<span class="sec_view sec_open" link="div_notes"></span></div><!--contenthead-->
            <div class="contentcontent">
                <div id="div_notes">
                    <form method="post">
                        <input type="hidden" name="user_id" value="51062" />
                        <textarea rows="5" cols="60" name="text"><?php echo $worked007['notes']; ?></textarea>
                        <br />
                        <input class="button" type="submit" name="save_notes" value=" Update " />
                    </form>
                </div>
            </div><!--contentcontent-->
            <div class="contentfoot"></div><!--contentfoot-->
            <?php

        }
    }
    ?>

    <div class="contenthead">Signature<span class="sec_view sec_open" link="div_sig"></span></div><!--contenthead-->
    <div class="contentcontent">
        <div id="div_sig">
            <div style="display:block; width:750px; overflow:hidden;">
                <?php echo bbcode_format($user_class->check_badwords($profile_class->signature)); ?>
            </div>
        </div>
    </div><!--contentcontent-->
    <div class="contentfoot"></div><!--contentfoot-->

    <?php

    if ($user_class->accesslevel == 1) {
        ?>
        <div class="contenthead">Staff Only</div><!--contenthead-->
        <div class="contentcontent">
            <table width='100%' id='cleanTable'>
                <tr>
                    <td width='15%'>IP:</td>
                    <td width='35%'><a
                            href='search_ip.php?ip=<?php echo str_replace(".", "-", $profile_class->ip); ?>'><?php echo $profile_class->ip; ?></a>
                    </td>
                    <td width='15%'>Last Active:</td>
                    <td width='35%'><?php echo date("d M Y, g:ia", $profile_class->lastactive); ?></td>
                </tr>

                <form method='post'>
                    <tr>
                        <td>Profile Flag:</td>
                        <td>
                            <select name='flagtype'>
                                <option value='0' <?php if ($profile_class->scammer == "no") {
                                    echo " selected";
                                } ?>>No Flag
                                </option>
                                <option value='1' <?php if ($profile_class->scammer == "<b><font color=red>YES</font></b>") {
                                    echo " selected";
                                } ?>>Scammer</option>
                            </select>
                        </td>
                        <td><input type='submit' name='update' value='Update'></td>
                        <td>&nbsp;</td>
                    </tr>
                </form>
            </table>
        </div><!--contentcontent-->
        <div class="contentfoot"></div><!--contentfoot-->

        <div class="contenthead">Staff Only - Support Tickets</div><!--contenthead-->
        <div class="contentcontent">
            <table width='100%' id='cleanTable'>
                <form method='post'>
                    <?php

                    function t_department($number)
                    {
                        if ($number == 1)
                            $department = "Support";
                        elseif ($number == 2)
                            $department = "Bug Reports";
                        elseif ($number == 3)
                            $department = "Sales";
                        return $department;
                    }

                    $query12 = "
            SELECT *
            FROM support_messages
            WHERE userid = '{$profile_class->id}'
            ORDER BY id DESC
            ";
                    $an_query = $mysql->query($query12);

                    while ($an_row = mysql_fetch_assoc($an_query)) {
                        $x_utickets++;
                        if ($an_row['modified'] == $an_row['timestamp']) {
                            $l_reply = "No Reply";
                        }
                        if (($an_row['status'] == 'Pending') || ($an_row['status'] == 'Open')) {
                            $an_row['status'] = "<font color='green'>" . $an_row['status'] . "</font>";
                        }
                        if ($an_row['status'] == 'Responded') {
                            $an_row['status'] = "<font color='yellow'>" . $an_row['status'] . "</font>";
                        }
                        if ($an_row['status'] == 'Closed') {
                            $an_row['status'] = "<font color='gray'>" . $an_row['status'] . "</font>";
                        }
                        echo "<tr>
                            <td>Last Reply: " . $an_row['modified'] . "</td>
                            <td><a href='supportadmin.php?action=viewticket&id=" . $an_row['id'] . "'>" . htmlspecialchars(stripslashes($an_row['subject'])) . "</a> [" . $an_row['status'] . "]</td>
                        </tr>";
                    }

                    if (!$x_utickets) {
                        echo "<tr>
                            <td colspan='2' align='center'>No tickets have been filed by this user.</td>
                        </tr>";
                    }
                    ?>

                </form>
            </table>
        </div><!--contentcontent-->
        <div class="contentfoot"></div><!--contentfoot-->
        <?php

    }


    $namehistory = "<b><u>Name change history:</u></b><br>";
    $resulthist = $mysql->query("SELECT * FROM `name_history` WHERE `userid` = '" . $profile_class->id . "' ORDER BY `id` DESC");
    if (mysql_num_rows($resulthist) > 0) {
        while ($lineh = mysql_fetch_array($resulthist, MYSQL_ASSOC)) {
            $namehistory .= $lineh['oldname'] . "<br>";
        }
    } else {
        $namehistory .= "-";
    }
    ?>
    <span id="formH"><?php echo $namehistory; ?></span>

    <script>
        <?php

        $secview = unserialize($user_class->secview);
        if (is_array($secview['profile'])) {
            foreach ($secview['profile'] as $section => $open) {
                if (!$open) {
                    echo '$("#' . $section . '").hide();' . "\n";
                    echo '$("span[link=' . $section . ']").addClass("sec_close");' . "\n";
                }
            }
        }
        ?>
    </script>

    <?php
} else {
    echo Message("That user does not exist.");
}
include 'footer.php';
?>