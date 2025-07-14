<?php
include 'header.php';
$rankcolours = array(
    'FFD700',
    'C0C0C0',
    'CD7F32'
);
$loop = array(
    'level' => array(
        20000,
        10000,
        5000
    ),
    'crimes' => array(
        20000,
        10000,
        5000
    ),
    'referrals' => array(
        20000,
        10000,
        5000
    ),
    'attacks won' => array(
        20000,
        10000,
        5000
    ),
    'attacks lost' => array(
        20000,
        10000,
        5000
    ),
    'defend won' => array(
        20000,
        10000,
        5000
    ),
    'defend lost' => array(
        20000,
        10000,
        5000
    ),
    'busts' => array(
        20000,
        10000,
        5000
    ),
    'mugs' => array(
        20000,
        10000,
        5000
    )
);
$nor = 3; // number of ranks to be shown per category
?>
<script>
    setInterval(() => {
        $.post('ajax_bb.php', (data) => {
            $("#ajax_bb").html(data);
        })
    }, 1000);
</script>


<div class="box_top">Bloodbath</div>
<div class="box_middle">
    <div class="pad">
        <table id="newtables" style="width:100%;">
            <span style='color:red;'>
                <center>Welcome to bloodbath, Bloodbath allows you the chance to gain some extra points for your hard
                    work!
            </span><br />

            <br>
            <center>
                <font size="3px"> <?php
                $db->query("SELECT endtime FROM bloodbath ORDER BY endtime DESC LIMIT 1");
                $db->execute();
                $bb = $db->fetch_single();
                echo 'Bloodbath will end in <span id="ajax_bb">' . howlongtil($bb) . '</span></font></center><tr>';

                $db->query("SELECT b.*, g.dprivacy FROM bbusers b LEFT JOIN grpgusers g ON userid = id WHERE b.donator <> 0 AND lastactive > unix_timestamp() - (86400 * 7) ORDER BY b.donator DESC LIMIT $nor");
                $db->execute();
                $donators = $db->fetch_row();
                $donate_prizes = array(30, 20, 10);

                echo '<table id="newtables" style="width:100%;table-layout:fixed;margin-top:20px">
             <tr>
                 <th colspan="3" style="font-size:1.1em;">Donations</th>
             </tr>
             <tr>
                 <th><b>Rank</b></td>
                 <th><b>Username</b></td>
                 <th><b>Reward<br>(credits of total donation)</b></td>
             </tr>';

                $rank = 0;
                foreach ($donators as $line) {
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

                    if ($line['userid'] == $user_class->id) {
                        $name = formatName($line['userid']);
                        if ($line['dprivacy'] == 1) {
                            $name .= ' (HIDDEN)';
                        }
                    } else {
                        $name = ($line['dprivacy'] == 1) ? 'Anonymous' : formatName($line['userid']);
                        if ($user_class->admin > 0) {
                            $name .= ' (' . formatName($line['userid']) . ')';
                        }
                    }

                    echo (!empty($rankcolours[$rank - 1])) ? "</span></td>" : "</td>";
                    echo '<td width="40%">' . $name . '</td>';
                    echo (!empty($donate_prizes[$rank - 1])) ? "<td><span style='font-weight:bold;color:#{$rankcolours[$rank - 1]}'>" . number_format($donate_prizes[$rank - 1]) . "%" : "-";
                    echo '</td></tr>';
                }
                echo '</table>';

                foreach ($loop as $lop => $prizes) {
                    ?>
                        <table id="newtables" style="width:100%;table-layout:fixed;margin-top:20px">
                            <tr>
                                <th colspan='4' style="font-size:1.1em;"><?php
                                echo ucfirst(str_replace('crimes', 'Nerve used on Crimes divided by Level (Nerve/Level)', $lop));
                                ?></td>
                            </tr>
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

                            $db->query("SELECT b.* FROM bbusers b LEFT JOIN grpgusers g ON userid = id WHERE b.$lol <> 0 AND lastactive > unix_timestamp() - (86400 * 7) AND g.admin = 0 ORDER BY b.$lol DESC LIMIT $nor");
                            $db->execute();
                            $result = $db->fetch_row();

                            $rank = 0;
                            $top = array();
                            foreach ($result as $line) {
                                $top[] = $line['userid'];
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
                            if ($user_class->id == 682) {
                                if (!in_array($user_class->id, $top)) {
                                    $db->query("SELECT userid, `$lol`, FIND_IN_SET( `$lol`, (SELECT GROUP_CONCAT(DISTINCT `$lol` ORDER BY `$lol` DESC) FROM bbusers) AS rank FROM bbusers WHERE userid = 65");
                                    $db->execute();
                                    $ranking = $db->fetch_row(true);
                                    $rank = $ranking['rank'];

                                    if ($ranking["$lol"] > 0) {
                                        echo '<tr>
                            <td>' . ordinal($rank) . '</td>
                            <td>' . formatName($user_class->id) . '</td>
                            <td>' . prettynum($ranking["$lol"]) . ' ' . ucfirst(str_replace('crimes', 'points', $lop)) . '</td>
                            <td>-</td>
                        </tr>';
                                    }
                                }
                            }
                            unset($top);
                            echo '</table>';
                }
                ?>
    </div>

</div>
<?php
include 'footer.php';
?>