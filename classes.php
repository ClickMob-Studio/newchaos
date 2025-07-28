<?php

include_once "includes/functions.php";

if (!isset($_SESSION['id'])) {
    error_reporting(0);
}

class User_Stats
{
    function User_Stats($wutever)
    {
        global $db;

        $this->playersloggedin = 0;
        $this->playersonlineinlastday = 0;
        $this->playerstotal = 0;
        $db->query("SELECT lastactive FROM grpgusers");
        $db->execute();
        $rows = $db->fetch_row();
        foreach ($rows as $row) {
            $secondsago = time() - $row['lastactive'];
            if ($secondsago <= 3600)
                $this->playersloggedin++;
            if ($secondsago <= 86400)
                $this->playersonlineinlastday++;
            $this->playerstotal++;
        }
    }
}
class Gang
{
    public int $id;
    public string $formattedname;
    public string $nobanner;
    public array $memberids = [];
    public int $members = 0;
    public string $Color1;
    public string $Color2;
    public string $Color3;
    public string $crimeend;
    public int|string $house;
    public string $housename = "None";
    public int $houseawake = 0;
    public int $housecost;
    public float $tax;
    public int $maxexp;
    public int $exppercent;
    public string $formattedexp;
    public int $level;
    public int $exp;

    public function __construct(int $id)
    {
        global $db;

        // Fetch gang details
        $db->query("SELECT * FROM gangs WHERE id = ?");
        $db->execute([$id]);
        $row = $db->fetch_row(true);

        if (empty($row)) {
            $this->id = -1;
            $this->formattedname = '';
            $this->nobanner = '';
            $this->memberids = [];
            $this->members = [];
            $this->color1 = '';
            $this->color2 = '';
            $this->color3 = '';
            $this->crimeend = null;
            $this->exppercent = 0;
            $this->formattedexp = '';
            $this->level = -1;
            $this->exp = -1;

            return;
        }

        // Populate properties from the row
        foreach ($row as $title => $value) {
            $this->$title = $value;
        }

        $this->id = $id;

        // Fetch gang members
        $db->query("SELECT id FROM grpgusers WHERE gang = ?");
        $db->execute([$id]);
        $members = $db->fetch_row(); // multiple rows
        $this->members = count($members);
        $this->memberids = $members;

        // Formatted name with or without banner
        if (!empty($row['banner'])) {
            $this->formattedname = "<a href='viewgang.php?id={$row['id']}'><img src='{$row['banner']}' height='75' width='250' /></a>";
        } else {
            $this->formattedname = "<a href='viewgang.php?id={$row['id']}'>{$row['name']}</a>";
        }

        $this->nobanner = "<a href='viewgang.php?id={$row['id']}'>{$row['name']}</a>";

        // House info
        $this->Color1 = $row["tColor1"];
        $this->Color2 = $row["tColor2"];
        $this->Color3 = $row["tColor3"];
        $this->crimeend = $row['ending'];
        $this->house = $row['ghouse'];

        $db->query("SELECT * FROM ghouses WHERE id = ?");
        $db->execute([$this->house]);
        $ganghouse = $db->fetch_row(true);

        if (!empty($ganghouse)) {
            $this->housename = $ganghouse['name'] ?? "None";
            $this->houseawake = $ganghouse['awake'] ?? 0;
            $this->housecost = $ganghouse['cost'] ?? 0;
        }

        $this->tax = $row['tax'];

        // Handle experience and leveling
        $this->maxexp = GangExperience($this->level + 1);
        $this->exppercent = ($this->exp == 0) ? 0 : floor(($this->exp / $this->maxexp) * 100);
        $this->formattedexp = prettynum($this->exp) . " / " . prettynum($this->maxexp) . " [{$this->exppercent}%]";

        // Level up if needed
        if ($this->exp >= $this->maxexp && $this->exp > 0) {
            $this->level += 1;
            $this->exp -= $this->maxexp;

            $db->query("UPDATE gangs SET level = ?, exp = ? WHERE id = ?");
            $db->execute([$this->level, $this->exp, $this->id]);

            Gang_Event($this->id, "Your gang has just gained a level!");

            foreach ($members as $member) {
                Send_Event($member['id'], "Your gang has just gained a level!");
            }
        }
    }
}

class OwnedBusiness
{
    public $ownership_id;
    public $user_id;
    public $name;
    public $rating;
    public $employees;
    public $vault;
    public $earnedtoday;
    public $business_id;

    function OwnedBusiness($ownership_id)
    {
        global $db;
        $db->query("SELECT * FROM OwnedBusinesses WHERE ownership_id = ?");
        $db->execute(array($ownership_id));
        $row = $db->fetch_row(true);
        if (empty($row))
            return;

        // Assign values to the properties
        $this->ownership_id = $row['ownership_id'];
        $this->user_id = $row['user_id'];
        $this->name = $row['name'];
        $this->rating = $row['rating'];
        $this->employees = $row['employees'];
        $this->business_id = $row['business_id'];
        $this->vault = $row['vault'];
        $this->earnedtoday = $row['earnedtoday'];
    }

    public function deposit($amount)
    {
        global $db, $user_class;

        $newUserMoney = $user_class->money - $amount;
        $db->query("UPDATE grpgusers SET money = ? WHERE id = ?");
        $db->execute([$newUserMoney, $_SESSION['id']]);

        // Update business vault
        $newVaultValue = $this->vault + $amount;

        $db->query("UPDATE OwnedBusinesses SET vault = ? WHERE ownership_id = ?");
        $db->execute([$newVaultValue, $this->ownership_id]);
    }

    public function withdraw($amount)
    {
        global $db, $user_class;

        $newUserMoney = $user_class->money + $amount;
        $db->query("UPDATE grpgusers SET money = ? WHERE id = ?");
        $db->execute([$newUserMoney, $_SESSION['id']]);


        $newVaultValue = $this->vault - $amount;
        $db->query("UPDATE OwnedBusinesses SET vault = ? WHERE ownership_id = ?");
        $db->execute([$newVaultValue, $this->ownership_id]);
    }
}


