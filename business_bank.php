<?php
include 'header.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$userId = $_SESSION['id'];

// Fetch the ownership_id for the user
$result = mysql_query("SELECT ownership_id FROM OwnedBusinesses WHERE user_id = '$userId' LIMIT 1");

if (!$result || mysql_num_rows($result) == 0) {
    echo "You're not associated with any business.";
    exit;
}

$row = mysql_fetch_assoc($result);
$ownershipId = $row['ownership_id'];

$business_class = new OwnedBusiness($ownershipId);

// Handle deposit
if(isset($_POST['deposit'])) {
    $amount = intval($_POST['amount']);
    $business_class->deposit($amount);
    echo "Deposited successfully!";
}

// Handle withdrawal
if(isset($_POST['withdraw'])) {
    $amount = intval($_POST['amount']);
    $business_class->withdraw($amount);
    echo "Withdrawn successfully!";
}

?>

<h2>Business Bank for <?php echo $business_class->name; ?></h2>
<p>Current Balance: $<?php echo number_format($business_class->vault); ?></p>

<h3>Deposit</h3>
<form method="post" action="">
    <input type="number" name="amount" placeholder="Enter amount to deposit" required>
    <input type="submit" name="deposit" value="Deposit">
</form>

<h3>Withdraw</h3>
<form method="post" action="">
    <input type="number" name="amount" placeholder="Enter amount to withdraw" required>
    <input type="submit" name="withdraw" value="Withdraw">
</form>

<h3>Transaction Log</h3>
<!-- You'll need to implement the logic to display transactions similar to how you display auction items -->

<?php
include 'footer.php';
?>
