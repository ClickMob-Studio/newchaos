<?php
include 'header.php';
?>

<div class='box_top'>Pet Profile</div>
<div class='box_middle'>
    <div class='pad'>
        <?php
        security($_GET['id'], 'num');
        $pet = new Pet($_GET['id']);
        if (!isset($pet->house)) {
            $db->query("SELECT name FROM pethouses WHERE id = ?");
            $db->execute([$pet->house]);
            $pet->house = $db->fetch_single();
        }

        $db->query("SELECT name, picture FROM petshop WHERE id = ?");
        $db->execute([$pet->petid]);

        $petinfo = $db->fetch_row(true);
        $info = array(
            "Pet:" => "<img src='$pet->avi' style='width:100px;height:100px;' />",
            "Pet Type:" => $petinfo['name'],
            "Pet's Name:" => $pet->formatName(),
            "Owner's Name:" => formatName($pet->userid),
            "Level (EXP):" => $pet->level . " (" . $pet->exp . ")",
            "Busts:" => $pet->busts,
            "HP:" => $pet->hp . "/" . $pet->maxhp,
            "Energy:" => $pet->energy . "/" . $pet->maxenergy,
            "Nerve:" => $pet->nerve . "/" . $pet->maxnerve,
            "Awake:" => $pet->awake . "/" . $pet->maxawake,
            "House:" => $pet->house,
            "Leashed?:" => ($pet->leash) ? "Yes" : "No",
            "Jail Time:" => ($pet->jail / 60) . " mins",
            "Hospital Time:" => ($pet->hospital / 60) . " mins",
            "Attacks Won:" => $pet->attacksWon,
            "Attacks Lost:" => $pet->attacksLost
        );
        print "<table id='newtables' style='width:100%;'>
<tr><th colspan='4'>" . $pet->formatName() . "'s Profile</th></tr>";
        $i = 0;
        foreach ($info as $th => $td)
            if ($i++ % 2 == 0)
                print "<tr><th>$th</th><td>$td</td>";
            else
                print "<th>$th</th><td>$td</td></tr>";
        print "<tr><th colspan='4'><a href='petattack.php?attack=$pet->userid'>Attack Pet!</a></th></tr>
</table>";
        include 'footer.php';
        ?>