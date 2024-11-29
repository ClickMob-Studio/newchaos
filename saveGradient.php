<?php
require_once "ajax_header.php";  // Ensure this includes your database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get user ID from the session
    $user_id = $_SESSION['id'];  // Assuming the user ID is stored in the session

    // Ensure we are getting values from the POST request
    $startColor = isset($_POST['startColor']) ? $_POST['startColor'] : '';
    $endColor = isset($_POST['endColor']) ? $_POST['endColor'] : '';
    $isBold = isset($_POST['bold']) && $_POST['bold'] == 'true' ? 1 : 0;
    $isItalic = isset($_POST['italic']) && $_POST['italic'] == 'true' ? 1 : 0;
    $glow = isset($_POST['glow']) && $_POST['glow'] == 'true' ? 1 : 0;

    // Debugging: Check the values received
    var_dump($startColor, $endColor, $isBold, $isItalic, $glow);  // Debug the received POST data

    // Check if the user already has a record in the user_gradients table
    $db->query("SELECT id FROM user_gradients WHERE user_id = ?");
    $db->execute([$user_id]);
    $existingSettings = $db->fetch_single();

    // Debugging: Check if the user has existing settings
    var_dump($existingSettings);  // Should show the existing settings (if any)

    if ($existingSettings) {
        // Update the existing record
        $db->query("UPDATE user_gradients SET start_color = ?, end_color = ?, is_bold = ?, is_italic = ?, glow = ? WHERE user_id = ?");
        $db->execute([$startColor, $endColor, $isBold, $isItalic, $glow, $user_id]);

        // Check if the update was successful
        if ($db->affected_rows() > 0) {
            echo "Gradient settings updated successfully!";
        } else {
            echo "Error updating gradient settings!";
        }
    } else {
        // Insert new record if no existing settings are found
        $db->query("INSERT INTO user_gradients (user_id, start_color, end_color, is_bold, is_italic, glow) VALUES (?, ?, ?, ?, ?, ?)");
        $db->execute([$user_id, $startColor, $endColor, $isBold, $isItalic, $glow]);

        // Check if the insert was successful
        if ($db->insert_id()) {
            echo "Gradient settings saved successfully!";
        } else {
            echo "Error saving gradient settings!";
        }
    }
}
?>
