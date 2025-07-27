<?php
include 'header.php';
if (isset($_POST['submit'])) {
	$oldpass = htmlspecialchars($_POST['oldpass'] ?? '', ENT_QUOTES);

	$opassword = sha1($oldpass);
	$opassword2 = fuzzehCrypt($opassword);
	$password = htmlspecialchars($_POST['newpass'] ?? '', ENT_QUOTES);
	$password2 = htmlspecialchars($_POST['newpassagain'] ?? '', ENT_QUOTES);
	if ($opassword != $user_class->password && $opassword2 != $user_class->password)
		$message .= "<div>You entered the wrong old password.</div>";
	if (strlen($password) < 4 or strlen($username) > 20)
		$message .= "<div>The password you chose has " . strlen($password) . " characters. You need to have between 4 and 20 characters.</div>";
	if ($password != $password2)
		$message .= "<div>Your passwords don't match. Please try again.</div>";
	if (!isset($message)) {
		perform_query("UPDATE `grpgusers` SET password = ? WHERE id = ?", [fuzzehCrypt(sha1($password)), $user_class->id]);
		echo Message('Your password has been changed.');
	}
}
if (isset($message))
	echo Message($message);
?>
<tr>
	<td class="contentspacer"></td>
</tr>
<tr>
	<td class='contentcontent'>
		<div class='contenthead'>Change Password </div>
		<form name='login' method='post'>
			<table width='44%' border='0'>
				<tr>
					<td height='28'><b>Old Password:</b></td>
					<td><input type='password' name='oldpass'></td>
				</tr>
				<tr>
					<td height='28'><b>New Password:</b></td>
					<td><input type='password' name='newpass'></td>
				</tr>
				<tr>
					<td height='28'><b>Confirm Password:</b></td>
					<td><input type='password' name='newpassagain'></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td><input type='submit' name='submit' value='Change Password'></td>
				</tr>
			</table>
		</form>
		<?php
		include 'footer.php';
		function fuzzehCrypt($pass)
		{
			return crypt($pass, '$6$rounds=5000$awrgwrnuBUIEF89243t89bNFAEb942$');
		}
		?>