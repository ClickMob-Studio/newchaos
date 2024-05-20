<?php
// Include the database connection from header.php
include 'header.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Define the times array
$times = array(
    'Last 5 Minutes' => 300,    // 5 minutes
    'Last 60 Minutes' => 3600,  // 60 minutes
    'Last 1 Day' => 86400,      // 1 day
);

// Set the default timeframe to 'Last 60 Minutes'
$defaultTimeframe = 'Last 60 Minutes';

// Get the selected timeframe from the URL, default to 'Last 60 Minutes' if not set
$selectedTimeframe = isset($_GET['timeframe']) ? $_GET['timeframe'] : $defaultTimeframe;

// Ensure the selected timeframe is valid
if (!array_key_exists($selectedTimeframe, $times)) {
    $selectedTimeframe = $defaultTimeframe;
}

$intval = $times[$selectedTimeframe];
$limit = 50; // Number of users per page

// Get the current page from the URL, default to 1 if not set
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$users = array();

// Prepare and execute the query to fetch online users
$query = "
    SELECT * FROM grpgusers
    WHERE lastactive > " . (time() - $intval) . "
    ORDER BY lastactive DESC
    LIMIT $limit OFFSET $offset
";
$db->query($query);
$onlineUsers = $db->fetch_row();
$onlineCount = count($onlineUsers);

foreach ($onlineUsers as $row) {
    // Assuming the grpgusers table contains the necessary user information
    if (isset($row['id'])) {
        $users[] = array(
            "user" => $row['username'],
            "pic" => $row['avatar'], // Assuming profile_picture is a field in the grpgusers table
            "date" => $row["lastactive"],
            "id" => $row['id']
        );
    }
}

// Pagination logic
$totalUsersQuery = "
    SELECT COUNT(*) as count FROM grpgusers
    WHERE lastactive > " . (time() - $intval);
$db->query($totalUsersQuery);
$totalUsers = $db->fetch_row(true)[0];
$totalPages = ceil($totalUsers / $limit);

// Render the HTML directly
echo '<h3 class="text-uppercase">Players Online</h3>';
echo '<p class="text-warning">' . number_format($onlineCount) . ' Active players in the last ' . $selectedTimeframe . '.</p>';
echo '<p class="w-lg-50 mb-3">Check online players and know who is active in the last ' . $selectedTimeframe . '.</p>';

// Timeframe links
echo '<div class="timeframe-links">';
foreach ($times as $title => $intval) {
    echo '<a href="?timeframe=' . urlencode($title) . '">' . $title . '</a> ';
}
echo '</div>';

echo '<div class="card crime-card no-hover">';
echo '<div class="card-body">';
echo '<ul class="list-inline flex-wrap">';
foreach ($users as $user) {
    echo '<li class="list-inline-item flex-fill mb-2">';
    // Render the user card directly
    echo '<div class="user-card">';
    echo '<img style="width: 20px; height:20px;" src="' . $user['pic'] . '" alt="' . $user['user'] . '">';
    echo '<p>' . $user['user'] . '</p>';
    echo '<p>' . howlongago($user['date']) . '</p>';
    echo '</div>';
    echo '</li>';
}
echo '</ul>';
echo '</div>';
echo '</div>';

// Pagination
echo '<nav aria-label="Page navigation example">';
echo '<ul class="pagination">';
for ($i = 1; $i <= $totalPages; $i++) {
    echo '<li class="page-item' . ($i == $page ? ' active' : '') . '"><a class="page-link" href="?timeframe=' . urlencode($selectedTimeframe) . '&page=' . $i . '">' . $i . '</a></li>';
}
echo '</ul>';
echo '</nav>';
?>
