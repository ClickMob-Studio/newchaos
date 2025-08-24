<?php
$ignoreslashes = 1;
$validtax = array(
    0,
    10,
    20,
    30,
    40,
    50,
    60,
    70
);
include 'header.php';
?>
<div class='box_top'>Edit Gang</div>
<div class='box_middle'>
    <div class='pad'>
        <?php
        $gang_class = new Gang($user_class->gang);
        if ($user_class->gang == 0)
            error("You aren't in a gang.");
        $user_rank = new GangRank($user_class->grank);
        if ($user_class->gangleader == 0)
            error("You don't have permission to be here!");
        if (isset($_POST['submit'])) {
            if (!in_array($_POST['tax'], $validtax))
                error("Tax Fraud Detected!");
            $tax = security($_POST['tax']);
            if (isset($_POST['banner']) && !empty($_POST['banner'])) {
                $gangbanner = (getimagesize($_POST['banner']) !== false) ? $_POST['banner'] : "";
            } else {
                $gangbanner = "";
            }

            $error = (strlen($gangname) < 3) ? "<div>Your gang's name has to be at least 3 characters long.</div>" : "";
            $error = (strlen($gangname) > 25) ? "<div>Your gang's name can only be a max of 25 characters long.</div>" : "";
            $error = (strlen($gangtag) < 1) ? "<div>Your gang's tag has to be at least 1 character long.</div>" : "";
            $error = (strlen($gangtag) > 3) ? "<div>Your gang's tag can only be a max of 3 characters long.</div>" : "";
            $gangtag = strip_tags($_POST['tag']);
            $gangname = strip_tags($_POST['name']);
            $gangdesc = $_POST['desc'];
            $gangdesc2 = $_POST['desc2'];
            $db->query("SELECT name FROM gangs WHERE name LIKE ? AND id <> ?");
            $db->execute(array(
                $gangname,
                $gang_class->id
            ));
            $error = ($db->fetch_single()) ? "<div>The gang name you chose is already taken.</div>" : "";
            $db->query("SELECT tag FROM gangs WHERE tag LIKE ? AND id <> ?");
            $db->execute(array(
                $gangtag,
                $gang_class->id
            ));
            $error = ($db->fetch_single()) ? "<div>The gang tag you chose is already taken.</div>" : "";
            if (empty($error)) {
                $db->query("UPDATE gangs SET tag = ?, name = ?, description = ?, publicpage = ?, banner = ?, tax = ? WHERE id = ?");
                $db->execute(array(
                    $gangtag,
                    $gangname,
                    $gangdesc,
                    $gangdesc2,
                    $gangbanner,
                    $tax,
                    $gang_class->id
                ));
                echo Message("Your gang has successfully been edited.");
            } else
                error($error);
        }
        print "

<tr><td class='contentcontent'>
    <form method='post'>
        <table width='100%' border='0'>
            <tr>
                <td width='25%'><b>Gang Tag:</b></td>
                <td width='25%'><input type='text' name='tag' size='3' value='$gang_class->tag' /></td>
            </tr>
            <tr>
                <td width='25%'><b>Gang Name:</b></td>
                <td width='25%'><input type='text' name='name' size='30' value='$gang_class->name' /></td>
            </tr>
            <tr>
                <td width='25%'><b>Gang Tax:</b></td>
                <td width='25%'>
                    <select name='tax'>
";
        foreach ($validtax as $tax)
            echo "<option value='$tax'", ($tax == $gang_class->tax) ? " selected" : "", ">$tax%</option>";
        echo "
                    </select>
                </td>
            </tr>
            <tr>
                <td width='25%'><b>Gang Banner URL:</b></td>
                <td width='25%'>", ($gang_class->boughtbanner == 1) ? "<input type='text' name='banner' size='40' value='$gang_class->banner' /> [75x250]" : "Not Upgraded", "</td>
            </tr>
            <tr>
                <td width='25%'><b>Public Gang Page:</b><br />[<a href='bbcode.php'>BBCode</a>]</td>
                <td width='75%'><textarea name='desc2' cols='53' rows='7'>" . str_replace(array('<', '>'), array('&lt;', '&gt;'), (isset($gang_class->publicpage) ? $gang_class->publicpage : "")) . "</textarea></td>
            </tr>
            <tr>
                <td width='25%'><b>Private Gang Page:</b><br />[<a href='bbcode.php'>BBCode</a>]</td>
                <td width='75%'><textarea name='desc' cols='53' rows='7'>" . str_replace(array('<', '>'), array('&lt;', '&gt;'), (isset($gang_class->description) ? $gang_class->description : "")) . "</textarea></td>
            </tr>
            <tr>
                <td width='25%'></td>
                <td width='25%'><input type='submit' name='submit' value='Edit Gang' /></td>
            </tr>
        </table>
    </form>
</td></tr>
";
        include("gangheaders.php");
        include 'footer.php';
        function error($text)
        {
            echo Message($text);
            include 'footer.php';
            die();
        }
        ?>