<?php
require "ajax_header.php";
$user_class = new User($_SESSION['id']);
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

if(isset($_POST['action']) && $_POST['action'] == 'username'){
    if(empty($_POST['username'])){
        echo json_encode(array(
            'text'=> 'You did not provide a username',
        ));
            exit;
        }
        $username = substr(strip_tags(trim($_POST['username'])), 0, 50);
        if (strlen($username) < 3 || strlen($username) > 12){
            echo json_encode(array(
                'text'=> 'You need to have a username at least 3 characters long and not greater then 12',
            ));
            exit;
        }

        $db->query("UPDATE grpgusers SET username = ? WHERE id =".$user_class->id);
        $db->execute(array($username));
        echo json_encode(array(
            "text"=> "You have updated your username, you will still need to use your login name which is your orginal username",
        ));
    
}
if(isset($_POST['action']) && $_POST['action'] == 'email'){
    if(!isset($_POST['email'])){
        echo json_encode(array(
            'text'=> 'You did not provide an email',
        ));
        exit;
    }
        $email = $_POST['email'];
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(array(
                'text'=> 'You need to provide a valid email',
            ));
            exit;
    }
    $db->query("UPDATE grpgusers SET email = ? WHERE id =".$user_class->id);
    $db->execute(array($email));
    echo json_encode(array(
        "text"=> "You have updated your email",
    ));
}
if(isset($_POST['action']) && $_POST['action'] == 'avatar'){
    if (isset($_POST['avatar'])) {
        $url = $_POST['avatar'];
$imageData = file_get_contents($url);
if ($imageData === false) {
    echo json_encode(array('text' => 'Failed to retrieve image.'));
    exit;
}

$tmpFile = tmpfile();
fwrite($tmpFile, $imageData);
$metaData = stream_get_meta_data($tmpFile);
$filePath = $metaData['uri'];


$mimeType = mime_content_type($filePath);
if (strpos($mimeType, 'image/') !== 0) {
    echo json_encode(array('text' => 'You need to provide a valid image'));
    fclose($tmpFile);
    exit;
} 

fclose($tmpFile); 
    $db->query("UPDATE grpgusers SET avatar = ? WHERE id =".$user_class->id);
    $db->execute(array($file));
    echo json_encode(array(
        "text"=> "You have updated your avatar",
    ));

}else{
    echo json_encode(array(
        'text'=> 'You did not provide an avatar',
    ));
    exit;
}
}

if(isset($_POST['action']) && $_POST['action'] == 'quote'){
    $quote = filter_var($_POST['quote'], FILTER_SANITIZE_STRING);
    $db->query("UPDATE grpgusers SET quote = ? WHERE id = ".$user_class->id);
    $db->execute(array($quote));
    echo json_encode(array(
        "text"=> "You have updated you quote"
    ));

}

if(isset($_POST['action']) && $_POST['action'] == 'sig'){
    $sig = filter_var($_POST['sig'], FILTER_SANITIZE_STRING);
    $db->query("UPDATE grpgusers SET `signature` = ? WHERE id = ".$user_class->id);
    $db->execute(array($sig));
    echo json_encode(array(
        "text"=> "You have updated you signature"
    ));
}
if(isset($_POST["action"]) && $_POST["action"] == "comments"){
    $comment = intval($_POST['comments']);
    if($comment != 1 & $comment !=2){
        echo json_encode(array(
            'text'=> 'You did not select a correct value'
        ));
        exit;
    }
    $db->query("UPDATE grpusers SET profilewall = ? WHERE id = ".$user_class->id);
    $db->execute(array($comment));
    echo json_encode(array(
        "text"=> "You have updated your profile comments"
    ));
}