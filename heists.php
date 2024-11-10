<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once "header.php";

if($user_class0>jail > 0 ){
    diefun("You can not access this page whilst in jail");

}
?>


<div class="container my-5">
    <?php if ($fetch->cc == "0") { ?>
        <h2 class="text-center">Starting Classified Crimes</h2>
        <p class="text-center">TOTAL LOOT: <strong>£<?= htmlspecialchars(number_format($fetch->octotloot)) ?></strong></p>

        <form action="" method="post">
            <div class="mb-3">
                <label for="class" class="form-label">Classified Crime Class</label>
                <select name="class" id="class" class="form-select">
                    <option value="terrorism">Terrorism Act - £500,000</option>
                    <option value="execute">Execution Act - £750,000</option>
                    <option value="assassinate">Assassination Act - £1,250,000</option>
                </select>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <label for="leader" class="form-label">% CC Leader</label>
                    <input type="text" name="leader" id="leader" class="form-control" value="50">
                </div>
                <div class="col-md-6">
                    <label for="weaponsmaster" class="form-label">% Weapon Master</label>
                    <input type="text" name="weaponsmaster" id="weaponsmaster" class="form-control" value="20">
                </div>
                <div class="col-md-6">
                    <label for="explosivesmaster" class="form-label">% Explosion Master</label>
                    <input type="text" name="explosivesmaster" id="explosivesmaster" class="form-control" value="20">
                </div>
                <div class="col-md-6">
                    <label for="getawaydriver" class="form-label">% Getaway Driver</label>
                    <input type="text" name="getawaydriver" id="getawaydriver" class="form-control" value="10">
                </div>
            </div>

            <button type="submit" name="NewCC" class="btn btn-primary mt-4 w-100">Permit Classified Crime</button>
        </form>

    <?php } elseif ($fetch->cc == "1") { ?>
        
        <h2 class="text-center">Classified Crime Team</h2>
        <table class="table table-dark table-striped">
            <thead>
                <tr>
                    <th scope="col">Position</th>
                    <th scope="col">Username</th>
                    <th scope="col">Equipment</th>
                    <th scope="col">Rank</th>
                    <th scope="col">Percent</th>
                    <?php if ($fetch->ccpost == "leader") { echo "<th scope='col'>Action</th>"; } ?>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>CC Leader</td>
                    <td><a href="profile.php?viewing=<?= htmlspecialchars($fetchcc->leader) ?>" class="text-light"><?= htmlspecialchars($fetchcc->leader) ?></a></td>
                    <td>Not Available</td>
                    <td><?= htmlspecialchars($fetchleader->rank) ?></td>
                    <td><?= htmlspecialchars($fetchcc->leaderperc) ?>%</td>
                    <?php if ($fetch->ccpost == "leader") { echo "<td><input type='checkbox' name='kickleader'></td>"; } ?>
                </tr>
                <!-- Repeat for other roles -->
            </tbody>
        </table>

        <form action="" method="post">
            <button type="submit" name="leave" class="btn btn-danger w-100">Leave Classified Crime</button>
        </form>

        <?php if ($fetch->ccpost == "leader") { ?>
            <h4 class="text-center mt-5">Leader Control Panel</h4>
            <form action="" method="post">
                <div class="row g-2">
                    <div class="col-md-4">
                        <label for="weaponsmaster" class="form-label">Weapons Master</label>
                        <input type="text" name="weaponsmaster" id="weaponsmaster" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label for="explosivesmaster" class="form-label">Explosives Master</label>
                        <input type="text" name="explosivesmaster" id="explosivesmaster" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label for="getawaydriver" class="form-label">Getaway Driver</label>
                        <input type="text" name="getawaydriver" id="getawaydriver" class="form-control">
                    </div>
                </div>
                <button type="submit" name="inviteaccounts" class="btn btn-primary mt-3">Invite Accounts</button>
                <button type="submit" name="kick" class="btn btn-warning mt-3">Kick Selected Accounts</button>
                <button type="submit" name="finish" class="btn btn-success mt-3">Complete & Finish</button>
            </form>
        <?php } ?>
    <?php } ?>
</div>
