<?php
include 'header.php';
?>
<div class='box_top'>Gang Events</div>
						<div class='box_middle'>
							<div class='pad'>

    <table id="newtables" style="width:100%;">
        <tr>
            <th>Description</th>
            <th>Time</th>
        </tr>
        <?php
		$r = mysql_query("SELECT * FROM gangevents WHERE gang = ".$user_class->gang." ORDER BY timesent DESC LIMIT 30");
		
        if(mysql_num_rows($r)){
		
        while($row = mysql_fetch_array($r)){
			$extra_user = new User($row['extra']);
			$text = str_replace('[-_USERID_-]', $extra_user->formattedname, $row['text']);
			echo'<tr>';
				echo'<td width="70%">' . $text . '</td>';
				echo'<td width="30%">' . date("d M Y, g:ia", $row['timesent']) . '</td>';
			echo'</tr>';
        }
    }else{
        echo '<tr><td colspan="2">No events found.</td></tr>';
    }
        ?>
    </table>
<?php
include("gangheaders.php");

include 'footer.php';
?>