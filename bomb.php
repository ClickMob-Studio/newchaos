<?php
include 'header.php';

if ($user_class->id != 150)
    header('location: index.php');

echo'<h3>Bomb The City</h3>';
echo'<hr>';

echo '<div class="floaty">
<p></p>
</div>';

include 'footer.php';
?>