<?php
include 'header.php';
if(isset($_GET['jailbreak'])){
    $jailbreak = $_GET['jailbreak'];
}else{
    $jailbreak = '';
}

if (checkCaptchaRequired($user_class)) {
    header('Location: captcha.php?token=' . $user_class->macro_token . '&page=jail');
}

if ($user_class->jail_bot_credits < 1) {
    mysql_query("UPDATE `grpgusers` SET `is_jail_bots_active` = 0 WHERE `id` = " . $user_class->id);
    $user_class->is_jail_bots_active = 0;
}

if (isset($_GET['action']) && $_GET['action'] === 'start_bot_process') {
    if ($user_class->jail_bot_credits > 0) {
        mysql_query("UPDATE `grpgusers` SET `is_jail_bots_active` = 1 WHERE `id` = " . $user_class->id);
        $user_class->is_jail_bots_active = 1;
    } else {
        echo Message("You have used all your jail bot credits.");
    }
}

if ($jailbreak != ""){
    if(empty($_GET['token'])){
        echo Message("There has been a issue");
    }
    if($_GET['token'] != $_SESSION['tokens']){
        $mes = "Something has gone wrong";
        $error = true;
    }else{
        unset($_SESSION['tokens']);
    }

    if (!$error){
        if ($jailbreak === 'bot') {
            $exp = mt_rand(1, 10);
            $_SESSION['message'] = "Success! You receive ".$exp." exp ";
            $mes = "Success! You receive ".$exp." exp ";

            $error = false;
            if ($user_class->jail_bot_credits < 1) {
                $_SESSION['message'] = 'You do not have any bot credits remaining.';
                $mes = 'You do not have any bot credits remaining.';

                $error = true;
            }
            if ($user_class->hospital > 0) {
                $_SESSION['message'] = "You can't break people out of jail whilst your in hospital.";
                $mes = "You can't break people out of jail whilst your in hospital.";
                $error = true;
            }
            if ($user_class->jail > 0) {
                $_SESSION['message'] = "You can't break people out of jail whilst your in jail.";
                $mes = "You can't break people out of jail whilst your in jail.";
                $error = true;
            }

            if (!$error) {
                $exp = $exp + $user_class->exp;
                $crimesucceeded = 1 + $user_class->crimesucceeded;

                mysql_query("UPDATE grpgusers SET `both` = `both` + 1, `epoints` = `epoints` + `eventbusts`, `bustcomp` = `bustcomp` + 1, exp =  ".$exp.", busts = busts + 1, jail_bot_credits = jail_bot_credits - 1 WHERE id = ".$user_class->id);
                $user_class->jail_bot_credits = $user_class->jail_bot_credits - 1;
                mission('b');
                newmissions('busts');
                gangContest(array(
                    'busts' => 1,
                    'exp' => $exp
                ));
                updateGangActiveMission('busts', 1);
                $toadd = array('botd' => 1);
                ofthes($user_class->id, $toadd);
                bloodbath('busts', $user_class->id);

            }
        } else {
            $db->query("SELECT * FROM grpgusers WHERE id = " . $jailbreak);
            $db->execute();
            $jailed_person = $db->fetch_row();

            if (!isset($jailed_person[0])) {
                echo Message('That person does not exist.');
                include 'footer.php';
                exit;
            }
            $jailed_person = $jailed_person[0];

            $error = false;
            if ($jailed_person['id'] == $user_class->id) {
                $_SESSION['message'] = "You can't break yourself out of jail.";
                $error = true;
            }
            if ($jailed_person['jail'] == "0"){
                $_SESSION['message'] = "That person is not in jail.";
                $error = true;
            }
            if ($user_class->hospital > 0) {
                $_SESSION['message'] = "You can't break people out of jail whilst your in hospital.";
                $error = true;
            }
            if ($user_class->jail > 0) {
                $_SESSION['message'] = "You can't break people out of jail whilst your in jail.";
                $error = true;
            }
            $chance = rand(1,(100 * 1 - ($user_class->speed / 25)));
            //$money = 785;
            $nerve = 10;
            $exp = 2500;

            if (!$error) {
                if ($user_class->nerve >= $nerve) {
                    if($chance <= 75) {
                        $_SESSION['message'] = "Success! You receive ".$exp." exp and 3 points";
                        $exp = $exp + $user_class->exp;
                        $crimesucceeded = 1 + $user_class->crimesucceeded;
                        $crimemoney = $user_class->crimemoney;
                        //$money = $money + $user_class->money;
                        $nerve = $user_class->nerve - $nerve;
                        if ($user_class->gang != 0) {
                            mysql_query("UPDATE gangs SET dailyBusts = dailyBusts + 1 WHERE id = ".$user_class->gang);
                        }
                        mysql_query("UPDATE grpgusers SET `both` = `both` + 1, `epoints` = `epoints` + `eventbusts`, `bustcomp` = `bustcomp` + 1, exp =  ".$exp.", busts = busts + 1, points = points + 3, nerve = nerve - 10 WHERE id = ".$user_class->id);
                        mission('b');
                        newmissions('busts');
                        gangContest(array(
                            'busts' => 1,
                            'exp' => $exp
                        ));
                        $toadd = array('botd' => 1);
                        ofthes($user_class->id, $toadd);
                        bloodbath('busts', $user_class->id);
                        updateGangActiveMission('busts', 1);
                        $result = mysql_query("UPDATE `grpgusers` SET `jail` = '0' WHERE `id`='".$jailed_person['id']."'");
                        //send even to that person
                        Send_Event($jailed_person['id'], "You have been busted out of Jail by [-_USERID_-].", $user_class->id);

                        addToGangCompLeaderboard($user_class->gang, 'busts_complete', 1);
                        $bpCategory = getBpCategory();
                        if ($bpCategory) {
                            addToBpCategoryUser($bpCategory, $user_class, 'busts', 1);
                        }

                        addToUserCompLeaderboard($user_class->id, 'busts_complete', 1);
                        $db->query("SELECT * FROM activity_contest WHERE id = 1 LIMIT 1");
                        $db->execute();
                        $activityContest = $db->fetch_row(true);
                        if ($activityContest['type'] == 'busts') {
                            addToUserCompLeaderboard($user_class->id, 'activity_complete', $activityContest['type_value']);
                        }

                        //header('Location: jail.php');
                    }elseif ($chance >= 150) {
                        $_SESSION['message'] = "You were caught. You were hauled off to jail for 10  minutes.";
                        $crimefailed = 1 + $user_class->crimefailed;
                        $jail = 10800;
                        $nerve = $user_class->nerve - $nerve;
                        $result = mysql_query("UPDATE grpgusers SET crimefailed = crimefailed + 1, caught = caught + 1, jail = 600, nerve = nerve - 10 WHERE id =".$user_class->id);
                    }else{
                        $_SESSION['message'] ="You failed.";
                        $crimefailed = 1 + $user_class->crimefailed;
                        $nerve = $user_class->nerve - $nerve;
                        $result = mysql_query("UPDATE grpgusers SET crimefailed = crimefailed + 1, nerve = nerve - 10 WHERE id = '".$_SESSION['id']."'");
                    }
                } else {
                    echo Message("You don't have enough nerve for that crime.");
                    include 'footer.php';
                    die();
                }
            }
        }
    }
}

