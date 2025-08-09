<?php
include 'header.php';

$db->query("SELECT * FROM `ignorelist` WHERE `id` = ? LIMIT 1");
$db->execute([$_GET['remove']]);
$result = $db->fetch_row(true);
$contact_class = new User($result['blocked']);
if (isset($_GET['remove']) && $_GET['remove'] != "" && !empty($result)) {
    if ($result['blocker'] == $user_class->id) {
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
            $db->query("SELECT * FROM `ignorelist` WHERE `blocker` = ? ORDER BY `id` ASC");
            $db->execute([$user_class->id]);
            $results = $db->fetch_row();
            foreach ($results as $line) {
                $contact_class = new User($line['blocked']);
                echo "<tr><td width='10%'>" . $contact_class->id . "</td><td>" . $contact_class->formattedname . "</td><td><a href='ignorelist.php?remove=" . $line['id'] . "'>Remove</a></td></tr>";
            }
            include 'footer.php';
            ?>