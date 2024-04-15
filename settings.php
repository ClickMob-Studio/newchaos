<?php 
require "header.php";
?>
<script>
$(document).ready(function() {
    $("#passwordForm").submit(function(event) {
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
                action : 'password',
                oldPassword: oldPassword,
                newPassword: newPassword,
                confirmPassword: confirmPassword
            },
            success: function(response) {
                $('.info-alert').html(response.text).show();
            },
            error: function() {
                alert("An error occurred. Please try again.");
            }
        });
    });
});
$(document).ready(function() {
    $("#usernameForm").submit(function(event) {
        event.preventDefault(); 
        var username = $("#username").val();
        $.ajax({
            url: '/ajax_settings.php', 
            type: 'POST',
            dataType: 'json',
            data: {
                action : 'username',
                username: username,
            },
            success: function(response) {
                $('.info-alert').html(response.text).show();
            },
            error: function() {
                alert("An error occurred. Please try again.");
            }
        });
    });
});
$(document).ready(function() {
    $("#emailForm").submit(function(event) {
        event.preventDefault(); 
        var email = $("#email").val();
        $.ajax({
            url: '/ajax_settings.php', 
            type: 'POST',
            dataType: 'json',
            data: {
                action : 'email',
                email: email,
            },
            success: function(response) {
                $('.info-alert').html(response.text).show();
            },
            error: function() {
                alert("An error occurred. Please try again.");
            }
        });
    });
});
$(document).ready(function() {
    $("#avatarForm").submit(function(event) {
        event.preventDefault(); 
        var avatar = $("#avatar").val();
        $.ajax({
            url: '/ajax_settings.php', 
            type: 'POST',
            dataType: 'json',
            data: {
                action : 'avatar',
                email: avatar,
            },
            success: function(response) {
                $('.info-alert').html(response.text).show();
            },
            error: function() {
                alert("An error occurred. Please try again.");
            }
        });
    });
});
</script>
<div class="container">
<div class="alert alert-success info-alert" style="display: none";></div>
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
                    <input type="password"id="confirmPassword" required>
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
                <button type="submit">Update Username</button>
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
    </div>
</div>
