<?php

// This script is used to migrate the database to support the new specialization system.
// Should be run only once to set up the neccessary tables and columns.

if (!isset($_GET['abracadabra']) || $_GET['abracadabra'] !== 'open-sesame') {
    die('Access denied');
}

require_once 'dbcon.php';
require_once 'pdo.php';
require_once 'includes/functions.php';

// Add new tables
$db->query("CREATE TABLE IF NOT EXISTS `skilltrees` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `icon` VARCHAR(255) NOT NULL
)");
$db->execute();

$db->query("CREATE TABLE IF NOT EXISTS `skilltree_nodes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `treeid` INT NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` VARCHAR(255) NOT NULL,
    `icon` VARCHAR(255) NOT NULL,
    `parent` INT,
    `rewards` JSON DEFAULT NULL,
    FOREIGN KEY (`treeid`) REFERENCES `skilltrees`(`id`)
)");
$db->execute();

// Add skill_ids column to grpgusers table
$db->query("SHOW COLUMNS FROM `grpgusers` LIKE 'skill_ids'");
$result = $db->fetch_row();
if (!$result || empty($result)) {
    $db->query("ALTER TABLE `grpgusers` ADD COLUMN `skill_ids` VARCHAR(255) DEFAULT NULL");
    $db->execute();
}

// Add specialization_level column to grpgusers table
$db->query("SHOW COLUMNS FROM `grpgusers` LIKE 'specialization_level'");
$result = $db->fetch_row();
if (!$result || empty($result)) {
    $db->query("ALTER TABLE `grpgusers` ADD COLUMN `specialization_level` INT DEFAULT 0");
    $db->execute();
}

// Add skill_points column to grpgusers table
$db->query("SHOW COLUMNS FROM `grpgusers` LIKE 'skill_points'");
$result = $db->fetch_row();
if (!$result || empty($result)) {
    $db->query("ALTER TABLE `grpgusers` ADD COLUMN `skill_points` INT DEFAULT 0");
    $db->execute();
}

// Add specialization_exp column to grpgusers table
$db->query("SHOW COLUMNS FROM `grpgusers` LIKE 'specialization_exp'");
$result = $db->fetch_row();
if (!$result || empty($result)) {
    $db->query("ALTER TABLE `grpgusers` ADD COLUMN `specialization_exp` INT DEFAULT 0");
    $db->execute();
}


$db->query("SELECT COUNT(*) FROM `skilltrees`");
$result = $db->fetch_single();
if (!$result || empty($result)) {
    // Insert skilltrees
    $db->query("INSERT INTO `skilltrees` (`title`, `icon`) VALUES (?, ?)");
    $db->execute(['Skilltree', 'root.png']);

    // $db->execute(['Stealth', 'stealth.png']);
    // $db->execute(['Operations', 'operations.png']);
    // $db->execute(['Brawler', 'brawler.png']);
}

$db->query("SELECT id FROM `skilltrees` LIMIT 1");
$db->execute();
$skilltreeId = $db->fetch_single();

