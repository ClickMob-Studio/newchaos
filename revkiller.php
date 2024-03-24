<?php
require_once __DIR__ . '/gmheader.php';
require_once __DIR__ . '/includes/class_mtg_paginate.php';
include '/includes/class_mtg_functions.php';
include "codeparser.php";
if (isset($_GET['action']) && $_GET['action'] = 'acthour') {
    view_act_hour_logs();
    include 'footer.php';
    die();
}
$pages = new Paginator();
$_POST['postid'] = isset($_POST['postid']) && ctype_digit($_POST['postid']) ? $_POST['postid'] : null;
if (isset($_POST['unreportprofile'])) {
    if (empty($_POST['postid']))
        $mtg->error("You didn't select a valid player");
    $db->query("SELECT id, reported FROM grpgusers WHERE id = ?");
    $db->execute(array(
        $_POST['postid']
    ));
    if (!$db->num_rows())
        $mtg->error("That player doesn't exist");
    $row = $db->fetch_row(true);
    $user = new User($_POST['postid']);
    if (!$row['reported'])
        $mtg->error($user->formattedname . " hasn't been reported");
    $db->query("UPDATE grpgusers SET reported = 0, reporter = 0 WHERE id = ?");
    $db->execute(array(
        $_POST['postid']
    ));
    $mtg->success("You've cleared the report flag from " . $user->formattedname);
}
if (isset($_POST['unreportmail'])) {
    if (empty($_POST['postid']))
        $mtg->error("You didn't select a valid message");
    $db->query("SELECT id, reported FROM pms WHERE id = ?");
    $db->execute(array(
        $_POST['postid']
    ));
    if (!$db->num_rows())
        $mtg->error("That message hasn't been reported");
    $db->query("UPDATE pms SET reported = 0 WHERE id = ?");
    $db->execute(array(
        $_POST['postid']
    ));
    $mtg->success("You've cleared the report flag");
}
$bansarray = array(
    "bans" => "Game Banned List|perm|perma-bans",
    "mbans" => "Mail Banned List|mail|mailbans",
    "qa" => "Quick Ad Banned List|quicka|quick-ad bans",
    "freeze" => "Frozen Accounts List|freeze|frozen accounts",
    "fbans" => "Forum Banned List|forum|forum bans"
);
if (isset($_GET['page_action'])) {
    if (array_key_exists($_GET['page_action'], $bansarray)) {
        $array = explode("|", $bansarray[$_GET['page_action']]);
        genHead($array[0]);
        print "
        <table id='newtables' style='width:100%;table-layout:fixed;'>
            <tr>
                <th>Player</th>
                <th>Duration</th>
                <th>Banned By</th>
            </tr>
    ";
        $db->query("SELECT id, bannedby, days FROM bans WHERE type = '{$array[1]}' ORDER BY days DESC, id ASC");
        $db->execute();
        if (!$db->num_rows())
            echo "<tr><td colspan='3' class='center'>There are no active {$array[2]}</td></tr>";
        else {
            $rows = $db->fetch_row();
            foreach ($rows as $row) {
                print "
                <tr>
                    <td>" . formatName($row['id']) . "</td>
                    <td>" . $row['days'] . " days</td>
                    <td>" . formatName($row['bannedby']) . "</td>
                </tr>
            ";
            }
        }
        print "</table></td></tr>";
    }
    if ($_GET['page_action'] == "reportprofile") {
        genHead("Reported Profiles");
        print "
        <table id='newtables' style='width:100%;table-layout:fixed;'>
        <tr>
            <th>Profile</th>
            <th>Reporter</th>
            <th>Clear Flag</th>
        </tr>
    ";
        $db->query("SELECT id, reporter FROM grpgusers WHERE reported > 0 ORDER BY id ASC");
        $db->execute();
        if (!$db->num_rows())
            echo "<tr><td colspan='3' class='center'>No players have been reported</td></tr>";
        else {
            $rows = $db->fetch_row();
            foreach ($rows as $row) {
                print "
                <tr>
                    <td>" . formatName($row['id']) . "</td>
                    <td>" . formatName($row['reporter']) . "</td>
                    <td><form action='gmpanel.php?page_action=reportprofile' method='post'>
                        <input type='hidden' name='postid' value='{$row['id']}' />
                        <input type='submit' name='unreportprofile' value='Clear Flag' />
                    </form></td>
                </tr>
            ";
            }
        }
        print "</table></td></tr>";
    }
    if ($_GET['page_action'] == "maillog") {
        $_GET['to'] = isset($_GET['to']) && ctype_digit($_GET['to']) ? $_GET['to'] : null;
        $_GET['from'] = isset($_GET['from']) && ctype_digit($_GET['from']) ? $_GET['from'] : null;
        $sql = ' WHERE `to` <> 146 AND `from` <> 146';
        if (isset($_GET['filter'])) {
            if (!empty($_GET['to']) && !empty($_GET['from']))
                $sql .= " AND `to` = {$_GET['to']} AND `from` = {$_GET['from']}";
            else if (empty($_GET['to']) && !empty($_GET['from']))
                $sql .= " AND `from` = {$_GET['from']}";
            else if (!empty($_GET['to']) && empty($_GET['from']))
                $sql .= " AND `to` = {$_GET['to']}";
        }
        require_once __DIR__ . '/includes/class_mtg_paginate.php';
        $pages = new Paginator();
        $db->query("SELECT COUNT(id) FROM maillog" . $sql);
        $db->execute();
        $pages->items_total = $db->fetch_single();
        $pages->mid_range = 5;
        $pages->paginate();
        ?><tr><td class="contentspacer"></td></tr>
        <tr><td class="contenthead">Mail Log</td></tr>
        <tr><td class="contentcontent">
                <form action='gmpanel.php' method='get'>
                    <input type="hidden" name="page_action" value="maillog" />
                    <table id='newtables' width='100%'>
                        <tr>
                            <th width="12.5%">To</th>
                            <td width="37.5%"><input type="text" name="to" value="<?php
                                echo $_GET['to'];
                                ?>" /></td>
                            <th width="12.5%">From</th>
                            <td width="37.5%"><input type="text" name="from" value="<?php
                                echo $_GET['from'];
                                ?>" /></td>
                        </tr>
                        <tr>
                            <td colspan='4' class='center'><input type="submit" name="filter" value="Filter" /></td>
                        </tr>
                    </table>
                </form><br />
                <div class='paginate'><?php
                    echo $pages->display_pages();
                    ?></div>
                <br />
                <table id='newtables' style='width:100%;'>
                    <?php
                    $db->query("SELECT * FROM maillog$sql ORDER BY timesent DESC" . $pages->limit);
                    $db->execute();
                    $rows = $db->fetch_row();
                    foreach ($rows as $row) {
                        print "<tr>
					<th>{$row['subject']}</th>
                    <th>" . formatName($row['from']) . "</th>
                    <th>" . formatName($row['to']) . "</th>
                    <th>" . date('H:i:s d/m/Y', $row['timesent']) . "</th>
                </tr><tr>
                    <td colspan='4'>" . BBCodeParse($row['msgtext']) . "</td>
                </tr><tr>
                    <td colspan='4' style='opacity:0;'><br /></td>
                </tr>";
                    }
                    ?></table>
                <br />
                <div class='paginate'><?php
                    echo $pages->display_pages();
                    ?></div>
                <br />
            </td></tr><?php
    }
    if ($_GET['page_action'] == "attlog") {
        $sql = '';
        $_GET['att'] = isset($_GET['att']) && ctype_digit($_GET['att']) ? $_GET['att'] : null;
        $_GET['def'] = isset($_GET['def']) && ctype_digit($_GET['def']) ? $_GET['def'] : null;
        if (isset($_GET['filter2'])) {
            if (!empty($_GET['att']) && !empty($_GET['def']))
                $sql = sprintf(" WHERE attacker = %u AND defender = %u", $_GET['att'], $_GET['def']);
            else if (empty($_GET['att']) && !empty($_GET['def']))
                $sql = sprintf(" WHERE defender = %u", $_GET['def']);
            else if (!empty($_GET['att']) && empty($_GET['def']))
                $sql = sprintf(" WHERE attacker = %u", $_GET['att']);
        }
        $db->query("SELECT COUNT(id) FROM attacklog" . $sql);
        $db->execute();
        $pages->items_total = $db->fetch_single();
        $pages->mid_range = 5;
        $pages->paginate();
        $db->query("SELECT * FROM attacklog" . $sql . " ORDER BY timestamp DESC " . $pages->limit);
        $db->execute();
        genHead("Attack Log");
        echo"
        <form method='get'>
            <input type='hidden' name='page_action' value='attlog' />
            <table width='100%' cellspacing='0' border='0' id='table center'>
                <tr>
                    <th width='12.5%'>Attacker</th>
                    <td width='37.5%'><input type='text' name='att' value='{$_GET['att']}' /></td>
                    <th width='12.5%'>Defender</th>
                    <td width='37.5%'><input type='text' name='def' value='{$_GET['def']}' /></td>
                </tr>
                <tr>
                    <td colspan='4'><input type='submit' name='filter2' value='Filter' /></td>
                </tr>
            </table>
        </form><br />
    <div class='paginate'>", $pages->display_pages(), "</div><br />
    <table id='newtables' width='100%'>
        <tr>
            <th>Attacker</th>
            <th>Defender</th>
            <th>Winner</th>
            <th>Exp</th>
            <th>Money</th>
            <th>Time</th>
        </tr>";
        if (!$db->num_rows())
            echo "<tr><td colspan='6' class='center'>There are no attack logs</td></tr>";
        else {
            $rows = $db->fetch_row();
            foreach ($rows as $row) {
                $which = $row['attacker'] == $row['winner'] ? 'attacker' : 'defender';
                print"
                <tr>
                    <td>" . formatName($row['attacker']) . "</td>
                    <td>" . formatName($row['defender']) . "</td>
                    <td>" . formatName($row[$which]) . "</td>
                    <td>" . prettynum($row['exp']) . "</td>
                    <td>" . prettynum($row['money'], 1) . "</td>
                    <td>" . date('H:i:s d/m/Y', $row['timestamp']) . "</td>
                </tr>
            ";
            }
        }
        echo"</table><br /><div class='paginate'>", $pages->display_pages(), "</div><br /></td></tr>";
    }
    if ($_GET['page_action'] == "marketlog1") {
        $_GET['owner'] = isset($_GET['owner']) && ctype_digit($_GET['owner']) ? $_GET['owner'] : null;
        $sql = isset($_GET['filter3']) && !empty($_GET['owner']) ? ' WHERE owner = ' . $_GET['owner'] : '';
        $db->query("SELECT COUNT(id) FROM addptmarketlog" . $sql);
        $db->execute();
        $pages->items_total = $db->fetch_single();
        $pages->mid_range = 3;
        $pages->paginate();
        $db->query("SELECT * FROM addptmarketlog" . $sql . " ORDER BY timestamp DESC " . $pages->limit);
        $db->execute();
        genHead("Point Market Log [Added Points]");
        echo"
        <form method='get'>
            <input type='hidden' name='page_action' value='marketlog1' />
            <table id='newtables' width='100%'>
                <tr>
                    <th width='25%'>Adder/Owner</th>
                    <td width='75%'><input type='text' name='owner' value='{$_GET['owner']}' /></td>
                </tr>
                <tr>
                    <td colspan='2' class='center'><input type='submit' name='filter3' value='Filter' /></td>
                </tr>
            </table>
        </form><br />
        <div class='paginate'>", $pages->display_pages(), "</div>
        <br />
        <table id='newtables' width='100%'>
            <tr>
                <th>Owner</th>
                <th>Amount</th>
                <th>Price [each]</th>
                <th>Time</th>
            </tr>
    ";
        if (!$db->num_rows())
            echo "<tr><td colspan='4' class='center'>There are no logs</td></tr>";
        else {
            $rows = $db->fetch_row();
            foreach ($rows as $row) {
                print"
                <tr>
                    <td>" . formatName($row['owner']) . "</td>
                    <td>" . prettynum($row['amount']) . "</td>
                    <td>" . prettynum($row['price'], 1) . "<br /><span class='small'>(Total: " . prettynum($row['price'] * $row['amount'], 1) . ")</span></td>
                    <td>" . date('H:i:s d/m/Y', $row['timestamp']) . "</td>
                </tr>
            ";
            }
        }
        echo"</table><br /><div class='paginate'>", $pages->display_pages(), "</div><br /></td></tr>";
    }
    if ($_GET['page_action'] == "marketlog2") {
        $_GET['buyer'] = isset($_GET['buyer']) && ctype_digit($_GET['buyer']) ? $_GET['buyer'] : null;
        $_GET['owner'] = isset($_GET['owner']) && ctype_digit($_GET['owner']) ? $_GET['owner'] : null;
        $sql = "";
        if (isset($_GET['filter3'])) {
            if (!empty($_GET['buyer']) && !empty($_GET['owner']))
                $sql = sprintf(" WHERE buyer = %u AND owner = %u", $_GET['buyer'], $_GET['owner']);
            else if (empty($_GET['buyer']) && !empty($_GET['owner']))
                $sql = sprintf(" WHERE owner = %u", $_GET['owner']);
            else if (!empty($_GET['buyer']) && empty($_GET['owner']))
                $sql = sprintf(" WHERE buyer = %u", $_GET['buyer']);
        }
        $db->query("SELECT COUNT(*) FROM buyptmarketlog" . $sql);
        $db->execute();
        $pages->items_total = $db->fetch_single();
        $pages->mid_range = 5;
        $pages->paginate();
        $db->query("SELECT * FROM buyptmarketlog" . $sql . " ORDER BY timestamp DESC " . $pages->limit);
        $db->execute();
        genHead("Point Market Log [Bought Points]");
        print"
        <form method='get'>
            <input type='hidden' name='page_action' value='marketlog2' />
            <table id='newtables' width='100%'>
                <tr>
                    <th width='12.5%'>Adder/Owner</th>
                    <td width='37.5%'><input type='text' name='owner' value='{$_GET['owner']}' /></td>
                    <th width='12.5%'>Buyer</th>
                    <td width='37.5%'><input type='text' name='buyer' value='{$_GET['buyer']}' /></td>
                </tr>
                <tr>
                    <td colspan='4' class='center'><input type='submit' name='filter3' value='Filter' /></td>
                </tr>
            </table>
        </form><br />
        <div class='paginate'>" . $pages->display_pages() . "</div>
        <br />
        <table id='newtables' width='100%'>
            <tr>
                <th>Owner</th>
                <th>Buyer</th>
                <th>Amount</th>
                <th>Price [each]</th>
                <th>Time</th>
            </tr>
    ";
        if (!$db->num_rows())
            echo "<tr><td colspan='5' class='center'>There are no logs</td></tr>";
        else {
            $rows = $db->fetch_row();
            foreach ($rows as $row) {
                $adder = new User($row['owner']);
                $buyer = new User($row['buyer']);
                print"
                <tr>
                    <td>$adder->formattedname</td>
                    <td>$buyer->formattedname</td>
                    <td>" . prettynum($row['amount']) . "</td>
                    <td>" . prettynum($row['price'], 1) . "<br /><span class='small'>(Total: " . prettynum($row['price'] * $row['amount'], 1) . ")</span></td>
                    <td>" . date('H:i:s d/m/Y', $row['timestamp']) . "</td>
                </tr>
            ";
            }
        }
        echo"</table><br /><div class='paginate'>", $pages->display_pages(), "</div><br /></td></tr>";
    }
    if ($_GET['page_action'] == "marketlog3") {
        $_GET['owner'] = isset($_GET['owner']) && ctype_digit($_GET['owner']) ? $_GET['owner'] : null;
        $sql = isset($_GET['filter3']) && !empty($_GET['owner']) ? ' WHERE owner = ' . $_GET['owner'] : '';
        $db->query("SELECT COUNT(id) FROM removeptmarketlog" . $sql);
        $db->execute();
        $pages->items_total = $db->fetch_single();
        $pages->mid_range = 3;
        $pages->paginate();
        $db->query("SELECT * FROM removeptmarketlog" . $sql . " ORDER BY timestamp DESC " . $pages->limit);
        $db->execute();
        genHead("Point Market Log [Removed Points]");
        print"
        <form method='get'>
            <input type='hidden' name='page_action' value='marketlog3' />
            <table id='newtables' width='100%'>
                <tr>
                    <th width='25%'>Adder/Owner</th>
                    <td width='75%'><input type='text' name='owner' value='{$_GET['owner']}' /></td>
                </tr>
                <tr>
                    <td colspan='2' class='center'><input type='submit' name='filter3' value='Filter' /></td>
                </tr>
            </table>
        </form><br />
        <div class='paginate'>" . $pages->display_pages() . "</div>
        <br />
        <table id='newtables' width='100%'>
            <tr>
                <th>Owner</th>
                <th>Amount</th>
                <th>Price [each]</th>
                <th>Time</th>
            </tr>
    ";
        if (!$db->num_rows())
            echo "<tr><td colspan='4' class='center'>There are no logs</td></tr>";
        else {
            $rows = $db->fetch_row();
            foreach ($rows as $row) {
                print"
                <tr>
                    <td>" . formatName($row['owner']) . "</td>
                    <td>" . prettynum($row['amount']) . "</td>
                    <td>" . prettynum($row['price'], 1) . "<br /><span class='small'>(Total: " . prettynum($row['price'] * $row['amount'], 1) . ")</span></td>
                    <td>" . date('H:i:s d/m/Y', $row['timestamp']) . "</td>
                </tr>
            ";
            }
        }
        print"</table><br /><div class='paginate'>" . $pages->display_pages() . "</div><br /></td></tr>";
    }
    if (isset($_GET['player']) && $_GET['page_action'] == "vaultlog") {
        $_GET['player'] = isset($_GET['player']) && ctype_digit($_GET['player']) ? $_GET['player'] : null;
        $db->query("SELECT id FROM grpgusers WHERE id = ?");
        $db->execute(array(
            $_GET['player']
        ));
        if (!$db->num_rows())
            $mtg->error("That player doesn't exist");
        $user = new User($_GET['player']);
        $db->query("SELECT COUNT(id) FROM vlog WHERE userid = ?");
        $db->execute(array(
            $_GET['player']
        ));
        $pages->items_total = $db->fetch_single();
        $pages->mid_range = 3;
        $db->query("SELECT * FROM vlog WHERE userid = ?" . $pages->limit);
        $db->execute(array(
            $_GET['player']
        ));
        ?><tr><td class="contentspacer"></td></tr>
        <tr><td class="contenthead"><?php
                echo $user->formattedname;
                ?>'s Vault Log</td></tr>
        <tr><td class="contentcontent">
                <div class='paginate'><?php
                    echo $pages->display_pages();
                    ?></div>
                <table id='newtables' style='width:100%;'>
                    <tr>
                        <th width='25%'>Received</th>
                        <th width='75%'>Description</th>
                    </tr><?php
                    if (!$db->num_rows())
                        echo "<tr><td colspan='2' class='center'>There are no logs</td></tr>";
                    else {
                        $rows = $db->fetch_row();
                        foreach ($rows as $row) {
                            if ($row['userid']) {
                                if (preg_match('/\[-_USERID_-\]/', $row['text'])) {
                                    $row['text'] = str_replace('[-_USERID_-]', formatName($row['userid']), $row['text']);
                                }
                                if (preg_match('/\[-_GANGID_-\]/', $row['text'])) {
                                    $gang = new Gang($row['extra']);
                                    $row['text'] = str_replace('[-_GANGID_-]', $gang->formattedname, $row['text']);
                                }
                            }
                            ?><tr>
                                <td><?php
                                    echo date('d F Y, g:i:sa', $row['timestamp']);
                                    ?></td>
                                <td><?php
                                    echo stripslashes($row['text']);
                                    ?></td>
                            </tr><?php
                        }
                    }
                    ?></table>
                <div class='paginate'><?php
                    echo $pages->display_pages();
                    ?></div>
            </td></tr><?php
    }
    if ($_GET['page_action'] == "vaultlog") {
        ?><tr><td class="contentspacer"></td></tr>
        <tr><td class="contenthead">Search Players Vault Log</td></tr>
        <tr><td class="contentcontent">
                <form method="get" action="gmpanel.php">
                    <input type='hidden' name='page_action' value='vaultlog' />
                    <table id='newtables' width='100%'>
                        <tr>
                            <th width="25%">Player ID</th>
                            <td width="75%"><input type="text" name="player" value="<?php
                                echo $_GET['player'];
                                ?>" /></td>
                        </tr>
                        <tr>
                            <td colspan='2' class='center'><input type="submit" value="Search Vault Logs" /></td>
                        </tr>
                    </table>
                </form>
            </td></tr><?php
    }
    if (isset($_POST['searchtra'])) {
        $_POST['id'] = isset($_POST['id']) && ctype_digit($_POST['id']) ? $_POST['id'] : null;
        if (empty($_POST['id']))
            $mtg->error("You didn't enter a valid player ID");
        $db->query("SELECT id FROM grpgusers WHERE id = ?");
        $db->execute(array(
            $_POST['id']
        ));
        if (!$db->num_rows())
            $mtg->error("That player doesn't exist");
        $user = new User($_POST['id']);
        $db->query("SELECT COUNT(id) FROM transferlog WHERE from = ? OR to = ?");
        $db->execute(array(
            $_POST['id'],
            $_POST['id']
        ));
        $pages->items_total = $db->fetch_single();
        $pages->mid_range = 3;
        $pages->paginate();
        $db->query("SELECT * FROM transferlog WHERE from = ? OR to = ? ORDER BY timestamp DESC " . $pages->limit);
        $db->execute(array(
            $_POST['id'],
            $_POST['id']
        ));
        ?><tr><td class="contentspacer"></td></tr>
        <tr><td class="contenthead"><?php
                echo $user->formattedname;
                ?>'s Sent Transfers</td></tr>
        <tr><td class="contentcontent">
                <table id='newtables' width='100%'>
                    <tr>
                        <th width='25%'>Time</th>
                        <th width='25%'>From</th>
                        <th width='25%'>To</th>
                        <th width='75%'>Transfer</th>
                    </tr><?php
                    if (!$db->num_rows())
                        echo "<tr><td colspan='4' class='center'>There are no logs</td></tr>";
                    else {
                        $rows = $db->fetch_row();
                        foreach ($rows as $row) {
                            $sender = new User($row['from']);
                            $recipient = new User($row['to']);
                            ?><tr>
                                <td><?php
                                    echo date('H:i:s d/m/Y', $row['timestamp']);
                                    ?></td>
                                <td><?php
                                    echo $sender->formattedname;
                                    ?></td>
                                <td><?php
                                    echo $recipient->formattedname;
                                    ?></td>
                                <td><?php
                                    if ($row['item']) {
                                        $db->query("SELECT itemname FROM items WHERE id = ?");
                                        $db->execute(array(
                                            $row['item']
                                        ));
                                        echo $db->num_rows() ? item_popup($db->fetch_single(), $row['item']) : 'Deleted item';
                                    } else if ($row['money'])
                                        echo '$' . $mtg->format($row['money']);
                                    else if ($row['points'])
                                        echo $mtg->format($row['points']) . ' point' . $mtg->s($row['points']);
                                    else if ($row['credits'])
                                        echo $mtg->format($row['credits']) . ' credit' . $mtg->s($row['credits']);
                                    ?></td>
                            </tr><?php
                        }
                    }
                    ?></table>
                <div class='paginate'><?php
                    echo $pages->display_pages();
                    ?></div>
            </td></tr><?php
    }
    if ($_GET['page_action'] == "tralog") {
        $_GET['player'] = isset($_GET['player']) && ctype_digit($_GET['player']) ? $_GET['player'] : null;
        ?><tr><td class="contentspacer"></td></tr>
        <tr><td class="contenthead">Search Player Transfers</td></tr>
        <tr><td class="contentcontent">
                <form action='gmpanel.php' method='post'>
                    <table id='newtables' width='100%'>
                        <tr>
                            <th width="25%">Player ID</th>
                            <td width="75%"><input type="text" name="id" value="<?php
                                echo $_GET['player'];
                                ?>" /></td>
                        </tr>
                        <tr>
                            <td colspan='2' class='center'><input type="submit" name="searchtra" value="Search Transfers" /></td>
                        </tr>
                    </table>
                </form>
            </td></tr><?php
    }
    if ($_GET['page_action'] == "reportmail") {
        $db->query("SELECT COUNT(id) FROM pms WHERE reported = 1");
        $db->execute();
        $pages->items_total = $db->fetch_single();
        $pages->mid_range = 3;
        $pages->paginate();
        $db->query("SELECT * FROM pms WHERE reported = 1 ORDER BY timesent DESC " . $pages->limit);
        $db->execute();
        ?><tr><td class="contentspacer"></td></tr>
        <tr><td class="contenthead">Reported Mail</td></tr>
        <tr><td class="contentcontent">
                <div class='paginate'><?php
                    echo $pages->display_pages();
                    ?></div>
                <table id='newtables' width='100%'>
                    <tr>
                        <th>Mail</th>
                        <th>Reported By</th>
                        <th>Clear Flag</th>
                    </tr><?php
                    if (!$db->num_rows())
                        echo "<tr><td colspan='3' class='center'>No messages have been flagged</td></tr>";
                    else {
                        $rows = $db->fetch_row();
                        foreach ($rows as $row) {
                            $reporter = new User($row['to']);
                            ?><tr>
                                <td><a href='gmpm.php?id=<?php
                                    echo $row['id'];
                                    ?>' target='new'><?php
                                           echo $mtg->format($row['subject']);
                                           ?></a></td>
                                <td><?php
                                    echo $reporter->formattedname;
                                    ?></td>
                                <td><form action='gmpanel.php?page_action=reportmail' method='post'>
                                        <input type='hidden' name='postid' value='<?php
                                        echo $row['id'];
                                        ?>' />
                                        <input type='submit' name='unreportmail' value='Clear Flag' />
                                    </form></td>
                            </tr><?php
                        }
                    }
                    ?></table>
                <div class='paginate'><?php
                    echo $pages->display_pages();
                    ?></div>
            </td></tr><?php
    }
    if ($_GET['page_action'] == "transfercheck") {
        $sql = "";
        if (isset($_GET['same']))
            $sql = " WHERE toip = fromip";
        if (isset($_GET['cash']))
            $sql = " WHERE money > 0";
        if (isset($_GET['pts']))
            $sql = " WHERE points > 0";
        if (isset($_GET['items']))
            $sql = " WHERE item > 0";
        $db->query("SELECT COUNT(id) FROM transferlog$sql");
        $db->execute();
        $pages->items_total = $db->fetch_single();
        $pages->mod_range = 3;
        $pages->paginate();
        $db->query("SELECT * FROM transferlog$sql ORDER BY timestamp DESC " . $pages->limit);
        $db->execute();
        ?><tr><td class="contentspacer"></td></tr>
        <tr><td class="contenthead">IP Transfer Checks</td></tr>
        <tr><td class="contentcontent">
        <center>
            <a href='?page_action=transfercheck&same'><button class='ycbutton'>Same IP Transfers</button></a>
            <a href='?page_action=transfercheck&cash'><button class='ycbutton'>Cash</button></a>
            <a href='?page_action=transfercheck&pts'><button class='ycbutton'>Points</button></a>
            <a href='?page_action=transfercheck&items'><button class='ycbutton'>Items</button></a>
        </center>
        <table id='newtables' style='width:100%;'>
            <tr>
                <th width='25%'>Time</th>
                <th width='25%'>Sender</th>
                <th width='25%'>Recipient</th>
                <th widrh='25%'>Transfer</th>
            </tr><?php
            if (!$db->num_rows())
                echo "<tr><td colspan='4' class='center'>No multiple-account transfers have been detected</td></tr>";
            else {
                $rows = $db->fetch_row();
                foreach ($rows as $row) {
                    $sender = new User($row['from']);
                    $recipient = new User($row['to']);
                    if ($row['item']) {
                        $db->query("SELECT itemname FROM items WHERE id = ?");
                        $db->execute(array(
                            $row['item']
                        ));
                        $what = $db->num_rows() ? item_popup($db->fetch_single(), $row['item']) : 'Deleted item';
                    } else if ($row['money'])
                        $what = prettynum($row['money'], 1);
                    else if ($row['points'])
                        $what = prettynum($row['points']) . ' points';
                    else if ($row['credits'])
                        $what = prettynum($row['credits']) . ' credits';
                    $color = $row['toip'] == $row['fromip'] ? " style='background:rgba(0,0,255,.5);'" : "";
                    print"
		<tr$color>
			<td>" . date('H:i:s d/m/Y', $row['timestamp']) . "</td>
			<td>$sender->formattedname<span style='font-size:10px;'><br />{$row['fromip']}</span></td>
			<td>$recipient->formattedname<span style='font-size:10px;'><br />{$row['toip']}</span></td>
			<td>$what</td>
		</tr>
			";
                }
            }
            ?></table>
        <div class='paginate'><?php
            echo $pages->display_pages();
            ?></div>
        </td></tr><?php
    }
    if ($_GET['page_action'] == "5050check") {
        $urlall = "?page_action=5050check";
        $urlsame = "?page_action=5050check&same";
        $urlpts = "?page_action=5050check&pts";
        $urlcash = "?page_action=5050check";
        $sql = "";
        $table = "cash5050log";
        if (isset($_GET['same'])) {
            $sql = " WHERE matcherip = betterip";
            $urlpts .= "&same";
            $urlcash .= "&same";
        }
        if (isset($_GET['pts'])) {
            $table = "pts5050log";
            $urlsame .= "&pts";
            $urlall .= "&pts";
        }
        $table = (isset($_GET['pts'])) ? "pts5050log" : "cash5050log";
        $db->query("SELECT COUNT(id) FROM $table{$sql}");
        $db->execute();
        $pages->items_total = $db->fetch_single();
        $pages->mid_range = 3;
        $pages->paginate();
        $db->query("SELECT * FROM $table{$sql} ORDER BY timestamp DESC " . $pages->limit);
        $db->execute();
        ?><tr><td class="contentspacer"></td></tr>
        <tr><td class="contenthead">50/50 Checks [<?php echo isset($_GET['pts']) ? "points" : "cash"; ?>]</td></tr>
        <tr><td class="contentcontent">
        <center>
            <?php
            echo"
			<a href='$urlall'><button class='ycbutton' style='", isset($_GET['same']) ? "" : "background:#333;", "'>All</button></a>
			<a href='$urlsame'><button class='ycbutton' style='", !isset($_GET['same']) ? "" : "background:#333;", "'>Same IP</button></a>
			<br /><br />
			<a href='$urlcash'><button class='ycbutton' style='", isset($_GET['pts']) ? "" : "background:#333;", "'>Cash</button></a>
			<a href='$urlpts'><button class='ycbutton' style='", !isset($_GET['pts']) ? "" : "background:#333;", "'>Points</button></a>
		";
            ?>
        </center>
        <div class='paginate'><?php
            echo $pages->display_pages();
            ?></div>
        <table id='newtables' style='width:100%;'>
            <tr>
                <th width='20%'>Time</th>
                <th width='20%'>Better</th>
                <th width='20%'>Matcher</th>
                <th width='20%'>Winner</th>
                <th width='20%'>Prize</th>
            </tr><?php
            if (!$db->num_rows())
                echo "<tr><td colspan='5' class='center'>No multiple-account bets have been detected</td></tr>";
            else {
                $rows = $db->fetch_row();
                foreach ($rows as $row) {
                    $better = new User($row['better']);
                    $matcher = new User($row['matcher']);
                    $winner = $row['better'] == $row['winner'] ? formatName($row['better']) : formatName($row['winner']);
                    $amnt = isset($_GET['pts']) ? prettynum($row['amount']) . " points" : prettynum($row['amount'], 1);
                    $color = $row['matcherip'] == $row['betterip'] ? " style='background:rgba(0,0,255,.5);'" : "";
                    print"
		<tr$color>
            <td>" . date('H:i:s d/m/Y', $row['timestamp']) . "</td>
			<td>$better->formattedname</td>
			<td>$matcher->formattedname</td>
			<td>$winner</td>
			<td>$amnt</td>
		</tr>
			";
                }
            }
            ?></table>
        <div class='paginate'><?php
            echo $pages->display_pages();
            ?></div>
        </td></tr><?php
    }
}
if (empty($_GET['page_action'])) {
    ?><tr><td class="contentspacer"></td></tr>
    <tr><td class="contenthead">GM Guide</td></tr>
    <tr><td class="contentcontent">
            <hr width="95%" />
            <b><font color="#EE0000">Offense System</font></b>
            <br /><br />
            <i>The following part of the guide states which punishment should be placed for each offense made in the game. If you are unsure of the one that should be placed please message an Admin with a detailed explanation.</i><br /><br />
            <i>All bannings and warnings should be noted in the Player Notes section of their profile. This should include the punishment, the date and your username.</i>
            <br /><br />
            <b><u>Hidden Links</u></b>
            <br />
            Hidden links are known as url's that are hidden with a misleading title. For example if i was to show this: <a href="http://mafiatown.com/profiles.php?id=1&rate=up" target="_blank">Free Stuff!</a> then thats classed as a hidden link. This is because i said you can get free stuff, however the link rates me up. For this offense the following actions should apply. For a first offense, a formal warning should be placed. For a second offense, a 3 day ban of the communication used to perform the offense should be placed. For a third offense, a 14 day ban of the communication used to perform the offense should be placed. And for a fourth offense, a permanent ban of the communication used to perform the offense should be placed (just set the days to 999,999).
            <br /><br />
            <b><u>Advertising</u></b>
            <br />
            Advertising is known as a player posting a link to their website or game. This could be done in the form of a link, an image or even just plain text. For this offense the following actions should be made. For a first offense, a formal warning should be placed. For a second offense, a 14 day ban of the communication used to perform the offense should be placed. For a third offense, a permanent ban of the communication used to perform the offense should be placed (just set the days to 999,999).
            <br /><br />
            <b><u>Spamming</u></b>
            <br />
            Spamming is known as a player purposely repeating the same post or thread. For example if i was to create five consecutive threads about the same thing it would be classed as spamming. For this offense the following actions should be placed. For a first offense, a 1 day ban of the communication used to perform the offense should be placed, for a second offense, a 14 day ban of the communication used to perform the offense should be placed. And for a third offense, a permanent ban of the communication used to perform the offense should be placed (just set the days to 999,999).
            <br /><br />
            <b><u>Verbal Abuse</u></b>
            <br />
            Verbal abuse is known as a player saying rude or inappripriate statements to another. This is usually in the form of extreme language. Depending on the seriousness of the abuse being caused the following actions should take place. For a small abusive statement, a formal warning should be placed and an appropriate apology to be written to the target. For an extreme abusive statement, the player should be banned of the communication used to perform the offense for 3 days. For a second offense, the player should be banned of the communication used to perform the offense for 14 days. And for a third offense a permanent ban of the communication used to perform the offense should be placed (just set the days to 999,999).
            <br /><br />
            <b><u>Multi-accounting</u></b>
            <br />
            Multi-accounting is known as a player having more than one account. They would usually do this to get extra stuff on one account and then trade it over to the other. However they would usually be on the same IP so it will be logged in the IP checks. If someone is caught of munti-account then a permanent ban of the game should be placed. If just a suspicion of multi-accounting is seen, then you should freeze the user for 3 days and message an Admin ASAP.
            <br /><br />
            <b><u>Under Age Players</u></b>
            <br />
            The Terms of Service state that you must be over the age of 14 to play Mafia Town. If a player is caught of being under the age limit then a permanent ban of the game should be placed. If a suspicion is seen then that user should be frozen for 3 days and message an Admin ASAP.
        </td></tr><?php
}
//require_once __DIR__ . '/footer.php';
function view_act_hour_logs() {
    $timestamp = $_GET['time'];
    $_GET['st'] = abs((int) $_GET['st']);
    if (isset($_GET['userid'])) {
        $_GET['userid'] = abs((int) $_GET['userid']);
        $url = "&userid={$_GET['userid']}";
    }
    print "<h3>Activity Logs</h3>
<br /><br />
<table width=100% cellspacing='1' class='table'> \n<tr style='background:rgba(0,255,0,.33)'> <th>Username</th> <th>Page</th> <th>POST TAGS</th> <th>Time</th> <th>Last</th> </tr>";
    $file = 'actlog.txt';
    $time = (!isset($_GET['time'])) ? time() : $_GET['time'] + 3600;
    $month = date('m', $time);
    $day = date('d', $time);
    $hour = date('G', $time) - 1;
    if (isset($_GET['time'])) {
        $file = "/usr/share/nginx/logs/actlog/" . $month . "_" . $day . "_" . $hour . $file;
    } else {
        $file = '/usr/share/nginx/logs/actlog.txt';
    }
    $current = file_get_contents($file);
    $add = array();
    $usid = (isset($_GET['userid'])) ? $_GET['userid'] : "[0-9]*";
    preg_match_all("/($usid)\|-\|-\|([^\|]*)\|-\|-\|(.+)\|-\|-\|([0-9]*);/", $current, $out, PREG_PATTERN_ORDER);
    $out[1] = array_reverse($out[1]);
    $out[2] = array_reverse($out[2]);
    $out[3] = array_reverse($out[3]);
    $out[4] = array_reverse($out[4]);
    for ($i = $_GET['st']; $i < count($out[1]); $i++) {
        if (!isset($last)) {
            $last = $out[4][$i];
        } else {
            $since = $last - $out[4][$i];
            $last = $out[4][$i];
        }
        if (isset($_GET['time'])) {
            $start = $_GET['time'];
            $end = $_GET['time'] + 86400;
            if ($out[4][$i] < $start)
                continue;
            if ($out[4][$i] > $end)
                break;
        }
        $uid = (!isset($_GET['userid'])) ? $out[1][$i] : $usid;
        print "<tr><td>" . formatName($out[1][$i]) . "</td> <td>{$out[2][$i]}</td><td><span title=\"";
        print_r(unserialize($out[3][$i]));
        print"\">";
        echo (!empty(unserialize($out[3][$i]))) ? "YES" : "NO";
        print"</span></td><td>" . date('F j Y g:i:s a', $out[4][$i]) . "</td><td>$since</td></tr>";
        if ($i > $_GET['st'] + 100)
            break;
    }
    print "</table><br />
";
    $tc = count($out[1]);
    $pages = ceil($tc / 100);
    print "Pages: ";
    for ($i = 1; $i <= $pages; $i++) {
        $st = ($i - 1) * 100;
        print "<a href='?action=acthour";
        echo (!empty($timestamp)) ? "&time=$timestamp" : "";
        print"&st=$st{$url}'>$i</a>&nbsp;";
        if ($i % 20 == 0) {
            print "<br />\n";
        }
    }
}
