<?php
include 'header.php';

?>

<div class='box_top'>Gang List</div>
<div class='box_middle'>
	<div class='pad'>


		<div class="contenthead floaty">
			<table id="newtables" style="width:100%;">
				<td align="center" class="td"><b>Rank</b></td>
				<td align="center" class="td"><b>Gang</b></td>
				<td align="center" class="td"><b>Level</b></td>
				<td align="center" class="td"><b>Respect</b></td>


				<td align="center" class="td"><b>Tax</b></td>
				<td align="center" class="td"><b>Members</b></td>
				<td align="center" class="td"><b>Leader</b></td>
				</tr>
				<?php

				$db->query("SELECT * FROM `gangs` ORDER BY `level` DESC LIMIT 50");
				$db->execute();
				$rows = $db->fetch_row();
				$rank = 1;
				foreach ($rows as $line) {
					$gang = new Gang($line['id']);
					if ($gang->members == 0)
						continue;
					$gang_leader = new User($line['leader']);
					echo "
		<tr>
		<td width='10%'>" . $rank . "</td>
		<td width='40%'>" . $gang->formattedname . "</td>
		<td width='10%'>" . $gang->level . "</td>
		<td width='10%'>" . $gang->respect . "</td>

		<td width='10%'>" . $gang->tax . "%</td>
		<td width='12%'>" . $gang->members . "</td>
		<td width='15%'>" . $gang_leader->formattedname . "</td>
		</tr>
		";
					$rank++;
				}
				?>
			</table>
		</div>
		</tr>
		</td>
		<?php
		include 'footer.php';
		?>