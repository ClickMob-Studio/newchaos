<?php

include 'header.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $_SESSION['donation_count'] += 1;
    $_SESSION['level'] = floor($_SESSION['donation_count'] / 10) + 1;
    $reward = getRandomReward($_SESSION['level']);
    echo "<p>Thank you for your donation! You received: $reward</p>";
}

// Function to get a random reward based on level
function getRandomReward($level) {
    $rewards = [
        1 => ['Candy Cane', 'Chocolate'],
        2 => ['Toy Car', 'Stuffed Animal'],
        3 => ['Gift Card', 'Board Game'],
        // Add more levels and rewards as needed
    ];
    $level_rewards = $rewards[$level] ?? $rewards[max(array_keys($rewards))];
    return $level_rewards[array_rand($level_rewards)];
}
?>

    <div>
        <h1>Donate a Christmas Gift</h1>
        <form method="post">
            <button type="submit">Donate</button>
        </form>
        <div>
            <p>Donation Count: <?php echo $_SESSION['donation_count']; ?></p>
            <p>Current Level: <?php echo $_SESSION['level']; ?></p>
            <div style="border: 1px solid #000; width: 100%; height: 20px;">
                <div style="background-color: green; width: <?php echo ($_SESSION['donation_count'] % 10) * 10; ?>%; height: 100%;"></div>
            </div>
        </div>
    </div>

<?php
include 'footer.php';
?>
