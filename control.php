<?php
include 'header.php';
if ($user_class->admin != 1) {
    message("You are not allowed here.");
    include("footer.php");
    die();
}
genHead("Navigation");
print '
    <a href="admin_clicker.php">Click Checker</a>
    <a href="control.php?page=playeritems">Player Items</a><br />
    <a href="control.php?page=referrals">Referrals</a><br />
    <a href="control.php?page=crimes">Crime Management</a><br />
    <a href="control.php?page=gcrimes">Gang Crime Managemenr</a><br />
    <a href="control.php?page=cities">City Management</a><br />
    <a href="control.php?page=jobs">Job Management</a><br />
    <a href="control.php?page=houses">House Management</a><br />
    <a href="control.php?page=gang">Gang Management</a><br />
';
if (!empty($_GET['givecredit'])) {
    $db->query("SELECT * FROM referrals WHERE id = ? AND credited = 0 LIMIT 1");
    $db->execute([$_GET['givecredit']]);
    $line = $db->fetch_row(true);
    bloodbath('referrals', $line['referrer']);

    perform_query("UPDATE grpgusers SET credits = credits + 50, points = points + 100, referrals = referrals + 1, refcomp = refcompt + 1, refcount = refcount + 1 WHERE id = ?", [$line['referrer']]);
    perform_query("UPDATE referrals SET credited = 1 WHERE id = ?", [$_GET['givecredit']]);
    perform_query("UPDATE referrals SET viewed = 1 WHERE id = ?", [$_GET['givecredit']]);

    Send_Event($line['referrer'], "You have been credited 50 Credits & 100 Points for referring [-_USERID_-]. Keep up the good work!", $line['referred']);
    echo Message("You have accepted the referral.");
}
if (!empty($_GET['denycredit'])) {
    $db->query("SELECT * FROM referrals WHERE id = ? AND credited = 0 LIMIT 1");
    $db->execute([$_GET['denycredit']]);
    $line = $db->fetch_row(true);

    perform_query("DELETE FROM referrals WHERE id = ?", [$_GET['denycredit']]);
    Send_Event($line['referrer'], "Unfortunately you have recieved no points for referring [-_USERID_-].", $line['referred']);
    perform_query("UPDATE referrals SET credited = 1 WHERE id = ?", [$_GET['denycredit']]);
    echo Message("You have denied the referral.");
}
if (isset($_GET['deletejob'])) {
    perform_query("DELETE FROM jobs WHERE id = ?", [$_GET['deletejob']]);

    echo Message("You have deleted a job.");
    mrefresh("control.php?page=jobs");
    include 'footer.php';
    die();
}
if (isset($_POST['addjobdb'])) {
    perform_query("INSERT INTO jobs (name, money, strength, defense, speed, level) VALUES (?, ?, ?, ?, ?, ?)", [$_POST['name'], $_POST['money'], $_POST['strength'], $_POST['defense'], $_POST['speed'], $_POST['level']]);
    echo Message("You have added a job to the database.");
}
if (isset($_POST['editjobdb'])) {
    perform_query("UPDATE jobs SET name = ?, money = ?, strength = ?, defense = ?, speed = ?, level = ? WHERE id = ?", [$_POST['name'], $_POST['money'], $_POST['strength'], $_POST['defense'], $_POST['speed'], $_POST['level'], $_POST['id']]);
    echo Message("You have edited a job.");
}
if (isset($_GET['deletehouse'])) {
    perform_query("DELETE FROM houses WHERE id = ?", [$_GET['deletehouse']]);
    echo Message("You have deleted a house.");
    mrefresh("control.php?page=houses");
    include 'footer.php';
    die();
}
if (isset($_POST['addhousedb'])) {
    perform_query("INSERT INTO houses (name, awake, cost) VALUES (?, ?, ?)", [$_POST['name'], $_POST['awake'], $_POST['cost']]);
    echo Message("You have added a house to the database.");
}
if (isset($_POST['edithousedb'])) {
    perform_query("UPDATE houses SET name = ?, awake = ?, cost = ? WHERE id = ?", [$_POST['name'], $_POST['awake'], $_POST['cost'], $_POST['id']]);
    echo Message("You have edited a house.");
}
if (isset($_GET['deletecity'])) {
    perform_query("DELETE FROM cities WHERE id = ?", [$_GET['deletecity']]);
    echo Message("You have deleted a city.");
    mrefresh("control.php?page=cities");
    include 'footer.php';
    die();
}
if (isset($_POST['addcitydb'])) {
    perform_query("INSERT INTO cities (name, levelreq, landleft, landprice, description, price) VALUES (?, ?, ?, ?, ?, ?)", [$_POST['name'], $_POST['levelreq'], $_POST['landleft'], $_POST['landprice'], $_POST['description'], $_POST['price']]);
    echo Message("You have added a city.");
}
if (isset($_POST['editcitydb'])) {
    perform_query("UPDATE cities SET name = ?, levelreq = ?, landleft = ?, landprice = ?, description = ?, price = ? WHERE id = ?", [$_POST['name'], $_POST['levelreq'], $_POST['landleft'], $_POST['landprice'], $_POST['description'], $_POST['price'], $_POST['id']]);
    echo Message("You have edited a city.");
}
if (isset($_GET['deletecrime'])) {
    perform_query("DELETE FROM crimes WHERE id = ?", [$_GET['deletecrime']]);
    echo Message("You have deleted a crime.");
    mrefresh("control.php?page=crimes");
    include 'footer.php';
    die();
}
if (isset($_POST['addcrimedb'])) {
    perform_query("INSERT INTO crimes (name, nerve, stext, ftext, ctext) VALUES (?, ?, ?, ?, ?)", [$_POST['name'], $_POST['nerve'], $_POST['stext'], $_POST['ftext'], $_POST['ctext']]);
    echo Message("You have added a crime.");
}
if (isset($_POST['editcrimedb'])) {
    perform_query("UPDATE crimes SET name = ?, nerve = ?, stext = ?, ftext = ?, ctext = ? WHERE id = ?", [$_POST['name'], $_POST['nerve'], $_POST['stext'], $_POST['ftext'], $_POST['ctext'], $_POST['id']]);
    echo Message("You have edited a crime.");
}
if (isset($_GET['deletegcrime'])) {
    perform_query("DELETE FROM gangcrime WHERE id = ?", [$_GET['deletegcrime']]);
    echo Message("You have deleted a gang crime.");
    mrefresh("control.php?page=gcrimes");
    include 'footer.php';
    die();
}
if (isset($_POST['addgcrimedb'])) {
    perform_query("INSERT INTO gangcrime (name, duration, reward, members) VALUES (?, ?, ?, ?)", [$_POST['name'], $_POST['duration'], $_POST['reward'], $_POST['members']]);
    echo Message("You have added a gang crime.");
}
if (isset($_POST['editgcrimedb'])) {
    perform_query("UPDATE gangcrime SET name = ?, duration = ?, reward = ?, members = ? WHERE id = ?", [$_POST['name'], $_POST['duration'], $_POST['reward'], $_POST['members'], $_POST['id']]);
    echo Message("You have edited a gang crime.");
}
if (isset($_POST['additemdb'])) {
    perform_query("INSERT INTO items (rmdays, money, points, itemname, description, cost, image, offense, defense, speed, heal, buyable, level) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", [$_POST['rmdays'], $_POST['money'], $_POST['points'], $_POST['itemname'], $_POST['description'], $_POST['cost'], $_POST['image'], $_POST['offense'], $_POST['defense'], $_POST['speed'], $_POST['heal'], $_POST['buyable'], $_POST['level']]);
}
if (isset($_GET['takealluser'])) {
    $oldamount = Check_Item($_GET['takeallitem'], $_GET['takealluser']);
    perform_query("DELETE FROM inventory WHERE userid = ? AND itemid = ?", [$_GET['takealluser'], $_GET['takeallitem']]);
    echo Message("That user had {$oldamount} of those, now they are all gone.");
}
if (isset($_POST['giveitem'])) {
    $oldamount = Check_Item($_POST['itemnumber'], Get_ID($_POST['username']));
    Give_Item($_POST['itemnumber'], Get_ID($_POST['username']), $_POST['itemquantity']);
    $newamount = Check_Item($_POST['itemnumber'], Get_ID($_POST['username']));
    echo Message("That user had {$oldamount} of those, and now has {$newamount} of them.");
}
if (isset($_POST['takeitem'])) {
    $oldamount = Check_Item($_POST['itemnumber'], Get_ID($_POST['username']));
    Take_Item($_POST['itemnumber'], Get_ID($_POST['username']), $_POST['itemquantity']);
    $newamount = Check_Item($_POST['itemnumber'], Get_ID($_POST['username']));
    echo Message("That user had {$oldamount} of those, and now has {$newamount} of them.");
}
if (isset($_POST['viewedititem'])) {
    $worked = Get_Item($_POST['itemid']);
    genHead("Edit Item");
    print "
        <form method='post'>
            <input type='text' name='itemname' size='10' maxlength='75' value='{$worked['itemname']}'> [itemname]<br />
            <input type='text' name='description' size='10' maxlength='75' value='{$worked['description']}'> [description]<br />
            <input type='text' name='cost' size='10' maxlength='75' value='{$worked['cost']}'> [cost]<br />
            <input type='text' name='image' size='10' maxlength='75' value='{$worked['image']}'> [image]<br />
            <input type='text' name='offense' size='10' maxlength='75' value='{$worked['offense']}'> [offense]<br />
            <input type='text' name='defense' size='10' maxlength='75' value='{$worked['defense']}'> [defense]<br />
            <input type='text' name='speed' size='10' maxlength='75' value='{$worked['speed']}'> [speed]<br />
            <input type='text' name='heal' size='10' maxlength='75'value='0' value='{$worked['heal']}'> [heal]<br />
            <input type='text' name='rmdays' size='10' maxlength='75'value='0' value='{$worked['rmdays']}'> [rmdays]<br />
            <input type='text' name='money' size='10' maxlength='75'value='0' value='{$worked['money']}'> [money]<br />
            <input type='text' name='points' size='10' maxlength='75'value='0' value='{$worked['points']}'> [points]<br />
            <input type='text' name='buyable' size='10' maxlength='75'value='0' value='{$worked['buyable']}'> [buyable]<br />
            <input type='text' name='level' size='10' maxlength='75' value='0' value='{$worked['level']}'> [level]<br />
            <input type='submit' name='edititemdb' value='Edit Item'></td></tr>
        </form>
    </td></tr>
    ";
}
if (isset($_POST['edititemdb'])) {
    perform_query("UPDATE items SET itemname = ?, description = ?, cost = ?, image = ?, offense = ?, defense = ?, speed = ?, heal = ?, buyable = ?, level = ?, rmdays = ?, money = ?, points = ? WHERE id = ?", [
        $_POST['itemname'],
        $_POST['description'],
        $_POST['cost'],
        $_POST['image'],
        $_POST['offense'],
        $_POST['defense'],
        $_POST['speed'],
        $_POST['heal'],
        $_POST['buyable'],
        $_POST['level'],
        $_POST['rmdays'],
        $_POST['money'],
        $_POST['points'],
        $_POST['itemid']
    ]);
    echo Message("You have edited an item.");
}
if (isset($_POST['listitems'])) {
    $oldamount = Check_Item($_POST['itemnumber'], Get_ID($_POST['username']));

    $db->query("SELECT * FROM inventory WHERE userid = ?");
    $db->execute([Get_ID($_POST['username'])]);
    $rows = $db->fetch_row();
    foreach ($rows as $line) {
        $worked2 = Get_Item($line['itemid']);
        $out .= "<div>{$line['itemid']} " . item_popup($worked2['itemname'], $worked2['id']) . " ${$worked2['cost']} Quantity: {$line['quantity']} <a href='control.php?page=playeritems&takealluser=" . Get_ID($_POST['username']) . "&takeallitem={$line['itemid']}'>Take All</a></div>";
    }
    echo Message($_POST['username'] . "'s Items<br>$out");
}
if (isset($_POST['changemessage'])) {
    perform_query("UPDATE serverconfig SET messagefromadmin = ?", [addslashes($_POST['message'])]);
    echo Message("You have changed the marquee text.");
}
if (isset($_POST['changeadmin'])) {
    perform_query("UPDATE serverconfig SET admin = ?", [addslashes($_POST['message'])]);
    echo Message("You have changed the admin notification.");
}
if (isset($_POST['changeserverdown'])) {
    perform_query("UPDATE serverconfig SET serverdown = ?", [addslashes($_POST['message'])]);
    echo Message("You have changed the server down text.");
}
if (isset($_POST['activate1'])) {
    perform_query("UPDATE serverconfig SET polled1 = 'active'");
    perform_query("UPDATE grpgusers SET polled1 = 0");
    echo Message("You have activated the poll.");
}
if (isset($_POST['unactivate1'])) {
    perform_query("UPDATE serverconfig SET polled1 = 'unactive'");
    perform_query("UPDATE grpgusers SET polled1 = 1");
    echo Message("You have un-activated the poll.");
}
if (isset($_POST['resetpoll1'])) {
    perform_query("UPDATE grpgusers SET polled1 = 0");
    perform_query("UPDATE poll1 SET votes = '0'");
    echo Message("You have reset the poll.");
}
if (isset($_POST['giveitem'])) {
    $oldamount = Check_Item($_POST['itemnumber'], Get_ID($_POST['username']));
    Give_Item($_POST['itemnumber'], Get_ID($_POST['username']), $_POST['itemquantity']);
    $newamount = Check_Item($_POST['itemnumber'], Get_ID($_POST['username']));
    echo Message("That user had {$oldamount} of those, and now has {$newamount} of them.");
}
genHead("Control Panel");
print "Welcome to the control panel. Here you can do just about anything, from giving players items they have paid for with real money, to adding, changing, or deleting jobs, cities, items, etc. </td></tr>";
if (empty($_GET['page'])) {
    genHead("Activate Poll");
    print "
        <form method='post'>
            <center>
            <input type='submit' name='activate1' value='Activate Poll'>&nbsp;&nbsp;&nbsp;
            <input type='submit' name='unactivate1' value='Unactivate Poll'>
            <br />
            <input type='submit' name='resetpoll1' value='Reset Poll'>
            </center>
        </form>
    </td></tr>
    ";
    genHead("Poll 1");
    $db->query("SELECT * FROM poll1 ORDER BY optionid");
    $db->execute();
    $rows = $db->fetch_row();

    $db->query("SELECT SUM(votes) FROM poll1");
    $db->execute();
    $total = $db->fetch_row(true);
    print '
        <table width="100%">
            <tr><td><b>Option Name</b></td><td><b>Votes</b></td></tr>
    ';
    foreach ($rows as $line) {
        $percent = ($total != 0) ? round(($line['votes'] / $total) * 100) : 0;
        $votes = "{$line['votes']}&nbsp;[{$percent}%]";
        echo "<tr><td width='70%'>{$line['optionname']}</td><td width='30%'>{$votes}</td></tr>";
    }
    print "</table>
            </td></tr>";
    genHead("Change Admin Notification");
    print "<form method='post'>";

    $db->query("SELECT * FROM serverconfig");
    $db->execute();
    $worked = $db->fetch_row(true);
    print "
            <textarea name='message' cols='53' rows='7'>{$worked['admin']}</textarea><br />
            <input type='submit' name='changeadmin' value='Change Admin Notification'>
        </form>
    </td></tr>
    ";
    genHead("Change Marquee Text");
    print "<form method='post'>";
    print "
            <textarea name='message' cols='53' rows='7'>{$worked['messagefromadmin']}</textarea><br />
            <input type='submit' name='changemessage' value='Change Marquee Text'>
        </form>
    </td></tr>
    ";
    genHead("Change Server Down Text");
    print "<form method='post'>";
    print "
            <textarea name='message' cols='53' rows='7'>{$worked['serverdown']}</textarea><br />
            <input type='submit' name='changeserverdown' value='Change Server Down Text'>
        </form>
    </td></tr>
    ";
} else {
    if ($_GET['page'] == "playeritems") {
        genHead("List Of All Items");
        $db->query("SELECT * FROM items ORDER BY id ASC");
        $db->execute();
        $rows = $db->fetch_row();
        foreach ($rows as $line) {
            echo "<div>{$line['id']} " . item_popup($line['itemname'], $line['id']) . "&nbsp;&nbsp;$" . prettynum($line['cost']) . "</div>";
        }
        print "</td></tr>";
        genHead("Add New Item To Database");
        print "
            <form method='post'>
                <input type='text' name='itemname' size='10' maxlength='75'> [itemname]<br />
                <input type='text' name='description' size='10' maxlength='75'> [description]<br />
                <input type='text' name='cost' size='10' maxlength='75'> [cost]<br />
                <input type='text' name='image' size='10' maxlength='75' value='images/noimage.png'> [image]<br />
                <input type='text' name='offense' size='10' maxlength='75'> [offense]<br />
                <input type='text' name='defense' size='10' maxlength='75'> [defense]<br />
                <input type='text' name='speed' size='10' maxlength='75'> [speed]<br />
                <input type='text' name='heal' size='10' maxlength='75'value='0'> [heal]<br />
                <input type='text' name='rmdays' size='10' maxlength='75'value='0'> [rmdays]<br />
                <input type='text' name='money' size='10' maxlength='75'value='0'> [money]<br />
                <input type='text' name='points' size='10' maxlength='75'value='0'> [points]<br />
                <input type='text' name='buyable' size='10' maxlength='75'value='1'> [buyable]<br />
                <input type='text' name='level' size='10' maxlength='75' value='0'> [level]<br />
                <input type='submit' name='additemdb' value='Add Item'></td></tr>
            </form>
        </tr></td>
        ";
        genHead("View/Edit An Item");
        print "
            <form method='post'>
                <input type='text' name='itemid' size='10' maxlength='75'> [Item ID]<br />
                <input type='submit' name='viewedititem' value='View/Edit Item'></td></tr>
        ";
        genHead("Give Item");
        print "
            <form method='post'>
                <input type='text' name='username' size='10' maxlength='75'> [Username]<br />
                <input type='text' name='itemnumber' size='10' maxlength='75'> [Item Number]<br/>
                <input type='text' name='itemquantity' size='10' maxlength='75'> [Quantity]<br/>
                <input type='submit' name='giveitem' value='Give Items'></td></tr>
            </form>
        ";
    }
    if ($_GET['page'] == "referrals") {
        genHead("Manage Referrals");
        $db->query("SELECT * FROM referrals WHERE credited = 0 AND referrer > 0 ORDER BY `when` DESC");
        $db->execute();
        $rows = $db->fetch_row();
        echo '<table id="newtables" style="width:100%;">';
        echo '<tr>';
        echo '<th>Username</th>';
        echo '<th>Level</th>';
        echo '<th>Referref By</th>';
        echo '<th>Last Active</th>';
        echo '<th>Time</th>';
        echo '<th>IP</th>';
        echo '<th>Actions</th>';
        echo '</tr>';
        foreach ($rows as $row) {
            $them = new User($row['referred']);
            echo '<tr>';
            echo '<td>' . $them->formattedname . '</td>';
            echo '<td>' . $them->level . '</td>';
            echo '<td>' . formatName($row['referrer']) . '</td>';
            echo '<td>' . $them->formattedlastactive . '</td>';
            echo '<td>' . date("d F Y, g:ia", $row['when']) . '</td>';
            echo '<td>' . $them->ip . '</td>';
            echo '<td><a href="control.php?page=referrals&givecredit=' . $row['id'] . '">Credit</a> | <a href="control.php?page=referrals&denycredit=' . $row['id'] . '">Deny</a></td>';
            echo '</tr>';
        }
        print '</td></tr>';
    }
    if ($_GET['page'] == "crimes") {
        genHead("Crimes");

        $db->query("SELECT * FROM crimes");
        $db->execute();
        $rows = $db->fetch_row();
        echo "<table width='100%'><tr><td><b>ID</b></td><td><b>Name</b></td><td><b>Nerve</b></td><td><b>Delete</b></td><tr>";
        foreach ($rows as $line) {
            echo "<tr><td>{$line['id']}</td><td>{$line['name']}</td><td>{$line['nerve']}</td><td><a href='control.php?page=crimes&deletecrime={$line['id']}'>[Delete Crime]</a></td></tr>";
        }
        echo "</table></td></tr>";
        genHead("Add New Crime To Database");
        print "
            <form method='post'>
                <input type='text' name='name' size='30' maxlength='75'> [name]<br />
                <input type='text' name='nerve' size='30' maxlength='75'> [nerve]<br />
                <textarea name='stext' cols='53' rows='7'>Success message</textarea><br />
                <textarea name='ctext' cols='53' rows='7'>Fail message</textarea><br />
                <textarea name='ftext' cols='53' rows='7'>Fail and caught message</textarea><br />
                <input type='submit' name='addcrimedb' value='Add Crime'></td></tr>
            </form>
        ";
        genHead("View/Edit A Crime");
        print "
            <form method='post'>
                <input type='text' name='crimeid' size='10' maxlength='75'> [Crime ID]<br />
                <input type='submit' name='vieweditcrime' value='View/Edit Crime'></td></tr>
        ";
        if ($_POST['vieweditcrime']) {
            $db->query("SELECT * FROM crimes WHERE id = ?");
            $db->execute([$_POST['crimeid']]);
            $worked = $db->fetch_row(true);
            genHead("Edit Crime");
            print "
                <form method='post'>
                    <input type='text' name='name' size='30' maxlength='75' value='{$worked['name']}'> [name]<br />
                    <input type='text' name='nerve' size='30' maxlength='75' value='{$worked['nerve']}'> [nerve]<br />
                    <textarea name='stext' cols='53' rows='7'>{$worked['stext']}</textarea><br />
                    <textarea name='ctext' cols='53' rows='7'>{$worked['ctext']}</textarea><br />
                    <textarea name='ftext' cols='53' rows='7'>{$worked['ftext']}</textarea><br />
                    <input type='hidden' name='id' value='{$worked['id']}'>
                    <input type='submit' name='editcrimedb' value='Edit Crime'></td></tr>
                </form>
            ";
        }
    }
    if ($_GET['page'] == "gcrimes") {
        genHead("Gang Crimes");
        $db->query("SELECT * FROM gangcrime");
        $db->execute();
        $rows = $db->fetch_row();
        echo "<table width='100%'><tr><td><b>ID</b></td><td><b>Name</b></td><td><b>Duration</b></td><td><b>Reward</b></td><td><b>Members</b></td><td><b>Delete</b></td><tr>";
        foreach ($rows as $line) {
            echo "<tr><td>{$line['id']}</td><td>{$line['name']}</td><td>{$line['duration']} hrs</td><td>${prettynum($line['reward'])}</td><td>{$line['members']}</td><td><a href='control.php?page=gcrimes&deletegcrime={$line['id']}'>[Delete Gang Crime]</a></td></tr>";
        }
        echo "</table></td></tr>";
        genHead("Add New Gang Crime To Database");
        print "
            <form method='post'>
                <input type='text' name='name' size='30' maxlength='75'> [name]<br />
                <input type='text' name='duration' size='30' maxlength='75'> [duration]<br />
                <input type='text' name='reward' size='30' maxlength='75'> [reward]<br />
                <input type='text' name='members' size='30' maxlength='75'> [members]<br />
                <input type='submit' name='addgcrimedb' value='Add Gang Crime'></td></tr>
            </form>
        ";
        genHead("View/Edit a Gang Crime");
        print "
            <form method='post'>
                <input type='text' name='gcrimeid' size='10' maxlength='75'> [Gang Crime ID]<br />
                <input type='submit' name='vieweditgcrime' value='View/Edit Gang Crime'></td></tr>
        ";
        if ($_POST['vieweditgcrime']) {
            $db->query("SELECT * FROM gangcrime WHERE id = ?");
            $db->execute([$_POST['gcrimeid']]);
            $worked = $db->fetch_row(true);
            genHead("Edit Gang Crime");
            print "
                <form method='post'>
                    <input type='text' name='name' size='30' maxlength='75' value='{$worked['name']}'> [name]<br />
                    <input type='text' name='duration' size='30' maxlength='75' value='{$worked['duration']}'> [duration]<br />
                    <input type='text' name='reward' size='30' maxlength='75' value='{$worked['reward']}'> [reward]<br />
                    <input type='text' name='members' size='30' maxlength='75' value='{$worked['members']}'> [members]<br />
                    <input type='hidden' name='id' value='{$worked['id']}'>
                    <input type='submit' name='editgcrimedb' value='Edit Gang Crime'></td></tr>
                </form>
            ";
        }
    }
    if ($_GET['page'] == "cities") {
        genHead("Cities");
        $db->query("SELECT * FROM cities");
        $db->execute();
        $rows = $db->fetch_row();
        echo "<table width='100%'><tr><td><b>ID</b></td><td><b>Name</b></td><td><b>Level Req</b></td><td><b>Land Left</b></td><td><b>Land Price</b></td><td><b>Price</b></td><td><b>Delete</b></td></tr>";
        foreach ($rows as $line) {
            echo "<tr><td>{$line['id']}</td><td>{$line['name']}</td><td>{$line['levelreq']}</td><td>" . prettynum($line['landleft']) . "</td><td>$" . prettynum($line['landprice']) . "</td><td>$" . prettynum($line['price']) . "</td><td><a href='control.php?page=cities&deletecity={$line['id']}'>[Delete City]</a></td></tr>";
        }
        echo "</table></td></tr>";
        genHead("Add New City To Database");
        print "
            <form method='post'>
                <input type='text' name='name' size='30' maxlength='75'> [name]<br />
                <input type='text' name='levelreq' size='30' maxlength='75'> [level req]<br />
                <input type='text' name='landleft' size='30' maxlength='75'> [land left]<br />
                <input type='text' name='landprice' size='30' maxlength='75'> [land price]<br />
                <input type='text' name='price' size='30' maxlength='75'> [price] <font size=1.5px>this will be the bus price and the drive price will be a 3rd of the cost.</font><br />
                <textarea name='description' cols='53' rows='7'>Description goes here...</textarea><br />
                <input type='submit' name='addcitydb' value='Add City'></td></tr>
            </form>
        ";
        genHead("View/Edit a City");
        print "
            <form method='post'>
                <input type='text' name='cityid' size='10' maxlength='75'> [City ID]<br />
                <input type='submit' name='vieweditcity' value='View/Edit City'></td></tr>
        ";
        if ($_POST['vieweditcity']) {
            $db->query("SELECT * FROM cities WHERE id = ?");
            $db->execute([$_POST['cityid']]);
            $worked = $db->fetch_row(true);
            genHead("Edit City");
            print "
                <form method='post'>
                    <input type='text' name='name' size='30' maxlength='75' value='{$worked['name']}'> [name]<br />
                    <input type='text' name='levelreq' size='30' maxlength='75' value='{$worked['levelreq']}'> [level req]<br />
                    <input type='text' name='landleft' size='30' maxlength='75' value='{$worked['landleft']}'> [land left]<br />
                    <input type='text' name='landprice' size='30' maxlength='75' value='{$worked['landprice']}'> [land price]<br />
                    <input type='text' name='price' size='30' maxlength='75' value='{$worked['price']}'> [price] <font size=1.5px>this will be the bus price and the drive price will be a 3rd of the cost.</font><br />
                    <textarea name='description' cols='53' rows='7'>{$worked['description']}</textarea><br />
                    <input type='hidden' name='id' value='{$worked['id']}'>
                    <input type='submit' name='editcitydb' value='Edit City'></td></tr>
                </form>
            ";
        }
    }
    if ($_GET['page'] == "jobs") {
        genHead("Jobs");
        $db->query("SELECT * FROM jobs");
        $db->execute();
        $rows = $db->fetch_row();
        echo "<table width='100%'><tr><td><b>ID</b></td><td><b>Name</b></td><td><b>Money</b></td><td><b>Strength</b></td><td><b>Defense</b></td><td><b>Speed</b></td><td><b>Level</b></td><td><b>Delete</b></td><tr>";
        foreach ($rows as $line) {
            echo "<tr><td>{$line['id']}.)</td><td>{$line['name']}</td><td>$" . prettynum($line['money']) . "</td><td>" . prettynum($line['strength']) . "</td><td>" . prettynum($line['defense']) . "</td><td>" . prettynum($line['speed']) . "</td><td>{$line['level']}</td><td><a href='control.php?page=jobs&deletejob={$line['id']}'>[Delete Job]</a></td></tr>";
        }
        echo "</table></td></tr>";
        genHead("Add New Job To Database");
        print "
            <form method='post'>
                <input type='text' name='name' size='30' maxlength='75'> [name]<br />
                <input type='text' name='money' size='30' maxlength='75'> [money]<br />
                <input type='text' name='strength' size='30' maxlength='75'> [strength]<br />
                <input type='text' name='defense' size='30' maxlength='75'> [defense]<br />
                <input type='text' name='speed' size='30' maxlength='75'> [speed]<br />
                <input type='text' name='level' size='30' maxlength='75'> [level]<br />
                <input type='submit' name='addjobdb' value='Add Job'></td></tr>
            </form>
        ";
        genHead("View/Edit a Job");
        print "
            <form method='post'>
                <input type='text' name='jobid' size='10' maxlength='75'> [Job ID]<br />
                <input type='submit' name='vieweditjob' value='View/Edit Job'></td></tr>
        ";
        if ($_POST['vieweditjob']) {
            $db->query("SELECT * FROM jobs WHERE id = ?");
            $db->execute([$_POST['jobid']]);
            $worked = $db->fetch_row(true);
            genHead("Edit Job");
            print "
                <form method='post'>
                    <input type='text' name='name' size='30' maxlength='75' value='{$worked['name']}'> [name]<br />
                    <input type='text' name='money' size='30' maxlength='75' value='{$worked['money']}'> [money]<br />
                    <input type='text' name='strength' size='30' maxlength='75' value='{$worked['strength']}'> [strength]<br />
                    <input type='text' name='defense' size='30' maxlength='75' value='{$worked['defense']}'> [defense]<br />
                    <input type='text' name='speed' size='30' maxlength='75' value='{$worked['speed']}'> [speed]<br />
                    <input type='text' name='level' size='30' maxlength='75' value='{$worked['level']}'> [level]<br />
                    <input type='hidden' name='id' value='{$worked['id']}'>
                    <input type='submit' name='editjobdb' value='Edit Job'>
                </form>
            </td></tr>
            ";
        }
    }
    if ($_GET['page'] == "houses") {
        genHead("Houses");
        $db->query("SELECT * FROM houses");
        $db->execute();
        $rows = $db->fetch_row();
        echo "<table width='100%'><tr><td><b>ID</b></td><td><b>Name</b></td><td><b>Awake</b></td><td><b>Cost</b></td><td><b>Delete</b></td><tr>";
        foreach ($rows as $line) {
            echo "<tr><td>{$line['id']}.)</td><td>{$line['name']}</td><td>" . prettynum($line['awake']) . "</td><td>$" . prettynum($line['cost']) . "</td><td><a href='control.php?page=jobs&deletehouse={$line['id']}'>[Delete House]</a></td></tr>";
        }
        echo "</table></td></tr>";
        genHead("Add New House To Database");
        print "
            <form method='post'>
                <input type='text' name='name' size='30' maxlength='75'> [name]<br />
                <input type='text' name='awake' size='30' maxlength='75'> [awake]<br />
                <input type='text' name='cost' size='30' maxlength='75'> [cost]<br />
                <input type='submit' name='addhousedb' value='Add House'></td></tr>
            </form>
        ";
        genHead("Add New House To Database");
        print "
            <form method='post'>
                <input type='text' name='houseid' size='10' maxlength='75'> [House ID]<br />
                <input type='submit' name='viewedithouse' value='View/Edit House'></td></tr>
        ";
        if ($_POST['viewedithouse']) {
            $db->query("SELECT * FROM houses WHERE id = ?");
            $db->execute([$_POST['houseid']]);
            $worked = $db->fetch_row(true);
            genHead("Edit House");
            print "
                <form method='post'>
                    <input type='text' name='name' size='30' maxlength='75' value='{$worked['name']}'> [name]<br />
                    <input type='text' name='awake' size='30' maxlength='75' value='{$worked['awake']}'> [awake]<br />
                    <input type='text' name='cost' size='30' maxlength='75' value='{$worked['cost']}'> [cost]<br />
                    <input type='hidden' name='id' value='{$worked['id']}'>
                    <input type='submit' name='edithousedb' value='Edit House'>
                </form>
            </td></tr>
            ";
        }
    }
    if ($_GET['page'] == "gang") {
        genHead("Gang Info");
        if ($_POST['gangid']) {
            $gang_class = new Gang($_POST['gangid']);
            if (isset($_POST['delete'])) {
                $atawr = CheckGangWar($_POST['gangid']);
                if ($atwar == 1) {
                    echo Message("You can't delete your gang while your at war.");
                    include("footer.php");
                    die();
                }

                perform_query("UPDATE grpgusers SET gangleader = '0' WHERE id = ?", [$gang_class->leader]);
                perform_query("UPDATE grpgusers SET gang = '0' WHERE gang = ?", [$gang_class->id]);
                perform_query("DELETE FROM gangs WHERE id = ?", [$gang_class->id]);
                perform_query("DELETE FROM gangarmory WHERE gangid = ?", [$gang_class->id]);
                perform_query("DELETE FROM gangmail WHERE gangid = ?", [$gang_class->id]);
                perform_query("DELETE FROM ranks WHERE gang = ?", [$gang_class->id]);
                perform_query("DELETE FROM ganginvites WHERE gangid = ?", [$gang_class->id]);
                perform_query("DELETE FROM gang_loans WHERE gang = ?", [$gang_class->id]);

                $db->query("SELECT * FROM grpgusers WHERE gang = ?");
                $db->execute([$gang_class->id]);
                $rows = $db->fetch_row();
                foreach ($rows as $line) {
                    $gang_user = new User($line['id']);
                    if ($gang_user->weploaned == 1)
                        perform_query("UPDATE grpgusers SET eqweapon = 0, weploaned = 0 WHERE id = ?", [$line['id']]);
                    if ($gang_user->armorloaned == 1)
                        perform_query("UPDATE grpgusers SET eqarmor = 0, armloaned = 0 WHERE id = ?", [$line['id']]);
                    if ($gang_user->shoesloaned == 1)
                        perform_query("UPDATE grpgusers SET eqshoes = 0, shoeloaned = 0 WHERE id = ?", [$line['id']]);
                }
                echo Message("Your gang has been permanently deleted.");
            }
            print "
                Gang ID:" . prettynum($gang_class->id) . "<br />
                Gang Name: {$gang_class->name}<br />
                Gang Owner: {$gang_class->leader}<br />
                Money Vault: $" . prettynum($gang_class->moneyvault) . "<br />
                Point Vault: " . prettynum($gang_class->pointsvault) . "<br />
                Gang Level: " . prettynum($gang_class->level) . "<br />
                <form method='post'><input type='hidden' value='{$_POST['gangid']}' name='gangid' /><input type='submit' name='delete' id='delete' value='Delete' /></form>
            ";
        } else
            print "<br />
                <form method='post'>
                    <input type='text' name='gangid' id='gangid' />[gang ID]<br />
                    <input type='submit' name='submit' id='submit' value='Submit' />
                </form>
            ";
    }
}
include 'footer.php';
