<?php

include 'header.php';
$im = new imageupload($user_class->id);

$getsignature = $mysql->query("SELECT signature FROM `grpgusers_extra` WHERE `userid` = '".$user_class->id."' LIMIT 1");
$workedsignature = mysql_fetch_array($getsignature);
$user_class->signature = $workedsignature['signature'];

if ($user_class->cindays > 0) {
    if ($_FILES['file']['tmp_name']) {
        $imgSize = getimagesize($_FILES['file']['tmp_name']);

        if ($imgSize[0] > 100 || $imgSize[1] > 16) {
            echo Message("Dimensions cannot exceed 100px x 16px!");
        } else {
            if (eregi('jpg',$_FILES['file']['name'])) {
                if (file_exists('avatars/cin/'.$user_class->id.'.jpg')) { unlink('avatars/cin/'.$user_class->id.'.jpg'); }
                    elseif (file_exists('avatars/cin/'.$user_class->id.'.gif')) { unlink('avatars/cin/'.$user_class->id.'.gif'); }
                        elseif (file_exists('avatars/cin/'.$user_class->id.'.png')) { unlink('avatars/cin/'.$user_class->id.'.png'); }
                move_uploaded_file($_FILES['file']['tmp_name'], './avatars/cin/'.$user_class->id.'.jpg');
            }
            else if (eregi('gif',$_FILES['file']['name'])) {
                if (file_exists('avatars/cin/'.$user_class->id.'.jpg')) { unlink('avatars/cin/'.$user_class->id.'.jpg'); }
                    elseif (file_exists('avatars/cin/'.$user_class->id.'.gif')) { unlink('avatars/cin/'.$user_class->id.'.gif'); }
                        elseif (file_exists('avatars/cin/'.$user_class->id.'.png')) { unlink('avatars/cin/'.$user_class->id.'.png'); }
                move_uploaded_file($_FILES['file']['tmp_name'], './avatars/cin/'.$user_class->id.'.gif');
            }
            else if (eregi('png',$_FILES['file']['name'])) {
                if (file_exists('avatars/cin/'.$user_class->id.'.jpg')) { unlink('avatars/cin/'.$user_class->id.'.jpg'); }
                    elseif (file_exists('avatars/cin/'.$user_class->id.'.gif')) { unlink('avatars/cin/'.$user_class->id.'.gif'); }
                        elseif (file_exists('avatars/cin/'.$user_class->id.'.png')) { unlink('avatars/cin/'.$user_class->id.'.png'); }
                move_uploaded_file($_FILES['file']['tmp_name'], './avatars/cin/'.$user_class->id.'.png');
            }

            $mysql->query("UPDATE grpgusers SET cinuseruploaded=1,cinapproved=0 WHERE id=".$user_class->id." LIMIT 1");
            Send_Event(10000, makeuserfromid($user_class->id) . " uploaded an Image Name to be Approved!");
        }
    }
}

if (($_GET['resendValidation'] == "1") && ($user_class->active != 'yes')) {
    $vermessage = "Hi ".$user_class->gamename.",\n\nWelcome to WorldOfMobsters.com\n\nPlease click the link below to activate your account\n\n https://www.mafiagangstas.com/verify.php?code=" . $user_class->active . "\n\nOr simply go to https://www.mafiagangstas.com/verify.php and enter the code: ".$user_class->active." \n\n For some tips to get you started check out the tutorial: https://www.mafiagangstas.com/tutorial_new.php\n\n Don't give your password to any other person. The Admins of StreetGangstas will NEVER ask you for your password!\n\n Thank you for playing StreetGangstas and Have Fun in the game! \n\n\nRegards,\nStreetGangstas Staff\nhttps://www.mafiagangstas.com";
    $title = "WorldOfMobsters.com account activation";
    mail($user_class->email,$title,$vermessage,"From: StreetGangstas <webmaster@mafiagangstas.com>\r\n");
    echo Message("Activation e-mail sent!");
} elseif ($_GET['resendValidation'] == "1") {
    echo Message("It seems your account is already activated.");
}

if ($_GET['change'] == "yes"){
	echo Message("Account updated!");
}

