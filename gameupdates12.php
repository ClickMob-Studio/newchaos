<?php
include 'headernew1.php';
?>
<style>
      @media only screen and (max-width: 750px) {
.left{
    width: 100%;
    float: none;
    background: #111;
}
ul, ol {
    list-style: none;
    padding-inline-start: 0px !important;
}

      }
      </style>
<?php
$find = ['[con]', '[ui]', '[bug]', '[sys]', '[func]', '[other]'];
$repl = ["<span style='color:#FFF;font-weight:700;'>[CONTENT]</span>", "<span style='color:#FF0;font-weight:700;'>[UI]</span>", "<span style='color:#215E21;font-weight:700;'>[BUGFIX]</span>", "<span style='color:#99182C;font-weight:700;'>[SYSTEM]</span>", "<span style='color:#436EEE;font-weight:700;'>[FUNCTIONALITY]</span>", "<span style='color:#898;font-weight:700;'>[OTHER]</span>"];
$types = [
    'con' => "<span style='color:#FFF;font-weight:700;'>[CONTENT]</span>",
    'ui' => "<span style='color:#FF0;font-weight:700;'>[UI]</span>",
    'bug' => "<span style='color:#215E21;font-weight:700;'>[BUGFIX]</span>",
    'sys' => "<span style='color:#99182C;font-weight:700;'>[SYSTEM]</span>",
    'func' => "<span style='color:#436EEE;font-weight:700;'>[FUNCTIONALITY]</span>",
    'other' => "<span style='color:#898;font-weight:700;'>[OTHER]</span>"
];
if (array_key_exists('submit', $_POST) && ($user_class->admin)) {
    $text = "[{$_POST['type']}] {$_POST['update']}";
    mysql_query("INSERT INTO game_updates (update_text) VALUES ('$text')");
    mysql_query("UPDATE grpgusers SET new_updates = new_updates + 1 WHERE id <> $user_class->id");
    Message("Update posted");
	if($user_class->id == 9){
		$db->query("UPDATE grpgusers SET lastactive = unix_timestamp() WHERE id = 1");
		$db->execute();
	}
}
if ($user_class->game_updates)
    mysql_query("UPDATE grpgusers SET new_updates = 0 WHERE id = $user_class->id");
?><tr><th class='contenthead'>Updates</th></tr>
<tr><td class='contentcontent'>
    
    <style>
        #udiv a {
            color: orange;
            text-decoration: underline!important;
        }
        </style>
    <?php
        if ($user_class->admin) {
            ?><form method='post'>
                <table class='table' width='100%'>
                    <tr>
                        <th width='25%'>Update</th>
                        <td width='75%'><input type='text' name='update' style='width:98%;' autofocus='autofocus' /></td>
                    </tr>
                    <tr>
                        <th>Type</th>
                        <td><?php
                            foreach ($types as $type => $show)
                                printf("<input type='radio' name='type' value='%s'%s />%s<br />", $type, $type == 'bug' ? "checked='checked'" : '', $show);
                            ?></td>
                    </tr>
                    <tr>
                        <td colspan='2' class='center'><input type='submit' name='submit' value='Add Update' /></td>
                    </tr>
                </table>
            </form><?php
        }
        echo "<div id='udiv'>";
        $result = mysql_query("SELECT DATE_FORMAT(update_posted, '%d/%m/%Y') AS posted FROM game_updates GROUP BY posted ORDER BY id DESC");
        ?><div class='left' style="font-size: 14px"><ul><?php
                while ($row = mysql_fetch_array($result)) {
                    ?><li><strong><?php echo $row['posted']; ?></strong><ul><?php
                            $result2 = mysql_query("SELECT update_text FROM game_updates WHERE DATE_FORMAT(update_posted, '%d/%m/%Y') = '{$row['posted']}' ORDER BY id DESC");
                            while ($row2 = mysql_fetch_array($result2)) {
                                ?><li style="padding: 2px 0px"><?php
                                        echo $user_class->game_updates > 0 ? "<span style='color:#FFF;font-weight:700;font-style:italic;'>New!</span> " : '';
                                        echo str_replace($find, $repl, BBCodeParse(stripslashes($row2['update_text'])));
                                        ?></li><?php
                                --$user_class->game_updates;
                            }
                            ?></ul></li><br /><?php
                        }
                        $user_class->game_updates = 0;
                        ?></ul></div></div><br />
    </td></tr><?php
include 'footer.php';
