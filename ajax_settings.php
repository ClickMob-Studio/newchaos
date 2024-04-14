<?php
require "ajax_header.php";

if(isset($_POST['action']) && $_POST['action'] == 'password'){
    $oldPassword = $_POST['oldPassword'];
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];

    if($newPassword != $confirmPassword){
        echo json_encode(array(
            'text' => "New passwords do not match!",
        ));
        exit;
    }
    if($oldPassword == $newPassword){
        echo json_encode(array(
            'text' => "Old and new passwords cannot be the same!",
        ));
        exit;
    }
    $password = sha1($oldPassword);
   
    if($user_class->password != $password){
        echo json_encode(array(
            'text' => "Old password is incorrect!",
        ));
        exit;
    }
    $newPassword = sha1($newPassword);
    $db->query("UPDATE grpgusers SET password = ? WHERE id = ?");
    $db->execute(array(
        $newPassword,
        $user_class->id
    ));
    Send_Event($user_class->id, 'You have updated your password');
    echo json_encode(array(
        'text' => "Password updated successfully!",
    ));

}