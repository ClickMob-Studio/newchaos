<?php
if ($user_class->crew != 0) {
    $crew_class = new crew($user_class->crew);
    ?>
    
    <div class='contenthead floaty'>
    
   

    <div class='profile-container' style='display: flex; justify-content: space-around; align-items: flex-start;'>
        <!-- Left Profile Box -->
        <div class='profile-package' style='flex: 1; padding: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.5); margin: 5px;'>


        </div>

        <!-- Right Profile Box -->
        <div class='profile-stats' style='flex: 1; padding: 18px; box-shadow: 0 0 10px rgba(0,0,0,0.5); margin: 5px; background-color: #222;'>
            <table id='profile_table' style='width:100%; color: white;'>

                <!-- Existing Stats -->
                
            </table>
        </div>
    </div>
    
    <tr><td class="contentcontent">
            <hr />
            <table id="newtables" class="linkstable" style="width:100%;table-layout:fixed;">
                <tr>
                    <th colspan="4">crew Links</td>
                </tr>
                <tr>
                    <td class"tdHover"><a href="crewdetails.php">crew Details</a></td>
                    <td><a href="attlog.php">Attack Log</a></td>
                    <td><a href="deflog.php">Defense Log</a></td>
                    <td><a href="vlog.php">Vault Log</a></td>
                </tr>
                <tr>
                    <td><a href="gcrimelog.php">crew crime log</a></td>
                    <td><a href="crewvault.php">crew Vault</a></td>
                    <td><a href="crewmembers.php">View Members</a></td>
                    <td><a href="#">-</a></td>
                </tr>
                <tr>
                    <td><a href="crewevents.php">crew Events</a></td>
                    <td><a href="crewforum.php">crew Forum</a></td>
                    <td><a href="leavecrew.php">Leave crew</a></td>
                    <td><a href="#">-</a></td>
                </tr>
<tr>
                    <td><a href="#">-</a></td>
                    <td><a href="#">-</a></td>
                    <td><a href="#">-</a></td>
                    <td><a href="#">-</a></td>
                </tr>

            </table>
            <?php
            $user_rank = new CrewRank($user_class->crank);
            if ($user_rank->members == 1 || $user_rank->crime == 1 || $user_rank->vault == 1 || $user_rank->massmail == 1 || $user_rank->applications == 1 || $user_rank->appearance == 1 || $user_rank->ranks == 1 || $user_rank->invite == 1 || $user_rank->upgrade == 1 || $user_rank->crewgrad == 1 || $user_rank->crewwars == 1 || $user_class->admin == 1) {
                ?>
                <table id="newtables" class="linkstable" style="width:100%;table-layout:fixed;">
                    <tr>
                        <th colspan="4">crew Management</th>
                    </tr>
                    <tr>
                        <?php
                        echo
                        ($user_rank->invite == 1 || $user_class->admin) ? "<td><a href='invite.php'>Invite Mobster</a></td>" : "<td></td>",
                        ($user_rank->applications == 1 || $user_class->admin) ? "<td><a href='manageapps.php'>crew Applications</a></td>" : "<td></td>",
                        ($user_rank->appearance == 1 || $user_class->admin) ? "<td><a href='editcrew.php'>Edit crew</a></td>" : "<td></td>",
                        ($user_rank->members == 1 || $user_class->admin) ? "<td><a href='managecrew.php'>Manage Members</a></td>" : "<td></td>",
                        "</tr><tr>",
                        ($user_rank->crewwars == 1 || $user_class->admin) ? "<td><a href='crewwar.php'>Manage crew Wars</a></td>" : "<td></td>",
                        ($user_rank->crime == 1 || $user_class->admin) ? "<td><a href='crewcrime.php'>Manage crew Crime</a></td>" : "<td></td>",
                        ($user_rank->ranks == 1 || $user_class->admin) ? "<td><a href='manageranks.php'>Rank Management</a></td>" : "<td></td>",
                        ($user_rank->vault == 1 || $user_class->admin) ? "<td><a href='managecrewvault.php'>Manage Vault</a></td>" : "<td></td>",
                        "</tr><tr>",
                        ($crew_class->leader == $user_class->id || $user_class->admin) ? "<td><a href='disband.php'>Delete crew</a></td>" : "<td></td>",
                        ($user_rank->houses == 1 || $user_class->admin) ? "<td><a href='crewhouse.php'>crew Housing</a></td>" : "<td></td>",
                        ($user_rank->upgrade == 1 || $user_class->admin) ? "<td><a href='crewupgrade.php'>Upgrade</a></td>" : "<td></td>",
                        ($user_class->id == $crew_class->leader || $user_class->admin) ? "<td><a href='changeleader.php'>Change Leader</a></td>" : "<td></td>",
                        "</tr><tr>",
                        "<td></td>",
                        ($user_rank->crewgrad == 1 || $user_class->admin) ? "<td><a href='crewgrad.php'>crew Gradient</a></td>" : "<td></td>",
                        "<td><a href='crewmassmail.php'>crew Mass Mail</a></td>";
                        ?>
                        <td></td>
                    </tr>
            </td></tr>
        </table></table>
        <?php
    }
}
?>