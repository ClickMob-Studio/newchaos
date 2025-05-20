<?php
require_once 'includes/functions.php';

start_session_guarded();

include 'header.php';
include_once 'databse/pdo_class.php';
?>
<style>
    .contenthead {
        width: 90%;
        margin: 0 auto;
    }
</style>
<h1>>Welcome to <!_-cityname-_!></h1>
<div class="container mt-3">


    <?php
    $db->query("SELECT * FROM luckyboxes WHERE playerid = ?");
    $db->execute(array(
        $user_class->id
    ));
    $boxesrows = $db->num_rows();
    $db->query("SELECT id FROM grpgusers ORDER BY todayskills DESC LIMIT 1");
    $db->execute();
    $worked = $db->fetch_row(true);
    $hitman = new User($worked['id']);
    $db->query("SELECT id FROM `grpgusers` WHERE `admin` = 0 ORDER BY `todaysexp` DESC LIMIT 1");
    $db->execute();
    $worked2 = $db->fetch_row(true);
    $leveler = new User($worked2['id']);
    $db->query("SELECT COUNT(*) FROM fiftyfifty");
    $db->execute();
    $count5050 = $db->fetch_single();

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

    $city_query = mysql_query("SELECT owned_points FROM cities WHERE id = '" . mysql_real_escape_string($current_city) . "' LIMIT 1");
    $city_query = mysql_fetch_assoc($city_query);

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


    $admin_ids = array_map(function ($a) {
        return $a['id'];
    }, $rows);

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
                    <img src="images/vacant.png" style="width: 100px; height: 100px;" alt="No Under Boss"
                        class="vacant-throne">
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

        <div class="container">
            <div class="row">
                <div class="col-6 col-md-4">
                    <div class="p-3 border bg-light">Economic Activities</div>
                    <div class="p-2">
                        <a href='stores.php'>Item Stores</a><br />
                        <a href='pharmacy.php'>General Pharmacy</a><br />
                        <a href='raidpointstore.php'>Raid Point Store</a><br />
                        <a href="itemmarket.php">Item Market</a><br />
                        <a href="pointmarket.php">Points Market</a><br />
                        <a href="goldmarket.php">Gold Market</a><br />
                        <a href="store.php">Upgrades</a><br />
                        <a href='jobs.php'>Job Center</a><br />
                        <a href='house.php'>Estate Agency</a><br />
                        <a href='portfolio.php'>Your Properties</a>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="p-3 border bg-light">Statistics and Achievements</div>
                    <div class="p-2">
                        <a href="polls.php">Polls</a><br />
                        <a href='tos.php'>Terms of Service</a><br />
                        <a href='contest.php' style="color:red;">Raid/Attack Contests</a><br />
                        <a href='halloffame.php'>Hall of Fame</a><br />
                        <a href='viewstaff.php'>View Game Staff</a><br />
                        <a href='otds.php'>Daily HOF</a><br />
                        <a href='oth.php'>Hourly HOF</a><br />
                        <a href='ratings.php'>Users Ratings</a><br />
                        <a href='worldstats.php'>Game Stats</a><br />
                        <a href='pointsdealer.php'>Points Dealer</a>
                    </div>
                </div>
                <div class="col-6 col-md-4 mt-3 mt-md-0">
                    <div class="p-3 border bg-light">Personal and Pet Management</div>
                    <div class="p-2">
                        <a href='mypets.php'>My Pet</a><br />
                        <a href='petcrime.php'>Pet Crimes</a><br />
                        <a href='petgym.php'>Pet Gym</a><br />
                        <a href='pethouse.php'>Pet House</a><br />
                        <a href='pethof.php'>Pet HOF</a><br />
                        <a href='petmarket.php'>Pet Market</a><br />
                        <a href='pettrack.php'>Pet Track</a><br />
                        <a href='petjail.php'>Pet Pound</a>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="p-3 border bg-light">Community and Social</div>
                    <div class="p-2">
                        <a href='online.php'>Users Online</a><br />
                        <a href='gang_list.php'>Gang List</a><br />
                        <a href='citizens.php'>User List</a><br />
                        <a href='contactlist.php'>Contact List</a><br />
                        <a href='vote.php'>Vote</a><br />
                        <a href='tickets.php'>Support Center</a><br />
                        <a href='refer.php'>Your Referrals</a><br />
                        <a href='contactlist.php'>Your Friends/Enemy list</a><br />
                        <a href='crafter.php'>Crafter</a><br />
                        <a href='gameupdates.php'>Updates</a><br />
                    </div>
                </div>
            </div>
        </div>


    </div>
    </table>
