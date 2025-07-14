<?php
include "header.php";
$mypet = new Pet($user_class->id);
if ($mypet->maxhp == 0 || $mypet->loaned != 0) {
    diefun("You do not own a pet.");
}

if (isset($_POST['userid'])) {
    security($_POST['userid'], 'num');
    try {
        $them = new User($_POST['userid']);
        if (!($them->maxnerve > 0))
            diefun("This user does not exist.");
        $theirpet = new Pet($them->id);
        if ($theirpet->maxhp != 0)
            diefun("They already own a pet.");
        perform_query("UPDATE pets SET userid = ?, loaned = ? WHERE userid = ?", [$them->id, $user_class->id, $user_class->id]);
        Send_Event($them->id, formatName($user_class->id) . " lent you their pet!");
        diefun("You have loaned out your pet to " . formatName($them->id) . "!");
    } catch (Exception $e) {
        diefun("That user does not exist.");
    }
}
?>
<span style='font-size:16px;'>Who would you like to loan your pet to?</span>
<br />
<br />
<form method='post'>
    Enter their user id:
    <input type='text' name='userid' size='5' value='0' />
    <input type='submit' value='Loan My Pet' />
</form>
<?php
include "footer.php";
?>