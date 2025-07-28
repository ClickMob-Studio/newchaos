<?php
include "header.php";
?>

<div class='box_top'>Portfolio</div>
<div class='box_middle'>
    <div class='pad'>
        <?php
        if (isset($_GET['move'])) {
            security($_GET['move']);
            $db->query("SELECT * FROM ownedproperties o JOIN houses h ON o.houseid = h.id WHERE o.id = ? AND userid = ?");
            $db->execute(array(
                $_GET['move'],
                $user_class->id
            ));
            $row = $db->fetch_row(true);
            if (empty($row))
                diefun("This house was not found in your portfolio.");
            $db->startTrans();
            if ($user_class->house > 0) {
                $db->query("UPDATE grpgusers SET house = 0, awake = 0 WHERE id = ?");
                $db->execute(array(
                    $user_class->id
                ));
                $db->query("INSERT INTO ownedproperties VALUES(NULL, ?, ?)");
                $db->execute(array(
                    $user_class->id,
                    $user_class->house
                ));
                $user_class->house = 0;
            }
            $db->query("UPDATE grpgusers SET house = ?, awake = 0 WHERE id = ?");
            $db->execute(array(
                $row['houseid'],
                $user_class->id
            ));
            $user_class->house = $row['houseid'];
            $db->query("DELETE FROM ownedproperties WHERE id = ?");
            $db->execute(array(
                $_GET['move']
            ));
            $db->endTrans();
            echo Message("You have moved into your house.");
        }
        if (isset($_GET['sell'])) {
            security($_GET['sell']);
            $db->query("SELECT * FROM ownedproperties o JOIN houses h ON o.houseid = h.id WHERE o.id = ? AND userid = ?");
            $db->execute(array(
                $_GET['sell'],
                $user_class->id
            ));
            $row = $db->fetch_row(true);
            if (empty($row))
                diefun("This house was not found in your portfolio.");
            $db->startTrans();
            $add = $row['cost'] / 2;
            $db->query("UPDATE grpgusers SET money = money + ? WHERE id = ?");
            $db->execute(array(
                $add,
                $user_class->id
            ));
            $user_class->money += $add;
            $db->query("DELETE FROM ownedproperties WHERE id = ?");
            $db->execute(array(
                $_GET['sell']
            ));
            $db->endTrans();
            echo Message("You have sold your {$row['name']} for " . prettynum($add, 1) . ".");
        }
        if (isset($_GET['sellcur'])) {
            $db->query("SELECT * FROM houses WHERE id = ?");
            $db->execute(array(
                $user_class->house
            ));
            $row = $db->fetch_row(true);
            if (empty($row))
                diefun("This house was not found in the database.");
            $add = $row['cost'] / 2;
            $db->query("UPDATE grpgusers SET money = money + ?, house = 0, awake = 0 WHERE id = ?");
            $db->execute(array(
                $add,
                $user_class->id
            ));
            $user_class->house = 0;
            $user_class->money += $add;
            echo Message("You have sold your {$row['name']} for " . prettynum($add, 1) . ".");
        }

        if (isset($_GET['moveout'])) {
            if ($user_class->house == 0)
                diefun("You do not currently live in a house.");
            $db->query("SELECT * FROM houses WHERE id = ?");
            $db->execute(array(
                $user_class->house
            ));
            $row = $db->fetch_row(true);
            if (empty($row))
                diefun("Error, your house was not found.");
            $db->startTrans();
            $db->query("UPDATE grpgusers SET house = 0, awake = 0 WHERE id = ?");
            $db->execute(array(
                $user_class->id
            ));
            $db->query("INSERT INTO ownedproperties VALUES ('',?,?)");
            $db->execute(array(
                $user_class->id,
                $user_class->house
            ));
            $db->endTrans();
            $user_class->house = 0;
        }
        if (isset($_GET['return'])) {
            $return = security($_GET['return']);
            $db->query("SELECT * FROM rentedproperties WHERE renter = ? AND id = ?");
            $db->execute(array(
                $user_class->id,
                $return
            ));
            $row = $db->fetch_row(true);
            if (empty($row))
                diefun("Rental property was not found.");
            $db->startTrans();
            $db->query("DELETE FROM rentedproperties WHERE renter = ? AND id = ?");
            $db->execute(array(
                $user_class->id,
                $return
            ));
            $db->query("INSERT INTO ownedproperties VALUES ('', ?, ?)");
            $db->execute(array(
                $row['owner'],
                $row['houseid']
            ));
            Send_Event($row['owner'], "Your property that [-_USERID_-] was renting has been returned to you.", $user_class->id);
            $db->endTrans();
            echo Message("You have return this property to it's owner.");
        }
        echo "
    <br />
<div class='collegebox'>


