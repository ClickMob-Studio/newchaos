<?php
include "ajax_header.php";
if (empty($_SESSION['id']))
    die();
$user_class = new User($_SESSION['id']);
$db->query("SELECT uninfo FROM grpgusers WHERE id = ?");
$db->execute(array(
	$user_class->id
));
$uninfo = explode("|", $db->fetch_single());
if (isset($_POST['num'])) {
    $uninfo[0] = $_POST['num'];
    $db->query("UPDATE grpgusers SET uninfo = ? WHERE id = ?");
	$db->execute(array(
		implode("|", $uninfo),
		$user_class->id
	));
    die();
}
if (isset($_POST['glow'])) {
    $uninfo[6] = $_POST['glow'];
    $db->query("UPDATE grpgusers SET uninfo = ? WHERE id = ?");
	$db->execute(array(
		implode("|", $uninfo),
		$user_class->id
	));
    die();
}
switch ($_POST['which']) {
    case 'bold':
        $uninfo[2] = $_POST['var'];
        $db->query("UPDATE grpgusers SET uninfo = ? WHERE id = ?");
		$db->execute(array(
			implode("|", $uninfo),
			$user_class->id
		));
        break;
    case 'italic':
        $uninfo[3] = $_POST['var'];
        $db->query("UPDATE grpgusers SET uninfo = ? WHERE id = ?");
		$db->execute(array(
			implode("|", $uninfo),
			$user_class->id
		));
        break;
    case 'spacing':
        $uninfo[5] = $_POST['var'];
        $db->query("UPDATE grpgusers SET uninfo = ? WHERE id = ?");
		$db->execute(array(
			implode("|", $uninfo),
			$user_class->id
		));
        break;
    case 'outline':
        $sub = explode("~", $uninfo[4]);
        $sub[0] = $_POST['var'];
        $uninfo[4] = implode("~", $sub);
        $db->query("UPDATE grpgusers SET uninfo = ? WHERE id = ?");
		$db->execute(array(
			implode("|", $uninfo),
			$user_class->id
		));
        break;
    case 'color1':
        $sub = explode("~", $uninfo[1]);
        $sub[0] = $_POST['var'];
        $uninfo[1] = implode("~", $sub);
        $db->query("UPDATE grpgusers SET uninfo = ? WHERE id = ?");
		$db->execute(array(
			implode("|", $uninfo),
			$user_class->id
		));
        break;
    case 'color2':
        $sub = explode("~", $uninfo[1]);
        $sub[1] = $_POST['var'];
        $uninfo[1] = implode("~", $sub);
        $db->query("UPDATE grpgusers SET uninfo = ? WHERE id = ?");
		$db->execute(array(
			implode("|", $uninfo),
			$user_class->id
		));
        break;
    case 'color3':
        $sub = explode("~", $uninfo[1]);
        $sub[2] = $_POST['var'];
        $uninfo[1] = implode("~", $sub);
        $db->query("UPDATE grpgusers SET uninfo = ? WHERE id = ?");
		$db->execute(array(
			implode("|", $uninfo),
			$user_class->id
		));
        break;
    case 'glow1':
		$out = explode("~", $uninfo[4]);
		$sub = explode(",", $out[1]);
        $sub[0] = $_POST['var'];
        $uninfo[4] = $out[0] . '~' . implode(",", $sub);
        $db->query("UPDATE grpgusers SET uninfo = ? WHERE id = ?");
		$db->execute(array(
			implode("|", $uninfo),
			$user_class->id
		));
        break;
    case 'glow2':
		$out = explode("~", $uninfo[4]);
		$sub = explode(",", $out[1]);
        $sub[1] = $_POST['var'];
        $uninfo[4] = $out[0] . '~' . implode(",", $sub);
        $db->query("UPDATE grpgusers SET uninfo = ? WHERE id = ?");
		$db->execute(array(
			implode("|", $uninfo),
			$user_class->id
		));
        break;
    case 'glow3':
		$out = explode("~", $uninfo[4]);
		$sub = explode(",", $out[1]);
        $sub[2] = $_POST['var'];
        $uninfo[4] = $out[0] . '~' . implode(",", $sub);
        $db->query("UPDATE grpgusers SET uninfo = ? WHERE id = ?");
		$db->execute(array(
			implode("|", $uninfo),
			$user_class->id
		));
        break;
    case 'line':
        $sub = explode("~", $uninfo[4]);
        $sub[1] = $_POST['var'];
        $uninfo[4] = implode("~", $sub);
        $db->query("UPDATE grpgusers SET uninfo = ? WHERE id = ?");
		$db->execute(array(
			implode("|", $uninfo),
			$user_class->id
		));
        break;
}
print formatName($user_class->id);
?>