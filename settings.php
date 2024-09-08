<?php
session_start();

include 'header.php';

?>
<script type="text/javascript" data-cfasync="false" src="js/cp/jscolor.js"></script>
<script>
    $(document).ready(function () {
        $("#passwordForm").submit(function (event) {
            event.preventDefault();

            var oldPassword = $("#oldPassword").val();
            var newPassword = $("#newPassword").val();
            var confirmPassword = $("#confirmPassword").val();

            if (newPassword !== confirmPassword) {
                alert("New passwords do not match!");
                return;
            }

            $.ajax({
                url: '/ajax_settings.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'password',
                    oldPassword: oldPassword,
                    newPassword: newPassword,
                    confirmPassword: confirmPassword
                },
                success: function (response) {
                    $('.info-alert').html(response.text).show();
                },
                error: function () {
                    alert("An error occurred. Please try again.");
                }
            });
        });
    });
    $(document).ready(function () {
        $("#usernameForm").submit(function (event) {
            event.preventDefault();
            var username = $("#username").val();
            $.ajax({
                url: '/ajax_settings.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'username',
                    username: username,
                },
                success: function (response) {
                    $('.info-alert').html(response.text).show();
                },
                error: function () {
                    alert("An error occurred. Please try again.");
                }
            });
        });
    });
    $(document).ready(function () {
        $("#emailForm").submit(function (event) {
            event.preventDefault();
            var email = $("#email").val();
            $.ajax({
                url: '/ajax_settings.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'email',
                    email: email,
                },
                success: function (response) {
                    $('.info-alert').html(response.text).show();
                },
                error: function () {
                    alert("An error occurred. Please try again.");
                }
            });
        });
    });
    $(document).ready(function () {
        $("#avatarForm").submit(function (event) {
            event.preventDefault();
            var avatar = $("#avatar").val();
            $.ajax({
                url: '/ajax_settings.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'avatar',
                    avatar: avatar,
                },
                success: function (response) {
                    $('.info-alert').html(response.text).show();
                },
                error: function () {
                    alert("An error occurred. Please try again.");
                }
            });
        });
    });
    $(document).ready(function () {
        $("#imagenameForm").submit(function (event) {
            event.preventDefault();
            var imagename = $("#imagename").val();
            $.ajax({
                url: '/ajax_settings.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'imagename',
                    imagename: imagename,
                },
                success: function (response) {
                    $('.info-alert').html(response.text).show();
                },
                error: function () {
                    alert("An error occurred. Please try again.");
                }
            });
        });
    });
    $(document).ready(function () {
        $("#quoteForm").submit(function (event) {
            event.preventDefault();
            var quote = $("#quote").val();
            $.ajax({
                url: '/ajax_settings.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'quote',
                    quote: quote,
                },
                success: function (response) {
                    $('.info-alert').html(response.text).show();
                },
                error: function () {
                    alert("An error occurred. Please try again.");
                }
            });
        });
    });
    $(document).ready(function () {
        $("#sigForm").submit(function (event) {
            event.preventDefault();
            var sig = $("#sig").val();
            $.ajax({
                url: '/ajax_settings.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'sig',
                    sig: sig,
                },
                success: function (response) {
                    $('.info-alert').html(response.text).show();
                },
                error: function () {
                    alert("An error occurred. Please try again.");
                }
            });
        });
    });
    $(document).ready(function () {
        $("#updateCommentsBtn").click(function () {
            var comments = $("#commentsSelect").val();
            $.ajax({
                url: '/ajax_settings.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'comments',
                    comments: comments
                },
                success: function (response) {
                    $('.info-alert').html(response.text).show();
                },
                error: function () {
                    alert("An error occurred. Please try again.");
                }
            });
        });
    });
    $(document).ready(function () {
        $("#updateShoutboxBtn").click(function () {
            var shoutbox = $("#shoutboxSelect").val();
            $.ajax({
                url: '/ajax_settings.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'shoutbox',
                    shoutbox: shoutbox
                },
                success: function (response) {
                    $('.info-alert').html(response.text).show();
                },
                error: function () {
                    alert("An error occurred. Please try again.");
                }
            });
        });
    });
    $(document).ready(function () {
        $("#updateMobileDisplayBtn").click(function () {
            var mobileDisplay = $("#mobileDisplaySelect").val();
            $.ajax({
                url: '/ajax_settings.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'mdisplay',
                    mobileDisplay: mobileDisplay
                },
                success: function (response) {
                    $('.info-alert').html(response.text).show();
                },
                error: function () {
                    alert("An error occurred. Please try again.");
                }
            });
        });
    });
    $(document).ready(function () {
        $("#updateChatDisplayBtn").click(function () {
            var mobileDisplay = $("#chatDisplaySelect").val();
            $.ajax({
                url: '/ajax_settings.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'cdisplay',
                    chatDisplay: chatDisplay
                },
                success: function (response) {
                    $('.info-alert').html(response.text).show();
                },
                error: function () {
                    alert("An error occurred. Please try again.");
                }
            });
        });
    });
    document.addEventListener('DOMContentLoaded', function () {
        const refillButton = document.querySelector('.nerve-action');
        refillButton.addEventListener('click', function (event) {
            $.ajax({
                url: '/ajax_settings.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'refillnerve',
                },
                success: function (response) {
                    $('.info-alert').html(response.text).show();
                },
                error: function () {
                    alert("An error occurred. Please try again.");
                }
            });
        });
    });
    document.addEventListener('DOMContentLoaded', function () {
        const refillButton = document.querySelector('.energy-action');
        refillButton.addEventListener('click', function (event) {
            $.ajax({
                url: '/ajax_settings.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'refillenergy',
                },
                success: function (response) {
                    $('.info-alert').html(response.text).show();
                },
                error: function () {
                    alert("An error occurred. Please try again.");
                }
            });
        });
    });
    $(document).ready(function () {
        $("#gradientNameForm").submit(function (event) {
            event.preventDefault();
            var startColor = $("input[name='startcolor']").val();
            var midColor = $("input[name='midcolor']").val();
            var endColor = $("input[name='endcolor']").val();
            $.ajax({
                url: '/ajax_settings.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'gradient_name',
                    startColor: startColor,
                    midColor: midColor,
                    endColor: endColor
                },
                success: function (response) {
                    $('.info-alert').html(response.text).show();
                },
                error: function () {
                    alert("An error occurred. Please try again.");
                }
            });
        });
    });
    $(document).ready(function () {
        $("#updatePrivacyBtn").click(function () {
            var privacy = $("#privacySelect").val();
            $.ajax({
                url: '/ajax_settings.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'privacy',
                    privacy: privacy
                },
                success: function (response) {
                    $('.info-alert').html(response.text).show();
                },
                error: function () {
                    alert("An error occurred. Please try again.");
                }
            });
        });
    });
    $(document).ready(function () {
        $("#aprotectionForm").submit(function (event) {
            event.preventDefault();
            $.ajax({
                url: '/ajax_settings.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'aprotection',
                },
                success: function (response) {
                    $('.info-alert').html(response.text).show();
                },
                error: function () {
                    alert("An error occurred. Please try again.");
                }
            });
        });
    });
    $(document).ready(function () {
        $("#mprotectionForm").submit(function (event) {
            event.preventDefault();
            $.ajax({
                url: '/ajax_settings.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'mprotection',
                },
                success: function (response) {
                    $('.info-alert').html(response.text).show();
                },
                error: function () {
                    alert("An error occurred. Please try again.");
                }
            });
        });
    });
