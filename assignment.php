<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars(trim($_POST['name']));
    $room = htmlspecialchars(trim($_POST['room']));
    $message = htmlspecialchars(trim($_POST['message']));

    $entries = read_entries();
    $entries[] = [
        'name' => $name,
        'room' => $room,
        'message' => $message,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    file_put_contents('entries.json', json_encode($entries, JSON_PRETTY_PRINT));

    echo "<div style='text-align:center;'>";
    echo "<h3>Thank you for your message, " . $name . "!</h3>";
    echo "<p>Your message has been recorded.</p>";
    echo "</div>";
    exit;
}
?>

<div style="text-align:center;">
    <h2>Marion's B&B Guest Book</h2>
    <br /><br />
    <form method="POST">
        Name: <input type="text" name="name" required /><br /><br />
        Room: <input type="text" name="room" required /><br /><br />
        Message:<br />
        <textarea name="message" rows="4" cols="50" required></textarea><br /><br />
        <input type="submit" value="Submit" />
    </form>

    <br /><br />

    <h3>Previous Entries:</h3>
    <?php
    $entries = read_entries();
    if (!empty($entries)) {
        foreach (array_reverse($entries) as $entry) {
            echo "<div style='border:1px solid #000; padding:10px; margin:10px; text-align:left;'>";
            echo "<strong>" . htmlspecialchars($entry['name']) . "</strong> (Room: " . htmlspecialchars($entry['room']) . ")<br />";
            echo "<em>" . htmlspecialchars($entry['timestamp']) . "</em><br /><br />";
            echo nl2br(htmlspecialchars($entry['message']));
            echo "</div>";
        }
    } else {
        echo "<p>No entries yet. Be the first to sign the guest book!</p>";
    }
    ?>

</div>

<?php

function read_entries(): array
{
    if (file_exists('entries.json')) {
        return json_decode(file_get_contents('entries.json'), true);
    }

    return [];
}

?>