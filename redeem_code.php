<?php

use Stripe\Terminal\Location;

include 'header.php';

if (isset($_GET['code'])) {
    $code_input = trim($_GET['code']);

    // Example: AE9F4-FG12H-9OI82
    if (!preg_match('/^[A-Z0-9]{5}-[A-Z0-9]{5}-[A-Z0-9]{5}$/i', $code_input)) {
        die('Invalid gift code format.');
    }

    $db->query('SELECT * FROM gift_codes WHERE code = ?');
    $db->execute([$code_input]);
    $code = $db->fetch_row(true);

    // If code exists, we can check if user already redeemed it
    if ($code) {
        $db->query('SELECT * FROM redeemed_codes WHERE code_id = ? AND user_id = ?');
        $db->execute([$code['id'], $user_class->id]);
        $redeemed = $db->fetch_row(true);

        if ($redeemed) {
            // Already redeemed
            header('Location: redeem_code.php?error=already_redeemed');
            exit;
        }

        $items = trim($code['items']);
        if ($items) {
            $item_ids = explode(',', $items);
            foreach ($item_ids as $item_id) {
                Give_Item($item_id, $user_class->id, 1);
            }
        }

        $money = $user_class->money; # Cash
        $points = $user_class->points; # Points
        $credits = $user_class->credits; # Gold bars
        $raidpoints = $user_class->raidpoints; # Raid points
        $cityturns = $user_class->cityturns; # Maze turns

        $money += $code['money'] ? (int) $code['money'] : 0;
        $points += $code['points'] ? (int) $code['points'] : 0;
        $credits += $code['credits'] ? (int) $code['credits'] : 0;
        $raidpoints += $code['raidpoints'] ? (int) $code['raidpoints'] : 0;
        $cityturns += $code['cityturns'] ? (int) $code['cityturns'] : 0;

        $db->query("UPDATE grpgusers SET money = ?, points = ?, credits = ?, raidpoints = ?, cityturns = ? WHERE id = ?");
        $db->execute(array(
            $money,
            $points,
            $credits,
            $raidpoints,
            $cityturns,
            $user_class->id
        ));
    } else {
        header('Location: redeem_code.php?error=invalid_code');
        exit;
    }
}

?>

<?php

if (isset($_GET['error'])) {
    if ($_GET['error'] == 'already_redeemed') {
        echo "<div class='error'>You have already redeemed this code.</div>";
    } elseif ($_GET['error'] == 'invalid_code') {
        echo "<div class='error'>Invalid code.</div>";
    }
}

?>

<div class='box_top'>Redeem code</div>

<div id="redeem-box">
    <form method="get">
        <input type="text" name="code" placeholder="Enter your code here" required>
        <button tpye="submit">Redeem</button>
    </form>
</div>