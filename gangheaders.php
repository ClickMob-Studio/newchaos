<?php
if ($user_class->gang != 0) {
    $gang_class = new Gang($user_class->gang);
    ?>
    <tr><td class="contentcontent">
            <hr />
            <table id="newtables" class="linkstable" style="width:100%;table-layout:fixed;">
                <tr>
                    <th colspan="4">Gang Links</td>
                </tr>
                <tr>
                    <td class"tdHover"><a href="gangdetails.php">Gang Details</a></td>
                    <td><a href="attlog.php">Attack Log</a></td>
                    <td><a href="deflog.php">Defense Log</a></td>
                    <td><a href="vlog.php">Vault Log</a></td>
                </tr>
                <tr>
                    <td><a href="gcrimelog.php">Gang crime log</a></td>
                    <td><a href="gangvault.php">Gang Vault</a></td>
                    <td><a href="gangmembers.php">View Members</a></td>
                    <td><a href="viewwar.php">Gang Wars</a></td>
                </tr>
                <tr>
                    <td><a href="gangevents.php">Gang Events</a></td>
                    <td><a href="gangforum.php">Gang Forum</a></td>
                    <td><a href="leavegang.php">Leave Gang</a></td>
                    <td><a href="gangcontest.php">Gang Contest</a></td>
                </tr>
<tr>
                    <td><a href="giftgang.php">Gift Gang</a></td>
                    <td><a href="gangmail.php">Gang Mail</a></td>
                    <td><a href="leavegang.php">-</a></td>
                    <td><a href="gangcontest.php">-</a></td>
                </tr>

            </table>
            <?php
            $user_rank = new GangRank($user_class->grank);
            if ($user_rank->members == 1 || $user_rank->crime == 1 || $user_rank->vault == 1 || $user_rank->massmail == 1 || $user_rank->applications == 1 || $user_rank->appearance == 1 || $user_rank->ranks == 1 || $user_rank->invite == 1 || $user_rank->upgrade == 1 || $user_rank->ganggrad == 1 || $user_rank->gangwars == 1 || $gang_class->leader == $user_class->id || $user_class->admin == 1) {
                ?>
                <table id="newtables" class="linkstable" style="width:100%;table-layout:fixed;">
                    <tr>
                        <th colspan="4">Gang Management</th>
                    </tr>
                    <tr>
                        <?php
                        echo
                        ($user_rank->invite == 1 || $user_class->admin || $user_class->id == $gang_class->leader) ? "<td><a href='invite.php'>Invite Mobster</a></td>" : "<td></td>",
                        ($user_rank->applications == 1 || $user_class->admin || $user_class->id == $gang_class->leader) ? "<td><a href='manageapps.php'>Gang Applications</a></td>" : "<td></td>",
                        ($user_rank->appearance == 1 || $user_class->admin || $user_class->id == $gang_class->leader) ? "<td><a href='editgang.php'>Edit Gang</a></td>" : "<td></td>",
                        ($user_rank->members == 1 || $user_class->admin || $user_class->id == $gang_class->leader) ? "<td><a href='managegang.php'>Manage Members</a></td>" : "<td></td>",
                        "</tr><tr>",
                        ($user_rank->gangwars == 1 || $user_class->admin || $user_class->id == $gang_class->leader) ? "<td><a href='gangwar.php'>Manage Gang Wars</a></td>" : "<td></td>",
                        ($user_rank->crime == 1 || $user_class->admin || $user_class->id == $gang_class->leader) ? "<td><a href='gangcrime.php'>Manage Gang Crime</a></td>" : "<td></td>",
                        ($user_rank->ranks == 1 || $user_class->admin || $user_class->id == $gang_class->leader) ? "<td><a href='manageranks.php'>Rank Management</a></td>" : "<td></td>",
                        ($user_rank->vault == 1 || $user_class->admin || $user_class->id == $gang_class->leader) ? "<td><a href='managegangvault.php'>Manage Vault</a></td>" : "<td></td>",
                        "</tr><tr>",
                        ($gang_class->leader == $user_class->id || $user_class->admin || $user_class->id == $gang_class->leader) ? "<td><a href='disband.php'>Delete Gang</a></td>" : "<td></td>",
                        ($user_rank->houses == 1 || $user_class->admin || $user_class->id == $gang_class->leader) ? "<td><a href='ganghouse.php'>Gang Housing</a></td>" : "<td></td>",
                        ($user_rank->upgrade == 1 || $user_class->admin || $user_class->id == $gang_class->leader) ? "<td><a href='gangupgrade.php'>Upgrade</a></td>" : "<td></td>",
                         ($user_rank->upgrade == 1 || $user_class->admin || $user_class->id == $gang_class->leader) ? "<td><a href='pointsupgrades.php'>Points Upgrades</a></td>" : "<td></td>",

                        "</tr><tr>",
                        "<td></td>",
                        ($user_rank->ganggrad == 1 || $user_class->admin || $user_class->id == $gang_class->leader) ? "<td><a href='ganggrad.php'>Gang Gradient</a></td>" : "<td></td>",
                                                ($user_class->id == $gang_class->leader || $user_class->admin) ? "<td><a href='changeleader.php'>Change Leader</a></td>" : "<td></td>",
                        "<td><a href='gangmassmail.php'>Gang Mass Mail</a></td>";
                        ?>
                        <td></td>
                    </tr>
            </td></tr>
        </table></table>
        <?php
    }
}
?>
