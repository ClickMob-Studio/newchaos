<?php
include 'header.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
genHead("<h1>Item Guide</h1>");
echo "
<hr>
<br />
<div class='container'>
    <div class='row'>
        <div class='col-12'>
           <span> <a href='#' class='nav-link'  style='display:inline;' onclick='showSection(\"weapons\")'>Weapons</a> |</span>
           <span> <a href='#' class='nav-link' style='display:inline;' onclick='showSection(\"armors\")'>Armors</a> |</span>
           <span><a href='#' class='nav-link' style='display:inline;' onclick='showSection(\"shoes\")'>Shoes</a> |</span>
           <span><a href='#' class='nav-link' style='display:inline;' onclick='showSection(\"gloves\")'>Gloves</a> |</span>
           <span><a href='#' class='nav-link' style='display:inline;' onclick='showSection(\"consumables\")'>Consumables</a> |</span>
           <span><a href='#' class='nav-link' style='display:inline;' onclick='showSection(\"rares\")'>Rares</a> |</span>
           <span><a href='#' class='nav-link' style='display:inline;' onclick='showSection(\"house\")'>Home Improvement</a></span>
        </div>
    </div>
    <hr>
    <div id='weapons' class='item-section'>
        <h2>Weapons</h2>
        <div class='row'>";
$db->query("SELECT *, (SELECT SUM(quantity) FROM inventory WHERE itemid = i.id) AS qty FROM items i WHERE offense != 0 AND buyable = 1 ORDER BY offense ASC");
$db->execute();
$rows = $db->fetch_row();
echo displayItem($rows, 'offense');
echo "
        </div>
    </div>
    <div id='armors' class='item-section' style='display:none;'>
        <h2>Armors</h2>
        <div class='row'>";
$db->query("SELECT *, (SELECT SUM(quantity) FROM inventory WHERE itemid = i.id) AS qty FROM items i WHERE defense != 0 AND buyable = 1 ORDER BY defense ASC");
$db->execute();
$rows = $db->fetch_row();
echo displayItem($rows, 'defense');
echo "
        </div>
    </div>
    <div id='shoes' class='item-section' style='display:none;'>
        <h2>Shoes</h2>
        <div class='row'>";
$db->query("SELECT *, (SELECT SUM(quantity) FROM inventory WHERE itemid = i.id) AS qty FROM items i WHERE speed != 0 AND buyable = 1 ORDER BY speed ASC");
$db->execute();
$rows = $db->fetch_row();
echo displayItem($rows, 'speed');
echo "
        </div>
    </div>
    <div id='gloves' class='item-section' style='display:none;'>
        <h2>Gloves</h2>
        <div class='row'>";
$db->query("SELECT *, (SELECT SUM(quantity) FROM inventory WHERE itemid = i.id) AS qty FROM items i WHERE agility != 0 AND buyable = 1 ORDER BY agility ASC");
$db->execute();
$rows = $db->fetch_row();
echo displayItem($rows, 'gloves');
echo "
        </div>
    </div>
    <div id='consumables' class='item-section' style='display:none;'>
        <h2>Consumables</h2>
        <div class='row'>";
$db->query("SELECT *, (SELECT SUM(quantity) FROM inventory WHERE itemid = i.id) AS qty FROM items i WHERE speed = 0 AND offense = 0 AND defense = 0 AND buyable = 1 AND (drugstime > 0 OR heal > 0) ORDER BY id ASC");
$db->execute();
$rows = $db->fetch_row();
echo displayItem($rows);
echo "
        </div>
    </div>
    <div id='rares' class='item-section' style='display:none;'>
        <h2>Rares</h2>
        <div class='row'>";
$db->query("SELECT *, (SELECT SUM(quantity) FROM inventory WHERE itemid = i.id) AS qty FROM items i WHERE rare = 1 ORDER BY id ASC");
$db->execute();
$rows = $db->fetch_row();
echo displayItem($rows, 'rares');
echo "
        </div>
    </div>
    
    <div id='house' class='item-section' style='display:none;'>
        <h2>Home Improvements</h2>
        <div class='row'>";
$db->query("SELECT *, (SELECT SUM(quantity) FROM inventory WHERE itemid = i.id) AS qty FROM items i WHERE awake_boost > 0 ORDER BY id ASC");
$db->execute();
$rows = $db->fetch_row();
echo displayItem($rows);
echo "
        </div>
    </div>
</div> <!-- Close container -->
";
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
            elseif ($row['agility'])
                $boost = "<p>Boost: {$row['agility']}% Agility</p>";
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
<script>
function showSection(sectionId) {
    // Hide all sections
    var sections = document.querySelectorAll('.item-section');
    sections.forEach(function(sec) {
        sec.style.display = 'none';
    });

    // Show the selected section
    document.getElementById(sectionId).style.display = 'block';
}
document.addEventListener('DOMContentLoaded', function() {
    showSection('weapons'); // Show weapons by default
});
</script>
<?php
include 'footer.php';
?>
