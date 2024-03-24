<?php
include 'header.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
mysql_set_charset('utf8');

$business_id = intval($_GET['id']);
$business_class = new OwnedBusiness($business_id);

if (empty($business_class->ownership_id)) {
    echo Message("That business doesn't exist!");
    include 'footer.php';
    die();
}

// If the user clicked the apply button
if (isset($_POST['apply_for_business'])) {
    $user_id = $user_class->id;

    // Check if the user has already applied
    $check_query = "SELECT * FROM business_applications WHERE user_id = '$user_id' AND business_id = '$business_id'";
    $result = mysql_query($check_query);
    if (mysql_num_rows($result) > 0) {
        echo Message("You have already applied to this business.");
    } else {
        $insert_query = "INSERT INTO business_applications (user_id, business_id, applied_at) VALUES ('$user_id', '$business_id', NOW())";
        mysql_query($insert_query);

        if (mysql_error()) {
            echo "MySQL Error: " . mysql_error();
            exit;
        }

        if (mysql_affected_rows() > 0) {
            echo Message("You have successfully applied to the business.");
        } else {
            echo Message("Error applying. Please try again later.");
        }
    }
}

// Calculate the number of filled and empty stars
$filledStars = intval($business_class->rating);
$emptyStars = 5 - $filledStars;
$ratingStars = str_repeat("?", $filledStars) . str_repeat("?", $emptyStars);

print "
<table id='newtables' style='width:100%;table-layout:fixed;'>
    <tr>
        <th colspan='4'>Your Business</th>
    </tr>
    <tr>
        <th>Business:</th><td>$business_class->name</td>
        <th>Business Rating:</th><td><span style='color: gold;'>$ratingStars</span></td>    
    </tr>
    <tr>
        <th>Employees:</th><td>$business_class->employees</td>
        <th>Vault:</th><td>$business_class->vault</td>
    </tr>
    <tr>
        <th>Earnings Today:</th><td>$business_class->earnedtoday</td>
    </tr>
</table>
";

// Check if the user has already applied
$check_query = "SELECT * FROM business_applications WHERE user_id = {$user_class->id} AND business_id = {$business_class->business_id}";
$check_result = mysql_query($check_query);

// Display the apply button if user is not associated with any business and has not applied yet
if($user_class->current_employer == 0 && mysql_num_rows($check_result) == 0) {
    print "
    <form method='post' action=''>
        <input type='submit' name='apply_for_business' value='Apply to this Business'>
    </form>";
}

print "<table id='newtables' style='width:100%;'>
    <tr>
        <th>Position</th>
        <th>Employee</th>
        <th>Level</th>
        <th>Salary</th>
        <th>Online</th>
    </tr>
";

// Fetch all members associated with the business
$result = mysql_query("SELECT * FROM grpgusers WHERE current_employer = {$business_class->business_id} ORDER BY level DESC");
$position = 0;
while ($line = mysql_fetch_array($result)) {
    $business_member = new User($line['id']);
    print "
    <tr>
        <td width='10%'>" . ( ++$position) . "</td>
        <td width='30%'>$business_member->formattedname</td>
        <td width='10%'>$business_member->level</td>
        <td width='18%'>" . prettynum($business_member->wage, 1) . "</td>
        <td width='10%'>$business_member->formattedonline</td>
    </tr>
";
}
print "</table>";

include 'footer.php';
?>
