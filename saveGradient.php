<?php
require_once "ajax_header.php";  // Include necessary database connection and session start

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get user ID from session
    $user_id = $_SESSION['id'];  // Assuming the user ID is stored in the session
    $user_class = new User($user_id);
    if($user_class->gndays < 1){
        echo 'You do not have any gradient name days';
    }else{
    $startColor = $_POST['startColor'];
    $endColor = $_POST['endColor'];
    $isBold = $_POST['bold'] == 'true' ? 1 : 0;
    $isItalic = $_POST['italic'] == 'true' ? 1 : 0;
    $glow = $_POST['glow'] == 'true' ? 1 : 0;

    // Check if the user already has a record in the user_gradients table
    $db->query("SELECT id FROM user_gradients WHERE user_id = ?");
    $db->execute([$user_id]);
    $existingSettings = $db->fetch_single();

    if ($existingSettings) {
        // Update the existing record
        $db->query("UPDATE user_gradients SET start_color = ?, end_color = ?, is_bold = ?, is_italic = ?, glow = ? WHERE user_id = ?");
        $db->execute([$startColor, $endColor, $isBold, $isItalic, $glow, $user_id]);
        echo "Gradient settings updated successfully!";
    } else {
        // Insert new record if no existing settings are found
        $db->query("INSERT INTO user_gradients (user_id, start_color, end_color, is_bold, is_italic, glow) VALUES (?, ?, ?, ?, ?, ?)");
        $db->execute([$user_id, $startColor, $endColor, $isBold, $isItalic, $glow]);
        echo "Gradient settings saved successfully!";
    }
}
}
?>
