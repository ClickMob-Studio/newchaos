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
$metatitle = 'Text-Based Mafia Game - Free Online Multiplayer MMORPG';
$metadesc = 'BRAND NEW MMORPG';

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
  
 <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src='js/reg.js' type='text/javascript'></script>
<script type="text/javascript">
$(document).ready(function() {
    window.validationStates = {
        usernameValid: false,
        emailValid: false,
        passwordValid: false,
        confirmPasswordValid: false
    };

    // Username sanitization
    var usernameField = $('[name="username"]');
    usernameField.on('keyup', function() {
        var userName = $(this).val().replace(/\s/g, '');
        $(this).val(userName);
    });

    // Update input visuals based on validation state
    function updateInputVisuals(inputElement, isValid) {
        if (isValid) {
            inputElement.removeClass('fail').addClass('pass');
        } else {
            inputElement.removeClass('pass').addClass('fail');
        }
    }

    // Check username uniqueness
    function checkUsernameUnique() {
        var username = usernameField.val();
        if (username) {
            $.ajax({
                url: 'regCheck.php',
                data: { username: username },
                type: 'GET',
                success: function(response) {
                    window.validationStates.usernameValid = (response.trim() === 'pass');
                    updateInputVisuals(usernameField, window.validationStates.usernameValid);
                },
                error: function() {
                    window.validationStates.usernameValid = false;
                    updateInputVisuals(usernameField, false);
                }
            });
        }
    }


function validate() {
    var errors = [];
    if (!checkUsername(0)) {
        errors.push("Your username has to be between 1 and 20 characters.");
    }
    if (!checkExistingUsername()) {
        errors.push("This username already exists.");
    }
    if (!checkPassword()) {
        errors.push("Your password has to be at least 6 characters.");
    }
    if (!checkConfPassword()) {
        errors.push("Your password and confirm password do not match.");
    }
    if (!checkEmail()) {
        errors.push("Double check your email, it either exists on this game or it is not properly formatted.");
    }
    
    if (errors.length === 0) {
        return true; // No errors, form can be submitted
    } else {
        document.getElementById("errors").innerHTML = errors.join("<br />");
        shakeMe();
        return false; // There are errors, form should not be submitted
    }
}

    // Call checkUsernameUnique when username input loses focus
    usernameField.on('blur', checkUsernameUnique);

    // Extend validateForm to include asynchronous check
    window.validateForm = function() {
        // Trigger validations
        checkUsernameUnique();
        // ...include other validation checks...

        // Delay form submission to wait for AJAX validation to complete
        setTimeout(function() {
            if (Object.values(window.validationStates).every(status => status)) {
                // If all validations pass, submit the form
                $('#regForm')[0].submit();
            } else {
                // If any validations fail, alert the user
                alert("Please correct the errors before submitting.");
            }
        }, 1000); // Adjust the timeout to match your server's response time
        return false; // Prevent form from submitting immediately
    };
});
</script>
  
  <style>
   body {
  background-image: url('/mlordsimages/backgroundlogin.jpeg');
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

.fail {
    border-color: red;
    box-shadow: 0 0 5px red;
}
.pass {
    border-color: green;
    box-shadow: 0 0 5px green;
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

 
   
    </div>
    <center><img src="/mlordsimages/logologin.png"></center>

<div class="container">
  <h2>REGISTER HERE</h2>
<span id="errors"></span>
<form id="regForm" onsubmit="event.preventDefault(); if(validate()) this.submit();" method="post" action="regsub.php">


						   <div class="form-group">
      <label for="username" class="form-label">Username:</label>
       <input class="form-control" type="text" id="username" onBlur="checkUsername(1);" name="username" />
      
      </div>

    <div class="form-group">
      <label for="email" class="form-label">Email:</label>
        <input type="email" id="email" name="email" onBlur="checkEmail();" class="form-control" required>
    </div>
    
    <div class="form-group">
<label for="pass" class="form-label">Password:</label>
        <input type="password" id="pass" name="pass" onBlur="checkPassword();" class="form-control" required>
            </div>

<!-- Confirm Password -->
      <div class="form-group">
        <label for="conpass" class="form-label">Confirm Password:</label>
        <input type="password" id="conpass" name="conpass" onBlur="checkConfPassword();" class="form-control" required>
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

   

</a>

</div>

  </form>
</div>
</script>
</body>
</html>
