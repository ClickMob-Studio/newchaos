<?php
require "header.php";
if ($user_class->admin < 1) {
    die();
}

$db->query("SELECT * FROM bosses");
$db->execute();
$rows = $db->fetch_row();

$id = null;
if (isset($_GET['id']) && (int)$_GET['id']) {
    security($_GET['id']);

    $id = (int)$_GET['id'];

    $db->query("SELECT * FROM bosses WHERE id = " . $id . " LIMIT 1");
    $db->execute();
    $boss = $db->fetch_row(true);

    if (!$boss) {
        die('Something went wrong.');
    }
}


if (isset($boss) && isset($_POST['name']) && isset($_POST['tokencost']) && isset($_POST['level'])) {
    if (empty($_POST['name'])) {
        die('please ensure a name is provided.');
    }

    if (empty($_POST['tokencost']) || (int)$_GET['tokencost'] < 0) {
        die('please ensure a token cost is provided.');
    }

    if (empty($_POST['level']) || (int)$_GET['level'] < 0) {
        die('please ensure a level is provided.');
    }

    security($_POST['tokencost']);
    security($_POST['level']);

    $name = $_GET['name'];
    $tokencost = (int)$_GET['tokencost'];
    $level = (int)$_GET['level'];

    $db->query("UPDATE bosses SET name = ?, tokencost = ?, level = ? WHERE id = " . $boss['id']);
    $db->execute(array($name, $tokencost, $level));

    diefun('You have successfully edited ' . $boss['name'] . '. <a href="admin_edit_boss.php">Go back</a>');
}



?>

<div class="container">
    <?php if ($id && isset($boss)): ?>

        <h1>Edit <?php echo $boss['name'] ?></h1>

        <form method="POST" action="admin_edit_boss.php?id=<?php echo $boss['id'] ?>">
            <p>Name:</p>
            <input type="text" class="form-control" name="name" value="<?php echo $boss['name'] ?>" /><br />

            <p>Level:</p>
            <input type="text" class="form-control" name="level" value="<?php echo $boss['level'] ?>" /><br />

            <p>Token Cost:</p>
            <input type="text" class="form-control" name="tokencost" value="<?php echo $boss['tokencost'] ?>" />

            <input type="submit" class="btn btn-primary" value="SAVE" />
        </form>
    <?php else: ?>
        <h1>Raids</h1>

        <table class="table" style="color:white">
            <thead>
            <tr>
                <th>Name</th>
                <th>Level</th>
                <th>Cost</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?php echo $row['name'] ?></td>
                    <td><?php echo $row['level'] ?></td>
                    <td><?php echo $row['tokencost'] ?></td>
                    <td><a href="admin_edit_boss.php?id=<?php echo $row['id'] ?>" class="dcSecondaryButton">Edit</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
