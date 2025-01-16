<?php
include 'header.php';
?>
<!-- Add a div for displaying the success message -->
<div id="messageBox" class="alert" style="display: none;"></div>
<div class='box_top'>Hospital</div>
						<div class='box_middle'>
							<div class='pad'>
                        <?php
if ($_GET['buy'] == "hospital") {
   $cost = $user_class->level * 300;
   echo Message("Are you sure you want to buy out of hospital for $cost? <br><a href='emergencyroom.php?buy=hospitalyes'>Continue</a><br /><a href='rmstore.php'>No thanks!</a>");
   include 'footer.php';
   die();
}
if ($_GET['buy'] == "hospitalyes") {
   $cost = $user_class->level * 300;
   if ($user_class->bank > $cost) {
	   if($user_class->hospital){
		  $newcredit = $user_class->bank - $cost;
		  $puremaxhp = $user_class->puremaxhp;

		  $newhosp   = 0;
		  $time      = time();
		  $result    = mysql_query("UPDATE `grpgusers` SET `bank`='" . $newcredit . "', `hp`='" . $puremaxhp . "', `hospital`='" . $newhosp . "' WHERE `id`='" . $_SESSION['id'] . "'");
		  echo Message("You spent $$cost and brought yourself out of the hospital.");
	   } else {
		   echo Message("You are not in the hospital!");
	   }
   } else
      echo Message("You don't have enough money in the bank. You need $$cost");
}
if ($user_class->hospital != "0" && ($user_class->hhow != "bombed" && $user_class->hhow != "abombed")) {
   $cost = $user_class->level * 300;
   echo "- <a href='hospital.php?buy=hospitalyes'><font color=red><b>Buy Out for $$cost<b></font></a></br></br>";

   $meds = '11, 12, 13, 14';
   $db->query("SELECT * FROM inventory LEFT JOIN items ON inventory.itemid = items.id WHERE itemid IN ($meds) AND userid = ?");
   $db->execute(array($user_class->id));
   $meds = $db->fetch_row();

   echo "<div style='text-align:center;display: flex;flex-direction: row;justify-content: space-evenly;align-items: center;margin-bottom:10px;'>";

   foreach ($meds as $med) {
       echo "<div>";
       echo image_popup($med['image'], $med['id']);
       echo '<br />';
       echo item_popup($med['itemname'], $med['id']) . ' [x' . $med['quantity'] . ']</br>';

       if ($med['id'] == 14) { // Use ajax for item ID 14
           echo '<button class="use-btn button-sm btn btn-primary" data-item-id="' . $med['id'] . '">Use</button>';
       } else { // Regular link for other items
           echo '<a class="button-sm btn btn-secondary" href="inventory.php?use=' . $med['id'] . '">Use</a>';
       }
       echo "</div>";
   }
   echo "</div>";
}
?>



<!-- Include JavaScript -->
<script>
   $(document).on('click', '.use-btn', function () {
       var itemId = $(this).data('item-id'); // Get item ID from button

       if (!itemId) {
           showMessage("Invalid item selected.", false);
           return;
       }

       $.ajax({
           url: 'ajax_use_item.php', // AJAX endpoint
           type: 'GET',
           dataType: 'json',
           data: { use: itemId },
           success: function (response) {
               if (response.success) {
                   // Show success message in the Bootstrap alert
                   showMessage(response.message, true);
               } else {
                   showMessage(response.message || "An error occurred while using the item.", false);
               }
           },
           error: function () {
               showMessage("Error processing your request.", false);
           }
       });
   });

   function showMessage(message, isSuccess) {
       var messageBox = $("#messageBox");
       messageBox
           .text(message)
           .removeClass("alert-success alert-danger")
           .addClass(isSuccess ? "alert-success" : "alert-danger")
           .fadeIn();

       // Auto-hide the alert after 5 seconds
       setTimeout(function () {
           messageBox.fadeOut();
       }, 5000);
   }
</script>

<style>
   #messageBox {
       margin: 20px auto;
       width: 50%;
       text-align: center;
   }
</style>

</td></tr>
<tr><td class="contentcontent">
<table width="100%">
<tr><th width="35%"><b>Player</b></td><th width="40%"><b>Reason</b></td><th width="25%"><b>Time Left</b></th></tr>
<?php
$result      = mysql_query("SELECT COUNT(*) FROM `grpgusers` WHERE `hospital` != '0'");
$r           = mysql_fetch_row($result);
$numrows     = $r[0];
$rowsperpage = 30;
$totalpages  = ceil($numrows / $rowsperpage);
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
$offset = ($currentpage - 1) * $rowsperpage;
$result = mysql_query("SELECT * FROM `grpgusers` WHERE `hospital` != '0' ORDER BY `hospital` ASC LIMIT $offset, $rowsperpage");
$people = mysql_num_rows($result);
if ($people > 0) {
   while ($line = mysql_fetch_array($result)) {
      $secondsago     = time() - $line['lastactive'];
      $user_hospital  = new User($line['id']);
      $hospital_class = formatName($line['hwho']);
      $someonehere    = 1;
      if ($line['hhow'] == "wasattacked")
         $how = "Attacked by " . $hospital_class;
      else if ($line['hhow'] == "attacked")
         $how = "Lost to " . $hospital_class;
      else if ($line['hhow'] == "roulette")
         $how = "Wounded by Russian Roulette";
      else if ($line['hhow'] == "maze")
          $how = "Hospitalised searching the Maze";
      else if ($line['hhow'] == "door")
         $how = "Explosion at Doors";
      else if ($line['hhow'] == "bombed") {
         if ($line['id'] == $line['hwho'])
            $how = "Blew up themself";
         else
            $how = "Got blown up by " . $hospital_class;
      }
      else if ($line['hhow'] == "abombed") {
         if ($line['id'] == $line['hwho'])
            $how = "Blew up themself";
         else
            $how = "Got blown up by someone";
      }
      else if ($line['hhow'] == "cbombed")
         $how = "Got blown up by " . $hospital_class . " (City Bomb)";
      else if ($line['hhow'] == "mbomb")
         $how = "Mail Bombed by " . $hospital_class;
      else if ($line['hhow'] == "backalley")
         $how = "Got beaten up in the back alley";
	  else if ($line['hhow'] == "robbed")
         $how = "Got robbed";
      echo "<tr><td width='35%'>" . $user_hospital->formattedname . "</td><td width='45%'>" . $how . "</td><td width='20%'>" . ($user_hospital->hospital / 60) . " Minutes Left</td></tr>";
   }
   echo "</table><br />";
   $range = 8;
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
} else
   echo "</table><br /><center><font color=black>Nobody is in the Hospital</font></center>";
?>
</td></tr>
<?php
include 'footer.php';
?>
