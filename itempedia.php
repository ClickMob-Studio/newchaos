<?php
include 'header.php';
genHead("<h1>Item Guide</h1>");
echo"
<hr>
<table id='newtables' style='width:100%;'>
    <tr style='background-color: #ff6218;'>
        <th style='background-color: #ff6218;' colspan='2'>Weapon</th>
    </tr>";
$db->query("SELECT *, (SELECT SUM(quantity) FROM inventory WHERE itemid = i.id) AS qty FROM items i WHERE offense != 0 AND buyable = 1 ORDER BY offense ASC");
$db->execute();
$rows = $db->fetch_row();
print displayItem($rows, 'offense');
print"
</table>
<br /><br />
<table id='newtables' style='width:100%;'>
    <tr style='background-color: #ff6218;'>
        <th style='background-color: #ff6218;' colspan='2 id='armor-section'>Armor</th>
    </tr>";
$db->query("SELECT *, (SELECT SUM(quantity) FROM inventory WHERE itemid = i.id) AS qty FROM items i WHERE defense != 0 AND buyable = 1 ORDER BY defense ASC");
$db->execute();
$rows = $db->fetch_row();
print displayItem($rows, 'defense');
print"
</table>
<br /><br />
<table id='newtables' style='width:100%;'>
    <tr style='background-color: #ff6218;'>
        <th style='background-color: #ff6218;' colspan='2' id='shoes-section'>Shoes</th>
    </tr>";
$db->query("SELECT *, (SELECT SUM(quantity) FROM inventory WHERE itemid = i.id) AS qty FROM items i WHERE speed != 0 AND buyable = 1 ORDER BY speed ASC");
$db->execute();
$rows = $db->fetch_row();
print displayItem($rows, 'defense');
print"</table>
   <br /><br />
<table id='newtables' style='width:100%;'>
    <tr style='background-color: #ff6218;'>
        <th style='background-color: #ff6218;' colspan='2' id='cons-section'>Consumables</th>
    </tr>";
$db->query("SELECT *, (SELECT SUM(quantity) FROM inventory WHERE itemid = i.id) AS qty FROM items i WHERE speed = 0 AND offense = 0 AND defense = 0 AND buyable = 1 AND (drugstime > 0 OR heal > 0) ORDER BY id ASC");
$db->execute();
$rows = $db->fetch_row();
print displayItem($rows);
print"</table>
    <br /><br />
<table id='newtables' style='width:100%;'>
    <tr style='background-color: #ff6218;'>
        <th style='background-color: #ff6218;' colspan='2' id='rares-section'>Rares</th>
    </tr>";
$db->query("SELECT *, (SELECT SUM(quantity) FROM inventory WHERE itemid = i.id) AS qty FROM items i WHERE rare = 1 ORDER BY id ASC");
$db->execute();
$rows = $db->fetch_row();
print displayItem($rows, 'rares');
print"</table>
</table>
</td></tr>";
include 'footer.php';
function displayItem(&$rows, $type = null) {
    global $m, $db;
    $rtn = "";
    $co = 0;
    $rowspan = ($type == null) ? 2 : 3;
    foreach ($rows as $row) {
        $boost = ($type == null || $type = 'rares') ? "" : "<tr><td>Boost: {$row[$type]}%</td></tr>";
        if($type = 'rares'){
            if($row['offense'])
                $boost = "<tr><td>Boost: {$row['offense']}% Strength</td></tr>";
            elseif($row['defense'])
                $boost = "<tr><td>Boost: {$row['defense']}% Defense</td></tr>";
            elseif($row['speed'])
                $boost = "<tr><td>Boost: {$row['speed']}% Speed</td></tr>";
        }




        //$city = ($row['city'] > 0) ? $m->get('city.' . $row['name']) : "none";
        // $city = ($type == null) ? "Every City" : $city;

        $city = "None";

        if ($row['city'] > 0) {
            $city = getCityNameByID($row['city']);
        } else {
            $city = ($type == null) ? "Every City" : $city;
        }


        $rtn .= "
            <tr colspan='2'>
            <td>
                <table style='width:100%;table-layout:fixed;'>
                    <tr>
                        <th>Name: <a href=description.php?id={$row['id']}> {$row['itemname']}</a></th>
                    </tr>
                    <tr>
                        <td>
                            <img src='{$row['image']}' style='width:100px;height:100px;' /><br />
                            <strong>Cost:</strong> " . prettynum($row['cost'], 1) . "<br />
                            <strong>City:<strong> $city<br />
                            {$row['description']}
                            
                        </td>
                    </tr>
                  
                    $boost
                </table>
            </td></tr>";
    }
    return $rtn;
}
?>