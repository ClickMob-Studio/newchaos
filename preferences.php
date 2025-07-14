<?php
$ignoreslashes = 1;
include 'header.php';
?>
<div class='box_top'>Preferences</div>
<div class='box_middle'>
    <div class='pad'>
        <?php
        echo '<div class="floaty">';
        echo '<table style="width:100%;table-layout:fixed;text-align:center;font-weight:bold;">';
        echo '<tr style="line-height:25px;">';
        echo '<td><a href="?username">Username</a></td>';
        echo '<td><a href="?email">Email Address</a></td>';
        echo '<td><a href="?avatar">Avatar</a></td>';
        echo '<td><a href="?quote">Quote</a></td>';
        echo '<td><a href="?display">Display</a></td>';
        echo '</tr>';
        echo '<tr style="line-height:25px;">';
        echo '<td><a href="?cpass">Change Password</a></td>';
        //echo'<td><a href="?gender">Gender</a></td>';
        echo '<td><a href="?sig">Signature</a></td>';
        echo '<td><a href=""></a></td>';
        echo '</tr>';
        echo '<tr style="line-height:25px;">';
        echo '<td><a href="?image_name">Image Name</a></td>';
        echo '<td><a href="?removeprotection">Remove Attack Protection</a></td>';
        echo '<td><a href="?autoplay">Auto Play</a></td>';
        echo '<td><a href="?refills">User Refills</a></td>';
        echo '<td><a href="?mobile">Disable Mobile</a></td>';
        echo '<td><a href="?prefills">Pet Refills</a></td>';
        echo '</tr>';
        echo '<tr style="line-height:25px;">';
        echo '<td><a href=""></a></td>';
        echo '<td><a href="?privacy">Privacy</a></td>';
        echo '<td><a href="?profilewall">Profile Comment Wall</a></td>';
        echo '<td><a href="notepad.php">Notepad</a></td>';
        echo '</tr>';
        echo '<tr style="line-height:25px;">';
        echo '<td><a href=""></a></td>';
        echo '<td><a href=""></a></td>';

        echo '</tr>';
        echo '</table>';
        echo '</div>';
        if ($user_class->id == 174) {

            //$user_class = new User(152);
        
            echo "<style>
    .container {
        margin-top: 14px;
        border: 4px solid black;
        background-color: #222222;
    }
    .header {
        padding: 10px 10px;
        font-size: 16px;
        background-color: #111111;
    }
    .padded {
        padding: 10px 10px;
        font-size: 14px;
    }
    .profile_comment {
        display: flex;
        justify-content: flex-start;
        align-items: center;
        padding: 5px;
    }
    .signature {
        text-align: center;
    }
    .action {
        font-size: 14px;
    }
    .action a, .equipped_main a {
        color: yellow!important;
    }
    .profile_button {
        padding: 15px;
        text-align: center;
    }
    .achievements_main, .equipped_main, .actions_main,.received_gifts_main {
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
        justify-content: space-evenly;
        align-items: center;
        text-align: center;
    }
    .profile_comment_message {
        margin-left: 1em;
    }
    .profile_comment_user {
        width: 10em;
    }
    .received_gifts_main:after, .equipped_main:after, .achievements_main:after {
        content: '';
        flex: auto;
    }
    .gift_item, .equip_item {
        height: 140px;
        width: 25%;
    }
    .achievement {
        height: 110px;
        width: 20%;
    }
    </style>";

            echo '<div>
    <div class="container">
        <div class="header">
            Side Menu Options
        </div>
        <div class="actions_main padded">
            <div class="action">
                <a href="?resetmenu">Reset Menu</a>
            </div>
            <div class="action">
                <a href="?sortmenu">Sortable Menu</a>
            </div>
            <div class="action">
                <a href="?editmenu">Show/Hide Menu Items</a>
            </div>
        </div>
    </div>
    </div>
';
        }

        if (isset($_GET['username'])) {
            if (isset($_POST['username'])) {
                $un = str_replace(array('"', "'"), '', $_POST['username']);
                if (strlen($un) > 20) {
                    diefun("Your username cannot be longer than 20 characters.");
                }

                $db->query("SELECT * FROM grpgusers WHERE username LIKE ? AND id <> ?");
                $db->execute(array(
                    $un,
                    $user_class->id
                ));
                if ($db->num_rows())
                    diefun("This username is already in use.");
                $db->query("UPDATE grpgusers SET username = ? WHERE id = ?");
                $db->execute(array(
                    $un,
                    $user_class->id
                ));
                $user_class->username = $un;
            }
            echo '<div class="floaty" style="width:60%;">';
            echo '<form method="post">';
            echo '<input type="text" value="' . $user_class->username . '" name="username" /> ';
            echo '<input type="submit" value="Change Username" />';
            echo '</form>';
            echo '</div>';
            $uninfo = explode("|", $user_class->uninfo);
            $gns = explode("~", $uninfo[1]);
            $out = explode("~", $uninfo[4]);
            $glows = explode(",", $out[1]);
            $line = "<input type='text' class='color' onChange='change(\"line\");' id='line' value='{$out[1]}' />";
            switch ($uninfo[0]) {
                case 3:
                    $gn = "<input type='text' class='color' onChange='change(\"color1\");' id='color1' value='{$gns[0]}' /> => <input type='text' class='color' onChange='change(\"color2\");' id='color2' value='{$gns[1]}' /> => <input type='text' class='color' onChange='change(\"color3\");' id='color3' value='{$gns[2]}' />";
                    break;
                case 2:
                    $gn = "<input type='text' class='color' onChange='change(\"color1\");' id='color1' value='{$gns[0]}' /> => <input type='text' class='color' onChange='change(\"color2\");' id='color2' value='{$gns[1]}' />";
                    break;
                default:
                    $gn = "<input type='text' class='color' onChange='change(\"color1\");' id='color1' value='{$gns[0]}' />";
                    break;
            }
            switch ($uninfo[6]) {
                case 3:
                    $glow = "<input type='text' class='color' onChange='change(\"glow1\");' id='glow1' value='{$glows[0]}' /> => <input type='text' class='color' onChange='change(\"glow2\");' id='glow2' value='{$glows[1]}' /> => <input type='text' class='color' onChange='change(\"glow3\");' id='glow3' value='{$glows[2]}' />";
                    break;
                case 2:
                    $glow = "<input type='text' class='color' onChange='change(\"glow1\");' id='glow1' value='{$glows[0]}' /> => <input type='text' class='color' onChange='change(\"glow2\");' id='glow2' value='{$glows[1]}' />";
                    break;
                default:
                    $glow = "<input type='text' class='color' onChange='change(\"glow1\");' id='glow1' value='{$glows[0]}' />";
                    break;
            }
            $boldOptions = array(
                'Thin' => 100,
                'Medium' => 400,
                'Bold' => 700
            );
            $italicOptions = array(
                'yes',
                'no'
            );
            $outlineOptions = range(0, 10);
            $spacingOptions = range(-2, 4);
            $bold = $italic = $outline = $spacing = "";
            foreach ($boldOptions as $name => $value)
                $bold .= ($value == $uninfo[2]) ? "<option value='$value' selected>$name</option>" : "<option value='$value'>$name</option>";
            foreach ($italicOptions as $value)
                $italic .= ($value == $uninfo[3]) ? "<option value='$value' selected>$value</option>" : "<option value='$value'>$value</option>";
            foreach ($outlineOptions as $value)
                $outline .= ($value == $out[0]) ? "<option value='$value' selected>$value</option>" : "<option value='$value'>$value</option>";
            foreach ($spacingOptions as $value)
                $spacing .= ($value == $uninfo[5]) ? "<option value='$value' selected>$value</option>" : "<option value='$value'>$value</option>";
            print "
    <script type='text/javascript' src='js/cp/jscolor.js'></script>
    <script>
    function change(which){
        $.post('newname.php', {which:which,var:$('#' + which).val()}, function (d){
            $('#gendName').html(d);
        });
    }
    function regn(num){
        $.post('newname.php', {num,num}, function(){
            window.location = '?username';
        });

    }
    function regn_g(num){
        $.post('newname.php', {'glow':num}, function(){
            window.location = '?username';
        });

    }
    </script>
    <div class='floaty'>";
            print "
        <span style='font-size: 1.5em;'>You have $user_class->gndays Gradient Name Days left.<br />
        <br />
        For Gradient Name Select: <button onClick='regn(1);'>1 Color</button> <button onClick='regn(2);'>2 Colors</button> <button onClick='regn(3);'>3 Colors</button><br />
        <br />
        For Gradient <span style='text-shadow: 0 0 3px red;'>Glow</span> Select: </span><button onClick='regn_g(1);'>1 Color</button> <button onClick='regn_g(2);'>2 Colors</button> <button onClick='regn_g(3);'>3 Colors</button><br />
        <br /></span>
        <table id='newtables' style='width:100%;'>
            <tr>
                <th>Gradient Name</th>
                <td id='gncolors'>$gn</td>
            </tr>
            <tr>
                <th>Bold Name</th>
                <td><select type='dropdown' onChange='change(\"bold\");' id='bold'>$bold</select></td>
            </tr>
            <tr>
                <th>Italic Name</th>
                <td><select type='dropdown' onChange='change(\"italic\");' id='italic'>$italic</select></td>
            </tr>
            <tr>
                <th>Text Outline/Glow</th>
                <td><select type='dropdown' onChange='change(\"outline\");' id='outline'>$outline</select> => $glow</td>
            </tr>
            <tr>
                <th>Letter Spacing</th>
                <td><select type='dropdown' onChange='change(\"spacing\");' id='spacing'>$spacing</select></td>
            </tr>
        </table>
        <br />
        <br />
        <span id='gendName'>" . formatName($user_class->id) . "</span>
    </div>";

        } elseif (isset($_GET['resetmenu'])) {
            $db->query("UPDATE grpgusers SET menuorder = DEFAULT(menuorder) WHERE id = ?");
            $db->execute(array($user_class->id));
            echo Message("Menu order has been reset");
        } elseif (isset($_GET['cpass'])) {
            if (isset($_POST['opw'])) {
                $npw = $_POST['npw'];
                $cpw = $_POST['cpw'];
                $password = sha1($_POST['opw']);
                $password2 = sha1($password);
                $new = sha1($db->real_escape_string($npw));
                //$new = fuzzehCrypt($new);
                if ($user_class->password != $password && $user_class->password != $password2)
                    diefun("Incorrect old password entered.");
                if ($npw != $cpw)
                    diefun("Your new passwords do not match.");
                $db->query("UPDATE grpgusers SET password = ? WHERE id = ?");
                $db->execute(array(
                    $new,
                    $user_class->id
                ));
                echo Message("Password changed successfully.");
            }
            echo '<div class="floaty" style="width:60%;">';
            echo '<form method="post">';
            echo '<table>';
            echo '<tr>';
            echo '<td>Old Password:</td>';
            echo '<td><input type="password" name="opw" /></td>';
            echo '</tr>';
            echo '<tr>';
            echo '<td>New Password:</td>';
            echo '<td><input type="password" name="npw" /></td>';
            echo '</tr>';
            echo '<tr>';
            echo '<td>Confirm Password:</td>';
            echo '<td><input type="password" name="cpw" /></td>';
            echo '</tr>';
            echo '<tr>';
            echo '<td colspan="2" style="text-align:center;"><input type="submit" value="Change Password" /></td>';
            echo '</tr>';
            echo '</table>';
            echo '</form>';
            echo '</div>';
        } elseif (isset($_GET['email'])) {
            if (isset($_POST['email'])) {
                $em = $_POST['email'];
                $db->query("SELECT * FROM grpgusers WHERE email LIKE ? AND id <> ?");
                $db->execute(array(
                    $em,
                    $user_class->id
                ));
                if ($db->num_rows())
                    diefun("This email is already in use.");
                $db->query("UPDATE grpgusers SET email = ? WHERE id = ?");
                $db->execute(array(
                    $em,
                    $user_class->id
                ));
                $user_class->email = $em;
            }
            echo '<div class="floaty" style="width:60%;">';
            echo '<form method="post">';
            echo '<input type="text" value="' . $user_class->email . '" name="email" /> ';
            echo '<input type="submit" value="Change Email" />';
            echo '</form>';
            echo '</div>';
        } elseif (isset($_GET['avatar'])) {
            if (isset($_POST['avatar'])) {
                $avi = $_POST['avatar'];
                if (!getimagesize($avi) && $avi != '')
                    diefun("Invalid image detected.");
                $db->query("UPDATE grpgusers SET avatar = ? WHERE id = ?");
                $db->execute(array(
                    $avi,
                    $user_class->id
                ));
                $user_class->avatar = $avi;
            }
            echo '<div class="floaty" style="width:80%;">';
            echo '<form method="post">';
            echo '<input type="text" value="' . $user_class->avatar . '" name="avatar" size="75" /> ';
            echo '<input type="submit" value="Change Avatar" />';
            echo '</form>';
            echo '</div>';
        } elseif (isset($_GET['quote'])) {
            if (isset($_POST['quote'])) {
                $qt = $_POST['quote'];
                $db->query("UPDATE grpgusers SET quote = ? WHERE id = ?");
                $db->execute(array(
                    $qt,
                    $user_class->id
                ));
                $user_class->quote = $qt;
            }
            echo '<div class="floaty" style="width:80%;">';
            echo '<form method="post">';
            echo '<input type="text" value="' . $user_class->quote . '" name="quote" size="75" /> ';
            echo '<input type="submit" value="Change Quote" />';
            echo '</form>';
            echo '</div>';
        } elseif (isset($_GET['display'])) {

            if (isset($_GET['display_s']) && $_GET['display_s'] === 'switch') {
                if ($user_class->is_mobile_disabled > 0) {
                    perform_query("UPDATE grpgusers SET is_mobile_disabled = 0 WHERE id = ?", [$user_class->id]);
                } else {
                    perform_query("UPDATE grpgusers SET is_mobile_disabled = 1 WHERE id = ?", [$user_class->id]);
                }

                header('Location: preferences.php?display');
            }
            ?>
            <p>Would you like to enable/disable mobile display?</p>

            <?php if ($user_class->is_mobile_disabled > 0): ?>
                <a href="preferences.php?display&display_s=switch" style="color: green">Enable</a>
            <?php else: ?>
                <a href="preferences.php?display&display_s=switch" style="color: red;">Disable</a>
            <?php endif; ?>


            <?php

        } elseif (isset($_GET['mobile'])) {
            if (isset($_COOKIE['useMobileHeader'])) {
                setcookie('useMobileHeader', '', time() - 3600, '/'); // Expiration time: 1 hour ago
        
                echo "You have turned on mobile display";
            } else {
                $expirationTime = time() + (365 * 24 * 60 * 60); // Current time + 365 days
        
                // Set the cookie with the calculated expiration time
                setcookie('useMobileHeader', 'true', $expirationTime, '/');
                echo "You have disabled the mobile display";
            }
        } elseif (isset($_GET['autoplay'])) {
            if (isset($_POST['autoplay'])) {
                $music = security($_POST['autoplay']);
                $db->query("UPDATE grpgusers SET music = ? WHERE id = ?");
                $db->execute(array(
                    $music,
                    $user_class->id
                ));
                $user_class->music = $music;
            }
            $opts = array('No', 'Yes');
            echo '<div class="floaty" style="width:60%;">';
            echo '<form method="post">';
            echo '<select name="autoplay">';
            foreach ($opts as $index => $opt)
                echo '<option value="' . $index . '"', ($index == $user_class->music) ? ' selected' : '', '>' . $opt . '</option>';
            echo '</select> ';
            echo '<input type="submit" value="Change Auto Play" />';
            echo '</form>';
            echo '</div>';
        } elseif (isset($_GET['gender'])) {
            exit;
            $opts = array('Male', 'Female');
            if (isset($_POST['gender'])) {
                $gender = $_POST['gender'];
                if (!in_array($gender, $opts))
                    diefun("Invalid Gender.");
                $db->query("UPDATE grpgusers SET gender = ? WHERE id = ?");
                $db->execute(array(
                    $gender,
                    $user_class->id
                ));
                $user_class->gender = $gender;
            }
            echo '<div class="floaty" style="width:60%;">';
            echo '<form method="post">';
            echo '<select name="gender">';
            foreach ($opts as $opt)
                echo '<option value="' . $opt . '"', ($opt == $user_class->gender) ? ' selected' : '', '>' . $opt . '</option>';
            echo '</select> ';
            echo '<input type="submit" value="Change Gender" />';
            echo '</form>';
            echo '</div>';
        } elseif (isset($_GET['gradname'])) {
            if ($user_class->gndays <= 0)
                diefun("You do not have a gradient name.");
            if (isset($_POST['startcolor'])) {
                if (!isset($_POST['midcolor'])) {
                    $final = '#' . $_POST['startcolor'] . '~#' . $_POST['endcolor'];
                    $grad = 2;
                } else {
                    $final = '#' . $_POST['startcolor'] . '~#' . $_POST['midcolor'] . '~#' . $_POST['endcolor'];
                    $grad = 3;
                }
                $db->query("UPDATE grpgusers SET colours = ?, gradient = ? WHERE id = ?");
                $db->execute(array(
                    $final,
                    $grad,
                    $user_class->id
                ));
                $user_class->colours = $final;
            }
            $colors = explode('~', $user_class->colours);
            if (count($colors) == 2) {
                $st = $colors[0];
                $ed = $colors[1];
            } else {
                $st = $colors[0];
                $mid = $colors[1];
                $ed = $colors[2];
            }
            echo '<script type="text/javascript" data-cfasync="false" src="js/cp/jscolor.js"></script>';
            echo '<tr><td class="contentspacer"></td></tr>';
            echo '<tr><td style="text-align:center;font-size:1.5em;">You have ' . $user_class->gndays . ' gradient name days left.</td></tr>';
            echo '<tr><td class="contenthead">Gradient Name Settings (2 Colours)</td></tr>';
            echo '<tr><td class="contentcontent">';
            echo '<form method="post">';
            echo '<table id="newtables" style="width:100%;">';
            echo '<tr>';
            echo '<th>Starting Colour</th>';
            echo '<td><input type="text" class="color" value="' . $st . '" name="startcolor"></td>';
            echo '<th>Ending Colour</th>';
            echo '<td><input type="text" class="color" value="' . $ed . '" name="endcolor"></td>';
            echo '</tr>';
            echo '<tr>';
            echo '<td colspan="4" class="center"><input type="submit" name="submit" value="Save Preferences" /></td>';
            echo '</tr>';
            echo '</table>';
            echo '</form>';
            echo '<tr><td class="contentspacer"></td></tr>';
            echo '<tr><td class="contenthead">Gradient Name Settings (3 Colours)</td></tr>';
            echo '<tr><td class="contentcontent">';
            echo '<form method="post">';
            echo '<table id="newtables" style="width:100%;">';
            echo '<tr>';
            echo '<th>Starting Colour</th>';
            echo '<td><input type="text" class="color" value="' . $st . '" name="startcolor"></td>';
            echo '<th>Middle Colour</th>';
            echo '<td><input type="text" class="color" value="' . $mid . '" name="midcolor"></td>';
            echo '<th>Ending Colour</th>';
            echo '<td><input type="text" class="color" value="' . $ed . '" name="endcolor"></td>';
            echo '</tr>';
            echo '<tr>';
            echo '<td colspan="6" class="center"><input type="submit" name="submit" value="Save Preferences" /></td>';
            echo '</tr>';
            echo '</table>';
            echo '</form>';
        } elseif (isset($_GET['image_name'])) {
            if ($user_class->pdimgname == 0)
                diefun("You must purchase an image name off the donation store.");
            if (isset($_POST['image_name'])) {
                $image_name = $_POST['image_name'];
                if (!getimagesize($image_name) && $image_name != '')
                    diefun("Invalid image detected.");
                $db->query("UPDATE grpgusers SET image_name = ? WHERE id = ?");
                $db->execute(array(
                    $image_name,
                    $user_class->id
                ));
                $user_class->image_name = $image_name;
            }
            echo '<div class="floaty" style="width:80%;">';
            echo '<form method="post">';
            echo '<input type="text" value="' . $user_class->image_name . '" name="image_name" size="75" /> ';
            echo '<input type="submit" value="Change Image Name" />';
            echo '</form>';
            echo '</div>';
        } elseif (isset($_GET['sig'])) {
            if (isset($_POST['sig'])) {
                $sig = $_POST['sig'];
                $db->query("UPDATE grpgusers SET signature = ? WHERE id = ?");
                $db->execute(array(
                    $sig,
                    $user_class->id
                ));
                $user_class->signature = $sig;
            }
            echo '<div class="floaty">';
            echo '<form method="post">';
            echo '<textarea style="width:100%;height:350px;" name="sig">' . $user_class->signature . '</textarea><br />';
            echo '<input type="submit" value="Change Signature" />';
            echo '</form>';
            echo '</div>';
        } elseif (isset($_GET['disablemobile'])) {
            if ($user_class->view_preference == 0) {
                perform_query("UPDATE grpgusers SET is_mobile_disabled = 1 WHERE id = ?", [$user_class->id]);
                echo Message("Mobile view diabled please click away from this page");
            } else {
                perform_query("UPDATE grpgusers SET is_mobile_disabled = 0 WHERE id = ?", [$user_class->id]);
                echo Message("Mobile view enabled please click away from this page");
            }


        } elseif (isset($_GET['prefills'])) {
            $db->query("SELECT * FROM pets WHERE userid = ?");
            $db->execute(array(
                $user_class->id
            ));
            $row = $db->fetch_row(true);
            if (isset($_GET['ner'])) {
                switch ($_GET['ner']) {
                    case 0:
                        if ($row['nerref'] != 0)
                            diefun("Nice Try.");
                        if ($user_class->points < 250)
                            diefun("You do not have enough points.");
                        $user_class->points -= 250;
                        $db->query("UPDATE grpgusers SET points = ? WHERE id = ?");
                        $db->execute(array(
                            $user_class->points,
                            $user_class->id
                        ));
                        $db->query("UPDATE pets SET nerref = 2, nerreftime = unix_timestamp() WHERE userid = ?");
                        $db->execute(array(
                            $user_class->id
                        ));
                        $row['nerref'] = 2;
                        break;
                    case 1:
                        if ($row['nerref'] == 0)
                            diefun("Nice Try.");
                        $db->query("UPDATE pets SET nerref = 2 WHERE userid = ?");
                        $db->execute(array(
                            $user_class->id
                        ));
                        $row['nerref'] = 2;
                        break;
                    case 2:
                        if ($row['nerref'] == 0)
                            diefun("Nice Try.");
                        $db->query("UPDATE pets SET nerref = 1 WHERE userid = ?");
                        $db->execute(array(
                            $user_class->id
                        ));
                        $row['nerref'] = 1;
                        break;
                }
            }
            switch ($row['nerref']) {
                case 0:
                    $status = "<span style='color:red;'>[Not Paid For]</span>";
                    $button = '<button onClick="if(confirm(\'Are you sure you want enable nerve refills?\')){window.location.href = \'?prefills&ner=0\';}">Buy(250 Points)</button>';
                    break;
                case 1:
                    $status = "<span style='color:orange;'>[Paid For/Disabled]</span>";
                    $button = "<a href='?prefills&ner=1'><button>Enable</button></a>";
                    break;
                case 2:
                    $status = "<span style='color:green;'>[Paid For/Enabled]</span>";
                    $button = "<a href='?prefills&ner=2'><button>Disable</button></a>";
                    break;
            }
            echo '<div class="flexcont">';
            echo '<div class="floaty" style="flex:1;margin-right:4px;">';
            echo 'Nerve Refill<br />';
            echo '<br />';
            echo 'Current Status: ' . $status . '<br />';
            echo '<br />';
            echo $button;
            echo '</div>';
            echo '</div>';
        } else if (isset($_GET['privacy'])) {
            if (isset($_POST['privacy'])) {
                echo Message("Updated");
                $db->query("UPDATE grpgusers SET dprivacy = ? WHERE id = ?");
                $db->execute(
                    array(
                        $_POST['priv'],
                        $user_class->id
                    )
                );
                $user_class->dprivacy = $_POST['priv'];
            }
            echo '<div class="floaty" style="width:60%;">';
            echo '<h2 class="text-14">Do you wish to be anonymous in the bloodbath for donations?</h2>';
            echo '<form method="post">';
            echo '<select name="priv">';
            echo '<option value="1"' . (($user_class->dprivacy == 1) ? ' selected' : ''), '>Yes</option>';
            echo '<option value="0"' . (($user_class->dprivacy == 0) ? ' selected' : ''), '>No</option>';
            echo '</select> ';
            echo '<input type="submit" name="privacy" value="Update" />';
            echo '</form>';
            echo '</div>';
        } else if (isset($_GET['sortmenu'])) {
            if (isset($_POST['sortmenu'])) {
                echo Message("Updated");
                $db->query("UPDATE grpgusers SET sortablemenu = ? WHERE id = ?");
                $db->execute(
                    array(
                        $_POST['sortm'],
                        $user_class->id
                    )
                );
                $user_class->sortablemenu = $_POST['sortm'];
            }
            echo '<div class="floaty" style="width:100%;">';
            echo '<h2 class="text-14">Enable sortable menu?</h2>';
            echo '<form method="post">';
            echo '<select name="sortm">';
            echo '<option value="1"' . (($user_class->sortablemenu == 1) ? ' selected' : ''), '>Yes</option>';
            echo '<option value="0"' . (($user_class->sortablemenu == 0) ? ' selected' : ''), '>No</option>';
            echo '</select> ';
            echo '<input type="submit" name="sortmenu" value="Update" />';
            echo '</form>';
            echo '</div>';
        } else if (isset($_GET['profilewall'])) {
            if (isset($_POST['profilewall'])) {
                echo Message("Updated");
                $db->query("UPDATE grpgusers SET profilewall = ? WHERE id = ?");
                $db->execute(
                    array(
                        $_POST['pwall'],
                        $user_class->id
                    )
                );
                $user_class->profilewall = $_POST['pwall'];
            }
            echo '<div class="floaty" style="width:100%;">';
            echo '<h2 class="text-14">Enable Profile Comment Wall?</h2>';
            echo '<form method="post">';
            echo '<select name="pwall">';
            echo '<option value="1"' . (($user_class->profilewall == 1) ? ' selected' : ''), '>On</option>';
            echo '<option value="0"' . (($user_class->profilewall == 0) ? ' selected' : ''), '>Off</option>';
            echo '</select> ';
            echo '<input type="submit" name="profilewall" value="Update" />';
            echo '</form>';
            echo '</div>';
        } else if (isset($_GET['prestige'])) {

            $max_prestige = 15;

            if (isset($_POST['prestige'])) {
                // echo Message("Updated");
        
                if ($_POST['pres'] > 0 && $_POST['pres'] <= $max_prestige) {
                    if ($user_class->prestige >= $_POST['pres']) {
                        $skull = $_POST['pres'];
                        $db->query("INSERT INTO prestige_skull (id, `user_id`, skull) VALUES (null, ?, ?) ON DUPLICATE KEY UPDATE skull = ?");
                        $db->execute(
                            array(
                                $user_class->id,
                                $skull,
                                $skull,
                            )
                        );
                        echo "<br><br>";
                        echo Message("Prestige Icon Updated - the change may take a few minutes to take effect");
                        echo "</div>";
                    }
                }
            } else {
                echo '<style>
            .pres {
                display: flex;
                flex-direction: row;
                flex-wrap: wrap;
                align-items: stretch;
                justify-content: center;
            }
            .text-orange {
                color: orange;
                font-size: 14px;
            }
            </style>';
                echo '<div class="floaty" style="width:100%;">';
                echo '<h1 class="text-14">Choose Your Prestige Icon</h1>';
                echo '<h1 class="text-14 text-orange">Due to browser cache the change may take a few minutes to apply</h1>';
                echo "<div style='display: flex;
                flex-wrap: wrap;
                flex-direction: column;'>";
                echo '<form method="post">';
                for ($i = 1; $i <= $max_prestige; $i++) {
                    if ($user_class->prestige < $i)
                        continue;
                    echo "<div class='pres'>";
                    echo "<input type='radio' id='pres_$i' name='pres' value='$i'>";
                    echo "<label for='pres_$i'><img src='images/skullpres_$i.png?1'></label>";
                    echo "</div>";
                }
                // echo'<select name="pwall">';
                //     echo'<option value="1"' . (($user_class->profilewall == 1) ? ' selected' : '') , '>On</option>';
                //     echo'<option value="0"' . (($user_class->profilewall == 0) ? ' selected' : '') , '>Off</option>';
                // echo'</select> ';
                echo '<input type="submit" name="prestige" value="Update" />';
                echo '</form>';
                echo "<div/>";
                echo '</div>';
            }

        } else {
            if (isset($_GET['ner'])) {
                switch ($_GET['ner']) {
                    case 0:
                        if ($user_class->nerref != 0)
                            diefun("Nice Try.");
                        if ($user_class->points < 250)
                            diefun("You do not have enough points.");
                        $user_class->points -= 250;
                        $user_class->nerref = 2;
                        $db->query("UPDATE grpgusers SET nerref = ?, points = ?, nerreftime = unix_timestamp() WHERE id = ?");
                        $db->execute(array(
                            $user_class->nerref,
                            $user_class->points,
                            $user_class->id
                        ));
                        break;
                    case 1:
                        if ($user_class->nerref == 0)
                            diefun("Nice Try.");
                        $user_class->nerref = 2;
                        $db->query("UPDATE grpgusers SET nerref = ? WHERE id = ?");
                        $db->execute(array(
                            $user_class->nerref,
                            $user_class->id
                        ));
                        break;
                    case 2:
                        if ($user_class->nerref == 0)
                            diefun("Nice Try.");
                        $user_class->nerref = 1;
                        $db->query("UPDATE grpgusers SET nerref = ? WHERE id = ?");
                        $db->execute(array(
                            $user_class->nerref,
                            $user_class->id
                        ));
                        break;
                }
            }
            if (isset($_GET['ngy'])) {
                switch ($_GET['ngy']) {
                    case 0:
                        if ($user_class->ngyref != 0)
                            diefun("Nice Try.");
                        if ($user_class->points < 250)
                            diefun("You do not have enough points.");
                        $user_class->points -= 250;
                        $user_class->ngyref = 2;
                        $db->query("UPDATE grpgusers SET ngyref = ?, points = ?, ngyreftime = unix_timestamp() WHERE id = ?");
                        $db->execute(array(
                            $user_class->ngyref,
                            $user_class->points,
                            $user_class->id
                        ));
                        break;
                    case 1:
                        if ($user_class->ngyref == 0)
                            diefun("Nice Try.");
                        $user_class->ngyref = 2;
                        $db->query("UPDATE grpgusers SET ngyref = ? WHERE id = ?");
                        $db->execute(array(
                            $user_class->ngyref,
                            $user_class->id
                        ));
                        break;
                    case 2:
                        if ($user_class->ngyref == 0)
                            diefun("Nice Try.");
                        $user_class->ngyref = 1;
                        $db->query("UPDATE grpgusers SET ngyref = ? WHERE id = ?");
                        $db->execute(array(
                            $user_class->ngyref,
                            $user_class->id
                        ));
                        break;
                }
            }
            switch ($user_class->nerref) {
                case 0:
                    $status = "<span style='color:red;'>[Not Paid For]</span>";
                    $button = '<button onClick="if(confirm(\'Are you sure you want enable nerve refills?\')){window.location.href = \'?ner=0\';}">Buy(250 Points)</button>';
                    break;
                case 1:
                    $status = "<span style='color:orange;'>[Paid For/Disabled]</span>";
                    $button = "<a href='?ner=1'><button>Enable</button></a>";
                    break;
                case 2:
                    $status = "<span style='color:green;'>[Paid For/Enabled]</span>";
                    $button = "<a href='?ner=2'><button>Disable</button></a>";
                    break;
            }
            echo '<div class="flexcont">';
            echo '<div class="floaty" style="flex:1;margin-right:4px;">';
            echo 'Nerve Refill<br />';
            echo '<br />';
            echo 'Current Status: ' . $status . '<br />';
            echo '<br />';
            echo $button;
            echo '</div>';
            switch ($user_class->ngyref) {
                case 0:
                    $status = "<span style='color:red;'>[Not Paid For]</span>";
                    $button = '<button onClick="if(confirm(\'Are you sure you want enable energy refills?\')){window.location.href = \'?ngy=0\';}">Buy(250 Points)</button>';
                    break;
                case 1:
                    $status = "<span style='color:orange;'>[Paid For/Disabled]</span>";
                    $button = "<a href='?ngy=1'><button>Enable</button></a>";
                    break;
                case 2:
                    $status = "<span style='color:green;'>[Paid For/Enabled]</span>";
                    $button = "<a href='?ngy=2'><button>Disable</button></a>";
                    break;
            }
            echo '<div class="floaty" style="flex:1;margin-left:4px;">';
            echo 'Energy Refill<br />';
            echo '<br />';
            echo 'Current Status: ' . $status . '<br />';
            echo '<br />';
            echo $button;
            echo '</div>';
            echo '</div>';
        }
        echo '</div>';
        include 'footer.php';
        function fuzzehCrypt($pass)
        {
            return crypt($pass, '$6$rounds=5000$awrgwrnuBUIEF89243t89bNFAEb942$');
        }
        ?>