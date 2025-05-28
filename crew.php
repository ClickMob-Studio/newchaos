<?php
include 'header.php';
if ($user_class->crew == 0) {
    echo Message("You aren't in a crew.");
    include 'footer.php';
    die();
}
$crew_class = new crew($user_class->crew);
if (isset($_POST['notes'])) {
    $notes = strip_tags($_POST['notes']);
    security($_POST['userid'], 'num');
    mysql_query("INSERT INTO crewtargetlist VALUES ('',$user_class->crew,{$_POST['userid']},'{$notes}')");
}
if (isset($_GET['remove'])) {
    security($_GET['remove'], 'num');
    perform_query("DELETE FROM crewtargetlist WHERE crewid = ? AND id = ?", [$user_class->crew, $_GET['remove']]);
}

$csrf = md5(uniqid(rand(), TRUE));
$_SESSION['csrf'] = $csrf;

print "
<table style='width:87%;table-layout:fixed;' id='newtables'>
    <tr>
        <th>Target</th>
        <th>Notes</th>
        <th>Links</th>
        <th style='width:5%;'>Hos</th>
    </tr>";
$targetlist = mysql_query("SELECT t.*,hospital FROM crewtargetlist t JOIN grpgusers g ON userid = c.id WHERE crewid = $user_class->crew");
while ($target = mysql_fetch_array($targetlist)) {
    print "
    <tr>
        <td>" . formatName($target['userid']) . "</td>
        <td><div class='noedit{$target['id']}' onClick='crewhitlistEdit({$target['id']});'>{$target['notes']}</div>
			<div class='edit{$target['id']}' style='display:none;'><input type='text' id='edit{$target['id']}' value='{$target['notes']}' /><button onClick='crewhitlistEditSubmit({$target['id']});'>Edit</button></div></td>
        <td><a onClick='crewhitlistEdit({$target['id']});'>[edit]</a> - <a href='mug.php?mug={$target['userid']}'>[Mug]</a> - <a href='attack.php?attack={$target['userid']}&csrf={$csrf}'>[Attack]</a> - <a href='?remove={$target['id']}'>[Remove]</a></td>
        <td>{$target['hospital']} Mins</td>
    </tr>
    ";
}
print "</table>
<form method='post'>
    <table id='newtables' style='width:40%;'>
        <tr>
            <th>Userid:</th>
            <td><input name='userid' type='text' /></td>
        </tr>
        <tr>
            <th>Notes:</th>
            <td><input name='notes' type='text' /></td>
        </tr>
        <tr>
            <td colspan='2'><input type='submit' value='Add Target' /></td>
        </tr>
    </table>
</form>
<table id='newtables' style='width:100%;'>
    <tr>
        <th>crew Private Page</th>
    </tr>
    <tr>
        <td>" . BBCodeParse(strip_tags($crew_class->description)) . "</td>
    </tr>
</table>";
include("crewheaders.php");
include 'footer.php';
?>