<?php
include 'header.php';
?>
<div class='box_top'>MailBox</div>
						<div class='box_middle'>
							<div class='pad'>
                                <?php
  $db->query("UPDATE grpgusers SET diamonds = 0 WHERE id = ?");
$db->execute(array(
    $user_class->id
));
$db->query("SELECT * FROM bans WHERE type = 'mail' AND id = ?");
$db->execute(array(
    $user_class->id
));
$row = $db->fetch_row(true);
if (!empty($row)) {
    diefun('&nbsp;You have been mail banned for ' . prettynum($row['days']) . ' days.');
}


if ($user_class->fbitime > 0) {
    diefun("You can't communicate if you're in FBI Jail!");
}


if (isset($_GET['delete'])) {
    security($_GET['delete']);
    $deletemsg = $_GET['delete'];
    $db->query("DELETE FROM pms WHERE id = ? AND `to` = ? AND starred = 0");
    $db->execute(array(
        $deletemsg,
        $user_class->id
    ));
    echo Message("You have deleted this mail.");
}
if (isset($_GET['star'])) {
    $starmsg = $_GET['star'];
    $db->query("SELECT starred FROM pms WHERE id = ? AND `to` = ?");
    $db->execute(array(
        $starmsg,
        $user_class->id
    ));
    $star = $db->fetch_single();
    $db->query("UPDATE pms SET starred = ? WHERE id = ? AND `to` = ?");
    if ($star == 1) {
        $db->execute(array(
            0,
            $starmsg,
            $user_class->id
        ));
        echo Message("You have unstarred this mail.");
    } else {
        $db->execute(array(
            1,
            $starmsg,
            $user_class->id
        ));
        echo Message("You have starred this mail.");
    }
}
if (isset($_GET['check'])) {
    if ($user_class->money < 100000) {
        diefun("You do not have enough money to check for mail bombs.");
    }
    $db->query("SELECT id FROM pms WHERE `to` = ? AND viewed = 2 AND check = 0 AND (bomb = 1 OR bomb = 2)");
    $db->execute(array(
        $user_class->id
    ));
    $rows = $db->fetch_row();
    foreach ($rows as $row) {
        if (rand(1, 10) <= 8) {
            $db->query("UPDATE pms SET check = 1 WHERE id = ?");
            $db->execute(array(
                $row['id']
            ));
        }
    }
    $user_class->money -= 100000;
    $db->query("UPDATE grpgusers SET money = ? WHERE id = ?");
    $db->execute(array(
        $user_class->money,
        $user_class->id
    ));
}
if (isset($_GET['report'])) {
    security($_GET['report']);
    $db->query("SELECT * FROM pms WHERE id = ?");
    $db->execute(array(
        $_GET['report']
    ));
    $db->query("UPDATE maillog SET reported = 1 WHERE id = ? AND `to` = ?");
    $db->execute(array(
        $_GET['report'],
        $user_class->id
    ));
    echo Message("You have reported this mail.");
}
if (isset($_GET['deleteall'])) {
    $db->query("DELETE FROM pms WHERE `to` = ? AND viewed = 2 AND starred = 0");
    $db->execute(array(
        $user_class->id
    ));
    echo Message("You successfully deleted all your mail.");
}
if (isset($_GET['deletealloutbox'])) {
    //$db->query("UPDATE pms SET outboxhidden = 1 WHERE `from` = ?");
    $db->query("DELETE FROM pms WHERE `from` = ?");
    $db->execute(array(
        $user_class->id
    ));
    echo Message("You successfully deleted all your outbox mail.");
}
if (isset($_POST['newmessage']) && $user_class->level > 0) {
    security($_POST['to']);
    security($_POST['bomb']);
    security($_POST['parent']);
    $to = $_POST['to'];
    $to_user = new User($to);
    $from = $user_class->id;
    $bomb = $_POST['bomb'];
    $parent = ($_POST['parent'] != 0) ? $_POST['parent'] : floor(time() / (uniqid(rand(1, 20), true) + uniqid(rand(1, 200))) - rand(100, 1000));
    $subject = str_replace(" ", "", $_POST['subject']);
    $subject = strip_tags($subject);
    if (empty($subject)) {
        $subject = "No Subject";
    }
    $_POST['msgtext'] = str_replace('"', '', $_POST['msgtext']);
    $msgtext = strip_tags($_POST['msgtext']);
    $msgtext = nl2br($msgtext);
    $db->query("SELECT blocker FROM ignorelist WHERE blocker = ? AND blocked = ? LIMIT 1");
    $db->execute(array(
        $to,
        $user_class->id
    ));
    if ($db->num_rows('You cannot mail this user because they have you on their ignore list.')) {
        diefun();
    }
    if (!empty($to_user->id)) {
        $cost = ($bomb == 1) ? 500000 : 1200000;
        if ($user_class->money < $cost && ($bomb == 1 || $bomb == 2)) {
            echo Message("You don't have enough money to send a mail bomb.");
        } else {
            if ($bomb == 1 || $bomb == 2) {
                $user_class->money -= $cost;
                $db->query("UPDATE grpgusers SET money = ? WHERE id = ?");
                $db->execute(array(
                    $user_class->money,
                    $user_class->id
                ));
            }
            $db->query("INSERT INTO pms (parent, `to`, `from`, timesent, subject, msgtext, bomb) VALUES (?, ?, ?, unix_timestamp(), ?, ?, ?)");
            $db->execute(array(
                $parent,
                $to,
                $from,
                $subject,
                $msgtext,
                $bomb
            ));
            $db->query("INSERT INTO maillog (`to`, `from`, timesent, subject, msgtext) VALUES (?, ?, unix_timestamp(), ?, ?)");
            $db->execute(array(
                $to,
                $from,
                $subject,
                $msgtext
            ));
                    $db->query("UPDATE grpgusers SET diamonds = 1 WHERE id = ?");
        $db->execute(array($to));

            echo Message("Message successfully sent to $to_user->formattedname.");
        }
    } else {
        echo Message("The player you specified doesn't exist!");
    }
}
if (isset($_GET['view']) && $_GET['view'] == "inbox") {
    print"
    <style type='text/css'>
        .delete {
            background-color: #001c1e;
            border: 1px solid #004349;
        }
    </style>
    " . mailHeader() . "
        <br />



        <table id='newtables' style='width:100%; color:white'>
        <tr>
            <th width='30%'>Subject</th>
            <th width='30%'>Sender</th>
            <th width='30%'>Time Recieved</th>
            <th colspan='3'></th>
        </tr>";
    $db->query("SELECT COUNT(*) FROM pms WHERE `to` = ?");
    $db->execute(array(
        $user_class->id
    ));
    $numrows = $db->fetch_single();
    $rowsperpage = 30;
    $totalpages = ceil($numrows / $rowsperpage);
    $totalpages = ($totalpages <= 0) ? 1 : ceil($numrows / $rowsperpage);
    $currentpage = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int) $_GET['page'] : 1;
    if ($currentpage > $totalpages) {
        $currentpage = $totalpages;
    }
    if ($currentpage < 1) {
        $currentpage = 1;
    }
    $offset = ($currentpage - 1) * $rowsperpage;
    $db->query("SELECT * FROM pms WHERE `to` = ? ORDER BY timesent DESC LIMIT $offset, $rowsperpage");
    $db->execute(array(
        $user_class->id
    ));
    $rows = $db->fetch_row();
    foreach ($rows as $row) {
        if ($row['to'] == $user_class->id) {
            $from_user_class = new User($row['from']);
            $subject = ($row['subject'] == "") ? "No Subject" : $row['subject'];
            if ($row['check'] == 1 && $row['viewed'] == 1) {
                $bold = "<b>";
                $bold2 = "</b>&nbsp;[mail bomb]";
            } elseif ($row['check'] == 1) {
                $bold = "";
                $bold2 = "&nbsp;[mail bomb]";
            } elseif ($row['viewed'] == 1) {
                $bold = "<span style='color: red;'><b>";
                $bold2 = "</b>&nbsp;[new]</span>";
            } elseif ($row['reported'] == 1) {
                $bold = "";
                $bold2 = "&nbsp;[reported]";
            } else {
                $bold = "";
                $bold2 = "";
            }
            $namee = ($row['from'] == 0000) ? "<b><i>Auto Mail</i></b>" : $from_user_class->formattedname;
            $fill = ($row['starred'] == 1) ? "fill" : "";
            $antifill = ($row['starred'] == 0) ? "fill" : "";
            echo "

                    <tr style='height:30px;'>
                        <td width='30%'>$bold<a href='viewpm.php?id={$row['id']}'>$subject</a>$bold2</td>
                        <td width='30%'>$namee</td>
                        <td width='30%'>" . date("d F, Y g:ia", $row['timesent']) . "</td>
                        <td width='3%'><a href='?view=inbox&star={$row['id']}'><img src='/images/star{$fill}.png' height='20px;' /></a></td>
                        <td width='3%'></td>
                        <td width='3%'><a href='pms.php?view=inbox&delete={$row['id']}'><span class='delete'>&nbsp;X&nbsp;</span></a></td>
                    </tr>";
        }
    }
    if (count($rows) > 0) {
        print"
        <br />
        <div align='right'>[<a href='pms.php?deleteall=true'>Delete All Mail</a>]</div>";
    }
    $range = 2;
    if ($currentpage > 1) {
        echo " <a href='?page=1&view=inbox'><<</a> ";
    }
    for ($x = ($currentpage - $range); $x < (($currentpage + $range) + 1); $x++) {
        if (($x > 0) && ($x <= $totalpages)) {
            if ($x == $currentpage) {
                echo " [<b>$x</b>] ";
            } else {
                echo " <a href='?page=$x&view=inbox'>$x</a> ";
            }
        }
    }
    if ($currentpage < $totalpages) {
        echo " <a href='?page=$totalpages&view=inbox'>>></a> ";
    }
    print"
    </td></tr>
    </table>
    </td></tr>";
}
if (isset($_GET['view']) && $_GET['view'] == "new") {
    if (isset($_GET['reply'])) {
        security($_GET['reply']);
        $db->query("SELECT * FROM pms WHERE id = ?");
        $db->execute(array(
            $_GET['reply']
        ));
        $row = $db->fetch_row(true);
        if ($row['from'] == 0000) {
            echo Message("You can't reply to an automated message.");
        }
    }
    echo mailHeader() . "
            <table width='100%' style='padding: 5px;'><tr><td>
                        <table width='100%'>
                            <form method='post' name='message' action='pms.php?view=inbox'>
                                <tr>
                                    <td width='15%'><b>Send To:</b></td>
                                    <td width='85%'><input type='text' name='to' value='", (isset($_GET['to'])) ? $_GET['to'] : $row['from'], "' size='10' maxlength='75'> [ID]</td>
                                </tr>
                                <tr>
                                    <td width='15%'><b>Mail Bomb:</b></td>
                                    <td width='85%'>
                                        <select name='bomb'>
                                            <option value='0' selected='selected'>None</option>
                                            <option value='1'>20 Minutes - $500,000</option>
                                            <option value='2'>40 Minutes - $1,200,000</option>
                                        </select>
                                </tr>
                                <tr>
                                    <td width='15%'><b>Subject:</b></td>
                                    <td width='85%'><input type='text' name='subject' size='70' maxlength='75' value='";
    if (isset($_GET['reply'])) {
        echo (preg_match("/Re: /", $worked2['subject'])) ? $worked2['subject'] : "Re: " . $worked2['subject'];
    }
    echo"'></td>
                                </tr>
                                <tr>
                                    <td width='15%'><b>Message:</b></td>
                                    <td width='85%' colspan='3'><textarea autofocus name='msgtext' style='width:75%;height:125px;'></textarea></td>
                                </tr>
                                <tr>
                                    <td width='100%' colspan='4' align='center'>
                                        <input type='hidden' name='parent' value='", (isset($_GET['reply'])) ? $worked2['parent'] : 0, "' />
                                        <input type='submit' name='newmessage' value='Send'>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan='2'>", emotes(), "</td>
                                </tr>
                            </form>
                        </table>
</table>
                    </td>
                </tr>
                ";
}
if (isset($_GET['view']) && $_GET['view'] == "outbox") {
    print mailHeader() . "
            <table id='newtables' class='altcolors' style='width:100%;table-layout:fixed;'>
                <tr>
                    <th>Subject</th>
                    <th>Sent To</th>
                    <th>Time Sent</th>
                </tr>";
    $db->query("SELECT COUNT(*) FROM pms WHERE `from` = ? AND outboxhidden = 0");
    $db->execute(array(
        $user_class->id
    ));
    $numrows = $db->fetch_single();
    $rowsperpage = 30;
    $totalpages = ceil($numrows / $rowsperpage);
    $totalpages = ($totalpages <= 0) ? 1 : ceil($numrows / $rowsperpage);
    $currentpage = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int) $_GET['page'] : 1;
    if ($currentpage > $totalpages) {
        $currentpage = $totalpages;
    }
    if ($currentpage < 1) {
        $currentpage = 1;
    }
    $offset = ($currentpage - 1) * $rowsperpage;
    $db->query("SELECT * FROM pms WHERE `from` = ? AND outboxhidden = 0 ORDER BY timesent DESC LIMIT $offset, $rowsperpage");
    $db->execute(array(
        $user_class->id
    ));
    $rows = $db->fetch_row();
    foreach ($rows as $row) {
        if ($row['from'] == $user_class->id) {
            echo "
                            <tr>
                                <td width='42%'><a href='viewsent.php?id={$row['id']}'>", ($row['subject'] == "") ? "No Subject" : $row['subject'], "</a></td>
                                <td width='27%'>" . formatName($row['to']) . "</td>
                                <td width='31%'>" . date("d F, Y g:ia", $row['timesent']) . "</td>
                            </tr>";
        }
    }
    print"
    </table>
    <br />";
    if (count($rows) > 0) {
        print"
        <br />
        <div align='right'>[<a href='pms.php?deletealloutbox=true'>Delete All Outbox Mail</a>]</div>";
    }
    $range = 2;
    if ($currentpage > 1) {
        echo " <a href='?page=1&view=outbox'><<</a> ";
    }
    for ($x = ($currentpage - $range); $x < (($currentpage + $range) + 1); $x++) {
        if (($x > 0) && ($x <= $totalpages)) {
            if ($x == $currentpage) {
                echo " [<b>$x</b>] ";
            } else {
                echo " <a href='?page=$x&view=outbox'>$x</a> ";
            }
        }
    }
    if ($currentpage < $totalpages) {
        echo " <a href='?page=$totalpages&view=outbox'>>></a> ";
    }
    print"
                    </td></tr>
            </table>
        </td></tr>";
}
include 'footer.php';
