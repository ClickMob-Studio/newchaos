<?php
require_once 'includes/functions.php';

start_session_guarded();

include 'header.php';

?>
<style>
    .contenthead {
        width: 90%;
        margin: 0 auto;
    }
</style>
<h1>>Welcome to <!_-cityname-_!></h1>

<?php

$db->query("SELECT id FROM `grpgusers` WHERE `admin` = 1");
$db->execute();
$rows = $db->fetch_row();

// Assuming we have a city variable for the current user's city
$current_city = $user_class->city;

$nowCurrentTime = time();

if (isset($_GET['claim_king']) && $_GET['claim_king'] == 'claimnow') {
    if ($user_class->hospital > 0) {
        echo Message("You can not become a Boss whilst in hospital");
        require "footer.php";
        exit;
    }
    if ($user_class->aprotection > $nowCurrentTime) {
        echo Message("You can not become a Boss whilst your using attack protection");
        require "footer.php";
        exit;
    }
    $king_query = "SELECT id FROM grpgusers WHERE king = :current_city LIMIT 1";
    $db->query($king_query);
    $db->bind(':current_city', $user_class->city);
    $king_result = $db->fetch_row();
    if (count($king_result) < 1) {
        $queen_query = "SELECT id FROM grpgusers WHERE queen = :current_city AND id = :user_id LIMIT 1";
        $db->query($queen_query);
        $db->bind(':current_city', $user_class->city);
        $db->bind(':user_id', $user_class->id);
        $queen_result = $db->fetch_row();
        if (count($queen_result) > 0) {
            echo Message("You are already the Under Boss!");
        } else {
            $update_query = "UPDATE grpgusers SET king = :current_city, queen = 0 WHERE id = :user_id";
            $db->query($update_query);
            $db->bind(':current_city', $user_class->city);
            $db->bind(':user_id', $user_class->id);
            $db->execute();
            header('Location: city.php');
            exit();
        }

    }
}

if (isset($_GET['claim_queen']) && $_GET['claim_queen'] == 'claimnow') {
    if ($user_class->hospital > 0) {
        echo Message("You can not become an Under Boss whilst in hospital");
        require "footer.php";
        exit;
    }
    if ($user_class->aprotection > $nowCurrentTime) {
        echo Message("You can not become an Under Boss whilst your using attack protection");
        require "footer.php";
        exit;
    }
    $queen_query = "SELECT id FROM grpgusers WHERE queen = :current_city LIMIT 1";
    $db->query($queen_query);
    $db->bind(':current_city', $user_class->city);
    $queen_result = $db->fetch_row();
    if (count($queen_result) < 1) {
        $king_query = "SELECT id FROM grpgusers WHERE king = :current_city AND id = :user_id LIMIT 1";
        $db->query($king_query);
        $db->bind(':current_city', $user_class->city);
        $db->bind(':user_id', $user_class->id);
        $king_result = $db->fetch_row();
        if (count($king_result) > 0) {
            echo Message("You are already the Boss!");
        } else {
            $update_query = "UPDATE grpgusers SET queen = :current_city, king = 0 WHERE id = :user_id";
            $db->query($update_query);
            $db->bind(':current_city', $user_class->city);
            $db->bind(':user_id', $user_class->id);
            $db->execute();

            header('Location: city.php');
            exit();
        }

    }
}

$db->query("SELECT owned_points FROM cities WHERE id = ? LIMIT 1");
$db->execute([$current_city]);
$city_query = $db->fetch_row(true);


// PHP to fetch king's information including avatar
$db->query("SELECT id, username, avatar FROM grpgusers WHERE king = ? LIMIT 1");
$db->execute([$current_city]);
$king_result = $db->fetch_row(true);

// PHP to fetch queen's information including avatar
$db->query("SELECT id, username, avatar FROM grpgusers WHERE queen = ? LIMIT 1");
$db->execute([$current_city]);
$queen_result = $db->fetch_row(true);

$admin_ids = array_map(function ($a) {
    return $a['id'];
}, $rows);

