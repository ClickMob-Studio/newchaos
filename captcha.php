<?php

include 'header.php';

$code = rand(1000, 9999);
$_SESSION["code"] = $code;
?>

<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-6">
        <center>
            <p>Please enter the code below.</p>
            <img src="captcha_image.php" width="100%" /><br />
            <input type="text" class="form-control" />
            <input type="submit" value="Submit" />
        </center>
    </div>
    <div class="col-md-3"></div>
</div>

<?php
include 'footer.php';
?>


