<?php

include 'header.php';


$christmasGiftCount = Check_Item(295, $user_class->id);
$userSantasGrotto = getUserSantasGrotto($user_class->id);

if (isset($_GET['donate']) && $_GET['donate'] == 'yes') {

    if ($christmasGiftCount > 0) {
        $newProgress = $userSantasGrotto['progress'] + 1;
        $newLevel = $userSantasGrotto['level'];
        if ($newLevel < 3) {
            $newExp = $userSantasGrotto['exp'] + mt_rand(4,12);

            if ($newExp >= 100) {
                $newExp = 0;
                $newLevel = $newLevel + 1;
            }
        }

        $db->query("UPDATE user_santas_grotto SET exp = ?, level = ?, gifts_donated = gifts_donated + 1 WHERE user_id = ?");
        $db->execute([$newExp, $newLevel, $user_class->id]);

       Take_Item(295, $user_class->id);

        $prizeChance = mt_rand(1,100);
       if ($newLevel == 1) {
           if ($prizeChance <= 20) {
               // Cash
               $cashPrize = mt_rand(100000, 500000);

               $db->query('UPDATE grpgusers SET money = money + ? WHERE id = ?');
               $db->execute([$cashPrize, $user_class->id]);

                echo '
                    <div class="alert alert-success">
                        <strong>Success!</strong> You have successfully donated a Christmas Gift. You have received $' . number_format($cashPrize) . ' in return. <a href="santasgrotto.php">Go back</a>.
                    </div>';
                exit;
           } else if ($prizeChance <= 40) {
               // Points
               $pointsPrize = mt_rand(5000, 15000);

               $db->query('UPDATE grpgusers SET points = points + ? WHERE id = ?');
               $db->execute([$pointsPrize, $user_class->id]);

               echo '
                    <div class="alert alert-success">
                        <strong>Success!</strong> You have successfully donated a Christmas Gift. You have received ' . number_format($pointsPrize) . ' points in return. <a href="santasgrotto.php">Go back</a>.
                    </div>';
               exit;

           } else if ($prizeChance <= 60) {
               // Raid Tokens
               $raidTokensPrize = mt_rand(10, 20);

               $db->query('UPDATE grpgusers SET raidtokens = raidtokens + ? WHERE id = ?');
               $db->execute([$raidTokensPrize, $user_class->id]);

               echo '
                    <div class="alert alert-success">
                        <strong>Success!</strong> You have successfully donated a Christmas Gift. You have received ' . number_format($raidTokensPrize) . ' raid tokens in return. <a href="santasgrotto.php">Go back</a>.
                    </div>';
               exit;
           } else {
               $itemIds = array(
                    10,
                    254, // Crime Potion
                    288, // Cotton Candy
                    289, // Draculas Loot Crate
                    253, // Gold Rush Token
               );

               $itemId = $itemIds[array_rand($itemIds)];

               Give_Item($itemId, $user_class->id);

                $itemName = Get_Item_Name($itemId);

                echo '
                    <div class="alert alert-success">
                        <strong>Success!</strong> You have successfully donated a Christmas Gift. You have received a ' . $itemName . ' in return. <a href="santasgrotto.php">Go back</a>.
                    </div>';
                exit;
           }

       } elseif ($newLevel == 2) {
           if ($prizeChance <= 20) {
               // Cash
               $cashPrize = mt_rand(500000, 1000000);

               $db->query('UPDATE grpgusers SET money = money + ? WHERE id = ?');
               $db->execute([$cashPrize, $user_class->id]);

               echo '
                    <div class="alert alert-success">
                        <strong>Success!</strong> You have successfully donated a Christmas Gift. You have received $' . number_format($cashPrize) . ' in return. <a href="santasgrotto.php">Go back</a>.
                    </div>';
               exit;
           } else if ($prizeChance <= 40) {
               // Points
               $pointsPrize = mt_rand(10000, 25000);

               $db->query('UPDATE grpgusers SET points = points + ? WHERE id = ?');
               $db->execute([$pointsPrize, $user_class->id]);

               echo '
                    <div class="alert alert-success">
                        <strong>Success!</strong> You have successfully donated a Christmas Gift. You have received ' . number_format($pointsPrize) . ' points in return. <a href="santasgrotto.php">Go back</a>.
                    </div>';
               exit;

           } else if ($prizeChance <= 60) {
               // Raid Tokens
               $raidTokensPrize = mt_rand(20, 40);

               $db->query('UPDATE grpgusers SET raidtokens = raidtokens + ? WHERE id = ?');
               $db->execute([$raidTokensPrize, $user_class->id]);

               echo '
                    <div class="alert alert-success">
                        <strong>Success!</strong> You have successfully donated a Christmas Gift. You have received ' . number_format($raidTokensPrize) . ' raid tokens in return. <a href="santasgrotto.php">Go back</a>.
                    </div>';
               exit;
           } else {
               $itemIds = array(
                   42, // Mystery Box
                   163, // Police Badge
                   251, // Raid Pass
                   255, // Crime Booster
                   281, // Gym Super Pills
               );

               $itemId = $itemIds[array_rand($itemIds)];

               Give_Item($itemId, $user_class->id);

               $itemName = Get_Item_Name($itemId);

               echo '
                    <div class="alert alert-success">
                        <strong>Success!</strong> You have successfully donated a Christmas Gift. You have received a ' . $itemName . ' in return. <a href="santasgrotto.php">Go back</a>.
                    </div>';
               exit;
           }
       } else {
           if ($prizeChance <= 20) {
               // Cash
               $cashPrize = mt_rand(1000000, 5000000);

               $db->query('UPDATE grpgusers SET money = money + ? WHERE id = ?');
               $db->execute([$cashPrize, $user_class->id]);

               echo '
                    <div class="alert alert-success">
                        <strong>Success!</strong> You have successfully donated a Christmas Gift. You have received $' . number_format($cashPrize) . ' in return. <a href="santasgrotto.php">Go back</a>.
                    </div>';
               exit;
           } else if ($prizeChance <= 40) {
               // Points
               $pointsPrize = mt_rand(25000, 50000);

               $db->query('UPDATE grpgusers SET points = points + ? WHERE id = ?');
               $db->execute([$pointsPrize, $user_class->id]);

               echo '
                    <div class="alert alert-success">
                        <strong>Success!</strong> You have successfully donated a Christmas Gift. You have received ' . number_format($pointsPrize) . ' points in return. <a href="santasgrotto.php">Go back</a>.
                    </div>';
               exit;

           } else if ($prizeChance <= 60) {
               // Raid Tokens
               $raidTokensPrize = mt_rand(40, 100);

               $db->query('UPDATE grpgusers SET raidtokens = raidtokens + ? WHERE id = ?');
               $db->execute([$raidTokensPrize, $user_class->id]);

               echo '
                    <div class="alert alert-success">
                        <strong>Success!</strong> You have successfully donated a Christmas Gift. You have received ' . number_format($raidTokensPrize) . ' raid tokens in return. <a href="santasgrotto.php">Go back</a>.
                    </div>';
               exit;
           } else {
               $itemIds = array(
                   277, // Mission Pass
                   279, // Protein Bar
                   283, // GRT Chest
                   256, // Nerve Vial
                   265, // Voidglass
                   266, // Hour Glass
                   267, // Lifewood
               );

               $itemId = $itemIds[array_rand($itemIds)];

               Give_Item($itemId, $user_class->id);

               $itemName = Get_Item_Name($itemId);

               echo '
                    <div class="alert alert-success">
                        <strong>Success!</strong> You have successfully donated a Christmas Gift. You have received a ' . $itemName . ' in return. <a href="santasgrotto.php">Go back</a>.
                    </div>';
               exit;
           }

       }

        echo '
            <div class="alert alert-success">
                <strong>Success!</strong> You have successfully donated a Christmas Gift. You have received a random gift in return. <a href="santasgrotto.php">Go back</a>.
            </div>
        ';
    } else {
        echo '
            <div class="alert alert-danger">
                <strong>Fail!</strong> You do not have any Christmas Gifts to donate. <a href="santasgrotto.php">Go back</a>.
            </div>
        ';
    }

}
?>

