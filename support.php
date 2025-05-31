<?php
include 'header.php';

if (isset($_POST['submit'])) {
    if ($_POST['subject'] != "") {
        if ($_POST['body'] != "") {
            if (strlen($_POST['subject']) < 41) {
                $playerid = $user_class->id;
                $timesent = time();
                $status = "OPEN";
                $_POST['subject'] = str_replace('"', '', $_POST['subject']);
                $subject = strip_tags($_POST['subject']);
                $subject = addslashes($subject);
                $_POST['body'] = str_replace('"', '', $_POST['body']);
                $body = strip_tags($_POST['body']);
                $body = nl2br($body);
                $body = addslashes($body);
                perform_query("INSERT INTO `tickets` (`ticketid`, `playerid`, `timesent`, `status`, `subject`, `body`)" . "VALUES (?, ?, ?, ?, ?, ?)", [$ticketid, $playerid, $timesent, $status, $subject, $body]);
                if ($result) {
                    // Call the Send_Event function for user ID 1 and 2
                    Send_Event(1, $user_class->formattedname . " created a ticket. <a href='managetickets.php'>View here</a>.");
                    Send_Event(2, $user_class->formattedname . " created a ticket. <a href='managetickets.php'>View here</a>.");

                    echo Message("Your ticket has been Submitted, please check back tomorrow to see if it has been attended to.");
                } else {
                    echo Message("There was an error submitting your ticket.");
                }
            } else {
                echo Message("Your subject can only be 40 characters in length!");
            }
        } else {
            echo Message("You didn't enter a topic body!");
        }
    } else {
        echo Message("You didn't enter a topic subject!");
    }
}
?>
<tr>
    <td class="contentspacer"></td>
</tr>
<tr>
    <div class="contenthead">Help Desk</div>
</tr>
<tr>
    <td class="contentcontent">
        <table width='100%'>
            <tr>
                <br>
            <tr>
                <div class="contentcontent">
                    <center>Welcome to the Help Desk. Here you can report problems such as, Bugs,Hacks,Exploits &
                        General Questions, Please do not Spam this area as you will be banned from using this.
                        Please Fill Out The Subject And Your Issue Below. Thank you.</center>
    </td>
    <br>
    <form method="post">
<tr>
    <td width="12%"><b>Subject:</b></td>
    <td width="80%"><input type="text" name="subject" size="50" /></td>
</tr>
<tr>
    <td width="12%"><b>Message:</b><br />[<a href="bbcode.php">BBCode</a>]</td>
    <td width="80%"><textarea name="body" cols="66" rows="5"></textarea></td>
</tr>
</table>
<table width="100%">
    <tr>
        <td align="center"><input type="submit" name="submit" value="Submit Ticket" /></td>
    </tr>
    </form>
</table>
</td>
</tr>
<h4>My Tickets</h4></span>
<table id="newtables" style="width:100%;">
    <tr>
        <th>Ticket ID</th>
        <th>Subject</th>
        <th>Date</th>
        <th>Status</th>
    </tr>
    <?php
    $db->query("SELECT * FROM tickets WHERE playerid = ? ORDER BY ticketid");
    $db->execute(array($user_class->id));
    $rows = $db->fetch_row();
    foreach ($rows as $row) {
        ?>
        <tr>
            <td><?= $row['ticketid'] ?></td>
            <td><a href="viewticket.php?ticketid=<?= $row['ticketid'] ?>"><?= $row['subject'] ?></a></td>
            <td><?= date("d F Y, g:ia", $row['timesent']) ?></td>
            <td><span style="color:<?= $row['status'] == 'OPEN' ? 'green' : 'red' ?>;"><?= $row['status'] ?></span></td>
        </tr>
        <?php
    }
    ?>
</table>
<br /><br />
<?php
/*         * ****  build the pagination links ***** */
// range of num links to show
$range = 2;
// if not on page 1, don't show back links
if ($currentpage > 1) {
    // show << link to go back to page 1
    echo " <a href='{$_SERVER['PHP_SELF']}?page=1'><<</a> ";
} // end if 
// loop to show links to range of pages around current page
for ($x = ($currentpage - $range); $x < (($currentpage + $range) + 1); $x++) {
    // if it's a valid page number...
    if (($x > 0) && ($x <= $totalpages)) {
        // if we're on current page...
        if ($x == $currentpage) {
            // 'highlight' it but don't make a link
            echo " [<b>$x</b>] ";
            // if not current page...
        } else {
            // make it a link
            echo " <a href='{$_SERVER['PHP_SELF']}?page=$x'>$x</a> ";
        } // end else
    } // end if 
} // end for
// if not on last page, show forward and last page links        
if ($currentpage < $totalpages) {
    // echo forward link for lastpage
    echo " <a href='{$_SERVER['PHP_SELF']}?page=$totalpages'>>></a> ";
} // end if
/*         * **** end build pagination links ***** */
?>
</td>
</tr>
</table>
<?php
include("footer.php");
?>