if (isset($_POST['delete_avatar'])) {
    if (file_exists('avatars/'.$user_class->id.'.jpg')) { unlink('avatars/'.$user_class->id.'.jpg'); }
       elseif (file_exists('avatars/'.$user_class->id.'.gif')) { unlink('avatars/'.$user_class->id.'.gif'); }
              elseif (file_exists('avatars/'.$user_class->id.'.png')) { unlink('avatars/'.$user_class->id.'.png'); }
}
if (isset($_POST['update'])) {
    if ($_POST["gender"] == "1") { $gender = "Male"; }
       elseif ($_POST["gender"] == "2") { $gender = "Female"; }
              else { $gender = "Male"; }



if ($_POST["tutorialtoggle"] == "1") { $tutorialtoggle = "On"; }
       elseif ($_POST["tutorialtoggle"] == "2") { $tutorialtoggle = "Off"; }
              else { $tutorialtoggle = "On"; }





    if (($user_class->familymember == 0) && ($_POST['email'] == "")) {
       $message .= "<div>The e-mail address you entered was invalid.</div>";
    }
    if (($user_class->familymember == 0) && ($_POST['email'] != "") && (!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $_POST['email']))) {
       $message .= "<div>The e-mail address you entered was invalid.</div>";
    }
    if (($user_class->familymember == 0) && ($_POST['email'] != "")) {
        list($e_username, $e_maildomain) = split("@", $_POST['email']);
        if (checkdnsrr($e_maildomain, "MX")) {
        } else {
            $message .= "<div>The e-mail address you entered was invalid.</div>";
        }
    }

    $resultemail = $mysql->query("SELECT id FROM `grpgusers` WHERE `email`='".$_POST['email']."' AND `active`='yes' AND `id`!='".$user_class->id."' AND `familymember` = '0'");
    $workedemail = mysql_fetch_array($resultemail);
    $emailexist = mysql_num_rows($resultemail);
    if ($emailexist != 0) {
        $message .= "<div>The e-mail address you entered is used by another account.</div>";
    }

    if(($_POST['password'] != "") && ((strlen($_POST['password']) < 4 or strlen($_POST['password']) > 15))){
       $message .= "<div>The password you chose has " . strlen($_POST['password']) . " characters. You need to have between 4 and 15 characters.</div>";
    }
    $resultmessage = "";
    if (!isset($message)) {
        if (($user_class->familymember == 0) && ($_POST['email'] != $user_class->email)) {
            $vercode = chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122)).chr(rand(97,122));
            $result = $mysql->query("UPDATE `grpgusers` SET `email`='".$_POST['email']."', `active`='".$vercode."' WHERE `id`='".$user_class->id."'");
            $user_class->email = $_POST['email'];
            $user_class->active = $vercode;
            $vermessage = "Hi ".$user_class->gamename.",\n\n You modified the e-mail address for your account on WorldOfMobsters.com therefor you are asked to verify your email. \n\nPlease click the link below to activate your account\n\n https://www.mafiagangstas.com/verify.php?code=" . $vercode . "\n\nOr simply go to https://www.mafiagangstas.com/verify.php and enter the code: ".$vercode." \n\n Thank you for playing StreetGangstas! \n\n\nRegards,\nStreetGangstas Staff\nhttps://www.mafiagangstas.com";
            $title = "WorldOfMobsters.com e-mail change";
            mail($_POST['email'],$title,$vermessage,"From: World Of Mobsters <webmaster@worldofmobsters.com>\r\n");
            $resultmessage .= "E-mail changed. Activation e-mail sent to the new address.<br>";
        }
       if ($_POST['password'] != "") {
          $result2 = $mysql->query("UPDATE `grpgusers` SET `password`='".mysql_real_escape_string($_POST['password'])."' WHERE `id`='".$user_class->id."'");
          $resultmessage .= "Password changed.<br>";
       }
       if ($_POST['quote'] != $user_class->quote) {
            $result3 = $mysql->query("UPDATE `grpgusers` SET `quote`='".mysql_escape(stripslashes(strip_tags($_POST['quote'])))."' WHERE `id`='".$user_class->id."'");
            $user_class->quote = $_POST['quote'];
            $resultmessage .= "Quote changed.<br>";
       }
       if ($_POST['status'] != $user_class->mobsterstatus) {
            $result3 = $mysql->query("UPDATE `grpgusers` SET `mobsterstatus`='".mysql_escape(stripslashes(strip_tags($_POST['status'])))."' WHERE `id`='".$user_class->id."'");
            $user_class->mobsterstatus = $_POST['status'];
            $resultmessage .= "Status changed.<br>";
       }
       if ($resultmessage != "") {
           echo Message($resultmessage);
       }
       if ($_POST['avatar'] != "") {
            $result4= $mysql->query("UPDATE `grpgusers` SET `avatar`='".mysql_real_escape_string($_POST['avatar'])."' WHERE `id`='".$user_class->id."'");
       } else { $result5= $mysql->query("UPDATE `grpgusers` SET `avatar`='images/noavatar.jpg' WHERE `id`='".$user_class->id."'"); }
       $result6 = $mysql->query("UPDATE `grpgusers` SET `gender`='".$gender."' WHERE `id`='".$user_class->id."'");
       $result8 = $mysql->query("UPDATE `grpgusers` SET `tutorialtoggle`='".$tutorialtoggle."' WHERE `id`='".$user_class->id."'");

       $result7 = $mysql->query("UPDATE `grpgusers_extra` SET `signature`='".mysql_escape(stripslashes($_POST['signature']))."' WHERE `userid`='".$user_class->id."'");
       $user_class->gender = $gender;

       $user_class->signature = $_POST['signature'];
