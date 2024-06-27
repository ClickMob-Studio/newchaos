<?php
include 'header.php';
if($user_class->admin < 1){
    exit();
}
$db->query("UPDATE grpgusers SET crimes = 'newcrimes', lastactive = unix_timestamp() WHERE id = ?");
$db->execute(array(
    $user_class->id
));
$m->set('lastcrimeload.'.$user_class->id, time());
error_reporting(0);

$db->query("SELECT `name`, mission.crimes as crimestarget, missions.crimes as crimesdone FROM missions LEFT JOIN mission ON missions.mid = mission.id WHERE `userid` = ? AND `completed` = \"no\" LIMIT 1");
$db->execute(array(
    $user_class->id
));
$activeMission = $db->fetch_row()[0];

$db->query("SELECT * FROM crimes ORDER BY nerve DESC");
$db->execute();
$rows = $db->fetch_row();

$crimesave = ($m->get('crimesave' . $user_class->id)) ? $m->get('crimesave' . $user_class->id) : "";
?>

<style>
.gold {
    color: gold; /* Or any other color code you prefer */
    font-size: 24px; /* Adjust this value to increase or decrease the size of the stars */
}

.gray {
    color: gray; /* Or any other color code you prefer */
    font-size: 24px; /* Adjust this value to increase or decrease the size of the stars */
}
</style>

<h1>Crimes</h1>

<?php
$error = ($user_class->fbitime > 0) ? "You can't do crimes if you're in FBI Jail!" : "";
$error = ($user_class->jail > 0) ? "You can't do crimes if you're in prison!" : "";
$error = ($user_class->hospital > 0) ? "You can't do crimes if you're in hospital!" : $error;
if (!empty($error)) {
    diefun($error);
}

