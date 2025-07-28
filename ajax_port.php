<?php
include "ajax_header.php";
$uid = security($_POST['uid']);
$db->query("SELECT h.* FROM houses h JOIN grpgusers g ON h.id = g.house WHERE g.id = ? AND g.house > 0");
$db->execute(array(
    $uid
));
$cur = $db->fetch_row(true);
$db->query("SELECT h.* FROM houses h JOIN ownedproperties o ON h.id = o.houseid WHERE userid = ?");
$db->execute(array(
    $uid
));
$backs = $db->fetch_row();
$db->query("SELECT h.* FROM houses h JOIN rentalmarket r ON r.houseid = h.id WHERE owner = ?");
$db->execute(array(
    $uid
));
$rents = $db->fetch_row();
$db->query("SELECT h.* FROM houses h JOIN rentedproperties r ON h.id = r.houseid WHERE owner = ?");
$db->execute(array(
    $uid
));
$rentals = $db->fetch_row();
$all = array();
if (!empty($cur))
    $all[] = array('cur' => $cur);
foreach ($backs as $back)
    $all[] = array('back' => $back);
foreach ($rents as $rent)
    $all[] = array('rent' => $rent);
foreach ($rentals as $rental)
    $all[] = array('rental' => $rental);
$co = 0;
print "
    <table id='newtables' style='width:100%;'>";
foreach ($all as $them) {
    foreach ($them as $type => $row) {
        switch ($type) {
            case 'cur':
                $type = "<span style='color:green;font-weight:bold;'>House Owned</span>";
                break;
            case 'back':
                $type = "<span style='color:blue;font-weight:bold;'>Housing in Portfolio</span>";
                break;
            case 'rent':
                $type = "<span style='color:red;font-weight:bold;'>House on Market</span>";
                break;
            case 'rental':
                $type = "<span style='color:Orange;font-weight:bold;'>House Rented Out</span>";
                break;
        }
        if ($co++ % 2 == 0)
            print "<tr>";
        print "
            <td>
                <table style='width:100%;'>
                    <tr>
                        <td rowspan='3' style='width:200px;'><img src='images/" . str_replace(array(" ", "*"), "", strtolower($row['name'])) . ".png' /></td>
                        <td>$type</td>
                    </tr>
                    <tr>
                        <td>{$row['name']}</td>
                    </tr>
                    <tr>
                        <td>Awake: " . prettynum($row['awake']) . "</td>
                    </tr>
                </table>
            </td>";
        if ($co % 2 == 0)
            print "</tr>";
    }
}
print "</table>";