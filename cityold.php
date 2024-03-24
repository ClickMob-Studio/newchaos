<?php
include 'header.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);
$db->query("SELECT * FROM luckyboxes WHERE playerid = ?");
$db->execute(array(
    $user_class->id
));
$boxesrows = $db->num_rows();
$db->query("SELECT id FROM grpgusers ORDER BY todayskills DESC LIMIT 1");
$db->execute();
$worked = $db->fetch_row(true);
$hitman  = new User($worked['id']);
$db->query("SELECT id FROM `grpgusers` WHERE `admin` = 0 ORDER BY `todaysexp` DESC LIMIT 1");
$db->execute();
$worked2 = $db->fetch_row(true);
$leveler    = new User($worked2['id']);
$db->query("SELECT COUNT(*) FROM fiftyfifty");
$db->execute();
$count5050 = $db->fetch_single();

$db->query("SELECT id FROM `grpgusers` WHERE `admin` = 1");
$db->execute();
$rows = $db->fetch_row();


// Assuming we have a city variable for the current user's city
$current_city = $user_class->city;

// PHP to fetch king's information including avatar
$king_query = mysql_query("SELECT id, username, avatar FROM grpgusers WHERE king = '" . mysql_real_escape_string($current_city) . "' LIMIT 1");
if ($king_query) {
    $king_result = mysql_fetch_assoc($king_query);
} else {
    $king_result = null;
}

// PHP to fetch queen's information including avatar
$queen_query = mysql_query("SELECT id, username, avatar FROM grpgusers WHERE queen = '" . mysql_real_escape_string($current_city) . "' LIMIT 1");
if ($queen_query) {
    $queen_result = mysql_fetch_assoc($queen_query);
} else {
    $queen_result = null;
}



$admin_ids = array_map(function($a) {
    return $a['id'];
}, $rows);


?>
<br>





    <style>
.user-avatar {
    width: 100px; /* Adjust size as needed */
    height: 100px; /* Adjust size as needed */
    border-radius: 50%; /* Makes the image circular */
    object-fit: cover; /* Ensures the image covers the area nicely */
    border: 3px solid gold; /* Optional: adds a border around the avatar */
    box-shadow: 0 0 8px rgba(0,0,0,0.3); /* Optional: adds a shadow for depth */
}
</style>
<style>

 .vip-packages {
    display: flex;
    justify-content: space-around;
    align-items: stretch;
    flex-wrap: wrap;
    gap: 20px; /* Adds space between the packages */
}

.vip-package {
    background-color: #333; /* Dark background */
    color: white; /* Light text */
    padding: 20px;
    border-radius: 10px; /* Rounded corners */
    box-shadow: 0 -4px 8px rgba(0, 0, 0, 0.2); /* Subtle shadow */
    width: 30%; /* Adjust based on preference and screen size */
}

.glowing-text a {
    animation: glow 2s ease-in-out infinite alternate;
    color: red;
}

@keyframes glow {
    0% {
        text-shadow: 0 0 10px gold, 0 0 20px gold, 0 0 30px gold, 0 0 40px gold, 0 0 50px gold, 0 0 60px gold, 0 0 70px gold;
    }
    100% {
        text-shadow: 0 0 20px gold, 0 0 30px gold, 0 0 40px gold, 0 0 50px gold, 0 0 60px gold, 0 0 70px gold, 0 0 80px gold;
    }
}
</style>

 
 <div class='divider'></div>
<style>
.bordered-table {
    background-color: #333131; /* Dark background */
    color: white; /* Light text */
    padding: 20px;
    border-radius: 10px; /* Rounded corners */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Subtle shadow */
}

.bordered-table th, .bordered-table td {
    padding: 10px; /* Adjust padding for cells */
    border-right: 1px solid #5d5757; /* Adds vertical grid lines */
    border-bottom: 1px solid #5d5757; /* Adds horizontal grid lines */
    color: white; /* Ensures text color is white for visibility */
}

.bordered-table th {
    background-color: #333131; /* Slightly darker background for headers */
}


