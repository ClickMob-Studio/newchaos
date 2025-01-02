<?php
exit;
include 'header.php';
include 'includepet.php';
?>

<style>
    .contenthead {

  color: white;
  border-radius: 10px;
  padding: 20px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
  margin-bottom: 20px; /* Adjust as necessary */
}

.floaty {
  /* Your existing .floaty styles */
}

.profile_container {
    margin-top: 14px;
     /* Slightly lighter than #333 for a subtle border */
 /* Dark background */
    border-radius: 10px; /* Rounded corners */
    box-shadow: 0 -4px 8px rgba(0, 0, 0, 0.2); /* Subtle shadow at the top */
    padding: 15px; /* Consistent padding from .floaty */
    color: white; /* Light text */
}


.profile-package, .profile-stats {
  flex: 1;
  padding: 18px;
  box-shadow: 0 0 10px rgba(0,0,0,0.5);
  margin: 5px;
       /* Slightly lighter than #333 for a subtle border */
   /* Slightly different background for contrast */
  border-radius: 10px; /* Rounded corners for the profile boxes */
}
.profile-stats-container {
    display: flex;
    flex-direction: column;
    max-width: 300px; /* Adjust as needed */
    background: #333; /* Dark background */
    padding: 20px;
     /* Slightly lighter than #333 for a subtle border */
    border-radius: 10px; /* Rounded corners */
    color: #fff; /* white text color */
    font-family: 'Arial', sans-serif; /* Modern font */
    margin: 5px;
}
.profile-stats {
    display: grid;
    grid-template-columns: 1fr 1fr; /* Two columns of equal width */
    grid-gap: 10px;
    padding: 18px;
    box-shadow: 0 0 10px rgba(0,0,0,0.5);
    margin: 5px;
    background-color: #444; /* Slightly lighter background for the grid container */
}

.profile-stats div {
    padding: 10px; /* Padding inside each grid item */
}

.profile-stat:last-child {
    margin-bottom: 0;
}

.profile-stat-title {
    font-weight: bold; /* Bold title */
}

.online-status {
    color: #4CAF50; /* Green color for online status */
    font-weight: bold;
}

.last-active {
    color: #aaa; /* Lighter text for last active info */
}

/* Additional styles for icons, arrows, etc. */
.green-arrow {
    color: #76C043; /* Green color for the up arrow */
}

.red-arrow {
    color: #E53E3E; /* Red color for the down arrow */
}

/* Responsive design adjustments if necessary */
@media (max-width: 768px) {
    .profile-stats-container {
        width: 100%;
    }
}
.user-avatar {
  width: 100px;
  height: 100px;
  border-radius: 50%; /* Circular avatar */
  margin-bottom: 10px;
}

/* Adjust the following as needed for responsive design */
@media (max-width: 768px) {
  .profile-container {
    flex-direction: column;
  }

  .profile-package, .profile-stats {
    flex: 0 0 auto; /* Adjust this to change the mobile layout */
    margin: 5px auto; /* Center the boxes on smaller screens */
    width: 90%; /* Adjust width as necessary */
  }
}


.avatar {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  margin-bottom: 10px;
}

.basic_info h2, .basic_info p {
  margin: 0;
  padding: 5px 0;
}

/* Adjust the following as needed for responsive design */
@media (max-width: 768px) {
  .profile_container {
    flex-direction: column;
  }

  .profile_left, .profile_right {
    flex: 0 0 auto;
    padding-right: 0;
    padding-left: 0;
  }
}

.avatar {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  margin-bottom: 10px;
}

.basic_info h2, .basic_info p {
  margin: 0;
  padding: 5px 0;
}

.stats_table {
  width: 100%;
  margin-left: 20px;
}

.stats_table th, .stats_table td {
  padding: 5px;
  text-align: left;
}

@media (max-width: 768px) {
  .profile_flex_container {
    flex-direction: column;
  }

  .profile_left, .profile_right {
    width: 100%;
    padding: 10px 0;
  }

  .stats_table {
    margin-left: 0;
  }
}

.stats_table th, .stats_table td {
  padding: 5px;
  text-align: left;
}

.stats_table th {
  width: 30%; /* Adjust as necessary */
}

/* Responsive design adjustments */
@media (max-width: 768px) {
  .profile_flex_container {
    flex-direction: column;
  }

  .profile_left, .profile_right {
    width: 100%;
    padding: 10px 0; /* Adjust padding for mobile */
  }

  .stats_table {
    margin-left: 0; /* Adjust table margin for mobile */
  }
}

