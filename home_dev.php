<?php
$metatitle = 'Text-Based Mafia Game - Free Online Multiplayer RPG';
$metadesc = 'True MMO (TMMO) is one of the most popular original text-based mafia games today. Fight in the bloodbath, shoot out in live gang wars with your crime family, or gamble your way to the top. Don a thompson or a sledgehammer and play your way to become the most powerful godfather in TrueMMO, the best textbased game on the net!';

if (empty($metatitle))
    $metatitle = 'TrueMMO';
else
    $metatitle = $metatitle.' | TrueMMO';

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="css/newgame/login.css?<?php echo filemtime('/var/www/html/css/newgame/login.css') ?>" media="screen"/>
        <link rel="stylesheet" type="text/css" href="css/_misc.css?<?php echo filemtime('/var/www/html/css/_misc.css') ?>">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <title><?php echo $metatitle ?></title>
        <?php
        if (!empty($metadesc)) echo '<meta name="description" content="'.$metadesc.'">';
        ?>
    </head>
    <body>
        <div id="outer" class="wrap">
            <div id="header" class="row">
                <div id="logo" style="color:transparent;overflow:hidden;"><h1>TrueMMO</h1><h2>True original MMO text-based mafia game</h2></div>
                <div id="navi">
                    <a href="login.php">HOME</a>
                    <a href="register.php">REGISTER</a>
                    <a href="contact.php">CONTACT</a>
                </div>
                <div class="spacer"></div>
            </div>
            <div id="main_area" class="row">
                <div class="top row"></div>
                <div class="middle row">
                    <div class="pad">
                        <div class="left_side">
                            <h2>What is TrueMMO (TMMO)?</h2>
                            <div class="divider"></div>
                            <p>
                                True MMO is one of the most popular original text-based mafia games on the web today.
                                <br>
                                Fight in the bloodbath, shoot out in live gang wars with your crime family, or gamble your way to the top.
                                <br>
                                Will you don a thompson or a sledgehammer as you play your way to become the most powerful godfather in TrueMMO, the best textbased game on the net!
                            </p>
                        </div>
                        <div style="color:red;font-weight:bold;text-align:right;"><?php echo $_SESSION['failmessage'] ?></div>
                        <div class="right_side">
                            <div id="login_panel">
                                <div class="padding">
                                    <form name="login" action="login.php" method="post" accept-charset="utf-8">
                                        <input type="text" class="user_box" name="username" placeholder="Username" />
                                        <input type="password" class="pass_box" name="password" placeholder="Password" />
                                        <input type="submit" class="login" value="login now" />
                                    </form>
                                    <div class="divider"></div>
                                    <a href="forgot.php">Forgot Password?</a> || <a href="register.php">New Player?</a>
                                    <div class="divider"></div>
                                    <a href="register.php" class="register"></a>
                                </div>
                            </div>
                            <div class="spacer"></div>
                        </div>
                        <div class="spacer"></div>
                    </div>
                    <div class="statistics">
                        <h2>Statistics</h2>
                        <div class="divider"></div>
                        <div class="news_slot">

                            <div class="onlinePlayers" style="">
                                <div style="text-align: center;background-color: #100f0e;border-radius: 10px;padding: 15px;margin: 5px;color: #08bf03;font-weight: 900;font-size: 1.5em;"><span class="ol"><?php echo get_users_online(); ?></span> Players Online</div></div>

                            <div class="flexcont">
                                <div class="flexele2">
                                <div class="old">
                                <?php
                                    $db->query("SELECT id, lastactive FROM grpgusers ORDER BY lastactive DESC LIMIT 5");
                                    $rows = $db->fetch_row();
                                    $i = 1;
                                    echo'<table class="mtable" style="margin:auto;width:100%;text-align:left;">';
                                        echo'<tr>';
                                            echo'<th colspan="3">Last 5 Active Players</th>';
                                        echo'</tr>';
                                    foreach($rows as $row){
                                        echo'<tr><td>' . $i++ . '.</td><td>' . formatName($row['id']) . '</td><td style="text-align:center;">'.howLongAgo($row['lastactive']).'</td></tr>';
                                    }
                                    echo'</table>';
                                ?>
                                </div>
                                </div>
                                <div class="flexele2">
                                <?php
                                    $db->query("SELECT id FROM grpgusers WHERE admin <> 1 AND id <> 103 ORDER BY total DESC LIMIT 5");
                                    $rows = $db->fetch_row();
                                    $i = 1;
                                    echo'<table class="mtable" style="margin:auto;width:100%;text-align:left;">';
                                        echo'<tr>';
                                            echo'<th colspan="2">Top 5 Strongest Players</th>';
                                        echo'</tr>';
                                    foreach($rows as $row){
                                        echo'<tr><td>' . $i++ . '.</td><td>' . formatName($row['id']) . '</td></tr>';
                                    }
                                    echo'</table>';
                                ?>
                                </div>
                                <div class="flexele2">
                                <?php
                                    $db->query("SELECT id, level FROM grpgusers WHERE admin <> 1 AND id <> 103 ORDER BY level DESC LIMIT 5");
                                    $rows = $db->fetch_row();
                                    $i = 1;
                                    echo'<table class="mtable" style="margin:auto;width:100%;text-align:left;">';
                                        echo'<tr>';
                                            echo'<th colspan="3">Top 5 Highest Leveled Players</th>';
                                        echo'</tr>';
                                    foreach($rows as $row){
                                        echo'<tr><td>' . $i++ . '.</td><td>' . formatName($row['id']) . '</td><td style="text-align:center;">'.$row['level'].'</td></tr>';
                                    }
                                    echo'</table>';
                                ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bottom row">
                </div>
            </div>            
            <?php
                // $db->query('SELECT
                //               CASE WHEN `text` LIKE "%attacked you and won%" THEN CONCAT(UF.`username`, " Attacked ", UT.`username`, " and Won!")
                //               WHEN `text` LIKE "%attacked you and lost%" THEN CONCAT(UF.`username`, " Attacked ", UT.`username`, " and Lost!")
                //               WHEN `text` LIKE "%You were mugged by%" THEN CONCAT(UT.`username`, " was mugged by ", UF.`username`)
                //               WHEN `text` LIKE "%You have been busted out of Jail by%" THEN CONCAT(UT.`username`, " was busted out of Jail by", UF.`username`)
                //               ELSE "NADA"
                //                END `message`
                //               FROM `events` E
                //                     JOIN `grpgusers` UT
                //                       ON UT.`id` = E.`to`
                //                     JOIN `grpgusers` UF
                //                       ON UF.`id` = E.`extra`
                //               WHERE `text` LIKE "%attacked you and won%"
                //                  OR `text` LIKE "%attacked you and lost%"
                //                  OR `text` LIKE "%You were mugged by%"
                //                  OR `text` LIKE "%You have been busted out of Jail by%"
                //               ORDER BY E.`id` DESC
                //               LIMIT 100;');
                // $rows = $db->fetch_row();
                // $i = 1;
                // echo'<div class="live-feed-container">';
                // foreach($rows as $row){
                //     echo '<span class="live-feed-item">' . $row['message'] . '</span>';
                //     // echo '<span class="live-feed-item" data-id="' . $i++ . '" style="position: relative;left: '.rand(100,1800).'px;top: '.rand(100,800).'px;">' . $row['message'] . '</span>';
                // }
                // echo'</div>';
            ?>            
            <div class="live-feed-container live-feed-container-left"></div>
            <div class="live-feed-container live-feed-container-right"></div>
           
        </div>
    </body>
    <script type="text/javascript" src="/js/homefeed.js?<?php echo filemtime('/var/www/html/js/homefeed.js') ?>"></script>
    <script>
        function get_olu(){
            $.ajax({
                type: "POST",
                url: "ajax_onlineusers.php",
                data: {"page" : "home"},
                async: false,
                success: function(response) {
                    data = $.parseJSON(response);
                    $('.ol').html(data.count);
                    $('.old').html(data.html);
                },
                complete: function() {
                    setTimeout(function(){get_olu();}, 1000);
                }
            });
        }
        get_olu();
    </script>
</html>
<?php $_SESSION['failmessage'] = ""; ?>