$db->query("SELECT COUNT(*) FROM `skilltree_nodes`");
$result = $db->fetch_single();
if (!$result || empty($result)) {
    // Insert Skilltree Nodes
    // Root
    $db->query("INSERT INTO `skilltree_nodes` (`treeid`, `title`, `description`, `icon`, `parent`) VALUES (?, ?, ?, ?, NULL)");
    $db->execute([$skilltreeId, 'Skilltree', 'Carve your own path as a respectable mobster', 'root.png']);
    // Children
    $db->query("INSERT INTO `skilltree_nodes` (`treeid`, `title`, `description`, `icon`, `parent`, `rewards`) VALUES (?, ?, ?, ?, ?, ?)");
    $db->execute([$skilltreeId, 'Stealth', '-5% Chance to be caught by Police', 'stealth.png', 1, json_encode(['avoid_police' => 1.05])]);
    $db->execute([$skilltreeId, 'Picklocking', '-5% Chance to be caught by Police', 'picklocking.png', 2, json_encode(['avoid_police' => 1.05])]);
    $db->execute([$skilltreeId, 'Pocket Thief', '+10% Earnings in Back Alley', 'pocket_thief.png', 3, json_encode(['ba_earnings' => 1.10])]);
    $db->execute([$skilltreeId, 'Directionist Master', '+10% Earnings in Back Alley and Maze', 'directionist_master.png', 4, json_encode(['ba_earnings' => 1.10, 'maze_earnings' => 1.10])]);
    $db->execute([$skilltreeId, "Crow's Tools", '+25% Chance to escape Police after being caught', 'crows_tools.png', 3, json_encode(['escape_police' => 1.25])]);
    $db->execute([$skilltreeId, "Escapist", '+10% Earnings from Crimes', 'escapist.png', 3, json_encode(['crime_earnings' => 1.10])]);
    $db->execute([$skilltreeId, "Surveillance", '+25% Chance to escape Police after being caught', 'surveillance.png', 2, json_encode(['escape_police' => 1.25])]);
    $db->execute([$skilltreeId, "Camera Setup", '+25% Chance to Successfully Spy on a Player', 'camera_setup.png', 8, json_encode(['spy_chance' => 1.25])]);
    $db->execute([$skilltreeId, "Spyware Expert", '+10% Chance to Successfully Spy on a Player', 'spyware_expert.png', 9, json_encode(['spy_chance' => 1.10])]);
    $db->execute([$skilltreeId, "Preparation", '+5% Earnings from Crimes and -5% Chance to be caught by Police', 'preparation.png', 8, json_encode(['crime_earnings' => 1.05, 'avoid_police' => 1.05])]);

    $db->execute([$skilltreeId, 'Operations', '+5% Earnings from Missions', 'operations.png', 1, json_encode(['mission_earnings' => 1.05])]);
    $db->execute([$skilltreeId, 'Back Alley Luck', '+10% Chance to find Gold Rush in Back Alley', 'maze_expert.png', 12, json_encode(['ba_gold_rush_chance' => 1250])]);
    $db->execute([$skilltreeId, 'Gold Digger', '+10% Chance to find Gold Rush in Back Alley', 'maze_architect.png', 13, json_encode(['ba_gold_rush_chance' => 1250])]);
    $db->execute([$skilltreeId, 'Logistics', '+5% Earnings from Missions', 'logistics.png', 12, json_encode(['mission_earnings' => 1.05])]);
    $db->execute([$skilltreeId, 'Intel Broker', '+5% Earnings in Back Alley', 'intel_broker.png', 14, json_encode(['ba_earnings' => 1.05])]);
    $db->execute([$skilltreeId, 'Special Operations', '+5% Earnings from Operations', 'special_operations.png', 14, json_encode(['operations_earnings' => 1.05])]);
    $db->execute([$skilltreeId, 'Operations Director', '+5% Earnings from Missions & Operations', 'operations_director.png', 17, json_encode(['mission_earnings' => 1.05, 'operations_earnings' => 1.05])]);
    $db->execute([$skilltreeId, 'Recon Specialist', '+10% Drop Rate in Raids', 'recon_specialist.png', 12, json_encode(['raid_drop_rate' => 1.10])]);
    $db->execute([$skilltreeId, 'Extraction Expert', '+5% Drop Rate in Raids', 'extraction_expert.png', 19, json_encode(['raid_drop_rate' => 1.05])]);
    $db->execute([$skilltreeId, 'Stamina', '+5% Stat Bonus from Gym', 'stamina.png', 19, json_encode(['gym_boost' => 1.05])]);

    $db->execute([$skilltreeId, 'Brawler', '+5% Stat Gain from Gym', 'brawler.png', 1, json_encode(['gym_boost' => 1.05])]);
    $db->execute([$skilltreeId, 'Power Lifter', '+5% Stat Gain from Gym', 'power_lifter.png', 22, json_encode(['gym_boost' => 1.05])]);
    $db->execute([$skilltreeId, 'Raider', '+5% Drop Rate from Raids', 'raider.png', 22, json_encode(['raid_drop_rate' => 1.05])]);
    $db->execute([$skilltreeId, 'Strike Agent', '+5% Drop Rate from Raids', 'strike_agent.png', 24, json_encode(['raid_drop_rate' => 1.05])]);
    $db->execute([$skilltreeId, 'Raid Architect', '+5% Drop Rate from Raids', 'raid_architect.png', 25, json_encode(['raid_drop_rate' => 1.05])]);
    $db->execute([$skilltreeId, 'Tamer', '+5% Pet Stat Gain from Pet Gym', 'tamer.png', 22, json_encode(['pet_gym_boost' => 1.05])]);
    $db->execute([$skilltreeId, 'Beast Master', '+10% Pet Stat Gain from Pet Gym', 'beast_master.png', 27, json_encode(['pet_gym_boost' => 1.10])]);
    $db->execute([$skilltreeId, 'Beast Mind', '+10% Drop Rate from Raids', 'beast_mind.png', 28, json_encode(['raid_drop_rate' => 1.10])]);
    $db->execute([$skilltreeId, 'Street Brawler', '+10% Earnings in Back Alley', 'street_brawler.png', 22, json_encode(['ba_earnings' => 1.10])]);
    $db->execute([$skilltreeId, 'Street Veteran', '+10% Earnings in Back Alley', 'street_veteran.png', 30, json_encode(['ba_earnings' => 1.10])]);

    // Insert Stealth Skill Tree
    // Root
    // $db->query("INSERT INTO `skilltree_nodes` (`treeid`, `title`, `description`, `icon`, `parent`) VALUES (?, ?, ?, ?, NULL)");
    // $db->execute([1, 'Stealth', '-5% Chance to be caught by Police', 'stealth.png']);
    // Children
    // $db->query("INSERT INTO `skilltree_nodes` (`treeid`, `title`, `description`, `icon`, `parent`) VALUES (?, ?, ?, ?, ?)");
    // $db->execute([1, 'Picklocking', '-5% Chance to be caught by Police', 'picklocking.png', 1]);
    // $db->execute([1, 'Pocket Thief', '+10% Earnings from Back Alley', 'picklocking.png', 2]);
    // $db->execute([1, 'Directionist Master', '+10% Earnings from Back Alley and Maze', 'directionist_master.png', 3]);
    // $db->execute([1, "Crow's Tools", '+25% Chance to escape Police after being caught', 'crows_tools.png', 2]);
    // $db->execute([1, "Escapist", '+10% Earnings from Crimes', 'escapist.png', 2]);
    // $db->execute([1, "Surveillance", '+25% Chance to escape Police after being caught', 'surveillance.png', 1]);
    // $db->execute([1, "Camera Setup", '+25% Chance to Successfully Spy on a Player', 'camera_setup.png', 7]);
    // $db->execute([1, "Spyware Expert", '+10% Chance to Successfully Spy on a Player', 'spyware_expert.png', 8]);
    // $db->execute([1, "Preparation", '+5% Earnings from Crimes and -5% Chance to be caught by Police', 'preparation.png', 7]);

    // Insert Operations Skill Tree
    // Root
    // $db->query("INSERT INTO `skilltree_nodes` (`treeid`, `title`, `description`, `icon`, `parent`) VALUES (?, ?, ?, ?, NULL)");
    // $db->execute([2, 'Operations', '+5% Earnings from Missions', 'operations.png']);
    // Children
    // $db->query("INSERT INTO `skilltree_nodes` (`treeid`, `title`, `description`, `icon`, `parent`) VALUES (?, ?, ?, ?, ?)");
    // $db->execute([2, 'Maze Expert', '+10% Maze Regeneration Rate', 'maze_expert.png', 11]);
    // $db->execute([2, 'Maze Architect', '+20 Maze Turns Upper Limit', 'maze_architect.png', 12]);
    // $db->execute([2, 'Logistics', '+5% Earnings from Missions', 'logistics.png', 11]);
    // $db->execute([2, 'Intel Broker', '+5% Drop Rate in Back Alley', 'intel_broker.png', 13]);
    // $db->execute([2, 'Special Operations', '+5% Earnings from Operations', 'special_operations.png', 13]);
    // $db->execute([2, 'Operations Director', '+5% Earnings from Missions & Operations', 'operations_director.png', 16]);
    // $db->execute([2, 'Recon Specialist', '+10% Drop Rate in Successful Raids', 'recon_specialist.png', 11]);
    // $db->execute([2, 'Extraction Expert', '+5% Drop Rate in Successful Raids', 'extraction_expert.png', 18]);
    // $db->execute([2, 'Stamina', '+5% Stat Bonus from Gym', 'stamina.png', 18]);

    // Insert Brawler Skill Tree
    // Root
    // $db->query("INSERT INTO `skilltree_nodes` (`treeid`, `title`, `description`, `icon`, `parent`) VALUES (?, ?, ?, ?, NULL)"); // ID 21
    // $db->execute([3, 'Brawler', '+5% Stat Gain from Gym', 'brawler.png']);
    // Children
    // $db->query("INSERT INTO `skilltree_nodes` (`treeid`, `title`, `description`, `icon`, `parent`) VALUES (?, ?, ?, ?, ?)");
    // $db->execute([3, 'Power Lifter', '+5% Stat Gain from Gym', 'power_lifter.png', 21]);
    // $db->execute([3, 'Raider', '+5% Drop Rate from Raids', 'raider.png', 21]);
    // $db->execute([3, 'Strike Agent', '+5% Drop Rate from Raids', 'strike_agent.png', 23]);
    // $db->execute([3, 'Raid Architect', '+5% Drop Rate from Raids', 'raid_architect.png', 24]);
    // $db->execute([3, 'Tamer', '+5% Pet Stat Gain from Pet Gym', 'tamer.png', 21]);
    // $db->execute([3, 'Beast Master', '+10% Pet Stat Gain from Pet Gym', 'beast_master.png', 26]);
    // $db->execute([3, 'Beast Mind', '+10% Drop Rate from Raids', 'beast_mind.png', 27]);
    // $db->execute([3, 'Street Brawler', '+10% Drop Rate in Back Alley', 'street_brawler.png', 21]);
    // $db->execute([3, 'Street Veteran', '+10% Drop Rate in Back Alley', 'street_veteran.png', 29]);
}