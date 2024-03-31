<?php
include 'header.php';

$result2 = $conn->query("UPDATE `events` SET `viewed` = '2' WHERE `to`={$user_class->id}");
if (isset($_GET['deleteall']) && $_GET['deleteall'] == 1) {
    $result = $conn->query("DELETE FROM `events` WHERE `to` = {$user_class->id}");
    echo Message("All your events have been deleted.");
}
if (isset($_GET['delete']) && $_GET['delete'] != "") {
    $result = $conn->query("DELETE FROM `events` WHERE `id`='{$_GET['delete']}' AND `to` = {$user_class->id}");
    echo Message("You have deleted that event.");
}
?>
 
<form method="GET" class="d-inline float-right">
     <input type="text" id="filterInput" placeholder="Search for an Event" name="search" <?php if (isset($_GET['search'])){?>value="<?php echo $_GET['search'] ?>"<?php } ?>>
     <input type="submit" value="Search">
</form>

<hr>
<div class="contenthead floaty">
<span style="margin: 0; line-height: 27px; text-transform: uppercase; font-size: 20px; text-align: left; text-indent: 25px;">
<h4>Your Events</h4></span>
<table id="newtables" style="width:100%; text-align: left;">
<tr>
<th><b>Description</b></th>
<th><b>Received</b></th>
</tr>
<?php
$statement = $conn->prepare("SELECT COUNT(*) FROM `events` WHERE `to` = ?");
$statement->bind_param("i", $user_class->id);
$statement->execute();
$statement->bind_result($numrows);
$statement->fetch();
$statement->close();

$rowsperpage = 30;
$totalpages  = ceil($numrows / $rowsperpage);
$searchString = null;

if ($totalpages <= 0)
    $totalpages = 1;
else
    $totalpages = ceil($numrows / $rowsperpage);

if (isset($_GET['page']) && is_numeric($_GET['page']))
    $currentpage = (int) $_GET['page'];
else
    $currentpage = 1;

if ($currentpage > $totalpages)
    $currentpage = $totalpages;
if ($currentpage < 1)
    $currentpage = 1;

if (isset($_GET['search']) && $_GET['search'] != ''){
    $searchString = '%' . $_GET['search'] . '%';
}

$offset = ($currentpage - 1) * $rowsperpage;

if ($searchString == null) {
    $statement = $conn->prepare("SELECT * FROM `events` WHERE `to` = ? ORDER BY `timesent` DESC LIMIT ?, ?");
    $statement->bind_param("iii", $user_class->id, $offset, $rowsperpage);
    $statement->execute();
} else {
    $statement = $conn->prepare("SELECT * FROM `events` WHERE `to` = ? AND `text` like ? ORDER BY `timesent` DESC LIMIT ?, ?");
    $statement->bind_param("issi", $user_class->id, $searchString, $offset, $rowsperpage);
    $statement->execute();
}

$result = $statement->get_result();

while ($row = $result->fetch_assoc()) {
    $text = str_replace('[-_USERID_-]', formatName($row['extra']), $row['text']);
    echo "<tr><td width='67%'>" . $text . "</td><td width='31%'>" . date("d F Y, g:ia", $row['timesent']) . "</td><td width='2%'><a href='events.php?delete={$row['id']}'><span class='delete'>&nbsp;X&nbsp;</span></a></td></tr>";
}
?>
</table>
<span style="display: none" id="dataHolder" data-offset="<?php echo $offset ?>" data-rowsPerPage="30" data-userId ="<?php echo $user_class->id ?>"></span>
<br />
<table width="100%">
<tr>
<td align="left">
<?php
$range = 2;
if ($currentpage > 1)
    echo " <a href='?page=1'><<</a> ";
for ($x = ($currentpage - $range); $x < (($currentpage + $range) + 1); $x++)
    if (($x > 0) && ($x <= $totalpages))
        if ($x == $currentpage)
            echo " [<b>$x</b>] ";
        else
            echo " <a href='?page=$x'>$x</a> ";
if ($currentpage < $totalpages)
    echo " <a href='?page=$totalpages'>>></a> ";
?>
</td>
<td align="right">[<a href="events.php?deleteall=1">delete all events</a>]</td>
</tr>
</table>
</td></tr>
<?php
include 'footer.php';
function gangName($id){
	$gang = new Gang($id);
	return $gang->formattedname;
}
?>
<script>
    $(document).ready(function (){
        let offset = $('#dataHolder').attr('data-offset');
        let rowsPerPage = $('#dataHolder').attr('data-rowsPerPage');
        let uid = $('#dataHolder').attr('data-userId');
        $.ajax({
            type: "POST",
            url: "ajax_event_search.php",
            data: { offset: offset, rowsPerPage: rowsPerPage, userId: uid },
            success: function (response){
                let data = JSON.parse(response);
                let innerHtml;
                $.each(data.events, function (key, item){
                    innerHtml = innerHtml+item
                })
                $('.eventsTable').html(innerHtml);
            }
        });
    });
     $('#filterInput').keyup(function (){
         let value = $(this).val();
         let offset = $('#dataHolder').attr('data-offset');
         let rowsPerPage = $('#dataHolder').attr('data-rowsPerPage');
         let uid = $('#dataHolder').attr('data-userId');
         $.ajax({
             type: "POST",
             url: "ajax_event_search.php",
             data: { search: value, offset: offset, rowsPerPage: rowsPerPage, userId: uid },
             success: function (response){
                 let data = JSON.parse(response);
                 let innerHtml;
                 $.each(data.events, function (key, item){
                     innerHtml = innerHtml+item
                 })
                 $('.eventsTable').html(innerHtml);
             }
         });
     })
</script>
