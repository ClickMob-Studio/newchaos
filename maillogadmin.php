<?php
include 'header.php';
if ($user_class->admin != 1 && $user_class->cm != 1) {
    echo Message("You are not authorized to be here.");
    include 'footer.php';
    die();
}
if ($_GET['action'] == "empty") {
    echo Message("<tr><td class='contentprofile'><font size=2>The mail logs have been emptied</font></td></tr>");
    $result = mysql_query("TRUNCATE TABLE `maillog`");
}
if ($_GET['page'] == "maillog") {
    if (isset($_GET['filter'])) {
        $to = $_GET['to'];
        $from = $_GET['from'];
        if ($to != "" && $from != "") { //Both Fields
            $sql = " WHERE `to` = '" . $to . "' AND `from` = '" . $from . "'";
        } else if ($to == "" && $from != "") { //Just From
            $sql = " WHERE `from` = '" . $from . "'";
        } else if ($to != "" && $from == "") { //Just To
            $sql = " WHERE `to` = '" . $to . "'";
        }
        $pages = "&to=$to&from=$from&filter=Filter";
    }
}
echo "
   <tr><td class='contenthead'>Mail Logs</td></tr>
   <tr><td class='contentcontent'><center><font size=2>
        Turf Wars Mail Logs - <a href='maillogadmin.php?action=empty'>[Click here to empty mail logs]</a><br><br>
        ";
$rowsPerPage = 50;
$pageNum = 1;
if (isset($_GET['page'])) {
    $pageNum = $_GET['page'];
}
$offset = ($pageNum - 1) * $rowsPerPage;
$result = mysql_query("SELECT * from `maillog` ORDER BY `timesent` DESC LIMIT $offset, $rowsPerPage") or die(mysql_error());
$num_rows = mysql_num_rows(mysql_query("SELECT * from `maillog` WHERE  `to` != '0' ORDER BY `timesent`"));
$total_pages = ceil($num_rows / $rowsPerPage);
echo "<center><font size=1>";
for ($i = 1; $i <= $total_pages; $i++) {
    echo " <font size=3><a href='maillogadmin.php?page=" . $i . "'>[" . $i . "]</a></font> ";
};
echo "<BR><BR></center></font>";
echo "<table width='680' class='myTable'>
        <tr>
            <td class='tablehead' width='130'>When</td>
            <td class='tablehead' width='150'>Sender</td>
            <td class='tablehead' width='150'>Receiver</td>
            <td class='tablehead' width='250'>Message</td>
            <td class='tablehead' width='80'>read</td>
        </tr>
        ";
while ($r = mysql_fetch_array($result)) {
    $sender = new User($r['from']);
    $receiver = new User($r['to']);
    $time1 = date(F . " " . d . "  " . Y . " ", $r['timesent']);
    $time = date("" . g . ":" . i . ":" . sa, $r['timesent']);
    if ($r['subject'] == "") {
        $r['subject'] = "No Subject";
    }
    echo "
            ";
    ?> <tr class="second" onmouseover="this.className = 'lasthl'" onmouseout="this.className = 'second'"> <?php
        echo "
                <td>{$time1}<br />{$time}</td>
                <td>{$sender->formattedname}</td>
                <td>{$receiver->formattedname}</td>
                <td>" . nl2br($r['msgtext']) . "</td>
                <td>" . nl2br($r['viewed']) . "</td>
            </tr>
          
        ";
    }
    echo "</table></td></tr>";
    include 'footer.php';
    ?>