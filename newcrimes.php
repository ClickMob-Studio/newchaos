<?php

require_once 'includes/cache.php';

include 'header.php';

$db->query("UPDATE grpgusers SET crimes = 'newcrimes', lastactive = unix_timestamp() WHERE id = ?");
$db->execute(array(
    $user_class->id
));

$db->query("SELECT `name`, mission.crimes as crimestarget, missions.crimes as crimesdone FROM missions LEFT JOIN mission ON missions.mid = mission.id WHERE `userid` = ? AND `completed` = \"no\" LIMIT 1");
$db->execute([$user_class->id]);
$activeMission = $db->fetch_row(true);

$tempItemUse = getItemTempUse($user_class->id);

$crimes = $cache->get("all_crimes");
if (empty($crimes)) {
    $db->query("SELECT * FROM crimes ORDER BY nerve DESC");
    $db->execute();
    $crimes = $db->fetch_row();
    $cache->setEx("all_crimes", 7200, json_encode($crimes)); // Cache for 2 hours
} else {
    $crimes = json_decode($crimes, true);
}

$filter_ids = [];
if ($tempItemUse['ghost_vacuum_time'] < time()) {
    $filter_ids[] = 51;
}

$currentQuestSeason = getCurrentQuestSeasonForUser($user_class->id);
if (!empty($currentQuestSeason['id'])) {
    $questSeasonUser = getQuestSeasonUser($user_class->id, $currentQuestSeason['id']);
    $questSeasonMissionUser = getQuestSeasonMissionUser($user_class->id, $currentQuestSeason['id']);
    $questSeasonMission = getQuestSeasonMission($user_class->id, $currentQuestSeason['id']);

    $requirements = $questSeasonMission['requirements'] ?? null;
    $progressObj = $questSeasonMissionUser['progress'] ?? null;

    $req = (is_object($requirements) && isset($requirements->whitecollar_fraud))
        ? (int) $requirements->whitecollar_fraud
        : null;

    $prog = (is_object($progressObj) && isset($progressObj->whitecollar_fraud))
        ? (int) $progressObj->whitecollar_fraud
        : 0;

    if ($req === null || $prog < $req) {
        $filter_ids[] = 52;
    }
}

$crimes = array_filter($crimes, function ($item) use ($filter_ids) {
    return !in_array((int) $item['id'], $filter_ids, true);
});

$rows = $crimes;
?>

