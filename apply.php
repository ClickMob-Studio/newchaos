<?php
include 'header.php';
security($_GET['gang']);
$gang_class = new Gang($_GET['gang']);
// if ($user_class->gangwait != 0)
//     diefun("You've just left a gang! Please wait before applying to another gang!.");

if ($user_class->gang != 0)
    diefun("Please leave your current gang to apply for a new one.");
$db->query("SELECT * FROM gangapps WHERE applicant = ? AND gangid = ?");
$db->execute(array(
    $user_class->id,
    $_GET['gang']
));
$applied = $db->num_rows();
if ($applied != 0)
    diefun("You have already applied for this gang.");
if ($gang_class->members >= $gang_class->capacity)
    diefun('This gang already has the maximum number of members.');
$leader = $gang_class->leader;
$db->query("SELECT * FROM ranks WHERE gang = ? AND applications = 1");
$db->execute(array(
    $_GET['gang']
));
$rows = $db->fetch_row();
foreach ($rows as $row) {
    $db->query("SELECT id FROM grpgusers WHERE gang = ? AND (grank = ? AND id <> ?)");
    $db->execute(array(
        $_GET['gang'],
        $row['id'],
        $gang_class->leader
    ));
    $users = $db->fetch_row();
    foreach ($users as $user)
        Send_Event($user['id'], "[-_USERID_-] has applied for the gang.", $user_class->id);
}
Send_Event($gang_class->leader, "[-_USERID_-] has applied for the gang.", $user_class->id);
echo Message("You have successfully applied. Please wait until a member has accepted your application.");
$db->query("INSERT INTO gangapps (applicant, gangid, date) VALUES (?, ?, unix_timestamp())");
$db->execute(array(
    $user_class->id,
    $_GET['gang']
));
Gang_Event($_GET['gang'], "[-_USERID_-] has applied for the gang.", $user_class->id);
include 'footer.php';
?>