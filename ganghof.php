<?php
include 'header.php';
?>
<tr>
    <td class="contentspacer"></td>
</tr>
<tr>
    <td class="contenthead">Crew Hall Of Fame</div>
</tr>
<tr>
    <td class="contentcontent">
        <table width="100%">
            <tr>
                <td align="center">
                    <a href="ganghof.php?view=level">Crew Level</a>&nbsp; | &nbsp;<a
                        href="ganghof.php?view=moneyvault">Vault Money</a>&nbsp; | &nbsp;<a
                        href="ganghof.php?view=pointsvault">Vault Points</a>&nbsp; | &nbsp;<a
                        href="ganghof.php?view=tmstats">Total Member Stats</a>
                </td>
            </tr>
        </table>
        <br />
        <table width='100%'>
            <tr>
                <td><b>Rank</b></td>
                <td><b>Crew</b></td>
                <td><b>Level</b></td>
                <td><b>Leader</b></td>
            </tr>
            <?php
            $view = ($_GET['view'] != "") ? $_GET['view'] : 'level';

            $db->query("SELECT * FROM `gangs` ORDER BY $view DESC LIMIT 50");
            $db->execute();
            $rows = $db->fetch_row();
            $rank = 0;
            foreach ($rows as $line) {
                $rank++;
                $user_hall = new Gang($line['id']);
                $leader_class = new User($user_hall->leader);
                ?>
                <tr>
                    <td><?php echo $rank ?></td>
                    <td><?php echo $user_hall->nobanner ?></td>
                    <td><?php echo $user_hall->level ?></td>
                    <td><?php echo $leader_class->formattedname ?></td>
                </tr>
                <?php
            }
            ?>
    </td>
</tr>
<?php
include 'footer.php';
?>