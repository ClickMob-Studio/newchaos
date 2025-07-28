<?php
include 'header_dev.php';
if (isset($_GET['buy'])) {
    $buy = security($_GET['buy']);
    $db->query("SELECT * FROM houses WHERE id = ? AND buyable = 1");
    $db->execute(array($buy));
    $row = $db->fetch_row(true);
    if (empty($row))
        diefun("Error, this building was not found.");
    $cost = $row['cost'];
    $houselevel = $row['houselevel'];
    $text = "You have purchased a {$row['name']}. To move into this house, you have to visit the 'Your Properties' link in the mainmenu.";

    // can they afford it?
    if ($cost > ($user_class->money + $oldhouse) && $error != 1)
        diefun("You don't have enough money to buy that house.");
    if ($user_class->level < $houselevel && $error != 1)
        diefun("You need to be level $houselevel to live here.");

    $user_class->money += floor($oldhouse) - $cost;
    $db->query("UPDATE grpgusers SET money = ?, awake = 0 WHERE id = ?");
    $db->execute(array(
        $user_class->money,
        $user_class->id
    ));
    $db->query("INSERT INTO ownedproperties VALUES('', ?, ?)");
    $db->execute(array(
        $user_class->id,
        $buy
    ));
    echo Message($text);
}
$db->query(
    "SELECT * FROM buildinglevels
     WHERE enabled = 1 ORDER BY typeid, `level`"
);
$db->execute();
$rows = $db->fetch_row();
foreach ($rows as $row) {
    $owned = ($row['id'] == $user_class->house) ? 'background:rgba(0,0,255,.25);' : '';
    ?>
    <div class="floaty flexcont" style="width:98%;">
        <div class="flexele" style="border-right:thin solid #333;"><img src="images/hovel.png"></div>
        <div class="flexele">Level 1<br><br>Hovel</div>
        <div class="flexele" style="border-left:thin solid #333;">
            Food<br><br><?php echo isset($row['costfood']) && $row['costfood'] > 0 ? $row['costfood'] : 0; ?></div>
        <div class="flexele" style="border-left:thin solid #333;">
            Iron<br><br><?php echo isset($row['costiron']) && $row['costiron'] > 0 ? $row['costiron'] : 0; ?></div>
        <div class="flexele" style="border-left:thin solid #333;">
            Stone<br><br><?php echo isset($row['coststone']) && $row['coststone'] > 0 ? $row['coststone'] : 0; ?></div>
        <div class="flexele" style="border-left:thin solid #333;">
            Wood<br><br><?php echo isset($row['costwood']) && $row['costwood'] > 0 ? $row['costwood'] : 0; ?></div>
        <div class="flexele" style="border-left:thin solid #333;">
            Time<br><br><?php echo isset($row['costtime']) && $row['costtime'] > 0 ? $row['costtime'] : 0; ?></div>
        <div class="flexele" style="border-left:thin solid #333;">
            <a href="house.php?buy=1"><button>Buy</button></a>
        </div>
    </div>
    <?php
}
include 'footer.php';
?>