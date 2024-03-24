<?php
$ignoreslashes = 1;
include 'header.php';
if(isset($_POST['msgtext'])){
	$db->query("UPDATE grpgusers SET notepad = ? WHERE id = ?");
	$db->execute(array(
		$_POST['msgtext'],
		$user_class->id
	));
	$user_class->notepad = $_POST['msgtext'];
}
echo'<tr><td class="contentcontent">';
        echo'<form name="message" method="post">';
			echo'<div class="floaty" style="margin-bottom:-10px;text-align:center;">';
				echo'<div class="flexcont">';
					echo'<div class="flexele forumhover" onclick="insertAtCursor(\'[b][/b]\', 4);return false;">';
						echo'[b]';
					echo'</div>';
					echo'<div class="flexele forumhover" onclick="insertAtCursor(\'[u][/u]\', 4);return false;">';
						echo'[u]';
					echo'</div>';
					echo'<div class="flexele forumhover" onclick="insertAtCursor(\'[i][/i]\', 4);return false;">';
						echo'[i]';
					echo'</div>';
					echo'<div class="flexele forumhover" onclick="insertAtCursor(\'[s][/s]\', 4);return false;">';
						echo'[s]';
					echo'</div>';
					echo'<div class="flexele forumhover" onclick="insertAtCursor(\'[url][/url]\', 6);return false;">';
						echo'[url]';
					echo'</div>';
					echo'<div class="flexele forumhover" onclick="insertAtCursor(\'[img][/img]\', 6);return false;">';
						echo'[img]';
					echo'</div>';
					echo'<div class="flexele forumhover" onclick="insertAtCursor(\'[youtube][/youtube]\', 10);return false;">';
						echo'[youtube]';
					echo'</div>';
					echo'<div id="semojis" class="flexele forumhover" onclick="return showemojis();" style="display:' , ($user_class->hideemojis) ? 'block' : 'none' , ';flex:2;">';
						echo'Show Emojis';
					echo'</div>';
					echo'<div id="hemojis" class="flexele forumhover" onclick="return hideemojis();" style="display:' , ($user_class->hideemojis) ? 'none' : 'block' , ';flex:2;">';
						echo'Hide Emojis';
					echo'</div>';
				echo'</div>';
				echo'<hr style="border:0;border-top:thin solid #333;" />';
					echo'<textarea name="msgtext" id="reply" style="width:95%;height:250px;">[:NOTEPAD:]</textarea><br />';
					echo'<br />';
					echo'<button onclick="return notepad();">Preview</button> ';
					echo'<button>Save</button>';
					echo'<br>';
					echo'<button>$£$£$</button>';
					echo'<br />';
					echo'<br />';
				echo'<div id="emojis" style="display:' , ($user_class->hideemojis) ? 'none' : 'block' , ';">';
					emotes();
				echo'</div>';
				echo'<hr style="border:0;border-top:thin solid #333;" />';
				echo'<div id="rtn">' . bbcodeparse($user_class->notepad) . '</div>';
			echo'</div>';
        echo'</form>';
        include 'footer.php';
        ?>