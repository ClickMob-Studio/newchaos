<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if(isset($_SESSION['id'])){
    header("Location:refer.php");
}

if (!file_exists('dbcon.php')) {
  include 'dbcon.php';
}

if (!file_exists('database/pdo_class.php')) {
  include 'database/pdo_class.php';
}

if (!file_exists('classes.php')) {
  include 'classes.php';
}

$metatitle = 'Text-Based Mafia Game - Free Online Multiplayer RPG';
$metadesc = 'Mafialords (TML) is one of the most popular original text-based mafia games today. Fight in the bloodbath, shoot out in live gang wars with your crime family, or gamble your way to the top. Don a thompson or a sledgehammer and play your way to become the most powerful godfather in mafialords, the best textbased game on the net!';
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
      background-color: #212529;
      color: #ffffff;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .container {
      max-width: 960px;
      padding: 40px;
      margin-top: 100px;
      background-color: #333;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .logo-container img {
      display: block;
      margin: 0 auto 20px;
      width: 200px;
    }
    h1, h2, h3 {
      text-align: center;
      color: #ffc107;
    }
    .form-control {
      background-color: #484e53;
      border-color: #60666b;
      color: #fff;
    }
    .form-control:focus {
      background-color: #484e53;
      border-color: #ffc107;
    }
    .btn-primary {
      background-color: #ffc107;
      border-color: #ffc107;
      color: #212529;
    }
    .btn-primary:hover {
      background-color: #e0a800;
      border-color: #d39e00;
    }
    a {
      color: #ffc107;
    }
    .footer {
      text-align: center;
      margin-top: 20px;
    }
    .players-online {
      text-align: center;
      background-color: #333;
      color: #ffc107;
      padding: 10px;
      margin-bottom: 20px;
      border-radius: 5px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    .leaderboards {
      text-align: center;
      color: #ffffff;
      margin-bottom: 20px;
    }
    .leaderboard-category {
      background-color: #333;
      padding: 15px;
      margin-bottom: 20px;
      border-radius: 5px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    .flex-container {
    flex: 1; /* The flex items will grow and shrink equally */
    padding: 15px; /* Spacing inside each leaderboard */
    margin: 0 10px; /* Spacing between leaderboards */
    background-color: #2a2a2a; /* Background color for leaderboard */
    color: #fff; /* Text color */
    border-radius: 5px; /* Rounded corners for the leaderboard */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Shadow for depth */
  }

  .leaderboard h3 {
    margin-bottom: 15px;
    color: #ffc107; /* Using a color to highlight titles */
  }

  .leaderboard-list {
    list-style-type: none; /* Remove list bullets */
    padding-left: 0; /* Align list to the left */
  }

  .leaderboard-list li {
    padding: 5px 0; /* Spacing between list items */
    border-bottom: 1px solid #444; /* Separator for list items */
  }

  /* Responsive adjustments */
  @media (max-width: 768px) {
    .flex-container {
      margin-bottom: 15px; /* Spacing between stacked leaderboards on small screens */
    }
  }
    
    
    
  </style>
</head>
<body>
    
   

<div class="container">
  <div class="logo-container">
    <img src="http://mafialords.com/themafialife.png" alt="Your Logo">
  </div>
  <h4>Welcome to MafiaLords</h>
  <h2>Please Log In</h2>
  <form class="form-signin" method="post" action="login.php">
    <div class="form-group">
      <label for="inputUsername">Username</label>
      <input type="text" id="inputUsername" name="username" class="form-control" placeholder="Username"
      
      </div>

    <div class="form-group">
      <label for="inputPassword">Password</label>
      <input type="password" id="inputPassword" name="password" class="form-control" placeholder="Password" required>
    </div>

    <button class="btn btn-lg btn-primary btn-block" type="submit">Login</button>
    <div class="footer">
      <p>Not yet a member? <a href='register.php'>Register now</a></p>
    </div>
  </form>
</div>

<!-- Players Online -->
<div class="players-online">
  <span class="online-count"><?php echo get_users_online(); ?></span> Players Online
</div>

<!-- Leaderboards Section -->
<div class="container mt-4">
  <h2 class="text-center mb-4">Leaderboards</h2>
  <div class="d-flex flex-row justify-content-center flex-wrap">

    <!-- Last 5 Active Players -->
    <div class="flex-container mb-3">
      <?php
        $db->query("SELECT id, lastactive FROM grpgusers ORDER BY lastactive DESC LIMIT 5");
        $rows = $db->fetch_row();
        echo '<div class="leaderboard">';
        echo '<h3>Active Players</h3>';
        echo '<ul class="leaderboard-list">';
        foreach($rows as $row) {
          echo '<li>' . formatName($row['id']) . ' - ' . howLongAgo($row['lastactive']) . '</li>';
        }
        echo '</ul>';
        echo '</div>';
      ?>
    </div>

    <!-- Top 5 Strongest Players -->
    <div class="flex-container mb-3">
      <?php
        $db->query("SELECT id FROM grpgusers WHERE admin <> 1 AND id <> 103 ORDER BY total DESC LIMIT 5");
        $rows = $db->fetch_row();
        echo '<div class="leaderboard">';
        echo '<h3>Strongest Players</h3>';
        echo '<ul class="leaderboard-list">';
        foreach($rows as $row) {
          echo '<li>' . formatName($row['id']) . '</li>';
        }
        echo '</ul>';
        echo '</div>';
      ?>
    </div>

    <!-- Top 5 Highest Leveled Players -->
    <div class="flex-container mb-3">
      <?php
        $db->query("SELECT id, level FROM grpgusers WHERE admin <> 1 AND id <> 103 ORDER BY level DESC LIMIT 5");
        $rows = $db->fetch_row();
        echo '<div class="leaderboard">';
        echo '<h3>Highest Leveled Players</h3>';
        echo '<ul class="leaderboard-list">';
        foreach($rows as $row) {
          echo '<li>' . formatName($row['id']) . ' - Level ' . $row['level'] . '</li>';
        }
        echo '</ul>';
        echo '</div>';
      ?>
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
