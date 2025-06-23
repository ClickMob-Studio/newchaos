<?php
include 'header.php';

if ($user_class->admin == 1) {
    echo Message("You are not authorized to be here!");
    include("footer.php");
    die();
}
?>

<tr>
    <td class="contentspacer"></td>
</tr>
<tr>
    <td class="contenthead">Staff Applications</td>
</tr>
<tr>
    <td class="contentcontent">
        <table width="100%">
            <tr>
                <td>
                    <b>Submitted By</b>
                </td>
                <td><b>Time On</b></td>
                <td><b>Past Exp</b></td>
                <td><b>Why they should be chosen</b></td>
                <td><b>Role Applied for</b></td>

            </tr>

            <?php
            $db->query("SELECT * FROM `staffapps`");
            $db->execute();
            $rows = $db->fetch_row();
            foreach ($rows as $row) {
                $app_user = new User($row['userid']);

                echo " <tr>
						<td>
						" . $app_user->formattedname . "</td>
						
						<td>" . $row['timeon'] . "</td>
					  
					<td>" . $row['pastexp'] . "</td>
				  	  <td>" . $row['better'] . "</td>

				  	  <td>" . $row['staffrole'] . "</td>
</tr>

                <tr><td>&nbsp;</td></tr>
";
            }

            perform_query("UPDATE `grpgusers` SET `viewedupdate` = '0' WHERE `id` = ?", [$user_class->id]);

            include 'footer.php';
            ?>