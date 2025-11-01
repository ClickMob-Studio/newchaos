<?php
include 'header.php';

$prices = array(
    151 => 2000000,
    152 => 5000000,
    154 => 10000000
);

if (isset($_GET['suit'])) {
    $suit = security($_GET['suit']);

    $db->query("SELECT * FROM bomb_protections WHERE user_id = ? LIMIT 1");
    $db->execute(
        array(
            $user_class->id
        )
    );
    $bomb_protection = $db->fetch_row(true);
    if ($bomb_protection)
        echo Message("You already have a protection suit");
    else {

        $db->query("SELECT * FROM protection_suits WHERE id = ? LIMIT 1");
        $db->execute(
            array(
                $suit
            )
        );
        $suit = $db->fetch_row(true);

        if ($suit) {
            if ($user_class->money < $suit['cost'])
                diefun('You do not have enough money');

            $db->query("INSERT INTO bomb_protections (user_id, protection) VALUES (?, ?)");
            $db->execute(
                array(
                    $user_class->id,
                    $suit['id']
                )
            );
            $db->query("UPDATE grpgusers SET money = money - ? WHERE id = ?");
            $db->execute(
                array(
                    $suit['cost'],
                    $user_class->id
                )
            );
            echo Message("You have purchased the " . $suit['name'] . " for " . $suit['cost'] . " money");
        } else {
            echo Message("Unable to find that particular protection suit");
        }
    }
}

if (isset($_GET['buy'])) {
    $buy = security($_GET['buy']);

    $db->query("SELECT * FROM items WHERE `type` = 'bomb' AND id = ? LIMIT 1");
    $db->execute(
        array(
            $buy
        )
    );
    $bomb = $db->fetch_row();
    if ($bomb) {
        $bomb = $bomb[0];
        $cost = $prices[$bomb['id']];
        if ($user_class->money >= $cost) {

            $user_class->money -= $cost;
            $db->query("UPDATE grpgusers SET money = money - ? WHERE id = ?");
            $db->execute(
                array(
                    $cost,
                    $user_class->id
                )
            );
            echo Message("You have purchased 1x " . $bomb['itemname'] . " for " . $cost . " money");
            Give_Item($bomb['id'], $user_class->id);

        } else {
            echo Message("Sorry but you do not have enough money");
        }
    } else {
        echo Message("Please choose one of the bombs from the market");
    }
}

$db->query("SELECT * FROM items WHERE `type` = 'bomb' ORDER BY id ASC");
$db->execute();
$bombs = $db->fetch_row();

?>

<div class="floaty">
    <h2>Protection Suits</h2>
    <p class="item-text mb-3">All suits are single use</p>

    <?php
    $db->query("SELECT * FROM bomb_protections WHERE user_id = ?");
    $db->execute(
        array(
            $user_class->id
        )
    );
    $bomb_protection = $db->fetch_row(true);

    if (!$bomb_protection) {
        ?>
        <p class="item-text text-red mb-3">You currently have no protection</p>
        <div class="container" style="display:flex;justify-content:space-around;">

            <?php


            $db->query("SELECT * FROM protection_suits");
            $db->execute();
            $protection_suits = $db->fetch_row();

            foreach ($protection_suits as $suit) {
                echo '
                    <div class="item">
                        <img src="' . $suit['image'] . '" width="150px" height="150px" alt="">
                        <p>' . $suit['description'] . '</p>
                        <p class="item-text">Cost: $' . number_format($suit['cost']) . '</p>
                        <a class="item-purchase" href="?suit=' . $suit['id'] . '">Purchase</a>
                    </div>';
            }
            ;
    } else {


        $db->query("SELECT * FROM protection_suits WHERE id = ?");
        $db->execute(
            array(
                $bomb_protection['protection']
            )
        );
        $suit = $db->fetch_row(true);

        echo '
            <div class="container" style="display:flex;justify-content:space-around;">
            <div class="item">
                <img src="' . $suit['image'] . '" width="150px" height="150px" alt="">
                <p class="item-text text-lime">Active</p>
                <p class="item-text">' . $suit['description'] . '</p>
            </div>';
    }
    ?>


        <!-- <div class="item">
            <img src="/css/newgame/items/psuit_5.png" width="150px" height="150px" alt="">
            Lorem ipsum dolor sit amet consectetur adipisicing elit. Magnam, ea molestias ad officiis dolorem iusto cum dicta recusandae, quibusdam architecto eligendi optio ipsam dolore distinctio odit beatae amet repellat aliquid!
        </div> -->
    </div>
</div>

<div class="floaty">
    <div class="container" style="display:flex;justify-content:space-around;">
        <?php
        foreach ($bombs as $bomb) {
            echo '<div class="item" style="display:inline-grid">';
            echo '<p style="font-size:14px">' . item_popup($bomb['itemname'], $bomb['id']) . '</p>';
            echo '<img src="' . $bomb['image'] . '" width="150px" height="150px">';
            echo '<p class="item-text">Cost: $' . number_format($prices[$bomb['id']], 0) . '</p>';
            echo '<a class="item-purchase" href="?buy=' . $bomb['id'] . '">Purchase</a>';
            echo '</div>';
        }
        ?>
    </div>
</div>

<?
include 'footer.php';
?>