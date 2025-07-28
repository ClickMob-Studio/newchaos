<?php
include "header.php";
exit;
?>

<div class='box_top'>Rental Market</div>
<div class='box_middle'>
    <div class='pad'>

        <style>
            .houseimage {
                max-width: 100px;

                max-height: 100px;

            }
        </style>
        <?php
        if (isset($_GET['remove'])) {
            $remove = security($_GET['remove']);
            $db->query("SELECT * FROM rentalmarket WHERE id = ? AND owner = ?");
            $db->execute(array(
                $remove,
                $user_class->id
            ));
            $row = $db->fetch_row(true);
            if (empty($row))
                diefun("Error, this cell was not found on the rental market.");
            $db->startTrans();
            $db->query("DELETE FROM rentalmarket WHERE id = ? AND owner = ?");
            $db->execute(array(
                $remove,
                $user_class->id
            ));
            $db->query("INSERT INTO ownedproperties VALUES ('', ?, ?)");
            $db->execute(array(
                $user_class->id,
                $row['houseid']
            ));
            $db->endTrans();
            echo Message("You have taken your property off the market.");
        }
        if (isset($_GET['rent'])) {
            $rent = security($_GET['rent']);
            $db->query("SELECT * FROM rentalmarket WHERE id = ?");
            $db->execute(array(
                $rent
            ));
            $row = $db->fetch_row(true);
            if (empty($row))
                diefun("Error, this cell was not found on the rental market.");
            $totalcost = $row['costperday'] * $row['days'];
            if ($totalcost > $user_class->money)
                diefun("You do not have enough cash to rent this property.");
            $db->startTrans();
            if ($user_class->house > 0) {
                $db->query("INSERT INTO ownedproperties VALUES ('', ?, ?)");
                $db->execute(array(
                    $user_class->id,
                    $user_class->house
                ));
                $db->query("UPDATE grpgusers SET house = 0 WHERE id = ?");
                $db->execute(array(
                    $user_class->id
                ));
                $user_class->house = 0;
            }
            $db->query("UPDATE grpgusers SET money = money - ? WHERE id = ?");
            $db->execute(array(
                $totalcost,
                $user_class->id
            ));
            $db->query("UPDATE grpgusers SET bank = bank + ? WHERE id = ?");
            $db->execute(array(
                $totalcost,
                $row['owner']
            ));
            $db->query("INSERT INTO rentedproperties VALUES ('', ?, ?, ?, ?)");
            $db->execute(array(
                $row['owner'],
                $user_class->id,
                $row['houseid'],
                $row['days']
            ));
            $db->query("DELETE FROM rentalmarket WHERE id = ?");
            $db->execute(array(
                $rent
            ));
            Send_Event($row['owner'], "[-_USERID_-] has started renting one of your properties.", $user_class->id);
            $db->endTrans();
            echo Message("You have moved into your rental.");
        }
        echo '<div class="contenthead floaty">';
        echo '<table id="newtables" class="altcolors" style="width:100%;">';
        echo '<tr>';
        echo '<th>Cell Image</th>';
        echo '<th>Cell Info</th>';
        echo '<th>Rental Info</th>';
        echo '<th>Actions</th>';
        echo '</tr>';
        $db->query("SELECT *, r.id as rid FROM rentalmarket r JOIN houses h ON r.houseid = h.id ORDER BY awake DESC");
        $db->execute();
        $rows = $db->fetch_row();
        foreach ($rows as $row) {
            echo '<tr>';
            echo '<td>';
            echo '<img class="houseimage" src="images/' . str_replace(array(" ", "*"), "", strtolower($row['name'])) . '.png" />';
            echo '</td>';
            echo '<td>';
            echo $row['name'] . '<br />';
            echo '<br />';
            echo prettynum($row['cost'], 1) . '<br />';
            echo '<br />';
            echo number_format($row['awake']) . ' Awake';
            echo '</td>';
            echo '<td>';
            echo 'Owner: ' . formatName($row['owner']) . '<br />';
            echo '<br />';
            echo 'Cost Per Day: ' . prettynum($row['costperday'], 1) . '<br />';
            echo '<br />';
            echo 'Must buy <span style="color:red;font-weight:bold;">' . $row['days'] . '</span> Days<br />';
            echo '<br />';
            echo 'Total Cost: ' . prettynum($row['costperday'] * $row['days'], 1);
            echo '</td>';
            echo '<td>';
            echo ($row['owner'] == $user_class->id) ?
                "<a href='?remove={$row['rid']}'><button>Remove</button></a>" :
                "<a href='?rent={$row['rid']}'><button>Rent</button></a>";
            echo '</td>';
            echo '</tr>';
        }
        echo '</table>';
        echo '</div>';
        include "footer.php";
        ?>