$user_class->tutorialtoggle = $tutorialtoggle;


//       echo Message("Please wait while your account is being updated...");
//       mrefresh("editaccount.php?change=yes", 0);
//       include 'footer.php';
//       exit;
    } elseif (isset($message)) {
       echo Message($message);
    }
}
if (isset($_POST['changecolourn'])) {
    $_POST['colouraccept'] = check_number($_POST['colouraccept']);
    $_POST['paytype'] = check_number($_POST['paytype']);
    $_POST['sel_colour'] = check_number($_POST['sel_colour']);
    if (isset($_POST['colouraccept']) && isset($_POST['colouraccept']) && isset($_POST['sel_colour'])) {
       if ($_POST['colouraccept'] == '1') {
         if ($_POST['paytype'] == '1') {
             if ($user_class->points >= 5000){
                $resultcc = $mysql->query("UPDATE `grpgusers` SET `points`=`points`-5000, `namestyle`='".mysql_real_escape_string($_POST["sel_colour"])."' WHERE `id`='".$user_class->id."' AND `points`>=5000");
                if (mysql_affected_rows() > 0) {
                    $user_class->points -= 5000;
                    echo Message("Name colour changed successfully.");
                }
             }
         } elseif ($_POST['paytype'] == '2') {
             if ($user_class->money >= 45000000){
                $resultcc = $mysql->query("UPDATE `grpgusers` SET `money`=`money`-45000000, `namestyle`='".mysql_real_escape_string($_POST["sel_colour"])."' WHERE `id`='".$user_class->id."' AND `money`>=45000000");
                if (mysql_affected_rows() > 0) {
                    $user_class->money -= 45000000;
                    echo Message("Name colour changed successfully.");
                }
             }
         }
       }
    }
}
if (isset($_POST['changenamen'])) {
    if (isset($_POST['newname'])) {
             $checkname = $mysql->query("SELECT * FROM `grpgusers` WHERE `gamename`='".mysql_real_escape_string($_POST['newname'])."'");
             $name_exist = mysql_num_rows($checkname);
             if ($name_exist > 0) {
                $message .= "<div>I'm sorry but the Name you chose is already taken.</div>";
             }
             if(strlen($_POST['newname']) < 4 or strlen($_POST['newname']) > 15){
                $message .= "<div>The Name you chose has " . strlen($_POST['newname']) . " characters. You need to have between 4 and 15 characters.</div>";
             }
             $preg = '/[^0-9a-zA-Z\.\:\-\_]/';
             if (preg_match($preg, $_POST['newname'])){
                $message .= "<div>I'm sorry but your Name can contain only alpha-numeric characters and only a few special characters. (0-9, a-z, A-Z, '.', ':', '-', '_').</div>";
             }
             if (isset($message)) {
                echo Message($message);
             }
             if (!isset($message)){

                if ($user_class->money >= 1000000){
                    $resultcc = $mysql->query("UPDATE `grpgusers` SET `money`=`money`-1000000, `gamename`='".mysql_real_escape_string($_POST["newname"])."' WHERE `id`='".$user_class->id."' AND `money`>=1000000");
                    if (mysql_affected_rows() > 0) {
                        $salt = substr(md5(mt_rand()), 0, 4);
                        $username = $_POST["newname"];
                        $result_p = $mysql->query("SELECT password FROM `grpgusers` WHERE `id`='".$user_class->id."'");
                        $worked_p = mysql_fetch_array($result_p);
                        $password = $worked_p['password'];

                        $resultlog = $mysql->query("INSERT INTO `name_history` VALUES ('', '".$user_class->id."', '".$user_class->gamename."')");
                        $user_class->money -= 1000000;
                        $user_class->gamename = $_POST["newname"];
                        echo Message("Name changed successfully.");
                    }
                }
             }
    }
}

