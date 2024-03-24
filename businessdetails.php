<?php
include 'header.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($user_class->current_employer != 0) { // Check if user is employed at a business
    $business_class = new OwnedBusiness($user_class->current_employer); // Assuming OwnedBusiness class handles business data
    if (!empty($business_class->banner)) {
        echo "
        <center>
            <a href='viewbusiness.php?id=$business_class->ownership_id'>
                <img src='$business_class->banner' width='300' height='75' alt='Business Banner' title='$business_class->name' />
            </a>
        </center>";
    }
    ?>
    <table id="newtables" style="width:100%;table-layout:fixed;">
        <tr>
            <th colspan="4">Your Business</td>
        </tr>
        <tr>
            <th>Business:</th><td><?php echo $business_class->name; ?></td>
            <th>Business Level:</th>
            <td>
                <?php
                $rating = $business_class->rating;
                $stars = str_repeat('&#9733;', $rating); // Display gold stars based on the rating
                $emptyStars = str_repeat('&#9734;', 5 - $rating); // Display empty stars for the remaining slots
                echo "$stars$emptyStars $rating/5";
                ?>
            </td>
        </tr>
        <tr>
           <th>Business Type:</th>
<td>
    <?php
    $business_id = $business_class->business_id;

    // Assuming you have a database connection named $db
    $db->query("SELECT name FROM businesses WHERE id = ?");
    $db->execute(array($business_id));
    $business_row = $db->fetch_row(true);

    if (!empty($business_row)) {
        echo $business_row['name'];
    } else {
        echo "Unknown Business";
    }
    ?>
</td>          <th>Employees:</th>
<td>
    <?php
    $business_id = $business_class->business_id;

    // Assuming you have a database connection named $db
    $db->query("SELECT COUNT(*) AS employee_count FROM grpgusers WHERE current_employer = ?");
    $db->execute(array($business_id));
    $employee_row = $db->fetch_row(true);

    if (!empty($employee_row)) {
        $employee_count = $employee_row['employee_count'];
        echo "$employee_count&nbsp;/&nbsp;$business_class->employees";
    } else {
        echo "Unknown";
    }
    ?>
</td><tr>
            <th>Vault:</th><td><?php echo $business_class->vault; ?></td>
            <th>Earned Today:</th>
            <td><?php echo $business_class->earnedtoday; ?>&nbsp</td>
        </tr>

    </table>
    <?php
} else {
    echo Message("You aren't employed at a business.");
}
include("businessheader.php"); // Include appropriate headers for the business section
include 'footer.php';
?>