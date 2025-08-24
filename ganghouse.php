<?php
include 'header.php';
?>
<div class='box_top'>Gang House</div>
<div class='box_middle'>
    <div class='pad'>
        <?php
        $gang_class = new Gang($user_class->gang);
        if ($user_class->gang == 0)
            diefun("You aren't in a gang.");
        $user_rank = new GangRank($user_class->grank);
        if ($user_rank->houses != 1 && !$user_class->admin)
            diefun("You don't have permission to be here!");
        if (isset($_GET['action']) && $_GET['action'] == "sell") {
            if ($gang_class->ghouse != 0) {
                $db->query("SELECT * FROM ghouses WHERE id = ?");
                $db->execute(array(
                    $gang_class->ghouse
                ));
                $row = $db->fetch_row(true);
                $gang_class->moneyvault = floor($gang_class->moneyvault + ($row['cost'] / 2));
                $db->query("UPDATE gangs SET ghouse = 0, moneyvault = ? WHERE id = ?");
                $db->execute(array(
                    $gang_class->moneyvault,
                    $gang_class->id
                ));
                echo Message("You have sold the gang house for " . prettynum(floor($row['cost'] / 2), 1) . ".");
            } else {
                echo Message("Your gang doesn't currently own a house.");
            }
        }
        if (isset($_GET['buy'])) {
            $id = security($_GET['buy']);
            $db->query("SELECT * FROM ghouses WHERE id = ?");
            $db->execute(array(
                $id
            ));
            $row = $db->fetch_row(true);
            $cost = $row['cost'];
            if ($gang_class->ghouse != 0) {
                $db->query("SELECT * FROM ghouses WHERE id = ?");
                $db->execute(array(
                    $gang_class->ghouse
                ));
                $oldhouse = $db->fetch_row(true);
                $subcost = floor($oldhouse['cost'] * .50);
                $text = "You have sold your gang house for 50% of its original cost (" . prettynum($subcost, 1) . ") and purchased a " . $row['name'] . ".";
            } else {
                $subcost = 0;
                $text = "You have purchased a " . $row['name'] . ".";
            }


            if ($row['id'] == $gang_class->ghouse)
                diefun("You already have that gang house.");
            elseif ($cost > ($gang_class->moneyvault + $subcost))
                diefun("You don't have enough money in your vault to buy that gang house.");
            elseif ($row['name'] == "")
                diefun("That's not a real house.");
            else {
                $gang_class->moneyvault = floor(($gang_class->moneyvault + $subcost) - $cost);
                $db->query("UPDATE gangs SET ghouse = ?, moneyvault = ? WHERE id = ?");
                $db->execute(array(
                    $id,
                    $gang_class->moneyvault,
                    $gang_class->id
                ));
                echo Message($text);
            }
        }
        echo "<div class='contenthead floaty'>";

        echo "Your gang currently has <b>$" . prettynum($gang_class->moneyvault) . "</b> to buy a gang house.";
        if ($gang_class->ghouse > 0) {
            $db->query("SELECT * FROM ghouses WHERE id = ?");
            $db->execute(array(
                $gang_class->ghouse
            ));
            $row = $db->fetch_row(true);
            echo "<br /><br /><a href='ganghouse.php?action=sell' onclick=\"return confirm('Are you sure you want to sell your gang house for $" . prettynum(round($row['cost'] / 2)) . "?');\">Sell Gang House for $" . prettynum(round($row['cost'] / 2)) . "</a>";
        }
        echo "</td></tr>";
        genHead("Purchase a new house:");
        echo '<table id="newtables" style="margin:auto;">';
        echo '<tr>';
        echo '<th>Type</th>';
        echo '<th>Awake</th>';
        echo '<th>Cost</th>';
        echo '<th>Move</th>';
        echo '</tr>';
        $db->query("SELECT * FROM ghouses ORDER BY id ASC");
        $db->execute();
        $rows = $db->fetch_row();
        foreach ($rows as $row) {
            echo '<tr>';
            echo '<td>' . $row['name'] . '</td>';
            echo '<td>' . prettynum($row['awake']) . '%</td>';
            echo '<td>' . prettynum($row['cost'], 1) . '</td>';
            echo '<td>',
                ($row['id'] != $gang_class->ghouse || $gang_class->ghouse == 0) ?
                '<a href="ganghouse.php?buy=' . $row['id'] . '" onclick="return confirm(\'Are you sure you want to move into this gang house?\');">Move In</a>' :
                '<s>Move In</s>',
                '</td>';
            echo '</tr>';
        }
        echo '</table>';
        echo '</td>';
        echo '</tr>';
        include("gangheaders.php");
        include 'footer.php';
        ?>