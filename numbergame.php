<?php
include "header.php";

?>

<div class='box_top'>Numbers Game</div>
<div class='box_middle'>
    <div class='pad'>
        <?php
        $stmt = $db->prepare("SELECT * FROM `grpgusers` WHERE `id` = :id LIMIT 1");
        $stmt->bindParam(':id', $_SESSION['id']);
        $stmt->execute();
        $ir = $stmt->fetch(PDO::FETCH_ASSOC);

        $_GET['ID'] = abs((int)$_GET['ID']);
        $userid = $_SESSION['id'];
        if ($ir['jailtime'] or $ir['hospital']) {
            die("<br />This page is unavailable while in hospital or jail.<br /><br /><hr width=80%> <a href='index.php'>Home</a><hr width=80%>");
        }

        $stmt = $db->prepare("SELECT * FROM numbergame WHERE userid=:userid");
        $stmt->bindParam(':userid', $user_class->id);
        $stmt->execute();
        $no2 = $stmt->rowCount();

        $_GET['take'] = abs((int)$_GET['take']);
        $getid = $_GET['take'];
        if (!$getid) {
            print "<b>Mini Competition! Current Prize: 1,000 Points<br />Guess a number from 1 to 25<br />You will receive an event if you win.</b><br /><br /><table width='30%' cellspacing=1 class='myTable'><tr><td class='contenthead' align='center'>Number</td><td class='contenthead' align='center'>Player</td></tr>";
            $stmt = $db->query("SELECT * FROM numbergame ORDER BY number ASC");
            while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $stmt_user = $db->prepare("SELECT * FROM grpgusers WHERE id=:id");
                $stmt_user->bindParam(':id', $r['userid']);
                $stmt_user->execute();
                $u = $stmt_user->fetch(PDO::FETCH_ASSOC);
                if ($r['userid'] == 0) {
                    if ($no2 > 0) {
                        $u['username'] = "-";
                    } else {
                        $u['username'] = "[<a href='numbergame.php?take={$r['number']}'>select</a>]";
                    }
                } else {
                    $u['username'] = "<a href='profiles.php?id={$u['id']}'>{$u['username']}</a>";
                }
                print "\n<tr align=center><td class=contentcontent>{$r['number']}</td><td class=contentcontent>{$u['username']}</td></tr>";
                $current_row = 1 - $current_row;
            }
        } else {
            $stmt = $db->prepare("SELECT * FROM numbergame WHERE number=:number");
            $stmt->bindParam(':number', $getid);
            $stmt->execute();
            $no = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($no['userid'] != 0) {
                die("<br />This number is already taken.<br /><br /><hr width=80%> <a href='numbergame.php'>Number Game</a><hr width=80%>");
            }
            if ($no2 > 0) {
                die("<br />You have already chosen a number.<br /><br /><hr width=80%> <a href='numbergame.php'>Number Game</a><hr width=80%>");
            }
            $stmt = $db->prepare("UPDATE numbergame SET userid=:userid WHERE number=:number");
            $stmt->bindParam(':userid', $user_class->id);
            $stmt->bindParam(':number', $getid);
            $stmt->execute();
            print "<br />You have taken the number $getid<br /><br /><hr width=80%> <a href='numbergame.php'>Number Game</a><hr width=80%>";
        }

?>
</table>
<?php
include 'footer.php';
?>
