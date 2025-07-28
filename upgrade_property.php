<?php
include "header.php"; // Include the header file, adjust the path as necessary

echo '<style>
    .upgrade-container { font-family: "Arial", sans-serif; color: #fff; }
    .upgrade-container .upgrade-table { width: 100%; margin: 20px auto; border-collapse: collapse; }
    .upgrade-container .upgrade-table th, .upgrade-container .upgrade-table td { text-align: left; padding: 15px; border-bottom: 1px solid #444; }
    .upgrade-container .upgrade-table th { background-color: #222; color: #fff; }
    .upgrade-container .upgrade-table tr:nth-child(even) { background-color: #2c2c2c; }
    .upgrade-container .upgrade-table tr:nth-child(odd) { background-color: #242424; }
    .upgrade-container .upgrade-table td.action { text-align: center; }
    .upgrade-container .button { background-color: #5cb85c; color: white; padding: 10px 20px; text-align: center; border: none; border-radius: 4px; font-size: 14px; cursor: pointer; transition: background-color 0.3s; }
    .upgrade-container .button:hover { background-color: #4cae4c; }
    .upgrade-container .debug-info { background-color: #202020; padding: 10px; margin-bottom: 20px; border-radius: 4px; text-align: center; }
</style>';

echo "<div class='upgrade-container'>"; // Start of the upgrade container

if (!isset($_GET['property_id'])) {
    echo "<div class='debug-info'>No property specified.</div>";
    exit;
}

$property_id = $_GET['property_id'];
$userid = $user_class->id; // Assuming $user_class contains user details

// Debug info
echo "<div class='debug-info'>Debug Info: Property ID = $property_id, User ID = $userid</div>";

// Fetch property details to confirm ownership
$query = mysql_query("SELECT * FROM ownedproperties WHERE id = '" . mysql_real_escape_string($property_id) . "' AND userid = '" . mysql_real_escape_string($userid) . "'");
$property = mysql_fetch_assoc($query);

if (!$property) {
    echo "<div class='debug-info'>Property not found or you do not have permission to upgrade this property.</div>";
    echo "</div>"; // Close the upgrade container
    include "footer.php"; // Include the footer file, adjust the path as necessary
    exit;
}

// Display available upgrades
$query = mysql_query("SELECT * FROM upgrades");
if (mysql_num_rows($query) == 0) {
    echo "<div class='debug-info'>No upgrades found in the database.</div>";
} else {
    echo "<table class='upgrade-table'><tr><th>Upgrade Name</th><th>Cost</th><th>Duration</th><th>Effect</th><th>Action</th></tr>";
    while ($upgrade = mysql_fetch_assoc($query)) {
        echo "<tr>
                <td>" . htmlspecialchars($upgrade['upgrade_name']) . "</td>
                <td>" . htmlspecialchars($upgrade['cost']) . "</td>
                <td>" . htmlspecialchars($upgrade['duration']) . "</td>
                <td>" . htmlspecialchars($upgrade['effect']) . "</td>
                <td class='action'><button class='button' onclick='upgradeProperty(" . htmlspecialchars($upgrade['upgrade_id']) . ")'>Upgrade</button></td>
              </tr>";
    }
    echo "</table>";
}

echo "</div>"; // Close the upgrade container

// Include JavaScript for handling the upgrade button click
echo "<script>
function upgradeProperty(upgradeId) {
    // Example function for handling upgrade click
    alert('Upgrade with ID: ' + upgradeId + ' clicked');
}
</script>";

include "footer.php"; // Include the footer file, adjust the path as necessary
?>