<style>
    .gold {
        color: gold;
        /* Or any other color code you prefer */
        font-size: 24px;
        /* Adjust this value to increase or decrease the size of the stars */

    }

    .gray {
        color: gray;
        /* Or any other color code you prefer */
        font-size: 24px;
        /* Adjust this value to increase or decrease the size of the stars */

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
            perform_query("UPDATE grpgusers SET nerref = ? WHERE id = ?", [$user_class->nerref, $user_class->id]);
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
                <div class="flexele floaty" style="margin:3px;">
                    <hr style="border:0;border-bottom:thin solid #333;">
                    <center>
                        <div style="display:flex;min-height:60px;flex-direction:row;">
                            <div id="noti" class="alert alert-info" style="display: none;">
                                <p><img style="display:none;" id="spinner" src="images/ajax-loader.gif" /> <span
                                        class="response-text"></span></p>
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
                                $selected = false;
                                foreach ($rows as $row) {
                                    $db->query("SELECT `count` FROM crimeranks WHERE userid = ? AND crimeid = ?");
                                    $db->execute(array($user_class->id, $row['id']));
                                    $crimeRankResult = $db->fetch_row(true);

                                    // Debugging
                                    if ($crimeRankResult) {
                                        $crimeCount = (int) $crimeRankResult['count'];
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
                                        if (isset($user_class->completeUserResearchTypesIndexedOnId[16])) {
                                            if ($crimeCount >= 30000000) {
                                                $star_level = 6;
                                            } else {
                                                $star_level = 5;
                                            }
                                        } else {
                                            $star_level = 5;
                                        }
                                    } else {
                                        $star_level = 0; // No bonus if the conditions are not met
                                    }
                                    echo "<!-- Crime ID: {$row['id']}, Count: $crimeCount, Level: $star_level -->";
                                    // Output the option with the data-stars attribute
                                    $hasEnoughNerve = $row['nerve'] <= $user_class->nerve;

                                    $disabled = $hasEnoughNerve ? '' : 'disabled';

                                    $additionalStyles = '';
                                    if ($row['id'] == 51) {
                                        $additionalStyles = 'style="color: red; font-weight: bold;"';
                                    }
                                    echo '<option ' . $additionalStyles . ' value="' . $row['id'] . '" data-stars="' . $star_level . '" data-crime-count="' . $crimeCount . '" ' . $disabled . (empty($disabled) && !$selected ? 'selected' : '') . '>' . $row['name'] . ' | Cost: ' . $row['nerve'] . ' Nerve</option>';

                                    if ($hasEnoughNerve && !$selected) {
                                        $selected = true;
                                    }
                                }
                                ?>
                            </select>

                            <?php $rmOnly = ($user_class->rmdays <= 0) ? 'disabled' : ''; ?>
                            <select name="cm" id="cm" style="padding: 1em;">
                                <option value="1">1x</option>
                                <option value="2">2x</option>
                                <option value="4">4x</option>
                                <option value="15" <?php echo $rmOnly ?>>15x (VIP)</option>
                                <option value="30" <?php echo $rmOnly ?>>30x (VIP)</option>
                                <option value="50" <?php echo $rmOnly ?>>50x (VIP)</option>

                                <?php
                                $tempItemUse = getItemTempUse($user_class->id);
                                if ($tempItemUse['crime_15_multiplier_time'] > time()):
                                    ?>
                                    <option value="75">75x</option>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="star-rating" style="margin-top: 10px;"></div>
                        <div class="row">
                            <div class="col-md-4"></div>
                            <div class="col-md-4">
                                <p>Progress to next star:</p>
                                <div class="progress pb-star-holder" style="height:2rem;" role="progressbar"
                                    aria-valuenow="39.84" aria-valuemin="0" aria-valuemax="100" title="3984/10,000">
                                    <div class="progress-bar bg-success pb-star-bar"
                                        style="background-color: #ff6218 !important; width: 39.84%">
                                        <span class="pb-star-text"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4"></div>
                        </div>
                        <br />

                        <button type="button" id="acrimebtn2" onblue="finish(event);" onmouseup="finish(event);"
                            ontouchend="finish(event);" onmouseleave="finish(event);" onmousedown="start();"
                            ontouchstart="start();" style="padding: 1em; margin-bottom:5px;">Do Crimes</button>

                        <br />
                        <br><span style="color:red">Warning: Using the multiplier will increase points consumption
                            considerably!</span>

                        <h3>Recommendation: Use a <?php echo item_popup('Double EXP', 10) ?> to double your EXP and have
                            100% success rate! (1h)</h3>
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

<script>
    let doingcrime = false;
    const TARGET_MS = 70;

    const sleep = (ms) => new Promise(res => setTimeout(res, ms))

    var id = 0;
    const element = document.querySelector('.mission-crime-counter');
    var missionCrimesCount = 0;
    if (element) {
        missionCrimesCount = parseInt(element.dataset.value, 10);
    }

    function submitCrime(id, cm = 1) {
        $("#noti").show();
        $('#spinner').show();

        return $.ajax({
            url: "ajax_crimes2.php",
            method: "POST",
            data: { id, cm },
            dataType: "json"
        }).then(function (res) {
            if (res.error === 'refresh') finish();

            missionCrimesCount += cm;
            $('.mission-crime-counter').data('value', missionCrimesCount);
            $('.money').html(res.stats.money);
            $(".level").html(res.stats.level);
            $(".points").html(res.stats.points);
            $(".mb-points").html(res.stats.mb_points);
            $(".mb-money").html(res.stats.mb_money);
            $(".response-text").html(res.text);
            $("#missiontext").html(res.stats.mission);

            $('.after_title').eq(0).text(res.bars.energy.title);
            $('.after_title').eq(1).text(res.bars.nerve.title);
            $('.after_title').eq(2).text(res.bars.awake.title + '%');
            $('.after_title').eq(4).text(res.bars.exp.title + '%');

            $('.stat-bar.progress-bar').eq(1).width(res.bars.energy.percent + '%');
            $('.stat-bar.progress-bar').eq(2).width(res.bars.nerve.percent + '%');
            $('.stat-bar.progress-bar').eq(3).width(res.bars.awake.percent + '%');
            $('.expbar').width(res.bars.exp.percent + '%');

            return res;
        }).catch(function (jqXHR) {
            const res = jqXHR.responseJSON || {};
            if (res.error === 'refresh') finish();

            throw res;
        });
    }

    async function crimeLoop() {
        if (doingcrime) return;
        doingcrime = true;

        const id = $('#scrime').val();
        const cm = $('#cm').val();

        while (doingcrime && id > 0) {
            const t0 = performance.now();
            try {
                const res = await submitCrime(id, cm);

                let retryMs = 0;
                if (res && typeof res.retry_ms === 'number') {
                    retryMs = Math.max(0, res.retry_ms);
                }

                const elapsed = performance.now() - t0;
                const remaining = Math.max(0, TARGET_MS - elapsed, retryMs);
                if (remaining > 0) await sleep(remaining);

            } catch (err) {
                const remaining = Math.max(0, (err && err.remaining_ms) || TARGET_MS);
                await sleep(remaining);
            }
        }

        $('#spinner').hide();
    }


    $(document).ready(function () {
        $('#scrime').change(function () {
            var selectedOption = $(this).find('option:selected');
            var stars = selectedOption.data('stars');
            var starRatingHtml = '';

            for (var i = 1; i <= 5; i++) {
                starRatingHtml += i <= stars ? '<span class="gold">&#9733;</span>' : '<span class="gray">&#9733;</span>';
            }

            <?php if (isset($user_class->completeUserResearchTypesIndexedOnId[16])): ?>
                starRatingHtml += 6 <= stars ? '<span class="gold">&#9733;</span>' : '<span class="gray">&#9733;</span>';
            <?php endif; ?>


            var requiredCrimeCount = 10000;
            if (stars < 1) {
                var requiredCrimeCount = 10000;
            } else if (stars < 2) {
                var requiredCrimeCount = 100000;
            } else if (stars < 3) {
                var requiredCrimeCount = 1000000;
            } else if (stars < 4) {
                var requiredCrimeCount = 5000000;
            } else if (stars < 5) {
                var requiredCrimeCount = 15000000;
            } else if (stars < 6) {
                var requiredCrimeCount = 30000000;
            }
            var actualCrimeCount = selectedOption.data('crime-count');


            var pbStarWidth = actualCrimeCount / requiredCrimeCount * 100;
            $('.pb-star-bar').width(pbStarWidth + '%');
            $('.pb-star-holder').prop('title', addCommas(actualCrimeCount) + '/' + addCommas(requiredCrimeCount));
            $('.pb-star-text').html(addCommas(actualCrimeCount) + '/' + addCommas(requiredCrimeCount) + ' (' + pbStarWidth.toFixed(2) + '%' + ')');

            $('.star-rating').html(starRatingHtml);
        });

        $('#scrime').change();
    });


    function start() {
        if (doingcrime) return;
        crimeLoop();
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

    function finish(e) {
        if (e) e.preventDefault();
        doingcrime = false;
        $('#spinner').hide();
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

    fetch('ajax_crimes2.php', {
        method: 'POST', // or 'GET'
        body: JSON.stringify({/* your data here */ }),
        headers: { 'Content-Type': 'application/json' }
    })
        .then(data => {
            if (data.stats) {
                updateHeaderStats(data.stats.points, data.stats.money);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });

</script>


<meta http-equiv='refresh' content='900'>


<?php
include 'footer.php';
?>