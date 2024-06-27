<?php
include 'header.php';

if ($user_class->gang == 0) {
    diefun('Your not in a gang');
}
?>
<div class='box_top'>Protection Rackets</div>
<div class='box_middle'>
    <div class='pad'>
    </div>
</div>

<br /><hr />

<?php
include("gangheaders.php");
include 'footer.php';

