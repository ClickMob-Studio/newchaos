<?php
include 'header.php';
if ($user_class->admin < 1) {
    echo 'You should not be here';
    exit;
}
?>

<h1>Admin Panel</h1>

<ul>
    <li><a href="adamexp.php">Donations</a></li>
    <li><a href="sendlogs.php">Send Logs</a></li>
</ul>

<?php
include 'footer.php';
?>
