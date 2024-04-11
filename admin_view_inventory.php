<?php

require ('header.php');

if($user_class->admin < 1){
    exit();
}

if(isset($_POST['id'])){

    $db->query("SELECT inv.*, it.*, c.name overridename, c.image overrideimage FROM inventory inv JOIN items it ON inv.itemid = it.id LEFT JOIN customitems c ON it.id = c.itemid AND c.userid = inv.userid WHERE inv.userid = ?");
$db->execute(array(
    $user_class->id
));
if($db->num_rows() < 1){
    echo Message("There has been a issue with selecting this shit ");
    exit();
}
$rows = $db->fetch_row();
foreach ($rows AS $row){
    echo $row['itemname']." x ".$row['quantity'];
    echo '<br>';
}
}
?>


<h1>View Users Inventory</h1>

<form action='' method='post'>
<input type='number' name='id''>
<input type='submit' value='Submit'> 
</form>