?>
<div class="contenthead">Edit Account Details</div><!--contenthead-->
<div class="contentcontent">
<form method='post'><table width='100%'>
		<tr>
			<td width='15%'>Name:</td>
			<td width='35%'><?= $user_class->gamename ?></td>
			<td width='15%'>Email:</td>
			<td width='35%'><?php if ($user_class->familymember > 0) { echo "<input type='text' name='email' size='25' value='". $user_class->email ."' readonly='readonly'></td>"; } else { echo "<input type='text' name='email' size='25' value='". $user_class->email ."'></td>"; } ?>
		</tr>

		<tr>
			<td width='15%'>New Password:</td>
			<td colspan='3' width='85%'><input type='password' name='password' size='12'></td>
		</tr>

        <tr>
            <td width='15%'>Status:</td>
            <td colspan='3' width='85%'><input type='text' name='status' value="<?= htmlspecialchars($user_class->mobsterstatus) ?>" maxlength='30' size='35'></td>
        </tr>

		<tr>
			<td width='15%'>Quote:</td>
			<td colspan='3' width='85%'><input type='text' name='quote' value="<?= htmlspecialchars($user_class->quote) ?>" maxlength='200' size='75'></td>
		</tr>
                <tr>
                        <td height='28'>Gender:</td>
                        <td colspan='3' width='85%'><font size='2' face='verdana'>
                        <select name='gender'>
                        <?= $user_class->formattedgender ?>
                        </select>
                        </td>
                </tr>

 <tr>
                        <td height='28'>Tutorial Toggle:</td>
                        <td colspan='3' width='85%'><font size='2' face='verdana'>
                        <select name='tutorialtoggle'>
                        <?= $user_class->formattedtutorialtoggle ?>
                        </select>
                        </td>
                </tr>


                <tr>
                        <td height='28'>Signature: [<a href='https://www.worldofmobsters.com/gameguide.php?id=1' target='_blank'>BBCode</a>]</td>
                        <td colspan='3' width='85%'><font size='2' face='verdana'><textarea name='signature' cols='56' rows='7'><?= $user_class->signature ?></textarea></font></td>
                </tr>
                <tr>
			<td width='100%' colspan='4' align='center'><input type='submit' name='update' value='Update'></td>
		</tr>
</table></form>

	<br><br>
	<table width='100%'>
		<tr>
<?php

    if (file_exists('avatars/'.$user_class->id.'.jpg')) { $avatar = 'avatars/' . $user_class->id . '.jpg'; }
       elseif (file_exists('avatars/'.$user_class->id.'.gif')) { $avatar = 'avatars/' . $user_class->id . '.gif'; }
              elseif (file_exists('avatars/'.$user_class->id.'.png')) { $avatar = 'avatars/' . $user_class->id . '.png'; }
                     else { $avatar = 'avatars/0.jpg'; };
