<?php
require "header.php";
$userId = $user_class->id; // Assuming $user_class->id holds the current user's ID

// Fetch equipped items for weapon, armor, and shoes
$db->query("
    SELECT 
        eqweapon.id AS weapon_id, eqweapon.itemname AS weapon_name, eqweapon.image AS weapon_image,
        eqarmor.id AS armor_id, eqarmor.itemname AS armor_name, eqarmor.image AS armor_image,
        eqshoes.id AS shoes_id, eqshoes.itemname AS shoes_name, eqshoes.image AS shoes_image
    FROM 
        grpgusers u
    LEFT JOIN 
        items eqweapon ON u.eqweapon = eqweapon.id
    LEFT JOIN 
        items eqarmor ON u.eqarmor = eqarmor.id
    LEFT JOIN 
        items eqshoes ON u.eqshoes = eqshoes.id
    WHERE 
        u.id = ?
");
$db->execute([$userId]);

// Fetch the results as an associative array
$equippedItems = $db->fetch_row(true);

// Display equipped items
echo '<div class="equipped-items">';

if ($equippedItems['weapon_id']) {
    echo "<div class='equipped-item'>
            <img src='{$equippedItems['weapon_image']}' alt='{$equippedItems['weapon_name']}'>
            <p>Weapon: {$equippedItems['weapon_name']}</p>
          </div>";
} else {
    echo "<div class='equipped-item'>
            <p>Weapon: None</p>
          </div>";
}

if ($equippedItems['armor_id']) {
    echo "<div class='equipped-item'>
            <img src='{$equippedItems['armor_image']}' alt='{$equippedItems['armor_name']}'>
            <p>Armor: {$equippedItems['armor_name']}</p>
          </div>";
} else {
    echo "<div class='equipped-item'>
            <p>Armor: None</p>
          </div>";
}

if ($equippedItems['shoes_id']) {
    echo "<div class='equipped-item'>
            <img src='{$equippedItems['shoes_image']}' alt='{$equippedItems['shoes_name']}'>
            <p>Shoes: {$equippedItems['shoes_name']}</p>
          </div>";
} else {
    echo "<div class='equipped-item'>
            <p>Shoes: None</p>
          </div>";
}

echo '</div>';
?>
