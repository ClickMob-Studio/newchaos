<?php
require_once 'includes/functions.php';

start_session_guarded();


include_once 'dbcon.php';
include_once 'database/pdo_class.php';
include_once 'classes.php';


$string = "1234567890";
$length = 4;
$rand = substr(str_shuffle($string), 0, $length);
$_SESSION['cap'] = $rand;
$metatitle = 'Chaos City Text-Based Mafia Game - Free Online Multiplayer MMORPG';
$metadesc = 'Chaos City Text-Based Mafia Game - Free Online Multiplayer MMORPG';

?>

!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="lang" content="english">
    <meta name="robots" content="All">
    <title>Chaos City - Free Online Web RPG</title>
    <link href="assets/css/login.css" type="text/css" rel="stylesheet" />
    <link href="assets/css/game.css?v=2" type="text/css" rel="stylesheet" />
    <link rel="icon" type="image/png" href="favicon.ico">

    <script src='js/reg.js' type='text/javascript'></script>
    <script type="text/javascript">
        $(document).ready(function () {
            window.validationStates = {
                usernameValid: false,
                emailValid: false,
                passwordValid: false,
                confirmPasswordValid: false
            };

            // Username sanitization
            var usernameField = $('[name="username"]');
            usernameField.on('keyup', function () {
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
                        success: function (response) {
                            window.validationStates.usernameValid = (response.trim() === 'pass');
                            updateInputVisuals(usernameField, window.validationStates.usernameValid);
                        },
                        error: function () {
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
            window.validateForm = function () {
                // Trigger validations
                checkUsernameUnique();
                // ...include other validation checks...

                // Delay form submission to wait for AJAX validation to complete
                setTimeout(function () {
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
</head>

<body>
    <div id="outer" class="wrap">
        <div id="top_bar" class="wrap">
            <div style="float:left;color:#910503;margin-left:5px;">
                <strong>Users Online: <?php echo $usersOnline; ?></strong> |
                <strong>Users Online in last 24 hours: <?php echo $users24; ?></strong>
            </div>
            <a href="login.php">Homepage</a> <a href="register.php">Create an account</a>
        </div>
        <div id="main" class="row">
            <div id="header" class="row">

            </div>
            <div id="content" class="row">
                <div id="left_side">
                    <h2>Welcome to Chaos City!</h2>
                    Chaos City is a Massively Multiplayer Online Role Playing Game, MMORPG for short.<br /><br />
                    In this game, you play the role of a mobster who is leading a life of crime and deception.
                    Join gangs and team up with your friends to kick ass, or go your own way.
                    The choices are endless.<br /><br />
                    <strong>Contact us at <a style='color:#FFF;text-decoration:none;'
                            href="mailto:support@chaoscityrpg.com">support@chaoscityrpg.com</a></strong><br /><br />

                    <!-- <strong style='color:red;'>Countdown To beta Launch (<small>11/11/2016</small>)</strong><br /> -->
                    <!-- <strong><span id="countdown" style="font-size:25px;"></span></strong><br /><br />-->
                </div>

                <div id="right_side">
                    <div id="error_area">
                        <?php
                        if (isset($_SESSION['failmessage'])) {
                            echo '<div class="warning-msg">
                        <i class="fa fa-warning"></i>
                        ' . $_SESSION['failmessage'] . '
                      </div>';
                            unset($_SESSION['failmessage']);
                        }
                        ?>
                    </div>
                    <div id="login_panel">
                        <h3>:: Create An Account ::</h3>

                        <form id="regForm" onsubmit="event.preventDefault(); if(validate()) this.submit();"
                            method="post" action="regsub.php">


                            <div class="form-group">
                                <label for="username" class="form-label">Username:</label>
                                <input class="login" type="text" id="username" onBlur="checkUsername(1);"
                                    name="username" />

                            </div>

                            <div class="form-group">
                                <label for="email" class="form-label">Email:</label>
                                <input type="email" id="email" name="email" onBlur="checkEmail();" class="login"
                                    required>
                            </div>

                            <div class="form-group">
                                <label for="pass" class="form-label">Password:</label>
                                <input type="password" id="pass" name="pass" onBlur="checkPassword();" class="login"
                                    required>
                            </div>

                            <!-- Confirm Password -->
                            <div class="form-group">
                                <label for="conpass" class="form-label">Confirm Password:</label>
                                <input type="password" id="conpass" name="conpass" onBlur="checkConfPassword();"
                                    class="login" required>
                            </div>

                            <!-- Gender -->
                            <div class="form-group text-center">
                                <label for="gender" class="form-label">Gender:</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" id="genderMale" name="" value="Male"
                                        required>
                                    <label class="form-check-label" for="genderMale">Male</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" id="genderFemale" name="gender"
                                        value="Female" required>
                                    <label class="form-check-label" for="genderFemale">Female</label>
                                </div>
                            </div>

                            <!-- Captcha -->
                            <div class="form-group">
                                <div class="text-center">
                                    <img src='cap.php' alt="Captcha" class="captcha-image mb-3" />
                                </div>
                                <label for="cap" class="form-label">Captcha:</label>
                                <input class="login" type="text" id="cap" name="cap" required>
                            </div>

                            <!-- Hidden Referrer Field -->
                            <input type="hidden" name="referer"
                                value="<?php echo isset($_GET['referer']) ? $_GET['referer'] : ''; ?>" />

                            <!-- Submit Button -->

                            <!-- Submit Button -->
                            <div class="form-group">
                                <button class="log_btn" type="submit">Register</button>
                            </div>
                    </div>
                    </form>


                    <a style="color: #7f4144;" href="register.php">Create An Account</a>
                </div>
            </div>
            <div class="spacer"></div>
        </div>


        <div id="bottom_content" class="row"></div>
    </div>
    <div id="footer" class="row">
        <span>Chaos City</span><br />
        &copy; 2015+ . All Rights Reserved.<br />
        <a href="">Privacy Policy.</a> <a href="">Terms of Services.</a> <a href="">Help Tutorial.</a> <a href="">Staff
        </a>
    </div>
    </div>
</body>

</html>