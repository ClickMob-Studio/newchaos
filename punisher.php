<?php
include 'header.php';

if ($user_class->id != 1) {
    header('location: index.php');
}

$test = new User(1);
echo "<pre>";
print_r($test);
echo "</pre>";

echo '<div>House Awake: ' . $user_class->houseawake . '</div>';
echo '<div>Gang Awake: ' . $user_class->gangawake . '</div>';
echo '<div>Awake: ' . $user_class->awake . '</div>';
echo '<div>Max Awake: ' . $user_class->maxawake . '</div>';
echo '<div>Direct Awake: ' . $user_class->directawake . '</div>';
echo '<div>Direct Max Awake: ' . $user_class->directmaxawake . '</div>';

?>