<?php
include 'header.php';
$firstn = $_GET['firstn'];
$secondn = $_GET['secondn'];
if ($user_class->namedays < 0) {
    echo Message('Sorry, you have not bought a gradient name for this account.');
    die();
}
if ($firstn == "1") {
    $grada = "FF0000";
}
if ($firstn == "2") {
    $grada = "FF8000";
}
if ($firstn == "3") {
    $grada = "FFFF00";
}
if ($firstn == "4") {
    $grada = "00FF00";
}
if ($firstn == "5") {
    $grada = "00FFFF";
}
if ($firstn == "6") {
    $grada = "0101DF";
}
if ($firstn == "7") {
    $grada = "4B088A";
}
if ($firstn == "8") {
    $grada = "DF01D7";
}
if ($firstn == "9") {
    $grada = "000000";
}
if ($firstn == "10") {
    $grada = "FFFFFF";
}
if ($firstn == "11") {
    $grada = "610B0B";
}
if ($firstn == "12") {
    $grada = "8A4B08";
}
if ($firstn == "13") {
    $grada = "868A08";
}
if ($firstn == "14") {
    $grada = "0B3B0B";
}
if ($firstn == "15") {
    $grada = "0B615E";
}
if ($firstn == "16") {
    $grada = "0A0A2A";
}
if ($firstn == "17") {
    $grada = "1B0A2A";
}
if ($firstn == "18") {
    $grada = "610B38";
}
if ($secondn == "1") {
    $gradb = "FF0000";
}
if ($secondn == "2") {
    $gradb = "FF8000";
}
if ($secondn == "3") {
    $gradb = "FFFF00";
}
if ($secondn == "4") {
    $gradb = "00FF00";
}
if ($secondn == "5") {
    $gradb = "00FFFF";
}
if ($secondn == "6") {
    $gradb = "0101DF";
}
if ($secondn == "7") {
    $gradb = "4B088A";
}
if ($secondn == "8") {
    $gradb = "DF01D7";
}
if ($secondn == "9") {
    $gradb = "000000";
}
if ($secondn == "10") {
    $gradb = "FFFFFF";
}
if ($secondn == "11") {
    $gradb = "610B0B";
}
if ($secondn == "12") {
    $gradb = "8A4B08";
}
if ($secondn == "13") {
    $gradb = "868A08";
}
if ($secondn == "14") {
    $gradb = "0B3B0B";
}
if ($secondn == "15") {
    $gradb = "0B615E";
}
if ($secondn == "16") {
    $gradb = "0A0A2A";
}
if ($secondn == "17") {
    $gradb = "1B0A2A";
}
if ($secondn == "18") {
    $gradb = "610B38";
}
if ($firstn > "0") {
    if ($grada != $user_class->gradtwo) {
        perform_query("UPDATE `opexusers` SET `gradone`= ? WHERE `id`= ?", [$grada, $user_class->id]);
        echo Message('<center>Your gradient name has been updated. You will see the effect when you visit another page.</center>');
    } else {
        echo Message("<center>Sorry, your first gradient cannot be the same as your second gradient. Please pick another.</center>");
    }
}
if ($secondn > "0") {
    if ($gradb != $user_class->gradone) {
        perform_query("UPDATE `opexusers` SET `gradtwo`= ? WHERE `id`= ?", [$gradb, $user_class->id]);
        echo Message('<center>Your gradient name has been updated. You will see the effect when you visit another page.</center>');
    } else {
        echo Message("<center>Sorry, your second gradient cannot be the same as your first gradient. Please pick another.</center>");
    }
}
?>
<tr>
    <td class="contenthead">Gradient Color Change</td>
</tr>
<tr>
    <td class="contentcontent">
        <center>
            Here you can choose a first/secondary color for your gradient name.<br>
            Scroll down for the darker colours.
        </center>
    </td>