if (isset($_GET['ner'])) {
    switch ($_GET['ner']) {
        case 0:
            if ($user_class->nerref != 0)
                diefun("Nice Try.");
            if ($user_class->points < 250)
                diefun("You do not have enough points.");
            $user_class->points -= 250;
            $user_class->nerref = 2;
            $db->query("UPDATE grpgusers SET nerref = ?, points = ?, nerreftime = unix_timestamp() WHERE id = ?");
            $db->execute(array(
                $user_class->nerref,
                $user_class->points,
                $user_class->id
            ));
            break;
        case 1:
            if ($user_class->nerref == 0)
                diefun("Nice Try.");
            $user_class->nerref = 2;
            $db->query("UPDATE grpgusers SET nerref = ? WHERE id = ?");
            $db->execute(array(
                $user_class->nerref,
                $user_class->id
            ));
            mysql_query("UPDATE grpgusers SET nerref = $user_class->nerref WHERE id = $user_class->id");
            break;
        case 2:
            if ($user_class->nerref == 0)
                diefun("Nice Try.");
            $user_class->nerref = 1;
            $db->query("UPDATE grpgusers SET nerref = ? WHERE id = ?");
            $db->execute(array(
                $user_class->nerref,
                $user_class->id
            ));
            break;
    }
}
?>
<table>
    <tbody>
        <tr>
            <td>
                <div class="flexele floaty" style="margin:3px;"><hr style="border:0;border-bottom:thin solid #333;">
                    <center>
                        <div style="display:flex;min-height:60px;flex-direction:row;">
                            <div id="noti" class="alert alert-info" style="display: none;">
                                <p><img style="display:none;" id="spinner" src="images/ajax-loader.gif"/> <span class="response-text"></span></p>
                            </div>
                        </div>
                    </center>

                    <?php if ($activeMission) {
                        echo "<div id='missiontext' style='font-size: 1.2em'>Active Mission: {$activeMission['name']} Crimes: {$activeMission['crimesdone']}/{$activeMission['crimestarget']}</div></center>";
                    } ?>

                    <center>
                        <h3><u>Choose Your Crime</u></h3>
                        <p>Select your crime and click and <strong>hold</strong> the button to do fast crimes</p>

                        <div class="selectors-container">
                            <select name="crime" id="scrime" style="padding: 1em; margin-right: 10px; width: 100%;">
                                <?php
                                foreach ($rows as $row) {
                                    $db->query("SELECT `count` FROM crimeranks WHERE userid = ? AND crimeid = ?");
                                    $db->execute(array($user_class->id, $row['id']));
                                    $crimeRankResult = $db->fetch_row(true);

                                    // Debugging
                                    if ($crimeRankResult) {
                                        $crimeCount = (int)$crimeRankResult['count'];
                                        // Log or echo to check the value
                                        error_log("Crime ID: {$row['id']}, Count: {$crimeCount}");
                                    } else {
                                        $crimeCount = 0;
                                    }
                                    if ($crimeCount >= 10000 && $crimeCount < 100000) {
                                        $star_level = 1;
                                    } elseif ($crimeCount >= 100000 && $crimeCount < 1000000) {
                                        $star_level = 2;
                                    } elseif ($crimeCount >= 1000000 && $crimeCount < 5000000) {
                                        $star_level = 3;
                                    } elseif ($crimeCount >= 5000000 && $crimeCount < 15000000) {
                                        $star_level = 4;
                                    } elseif ($crimeCount >= 15000000) {
                                        $star_level = 5;
                                    } else {
                                        $star_level = 0; // No bonus if the conditions are not met
                                    }
                                    echo "<!-- Crime ID: {$row['id']}, Count: $crimeCount, Level: $star_level -->";
                                    // Output the option with the data-stars attribute
                                    $hasEnoughNerve = $row['nerve'] <= $user_class->nerve;

                                    $disabled = $hasEnoughNerve ? '' : 'disabled';

                                    echo '<option value="' . $row['id'] . '" data-stars="' . $star_level . '" data-crime-count="' . $crimeCount . '" ' . $disabled . '>' . $row['name'] . ' | Cost: ' . $row['nerve'] . ' Nerve</option>';
                                }
                                ?>
                            </select>

                            <?php $rmOnly = ($user_class->rmdays <= 0) ? 'disabled' : ''; ?>
                            <select name="cm" id="cm" style="padding: 1em;">
                                <option value="1">1X</option>
                               <!-- <option value="2">2X</option> -->
                                <!--<option value="4" --><?php // echo $rmOnly ?><!-->4X (VIP Only)</option>-->
                                <option value="10" <?php echo $rmOnly ?>>10X (VIP Only)</option>
                            </select>
                        </div>

                        <div class="star-rating" style="margin-top: 10px;"></div>
                        <div class="row">
                            <div class="col-md-4"></div>
                            <div class="col-md-4">
                                <p>Progress to next star:</p>
                                <div class="progress pb-star-holder" style="height:2rem;" role="progressbar" aria-valuenow="39.84" aria-valuemin="0" aria-valuemax="100" title="3984/10,000">
                                    <div class="progress-bar bg-success pb-star-bar" style="background-color: #ff6218 !important; width: 39.84%">
                                        <span class="pb-star-text"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4"></div>
                        </div>
                        <br />

                        <button id="acrimebtn2" onblue="finish();" onmouseup="finish();" ontouchend="finish();" onmouseleave="finish();" onmousedown="start();" ontouchstart="start();" style="padding: 1em; margin-bottom:5px;">Do Crimes</button>

                        <br />
                        <br><span style="color:red">Warning: Using the multiplier will increase points consumption considerably!</span>

                        <h3>Recommendation: Use a <?php echo item_popup('Double EXP', 10) ?> to double your EXP and have 100% success rate! (1h)</h3>
                        <hr />

                        <div class="flexcont">
                            <div class="floaty" style="flex:1;margin-right:4px;">
                                <h2><u>Nerve Refill</u></h2>
                                <p>Enable automated nerve refills until rollover!</p>
                                <br />
                                <?php
                                switch ($user_class->nerref) {
                                    case 0:
                                        $status = "<span style='color:red;'>[Not Paid For]</span>";
                                        $button = '<button onClick="if(confirm(\'Are you sure you want enable nerve refills until rollover?\')){window.location.href = \'?ner=0\';}">Buy(250 Points)</button>';
                                        break;
                                    case 1:
                                        $status = "<span style='color:orange;'>[Paid For/Disabled]</span>";
                                        $button = "<a href='?ner=1'><button>Enable</button></a>";
                                        break;
                                    case 2:
                                        $status = "<span style='color:green;'>[Paid For/Enabled]</span>";
                                        $button = "<a href='?ner=2'><button>Disable</button></a>";
                                        break;
                                }
                                ?>
                                Current Status: <?php echo $status ?><br />
                                <br />
                                <?php echo $button ?>
                            </div>
                        </div>

                    </center>
                </div>
            </td>
        </tr>
    </tbody>
</table>

<script>var doingcrime = false;
var lastExecution = 0;
var minInterval = 25; // Minimum interval in milliseconds