.actions_grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); /* Smaller button width */
    gap: 8px; /* Adjust the gap between grid items if needed */
    padding: 0; /* Remove padding if necessary */
}

.action {
    background: var(--colorHighlight);
    color: #fff !important; /* Example text color */
    padding: 5px 10px; /* Reduced padding for smaller height, but maintain horizontal padding for comfort */
    text-align: center;
    border-radius: 4px; /* Rounded corners */
    text-decoration: none; /* Removes underline from links */
    font-size: 0.8em; /* Smaller font size */
    display: block; /* Ensure it takes up the full grid cell */
}

.action:hover {
    background: #444; /* Example hover background color change */
    /* Add other hover effects as necessary */

     .profile_comment {
     /* Replace with your desired background color */
        color: #fff; /* This is for the text color */
        border-radius: 4px; /* Adjust as necessary for rounded corners */
        padding: 10px; /* Add some padding inside the comments */
        margin-bottom: 10px; /* Adds space between the comments */
    }
    .profile_comment_user {
        font-weight: bold; /* Make the user's name bold */
    }
    .profile_comment_message {
        margin-top: 5px; /* Space between the user's name and their message */
    }
    /* If you want to style the delete button [x] differently: */
    .profile_comment_user a {
        color: red; /* or any other color */
        margin-left: 5px; /* Space out the delete button a bit */
    }


        </style>
    <div class='box_top'>My Pets</div>
    <div class='box_middle'>
        <div class='pad'>
<?php
$_GET['pet'] = isset($_GET['pet']) && ctype_digit($_GET['pet']) ? $_GET['pet'] : null;
$q = mysql_query("SELECT userid FROM petmarket WHERE userid = $user_class->id");
if (mysql_num_rows($q))
    diefun("Your pet is on the market");
$q = mysql_query("SELECT petid FROM pets WHERE userid = $user_class->id");
if (!mysql_num_rows($q))
    header('location: petshop.php');
if (isset($_GET['name']) && $_GET['name'] == 'change' && !empty($_GET['pet'])) {
    $q = mysql_query("SELECT pname FROM pets WHERE userid = $user_class->id AND petid = {$_GET['pet']}");
    if (!mysql_num_rows($q))
        diefun("Either that pet doesn't exist, or it's not yours");
    $name = mysql_result($q, 0, 0);
    if (array_key_exists('name', $_POST)) {
        $_POST['name'] = isset($_POST['name']) ? trim($_POST['name']) : null;
        if (empty($_POST['name']))
            diefun("You didn't enter a valid name");
        if (strlen($_POST['name']) < 3)
            diefun("Your pet's name must be at least 3 characters");
        if (strlen($_POST['name']) > 10)
            diefun("Your pet's name can be no longer than 10 characters");
        mysql_query("UPDATE pets SET pname = '{$_POST['name']}' WHERE userid = $user_class->id AND petid = {$_GET['pet']}");
        echo Message("You've changed your pet's name");
    } elseif (array_key_exists('cn', $_POST)) {
        if (strlen($_POST['startcolor']) != 6)
            diefun("Error.");
        if (strlen($_POST['endcolor']) != 6)
            diefun("Error.");
        $colors[] = $_POST['startcolor'];
        $colors[] = $_POST['endcolor'];
        mysql_query("UPDATE pets SET coloredname = '" . implode("|", $colors) . "' WHERE userid = $user_class->id AND petid = {$_GET['pet']}");
        echo Message("You've changed your pet's gradient name.");
    } elseif (array_key_exists('buycolor', $_POST)) {
        if ($user_class->credits < 3)
            diefun("You do not have enough credits.");
        $colors[] = "FF0000";
        $colors[] = "FF0000";
        mysql_query("UPDATE pets SET coloredname = '" . implode("|", $colors) . "' WHERE userid = $user_class->id AND petid = {$_GET['pet']}");
        mysql_query("UPDATE grpgusers SET credits = credits - 3 WHERE id = $user_class->id");
        echo Message("You've added a colored name to your pet.");
    } else {
        $petinfo = new Pet($user_class->id);
        print"<form action='mypets.php?pet={$_GET['pet']}&amp;name=change' method='post'>
			<strong>New Name:</strong> <input type='text' name='name' placeholder='$name' /><br />
			<input type='submit' name='submit' value='Change Pet Name' />
		</form>";
        if ($petinfo->coloredname != "FFFFFF|FFFFFF") {
            $colors = explode("|", $petinfo->coloredname);
            print"
                <script type='text/javascript' src='js/cp/jscolor.js'></script>
                <form method='post'>
                    <table id='newtables' style='width:100%;'>
                        <tr>
                            <th colspan='4'>Choose your pet's Gradient Name</td>
                        </tr>
                        <tr>
                            <th>Starting Colour</th>
                            <td><input type='text' class='color' value='{$colors[0]}' name='startcolor'></td>
                            <th>Ending Colour</th>
                            <td><input type='text' class='color' value='{$colors[1]}' name='endcolor'></td>
                        </tr>
                        <tr>
                            <td colspan='4' class='center'><input type='submit' name='cn' value='Save Preferences' /></td>
                        </tr>
                    </table>
                </form>
            ";
        } else {
            print"<br /><br />
                <form method='post'>
                    <input type='submit' name='buycolor' value='Buy Pet Colored Name (3 Credits)' /> (One time buy)
                </form>
            <br />";
        }
    }
}
if (array_key_exists('avi', $_POST)) {
    $avi = $_POST['avi'];
    if(!getimagesize($avi) && $avi != '')
        diefun("Invalid image detected.");
    if($avi == ''){
        $db->query("SELECT picture FROM petshop ps JOIN pets p ON ps.id = p.petid WHERE userid = ?");
        $db->execute(array(
            $user_class->id
        ));
        $avi = $db->fetch_single();
    }
    $db->query("UPDATE pets SET avi = ? WHERE userid = ?");
    $db->execute(array(
        $avi,
        $user_class->id
    ));
    mysql_query("UPDATE pets SET avi = '" . implode("|", $colors) . "' WHERE userid = $user_class->id AND petid = {$_GET['pet']}");
    echo Message("You've changed your pet's avatar.");
} elseif(isset($_GET['avi'])){
    $petinfo = new Pet($user_class->id);
    print"<form action='mypets.php' method='post'>
			<strong>New Avatar:</strong> <input type='text' name='avi' placeholder='$petinfo->avi' /><br />
			<input type='submit' name='submit' value='Change Pet Avatar' />
		</form>";
}
$_GET['use'] = isset($_GET['use']) && ctype_digit($_GET['use']) ? $_GET['use'] : null;
if (!empty($_GET['use'])) {
    $q = mysql_query("SELECT itemname FROM items WHERE id = {$_GET['use']}");
    if (!mysql_num_rows($q))
        diefun("That item doesn't exist");
    $item = mysql_result($q, 0, 0);
    $q = mysql_query("SELECT quantity FROM inventory WHERE userid = $user_class->id AND itemid = {$_GET['use']}");
    if (!mysql_num_rows($q))
        diefun("You don't own that item");
    switch ($_GET['use']) {
        case 14:
            mysql_query("UPDATE grpgusers SET awake = $user_class->maxawake WHERE id = $user_class->id");
            Take_Item($_GET['use'], $user_class->id);
            echo Message("You've popped an awake pill");
            break;
        default:
            diefun("lulwut");
            break;
    }
}
if (isset($_GET['leash']) && !empty($_GET['pet'])) {
    $_GET['leash'] = isset($_GET['leash']) && in_array($_GET['leash'], array(
        0,
        1
    )) ? $_GET['leash'] : 0;
    $q = mysql_query("SELECT * FROM pets WHERE userid = $user_class->id AND petid = {$_GET['pet']}");
    if (!mysql_num_rows($q))
        diefun("Either that pet doesn't exist or it's not yours");
    $row = mysql_fetch_array($q);
    if ($row['leash'] == 1 && $_GET['leash'] == 1)
        diefun("This pet is already leashed.");
    if ($row['leash'] == 0 && $_GET['leash'] == 0)
        diefun("This pet is already unleashed.");
    mysql_query("UPDATE pets SET leash = {$_GET['leash']} WHERE userid = $user_class->id AND petid = {$_GET['pet']}");
    $opts = array(
        0 => 'unleashed',
        1 => 'leashed'
    );
    echo Message("You've {$opts[$_GET['leash']]} your pet");
}

if (isset($_GET['raid_leash']) && !empty($_GET['pet'])) {
    $_GET['raid_leash'] = isset($_GET['raid_leash']) && in_array($_GET['raid_leash'], array(
        0,
        1
    )) ? $_GET['raid_leash'] : 0;
    $q = mysql_query("SELECT * FROM pets WHERE userid = $user_class->id AND petid = {$_GET['pet']}");
    if (!mysql_num_rows($q))
        diefun("Either that pet doesn't exist or it's not yours");
    $row = mysql_fetch_array($q);
    if ($row['raid_leash'] == 1 && $_GET['raid_leash'] == 1)
        diefun("This pet is already joining you in raids.");
    if ($row['raid_leash'] == 0 && $_GET['raid_leash'] == 0)
        diefun("This pet is already not joining you in raids.");
    mysql_query("UPDATE pets SET raid_leash = {$_GET['raid_leash']} WHERE userid = $user_class->id AND petid = {$_GET['pet']}");
    $opts = array(
        0 => 'unleashed for raids',
        1 => 'leashed for raids'
    );
    echo Message("You've {$opts[$_GET['leash']]} your pet");
}
print'<script type="text/javascript">
function leash(value,pets) {
	if(value!="")
		window.location="mypets.php?leash="+value+"&pet="+pets;
}

function raidLeash(value,pets) {
	if(value!="")
		window.location="mypets.php?raid_leash="+value+"&pet="+pets;
}
</script>';
$q = mysql_query("SELECT * FROM pets WHERE userid = $user_class->id ORDER BY petid ASC");
while ($row = mysql_fetch_array($q)) {
    $y = mysql_query("SELECT * FROM petshop WHERE id = {$row['petid']}");
    $pet = mysql_fetch_array($y);
    $petinfo = new Pet($user_class->id);
?>

    <div class="container">
        <!-- Use Bootstrap's row and col classes for responsiveness -->
        <div class="row">
            <!-- First Card -->
            <div class="col-md-12 col-12">
                <div class="card" style="margin: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.3) !important; background: rgba(0,0,0,0.2);">
                    <div class="card-body">
                        <div class="profile-container d-flex justify-content-around">
                            <div class='profile-package shadow-sm p-3 mb-5 rounded' style='flex: 1; margin: 5px;'>
                                <div style="text-align: center;">
                                    <img src='<?php echo $petinfo->avi; ?>' class='img-thumbnail' alt='User Avatar' style='width: 100px; height: 100px;'>
                                    <h4><?php echo $petinfo->formatName(); ?> (<?php echo $pet['name'] ?>)</h4>
                                </div>
                                <div class="text-center p-2" style="background-color: #111; color: white;">Actions:</div>
                                <div class="text-center p-2">
                                    <a href='mypets.php?avi' id='botlink'>Change Avatar</a> |
                                    <a href='mypets.php?name=change&pet=<?php echo $row['petid'] ?>' id='botlink'>Change Name</a> |
                                    <a href='loanpet.php' id='botlink'>Loan Pet</a>
                                </div>
                                <div class="text-center p-2" style="background-color: #111; color: white;">Strength:</div>
                                <div class="text-center p-2">
                                    <?php echo prettynum($row['str']) ?>
                                </div>
                                <div class="text-center p-2" style="background-color: #111; color: white;">Defense:</div>
                                <div class="text-center p-2">
                                    <?php echo prettynum($row['def']) ?>
                                </div>
                                <div class="text-center p-2" style="background-color: #111; color: white;">Speed:</div>
                                <div class="text-center p-2">
                                    <?php echo prettynum($row['spe']) ?>
                                </div>
                                <div class="text-center p-2">
                                    <center>
                                        <select name='leash' onchange='javascript:leash(this.value,<?php echo $row['petid'] ?>);'>
                                            <option value='1' <?php if ($row['leash']): ?> selected='selected' <?php endif; ?>>Leash</option>
                                            <option value='0' <?php if (!$row['leash']): ?> selected='selected' <?php endif; ?>>Unleash</option>
                                        </select>
                                        <br />
                                        <select name='raid_leash' onchange='javascript:raidLeash(this.value,<?php echo $row['petid'] ?>);'>
                                            <option value='1' <?php if ($row['raid_leash']): ?> selected='selected' <?php endif; ?>>Leash for Raids</option>
                                            <option value='0' <?php if (!$row['raid_leash']): ?> selected='selected' <?php endif; ?>>Unleash for Raids</option>
                                        </select>
                                    </center>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
}
include 'footer.php';
