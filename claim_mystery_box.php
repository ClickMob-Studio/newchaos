<?php

include 'header.php';

?>

<div class="box_top"></div>
<div class="box_middle">
    <?php
    macroTokenCheck($user_class->id);


    if ($user_class->box_hunt_count < 3) {
        Give_Item(277, $user_class->id, 1);
        perform_query("UPDATE grpgusers SET box_hunt_count = box_hunt_count + 1 WHERE id = ?", [$user_class->id]);

        echo "
    <div class='alert alert-info'>
        <p>Your Mission Pass has been added to your inventory!</p>
    </div>
    ";

    } else {
        echo "
    <div class='alert alert-danger'>
        <p>You have claimed all of your Mission Pass!</p>
    </div>
    ";

    }
    ?>
</div>

<?php
include 'footer.php';
?>