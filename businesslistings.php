<?php
include 'header.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Fetch all businesses for the leaderboard, joining with grpgusers table to get owner's name
$query = "
SELECT OwnedBusinesses.*, grpgusers.username as owner_name 
FROM OwnedBusinesses 
LEFT JOIN grpgusers ON OwnedBusinesses.user_id = grpgusers.id
ORDER BY rating DESC";

$result = mysql_query($query);

if (!$result || mysql_num_rows($result) == 0) {
    echo Message("No businesses found.");
    include 'footer.php';
    exit;
}

?>

<h2>Business Leaderboard</h2>
<table id="newtables" style="width:100%;table-layout:fixed;">
    <tr>
        <th>Business Name</th>
        <th>Owner</th>
        <th>Rating</th>
        <th>Employees</th>
        <th>Vault</th>
        <th>Earnings Today</th>
    </tr>

    <?php
    while ($business = mysql_fetch_assoc($result)) {
        $rating = intval($business['rating']);
        $stars = str_repeat('&#9733;', $rating); // Display gold stars based on the rating
        $emptyStars = str_repeat('&#9734;', 5 - $rating); // Display empty stars for the remaining slots
        $ratingStars = "$stars$emptyStars $rating/5";
        
        // Fetch employee count
        $employee_result = mysql_query("SELECT COUNT(*) AS employee_count FROM grpgusers WHERE current_employer = {$business['business_id']}");
        $employee_row = mysql_fetch_assoc($employee_result);
        $employee_count = $employee_row['employee_count'];

        echo "
        <tr>
            <td><a href='viewbusiness.php?id={$business['ownership_id']}'>{$business['name']}</a></td>
            <td>{$business['owner_name']}</td>
            <td><span style='color: gold;'>$ratingStars</span></td>
            <td>$employee_count / {$business['employees']}</td>
            <td>{$business['vault']}</td>
            <td>{$business['earnedtoday']}</td>
        </tr>";
    }
    ?>

</table>

<?php
include 'footer.php';
?>