var submitCrime = function (id, cm = 1) {
    $("#noti").show();
    $('#spinner').show();

    var request = $.ajax({
        url: "ajax_crimes.php",
        method: "POST",
        data: { id: id, cm: cm },
        dataType: "json"
    });

    request.fail(function (res) {
        console.log(res);
        $('#spinner').hide();
        if (res.responseJSON && res.responseJSON.error == 'refresh') {
            finish();
        }
    });

    request.done(function (res) {
        $('#spinner').hide();
        if (res.error) {
            location.reload();
        }
        if (res.text) {
            $(".response-text").html(res.text);
        }
        if (res.stats) {
            console.log("Stats:", res.stats);  // Debugging line
            $('.money').html(res.stats.money);
            $(".level").html(res.stats.level);
            $(".points").html(res.stats.points);
            $(".mb-points").html(res.stats.mb_points);
            $(".mb-money").html(res.stats.mb_money);
            $("#missiontext").html(res.stats.mission);

            $('.after_title').eq(0).text(res.bars.energy.title);
            $('.after_title').eq(1).text(res.bars.nerve.title);
            $('.after_title').eq(2).text(res.bars.awake.title + '%');
            $('.after_title').eq(4).text(res.bars.exp.title + '%');

            $('.stat-bar').eq(1).width(res.bars.energy.percent + '%');
            $('.stat-bar').eq(2).width(res.bars.nerve.percent + '%');
            $('.stat-bar').eq(3).width(res.bars.awake.percent + '%');
            $('.expbar').width(res.bars.exp.percent + '%');
        }
        // Stop crime if no enough nerve
        if (res.stats && res.stats.bars.nerve.percent === 0) {
            finish();
        }
    });
}

function start() {
    if (doingcrime) return;

    var id = $('#scrime').val();
    var cm = $('#cm').val();
    doingcrime = true;

    var resetAction = function() {
        doingcrime = false;
        clearInterval(timerId);
        location.reload();
    };

    var timerId = setInterval(function () {
        var now = Date.now();
        if (doingcrime && (now - lastExecution >= minInterval)) {
            if (id > 0) {
                submitCrime(id, cm);
                lastExecution = now;
            } else {
                resetAction();
            }
        }
    }, 25);
    document.addEventListener('mouseup', resetAction, { once: true });
    document.addEventListener('touchend', resetAction, { once: true });
    document.addEventListener('mouseleave', resetAction, { once: true });
    document.addEventListener('blur', resetAction, { once: true });
    document.addEventListener('contextmenu', resetAction, { once: true });
}

$(document).ready(function() {
    $('#scrime').change(function() {
        var selectedOption = $(this).find('option:selected');
        var stars = selectedOption.data('stars');
        var starRatingHtml = '';

        for (var i = 1; i <= 5; i++) {
            starRatingHtml += i <= stars ? '<span class="gold">&#9733;</span>' : '<span class="gray">&#9733;</span>';
        }

        var requiredCrimeCount = 10000;
        if (stars < 1) {
            requiredCrimeCount = 10000;
        } else if (stars < 2) {
            requiredCrimeCount = 100000;
        } else if (stars < 3) {
            requiredCrimeCount = 1000000;
        } else if (stars < 4) {
            requiredCrimeCount = 5000000;
        } else if (stars < 5) {
            requiredCrimeCount = 15000000;
        }
        var actualCrimeCount = selectedOption.data('crime-count');

        var pbStarWidth = actualCrimeCount / requiredCrimeCount * 100;
        $('.pb-star-bar').width(pbStarWidth + '%');
        $('.pb-star-holder').prop('title', addCommas(actualCrimeCount) + '/' + addCommas(requiredCrimeCount));
        $('.pb-star-text').html(addCommas(actualCrimeCount) + '/' + addCommas(requiredCrimeCount) + ' (' + pbStarWidth.toFixed(2) + '%' + ')');

        $('.star-rating').html(starRatingHtml);
    });

    $('#scrime').change();

    $('#scrime').change(function() {
        var selectedCrime = $(this).val();
        setCookie("selectedCrime", selectedCrime, 30);
    });

    var selectedCrime = getCookie("selectedCrime");
    if (selectedCrime) {
        $("#scrime").val(selectedCrime).change();
    }
});

function getCookie(name) {
    var value = "; " + document.cookie;
    var parts = value.split("; " + name + "=");
    if (parts.length == 2) return parts.pop().split(";").shift();
}

function setCookie(name, value, days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "") + expires + "; path=/";
}

document.onblur = function () {
    finish();
}
window.onblur = function () {
    finish();
}
document.body.onmouseup = function (evt) {
    finish();
}
document.addEventListener('orientationchange', finish);

function finish() {
    if (doingcrime) {
        doingcrime = false;
        location.reload();
    }
    id = 0;
}

$(document).ready(function () {
    doingcrime = false;
    id = 0;
});

function addCommas(nStr) {
    nStr += '';
    x = nStr.split('.');
    x1 = x[0];
    x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + ',' + '$2');
    }
    return x1 + x2;
}


</script>

<meta http-equiv='refresh' content='900'>

<?php
include 'footer.php';
?>