<?php
include 'header.php';
?>

<div class='box_top'>Pet Shop</div>
<div class='box_middle'>
    <div class='pad'>
        <?php
        security($_GET['buy']);
        $check = mysql_fetch_row(mysql_query("SELECT * FROM pets WHERE loaned = $user_class->id"));
        if ($check)
            diefun("You cannot buy a pet, you have one loaned out. Would you like to <a href='retpet.php' style='color:purple;'>[retrieve]</a> your pet?");
        if (!empty($_GET['buy'])) {
            if (mysql_num_rows(mysql_query("SELECT * FROM pets WHERE userid = $user_class->id")))
                diefun("You already own a pet");
            $q = mysql_query("SELECT * FROM petshop WHERE id = {$_GET['buy']}");
            if (!mysql_num_rows($q))
                diefun("That pet doesn't exist");
            $row = mysql_fetch_array($q);
            if (!$row['cost'])
                diefun("That pet can't be purchased using this method");
            if ($row['cost'] > $user_class->money)
                diefun("You don't have enough cash");

            perform_query("UPDATE grpgusers SET money = money - ? WHERE id = ?", [$row['cost'], $user_class->id]);
            Give_Pet($_GET['buy'], $user_class->id, $row['picture']);
            echo Message("You've purchased a {$row['name']} for " . prettynum($row['cost'], 1));
        }
        $q = mysql_query("SELECT id, name, cost, picture FROM petshop ORDER BY cost ASC, id ASC");
        print "
<table id='newtables' style='width:90%;'>
<tr>
    <th colspan='4'>Pet Shop</th>
</tr>
<tr>";
        $cnt = 0;
        while ($row = mysql_fetch_array($q)) {
            print "
    <td>
        <img src='{$row['picture']}' width='100' height='100' /><br />
        {$row['name']}<br />
        " . prettynum($row['cost'], 1) . "<br />
        <br /><a href='petshop.php?buy={$row['id']}' id='botlink'>Purchase</a><br ><br />
    </td>";
            if (!(++$cnt % 4))
                echo "</tr><tr>";
        }
        print "</tr>
</table>
</td></tr>";
        include 'footer.php';
