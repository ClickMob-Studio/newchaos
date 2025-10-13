<?php
include 'nliheader.php';

echo '<title>Contact | TrueMMO</title>';

if ($_POST['submit']) {
  $email = strip_tags($_POST['email']);
  $email = addslashes($email);
  $subject = strip_tags($_POST['subject']);
  $subject = addslashes($subject);
  $sbody = strip_tags($_POST['body']);
  $sbody = nl2br($sbody);
  $sbody = addslashes($sbody);

  if (preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i", $email)) {
    if ($email != "") {
      if ($subject != "") {
        if ($body != "") {

          require_once "Mail.php";
          $from = "noreply@yobcity.com";
          $to = "issy.is.sy@hotmail.com";
          $subject = $subject;
          $body = "Email: " . $email . "\n\nBody:\n" . $sbody;
          $host = "mail.yobcity.com";
          $username = "noreply+yobcity.com";
          $password = "blue123!";
          $headers = array(
            'From' => $from,
            'To' => $to,
            'Subject' => $subject
          );
          $smtp = Mail::factory(
            'smtp',
            array(
              'host' => $host,
              'auth' => true,
              'username' => $username,
              'password' => $password
            )
          );
          $mail = $smtp->send($to, $headers, $body);
          if (PEAR::isError($mail)) {
            echo ("<p>" . $mail->getMessage() . "</p>");
          } else {
            echo Message("Thank you for your concern. Please allow 2 days for your message to be attended to.");
          }
        } else {
          echo Message("You didn't enter anything in the body.");
        }
      } else {
        echo Message("You didn't enter a subject.");
      }
    } else {
      echo Message("You didn't enter an email address.");
    }
  } else {
    echo Message("The email address you entered was invalid.");
    $_POST['email'] = "";
  }
}
?>
<tr>
  <td class="contentspacer"></td>
</tr>
<tr>
  <td class="contenthead">Contact Admin</td>
</tr>
<tr>
  <td class='contentcontent'>
    If you have any questions or wish to discuss anything of importance you can contact the admins of Yob City here.
    Make sure to add in an email that we can contact you at.<br><br>
    <form method='post'>
      <table width="100%">
        <tr>
          <td width="15%"><b>Your Email:</b></td>
          <td width="85%"><input type="text" name="email" size="50" value="<?php echo $_POST['email']; ?>"></td>
        </tr>
        <tr>
          <td width="15%"><b>Subject:</b></td>
          <td width="85%"><input type="text" name="subject" size="50" value="<?php echo $_POST['subject']; ?>"></td>
        </tr>
        <tr>
          <td width="15%"><b>Body:</b></td>
          <td width="85%"><textarea name="body" cols="50" rows="6"><?php echo $_POST['body']; ?></textarea></td>
        </tr>
        <tr>
          <td width="15%"><b>&nbsp;</b></td>
          <td width="85%"><input type="submit" name="submit" value="Send"></td>
        </tr>
      </table>
    </form>
    <br />
  </td>
</tr>
<?php
include 'nlifooter.php';
?>