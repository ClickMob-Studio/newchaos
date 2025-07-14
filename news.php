<?php
include 'header.php';
include 'includes/pagination.class.php';
?>
<div class='box_top'>News</div>
<div class='box_middle'>
	<div class='pad'>
		<?php
		$db->query("UPDATE grpgusers SET news = 0 WHERE id = ?");
		$db->execute(array(
			$user_class->id
		));
		$pages = new pagination();
		$db->query("SELECT COUNT(*) FROM ftopics WHERE sectionid = 1");
		$db->execute();
		echo '<script type="text/javascript">';
		echo 'function rate(arrow, fpid, rate) {';
		echo '$(this).load("ajax_forumrate" + arrow + ".php?fpid=" + fpid);';
		echo '$("#" + arrow + "rate" + fpid).html(rate);';
		echo '}';
		echo '</script>';
		$pages->items_total = $db->fetch_single();
		$pages->items_per_page = 15;
		$pages->max_pages = 10;
		$db->query("SELECT * FROM ftopics WHERE sectionid = 1 ORDER BY timesent DESC" . $pages->limit());
		$db->execute();
		$rows = $db->fetch_row();
		foreach ($rows as $row) {
			$reply_class = new User($row['playerid']);

			$db->query("SELECT COUNT(*) FROM `freplies` WHERE `topicid` = ?");
			$db->execute(array($row['forumid']));
			$comments = $db->fetch_single();

			echo '<div class="floaty">';
			echo '<div class="flexcont">';
			echo '<div class="flexele">';
			echo date('d M Y - g:i a', $row['timesent']);
			echo '</div>';
			echo '<div class="flexele forumhover" onclick="rate(\'up\',' . $row['forumid'] . ', ' . ($row['rateup'] + 1) . ');">';
			echo '<img src="images/up.jpg" /> Rate Up (<span id="uprate' . $row['forumid'] . '">' . $row['rateup'] . '</span>)';
			echo '</div>';
			echo '<div class="flexele forumhover" onclick="rate(\'down\',' . $row['forumid'] . ', ' . ($row['ratedown'] + 1) . ');">';
			echo '<img src="images/down.jpg" /> Rate Down (<span id="downrate' . $row['forumid'] . '">' . $row['ratedown'] . '</span>)';
			echo '</div>';
			echo '<div class="flexele forumhover">';
			echo '<a href="forum.php?topic=' . $row['forumid'] . '" style="color:yellow;">[ ' . $comments . ' Comments ]</a>';
			echo '</div>';
			echo '</div>';
			echo '<hr style="border:0;border-top:thin solid #333;" />';
			echo '<div class="flexcont">';
			echo '<div class="flexele" style="border-right:thin solid #333;">';
			echo '<br />';
			echo $reply_class->formattedname . '<br />';
			echo '<br />';
			echo '<img src="' . $reply_class->avatar . '" width="150" height="150" />';
			echo '<br />';
			echo '<br />';
			echo '</div>';
			echo '<div class="flexele" style="flex:3;padding:10px;">';
			echo '<br />';
			echo '<div class="noedit">' . BBCodeParse($row['body']) . '</div>' . $edit;

			$db->query("SELECT * FROM polls WHERE topic_id = '" . $row['forumid'] . "'");
			$db->execute();
			$poll = $db->fetch_row(true);
			if ($poll) {
				$title = $poll['title'];
				$choices = unserialize($poll['options']);
				$votes = unserialize($poll['votes']);
				$end = $poll['finish'];
				$pollId = $poll['id'];

				$db->query("SELECT * FROM voters WHERE `user_id` = ? AND `poll_id` = ?");
				$db->execute(
					array(
						$user_class->id,
						$pollId
					)
				);
				$voted = $db->fetch_row(true);
				echo '<div class="poll-box">
							<h3>' . $title . '</h3>';
				if (!$voted && (time() < $end)) {
					echo '
								<form class="poll" method="POST">
									<input type="hidden" name="pollid" value="' . $pollId . '">
									<div class="radiobuttons">
									<ul style="text-align: left;list-style: none;padding-left: 0px;display: inline-block;">';
					foreach ($choices as $key => $value) {
						echo '<li><input type="radio" value="' . $key . '" name="radioq" id="' . $key . '">' . $value . '</li>';
					}

					echo '
								</ul>
								</div>
								<div class="clear"></div>
								<button>Submit</button>
								<div class="clear"></div>
								</form>
								<div class="results"></div>';
				} else {

					$db->query("SELECT *, u.username FROM voters INNER JOIN grpgusers u ON `user_id` = u.id WHERE poll_id = ?");
					$db->execute(
						array($poll['id'])
					);
					$voters = $db->fetch_row();

					echo '<div class="results">';
					echo '<h3>Results</h3>';
					for ($i = 0; $i < count($choices); $i++) {
						$votePercent = round(($votes[$i] / $poll['voters']) * 100);
						$votePercent = !empty($votePercent) ? $votePercent . '%' : '0%';
						echo '<p>' . $choices[$i] . '</p>';
						echo '<div class="progress" style="margin: auto;max-width:80%">';
						echo '<div class="poll-progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width:' . $votePercent . '">' . $votePercent . '</div></div>';

						if ($user_class->id == 150 || $user_class->id == 1) {
							echo '<div>';
							foreach ($voters as $voter) {
								if ($voter['choice'] == $i) {
									echo $voter['username'] . ' ';
								}
							}
							echo '</div>';
						}

					}
					echo '</div></div>';
				}
			}

			echo '</div>';
			echo '</div>';
			echo '</div>';
		}
		if ($rtn = $pages->displayPages())
			echo '<span class="floaty">' . $rtn . '</span>';
		include("footer.php");
		?>