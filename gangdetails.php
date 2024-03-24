<?php
include 'header.php';
?>

<div class='box_top'>Gang Details</div>
						<div class='box_middle'>
							<div class='pad'>
<?php
if ($user_class->gang != 0) {
    $gang_class = new Gang($user_class->gang);
    if (!empty($gang_class->banner))
        print"
    <center>
        <a href='viewgang.php?id=$gang_class->id' />
            <img src='$gang_class->banner' width='300' height='75' alt='Gang Banner' title='$gang_class->name' />
        </a>
    </center>
    ";
    ?>
    <table id="newtables" style="width:100%;table-layout:fixed;">
        <tr>
            <th colspan="4">Your Gang</td>
        </tr>
        <tr>
            <th>Gang:</th><td>[<?php echo $gang_class->tag; ?>] <?php echo $gang_class->name; ?></td>
            <th>Gang Level:</th><td><?php echo $gang_class->level; ?></td>
        </tr>
        <tr>
            <th>Gang Exp:</th><td><?php echo $gang_class->formattedexp; ?></td>
            <th>Members:</th><td><?php echo $gang_class->members; ?>&nbsp;/&nbsp;<?php echo $gang_class->capacity; ?></td>
        </tr>
        <tr>
            <th>Money:</th><td>$<?php echo prettynum($gang_class->moneyvault); ?></td>
            <th>Points:</th><td><?php echo prettynum($gang_class->pointsvault); ?></td>
        </tr>
        <tr>
            <th>Gang House:</th><td><?php echo $gang_class->housename; ?> [+<?php echo $gang_class->houseawake; ?>%]</td>
            <th>Respect:</th><td><?php echo number_format($gang_class->respect, 5); ?></td>
        </tr>
    </table>
    <?php
} else
    echo Message("You aren't in a gang.");
include("gangheaders.php");
include 'footer.php';
?>