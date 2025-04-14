<?php
require "header.php";
if ($user_class->admin != 1 && $user_class->id != 9) {
  message("You are not allowed here.");
  include("footer.php");
  die();
}
$resultsPerPage = 25;
$currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$startFrom = ($currentPage - 1) * $resultsPerPage;

// Search
$searchUserId = isset($_GET['userid']) ? (int) $_GET['userid'] : null;

$query = "SELECT * FROM `item_sell`";

if ($searchUserId > 0) {
  $query .= " WHERE `userid` = $searchUserId";
}

$query .= " ORDER BY `id` DESC LIMIT $startFrom, $resultsPerPage";

$result = mysql_query($query);
// Display the sale logs in a table
echo '<table border="1">';
echo '<tr><th>ID</th><th>User</th><th>Item</th><th>Quantity</th><th>Price</th><th>When</th></tr>';
if (mysql_num_rows($result) < 1) {
  echo "<tr><td colspan=6>No entries found</td></tr>";
}
while ($row = mysql_fetch_assoc($result)) {
  echo '<tr>';
  echo '<td>' . $row['id'] . '</td>';
  echo '<td>' . formatName($row['userid']) . '</td>';
  echo '<td>' . Item_Name($row['itemid']) . '</td>';
  echo '<td>' . $row['quantity'] . '</td>';
  echo '<td>' . $row['price'] . '</td>';
  echo '<td>' . date('Y-m-d H:i:s', $row['when']) . '</td>'; // Convert Unix timestamp to readable format
  echo '</tr>';
}
echo '</table>';

// Pagination links
$totalResults = mysql_query("SELECT COUNT(*) as total FROM `item_sell`" . ($searchUserId !== null ? " WHERE `userid` = $searchUserId" : ""));
$total = mysql_fetch_assoc($totalResults)['total'];
$totalPages = ceil($total / $resultsPerPage);

echo '<div>';
for ($i = 1; $i <= $totalPages; $i++) {
  echo '<a href="?page=' . $i . '&userid=' . $searchUserId . '">' . $i . '</a> ';
}
echo '</div>';

// Search form
echo '<form action="" method="GET">';
echo '<label for="userid">Search by User ID:</label>';
echo '<input type="text" name="userid" id="userid" value="' . $searchUserId . '">';
echo '<input type="submit" value="Search">';
echo '</form>';