if(isset($_GET['action']) && $_GET['action'] == 'bail'){
    if($user_class->jail < 1){
        $_SESSION['message'] = 'You are currently not in jail';
    }else{
        $cost = ceil($user_class->jail / 60);
        if($user_class->points < $cost){
            $_SESSION['message'] = 'You do not have enough points';
        }else{
            $_SESSION['message'] = 'You have bailed you self out of jail for '.$cost.' points';
            mysql_query("UPDATE grpgusers SET jail = 0, points = points - ".$cost." WHERE id = ".$user_class->id);
        }
    }
}

$cost = ceil($user_class->jail / 60);
?>

<?php
if(isset($_SESSION['message'])){
    echo Message($_SESSION['message']);
    unset($_SESSION['message']);
} else if (isset($mes)) {
    echo Message($mes);
}
if($user_class->jail > 0){
    echo "<span style='color:red'>You are currently in jail click<a href='jail.php?action=bail' style='color:white'>here</a> to bail your self out this will cost you ".$cost." points</span>";
}

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
    <tr><td class="contentcontent">
            <?php if ($user_class->jail_bot_credits > 0): ?>
                <div class="alert alert-info">
                    <center>
                        You currently have <span class="jail-bot-credit-count"><?php echo $user_class->jail_bot_credits ?></span> Jail Bot Credits Remaining.

                        <?php if (!$user_class->is_jail_bots_active): ?>
                            <br /><br />
                            <a href="?action=start_bot_process" class="btn btn-primary">Start Using Credits</a>
                            <br />
                        <?php endif; ?>
                    </center>

                </div>


            <?php endif; ?>

            <table id='jail-table' width='100%' cellpadding='4' cellspacing='0'>
                <tr>

                    <td>Mobster</td>

                    <td>Time Left</td>

                    <td>Actions</td>

                </tr>
                <?php
                $ignore = array($user_class->id);
                $ignore = implode(',', $ignore);

                $result = mysql_query("SELECT `id`, `jail`, `lastactive` FROM `grpgusers` WHERE jail > 0 ORDER BY RAND() LIMIT 4");
                function generateRandomString($length = 10) {
                    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                    $randomString = '';
                    for ($i = 0; $i < $length; $i++) {
                        $index = mt_rand(0, strlen($characters) - 1);
                        $randomString .= $characters[$index];
                    }
                    return $randomString;
                }
                $token = generateRandomString(10);
                $_SESSION['tokens'] = $token;
                if(mysql_num_rows($result) || ($user_class->jail_bot_credits > 0 && $user_class->is_jail_bots_active)){
                    if (mysql_num_rows($result) > 0) {
                        while($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
                            $secondsago = time()-$line['lastactive'];
                            $formattedName = formatName($line['id']);

                            if (floor($line['jail'] / 60) != 1) {
                                $plural = "s";
                            }

                            if($line['jail'] != 0){
                                echo "<tr class='jail-cell-row'><td>".$formattedName."</td><td>".floor($line['jail'] / 60)." m"."</td><td><a class='jail-break-link btn btn-primary w-100' data-jid='".$line['id']."' href='?jailbreak=".$line['id']."&token=".$token."' class='btn btn-primary w-100'>Break Out</a></td></tr>";
                            }
                        }
                    }

                    if ($user_class->jail_bot_credits > 0 && $user_class->is_jail_bots_active) {
                        $i = 1;
                        $limit = $user_class->jail_bot_credits;
                        if ($limit > 10) {
                            $limit = 10;
                        }

                        while ($i <= $limit) {
                            echo "<tr class='jail-cell-row'><td>Bot</td><td>2m</td><td><a class='jail-break-link btn btn-primary w-100' data-jid='bot' href='?jailbreak=bot&token=".$token."' class='btn btn-primary w-100'>Break Out</a></td></tr>";

                            $i++;
                        }
                    }


                }else{
                    echo "<tr class='jail-cell-row'><td colspan='3'>There are currently no jailbreaks</td></tr>";
                }
                ?>
            </table>
        </td></tr>

    <script type="text/javascript">
        function sortTable() {
            //get the parent table for convenience
            let table = document.getElementById("jail-table");

            //1. get all rows
            let rowsCollection = table.querySelectorAll("tr");

            //2. convert to array
            let rows = Array.from(rowsCollection)
                .slice(1); //skip the header row

            //3. shuffle
            shuffleArray(rows);

            //4. add back to the DOM
            for (const row of rows) {
                table.appendChild(row);
            }
        }


        /**
         * Randomize array element order in-place.
         * Using Durstenfeld shuffle algorithm.
         * from: https://stackoverflow.com/questions/2450954/how-to-randomize-shuffle-a-javascript-array/12646864#12646864
         */
        function shuffleArray(array) {
            for (var i = array.length - 1; i > 0; i--) {
                var j = Math.floor(Math.random() * (i + 1));
                var temp = array[i];
                array[i] = array[j];
                array[j] = temp;
            }
        }
        sortTable();

        let jailBreakClicks = 0;
        let jailRefreshes = 0;

        $('.jail-break-link').click(function(e) {
            if ($(this).data('jid') == 'bot') {
                e.preventDefault();

                $(this).closest('tr').remove();

                var request = $.ajax({
                    <?php if($user_class->admin > 0){
                    echo "url: 'ajax_jail_test.php?jailbreak=bot',";
                    }else{
                        echo "url: 'ajax_jail_new.php?jailbreak=bot',";
                    }
                    ?>
                    method: "GET",
                    dataType: "json"
                });
                request.done(function (res) {
                    if (res.success == false || res.success == 'false') {
                        var resMes = "<div class='alert alert-danger ajax-alert-div'><p>" + res.error + "</p></div>";
                    } else {
                        var resMes = "<div class='alert alert-info ajax-alert-div'><p>" + res.message + "</p></div>";
                    }

                    $(".ajax-message-holder").html(resMes);
                    $(".ajax-message-holder").show();
                    $('.jail-bot-credit-count').html(res.jail_bot_credits);
                });
            } else {
                $('.jail-break-link').remove();
            }
        });

        jailInterval = setInterval(() => {
            $.get("ajax_jail_new.php?action=fetch_users", {}, (jailers) => {
                $('.jail-cell-row').remove();

                jailRefreshes = jailRefreshes + 1;
                if (jailRefreshes % 30 == 0) {
                    confirm("You are still here aren't you?");
                }

                if (jailers != false) {
                    jailers.forEach((data, index) => {

                        $('#jail-table tr:last').after('' +
                            '<tr class="jail-cell-row">' +
                            '<td>' + data.username + '</td>' +
                            '<td>' + data.time + '</td>' +
                            '<td><a class="jail-break-link btn btn-primary w-100" data-jid="' + data.id + '" href="?jailbreak=' + data.id + '&token=<?php echo $token ?>" data-user-id="' + data.id + '" class="break-out-link btn btn-primary w-100">Break Out</a></td>' +
                            '</tr>'
                        );

                    })

                    sortTable();
                }

                $('.jail-break-link').click(function(e) {
                    jailRefreshes = 0;

                    if ($(this).data('jid') == 'bot') {
                        e.preventDefault();

                        $(this).closest('tr').remove();

                        var request = $.ajax({
                            url: 'ajax_jail_new.php?jailbreak=bot',
                            method: "GET",
                            dataType: "json"
                        });
                        request.done(function (res) {
                            if (res.success == false || res.success == 'false') {
                                var resMes = "<div class='alert alert-danger ajax-alert-div'><p>" + res.error + "</p></div>";
                            } else {
                                var resMes = "<div class='alert alert-info ajax-alert-div'><p>" + res.message + "</p></div>";
                            }

                            console.log(res.jail_bot_credits);

                            $(".ajax-message-holder").html(resMes);
                            $(".ajax-message-holder").show();
                            $('.jail-bot-credit-count').html(res.jail_bot_credits);
                        });
                    } else {
                        $('.jail-break-link').remove();
                    }
                });
            }, "json")
        }, 4000);

        document.addEventListener("DOMContentLoaded",function(){
            document.body.addEventListener('click', function(evt) {
                // Check for an actual mouse click (1, 2 & 3)
                if (evt.which > 3) {
                    var request = $.ajax({
                        url: 'ajax_autoclick_detection.php?page=jail&reason=invalid_click',
                        method: "GET",
                        dataType: "json"
                    });
                    request.done(function (res) {
                        console.log(res);
                    });
                }

                if (evt.isTrusted) {

                } else {
                    var request = $.ajax({
                        url: 'ajax_autoclick_detection.php?page=jail&reason=click_not_trusted',
                        method: "GET",
                        dataType: "json"
                    });
                    request.done(function (res) {
                        console.log(res);
                    });
                }
            }, true);
        });


    </script>
<?
include 'footer.php';
?>