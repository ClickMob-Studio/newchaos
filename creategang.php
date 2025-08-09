<?php
include 'header.php';
?>


<div class="box_top">Create A Gang</div>
<div class="box_middle">
    <div class="pad">
        <?php
        if ($user_class->gang == 0) {
            if (isset($_POST['create']) && $_POST['create'] != "") { // if they are wanting to start a new gang
                $_POST['tag'] = str_replace('"', '', $_POST['tag']);
                $_POST['name'] = str_replace('"', '', $_POST['name']);

                $gangname = strip_tags($_POST['name']);
                $gangtag = strip_tags($_POST['tag']);

                $error = "";
                $error .= ($user_class->money < 500000) ? "<div>You don't have enough money to start a gang. You need at least $500,000</div>" : $error;
                $error .= ($user_class->gang != 0) ? "<div>You have to leave your gang to start a new gang.</div>" : "";
                $error .= (strlen($gangname) < 3) ? "<div>Your gang's name has to be at least 3 characters long.</div>" : "";
                $error .= (strlen($gangname) > 25) ? "<div>Your gang's name can only be a max of 25 characters long.</div>" : "";
                $error .= (strlen($gangtag) < 1) ? "<div>Your gang's tag has to be at least 1 character long.</div>" : "";
                $error .= (strlen($gangtag) > 3) ? "<div>Your gang's tag can only be a max of 3 characters long.</div>" : "";

                $gangname = addslashes($gangname);
                $gangtag = addslashes($gangtag);

                //check if name is taken yet
                $db->query("SELECT * FROM `gangs` WHERE `name` = ?");
                $db->execute([$gangname]);
                $exist = $db->num_rows();

                $error .= ($exist > 0) ? "<div>The gang name you chose is already taken.</div>" : "";

                //check if tag is taken yet
                $db->query("SELECT * FROM `gangs` WHERE `tag` = ?");
                $db->execute([$gangtag]);
                $exist = $db->num_rows();

                $error .= ($exist > 0) ? "<div>The gang tag you chose is already taken.</div>" : "";
                if ($error == "") { // if there are no errors, make the gang
                    perform_query("INSERT INTO `gangs` (name, tag, leader) VALUES (?, ?, ?)", [$gangname, $gangtag, $user_class->id]);
                    $newmoney = $user_class->money - 500000; //deduct the cost of the money
                    $gangid = $db->insert_id();

                    perform_query("INSERT INTO `ranks` (gang, title, gangwars, ganggrad, color) VALUES (?, 'Member', 0, 0, '#FFFFFF')", [$gangid]);
                    $gang_rank = $db->insert_id();

                    perform_query("UPDATE `grpgusers` SET `gang` = ?, `money` = ?, `gangleader` = '1', `grank` = ? WHERE `id` = ?", [$gangid, $newmoney, $gang_rank, $user_class->id]);
                    perform_query("DELETE FROM `ganginvites` WHERE `playerid` = ?", [$user_class->id]);
                    echo Message("You have successfully created a gang!");
                } else {
                    echo Message($error);
                }
            }
            ?>


            <div class="contenthead floaty">
                <table id="newtables" style="width:100%;">
                    <tr>
                        <form method='post'>
                            Well, it looks like you haven't join or created a gang yet.<br><br>
                            To create a gang it costs $500,000. If you don't have enough, or would like to join someone
                            elses gang, check out the <a href="gang_list.php">Gang List</a> for other gangs to join.<br><br>
                            <table width='100%'>
                                <tr>
                                    <td width='15%'>Gang Name:</td>
                                    <td width='35%'><input type='text' name='name' value='' maxlength='20' size='16'></td>
                                    <td width='15%'>Gang Tag</td>
                                    <td width='35%'><input type='text' name='tag' value='' maxlength='3' size='4'></td>
                                </tr>
                                <tr>
                                    <td colspan='4' align='center'><input type='submit' name='create' value='Create'></td>
                                </tr>
                            </table>
                        </form>
                        </td>
                    </tr>
                    <?php
        } else {
            echo "You're already in a gang!";
        }

        ?>
            </table>
        </div>
    </div>
    <?php
    include 'footer.php';
    ?>