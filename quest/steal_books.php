<?php
if ($user_class->jail > 0 || $user_class->hospital > 0) {
    echo "
            <div class='alert alert-danger'>
                <strong>Fail!</strong> You are currently in jail or hospital and cannot complete this quest.
            </div>
        ";
    exit;
}
?>

<h1>Steal The Books</h1><hr />
<p>It's the dark of night and you've snuck into Salvatore's accountants office whilst it's closed. Be quick but be careful, you don't want to raise an alarm.</p>

<?php
if (isset($_GET['search'])) {
    $search = $_GET['search'];

    if ($search === 'desk') {
        // Jail
        $jailTime = mt_rand(60, 300);
        $db->query("UPDATE grpgusers SET jail = ? WHERE id = ?");
        $db->execute(array($jailTime, $user_class->id));

        echo "
            <div class='alert alert-danger'>
                <strong>Fail!</strong> You search the desk and find nothing, but as you're about to leave you hear a noise behind you. You turn around to see a police officer standing there. You have been arrested.
            </div>";
        exit;
    } elseif ($search === 'safe') {
        // Jail
        $jailTime = mt_rand(60, 300);
        $db->query("UPDATE grpgusers SET jail = ? WHERE id = ?");
        $db->execute(array($jailTime, $user_class->id));

        echo "
            <div class='alert alert-danger'>
                <strong>Fail!</strong> You search the safe and find nothing, but as you're about to leave you hear a noise behind you. You turn around to see a police officer standing there. You have been arrested.
            </div>";
        exit;
    } elseif ($search === 'window') {
        // Fail
        echo "
            <div class='alert alert-info'>
                <strong>Fail!</strong> You search the window ledge and find nothing.
            </div>";
    } elseif ($search === 'drawers') {
        // Success
        echo "
            <div class='alert alert-success'>
                <strong>Success!</strong> You search the drawers and find the books you were looking for.
            </div>";

        updateQuestSeasonMissionUserProgress($questSeasonMissionUser, 'steal_books', 1);

        header('Location: quest.php');
        exit;

    } elseif ($search === 'carpet') {
        // Fail
        echo "
            <div class='alert alert-info'>
                <strong>Fail!</strong> You search under the capret and find nothing.
            </div>";
    }
}
?>


<table id="newtables" style="width:100%;">
    <tr>
        <th>Decide where your going to search:</th>
    </tr>
    <tr>
        <td>
            <a href="quest.php?mode=steal_books&search=desk" >Search the Desk</a>
        </td>
    </tr>
    <tr>
        <td>
            <a href="quest.?mode=steal_books&search=safe" >Search the Safe</a>
        </td>
    </tr>
    <tr>
        <td>
            <a href="quest.php?mode=steal_books&search=window" >Search the Window Ledge</a>
        </td>
    </tr>
    <tr>
        <td>
            <a href="quest.php?mode=steal_books&search=drawers" >Search the Drawers</a>
        </td>
    </tr>
    <tr>
        <td>
            <a href="quest.php?mode=steal_books&search=carpet" >Search under the carpet</a>
        </td>
    </tr>
</table>


<?php
exit;
