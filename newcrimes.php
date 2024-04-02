<?php
 include 'header.php';
 error_reporting(0);
?>

<div class='box_top'>Speed Crimes</div>
						<div class='box_middle'>
							<div class='pad'>
                                <?php

if ($user_class->fbitime > 0) {
    diefun("You can't do crimes if you're in FBI Jail!");
}


$db->query("UPDATE grpgusers SET crimes = 'newcrimes', lastactive = unix_timestamp() WHERE id = ?");
$db->execute(array(
    $user_class->id
));
$m->set('lastcrimeload.'.$user_class->id, time());
$error = ($user_class->jail > 0) ? "You can't do crimes if you're in prison!" : "";
$error = ($user_class->hospital > 0) ? "You can't do crimes if you're in hospital!" : $error;
if (!empty($error))
    diefun($error);

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

    // echo '<script>var doingcrime = 0;';
    // echo 'function start(i){id=i,doingcrime=!0;var n=setInterval(function(){doingcrime&&0<id?docrime(id):(clearInterval(n),n=null)},10)}function finish(){doingcrime&&location.reload(),id=0,doingcrime=!1}function docrime(i){$("#noti").html("<img src=\'images/ajax-loader.gif\' />"),$.post("ajax_crimes.php",{id:i},function(i){var n=i.split("|");$("#noti").html(n[0]),$(".points").html(n[1]),$(".money").html(n[2]),$(".level").html(n[3]),$(".genBars").html(n[4])})}$(document).ready(function(){}),document.onblur=function(){finish()},window.onblur=function(){finish()},document.body.onmouseup=function(i){finish()};';
?>
<style>.gold {
    color: gold; /* Or any other color code you prefer */
        font-size: 24px; /* Adjust this value to increase or decrease the size of the stars */

}

.gray {
    color: gray; /* Or any other color code you prefer */
        font-size: 24px; /* Adjust this value to increase or decrease the size of the stars */

}</style>

    <?php
echo '<div class="crimebox">';
    if (time() < 1644451175)
        echo '<span style="color:red;font-weight:bold;display:block;text-align:center;font-size:1.3em;">Crimes are currently giving double experience!</span><br />';

    echo '<div style="display:flex;min-height:30px;flex-direction:row;"><img style="display:none;" id="spinner" src="images/ajax-loader.gif"/><div id="noti" style="height:16px;"></div></div>';

    $db->query("SELECT `name`, mission.crimes as crimestarget, missions.crimes as crimesdone FROM missions LEFT JOIN mission ON missions.mid = mission.id WHERE `userid` = ? AND `completed` = \"no\" LIMIT 1");
    $db->execute(array(
        $user_class->id
    ));
    $activeMission = $db->fetch_row()[0];
    if ($activeMission)
        echo "<div id='missiontext' style='font-size: 1.2em'>Active Mission: {$activeMission['name']} Crimes: {$activeMission['crimesdone']}/{$activeMission['crimestarget']}</div></center>";


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

        $db->query("SELECT * FROM crimes ORDER BY nerve DESC");
        $db->execute();
        $rows = $db->fetch_row();

        $crimesave = ($m->get('crimesave' . $user_class->id)) ? $m->get('crimesave' . $user_class->id) : "";

echo '<div class="floaty">';
echo '    <h3>Choose Your Crime</h3>';
echo '    <p>Select your crime and click and <strong>hold</strong> the button to do fast crimes</p>';

// Start of the selectors container
echo '<div class="selectors-container"  justify-content: start; align-items: center;">';
// Assuming $user_class->nerve holds the current user's nerve
$currentNerve = $user_class->nerve;

// Start of the crime dropdown
echo '<select name="crime" id="scrime" style="padding: 1em; margin-right: 10px;">';

foreach ($rows as $row) {
    // Your existing code for $state and $selected
    // ...

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
    $level = 1;
} elseif ($crimeCount >= 100000 && $crimeCount < 100000000000) {
    $level = 2;
} elseif ($crimeCount >= 10000000 && $crimeCount < 20000000) {
    $level = 3;
} elseif ($crimeCount >= 20000000 && $crimeCount < 40000000) {
    $level = 4;
} elseif ($crimeCount >= 40000000) {
    $level = 5;
}
echo "<!-- Crime ID: {$row['id']}, Count: $crimeCount, Level: $level -->";
    // Output the option with the data-stars attribute
      $hasEnoughNerve = $row['nerve'] <= $currentNerve;
    
    // Calculate if the option should be disabled
    $disabled = $hasEnoughNerve ? '' : 'disabled';

    echo '<option ' . $selected . ' ' . $state . ' value="' . $row['id'] . '" data-stars="' . $level . '" ' . $disabled . '>' . $row['name'] . ' | Cost: ' . $row['nerve'] . ' Nerve</option>';

}
echo '</select>';

