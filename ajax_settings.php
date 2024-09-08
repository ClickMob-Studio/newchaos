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
            "text"=> "You have updated your username, you will still need to use your login name which is your original username",
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
    $db->execute(array($_POST['avatar']));
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
    if($comment != 1 && $comment !=0){
        echo json_encode(array(
            'text'=> 'You did not select a correct value'
        ));
        exit;
    }
    $db->query("UPDATE grpgusers SET profilewall = ? WHERE id = ".$user_class->id);
    $db->execute(array($comment));
    echo json_encode(array(
        "text"=> "You have updated your profile comments"
    ));
}

if(isset($_POST["action"]) && $_POST["action"] == "shoutbox"){
    $shoutbox = intval($_POST['shoutbox']);
    if($shoutbox != 1 && $shoutbox !=0){
        echo json_encode(array(
            'text'=> 'You did not select a correct value'
        ));
        exit;
    }
    $db->query("UPDATE grpgusers SET is_ads_disabled = ? WHERE id = ".$user_class->id);
    $db->execute(array($shoutbox));
    echo json_encode(array(
        "text"=> "You have updated your shoutbox settings"
    ));
}
if(isset($_POST["action"]) && $_POST["action"] == "mdisplay"){
    $mdisplay = intval($_POST['mobileDisplay']);
    if($mdisplay != 1 && $mdisplay != 0){
        echo json_encode(array(
            'text'=> 'You did not select a correct value'
        ));
        exit;
    }
    $db->query("UPDATE grpgusers SET is_mobile_disabled = ? WHERE id = ".$user_class->id);
    $db->execute(array($mdisplay));
    echo json_encode(array(
        "text"=> "You have updated your mobile display settings"
    ));
}
if(isset($_POST["action"]) && $_POST["action"] == "cdisplay"){
    $mdisplay = intval($_POST['chatDisplay']);
    if($mdisplay != 1 && $mdisplay != 0){
        echo json_encode(array(
            'text'=> 'You did not select a correct value'
        ));
        exit;
    }
    $db->query("UPDATE grpgusers SET is_chat_disabled = ? WHERE id = ".$user_class->id);
    $db->execute(array($mdisplay));
    echo json_encode(array(
        "text"=> "You have updated your mobile display settings"
    ));
}
if(isset($_POST["action"]) && $_POST["action"] == "privacy"){
    $privacy = intval($_POST['privacy']);
    if($privacy != 1 && $privacy !=0){
        echo json_encode(array(
            'text'=> 'You did not select a correct value'
        ));
        exit;
    }
    $db->query("UPDATE grpgusers SET dprivacy = ? WHERE id = ".$user_class->id);
    $db->execute(array($privacy));
    echo json_encode(array(
        "text"=> "You have updated your privacy status"
    ));
}
if(isset($_POST["action"]) && $_POST["action"] == "refillnerve"){
    if($user_class->nerref == 0){
        if($user_class->points < 250){
            echo json_encode(array(
                'text'=> 'You do not have enough points'
            ));
            exit;
        }
        $db->query("UPDATE grpgusers SET nerref = 2, points = points - 250, nerreftime = unix_timestamp()   WHERE id = ".$user_class->id);
        $db->execute();
        echo json_encode(array(
            "text"=> "You have turned on auto refill of your nerve"
        ));
    }else{
        $db->query("UPDATE grpgusers SET nerref = 0 WHERE id =".$user_class->id);
        $db->execute();
        echo json_encode(array(
            "text"=> "You have turned off auto refill of your nerve"
        ));
    }
}
if(isset($_POST["action"]) && $_POST["action"] == "refillenergy"){
    if($user_class->ngyref == 0){
        if($user_class->points < 250){
            echo json_encode(array(
                'text'=> 'You do not have enough points'
            ));
            exit;
        }
        $db->query("UPDATE grpgusers SET ngyref = 2, points = points - 250, ngyreftime = unix_timestamp() WHERE id = ".$user_class->id);
        $db->execute();
        echo json_encode(array(
            "text"=> "You have turned on auto refill of your energy"
        ));
    }else{
        $db->query("UPDATE grpgusers SET ngyref = 0 WHERE id =".$user_class->id);
        $db->execute();
        echo json_encode(array(
            "text"=> "You have turned off auto refill of your energy"
        ));
    }
}

if(isset($_POST['action']) && $_POST['action'] == 'imagename'){
    if($user_class->pdimgname < 1){
        echo json_encode(array(
            'text'=> 'You do not have the image name token'
            ));
            exit;
    }
    if (isset($_POST['imagename'])) {
        $url = $_POST['imagename'];
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
    $db->query("UPDATE grpgusers SET image_name = ? WHERE id =".$user_class->id);
    $db->execute(array($_POST['imagename']));
    echo json_encode(array(
        "text"=> "You have updated your image name",
    ));

}else{
    echo json_encode(array(
        'text'=> 'You did not provide an image name',
    ));
    exit;
}
}

if(isset($_POST['action']) && $_POST['action'] == 'gradient_name'){
    if ($user_class->gndays <= 0) {
        echo json_encode(array(
            'text'=> 'You do not have any gradient name days',
            ));
            exit;
        }
            if(empty($_POST['startColor']) || empty($_POST['midColor']) || empty($_POST['endColor'])){
        echo json_encode(array(
            'text'=> 'Please provide all colors for the gradient',
        ));
        exit;
    }
    
    $startColor = $_POST['startColor'];
    $midColor = $_POST['midColor'];
    $endColor = $_POST['endColor'];

    $finalGradientName = '#' . $startColor . '~#' . $midColor . '~#' . $endColor;
    $db->query("UPDATE grpgusers SET colours = ? WHERE id = ?");
    $db->execute(array(
        $finalGradientName,
        $user_class->id
    ));
    
    echo json_encode(array(
        "text"=> "Gradient name updated successfully!",
    ));
}
if(isset($_POST["action"]) && $_POST["action"] == "aprotection"){
if($user_class->aprotection <= time()){
    echo json_encode(array(
        'text'=> 'You are currently not under protection',
        ));
        exit;
}
    $db->query("UPDATE grpgusers SET aprotection = ".time()." WHERE id =".$user_class->id);
    $db->execute();
    echo json_encode(array(
        "text"=> "You have gave up your attack protection"
    ));
}
if(isset($_POST["action"]) && $_POST["action"] == "mprotection"){
    if($user_class->mprotection <= time()){
        echo json_encode(array(
            'text'=> 'You are currently not under protection',
            ));
            exit;
    }
        $db->query("UPDATE grpgusers SET mprotection = ".time()." WHERE id =".$user_class->id);
        $db->execute();
        echo json_encode(array(
            "text"=> "You have gave up your mug protection"
        ));
    }