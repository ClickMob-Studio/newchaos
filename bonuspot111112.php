<?php
include 'header.php';

if (isset($_POST['deposit'])) {
    security($_POST['damount']);
    $amount = $_POST['damount'];
    $result2 = mysql_query("SELECT * FROM gamebonus WHERE id = 1");
    $worked = mysql_fetch_array($result2);

    if ($amount > $user_class->money)
        echo Message("You do not have that much money.");
    elseif ($amount < 100000)
        echo Message("Please enter at least $100,000.");
    elseif ($worked['Current'] + $amount > $worked['Target']) {
        echo Message("You have donated $" . prettynum($amount) . " and unlocked the server bonus!!.");

        $db->query("INSERT INTO bonuslogs (ID, user_id, Time, Amount) VALUES (?, ?, unix_timestamp(), ?)");
        $db->execute(
            array(
                1,
                $user_class->id,
                $amount
            )
        );

        perform_query("UPDATE grpgusers SET money = ? WHERE id = ?", [$user_class->money - $amount, $user_class->id]);
        perform_query("UPDATE gamebonus SET Current = Current + ? - Target WHERE id = 1", [$amount]);
        perform_query("UPDATE gamebonus SET Time = Time + Timetoadd WHERE id = 1");

        // Insert new record into ads table
        $message = "Has Donated $" . prettynum($amount) . " to the Server Pot";
        $timestamp = time();  // current Unix timestamp
        $poster = $user_class->id;  // ID of the player who donated
        $displaymins = 15;  // update the displaymins column to 15

        $db->query("INSERT INTO ads (timestamp, poster, title, message, displaymins) VALUES (?, ?, '', ?, ?)");
        $db->execute(
            array(
                $timestamp,
                $poster,
                $message,
                $displaymins
            )
        );

    } else {
        echo Message("You have donated $" . prettynum($amount) . " to the server Bonus Pot!.");

        $db->query("INSERT INTO bonuslogs (ID, user_id, Time, Amount) VALUES (?, ?, unix_timestamp(), ?)");
        $db->execute(
            array(
                1,
                $user_class->id,
                $amount
            )
        );

        perform_query("UPDATE grpgusers SET money = ? WHERE id = ?", [$user_class->money - $amount, $user_class->id]);
        perform_query("UPDATE gamebonus SET Current = Current + ? WHERE id = 1", [$amount]);

        // Insert new record into ads table
        $message = "Has Donated $" . prettynum($amount) . " to the Server Pot";
        $timestamp = time();  // current Unix timestamp
        $poster = $user_class->id;  // ID of the player who donated
        $displaymins = 15;  // update the displaymins column to 15

        $db->query("INSERT INTO ads (timestamp, poster, title, message, displaymins) VALUES (?, ?, '', ?, ?)");
        $db->execute(
            array(
                $timestamp,
                $poster,
                $message,
                $displaymins
            )
        );
    }
}

echo '<h3>TML Server Bonuses</h3>';
echo '<hr>';
echo '<div class="floaty">';
echo '&bull; Welcome to the TML Bonus Pots! <br />';
echo '&bull;Here you can choose to contribute towards a server wide bonus! <br />';
echo '&bull;Once the Target is hit for the bonus, 6 Hour will be added to that bonus time.<br />';
echo '<br />';
echo '<hr style="border:0;border-bottom:thin solid #333;" />';
echo '<table id="newtables" class="altcolors" style="width:100%;">';
echo '<tr>';
echo '<th>Title</th>';
echo '<th>Description</th>';
echo '<th>Target</th>';
echo '<th>Current</th>';
echo '<th>Remaining</th>';
echo '<th>Time</th>';
echo '<th>Amount</th>';
echo '</tr>';
$db->query("SELECT * FROM gamebonus ORDER BY `Time` ASC");
$db->execute();
$rows = $db->fetch_row();
foreach ($rows as $row) {
    echo '<tr>';
    echo '<td>' . $row['Title'] . '</td>';
    echo '<td>' . $row['Description'] . '</td>';
    echo '<td>$' . prettynum($row['Target']) . '</td>';
    echo '<td> $' . prettynum($row['Current']) . ' </td>';
    echo '<td> $' . prettynum($row['Target'] - $row['Current']) . ' </td>';
    echo '<td>' . $row['Time'] . '  </td>';
    echo '<td>                    <form method="post">    <input type="text" name="damount" value="' . $user_class->money . '" size="10" maxlength="20">    <input type="submit" name="deposit" value="Donate Money"></form>        </td>';
    echo '</tr>';
}

echo '</table>';
echo '<br />';
echo '<hr style="border:0;border-bottom:thin solid #333;" />';
echo 'Remember to respect the server rules. Abuse of this feature will result in a ban. ';


echo '<table id="newtables" class="altcolors" style="width:100%;">';
echo '<tr>';
echo '<th>ID</th>';
echo '<th>User</th>';
echo '<th>Amount</th>';
echo '<th>Time</th>';
echo '</tr>';
$db->query("SELECT * FROM bonuslogs ORDER BY Time DESC");
$db->execute();
$rows = $db->fetch_row();

foreach ($rows as $row) {
    echo '<tr>';
    echo '<td>' . $row['ID'] . '</td>';
    echo '<td>' . formatName($row['user_id']) . '
 </td>';
    echo '<td>$' . prettynum($row['Amount']) . '</td>';
    echo '<td>' . date("d F Y, g:ia", $row['Time']) . '</td>';
    echo '</tr>';
}
echo '</table>';


echo '</div>';





include 'footer.php';
?>