<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
// Include dbcon.php if not already included

  include 'dbcon.php';


// Include database/pdo_class.php if not already included

  include 'database/pdo_class.php';

  include 'classes.php';


$string = "1234567890";
$length = 4;
$rand = substr(str_shuffle($string), 0, $length);
$_SESSION['cap'] = $rand;
$metatitle = 'Text-Based Mafia Game - Free Online Multiplayer RPG';
$metadesc = 'TheMafiaLife (TML) is one of the most popular original text-based mafia games today. Fight in the bloodbath, shoot out in live gang wars with your crime family, or gamble your way to the top. Don a thompson or a sledgehammer and play your way to become the most powerful godfather in TheMafiaLife, the best textbased game on the net!';

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
  background-image: url('/mlordsimages/blood.jpg');
  background-size: cover; /* This property ensures that the background image covers the entire body */
  background-repeat: no-repeat; /* This property prevents the background image from repeating */
  color: #ffffff;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

   .container {
  max-width: 960px;
  padding: 40px;
  background-color: rgb(45, 26, 26, 0.7); /* Adjust the alpha value (0.7 in this example) to control the opacity */
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
  background-color: rgb(45, 26, 26, 0.7); /* Adjust the alpha value (0.7 in this example) to control the opacity */
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
    
.glowing-link img {
    vertical-align: middle;
    animation: glow 2s infinite alternate;
    border-radius: 20px; /* Increased border radius */
    margin-left: 10px; /* Add spacing between the description and the button */
}

.game-description {
    padding: 20px; /* Adjusted padding to match the size of the glowing register image */
    background-color: rgba(51, 26, 26, 0.7); /* Use the same color and opacity as the container */
    border-radius: 20px; /* Increased border radius */
    margin-right: 10px; /* Add spacing between the description and the button */
}

.game-description h4 {
    margin-top: 0;
    color: #ffffff; /* Set text color to white */
}

@keyframes glow {
    from {
        box-shadow: 0 0 10px 0 rgb(85 46 46 / 70%); /* Adjusted box-shadow color and blur */
    }
    to {
        box-shadow: 0 0 20px 10px rgb(85 46 46 / 70%); /* Adjusted box-shadow color and blur */
    }
}





    
  </style>
</head>
<body>
  <!-- Navigation Bar -->
  <!-- ... [navbar code] -->

<center><img src="/mlordsimages/mafialordsmain.png"></center>

  <div class="content-container">
  <div class="left_side" style="">
							<h2>REGISTER HERE</h2>
							<div class="divider"></div>
							<span id="errors"></span>
							<form id="regForm" onSubmit="return validate();" method="post" action="regsub.php">
  <div class="flexCont">
    <div class="formflex">
      <div class="flexBox flexBox-title left">Username:</div>
      <div class="flexBox right"><input class="form-control" type="text" id="username" onBlur="checkUsername(1);" name="username" /></div>
    </div>
    <div class="formflex">
      <div class="flexBox flexBox-title left">Email:</div>
      <div class="flexBox right"><input class="form-control" type="text" id="email" onBlur="checkEmail();" name="email" /></div>
    </div>
    <div class="formflex">
      <div class="flexBox flexBox-title left">Password:</div>
      <div class="flexBox right"><input class="form-control" type="password" id="pass" onBlur="checkPassword();" name="pass" /></div>
    </div>
    <div class="formflex">
      <div class="flexBox flexBox-title left">Confirm Password:</div>
      <div class="flexBox right"><input class="form-control" type="password" id="conpass" onBlur="checkConfPassword();" name="conpass" /></div>
    </div>
    <div class="formflex">
      <div class="flexBox flexBox-title left">Gender:</div>
      <div class="flexBox right text-center">
        <input type="radio" id="genderMale" name="gender" value="Male">
        <label for="genderMale">Male</label>
        <input type="radio" id="genderFemale" name="gender" value="Female">
        <label for="genderFemale">Female</label>
      </div>
    </div>
    <div class="flexBox right formflex text-center"><img src='cap.php' /></div>
    <div class="formflex">
      <div class="flexBox flexBox-title left">Captcha:</div>
      <div class="flexBox right"><input class="form-control" type="text" id="cap" name="cap" /></div>
    </div>
    <div class="flexBox left"></div>
    <input type="hidden" name="referer" value="<?php echo isset($_GET['referer']) ? $_GET['referer'] : 0; ?>" />
    <div class="flexSub formflex"><button class="btn btn-lg btn-primary btn-block" type="submit" value="Register">Register</button></div>
    <div class="formflex" style="margin-bottom:1.5rem;"><span>Existing Player ?<a id="logsign" style="color:red" href="login.php">Login Here!</a></span></div>
  </div>
</form>
    </div> <!-- End of register-container -->

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
  </body>
</html>