</div>
<br>
<div class="contenthead floaty">
    <h1> Leaderboards</h1>
    <div style="display: flex; flex-wrap: wrap;">
        <div class="vip-package" style="flex: 1; padding: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.5); margin: 5px;">
            <?php if ($king_result): ?>
                <h4>
                    <center>
                        <font color=red>Killer of the Day</font>
                    </center>
                </h4>
                <center>
                    <?php
                    $db->query("SELECT userid, kotd FROM ofthes WHERE userid NOT IN (?) ORDER BY kotd DESC LIMIT 1");
                    $db->execute([$admin_ids]);
                    $kotd = $db->fetch_row(true);

                    $db->query("SELECT * FROM ofthes WHERE userid = ?");
                    $db->execute([$user_class->id]);
                    $ofthes = $db->fetch_row(true);



                    print "<br />" . formatName($kotd['userid']) . "<br /><br />Killed: " . prettynum($kotd['kotd']) . " Mobsters.<br /><br />You Killed: " . prettynum($user_class->todayskills) . " Mobsters<br /><br />";
                    ?>


                    <h3>Reward: 10,000 Points</h3>
                </center>



            <?php endif; ?>
        </div>

        <div class="vip-package" style="flex: 1; padding: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.5); margin: 5px;">
            <?php if ($king_result): ?>
                <h4>
                    <center>
                        <font color=red>Leveller of the Day</font>
                    </center>
                </h4>
                <center>
                    <?php

                    $db->query("SELECT id, todaysexp FROM grpgusers WHERE `admin` != 1 ORDER BY todaysexp DESC LIMIT 1");
                    $db->execute();
                    $lotd = $db->fetch_row(true);

                    $db->query("SELECT * FROM grpgusers WHERE id = ?");
                    $db->execute([$user_class->id]);
                    $grpgusers = $db->fetch_row(true);

                    print "<br />" . formatName($lotd['id']) . "<br /><br />Gained: " . prettynum($lotd['todaysexp']) . " EXP<br /><br />You: " . prettynum($grpgusers['todaysexp']) . " EXP<br /><br />";
                    ?>

                    <h3>Reward: 10,000 Points</h3>

                <?php endif; ?>
        </div>

        <div class="vip-package" style="flex: 1; padding: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.5); margin: 5px;">
            <?php if ($king_result): ?>
                <h4>
                    <center>
                        <font color=red>Buster of the Day</font>
                    </center>
                </h4>
                <center>
                    <?php
                    $db->query("SELECT userid, botd FROM ofthes WHERE userid NOT IN (?) ORDER BY botd DESC LIMIT 1");
                    $db->execute([$admin_ids]);
                    $botd = $db->fetch_row(true);

                    $db->query("SELECT * FROM ofthes WHERE userid = ?");
                    $db->execute([$user_class->id]);
                    $ofthes = $db->fetch_row(true);

                    print "<br />" . formatName($botd['userid']) . "<br /><br />Busted: " . prettynum($botd['botd']) . " Mobsters.<br /><br />You busted: " . prettynum($ofthes['botd']) . " Mobsters<br /><br />";
                    ?>

                    <h3>Reward: 10,000 Points</h3>

                <?php endif; ?>
        </div>
    </div>
    </table>

    <span
        style="margin: 0; line-height: 27px; text-transform: uppercase; font-size: 20px; text-align: left; text-indent: 25px;">
        <div class="vip-container" style="display: flex; justify-content: space-around; align-items: flex-start;">
            <!-- King of the City -->


            <div class="vip-package" style="flex: 1; padding: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.5); margin: 5px;">
                <?php if ($king_result): ?>
                    <h4>
                        <center>
                            <font color=green>Highest killer in <!_-cityname-_!> this hour</font>
                        </center>
                    </h4>

                    <center> <?php
                    $db->query("SELECT id, koth FROM grpgusers WHERE `admin` != 1 ORDER BY koth DESC LIMIT 1");
                    $db->execute();
                    $koth = $db->fetch_row(true);

                    if ($koth['koth'] == 0) {
                        print "Nobody<br /><br />";
                    } else {
                        print "<br />" . formatName($koth['id']) . "<br /><br />Killed: " . prettynum($koth['koth']) . " Mobsters.<br /><br />You: " . prettynum($user_class->koth) . " Kills<br /><br />";
                    }

                    ?>
                        <h3>Reward: 500 Points</h3>


                    <?php endif; ?>

            </div>
            <div class="vip-package" style="flex: 1; padding: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.5); margin: 5px;">
                <?php if ($king_result): ?>
                    <h4>
                        <center>
                            <font color=green>Highest Leveller this hour</font>
                        </center>
                    </h4>

                    <center> <?php
                    $db->query("SELECT id, loth FROM grpgusers WHERE `admin` = 0 ORDER BY loth DESC LIMIT 1");
                    $db->execute();
                    $loth = $db->fetch_row(true);

                    if ($loth['loth'] == 0) {
                        print "Nobody<br /><br />";
                    } else {
                        print "<br />" . formatName($loth['id']) . "<br /><br />Gained: " . prettynum($loth['loth']) . " EXP.<br /><br />You: " . prettynum($user_class->loth) . " EXP<br /><br />";
                    }

                    ?>
                        <h3>Reward: 500 Points</h3>


                    <?php endif; ?>

            </div>
            <div class="vip-package" style="flex: 1; padding: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.5); margin: 5px;">
                <?php if ($king_result): ?>
                    <h4>
                        <center>
                            <font color=green>Highest Buster of the hour</font>
                        </center>
                    </h4>

                    <center> <?php
                    $db->query("SELECT id, `both` FROM grpgusers WHERE `admin` = 0 ORDER BY `both` DESC LIMIT 1");
                    $db->execute();
                    $both = $db->fetch_row(true);

                    if ($both['both'] == 0) {
                        print "Nobody<br /><br />";
                    } else {
                        print "<br />" . formatName($both['id']) . "<br /><br />Busted: " . prettynum($both['both']) . " Mobsters.<br /><br />You busted: " . prettynum($user_class->both) . " Mobsters<br /><br />";
                    }

                    ?>
                        <h3>Reward: 500 Points</h3>


                    <?php endif; ?>

            </div>
        </div>

        </table>
        <tr>
            <div style="display: flex; flex-wrap: wrap;">
                <div class="vip-package"
                    style="flex: 1; padding: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.5); margin: 5px;">
                    <?php if ($king_result): ?>
                        <p>
                            <center>
                                <font color=orange>Mugger of the Hour</font>
                            </center>
                            </h4>
                            <center>
                                <?php
                                $db->query("SELECT id, moth FROM grpgusers WHERE `admin` != 1 ORDER BY moth DESC LIMIT 1");
                                $db->execute();
                                $moth = $db->fetch_row(true);

                                if ($moth['moth'] == 0) {
                                    print "Nobody<br/><br/>";
                                } else {
                                    print "<br />" . formatName($moth['id']) . "<br /><br />Mugs: " . prettynum($moth['moth']) . "<br /><br />You: " . prettynum($user_class->moth) . " Mugs<br /><br />";
                                }
                                ?>

                                <h3>Reward: 500 Points</h3>


                            <?php endif; ?>
                </div>

                <div class="vip-package"
                    style="flex: 1; padding: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.5); margin: 5px;">
                    <?php if ($king_result): ?>
                        <h4>
                            <center>
                                <font color=orange>Mugger of the Day</font>
                            </center>
                        </h4>
                        <center><?php
                        $db->query("SELECT userid, motd FROM ofthes WHERE userid NOT IN (?) ORDER BY motd DESC LIMIT 1");
                        $db->execute([$admin_ids]);
                        $motd = $db->fetch_row(true);

                        $db->query("SELECT userid, motd FROM ofthes WHERE userid = ?");
                        $db->execute([$user_class->id]);
                        $mymotd = $db->fetch_row(true);
                        if ($motd['motd'] == 0) {
                            print "Nobody<br/><br/>";
                        } else {
                            print "<br />" . formatName($motd['userid']) . "<br /><br />Mugs: " . prettynum($motd['motd']) . "<br /><br />You: " . prettynum($mymotd['motd']) . " Mugs<br /><br />";
                        }
                        ?>
                            <h3>Reward: 10,000 Points</h3>


                        <?php endif; ?>
                </div>

                <div class="vip-package"
                    style="flex: 1; padding: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.5); margin: 5px;">
                    <?php if ($king_result): ?>
                        <h4>
                            <center>
                                <font color=orange>Buster of the Day</font>
                            </center>
                        </h4>
                        <center>
                            <?php
                            $db->query("SELECT userid, botd FROM ofthes WHERE userid NOT IN (?) ORDER BY botd DESC LIMIT 1");
                            $db->execute([$admin_ids]);
                            $botd = $db->fetch_row(true);

                            $db->query("SELECT * FROM ofthes WHERE userid = ?");
                            $db->execute([$user_class->id]);
                            $ofthes = $db->fetch_row(true);

                            print "<br />" . formatName($botd['userid']) . "<br /><br />Busted: " . prettynum($botd['botd']) . " Mobsters.<br /><br />You busted: " . prettynum($ofthes['botd']) . " Mobsters<br /><br />";
                            ?>

                            <h3>Reward: 10,000 Points</h3>


                        <?php endif; ?>
                </div>
            </div>
            </table>







            <!-- <br>
<center><font color=white>This is your referral link: https://themafialife.com/register.php?referer=<?php echo $user_class->id; ?></span><br>
Every <span class="color:yellow;">Valid</span> signup on this link gets you 100 Points + 50 cyellowit</font>
<br> -->
            </table>


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