$currentQuestSeason = getCurrentQuestSeasonForUser($user_class->id);
if (isset($currentQuestSeason['id'])) {
    $questSeasonUser = getQuestSeasonUser($user_class->id, $currentQuestSeason['id']);
    $questSeasonMissionUser = getQuestSeasonMissionUser($user_class->id, $currentQuestSeason['id']);
    $questSeasonMission = getQuestSeasonMission($user_class->id, $currentQuestSeason['id']);
}
?>
<br>
<div class="contenthead floaty">
    <?php
    $csrf = md5(uniqid(rand(), true));
    $_SESSION['csrf'] = $csrf;
    ?>


    <div class="vip-container" style="display: flex; justify-content: space-around; align-items: flex-start;">

        <div class="vip-package"
            style="flex: 1; padding: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.5); margin: 5px; text-align:center ">
            <?php if ($king_result): ?>
                <img src="<?php echo htmlspecialchars($king_result['avatar']); ?>" style="width: 100px; height: 100px;"
                    alt="King's Avatar" class="user-avatar">
                <h4>Boss of <!_-cityname-_!></h4>
                <p><strong><?php echo formatName($king_result['id']); ?></strong></p>
                <a href="/attack.php?attack=<?php echo $king_result['id']; ?>&csrf=<?php echo $csrf; ?>&thrones=attack"
                    class="challenge-btn" style="text-decoration: underline;">Challenge</a>

            <?php else: ?>
                <img src="images/vacant.png" style="width: 100px; height: 100px;" alt="No Boss" class="vacant-throne">
                <h4>VACANT</h4>
                <p>Boss of <!_-cityname-_!></p>
                <a href="?claim_king=claimnow" style="text-decoration: underline;">Claim</a>

            <?php endif; ?>
            <br />

            <?php
            $owned_points = $city_query['owned_points'];
            $userPrestigeSkills = getUserPrestigeSkills($user_class);
            if ($userPrestigeSkills['throne_points_unlock'] > 0) {
                $owned_points = $owned_points + ($owned_points / 100 * 20);
            }

            ?>
            <p style="font-weight: bold; margin-top: 5px;">By being the Boss of this city you will earn
                <?php echo number_format($owned_points, 0) ?> points an hour.
            </p>



        </div>
        <?php
        if ($user_class->city == 600) {
            $twenty_percent = 3250;
        } else {
            $twenty_percent = $owned_points - $owned_points * 0.20;
        }
        ?>
        <div class="vip-package"
            style="flex: 1; padding: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.5); margin: 5px; text-align:center">
            <?php if ($queen_result): ?>
                <img src="<?php echo htmlspecialchars($queen_result['avatar']); ?>" style="width: 100px; height: 100px;"
                    alt="Under Boss's Avatar" class="user-avatar">
                <h4>Under Boss of <!_-cityname-_!></h4>
                <p><strong><?php echo formatName($queen_result['id']); ?></strong></p>
                <a href="/attack.php?attack=<?php echo $queen_result['id']; ?>&csrf=<?php echo $csrf; ?>&thrones=attack"
                    class="challenge-btn" style="text-decoration: underline;">Challenge</a>

            <?php else: ?>
                <img src="images/vacant.png" style="width: 100px; height: 100px;" alt="No Under Boss" class="vacant-throne">
                <h4>VACANT</h4>
                <p>Under Boss of <!_-cityname-_!></p>
                <a href="?claim_queen=claimnow" style="text-decoration: underline;">Claim</a>
            <?php endif; ?>
            <br />

            <p style="font-weight: bold; margin-top: 5px">By being the Under Boss of this City you will earn
                <?php echo number_format($twenty_percent, 0) ?> points an hour.
            </p>
        </div>
    </div>
