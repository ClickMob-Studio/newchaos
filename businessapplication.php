<?php
include 'header.php';
mysql_set_charset('utf8');

// Check if the user is the owner of any businesses
$owner_check_query = "SELECT * FROM OwnedBusinesses WHERE user_id = {$user_class->id}";
$owner_check_result = mysql_query($owner_check_query);

if (mysql_num_rows($owner_check_result) <= 0) {
    echo Message("You don't own any businesses!");
    include 'footer.php';
    die();
}

// Handle accept or decline actions
if (isset($_POST['accept']) || isset($_POST['decline'])) {
    $applicant_id = intval($_POST['applicant_id']);
    $business_id = intval($_POST['business_id']);

    if (isset($_POST['accept'])) {
        // Update user's current_employer to the business_id
        $update_query = "UPDATE grpgusers SET current_employer = '$business_id' WHERE id = '$applicant_id'";
        mysql_query($update_query);
        // Delete the application after handling
        $delete_query = "DELETE FROM business_applications WHERE user_id = '$applicant_id' AND business_id = '$business_id'";
        mysql_query($delete_query);

        echo Message("Application accepted and user added to the business!");
    } elseif (isset($_POST['decline'])) {
        // Simply delete the application
        $delete_query = "DELETE FROM business_applications WHERE user_id = '$applicant_id' AND business_id = '$business_id'";
        mysql_query($delete_query);
        echo Message("Application declined.");
    }
}

print "<h2>Manage Business Applications</h2>";

while ($owned_business = mysql_fetch_assoc($owner_check_result)) {
    print "<h3>Applications for: {$owned_business['name']}</h3>";

    $applications_query = "SELECT * FROM business_applications WHERE business_id = {$owned_business['business_id']}";
    $applications_result = mysql_query($applications_query);

    if (mysql_num_rows($applications_result) > 0) {
        print "<table id='newtables' style='width:100%;'>
            <tr>
                <th>Applicant Name</th>
                <th>Actions</th>
            </tr>
        ";

        while ($application = mysql_fetch_assoc($applications_result)) {
            $applicant_query = "SELECT * FROM grpgusers WHERE id = {$application['user_id']}";
            $applicant_result = mysql_query($applicant_query);
            $applicant = mysql_fetch_assoc($applicant_result);
            
            print "
            <tr>
                <td>{$applicant['username']}</td>
                <td>
                    <form method='post' action=''>
                        <input type='hidden' name='applicant_id' value='{$applicant['id']}'>
                        <input type='hidden' name='business_id' value='{$owned_business['business_id']}'>
                        <input type='submit' name='accept' value='Accept'>
                        <input type='submit' name='decline' value='Decline'>
                    </form>
                </td>
            </tr>
            ";
        }
        print "</table>";
    } else {
        echo Message("No applications for this business.");
    }
}

include 'footer.php';
?>