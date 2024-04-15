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
                $('.passwordAlert').html(response.text).show();
            },
            error: function() {
                alert("An error occurred. Please try again.");
            }
        });
    });
});
$(document).ready(function() {
$.ajax({
            url: '/ajax_settings.php', 
            type: 'POST',
            dataType: 'json',
            data: {
                action : 'username',
                username: username,
            },
            success: function(response) {
                $('.usernameAlert').html(response.text).show();
            },
            error: function() {
                alert("An error occurred. Please try again.");
            }
        });
});
</script>
<div class="container">
    <div class="row">
        <div class="col-md-4 col-6">
            <h1>Change Password</h1>
            <div class="alert alert-success passwordAlert" style="display: none";></div>
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
            <div class="alert alert-success usernameAlert" style="display: none";></div>
            <form id="usernameForm">
                <div>
                    <label for="username">Old Password</label>
                    <input type="text" id="username" value="<?= $user_class->username; ?>" required>
                </div>
                <button type="submit">Update Username</button>
            </form>
        </div>
    </div>
</div>
