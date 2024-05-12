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
            <a href='#' class='nav-link' onclick='showSection(\"weapons\")'>Weapons</a> |
            <a href='#' class='nav-link' onclick='showSection(\"armors\")'>Armors</a> |
            <a href='#' class='nav-link' onclick='showSection(\"shoes\")'>Shoes</a> |
            <a href='#' class='nav-link' onclick='showSection(\"consumables\")'>Consumables</a> |
            <a href='#' class='nav-link' onclick='showSection(\"rares\")'>Rares</a>
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
</div> <!-- Close container -->
";

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

<?php
include 'footer.php';
?>
