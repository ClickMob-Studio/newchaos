<?php
include 'header.php';
?>

<div class='box_top'>Rated</div>
<div class='box_middle'>
    <div class='pad'>


        <div class="contenthead floaty">
            <h1>Top Rated</h1>
            <table id="newtables" style="width:100%;">
                <tr>
                    <td><b>Rank</b></td>
                    <td><b>Mobster</b></td>
                    <td><b>Rating</b></td>
                </tr>
                <?php
                $db->query("SELECT * FROM grpgusers g WHERE admin = 0 AND NOT EXISTS(SELECT * FROM bans b WHERE b.id = g.id AND type IN ('perm','freeze')) ORDER BY `rating` DESC LIMIT 10");
                $results = $db->fetch_row();

                $rank = 0;
                foreach ($results as $line) {
                    $rank++;
                    $user_hall = new User($line['id']);
                    ?>
                    <tr>
                        <td width="15%"><?php echo $rank; ?></td>
                        <td width="40%"><?php echo $user_hall->formattedname; ?></td>
                        <td width="20%"><?php echo prettynum($user_hall->rating); ?></td>
                    </tr>
                    <?php
                }
                ?>
            </table>
            </td>
            </tr>
        </div>
        <div class="contenthead floaty">
            <h1>Bottom Rated</h1>
            <table id="newtables" style="width:100%;">
                <tr>
                    <td><b>Rank</b></td>
                    <td><b>Mobster</b></td>
                    <td><b>Rating</b></td>
                </tr>
                <?php
                $db->query("SELECT * FROM `grpgusers` g WHERE `admin` = 0 AND NOT EXISTS(SELECT * FROM bans b WHERE b.id = g.id AND type IN ('perm','freeze')) ORDER BY `rating` ASC LIMIT 10");
                $results = $db->fetch_row();
                $rank = 0;
                foreach ($results as $line) {
                    $rank++;
                    $user_hall = new User($line['id']);
                    ?>
                    <tr>
                        <td width="15%"><?php echo $rank; ?></td>
                        <td width="40%"><?php echo $user_hall->formattedname; ?></td>
                        <td width="20%"><?php echo prettynum($user_hall->rating); ?></td>
                    </tr>
                    <?php
                }
                ?>
                </td>
                </tr>
            </table>
        </div>
        <?php
        include 'footer.php';
        ?>