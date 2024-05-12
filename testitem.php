<?php
include 'header.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
genHead("<h1>Item Guide</h1>");
echo "
<hr>
<br />
<a href='#weapon-section'>Weapons</a> | <a href='#armor-section'>Armors</a> | <a href='#shoes-section'>Shoes</a> | <a href='#cons-section'>Consumables</a> | <a href='#rares-section'>Rares</a>
<br />
<hr>
<div class='container'>
    <h2 id='weapon-section'>Weapons</h2>
    <div class='row d-flex align-items-stretch'>";
$db->query("SELECT *, (SELECT SUM(quantity) FROM inventory WHERE itemid = i.id) AS qty FROM items i WHERE offense != 0 AND buyable = 1 ORDER BY offense ASC");
$db->execute();
$rows = $db->fetch_row();
echo displayItem($rows, 'offense');
echo "
    </div> <!-- Close weapon row -->
    <h2 id='armor-section'>Armors</h2>
    <div class='row d-flex align-items-stretch'>";
$db->query("SELECT *, (SELECT SUM(quantity) FROM inventory WHERE itemid = i.id) AS qty FROM items i WHERE defense != 0 AND buyable = 1 ORDER BY defense ASC");
$db->execute();
$rows = $db->fetch_row();
echo displayItem($rows, 'defense');
echo "
    </div> <!-- Close armor row -->
    <h2 id='shoes-section'>Shoes</h2>
    <div class='row d-flex align-items-stretch'>";
$db->query("SELECT *, (SELECT SUM(quantity) FROM inventory WHERE itemid = i.id) AS qty FROM items i WHERE speed != 0 AND buyable = 1 ORDER BY speed ASC");
$db->execute();
$rows = $db->fetch_row();
echo displayItem($rows, 'speed');
echo "
    </div> <!-- Close shoes row -->
    <h2 id='cons-section'>Consumables</h2>
    <div class='row d-flex align-items-stretch'>";
$db->query("SELECT *, (SELECT SUM(quantity) FROM inventory WHERE itemid = i.id) AS qty FROM items i WHERE speed = 0 AND offense = 0 AND defense = 0 AND buyable = 1 AND (drugstime > 0 OR heal > 0) ORDER BY id ASC");
$db->execute();
$rows = $db->fetch_row();
echo displayItem($rows);
echo "
    </div> <!-- Close consumables row -->
    <h2 id='rares-section'>Rares</h2>
    <div class='row d-flex align-items-stretch'>";
$db->query("SELECT *, (SELECT SUM(quantity) FROM inventory WHERE itemid = i.id) AS qty FROM items i WHERE rare = 1 ORDER BY id ASC");
$db->execute();
$rows = $db->fetch_row();
echo displayItem($rows, 'rares');
echo "
    </div> <!-- Close rares row -->
</div> <!-- Close container -->
";
include 'footer.php';

function displayItem($rows, $type = null) {
    $rtn = "";
    foreach ($rows as $row) {
        $boost = "";
        if ($type && $type != 'rares') {
            $boost = "<p>Boost: {$row[$type]}%</p>";
        } elseif ($type == 'rares') {
            if ($row['offense'])
                $boost = "<p>Boost: {$row['offense']}% Strength</p>";
            elseif ($row['defense'])
                $boost = "<p>Boost: {$row['defense']}% Defense</p>";
            elseif ($row['speed'])
                $boost = "<p>Boost: {$row['speed']}% Speed</p>";
        }
        $city = $row['city'] > 0 ? getCityNameByID($row['city']) : "Every City";
        $rtn .= "
        <div class='col-lg-4 col-md-6 col-sm-6 mb-4'>
            <div class='card h-100'>
                <img src='{$row['image']}' class='card-img-top' alt='{$row['itemname']}' style='max-height: 200px;
                max-width: 200px;
                text-align: center;
                margin: 0 auto;'>
                <div class='card-body d-flex flex-column'>
                    <h5 class='card-title'><a href='description.php?id={$row['id']}'>{$row['itemname']}</a></h5>
                    <p class='card-text'><strong>Cost:</strong> " . prettynum($row['cost'], 1) . "<br>
                    <strong>City:</strong> $city<br>{$row['description']}</p>
                    $boost
                </div>
            </div>
        </div>";
    }
    return $rtn;
}
?>
