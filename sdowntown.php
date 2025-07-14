<?php
include 'header.php';
if ($_GET['buy'] == "buysearches") {
    echo Message("Are you sure you want to buy 100 searches for 10 Apples? <br><a href='sdowntown.php?buy=buysearchesyes'>Continue</a><br />
<a href='rmstore.php'>No thanks!</a>
");
    include 'footer.php';
    die();
}
if ($_GET['buy'] == "buysearchesyes") {
    if ($user_class->turns > 9) {
        $newcredit = $user_class->turns - 10;
        $time = time();
        perform_query("INSERT INTO `spentcredits` (timestamp, spender, spent, amount)" . "VALUES (?, ?, '100 searches', '10')", [$time, $user_class->id]);
        perform_query("UPDATE `grpgusers` SET `searchdowntown` = searchdowntown + 100 WHERE `id` = ?", [$_SESSION['id']]);
        echo Message("You spent 10 Apples for 100 Searches.");
    } else {
        echo Message("You don't have enough apples. You can buy some at the upgrade store.");
    }
}
?>
<tr>
    <td class="contentspacer"></td>
</tr>
<tr>
    <div class="contenthead">Search Downtown</div>
</tr>
<tr>
    <div class="contentcontent">
        Welcome to Search Downtown, Here you can search downtown for free but you can also buy searches using Apples?
        <br>
        <br>
        Please select below what you require:
        <br>
        <br>
        Below you can search for free
        <br>
        <br>
        <a href="downtown.php"><b>Search</b></font></a>
        <br>
        <br>
        Ran out of searches? You can buy 100 more below for 10 Apples
        <br>
        <br>
        <a href="sdowntown.php?buy=buysearches"><b>Buy</b></a>
        <br>
        100 Searches for 10 Apples
        </td>
</tr>
</table>
<?php
include 'footer.php';
?>