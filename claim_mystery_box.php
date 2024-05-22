<?php

include 'header.php';

?>

<div class="box_top"></div>
<div class="box_middle">
<?php
macroTokenCheck($user_class);


if ($user_class->box_hunt_count < 2) {
    Give_Item(256, $user_class->id, 1);
    mysql_query("UPDATE grpgusers SET box_hunt_count = box_hunt_count + 1 WHERE id = " . $user_class->id);

    echo "
    <div class='alert alert-info'>
        <p>Your Nerve Vial has been added to your inventory!</p>
    </div>
    ";

} else {
    echo "
    <div class='alert alert-danger'>
        <p>You have claimed all of your Nerve Vial!</p>
    </div>
    ";

}
?>
</div>

<?php
include 'footer.php';
?>