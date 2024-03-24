<?php
include 'header.php'; // Your header file, which includes the database connection setup
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
ini_set('display_errors', 1);

$message = ''; // Initialize a message variable

// Handle the attack if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['attack_type'])) {
    // Sanitize and validate inputs
    $boss_id = (int)$_POST['boss_id'];
    $damage = 10; // Fixed damage amount for now
    $user_id = $_SESSION['id']; // Assuming you have the user's ID stored in session

    // Deduct damage from boss's HP and update the database
    $update = mysql_query("UPDATE ServerBosses SET hp_current = hp_current - {$damage} WHERE id = {$boss_id} AND is_active = 1");

    if ($update && mysql_affected_rows() > 0) {
        // Insert hit log into the BossHits table
        $insert = mysql_query("INSERT INTO BossHits (user_id, boss_id, damage_dealt) VALUES ({$user_id}, {$boss_id}, {$damage})");
        if ($insert) {
            $message = "You hit the boss for {$damage} damage.";
        } else {
            $message = "Failed to record hit: " . mysql_error();
        }
    } else {
        $message = "Failed to attack the boss: " . mysql_error();
    }
}

// Fetch the active boss
$result = mysql_query("SELECT * FROM ServerBosses WHERE is_active = 1 LIMIT 1");
$boss = mysql_fetch_assoc($result);

// Fetch the last 5 hits on the boss
$hits_result = mysql_query("SELECT * FROM BossHits WHERE boss_id = {$boss['id']} ORDER BY hit_time DESC LIMIT 5");
$hits = [];
while ($hit = mysql_fetch_assoc($hits_result)) {
    $user = new User($hit['user_id']); // Instantiate a User object
    $hit['username'] = $user->formattedname; // Assuming formattedname is a property of the User object
    $hits[] = $hit;
}

if ($boss) {
    // Calculate HP percentage after any possible attack
    // Make sure hp_on_spawn is not zero to avoid division by zero error
    $hp_percentage = $boss['hp_on_spawn'] > 0 ? ($boss['hp_current'] / $boss['hp_on_spawn']) * 100 : 0;
    ?>
    <div>
        <h2 class="boss-name"><?php echo htmlspecialchars($boss['name']); ?></h2>
        <img src="<?php echo htmlspecialchars($boss['image']); ?>" alt="Boss Image" style="max-width:100%; height:auto;">
        <div class="hp-bar-container" style="background-color: #ddd; width: 100%; height: 24px; border-radius: 12px; overflow: hidden;">
            <div class="hp-bar" style="background-color: #f00; height: 100%; width: <?php echo $hp_percentage; ?>%;"></div>
        </div>
        <p>HP: <?php echo round($hp_percentage); ?>%</p>
        
        <?php if (!empty($message)) : ?>
            <div style="color: green; font-weight: bold;"><?php echo $message; ?></div>
        <?php endif; ?>

        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <input type="hidden" name="boss_id" value="<?php echo $boss['id']; ?>">
            <button type="submit" name="attack_type" value="basic_attack">Basic Attack</button>
        </form>
    </div>

    <div>
      <h3>Last 5 Hits:</h3>
      <ul>
        <?php foreach ($hits as $hit): ?>
          <li><?php echo $hit['username'] . ' dealt ' . htmlspecialchars($hit['damage_dealt']) . ' damage'; ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <?php
} else {
    echo "<p>No active boss at the moment.</p>";
}

include 'footer.php'; // Your footer file
?>
