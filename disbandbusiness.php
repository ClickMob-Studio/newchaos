<?php
include 'header.php';

// Check if the user has a business
if ($user_class->current_employer != 0) {
    // Assuming OwnedBusiness class handles business data
    $business_class = new OwnedBusiness($user_class->current_employer);

    echo "
        <tr><td class='contentspacer'></td></tr>
        <tr><td class='contenthead'>Disband Business</td></tr>
        <tr><td class='contentcontent'>This action will permanently disband the business. All business data, employees, and assets will be deleted!<br /><br />
            <a href='disbandbusiness.php?x=disband'>Continue</a><br />
            <a href='viewbusiness.php?id={$business_class->ownership_id}'>No thanks!</a>
        </td></tr>";

    if ($_GET['x'] == "disband") {
        // Assuming you have appropriate checks here (e.g., not disbanding if there are ongoing transactions, etc.)

        // Delete business data and related records
        perform_query("DELETE FROM OwnedBusinesses WHERE user_id = ?", [$user_class->id]);
        perform_query("UPDATE grpgusers SET current_employer = 0 WHERE current_employer = ?", [$business_class->business_id]);

        // Other cleanup operations related to the business

        echo Message("Your business has been permanently disbanded.");
    }
} else {
    echo Message("You don't have a business to disband.");
}

include 'footer.php';
?>