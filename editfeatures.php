<?php
include 'header.php';
if ($user_class->admin == 1) {
    ?>

    <tr><td class="contentspacer"></td></tr><tr><td class="contenthead">Add Reply</td></tr>
    <tr><td class="contentcontent">
    <center>
        <i>Welcome to the Edit Game panel. Here you can edit features such as items, houses etc. You can also add new items and such.</i>
        <br /><br />
        &nbsp;&nbsp;|&nbsp;&nbsp;<a href="control.php?page=crimes">Manage Crimes</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="control.php?page=gcrimes">Manage Gang Crimes</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="control.php?page=cities">Manage Cities</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="control.php?page=jobs">Manage Jobs</a>&nbsp;&nbsp;|&nbsp;&nbsp;<br />&nbsp;&nbsp;|&nbsp;&nbsp;<a href="control.php?page=houses">Manage Houses</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="control.php?page=playeritems">Manage Items</a>&nbsp;&nbsp;|&nbsp;&nbsp;
    </center>
    </td></tr>

    <?php
}
include("footer.php");
?>