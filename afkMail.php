<?php
ini_set('display_errors', 1);
error_reporting(E_ERROR);

include 'header.php';
include 'mailer/PHPMailer.php';
include 'mailer/SMTP.php';
//if (intval($user_class->admin) !== 1) {
//    header('location: index.php');
//}
if (isset($_POST['message'])){
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->SMTPAuth   = true;
        $mail->Username   = "Themafialife21@gmail.com";
        $mail->Password   = 'Mickey97';
        $mail->Port = 587;
        $mail->setFrom('Themafialife21@gmail.com', 'The Mafia Life');
        $mail->Subject = $_POST['subject'];
        $mail->Body    = $_POST['message'];
        $mail->isHTML(true);

        $result = mysql_query("SELECT * from `grpgusers` ORDER BY `id` DESC");
        $i = 0;
        while ($row = mysql_fetch_array($result)) {
//            $currentTime = time();
//            $lastActive = $row['lastactive'];
//            $diff = abs($currentTime - $lastActive)/60/60/24 ;
//            if ($diff >= 5){
            $mail->addAddress($row['email']);
            $i++;
            if ($i == 20){
                $mail->SMTPSecure = 'tls';
                $mail->Host = 'smtp.gmail.com';
                $mail->send();
                $i = 0;
            }
//            }
        }



        echo '<strong style="color: #00A000">Message has been sent</strong>';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>
<div style="text-align: center">
    <h3>Send a mail to users that has been inactive more than 5 days</h3>
    <form method="post" action="afkMail.php">
        <input type="text" name="subject"/>
        <textarea name="message" cols="50" rows="10">Enter your message here</textarea>
        <br>
        <br>
        <input type="submit" value="send">
    </form>
</div>