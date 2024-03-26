<?php
include 'header.php';
?>
	
	<div class='box_top'>Top Rated</div>
						<div class='box_middle'>
							<div class='pad'>
								
 
<div class="contenthead floaty">

<table id="newtables" style="width:100%;">
            <tr>
                <td><b>Rank</b></td>
                <td><b>Mobster</b></td>
                <td><b>Rating</b></td>
            </tr>
            <?php
            $result = mysql_query("SELECT * FROM `grpgusers` g WHERE `admin` = 0 AND NOT EXISTS(SELECT * FROM bans b WHERE b.id = g.id AND type IN ('perm','freeze')) ORDER BY `rating` DESC LIMIT 10");
            $rank = 0;
            while ($line = mysql_fetch_array($result)) {
                $rank++;
                $user_hall = new User($line['id']);
                ?>
                <tr>
                    <td width="15%"><?php echo $rank; ?></td>
                    <td width="40%"><?php echo $user_hall->formattedname; ?></td>
                    <td width="20%"><?php echo prettynum($user_hall->rating); ?></td>
                </tr>
                <?php
            }
            ?>
        </table>
    </td></tr>
 
<div class="contenthead floaty">
<span style="margin: 0; line-height: 27px; text-transform: uppercase; font-size: 20px; text-align: left; text-indent: 25px;">
<h4>Bottom Rated</h4></span>
<table id="newtables" style="width:100%;">
            <tr>
                <td><b>Rank</b></td>
                <td><b>Mobster</b></td>
                <td><b>Rating</b></td>
            </tr>
            <?php
            $result = mysql_query("SELECT * FROM `grpgusers` g WHERE `admin` = 0 AND NOT EXISTS(SELECT * FROM bans b WHERE b.id = g.id AND type IN ('perm','freeze')) ORDER BY `rating` ASC LIMIT 10");
            $rank = 0;
            while ($line = mysql_fetch_array($result)) {
                $rank++;
                $user_hall = new User($line['id']);
                ?>
                <tr>
                    <td width="15%"><?php echo $rank; ?></td>
                    <td width="40%"><?php echo $user_hall->formattedname; ?></td>
                    <td width="20%"><?php echo prettynum($user_hall->rating); ?></td>
                </tr>
                <?php
            }
            ?>
    </td></tr>
</table>
<?php
include 'footer.php';
?>