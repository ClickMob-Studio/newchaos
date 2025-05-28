<?php
include("header.php");
?>
<html>

<head>
</head>
<table align="center" width=100%>
  <tr>
    <td class="contenthead"><b>Crack the Safe</b></td>
  </tr>
  <?php
  $dbres = mysql_query("SELECT * FROM `[kraakkluis]` WHERE `area`='2' ");
  $gok = mysql_fetch_object($dbres);
  if (isset($_POST['submit']) && preg_match('/^[0-9]+$/', $_POST['code'])) {
    $dbres = mysql_query("SELECT * FROM `[kraaknr]` WHERE `login`='$user_class->username' AND `area`='2' AND FLOOR(UNIX_TIMESTAMP(`time`)/(60*60*24))=FLOOR(UNIX_TIMESTAMP(NOW())/(60*60*24))");
    $num = mysql_num_rows($dbres);
    $code = $_POST['code'];
    $code2 = $gok->code;
    if ($user_class->points < 5) {
      print "<tr><td class=\"contentcontent\">You dont have 5 points.";
    } else if ($user_class->crack == 5) {
      print "<tr><td class=\"contentcontent\">You finished your 5 tries for the day.";
    } else if ($code >= 150) {
      print "<tr><td class=\"contentcontent\">The code you entered is to high.";
    } else if ($code <= -1) {
      print "<tr><td class=\"contentcontent\">The code you entered is to low";
    } else if ($code == $code2) {
      $data->belcredit += $gok->kluis;
      $code = rand(0, 149);

      perform_query("UPDATE `grpgusers` SET `money` = `money` + 7000000 WHERE `id` = ?", [$_SESSION['id']]);
      perform_query("UPDATE `[kraakkluis]` SET `kluis` = '5000', `code` = ?, `winnaar` = ? WHERE `area` = '2'", [$code, $user_class->username]);
      perform_query("DELETE FROM `[kraaknr]` WHERE `area` = '2'");

      print "<tr><td class=\"contentcontent\">You cracked the code!";
    } else {
      $data->belcredit - 10;
      perform_query("UPDATE `grpgusers` SET `points` = `points` - 5, `crack` = `crack` + 1 WHERE `id` = ?", [$_SESSION['id']]);
      perform_query("UPDATE `[kraakkluis]` SET `kluis` = `kluis` + 10 WHERE `area` = '2'");
      perform_query("INSERT INTO `[kraaknr]` (time, login, getal, area) VALUES (NOW(), ?, ?, '2')", [$user_class->username, $code]);
      print "<tr><td class=\"contentcontent\">You got the wrong code!";
    }
  }
  $dbres = mysql_query("SELECT * FROM `[kraaknr]` WHERE `login`='$user_class->username}'  AND FLOOR(UNIX_TIMESTAMP(`time`)/(60*60*24))=FLOOR(UNIX_TIMESTAMP(NOW())/(60*60*24))");
  $num = mysql_num_rows($dbres);
  $time2 = time() + 105400;
  $time = date("Y-m-d", "$time2");
  print <<<ENDHTML
 <tr><td class="contentcontent">
The object of 'Crack the Safe'... Well the name explains it all.<br>
You have to guess a number between 0-150 and try to see if it cracks the safe.<br>
It costs 5 points for each guess, but you will receive <b>$7,000,000</b> if you guess correctly.<br>
<br>
You have a maximum of 5 attempts each day!
<br>
The last winner was : <font color=red>$gok->winnaar.</font><br>
<br>
<form method="POST">
<center>Code (0-150): <input type="text" name="code" size=3 maxlength=3 >
				<input type="button" onClick="document.forms[0].elements[0].value = Math.round(Math.random()*148+1);" value="Random number">
<br><input type="submit" name="submit" value="Crack" style="width: 75px;"></center></form>
  </td></tr>
</table>
ENDHTML;
  $dbres = mysql_query("SELECT * FROM `[kraaknr]` WHERE `area`='2' ORDER BY `time` DESC LIMIT 0,25");
  while ($info = mysql_fetch_object($dbres)) {

    print <<<ENDHTML
ENDHTML;
  }
  ?>
  </body>

</html>

</html>