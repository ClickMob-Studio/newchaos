<?php

include_once "header.php";
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if ($user_class->jail > 0) {
    diefun("You cannot access this page while in jail");
}


// Fetch classified crime data for the user
$crime_data = null;
$db->query("SELECT * FROM cc WHERE leader = :user_id OR wmaster = :user_id OR emaster = :user_id OR gdriver = :user_id");
$db->bind(':user_id', $user_class->id);
$result = $db->fetch_row(true);

if ($result) {
    $crime_data = $result;
}

// Display the content based on whether the user is involved in a classified crime
?>
<div class="container my-5">
    <?php if (!$crime_data) { // User is not involved in a classified crime ?>
        <h2 class="text-center">Starting Classified Crimes</h2>
        <p class="text-center">TOTAL LOOT: <strong>£<?= htmlspecialchars(number_format(0)) ?></strong></p> <!-- Placeholder -->

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

    <?php } else { // User is involved in a classified crime ?>
        <h2 class="text-center">Classified Crime Team</h2>
        <table class="table table-dark table-striped">
            <thead>
                <tr>
                    <th scope="col">Position</th>
                    <th scope="col">Username</th>
                    <th scope="col">Equipment</th>
                    <th scope="col">Rank</th>
                    <th scope="col">Percent</th>
                    <?php if ($crime_data['leader'] == $user_class->id) { echo "<th scope='col'>Action</th>"; } ?>
                </tr>
            </thead>
            <tbody>
                <?php
                // Define roles and their corresponding fields in `cc` table
                $roles = [
                    'CC Leader' => ['user_id' => $crime_data['leader'], 'perc' => $crime_data['leaderperc']],
                    'Weapons Master' => ['user_id' => $crime_data['wmaster'], 'equipment' => $crime_data['weapons'], 'perc' => $crime_data['wmasterperc']],
                    'Explosion Master' => ['user_id' => $crime_data['emaster'], 'equipment' => $crime_data['explosives'], 'perc' => $crime_data['emasterperc']],
                    'Getaway Driver' => ['user_id' => $crime_data['gdriver'], 'equipment' => $crime_data['car'], 'perc' => $crime_data['driverperc']]
                ];

                // Display each role in the classified crime
                foreach ($roles as $role => $data) {
                    if ($data['user_id']) {
                        $db->query("SELECT username, rank FROM accounts WHERE id = :user_id");
                        $db->bind(':user_id', $data['user_id']);
                        $user_info = $db->fetch_row(true);
                        ?>
                        <tr>
                            <td><?= $role ?></td>
                            <td><a href="profile.php?viewing=<?= htmlspecialchars($user_info['username']) ?>" class="text-light"><?= htmlspecialchars($user_info['username']) ?></a></td>
                            <td><?= htmlspecialchars($data['equipment'] ?? 'Not Available') ?></td>
                            <td><?= htmlspecialchars($user_info['rank']) ?></td>
                            <td><?= htmlspecialchars($data['perc']) ?>%</td>
                            <?php if ($crime_data['leader'] == $user_class->id) { echo "<td><input type='checkbox' name='kick[]' value='{$data['user_id']}'></td>"; } ?>
                        </tr>
                        <?php
                    }
                }
                ?>
            </tbody>
        </table>

        <form action="" method="post">
            <button type="submit" name="leave" class="btn btn-danger w-100">Leave Classified Crime</button>
        </form>

        <?php if ($crime_data['leader'] == $user_class->id) { ?>
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