?>
		<form method='post'>
			<td width='120' align='center'><a href='profiles.php?id=<?php echo $user_class->id ?>'><img src='<?php echo $avatar ?>' width='100' height='100' style='border: 1px solid #cccccc'></a><br><input type='submit' name='delete_avatar' value='Delete Avatar'>
			</td>
		</form>
		
			<td align='center'>
                <form method='post' enctype='multipart/form-data'>
				<input type='file' name='__upload' size='30'><br><br>
				<b><u>Supported File Types:</u></b><br>.jpg &nbsp; .gif &nbsp; .png<br><br>
				<b><u>Max File Size:</u></b><br>76800 bytes (75kb)
                <input type='submit' name='edit_avatar' value='Upload'>
                </form>
			</td>
            <!-- <td>
                <td width='100%' colspan='4' align='center'><input type='submit' name='edit_avatar' value='Upload'></td>
            </td> -->
        
		</tr>
		<tr>
			<!-- <td width='100%' colspan='4' align='center'><input type='submit' name='edit_avatar' value='Upload'></td> -->
		</tr>
		
	</table>
</div><!--contentcontent--><div class="contentfoot"></div><!--contentfoot-->

<div class="contenthead">Image Name</div><!--contenthead-->
<div class="contentcontent">
<?php

if ($user_class->cindays > 0) {
            $cin_exists = false;
            if (file_exists('avatars/cin/'.$user_class->id.'.jpg')) { $cinimage = 'avatars/cin/' . $user_class->id . '.jpg'; $cin_exists = true; }
                elseif (file_exists('avatars/cin/'.$user_class->id.'.gif')) { $cinimage = 'avatars/cin/' . $user_class->id . '.gif'; $cin_exists = true; }
                    elseif (file_exists('avatars/cin/'.$user_class->id.'.png')) { $cinimage = 'avatars/cin/' . $user_class->id . '.png'; $cin_exists = true; }
            if ($cin_exists) { $cin_image_full = "<img src='".$cinimage."' alt='".$user_class->gamename."' border='0' />"; }
                else { $cin_image_full = "<b><i>None</i></b>"; }
?>
    <table width='100%'>
        <tr>
            <td width="5"></td>
            <td align="left" valign="top"><?=$cin_image_full?><br><?php if (($user_class->cinapproved == 1) && ($cin_exists)) { echo "(<b>Approved</b>)"; } elseif (($user_class->cinapproved == 0) && ($cin_exists)) { echo "(<i>Pending</i>)"; } ?></td>
            <td width="5"></td>
            <td align="left" valign="top">
                <form enctype="multipart/form-data" method='post'>
                    <b><u>Supported File Types:</u></b> .jpg &nbsp; .gif &nbsp; .png<br />
                    <b><u>Max File Size:</u></b> 16000 bytes (100px x 16px MAX)<br />
                    <input type="hidden" name="MAX_FILE_SIZE" value="16000" />
                    <input type='file' name='file' /><br />
                    <input type='submit' value='Upload' /><br /><br />
                </form>
            </td>
            <td width="5"></td>
        </tr>
    </table>
<?php

} else {
    echo "You must purchase <a href='rmstore.php'>Image Name Days</a> in order to have an Image Name.";
}
?>
</div><!--contentcontent--><div class="contentfoot"></div><!--contentfoot-->

<div class="contenthead">Character Class</div><!--contenthead-->
<div class="contentcontent">
    <table width='100%'>
        <tr>
            <td width="5"></td>
            <td width="70" height="70" align="left" valign="top"><img src="<?php echo "images/class".$user_class->charclassid.".jpg"; ?>" title="<?php echo $user_class->charclass; ?>"/></td>
            <td width="5"></td>
            <td align="left" valign="top">Click here to select your Character Class. If you already have a class it will cost 800 points to change to another class.<br /><br />
                <span><a href="charselect.php">Character Selection</a></span></td>
            <td width="5"></td>
        </tr>
    </table>
</div><!--contentcontent--><div class="contentfoot"></div><!--contentfoot-->

<div class="contenthead">Change Name</div><!--contenthead-->
<div class="contentcontent">
<?php

if ($user_class->rmdays > 0) {
?>
<form method='post'><table width='65%'>
    	       <tr>
    	           <td>Name:</td>
    	           <td><input type='text' name='newname' value="<?= $user_class->gamename ?>" maxlength='20' size='20'></td>
    	       </tr>
    	       <tr>
    	           <td>&nbsp;</td>
    	           <td>(0-9, a-z, A-Z, '.', '-', '_')</td>
    	       </tr>
    	       <tr>
    	           <td>&nbsp;</td>
    	           <td><input type='submit' name='changenamen' value='Change Name'></td>
    	       </tr>
    	       <tr>
    	           <td colspan='2'>*Note: It will cost you $1,000,000 to change name.</td>
    	       </tr>
</form></table>
<?php

} else {
    echo "You must be a <a href='rmstore.php'>Respected Gangsta</a> to be able to change your name.";
}
?>
</div><!--contentcontent--><div class="contentfoot"></div><!--contentfoot-->

