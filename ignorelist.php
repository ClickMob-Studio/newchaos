<?php
include 'header.php';
$result1234 = mysql_query("SELECT * FROM `ignorelist` WHERE `id` = '" . $_GET['remove'] . "'");
$worked1234 = mysql_fetch_array($result1234);
$contact_class = new User($worked1234['blocked']);
if ($_GET['remove'] != "") {
    if ($worked1234['blocker'] == $user_class->id) {
        echo Message("You have removed " . $contact_class->formattedname . " from your ignore list.");
        perform_query("DELETE FROM `ignorelist` WHERE `id` = ?", [$_GET['remove']]);
    }
}
?>
<tr>
    <td class="contentspacer"></td>
</tr>
<tr>
    <div class="contenthead">Ignore List</div>
</tr>
<tr>
    <td class="contentcontent">
        <table width="100%">
            <tr>
                <td width='8%'><b>#ID</b></td>
                <td width="77%"><b>User</b></td>
                <td width="15%"><b>Actions</b></td>
            </tr>
            <?php
            $result = mysql_query("SELECT * FROM `ignorelist` WHERE `blocker` = '" . $user_class->id . "' ORDER BY `id` ASC");
            while ($line = mysql_fetch_array($result, mysql_ASSOC)) {
                $contact_class = new User($line['blocked']);
                echo "<tr><td width='10%'>" . $contact_class->id . "</td><td>" . $contact_class->formattedname . "</td><td><a href='ignorelist.php?remove=" . $line['id'] . "'>Remove</a></td></tr>";
            }
            include 'footer.php';
            ?>