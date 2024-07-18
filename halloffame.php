    <?php
include 'header.php';?>
	
<div class='box_top'>Hall Of Fame</div>
                    <div class='box_middle'>
                        <div class='pad'>
                        
<div class="contenthead floaty">

<table id="newtables" style="width:100%;">
        <tr><td align="center">
                <form method="get">
                    <input type="hidden" name="view" value="<?php
                    echo $_GET['view'];
                    ?>" />
                        <?php
                        $result = mysql_query("SELECT * FROM `cities`");
                        while ($row = mysql_fetch_array($result, mysql_ASSOC)) {
                            ?>
                            <option value='<?php
                            echo $row['id'];
                            ?>'<?php
                                    echo ($_GET['cityid'] == $row['id']) ? " selected" : "";
                                    ?>><?php
                                        echo $row['name'];
                                        ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </form>
                <form method="get">
                    <select name='view' onchange='this.form.submit()'>
                        <option value='level'>Select...</option>
                        <option value='level' <?php
                        echo ($_GET['view'] == "level") ? " selected" : "";
                        ?>>Level</option>
                        <option value='strength' <?php
                        echo ($_GET['view'] == "strength") ? " selected" : "";
                        ?>>Strength</option>
                        <option value='defense' <?php
                        echo ($_GET['view'] == "defense") ? " selected" : "";
                        ?>>Defense</option>
                        <option value='speed' <?php
                        echo ($_GET['view'] == "speed") ? " selected" : "";
                        ?>>Speed</option>
                        <option value='agility' <?php
                        echo ($_GET['view'] == "agility") ? " selected" : "";
                        ?>>Agility</option>
                        <option value='money' <?php
                        echo ($_GET['view'] == "money") ? " selected" : "";
                        ?>>Money</option>
                        <option value='points' <?php
                        echo ($_GET['view'] == "points") ? " selected" : "";
                        ?>>Points</option>
                        <option value='total' <?php
                        echo ($_GET['view'] == "total") ? " selected" : "";
                        ?>>Total</option>
                        <option value='rmdays' <?php
                        echo ($_GET['view'] == "rmdays") ? " selected" : "";
                        ?>>RM Days</option>
                        <option value='bank' <?php
                        echo ($_GET['view'] == "bank") ? " selected" : "";
                        ?>>Bank</option>
                        <option value='crimes' <?php echo ($_GET['view'] == "crimes") ? " selected" : ""; ?>>Crimes Done</option>

                        <option value='battlewon' <?php
                        echo ($_GET['view'] == "battlewon") ? " selected" : "";
                        ?>>Kills</option>
                        <option value='battlelost' <?php
                        echo ($_GET['view'] == "battlelost") ? " selected" : "";
                        ?>>Deaths</option>

<option value='muggedmoney' <?php
                        echo ($_GET['view'] == "muggedmoney") ? " selected" : "";
                        ?>>Highest Muggers</option>


                        <option value='posts' <?php
                        echo ($_GET['view'] == "posts") ? " selected" : "";
                        ?>>Forum Posts</option>
                        <option value='backalleywins' <?php
                        echo ($_GET['view'] == "backalleywins") ? " selected" : "";
                        ?>>Back Alley Wins</option>
<option value='relationshipdays' <?php
                        echo ($_GET['view'] == "relationshipdays") ? " selected" : "";
                        ?>>Time Married</option>

                    </select>
                </form>
            </td></tr>
        <table width='100%' border="0" bordercolor="#444444" cellpadding="4" cellspacing="0" align="center" class="myTable">
            <tr>
                <td><b>Rank</b></td>
                <td><b>Mobster</b></td>
                <td><b>Level</b></td>
                <td><b>Money</b></td>
                <td><b>Gang</b></td>
                <td><b>Online</b></td>
            </tr>
            
            <?php
            $view = ($_GET['view'] != "") ? $_GET['view'] : 'level';
            if($view == 'crimes'){
                $view = 'crimesucceeded';
            }
            $view2 = ($view == "level") ? ", `exp` DESC" : "";
            $result = mysql_query("SELECT * FROM `grpgusers` gu WHERE (SELECT count(*) FROM bans b WHERE b.id = gu.id AND type IN ('perm','freeze')) = 0 AND `admin` = '0' AND `ban/freeze` = '0'" . $city . "ORDER BY `" . $view . "` DESC" . $view2 . " LIMIT 0,50");
            $rank = 0;
            while ($line = mysql_fetch_array($result)) {
                $rank++;
                $user_hall = new User($line['id']);
                echo '<tr><td>';
                if ($rank == 1)
                    echo "<font color=#FFD700><b>1st</b></font>";
                elseif ($rank == 2)
                    echo "<font color=#C0C0C0><b>2nd</b></font>";
                elseif ($rank == 3)
                    echo "<font color=#CD7F32><b>3rd</b></font>";
                else {
                    echo ordinal($rank);
                }
                // elseif (($rank > 3 AND $rank < 21) || ($rank > 23))
                //     echo $rank . "th";
                // elseif ($rank == 21)
                //     echo "21st";
                // elseif ($rank == 22)
                //     echo "22nd";
                // elseif ($rank == 23)
                //     echo "23rd";
                echo '</td>';
                echo "
<td>$user_hall->formattedname</td>
<td>$user_hall->level</td>
<td>" . prettynum($user_hall->money, 1) . "</td>
<td>$user_hall->formattedgang</td>
<td>$user_hall->formattedonline</td>
</tr>
";
            }
            ?>
        </table>
</td></tr>
<?php
include 'footer.php';
?>