</div>
<br>
<div class="contenthead floaty">
    <h1>City links</h1>


    <div class='divider'></div>
    <style>
        .section-header {
            background-color: #000;
            color: #fff;
            padding: 10px;
            text-align: center;
            margin-bottom: 10px;
            margin-top: 15px;
            font-weight: bold;
            border-radius: 0.25rem;
        }

        .link-container {
            padding: 0.5rem;
        }

        .link-column {
            margin-bottom: 15px;
        }

        @media (max-width: 768px) {
            .link-column {
                flex: 0 0 auto;
                width: 50%;
            }
        }
    </style>
    <div class="container mt-3">
        <!-- Economic Activities -->
        <div class="row">
            <div class="col-6 col-md-4 text-center">
                <div class="col-12 text-center mb-2 section-header">
                    <strong>Economic Activities</strong>
                </div>
                <?php if (isset($questSeasonMission['requirements']['vinny_the_fish_delivery'])): ?>
                    <a href='quest.php?mode=therustnail'>The Rusty Nail</a><br>
                <?php endif; ?>

                <?php if (isset($questSeasonMission['requirements']['pharmacy_protection'])): ?>
                    <a href='quest.php?mode=marocs_pharmacy'>Marcos Pharmacy</a><br>
                <?php endif; ?>

                <?php if (isset($questSeasonMission['requirements']['follow_salvatore'])): ?>
                    <a href='quest.php?mode=follow_salvatore'>Follow Salvatore</a><br>
                <?php endif; ?>

                <?php if (isset($questSeasonMission['requirements']['steal_books'])): ?>
                    <a href='quest.php?mode=steal_books'>Steal The Books</a><br>
                <?php endif; ?>

                <?php if (isset($questSeasonMission['requirements']['interrogate_phil'])): ?>
                    <a href='quest.php?mode=interrogate_phil'>Interrogate Phil</a><br>
                <?php endif; ?>

                <a href='stores.php'>Item Stores</a><br>
                <a href='pharmacy.php'>General Pharmacy</a><br>
                <a href='raidpointstore.php'>Raid Point Store</a><br>
                <a href='spendactivity.php'>Activity Store</a><br>
                <a href="itemmarket.php">Item Market</a><br>
                <a href="pointmarket.php">Points Market</a><br>
                <a href="goldmarket.php">Gold Market</a><br>
                <a href="store.php">Upgrades</a><br>
                <a href='jobs.php'>Job Center</a><br>
                <a href='house.php'>Estate Agency</a><br>
                <a href='portfolio.php'>Your Properties</a>
            </div>
            <div class="col-6 col-md-4 text-center">
                <div class="col-12 text-center mb-2 section-header">
                    <strong>Statistics and Achievements</strong>
                </div>
                <a href="polls.php">Polls</a><br>
                <a href='tos.php'>Terms of Service</a><br>
                <!--            <a href='contest.php'><font color=red>Raid/Attack Contests</font></a><br>-->
                <a href='halloffame.php'>Hall of Fame</a><br>
                <a href='viewstaff.php'>View Game Staff</a><br>
                <a href='viewbanned.php'>Federal Jail</a><br>
                <a href='otds.php'>Daily HOF</a><br>
                <a href='oth.php'>Hourly HOF</a><br>
                <a href='ratings.php'>Users Ratings</a><br>
                <a href='worldstats.php'>Game Stats</a><br>
                <a href='pointsdealer.php'>Points Dealer</a>
            </div>
            <div class="col-md-4 text-center">
                <div class="col-12 text-center mb-2 section-header">
                    <strong>Personal and Pet Management</strong>
                </div>
                <a href='mypets.php'>My Pet</a><br>
                <a href='petcrime.php'>Pet Crimes</a><br>
                <a href='petgym.php'>Pet Gym</a><br>
                <a href='pethouse.php'>Pet House</a><br>
                <a href='petlist.php'>Pet List</a><br>
                <a href='pethof.php'>Pet HOF</a><br>
                <a href='petmarket.php'>Pet Market</a><br>
                <a href='pettrack.php'>Pet Track</a><br>
                <a href='petjail.php'>Pet Pound</a><br />
                <a href='petladder.php'>Pet Ladder</a>
            </div>
        </div>
        <!-- Community and Social -->
        <div class="row">

            <div class="col-6 col-md-4 text-center">
                <div class="col-12 text-center mb-2 section-header">
                    <strong>Community and Social</strong>
                </div>
                <a href='online.php'>Users Online</a><br>
                <a href='gang_list.php'>Gang List</a><br>
                <a href='citizens.php'>User List</a><br>
                <a href='contactlist.php'>Contact List</a><br>
                <a href='vote.php'>Vote</a><br>
                <a href='refer.php'>Your Referrals</a><br>
                <a href='contactlist.php'>Your Friends/Enemy list</a><br>
                <a href='research.php'>Research</a><br>
                <a href='crafter.php'>Crafter</a><br>
                <a href='gameupdates.php'>Updates</a><br>
                <a href="forum.php">Forums</a>
            </div>
            <div class="col-6 col-md-4 text-center">
                <div class="col-12 text-center mb-2 section-header">
                    <strong>Gaming and Entertainment</strong>
                </div>
                <a href='prestige.php'>Account Prestige</a><br>
                <a href='raidtokensmuggling.php'>Find Some Raid Tokens</a><br>
                <a href='psmuggling.php'>Points Smuggling</a><br>
                <a href="casinonew.php">Casino</a><br>
                <a href='lucky_boxes.php'>Lucky Boxes</a><br>
                <a href="FruitMachine.php">Fruit Machine</a><br>
                <a href='thedoors.php'>The Doors</a><br>
                <a href='bloodbath.php'>Bloodbath</a><br>
                <a href='missions.php'>Missions</a><br>
                <a href='chapel.php'>Chapel</a><br>
                <a href='trainingdummies.php'>City Goons</a><br>
            </div>
            <div class="col-md-4 text-center">
                <div class="col-12 text-center mb-2 section-header">
                    <strong>Miscellaneous</strong>
                </div>
                <a href='quest.php'>Quests</a><br>
                <a href='battlepass.php'>Battle Pass</a><br>
                <a href='user_operations.php'>Operations</a><br>
                <a href='claim_achievements.php'>Claim Achievements</a><br>
                <a href='itempedia.php'>Item Guide</a><br>
                <a href='thecity.php'>Search The City</a><br>
                <a href='prayer.php'>Pray</a><br>
                <a href='attackLadder.php'>Attack Ladder</a><br>
                <a href='hitlist.php'>Hitlist</a><br>
                <a href='pointsden.php'>Points Den</a><br>
                <a href='uni.php'>Education</a><br>
                <a href='travel.php'>Travel</a><br>
                <a href='maze.php'>Maze</a><br>
                <?php
                $userPrestigeSkills = getUserPrestigeSkills($user_class);
                if ($userPrestigeSkills['speed_attack_unlock'] > 0) {
                    echo "<a href='super_attack.php'>Super Attack</a><br>";
                }
                ?>
            </div>
        </div>
    </div>