<center><font color=white>Click <a href='house.php'>[Here]</a> to buy houses.</font></center><br />


    <table id='newtables' class='altcolors' style='width:100%;'>
        <tr>
            <th>House Image</th>
            <th>House Info</th>
            <th>Actions</th>
        </tr>";
        if ($user_class->house) {
            $db->query("SELECT * FROM houses WHERE id = ?");
            $db->execute(array(
                $user_class->house
            ));
            $row = $db->fetch_row(true);
            print "
        <tr>
            <td><img src='images/" . str_replace(array(" ", "*"), "", strtolower($row['name'])) . ".png' style='width:100px;'/></td>
            <td><span style='color:green;font-weight:bold;'>Equipped</span><br /><br />{$row['name']}<br /><br />" . prettynum($row['cost'], 1) . "<br /><br />" . number_format($row['awake']) . " Awake</td>
            <td>
                <a href='?moveout'><button>Move out of this House!</button></a><br /><br />
                <button onclick=\"if(confirm('Are you sure you want to sell your {$row['name']} for " . prettynum($row['cost'] / 2) . "?')) window.location.href = '?sellcur'\">Sell this House!</button><br /><br />
            </td>
        </tr>";
        }


        $db->query("SELECT *, r.id as rid FROM rentedproperties r JOIN houses h ON r.houseid = h.id WHERE renter = ? ORDER BY awake DESC");
        $db->execute(array(
            $user_class->id
        ));
        $rows = $db->fetch_row();
        foreach ($rows as $row)
            print "
        <tr>
            <td><img src='images/" . str_replace(array(" ", "*"), "", strtolower($row['name'])) . ".png' style='width:100px;' /></td>
            <td><span style='color:red;font-weight:bold;'>Rented Property</span><br /><br />Owner: " . formatName($row['owner']) . "<br /><br />{$row['name']}<br /><br />" . prettynum($row['cost'], 1) . "<br /><br />" . number_format($row['awake']) . " Awake</td>
            <td><a href='?return={$row['rid']}'><button>Return House to Owner</button></a><br /><br /></td>
        </tr>";

        // Move in with partner to rented property
        
        // if ($user_class->id == 174) {
//     $db->query("SELECT *, r.id as rid FROM rentedproperties r JOIN houses h ON r.houseid = h.id WHERE renter = ? ORDER BY awake DESC");
//     $db->execute(array(
//         $user_class->relplayer
//     ));
//     $rows = $db->fetch_row();
//     foreach ($rows as $row)
//         print"
//             <tr>
//                 <td><img src='images/" . str_replace(array(" ", "*"), "", strtolower($row['name'])) . ".png' /></td>
//                 <td><span style='color:red;font-weight:bold;'>Rented Property</span><br /><br />Owner: ".formatName($row['owner'])."<br /><br />{$row['name']}<br /><br />" . prettynum($row['cost'], 1) . "<br /><br />" . number_format($row['awake']) . " Awake</td>
//                 <td><a href='?moveinr={$row['rid']}'><button>Move In With " . formatName($user_class->relplayer) . "</button></a><br /><br /></td>
//             </tr>";
// }
        
        //
        

        $db->query("SELECT *, o.id as oid FROM ownedproperties o JOIN houses h ON houseid = h.id WHERE userid = ? ORDER BY awake DESC");
        $db->execute(array(
            $user_class->id
        ));
        $rows = $db->fetch_row();
        foreach ($rows as $row) {
            print "
        <tr>
            <td><img src='images/" . str_replace(array(" ", "*"), "", strtolower($row['name'])) . ".png' style='width:100px;' /></td>
            <td>{$row['name']}<br /><br />" . prettynum($row['cost'], 1) . "<br /><br />" . number_format($row['awake']) . " Awake</td>
            <td>
                <a href='?move={$row['oid']}'><button>Move into this House!</button></a><br /><br />
                <button onclick=\"if(confirm('Are you sure you want to sell your {$row['name']} for " . prettynum($row['cost'] / 2) . "?')) window.location.href = '?sell={$row['oid']}'\">Sell this House!</button><br /><br />
                
                </td>
        </tr>";
        }
        $db->query("SELECT *, r.id as rid FROM rentalMarket r JOIN houses h ON r.houseid = h.id WHERE owner = ? ORDER BY awake DESC");
        $db->execute(array(
            $user_class->id
        ));
        $rows = $db->fetch_row();
        foreach ($rows as $row) {
            echo "
        <tr>
            <td><img src='images/" . str_replace(array(" ", "*"), "", strtolower($row['name'])) . ".png' style='width:100px;' /></td>
            <td>{$row['name']}<br /><br />" . prettynum($row['cost'], 1) . "<br /><br />" . number_format($row['awake']) . " Awake<br /><br />Cost Per Day: {$row['costperday']}<br /><br />Must buy <span style='color:red;font-weight:bold;'>{$row['days']}</span> Days<br /><br />Total Cost: " . prettynum($row['costperday'] * $row['days'], 1) . "</td>
            <td><a href='rentalmarket.php'><button>Rental Market</button></a></td>
        </tr>";
        }
        $db->query("SELECT * FROM rentedproperties r JOIN houses h ON r.houseid = h.id WHERE owner = ?");
        $db->execute(array(
            $user_class->id
        ));
        $rows = $db->fetch_row();
        foreach ($rows as $row)
            print "
        <tr>
            <td><img src='images/" . str_replace(array(" ", "*"), "", strtolower($row['name'])) . ".png' style='width:100px;' /></td>
            <td>{$row['name']}<br /><br />" . prettynum($row['cost'], 1) . "<br /><br />" . number_format($row['awake']) . " Awake</td>
            <td>Renter: " . formatName($row['renter']) . "<br /><br />Days Left: {$row['days']}</td>
        </tr>";
        print "</table></div>";
        include "footer.php";
        ?>