<div class="contenthead">Coloured Name</div><!--contenthead-->
<div class="contentcontent">
<?php

if ($user_class->rmdays > 0) {
?>
<form method='post'><table width='100%'>
 	       <tr>
    	           <td>New Colour:</td>
    	           <td>
    	           <select name='sel_colour' class="field_box">
    	           <option value='1' style='color:#FF69B4;font-weight:bold;background-color:#FFF000;'>Hot Pink</option>
    	           <option value='2' style='color:#3399FF;font-weight:bold;background-color:#FFF000;'>Dodger Blue</option>
    	           <option value='3' style='color:#ADD6FF;font-weight:bold;background-color:#FFF000;'>Columbia Blue</option>
    	           <option value='4' style='color:#FF7A00;font-weight:bold;background-color:#FFF000;'>Dark Orange</option>
    	           <option value='5' style='color:#FFFFFF;font-weight:bold;background-color:#FFF000;'>White</option>
    	           <option value='6' style='color:#999999;font-weight:bold;background-color:#FFF000;'>Nobel</option>
    	           <option value='7' style='color:#80FFFF;font-weight:bold;background-color:#FFF000;'>Electric Blue</option>
    	           <option value='8' style='color:#F200F2;font-weight:bold;background-color:#FFF000;'>Magenta</option>
    	           <option value='9' style='color:#FFE4C4;font-weight:bold;background-color:#FFF000;'>Bisque</option>
    	           <option value='10' style='color:#85B200;font-weight:bold;background-color:#FFF000;'>Citrus</option>
    	           <option value='11' style='color:#40FF00;font-weight:bold;background-color:#FFF000;'>Harlequin</option>
    	           <option value='12' style='color:#830EF7;font-weight:bold;background-color:#FFF000;'>Electric Indigo</option>
    	           <option value='13' style='color:#E50DA4;font-weight:bold;background-color:#FFF000;'>Hollywood Cerise</option>
    	           <option value='14' style='color:#685E08;font-weight:bold;background-color:#FFF000;'>Raw Umber</option>
    	           <option value='15' style='color:#4D4DFF;font-weight:bold;background-color:#FFF000;'>Neon Blue</option>
    	           <option value='16' style='color:#FF9999;font-weight:bold;background-color:#FFF000;'>Mona Lisa</option>
    	           <option value='17' style='color:#FFCC99;font-weight:bold;background-color:#FFF000;'>Peach-Orange</option>
    	           <option value='18' style='color:#CCFFCC;font-weight:bold;background-color:#FFF000;'>Honeydew</option>
    	           <option value='19' style='color:#9966FF;font-weight:bold;background-color:#FFF000;'>Light Slate Blue</option>
    	           <option value='20' style='color:#990000;font-weight:bold;background-color:#FFF000;'>Sangria</option>
    	           </select>
    	           </td>
    	       </tr>
    	       <tr>
    	           <td>Payment:</td>
    	           <td>
    	           <select name='paytype' class="field_box">
    	               <option value='2'>$45,000,000</option>
    	               <option value='1'>5,000 Points</option>
    	           </select>
    	           </td>
    	       </tr>
    	       <tr>
    	           <td>&nbsp;</td>
    	           <td>
                        <select name='colouraccept' class="field_box">
                        <option value='1'>Yes, Change Colour</option>
                        <option value='0' selected>Are you sure you want to change colour?</option>
                        </select>
                   </td>
    	       </tr>
    	       <tr>
    	           <td>&nbsp;</td>
    	           <td><input type='submit' name='changecolourn' value='Change Colour'></td>
    	       </tr>
</form></table>
<?php

} else {
    echo "You must be a <a href='rmstore.php'>Respected Gangsta</a> to buy a coloured name.";
}
?>
</div><!--contentcontent--><div class="contentfoot"></div><!--contentfoot-->

<?php

include 'footer.php';
?>