<?php
include 'header.php';
?>

<div class='box_top'>Gang</div>
<div class='box_middle'>
    <div class='pad'>
        <?php
        if ($user_class->gang == 0) {
            echo Message("You aren't in a gang.");
            include 'footer.php';
            die();
        }
        $gang_class = new Gang($user_class->gang);
        if (isset($_POST['notes'])) {
            $notes = strip_tags($_POST['notes']);
            security($_POST['userid'], 'num');
            perform_query("INSERT INTO gangtargetlist (gangid, userid, notes) VALUES (?, ?, ?)", [$user_class->gang, $_POST['userid'], $notes]);
        }
        if (isset($_GET['remove'])) {
            security($_GET['remove'], 'num');
            perform_query("DELETE FROM gangtargetlist WHERE gangid = ? AND id = ?", [$user_class->gang, $_GET['remove']]);
        }

        $csrf = md5(uniqid(rand(), TRUE));
        $_SESSION['csrf'] = $csrf;

        $db->query("SELECT t.*, hospital FROM gangtargetlist t JOIN grpgusers g ON userid = g.id WHERE gangid = ?");
        $db->execute([$user_class->gang]);

        $targetlist = $db->fetch_row();
        if (count($targetlist)) {
            print "
<table style='width:87%;table-layout:fixed;' id='newtables'>
    <tr>
        <th>Target</th>
        <th>Notes</th>
        <th>Links</th>
        <th style='width:5%;'>Hos</th>
    </tr>";
            foreach ($targetlist as $target) {
                print "
    <tr>
        <td>" . formatName($target['userid']) . "</td>
        <td><div class='noedit{$target['id']}' onClick='ganghitlistEdit({$target['id']});'>{$target['notes']}</div>
			<div class='edit{$target['id']}' style='display:none;'><input type='text' id='edit{$target['id']}' value='{$target['notes']}' /><button onClick='ganghitlistEditSubmit({$target['id']});'>Edit</button></div></td>
        <td><a onClick='ganghitlistEdit({$target['id']});'>[edit]</a> - <a class='ajax-link' href='ajax_mug.php?mug={$target['userid']}&token={$user_class->macro_token}'>[Mug]</a> - <a href='attack.php?attack={$target['userid']}&csrf={$csrf}'>[Attack]</a> - <a href='?remove={$target['id']}'>[Remove]</a></td>
        <td>{$target['hospital']} Mins</td>
    </tr>
    ";
            }
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
";
        if ($gang_class->description) {
            echo "
<table id='newtables' style='width:100%;'>
    <tr>
        <th>Gang Private Page</th>
    </tr>
    <tr>
        <td>" . BBCodeParse(strip_tags($gang_class->description)) . "</td>
    </tr>
</table>";
        }
        include("gangheaders.php");
        include 'footer.php';
        ?>