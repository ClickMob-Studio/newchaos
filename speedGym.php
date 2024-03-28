<?php
include 'header.php';
$m->set('lastcrimeload.'.$user_class->id, time());
?>
<div class='box_top'>Speed Gym</div>
						<div class='box_middle'>
							<div class='pad'>
                                <?php
if ($user_class->hospital > 0) {
    echo Message("You can't train at the gym if you are in the hospital.");
    include 'footer.php';
    die();
}
if ($printcaptcha != "") {
    echo $printcaptcha;
} else {
?>
<script>
var doingtrain = false, what = "";
function start(statName) {
    what = statName;
    doingtrain = true;
    var isMegaTrain = $('#mega_train').is(':checked') ? 'yes' : 'no';
    var intervalId = setInterval(function() {
        if (doingtrain && what) {
            trainrefill(what, isMegaTrain);
        } else {
            clearInterval(intervalId);
            intervalId = null;
        }
    }, 30);
}

function finish() {
    if (doingtrain) {
        location.reload();
    }
    what = "";
    doingtrain = false;
}

function trainrefill(stat, isMegaTrain) {
    $("#noti").html("<img src='images/ajax-loader.gif?' />");
    $.post("ajax_supergym.php", {
        amnt: <?php echo $user_class->maxenergy ?>,
        stat: stat,
        what: "trainrefill",
        mega_train: isMegaTrain // Pass the mega train status to the server
    }, function(response) {
        var info = response.split("|");
        $("#noti").html(info[0]);
        $(".points").html(info[1]);
        $("#" + stat + "amnt").html(info[2]);
        if (info[3]) {
            $("#strength").val(info[3]);
            $("#defense").val(info[3]);
            $("#speed").val(info[3]);
        }
    });
}

document.onblur = function() { finish(); };
window.onblur = function() { finish(); };
document.body.onmouseup = function() { finish(); };
</script>
<?php
    echo "
    <br />
    <div id='noti' style='height:16px'></div>
<div class='contenthead floaty'>
<span style='margin: 0; line-height: 27px; text-transform: uppercase; font-size: 20px; text-align: left; text-indent: 25px;'>
<h4>TRAINING CENTER</h4>
    <table id='newtables' class='altcolors' style='width:100%;'>
        <tr>
            <th>STRENGTH</th>
            <th>DEFENSE</th>
            <th>SPEED</th>
        </tr>
        <tr>
            <td><span id='strengthamnt'>".prettynum($user_class->strength)."</span> [Ranked: ".getRank("$user_class->id", "strength")."]</td>
            <td><span id='defenseamnt'>".prettynum($user_class->defense)."</span> [Ranked: ".getRank("$user_class->id", "defense")."]</td>
            <td><span id='speedamnt'>".prettynum($user_class->speed)."</span> [Ranked: ".getRank("$user_class->id", "speed")."]</td>
        </tr>
        <tr>
            <td><button onmousedown='start(\"strength\");' onmouseup='finish();' ontouchend='finish();' onmouseleave='finish();' ontouchstart='start(\"strength\");'>Strength + Refills</button></td>
            <td><button onmousedown='start(\"defense\");' onmouseup='finish();' ontouchend='finish();' onmouseleave='finish();' ontouchstart='start(\"defense\");'>Defense + Refills</button></td>
            <td><button onmousedown='start(\"speed\");' onmouseup='finish();' ontouchend='finish();' onmouseleave='finish();' ontouchstart='start(\"speed\");'>Speed + Refills</button></td>
        </tr>
        <tr>
            <td colspan='3'><span style='color:red;font-weight:bold;'>Click and hold down the mouse on the stat + refills button.</span></td>
        </tr>
    </table></div>";
}
include 'footer.php';
?>