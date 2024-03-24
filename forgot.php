<?php
session_start();
echo '<link rel="stylesheet"  href="css/stylemm.css" />';
echo '<title>Forgot Password | TrueMMO</title>';

if (isset($_POST['submit'])) {

    if (isset($_SESSION['p_reset']) && (time() - $_SESSION['p_reset']) < 600) {
        echo '<tr><th class="contenthead"><center><strong><font size="3">Account Recovery</font></strong></center></th></tr>
        <tr><td class="contentcontent"><br />';
        echo "<center><font color='#ffcc33'>You will need to wait before requesting another reset.</font></center>";
        die();
    }

    $db = new mysqli('localhost', 'aa_user', 'GmUq38&SVccVSpt', 'aa');

    $stmt = $db->prepare("SELECT `id`, `loginame`, `email` FROM `grpgusers` WHERE `email` = ?");
    $stmt->bind_param('s', $_POST['email']);
    $stmt->execute();
    $stmt->bind_result($id, $username, $email);

    if ($stmt->fetch()) {

        $stmt->close();

        $newPassword = bin2hex(openssl_random_pseudo_bytes(5));
        $passwordHash = sha1($newPassword);

        $db->query('UPDATE `grpgusers` SET `password` = "' . $passwordHash . '" WHERE id = "' . $id . '"');

        $emailTo = $email;
        $emailSubject = "Your TrueMMO Account Info";
        $emailBody = "This message has been sent to you because you requested your TrueMMO account info.\n
        If you didn't do that, disregard this e-mail. Use below info to login and change your password once online.
        \nUsername: - " . $username . "\nPassword: - ". $newPassword;

        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "From: TrueMMO <no-reply@truemmo.com>" . "\r\n" .
        "Reply-To: no-reply@truemmo.com" . "\r\n" .
        "X-Mailer: PHP/" . phpversion();

        @mail($emailTo, $emailSubject, $emailBody, $headers);

        $_SESSION['p_reset'] = time();

        echo '<tr><th class="contenthead"><center><strong><font size="3">Account Recovery</font></strong></center></th></tr>
        <tr><td class="contentcontent"><br />';
        echo "<center><font color='#ffcc33'>Please check your email (including spam folder)</font></center>";
    } else {
        echo "<center><br/><br/><h2>Sorry we are unable to locate your details</h2></center>";
        ?>

        <tr><th class="contenthead"><center><strong><font style="font-size:20px;">Account Recovery</font></strong></center></th></tr>
<tr><td class="contentcontent"><br />
<center><font color="#ffcc33">Enter account e-mail to recover password</font></center><br />
<form method='post'>
<center><input type="email" name="email" placeholder='address@domain.com' class='text' /><br />
<input type="submit" name="submit" value="Grab Info" class="button" />
<br />
<font color="#ffcc33">Password will be sent to your e-mail account.</font></center>
</form>
</td></tr>

    <?php
    }

} else {

?>
<tr><th class="contenthead"><center><strong><font size="3">Account Recovery</font></strong></center></th></tr>
<tr><td class="contentcontent"><br />
<center><font color="#ffcc33">Enter account e-mail to recover password</font></center><br />
<form method='post'>
<center><input type="email" name="email" placeholder='address@domain.com' class='text' /><br />
<input type="submit" name="submit" value="Grab Info" class="button" />
<br />
<font color="#ffcc33">Password will be sent to account e-mail.</font></center>
</form>
</td></tr>
<?php
}
?>