<?php
include 'header.php';

$db->query("SELECT * FROM `operations`");
$db->execute();
$operations = $db->fetch_row();

$indexedOperations = array();
foreach ($operations as $operation) {
    $indexedOperations[$operation['id']] = $operation;
}

$db->query("SELECT * FROM `user_operations` WHERE `user_id` = ? AND (`is_complete` = 1 OR `is_skipped` = 1) ORDER BY `id` DESC LIMIT 1");
$db->execute(array($user_class->id));
$lastUserOperation = $db->fetch_row(true);

if ($lastUserOperation && isset($lastUserOperation['id'])) {
    $nextUserOperation = $lastUserOperation['id'] + 1;
} else {
    $nextUserOperation = 1;
}

if (isset($indexedOperations[$nextUserOperation])) {
    $currentOperation = $indexedOperations[$nextUserOperation];
} else {
    $currentOperation = null;
}
?>

<h1>Operations</h1>

<div class="row">
    <div class="col-md-12">
        <?php if ($currentOperation): ?>
            <div class="table-container">
                <table class="new_table" id="newtables" style="width:100%;">
                    <tr>
                        <th colspan="2">Operation #<?php echo $currentOperation['id'] ?></th>
                    </tr>

                    <!-- CRIMES -->
                    <tr>
                        <th>
                            Crimes
                        </th>
                        <td>
                            0/<?php echo number_format($currentOperation['crimes'], 0) ?>
                        </td>
                    </tr>

                    <!-- MUGS -->
                    <tr>
                        <th>
                            Mugs
                        </th>
                        <td>
                            0/<?php echo number_format($currentOperation['mugs'], 0) ?>
                        </td>
                    </tr>

                    <!-- BUSTS -->
                    <tr>
                        <th>
                            Busts
                        </th>
                        <td>
                            0/<?php echo number_format($currentOperation['busts'], 0) ?>
                        </td>
                    </tr>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-danger">
                <p>You do not have anymore operations to complete.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
require "footer.php";
?>