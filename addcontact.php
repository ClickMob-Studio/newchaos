<?php
include "header.php";
genHead("Add Contact");
if (isset($_GET['id'])) {
    security($_GET['id']);
    $id = $_GET['id'];
    if ($_GET['confirm'] != "yes") {
        echo "<center>Are you sure that you want to add " . formatName($id) . " to your contacts list?<br><a href='addcontact.php?id=" . $id . "&confirm=yes'>Yes</a> | <a href='profiles.php?id=" . $id . "'>No</a></center>";
    } else {
        if ($cost > $user_class->money) {
            echo "Big error.";
        } else {
            echo "You have successfully added " . formatName($id) . " to your contact list.";
            $db->query("INSERT INTO contactlist (id, contact, age) VALUES (?, ?, unix_timestamp())");
            $db->execute(array(
               $user_class->id,
                $id
            ));
        }
    }
}
include 'footer.php';
?>