.bordered-table th:last-child, .bordered-table td:last-child {
    border-right: none; /* Removes the right border for the last column to maintain aesthetics */
}

.bordered-table tr:last-child td {
    border-bottom: none; /* Removes the bottom border for the last row to maintain aesthetics */
}

.bordered-table a {
    color: #ffffff; /* Adjust link color for better visibility */
    text-decoration: none;
    display: inline-block;
    padding: 2px;
}

.bordered-table a:hover {
    background-color: #2f2d2d;
    width:100%;
    border-radius: 3px;
}

.categories-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
}

.category-row {
    display: flex;
    justify-content: space-around;
    width: 100%;
}

.flexele.floaty {
    width: 30%; /* Adjust based on your layout */
    margin: 2px;
    /* Additional styling */
}



</style>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Modern City Layout</title>
<style>
body {
    margin: 0;
    padding: 0;
    font-family: 'Arial', sans-serif;
    background-color: #282c34; /* Dark grey background */
    color: #ffffff;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    overflow-x: hidden; /* Prevents horizontal scrolling */
}
.flex-wrapper {
    display: flex;
    flex-direction: column;
    justify-content: flex-start; /* Align items to the top on the main-axis */
    background-image: url('https://storage.googleapis.com/pai-images/102848a81a8f41aaae59b2f669ce23d1.jpeg');
    background-size: cover;
    background-position: center;
    padding: 20px;
    padding-left: 5%; /* Consistent padding on the left */
    padding-right: 5%; /* Consistent padding on the right */
    border-radius: 15px;
    box-shadow: 0 8px 16px rgba(0,0,0,0.5);
    max-width: 90%;
    width: auto; /* Adjust width to fit content */
    gap: 230px;
    margin: 0 auto; /* Center the wrapper on the page */
}

.container, .container-bottom {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 45px; /* Space between columns */
    width: 100%; /* Full width to use all available space */
    justify-content: center; /* Center grid items */
}
.dropdown {
    position: relative;
    width: 250px;
    margin: 0; /* Remove auto margin to allow even spacing */
    background-color: #3C3F41;
    color: white;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    transition: all 0.3s ease;
}
.dropdown-toggle {
    cursor: pointer;
    user-select: none;
    display: block;
    padding: 10px;
    border-radius: 8px;
    text-align: center;
    margin-bottom: 10px;
}
.dropdown-content, .dropdown-content-up {
    display: none;
    position: absolute;
    width: 100%; /* Match parent width */
    background-color: #3C3F41; /* Consistent with the dropdown */
    padding: 10px; /* Reduced padding for less bulkiness */
    border-radius: 8px;
    border: 1px solid rgba(255, 255, 255, 0.2); /* Subtle white border */
    z-index: 1;
    box-sizing: border-box;
}
.dropdown-content {
    top: 100%;
    left: 0;
    transform: translateY(10px);
}
.dropdown-content-up {
    bottom: 100%;
    left: 0;
    transform: translateY(-10px);
}
.dropdown-content a, .dropdown-content-up a {
    color: #dff9fb;
    padding: 8px 12px; /* Adjusted padding for a more tactile feel */
    text-decoration: none;
    display: block;
    transition: background-color 0.3s;
    margin: 3px 0; /* Reduced margin for a tighter look */
    border-radius: 4px; /* Slight rounding of links for visual appeal */
}
.dropdown-content a:hover, .dropdown-content-up a:hover {
    background-color: #4B4E51; /* Darker grey for hover state */
}
.show {
    display: block;
}

</style>
</head>
<body>

