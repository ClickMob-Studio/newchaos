<div class="p-2 mt-2 position-relative" data-id="events">
          <a href="/events.php">
            <?php if($ev > 0) { 
                $style='style="color:#dc3545;"';
            } else { 
                $style= '';
            }?>

            <i class="fa-solid fa-circle-exclamation" <?php echo $style;?>></i>
            <p>Events</p>
          </a>
        </div>