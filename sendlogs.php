<?php
include 'header.php';
if ($user_class->admin < 1) {
    echo 'You should not be here';
    exit;
}
?>

<?php
include 'footer.php';
?>
