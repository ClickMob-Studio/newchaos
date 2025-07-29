<?php
if (!isset($user_class->pet)) {
    header("location: petshop.php");
    diefun("You do not have a pet.");
}

if ($user_class->pet->onmarket != 0)
    diefun("Sorry, your pet is on the market.");

if (isset($_GET['pm'])) {
    $user_class->petMenu = ($user_class->petMenu == 'yes') ? 'no' : 'yes';
    perform_query("UPDATE grpgusers SET petMenu = ? WHERE id = ?", [$user_class->petMenu, $user_class->id]);
}

if (isset($_GET['spend']) && $_GET['spend'] == 'pnerve') {
    if ($user_class->pet->nervepercent == 100)
        echo Message("Your pet's nerve is already full.");
    elseif ($user_class->points < 10)
        echo Message("You need 10 points to refill your pet's nerve.");
    else {
        $refillamnt = (($user_class->pet->maxnerve - $user_class->pet->nerve) > 100) ? 100 : $user_class->pet->maxnerve - $user_class->pet->nerve;
        perform_query("UPDATE pets SET nerve = nerve + ? WHERE userid = ?", [$refillamnt, $user_class->id]);
        perform_query("UPDATE grpgusers SET points = points - 10 WHERE id = ?", [$user_class->id]);
        echo Message("You have refilled your pet's nerve for 10 points!");
        $user_class->pet = new Pet($user_class->id);
    }
}

if (isset($_GET['spend']) && $_GET['spend'] == 'penergy') {
    if ($user_class->pet->energypercent == 100)
        echo Message("Your pet's energy is already full.");
    elseif ($user_class->points < 8)
        echo Message("You need 8 points to refill your pet's energy.");
    else {
        perform_query("UPDATE pets SET energy = energy + ? WHERE userid = ?", [$user_class->pet->maxenergy, $user_class->id]);
        perform_query("UPDATE grpgusers SET points = points - 8 WHERE id = ?", [$user_class->id]);
        echo Message("You have refilled your pet's energy for 8 points!");
        $user_class->pet = new Pet($user_class->id);
    }
}

