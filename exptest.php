<?php
include 'header.php';
?>
<table id="newtables" style="width:100%;">
    <tr>
        <th colspan='6'>Exp Guide</th>
    </tr>
    <tr>
        <th width="13%"><b>Level</b></td>
        <th width="20%"><b>Exp</b></td>
        <th width="13%"><b>Level</b></td>
        <th width="20%"><b>Exp</b></td>
        <th width="13%"><b>Level</b></td>
        <th width="20%"><b>Exp</b></td>
    </tr>
<?php
for($x = 1; $x<=333; $x++) {
    echo "
    <tr>
        <th width='13%'>".($x)."</td>
        <td width='20%'>".prettynum(experience2($x))."</td>
        <th width='13%'>".($x + 333)."</td>
        <td width='20%'>".prettynum(experience2($x + 333))."</td>
        <th width='13%'>".($x + 666)."</td>
        <td width='20%'>".prettynum(experience2($x + 666))."</td>
    </tr>
    ";
}
?>
    </tr>
</table>
<?php
include("footer.php");
?>