// Multiplier dropdown
$rmOnly = ($user_class->rmdays <= 0) ? 'disabled' : '';
echo '<select name="cm" id="cm" style="padding: 1em;">';
echo '  <option value="1">1X</option>';
echo '  <option value="2">2X</option>';
echo '  <option value="4" ' . $rmOnly . '>4X (VIP Only)</option>';
echo '  <option value="10" ' . $rmOnly . '>10X (VIP Only)</option>';


echo '</select>';

// End of the selectors container
echo '</div>';

// Star ratings container
echo '<div class="star-rating" style="margin-top: 10px;"></div>';


        //}

        echo '<button id="acrimebtn2" onblue="finish();" onmouseup="finish();" ontouchend="finish();" onmouseleave="finish();"onmousedown="start();" ontouchstart="start();" style="padding: 1em; margin-bottom:5px;">Do Crimes</button>';

        echo '<br><span style="color:red">Warning: Using the multiplier will increase points consumption considerably!</span>';

        echo '<h3>Recommendation: Use a ' . item_popup('Double EXP', 10) . ' to double your EXP and have 100% success rate! (1h)</h3></div>';

        echo'<div class="flexcont">';
        echo'<div class="floaty" style="flex:1;margin-right:4px;">';
            echo'<h3>Nerve Refill</h3><br />';
            echo '<p>Enable automated nerve refills until rollover!</p>';
            echo'<br />';
            echo'Current Status: ' . $status . '<br />';
            echo'<br />';
            echo $button;
        echo'</div>';
    echo'</div>';
        echo '</td>';
    echo '</tr>';
echo '</div>';
?>

<script>
var doingcrime = false;
var id = 0;
var refresh = 75;


var submitCrime = function (id, cm=1) {
    //$("#noti").html("<img src='images/ajax-loader.gif' />")
    $('#spinner').show();

        var request = $.ajax({
            url: "ajax_crimes2.php",
            method: "POST",
            data: { id : id, cm : cm },
            dataType: "json"
        });

        request.fail(function(res) {
            if (res.error == 'refresh') {
                finish();
            }
        });

        request.done(function(res) {
            if (res.error == 'refresh') {
                finish();
            }
            console.log('test');
           
            $('.money').html(res.stats.money)
            $(".level").html(res.stats.level)
            $(".points").html(res.stats.points)
            $("#noti").html(res.text)
            $("#missiontext").html(res.stats.mission)

            $('.after_title').eq(0).text(res.bars.energy.title)
            $('.after_title').eq(1).text(res.bars.nerve.title)
            $('.after_title').eq(2).text(res.bars.awake.title + '%')
            $('.after_title').eq(4).text(res.bars.exp.title + '%')

            $('.stat-bar').eq(1).width(res.bars.energy.percent + '%')
            $('.stat-bar').eq(2).width(res.bars.nerve.percent + '%')
            $('.stat-bar').eq(3).width(res.bars.awake.percent + '%')
            $('.expbar').width(res.bars.exp.percent + '%')
        });

}



$(document).ready(function() {
    // This function updates the star rating when the selected crime changes
    $('#scrime').change(function() {
    var selectedOption = $(this).find('option:selected');
    var stars = selectedOption.data('stars');
        var starRatingHtml = '';

        // Create the star rating based on the data-stars attribute
        for (var i = 1; i <= 5; i++) {
            starRatingHtml += i <= stars ? '<span class="gold">&#9733;</span>' : '<span class="gray">&#9733;</span>';
        }

        // Update the star rating container
        $('.star-rating').html(starRatingHtml);
    });

    // Trigger the change event on page load to display the initial star rating
    $('#scrime').change();
    
    // Other JavaScript and jQuery code can follow here
});


function start() {
    var id = $('#scrime').val();
    var cm = $('#cm').val();
    doingcrime = true;
    var timerId = setInterval(function () {
        if (doingcrime) {
            if (id > 0) {
                submitCrime(id, cm);
            } else {
                clearInterval(timerId);
                timerId = null;
            }
        }
    }, refresh);
}

$(document).ready(function() {
    // Set the cookie when the selection changes
    $('#scrime').change(function() {
        var selectedCrime = $(this).val();
        setCookie("selectedCrime", selectedCrime, 30); // Change 30 to the number of days you want the cookie to last
    });

    // Get the selected option from the cookie and set it
    var selectedCrime = getCookie("selectedCrime");
    if (selectedCrime) {
        $("#scrime").val(selectedCrime).change(); // Trigger change event after setting the value
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
    if (doingcrime)
        location.reload();
    id = 0;
    doingcrime = false;
}
$(document).ready(function () {
    doingcrime = false;
    id = 0;
});






fetch('ajax_crimes2.php', {
    method: 'POST', // or 'GET'
    body: JSON.stringify({/* your data here */}),
    headers: {'Content-Type': 'application/json'}
})
.then(data => {
    // Assuming 'data' is the response from your server with the structure:
    // { "stats": { "points": "new points value", "money": "new money value" } }
    if(data.stats) {
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