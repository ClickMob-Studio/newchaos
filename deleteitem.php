<?php

include 'header.php';
exit;

if ($user_class->admin < 1 ) {
  echo Message("You are not authorized to be here.");
  include 'footer.php';
  die();
}

if ($_GET['id'] == ""){

	echo Message("No item picked.");

	include 'footer.php';

	die();

}

if ($_GET['person'] == ""){

	echo Message("No player picked.");

	include 'footer.php';

	die();

}



$howmany = Check_Item($_GET['id'], $_GET['person']);



$result3 = mysql_query("SELECT * FROM `grpgusers` WHERE `id`='".$_GET['person']."'");

$userexist = mysql_num_rows($result3);



$result2 = mysql_query("SELECT * FROM `items` WHERE `id`='".$_GET['id']."'");

$worked = mysql_fetch_array($result2);



if ($_POST['submit'] != ""){ //if they confirm they want to sell it

	$error = ($howmany == 0) ? "That player doesn't have any of those." : $error;

	$error = ($userexist == 0) ? "That player doesn't exist." : $error;



	if (isset($error)){

		echo Message($error);

		include 'footer.php';

		die();

	}

	$result = mysql_query("SELECT * FROM `inventory` WHERE `userid` = '".$_GET['person']."' AND `itemid` = '".$_GET['id']."'");
	$result5 = mysql_query("SELECT * FROM `inventory` WHERE `userid` = '".$_GET['person']."'");

	$itemexist = mysql_num_rows($result5);
	$quantity = $_POST['quantity'];

	if($itemexist != 0){
		$worked = mysql_fetch_array($result);
		$quantity = $worked['quantity'] - $quantity;
		if($quantity > 0){
			$result = mysql_query("UPDATE `inventory` SET `quantity` = '".$quantity."' WHERE `userid` = '".$_GET['person']."' AND `itemid` = '".$_GET['id']."'");

		} else {
			$result = mysql_query("DELETE FROM `inventory` WHERE `userid`='".$_GET['person']."' AND `itemid`='".$_GET['id']."'");
		}
		}

	$result2 = mysql_query("SELECT * FROM `items` WHERE `id`='".$_GET['id']."'");
	$worked = mysql_fetch_array($result2);
	echo Message("You have succesfully deleted a ".$worked['itemname'].".");

	include 'footer.php';

	die();

}

?>

<tr><td class="contentspacer"></td></tr><tr><td class="contenthead">Delete Item</td></tr>

<tr><td class="contentcontent">

<form method='post'>

  <table border='0' cellpadding='0' cellspacing='0'>

	<tr>

      <td colspan='2' height='27'>Are you sure you want to delete the item <?php echo $worked['itemname']; ?> from #<?php echo $_GET['person'] ?>?</td>

    </tr>

	<tr>

      <td width='15%' height='27'>Quantity:&nbsp;</td>

      <td width='65%'>

        <input name='quantity' type='text' size='22' value='1'>

    	</td>

    </tr>

    <tr>

      <td>&nbsp;</td>

      <td>

        <input type='submit' name='submit' value='Delete Item'>

        </td>

    </tr>

  </table>

</form>

</td></tr>

<?php

include 'footer.php';

?>