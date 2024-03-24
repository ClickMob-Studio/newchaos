<?php
include "header.php";
$mins = array(
    'cash' => 10000,
    'points' => 100,
    'credits' => 10 // Ensuring the minimum value for credits is set
);
$db->query("SELECT id FROM fiftyfifty");
$db->execute();
$rows = $db->fetch_row();
$ids = array();
foreach($rows as $row)
    $ids[] = $row['id'];
$ids = implode(",", $ids);
echo <<<YYY
<script>
// Your existing JavaScript code remains unchanged
var ids = "$ids";
function takeaway(takeaway){
    $("#rtn").html("");
    $.post("ajax_5050.php", {takeaway : takeaway}, function(d){
        var results = d.split("|");
        if(results[0] != 'error'){
            $("#bet"+ takeaway).fadeOut(500,function(){
                $(this).remove();
            });
        } else {
            $("#rtn").html("<div id='error'>" + results[1] + "</div>");
        }
    });
}
function take(take){
    $("#rtn").html("");
    $.post("ajax_5050.php", {take : take}, function(d){
        var results = d.split("|");
        if(results[0] == 'take'){
            $("#rtn").html("<div id='" + results[1] + "'>" + results[2] + "</div>");
            $("#bet"+ take).fadeOut(500, function(){
                $(this).remove();
            });
        } else {
            $("#rtn").html("<div id='error'>" + results[1] + "</div>");
        }
    });
}
function post(curr){
    $("#rtn").html("");
    var ts=new Date().getTime();
    $.post("ajax_5050.php", {curr : curr, amnt : $("#" + curr + "amnt").val()}, function(d){
        var results = d.split("|");
        if(results[0] == 'success'){
            ids += ',' + results[1];
            $("#" + curr + "bets").append('<div id="t' + ts + '" style="display:none">' + results[2] + '</div>');
            $("#" + curr + "bets div#t" + ts).slideDown(500);
        } else {
            $("#rtn").html("<div id='error'>" + results[1] + "</div>");
        }
    });
}
function update(){
    var ts = new Date().getTime();
    $.post("ajax_5050.php", {update : ids}, function(d){
        var results = d.split("|");
        if(results[0]){
            $("#cashbets").append('<div id="t' + ts + '" style="display:none">' + results[0] + '</div>');
            $("#cashbets div#t" + ts).slideDown(500);
        }
        if(results[1]){
            $("#pointsbets").append('<div id="t' + ts + '" style="display:none">' + results[1] + '</div>');
            $("#pointsbets div#t" + ts).slideDown(500);
        }
        if(results[2]){
            $("#creditsbets").append('<div id="t' + ts + '" style="display:none">' + results[2] + '</div>');
            $("#creditsbets div#t" + ts).slideDown(500);
        }
        if(results[3]){
            var del = results[3].split(",");
            for(var i = 0; i < del.length; i++){
                $("#bet"+ del[i]).fadeOut(500, function(){
                    $(this).remove();
                });
            }
        }
        if(results[4]){
            ids = results[4];
        }
        if(results[5]){
            $(".money").html(results[5]);
        }
        if(results[6]){
            $(".points").html(results[6]);
        }
        if(results[7]){
            $(".credits").html(results[7]);
        }
    });
}
setInterval(update, 1000);
</script>
YYY;
echo'<div id="rtn"></div>';
echo'<div class="flexcont">';
    // Modified call to headbox for credits to display as GOLD
    echo headbox('cash');
    echo headbox('points');
    echo headbox('credits');
    echo'</div>';
echo'<hr style="border:0;border-bottom:thin solid #333;" />';
echo'<div class="flexcont" style="align-items:stretch;">';
echo fillboxes('cash');
echo fillboxes('points');
echo fillboxes('credits'); // Displaying existing bets for credits
echo'</div>';
include "footer.php";

function headbox($curr){
global $mins;
// Changing the display for 'credits' to 'GOLD'
$displayCurr = ($curr == 'credits') ? 'GOLD' : ucfirst($curr);
$rtn = '<div class="flexele floaty" style="margin:3px;">';
$rtn .= $displayCurr . ' 50/50';
$rtn .= '<hr style="border:0;border-bottom:thin solid #333;" />';
$rtn .= '<input type="text" id="' . $curr . 'amnt" placeholder="Min ' . prettynum($mins[$curr], ($curr == 'cash' ? 1: 0)) . '" />';
$rtn .= '<br />';
$rtn .= '<br />';
$rtn .= '<button onclick="post(\'' . $curr . '\');">Add ' . $displayCurr . ' Bet</button>';
$rtn .= '</div>';
return $rtn;
}

function fillboxes($curr){
global $user_class, $db;
$rtn = '';
$db->query("SELECT * FROM fiftyfifty WHERE currency = ?");
$db->execute(array($curr));
$rows = $db->fetch_row();
$rtn .= '<div class="flexele" id="' . $curr . 'bets">';
foreach($rows as $row){
$rtn .= '<div class="floaty" id="bet' . $row['id'] . '" style="margin:3px;">';
$rtn .= formatName($row['userid']);
$rtn .= '<hr style="border:0;border-bottom:thin solid #333;" />';
$rtn .= prettynum($row['amnt'], ($curr == 'cash' ? 1: 0));
$rtn .= '<br />';
if($user_class->id == $row['userid'])
$rtn .= '<button onclick="takeaway(' . $row['id'] . ');">Remove Bet</button>';
else
$rtn .= '<button onclick="take(' . $row['id'] . ');">Take Bet</button>';
$rtn .= '</div>';
}
$rtn .= '</div>';
return $rtn;
}

function dbcol($input){
return str_replace('cash', 'money', $input); // Handling the database column naming
}

?>