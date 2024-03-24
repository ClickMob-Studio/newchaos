<?php
include 'header.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$userId = $_SESSION['id'];

// Fetch the ownership_id for the user
$result = mysql_query("SELECT ownership_id FROM OwnedBusinesses WHERE user_id = '$userId' LIMIT 1");

if (!$result || mysql_num_rows($result) == 0) {
    echo "You're not associated with any business.";
    exit;
}

$row = mysql_fetch_assoc($result);
$ownershipId = $row['ownership_id'];

$business_class = new OwnedBusiness($ownershipId);

// Fetch all members of the associated business
$query = "SELECT * FROM grpgusers WHERE current_employer = '$business_class->business_id'";
$result = mysql_query($query);

if (!$result) {
    echo "MySQL Error: " . mysql_error();
    exit;
}

$employees = array();
while($row = mysql_fetch_assoc($result)) {
    $employees[] = $row;
}

// Calculate total daily employee wage
$totalWageQuery = "SELECT SUM(wage) as totalWage FROM grpgusers WHERE current_employer = '$business_class->business_id'";
$totalWageResult = mysql_query($totalWageQuery);
$totalWageData = mysql_fetch_assoc($totalWageResult);
$totalDailyWage = $totalWageData['totalWage'];

$currentTotalWage = $totalDailyWage; // Defined here to avoid the error.

// Check if the business vault has 10 days worth of overall daily salary costs
$canSetWage = ($business_class->vault >= ($totalDailyWage * 10));

// Fetch all roles for this business
$rolesQuery = "SELECT * FROM business_roles WHERE business_id = '$business_class->business_id'";
$rolesResult = mysql_query($rolesQuery);
$roles = array();
while($role = mysql_fetch_assoc($rolesResult)) {
    $roles[$role['role_id']] = $role['role_name'];
}

// Handle wage update
if(isset($_POST['set_wage'])) {
    $userIdToUpdate = (int)$_POST['user_id'];
    $newWage = (float)$_POST['wage'];
    $currentWageForUser = floatval($employees[array_search($userIdToUpdate, array_column($employees, 'id'))]['wage']);
    $newTotalWage = $currentTotalWage - $currentWageForUser + $newWage;

    if(($newTotalWage * 10) <= $business_class->vault) {
        $updateWageQuery = "UPDATE grpgusers SET wage = '$newWage' WHERE id = '$userIdToUpdate'";
        mysql_query($updateWageQuery);

        if (mysql_error()) {
            echo "MySQL Error: " . mysql_error();
            exit;
        } else {
            echo "Wage updated successfully!";
        }
    } else {
        echo "You don't have enough funds in the business vault to set this wage.";
    }
}

// Handle role assignment
if(isset($_POST['assign_role'])) {
    $userIdToAssign = (int)$_POST['user_id'];
    $roleId = (int)$_POST['role'];

    $updateRoleQuery = "UPDATE grpgusers SET business_role_id = '$roleId' WHERE id = '$userIdToAssign'";
    mysql_query($updateRoleQuery);
    
    if (mysql_error()) {
        echo "MySQL Error: " . mysql_error();
        exit;
    } else {
        echo "Role assigned successfully!";
    }
}

// Handle the kick member action
if(isset($_POST['kick_member'])) {
    // ... [The kick member code remains unchanged] ...
}





// Fetch all members of the associated business
$query = "SELECT * FROM grpgusers WHERE current_employer = '$business_class->business_id'";
$result = mysql_query($query);

if (!$result) {
    echo "MySQL Error: " . mysql_error();
    exit;
}

$employees = array();
while($row = mysql_fetch_assoc($result)) {
    $employees[] = $row;
}
?>

<h2>Employee Management</h2>

<p>
    <strong>Total Salary of All Employees:</strong> $<?php echo number_format($totalDailyWage, 0); ?>
</p>
<?php
if(!$canSetWage) {
    echo "<p><strong>Note:</strong> You need at least 10 days worth of total daily salary costs in your business vault to set specific wages.</p>";
}
?>

<table>
    <thead>
        <tr>
            <th>Username</th>
            <th>Current Wage</th>
            <th>Set New Wage</th>
            <th>Role</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($employees as $employee): ?>
        <tr>
            <td><?php echo $employee['username']; ?></td>
            <td>$<?php echo number_format($employee['wage'], 0); ?></td>
            <td>
                <form method="post" action="">
                    <input type="number" name="wage" placeholder="Enter new wage" required <?php if(!$canSetWage) echo 'disabled'; ?>>
                    <input type="hidden" name="user_id" value="<?php echo $employee['id']; ?>">
                    <input type="submit" name="set_wage" value="Set Wage" <?php if(!$canSetWage) echo 'disabled'; ?>>
                </form>
            </td>
            <td>
                <form method="post" action="">
                    <select name="role">
                        <option value="null">No Role</option>
                        <?php foreach($roles as $roleId => $roleName): ?>
                        <option value="<?php echo $roleId; ?>" <?php echo $employee['business_role_id'] == $roleId ? 'selected' : ''; ?>><?php echo $roleName; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="hidden" name="user_id" value="<?php echo $employee['id']; ?>">
                    <input type="submit" name="assign_role" value="Assign Role">
                </form>
            </td>
            <td>
                <form method="post" action="" onsubmit="return confirm('Are you sure you want to kick this member?');">
                    <input type="hidden" name="user_id" value="<?php echo $employee['id']; ?>">
                    <input type="submit" name="kick_member" value="Kick Member">
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
