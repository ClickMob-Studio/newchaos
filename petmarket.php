<?php
include 'header.php';
$showpet = 1;
include 'includepet.php';
$db->query("SELECT * FROM pets WHERE userid = ?");
$db->execute(array(
    $user_class->id
));
$petinfo = $db->fetch_row(true);
if(isset($_GET['conf']) && $_GET['conf'] == $_SESSION['security']){
    if(isset($_GET['b'])){
        security($_GET['b']);
        $db->query("SELECT * FROM petmarket WHERE petid = ?");
        $db->execute(array(
           $_GET['id'] 
        ));
        $petmarketinfo = $db->fetch_row(true);
        if(empty($petmarketinfo))
            diefun("Pet Market entry not found.");
        if($petmarketinfo['userid'] == $user_class->id)
            diefun("You are not allowed to buy your own pet.");
        if($petmarketinfo['currency'] == 'points' && $petmarketinfo['cost'] > $user_class->points)
            diefun("You do not have enough points to buy this pet.");
        if($petmarketinfo['currency'] == 'money' && $petmarketinfo['cost'] > $user_class->money)
            diefun("You do not have enough money to buy this pet.");
        if(!empty($petinfo))
            diefun("Looks like you already own a pet.");
        $db->query("UPDATE pets SET userid = ?, onmarket = 0 WHERE id = ?");
        $db->execute(array(
            $user_class->id,
            $petmarketinfo['petid']
        ));
        if($petmarketinfo['currency'] == 'money'){
            $user_class->money -= $petmarketinfo['cost'];
            $db->query("UPDATE grpgusers SET money = ? WHERE id = ?");
            $db->execute(array(
                $user_class->money,
                $user_class->id
            ));
            $db->query("UPDATE grpgusers SET bank = bank + ? WHERE id = ?");
            $db->execute(array(
                $petmarketinfo['cost'],
                $petmarketinfo['userid']
            ));
            Send_Event($petmarketinfo['userid'], formatName($user_class->id) . " just purchased your pet for " . prettynum($petmarketinfo['cost'], 1) . ".");
        }
        if($petmarketinfo['currency'] == 'points'){
            $user_class->points -= $petmarketinfo['cost'];
            $db->query("UPDATE grpgusers SET points = ? WHERE id = ?");
            $db->execute(array(
                $user_class->points,
                $user_class->id
            ));
            $db->query("UPDATE grpgusers SET points = points + ? WHERE id = ?");
            $db->execute(array(
                $petmarketinfo['cost'],
                $petmarketinfo['userid']
            ));
            Send_Event($petmarketinfo['userid'], formatName($user_class->id) . " just purchased your pet for " . prettynum($petmarketinfo['cost']) . " points.");
        }
       $db->query("DELETE FROM petmarket WHERE petid = ?"); 
       $db->execute(array(
          $petmarketinfo['petid'] 
       ));
       unset($petinfo);
    }
    if(isset($_GET['r'])){
        $db->query("SELECT * FROM petmarket WHERE userid = ?");
        $db->execute(array(
           $user_class->id
        ));
        $petmarketinfo = $db->fetch_row(true);
        if(empty($petmarketinfo))
            diefun("Pet Market entry not found.");
        if($petmarketinfo['userid'] != $user_class->id)
            diefun("You are not allowed to remove a pet that you do not own.");
       $db->query("DELETE FROM petmarket WHERE petid = ?"); 
       $db->execute(array(
          $petmarketinfo['petid'] 
       ));
       $db->query("UPDATE pets SET onmarket = 0 WHERE id = ?");
       $db->execute(array(
          $petmarketinfo['petid'] 
       ));
    }
    header("Location: mypets.php");
}
if(isset($_POST['currency'])){
    if(empty($petinfo) || $petinfo['loaned'] != 0 || $petinfo['onmarket'] != 0)
        diefun("Sorry, you do not own your own pet.");
    if(!in_array($_POST['currency'], array('money', 'points')))
        diefun("Error, invalid currency.");
    security($_POST['amnt']);
    $db->query("INSERT INTO petmarket VALUES(?, ?, ?, ?)");
    $db->execute(array(
        $petinfo['id'],
        $user_class->id,
        $_POST['amnt'],
        $_POST['currency']
    ));
    $db->query("UPDATE pets SET onmarket = 1 WHERE id = ?");
    $db->execute(array(
        $petinfo['id']
    ));
}
if(!empty($petinfo) && $petinfo['loaned'] == 0 && $petinfo['onmarket'] == 0){
    print"
        <div style='background: rgba(0,0,0,.25);width:50%;margin:0 auto;text-align:center;'>
            <form method='post'>
                <table id='newtables' style='width:100%;'>
                    <tr>
                        <th colspan='2'>Would you like to sell your pet?</th>
                    </tr>
                    <tr>
                        <th>Pick a Currency:</th>
                        <td>
                            <select name='currency'>
                                <option value='money'>Money</option>
                                <option value='points'>Points</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>Amount to sell pet for?</th>
                        <td><input type='text' value='0' name='amnt' /></td>
                    </tr>
                    <tr>
                        <td colspan='2'><input type='submit' value='Add Pet to the Market' /></td>
                    </tr>
                </table>
            </form>
        </div>";
}
$db->query("SELECT * FROM petmarket");
$db->execute();
if(!$db->num_rows())
    print"<table id='newtables'><tr><th>There are no pets currently on the Pet Market.</th></tr></table>";
else{
    print"
        <table id='newtables' style='width:100%;'>
            <tr>
                <th colspan='8'>Pet Market</th>
            </tr>
            <tr>
                <th>Pet Name</th>
                <th>Owner Name</th>
                <th>Price</th>
                <th>Strength</th>
                <th>Defense</th>
                <th>Speed</th>
                <th>House</th>
                <th>Actions</th>
            </tr>";
    $rows = $db->fetch_row();
    $_SESSION['security'] = rand(1000000000,2000000000);
    foreach($rows as $row){
        $pet_info = new Pet($row['userid']);
        $actions = ($pet_info->userid == $user_class->id) ? "<a href='?r&conf={$_SESSION['security']}'><button>Remove</button></a>" : "<a href='?b&id=$pet_info->id&conf={$_SESSION['security']}'><button>Buy</button></a>";
        echo"
            <tr>
                <td>".$pet_info->formatName()."</td>
                <td>".formatName($pet_info->userid)."</td>
                <td>",($row['currency'] == 'money') ? prettynum($row['cost'],1) : prettynum($row['cost']) . " Points","</td>
                <td>".prettynum($pet_info->strength)."</td>
                <td>".prettynum($pet_info->defense)."</td>
                <td>".prettynum($pet_info->speed)."</td>
                <td>$pet_info->housename<br />[".prettynum($pet_info->houseawake)." awake]</td>
                <td>$actions</td>
            </tr>";
    }
    print"</table>";
}