</script>
<div class="container">
    <div class="alert alert-success info-alert" style="display: none" ;></div>
    <div class="row">
        <div class="col-md-4 col-6">
            <h1>Change Password</h1>
            <form id="passwordForm">
                <div>
                    <label for="oldPassword">Old Password</label>
                    <input type="password" id="oldPassword" required>
                </div>
                <div class="form-group">
                    <label for="newPassword">New Password</label>
                    <input type="password" id="newPassword" required>
                </div>
                <div class="form-group">
                    <label for="confirmPassword">Confirm New Password</label>
                    <input type="password" id="confirmPassword" required>
                </div>
                <button type="submit">Update Password</button>
            </form>
        </div>
        <div class="col-md-4 col-6">
            <h1>Change Username</h1>
            <form id="usernameForm">
                <div>
                    <label for="username">Username</label>
                    <input type="text" id="username" value="<?= $user_class->username; ?>" required>
                </div>
                <button type="submit">Update Username</button>
            </form>
        </div>
        <div class="col-md-4 col-6">
            <h1>Change Email</h1>
            <form id="emailForm">
                <div>
                    <label for="Email">Email</label>
                    <input type="text" id="email" value="<?= $user_class->email; ?>" required>
                </div>
                <button type="submit">Update Email</button>
            </form>
        </div>
        <div class="col-md-4 col-6">
            <h1>Change Avatar</h1>
            <form id="avatarForm">
                <div>
                    <label for="avatar">Avatar</label>
                    <input type="text" id="avatar" value="<?= $user_class->avatar; ?>" required>
                </div>
                <button type="submit">Update Avatar</button>
            </form>
        </div>
        <div class="col-md-4 col-6">
            <h1>Change Quote</h1>
            <form id="quoteForm">
                <div>
                    <label for="quote">Quote</label>
                    <textarea id="quote" name="quote" required><?php echo $user_class->quote; ?></textarea>
                </div>
                <button type="submit">Update Quote</button>
            </form>
        </div>
        <div class="col-md-4 col-6">
            <h1>Signature</h1>
            <form id="sigForm">
                <div>
                    <label for="sig">Sig</label>
                    <textarea style="width:100%" id="sig" name="sig" required><?php echo $user_class->sig; ?></textarea>
                </div>
                <button type="submit">Update Sig</button>
            </form>
        </div>
        <div class="col-md-4 col-6">
            <h1>Profile Comments</h1>
            <p>Turning to off will allow nobody to comment on your profile wall</p>
            <select id="commentsSelect">
                <option value="1" <?php echo $user_class->profilewall == 1 ? 'selected' : ''; ?>>On</option>
                <option value="0" <?php echo $user_class->profilewall == 0 ? 'selected' : ''; ?>>Off</option>
            </select>
            <button id="updateCommentsBtn" type="button">Update Comments</button>
        </div>
        <div class="col-md-4 col-6">
            <h1>Refills</h1>
            <p>Buy refills until next rollover</p>
            <?php if ($user_class->ngyref == 0): ?>
                <button class="energy-action" id="energyrefill" value='1'>Energy Refill 250 points</button>
            <?php else: ?>
                <button class="energy-action" id="energyrefill" value='0'>Disable Energy Refill</button>
            <?php endif; ?>

            <?php if ($user_class->nerref == 0): ?>
                <button class="nerve-action" id="nerverefill" value='1'>Nerve Refill 250 points</button>
            <?php else: ?>
                <button class="nerve-action" id="nerverefill" value='0'>Disable Nerve Refill</button>
            <?php endif; ?>
        </div>
        <div class="col-md-4 col-6">
            <h1>Privacy</h1>
            <p>Hide Yourself from bloodbath donations</p>
            <select id="privacySelect">
                <option value="1" <?php echo $user_class->dprivacy == 1 ? 'selected' : ''; ?>>On</option>
                <option value="0" <?php echo $user_class->dprivacy == 0 ? 'selected' : ''; ?>>Off</option>
            </select>
            <button id="updatePrivacyBtn" type="button">Update Privacy</button>
        </div>
        <?php if ($user_class->pdimgname > 0): ?>
            <div class="col-md-4 col-6">
                <h1>Image Usernames</h1>
                <p>For best results, use an image sized 95x50px</p>
                <form id="imagenameForm">
                    <div>
                        <label for="imagename">Image</label>
                        <input type="text" id="imagename" value="<?= $user_class->image_name; ?>" required>
                    </div>
                    <button type="submit">Update Name</button>
                </form>
            </div>
        <?php endif; ?>

        <?php
        $colors = explode('~', $user_class->colours);
        if (count($colors) == 2) {
            $st = $colors[0];
            $ed = $colors[1];
        } else {
            $st = $colors[0];
            $mid = $colors[1];
            $ed = $colors[2];
        }
        ?>
        <div class="col-md-4 col-6">
            <h1>Gradient Name</h1>
            <p>You have <?= $user_class->gndays; ?> gradient name days left.</p>
            <form id="gradientNameForm">
                <label>Starting Color</label>
                <input type="text" class="color" value="<?= $st; ?>" name="startcolor">
                <label>Middle Color</label>
                <input type="text" class="color" value="<?= $mid; ?>" name="midcolor">
                <label>Ending Color</label>
                <input type="text" class="color" value="<?= $ed; ?>" name="endcolor">
                <button type="submit">Update Gradient Name</button>
            </form>
        </div>

        <div class="col-md-4 col-6">
            <h1>Disable Shoutbox?</h1>
            <p>Turning to on will remove the shoutbox section in the header</p>
            <select id="shoutboxSelect">
                <option value="1" <?php echo $user_class->is_ads_disabled == 1 ? 'selected' : ''; ?>>On</option>
                <option value="0" <?php echo $user_class->is_ads_disabled == 0 ? 'selected' : ''; ?>>Off</option>
            </select>
            <button id="updateShoutboxBtn" type="button">Update Shoutbox</button>
        </div>

        <div class="col-md-4 col-6">
            <h1>Disable Mobile Display?</h1>
            <p>Turning to on will disable the mobile view</p>
            <select id="mobileDisplaySelect">
                <option value="1" <?php echo $user_class->is_mobile_disabled == 1 ? 'selected' : ''; ?>>On</option>
                <option value="0" <?php echo $user_class->is_mobile_disabled == 0 ? 'selected' : ''; ?>>Off</option>
            </select>
            <button id="updateMobileDisplayBtn" type="button">Update Mobile Display</button>
        </div>

        <div class="col-md-4 col-6">
            <h1>Disable Chat Box?</h1>
            <p>Turning to on will disable the chat box in the right hand side</p>
            <select id="chatDisplaySelect">
                <option value="1" <?php echo $user_class->is_chat_disabled == 1 ? 'selected' : ''; ?>>On</option>
                <option value="0" <?php echo $user_class->is_chat_disabled == 0 ? 'selected' : ''; ?>>Off</option>
            </select>
            <button id="updateChatDisplayBtn" type="button">Update Mobile Display</button>
        </div>
        <?php if ($user_class->aprotection > time()): ?>
            <div class="col-md-4 col-6">
                <h1>Remove Attack Protection</h1>
                <form id="aprotectionForm">
                    <button type="submit">Remove Attack Protection</button>
                </form>
            </div>
        <?php endif; ?>
        <?php if ($user_class->mprotection > time()): ?>
            <div class="col-md-4 col-6">
                <h1>Remove Attack Protection</h1>
                <form id="mprotectionForm">
                    <button type="submit">Remove Mug Protection</button>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <div class="col-md-4 col-6 d-lg-none">
                <h1>Edit mobile layout</h1>
                <p>
                    By clicking this button it will allow you to reorder the menu you see on mobile devices, in doing so it will allow you to press and 
                    hold a item and move it to where you want to within the bar. To leave the edit function when staying on the settings page come back and click the finish editing button,
                    however leaving the settings page will auto save and leave editing mode
                </p>
                <button id="edit-button">Edit</button>
            </div>
        </div>
        


<?php require "footer.php";
