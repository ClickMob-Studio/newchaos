<?php
include 'header.php';
?>

<div class='box_top'>Pet HOF</div>
<div class='box_middle'>
    <div class='pad'>
        <?php
        $valid = array(
            'level',
            'str',
            'def',
            'spe',
            'tot',
            'attacksWon',
            'attacksLost'
        );
        $_GET['view'] = isset($_GET['view']) && in_array($_GET['view'], $valid) ? strtolower($_GET['view']) : 'level';
        print '
    <form method="get">
        <select name="view" onchange="this.form.submit()">
            <option value="level">Select...</option>
            <option value="level">Level</option>
            <option value="str">Strength</option>
            <option value="def">Defense</option>
            <option value="spe">Speed</option>
            <option value="tot">Total Stats</option>
            <option value="attacksWon">Kills</option>
            <option value="attacksLost">Deaths</option>
        </select>
    </form>
    <br />
    <table id="newtables" style="width:100%;">
        <tr>
            <th colspan="5">Pet HOF</th>
        </tr>
        <tr>
            <th width="5%">Rank</th>
            <th width="25%">Owner</th>
            <th width="10%">Pet Level</th>
            <th width="30%">Pet Name</th>
            <th width="30%">Pet Type</th>
        </tr>
';
        $extra = (isset($_GET['view']) && $_GET['view'] == 'level') ? ', exp DESC' : '';
        if (isset($_GET['view']) && $_GET['view'] == 'tot') {
            $_GET['view'] = "str+def+spe";
        }

        $db->query("SELECT * FROM pets p WHERE NOT EXISTS(SELECT * FROM bans b WHERE b.id = p.userid AND type IN ('perm','freeze')) ORDER BY {$_GET['view']} DESC $extra LIMIT 30");
        $db->execute();
        $rows = $db->fetch_row();
        $cnt = 0;
        foreach ($rows as $row) {
            $petinfo = new Pet($row['userid']);

            $db->query("SELECT name FROM petshop WHERE id = ?");
            $db->execute([$row['petid']]);
            $pi = $db->fetch_row(true);
            $type = !empty($pi) ? $pi['name'] : 'Unknown';
            echo "
        <tr>
            <td>" . ++$cnt . "</td>
            <td>" . formatName($row['userid']) . "</td>
            <td>" . prettynum($row['level']) . "</td>
            <td>" . $petinfo->formatName() . "</td>
            <td>$type</td>
        </tr>
    ";
        }
        ?>
        </table>
        </td>
        </tr><?php
        require_once __DIR__ . '/footer.php';