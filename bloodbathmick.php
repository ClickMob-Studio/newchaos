<?php
include 'header.php';
$rankcolours = array(
    'FFD700',
    'C0C0C0',
    'CD7F32'
);
$loop = array(
    'level' => array(
        10000,
        6000,
        4000
    ),
    'crimes' => array(
       4000,
        2000,
        1000
    ),
    'referrals' => array(
        15000,
        10000,
        5000
    ),
    'attacks won' => array(
        4000,
        2000,
        1000
    ),
    'attacks lost' => array(
        4000,
        2000,
        1000
    ),
    'defend won' => array(
        4000,
        2000,
       1000
    ),
    'defend lost' => array(
        4000,
       2000,
        1000
    ),
    'busts' => array(
        4000,
        2000,
        1000
    ),
    'mugs' => array(
        4000,
        2000,
        1000
    )
);
$nor = 5; // number of ranks to be shown per category
?>
<h3>The Bloodbath</h3>
<hr>
<div class='contentcontent'>
    <span style='color:red;'><center>Welcome to bloodbath, Bloodbath allows you the chance to gain some extra points for your hard work!</span><br />
    <span style='color:yellow;'><center>Note: Payments will be processed manually.</span><br />
    <br>
    <center>
<font size="3px">        <?php
        $bb = mysql_fetch_array(mysql_query("SELECT endtime FROM bloodbath ORDER BY endtime DESC LIMIT 1"));
        echo ' Bloodbath is Over payments will be sent soon!</font></center><tr>';
        foreach ($loop as $lop => $prizes) {
            ?>
            <table id="newtables" style="width:100%;table-layout:fixed;margin-top:20px">
                <tr><th colspan='4' style="font-size:1.1em;"><?php
                        echo ucfirst(str_replace('crimes', 'Nerve used on Crimes divided by Level (Nerve/Level)', $lop));
                        ?></td></tr>
                <tr>
                    <th><b>Rank</b></td>
                    <th><b>Username</b></td>
                    <th><b><?php
                            echo ucfirst($lop);
                            ?></b></td>
                    <th><b>Reward</b></td>
                </tr>
                <?php
                $lol = str_replace(' ', '', $lop);
                $result = mysql_query("SELECT b.* FROM bbusers b LEFT JOIN grpgusers g ON userid = id WHERE b.$lol <> 0 AND lastactive > unix_timestamp() - (86400 * 7) ORDER BY b.$lol DESC LIMIT $nor");
                $rank = 0;
                while ($line = mysql_fetch_array($result)) {
                    $rank++;
                    echo '<tr><td width="10%">';
                    echo (!empty($rankcolours[$rank - 1])) ? "<span style='font-weight:bold;color:#{$rankcolours[$rank - 1]}'>$rank" : $rank;
                    if ($rank == 1)
                        echo "st";
                    elseif ($rank == 2)
                        echo "nd";
                    elseif ($rank == 3)
                        echo "rd";
                    else
                        echo "th";
                    echo (!empty($rankcolours[$rank - 1])) ? "</span></td>" : "</td>";
                    echo '<td width="40%">' . formatName($line['userid']) . '</td><td width="25%">' . prettynum($line["$lol"]) . ' ' . ucfirst(str_replace('crimes', 'points', $lop)) . '</td><td>';
                    echo (!empty($prizes[$rank - 1])) ? "<span style='font-weight:bold;color:#{$rankcolours[$rank - 1]}'>" . number_format($prizes[$rank - 1]) . " Points" : "-";
                    echo '</td></tr>';
                }
                echo '</table>';
            }
            include 'footer.php';
            ?>