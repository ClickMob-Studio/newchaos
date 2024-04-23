<?php

include 'header.php';

macroTokenCheck($user_class);

$code = rand(1000, 99999);


?>

<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-6">
        <center>
            <p>Please enter the code below.</p>
            <img src="captcha_image.php?code=<?php echo $code ?>" width="100%" /><br />
            <input type="text" class="form-control" />
            <input type="submit" value="Submit" />
        </center>
    </div>
    <div class="col-md-3"></div>
</div>

<?php
include 'footer.php';
?>