<div class='box_top'><h1>Santas Grotto</h1></div>
<div class='box_middle'>
    <div class='pad'>
        <div class="row">
            <div class="col-md-12">
                <img src="css/images/NewGameImages/santas-grotto.jpeg" class="img-responsive" style="width:100%;height:auto;max-width:100%;max-height:100%;"/>

                <center>
                    <br />
                    <p>
                        Welcome to Santa's Grotto. Being in the Mafia isn't just about killing, committing crimes and earning the big bucks. We've got to let
                        our community know that we support them to bring the future mafia bosses into the world. Santa's Grotto is a place where you can donate
                        Christmas Gifts to the community and in return you'll be given a random gift back. The more you donate, the more you'll receive in return.
                    </p>
                    <hr />

                    <div class="row">
                        <div class="col-md-4"></div>
                        <div class="col-md-4">
                            <h3><strong>Your Gifting Level: <?php echo $userSantasGrotto['level'] ?></strong></h3>
                            <br />
                            <p>Progress to next level:</p>
                            <div class="progress" role="progressbar" aria-valuenow="<?php echo ($userSantasGrotto['exp'] / 100 * 100 ); ?>" aria-valuemin="0" aria-valuemax="100" title="<?php echo $userSantasGrotto['exp'] . '/100'; ?>">
                                <div class="progress-bar bg-success ba-level-progress-bar" style="width: <?php echo ($userSantasGrotto['exp'] / 100 * 100 ); ?>%"></div>
                            </div>

                            <br />
                            <p style="color: red;"><strong>You currently have <?php echo $christmasGiftCount ?> Christmas Gifts</strong></p>

                            <?php if ($christmasGiftCount > 0): ?>
                                <a href="santasgrotto.php?donate=yes" class="btn btn-success">Donate Christmas Gift</a>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-4"></div>
                    </div>
                </center>
            </div>
        </div>
    </div>
</div>

<?php
include 'footer.php';
?>
