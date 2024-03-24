<?php
include 'dbcon.php';
include 'database/pdo_class.php';
include "classes.php";
include "codeparser.php";

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title><?php echo $metatitle; ?></title>
  <?php if (!empty($metadesc)) echo '<meta name="description" content="'.$metadesc.'">'; ?>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="css/style.css" />
  <link rel="stylesheet" type="text/css" href="css/new_login.css" />
  
  <script charset="UTF-8" src="//web.webpushs.com/js/push/947a2f1e5f99b261b9a784c688fe9be3_1.js" async></script>

</head>
<body>
    

<div class="container">
  <div class="login-header">
    <h2>        <center><img src="/mlordsimages/logologin.png"></center>
</h2>
<span style="margin: 0; line-height: 27px; text-transform: uppercase; font-size: 20px; text-align: left; text-indent: 25px;">
<h4><center><font color=green><?php echo get_users_online(); ?></font></span> Players Online</center></h4>
  </div>
  <form class="form-signin" method="post" action="login.php">
    <div class="form-group">
      <label for="inputUsername">Username</label>
      <input type="text" id="inputUsername" name="username" class="form-control" placeholder="Username">
    </div>
    <div class="form-group">
      <label for="inputPassword">Password</label>
      <input type="password" id="inputPassword" name="password" class="form-control" placeholder="Password" required>
    </div>
    <button class="btn btn-lg btn-primary btn-block" type="submit">LOGIN</button>
  </form>
  <div class="footer">
    <div class="game-description">
      <h4>ABOUT MAFIA LORDS       <a href="https://discord.gg/KF79HVpVQD" target="_blank"><img src="mlordsimages/Discordlogo899.png" alt="Discord" class="discord-logo"></a>
</h4>
      <p>Mafia Lords is a Fast growing new text based MMORPG. Dominate the city, Train up and Become the strongest Mafialord? Think you got what it takes?</p>
      <p><font color=yellow>We are currently at the Pre-Registration stage, Register now for an Exclusive Bonus!</font></p>
      
      <a href="register.php" class="glowing-link">
      <img src="mlordsimages/registernow.png" alt="Register">
    </a>
    </div>
    
  </div>
</div>
</body>
</html>
    </td>
  </tr>
</table>

</a>

</div>

  </form>
</div>

<!-- Players Online -->


<!-- Leaderboards Section -->
<div class="contenthead floaty">
  <div style="display: flex; justify-content: center;">
    <!-- Last 5 Active Players -->
    <div class="contenthead floaty">
    <table class="styled-table">
      <?php
      $db->query("SELECT id, lastactive FROM grpgusers ORDER BY lastactive DESC LIMIT 15");
      $rows = $db->fetch_row();
      $i = 1;
      echo '<thead><tr><th colspan="3">Last Active Players</th></tr></thead>';
      echo '<tbody>';
      foreach($rows as $row){
        echo '<tr><td>' . $i++ . '.</td><td>' . formatName($row['id']) . '</td><td>'.howLongAgo($row['lastactive']).'</td></tr>';
      }
      echo '</tbody>';
      ?>
    </table>
    </div>
    
    <!-- Top 5 Highest Leveled Players -->
    <div class="contenthead floaty">
    <table class="styled-table">
      <?php
      $db->query("SELECT id, level FROM grpgusers WHERE admin <> 1 ORDER BY level DESC LIMIT 15");
      $rows = $db->fetch_row();
      $i = 1;
      echo '<thead><tr><th colspan="3">Top 15 Highest Leveled Players</th></tr></thead>';
      echo '<tbody>';
      foreach($rows as $row){
        echo '<tr><td>' . $i++ . '.</td><td>' . formatName($row['id']) . '</td><td>'.$row['level'].'</td></tr>';
      }
      echo '</tbody>';
      ?>
    </table>
    </div>
  </div>
</div>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

<script>
  setInterval(function() {
    $.post("ajax_onlineusers.php", {"page" : "home"}, function(data) {
      $('.online-count').html(data.count + ' Players Online');
      // Update the leaderboards here if needed
    }, 'json');
  }, 5000);
</script>
</body>
</html>