if (isset($_GET['spend']) && $_GET['spend'] == 'pawake') {
    $cost = ceil(100 - $user_class->pet->awakepercent);
    if ($user_class->pet->awakepercent == 100)
        echo Message("Your pet's awake is already full.");
    elseif ($user_class->points < $cost)
        echo Message("You need $cost points to refill your pet's awake.");
    else {
        perform_query("UPDATE pets SET awake = ? WHERE userid = ?", [$user_class->pet->maxawake, $user_class->id]);
        perform_query("UPDATE grpgusers SET points = points - ? WHERE id = ?", [$cost, $user_class->id]);
        echo Message("You have refilled your pet's awake for $cost points!");
        $user_class->pet = new Pet($user_class->id);
    }
}
echo "
<style>
.progress-barpets {
    background-color: #000000;
    width: 100px;
    padding:1px;
    height:10px;
    -moz-border-radius: 3px;
    -webkit-border-radius: 3px;
    border-radius: 3px;
}
.progress-barpets span {
    display: inline-block;
    height: 8px;
    width: 100px;
    -moz-border-radius: 3px;
    -webkit-border-radius: 3px;
    border-radius: 3px;
    -moz-box-shadow: 0 0 5px #000 inset, 0 0 0 #444444;
    -webkit-box-shadow: 0 0 5px #000 inset, 0 0 0 #444444;
    box-shadow: 0 0 5px #000 inset, 0 0 0 #444444;
    -webkit-transition: width .4s ease-in-out;
    -moz-transition: width .4s ease-in-out;
    -ms-transition: width .4s ease-in-out;
    -o-transition: width .4s ease-in-out;
    transition: width .4s ease-in-out;

at:left;
}
.blue span {
    background-color: #000000;
}
.stripes span {
    -webkit-background-size: 30px 30px;
    -moz-background-size: 30px 30px;
    background-size: 30px 30px;
    background-image: -webkit-gradient(linear, left top, right bottom,
        color-stop(.25, rgba(50,100,200,0.75)), color-stop(.25, transparent),
        color-stop(.5, transparent), color-stop(.5, rgba(50,100,200,0.75)),
        color-stop(.75, rgba(50,100,200,0.75)), color-stop(.75, transparent),
        to(transparent));
    background-image: -webkit-linear-gradient(135deg, rgba(50,100,200,0.75) 25%, transparent 25%,
        transparent 50%, rgba(50,100,200,0.75) 50%, rgba(50,100,200,0.75) 75%,
        transparent 75%, transparent);
    background-image: -moz-linear-gradient(135deg, rgba(50,100,200,0.75) 25%, transparent 25%,
        transparent 50%, rgba(50,100,200,0.75) 50%, rgba(50,100,200,0.75) 75%,
        transparent 75%, transparent);
    background-image: -ms-linear-gradient(135deg, rgba(50,100,200,0.75) 25%, transparent 25%,
        transparent 50%, rgba(50,100,200,0.75) 50%, rgba(50,100,200,0.75) 75%,
        transparent 75%, transparent);
    background-image: -o-linear-gradient(135deg, rgba(50,100,200,0.75) 25%, transparent 25%,
        transparent 50%, rgba(50,100,200,0.75) 50%, rgba(50,100,200,0.75) 75%,
        transparent 75%, transparent);
    background-image: linear-gradient(135deg, rgba(50,100,200,0.75) 25%, transparent 25%,
        transparent 50%, rgba(50,100,200,0.75) 50%, rgba(50,100,200,0.75) 75%,
        transparent 75%, transparent);
    -webkit-animation: animate-stripes 3s linear infinite;
    -moz-animation: animate-stripes 3s linear infinite;
}
@-webkit-keyframes animate-stripes {
    0% {background-position: 0 0;} 100% {background-position: 60px 0;}
}
@-moz-keyframes animate-stripes {
    0% {background-position: 0 0;} 100% {background-position: 60px 0;}
}
</style>
<table id='newtables' style='width:100%;table-layout:fixed;'>
    <tr class='linkstable'>
        <td><a href='mypets.php'>My Pet</a></td>
        <td><a href='petcrime.php'>Crimes</a></td>
        <td><a href='petgym.php'>Gym</a></td>
        <td><a href='petjail.php'>Pound</a></td>
        <td><a href='pethouse.php'>House</a></td>
        <td><a href='pethof.php'>HOF</a></td>
    </tr>
    <tr>
        <th colspan='6'>Pet Information <a href='?pm' style='color:red;'>[", ($user_class->petMenu == 'yes') ? 'Disable' : 'Enable', " Pet Menu]</a></th>
    </tr>
    <tr>
        <th>Pet Level:</th>
        <td>" . $user_class->pet->level . "</td>
        <td></td>
        <th>Pet House:</th>
        <td><a href='pethouse.php'>" . $user_class->pet->housename . "</a></td>
        <td></td>
    </tr>
    <tr>
        <th><a href='?spend=penergy' style='color:orange;'>Pet Energy:</a></th>
        <td><div class='progress-barpets blue stripes' style='height:20px;width:100px;'><span style='width: " . $user_class->pet->energypercent . "%;height:20px;'></span></div></td>
        <td>" . $user_class->pet->formattedenergy . "</td>
        <th><a href='?spend=pawake' style='color:orange;'>Pet Awake:</a></th>
        <td><div class='progress-barpets blue stripes' style='height:20px;width:100px;'><span style='width: " . $user_class->pet->awakepercent . "%;height:20px;'></span></div></td>
        <td>" . $user_class->pet->formattedawake . "</td>
    </tr>
    <tr>
        <th><a href='?spend=pnerve' style='color:orange;'>Pet Nerve:</a></th>
        <td><div class='progress-barpets blue stripes' style='height:20px;width:100px;'><span style='width: " . $user_class->pet->nervepercent . "%;height:20px;'></span></div></td>
        <td>" . $user_class->pet->formattednerve . "</td>
        <th>Pet EXP:</th>
        <td><div class='progress-barpets blue stripes' style='height:20px;width:100px;'><span style='width: " . $user_class->pet->exppercent . "%;height:20px;'></span></div></td>
        <td>" . $user_class->pet->formattedexp . "</td>
    </tr>
</table>
";
