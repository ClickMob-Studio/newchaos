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
  <!-- ... [head content] -->
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  <style>
    /* ... [existing styles] */

    /* Additional styles for registration page to match login page */
    .register-container {
      background-color: #333; /* Background color */
      margin-top: 100px; /* Space from top */
      padding: 40px; /* Padding around content */
      max-width: 960px; /* Max width */
      border-radius: 8px; /* Rounded corners */
      box-shadow: 0 4px 8px rgba(0,0,0,0.1); /* Soft shadow */
      color: #fff; /* Text color */
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; /* Font family */
    }
    .form-label {
      color: #ffc107; /* Label color */
    }
    .form-control {
      background-color: #484e53; /* Input background */
      border: none; /* Remove border */
      color: #fff; /* Text color */
    }
    .form-control:focus {
      background-color: #484e53;
      border-color: #ffc107; /* Border color when focused */
    }
    .btn-primary {
      background-color: #ffc107; /* Button color */
      border-color: #ffc107;
      color: #212529; /* Text color */
    }
    .btn-primary:hover {
      background-color: #e0a800;
      border-color: #d39e00; /* Button hover effect */
    }
    .form-footer {
      text-align: center;
      margin-top: 20px; /* Space above footer text */
      color: #fff; /* Text color */
    }
    .form-footer a {
      color: #ffc107; /* Link color */
      text-decoration: none; /* Remove underline */
    }
  </style>
</head>
<body>
  <!-- Navigation Bar -->
  <!-- ... [navbar code] -->

  <!-- Logo -->
  <div class="logo-container">
    <!-- ... [logo image code] -->
  </div>

  <!-- Registration Container -->
  <div class="register-container container">
    <h1 class="text-center">Welcome to MafiaLords</h1>
    <h2 class="text-center mb-4">Register an Account</h2>

    <!-- Registration Form -->
    <form id="regForm" method="post" action="regsub.php">
      <!-- Username -->
      <div class="form-group">
        <label for="username" class="form-label">Username:</label>
        <input type="text" id="username" name="username" class="form-control" required autofocus>
      </div>

      <!-- Email -->
      <div class="form-group">
        <label for="email" class="form-label">Email:</label>
        <input type="email" id="email" name="email" class="form-control" required>
      </div>

      <!-- Password -->
      <div class="form-group">
        <label for="pass" class="form-label">Password:</label>
        <input type="password" id="pass" name="pass" class="form-control" required>
      </div>

      <!-- Confirm Password -->
      <div class="form-group">
        <label for="conpass" class="form-label">Confirm Password:</label>
        <input type="password" id="conpass" name="conpass" class="form-control" required>
      </div>
      
            <!-- Gender -->
        <div class="form-group text-center">
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="genderMale" name="gender" value="Male" required>
            <label class="form-check-label" for="genderMale">Male</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" id="genderFemale" name="gender" value="Female" required>
            <label class="form-check-label" for="genderFemale">Female</label>
          </div>
        </div>

        <!-- Captcha -->
        <div class="form-group">
          <div class="text-center">
            <img src='cap.php' alt="Captcha" class="captcha-image mb-3"/>
          </div>
          <label for="cap" class="form-label">Captcha:</label>
          <input class="form-control" type="text" id="cap" name="cap" required>
        </div>

        <!-- Hidden Referrer Field -->
        <input type="hidden" name="referer" value="<?php echo isset($_GET['referer']) ? $_GET['referer'] : ''; ?>" />

        <!-- Submit Button -->
        <div class="form-group">
          <button class="btn btn-lg btn-primary btn-block" type="submit">Register</button>
        </div>

        <!-- Already Registered -->
        <div class="form-footer">
          <span>Already registered? <a href="login.php">Login here!</a></span>
        </div>
      </form>
    </div> <!-- End of register-container -->

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
  </body>
</html>