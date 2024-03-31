<?php
include 'header.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

?>
<div class='box_top'>Events</div>
<div class='box_middle'>
    <div class='pad'>
        <?php
        // Assuming $conn is your PDO connection object
        $userId = $user_class->id;
        $conn->prepare("UPDATE `events` SET `viewed` = '2' WHERE `to` = :userId")->execute([':userId' => $userId]);

        if (isset($_GET['deleteall']) && $_GET['deleteall'] == 1) {
            $conn->prepare("DELETE FROM `events` WHERE `to` = :userId")->execute([':userId' => $userId]);
            echo Message("All your events have been deleted.");
        }

        if (isset($_GET['delete']) && $_GET['delete'] != "") {
            $deleteId = $_GET['delete'];
            $conn->prepare("DELETE FROM `events` WHERE `id` = :deleteId AND `to` = :userId")->execute([':deleteId' => $deleteId, ':userId' => $userId]);
            echo Message("You have deleted that event.");
        }
        ?>

        <form method="GET" class="d-inline float-right">
            <input type="text" id="filterInput" placeholder="Search for an Event" name="search" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
            <input type="submit" value="Search">
        </form>

        <hr>
        <table id="newtables" style="width:100%;">
            <tr>
                <th><b>Description</b></th>
                <th><b>Received</b></th>
            </tr>
            <?php
            $searchString = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : null;
            $stmt = $conn->prepare("SELECT * FROM `events` WHERE `to` = :userId " . ($searchString ? "AND `text` LIKE :searchString " : "") . "ORDER BY `timesent` DESC LIMIT :offset, :rowsperpage");
            $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
            if ($searchString) {
                $stmt->bindValue(':searchString', $searchString, PDO::PARAM_STR);
            }
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->bindValue(':rowsperpage', $rowsperpage, PDO::PARAM_INT);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($results as $row) {
                $text = str_replace('[-_USERID_-]', formatName($row['extra']), $row['text']);
                // Assuming formatName() is defined elsewhere
                echo "<tr><td width='67%'>" . htmlspecialchars($text) . "</td><td width='31%'>" . date("d F Y, g:ia", $row['timesent']) . "</td><td width='2%'><a href='events.php?delete=" . htmlspecialchars($row['id']) . "'><span class='delete'>&nbsp;X&nbsp;</span></a></td></tr>";
            }
            ?>
        </table>
        <span style="display: none" id="dataHolder" data-offset="<?= htmlspecialchars($offset) ?>" data-rowsPerPage="30" data-userId ="<?= htmlspecialchars($userId) ?>"></span>
        <br /><br />
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
</div>
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
