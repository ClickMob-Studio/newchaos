<?php
header("Content-Type: text/html; charset=utf-8");
include_once "includes/functions.php";
if (!isset($_SESSION['id'])) {
    error_reporting(0);
}

class User_Stats {
    function User_Stats($wutever) {
        global $db, $m;
        if ($m->get('user_stats')) {
            $this->playersloggedin = $m->get('user_stats.playersloggedin');
            $this->playersonlineinlastday = $m->get('user_stats.playersonlineinlastday');
            $this->playerstotal = $m->get('user_stats.playerstotal');
            return;
        }
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
        $m->set('user_stats.playersloggedin', $this->playersloggedin);
        $m->set('user_stats.playersonlineinlastday', $this->playersonlineinlastday);
        $m->set('user_stats.playerstotal', $this->playerstotal);
        $m->set('user_stats', 1, false, 60);
    }
}
class Gang {
    function Gang($id) {
        global $db;
        $db->query("SELECT * FROM gangs WHERE id = ?");
        $db->execute(array(
            $id
        ));
        $row = $db->fetch_row(true);
        if (empty($row))
            return;
        foreach ($row as $title => $value)
            $this->$title = $value;
        $db->query("SELECT id FROM grpgusers WHERE gang = ?");
        $db->execute(array(
            $id
        ));
        $members = $db->fetch_row();
        $this->members = count($members);
        //  style='border:1px solid #000000;'
        if ($row['banner'] != "")
            $this->formattedname = "<a href='viewgang.php?id=" . $row['id'] . "'><img src='" . $row['banner'] . "' height='75' width='250' /></a>";
        else
            $this->formattedname = "<a href='viewgang.php?id=" . $row['id'] . "'>" . $row['name'] . "</a>";
        $this->nobanner = "<a href='viewgang.php?id=" . $row['id'] . "'>" . $row['name'] . "</a>";
        $this->Color1 = $row["tColor1"];
        $this->Color2 = $row["tColor2"];
        $this->Color3 = $row["tColor3"];
        $this->crimeend = $row['ending'];
        $this->house = $row['ghouse'];
        $db->query("SELECT * FROM ghouses WHERE id = ?");
        $db->execute(array(
            $this->house
        ));
        $ganghouse = $db->fetch_row(true);
        $this->housename = $ganghouse['name'];
        $this->housename = ($this->housename == "") ? "None" : $this->housename;
        $this->houseawake = $ganghouse['awake'];
        $this->houseawake = ($this->houseawake == "") ? "0" : $this->houseawake;
        $this->housecost = $ganghouse['cost'];
        $this->tax = $row['tax'];
        $this->maxexp = GangExperience($this->level + 1);
        $this->exppercent = ($this->exp == 0) ? 0 : floor(($this->exp / $this->maxexp) * 100);
        $this->formattedexp = prettynum($this->exp) . " / " . prettynum($this->maxexp) . " [" . $this->exppercent . "%]";
        if ($this->exp >= $this->maxexp && $this->exp > 0) {
            $this->level += 1;
            $this->exp -= $this->maxexp;
            $db->query("UPDATE gangs SET level = ?, exp = ? WHERE id = ?");
            $db->execute(array(
                $this->level,
                $this->exp,
                $this->id
            ));
            Gang_Event($this->id, "Your gang has just gained a level!");
            foreach ($members as $member)
                Send_Event($member['id'], "Your gang has just gained a level!");
        }
    }
}
class User {
    function User($id) {
	    global $db;

        $db->query("SELECT grpg.*,grpg.tag AS ptag,g.ghouse,g.name AS gangname,g.leader,g.tag,g.description,ci.name AS cityname,h.name AS housename,h.awake AS houseawake,
                    co.name AS countryname, gh.awake AS gangawake, r.title AS ranktitle, r.color as rankcolor, b.days AS bandays,
		    g.tColor1, g.tColor2, g.tColor3, g.formattedTag, h.id AS houseid
                FROM grpgusers grpg
                LEFT JOIN gangs g ON g.id = grpg.gang
                JOIN cities ci ON ci.id = grpg.city
                LEFT JOIN houses h ON h.id = grpg.house
                JOIN countries co ON co.id = grpg.country
                LEFT JOIN ranks r ON r.id = grpg.grank
                LEFT JOIN ghouses gh ON gh.id = g.ghouse
                LEFT JOIN bans b ON b.id = grpg.id
                WHERE grpg.id = ?");
		$db->execute(array(
			$id
		));
		$worked = $db->fetch_row(true);

        if (empty($worked))
            return;
        foreach ($worked as $title => $value)
            $this->$title = $value;

		$db->query("SELECT * FROM pets WHERE userid = ? AND leash = 1");
		$db->execute(array(
			$id
		));
        if ($db->num_rows()) {
            $pet = $db->fetch_row(true);
        } else {
            $pet['str'] = 0;
            $pet['def'] = 0;
            $pet['spe'] = 0;
        }
		$db->query("SELECT days FROM bans WHERE id = ?");
		$db->execute(array(
			$id
		));
        if ($db->num_rows()) {
			$db->query("SELECT * FROM bans WHERE id = ? AND type = ?");
			$db->execute(array(
				$id,
				'perm'
			));
			$permban = $db->num_rows();
			$workedban = $db->fetch_row(true);
			$db->execute(array(
				$id,
				'freeze'
			));
			$freezeban = $db->num_rows();
			$workedban2 = $db->fetch_row(true);
			$db->execute(array(
				$id,
				'mail'
			));
			$mailban = $db->num_rows();
			$workedban3 = $db->fetch_row(true);
			$db->execute(array(
				$id,
				'forum'
			));
			$forumban = $db->num_rows();
			$workedban4 = $db->fetch_row(true);
        }
		$db->query("SELECT i.*, c.image overrideimage, c.name overridename FROM items i LEFT JOIN customitems c ON i.id = c.itemid AND c.userid = ? WHERE id = ?");
        if ($worked['eqweapon']) {
			$db->execute(array(
				$id,
				$worked['eqweapon']
			));
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
		if(in_array($this->gang, array(111,112))){
			$this->city = 14;
			$this->cityname = "Server War";
		}
        $this->mycityname = $this->cityname;
        if ($this->eqarmor == 43) {
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
		$db->query("SELECT SUM(paymentamount) FROM ipn WHERE user_id = ? AND date > unix_timestamp() - 2592000");
		$db->execute(array(
			$id
		));

        $this->shared_bank = $worked["shared_bank"];

		$this->donations = $db->fetch_single();
        $this->druggie = explode("|", $this->drugs);
        $this->drugstr = (isset($this->druggie[2]) && $this->druggie[2] > time() - 900) ? 1.25 : 1;
        $this->drugdef = (isset($this->druggie[1]) && $this->druggie[1] > time() - 900) ? 1.25 : 1;
        $this->drugspe = (isset($this->druggie[0]) && $this->druggie[0] > time() - 900) ? 1.25 : 1;
        $this->moddedstrength = round((($pet['str'] + $worked['strength']) * ($this->weaponoffense * .01 + 1)) * $this->drugstr);
        $this->moddeddefense = round((($pet['def'] + $worked['defense']) * ($this->armordefense * .01 + 1)) * $this->drugdef);
        $this->moddedspeed = round((($pet['spe'] + $worked['speed']) * ($this->shoesspeed * .01 + 1)) * $this->drugspe);
        $this->moddedtotalattrib = $this->moddedspeed + $this->moddedstrength + $this->moddeddefense;
        $this->maxexp = experience($this->level + 1);
        $this->exppercent = ($this->exp == 0) ? 0 : floor(($this->exp / $this->maxexp) * 100);
        $this->formattedexp = prettynum($this->exp) . " / " . prettynum($this->maxexp) . " [" . $this->exppercent . "%]";
        $this->money = ($this->money < 1) ? 0 : $this->money;
        $this->purehp = $worked['hp'];
        $this->hp = $this->purehp;
        $this->puremaxhp = floor($this->level * 50);
        $this->maxhp = $this->puremaxhp;
$this->energyboost =  $this->energyboost;
$this->nerveboost =  $this->nerveboost;


        $this->maxenergy = 9 + $this->level + $this->energyboost;


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
                if(empty($bon[0]) || empty($bon[1]))
                    continue;
                $var = $bon[0];
                $this->$var += $bon[1];
            }
        }
        $this->energypercent = floor(($this->energy / $this->maxenergy) * 100);
        $this->formattedenergy = $this->energy . " / " . $this->maxenergy . " [" . $this->energypercent . "%]";
        $this->staminapercent = floor(($this->stamina / $this->max_stamina) * 100);
        $this->formattedstamina = $this->stamina . " / " . $this->max_stamina . " [" . $this->staminapercent . "%]";
        $this->nervepercent = floor(($this->nerve / $this->maxnerve) * 100);
        $this->formattednerve = $this->nerve . " / " . $this->maxnerve . " [" . $this->nervepercent . "%]";
        $this->hppercent = floor((($this->hp + 1) / ($this->maxhp + 1)) * 100);
        $this->formattedhp = $this->hp . " / " . $this->maxhp . " [" . $this->hppercent . "%]";
        if ($this->relationship > 0) {
			$db->query("SELECT h.* FROM houses h JOIN grpgusers g ON h.id = g.house WHERE g.id = ?");
			$db->execute(array(
				$this->relplayer
			));
            $relhouse = $db->fetch_row(true);
            if (!empty($relhouse['name']) && ($relhouse['id'] > $worked['houseid']) || ($worked['houseid'] == 0 AND isset($relhouse))) {
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
        if($this->house == 0){
            $db->query("SELECT * FROM rentedProperties r JOIN houses h ON r.houseid = h.id WHERE renter = ? ORDER BY awake DESC LIMIT 1");
            $db->execute(array(
                $this->id
            ));
            $row = $db->fetch_row(true);
            if(!empty($row)){
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
        $this->directawake = ($this->directawake > $this->directmaxawake) ? $this->directmaxawake : $this->directawake;
        $this->directawake = ($this->directawake <= 0) ? 0 : $this->directawake;
        $this->awakepercent = floor(($this->directawake / $this->directmaxawake) * 100);
        $this->formattedawake = $this->directawake . " / " . $this->directmaxawake . " [" . $this->awakepercent . "%]";
        $this->formattedawake2forbar = $this->directawake . " / " . $this->directmaxawake . " [" . $this->awakepercent . "%]";
        $this->gangleader = $worked['leader'];
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
            else if ($this->id == 631)
            $this->type = "<u><b><font color=blue>Technical Support</font></b></u>";
            else if ($this->id == 0)
            $this->type = "<font color='yellow'>Game Dev</font>";
        else if ($this->freezeban > 0)
            $this->type = "Temporarily Frozen";
		elseif(in_array($_SESSION['id'], array(146, 864)) && $id == 864)
			$this->type = "<font color=gold>Spam Queen</font>";
        else if ($this->admin == 1)
            $this->type = "<img src='https://themafialife.com/css/images/admin.png' />";
        else if ($this->pg == 1)
            $this->type = "Player Guide</font>";
        else if ($this->gm == 1)
            $this->type = "<u><b><font color=blue>Game Moderator</font></b></u>";
        else if ($this->eo == 1)
            $this->type = "<span style='color:#FFA500;'>Events Manager</span>";
        else if ($this->rmdays >= 1)
            $this->type = "<font color=#00FF00>Respected Mobster</font>";
        else
            $this->type = "<font color=#FFFFFF>Not Respected</font>";
        $this->badgesex = explode(",", $this->badges);
        $levelbadges = array(
            '6' => array(
                'needed' => 400,
                'payout' => 20000,
                'img' => 'lvl400',
                'title' => 'Obsidian Leveler: Get to level 400'
            ),
            '5' => array(
                'needed' => 300,
                'payout' => 10000,
                'img' => 'lvl300',
                'title' => 'Gold Leveler: Get to level 300'
            ),
            '4' => array(
                'needed' => 200,
                'payout' => 4000,
                'img' => 'lvl200',
                'title' => 'Super Pro Leveler: Get to level 200'
            ),
            '3' => array(
                'needed' => 100,
                'payout' => 2000,
                'img' => 'lvl100',
                'title' => 'Pro Leveler: Get to level 100'
            ),
            '2' => array(
                'needed' => 50,
                'payout' => 1000,
                'img' => 'lvl50',
                'title' => 'Above Average Leveler: Get to level 50'
            ),
            '1' => array(
                'needed' => 25,
                'payout' => 300,
                'img' => 'lvl25',
                'title' => 'Average Leveler: Get to level 25'
            )
        );
        $crimebadge = array(
            '6' => array(
                'needed' => 500000,
                'payout' => 10000,
                'img' => 'crimes500k',
                'title' => 'Elite Criminal: Successfully complete 500,000 crimes'
            ),
            '5' => array(
                'needed' => 250000,
                'payout' => 5000,
                'img' => 'crimes250k',
                'title' => 'Elite Criminal: Successfully complete 250,000 crimes'
            ),
            '4' => array(
                'needed' => 100000,
                'payout' => 2000,
                'img' => 'crimes100k',
                'title' => 'Elite Criminal: Successfully complete 100,000 crimes'
            ),
            '3' => array(
                'needed' => 10000,
                'payout' => 500,
                'img' => 'crimes10k',
                'title' => 'Elite Criminal: Successfully complete 10,000 crimes'
            ),
            '2' => array(
                'needed' => 5000,
                'payout' => 250,
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
                'payout' => 10000,
                'img' => 'stats2-5b',
                'title' => 'Good Workout: Successfully gain 2,500,000,000 in Stats'
            ),
            '5' => array(
                'needed' => 1000000000,
                'payout' => 5000,
                'img' => 'stats1b',
                'title' => 'Good Workout: Successfully gain 1,000,000,000 in Stats'
            ),
            '4' => array(
                'needed' => 100000000,
                'payout' => 2000,
                'img' => 'stats100m',
                'title' => 'Good Workout: Successfully gain 100,000,000 in Stats'
            ),
            '3' => array(
                'needed' => 50000000,
                'payout' => 500,
                'img' => 'stats50m',
                'title' => 'Good Workout: Successfully gain 50,000,000 in Stats'
            ),
            '2' => array(
                'needed' => 25000000,
                'payout' => 250,
                'img' => 'stats25m',
                'title' => 'Good Workout: Successfully gain 25,000,000 in Stats'
            ),
            '1' => array(
                'needed' => 10000000,
                'payout' => 125,
                'img' => 'stats10m',
                'title' => 'Good Workout: Successfully gain 10,000,000 in Stats'
            )
        );
        $battlebadge = array(
            '6' => array(
                'needed' => 250000,
                'payout' => 10000,
                'img' => 'kills250k',
                'title' => 'Master Hitman: Win 250,000 kills'
            ),
            '5' => array(
                'needed' => 50000,
                'payout' => 5000,
                'img' => 'kills50k',
                'title' => 'Master Hitman: Win 50,000 kills'
            ),
            '4' => array(
                'needed' => 10000,
                'payout' => 2000,
                'img' => 'kills10k',
                'title' => 'Master Hitman: Win 10,000 kills'
            ),
            '3' => array(
                'needed' => 1000,
                'payout' => 500,
                'img' => 'kills1000',
                'title' => 'Master Hitman: Win 1,000 kills'
            ),
            '2' => array(
                'needed' => 250,
                'payout' => 250,
                'img' => 'kills250',
                'title' => 'Master Hitman: Win 250 kills'
            ),
            '1' => array(
                'needed' => 100,
                'payout' => 125,
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
			'6' => array(
				'needed' => 100000,
				'payout' => 6500,
				'img' => 'mugs100k',
				'title' => 'Golden Mugger: Mugged 100,000 times'
			),
            '5' => array(
                'needed' => 50000,
                'payout' => 3000,
                'img' => 'mugs50k',
                'title' => 'Golden Mugger: Mugged 50,000 times'
            ),
            '4' => array(
                'needed' => 20000,
                'payout' => 1000,
                'img' => 'mugs20k',
                'title' => 'Golden Mugger: Mugged 20,000 times'
            ),
            '3' => array(
                'needed' => 5000,
                'payout' => 500,
                'img' => 'mugs5k',
                'title' => 'Obsidian Mugger: Mugged 5,000 times'
            ),
            '2' => array(
                'needed' => 1000,
                'payout' => 250,
                'img' => 'mugs1k',
                'title' => 'Great Mugger: Mugged 1,000 times'
            ),
            '1' => array(
                'needed' => 250,
                'payout' => 125,
                'img' => 'mugs250',
                'title' => 'Good Mugger: Mugged 250 times'
            )
        );
        $bustbadge = array(
            '6' => array(
                'needed' => 100000,
                'payout' => 7000,
                'img' => 'busts100k',
                'title' => 'Golden Buster: Bust 100,000 Mobsters'
            ),
            '5' => array(
                'needed' => 50000,
                'payout' => 3000,
                'img' => 'busts20k',
                'title' => 'Golden Buster: Bust 50,000 Mobsters'
            ),
            '4' => array(
                'needed' => 20000,
                'payout' => 1000,
                'img' => 'busts20k',
                'title' => 'Golden Buster: Bust 20,000 Mobsters'
            ),
            '3' => array(
                'needed' => 5000,
                'payout' => 500,
                'img' => 'busts5k',
                'title' => 'Obsidian Buster: Bust 5,000 Mobsters'
            ),
            '2' => array(
                'needed' => 1000,
                'payout' => 250,
                'img' => 'busts1k',
                'title' => 'Great Buster: Bust 1,000 Mobsters'
            ),
            '1' => array(
                'needed' => 250,
                'payout' => 125,
                'img' => 'busts250',
                'title' => 'Good Buster: Bust 250 Mobsters'
            )
        );
        $db->query("UPDATE grpgusers SET points = points + ? WHERE id = ?");
        $this->debugtest = 'yes';
        foreach ($levelbadges as $number => $badgers) {
            if ($this->level >= $badgers['needed'] && $this->badgesex[0] == $number - 1) {
				$db->execute(array(
					$badgers['payout'],
					$id
				));
                Send_Event($this->id, "You have received {$badgers['payout']} Points for Reaching Level " . prettynum($badgers['needed']) . " Achievement.", $this->id);
                Send_Event1($this->id, "Has just received {$badgers['payout']} Points for Reaching Level " . prettynum($badgers['needed']) . " Achievement.", $this->id);
                $this->badgesex[0] = $number;
            }
            if (!isset($this->badge1) && $this->level >= $badgers['needed']) {
                $this->badge1 = '<div class="ach' . $badgers['img'] . '" title="' . $badgers['title'] . '"><img width="100px" src="css/images/'.$badgers["img"].'.png"></img></div>';
                $this->badge = 1;
            }
        }
        foreach ($crimebadge as $number => $badgers) {
            if ($this->crimesucceeded >= $badgers['needed'] && $this->badgesex[1] == $number - 1) {
				$db->execute(array(
					$badgers['payout'],
					$id
				));
                Send_Event($this->id, "You have received {$badgers['payout']} Points for Reaching " . prettynum($badgers['needed']) . " Crimes.", $this->id);
                Send_Event1($this->id, "Has just received {$badgers['payout']} Points for Reaching " . prettynum($badgers['needed']) . " Crimes.", $this->id);
                $this->badgesex[1] = $number;
            }
            if (!isset($this->badge2) && $this->crimesucceeded >= $badgers['needed']) {
                $this->badge2 = '<div class="ach' . $badgers['img'] . '" title="' . $badgers['title'] . '"><img src="css/images/'.$badgers["img"].'.png"></img></div>';
                $this->badge = 1;
            }
        }
        foreach ($statbadge as $number => $badgers) {
            if ($this->totalattrib >= $badgers['needed'] && $this->badgesex[2] == $number - 1) {
				$db->execute(array(
					$badgers['payout'],
					$id
				));
                Send_Event($this->id, "You have received {$badgers['payout']} Points for Reaching " . prettynum($badgers['needed']) . " in Stats.", $this->id);
                Send_Event1($this->id, "Has just received {$badgers['payout']} Points for Reaching " . prettynum($badgers['needed']) . " in Stats.", $this->id);
                $this->badgesex[2] = $number;
            }
            if (!isset($this->badge5) && $this->totalattrib >= $badgers['needed']) {
                $this->badge5 = '<div class="ach' . $badgers['img'] . '" title="' . $badgers['title'] . '"><img src="css/images/'.$badgers["img"].'.png"></img></div>';
                $this->badge = 1;
            }
        }
        foreach ($battlebadge as $number => $badgers) {
            if ($this->battlewon >= $badgers['needed'] && $this->badgesex[3] == $number - 1) {
				$db->execute(array(
					$badgers['payout'],
					$id
				));
                Send_Event($this->id, "You have received {$badgers['payout']} Points for Reaching " . prettynum($badgers['needed']) . " kills.", $this->id);
                Send_Event1($this->id, "Has just received {$badgers['payout']} Points for Reaching " . prettynum($badgers['needed']) . " kills.", $this->id);
                $this->badgesex[3] = $number;
            }
            if (!isset($this->badge4) && $this->battlewon >= $badgers['needed']) {
                $this->badge4 = '<div class="ach' . $badgers['img'] . '" title="' . $badgers['title'] . '"><img src="css/images/'.$badgers["img"].'.png"></img></div>';
                $this->badge = 1;
            }
        }
        foreach ($bankbadge as $number => $badgers) {
            if (($this->banklog >= $badgers['needed'] || $this->bank >= $badgers['needed']) && $this->badgesex[4] == $number - 1) {
				$db->execute(array(
					$badgers['payout'],
					$id
				));
                Send_Event($this->id, "You have received {$badgers['payout']} Points for Reaching " . prettynum($badgers['needed']) . " bank.", $this->id);
                Send_Event1($this->id, "Has just received {$badgers['payout']} Points for Reaching " . prettynum($badgers['needed']) . " bank.", $this->id);
                $this->badgesex[4] = $number;
            }
            if (!isset($this->badge6) && $this->banklog >= $badgers['needed']) {
                $this->badge6 = '<div class="ach' . $badgers['img'] . '" title="' . $badgers['title'] . '"><img src="css/images/'.$badgers["img"].'.png"></img></div>';
                $this->badge = 1;
            }
        }
        foreach ($mugbadge as $number => $badgers) {
            if ($this->mugsucceeded >= $badgers['needed'] && $this->badgesex[5] == $number - 1) {
				$db->execute(array(
					$badgers['payout'],
					$id
				));
                Send_Event($this->id, "You have received {$badgers['payout']} Points for Reaching " . prettynum($badgers['needed']) . " mugs.", $this->id);
                Send_Event1($this->id, "Has just received {$badgers['payout']} Points for Reaching " . prettynum($badgers['needed']) . " mugs.", $this->id);
                $this->badgesex[5] = $number;
            }
            if (!isset($this->badge7) && $this->mugsucceeded >= $badgers['needed']) {
                $this->badge7 = '<div class="ach' . $badgers['img'] . '" title="' . $badgers['title'] . '"><img src="css/images/'.$badgers["img"].'.png"></img></div>';
                $this->badge = 1;
            }
        }
        foreach ($bustbadge as $number => $badgers) {
            if ($this->busts >= $badgers['needed'] && $this->badgesex[6] == $number - 1) {
				$db->execute(array(
					$badgers['payout'],
					$id
				));
                Send_Event($this->id, "You have received {$badgers['payout']} Points for Reaching " . prettynum($badgers['needed']) . " busts.", $this->id);
                Send_Event1($this->id, "Has just received {$badgers['payout']} Points for Reaching " . prettynum($badgers['needed']) . " busts.", $this->id);
                $this->badgesex[6] = $number;
            }
            if (!isset($this->badge8) && $this->busts >= $badgers['needed']) {
                $this->badge8 = '<div class="ach' . $badgers['img'] . '" title="' . $badgers['title'] . '"><img src="css/images/'.$badgers["img"].'.png"></img></div>';
                $this->badge = 1;
            }
        }
        $this->badgesfinal = implode(",", $this->badgesex);
        if ($this->badgesfinal != $this->badges){
			$db->query("UPDATE grpgusers SET badges = ? WHERE id = ?");
			$db->execute(array(
				$this->badgesfinal,
				$id
			));
		}
        $this->formattedname = formatName($this->id);
        $this->formattedname2 = formatName($this->id);
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
            bloodbath('level', $this->id);
			$rndpts = rand(10,100);
			if($id == 146)
				$rndpts = 0;
			if($this->prestige)
				$rndpts *= 2;
			$addon = ($id == 934) ? ', 1 Jar of Peanut Butter' : '';
			if($id == 934){
				$db->query("UPDATE user_gifts SET qty = qty + 1 WHERE userid = 934 AND item LIKE 'Jar of Peanut Butter'");
				$db->execute();
			}
            Send_Event($this->id, "You have just gained a level. <span style='color:green;'>[+ $rndpts Points$addon]</span>", $this->id);
            if ($this->level == 500) {
                Send_Event($this->id, "Congratulations on reaching level 500!.  You are now able to <a href='prestige.php'>prestige</a>");
            }
			$db->query("UPDATE grpgusers SET level = ?, hp = ?, energy = ?, nerve = ?, exp = ?, points = points + ? WHERE id = ?");
			$db->execute(array(
				$this->level,
				$this->hp,
				$this->energy,
				$this->nerve,
				$this->exp,
				$rndpts,
				$id
			));
        }
        if (time() - $this->lastactive < 900) {
            $this->formattedonline = "<font style='color:green;padding:2px;font-weight:bold;'>[online]</font>";
        } else {
            $this->formattedonline = "<font style='color:red;padding:2px;font-weight:bold;'>[offline]</font>";
	}

        if ($this->id == 1)
            $this->admin = 1;
    }
}
class GangRank {
    function GangRank($rank, $notmyranks = 0) {
        global $user_class;
        $gang_class = (isset($GLOBALS['gang_class'])) ? $GLOBALS['gang_class'] : new Gang($user_class->gang);
        $field = mysql_fetch_array(mysql_query("SELECT * FROM ranks WHERE id = '$rank'"));
        if (empty($field))
            $field = mysql_fetch_array(mysql_query("SELECT * FROM ranks WHERE id = 6"));
        foreach ($field as $title => $value)
            if ($notmyranks)
                $this->$title = $value;
            else
                $this->$title = ($gang_class->leader == $user_class->id) ? 1 : $value;
    }
}
class Pet {
    function __construct($userid) {
        $q = mysql_query("SELECT * FROM pets WHERE userid = $userid");
        $row = mysql_fetch_array($q);
        if (empty($row))
            return false;
        foreach ($row as $key => $value)
            $this->$key = $value;
        $this->strength = $row['str'];
        $this->defense = $row['def'];
        $this->speed = $row['spe'];
        $this->totalatri = $row['spe'] + $row['def'] + $row['str'];
        $this->maxhp = $this->level * 50;
        $this->maxexp = experience($this->level + 1);
        $this->hppercent = floor($this->hp / $this->maxhp * 100);
        $this->formattedhp = $this->hp . " / " . $this->maxhp . " [" . $this->hppercent . "%]";
        $this->maxenergy = 9 + $this->level;
        $this->energypercent = floor(($this->energy / $this->maxenergy) * 100);
        $this->formattedenergy = $this->energy . " / " . $this->maxenergy . " [" . $this->energypercent . "%]";
        $this->maxnerve = 4 + $this->level;
        $this->nervepercent = floor(($this->nerve / $this->maxnerve) * 100);
        $this->formattednerve = $this->nerve . " / " . $this->maxnerve . " [" . $this->nervepercent . "%]";
        $y = mysql_query("SELECT name, awake FROM pethouses WHERE id = $this->house");
        $house = mysql_fetch_array($y);
        if (empty($house)) {
            $this->housename = 'Homeless';
            $this->houseawake = 100;
        } else {
            $this->housename = $house['name'];
            $this->houseawake = $house['awake'];
        }
        $this->maxawake = $this->houseawake;
        $this->awakepercent = floor(($this->awake / $this->maxawake) * 100);
        $this->formattedawake = $this->awake . " / " . $this->maxawake . " [" . $this->awakepercent . "%]";
        $this->exppercent = ($this->exp == 0) ? 0 : floor(($this->exp / $this->maxexp) * 100);
        $this->formattedexp = prettynum($this->exp) . " / " . prettynum($this->maxexp) . " [" . $this->exppercent . "%]";
		while($this->exp >= $this->maxexp AND $this->exp > 0){
			$this->exp -= $this->maxexp;
			$this->level++;
			$this->maxexp = experience($this->level + 1);
            $newhp = ($this->level + 1) * 50;
            Send_Event($userid, "Your pet has just gained a level.");
            mysql_query("UPDATE pets SET level = level + 1, hp = $newhp, energy = " . $this->maxenergy . " + 1, nerve = " . $this->maxnerve . " + 1, exp = $this->exp WHERE userid = $userid");
        }
    }
    function formatName() {
        $colors = explode("|", $this->coloredname);
        if ($this->coloredname != "FFFFFF|FFFFFF")
            return "<a href='petprofile.php?id=$this->userid'><b>" . text_gradient($colors[0], $colors[1], 1, $this->pname) . "</b></a>";
        else
            return "<a href='petprofile.php?id=$this->userid'>" . $this->pname . "</a>";
    }
}
class formatGang {
    function __construct($id) {
        global $db, $m;
        if (!$m->get('formatGang.' . $id)) {
            $db->query("SELECT tag, name, tColor1, tColor2, tColor3, formattedTag FROM gangs WHERE id = $id");
            $db->execute();
            $r = $db->fetch_row(true);
            $m->set('formatGang.' . $id, $r, 60);
        } else
            $r = $m->get('formatGang.' . $id);
        $this->tag = $r['tag'];
        $this->name = $r['name'];
        $this->formattedTag = $r['formattedTag'];
        $this->id = $id;
        $this->colors = array($r['tColor1'], $r['tColor2'], $r['tColor3']);
    }
    function formatName() {
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
    function formatTag() {
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
