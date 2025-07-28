<?php
include 'header.php';
?>

<div class='box_top'>House</div>
<div class='box_middle'>
    <div class='pad'>
        <?php
        if (isset($_GET['buy'])) {
            $buy = security($_GET['buy']);
            $db->query("SELECT * FROM houses WHERE id = ? AND buyable = 1");
            $db->execute(array(
                $buy
            ));
            $row = $db->fetch_row(true);
            if (empty($row))
                diefun("Error, this house was not found.");
            $cost = $row['cost'];
            $houselevel = $row['houselevel'];
            if ($houselevel > $user_class->prestige) {
                diefun("Your prestige isn't high enough to purchase this house.");
            }

            $text = "You have purchased a {$row['name']}. To move into this house, you have to visit the 'Your Properties' link in the mainmenu.";
            if ($cost > $user_class->money)
                diefun("You don't have enough money to buy that house.");
            $user_class->money -= $cost;
            $db->query("UPDATE grpgusers SET money = ?, awake = 0 WHERE id = ?");
            $db->execute(array(
                $user_class->money,
                $user_class->id
            ));
            $db->query("INSERT INTO ownedproperties VALUES('', ?, ?)");
            $db->execute(array(
                $user_class->id,
                $buy
            ));
            echo Message($text);
        }
        ?>
        <table class="house-table" style="width: 100%;">
            <?php
            $db->query("SELECT * FROM houses WHERE buyable = 1 ORDER BY id ASC");
            $db->execute();
            $rows = $db->fetch_row();
            foreach ($rows as $row) {
                $owned = ($row['id'] == $user_class->house) ? 'background:rgba(0,0,255,.25);' : '';
                ?>
                <tr>
                    <td style="border-right: thin solid #333;">
                        <img src="images/<?php echo str_replace(array(' ', '*'), '', strtolower($row['name'])); ?>.png"
                            style="width: 100px;">
                    </td>
                    <td>
                        <?php echo $row['name']; ?><br><br>
                        Awake: <?php echo prettynum($row['awake']); ?><br><br>
                        Cost: <?php echo prettynum($row['cost'], 1); ?>
                    </td>
                    <td style="border-left: thin solid #333; line-height: 100px;">
                        <a href="house.php?buy=<?php echo $row['id']; ?>"><button>Buy</button></a>
                    </td>
                </tr>
                <?php
            }
            ?>
        </table>
    </div>
</div>
<?php
include 'footer.php';
?>