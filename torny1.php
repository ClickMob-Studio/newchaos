<?php
// Assuming you have a database connection set up and it's included here
include 'header.php';







// Function to start the tournament (to be called when the start button is pressed)
function startTournament($tournamentId) {
    // Logic to initialize the tournament, like creating matchups, goes here

    // Update the tournament status to 'Ongoing'
    $updateQuery = "UPDATE new_tournaments SET current_status = 'Ongoing' WHERE id = '$tournamentId'";
    $result = mysql_query($updateQuery);

    // Redirect to the tournament view page or refresh the page
    header("Location: view_tournament.php?tournament_id=$tournamentId");
    exit;
}

// Check if the start button has been pressed
if (isset($_POST['start_tournament'])) {
    $tournamentId = mysql_real_escape_string($_POST['tournament_id']);
    startTournament($tournamentId);
}

// Fetch tournament data from the database
$query = "SELECT * FROM new_tournaments WHERE current_status = 'Registration' OR current_status = 'Ongoing'";
$result = mysql_query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tournaments</title>
    <!-- Include your stylesheet here -->
</head>
<body>
    <h1>Active Tournaments</h1>
    <table>
        <thead>
            <tr>
                <th>Tournament Name</th>
                <th>Max Players</th>
                <th>Current Status</th>
                <th>Start Time</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysql_fetch_assoc($result)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= $row['max_players'] ?></td>
                    <td><?= $row['current_status'] ?></td>
                    <td><?= date("Y-m-d H:i:s", $row['start_time']) ?></td>
                    <td>
                        <?php if ($row['current_status'] == 'Registration'): ?>
                            <!-- Start Tournament Form -->
                            <form method="post" action="">
                                <input type="hidden" name="tournament_id" value="<?= $row['id'] ?>">
                                <input type="submit" name="start_tournament" value="Start Tournament">
                            </form>
                            <!-- Registration Link -->
                            <a href="register.php?tournament_id=<?= $row['id'] ?>">Register</a>
                        <?php elseif ($row['current_status'] == 'Ongoing'): ?>
                            <a href="view_tournament.php?tournament_id=<?= $row['id'] ?>">View</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
