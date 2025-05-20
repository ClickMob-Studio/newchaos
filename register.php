<?php
exit;

require_once 'includes/functions.php';

start_session_guarded();

include_once 'dbcon.php';
include_once 'classes.php';

// Query to get users online in the last hour
$queryOnline = "SELECT id FROM grpgusers WHERE lastactive > UNIX_TIMESTAMP() - 3600 ORDER BY lastactive DESC";
$statementOnline = $db->prepare($queryOnline);
$statementOnline->execute();
$usersOnline = $statementOnline->rowCount();

// Query to get users online in the last 24 hours
$queryOnline24 = "SELECT id FROM grpgusers WHERE lastactive > UNIX_TIMESTAMP() - 86400 ORDER BY lastactive DESC";
$statementOnline24 = $db->prepare($queryOnline24);
$statementOnline24->execute();
$users24 = $statementOnline24->rowCount();

$string = "1234567890";
$length = 4;
$rand = substr(str_shuffle($string), 0, $length);
$_SESSION['cap'] = $rand;
$metatitle = 'Text-Based Mafia Game - Free Online Multiplayer MMORPG';
$metadesc = 'BRAND NEW MMORPG';

?>
<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title>Chaos City - Register</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <script type="text/javascript" src="{$set['jquery_location']}"></script>
    <script type="text/javascript" src="assets/js/register.js"></script>
    <link href="assets/css/register.css" type="text/css" rel="stylesheet" />
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

    <body>
        <div id="outer" class="wrap">
            <div id="top_bar" class="wrap">
                <a href="login.php">Homepage</a>
                <a href="register.php">Create an account</a>
                <a href="tos.php">Terms of Service</a>

            </div>
            <div id="main" class="row">
                <div id="header" class="row">
                    <div id="online"><?php echo $usersOnline; ?> Users Online</div>
                </div>

                <div id="content" class="row">
                    <div id="left_side">
                        <h2>Create an account!</h2>
                        <form action=regsub.php method=post>
                            <tr>
                                <td width='40%'>
                                    <input type="text" id="username" class="register" onBlur="checkUsername(1);"
                                        name="username" placeholder="Username" /> <span id='usernameresult'></span>
                                    <input placeholder='Password' type='password' class='register' id='pw1' name='pass'
                                        onkeyup='CheckPasswords(this.value);PasswordMatch();' />
                                    <span id='passwordresult'></span>
                                    <input placeholder='Confirm Password' class='register' type='password'
                                        name='conpass' id='pw2' onkeyup='PasswordMatch();' />
                                    <span id='cpasswordresult'></span>
                                </td>
                                <input type="email" id="email" name="email" onBlur="checkEmail();" class="register"
                                    placeholder="Email" required><span id='emailresult'></span></td>
                                <select class='register' name='gender' type='dropdown'>
                                    <option value=''>Please Choose Gender</option>
                                    <option value='Male'>Male</option>
                                    <option value='Female'>Female</option>
                                </select>
                                </td>

                                <?php if (isset($_GET['referer'])): ?>
                                    <?php
                                    $_GET['referer'] = abs((int) $_GET['referer']);
                                    ?>
                                    <input type='hidden' name='referer' value='<?php echo $_GET['referer'] ?>' />

                                <?php endif; ?>

                            <tr>
                                <td colspan='3'>
                                    <img src='cap.php' alt="Captcha" class="captcha-image mb-3" /><br>
                                    <input class='register' type='text' name='cap' />
                                </td>
                            </tr>
                            <?php

                            echo "<input type='submit'  class='create' value='Submit' />
	</form>";
                            register_footer();

                            ?>
                    </div>

                    <?php
                    function register_footer()
                    {
                        ?>
                    </div>
                    <div class="spacer"></div>
                </div>
                <div id="bottom_content" class="row"></div>
            </div>
            <div id="footer" class="row">
                <span>CHAOS CITY</span><br />
                COPYRIGHT 2024+ . All Rights Reserved.<br />
            </div>
            </div>
        </body>

    </html>

    </body>

    </html>
    <?php
                    }
                    ?>