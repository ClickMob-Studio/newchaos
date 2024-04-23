<?php

require ("header.php");

if($user_class->admin < 1){
  exit();
}
if (isset($_GET['claim_king']) && $_GET['claim_king'] == 'claimnow') {
  $king_query = "SELECT id FROM grpgusers WHERE king = :current_city LIMIT 1";
  $db->query($king_query);
  $db->bind(':current_city', $user_class->city);
  $king_result = $db->fetch_row();
  if (count($king_result) < 1) {
          $queen_query = "SELECT id FROM grpgusers WHERE queen = :current_city AND id = :user_id LIMIT 1";
          $db->query($queen_query);
          $db->bind(':current_city', $user_class->city);
          $db->bind(':user_id', $user_class->id);
          $queen_result = $db->fetch_row();
          if (count($queen_result) > 0) {
              echo Message("You are already the under boss!");
          } else {
              $update_query = "UPDATE grpgusers SET king = :current_city, queen = 0 WHERE id = :user_id";
              $db->query($update_query);
              $db->bind(':current_city', $user_class->city);
              $db->bind(':user_id', $user_class->id);
              $db->execute();
              header('Location: city.php');
              exit(); 
          }
      
  }
}

if (isset($_GET['claim_queen']) && $_GET['claim_queen'] == 'claimnow') {

  $queen_query = "SELECT id FROM grpgusers WHERE queen = :current_city LIMIT 1";
  $db->query($queen_query);
  $db->bind(':current_city', $current_city);
  $queen_result = $db->fetch_row();
  if (count($queen_result) < 1) {
          $king_query = "SELECT id FROM grpgusers WHERE king = :current_city AND id = :user_id LIMIT 1";
          $db->query($king_query);
          $db->bind(':current_city', $current_city);
          $db->bind(':user_id', $user_class->id);
          $king_result = $db->fetch_row();
          if (count($king_result) > 0) {
              echo Message("You are already the boss!");
          } else {
              $update_query = "UPDATE grpgusers SET queen = :current_city, king = 0 WHERE id = :user_id";
              $db->query($update_query);
              $db->bind(':current_city', $current_city);
              $db->bind(':user_id', $user_class->id);
              $db->execute();
              if ($update_query) {
                echo "Update successful";
            } else {
                echo "Update failed: " . $db->query_error(); // Assuming you have an error() method in your database class
            }
             // header('Location: city.php');
              exit(); 
          }
      
  }
}
$current_city = $user_class->city;
$city_query = mysql_query("SELECT owned_points FROM cities WHERE id = '" . mysql_real_escape_string($current_city) . "' LIMIT 1");
                            $city_query = mysql_fetch_assoc($city_query);

$king_query = mysql_query("SELECT id, username, avatar FROM grpgusers WHERE king = '" . mysql_real_escape_string($current_city) . "' LIMIT 1");
if ($king_query) {
    $king_result = mysql_fetch_assoc($king_query);
} else {
    $king_result = null;
}

$queen_query = mysql_query("SELECT id, username, avatar FROM grpgusers WHERE queen = '" . mysql_real_escape_string($current_city) . "' LIMIT 1");
if ($queen_query) {
    $queen_result = mysql_fetch_assoc($queen_query);
} else {
    $queen_result = null;
}
?>
<div class="vip-container" style="display: flex; justify-content: space-around; align-items: flex-start;">

    <div class="vip-package" style="flex: 1; padding: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.5); margin: 5px; text-align:center ">
        <?php if ($king_result): ?>
            <img src="<?php echo htmlspecialchars($king_result['avatar']); ?>" style="width: 100px; height: 100px;" alt="King's Avatar" class="user-avatar">
            <h4>Boss of <!_-cityname-_!></h4>
            <p><strong><?php echo formatName($king_result['id']); ?></strong></p>
            <a href="/attack.php?attack=<?php echo $king_result['id']; ?>&csrf=<?php echo $csrf;?>&thrones=attack" class="challenge-btn" style="text-decoration: underline;">Challenge</a>

        <?php else: ?>
            <img src="images/vacant.png" style="width: 100px; height: 100px;" alt="No Boss" class="vacant-throne">
            <h4>VACANT</h4>
            <p>Boss of <!_-cityname-_!></p>
            <a href="?claim_king=claimnow" style="text-decoration: underline;">Claim</a>
            
        <?php endif; ?>
        <br />

        <p style="font-weight: bold; margin-top: 5px;">By being the boss of this city you will earn <?php echo number_format($city_query['owned_points'], 0) ?> points an hour.</p>
       


</div>
<?php $owned_points = $city_query['owned_points'];
$twenty_percent =$owned_points - $owned_points * 0.20;
?>
    <div class="vip-package" style="flex: 1; padding: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.5); margin: 5px; text-align:center">
        <?php if ($queen_result): ?>
            <img src="<?php echo htmlspecialchars($queen_result['avatar']); ?>" style="width: 100px; height: 100px;" alt="Under Boss's Avatar" class="user-avatar">
            <h4>Under Boss of <!_-cityname-_!></h4>
            <p><strong><?php echo formatName($queen_result['id']); ?></strong></p>
            <a href="/attack.php?attack=<?php echo $queen_result['id']; ?>&csrf=<?php echo $csrf;?>&thrones=attack"  class="challenge-btn" style="text-decoration: underline;">Challenge</a>
        
            <?php else: ?>
            <img src="images/vacant.png" style="width: 100px; height: 100px;" alt="No under boss" class="vacant-throne">
            <h4>VACANT</h4>
            <p>Under Boss of <!_-cityname-_!></p>
            <a href="?claim_queen=claimnow" style="text-decoration: underline;">Claim</a>
            <?php endif; ?>
        <br />

        <p style="font-weight: bold; margin-top: 5px">By being the under boss of this City you will earn <?php echo number_format($twenty_percent, 0) ?> points an hour.</p>
          </div>
</div>

