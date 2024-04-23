<?php

require ("header.php");

if($user_class->admin < 1){
  exit();
}
if (isset($_GET['claim_king']) && $_GET['claim_king'] === 'claimnow') {
  // Check city doesn't have one already
  $king_query = "SELECT id FROM grpgusers WHERE king = :current_city LIMIT 1";
  $db->query($king_query);
  $db->bind(':current_city', $current_city);
  $king_result = $db->fetch_row();
  if (count($king_result) > 0) {
      // Do nothing - already claimed
  } else {
      if ($user_class->gender === 'Male') {
          $queen_query = "SELECT id FROM grpgusers WHERE queen = :current_city AND id = :user_id LIMIT 1";
          $db->query($queen_query);
          $db->bind(':current_city', $current_city);
          $db->bind(':user_id', $user_class->id);
          $queen_result = $db->fetch_row();
          if (count($queen_result) > 0) {
              echo Message("You are already the queen!");
          } else {
              $update_query = "UPDATE grpgusers SET king = :current_city, queen = 0 WHERE id = :user_id";
              $db->query($update_query);
              $db->bind(':current_city', $current_city);
              $db->bind(':user_id', $user_class->id);
              $db->execute();
              header('Location: city.php');
              exit(); // Always exit after a header redirect
          }
      }
  }
}

// Queen city claim
if (isset($_GET['claim_queen']) && $_GET['claim_queen'] === 'claimnow') {
  // Check city doesn't have one already
  $queen_query = "SELECT id FROM grpgusers WHERE queen = :current_city LIMIT 1";
  $db->query($queen_query);
  $db->bind(':current_city', $current_city);
  $queen_result = $db->fetch_row();
  if (count($queen_result) > 0) {
      // Do nothing - already claimed
  } else {
      if ($user_class->gender === 'Female') {
          $king_query = "SELECT id FROM grpgusers WHERE king = :current_city AND id = :user_id LIMIT 1";
          $db->query($king_query);
          $db->bind(':current_city', $current_city);
          $db->bind(':user_id', $user_class->id);
          $king_result = $db->fetch_row();
          if (count($king_result) > 0) {
              echo Message("You are already the king!");
          } else {
              $update_query = "UPDATE grpgusers SET queen = :current_city, king = 0 WHERE id = :user_id";
              $db->query($update_query);
              $db->bind(':current_city', $current_city);
              $db->bind(':user_id', $user_class->id);
              $db->execute();
              header('Location: city.php');
              exit(); // Always exit after a header redirect
          }
      }
  }
}

$city_query = mysql_query("SELECT owned_points FROM cities WHERE id = '" . mysql_real_escape_string($current_city) . "' LIMIT 1");
                            $city_query = mysql_fetch_assoc($city_query);

// PHP to fetch king's information including avatar
$king_query = mysql_query("SELECT id, username, avatar FROM grpgusers WHERE king = '" . mysql_real_escape_string($current_city) . "' LIMIT 1");
if ($king_query) {
    $king_result = mysql_fetch_assoc($king_query);
} else {
    $king_result = null;
}

// PHP to fetch queen's information including avatar
$queen_query = mysql_query("SELECT id, username, avatar FROM grpgusers WHERE queen = '" . mysql_real_escape_string($current_city) . "' LIMIT 1");
if ($queen_query) {
    $queen_result = mysql_fetch_assoc($queen_query);
} else {
    $queen_result = null;
}
?>
<div class="vip-container" style="display: flex; justify-content: space-around; align-items: flex-start;">
    <!-- King of the City -->
    <div class="vip-package" style="flex: 1; padding: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.5); margin: 5px; text-align:center ">
        <?php if ($king_result): ?>
            <img src="<?php echo htmlspecialchars($king_result['avatar']); ?>" style="width: 100px; height: 100px;" alt="King's Avatar" class="user-avatar">
            <h4>King of <!_-cityname-_!></h4>
            <p><strong><?php echo formatName($king_result['id']); ?></strong></p>
            <a href="/attack.php?attack=<?php echo $king_result['id']; ?>&csrf=<?php echo $csrf;?>&throne=attack" class="challenge-btn" style="text-decoration: underline;">Challenge</a>

        <?php else: ?>
            <img src="images/vacant.png" style="width: 100px; height: 100px;" alt="No King" class="vacant-throne">
            <h4>VACANT</h4>
            <p>King of <!_-cityname-_!></p>
            <a href="city.php?claim_king=claimnow" style="text-decoration: underline;">Claim</a>
            
        <?php endif; ?>
        <br />

        <p style="font-weight: bold; margin-top: 5px;">By being King of this City you will earn <?php echo number_format($city_query['owned_points'], 0) ?> points an hour.</p>
       


</div>
    <!-- Queen of the City -->
    <div class="vip-package" style="flex: 1; padding: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.5); margin: 5px; text-align:center">
        <?php if ($queen_result): ?>
            <img src="<?php echo htmlspecialchars($queen_result['avatar']); ?>" style="width: 100px; height: 100px;" alt="Queen's Avatar" class="user-avatar">
            <h4>Queen of <!_-cityname-_!></h4>
            <p><strong><?php echo formatName($queen_result['id']); ?></strong></p>
            <a href="/attack.php?attack=<?php echo $queen_result['id']; ?>&csrf=<?php echo $csrf;?>&throne=attack"  class="challenge-btn" style="text-decoration: underline;">Challenge</a>
        
            <?php else: ?>
            <img src="images/vacant.png" style="width: 100px; height: 100px;" alt="No Queen" class="vacant-throne">
            <h4>VACANT</h4>
            <p>Queen of <!_-cityname-_!></p>
            <a href="city.php?claim_queen=claimnow" style="text-decoration: underline;">Claim</a>
            <?php endif; ?>
        <br />

        <p style="font-weight: bold; margin-top: 5px">By being Queens of this City you will earn <?php echo number_format($city_query['owned_points'], 0) ?> points an hour.</p>
          </div>
</div>