<div class="flex-wrapper">
    <div class="container">
        <div class="dropdown">Shopping District
            <div class="dropdown-content">
                <a href='pacts.php'>Crime Family Pacts [NEW]</a>
                <a href='stores.php'>Item Stores</a>
                <a href="pharmacy.php">General Pharmacy</a>
                <a href="itemmarket.php">Item Market</a>
                <a href="pointmarket.php">Points Market</a>
                <a href="creditmarket.php">Credit Market</a>
                <a href='rentalmarket.php'>House Rental Market</a>
            </div>
        </div>
        <div class="dropdown">Gamblers Paradise
            <div class="dropdown-content">
                <a href="casinonew.php">Casino</a>
                <a href="bigdip.php">The Big Dipper</a>
                <a href='lucky_boxes.php'>Lucky Boxes</a>
                <a href='psmuggling.php'>Point Smuggling</a>
                <a href="megasmuggling.php">The Mega Smuggler</a>
                <a href="FruitMachine.php">Fruit Machine</a>
                <a href='thedoors.php'>The Doors</a>
            </div>
        </div>
        <div class="dropdown">Pets Corner
            <div class="dropdown-content">
                <a href='mypets.php'>My Pet</a>
                <a href='petcrime.php'>Pet Crimes</a>
                <a href='petgym.php'>Pet Gym</a>
                <a href='pethouse.php'>Pet House</a>
                <a href='pethof.php'>Pet HOF</a>
                <a href='petmarket.php'>Pet Market</a>
                <a href='pettrack.php'>Pet Track</a>
                <a href='petjail.php'>Pet Pound</a>
            </div>
        </div>
    </div>
    <div class="vip-packages" style="display: flex; justify-content: space-around; align-items: stretch; flex-wrap: wrap;">
    <!-- King of the City -->
    <div class="vip-container" style="display: flex; justify-content: space-around; align-items: flex-start;">
    <!-- King of the City -->
    <div class="vip-package" style="flex: 1; padding: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.5); margin: 5px;">
        <?php if ($king_result): ?>
            <img src="<?php echo htmlspecialchars($king_result['avatar']); ?>" style="width: 100px; height: 100px;" alt="King's Avatar" class="user-avatar">
            <h4>King of <!_-cityname-_!></h4>
            <p><strong><?php echo formatName($king_result['id']); ?></strong></p>
        <?php else: ?>
            <img src="mlordsimages/vacant.png" style="width: 100px; height: 100px;" alt="No King" class="vacant-throne">
            <h4>VACANT</h4>
            <p>King of <!_-cityname-_!></p>
        <?php endif; ?>
        <button type="button" class="challenge-btn">Challenge</button>
        <p style="color: gold; font-weight: bold;">Reign: 50 Days+</p>
    </div>
    <!-- Queen of the City -->
    <div class="vip-package" style="flex: 1; padding: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.5); margin: 5px;">
        <?php if ($queen_result): ?>
            <img src="<?php echo htmlspecialchars($queen_result['avatar']); ?>" style="width: 100px; height: 100px;" alt="Queen's Avatar" class="user-avatar">
            <h4>Queen of <!_-cityname-_!></h4>
            <p><strong><?php echo formatName($queen_result['id']); ?></strong></p>
        <?php else: ?>
            <img src="mlordsimages/vacant.png" style="width: 100px; height: 100px;" alt="No Queen" class="vacant-throne">
            <h4>VACANT</h4>
            <p>Queen of <!_-cityname-_!></p>
        <?php endif; ?>
        <button type="button" class="challenge-btn">Challenge</button>
        <p style="color: gold; font-weight: bold;">Reign: 50 Days+</p>
    </div>
