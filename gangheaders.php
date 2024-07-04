<?php
if ($user_class->gang != 0) {
    $gang_class = new Gang($user_class->gang);
?>
<style>
    .btn-info, .btn-primary {
        color: #fff !important; 
    font: 1.4rem 'Montserrat', sans-serif !important;
    padding: 15px 0;
    width: 130px;
    margin: 0 15px;
    text-transform: uppercase;
    background: #000000c4;
    display: inline-block;
    text-decoration: none;
    border: solid var(--colorHighlight) 1px;
    transition: background 0.5s, transform 0.5s;
    }
    .btn-secondary{
        padding: 15px 0;
    width: 130px;
    display: inline-block;
    margin: 0 15px;
    color: #fff !important; 
    font: 1.4rem 'Montserrat', sans-serif !important;
    }
.col{
    padding-bottom:5px;
}
</style>
    <div class="container mt-3">
        <h4>Gang Links</h4>
        <div class="row row-cols-2 row-cols-md-4 g-4">
            <div class="col"><a href="gangdetails.php" class="btn btn-primary w-100">Gang Details</a></div>
            <div class="col"><a href="attlog.php" class="btn btn-primary w-100">Attack Log</a></div>
            <div class="col"><a href="deflog.php" class="btn btn-primary w-100">Defense Log</a></div>
            <div class="col"><a href="vlog.php" class="btn btn-primary w-100">Vault Log</a></div>
        </div>
        <div class="row row-cols-2 row-cols-md-4 g-4">
            <div class="col"><a href="gcrimelog.php" class="btn btn-primary w-100">Gang Crime Log</a></div>
            <div class="col"><a href="gangvault.php" class="btn btn-primary w-100">Gang Vault</a></div>
            <div class="col"><a href="gangmembers.php" class="btn btn-primary w-100">View Members</a></div>
            <div class="col"><a href="viewwar.php" class="btn btn-primary w-100">Gang Wars</a></div>
        </div>
        <div class="row row-cols-2 row-cols-md-4 g-4">
            <div class="col"><a href="gangevents.php" class="btn btn-primary w-100">Gang Events</a></div>
            <div class="col"><a href="gangforum.php" class="btn btn-primary w-100">Gang Forum</a></div>
            <div class="col"><a href="leavegang.php" class="btn btn-primary w-100">Leave Gang</a></div>
            <div class="col"><a href="gangcontest.php" class="btn btn-primary w-100">Gang Contest</a></div>
        </div>
        <div class="row row-cols-2 row-cols-md-4 g-4">
            <div class="col"><a href="giftgang.php" class="btn btn-primary w-100">Gift Gang</a></div>
            <div class="col"><a href="gangmail.php" class="btn btn-primary w-100">Gang Mail</a></div>
            <div class="col"><a href="gang_territories.php" class="btn btn-primary w-100">Protection Rackets</a></div>
            <div class="col"><div class="btn btn-secondary w-100 disabled">-</div></div>
        </div>

        <?php
        $user_rank = new GangRank($user_class->grank);
        if ($user_rank->members == 1 || $user_rank->crime == 1 || $user_rank->vault == 1 || $user_rank->massmail == 1 || $user_rank->applications == 1 || $user_rank->appearance == 1 || $user_rank->ranks == 1 || $user_rank->invite == 1 || $user_rank->upgrade == 1 || $user_rank->ganggrad == 1 || $user_rank->gangwars == 1 || $gang_class->leader == $user_class->id || $user_class->admin == 1) {
            ?>
            <h4 class="mt-4">Gang Management</h4>
            <div class="row row-cols-2 row-cols-md-4 g-4">
                <div class="col"><?php echo ($user_rank->invite == 1 || $user_class->admin || $user_class->id == $gang_class->leader) ? "<a href='invite.php' class='btn btn-info w-100'>Invite Mobster</a>" : "<div class='btn btn-secondary w-100 disabled'>-</div>"; ?></div>
                <div class="col"><?php echo ($user_rank->applications == 1 || $user_class->admin || $user_class->id == $gang_class->leader) ? "<a href='manageapps.php' class='btn btn-info w-100'>Gang Applications</a>" : "<div class='btn btn-secondary w-100 disabled'>-</div>"; ?></div>
                <div class="col"><?php echo ($user_rank->appearance == 1 || $user_class->admin || $user_class->id == $gang_class->leader) ? "<a href='editgang.php' class='btn btn-info w-100'>Edit Gang</a>" : "<div class='btn btn-secondary w-100 disabled'>-</div>"; ?></div>
                <div class="col"><?php echo ($user_rank->members == 1 || $user_class->admin || $user_class->id == $gang_class->leader) ? "<a href='managegang.php' class='btn btn-info w-100'>Manage Members</a>" : "<div class='btn btn-secondary w-100 disabled'>-</div>"; ?></div>
            </div>
            <div class="row row-cols-2 row-cols-md-4 g-4">
                <div class="col"><?php echo ($user_rank->gangwars == 1 || $user_class->admin || $user_class->id == $gang_class->leader) ? "<a href='gangwar.php' class='btn btn-info w-100'>Manage Gang Wars</a>" : "<div class='btn btn-secondary w-100 disabled'>-</div>"; ?></div>
                <div class="col"><?php echo ($user_rank->crime == 1 || $user_class->admin || $user_class->id == $gang_class->leader) ? "<a href='gangcrime.php' class='btn btn-info w-100'>Manage Gang Crime</a>" : "<div class='btn btn-secondary w-100 disabled'>-</div>"; ?></div>
                <div class="col"><?php echo ($user_rank->ranks == 1 || $user_class->admin || $user_class->id == $gang_class->leader) ? "<a href='manageranks.php' class='btn btn-info w-100'>Rank Management</a>" : "<div class='btn btn-secondary w-100 disabled'>-</div>"; ?></div>
                <div class="col"><?php echo ($user_rank->vault == 1 || $user_class->admin || $user_class->id == $gang_class->leader) ? "<a href='managegangvault.php' class='btn btn-info w-100'>Manage Vault</a>" : "<div class='btn btn-secondary w-100 disabled'>-</div>"; ?></div>
            </div>
            <div class="row row-cols-2 row-cols-md-4 g-4">
                <div class="col"><?php echo ($gang_class->leader == $user_class->id || $user_class->admin || $user_class->id == $gang_class->leader) ? "<a href='disband.php' class='btn btn-primary w-100'>Delete Gang</a>" : "<div class='btn btn-secondary w-100 disabled'>-</div>"; ?></div>
                <div class="col"><?php echo ($user_rank->houses == 1 || $user_class->admin || $user_class->id == $gang_class->leader) ? "<a href='ganghouse.php' class='btn btn-info w-100'>Gang Housing</a>" : "<div class='btn btn-secondary w-100 disabled'>-</div>"; ?></div>
                <div class="col"><?php echo ($user_rank->upgrade == 1 || $user_class->admin || $user_class->id == $gang_class->leader) ? "<a href='gangupgrade.php' class='btn btn-info w-100'>Upgrade</a>" : "<div class='btn btn-secondary w-100 disabled'>-</div>"; ?></div>
                <div class="col"><?php echo ($user_rank->upgrade == 1 || $user_class->admin || $user_class->id == $gang_class->leader) ? "<a href='pointsupgrades.php' class='btn btn-info w-100'>Points Upgrades</a>" : "<div class='btn btn-secondary w-100 disabled'>-</div>"; ?></div>
            </div>
            <div class="row row-cols-2 row-cols-md-4 g-4">
                <div class="col"></div> <!-- Placeholder for alignment -->
                <div class="col"><?php echo ($user_rank->ganggrad == 1 || $user_class->admin || $user_class->id == $gang_class->leader) ? "<a href='ganggrad.php' class='btn btn-info w-100'>Gang Gradient</a>" : "<div class='btn btn-secondary w-100 disabled'>-</div>"; ?></div>
                <div class="col"><?php echo ($user_class->id == $gang_class->leader || $user_class->admin) ? "<a href='changeleader.php' class='btn btn-info w-100'>Change Leader</a>" : "<div class='btn btn-secondary w-100 disabled'>-</div>"; ?></div>
                <div class="col"><a href='gangmassmail.php' class='btn btn-info w-100'>Gang Mass Mail</a></div>
            </div>
        </div>
            <?php
        }
    }
?>