class User
{
    function __construct($id)
    {
        global $db;

        $db->query("SELECT 
                grpg.*,
                grpg.tag AS ptag,
                g.ghouse,
                g.name AS gangname,
                g.leader,
                g.tag,
                g.description,
                ci.name AS cityname,
                h.name AS housename,
                h.awake AS houseawake,
                co.name AS countryname,
                gh.awake AS gangawake,
                r.title AS ranktitle,
                r.color AS rankcolor,
                b.days AS bandays,
                g.tColor1,
                g.tColor2,
                g.tColor3,
                g.formattedTag,
                h.id AS houseid
            FROM grpgusers grpg
            LEFT JOIN gangs g ON g.id = grpg.gang
            LEFT JOIN cities ci ON ci.id = grpg.city
            LEFT JOIN houses h ON h.id = grpg.house
            LEFT JOIN countries co ON co.id = grpg.country
            LEFT JOIN ranks r ON r.id = grpg.grank
            LEFT JOIN ghouses gh ON gh.id = g.ghouse
            LEFT JOIN bans b ON b.id = grpg.id
            WHERE grpg.id = ?");
        $db->execute([$id]);
        $worked = $db->fetch_row(true);

        if (empty($worked)) {
            throw new Exception("User with id({$id}) not found");
        }

        foreach ($worked as $title => $value) {
            $this->$title = $value;
        }

        $skill_ids = $worked['skill_ids'];
        if (!empty($this->skill_ids)) {
            $this->skills = array_map('intval', explode(',', $skill_ids));
        }

        $db->query("SELECT * FROM pets WHERE userid = ? AND leash = 1");
        $db->execute([$id]);
        if ($db->num_rows()) {
            $pet = $db->fetch_row(true);
        } else {
            $pet['str'] = 0;
            $pet['def'] = 0;
            $pet['spe'] = 0;
        }
        $db->query("SELECT days FROM bans WHERE id = ?");
        $db->execute([$id]);
        if ($db->num_rows()) {
            $db->query("SELECT * FROM bans WHERE id = ? AND type = ?");
            $db->execute([$id, 'perm']);
            $permban = $db->num_rows();
            $workedban = $db->fetch_row(true);
            $db->execute([$id, 'freeze']);
            $freezeban = $db->num_rows();
            $workedban2 = $db->fetch_row(true);
            $db->execute([$id, 'mail']);
            $mailban = $db->num_rows();
            $workedban3 = $db->fetch_row(true);
            $db->execute([$id, 'forum']);
            $forumban = $db->num_rows();
            $workedban4 = $db->fetch_row(true);
        }
        $db->query("SELECT i.*, c.image overrideimage, c.name overridename FROM items i LEFT JOIN customitems c ON i.id = c.itemid AND c.userid = ? WHERE id = ?");
        if ($worked['eqweapon']) {
            $db->execute([
                $id,
                $worked['eqweapon']
            ]);
            $worked6 = $db->fetch_row(true);
            $this->eqweapon = $worked6['id'];
            $this->weaponoffense = $worked6['offense'];
            $this->weaponname = (!empty($worked6['overridename'])) ? $worked6['overridename'] : $worked6['itemname'];
            $this->weaponimg = (!empty($worked6['overrideimage'])) ? $worked6['overrideimage'] : $worked6['image'];
            $this->bonuses[] = $worked6['bonuses'];
        } else {
            $this->eqweapon = $this->weaponoffense = $this->weaponimg = 0;
            $this->weaponname = 'fists';
        }

        if ($worked['eqarmor']) {
            $db->execute(array(
                $id,
                $worked['eqarmor']
            ));
            $worked6 = $db->fetch_row(true);
            $this->eqarmor = $worked6['id'];
            $this->armordefense = $worked6['defense'];
            $this->armorname = (!empty($worked6['overridename'])) ? $worked6['overridename'] : $worked6['itemname'];
            $this->armorimg = (!empty($worked6['overrideimage'])) ? $worked6['overrideimage'] : $worked6['image'];
            $this->bonuses[] = $worked6['bonuses'];
        } else {
            $this->eqarmor = $this->armordefense = $this->armorname = $this->armorimg = 0;
        }
        if (in_array($this->gang, array(111, 112))) {
            $this->city = 14;
            $this->cityname = "Server War";
        }
        $this->mycityname = $this->cityname;
        // First check if the user has nightvision enabled
        if ($this->nightvision > 0) {
            // The user has nightvision, so they should see the true city name
            // You can handle this case as needed, possibly you don't need to do anything
        } else if ($this->eqarmor == 43) {
            // The user does not have nightvision and has the item equipped that hides the city
            $this->cityname = "Unknown";
        }
        if ($worked['eqshoes']) {
            $db->execute(array(
                $id,
                $worked['eqshoes']
            ));
            $worked6 = $db->fetch_row(true);
            $this->eqshoes = $worked6['id'];
            $this->shoesspeed = $worked6['speed'];
            $this->shoesname = (!empty($worked6['overridename'])) ? $worked6['overridename'] : $worked6['itemname'];
            $this->shoesimg = (!empty($worked6['overrideimage'])) ? $worked6['overrideimage'] : $worked6['image'];
            $this->bonuses[] = $worked6['bonuses'];
        } else {
            $this->shoesimg = $this->shoesname = $this->shoesspeed = $this->eqshoes = 0;
        }

        if ($worked['eqgloves']) {
            $db->execute(array(
                $id,
                $worked['eqgloves']
            ));
            $worked6 = $db->fetch_row(true);
            $this->eqgloves = $worked6['id'];
            $this->glovesagility = $worked6['agility'];
            $this->glovesname = (!empty($worked6['overridename'])) ? $worked6['overridename'] : $worked6['itemname'];
            $this->glovesimg = (!empty($worked6['overrideimage'])) ? $worked6['overrideimage'] : $worked6['image'];
            $this->bonuses[] = $worked6['bonuses'];
        } else {
            $this->glovesimg = $this->glovesname = $this->glovesagility = $this->eqgloves = 0;
        }

        $db->query("SELECT SUM(paymentamount) FROM ipn WHERE user_id = ? AND date > unix_timestamp() - 2592000");
        $db->execute(array(
            $id
        ));
        $donations = $db->fetch_single();

        $this->shared_bank = $worked["shared_bank"];

        $this->donations = $donations;
        $this->ffban = $worked["ffban"];
        $this->druggie = explode("|", $this->drugs);
        $this->drugstr = (isset($this->druggie[2]) && $this->druggie[2] > time() - 900) ? 1.25 : 1;
        $this->drugdef = (isset($this->druggie[1]) && $this->druggie[1] > time() - 900) ? 1.25 : 1;
        $this->drugspe = (isset($this->druggie[0]) && $this->druggie[0] > time() - 900) ? 1.25 : 1;
        $this->ktime = $worked['ktime'];
        $this->qtime = $worked['qtime'];
        $this->bankboost = $worked['bankboost'];
        $this->drugall = (isset($this->druggie[3]) && $this->druggie[3] > time() - 900) ? 1.50 : 1;

        $this->moddedstrength = round((($pet['str'] + $worked['strength']) * ($this->weaponoffense * .01 + 1)) * $this->drugstr);
        $this->moddeddefense = round((($pet['def'] + $worked['defense']) * ($this->armordefense * .01 + 1)) * ($this->drugdef * $this->drugall));
        $this->moddedspeed = round((($pet['spe'] + $worked['speed']) * ($this->shoesspeed * .01 + 1)) * ($this->drugspe * $this->drugall));
        $this->moddedagility = round((($worked['agility']) * ($this->glovesagility * .01 + 1)) * ($this->drugall));

        $this->moddedtotalattrib = $this->moddedspeed + $this->moddedstrength + $this->moddeddefense + $this->moddedagility;
        $this->maxexp = experience($this->level + 1);
        $this->exppercent = ($this->exp == 0) ? 0 : floor(($this->exp / $this->maxexp) * 100);
        $this->formattedexp = prettynum($this->exp) . " / " . prettynum($this->maxexp) . " [" . $this->exppercent . "%]";
        $this->money = ($this->money < 1) ? 0 : $this->money;
        if ($this->money >= 1000000000) { // Check if the number is at least a billion
            $this->shortMoney = round($this->money / 1000000000, 2) . 'B'; // Convert to billions, round to 2 decimal places, and append 'B'
        } elseif ($this->money >= 1000000) { // Check if the number is at least a million
            $this->shortMoney = round($this->money / 1000000, 2) . 'M'; // Convert to millions, round to 2 decimal places, and append 'M'
        } elseif ($this->money >= 1000) { // Check if the number is at least a thousand
            $this->shortMoney = round($this->money / 1000, 1) . 'k'; // Convert to thousands, round to 1 decimal place, and append 'k'
        }
        if ($this->bank >= 1000000000) { // Check if the number is at least a billion
            $this->shortBank = round($this->bank / 1000000000, 2) . 'B'; // Convert to billions, round to 2 decimal places, and append 'B'
        } elseif ($this->bank >= 1000000) { // Check if the number is at least a million
            $this->shortBank = round($this->bank / 1000000, 2) . 'M'; // Convert to millions, round to 2 decimal places, and append 'M'
        } elseif ($this->bank >= 1000) { // Check if the number is at least a thousand
            $this->shortBank = round($this->bank / 1000, 1) . 'k'; // Convert to thousands, round to 1 decimal place, and append 'k'
        }
        if ($this->points >= 1000000000) { // Check if the number is at least a billion
            $this->shortPoints = round($this->points / 1000000000, 2) . 'B'; // Convert to billions, round to 2 decimal places, and append 'B'
        } elseif ($this->points >= 1000000) { // Check if the number is at least a million
            $this->shortPoints = round($this->points / 1000000, 2) . 'M'; // Convert to millions, round to 2 decimal places, and append 'M'
        } elseif ($this->points >= 1000) { // Check if the number is at least a thousand
            $this->shortPoints = round($this->points / 1000, 1) . 'k'; // Convert to thousands, round to 1 decimal place, and append 'k'
        }
        if ($this->credits >= 1000000000) { // Check if the number is at least a billion
            $this->shortCredits = round($this->credits / 1000000000, 2) . 'B'; // Convert to billions, round to 2 decimal places, and append 'B'
        } elseif ($this->credits >= 1000000) { // Check if the number is at least a million
            $this->shortCredits = round($this->credits / 1000000, 2) . 'M'; // Convert to millions, round to 2 decimal places, and append 'M'
        } elseif ($this->credits >= 1000) { // Check if the number is at least a thousand
            $this->shortCredits = round($this->credits / 1000, 1) . 'k'; // Convert to thousands, round to 1 decimal place, and append 'k'
        }
        $this->purehp = $worked['hp'];
        $this->hp = $this->purehp;
        $this->puremaxhp = floor($this->level * 50);
        $this->maxhp = $this->puremaxhp;
        $this->gtachance = $worked['gtachance'];
        $this->lastgta = $worked['lastgta'];
        $this->energyboost = $this->energyboost;
        $this->nerveboost = $this->nerveboost;


        $this->maxenergy = 9 + $this->level + $this->energyboost;
        $userPrestigeSkills = getUserPrestigeSkills($this);
        if ($userPrestigeSkills['energy_boost_level'] > 0) {
            $this->maxenergy = $this->maxenergy + (50 * $userPrestigeSkills['energy_boost_level']);
        }

        $this->maxnerve = 4 + $this->level + $this->nerveboost;
        $this->totalattrib = $this->speed + $this->strength + $this->defense;
        $this->game_updates = $worked['new_updates'];
        $this->pg = $worked['pg'];
        $this->battletotal = $this->battlewon + $this->battlelost;
        $this->crimetotal = $this->crimesucceeded + $this->crimefailed;
        $this->age = lastactive($worked['signuptime'], 'days');
        $this->formattedlastactive = lastactive($this->lastactive);
        if (!empty($this->bonuses)) {
            $bonuses = explode("|", implode("|", $this->bonuses));
            foreach ($bonuses as $bonus) {
                $bon = explode(":", $bonus);
                if (empty($bon[0]) || empty($bon[1]))
                    continue;
                $var = $bon[0];
                $this->$var += $bon[1];
            }
        }
        $this->energypercent = floor(($this->energy / $this->maxenergy) * 100);
        $this->formattedenergy = prettynum($this->energy) . " / " . prettynum($this->maxenergy) . " [" . $this->energypercent . "%]";
        $this->staminapercent = floor(($this->stamina / $this->max_stamina) * 100);
        $this->formattedstamina = prettynum($this->stamina) . " / " . prettynum($this->max_stamina) . " [" . $this->staminapercent . "%]";
        $this->nervepercent = floor(($this->nerve / $this->maxnerve) * 100);
        $this->formattednerve = prettynum($this->nerve) . " / " . prettynum($this->maxnerve) . " [" . $this->nervepercent . "%]";
        $this->hppercent = floor((($this->hp + 1) / ($this->maxhp + 1)) * 100);
        $this->formattedhp = prettynum($this->hp) . " / " . prettynum($this->maxhp) . " [" . $this->hppercent . "%]";
        $this->view_preference = $worked['is_mobile_disabled'];
        $this->is_chat_disabled = $worked['is_chat_disabled'];
        $this->relationshipended = $worked['relationshipended'];
        if ($this->relationship > 0) {
            $db->query("SELECT h.* FROM houses h JOIN grpgusers g ON h.id = g.house WHERE g.id = ?");
            $db->execute(array(
                $this->relplayer
            ));
            $relhouse = $db->fetch_row(true);
            if (!empty($relhouse['name']) && ($relhouse['id'] > $worked['houseid']) || ($worked['houseid'] == 0 and isset($relhouse))) {
                $worked['houseid'] = $relhouse['id'];
                $worked['housename'] = $relhouse['name'];
                $worked['houseawake'] = $relhouse['awake'];
                $this->housename = "Living with [x] in a {$worked['housename']}";
                $this->house = $relhouse['id'];
            }
            if (($relhouse['id'] == $worked['house'])) {
                $worked['houseawake'] = ceil(1.20 * $worked['houseawake']);
                $this->house_shared = true;
            } else {
                $this->house_shared = false;
            }
        }
        $this->house = $worked['house'];
        if ($this->house == 0) {
            if (!empty($row)) {
                $worked['houseid'] = $row['houseid'];
                $worked['housename'] = $row['name'];
                $worked['houseawake'] = $row['awake'];
                $this->housename = "Renting a {$row['name']}";
            }
        }
        if (!isset($this->housename))
            $this->housename = ($worked['houseid'] == 0) ? "Homeless!" : $worked['housename'];
        $this->houseawake = ($worked['houseid'] == 0) ? 100 : $worked['houseawake'];
        $this->directawake = $worked['awake'];
        $this->gangawake = $worked['gangawake'];
        $this->awake = $worked['awake'];
        $this->maxawake = floor(($this->houseawake) * (1 + ($this->gangawake / 100)));
        $this->directmaxawake = floor(($this->houseawake) * (1 + ($this->gangawake / 100)));

        $db->query("SELECT SUM(i.awake_boost * inv.quantity) AS total FROM inventory AS inv LEFT JOIN items AS i on inv.itemid = i.id WHERE inv.userid = " . $this->id . " AND i.awake_boost > 0");
        $db->execute();
        $hiaTotal = $db->fetch_single();
        $this->maxawake = $this->maxawake + $hiaTotal;
        $this->directmaxawake = $this->directmaxawake + $hiaTotal;

        $tempItemUse = getItemTempUse($this->id);
        if ($tempItemUse['gym_super_pills_time'] > time()) {
            $gymSuperPillAdd = ceil($this->maxawake / 100 * 10);
            $this->maxawake = $this->maxawake + $gymSuperPillAdd;
            $this->directmaxawake = $this->directmaxawake + $gymSuperPillAdd;
        }

        $this->directawake = ($this->directawake > $this->directmaxawake) ? $this->directmaxawake : $this->directawake;
        $this->directawake = ($this->directawake <= 0) ? 0 : $this->directawake;
        $this->awakepercent = floor(($this->directawake / $this->directmaxawake) * 100);
        $this->formattedawake = prettynum($this->directawake) . " / " . number_format($this->directmaxawake) . " [" . $this->awakepercent . "%]";
        $this->formattedawake2forbar = prettynum($this->directawake) . " / " . number_format($this->directmaxawake) . " [" . $this->awakepercent . "%]";
        $this->gangleader = $worked['leader'];
        $this->crewleader = $worked['leader'];
        $this->gangtag = $worked['tag'];
        $this->gangdescription = $worked['description'];
        $this->formattedgang = "<a href='viewgang.php?id=" . $this->gang . "'>" . $this->gangname . "</a>";
        $this->sig = $worked['signature'];
        $this->permban = (isset($permban)) ? $permban : 0;
        $this->permbandays = (isset($workedban['days'])) ? $workedban['days'] : 0;
        $this->freezeban = (isset($freezeban)) ? $freezeban : 0;
        $this->freezebandays = (isset($workedban2['days'])) ? $workedban2['days'] : 0;
        $this->mailban = (isset($mailban)) ? $mailban : 0;
        $this->mailbandays = (isset($workedban3['days'])) ? $workedban3['days'] : 0;
        $this->forumban = (isset($forumban)) ? $forumban : 0;
        $this->prestige = $worked['prestige'];
        $this->forumbandays = (isset($workedban4['days'])) ? $workedban4['days'] : 0;
        $this->rank = $worked['ranktitle'];
        $this->rank = ($this->rank == "") ? "Mobster" : $this->rank;
        if ($worked['rankcolor'] != "")
            $this->rank = "<span style='color:{$worked['rankcolor']}'>" . $this->rank . "</span>";
        $this->protag = $worked['tag'];
        $this->thread = $worked['threadtime'];
        $this->gcse = $worked['gcses'];
        $this->sharedcontribution = $worked['sharedcontribution'];

        $this->gmail = $worked['gangmail'];
        $this->globalchat = $worked['globalchat'];
        $this->promusic = $worked['music'];
        $this->Color1 = $worked['tColor1'];
        $this->Color2 = $worked['tColor2'];
        $this->Color3 = $worked['tColor3'];
        $this->votetokens = $worked['votetokens'];


        $this->img_name = $worked['image_name'];
        if ($this->permban > 0)
            $this->type = "Banned";
        else if ($this->id == 0)
            $this->type = "<u><b><font color=blue>Manager</font></b></u>";
        else if ($this->id == 0)
            $this->type = "<u><b><font color=blue>Technical Support</font></b></u>";
        else if ($this->id == 0)
            $this->type = "<font color='yellow'>Game Dev</font>";
        else if ($this->freezeban > 0)
            $this->type = "Temporarily Frozen";
        else if ($this->admin == 1)
            $this->type = "<span style='color:red;'>ADMIN</span>";
        else if ($this->pg == 1)
            $this->type = "Player Guide</font>";
        else if ($this->gm == 1)
            $this->type = "<u><b><font color=blue>Game Moderator</font></b></u>";
        else if ($this->eo == 1)
            $this->type = "<span style='color:#FFA500;'>Events Manager</span>";
        else if ($this->rmdays >= 1)
            $this->type = "<font color=blue>VIP</font>";
        else
            $this->type = "<font color=#FFFFFF>No VIP Status</font>";
        $this->badgesex = explode(",", $this->badges);
        $levelbadges = array(
            '6' => array(
                'needed' => 1000,
                'payout' => 125000,
                'img' => 'lvl400',
                'title' => 'Obsidian Leveler: Get to level 400'
            ),
            '5' => array(
                'needed' => 750,
                'payout' => 40000,
                'img' => 'lvl300',
                'title' => 'Gold Leveler: Get to level 300'
            ),
            '4' => array(
                'needed' => 500,
                'payout' => 25000,
                'img' => 'lvl200',
                'title' => 'Super Pro Leveler: Get to level 200'
            ),
            '3' => array(
                'needed' => 250,
                'payout' => 12500,
                'img' => 'lvl100',
                'title' => 'Pro Leveler: Get to level 100'
            ),
            '2' => array(
                'needed' => 125,
                'payout' => 7500,
                'img' => 'lvl50',
                'title' => 'Above Average Leveler: Get to level 50'
            ),
            '1' => array(
                'needed' => 50,
                'payout' => 5000,
                'img' => 'lvl25',
                'title' => 'Average Leveler: Get to level 25'
            )
        );
        $crimebadge = array(
            '14' => array(
                'needed' => 100000000,
                'payout' => 125000,
                'img' => 'crimes100m',
                'title' => 'Elite Criminal: Successfully complete 100,000,000 crimes'
            ),
            '13' => array(
                'needed' => 50000000,
                'payout' => 100000,
                'img' => 'crimes50m',
                'title' => 'Elite Criminal: Successfully complete 50,000,000 crimes'
            ),
            '12' => array(
                'needed' => 25000000,
                'payout' => 80000,
                'img' => 'crimes25m',
                'title' => 'Elite Criminal: Successfully complete 25,000,000 crimes'
            ),
            '11' => array(
                'needed' => 10000000,
                'payout' => 70000,
                'img' => 'crimes10m',
                'title' => 'Elite Criminal: Successfully complete 10,000,000 crimes'
            ),
            '10' => array(
                'needed' => 5000000,
                'payout' => 60000,
                'img' => 'crimes5m',
                'title' => 'Elite Criminal: Successfully complete 5,000,000 crimes'
            ),
            '9' => array(
                'needed' => 2000000,
                'payout' => 55000,
                'img' => 'crimes2m',
                'title' => 'Elite Criminal: Successfully complete 2,000,000 crimes'
            ),
            '8' => array(
                'needed' => 1500000,
                'payout' => 50000,
                'img' => 'crimes1.5m',
                'title' => 'Elite Criminal: Successfully complete 1,500,000 crimes'
            ),
            '7' => array(
                'needed' => 1000000,
                'payout' => 40000,
                'img' => 'crimes1m',
                'title' => 'Elite Criminal: Successfully complete 1,000,000 crimes'
            ),
            '6' => array(
                'needed' => 500000,
                'payout' => 30000,
                'img' => 'crimes500k',
                'title' => 'Elite Criminal: Successfully complete 500,000 crimes'
            ),
            '5' => array(
                'needed' => 250000,
                'payout' => 10000,
                'img' => 'crimes250k',
                'title' => 'Elite Criminal: Successfully complete 250,000 crimes'
            ),
            '4' => array(
                'needed' => 100000,
                'payout' => 4000,
                'img' => 'crimes100k',
                'title' => 'Elite Criminal: Successfully complete 100,000 crimes'
            ),
            '3' => array(
                'needed' => 10000,
                'payout' => 2000,
                'img' => 'crimes10k',
                'title' => 'Elite Criminal: Successfully complete 10,000 crimes'
            ),
            '2' => array(
                'needed' => 5000,
                'payout' => 750,
                'img' => 'crimes5k',
                'title' => 'Elite Criminal: Successfully complete 5,000 crimes'
            ),
            '1' => array(
                'needed' => 1000,
                'payout' => 125,
                'img' => 'crimes1k',
                'title' => 'Elite Criminal: Successfully complete 1,000 crimes'
            )
        );
        $statbadge = array(
            '6' => array(
                'needed' => 2500000000,
                'payout' => 100000,
                'img' => 'stats2-5b',
                'title' => 'Good Workout: Successfully gain 2,500,000,000 in Stats'
            ),
            '5' => array(
                'needed' => 1000000000,
                'payout' => 50000,
                'img' => 'stats1b',
                'title' => 'Good Workout: Successfully gain 1,000,000,000 in Stats'
            ),
            '4' => array(
                'needed' => 100000000,
                'payout' => 20000,
                'img' => 'stats100m',
                'title' => 'Good Workout: Successfully gain 100,000,000 in Stats'
            ),
            '3' => array(
                'needed' => 50000000,
                'payout' => 12500,
                'img' => 'stats50m',
                'title' => 'Good Workout: Successfully gain 50,000,000 in Stats'
            ),
            '2' => array(
                'needed' => 25000000,
                'payout' => 5000,
                'img' => 'stats25m',
                'title' => 'Good Workout: Successfully gain 25,000,000 in Stats'
            ),
            '1' => array(
                'needed' => 10000000,
                'payout' => 1250,
                'img' => 'stats10m',
                'title' => 'Good Workout: Successfully gain 10,000,000 in Stats'
            )
        );
        $battlebadge = array(
            '12' => array(
                'needed' => 10000000,
                'payout' => 250000,
                'img' => 'kills100m',
                'title' => 'Master Hitman: Win 10,000,000 kills'
            ),
            '11' => array(
                'needed' => 7500000,
                'payout' => 225000,
                'img' => 'kills7.5m',
                'title' => 'Master Hitman: Win 7,500,000 kills'
            ),
            '10' => array(
                'needed' => 5000000,
                'payout' => 200000,
                'img' => 'kills5m',
                'title' => 'Master Hitman: Win 5,000,000 kills'
            ),
            '9' => array(
                'needed' => 2500000,
                'payout' => 175000,
                'img' => 'kills2.5m',
                'title' => 'Master Hitman: Win 2,500,000 kills'
            ),
            '8' => array(
                'needed' => 1000000,
                'payout' => 150000,
                'img' => 'kills1m',
                'title' => 'Master Hitman: Win 1,000,000 kills'
            ),
            '7' => array(
                'needed' => 500000,
                'payout' => 125000,
                'img' => 'kills500k',
                'title' => 'Master Hitman: Win 500,000 kills'
            ),
            '6' => array(
                'needed' => 250000,
                'payout' => 100000,
                'img' => 'kills250k',
                'title' => 'Master Hitman: Win 250,000 kills'
            ),
            '5' => array(
                'needed' => 50000,
                'payout' => 50000,
                'img' => 'kills50k',
                'title' => 'Master Hitman: Win 50,000 kills'
            ),
            '4' => array(
                'needed' => 10000,
                'payout' => 20000,
                'img' => 'kills10k',
                'title' => 'Master Hitman: Win 10,000 kills'
            ),
            '3' => array(
                'needed' => 1000,
                'payout' => 5000,
                'img' => 'kills1000',
                'title' => 'Master Hitman: Win 1,000 kills'
            ),
            '2' => array(
                'needed' => 250,
                'payout' => 2500,
                'img' => 'kills250',
                'title' => 'Master Hitman: Win 250 kills'
            ),
            '1' => array(
                'needed' => 100,
                'payout' => 1250,
                'img' => 'kills100',
                'title' => 'Master Hitman: Win 100 kills'
            )
        );
        $bankbadge = array(
            '6' => array(
                'needed' => 1000000000,
                'payout' => 7500,
                'img' => 'bank1bill',
                'title' => 'Pro Banker: Bank $1,000,000,000'
            ),
            '5' => array(
                'needed' => 500000000,
                'payout' => 5000,
                'img' => 'bank500mill',
                'title' => 'Pro Banker: Bank $500,000,000'
            ),
            '4' => array(
                'needed' => 100000000,
                'payout' => 5000,
                'img' => 'bank100mill',
                'title' => 'Pro Banker: Bank $100,000,000'
            ),
            '3' => array(
                'needed' => 25000000,
                'payout' => 500,
                'img' => 'bank25mill',
                'title' => 'Pro Banker: Bank $25,000,000'
            ),
            '2' => array(
                'needed' => 5000000,
                'payout' => 250,
                'img' => 'bank5mill',
                'title' => 'Pro Banker: Bank $5,000,000'
            ),
            '1' => array(
                'needed' => 1000000,
                'payout' => 125,
                'img' => 'bank1mill',
                'title' => 'Pro Banker: Bank $1,000,000'
            )
        );
        $mugbadge = array(
            '10' => array(
                'needed' => 2500000,
                'payout' => 200000,
                'img' => 'mugs2.5m',
                'title' => 'Golden Mugger: Mugged 2,500,000 times'
            ),
            '9' => array(
                'needed' => 1000000,
                'payout' => 150000,
                'img' => 'mugs1m',
                'title' => 'Golden Mugger: Mugged 1,000,000 times'
            ),
            '8' => array(
                'needed' => 500000,
                'payout' => 100000,
                'img' => 'mugs500k',
                'title' => 'Golden Mugger: Mugged 500,000 times'
            ),
            '7' => array(
                'needed' => 250000,
                'payout' => 75000,
                'img' => 'mugs250k',
                'title' => 'Golden Mugger: Mugged 250,000 times'
            ),
            '6' => array(
                'needed' => 100000,
                'payout' => 65000,
                'img' => 'mugs100k',
                'title' => 'Golden Mugger: Mugged 100,000 times'
            ),
            '5' => array(
                'needed' => 50000,
                'payout' => 30000,
                'img' => 'mugs50k',
                'title' => 'Golden Mugger: Mugged 50,000 times'
            ),
            '4' => array(
                'needed' => 20000,
                'payout' => 10000,
                'img' => 'mugs20k',
                'title' => 'Golden Mugger: Mugged 20,000 times'
            ),
            '3' => array(
                'needed' => 5000,
                'payout' => 5000,
                'img' => 'mugs5k',
                'title' => 'Obsidian Mugger: Mugged 5,000 times'
            ),
            '2' => array(
                'needed' => 1000,
                'payout' => 2500,
                'img' => 'mugs1k',
                'title' => 'Great Mugger: Mugged 1,000 times'
            ),
            '1' => array(
                'needed' => 250,
                'payout' => 1250,
                'img' => 'mugs250',
                'title' => 'Good Mugger: Mugged 250 times'
            )
        );
        $bustbadge = array(
            '13' => array(
                'needed' => 5000000,
                'payout' => 200000,
                'img' => 'busts5m',
                'title' => 'Golden Buster: Bust 5,000,000 Mobsters'
            ),
            '12' => array(
                'needed' => 2000000,
                'payout' => 170000,
                'img' => 'busts2m',
                'title' => 'Golden Buster: Bust 2,000,000 Mobsters'
            ),
            '11' => array(
                'needed' => 1000000,
                'payout' => 150000,
                'img' => 'busts1m',
                'title' => 'Golden Buster: Bust 1,000,000 Mobsters'
            ),
            '10' => array(
                'needed' => 750000,
                'payout' => 140000,
                'img' => 'busts750k',
                'title' => 'Golden Buster: Bust 750,000 Mobsters'
            ),
            '9' => array(
                'needed' => 500000,
                'payout' => 120000,
                'img' => 'busts500k',
                'title' => 'Golden Buster: Bust 500,000 Mobsters'
            ),
            '8' => array(
                'needed' => 300000,
                'payout' => 100000,
                'img' => 'busts300k',
                'title' => 'Golden Buster: Bust 300,000 Mobsters'
            ),
            '7' => array(
                'needed' => 200000,
                'payout' => 80000,
                'img' => 'busts200k',
                'title' => 'Golden Buster: Bust 200,000 Mobsters'
            ),
            '6' => array(
                'needed' => 100000,
                'payout' => 70000,
                'img' => 'busts100k',
                'title' => 'Golden Buster: Bust 100,000 Mobsters'
            ),
            '5' => array(
                'needed' => 50000,
                'payout' => 30000,
                'img' => 'busts20k',
                'title' => 'Golden Buster: Bust 50,000 Mobsters'
            ),
            '4' => array(
                'needed' => 20000,
                'payout' => 10000,
                'img' => 'busts20k',
                'title' => 'Golden Buster: Bust 20,000 Mobsters'
            ),
            '3' => array(
                'needed' => 5000,
                'payout' => 5000,
                'img' => 'busts5k',
                'title' => 'Obsidian Buster: Bust 5,000 Mobsters'
            ),
            '2' => array(
                'needed' => 1000,
                'payout' => 2500,
                'img' => 'busts1k',
                'title' => 'Great Buster: Bust 1,000 Mobsters'
            ),
            '1' => array(
                'needed' => 250,
                'payout' => 1250,
                'img' => 'busts250',
                'title' => 'Good Buster: Bust 250 Mobsters'
            )
        );
        $missionBadges = getMissionBadges();
        $raidBadges = getRaidBadges();
        $racketBadges = getRacketBadges();
        $backalleyBadges = getBackalleyBadges();
        $this->debugtest = 'yes';
        if (!isset($ignoreForAjax) && $_SERVER['REQUEST_METHOD'] === 'GET') {
            foreach ($levelbadges as $number => $badgers) {
                if ($this->level >= $badgers['needed'] && $this->badgesex[0] == $number - 1) {

                    Send_Event($this->id, "You have " . number_format($badgers['payout'], 0) . " points ready to be claimed for reaching level " . prettynum($badgers['needed']) . ". <a style='color: red;' href='claim_achievements.php'>Claim Now</a>", $this->id);
                    $this->badgesex[0] = $number;
                }
                if (!isset($this->badge1) && $this->level >= $badgers['needed']) {
                    $this->badge1 = '<div class="ach' . $badgers['img'] . '" title="' . $badgers['title'] . '"><img width="100px" src="css/images/' . $badgers["img"] . '.png?v200"></img></div>';
                    $this->badge = 1;
                }
            }
            foreach ($crimebadge as $number => $badgers) {
                if ($this->crimesucceeded >= $badgers['needed'] && $this->badgesex[1] == $number - 1) {
                    Send_Event($this->id, "You have " . number_format($badgers['payout'], 0) . " points ready to be claimed for reaching " . prettynum($badgers['needed']) . " Crimes. <a style='color: red;' href='claim_achievements.php'>Claim Now</a>", $this->id);
                    $this->badgesex[1] = $number;
                }
                if (!isset($this->badge2) && $this->crimesucceeded >= $badgers['needed']) {
                    $this->badge2 = '<div class="ach' . $badgers['img'] . '" title="' . $badgers['title'] . '"><img style="width:100px;" src="css/images/' . $badgers["img"] . '.png?v200"></img></div>';
                    $this->badge = 1;
                }
            }
            foreach ($statbadge as $number => $badgers) {
                if ($this->totalattrib >= $badgers['needed'] && $this->badgesex[2] == $number - 1) {
                    Send_Event($this->id, "You have " . number_format($badgers['payout'], 0) . " points ready to be claimed for reaching " . prettynum($badgers['needed']) . " in Stats. <a style='color: red;' href='claim_achievements.php'>Claim Now</a>", $this->id);
                    $this->badgesex[2] = $number;
                }
                if (!isset($this->badge5) && $this->totalattrib >= $badgers['needed']) {
                    $this->badge5 = '<div class="ach' . $badgers['img'] . '" title="' . $badgers['title'] . '"><img style="width:100px;" src="css/images/' . $badgers["img"] . '.png?v200"></img></div>';
                    $this->badge = 1;
                }
            }
            foreach ($battlebadge as $number => $badgers) {
                if ($this->battlewon >= $badgers['needed'] && $this->badgesex[3] == $number - 1) {
                    Send_Event($this->id, "You have " . number_format($badgers['payout'], 0) . " points ready to be claimed for reaching " . prettynum($badgers['needed']) . " kills. <a style='color: red;' href='claim_achievements.php'>Claim Now</a>", $this->id);
                    $this->badgesex[3] = $number;
                }
                if (!isset($this->badge4) && $this->battlewon >= $badgers['needed']) {
                    $this->badge4 = '<div class="ach' . $badgers['img'] . '" title="' . $badgers['title'] . '"><img style="width:100px;" src="css/images/' . $badgers["img"] . '.png?v200"></img></div>';
                    $this->badge = 1;
                }
            }
            foreach ($bankbadge as $number => $badgers) {
                if (($this->banklog >= $badgers['needed'] || $this->bank >= $badgers['needed']) && $this->badgesex[4] == $number - 1) {
                    Send_Event($this->id, "You have " . number_format($badgers['payout'], 0) . " points ready to be claimed for " . prettynum($badgers['needed']) . " bank. <a style='color: red;' href='claim_achievements.php'>Claim Now</a>", $this->id);
                    $this->badgesex[4] = $number;
                }
                if (!isset($this->badge6) && $this->banklog >= $badgers['needed']) {
                    $this->badge6 = '<div class="ach' . $badgers['img'] . '" title="' . $badgers['title'] . '"><img style="width:100px;" src="css/images/' . $badgers["img"] . '.png?v200"></img></div>';
                    $this->badge = 1;
                }
            }
            foreach ($mugbadge as $number => $badgers) {
                if ($this->mugsucceeded >= $badgers['needed'] && $this->badgesex[5] == $number - 1) {
                    Send_Event($this->id, "You have " . number_format($badgers['payout'], 0) . " points ready to be claimed for reaching " . prettynum($badgers['needed']) . " mugs. <a style='color: red;' href='claim_achievements.php'>Claim Now</a>", $this->id);
                    $this->badgesex[5] = $number;
                }
                if (!isset($this->badge7) && $this->mugsucceeded >= $badgers['needed']) {
                    $this->badge7 = '<div class="ach' . $badgers['img'] . '" title="' . $badgers['title'] . '"><img style="width:100px;" src="css/images/' . $badgers["img"] . '.png?v200"></img></div>';
                    $this->badge = 1;
                }
            }
            foreach ($bustbadge as $number => $badgers) {
                if ($this->busts >= $badgers['needed'] && $this->badgesex[6] == $number - 1) {
                    Send_Event($this->id, "You have " . number_format($badgers['payout'], 0) . " points ready to be claimed for reaching " . prettynum($badgers['needed']) . " busts. <a style='color: red;' href='claim_achievements.php'>Claim Now</a>", $this->id);
                    $this->badgesex[6] = $number;
                }
                if (!isset($this->badge8) && $this->busts >= $badgers['needed']) {
                    $this->badge8 = '<div class="ach' . $badgers['img'] . '" title="' . $badgers['title'] . '"><img style="width:100px;" src="css/images/' . $badgers["img"] . '.png?v200"></img></div>';
                    $this->badge = 1;
                }
            }
            foreach ($missionBadges as $number => $badgers) {
                $missionsCount = $this->mission_count;

                if ($missionsCount >= $badgers['needed'] && $this->badgesex[7] == $number - 1) {
                    Send_Event($this->id, "You have " . number_format($badgers['payout'], 0) . " points ready to be claimed for reaching " . prettynum($badgers['needed']) . " missions. <a style='color: red;' href='claim_achievements.php'>Claim Now</a>", $this->id);
                    $this->badgesex[7] = $number;
                }
                if (!isset($this->badge9) && $missionsCount >= $badgers['needed']) {
                    $this->badge9 = '<div class="ach' . $badgers['img'] . '" title="' . $badgers['title'] . '"><img style="width:100px;" src="css/images/' . $badgers["img"] . '.png?v200"></img></div>';
                    $this->badge = 1;
                }
            }
            foreach ($raidBadges as $number => $badgers) {
                $raidsCount = $this->raidwins;

                if ($raidsCount >= $badgers['needed'] && $this->badgesex[8] == $number - 1) {
                    Send_Event($this->id, "You have " . number_format($badgers['payout'], 0) . " points ready to be claimed for reaching " . prettynum($badgers['needed']) . " raids. <a style='color: red;' href='claim_achievements.php'>Claim Now</a>", $this->id);
                    $this->badgesex[8] = $number;
                }
                if (!isset($this->badge10) && $raidsCount >= $badgers['needed']) {
                    $this->badge10 = '<div class="ach' . $badgers['img'] . '" title="' . $badgers['title'] . '"><img style="width:100px;" src="css/images/' . $badgers["img"] . '.png?v200"></img></div>';
                    $this->badge = 1;
                }
            }
            foreach ($racketBadges as $number => $badgers) {
                $racketWins = $this->gtzb_count;

                if ($racketWins >= $badgers['needed'] && $this->badgesex[9] == $number - 1) {
                    Send_Event($this->id, "You have " . number_format($badgers['payout'], 0) . " points ready to be claimed for reaching " . prettynum($badgers['needed']) . " protection racket wins. <a style='color: red;' href='claim_achievements.php'>Claim Now</a>", $this->id);
                    $this->badgesex[9] = $number;
                }
                if (!isset($this->badge11) && $racketWins >= $badgers['needed']) {
                    $this->badge11 = '<div class="ach' . $badgers['img'] . '" title="' . $badgers['title'] . '"><img style="width:100px;" src="css/images/' . $badgers["img"] . '.png?v200"></img></div>';
                    $this->badge = 1;
                }
            }
            foreach ($backalleyBadges as $number => $badgers) {
                $backalleyWins = $this->backalleywins;

                if ($backalleyWins >= $badgers['needed'] && $this->badgesex[10] == $number - 1) {
                    Send_Event($this->id, "You have " . number_format($badgers['payout'], 0) . " points ready to be claimed for reaching " . prettynum($badgers['needed']) . " backalley wins. <a style='color: red;' href='claim_achievements.php'>Claim Now</a>", $this->id);
                    $this->badgesex[10] = $number;
                }
                if (!isset($this->badge12) && $backalleyWins >= $badgers['needed']) {
                    $this->badge12 = '<div class="ach' . $badgers['img'] . '" title="' . $badgers['title'] . '"><img style="width:100px;" src="css/images/' . $badgers["img"] . '.png?v200"></img></div>';
                    $this->badge = 1;
                }
            }
            $this->badgesfinal = implode(",", $this->badgesex);
            if ($this->badgesfinal != $this->badges) {
                $db->query("UPDATE grpgusers SET badges = ? WHERE id = ?");
                $db->execute(array(
                    $this->badgesfinal,
                    $id
                ));
            }
        }

        $db->query("SELECT * FROM `user_research_type` WHERE `user_id` = " . $this->id . " AND `duration_in_days` < 1");
        $db->execute();
        $completeUserResearchTypes = $db->fetch_row();
        $completeUserResearchTypesIndexedOnId = array();
        foreach ($completeUserResearchTypes as $completeUserResearchType) {
            $completeUserResearchTypesIndexedOnId[$completeUserResearchType['research_type_id']] = $completeUserResearchType;
        }
        $this->completeUserResearchTypes = $completeUserResearchTypes;
        $this->completeUserResearchTypesIndexedOnId = $completeUserResearchTypesIndexedOnId;

        $this->formattedname = formatName($this->id);
        $this->invent = ($this->rmdays > 0) ? 5000 : 2500;
        while ($this->exp >= $this->maxexp && $this->exp != 0) {
            $this->level += 1;
            $this->maxnerve += 1;
            $this->nerve = $this->maxnerve;
            $this->maxenergy += 1;
            $this->energy = $this->maxenergy;
            $this->maxhp = $this->hp = $this->purehp = $this->puremaxhp = $this->level * 50;
            $this->awake = $this->maxawake;
            $this->exp -= $this->maxexp;
            $this->maxexp = experience($this->level + 1);
            bloodbath('level', $this->id);


            Send_Event($this->id, "You have just gained a level. You are now level <span style='color:red;'><b>$this->level</b></span>", $this->id);
            if ($this->level == 1000) {
                //Send_Event($this->id, "Congratulations on reaching level 1000!.  You are now able to <a href='prestige.php'>prestige</a>");
            }
            $db->query("UPDATE grpgusers SET level = ?, hp = ?, energy = ?, nerve = ?, exp = ? WHERE id = ?");
            $db->execute(array(
                $this->level,
                $this->hp,
                $this->energy,
                $this->nerve,
                $this->exp,

                $id
            ));
        }
        if (time() - $this->lastactive < 900) {
            $this->formattedonline = "<font style='color:green;padding:2px;font-weight:bold;'>[online]</font>";
        } else {
            $this->formattedonline = "<font style='color:red;padding:2px;font-weight:bold;'>[offline]</font>";
        }

        $tempItemUse = getItemTempUse($this->id);
        $now = time();
        if ($tempItemUse['nerve_vial_time'] > $now) {
            $this->maxnerve = $this->maxnerve * 2;
            $this->nervepercent = floor(($this->nerve / $this->maxnerve) * 100);
            $this->formattednerve = prettynum($this->nerve) . " / " . prettynum($this->maxnerve) . " [" . $this->nervepercent . "%]";
        }
    }

    function addPoints($id, $points)
    {
        global $db;

        $db->query("UPDATE grpgusers SET points = points + ? WHERE id = ?");
        $db->execute([$points, $id]);
    }
}
class GangRank
{
    function GangRank($rank, $notmyranks = 0)
    {
        global $user_class, $db;
        $gang_class = (isset($GLOBALS['gang_class'])) ? $GLOBALS['gang_class'] : new Gang($user_class->gang);

        $db->query("SELECT * FROM ranks WHERE id = ?");
        $db->execute([$rank]);
        $field = $db->fetch_row(true);
        if (empty($field)) {
            $db->query("SELECT * FROM ranks WHERE id = 6");
            $db->execute();
            $field = $db->fetch_row(true);
        }

        foreach ($field as $title => $value)
            if ($notmyranks) {
                $this->$title = $value;
            } else {
                $this->$title = ($user_class->leader == $user_class->id) ? 1 : $value;
            }
    }
}
class crewRank
{
    function crewRank($rank, $notmyranks = 0)
    {
        global $user_class, $db;
        $crew_class = (isset($GLOBALS['crew_class'])) ? $GLOBALS['crew_class'] : new crew($user_class->crew);

        $db->query("SELECT * FROM crewranks WHERE id = ?");
        $db->execute([$rank]);
        $field = $db->fetch_row(true);

        if (empty($field)) {
            $db->query("SELECT * FROM crewranks WHERE id = 6");
            $db->execute();
            $field = $db->fetch_row(true);
        }

        foreach ($field as $title => $value) {
            if ($notmyranks) {
                $this->$title = $value;
            } else {
                $this->$title = ($crew_class->leader == $user_class->id) ? 1 : $value;
            }
        }
    }
}
class Pet
{
    function __construct($userid)
    {
        global $db;

        $db->query("SELECT * FROM pets WHERE userid = ?");
        $db->execute([$userid]);
        $row = $db->fetch_row(true);

        if (empty($row)) {
            throw new Exception("No pet found for user ID: $userid");
        }

        foreach ($row as $key => $value) {
            $this->$key = $value;
        }

        $this->strength = $row['str'];
        $this->defense = $row['def'];
        $this->speed = $row['spe'];
        $this->totalatri = $row['spe'] + $row['def'] + $row['str'];
        $this->maxhp = $this->level * 50;
        $this->maxexp = experience($this->level + 1);
        $this->hppercent = floor($this->hp / $this->maxhp * 100);
        $this->formattedhp = prettynum($this->hp) . " / " . prettynum($this->maxhp) . " [" . $this->hppercent . "%]";
        $this->maxenergy = 9 + $this->level;
        $this->energypercent = floor(($this->energy / $this->maxenergy) * 100);
        $this->formattedenergy = prettynum($this->energy) . " / " . prettynum($this->maxenergy) . " [" . $this->energypercent . "%]";
        $this->maxnerve = 4 + $this->level;
        $this->nervepercent = floor(($this->nerve / $this->maxnerve) * 100);
        $this->formattednerve = prettynum($this->nerve) . " / " . prettynum($this->maxnerve) . " [" . $this->nervepercent . "%]";

        $db->query("SELECT name, awake FROM pethouses WHERE id = ?");
        $db->execute([$this->house]);
        $house = $db->fetch_row(true);
        $this->housename = $house['name'] ?? 'Homeless';
        $this->houseawake = $house['awake'] ?? 100;

        $this->maxawake = $this->houseawake;
        $this->awakepercent = floor(($this->awake / $this->maxawake) * 100);
        $this->formattedawake = prettynum($this->awake) . " / " . prettynum($this->maxawake) . " [" . $this->awakepercent . "%]";
        $this->exppercent = ($this->exp == 0) ? 0 : floor(($this->exp / $this->maxexp) * 100);
        $this->formattedexp = prettynum($this->exp) . " / " . prettynum($this->maxexp) . " [" . $this->exppercent . "%]";
        while ($this->exp >= $this->maxexp and $this->exp > 0) {
            $this->exp -= $this->maxexp;
            $this->level++;
            $this->maxexp = experience($this->level + 1);
            $newhp = ($this->level + 1) * 50;
            Send_Event($userid, "Your pet has just gained a level.");

            $db->query("UPDATE pets SET level = level +1, hp = ?, energy = ? + 1, nerve = ? + 1, exp = ? WHERE userid = ?");
            $db->execute([
                $newhp,
                $this->maxenergy,
                $this->maxnerve,
                $this->exp,
                $userid
            ]);
        }
    }
    function formatName()
    {
        $colors = explode("|", $this->coloredname);
        if ($this->coloredname != "FFFFFF|FFFFFF")
            return "<a href='petprofile.php?id=$this->userid'><b>" . text_gradient($colors[0], $colors[1], 1, $this->pname) . "</b></a>";
        else
            return "<a href='petprofile.php?id=$this->userid'>" . $this->pname . "</a>";
    }
}
class formatGang
{
    function __construct($id)
    {
        global $db;
        $db->query("SELECT tag, name, tColor1, tColor2, tColor3, formattedTag FROM gangs WHERE id = $id");
        $db->execute();
        $r = $db->fetch_row(true);

        $this->tag = $r['tag'];
        $this->name = $r['name'];
        $this->formattedTag = $r['formattedTag'];
        $this->id = $id;
        $this->colors = array($r['tColor1'], $r['tColor2'], $r['tColor3']);
    }
    function formatName()
    {
        $name = $this->name;
        if ($this->formattedTag == 'Yes') {
            $half = (int) ((strlen($this->name) / 2));
            $left = substr($this->name, 0, $half);
            $right = substr($this->name, $half);
            $name = text_gradient($this->colors[0], $this->colors[1], 1, $left);
            $name .= text_gradient($this->colors[1], $this->colors[2], 1, $right);
        }
        return "<a href='viewgang.php?id=$this->id'>" . $name . "</a>";
    }
    function formatTag()
    {
        if ($this->formattedTag == 'Yes') {
            $tag = str_split($this->tag);
            $this->tag = "";
            $c = 0;
            foreach ($tag as $letter)
                $this->tag .= "<span style='color: #{$this->colors[$c++]}'>$letter</span>";
        }
        return "<a href='viewgang.php?id=$this->id'>[" . $this->tag . "]</a>";
    }
}
