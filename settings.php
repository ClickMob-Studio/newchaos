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
</script>
<div class="container">
    <div class="row">
        <div class="col-md-6 col-12">
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
    </div>
</div>
