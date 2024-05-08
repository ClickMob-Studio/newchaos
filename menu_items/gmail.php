<?php
if($user_class->gang > 0 ) { ?>
<div class="p-2 mt-2 position-relative" data-id="gmail">
          <a href="gangmail.php">
          <?php if($user_class->gmail > 0){
            $style='style="color:#dc3545;"';
          }else{
            $style= '';
          }?>
          <i class="fa-solid fa-envelopes-bulk" <?php echo $style;?>></i>
            <p style="text-wrap: nowrap;">Gang Mail</p>
          </a>
        </div>
        <?php } ?>