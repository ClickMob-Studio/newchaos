<?php
include "header.php";

?>
	
	<div class='box_top'>50/50</div>
						<div class='box_middle'>
							<div class='pad'>
								<?php
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
            $("#rtn").html("<div  class='floaty1' id='error'>" + results[1] + "</div>");
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
            $("#rtn").html("<div class='floaty1' id='error'>" + results[1] + "</div>");
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
            $("#rtn").html("<div class='floaty1' id='error'>" + results[1] + "</div>");
        }
    });
}
function update() {
    $.post("ajax_5050.php", { update: ids }, function(d) {
        var results = d.split("|");
        // Update the DOM with new bets
        if (results[0]) {
            $("#cashbets").append(results[0]);
        }
        if (results[1]) {
            $("#pointsbets").append(results[1]);
        }
        if (results[2]) {
            $("#creditsbets").append(results[2]);
        }

        // Handle deletions
        var delIDs = results[3].split(",");
        for (var i = 0; i < delIDs.length; i++) {
            $("#bet" + delIDs[i]).fadeOut(500, function() {
                $(this).remove();
            });
        }

        // Update the known ids to include only current bets
        ids = results[4];

        // Update totals
        $(".money").html(results[5]);
        $(".points").html(results[6]);
        $(".credits").html(results[7]);
    });
}


setInterval(update, 1000);
</script>
YYY;
echo'<div id="rtn"></div>';
echo "<table>";
echo "<tr>";
echo "<td>";
echo headbox('cash');
echo "</td>";
echo "<td>";
echo headbox('points');
echo "</td>";
echo "<td>";
echo headbox('credits');
echo "</td>";
echo "</tr>";
echo "</table>";

echo "<hr style='border:0;border-bottom:thin solid #333;' />";

echo "<table>";
?>
<thead>
    <th>Cash</th>
</th>
    <th>Points</th>
    <th>Credits</th>
</thead>
</thead>
<?php
echo "<tr>";
echo "<td>";
echo fillboxes('cash');
echo "</td>";
echo "<td>";
echo fillboxes('points');
echo "</td>";
echo "<td>";
echo fillboxes('credits');
echo "</td>";
echo "</tr>";
echo "</table>";

include "footer.php";

function headbox($curr){
    global $mins;
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
    $rtn = '<table id="' . $curr . 'bets">'; // Ensure the table has an ID that JavaScript expects
    $db->query("SELECT * FROM fiftyfifty WHERE currency = ?");
    $db->execute(array($curr));
    $rows = $db->fetch_row();
    foreach($rows as $row){
        $rtn .= '<tr id="bet' . $row['id'] . '">'; // Ensure each bet row has a unique ID that JavaScript can reference
        $rtn .= '<td>';
        $rtn .= formatName($row['userid']);
        $rtn .= '</td>';
        $rtn .= '<td>';
        $rtn .= prettynum($row['amnt'], ($curr == 'cash' ? 1: 0));
        $rtn .= '</td>';
        $rtn .= '<td>';
        if($user_class->id == $row['userid'])
            $rtn .= '<button onclick="takeaway(' . $row['id'] . ');">Remove Bet</button>';
        else
            $rtn .= '<button onclick="take(' . $row['id'] . ');">Take Bet</button>';
        $rtn .= '</td>';
        $rtn .= '</tr>';
    }
    $rtn .= '</table>';
    return $rtn;
}


function dbcol($input){
    return str_replace('cash', 'money', $input);
}

?>