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
  <style>
    body {
      background-image: url('/mlordsimages/backgroundlogin.jpeg');
      background-size: cover;
      background-repeat: no-repeat;
      color: #ffffff;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      padding: 20px; /* Add padding to body for better mobile layout */
    }

    .container {
      max-width: 600px; /* Adjusted max-width for better mobile layout */
      margin: 0 auto; /* Center align container */
      padding: 20px; /* Add padding for better spacing */
    }

    .logo-container img {
      display: block;
      margin: 0 auto 20px;
      width: 200px;
    }

    h1, h2, h3 {
      text-align: center;
      color: #ffffff;
    }

    .form-control {
      background-color: #5d3d3d;
      border-color: #60666b;
      color: #ffffff;
    }

    .form-control:focus {
      background-color: #5d3d3d;
      border-color: #5d3d3d;
    }

    .btn-primary {
      background-color: #5d3d3d;
      border-color: #ffffff;
      color: #ffffff;
    }

    .btn-primary:hover {
      background-color: #753f3f;
      border-color: #ffffff;
    }

    a {
      color: #ffffff;
    }

    .footer {
      text-align: center;
      margin-top: 20px;
    }

    .game-description {
      padding: 20px;
      background-color: rgba(51, 26, 26, 0.7);
      border-radius: 20px;
      margin-bottom: 20px;
    }

    .game-description h4 {
      margin-top: 0;
      color: #ffffff;
    }

    .login-header {
      text-align: center;
      margin-bottom: 20px; /* Add margin for better spacing */
    }

    .discord-logo {
      height: 30px; /* Adjust height for better scaling on mobile */
      margin-left: 10px;
    }

    @keyframes glow {
      from {
        box-shadow: 0 0 10px 0 rgb(85 46 46 / 70%);
      }
      to {
        box-shadow: 0 0 20px 10px rgb(85 46 46 / 70%);
      }
  .contenthead {
  background-color: rgba(0, 0, 0, 0.7); /* Semi-transparent black background */
  border-radius: 10px; /* Rounded corners */
  padding: 20px;
  margin-bottom: 20px; /* Space between this and other elements */
  color: white; /* Text color */
}

.floaty {
  box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2); /* Shadow effect */
  transition: 0.3s; /* Smooth transition effect for hover */
}

.floaty:hover {
  box-shadow: 0 8px 16px 0 rgba(0, 0, 0, 0.2); /* Shadow effect on hover */
}

#newtables {
  width: 100%; /* Full width of the container */
  border-collapse: collapse; /* Collapse borders */
}

#newtables th, #newtables td {
  text-align: left; /* Align text to the left */
  padding: 8px; /* Padding inside cells */
  border-bottom: 1px solid #ddd; /* Bottom border for each cell */
}

#newtables th {
  background-color: #333; /* Dark background for header */
  color: white; /* Text color for header */
}

#newtables tr:hover {
  background-color: #666; /* Background color on row hover */
}

#newtables td {
  color: #ddd; /* Text color for cells */
}
.styled-table {
  width: 100%;
  border-collapse: collapse;
}

.styled-table th, .styled-table td {
  text-align: left;
  padding: 8px;
  border-bottom: 1px solid #ddd;
}

.styled-table th {
  background-color: #333;
  color: white;
}

.styled-table tr:hover {
  background-color: #666;
}

.styled-table td {
  color: #ddd;
}
h4 {
  margin: 0; /* No margin for the heading */
}
  }


    
  </style>
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

