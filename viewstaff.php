<?php
include 'header.php';
?>

<div class='box_top'>Staff Members</div>
<div class='box_middle'>
    <div class='pad'>
        <table style="width:100%;">
            <tr>
                <th style="text-align: left;">Avatar</th>
                <th style="text-align: left;">Name</th>
                <th style="text-align: left;">Role</th>
                <th style="text-align: left;">Last Active</th>
                <th style="text-align: left;">Actions</th>
            </tr>

            <?php
            $db->query("SELECT id, avatar, lastactive, admin, gm, fm, pg, eo, st FROM grpgusers WHERE admin + gm + fm + pg + eo + st > 0 ORDER BY admin DESC, gm DESC, id ASC");
            $db->execute();
            $rows = $db->fetch_row();
            foreach ($rows as $row) {
                $avatar = ($row['avatar']) ? '<img src="' . $row['avatar'] . '" style="width:75px;height:75px;" />' : '';
            ?>
                <tr>
                    <td><?= $avatar ?></td>
                    <td><?= formatName($row['id']) ?></td>
                    <td>
                        <?php
                        if ($row['admin'])
                            echo '<span style="color:red;">Admin</span>';
                        elseif ($row['gm'])
                            echo '<span style="color:yellow;">Game Moderator</span>';
                        elseif ($row['fm'])
                            echo 'Forum Moderator';
                        elseif ($row['pg'])
                            echo '<span style="color:cyan;">Player Guide</span>';
                        elseif ($row['eo'])
                            echo 'Entertainer';
                        ?>
                    </td>
                    <td><?= howlongago($row['lastactive']) ?></td>
                    <td><a href="pms.php?view=new&to=<?= $row['id'] ?>">[Mail]</a></td>
                </tr>
            <?php } ?>
        </table>

<?php include 'footer.php'; ?>
