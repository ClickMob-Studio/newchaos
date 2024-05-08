<div class="p-2 mt-2 position-relative" data-id="pms">
          <a href="/pms.php?view=inbox">
            
          <?php 
              $db->query("SELECT count(viewed) FROM pms WHERE `to` = ? AND viewed = 1");
              $db->execute(array($user_class->id));
              $mailCount = $db->fetch_single();
          if($mailCount > 0) { 
                $style='style="color:#dc3545;"';
            } else { 
                $style= '';
            }?>
            <i class="fa-solid fa-message" <?php echo $style;?>></i>
            <p>PMS</p>
          </a>
        </div>