</div>
</table>
</div>
<br>
<div class="contenthead floaty">
    <h1> Leaderboards</h1>
    <div class="container mt-3">
        <div class="row">
            <!-- Killer of the Day -->
            <div class="col-6 col-md-4 mb-4">
                <div class="vip-package p-3" style="box-shadow: 0 0 10px rgba(0,0,0,0.5);">
                    <h4 class="text-center text-danger">Killer of the Day</h4>
                    <div class="text-center">
                        <?php
                        $db->query("SELECT userid, kotd FROM ofthes WHERE kotd > 0 AND userid NOT IN (?) ORDER BY kotd DESC LIMIT 1");
                        $db->execute([$admin_ids]);
                        $kotd = $db->fetch_row(true);

                        $db->query("SELECT * FROM ofthes WHERE userid = ?");
                        $db->execute([$user_class->id]);
                        $ofthes = $db->fetch_row(true);

                        $name = empty($kotd['userid']) ? 'Nobody' : formatName($kotd['userid']);
                        echo "<br />" . $name . "<br /><br />Killed: " . prettynum($kotd['kotd']) . " Mobsters.<br /><br />You Killed: " . prettynum($user_class->todayskills) . " Mobsters<br /><br />";
                        ?>
                        <h3>Reward: 10,000 Points</h3>
                    </div>
                </div>
            </div>

            <!-- Leveller of the Day -->
            <div class="col-6 col-md-4 mb-4">
                <div class="vip-package p-3" style="box-shadow: 0 0 10px rgba(0,0,0,0.5);">
                    <h4 class="text-center text-danger">Leveller of the Day</h4>
                    <div class="text-center">
                        <?php
                        $db->query("SELECT id, todaysexp FROM grpgusers WHERE todaysexp > 0 AND `admin` != 1 ORDER BY todaysexp DESC LIMIT 1");
                        $db->execute();
                        $lotd = $db->fetch_row(true);

                        $db->query("SELECT * FROM grpgusers WHERE id = ?");
                        $db->execute([$user_class->id]);
                        $grpgusers = $db->fetch_row(true);

                        $name = empty($lotd['id']) ? 'Nobody' : formatName($lotd['id']);
                        echo "<br />" . $name . "<br /><br />Gained: " . prettynum($lotd['todaysexp']) . " EXP<br /><br />You: " . prettynum($grpgusers['todaysexp']) . " EXP<br /><br />";
                        ?>
                        <h3>Reward: 10,000 Points</h3>
                    </div>
                </div>
            </div>

            <!-- Buster of the Day -->
            <div class="col-12 col-md-4 mb-4">
                <div class="vip-package p-3" style="box-shadow: 0 0 10px rgba(0,0,0,0.5);">
                    <h4 class="text-center text-danger">Buster of the Day</h4>
                    <div class="text-center">
                        <?php
                        $db->query("SELECT userid, botd FROM ofthes WHERE botd > 0 AND userid NOT IN (?) ORDER BY botd DESC LIMIT 1");
                        $db->execute([$admin_ids]);
                        $botd = $db->fetch_row(true);

                        $db->query("SELECT * FROM ofthes WHERE userid = ?");
                        $db->execute([$user_class->id]);
                        $ofthes = $db->fetch_row(true);

                        $name = empty($botd['userid']) ? 'Nobody' : formatName($botd['userid']);
                        echo "<br />" . $name . "<br /><br />Busted: " . prettynum($botd['botd']) . " Mobsters.<br /><br />You busted: " . prettynum($ofthes['botd']) . " Mobsters<br /><br />";
                        ?>
                        <h3>Reward: 10,000 Points</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="container mt-3">
        <div class="row justify-content-around">
            <!-- Highest Killer in City -->
            <div class="col-6 col-md-4 mb-4">
                <div class="vip-package p-3" style="box-shadow: 0 0 10px rgba(0,0,0,0.5);">
                    <h4 class="text-center" style="color: green;">Highest killer in <!_-cityname-_!> this hour</h4>
                    <div class="text-center">
                        <?php
                        $db->query("SELECT id, koth FROM grpgusers WHERE koth > 0 AND `admin` != 1 ORDER BY koth DESC LIMIT 1");
                        $db->execute();
                        $koth = $db->fetch_row(true);
                        if ($koth['koth'] == 0) {
                            echo "Nobody<br /><br />";
                        } else {
                            echo "<br />" . formatName($koth['id']) . "<br /><br />Killed: " . prettynum($koth['koth']) . " Mobsters.<br /><br />You: " . prettynum($user_class->koth) . " Kills<br /><br />";
                        }
                        ?>
                        <h3>Reward: 500 Points</h3>
                    </div>
                </div>
            </div>

            <!-- Highest Leveller This Hour -->
            <div class="col-6 col-md-4 mb-4">
                <div class="vip-package p-3" style="box-shadow: 0 0 10px rgba(0,0,0,0.5);">
                    <h4 class="text-center" style="color: green;">Highest Leveller this hour</h4>
                    <div class="text-center">
                        <?php
                        $db->query("SELECT id, loth FROM grpgusers WHERE loth > 0 AND `admin` = 0 ORDER BY loth DESC LIMIT 1");
                        $db->execute();
                        $loth = $db->fetch_row(true);
                        if ($loth['loth'] == 0) {
                            echo "Nobody<br /><br />";
                        } else {
                            echo "<br />" . formatName($loth['id']) . "<br /><br />Gained: " . prettynum($loth['loth']) . " EXP.<br /><br />You: " . prettynum($user_class->loth) . " EXP<br /><br />";
                        }
                        ?>
                        <h3>Reward: 500 Points</h3>
                    </div>
                </div>
            </div>

            <!-- Highest Buster of the Hour -->
            <!-- Adding offset to center this box on mobile -->
            <div class="col-6 col-md-4 mb-4">
                <div class="vip-package p-3" style="box-shadow: 0 0 10px rgba(0,0,0,0.5);">
                    <h4 class="text-center" style="color: green;">Highest Buster of the hour</h4>
                    <div class="text-center">
                        <?php
                        $db->query("SELECT id, `both` FROM grpgusers WHERE `both` > 0 AND `admin` = 0 ORDER BY `both` DESC LIMIT 1");
                        $db->execute();
                        $both = $db->fetch_row(true);
                        if ($both['both'] == 0) {
                            echo "Nobody<br /><br />";
                        } else {
                            echo "<br />" . formatName($both['id']) . "<br /><br />Busted: " . prettynum($both['both']) . " Mobsters.<br /><br />You busted: " . prettynum($user_class->both) . " Mobsters<br /><br />";
                        }
                        ?>
                        <h3>Reward: 500 Points</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-3">
        <div class="row justify-content-around">
            <!-- Mugger of the Hour -->
            <div class="col-6 col-md-4 mb-4">
                <div class="vip-package p-3" style="box-shadow: 0 0 10px rgba(0,0,0,0.5);">
                    <h4 class="text-center" style="color: orange;">Mugger of the Hour</h4>
                    <div class="text-center">
                        <?php
                        $db->query("SELECT id, moth FROM grpgusers WHERE `moth` > 0 AND `admin` != 1 ORDER BY moth DESC LIMIT 1");
                        $db->execute();
                        $moth = $db->fetch_row(true);
                        if ($moth['moth'] == 0) {
                            echo "Nobody<br/><br/>";
                        } else {
                            echo "<br />" . formatName($moth['id']) . "<br /><br />Mugs: " . prettynum($moth['moth']) . "<br /><br />You: " . prettynum($user_class->moth) . " Mugs<br /><br />";
                        }
                        ?>
                        <h3>Reward: 500 Points</h3>
                    </div>
                </div>
            </div>

            <!-- Mugger of the Day -->
            <div class="col-6 col-md-4 mb-4">
                <div class="vip-package p-3" style="box-shadow: 0 0 10px rgba(0,0,0,0.5);">
                    <h4 class="text-center" style="color: orange;">Mugger of the Day</h4>
                    <div class="text-center">
                        <?php
                        $db->query("SELECT userid, motd FROM ofthes WHERE motd > 0 AND userid NOT IN (?) ORDER BY motd DESC LIMIT 1");
                        $db->execute([$admin_ids]);
                        $motd = $db->fetch_row(true);

                        $db->query("SELECT userid, motd FROM ofthes WHERE userid = ?");
                        $db->execute([$user_class->id]);
                        $mymotd = $db->fetch_row(true);
                        if ($motd['motd'] == 0) {
                            echo "Nobody<br/><br/>";
                        } else {
                            echo "<br />" . formatName($motd['userid']) . "<br /><br />Mugs: " . prettynum($motd['motd']) . "<br /><br />You: " . prettynum($mymotd['motd']) . " Mugs<br /><br />";
                        }
                        ?>
                        <h3>Reward: 10,000 Points</h3>
                    </div>
                </div>
            </div>

            <!-- Buster of the Day -->
            <!-- Adding offset to center this box on mobile -->
            <div class="col-6 col-md-4 mb-4">
                <div class="vip-package p-3" style="box-shadow: 0 0 10px rgba(0,0,0,0.5);">
                    <h4 class="text-center" style="color: orange;">Buster of the Day</h4>
                    <div class="text-center">
                        <?php
                        $db->query("SELECT userid, botd FROM ofthes WHERE botd > 0 AND userid NOT IN (?) ORDER BY botd DESC LIMIT 1");
                        $db->execute([$admin_ids]);
                        $botd = $db->fetch_row(true);

                        $db->query("SELECT * FROM ofthes WHERE userid = ?");
                        $db->execute([$user_class->id]);
                        $ofthes = $db->fetch_row(true);

                        if ($botd['botd'] == 0) {
                            echo "Nobody<br /><br />";
                        } else {
                            echo "<br />" . formatName($botd['userid']) . "<br /><br />Busted: " . prettynum($botd['botd']) . " Mobsters.<br /><br />You busted: " . prettynum($ofthes['botd']) . " Mobsters<br /><br />";
                        }
                        ?>
                        <h3>Reward: 10,000 Points</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
    include 'footer.php';
    ?>
    <style>
        .special-users {
            background-color: #333;
            padding: 10px 0;
            text-align: center;
            margin-bottom: 20px;
        }

        .user {
            display: inline-block;
            margin: 0 10px;
        }

        .user img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: block;
            margin: 0 auto;
        }

        .user span {
            color: #fff;
            display: block;
        }

        .styled-table {
            border-collapse: collapse;
            width: 100%;
            margin: 25px 0;
            font-size: 18px;
            text-align: left;
        }

        .styled-table th,
        .styled-table td {
            padding: 10px 12px;
            border: 1px solid #ddd;
        }

        .styled-table tbody tr {
            border-bottom: 1px solid #dddddd;
        }

        .styled-table tbody tr:hover {
            background-color: #f5f5f5;
        }

        .styled-table thead {
            background-color: #f2f2f2;
        }
    </style>

    <style>
        .scrolling-section {
            display: flex;
            overflow-x: scroll;
            background-color: #333;
            padding: 20px 0;
        }

        .of-the-hour,
        .of-the-day {
            flex: 0 0 100%;
            display: flex;
            justify-content: space-around;
        }

        .user {
            width: 20%;
            text-align: center;
            color: #fff;
        }

        .user img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: block;
            margin: 0 auto 10px;
        }

        .user-details span {
            display: block;
        }
    </style>