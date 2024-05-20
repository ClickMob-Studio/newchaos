<?php
include 'header.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$times = array(
    'Last 5 Minutes' => 300,
    'Last 60 Minutes' => 3600,
    'Last 1 Day' => 86400,
);

$defaultTimeframe = 'Last 60 Minutes';
$selectedTimeframe = isset($_GET['timeframe']) ? $_GET['timeframe'] : $defaultTimeframe;

if (!array_key_exists($selectedTimeframe, $times)) {
    $selectedTimeframe = $defaultTimeframe;
}

$intval = $times[$selectedTimeframe];
$limit = 50;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$users = array();

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
    if (isset($row['id'])) {
        $users[] = array(
            "user" => $row['username'],
            "pic" => $row['avatar'],
            "date" => $row["lastactive"],
            "id" => $row['id']
        );
    }
}

$totalUsersQuery = "
    SELECT COUNT(*) as count FROM grpgusers
    WHERE lastactive > " . (time() - $intval);
$db->query($totalUsersQuery);
$totalUsers = $db->fetch_row(true)['count'];
$totalPages = ceil($totalUsers / $limit);

?>

<h3 class="text-uppercase">Players Online</h3>
<p class="text-warning"><?php echo number_format($totalUsers); ?> Active players in the last <?php echo $selectedTimeframe; ?>.</p>
<p class="w-lg-50 mb-3">Check online players and know who is active in the last <?php echo $selectedTimeframe; ?>.</p>

<div class="timeframe-links mb-3">
    <?php foreach ($times as $title => $intval) : ?>
        <a href="?timeframe=<?php echo urlencode($title); ?>" class="btn btn-secondary btn-sm"><?php echo $title; ?></a>
    <?php endforeach; ?>
</div>

<div class="card crime-card no-hover mb-4">
    <div class="card-body">
        <ul class="list-inline flex-wrap">
            <?php foreach ($users as $user) : ?>
                <li class="list-inline-item flex-fill mb-2">
                    <div class="user-card">
                        <img style="width: 20px; height:20px;" src="<?php echo $user['pic']; ?>" alt="<?php echo $user['user']; ?>">
                        <p><?php echo $user['user']; ?></p>
                        <p><?php echo howlongago($user['date']); ?></p>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<nav aria-label="Page navigation example">
    <ul class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
            <li class="page-item<?php echo ($i == $page) ? ' active' : ''; ?>"><a class="page-link" href="?timeframe=<?php echo urlencode($selectedTimeframe); ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
        <?php endfor; ?>
    </ul>
</nav>
