<?php
include 'header.php'; ?>

<div class='box_top'>Hall Of Fame</div>
<div class='box_middle'>
    <div class='pad'>

        <div class="contenthead floaty">

            <table id="newtables" style="width:100%;">
                <tr>
                    <td align="center">
                        <form method="get">
                            <input type="hidden" name="view" value="<?php
                            if (isset($_GET['view'])) {
                                echo $_GET['view'];
                            }
                            ?>" />
                            <?php
                            $db->query("SELECT * FROM cities");
                            $db->execute();
                            $cities = $db->fetch_row();
                            foreach ($cities as $row) {
                                ?>
                                <option value='<?php
                                echo $row['id'];
                                ?>' <?php
                                if (isset($_GET['cityid']) && $_GET['cityid'] == $row['id']) {
                                    echo ($_GET['cityid'] == $row['id']) ? " selected" : "";
                                }
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
                                if (isset($_GET['view']) && $_GET['view'] == "level") {
                                    echo " selected";
                                }
                                ?>>Level</option>
                                <option value='strength' <?php
                                if (isset($_GET['view']) && $_GET['view'] == "strength") {
                                    echo " selected";
                                }
                                ?>>Strength</option>
                                <option value='defense' <?php
                                if (isset($_GET['view']) && $_GET['view'] == "defense") {
                                    echo " selected";
                                }
                                ?>>Defense</option>
                                <option value='speed' <?php
                                if (isset($_GET['view']) && $_GET['view'] == "speed") {
                                    echo " selected";
                                }
                                ?>>Speed</option>
                                <option value='agility' <?php
                                if (isset($_GET['view']) && $_GET['view'] == "agility") {
                                    echo " selected";
                                }
                                ?>>Agility</option>
                                <option value='money' <?php
                                if (isset($_GET['view']) && $_GET['view'] == "money") {
                                    echo " selected";
                                }
                                ?>>Money</option>
                                <option value='points' <?php
                                if (isset($_GET['view']) && $_GET['view'] == "points") {
                                    echo " selected";
                                }
                                ?>>Points</option>
                                <option value='total' <?php
                                if (isset($_GET['view']) && $_GET['view'] == "total") {
                                    echo " selected";
                                }
                                ?>>Total</option>
                                <option value='rmdays' <?php
                                if (isset($_GET['view']) && $_GET['view'] == "rmdays") {
                                    echo " selected";
                                }
                                ?>>RM Days</option>
                                <option value='bank' <?php
                                if (isset($_GET['view']) && $_GET['view'] == "bank") {
                                    echo " selected";
                                }
                                ?>>Bank</option>
                                <option value='crimes' <?php if (isset($_GET['view']) && $_GET['view'] == "crimes") {
                                    echo " selected";
                                }
                                ?>>Crimes Done</option>

                                <option value='battlewon' <?php if (isset($_GET['view']) && $_GET['view'] == "battlewon") {
                                    echo " selected";
                                }
                                ?>>Kills</option>
                                <option value='battlelost' <?php if (isset($_GET['view']) && $_GET['view'] == "battlelost") {
                                    echo " selected";
                                }
                                ?>>Deaths</option>

                                <option value='muggedmoney' <?php if (isset($_GET['view']) && $_GET['view'] == "muggedmoney") {
                                    echo " selected";
                                }
                                ?>>
                                    Highest Muggers</option>


                                <option value='posts' <?php if (isset($_GET['view']) && $_GET['view'] == "posts") {
                                    echo " selected";
                                }
                                ?>>Forum Posts</option>
                                <option value='backalleywins' <?php if (isset($_GET['view']) && $_GET['view'] == "backalleywins") {
                                    echo " selected";
                                }
                                ?>>Back Alley Wins</option>
                                <option value='relationshipdays' <?php if (isset($_GET['view']) && $_GET['view'] == "relationshipdays") {
                                    echo " selected";
                                }
                                ?>>Time Married</option>

                            </select>
                        </form>
                    </td>
                </tr>
                <table width='100%' border="0" bordercolor="#444444" cellpadding="4" cellspacing="0" align="center"
                    class="myTable">
                    <tr>
                        <td><b>Rank</b></td>
                        <td><b>Mobster</b></td>
                        <td><b>Level</b></td>
                        <td><b>Money</b></td>
                        <td><b>Gang</b></td>
                        <td><b>Online</b></td>
                    </tr>

                    <?php
                    $view = (isset($_GET['view']) && $_GET['view'] != "") ? $_GET['view'] : 'level';
                    if ($view == 'crimes') {
                        $view = 'crimesucceeded';
                    }
                    $view2 = ($view == "level") ? ", `exp` DESC" : "";

                    $db->query("SELECT * FROM `grpgusers` gu WHERE (SELECT count(*) FROM bans b WHERE b.id = gu.id AND type IN ('perm','freeze')) = 0 AND `admin` = '0' AND `ban/freeze` = '0' ORDER BY `" . $view . "` DESC" . $view2 . " LIMIT 0,50");
                    $db->execute();
                    $rows = $db->fetch_row();
                    $rank = 0;
                    foreach ($rows as $line) {
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
                </td>
                </tr>
                <?php
                include 'footer.php';
                ?>