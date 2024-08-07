<?php
include 'header.php';

if (checkCaptchaRequired($user_class)) {
    header('Location: captcha.php?token=' . $user_class->macro_token . '&page=citizens');
}

if (isset($_GET['forced_captcha']) && $_GET['forced_captcha'] == 'yes') {
    mysql_query('UPDATE `grpgusers` SET `captcha_timestamp` = 0 WHERE `id` = ' . $user_class->id);

    header('Location: captcha.php?token=' . $user_class->macro_token . '&page=citizens');
}
?>
	
	<div class='box_top'>Citizens</div>
						<div class='box_middle'>
							<div class='pad'>
								<?php
echo '<tr><td class="contentcontent">';
//Pages Stuff
// find out how many rows are in the table
$result = mysql_query("SELECT COUNT(*) FROM `grpgusers`");
$r = mysql_fetch_row($result);
$numrows = $r[0];
// number of rows to show per page
$rowsperpage = 50;
// find out total pages
$totalpages = ceil($numrows / $rowsperpage);
if ($totalpages <= 0) {
    $totalpages = 1;
} else {
    $totalpages = ceil($numrows / $rowsperpage);
}
// get the current page or set a default
if (isset($_GET['page']) && is_numeric($_GET['page'])) {
    // cast var as int
    $currentpage = (int) $_GET['page'];
} else {
    // default page num
    $currentpage = 1;
} // end if
// if current page is greater than total pages...
if ($currentpage > $totalpages) {
    // set current page to last page
    $currentpage = $totalpages;
} // end if
// if current page is less than first page...
if ($currentpage < 1) {
    // set current page to first page
    $currentpage = 1;
} // end if
// the offset of the list, based on current page
$offset = ($currentpage - 1) * $rowsperpage;
$csrf = md5(uniqid(rand(), true));
$_SESSION['csrf'] = $csrf;
echo '<table width="100%">';
echo '<tr>';
echo '<th><b>#ID</b></th>';
echo '<th><b>Username</b></th>';
echo '<th><b>Level</b></th>';
echo '<th><b>Active</b></th>';
echo '<th><b>Actions</b></th>';
echo '</tr>';
$result = mysql_query("SELECT * FROM `grpgusers` ORDER BY `id` ASC LIMIT $offset, $rowsperpage");
while ($line = mysql_fetch_array($result)) {
    $secondsago = time() - $line['lastactive'];
    $user_online = new User($line['id']);
    echo "<tr>
    <td>" . $user_online->id . "</td>
    <td>" . $user_online->formattedname . "</td>
    <td>" . $user_online->level . "</td>
    <td>" . $user_online->formattedonline . "</td>
    <td>
    ";
    if($user_class->admin > 0){
        echo "
    <a class='action btn btn-primary ajax-link' href='ajax_mugtest.php?mug=".$user_online->id."&token=" . $user_class->macro_token . "'>Mug</a> 
    ";
    }else{
        echo "
        <a class='action btn btn-primary ajax-link' href='ajax_mug.php?mug=".$user_online->id."&token=" . $user_class->macro_token . "'>Mug</a> 
        ";
    }
    echo "
    <a class='action btn btn-primary' href='attack.php?attack=" . $user_online->id . "&csrf=" . $csrf . "'>Attack</a>
    </td>
    </tr>";
}
echo "</table><br />";
/* * ****  build the pagination links ***** */
// range of num links to show
$range = 20;
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
/* * **** end build pagination links ***** */
echo "</td></tr>";

?>

    <script type="text/javascript">
        let clickCount = 0;

        document.addEventListener("DOMContentLoaded",function(){
            document.body.addEventListener('click', function(evt) {
                clickCount = clickCount + 1;
                if (clickCount > 1500) {
                    window.location.href = "/citizens.php?forced_captcha=yes";
                }
            }, true);
        });

    </script>

<?php
include 'footer.php'
?>