</div>

    <div class="container-bottom">
        <div class="dropdown">Statistics Dept
            <div class="dropdown-content-up">
                <a href='halloffame.php'>Hall of Fame</a>
                <a href='otds.php'>Daily HOF</a>
                <a href='oth.php'>Hourly HOF</a>
                <a href='ratings.php'>Users Ratings</a>
                <a href='worldstats.php'>Game Stats</a>
                <a href='citizens.php'>User List</a>
                <a href='gang_list.php'>Gang List</a>
                <a href='notepad.php'>Notepad</a>
                <a href='contactlist.php'>Contact List</a>
                <a href='pointsdealer.php'>Points Dealer</a>
            </div>
        </div>
        <div class="dropdown">Downtown
            <div class="dropdown-content-up">
                <a href='thecity.php'>Search The City</a>
                <a href='prayer.php'>Pray</a>
                <a href='bloodbath.php'>Bloodbath</a>
                <a href='missions.php'>Missions</a>
                <a href='chapel.php'>Chapel</a>
                <a href='attackLadder.php'>Attack Ladder</a>
                <a href='hitlist.php'>Hitlist</a>
                <a href='pointsden.php'>Points Den</a>
                <a href='maze.php'>Maze</a>
                <a href='trades.php'>Trader [NEW]</a>
            </div>
        </div>
        <div class="dropdown">East Side
            <div class="dropdown-content-up">
                <a href='vote.php'>Vote</a>
                <a href='tickets.php'>Support Center</a>
                <a href='refer.php'>Your referrals</a>
                <a href='attackcontest.php'>Kill Contest</a>
                <a href='jobs.php'>Job Center</a>
                <a href='uni.php'>College Courses</a>
                <a href='house.php'>Estate Agency</a>
                <a href='viewstaff.php'>Game Staff</a>
                <a href='itempedia.php'>Itempedia</a>
                <a href='travel.php'>Travel</a>
                <a href='portfolio.php'>Your Properties</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>


		
<br><br>
<div class="floaty" style="margin:3px;">
    <h4 class="section-title">Hourly Statistics</h4>
    <hr>
<div class="categories-container">
    <!-- First Row -->
    <div class="category-row">
        <div class='flexele floaty' style="margin:2px;">
            <?php
                $query = mysql_query("SELECT id, koth FROM grpgusers WHERE `admin` != 1 ORDER BY koth DESC LIMIT 1");
                $koth = mysql_fetch_assoc($query);
                $_SESSION['koth_userid'] = $koth['id'];
                $_SESSION['koth_kills'] = $koth['koth'];
                if ($_SESSION['koth_kills'] == 0) {
                    print "Nobody<br /><br />";
                } else {
                    print "<br />" . formatName($_SESSION['koth_userid']) . "<br /><br />Killed: " . prettynum($_SESSION['koth_kills']) . " Mobsters.<br /><br />You: " . prettynum($user_class->koth) . " Kills<br /><br />";
                }
                print "Reward: 250 Points";
            ?>
        </div>
        <div class='flexele floaty' style="margin:2px;">
            <?php
                $query = mysql_query("SELECT id, loth FROM grpgusers WHERE `admin` = 0 ORDER BY loth DESC LIMIT 1");
                $loth = mysql_fetch_assoc($query);
                $_SESSION['loth_userid'] = $loth['id'];
                $_SESSION['loth_exp'] = $loth['loth'];
                if ($_SESSION['loth_exp'] == 0) {
                    print "Nobody<br /><br />";
                } else {
                    print "<br />" . formatName($_SESSION['loth_userid']) . "<br /><br />Gained: " . prettynum($_SESSION['loth_exp']) . " EXP.<br /><br />You: " . prettynum($user_class->loth) . " EXP<br /><br />";
                }
                print "Reward: 250 Points";
            ?>
        </div>
        <div class='flexele floaty' style="margin:2px;">
            <?php
                $query = mysql_query("SELECT id, `both` FROM grpgusers WHERE `admin` = 0 ORDER BY `both` DESC LIMIT 1");
                $both = mysql_fetch_array($query);
                $_SESSION['both_userid'] = $both['id'];
                $_SESSION['both_busts'] = $both['both'];
                if ($_SESSION['both_busts'] == 0) {
                    print "Nobody<br /><br />";
                } else {
                    print "<br />" . formatName($_SESSION['both_userid']) . "<br /><br />Busted: " . prettynum($_SESSION['both_busts']) . " Mobsters.<br /><br />You busted: " . prettynum($user_class->both) . " Mobsters<br /><br />";
                }
                print "Reward: 250 Points";
            ?>
        </div>
    </div>
   
   
	</table>
</div>


			<div class="spacer"></div>
		</div>




<?php
include 'footer.php';
?>