</tr>
<tr>
    <td class="contentcontent">
        <center><b>Normal Colours</b></center>
        <table align='center'>
            <tr>
                <td><b>
                        <font color="#FF0000">Red</font>
                    </b></td>
                <td> - </td>
                <td><a href='colorname.php?firstn=1'>First</a> / <a href='colorname.php?secondn=1'>Second</a></td>
            </tr>
            <tr>
                <td> <b>
                        <font color="#FF8000">Orange</font>
                    </b></td>
                <td> - </td>
                <td><a href='colorname.php?firstn=2'>First</a> / <a href='colorname.php?secondn=2'>Second</a></td>
            </tr>
            <tr>
                <td> <b>
                        <font color="#FFFF00">Yellow</font>
                    </b></td>
                <td> - </td>
                <td><a href='colorname.php?firstn=3'>First</a> / <a href='colorname.php?secondn=3'>Second</a></td>
            </tr>
            <tr>
                <td> <b>
                        <font color="#00FF00">Green</font>
                    </b></td>
                <td> - </td>
                <td><a href='colorname.php?firstn=4'>First</a> / <a href='colorname.php?secondn=4'>Second</a></td>
            </tr>
            <tr>
                <td> <b>
                        <font color="#00FFFF">Cyan</font>
                    </b></td>
                <td> - </td>
                <td><a href='colorname.php?firstn=5'>First</a> / <a href='colorname.php?secondn=5'>Second</a></td>
            </tr>
            <tr>
                <td> <b>
                        <font color="#0101DF">Blue</font>
                    </b></td>
                <td> - </td>
                <td><a href='colorname.php?firstn=6'>First</a> / <a href='colorname.php?secondn=6'>Second</a></td>
            </tr>
            <tr>
                <td> <b>
                        <font color="#4B088A">Purple</font>
                    </b></td>
                <td> - </td>
                <td><a href='colorname.php?firstn=7'>First</a> / <a href='colorname.php?secondn=7'>Second</a></td>
            </tr>
            <tr>
                <td> <b>
                        <font color="#DF01D7">Pink</font>
                    </b></td>
                <td> - </td>
                <td><a href='colorname.php?firstn=8'>First</a> / <a href='colorname.php?secondn=8'>Second</a></td>
            </tr>
        </table>
</tr>
</td>
<tr>
    <td class="contentcontent">
        <center><b>Darker Colours</b></center>
        <table align='center'>
            <tr>
                <td><b>
                        <font color="#610B0B">Red</font>
                    </b></td>
                <td> - </td>
                <td><a href='colorname.php?firstn=11'>First</a> / <a href='colorname.php?secondn=11'>Second</a></td>
            </tr>
            <tr>
                <td> <b>
                        <font color="#8A4B08">Orange</font>
                    </b></td>
                <td> - </td>
                <td><a href='colorname.php?firstn=12'>First</a> / <a href='colorname.php?secondn=12'>Second</a></td>
            </tr>
            <tr>
                <td> <b>
                        <font color="#868A08">Yellow</font>
                    </b></td>
                <td> - </td>
                <td><a href='colorname.php?firstn=13'>First</a> / <a href='colorname.php?secondn=13'>Second</a></td>
            </tr>
            <tr>
                <td> <b>
                        <font color="#0B3B0B">Green</font>
                    </b></td>
                <td> - </td>
                <td><a href='colorname.php?firstn=14'>First</a> / <a href='colorname.php?secondn=14'>Second</a></td>
            </tr>
            <tr>
                <td> <b>
                        <font color="#0B615E">Cyan</font>
                    </b></td>
                <td> - </td>
                <td><a href='colorname.php?firstn=15'>First</a> / <a href='colorname.php?secondn=15'>Second</a></td>
            </tr>
            <tr>
                <td> <b>
                        <font color="#0A0A2A">Blue</font>
                    </b></td>
                <td> - </td>
                <td><a href='colorname.php?firstn=16'>First</a> / <a href='colorname.php?secondn=16'>Second</a></td>
            </tr>
            <tr>
                <td> <b>
                        <font color="#1B0A2A">Purple</font>
                    </b></td>
                <td> - </td>
                <td><a href='colorname.php?firstn=17'>First</a> / <a href='colorname.php?secondn=17'>Second</a></td>
            </tr>
            <tr>
                <td> <b>
                        <font color="#610B38">Pink</font>
                    </b></td>
                <td> - </td>
                <td><a href='colorname.php?firstn=18'>First</a> / <a href='colorname.php?secondn=18'>Second</a></td>
            </tr>
        </table>
</tr>
</td>
<tr>
    <td class="contentcontent">
        <center><b>Other Colours</b></center>
        <table align='center'>
            <tr>
                <td><b>
                        <font color="#000000">Black</font>
                    </b></td>
                <td> - </td>
                <td><a href='colorname.php?firstn=9'>First</a> / <a href='colorname.php?secondn=9'>Second</a></td>
            </tr>
            <tr>
                <td> <b>
                        <font color="#FFFFFF">White</font>
                    </b></td>
                <td> - </td>
                <td><a href='colorname.php?firstn=10'>First</a> / <a href='colorname.php?secondn=10'>Second</a></td>
            </tr>
        </table>
</tr>
</td>
<tr>
    <td class="contentcontent">
        <center>Is there a colour you want which is not listed above? If so, please file a support ticket, thanks.
        </center>
    </td>
</tr>
<?php
include 'footer.php';
?>