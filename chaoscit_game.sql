-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Vært: localhost:3306
-- Genereringstid: 15. 10 2025 kl. 14:40:45
-- Serverversion: 10.11.14-MariaDB-ubu2204
-- PHP-version: 8.4.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `chaoscit_game`
--

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `5050log`
--

CREATE TABLE `5050log` (
  `better` int(11) NOT NULL,
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `winner` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `currency` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `active_gang_missions`
--

CREATE TABLE `active_gang_missions` (
  `id` int(11) NOT NULL,
  `gangid` int(11) NOT NULL,
  `kills` int(11) DEFAULT 0,
  `busts` int(11) DEFAULT 0,
  `crimes` int(11) DEFAULT 0,
  `mugs` int(11) DEFAULT 0,
  `backalleys` int(11) NOT NULL DEFAULT 0,
  `completed` tinyint(1) DEFAULT 0,
  `time` int(11) DEFAULT 0,
  `mission_id` int(11) NOT NULL,
  `end_time` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `active_raids`
--

CREATE TABLE `active_raids` (
  `id` int(11) NOT NULL,
  `boss_id` int(11) NOT NULL,
  `summoned_by` int(11) NOT NULL,
  `difficulty` enum('Easy','Medium','Hard') NOT NULL,
  `summoned_at` datetime DEFAULT current_timestamp(),
  `completed` int(11) NOT NULL DEFAULT 0,
  `raid_type` varchar(20) DEFAULT 'Public',
  `used_pass` tinyint(1) NOT NULL DEFAULT 0,
  `used_booster` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `activityqueue`
--

CREATE TABLE `activityqueue` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `rid` int(11) NOT NULL,
  `type` varchar(30) NOT NULL DEFAULT 'crime',
  `start` int(11) NOT NULL,
  `end` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `activityrewards`
--

CREATE TABLE `activityrewards` (
  `id` int(11) NOT NULL,
  `rewards` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `activity_contest`
--

CREATE TABLE `activity_contest` (
  `id` int(11) NOT NULL,
  `type` varchar(200) NOT NULL,
  `type_value` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `addptmarketlog`
--

CREATE TABLE `addptmarketlog` (
  `id` int(11) NOT NULL,
  `owner` int(11) NOT NULL DEFAULT 0,
  `amount` bigint(20) NOT NULL DEFAULT 0,
  `price` bigint(20) NOT NULL DEFAULT 0,
  `type` int(11) NOT NULL DEFAULT 1,
  `timestamp` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `ads`
--

CREATE TABLE `ads` (
  `id` int(11) NOT NULL,
  `timestamp` varchar(255) NOT NULL DEFAULT '',
  `poster` int(11) NOT NULL,
  `title` varchar(100) NOT NULL DEFAULT '',
  `message` text DEFAULT NULL,
  `displaymins` int(11) NOT NULL DEFAULT 0,
  `glow` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `advent_calendar`
--

CREATE TABLE `advent_calendar` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date_opened` date NOT NULL,
  `item_awarded` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `ajax_chat_online`
--

CREATE TABLE `ajax_chat_online` (
  `userID` int(11) NOT NULL,
  `userName` varchar(64) NOT NULL,
  `userRole` int(11) NOT NULL,
  `channel` int(11) NOT NULL,
  `dateTime` datetime NOT NULL,
  `ip` varbinary(16) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `attackladder`
--

CREATE TABLE `attackladder` (
  `id` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `spot` int(11) NOT NULL,
  `last_attack` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `attacklog`
--

CREATE TABLE `attacklog` (
  `id` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL DEFAULT 0,
  `attacker` int(11) NOT NULL DEFAULT 0,
  `defender` int(11) NOT NULL DEFAULT 0,
  `winner` int(11) NOT NULL DEFAULT 0,
  `exp` int(11) NOT NULL DEFAULT 0,
  `money` int(11) NOT NULL DEFAULT 0,
  `attackerHide` tinyint(4) NOT NULL DEFAULT 0,
  `defenderHide` tinyint(4) NOT NULL DEFAULT 0,
  `active` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `attack_turn_log`
--

CREATE TABLE `attack_turn_log` (
  `id` int(11) NOT NULL,
  `attack_id` int(11) NOT NULL,
  `attacking_user_id` int(11) NOT NULL,
  `defending_user_id` int(11) NOT NULL,
  `is_first_attack` tinyint(1) NOT NULL DEFAULT 0,
  `is_hit` tinyint(1) NOT NULL DEFAULT 0,
  `is_critical_hit` tinyint(1) NOT NULL DEFAULT 0,
  `is_counter_attack` tinyint(1) NOT NULL DEFAULT 0,
  `damage` bigint(20) NOT NULL,
  `yourhp` bigint(20) NOT NULL,
  `theirhp` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `attack_v2`
--

CREATE TABLE `attack_v2` (
  `id` int(11) NOT NULL,
  `attack_time` bigint(20) NOT NULL,
  `attacking_user_id` int(11) NOT NULL,
  `defending_user_id` int(11) NOT NULL,
  `winning_user_id` int(11) NOT NULL,
  `exp` bigint(20) NOT NULL,
  `money` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `attlog`
--

CREATE TABLE `attlog` (
  `id` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL DEFAULT 0,
  `gangid` int(11) NOT NULL DEFAULT 0,
  `attacker` int(11) NOT NULL DEFAULT 0,
  `defender` int(11) NOT NULL DEFAULT 0,
  `winner` int(11) NOT NULL DEFAULT 0,
  `gangexp` int(11) NOT NULL,
  `active` int(11) NOT NULL,
  `respect` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `auction_house`
--

CREATE TABLE `auction_house` (
  `auction_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `starting_bid` int(11) NOT NULL DEFAULT 0,
  `min_bid` decimal(10,2) NOT NULL,
  `current_bid` decimal(10,2) DEFAULT 0.00,
  `highest_bidder_id` int(11) DEFAULT NULL,
  `end_time` bigint(20) NOT NULL DEFAULT 0,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `winner` int(11) DEFAULT NULL,
  `current_bidder_id` int(11) DEFAULT NULL,
  `status` enum('active','finished') NOT NULL DEFAULT 'active'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `autoclick_detection`
--

CREATE TABLE `autoclick_detection` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `page` varchar(255) DEFAULT NULL,
  `reason` varchar(255) NOT NULL,
  `referer` varchar(255) DEFAULT NULL,
  `request_uri` varchar(255) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `ip` varchar(255) DEFAULT NULL,
  `count` int(11) DEFAULT NULL,
  `last_meta` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `badges`
--

CREATE TABLE `badges` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` varchar(1000) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Data dump for tabellen `badges`
--

INSERT INTO `badges` (`id`, `name`, `image`, `description`) VALUES
(1, 'Easter 2025', 'css/images/2025/easter_2025.png', 'Earned during Easter 2025 by trading in a few common easter eggs.'),
(2, 'No life during Easter 2025', 'css/images/2025/no_life_easter_2025.png', 'Limited achievement from Easter 2025, was traded in for a wild amount of 250 Ultra Rare Eggs.\r\n\r\nYou would\'ve had to have no life during Easter 2025 to exchange for this achievement.'),
(3, 'Legendary Looter', 'css/images/2025/legendary_looter.png', 'Purchased with hard-earned Raid Points gained from defeating bosses alongside your team. A costly but prestigious achievement that marks you as a top-tier raider.');

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `baltlepass_users`
--

CREATE TABLE `baltlepass_users` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `tier` int(11) NOT NULL DEFAULT 1,
  `exp` int(11) NOT NULL DEFAULT 0,
  `collected` int(11) NOT NULL DEFAULT 0,
  `paid` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `banksettings`
--

CREATE TABLE `banksettings` (
  `userid` smallint(6) NOT NULL,
  `format` enum('us','uk') NOT NULL,
  `limit` smallint(6) NOT NULL,
  `show` enum('all','money','points','withs','deps') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Data dump for tabellen `banksettings`
--

INSERT INTO `banksettings` (`userid`, `format`, `limit`, `show`) VALUES
(5, 'uk', 25, 'all'),
(6, 'uk', 25, 'all'),
(24, 'uk', 25, 'all'),
(134, 'uk', 25, 'all'),
(137, 'uk', 25, 'all'),
(4, 'us', 25, 'points'),
(96, 'uk', 25, 'all'),
(162, 'uk', 25, 'all'),
(162, 'us', 25, 'all'),
(218, 'uk', 25, 'all'),
(4, 'us', 25, 'all'),
(4, 'us', 25, 'all'),
(4, 'us', 25, 'all'),
(35, 'uk', 25, 'all'),
(35, 'us', 25, 'all'),
(35, 'uk', 25, 'all'),
(4, 'us', 25, 'all'),
(84, 'us', 25, 'points'),
(243, 'uk', 25, 'all'),
(162, 'uk', 25, 'money'),
(162, 'uk', 25, 'all'),
(7, 'us', 25, 'points'),
(4, 'us', 25, 'money'),
(4, 'us', 25, 'all'),
(84, 'us', 25, 'all'),
(84, 'us', 25, 'all'),
(84, 'us', 25, 'all'),
(140, 'us', 25, 'deps'),
(140, 'us', 25, 'money'),
(140, 'us', 25, 'all'),
(187, 'us', 25, 'points'),
(187, 'us', 25, 'all'),
(7, 'us', 25, 'all'),
(7, 'us', 25, 'points'),
(7, 'us', 25, 'money'),
(7, 'us', 25, 'points'),
(84, 'us', 25, 'all'),
(40, 'us', 2, 'all'),
(40, 'us', 0, 'all'),
(40, 'us', 0, 'all'),
(40, 'us', 0, 'all'),
(40, 'us', 1, 'all'),
(40, 'us', 10, 'all'),
(40, 'us', 100, 'all'),
(40, 'us', 100, 'all'),
(40, 'us', 25, 'all'),
(324, 'uk', 25, 'all'),
(53, 'us', 25, 'points'),
(358, 'uk', 25, 'all'),
(187, 'us', 25, 'all'),
(176, 'uk', 25, 'all'),
(40, 'us', 25, 'all'),
(40, 'us', 25, 'all'),
(386, 'uk', 25, 'all'),
(386, 'us', 25, 'all'),
(4, 'us', 25, 'money'),
(389, 'uk', 25, 'all'),
(413, 'uk', 25, 'all'),
(421, 'uk', 25, 'all'),
(40, 'us', 25, 'all'),
(423, 'uk', 25, 'all'),
(40, 'us', 25, 'all'),
(493, 'uk', 25, 'all'),
(493, 'us', 25, 'all'),
(220, 'uk', 25, 'all'),
(53, 'us', 25, 'money'),
(53, 'us', 25, 'all'),
(40, 'us', 25, 'all'),
(40, 'us', 0, 'all'),
(40, 'us', 25, 'all'),
(4, 'us', 25, 'all'),
(557, 'uk', 25, 'all'),
(4, 'uk', 25, 'points'),
(4, 'uk', 25, 'all'),
(162, 'uk', 25, 'points'),
(184, 'uk', 25, 'all'),
(184, 'us', 25, 'all'),
(40, 'us', 25, 'all'),
(40, 'us', 25, 'all'),
(40, 'us', 25, 'all'),
(4, 'us', 25, 'money'),
(4, 'us', 25, 'deps'),
(143, 'uk', 25, 'all'),
(143, 'us', 25, 'all'),
(143, 'uk', 25, 'all'),
(143, 'us', 25, 'all'),
(143, 'us', 25, 'deps'),
(143, 'us', 25, 'withs'),
(629, 'uk', 25, 'all'),
(629, 'uk', 25, 'deps'),
(629, 'uk', 25, 'all'),
(629, 'uk', 25, 'deps'),
(390, 'uk', 25, 'all'),
(249, 'us', 25, 'withs'),
(249, 'us', 25, 'withs'),
(390, 'uk', 25, 'money'),
(638, 'uk', 25, 'all'),
(97, 'us', 25, 'money'),
(699, 'uk', 25, 'all'),
(699, 'us', 25, 'all'),
(162, 'uk', 25, 'withs'),
(40, 'us', 25, 'all'),
(40, 'us', 12, 'all'),
(40, 'us', 102, 'all'),
(53, 'us', 25, 'money'),
(97, 'us', 25, 'points'),
(40, 'us', 25, 'all'),
(97, 'us', 25, 'points'),
(699, 'uk', 25, 'withs'),
(699, 'uk', 25, 'points'),
(699, 'uk', 25, 'all'),
(187, 'us', 25, 'money'),
(97, 'us', 25, 'points'),
(40, 'us', 25, 'all'),
(162, 'us', 25, 'all'),
(162, 'uk', 25, 'all'),
(162, 'us', 25, 'all'),
(162, 'uk', 25, 'all'),
(162, 'us', 25, 'all'),
(649, 'uk', 25, 'all'),
(649, 'us', 25, 'all'),
(649, 'uk', 25, 'all'),
(97, 'us', 25, 'points'),
(162, 'uk', 25, 'deps'),
(855, 'us', 25, 'points'),
(855, 'us', 25, 'deps'),
(855, 'us', 25, 'withs'),
(855, 'us', 25, 'all'),
(855, 'us', 25, 'all'),
(811, 'uk', 25, 'all'),
(855, 'us', 25, 'all'),
(855, 'us', 25, 'all'),
(855, 'uk', 25, 'all'),
(855, 'us', 25, 'all'),
(97, 'us', 25, 'points'),
(24, 'uk', 25, 'points'),
(87, 'us', 25, 'money'),
(875, 'us', 25, 'deps'),
(87, 'us', 25, 'all'),
(87, 'us', 25, 'points'),
(40, 'us', 25, 'all'),
(40, 'us', 25, 'all'),
(87, 'us', 25, 'points'),
(61, 'uk', 25, 'all'),
(61, 'us', 25, 'all'),
(187, 'us', 25, 'deps'),
(187, 'us', 25, 'money'),
(187, 'us', 25, 'all'),
(900, 'us', 25, 'points'),
(900, 'us', 25, 'deps'),
(521, 'uk', 25, 'all'),
(521, 'us', 25, 'all'),
(4, 'us', 25, 'money'),
(900, 'us', 25, 'money'),
(187, 'us', 25, 'deps'),
(187, 'us', 25, 'money'),
(187, 'us', 25, 'money'),
(187, 'us', 25, 'points'),
(187, 'us', 25, 'money'),
(900, 'us', 25, 'money'),
(886, 'us', 25, 'money'),
(900, 'us', 25, 'money'),
(900, 'us', 25, 'points'),
(900, 'us', 25, 'money'),
(900, 'us', 25, 'money'),
(900, 'us', 25, 'money'),
(940, 'uk', 25, 'all'),
(40, 'us', 20, 'all'),
(40, 'us', 200, 'all'),
(7, 'us', 25, 'deps'),
(7, 'us', 25, 'points'),
(957, 'uk', 25, 'all'),
(53, 'us', 25, 'money'),
(900, 'us', 25, 'money'),
(900, 'us', 25, 'money'),
(162, 'uk', 25, 'points'),
(162, 'uk', 25, 'all'),
(162, 'uk', 25, 'points'),
(162, 'uk', 25, 'all'),
(87, 'us', 25, 'points'),
(187, 'us', 25, 'all'),
(4, 'us', 25, 'money'),
(4, 'us', 25, 'money'),
(4, 'us', 25, 'money'),
(162, 'uk', 25, 'points'),
(162, 'uk', 25, 'all'),
(131, 'us', 25, 'points'),
(131, 'us', 25, 'all'),
(131, 'us', 25, 'all'),
(131, 'us', 25, 'all'),
(900, 'us', 25, 'money'),
(131, 'us', 25, 'all'),
(131, 'us', 25, 'all'),
(131, 'us', 25, 'all'),
(162, 'uk', 25, 'points'),
(131, 'us', 25, 'all'),
(131, 'uk', 25, 'points'),
(131, 'uk', 25, 'all'),
(131, 'us', 25, 'all'),
(87, 'us', 25, 'deps'),
(87, 'us', 25, 'money'),
(87, 'us', 25, 'deps'),
(87, 'us', 25, 'points'),
(712, 'uk', 25, 'all'),
(712, 'us', 25, 'all'),
(712, 'uk', 25, 'all'),
(712, 'us', 25, 'all'),
(131, 'us', 25, 'all'),
(131, 'us', 25, 'all'),
(87, 'us', 25, 'points'),
(900, 'us', 25, 'money'),
(131, 'us', 25, 'all'),
(17, 'us', 25, 'points'),
(1100, 'us', 2, 'all'),
(1100, 'us', 0, 'all'),
(1100, 'us', 1, 'all'),
(1100, 'us', 10, 'all'),
(1100, 'us', 100, 'all'),
(1116, 'uk', 25, 'all'),
(1116, 'us', 25, 'all'),
(1116, 'uk', 25, 'all'),
(1098, 'uk', 25, 'all'),
(1205, 'uk', 25, 'all'),
(1205, 'us', 25, 'all'),
(97, 'us', 25, 'points'),
(97, 'us', 25, 'points'),
(97, 'us', 25, 'points'),
(97, 'us', 25, 'points'),
(97, 'us', 25, 'points'),
(131, 'us', 25, 'all'),
(1100, 'uk', 2, 'all'),
(1100, 'uk', 2, 'all'),
(1100, 'us', 2, 'all'),
(162, 'uk', 25, 'points'),
(97, 'us', 25, 'points'),
(1216, 'uk', 25, 'all'),
(97, 'us', 25, 'points'),
(97, 'us', 25, 'points'),
(40, 'us', 2, 'all'),
(40, 'us', 52, 'all'),
(40, 'us', 502, 'all'),
(40, 'us', 52, 'all'),
(40, 'us', 2, 'all'),
(40, 'us', 0, 'all'),
(40, 'us', 5, 'all'),
(40, 'us', 50, 'all'),
(40, 'us', 0, 'all'),
(689, 'us', 2, 'all'),
(689, 'us', 0, 'all'),
(689, 'us', 1, 'all'),
(689, 'us', 10, 'all'),
(53, 'us', 25, 'money'),
(53, 'us', 25, 'money');

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `bank_log`
--

CREATE TABLE `bank_log` (
  `id` int(11) NOT NULL,
  `userid` smallint(6) NOT NULL,
  `amount` bigint(20) NOT NULL,
  `action` enum('mdep','mwith','pdep','pwith') NOT NULL,
  `newbalance` bigint(20) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `hand` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `bans`
--

CREATE TABLE `bans` (
  `banid` int(11) NOT NULL,
  `id` int(11) NOT NULL DEFAULT 0,
  `bannedby` int(11) NOT NULL,
  `type` varchar(6) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `days` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `battlepass`
--

CREATE TABLE `battlepass` (
  `id` int(11) NOT NULL,
  `tier` int(11) NOT NULL,
  `item` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `paid` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Data dump for tabellen `battlepass`
--

INSERT INTO `battlepass` (`id`, `tier`, `item`, `qty`, `type`, `paid`) VALUES
(1, 1, 1, 1, 'money', 0),
(2, 0, 1, 100, 'points', 1),
(3, 1, 1, 100, 'points', 1);

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `battle_members`
--

CREATE TABLE `battle_members` (
  `id` int(11) NOT NULL,
  `bmemberUser` int(11) NOT NULL,
  `bmemberLadder` int(11) NOT NULL,
  `bmemberScore` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `bbusers`
--

CREATE TABLE `bbusers` (
  `userid` int(11) NOT NULL,
  `level` int(11) NOT NULL DEFAULT 0,
  `referrals` int(11) NOT NULL DEFAULT 0,
  `attackswon` int(11) NOT NULL DEFAULT 0,
  `attackslost` int(11) NOT NULL DEFAULT 0,
  `defendwon` int(11) NOT NULL DEFAULT 0,
  `defendlost` int(11) NOT NULL DEFAULT 0,
  `spies` int(11) NOT NULL DEFAULT 0,
  `busts` int(11) NOT NULL DEFAULT 0,
  `donator` int(11) NOT NULL DEFAULT 0,
  `crimes` float(11,4) NOT NULL DEFAULT 0.0000,
  `mugs` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `bids`
--

CREATE TABLE `bids` (
  `bid_id` int(11) NOT NULL,
  `auction_id` int(11) NOT NULL,
  `bidder_id` int(11) NOT NULL,
  `bid_amount` decimal(10,2) NOT NULL,
  `bid_time` datetime NOT NULL,
  `is_anonymous` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `bloodbath`
--

CREATE TABLE `bloodbath` (
  `id` int(11) NOT NULL,
  `endtime` int(11) NOT NULL,
  `winners` longtext NOT NULL,
  `is_paid` tinyint(1) DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `bomb_protections`
--

CREATE TABLE `bomb_protections` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `protection` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `bonuslogs`
--

CREATE TABLE `bonuslogs` (
  `ID` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `Amount` int(11) NOT NULL,
  `Time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `bosses`
--

CREATE TABLE `bosses` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `level` int(11) NOT NULL,
  `image_link` varchar(255) DEFAULT NULL,
  `stat_limit` int(11) NOT NULL,
  `tier` enum('Easy','Medium','Hard') NOT NULL,
  `hp` int(11) NOT NULL,
  `available_unixtimestamp` bigint(20) DEFAULT NULL,
  `maxraiders` int(11) NOT NULL DEFAULT 0,
  `tokencost` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Data dump for tabellen `bosses`
--

INSERT INTO `bosses` (`id`, `name`, `level`, `image_link`, `stat_limit`, `tier`, `hp`, `available_unixtimestamp`, `maxraiders`, `tokencost`, `is_active`) VALUES
(1, 'The Godfather', 1, 'css/images/2025/the_godfather.png', 15, 'Easy', 50, NULL, 5, 1, 1),
(17, 'The BEAST', 100, 'css/images/2025/the_beast.png', 250000, 'Easy', 100, NULL, 5, 2, 1),
(18, 'Evil Pirate', 300, 'css/images/2025/evil_pirate_boss.png', 1000000, 'Hard', 300, NULL, 5, 5, 1),
(19, 'Dread Captain Shadow', 500, 'css/images/2025/dread_captain.png', 1000000000, 'Hard', 500, NULL, 5, 10, 1),
(20, 'Robobot', 100, 'css/images/NewGameImages/robobot.png', 2000000000, 'Hard', 500, NULL, 5, 101, 1),
(21, 'Dracula', 1, 'css/images/2025/dracula.png', 2000000000, 'Hard', 500, NULL, 5, 1, 1),
(22, 'Don Santa', 1000000, 'css/images/NewGameImages/santa.png', 2000000000, 'Hard', 500, NULL, 5, 750000000, 0),
(23, 'Madam Yolk', 100, 'css/images/2025/madam_yolk.png', 2000000000, 'Hard', 500, 1745575200, 5, 500000, 1),
(24, 'Don Egghopper', 50000, 'css/images/2025/don_egghopper.png', 2000000000, 'Hard', 1000, 1745575200, 5, 10000, 0),
(25, 'Omeggatron', 500000, 'css/images/2025/omeggatron.png', 2000000000, 'Hard', 500, 1745575200, 3, 1000000, 0),
(26, 'The Janitor', 100, 'css/images/2025/the_janitor.png', 2000000000, 'Hard', 500, NULL, 5, 1, 1),
(29, 'Samhain', 100, 'css/images/2025/samhain.png', 2000000000, 'Hard', 500, NULL, 3, 1, 0);

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `bosshits`
--

CREATE TABLE `bosshits` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `boss_id` int(11) DEFAULT NULL,
  `damage_dealt` int(11) DEFAULT NULL,
  `hit_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `bp_category`
--

CREATE TABLE `bp_category` (
  `id` int(11) NOT NULL,
  `month_year` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Data dump for tabellen `bp_category`
--

INSERT INTO `bp_category` (`id`, `month_year`) VALUES
(1, '05-2024'),
(2, '06-2024'),
(3, '07-2024'),
(4, '08-2024'),
(5, '09-2024'),
(6, '10-2024'),
(7, '11-2024'),
(8, '12-2024'),
(9, '01-2025'),
(10, '02-2025'),
(11, '03-2025'),
(12, '04-2025'),
(13, '05-2025'),
(14, '06-2025'),
(15, '07-2025'),
(16, '08-2025'),
(17, '09-2025'),
(18, '10-2025');

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `bp_category_challenges`
--

CREATE TABLE `bp_category_challenges` (
  `id` int(11) NOT NULL,
  `bp_category_id` int(11) NOT NULL,
  `type` varchar(200) NOT NULL,
  `amount` int(11) NOT NULL DEFAULT 0,
  `prize` int(11) NOT NULL DEFAULT 0,
  `is_premium` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Data dump for tabellen `bp_category_challenges`
--

INSERT INTO `bp_category_challenges` (`id`, `bp_category_id`, `type`, `amount`, `prize`, `is_premium`) VALUES
(1, 1, 'crimes', 1000, 10, 0),
(2, 1, 'mugs', 500, 10, 0),
(3, 1, 'busts', 200, 10, 0),
(4, 1, 'backalley', 200, 10, 0),
(5, 1, 'trains', 1000, 10, 0),
(6, 1, 'crimes', 150000, 10, 0),
(7, 1, 'mugs', 5000, 10, 0),
(8, 1, 'busts', 6000, 10, 0),
(9, 1, 'backalley', 3000, 10, 0),
(10, 1, 'attacks', 5000, 10, 0),
(11, 1, 'trains', 50000, 10, 0),
(12, 1, 'crimes', 750000, 10, 0),
(13, 1, 'mugs', 7500, 10, 0),
(14, 1, 'busts', 9000, 10, 0),
(15, 1, 'backalley', 6000, 10, 0),
(16, 1, 'attacks', 10000, 10, 0),
(17, 1, 'trains', 250000, 10, 0),
(18, 1, 'crimes', 1500000, 10, 0),
(19, 1, 'mugs', 11000, 10, 0),
(20, 1, 'busts', 20000, 10, 0),
(21, 1, 'backalley', 8000, 10, 0),
(22, 1, 'attacks', 15000, 10, 0),
(23, 1, 'trains', 500000, 10, 0),
(24, 1, 'crimes', 1750000, 10, 0),
(25, 1, 'mugs', 14000, 10, 0),
(26, 1, 'busts', 22000, 10, 0),
(27, 1, 'backalley', 9000, 10, 0),
(28, 1, 'attacks', 17500, 10, 0),
(29, 1, 'trains', 600000, 10, 0),
(30, 1, 'crimes', 2000000, 10, 0),
(31, 1, 'mugs', 18000, 10, 0),
(32, 1, 'busts', 25000, 10, 0),
(33, 1, 'backalley', 13000, 10, 0),
(34, 1, 'attacks', 20000, 10, 0),
(35, 1, 'trains', 800000, 10, 0),
(36, 1, 'crimes', 2250000, 10, 0),
(37, 1, 'mugs', 20000, 10, 0),
(38, 1, 'busts', 30000, 10, 0),
(39, 1, 'backalley', 15000, 10, 0),
(40, 1, 'crimes', 2500000, 10, 0),
(41, 2, 'crimes', 10000, 10, 0),
(44, 2, 'crimes', 250000, 10, 0),
(45, 2, 'crimes', 750000, 10, 0),
(46, 2, 'crimes', 1500000, 10, 0),
(47, 2, 'crimes', 2000000, 10, 1),
(48, 2, 'crimes', 5000000, 10, 0),
(49, 2, 'crimes', 7500000, 10, 0),
(50, 2, 'crimes', 10000000, 10, 0),
(51, 2, 'crimes', 11000000, 10, 0),
(52, 2, 'crimes', 12500000, 10, 1),
(53, 2, 'crimes', 15000000, 10, 0),
(54, 2, 'crimes', 17500000, 10, 0),
(55, 2, 'crimes', 20000000, 10, 0),
(56, 2, 'crimes', 22000000, 10, 0),
(57, 2, 'crimes', 25000000, 10, 1),
(58, 2, 'crimes', 27500000, 10, 0),
(59, 2, 'crimes', 30000000, 10, 0),
(60, 2, 'crimes', 32500000, 10, 0),
(61, 2, 'crimes', 35000000, 10, 0),
(62, 2, 'crimes', 40000000, 10, 1),
(63, 2, 'attacks', 5000, 10, 0),
(64, 2, 'attacks', 10000, 10, 0),
(65, 2, 'attacks', 25000, 10, 0),
(66, 2, 'attacks', 30000, 10, 0),
(67, 2, 'attacks', 40000, 10, 1),
(68, 2, 'attacks', 50000, 10, 0),
(69, 2, 'attacks', 75000, 10, 0),
(70, 2, 'attacks', 90000, 10, 0),
(71, 2, 'attacks', 100000, 10, 0),
(72, 2, 'attacks', 125000, 10, 1),
(73, 2, 'attacks', 150000, 10, 0),
(74, 2, 'attacks', 200000, 10, 0),
(75, 2, 'attacks', 225000, 10, 0),
(76, 2, 'attacks', 250000, 10, 0),
(77, 2, 'attacks', 300000, 10, 1),
(78, 2, 'mugs', 2500, 10, 0),
(79, 2, 'mugs', 5000, 10, 0),
(80, 2, 'mugs', 10000, 10, 0),
(81, 2, 'mugs', 20000, 10, 0),
(82, 2, 'mugs', 25000, 10, 1),
(83, 2, 'mugs', 40000, 10, 0),
(84, 2, 'mugs', 60000, 10, 0),
(85, 2, 'mugs', 75000, 10, 0),
(86, 2, 'mugs', 90000, 10, 0),
(87, 2, 'mugs', 125000, 10, 1),
(88, 2, 'mugs', 150000, 10, 0),
(89, 2, 'mugs', 200000, 10, 0),
(90, 2, 'mugs', 220000, 10, 0),
(91, 2, 'mugs', 250000, 10, 0),
(92, 2, 'mugs', 275000, 10, 1),
(93, 2, 'mugs', 300000, 10, 0),
(94, 2, 'mugs', 325000, 10, 0),
(95, 2, 'mugs', 350000, 10, 0),
(96, 2, 'mugs', 375000, 10, 0),
(97, 2, 'mugs', 400000, 10, 1),
(98, 2, 'busts', 5000, 10, 0),
(99, 2, 'busts', 12000, 10, 0),
(100, 2, 'busts', 20000, 10, 0),
(101, 2, 'busts', 25000, 10, 0),
(102, 2, 'busts', 30000, 10, 1),
(103, 2, 'busts', 40000, 10, 0),
(104, 2, 'busts', 50000, 10, 0),
(105, 2, 'busts', 75000, 10, 0),
(106, 2, 'busts', 90000, 10, 0),
(107, 2, 'busts', 100000, 10, 1),
(108, 2, 'busts', 125000, 10, 0),
(109, 2, 'busts', 150000, 10, 0),
(110, 2, 'busts', 200000, 10, 0),
(111, 2, 'busts', 250000, 10, 0),
(112, 2, 'busts', 300000, 10, 1),
(113, 2, 'backalley', 2500, 10, 0),
(114, 2, 'backalley', 5000, 10, 0),
(115, 2, 'backalley', 10000, 10, 0),
(116, 2, 'backalley', 12500, 10, 0),
(117, 2, 'backalley', 15000, 10, 1),
(118, 2, 'backalley', 25000, 10, 0),
(119, 2, 'backalley', 27500, 10, 0),
(120, 2, 'backalley', 30000, 10, 0),
(121, 2, 'backalley', 35000, 10, 1),
(122, 2, 'backalley', 50000, 10, 0),
(123, 2, 'backalley', 60000, 10, 0),
(124, 2, 'backalley', 70000, 10, 0),
(125, 2, 'backalley', 75000, 10, 0),
(126, 2, 'backalley', 80000, 10, 1),
(127, 2, 'backalley', 90000, 10, 0),
(128, 2, 'backalley', 100000, 10, 0),
(129, 2, 'backalley', 110000, 10, 0),
(130, 2, 'backalley', 125000, 10, 0),
(131, 2, 'backalley', 175000, 10, 1),
(132, 2, 'trains', 100, 10, 0),
(133, 2, 'trains', 250, 10, 0),
(134, 2, 'trains', 500, 10, 0),
(135, 2, 'trains', 600, 10, 0),
(136, 2, 'trains', 750, 10, 1),
(137, 2, 'trains', 1000, 10, 0),
(138, 2, 'trains', 1500, 10, 0),
(139, 2, 'trains', 2500, 10, 0),
(140, 2, 'trains', 5000, 10, 0),
(141, 2, 'trains', 10000, 10, 1),
(142, 3, 'crimes', 10000, 10, 0),
(143, 3, 'crimes', 250000, 10, 0),
(144, 3, 'crimes', 750000, 10, 0),
(145, 3, 'crimes', 1500000, 10, 0),
(146, 3, 'crimes', 2000000, 10, 1),
(147, 3, 'crimes', 5000000, 10, 0),
(148, 3, 'crimes', 7500000, 10, 0),
(149, 3, 'crimes', 10000000, 10, 0),
(150, 3, 'crimes', 11000000, 10, 0),
(151, 3, 'crimes', 12500000, 10, 1),
(152, 3, 'crimes', 15000000, 10, 0),
(153, 3, 'crimes', 17500000, 10, 0),
(154, 3, 'crimes', 20000000, 10, 0),
(155, 3, 'crimes', 22000000, 10, 0),
(156, 3, 'crimes', 23000000, 10, 1),
(157, 3, 'crimes', 24000000, 10, 0),
(158, 3, 'crimes', 25000000, 10, 0),
(159, 3, 'crimes', 26000000, 10, 0),
(160, 3, 'crimes', 27000000, 10, 0),
(161, 3, 'crimes', 28000000, 10, 1),
(162, 3, 'attacks', 5000, 10, 0),
(163, 3, 'attacks', 10000, 10, 0),
(164, 3, 'attacks', 25000, 10, 0),
(165, 2, 'attacks', 30000, 10, 0),
(166, 3, 'attacks', 40000, 10, 1),
(167, 3, 'attacks', 50000, 10, 0),
(168, 3, 'attacks', 75000, 10, 0),
(169, 3, 'attacks', 90000, 10, 0),
(170, 3, 'attacks', 100000, 10, 0),
(171, 3, 'attacks', 125000, 10, 1),
(172, 3, 'attacks', 150000, 10, 0),
(173, 3, 'attacks', 200000, 10, 0),
(174, 3, 'attacks', 225000, 10, 0),
(175, 3, 'attacks', 250000, 10, 0),
(176, 3, 'attacks', 300000, 10, 1),
(177, 3, 'mugs', 1500, 10, 0),
(178, 3, 'mugs', 3000, 10, 0),
(179, 3, 'mugs', 7500, 10, 0),
(180, 3, 'mugs', 10000, 10, 0),
(181, 3, 'mugs', 15000, 10, 1),
(182, 3, 'mugs', 20000, 10, 0),
(183, 3, 'mugs', 25000, 10, 0),
(184, 3, 'mugs', 30000, 10, 0),
(185, 3, 'mugs', 40000, 10, 0),
(186, 3, 'mugs', 50000, 10, 1),
(187, 3, 'mugs', 60000, 10, 0),
(188, 3, 'mugs', 70000, 10, 0),
(189, 3, 'mugs', 80000, 10, 0),
(190, 3, 'mugs', 90000, 10, 0),
(191, 3, 'mugs', 100000, 10, 1),
(192, 3, 'mugs', 110000, 10, 0),
(193, 3, 'mugs', 125000, 10, 0),
(194, 3, 'mugs', 140000, 10, 0),
(195, 3, 'mugs', 150000, 10, 0),
(196, 3, 'mugs', 175000, 10, 1),
(197, 3, 'busts', 5000, 10, 0),
(198, 3, 'busts', 7500, 10, 0),
(199, 3, 'busts', 10000, 10, 0),
(200, 3, 'busts', 12500, 10, 0),
(201, 3, 'busts', 15000, 10, 1),
(202, 3, 'busts', 20000, 10, 0),
(203, 3, 'busts', 25000, 10, 0),
(204, 3, 'busts', 35000, 10, 0),
(205, 3, 'busts', 50000, 10, 0),
(206, 3, 'busts', 70000, 10, 1),
(207, 3, 'busts', 80000, 10, 0),
(208, 3, 'busts', 90000, 10, 0),
(209, 3, 'busts', 100000, 10, 0),
(210, 3, 'busts', 125000, 10, 0),
(211, 3, 'busts', 150000, 10, 1),
(212, 3, 'backalley', 2500, 10, 0),
(213, 3, 'backalley', 5000, 10, 0),
(214, 3, 'backalley', 10000, 10, 0),
(215, 3, 'backalley', 12500, 10, 0),
(216, 3, 'backalley', 15000, 10, 1),
(217, 3, 'backalley', 25000, 10, 0),
(218, 3, 'backalley', 27500, 10, 0),
(219, 3, 'backalley', 30000, 10, 0),
(220, 3, 'backalley', 35000, 10, 1),
(221, 3, 'backalley', 50000, 10, 0),
(222, 3, 'backalley', 55000, 10, 0),
(223, 3, 'backalley', 60000, 10, 0),
(224, 3, 'backalley', 65000, 10, 0),
(225, 3, 'backalley', 70000, 10, 1),
(226, 3, 'backalley', 75000, 10, 0),
(227, 3, 'backalley', 80000, 10, 0),
(228, 3, 'backalley', 85000, 10, 0),
(229, 3, 'backalley', 90000, 10, 0),
(230, 3, 'backalley', 100000, 10, 1),
(231, 3, 'trains', 100, 10, 0),
(232, 3, 'trains', 250, 10, 0),
(233, 3, 'trains', 500, 10, 0),
(234, 3, 'trains', 600, 10, 0),
(235, 3, 'trains', 750, 10, 1),
(236, 3, 'trains', 1000, 10, 0),
(237, 3, 'trains', 1500, 10, 0),
(238, 3, 'trains', 2500, 10, 0),
(239, 3, 'trains', 5000, 10, 0),
(240, 3, 'trains', 10000, 10, 1),
(241, 3, 'crimes', 30000000, 10, 0),
(242, 3, 'attacks', 350000, 10, 0),
(243, 3, 'mugs', 200000, 10, 0),
(244, 3, 'busts', 200000, 10, 1),
(245, 3, 'backalley', 125000, 10, 1),
(246, 3, 'trains', 25000, 10, 0),
(247, 4, 'crimes', 10000, 10, 0),
(248, 4, 'crimes', 250000, 10, 0),
(249, 4, 'crimes', 750000, 10, 0),
(250, 4, 'crimes', 1500000, 10, 0),
(251, 4, 'crimes', 2000000, 10, 1),
(252, 4, 'crimes', 5000000, 10, 0),
(253, 4, 'crimes', 7500000, 10, 0),
(254, 4, 'crimes', 10000000, 10, 0),
(255, 4, 'crimes', 11000000, 10, 0),
(256, 4, 'crimes', 12500000, 10, 1),
(257, 4, 'crimes', 15000000, 10, 0),
(258, 4, 'crimes', 17500000, 10, 0),
(259, 4, 'crimes', 20000000, 10, 0),
(260, 4, 'crimes', 22000000, 10, 0),
(261, 4, 'crimes', 23000000, 10, 1),
(262, 4, 'crimes', 24000000, 10, 0),
(263, 4, 'crimes', 25000000, 10, 0),
(264, 4, 'crimes', 26000000, 10, 0),
(265, 4, 'crimes', 27000000, 10, 0),
(266, 4, 'crimes', 28000000, 10, 1),
(267, 4, 'attacks', 5000, 10, 0),
(268, 4, 'attacks', 10000, 10, 0),
(269, 4, 'attacks', 25000, 10, 0),
(270, 4, 'attacks', 40000, 10, 1),
(271, 4, 'attacks', 50000, 10, 0),
(272, 4, 'attacks', 75000, 10, 0),
(273, 4, 'attacks', 90000, 10, 0),
(274, 4, 'attacks', 100000, 10, 0),
(275, 4, 'attacks', 125000, 10, 1),
(276, 4, 'attacks', 150000, 10, 0),
(277, 4, 'attacks', 200000, 10, 0),
(278, 4, 'attacks', 225000, 10, 0),
(279, 4, 'attacks', 250000, 10, 0),
(280, 4, 'attacks', 300000, 10, 1),
(281, 4, 'mugs', 1500, 10, 0),
(282, 4, 'mugs', 3000, 10, 0),
(283, 4, 'mugs', 7500, 10, 0),
(284, 4, 'mugs', 10000, 10, 0),
(285, 4, 'mugs', 15000, 10, 1),
(286, 4, 'mugs', 20000, 10, 0),
(287, 4, 'mugs', 25000, 10, 0),
(288, 4, 'mugs', 30000, 10, 0),
(289, 4, 'mugs', 40000, 10, 0),
(290, 4, 'mugs', 50000, 10, 1),
(291, 4, 'mugs', 60000, 10, 0),
(292, 4, 'mugs', 70000, 10, 0),
(293, 4, 'mugs', 80000, 10, 0),
(294, 4, 'mugs', 90000, 10, 0),
(295, 4, 'mugs', 100000, 10, 1),
(296, 4, 'mugs', 110000, 10, 0),
(297, 4, 'mugs', 125000, 10, 0),
(298, 4, 'mugs', 140000, 10, 0),
(299, 4, 'mugs', 150000, 10, 0),
(300, 4, 'mugs', 175000, 10, 1),
(301, 4, 'busts', 5000, 10, 0),
(302, 4, 'busts', 7500, 10, 0),
(303, 4, 'busts', 10000, 10, 0),
(304, 4, 'busts', 12500, 10, 0),
(305, 4, 'busts', 15000, 10, 1),
(306, 4, 'busts', 20000, 10, 0),
(307, 4, 'busts', 25000, 10, 0),
(308, 4, 'busts', 35000, 10, 0),
(309, 4, 'busts', 50000, 10, 0),
(310, 4, 'busts', 70000, 10, 1),
(311, 4, 'busts', 80000, 10, 0),
(312, 4, 'busts', 90000, 10, 0),
(313, 4, 'busts', 100000, 10, 0),
(314, 4, 'busts', 125000, 10, 0),
(315, 4, 'busts', 150000, 10, 1),
(316, 4, 'backalley', 2500, 10, 0),
(317, 4, 'backalley', 5000, 10, 0),
(318, 4, 'backalley', 10000, 10, 0),
(319, 4, 'backalley', 12500, 10, 0),
(320, 4, 'backalley', 15000, 10, 1),
(321, 4, 'backalley', 25000, 10, 0),
(322, 4, 'backalley', 27500, 10, 0),
(323, 4, 'backalley', 30000, 10, 0),
(324, 4, 'backalley', 35000, 10, 1),
(325, 4, 'backalley', 50000, 10, 0),
(326, 4, 'backalley', 55000, 10, 0),
(327, 4, 'backalley', 60000, 10, 0),
(328, 4, 'backalley', 65000, 10, 0),
(329, 4, 'backalley', 70000, 10, 1),
(330, 4, 'backalley', 75000, 10, 0),
(331, 4, 'backalley', 80000, 10, 0),
(332, 4, 'backalley', 85000, 10, 0),
(333, 4, 'backalley', 90000, 10, 0),
(334, 4, 'backalley', 100000, 10, 1),
(335, 4, 'trains', 100, 10, 0),
(336, 4, 'trains', 250, 10, 0),
(337, 4, 'trains', 500, 10, 0),
(338, 4, 'trains', 600, 10, 0),
(339, 4, 'trains', 750, 10, 1),
(340, 4, 'trains', 1000, 10, 0),
(341, 4, 'trains', 1500, 10, 0),
(342, 4, 'trains', 2500, 10, 0),
(343, 4, 'trains', 5000, 10, 0),
(344, 4, 'trains', 10000, 10, 1),
(345, 4, 'crimes', 30000000, 10, 0),
(346, 4, 'attacks', 350000, 10, 0),
(347, 4, 'mugs', 200000, 10, 0),
(348, 4, 'busts', 200000, 10, 1),
(349, 4, 'backalley', 125000, 10, 1),
(350, 4, 'trains', 25000, 10, 0),
(351, 4, 'crimes', 500000, 10, 0),
(352, 4, 'crimes', 1000000, 10, 0),
(353, 4, 'crimes', 3000000, 10, 0),
(354, 4, 'crimes', 32000000, 10, 1),
(355, 4, 'attacks', 2500, 10, 0),
(356, 4, 'attacks', 60000, 10, 0),
(357, 4, 'attacks', 175000, 10, 1),
(358, 4, 'mugs', 500, 10, 0),
(359, 4, 'busts', 225000, 10, 0),
(360, 4, 'busts', 1000, 10, 0),
(361, 4, 'busts', 2500, 10, 1),
(362, 4, 'backalley', 1000, 10, 0),
(363, 4, 'backalley', 20000, 10, 1),
(364, 4, 'backalley', 150000, 10, 0),
(365, 4, 'trains', 15000, 10, 0),
(366, 5, 'crimes', 10000, 10, 0),
(367, 5, 'crimes', 250000, 10, 0),
(368, 5, 'crimes', 750000, 10, 0),
(369, 5, 'crimes', 1500000, 10, 0),
(370, 5, 'crimes', 2000000, 10, 1),
(371, 5, 'crimes', 5000000, 10, 0),
(372, 5, 'crimes', 7500000, 10, 0),
(373, 5, 'crimes', 10000000, 10, 0),
(374, 5, 'crimes', 11000000, 10, 0),
(375, 5, 'crimes', 12500000, 10, 1),
(376, 5, 'crimes', 15000000, 10, 0),
(377, 5, 'crimes', 17500000, 10, 0),
(378, 5, 'crimes', 20000000, 10, 0),
(379, 5, 'crimes', 22000000, 10, 0),
(380, 5, 'crimes', 23000000, 10, 1),
(381, 5, 'crimes', 24000000, 10, 0),
(382, 5, 'crimes', 25000000, 10, 0),
(383, 5, 'crimes', 26000000, 10, 0),
(384, 5, 'crimes', 27000000, 10, 0),
(385, 5, 'crimes', 28000000, 10, 1),
(386, 5, 'attacks', 5000, 10, 0),
(387, 5, 'attacks', 10000, 10, 0),
(388, 5, 'attacks', 25000, 10, 0),
(389, 5, 'attacks', 40000, 10, 1),
(390, 5, 'attacks', 50000, 10, 0),
(391, 5, 'attacks', 75000, 10, 0),
(392, 5, 'attacks', 90000, 10, 0),
(393, 5, 'attacks', 100000, 10, 0),
(394, 5, 'attacks', 125000, 10, 1),
(395, 5, 'attacks', 150000, 10, 0),
(396, 5, 'attacks', 200000, 10, 0),
(397, 5, 'attacks', 225000, 10, 0),
(398, 5, 'attacks', 250000, 10, 0),
(399, 5, 'attacks', 300000, 10, 1),
(400, 5, 'mugs', 1500, 10, 0),
(401, 5, 'mugs', 3000, 10, 0),
(402, 5, 'mugs', 7500, 10, 0),
(403, 5, 'mugs', 10000, 10, 0),
(404, 5, 'mugs', 15000, 10, 1),
(405, 5, 'mugs', 20000, 10, 0),
(406, 5, 'mugs', 25000, 10, 0),
(407, 5, 'mugs', 30000, 10, 0),
(408, 5, 'mugs', 40000, 10, 0),
(409, 5, 'mugs', 50000, 10, 1),
(410, 5, 'mugs', 60000, 10, 0),
(411, 5, 'mugs', 70000, 10, 0),
(412, 5, 'mugs', 80000, 10, 0),
(413, 5, 'mugs', 90000, 10, 0),
(414, 5, 'mugs', 100000, 10, 1),
(415, 5, 'mugs', 110000, 10, 0),
(416, 5, 'mugs', 125000, 10, 0),
(417, 5, 'mugs', 140000, 10, 0),
(418, 5, 'mugs', 150000, 10, 0),
(419, 5, 'mugs', 175000, 10, 1),
(420, 5, 'busts', 5000, 10, 0),
(421, 5, 'busts', 7500, 10, 0),
(422, 5, 'busts', 10000, 10, 0),
(423, 5, 'busts', 12500, 10, 0),
(424, 5, 'busts', 15000, 10, 1),
(425, 5, 'busts', 20000, 10, 0),
(426, 5, 'busts', 25000, 10, 0),
(427, 5, 'busts', 35000, 10, 0),
(428, 5, 'busts', 50000, 10, 0),
(429, 5, 'busts', 70000, 10, 1),
(430, 5, 'busts', 80000, 10, 0),
(431, 5, 'busts', 90000, 10, 0),
(432, 5, 'busts', 100000, 10, 0),
(433, 5, 'busts', 125000, 10, 0),
(434, 5, 'busts', 150000, 10, 1),
(435, 5, 'backalley', 2500, 10, 0),
(436, 5, 'backalley', 5000, 10, 0),
(437, 5, 'backalley', 10000, 10, 0),
(438, 5, 'backalley', 12500, 10, 0),
(439, 5, 'backalley', 15000, 10, 1),
(440, 5, 'backalley', 25000, 10, 0),
(441, 5, 'backalley', 27500, 10, 0),
(442, 5, 'backalley', 30000, 10, 0),
(443, 5, 'backalley', 35000, 10, 1),
(444, 5, 'backalley', 50000, 10, 0),
(445, 5, 'backalley', 55000, 10, 0),
(446, 5, 'backalley', 60000, 10, 0),
(447, 5, 'backalley', 65000, 10, 0),
(448, 5, 'backalley', 70000, 10, 1),
(449, 5, 'backalley', 75000, 10, 0),
(450, 5, 'backalley', 80000, 10, 0),
(451, 5, 'backalley', 85000, 10, 0),
(452, 5, 'backalley', 90000, 10, 0),
(453, 5, 'backalley', 100000, 10, 1),
(454, 5, 'trains', 100, 10, 0),
(455, 5, 'trains', 250, 10, 0),
(456, 5, 'trains', 500, 10, 0),
(457, 5, 'trains', 600, 10, 0),
(458, 5, 'trains', 750, 10, 1),
(459, 5, 'trains', 1000, 10, 0),
(460, 5, 'trains', 1500, 10, 0),
(461, 5, 'trains', 2500, 10, 0),
(462, 5, 'trains', 5000, 10, 0),
(463, 5, 'trains', 10000, 10, 1),
(464, 5, 'crimes', 30000000, 10, 0),
(465, 5, 'attacks', 350000, 10, 0),
(466, 5, 'mugs', 200000, 10, 0),
(467, 5, 'busts', 200000, 10, 1),
(468, 5, 'backalley', 125000, 10, 1),
(469, 5, 'trains', 25000, 10, 0),
(470, 5, 'crimes', 500000, 10, 0),
(471, 5, 'crimes', 1000000, 10, 0),
(472, 5, 'crimes', 3000000, 10, 0),
(473, 5, 'crimes', 32000000, 10, 1),
(474, 5, 'attacks', 2500, 10, 0),
(475, 5, 'attacks', 60000, 10, 0),
(476, 5, 'attacks', 175000, 10, 1),
(477, 5, 'mugs', 500, 10, 0),
(478, 5, 'busts', 225000, 10, 0),
(479, 5, 'busts', 1000, 10, 0),
(480, 5, 'busts', 2500, 10, 1),
(481, 5, 'backalley', 1000, 10, 0),
(482, 5, 'backalley', 20000, 10, 1),
(483, 5, 'backalley', 150000, 10, 0),
(484, 5, 'trains', 15000, 10, 0),
(485, 6, 'crimes', 10000, 10, 0),
(486, 6, 'crimes', 250000, 10, 0),
(487, 6, 'crimes', 750000, 10, 0),
(488, 6, 'crimes', 1500000, 10, 0),
(489, 6, 'crimes', 2000000, 10, 1),
(490, 6, 'crimes', 5000000, 10, 0),
(491, 6, 'crimes', 7500000, 10, 0),
(492, 6, 'crimes', 10000000, 10, 0),
(493, 6, 'crimes', 11000000, 10, 0),
(494, 6, 'crimes', 12500000, 10, 1),
(495, 6, 'crimes', 15000000, 10, 0),
(496, 6, 'crimes', 17500000, 10, 0),
(497, 6, 'crimes', 20000000, 10, 0),
(498, 6, 'crimes', 22000000, 10, 0),
(499, 6, 'crimes', 23000000, 10, 1),
(500, 6, 'crimes', 24000000, 10, 0),
(501, 6, 'crimes', 25000000, 10, 0),
(502, 6, 'crimes', 26000000, 10, 0),
(503, 6, 'crimes', 27000000, 10, 0),
(504, 6, 'crimes', 28000000, 10, 1),
(505, 6, 'attacks', 5000, 10, 0),
(506, 6, 'attacks', 10000, 10, 0),
(507, 6, 'attacks', 25000, 10, 0),
(508, 6, 'attacks', 40000, 10, 1),
(509, 6, 'attacks', 50000, 10, 0),
(510, 6, 'attacks', 75000, 10, 0),
(511, 6, 'attacks', 90000, 10, 0),
(512, 6, 'attacks', 100000, 10, 0),
(513, 6, 'attacks', 125000, 10, 1),
(514, 6, 'attacks', 150000, 10, 0),
(515, 6, 'attacks', 200000, 10, 0),
(516, 6, 'attacks', 225000, 10, 0),
(517, 6, 'attacks', 250000, 10, 0),
(518, 6, 'attacks', 300000, 10, 1),
(519, 6, 'mugs', 1500, 10, 0),
(520, 6, 'mugs', 3000, 10, 0),
(521, 6, 'mugs', 7500, 10, 0),
(522, 6, 'mugs', 10000, 10, 0),
(523, 6, 'mugs', 15000, 10, 1),
(524, 6, 'mugs', 20000, 10, 0),
(525, 6, 'mugs', 25000, 10, 0),
(526, 6, 'mugs', 30000, 10, 0),
(527, 6, 'mugs', 40000, 10, 0),
(528, 6, 'mugs', 50000, 10, 1),
(529, 6, 'mugs', 60000, 10, 0),
(530, 6, 'mugs', 70000, 10, 0),
(531, 6, 'mugs', 80000, 10, 0),
(532, 6, 'mugs', 90000, 10, 0),
(533, 6, 'mugs', 100000, 10, 1),
(534, 6, 'mugs', 110000, 10, 0),
(535, 6, 'mugs', 125000, 10, 0),
(536, 6, 'mugs', 140000, 10, 0),
(537, 6, 'mugs', 150000, 10, 0),
(538, 6, 'mugs', 175000, 10, 1),
(539, 6, 'busts', 5000, 10, 0),
(540, 6, 'busts', 7500, 10, 0),
(541, 6, 'busts', 10000, 10, 0),
(542, 6, 'busts', 12500, 10, 0),
(543, 6, 'busts', 15000, 10, 1),
(544, 6, 'busts', 20000, 10, 0),
(545, 6, 'busts', 25000, 10, 0),
(546, 6, 'busts', 35000, 10, 0),
(547, 6, 'busts', 50000, 10, 0),
(548, 6, 'busts', 70000, 10, 1),
(549, 6, 'busts', 80000, 10, 0),
(550, 6, 'busts', 90000, 10, 0),
(551, 6, 'busts', 100000, 10, 0),
(552, 6, 'busts', 125000, 10, 0),
(553, 6, 'busts', 150000, 10, 1),
(554, 6, 'backalley', 2500, 10, 0),
(555, 6, 'backalley', 5000, 10, 0),
(556, 6, 'backalley', 10000, 10, 0),
(557, 6, 'backalley', 12500, 10, 0),
(558, 6, 'backalley', 15000, 10, 1),
(559, 6, 'backalley', 25000, 10, 0),
(560, 6, 'backalley', 27500, 10, 0),
(561, 6, 'backalley', 30000, 10, 0),
(562, 6, 'backalley', 35000, 10, 1),
(563, 6, 'backalley', 50000, 10, 0),
(564, 6, 'backalley', 55000, 10, 0),
(565, 6, 'backalley', 60000, 10, 0),
(566, 6, 'backalley', 65000, 10, 0),
(567, 6, 'backalley', 70000, 10, 1),
(568, 6, 'backalley', 75000, 10, 0),
(569, 6, 'backalley', 80000, 10, 0),
(570, 6, 'backalley', 85000, 10, 0),
(571, 6, 'backalley', 90000, 10, 0),
(572, 6, 'backalley', 100000, 10, 1),
(573, 6, 'trains', 100, 10, 0),
(574, 6, 'trains', 250, 10, 0),
(575, 6, 'trains', 500, 10, 0),
(576, 6, 'trains', 600, 10, 0),
(577, 6, 'trains', 750, 10, 1),
(578, 6, 'trains', 1000, 10, 0),
(579, 6, 'trains', 1500, 10, 0),
(580, 6, 'trains', 2500, 10, 0),
(581, 6, 'trains', 5000, 10, 0),
(582, 6, 'trains', 10000, 10, 1),
(583, 6, 'crimes', 30000000, 10, 0),
(584, 6, 'attacks', 350000, 10, 0),
(585, 6, 'mugs', 200000, 10, 0),
(586, 6, 'busts', 200000, 10, 1),
(587, 6, 'backalley', 125000, 10, 1),
(588, 6, 'trains', 25000, 10, 0),
(589, 6, 'crimes', 500000, 10, 0),
(590, 6, 'crimes', 1000000, 10, 0),
(591, 6, 'crimes', 3000000, 10, 0),
(592, 6, 'crimes', 32000000, 10, 1),
(593, 6, 'attacks', 2500, 10, 0),
(594, 6, 'attacks', 60000, 10, 0),
(595, 6, 'attacks', 175000, 10, 1),
(596, 6, 'mugs', 500, 10, 0),
(597, 6, 'busts', 225000, 10, 0),
(598, 6, 'busts', 1000, 10, 0),
(599, 6, 'busts', 2500, 10, 1),
(600, 6, 'backalley', 1000, 10, 0),
(601, 6, 'backalley', 20000, 10, 1),
(602, 6, 'backalley', 150000, 10, 0),
(603, 6, 'trains', 15000, 10, 0),
(604, 7, 'crimes', 10000, 10, 0),
(605, 7, 'crimes', 250000, 10, 0),
(606, 7, 'crimes', 750000, 10, 0),
(607, 7, 'crimes', 1500000, 10, 0),
(608, 7, 'crimes', 2000000, 10, 1),
(609, 7, 'crimes', 5000000, 10, 0),
(610, 7, 'crimes', 7500000, 10, 0),
(611, 7, 'crimes', 10000000, 10, 0),
(612, 7, 'crimes', 11000000, 10, 0),
(613, 7, 'crimes', 12500000, 10, 1),
(614, 7, 'crimes', 15000000, 10, 0),
(615, 7, 'crimes', 17500000, 10, 0),
(616, 7, 'crimes', 20000000, 10, 0),
(617, 7, 'crimes', 22000000, 10, 0),
(618, 7, 'crimes', 23000000, 10, 1),
(619, 7, 'crimes', 24000000, 10, 0),
(620, 7, 'crimes', 25000000, 10, 0),
(621, 7, 'crimes', 26000000, 10, 0),
(622, 7, 'crimes', 27000000, 10, 0),
(623, 7, 'crimes', 28000000, 10, 1),
(624, 7, 'attacks', 5000, 10, 0),
(625, 7, 'attacks', 10000, 10, 0),
(626, 7, 'attacks', 25000, 10, 0),
(627, 7, 'attacks', 40000, 10, 1),
(628, 7, 'attacks', 50000, 10, 0),
(629, 7, 'attacks', 75000, 10, 0),
(630, 7, 'attacks', 90000, 10, 0),
(631, 7, 'attacks', 100000, 10, 0),
(632, 7, 'attacks', 125000, 10, 1),
(633, 7, 'attacks', 150000, 10, 0),
(634, 7, 'attacks', 200000, 10, 0),
(635, 7, 'attacks', 225000, 10, 0),
(636, 7, 'attacks', 250000, 10, 0),
(637, 7, 'attacks', 300000, 10, 1),
(638, 7, 'mugs', 1500, 10, 0),
(639, 7, 'mugs', 3000, 10, 0),
(640, 7, 'mugs', 7500, 10, 0),
(641, 7, 'mugs', 10000, 10, 0),
(642, 7, 'mugs', 15000, 10, 1),
(643, 7, 'mugs', 20000, 10, 0),
(644, 7, 'mugs', 25000, 10, 0),
(645, 7, 'mugs', 30000, 10, 0),
(646, 6, 'mugs', 40000, 10, 0),
(647, 7, 'mugs', 50000, 10, 1),
(648, 7, 'mugs', 60000, 10, 0),
(649, 7, 'mugs', 70000, 10, 0),
(650, 7, 'mugs', 80000, 10, 0),
(651, 7, 'mugs', 90000, 10, 0),
(652, 7, 'mugs', 100000, 10, 1),
(653, 7, 'mugs', 110000, 10, 0),
(654, 7, 'mugs', 125000, 10, 0),
(655, 7, 'mugs', 140000, 10, 0),
(656, 7, 'mugs', 150000, 10, 0),
(657, 7, 'mugs', 175000, 10, 1),
(658, 7, 'busts', 5000, 10, 0),
(659, 7, 'busts', 7500, 10, 0),
(660, 7, 'busts', 10000, 10, 0),
(661, 7, 'busts', 12500, 10, 0),
(662, 7, 'busts', 15000, 10, 1),
(663, 7, 'busts', 20000, 10, 0),
(664, 7, 'busts', 25000, 10, 0),
(665, 7, 'busts', 35000, 10, 0),
(666, 7, 'busts', 50000, 10, 0),
(667, 7, 'busts', 70000, 10, 1),
(668, 7, 'busts', 80000, 10, 0),
(669, 7, 'busts', 90000, 10, 0),
(670, 7, 'busts', 100000, 10, 0),
(671, 7, 'busts', 125000, 10, 0),
(672, 7, 'busts', 150000, 10, 1),
(673, 7, 'backalley', 2500, 10, 0),
(674, 7, 'backalley', 5000, 10, 0),
(675, 7, 'backalley', 10000, 10, 0),
(676, 7, 'backalley', 12500, 10, 0),
(677, 7, 'backalley', 15000, 10, 1),
(678, 7, 'backalley', 25000, 10, 0),
(679, 7, 'backalley', 27500, 10, 0),
(680, 7, 'backalley', 30000, 10, 0),
(681, 7, 'backalley', 35000, 10, 1),
(682, 7, 'backalley', 50000, 10, 0),
(683, 7, 'backalley', 55000, 10, 0),
(684, 7, 'backalley', 60000, 10, 0),
(685, 7, 'backalley', 65000, 10, 0),
(686, 7, 'backalley', 70000, 10, 1),
(687, 7, 'backalley', 75000, 10, 0),
(688, 7, 'backalley', 80000, 10, 0),
(689, 7, 'backalley', 85000, 10, 0),
(690, 7, 'backalley', 90000, 10, 0),
(691, 7, 'backalley', 100000, 10, 1),
(692, 7, 'trains', 100, 10, 0),
(693, 7, 'trains', 250, 10, 0),
(694, 7, 'trains', 500, 10, 0),
(695, 7, 'trains', 600, 10, 0),
(696, 7, 'trains', 750, 10, 1),
(697, 7, 'trains', 1000, 10, 0),
(698, 7, 'trains', 1500, 10, 0),
(699, 7, 'trains', 2500, 10, 0),
(700, 7, 'trains', 5000, 10, 0),
(701, 7, 'trains', 10000, 10, 1),
(702, 7, 'crimes', 30000000, 10, 0),
(703, 7, 'attacks', 350000, 10, 0),
(704, 7, 'mugs', 200000, 10, 0),
(705, 7, 'busts', 200000, 10, 1),
(706, 7, 'backalley', 125000, 10, 1),
(707, 7, 'trains', 25000, 10, 0),
(708, 7, 'crimes', 500000, 10, 0),
(709, 7, 'crimes', 1000000, 10, 0),
(710, 7, 'crimes', 3000000, 10, 0),
(711, 7, 'crimes', 32000000, 10, 1),
(712, 7, 'attacks', 2500, 10, 0),
(713, 7, 'attacks', 60000, 10, 0),
(714, 7, 'attacks', 175000, 10, 1),
(715, 7, 'mugs', 500, 10, 0),
(716, 7, 'busts', 225000, 10, 0),
(717, 7, 'busts', 1000, 10, 0),
(718, 7, 'busts', 2500, 10, 1),
(719, 7, 'backalley', 1000, 10, 0),
(720, 7, 'backalley', 20000, 10, 1),
(721, 7, 'backalley', 150000, 10, 0),
(722, 7, 'trains', 15000, 10, 0),
(723, 8, 'crimes', 10000, 10, 0),
(724, 8, 'crimes', 250000, 10, 0),
(725, 8, 'crimes', 750000, 10, 0),
(726, 8, 'crimes', 1500000, 10, 0),
(727, 8, 'crimes', 2000000, 10, 1),
(728, 8, 'crimes', 5000000, 10, 0),
(729, 8, 'crimes', 7500000, 10, 0),
(730, 8, 'crimes', 10000000, 10, 0),
(731, 8, 'crimes', 11000000, 10, 0),
(732, 8, 'crimes', 12500000, 10, 1),
(733, 8, 'crimes', 15000000, 10, 0),
(734, 8, 'crimes', 17500000, 10, 0),
(735, 8, 'crimes', 20000000, 10, 0),
(736, 8, 'crimes', 22000000, 10, 0),
(737, 8, 'crimes', 23000000, 10, 1),
(738, 8, 'crimes', 24000000, 10, 0),
(739, 8, 'crimes', 25000000, 10, 0),
(740, 8, 'crimes', 26000000, 10, 0),
(741, 8, 'crimes', 27000000, 10, 0),
(742, 8, 'crimes', 28000000, 10, 1),
(743, 8, 'attacks', 5000, 10, 0),
(744, 8, 'attacks', 10000, 10, 0),
(745, 8, 'attacks', 25000, 10, 0),
(746, 8, 'attacks', 40000, 10, 1),
(747, 8, 'attacks', 50000, 10, 0),
(748, 8, 'attacks', 75000, 10, 0),
(749, 8, 'attacks', 90000, 10, 0),
(750, 8, 'attacks', 100000, 10, 0),
(751, 8, 'attacks', 125000, 10, 1),
(752, 8, 'attacks', 150000, 10, 0),
(753, 8, 'attacks', 200000, 10, 0),
(754, 8, 'attacks', 225000, 10, 0),
(755, 8, 'attacks', 250000, 10, 0),
(756, 8, 'attacks', 300000, 10, 1),
(757, 8, 'mugs', 1500, 10, 0),
(758, 8, 'mugs', 3000, 10, 0),
(759, 8, 'mugs', 7500, 10, 0),
(760, 8, 'mugs', 10000, 10, 0),
(761, 8, 'mugs', 15000, 10, 1),
(762, 8, 'mugs', 20000, 10, 0),
(763, 8, 'mugs', 25000, 10, 0),
(764, 8, 'mugs', 30000, 10, 0),
(765, 8, 'mugs', 50000, 10, 1),
(766, 8, 'mugs', 60000, 10, 0),
(767, 8, 'mugs', 70000, 10, 0),
(768, 8, 'mugs', 80000, 10, 0),
(769, 8, 'mugs', 90000, 10, 0),
(770, 8, 'mugs', 100000, 10, 1),
(771, 8, 'mugs', 110000, 10, 0),
(772, 8, 'mugs', 125000, 10, 0),
(773, 8, 'mugs', 140000, 10, 0),
(774, 8, 'mugs', 150000, 10, 0),
(775, 8, 'mugs', 175000, 10, 1),
(776, 8, 'busts', 5000, 10, 0),
(777, 8, 'busts', 7500, 10, 0),
(778, 8, 'busts', 10000, 10, 0),
(779, 8, 'busts', 12500, 10, 0),
(780, 8, 'busts', 15000, 10, 1),
(781, 8, 'busts', 20000, 10, 0),
(782, 8, 'busts', 25000, 10, 0),
(783, 8, 'busts', 35000, 10, 0),
(784, 8, 'busts', 50000, 10, 0),
(785, 8, 'busts', 70000, 10, 1),
(786, 8, 'busts', 80000, 10, 0),
(787, 8, 'busts', 90000, 10, 0),
(788, 8, 'busts', 100000, 10, 0),
(789, 8, 'busts', 125000, 10, 0),
(790, 8, 'busts', 150000, 10, 1),
(791, 8, 'backalley', 2500, 10, 0),
(792, 8, 'backalley', 5000, 10, 0),
(793, 8, 'backalley', 10000, 10, 0),
(794, 8, 'backalley', 12500, 10, 0),
(795, 8, 'backalley', 15000, 10, 1),
(796, 8, 'backalley', 25000, 10, 0),
(797, 8, 'backalley', 27500, 10, 0),
(798, 8, 'backalley', 30000, 10, 0),
(799, 8, 'backalley', 35000, 10, 1),
(800, 8, 'backalley', 50000, 10, 0),
(801, 8, 'backalley', 55000, 10, 0),
(802, 8, 'backalley', 60000, 10, 0),
(803, 8, 'backalley', 65000, 10, 0),
(804, 8, 'backalley', 70000, 10, 1),
(805, 8, 'backalley', 75000, 10, 0),
(806, 8, 'backalley', 80000, 10, 0),
(807, 8, 'backalley', 85000, 10, 0),
(808, 8, 'backalley', 90000, 10, 0),
(809, 8, 'backalley', 100000, 10, 1),
(810, 8, 'trains', 100, 10, 0),
(811, 8, 'trains', 250, 10, 0),
(812, 8, 'trains', 500, 10, 0),
(813, 8, 'trains', 600, 10, 0),
(814, 8, 'trains', 750, 10, 1),
(815, 8, 'trains', 1000, 10, 0),
(816, 8, 'trains', 1500, 10, 0),
(817, 8, 'trains', 2500, 10, 0),
(818, 8, 'trains', 5000, 10, 0),
(819, 8, 'trains', 10000, 10, 1),
(820, 8, 'crimes', 30000000, 10, 0),
(821, 8, 'attacks', 350000, 10, 0),
(822, 8, 'mugs', 200000, 10, 0),
(823, 8, 'busts', 200000, 10, 1),
(824, 8, 'backalley', 125000, 10, 1),
(825, 8, 'trains', 25000, 10, 0),
(826, 8, 'crimes', 500000, 10, 0),
(827, 8, 'crimes', 1000000, 10, 0),
(828, 8, 'crimes', 3000000, 10, 0),
(829, 8, 'crimes', 32000000, 10, 1),
(830, 8, 'attacks', 2500, 10, 0),
(831, 8, 'attacks', 60000, 10, 0),
(832, 8, 'attacks', 175000, 10, 1),
(833, 8, 'mugs', 500, 10, 0),
(834, 8, 'busts', 225000, 10, 0),
(835, 8, 'busts', 1000, 10, 0),
(836, 8, 'busts', 2500, 10, 1),
(837, 8, 'backalley', 1000, 10, 0),
(838, 8, 'backalley', 20000, 10, 1),
(839, 8, 'backalley', 150000, 10, 0),
(840, 8, 'trains', 15000, 10, 0),
(841, 9, 'crimes', 10000, 10, 0),
(842, 9, 'crimes', 250000, 10, 0),
(843, 9, 'crimes', 750000, 10, 0),
(844, 9, 'crimes', 1500000, 10, 0),
(845, 9, 'crimes', 2000000, 10, 1),
(846, 9, 'crimes', 5000000, 10, 0),
(847, 9, 'crimes', 7500000, 10, 0),
(848, 9, 'crimes', 10000000, 10, 0),
(849, 9, 'crimes', 11000000, 10, 0),
(850, 9, 'crimes', 12500000, 10, 1),
(851, 9, 'crimes', 15000000, 10, 0),
(852, 9, 'crimes', 17500000, 10, 0),
(853, 9, 'crimes', 20000000, 10, 0),
(854, 9, 'crimes', 22000000, 10, 0),
(855, 9, 'crimes', 23000000, 10, 1),
(856, 9, 'crimes', 24000000, 10, 0),
(857, 9, 'crimes', 25000000, 10, 0),
(858, 9, 'crimes', 26000000, 10, 0),
(859, 9, 'crimes', 27000000, 10, 0),
(860, 9, 'crimes', 28000000, 10, 1),
(861, 9, 'attacks', 5000, 10, 0),
(862, 9, 'attacks', 10000, 10, 0),
(863, 9, 'attacks', 25000, 10, 0),
(864, 9, 'attacks', 40000, 10, 1),
(865, 9, 'attacks', 50000, 10, 0),
(866, 9, 'attacks', 75000, 10, 0),
(867, 9, 'attacks', 90000, 10, 0),
(868, 9, 'attacks', 100000, 10, 0),
(869, 9, 'attacks', 125000, 10, 1),
(870, 9, 'attacks', 150000, 10, 0),
(871, 9, 'attacks', 200000, 10, 0),
(872, 9, 'attacks', 225000, 10, 0),
(873, 9, 'attacks', 250000, 10, 0),
(874, 9, 'attacks', 300000, 10, 1),
(875, 9, 'mugs', 1500, 10, 0),
(876, 9, 'mugs', 3000, 10, 0),
(877, 9, 'mugs', 7500, 10, 0),
(878, 9, 'mugs', 10000, 10, 0),
(879, 9, 'mugs', 15000, 10, 1),
(880, 9, 'mugs', 20000, 10, 0),
(881, 9, 'mugs', 25000, 10, 0),
(882, 9, 'mugs', 30000, 10, 0),
(883, 9, 'mugs', 50000, 10, 1),
(884, 9, 'mugs', 60000, 10, 0),
(885, 9, 'mugs', 70000, 10, 0),
(886, 9, 'mugs', 80000, 10, 0),
(887, 9, 'mugs', 90000, 10, 0),
(888, 9, 'mugs', 100000, 10, 1),
(889, 9, 'mugs', 110000, 10, 0),
(890, 9, 'mugs', 125000, 10, 0),
(891, 9, 'mugs', 140000, 10, 0),
(892, 9, 'mugs', 150000, 10, 0),
(893, 9, 'mugs', 175000, 10, 1),
(894, 9, 'busts', 5000, 10, 0),
(895, 9, 'busts', 7500, 10, 0),
(896, 9, 'busts', 10000, 10, 0),
(897, 9, 'busts', 12500, 10, 0),
(898, 9, 'busts', 15000, 10, 1),
(899, 9, 'busts', 20000, 10, 0),
(900, 9, 'busts', 25000, 10, 0),
(901, 9, 'busts', 35000, 10, 0),
(902, 9, 'busts', 50000, 10, 0),
(903, 9, 'busts', 70000, 10, 1),
(904, 9, 'busts', 80000, 10, 0),
(905, 9, 'busts', 90000, 10, 0),
(906, 9, 'busts', 100000, 10, 0),
(907, 9, 'busts', 125000, 10, 0),
(908, 9, 'busts', 150000, 10, 1),
(909, 9, 'backalley', 2500, 10, 0),
(910, 9, 'backalley', 5000, 10, 0),
(911, 9, 'backalley', 10000, 10, 0),
(912, 9, 'backalley', 12500, 10, 0),
(913, 9, 'backalley', 15000, 10, 1),
(914, 9, 'backalley', 25000, 10, 0),
(915, 9, 'backalley', 27500, 10, 0),
(916, 9, 'backalley', 30000, 10, 0),
(917, 9, 'backalley', 35000, 10, 1),
(918, 9, 'backalley', 50000, 10, 0),
(919, 9, 'backalley', 55000, 10, 0),
(920, 9, 'backalley', 60000, 10, 0),
(921, 9, 'backalley', 65000, 10, 0),
(922, 9, 'backalley', 70000, 10, 1),
(923, 9, 'backalley', 75000, 10, 0),
(924, 9, 'backalley', 80000, 10, 0),
(925, 9, 'backalley', 85000, 10, 0),
(926, 9, 'backalley', 90000, 10, 0),
(927, 9, 'backalley', 100000, 10, 1),
(928, 9, 'trains', 100, 10, 0),
(929, 9, 'trains', 250, 10, 0),
(930, 9, 'trains', 500, 10, 0),
(931, 9, 'trains', 600, 10, 0),
(932, 9, 'trains', 750, 10, 1),
(933, 9, 'trains', 1000, 10, 0),
(934, 9, 'trains', 1500, 10, 0),
(935, 9, 'trains', 2500, 10, 0),
(936, 9, 'trains', 5000, 10, 0),
(937, 9, 'trains', 10000, 10, 1),
(938, 9, 'crimes', 30000000, 10, 0),
(939, 9, 'attacks', 350000, 10, 0),
(940, 9, 'mugs', 200000, 10, 0),
(941, 9, 'busts', 200000, 10, 1),
(942, 9, 'backalley', 125000, 10, 1),
(943, 9, 'trains', 25000, 10, 0),
(944, 9, 'crimes', 500000, 10, 0),
(945, 9, 'crimes', 1000000, 10, 0),
(946, 9, 'crimes', 3000000, 10, 0),
(947, 9, 'crimes', 32000000, 10, 1),
(948, 9, 'attacks', 2500, 10, 0),
(949, 9, 'attacks', 60000, 10, 0),
(950, 8, 'attacks', 175000, 10, 1),
(951, 9, 'mugs', 500, 10, 0),
(952, 9, 'busts', 225000, 10, 0),
(953, 9, 'busts', 1000, 10, 0),
(954, 9, 'busts', 2500, 10, 1),
(955, 9, 'backalley', 1000, 10, 0),
(956, 9, 'backalley', 20000, 10, 1),
(957, 9, 'backalley', 150000, 10, 0),
(958, 9, 'trains', 15000, 10, 0),
(959, 10, 'crimes', 10000, 10, 0),
(960, 10, 'crimes', 250000, 10, 0),
(961, 10, 'crimes', 750000, 10, 0),
(962, 10, 'crimes', 1500000, 10, 0),
(963, 10, 'crimes', 2000000, 10, 1),
(964, 10, 'crimes', 5000000, 10, 0),
(965, 10, 'crimes', 7500000, 10, 0),
(966, 10, 'crimes', 10000000, 10, 0),
(967, 10, 'crimes', 11000000, 10, 0),
(968, 10, 'crimes', 12500000, 10, 1),
(969, 10, 'crimes', 15000000, 10, 0),
(970, 10, 'crimes', 17500000, 10, 0),
(971, 10, 'crimes', 20000000, 10, 0),
(972, 10, 'crimes', 22000000, 10, 0),
(973, 10, 'crimes', 23000000, 10, 1),
(974, 10, 'crimes', 24000000, 10, 0),
(975, 10, 'crimes', 25000000, 10, 0),
(976, 10, 'crimes', 26000000, 10, 0),
(977, 10, 'crimes', 27000000, 10, 0),
(978, 10, 'crimes', 28000000, 10, 1),
(979, 10, 'attacks', 5000, 10, 0),
(980, 10, 'attacks', 10000, 10, 0),
(981, 10, 'attacks', 25000, 10, 0),
(982, 10, 'attacks', 40000, 10, 1),
(983, 10, 'attacks', 50000, 10, 0),
(984, 10, 'attacks', 75000, 10, 0),
(985, 10, 'attacks', 90000, 10, 0),
(986, 10, 'attacks', 100000, 10, 0),
(987, 10, 'attacks', 125000, 10, 1),
(988, 10, 'attacks', 150000, 10, 0),
(989, 10, 'attacks', 200000, 10, 0),
(990, 10, 'attacks', 225000, 10, 0),
(991, 10, 'attacks', 250000, 10, 0),
(992, 10, 'attacks', 300000, 10, 1),
(993, 10, 'mugs', 1500, 10, 0),
(994, 10, 'mugs', 3000, 10, 0),
(995, 10, 'mugs', 7500, 10, 0),
(996, 10, 'mugs', 10000, 10, 0),
(997, 10, 'mugs', 15000, 10, 1),
(998, 10, 'mugs', 20000, 10, 0),
(999, 10, 'mugs', 25000, 10, 0),
(1000, 10, 'mugs', 30000, 10, 0),
(1001, 10, 'mugs', 50000, 10, 1),
(1002, 10, 'mugs', 60000, 10, 0),
(1003, 10, 'mugs', 70000, 10, 0),
(1004, 10, 'mugs', 80000, 10, 0),
(1005, 10, 'mugs', 90000, 10, 0),
(1006, 10, 'mugs', 100000, 10, 1),
(1007, 10, 'mugs', 110000, 10, 0),
(1008, 10, 'mugs', 125000, 10, 0),
(1009, 10, 'mugs', 140000, 10, 0),
(1010, 10, 'mugs', 150000, 10, 0),
(1011, 10, 'mugs', 175000, 10, 1),
(1012, 10, 'busts', 5000, 10, 0),
(1013, 10, 'busts', 7500, 10, 0),
(1014, 10, 'busts', 10000, 10, 0),
(1015, 10, 'busts', 12500, 10, 0),
(1016, 10, 'busts', 15000, 10, 1),
(1017, 10, 'busts', 20000, 10, 0),
(1018, 10, 'busts', 25000, 10, 0),
(1019, 10, 'busts', 35000, 10, 0),
(1020, 10, 'busts', 50000, 10, 0),
(1021, 10, 'busts', 70000, 10, 1),
(1022, 10, 'busts', 80000, 10, 0),
(1023, 10, 'busts', 90000, 10, 0),
(1024, 10, 'busts', 100000, 10, 0),
(1025, 10, 'busts', 125000, 10, 0),
(1026, 10, 'busts', 150000, 10, 1),
(1027, 10, 'backalley', 2500, 10, 0),
(1028, 10, 'backalley', 5000, 10, 0),
(1029, 10, 'backalley', 10000, 10, 0),
(1030, 10, 'backalley', 12500, 10, 0),
(1031, 10, 'backalley', 15000, 10, 1),
(1032, 10, 'backalley', 25000, 10, 0),
(1033, 10, 'backalley', 27500, 10, 0),
(1034, 10, 'backalley', 30000, 10, 0),
(1035, 10, 'backalley', 35000, 10, 1),
(1036, 10, 'backalley', 50000, 10, 0),
(1037, 10, 'backalley', 55000, 10, 0),
(1038, 10, 'backalley', 60000, 10, 0),
(1039, 10, 'backalley', 65000, 10, 0),
(1040, 10, 'backalley', 70000, 10, 1),
(1041, 10, 'backalley', 75000, 10, 0),
(1042, 10, 'backalley', 80000, 10, 0),
(1043, 10, 'backalley', 85000, 10, 0),
(1044, 10, 'backalley', 90000, 10, 0),
(1045, 10, 'backalley', 100000, 10, 1),
(1046, 10, 'trains', 100, 10, 0),
(1047, 10, 'trains', 250, 10, 0),
(1048, 10, 'trains', 500, 10, 0),
(1049, 10, 'trains', 600, 10, 0),
(1050, 10, 'trains', 750, 10, 1),
(1051, 10, 'trains', 1000, 10, 0),
(1052, 10, 'trains', 1500, 10, 0),
(1053, 10, 'trains', 2500, 10, 0),
(1054, 10, 'trains', 5000, 10, 0),
(1055, 10, 'trains', 10000, 10, 1),
(1056, 10, 'crimes', 30000000, 10, 0),
(1057, 10, 'attacks', 350000, 10, 0),
(1058, 10, 'mugs', 200000, 10, 0),
(1059, 10, 'busts', 200000, 10, 1),
(1060, 10, 'backalley', 125000, 10, 1),
(1061, 10, 'trains', 25000, 10, 0),
(1062, 10, 'crimes', 500000, 10, 0),
(1063, 10, 'crimes', 1000000, 10, 0),
(1064, 10, 'crimes', 3000000, 10, 0),
(1065, 10, 'crimes', 32000000, 10, 1),
(1066, 10, 'attacks', 2500, 10, 0),
(1067, 10, 'attacks', 60000, 10, 0),
(1068, 10, 'mugs', 500, 10, 0),
(1069, 10, 'busts', 225000, 10, 0),
(1070, 10, 'busts', 1000, 10, 0),
(1071, 10, 'busts', 2500, 10, 1),
(1072, 10, 'backalley', 1000, 10, 0),
(1073, 10, 'backalley', 20000, 10, 1),
(1074, 10, 'backalley', 150000, 10, 0),
(1075, 10, 'trains', 15000, 10, 0),
(1076, 11, 'crimes', 10000, 11, 0),
(1077, 11, 'crimes', 250000, 10, 0),
(1078, 11, 'crimes', 750000, 10, 0),
(1079, 11, 'crimes', 1500000, 10, 0),
(1080, 11, 'crimes', 2000000, 10, 1),
(1081, 11, 'crimes', 5000000, 10, 0),
(1082, 11, 'crimes', 7500000, 10, 0),
(1083, 11, 'crimes', 10000000, 10, 0),
(1084, 11, 'crimes', 11000000, 10, 0),
(1085, 11, 'crimes', 12500000, 10, 1),
(1086, 11, 'crimes', 15000000, 10, 0),
(1087, 11, 'crimes', 17500000, 10, 0),
(1088, 11, 'crimes', 20000000, 10, 0),
(1089, 11, 'crimes', 22000000, 10, 0),
(1090, 11, 'crimes', 23000000, 10, 1),
(1091, 11, 'crimes', 24000000, 10, 0),
(1092, 11, 'crimes', 25000000, 10, 0),
(1093, 11, 'crimes', 26000000, 10, 0),
(1094, 11, 'crimes', 27000000, 10, 0),
(1095, 11, 'crimes', 28000000, 10, 1),
(1096, 11, 'attacks', 5000, 10, 0),
(1097, 11, 'attacks', 10000, 10, 0),
(1098, 11, 'attacks', 25000, 10, 0),
(1099, 11, 'attacks', 40000, 10, 1),
(1100, 11, 'attacks', 50000, 10, 0),
(1101, 11, 'attacks', 75000, 10, 0),
(1102, 11, 'attacks', 90000, 10, 0),
(1103, 11, 'attacks', 100000, 10, 0),
(1104, 11, 'attacks', 125000, 10, 1),
(1105, 11, 'attacks', 150000, 10, 0),
(1106, 11, 'attacks', 200000, 10, 0),
(1107, 11, 'attacks', 225000, 10, 0),
(1108, 11, 'attacks', 250000, 10, 0),
(1109, 11, 'attacks', 300000, 10, 1),
(1110, 11, 'mugs', 1500, 10, 0),
(1111, 11, 'mugs', 3000, 10, 0),
(1112, 11, 'mugs', 7500, 10, 0),
(1113, 11, 'mugs', 10000, 10, 0),
(1114, 11, 'mugs', 15000, 10, 1),
(1115, 11, 'mugs', 20000, 10, 0),
(1116, 11, 'mugs', 25000, 10, 0),
(1117, 11, 'mugs', 30000, 10, 0),
(1118, 11, 'mugs', 50000, 10, 1),
(1119, 11, 'mugs', 60000, 10, 0),
(1120, 11, 'mugs', 70000, 10, 0),
(1121, 11, 'mugs', 80000, 10, 0),
(1122, 11, 'mugs', 90000, 10, 0),
(1123, 11, 'mugs', 100000, 10, 1),
(1124, 11, 'mugs', 110000, 10, 0),
(1125, 11, 'mugs', 125000, 10, 0),
(1126, 11, 'mugs', 140000, 10, 0),
(1127, 11, 'mugs', 150000, 10, 0),
(1128, 11, 'mugs', 175000, 10, 1),
(1129, 11, 'busts', 5000, 10, 0),
(1130, 11, 'busts', 7500, 10, 0),
(1131, 11, 'busts', 10000, 10, 0),
(1132, 11, 'busts', 12500, 10, 0),
(1133, 11, 'busts', 15000, 10, 1),
(1134, 11, 'busts', 20000, 10, 0),
(1135, 11, 'busts', 25000, 10, 0),
(1136, 11, 'busts', 35000, 10, 0),
(1137, 11, 'busts', 50000, 10, 0),
(1138, 11, 'busts', 70000, 10, 1),
(1139, 11, 'busts', 80000, 10, 0),
(1140, 11, 'busts', 90000, 10, 0),
(1141, 11, 'busts', 100000, 10, 0),
(1142, 11, 'busts', 125000, 10, 0),
(1143, 11, 'busts', 150000, 10, 1),
(1144, 11, 'backalley', 2500, 10, 0),
(1145, 11, 'backalley', 5000, 10, 0),
(1146, 11, 'backalley', 10000, 10, 0),
(1147, 11, 'backalley', 12500, 10, 0),
(1148, 11, 'backalley', 15000, 10, 1),
(1149, 11, 'backalley', 25000, 10, 0),
(1150, 11, 'backalley', 27500, 10, 0),
(1151, 11, 'backalley', 30000, 10, 0),
(1152, 11, 'backalley', 35000, 10, 1),
(1153, 11, 'backalley', 50000, 10, 0),
(1154, 11, 'backalley', 55000, 10, 0),
(1155, 11, 'backalley', 60000, 10, 0),
(1156, 11, 'backalley', 65000, 10, 0),
(1157, 11, 'backalley', 70000, 10, 1),
(1158, 11, 'backalley', 75000, 10, 0),
(1159, 11, 'backalley', 80000, 10, 0),
(1160, 11, 'backalley', 85000, 10, 0),
(1161, 11, 'backalley', 90000, 10, 0),
(1162, 11, 'backalley', 100000, 10, 1),
(1163, 11, 'trains', 100, 10, 0),
(1164, 11, 'trains', 250, 10, 0),
(1165, 11, 'trains', 500, 10, 0),
(1166, 11, 'trains', 600, 10, 0),
(1167, 11, 'trains', 750, 10, 1),
(1168, 11, 'trains', 1000, 10, 0),
(1169, 11, 'trains', 1500, 10, 0),
(1170, 11, 'trains', 2500, 10, 0),
(1171, 11, 'trains', 5000, 10, 0),
(1172, 11, 'trains', 10000, 10, 1),
(1173, 11, 'crimes', 30000000, 10, 0),
(1174, 11, 'attacks', 350000, 10, 0),
(1175, 11, 'mugs', 200000, 10, 0),
(1176, 11, 'busts', 200000, 10, 1),
(1177, 11, 'backalley', 125000, 10, 1),
(1178, 11, 'trains', 25000, 10, 0),
(1179, 11, 'crimes', 500000, 10, 0),
(1180, 11, 'crimes', 1000000, 10, 0),
(1181, 11, 'crimes', 3000000, 10, 0),
(1182, 11, 'crimes', 32000000, 10, 1),
(1183, 11, 'attacks', 2500, 10, 0),
(1184, 11, 'attacks', 60000, 10, 0),
(1185, 11, 'mugs', 500, 10, 0),
(1186, 11, 'busts', 225000, 10, 0),
(1187, 11, 'busts', 1000, 10, 0),
(1188, 11, 'busts', 2500, 10, 1),
(1189, 11, 'backalley', 1000, 10, 0),
(1190, 11, 'backalley', 20000, 10, 1),
(1191, 11, 'backalley', 150000, 10, 0),
(1192, 11, 'trains', 15000, 10, 0),
(1193, 12, 'crimes', 10000, 11, 0),
(1194, 12, 'crimes', 250000, 10, 0),
(1195, 12, 'crimes', 750000, 10, 0),
(1196, 12, 'crimes', 1500000, 10, 0),
(1197, 12, 'crimes', 2000000, 10, 1),
(1198, 12, 'crimes', 5000000, 10, 0),
(1199, 12, 'crimes', 7500000, 10, 0),
(1200, 12, 'crimes', 10000000, 10, 0),
(1201, 12, 'crimes', 11000000, 10, 0),
(1202, 12, 'crimes', 12500000, 10, 1),
(1203, 12, 'crimes', 15000000, 10, 0),
(1204, 12, 'crimes', 17500000, 10, 0),
(1205, 12, 'crimes', 20000000, 10, 0),
(1206, 12, 'crimes', 22000000, 10, 0),
(1207, 12, 'crimes', 23000000, 10, 1),
(1208, 12, 'crimes', 24000000, 10, 0),
(1209, 12, 'crimes', 25000000, 10, 0),
(1210, 12, 'crimes', 26000000, 10, 0),
(1211, 12, 'crimes', 27000000, 10, 0),
(1212, 12, 'crimes', 28000000, 10, 1),
(1213, 12, 'attacks', 5000, 10, 0),
(1214, 12, 'attacks', 10000, 10, 0),
(1215, 12, 'attacks', 25000, 10, 0),
(1216, 12, 'attacks', 40000, 10, 1),
(1217, 12, 'attacks', 50000, 10, 0),
(1218, 12, 'attacks', 75000, 10, 0),
(1219, 12, 'attacks', 90000, 10, 0),
(1220, 12, 'attacks', 100000, 10, 0),
(1221, 12, 'attacks', 125000, 10, 1),
(1222, 12, 'attacks', 150000, 10, 0),
(1223, 12, 'attacks', 200000, 10, 0),
(1224, 12, 'attacks', 225000, 10, 0),
(1225, 12, 'attacks', 250000, 10, 0),
(1226, 12, 'attacks', 300000, 10, 1),
(1227, 12, 'mugs', 1500, 10, 0),
(1228, 12, 'mugs', 3000, 10, 0),
(1229, 12, 'mugs', 7500, 10, 0),
(1230, 12, 'mugs', 10000, 10, 0),
(1231, 12, 'mugs', 15000, 10, 1),
(1232, 12, 'mugs', 20000, 10, 0),
(1233, 12, 'mugs', 25000, 10, 0),
(1234, 12, 'mugs', 30000, 10, 0),
(1235, 12, 'mugs', 50000, 10, 1),
(1236, 12, 'mugs', 60000, 10, 0),
(1237, 12, 'mugs', 70000, 10, 0),
(1238, 12, 'mugs', 80000, 10, 0),
(1239, 12, 'mugs', 90000, 10, 0),
(1240, 12, 'mugs', 100000, 10, 1),
(1241, 12, 'mugs', 110000, 10, 0),
(1242, 12, 'mugs', 125000, 10, 0),
(1243, 12, 'mugs', 140000, 10, 0),
(1244, 12, 'mugs', 150000, 10, 0),
(1245, 12, 'mugs', 175000, 10, 1),
(1246, 12, 'busts', 5000, 10, 0),
(1247, 12, 'busts', 7500, 10, 0),
(1248, 12, 'busts', 10000, 10, 0),
(1249, 12, 'busts', 12500, 10, 0),
(1250, 12, 'busts', 15000, 10, 1),
(1251, 12, 'busts', 20000, 10, 0),
(1252, 12, 'busts', 25000, 10, 0),
(1253, 12, 'busts', 35000, 10, 0),
(1254, 12, 'busts', 50000, 10, 0),
(1255, 12, 'busts', 70000, 10, 1),
(1256, 12, 'busts', 80000, 10, 0),
(1257, 12, 'busts', 90000, 10, 0),
(1258, 12, 'busts', 100000, 10, 0),
(1259, 12, 'busts', 125000, 10, 0),
(1260, 12, 'busts', 150000, 10, 1),
(1261, 12, 'backalley', 2500, 10, 0),
(1262, 12, 'backalley', 5000, 10, 0),
(1263, 12, 'backalley', 10000, 10, 0),
(1264, 12, 'backalley', 12500, 10, 0),
(1265, 12, 'backalley', 15000, 10, 1),
(1266, 12, 'backalley', 25000, 10, 0),
(1267, 12, 'backalley', 27500, 10, 0),
(1268, 12, 'backalley', 30000, 10, 0),
(1269, 12, 'backalley', 35000, 10, 1),
(1270, 12, 'backalley', 50000, 10, 0),
(1271, 12, 'backalley', 55000, 10, 0),
(1272, 12, 'backalley', 60000, 10, 0),
(1273, 12, 'backalley', 65000, 10, 0),
(1274, 12, 'backalley', 70000, 10, 1),
(1275, 12, 'backalley', 75000, 10, 0),
(1276, 12, 'backalley', 80000, 10, 0),
(1277, 12, 'backalley', 85000, 10, 0),
(1278, 12, 'backalley', 90000, 10, 0),
(1279, 12, 'backalley', 100000, 10, 1),
(1280, 12, 'trains', 100, 10, 0),
(1281, 12, 'trains', 250, 10, 0),
(1282, 12, 'trains', 500, 10, 0),
(1283, 12, 'trains', 600, 10, 0),
(1284, 12, 'trains', 750, 10, 1),
(1285, 12, 'trains', 1000, 10, 0),
(1286, 12, 'trains', 1500, 10, 0),
(1287, 12, 'trains', 2500, 10, 0),
(1288, 12, 'trains', 5000, 10, 0),
(1289, 12, 'trains', 10000, 10, 1),
(1290, 12, 'crimes', 30000000, 10, 0),
(1291, 12, 'attacks', 350000, 10, 0),
(1292, 12, 'mugs', 200000, 10, 0),
(1293, 12, 'busts', 200000, 10, 1),
(1294, 12, 'backalley', 125000, 10, 1),
(1295, 12, 'trains', 25000, 10, 0),
(1296, 12, 'crimes', 500000, 10, 0),
(1297, 12, 'crimes', 1000000, 10, 0),
(1298, 12, 'crimes', 3000000, 10, 0),
(1299, 12, 'crimes', 32000000, 10, 1),
(1300, 12, 'attacks', 2500, 10, 0),
(1301, 12, 'attacks', 60000, 10, 0),
(1302, 12, 'mugs', 500, 10, 0),
(1303, 12, 'busts', 225000, 10, 0),
(1304, 12, 'busts', 1000, 10, 0),
(1305, 12, 'busts', 2500, 10, 1),
(1306, 12, 'backalley', 1000, 10, 0),
(1307, 12, 'backalley', 20000, 10, 1),
(1308, 12, 'backalley', 150000, 10, 0),
(1309, 12, 'trains', 15000, 10, 0),
(1310, 13, 'crimes', 10000, 11, 0),
(1311, 13, 'crimes', 250000, 10, 0),
(1312, 13, 'crimes', 750000, 10, 0),
(1313, 13, 'crimes', 1500000, 10, 0),
(1314, 13, 'crimes', 2000000, 10, 1),
(1315, 13, 'crimes', 5000000, 10, 0),
(1316, 13, 'crimes', 7500000, 10, 0),
(1317, 13, 'crimes', 10000000, 10, 0),
(1318, 13, 'crimes', 11000000, 10, 0),
(1319, 13, 'crimes', 12500000, 10, 1),
(1320, 13, 'crimes', 15000000, 10, 0),
(1321, 13, 'crimes', 17500000, 10, 0),
(1322, 13, 'crimes', 20000000, 10, 0),
(1323, 13, 'crimes', 22000000, 10, 0),
(1324, 13, 'crimes', 23000000, 10, 1),
(1325, 13, 'crimes', 24000000, 10, 0),
(1326, 13, 'crimes', 25000000, 10, 0),
(1327, 13, 'crimes', 26000000, 10, 0),
(1328, 13, 'crimes', 27000000, 10, 0),
(1329, 13, 'crimes', 28000000, 10, 1),
(1330, 13, 'attacks', 5000, 10, 0),
(1331, 13, 'attacks', 10000, 10, 0),
(1332, 13, 'attacks', 25000, 10, 0),
(1333, 13, 'attacks', 40000, 10, 1),
(1334, 13, 'attacks', 50000, 10, 0),
(1335, 13, 'attacks', 75000, 10, 0),
(1336, 13, 'attacks', 90000, 10, 0),
(1337, 13, 'attacks', 100000, 10, 0),
(1338, 13, 'attacks', 125000, 10, 1),
(1339, 13, 'attacks', 150000, 10, 0),
(1340, 13, 'attacks', 200000, 10, 0),
(1341, 13, 'attacks', 225000, 10, 0),
(1342, 13, 'attacks', 250000, 10, 0),
(1343, 13, 'attacks', 300000, 10, 1),
(1344, 13, 'mugs', 1500, 10, 0),
(1345, 13, 'mugs', 3000, 10, 0),
(1346, 13, 'mugs', 7500, 10, 0),
(1347, 13, 'mugs', 10000, 10, 0),
(1348, 13, 'mugs', 15000, 10, 1),
(1349, 13, 'mugs', 20000, 10, 0),
(1350, 13, 'mugs', 25000, 10, 0),
(1351, 13, 'mugs', 30000, 10, 0),
(1352, 13, 'mugs', 50000, 10, 1),
(1353, 13, 'mugs', 60000, 10, 0),
(1354, 13, 'mugs', 70000, 10, 0),
(1355, 13, 'mugs', 80000, 10, 0),
(1356, 13, 'mugs', 90000, 10, 0),
(1357, 13, 'mugs', 100000, 10, 1),
(1358, 13, 'mugs', 110000, 10, 0),
(1359, 13, 'mugs', 125000, 10, 0),
(1360, 13, 'mugs', 140000, 10, 0),
(1361, 13, 'mugs', 150000, 10, 0),
(1362, 13, 'mugs', 175000, 10, 1),
(1363, 13, 'busts', 5000, 10, 0),
(1364, 13, 'busts', 7500, 10, 0),
(1365, 13, 'busts', 10000, 10, 0),
(1366, 13, 'busts', 12500, 10, 0),
(1367, 13, 'busts', 15000, 10, 1),
(1368, 13, 'busts', 20000, 10, 0),
(1369, 13, 'busts', 25000, 10, 0),
(1370, 13, 'busts', 35000, 10, 0),
(1371, 13, 'busts', 50000, 10, 0),
(1372, 13, 'busts', 70000, 10, 1),
(1373, 13, 'busts', 80000, 10, 0),
(1374, 13, 'busts', 90000, 10, 0),
(1375, 13, 'busts', 100000, 10, 0),
(1376, 13, 'busts', 125000, 10, 0),
(1377, 13, 'busts', 150000, 10, 1),
(1378, 13, 'backalley', 2500, 10, 0),
(1379, 13, 'backalley', 5000, 10, 0),
(1380, 13, 'backalley', 10000, 10, 0),
(1381, 13, 'backalley', 12500, 10, 0),
(1382, 13, 'backalley', 15000, 10, 1),
(1383, 13, 'backalley', 25000, 10, 0),
(1384, 13, 'backalley', 27500, 10, 0),
(1385, 13, 'backalley', 30000, 10, 0),
(1386, 13, 'backalley', 35000, 10, 1),
(1387, 13, 'backalley', 50000, 10, 0),
(1388, 13, 'backalley', 55000, 10, 0),
(1389, 13, 'backalley', 60000, 10, 0),
(1390, 13, 'backalley', 65000, 10, 0),
(1391, 13, 'backalley', 70000, 10, 1),
(1392, 13, 'backalley', 75000, 10, 0),
(1393, 13, 'backalley', 80000, 10, 0),
(1394, 13, 'backalley', 85000, 10, 0),
(1395, 13, 'backalley', 90000, 10, 0),
(1396, 13, 'backalley', 100000, 10, 1),
(1397, 13, 'trains', 100, 10, 0),
(1398, 13, 'trains', 250, 10, 0),
(1399, 13, 'trains', 500, 10, 0),
(1400, 13, 'trains', 600, 10, 0),
(1401, 13, 'trains', 750, 10, 1),
(1402, 13, 'trains', 1000, 10, 0),
(1403, 13, 'trains', 1500, 10, 0),
(1404, 13, 'trains', 2500, 10, 0),
(1405, 13, 'trains', 5000, 10, 0),
(1406, 13, 'trains', 10000, 10, 1),
(1407, 13, 'crimes', 30000000, 10, 0),
(1408, 13, 'attacks', 350000, 10, 0),
(1409, 13, 'mugs', 200000, 10, 0),
(1410, 13, 'busts', 200000, 10, 1),
(1411, 13, 'backalley', 125000, 10, 1),
(1412, 13, 'trains', 25000, 10, 0),
(1413, 13, 'crimes', 500000, 10, 0),
(1414, 13, 'crimes', 1000000, 10, 0),
(1415, 13, 'crimes', 3000000, 10, 0),
(1416, 13, 'crimes', 32000000, 10, 1),
(1417, 13, 'attacks', 2500, 10, 0),
(1418, 13, 'attacks', 60000, 10, 0),
(1419, 13, 'mugs', 500, 10, 0),
(1420, 13, 'busts', 225000, 10, 0),
(1421, 13, 'busts', 1000, 10, 0),
(1422, 13, 'busts', 2500, 10, 1),
(1423, 13, 'backalley', 1000, 10, 0),
(1424, 13, 'backalley', 20000, 10, 1),
(1425, 13, 'backalley', 150000, 10, 0),
(1426, 13, 'trains', 15000, 10, 0),
(1427, 14, 'crimes', 10000, 11, 0),
(1428, 14, 'crimes', 250000, 10, 0),
(1429, 14, 'crimes', 750000, 10, 0),
(1430, 14, 'crimes', 1500000, 10, 0),
(1431, 14, 'crimes', 2000000, 10, 1),
(1432, 14, 'crimes', 5000000, 10, 0),
(1433, 14, 'crimes', 7500000, 10, 0),
(1434, 14, 'crimes', 10000000, 10, 0),
(1435, 14, 'crimes', 11000000, 10, 0),
(1436, 14, 'crimes', 12500000, 10, 1),
(1437, 14, 'crimes', 15000000, 10, 0),
(1438, 14, 'crimes', 17500000, 10, 0),
(1439, 14, 'crimes', 20000000, 10, 0),
(1440, 14, 'crimes', 22000000, 10, 0),
(1441, 14, 'crimes', 23000000, 10, 1),
(1442, 14, 'crimes', 24000000, 10, 0),
(1443, 14, 'crimes', 25000000, 10, 0),
(1444, 14, 'crimes', 26000000, 10, 0),
(1445, 14, 'crimes', 27000000, 10, 0),
(1446, 14, 'crimes', 28000000, 10, 1),
(1447, 14, 'attacks', 5000, 10, 0),
(1448, 14, 'attacks', 10000, 10, 0),
(1449, 14, 'attacks', 25000, 10, 0),
(1450, 14, 'attacks', 40000, 10, 1),
(1451, 14, 'attacks', 50000, 10, 0),
(1452, 14, 'attacks', 75000, 10, 0),
(1453, 14, 'attacks', 90000, 10, 0),
(1454, 14, 'attacks', 100000, 10, 0),
(1455, 14, 'attacks', 125000, 10, 1),
(1456, 14, 'attacks', 150000, 10, 0),
(1457, 14, 'attacks', 200000, 10, 0),
(1458, 14, 'attacks', 225000, 10, 0),
(1459, 14, 'attacks', 250000, 10, 0),
(1460, 14, 'attacks', 300000, 10, 1),
(1461, 14, 'mugs', 1500, 10, 0),
(1462, 14, 'mugs', 3000, 10, 0),
(1463, 14, 'mugs', 7500, 10, 0),
(1464, 14, 'mugs', 10000, 10, 0),
(1465, 14, 'mugs', 15000, 10, 1),
(1466, 14, 'mugs', 20000, 10, 0),
(1467, 14, 'mugs', 25000, 10, 0),
(1468, 14, 'mugs', 30000, 10, 0),
(1469, 14, 'mugs', 50000, 10, 1),
(1470, 14, 'mugs', 60000, 10, 0),
(1471, 14, 'mugs', 70000, 10, 0),
(1472, 14, 'mugs', 80000, 10, 0),
(1473, 14, 'mugs', 90000, 10, 0),
(1474, 14, 'mugs', 100000, 10, 1),
(1475, 14, 'mugs', 110000, 10, 0),
(1476, 14, 'mugs', 125000, 10, 0),
(1477, 14, 'mugs', 140000, 10, 0),
(1478, 14, 'mugs', 150000, 10, 0),
(1479, 14, 'mugs', 175000, 10, 1),
(1480, 14, 'busts', 5000, 10, 0),
(1481, 14, 'busts', 7500, 10, 0),
(1482, 14, 'busts', 10000, 10, 0),
(1483, 14, 'busts', 12500, 10, 0),
(1484, 14, 'busts', 15000, 10, 1),
(1485, 14, 'busts', 20000, 10, 0),
(1486, 14, 'busts', 25000, 10, 0),
(1487, 14, 'busts', 35000, 10, 0),
(1488, 14, 'busts', 50000, 10, 0),
(1489, 14, 'busts', 70000, 10, 1),
(1490, 14, 'busts', 80000, 10, 0),
(1491, 14, 'busts', 90000, 10, 0),
(1492, 14, 'busts', 100000, 10, 0),
(1493, 14, 'busts', 125000, 10, 0),
(1494, 14, 'busts', 150000, 10, 1),
(1495, 14, 'backalley', 2500, 10, 0),
(1496, 14, 'backalley', 5000, 10, 0),
(1497, 14, 'backalley', 10000, 10, 0),
(1498, 14, 'backalley', 12500, 10, 0),
(1499, 14, 'backalley', 15000, 10, 1),
(1500, 14, 'backalley', 25000, 10, 0);
INSERT INTO `bp_category_challenges` (`id`, `bp_category_id`, `type`, `amount`, `prize`, `is_premium`) VALUES
(1501, 14, 'backalley', 27500, 10, 0),
(1502, 14, 'backalley', 30000, 10, 0),
(1503, 14, 'backalley', 35000, 10, 1),
(1504, 14, 'backalley', 50000, 10, 0),
(1505, 14, 'backalley', 55000, 10, 0),
(1506, 14, 'backalley', 60000, 10, 0),
(1507, 14, 'backalley', 65000, 10, 0),
(1508, 14, 'backalley', 70000, 10, 1),
(1509, 14, 'backalley', 75000, 10, 0),
(1510, 14, 'backalley', 80000, 10, 0),
(1511, 14, 'backalley', 85000, 10, 0),
(1512, 14, 'backalley', 90000, 10, 0),
(1513, 14, 'backalley', 100000, 10, 1),
(1514, 14, 'trains', 100, 10, 0),
(1515, 14, 'trains', 250, 10, 0),
(1516, 14, 'trains', 500, 10, 0),
(1517, 14, 'trains', 600, 10, 0),
(1518, 14, 'trains', 750, 10, 1),
(1519, 14, 'trains', 1000, 10, 0),
(1520, 14, 'trains', 1500, 10, 0),
(1521, 14, 'trains', 2500, 10, 0),
(1522, 14, 'trains', 5000, 10, 0),
(1523, 14, 'trains', 10000, 10, 1),
(1524, 14, 'crimes', 30000000, 10, 0),
(1525, 14, 'attacks', 350000, 10, 0),
(1526, 14, 'mugs', 200000, 10, 0),
(1527, 14, 'busts', 200000, 10, 1),
(1528, 14, 'backalley', 125000, 10, 1),
(1529, 14, 'trains', 25000, 10, 0),
(1530, 14, 'crimes', 500000, 10, 0),
(1531, 14, 'crimes', 1000000, 10, 0),
(1532, 14, 'crimes', 3000000, 10, 0),
(1533, 14, 'crimes', 32000000, 10, 1),
(1534, 14, 'attacks', 2500, 10, 0),
(1535, 14, 'attacks', 60000, 10, 0),
(1536, 14, 'mugs', 500, 10, 0),
(1537, 14, 'busts', 225000, 10, 0),
(1538, 14, 'busts', 1000, 10, 0),
(1539, 14, 'busts', 2500, 10, 1),
(1540, 14, 'backalley', 1000, 10, 0),
(1541, 14, 'backalley', 20000, 10, 1),
(1542, 14, 'backalley', 150000, 10, 0),
(1543, 14, 'trains', 15000, 10, 0),
(1544, 15, 'crimes', 10000, 10, 0),
(1545, 15, 'crimes', 250000, 10, 0),
(1546, 15, 'crimes', 750000, 10, 0),
(1547, 15, 'crimes', 1500000, 10, 0),
(1548, 15, 'crimes', 2000000, 10, 1),
(1549, 15, 'crimes', 5000000, 10, 0),
(1550, 15, 'crimes', 7500000, 10, 0),
(1551, 15, 'crimes', 10000000, 10, 0),
(1552, 15, 'crimes', 11000000, 10, 0),
(1553, 15, 'crimes', 12500000, 10, 1),
(1554, 15, 'crimes', 15000000, 10, 0),
(1555, 15, 'crimes', 17500000, 10, 0),
(1556, 15, 'crimes', 20000000, 10, 0),
(1557, 15, 'crimes', 22000000, 10, 0),
(1558, 15, 'crimes', 23000000, 10, 1),
(1559, 15, 'crimes', 24000000, 10, 0),
(1560, 15, 'crimes', 25000000, 10, 0),
(1561, 15, 'crimes', 26000000, 10, 0),
(1562, 15, 'crimes', 27000000, 10, 0),
(1563, 15, 'crimes', 28000000, 10, 1),
(1564, 15, 'attacks', 5000, 10, 0),
(1565, 15, 'attacks', 10000, 10, 0),
(1566, 15, 'attacks', 25000, 10, 0),
(1567, 15, 'attacks', 40000, 10, 1),
(1568, 15, 'attacks', 50000, 10, 0),
(1569, 15, 'attacks', 75000, 10, 0),
(1570, 15, 'attacks', 90000, 10, 0),
(1571, 15, 'attacks', 100000, 10, 0),
(1572, 15, 'attacks', 125000, 10, 1),
(1573, 15, 'attacks', 150000, 10, 0),
(1574, 15, 'attacks', 200000, 10, 0),
(1575, 15, 'attacks', 225000, 10, 0),
(1576, 15, 'attacks', 250000, 10, 0),
(1577, 15, 'attacks', 300000, 10, 1),
(1578, 15, 'mugs', 1500, 10, 0),
(1579, 15, 'mugs', 3000, 10, 0),
(1580, 15, 'mugs', 7500, 10, 0),
(1581, 15, 'mugs', 10000, 10, 0),
(1582, 15, 'mugs', 15000, 10, 1),
(1583, 15, 'mugs', 20000, 10, 0),
(1584, 15, 'mugs', 25000, 10, 0),
(1585, 15, 'mugs', 30000, 10, 0),
(1586, 15, 'mugs', 50000, 10, 1),
(1587, 15, 'mugs', 60000, 10, 0),
(1588, 15, 'mugs', 70000, 10, 0),
(1589, 15, 'mugs', 80000, 10, 0),
(1590, 15, 'mugs', 90000, 10, 0),
(1591, 15, 'mugs', 100000, 10, 1),
(1592, 15, 'mugs', 110000, 10, 0),
(1593, 15, 'mugs', 125000, 10, 0),
(1594, 15, 'mugs', 140000, 10, 0),
(1595, 15, 'mugs', 150000, 10, 0),
(1596, 15, 'mugs', 175000, 10, 1),
(1597, 15, 'busts', 5000, 10, 0),
(1598, 15, 'busts', 7500, 10, 0),
(1599, 15, 'busts', 10000, 10, 0),
(1600, 15, 'busts', 12500, 10, 0),
(1601, 15, 'busts', 15000, 10, 1),
(1602, 15, 'busts', 20000, 10, 0),
(1603, 15, 'busts', 25000, 10, 0),
(1604, 15, 'busts', 35000, 10, 0),
(1605, 15, 'busts', 50000, 10, 0),
(1606, 15, 'busts', 70000, 10, 1),
(1607, 15, 'busts', 80000, 10, 0),
(1608, 15, 'busts', 90000, 10, 0),
(1609, 15, 'busts', 100000, 10, 0),
(1610, 15, 'busts', 125000, 10, 0),
(1611, 15, 'busts', 150000, 10, 1),
(1612, 15, 'backalley', 2500, 10, 0),
(1613, 15, 'backalley', 5000, 10, 0),
(1614, 15, 'backalley', 10000, 10, 0),
(1615, 15, 'backalley', 12500, 10, 0),
(1616, 15, 'backalley', 15000, 10, 1),
(1617, 15, 'backalley', 25000, 10, 0),
(1618, 15, 'backalley', 27500, 10, 0),
(1619, 15, 'backalley', 30000, 10, 0),
(1620, 15, 'backalley', 35000, 10, 1),
(1621, 15, 'backalley', 50000, 10, 0),
(1622, 15, 'backalley', 55000, 10, 0),
(1623, 15, 'backalley', 60000, 10, 0),
(1624, 15, 'backalley', 65000, 10, 0),
(1625, 15, 'backalley', 70000, 10, 1),
(1626, 15, 'backalley', 75000, 10, 0),
(1627, 15, 'backalley', 80000, 10, 0),
(1628, 15, 'backalley', 85000, 10, 0),
(1629, 15, 'backalley', 90000, 10, 0),
(1630, 15, 'backalley', 100000, 10, 1),
(1631, 15, 'trains', 100, 10, 0),
(1632, 15, 'trains', 250, 10, 0),
(1633, 15, 'trains', 500, 10, 0),
(1634, 15, 'trains', 600, 10, 0),
(1635, 15, 'trains', 750, 10, 1),
(1636, 15, 'trains', 1000, 10, 0),
(1637, 15, 'trains', 1500, 10, 0),
(1638, 15, 'trains', 2500, 10, 0),
(1639, 15, 'trains', 5000, 10, 0),
(1640, 15, 'trains', 10000, 10, 1),
(1641, 15, 'crimes', 30000000, 10, 0),
(1642, 15, 'attacks', 350000, 10, 0),
(1643, 15, 'mugs', 200000, 10, 0),
(1644, 15, 'busts', 200000, 10, 1),
(1645, 15, 'backalley', 125000, 10, 1),
(1646, 15, 'trains', 25000, 10, 0),
(1647, 15, 'crimes', 500000, 10, 0),
(1648, 15, 'crimes', 1000000, 10, 0),
(1649, 15, 'crimes', 3000000, 10, 0),
(1650, 15, 'crimes', 32000000, 10, 1),
(1651, 15, 'attacks', 2500, 10, 0),
(1652, 15, 'attacks', 60000, 10, 0),
(1653, 15, 'mugs', 500, 10, 0),
(1654, 15, 'busts', 225000, 10, 0),
(1655, 15, 'busts', 1000, 10, 0),
(1656, 15, 'busts', 2500, 10, 1),
(1657, 15, 'backalley', 1000, 10, 0),
(1658, 15, 'backalley', 20000, 10, 1),
(1659, 15, 'backalley', 150000, 10, 0),
(1660, 15, 'trains', 15000, 10, 0),
(1661, 16, 'crimes', 10000, 10, 0),
(1662, 16, 'crimes', 250000, 10, 0),
(1663, 16, 'crimes', 750000, 10, 0),
(1664, 16, 'crimes', 1500000, 10, 0),
(1665, 16, 'crimes', 2000000, 10, 1),
(1666, 16, 'crimes', 5000000, 10, 0),
(1667, 16, 'crimes', 7500000, 10, 0),
(1668, 16, 'crimes', 10000000, 10, 0),
(1669, 16, 'crimes', 11000000, 10, 0),
(1670, 16, 'crimes', 12500000, 10, 1),
(1671, 16, 'crimes', 15000000, 10, 0),
(1672, 16, 'crimes', 17500000, 10, 0),
(1673, 16, 'crimes', 20000000, 10, 0),
(1674, 16, 'crimes', 22000000, 10, 0),
(1675, 16, 'crimes', 23000000, 10, 1),
(1676, 16, 'crimes', 24000000, 10, 0),
(1677, 16, 'crimes', 25000000, 10, 0),
(1678, 16, 'crimes', 26000000, 10, 0),
(1679, 16, 'crimes', 27000000, 10, 0),
(1680, 16, 'crimes', 28000000, 10, 1),
(1681, 16, 'attacks', 5000, 10, 0),
(1682, 16, 'attacks', 10000, 10, 0),
(1683, 16, 'attacks', 25000, 10, 0),
(1684, 16, 'attacks', 40000, 10, 1),
(1685, 16, 'attacks', 50000, 10, 0),
(1686, 16, 'attacks', 75000, 10, 0),
(1687, 16, 'attacks', 90000, 10, 0),
(1688, 16, 'attacks', 100000, 10, 0),
(1689, 16, 'attacks', 125000, 10, 1),
(1690, 16, 'attacks', 150000, 10, 0),
(1691, 16, 'attacks', 200000, 10, 0),
(1692, 16, 'attacks', 225000, 10, 0),
(1693, 16, 'attacks', 250000, 10, 0),
(1694, 16, 'attacks', 300000, 10, 1),
(1695, 16, 'mugs', 1500, 10, 0),
(1696, 16, 'mugs', 3000, 10, 0),
(1697, 16, 'mugs', 7500, 10, 0),
(1698, 16, 'mugs', 10000, 10, 0),
(1699, 16, 'mugs', 15000, 10, 1),
(1700, 16, 'mugs', 20000, 10, 0),
(1701, 16, 'mugs', 25000, 10, 0),
(1702, 16, 'mugs', 30000, 10, 0),
(1703, 16, 'mugs', 50000, 10, 1),
(1704, 16, 'mugs', 60000, 10, 0),
(1705, 16, 'mugs', 70000, 10, 0),
(1706, 16, 'mugs', 80000, 10, 0),
(1707, 16, 'mugs', 90000, 10, 0),
(1708, 16, 'mugs', 100000, 10, 1),
(1709, 16, 'mugs', 110000, 10, 0),
(1710, 16, 'mugs', 125000, 10, 0),
(1711, 16, 'mugs', 140000, 10, 0),
(1712, 16, 'mugs', 150000, 10, 0),
(1713, 16, 'mugs', 175000, 10, 1),
(1714, 16, 'busts', 5000, 10, 0),
(1715, 16, 'busts', 7500, 10, 0),
(1716, 16, 'busts', 10000, 10, 0),
(1717, 16, 'busts', 12500, 10, 0),
(1718, 16, 'busts', 15000, 10, 1),
(1719, 16, 'busts', 20000, 10, 0),
(1720, 16, 'busts', 25000, 10, 0),
(1721, 16, 'busts', 35000, 10, 0),
(1722, 16, 'busts', 50000, 10, 0),
(1723, 16, 'busts', 70000, 10, 1),
(1724, 16, 'busts', 80000, 10, 0),
(1725, 16, 'busts', 90000, 10, 0),
(1726, 16, 'busts', 100000, 10, 0),
(1727, 16, 'busts', 125000, 10, 0),
(1728, 16, 'busts', 150000, 10, 1),
(1729, 16, 'backalley', 2500, 10, 0),
(1730, 16, 'backalley', 5000, 10, 0),
(1731, 16, 'backalley', 10000, 10, 0),
(1732, 16, 'backalley', 12500, 10, 0),
(1733, 16, 'backalley', 15000, 10, 1),
(1734, 16, 'backalley', 25000, 10, 0),
(1735, 16, 'backalley', 27500, 10, 0),
(1736, 16, 'backalley', 30000, 10, 0),
(1737, 16, 'backalley', 35000, 10, 1),
(1738, 16, 'backalley', 50000, 10, 0),
(1739, 16, 'backalley', 55000, 10, 0),
(1740, 16, 'backalley', 60000, 10, 0),
(1741, 16, 'backalley', 65000, 10, 0),
(1742, 16, 'backalley', 70000, 10, 1),
(1743, 16, 'backalley', 75000, 10, 0),
(1744, 16, 'backalley', 80000, 10, 0),
(1745, 16, 'backalley', 85000, 10, 0),
(1746, 16, 'backalley', 90000, 10, 0),
(1747, 16, 'backalley', 100000, 10, 1),
(1748, 16, 'trains', 100, 10, 0),
(1749, 16, 'trains', 250, 10, 0),
(1750, 16, 'trains', 500, 10, 0),
(1751, 16, 'trains', 600, 10, 0),
(1752, 16, 'trains', 750, 10, 1),
(1753, 16, 'trains', 1000, 10, 0),
(1754, 16, 'trains', 1500, 10, 0),
(1755, 16, 'trains', 2500, 10, 0),
(1756, 16, 'trains', 5000, 10, 0),
(1757, 16, 'trains', 10000, 10, 1),
(1758, 16, 'crimes', 30000000, 10, 0),
(1759, 16, 'attacks', 350000, 10, 0),
(1760, 16, 'mugs', 200000, 10, 0),
(1761, 16, 'busts', 200000, 10, 1),
(1762, 16, 'backalley', 125000, 10, 1),
(1763, 16, 'trains', 25000, 10, 0),
(1764, 16, 'crimes', 500000, 10, 0),
(1765, 16, 'crimes', 1000000, 10, 0),
(1766, 16, 'crimes', 3000000, 10, 0),
(1767, 16, 'crimes', 32000000, 10, 1),
(1768, 16, 'attacks', 2500, 10, 0),
(1769, 16, 'attacks', 60000, 10, 0),
(1770, 16, 'mugs', 500, 10, 0),
(1771, 16, 'busts', 225000, 10, 0),
(1772, 16, 'busts', 1000, 10, 0),
(1773, 16, 'busts', 2500, 10, 1),
(1774, 16, 'backalley', 1000, 10, 0),
(1775, 16, 'backalley', 20000, 10, 1),
(1776, 16, 'backalley', 150000, 10, 0),
(1777, 16, 'trains', 15000, 10, 0),
(1778, 17, 'crimes', 10000, 11, 0),
(1779, 17, 'crimes', 250000, 10, 0),
(1780, 17, 'crimes', 750000, 10, 0),
(1781, 17, 'crimes', 1500000, 10, 0),
(1782, 17, 'crimes', 2000000, 10, 1),
(1783, 17, 'crimes', 5000000, 10, 0),
(1784, 17, 'crimes', 7500000, 10, 0),
(1785, 17, 'crimes', 10000000, 10, 0),
(1786, 17, 'crimes', 11000000, 10, 0),
(1787, 17, 'crimes', 12500000, 10, 1),
(1788, 17, 'crimes', 15000000, 10, 0),
(1789, 17, 'crimes', 17500000, 10, 0),
(1790, 17, 'crimes', 20000000, 10, 0),
(1791, 17, 'crimes', 22000000, 10, 0),
(1792, 17, 'crimes', 23000000, 10, 1),
(1793, 17, 'crimes', 24000000, 10, 0),
(1794, 17, 'crimes', 25000000, 10, 0),
(1795, 17, 'crimes', 26000000, 10, 0),
(1796, 17, 'crimes', 27000000, 10, 0),
(1797, 17, 'crimes', 28000000, 10, 1),
(1798, 17, 'attacks', 5000, 10, 0),
(1799, 17, 'attacks', 10000, 10, 0),
(1800, 17, 'attacks', 25000, 10, 0),
(1801, 17, 'attacks', 40000, 10, 1),
(1802, 17, 'attacks', 50000, 10, 0),
(1803, 17, 'attacks', 75000, 10, 0),
(1804, 17, 'attacks', 90000, 10, 0),
(1805, 17, 'attacks', 100000, 10, 0),
(1806, 17, 'attacks', 125000, 10, 1),
(1807, 17, 'attacks', 150000, 10, 0),
(1808, 17, 'attacks', 200000, 10, 0),
(1809, 17, 'attacks', 225000, 10, 0),
(1810, 17, 'attacks', 250000, 10, 0),
(1811, 17, 'attacks', 300000, 10, 1),
(1812, 17, 'mugs', 1500, 10, 0),
(1813, 17, 'mugs', 3000, 10, 0),
(1814, 17, 'mugs', 7500, 10, 0),
(1815, 17, 'mugs', 10000, 10, 0),
(1816, 17, 'mugs', 15000, 10, 1),
(1817, 17, 'mugs', 20000, 10, 0),
(1818, 17, 'mugs', 25000, 10, 0),
(1819, 17, 'mugs', 30000, 10, 0),
(1820, 17, 'mugs', 50000, 10, 1),
(1821, 17, 'mugs', 60000, 10, 0),
(1822, 17, 'mugs', 70000, 10, 0),
(1823, 17, 'mugs', 80000, 10, 0),
(1824, 17, 'mugs', 90000, 10, 0),
(1825, 17, 'mugs', 100000, 10, 1),
(1826, 17, 'mugs', 110000, 10, 0),
(1827, 17, 'mugs', 125000, 10, 0),
(1828, 17, 'mugs', 140000, 10, 0),
(1829, 17, 'mugs', 150000, 10, 0),
(1830, 17, 'mugs', 175000, 10, 1),
(1831, 17, 'busts', 5000, 10, 0),
(1832, 17, 'busts', 7500, 10, 0),
(1833, 17, 'busts', 10000, 10, 0),
(1834, 17, 'busts', 12500, 10, 0),
(1835, 17, 'busts', 15000, 10, 1),
(1836, 17, 'busts', 20000, 10, 0),
(1837, 17, 'busts', 25000, 10, 0),
(1838, 17, 'busts', 35000, 10, 0),
(1839, 17, 'busts', 50000, 10, 0),
(1840, 17, 'busts', 70000, 10, 1),
(1841, 17, 'busts', 80000, 10, 0),
(1842, 17, 'busts', 90000, 10, 0),
(1843, 17, 'busts', 100000, 10, 0),
(1844, 17, 'busts', 125000, 10, 0),
(1845, 17, 'busts', 150000, 10, 1),
(1846, 17, 'backalley', 2500, 10, 0),
(1847, 17, 'backalley', 5000, 10, 0),
(1848, 17, 'backalley', 10000, 10, 0),
(1849, 17, 'backalley', 12500, 10, 0),
(1850, 17, 'backalley', 15000, 10, 1),
(1851, 17, 'backalley', 25000, 10, 0),
(1852, 17, 'backalley', 27500, 10, 0),
(1853, 17, 'backalley', 30000, 10, 0),
(1854, 17, 'backalley', 35000, 10, 1),
(1855, 17, 'backalley', 50000, 10, 0),
(1856, 17, 'backalley', 55000, 10, 0),
(1857, 17, 'backalley', 60000, 10, 0),
(1858, 17, 'backalley', 65000, 10, 0),
(1859, 17, 'backalley', 70000, 10, 1),
(1860, 17, 'backalley', 75000, 10, 0),
(1861, 17, 'backalley', 80000, 10, 0),
(1862, 17, 'backalley', 85000, 10, 0),
(1863, 17, 'backalley', 90000, 10, 0),
(1864, 17, 'backalley', 100000, 10, 1),
(1865, 17, 'trains', 100, 10, 0),
(1866, 17, 'trains', 250, 10, 0),
(1867, 17, 'trains', 500, 10, 0),
(1868, 17, 'trains', 600, 10, 0),
(1869, 17, 'trains', 750, 10, 1),
(1870, 17, 'trains', 1000, 10, 0),
(1871, 17, 'trains', 1500, 10, 0),
(1872, 17, 'trains', 2500, 10, 0),
(1873, 17, 'trains', 5000, 10, 0),
(1874, 17, 'trains', 10000, 10, 1),
(1875, 17, 'crimes', 30000000, 10, 0),
(1876, 17, 'attacks', 350000, 10, 0),
(1877, 17, 'mugs', 200000, 10, 0),
(1878, 17, 'busts', 200000, 10, 1),
(1879, 17, 'backalley', 125000, 10, 1),
(1880, 17, 'trains', 25000, 10, 0),
(1881, 17, 'crimes', 500000, 10, 0),
(1882, 17, 'crimes', 1000000, 10, 0),
(1883, 17, 'crimes', 3000000, 10, 0),
(1884, 17, 'crimes', 32000000, 10, 1),
(1885, 17, 'attacks', 2500, 10, 0),
(1886, 17, 'attacks', 60000, 10, 0),
(1887, 17, 'mugs', 500, 10, 0),
(1888, 17, 'busts', 225000, 10, 0),
(1889, 17, 'busts', 1000, 10, 0),
(1890, 17, 'busts', 2500, 10, 1),
(1891, 17, 'backalley', 1000, 10, 0),
(1892, 17, 'backalley', 20000, 10, 1),
(1893, 17, 'backalley', 150000, 10, 0),
(1894, 17, 'trains', 15000, 10, 0),
(1895, 18, 'crimes', 7500, 11, 0),
(1896, 18, 'crimes', 200000, 10, 0),
(1897, 18, 'crimes', 500000, 10, 0),
(1898, 18, 'crimes', 1000000, 10, 0),
(1899, 18, 'crimes', 1500000, 10, 1),
(1900, 18, 'crimes', 3500000, 10, 0),
(1901, 18, 'crimes', 7500000, 10, 0),
(1902, 18, 'crimes', 9000000, 10, 0),
(1903, 18, 'crimes', 10000000, 10, 0),
(1904, 18, 'crimes', 11000000, 10, 1),
(1905, 18, 'crimes', 12500000, 10, 0),
(1906, 18, 'crimes', 15000000, 10, 0),
(1907, 18, 'crimes', 17500000, 10, 0),
(1908, 18, 'crimes', 18000000, 10, 0),
(1909, 18, 'crimes', 19000000, 10, 1),
(1910, 18, 'crimes', 20000000, 10, 0),
(1911, 18, 'crimes', 21000000, 10, 0),
(1912, 18, 'crimes', 22000000, 10, 0),
(1913, 18, 'crimes', 23000000, 10, 0),
(1914, 18, 'crimes', 24000000, 10, 1),
(1915, 18, 'attacks', 5000, 10, 0),
(1916, 18, 'attacks', 10000, 10, 0),
(1917, 18, 'attacks', 25000, 10, 0),
(1918, 18, 'attacks', 40000, 10, 1),
(1919, 18, 'attacks', 50000, 10, 0),
(1920, 18, 'attacks', 75000, 10, 0),
(1921, 18, 'attacks', 90000, 10, 0),
(1922, 18, 'attacks', 100000, 10, 0),
(1923, 18, 'attacks', 125000, 10, 1),
(1924, 18, 'attacks', 150000, 10, 0),
(1925, 18, 'attacks', 200000, 10, 0),
(1926, 18, 'attacks', 225000, 10, 0),
(1927, 18, 'attacks', 250000, 10, 0),
(1928, 18, 'attacks', 300000, 10, 1),
(1929, 18, 'mugs', 1500, 10, 0),
(1930, 18, 'mugs', 3000, 10, 0),
(1931, 18, 'mugs', 7500, 10, 0),
(1932, 18, 'mugs', 10000, 10, 0),
(1933, 18, 'mugs', 15000, 10, 1),
(1934, 18, 'mugs', 20000, 10, 0),
(1935, 18, 'mugs', 25000, 10, 0),
(1936, 18, 'mugs', 30000, 10, 0),
(1937, 18, 'mugs', 50000, 10, 1),
(1938, 18, 'mugs', 60000, 10, 0),
(1939, 18, 'mugs', 70000, 10, 0),
(1940, 18, 'mugs', 80000, 10, 0),
(1941, 18, 'mugs', 90000, 10, 0),
(1942, 18, 'mugs', 100000, 10, 1),
(1943, 18, 'mugs', 110000, 10, 0),
(1944, 18, 'mugs', 125000, 10, 0),
(1945, 18, 'mugs', 140000, 10, 0),
(1946, 18, 'mugs', 150000, 10, 0),
(1947, 18, 'mugs', 175000, 10, 1),
(1948, 18, 'busts', 5000, 10, 0),
(1949, 18, 'busts', 7500, 10, 0),
(1950, 18, 'busts', 10000, 10, 0),
(1951, 18, 'busts', 12500, 10, 0),
(1952, 18, 'busts', 15000, 10, 1),
(1953, 18, 'busts', 20000, 10, 0),
(1954, 18, 'busts', 25000, 10, 0),
(1955, 18, 'busts', 35000, 10, 0),
(1956, 18, 'busts', 50000, 10, 0),
(1957, 18, 'busts', 70000, 10, 1),
(1958, 18, 'busts', 80000, 10, 0),
(1959, 18, 'busts', 90000, 10, 0),
(1960, 18, 'busts', 100000, 10, 0),
(1961, 18, 'busts', 125000, 10, 0),
(1962, 18, 'busts', 150000, 10, 1),
(1963, 18, 'backalley', 2500, 10, 0),
(1964, 18, 'backalley', 5000, 10, 0),
(1965, 18, 'backalley', 10000, 10, 0),
(1966, 18, 'backalley', 12500, 10, 0),
(1967, 18, 'backalley', 15000, 10, 1),
(1968, 18, 'backalley', 25000, 10, 0),
(1969, 18, 'backalley', 27500, 10, 0),
(1970, 18, 'backalley', 30000, 10, 0),
(1971, 18, 'backalley', 35000, 10, 1),
(1972, 18, 'backalley', 50000, 10, 0),
(1973, 18, 'backalley', 55000, 10, 0),
(1974, 18, 'backalley', 60000, 10, 0),
(1975, 18, 'backalley', 65000, 10, 0),
(1976, 18, 'backalley', 70000, 10, 1),
(1977, 18, 'backalley', 75000, 10, 0),
(1978, 18, 'backalley', 80000, 10, 0),
(1979, 18, 'backalley', 85000, 10, 0),
(1980, 18, 'backalley', 90000, 10, 0),
(1981, 18, 'backalley', 100000, 10, 1),
(1982, 18, 'trains', 100, 10, 0),
(1983, 18, 'trains', 250, 10, 0),
(1984, 18, 'trains', 500, 10, 0),
(1985, 18, 'trains', 600, 10, 0),
(1986, 18, 'trains', 750, 10, 1),
(1987, 18, 'trains', 1000, 10, 0),
(1988, 18, 'trains', 1500, 10, 0),
(1989, 18, 'trains', 2500, 10, 0),
(1990, 18, 'trains', 5000, 10, 0),
(1991, 18, 'trains', 10000, 10, 1),
(1992, 18, 'crimes', 25000000, 10, 0),
(1993, 18, 'attacks', 350000, 10, 0),
(1994, 18, 'mugs', 200000, 10, 0),
(1995, 18, 'busts', 200000, 10, 1),
(1996, 18, 'backalley', 125000, 10, 1),
(1997, 18, 'trains', 25000, 10, 0),
(1998, 18, 'crimes', 500000, 10, 0),
(1999, 18, 'crimes', 750000, 10, 0),
(2000, 18, 'crimes', 1500000, 10, 0),
(2001, 18, 'crimes', 28000000, 10, 1),
(2002, 18, 'attacks', 2500, 10, 0),
(2003, 18, 'attacks', 60000, 10, 0),
(2004, 18, 'mugs', 500, 10, 0),
(2005, 18, 'busts', 225000, 10, 0),
(2006, 18, 'busts', 1000, 10, 0),
(2007, 18, 'busts', 2500, 10, 1),
(2008, 18, 'backalley', 1000, 10, 0),
(2009, 18, 'backalley', 20000, 10, 1),
(2010, 18, 'backalley', 150000, 10, 0),
(2011, 18, 'trains', 15000, 10, 0);

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `bp_category_prizes`
--

CREATE TABLE `bp_category_prizes` (
  `id` int(11) NOT NULL,
  `bp_category_id` int(11) NOT NULL,
  `cost` int(11) NOT NULL DEFAULT 0,
  `type` varchar(200) NOT NULL,
  `amount` int(11) NOT NULL DEFAULT 0,
  `entity_id` int(11) NOT NULL DEFAULT 0,
  `is_premium` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Data dump for tabellen `bp_category_prizes`
--

INSERT INTO `bp_category_prizes` (`id`, `bp_category_id`, `cost`, `type`, `amount`, `entity_id`, `is_premium`) VALUES
(1, 1, 10, 'points', 1000, 0, 0),
(2, 1, 20, 'item', 10, 14, 0),
(3, 1, 40, 'money', 10000000, 0, 0),
(4, 1, 50, 'points', 10000, 0, 0),
(5, 1, 70, 'raid_tokens', 50, 0, 0),
(6, 1, 80, 'item', 3, 42, 0),
(7, 1, 90, 'item', 1, 283, 0),
(8, 1, 120, 'points', 10000, 0, 0),
(9, 1, 130, 'money', 25000000, 0, 0),
(10, 1, 150, 'item', 5, 10, 0),
(11, 1, 180, 'item', 5, 163, 0),
(12, 1, 200, 'raid_tokens', 50, 0, 0),
(13, 1, 230, 'points', 50000, 0, 0),
(14, 1, 250, 'item', 2, 283, 0),
(15, 1, 260, 'money', 25000000, 0, 0),
(16, 1, 280, 'points', 100000, 0, 0),
(17, 1, 290, 'item', 5, 255, 0),
(18, 1, 300, 'item', 1, 256, 0),
(19, 1, 320, 'item', 5, 194, 0),
(20, 1, 330, 'points', 200000, 0, 0),
(21, 1, 350, 'item', 1, 278, 0),
(22, 1, 370, 'points', 250000, 0, 0),
(23, 1, 390, 'money', 100000000, 0, 0),
(24, 1, 400, 'item', 5, 252, 0),
(25, 2, 10, 'points', 5000, 0, 0),
(26, 2, 20, 'money', 10000000, 0, 0),
(27, 2, 30, 'raid_tokens', 10, 0, 0),
(28, 2, 40, 'points', 50000, 0, 0),
(29, 2, 50, 'item', 5, 42, 1),
(30, 2, 70, 'money', 25000000, 0, 0),
(31, 2, 80, 'item', 2, 283, 0),
(32, 2, 90, 'points', 75000, 0, 0),
(33, 2, 100, 'item', 5, 163, 1),
(34, 2, 110, 'exp', 50, 0, 0),
(35, 2, 120, 'points', 100000, 0, 0),
(36, 2, 130, 'item', 5, 194, 0),
(37, 2, 140, 'money', 50000000, 0, 0),
(38, 2, 150, 'item', 5, 255, 1),
(39, 2, 170, 'raid_tokens', 20, 0, 0),
(40, 2, 180, 'item', 2, 283, 0),
(41, 2, 190, 'money', 50000000, 0, 0),
(42, 2, 200, 'points', 250000, 0, 1),
(43, 2, 210, 'exp', 75, 0, 0),
(44, 2, 220, 'raid_tokens', 20, 0, 0),
(45, 2, 230, 'points', 120000, 0, 0),
(46, 2, 240, 'item', 3, 253, 0),
(47, 2, 250, 'item', 5, 163, 1),
(48, 2, 270, 'raid_tokens', 20, 0, 0),
(49, 2, 280, 'exp', 100, 0, 0),
(50, 2, 290, 'money', 100000000, 0, 0),
(51, 2, 300, 'item', 1, 256, 1),
(52, 2, 320, 'item', 3, 254, 0),
(53, 2, 330, 'item', 5, 251, 0),
(54, 2, 340, 'raid_tokens', 30, 0, 0),
(55, 2, 350, 'item', 10, 252, 1),
(56, 2, 370, 'money', 125000000, 0, 0),
(57, 2, 380, 'exp', 100, 0, 0),
(58, 2, 390, 'item', 5, 42, 0),
(59, 2, 400, 'item', 5, 255, 1),
(60, 2, 420, 'vip', 7, 0, 0),
(61, 2, 380, 'money', 150000000, 0, 0),
(62, 2, 390, 'points', 150000, 0, 0),
(63, 2, 400, 'points', 200000, 0, 1),
(64, 2, 420, 'raid_tokens', 40, 0, 0),
(65, 2, 440, 'money', 200000000, 0, 0),
(66, 2, 450, 'item', 5, 163, 1),
(67, 2, 470, 'item', 2, 253, 0),
(68, 2, 490, 'exp', 100, 0, 0),
(69, 2, 500, 'points', 200000, 0, 1),
(70, 2, 520, 'item', 3, 255, 0),
(71, 2, 540, 'item', 3, 251, 0),
(72, 2, 550, 'item', 5, 252, 1),
(73, 2, 570, 'points', 175000, 0, 0),
(74, 2, 590, 'raid_tokens', 40, 0, 0),
(75, 2, 600, 'points', 200000, 0, 1),
(76, 2, 620, 'exp', 100, 0, 0),
(77, 2, 640, 'money', 200000000, 0, 0),
(78, 2, 650, 'item', 3, 253, 2),
(79, 2, 670, 'raid_tokens', 40, 0, 0),
(80, 2, 690, 'item', 2, 252, 0),
(81, 2, 700, 'item', 10, 42, 1),
(82, 2, 750, 'points', 175000, 0, 0),
(83, 2, 800, 'exp', 100, 0, 0),
(84, 2, 850, 'item', 5, 163, 1),
(85, 2, 900, 'item', 3, 42, 0),
(86, 2, 920, 'money', 200000000, 0, 0),
(87, 2, 940, 'item', 1, 256, 0),
(88, 2, 950, 'points', 175000, 0, 0),
(89, 2, 970, 'raid_tokens', 50, 0, 0),
(90, 2, 990, 'points', 250000, 0, 1),
(91, 3, 10, 'points', 5000, 0, 0),
(92, 3, 20, 'money', 10000000, 0, 0),
(93, 3, 30, 'raid_tokens', 15, 0, 0),
(94, 3, 40, 'points', 50000, 0, 0),
(95, 3, 50, 'item', 5, 42, 1),
(96, 3, 70, 'money', 25000000, 0, 0),
(97, 3, 80, 'item', 5, 253, 0),
(98, 3, 90, 'points', 100000, 0, 0),
(99, 3, 100, 'item', 5, 163, 1),
(100, 3, 110, 'exp', 100, 0, 0),
(101, 3, 120, 'points', 100000, 0, 0),
(102, 3, 130, 'item', 5, 194, 0),
(103, 3, 140, 'money', 50000000, 0, 0),
(104, 3, 150, 'item', 5, 255, 1),
(105, 3, 170, 'raid_tokens', 30, 0, 0),
(106, 3, 180, 'item', 10, 253, 0),
(107, 3, 190, 'money', 50000000, 0, 0),
(108, 3, 200, 'points', 300000, 0, 1),
(109, 3, 210, 'exp', 100, 0, 0),
(110, 3, 220, 'raid_tokens', 20, 0, 0),
(111, 3, 230, 'points', 120000, 0, 0),
(112, 3, 240, 'item', 10, 253, 0),
(113, 3, 250, 'item', 10, 163, 1),
(114, 3, 270, 'raid_tokens', 30, 0, 0),
(115, 3, 280, 'exp', 100, 0, 0),
(116, 3, 290, 'money', 100000000, 0, 0),
(117, 3, 300, 'item', 1, 256, 1),
(118, 3, 320, 'item', 3, 254, 0),
(119, 3, 330, 'item', 5, 251, 0),
(120, 3, 340, 'raid_tokens', 30, 0, 0),
(121, 3, 350, 'item', 10, 252, 1),
(122, 3, 370, 'money', 125000000, 0, 0),
(123, 3, 380, 'exp', 100, 0, 0),
(124, 3, 390, 'item', 5, 42, 0),
(125, 3, 400, 'item', 5, 255, 1),
(126, 3, 420, 'vip', 7, 0, 0),
(127, 3, 380, 'money', 150000000, 0, 0),
(128, 3, 390, 'points', 150000, 0, 0),
(129, 3, 400, 'points', 300000, 0, 1),
(130, 3, 420, 'raid_tokens', 40, 0, 0),
(131, 3, 440, 'money', 200000000, 0, 0),
(132, 3, 450, 'item', 1, 265, 1),
(133, 3, 470, 'item', 5, 253, 0),
(134, 3, 490, 'exp', 100, 0, 0),
(135, 3, 500, 'points', 200000, 0, 1),
(136, 3, 520, 'item', 3, 194, 0),
(137, 3, 540, 'item', 3, 251, 0),
(138, 3, 550, 'item', 5, 252, 1),
(139, 3, 570, 'points', 175000, 0, 0),
(140, 3, 590, 'raid_tokens', 40, 0, 0),
(141, 3, 600, 'points', 200000, 0, 1),
(142, 3, 620, 'exp', 100, 0, 0),
(143, 3, 640, 'money', 200000000, 0, 0),
(144, 3, 650, 'item', 20, 253, 2),
(145, 3, 670, 'raid_tokens', 40, 0, 0),
(146, 3, 690, 'item', 2, 252, 0),
(147, 3, 700, 'item', 10, 42, 1),
(148, 3, 750, 'points', 175000, 0, 0),
(149, 3, 800, 'exp', 100, 0, 0),
(150, 3, 850, 'item', 5, 163, 1),
(151, 3, 900, 'item', 3, 42, 0),
(152, 3, 920, 'money', 200000000, 0, 0),
(153, 3, 940, 'item', 1, 256, 0),
(154, 3, 950, 'points', 200000, 0, 0),
(155, 3, 970, 'raid_tokens', 50, 0, 0),
(156, 3, 990, 'points', 350000, 0, 1),
(157, 3, 1000, 'item', 5, 273, 0),
(158, 3, 1010, 'item', 10, 42, 0),
(159, 3, 1020, 'points', 250000, 0, 0),
(160, 3, 1030, 'item', 1, 257, 1),
(161, 3, 1040, 'item', 3, 276, 1),
(162, 4, 10, 'points', 5000, 0, 0),
(163, 4, 20, 'money', 10000000, 0, 0),
(164, 4, 30, 'raid_tokens', 15, 0, 0),
(165, 4, 40, 'points', 50000, 0, 0),
(166, 4, 50, 'item', 3, 267, 1),
(167, 4, 70, 'money', 50000000, 0, 0),
(168, 4, 80, 'item', 5, 253, 0),
(169, 4, 90, 'points', 100000, 0, 0),
(170, 4, 100, 'item', 5, 163, 1),
(171, 4, 110, 'exp', 100, 0, 0),
(172, 4, 120, 'points', 100000, 0, 0),
(173, 4, 130, 'item', 1, 277, 0),
(174, 4, 140, 'money', 50000000, 0, 0),
(175, 4, 150, 'item', 5, 255, 1),
(176, 4, 170, 'item', 5, 274, 0),
(177, 4, 180, 'item', 10, 253, 0),
(178, 4, 190, 'money', 100000000, 0, 0),
(179, 4, 200, 'points', 300000, 0, 1),
(180, 4, 210, 'exp', 100, 0, 0),
(181, 4, 220, 'item', 3, 247, 0),
(182, 4, 230, 'points', 120000, 0, 0),
(183, 4, 240, 'item', 10, 253, 0),
(184, 4, 250, 'item', 10, 163, 1),
(185, 4, 270, 'raid_tokens', 30, 0, 0),
(186, 4, 280, 'exp', 100, 0, 0),
(187, 4, 290, 'money', 100000000, 0, 0),
(188, 4, 300, 'item', 1, 256, 1),
(189, 4, 320, 'item', 3, 254, 0),
(190, 4, 330, 'item', 5, 251, 0),
(191, 4, 340, 'item', 5, 273, 0),
(192, 4, 350, 'item', 1, 278, 1),
(193, 3, 370, 'money', 125000000, 0, 0),
(194, 4, 380, 'exp', 100, 0, 0),
(195, 4, 390, 'item', 5, 42, 0),
(196, 4, 400, 'item', 5, 255, 1),
(197, 4, 420, 'vip', 7, 0, 0),
(198, 4, 380, 'money', 150000000, 0, 0),
(199, 4, 390, 'points', 150000, 0, 0),
(200, 4, 400, 'points', 300000, 0, 1),
(201, 4, 420, 'raid_tokens', 40, 0, 0),
(202, 4, 440, 'money', 200000000, 0, 0),
(203, 4, 450, 'item', 1, 268, 1),
(204, 4, 470, 'item', 5, 253, 0),
(205, 4, 490, 'exp', 100, 0, 0),
(206, 4, 500, 'points', 200000, 0, 1),
(207, 4, 520, 'item', 3, 194, 0),
(208, 4, 540, 'item', 3, 251, 0),
(209, 4, 550, 'item', 1, 276, 1),
(210, 4, 570, 'points', 175000, 0, 0),
(211, 4, 590, 'raid_tokens', 40, 0, 0),
(212, 4, 600, 'points', 200000, 0, 1),
(213, 4, 620, 'exp', 100, 0, 0),
(214, 4, 640, 'money', 200000000, 0, 0),
(215, 4, 650, 'item', 20, 253, 2),
(216, 4, 670, 'raid_tokens', 40, 0, 0),
(217, 4, 690, 'item', 2, 252, 0),
(218, 4, 700, 'item', 1, 269, 1),
(219, 4, 750, 'points', 175000, 0, 0),
(220, 4, 800, 'exp', 100, 0, 0),
(221, 4, 850, 'item', 5, 163, 1),
(222, 4, 900, 'item', 3, 42, 0),
(223, 4, 920, 'money', 200000000, 0, 0),
(224, 4, 940, 'item', 1, 256, 0),
(225, 4, 950, 'points', 200000, 0, 0),
(226, 4, 970, 'raid_tokens', 50, 0, 0),
(227, 4, 990, 'points', 350000, 0, 1),
(228, 4, 1000, 'item', 5, 273, 0),
(229, 4, 1010, 'item', 10, 42, 0),
(230, 4, 1020, 'points', 250000, 0, 0),
(231, 4, 1030, 'item', 1, 257, 1),
(232, 4, 1040, 'item', 3, 276, 1),
(233, 4, 1050, 'item', 1, 277, 0),
(234, 4, 1060, 'item', 1, 256, 1),
(236, 5, 20, 'money', 10000000, 0, 0),
(237, 5, 30, 'raid_tokens', 15, 0, 0),
(238, 5, 40, 'points', 50000, 0, 0),
(239, 5, 50, 'item', 3, 269, 1),
(240, 5, 70, 'money', 50000000, 0, 0),
(241, 5, 80, 'item', 5, 253, 0),
(242, 5, 90, 'points', 100000, 0, 0),
(243, 5, 100, 'item', 5, 281, 1),
(244, 5, 110, 'exp', 100, 0, 0),
(245, 5, 120, 'points', 100000, 0, 0),
(246, 5, 130, 'item', 1, 277, 0),
(247, 5, 140, 'money', 50000000, 0, 0),
(248, 5, 150, 'item', 5, 255, 1),
(249, 5, 170, 'item', 5, 274, 0),
(250, 5, 180, 'item', 10, 253, 0),
(251, 5, 190, 'money', 100000000, 0, 0),
(252, 5, 200, 'points', 300000, 0, 1),
(253, 5, 210, 'exp', 100, 0, 0),
(254, 5, 220, 'item', 3, 247, 0),
(255, 5, 230, 'points', 120000, 0, 0),
(256, 5, 240, 'item', 10, 253, 0),
(257, 5, 250, 'item', 10, 163, 1),
(258, 5, 270, 'raid_tokens', 30, 0, 0),
(259, 5, 280, 'exp', 100, 0, 0),
(260, 5, 290, 'money', 100000000, 0, 0),
(261, 5, 300, 'item', 1, 256, 1),
(262, 5, 320, 'item', 3, 254, 0),
(263, 5, 330, 'item', 5, 251, 0),
(264, 5, 340, 'item', 5, 273, 0),
(265, 5, 350, 'item', 1, 278, 1),
(266, 5, 380, 'exp', 100, 0, 0),
(267, 5, 390, 'item', 5, 42, 0),
(268, 5, 400, 'item', 5, 255, 1),
(269, 5, 420, 'vip', 7, 0, 0),
(270, 5, 380, 'money', 150000000, 0, 0),
(271, 5, 390, 'points', 150000, 0, 0),
(272, 5, 400, 'points', 300000, 0, 1),
(273, 5, 420, 'raid_tokens', 40, 0, 0),
(274, 5, 440, 'money', 200000000, 0, 0),
(275, 5, 450, 'item', 1, 268, 1),
(276, 5, 470, 'item', 5, 253, 0),
(277, 5, 490, 'exp', 100, 0, 0),
(278, 5, 500, 'points', 200000, 0, 1),
(279, 5, 520, 'item', 3, 194, 0),
(280, 5, 540, 'item', 3, 251, 0),
(281, 5, 550, 'item', 1, 276, 1),
(282, 5, 570, 'points', 175000, 0, 0),
(283, 5, 590, 'raid_tokens', 40, 0, 0),
(284, 5, 600, 'points', 200000, 0, 1),
(285, 5, 620, 'exp', 100, 0, 0),
(286, 5, 640, 'money', 200000000, 0, 0),
(287, 5, 650, 'item', 20, 253, 2),
(288, 5, 670, 'raid_tokens', 40, 0, 0),
(289, 5, 690, 'item', 2, 252, 0),
(290, 5, 700, 'item', 2, 266, 1),
(291, 5, 750, 'points', 175000, 0, 0),
(292, 5, 800, 'exp', 100, 0, 0),
(293, 5, 850, 'item', 5, 163, 1),
(294, 5, 900, 'item', 3, 42, 0),
(295, 5, 920, 'money', 200000000, 0, 0),
(296, 5, 940, 'item', 1, 256, 0),
(297, 5, 950, 'points', 200000, 0, 0),
(298, 5, 970, 'raid_tokens', 50, 0, 0),
(299, 5, 990, 'points', 350000, 0, 1),
(300, 5, 1000, 'item', 5, 273, 0),
(301, 5, 1010, 'item', 10, 42, 0),
(302, 5, 1020, 'points', 250000, 0, 0),
(303, 5, 1030, 'item', 1, 257, 1),
(304, 5, 1040, 'item', 3, 276, 1),
(305, 5, 1050, 'item', 1, 277, 0),
(306, 5, 1060, 'item', 1, 256, 1),
(307, 5, 1070, 'item', 3, 279, 0),
(308, 5, 1080, 'item', 10, 42, 1),
(309, 6, 20, 'money', 10000000, 0, 0),
(310, 6, 30, 'raid_tokens', 15, 0, 0),
(311, 6, 40, 'points', 50000, 0, 0),
(312, 6, 50, 'item', 3, 266, 1),
(313, 6, 70, 'money', 50000000, 0, 0),
(314, 6, 80, 'item', 1, 283, 0),
(315, 6, 90, 'points', 100000, 0, 0),
(316, 6, 100, 'item', 5, 281, 1),
(317, 6, 110, 'exp', 100, 0, 0),
(318, 6, 120, 'points', 100000, 0, 0),
(319, 6, 130, 'item', 1, 277, 0),
(320, 6, 140, 'money', 50000000, 0, 0),
(321, 6, 150, 'item', 5, 255, 1),
(322, 6, 170, 'item', 5, 274, 0),
(323, 6, 180, 'item', 2, 283, 0),
(324, 6, 190, 'money', 100000000, 0, 0),
(325, 6, 200, 'points', 300000, 0, 1),
(326, 6, 210, 'exp', 100, 0, 0),
(327, 6, 220, 'item', 3, 247, 0),
(328, 6, 230, 'points', 120000, 0, 0),
(329, 6, 240, 'item', 2, 283, 0),
(330, 6, 250, 'item', 10, 163, 1),
(331, 6, 270, 'raid_tokens', 30, 0, 0),
(332, 6, 280, 'exp', 100, 0, 0),
(333, 6, 290, 'money', 100000000, 0, 0),
(334, 6, 300, 'item', 1, 256, 1),
(335, 6, 320, 'item', 3, 254, 0),
(336, 6, 330, 'item', 5, 251, 0),
(337, 6, 340, 'item', 5, 273, 0),
(338, 6, 350, 'item', 1, 278, 1),
(339, 6, 380, 'exp', 100, 0, 0),
(340, 6, 390, 'item', 5, 42, 0),
(341, 6, 400, 'item', 5, 255, 1),
(342, 6, 420, 'vip', 7, 0, 0),
(343, 6, 380, 'money', 150000000, 0, 0),
(344, 6, 390, 'points', 150000, 0, 0),
(345, 6, 400, 'points', 300000, 0, 1),
(346, 6, 420, 'raid_tokens', 40, 0, 0),
(347, 6, 440, 'money', 200000000, 0, 0),
(348, 6, 450, 'item', 1, 268, 1),
(349, 6, 470, 'item', 1, 283, 0),
(350, 6, 490, 'exp', 100, 0, 0),
(351, 6, 500, 'points', 200000, 0, 1),
(352, 6, 520, 'item', 3, 194, 0),
(353, 6, 540, 'item', 3, 251, 0),
(354, 6, 550, 'item', 1, 276, 1),
(355, 6, 570, 'points', 175000, 0, 0),
(356, 6, 590, 'raid_tokens', 40, 0, 0),
(357, 6, 600, 'points', 200000, 0, 1),
(358, 6, 620, 'exp', 100, 0, 0),
(359, 6, 640, 'money', 200000000, 0, 0),
(360, 6, 650, 'item', 3, 283, 2),
(361, 6, 670, 'raid_tokens', 40, 0, 0),
(362, 6, 690, 'item', 2, 252, 0),
(363, 6, 700, 'item', 2, 266, 1),
(364, 6, 750, 'points', 175000, 0, 0),
(365, 6, 800, 'exp', 100, 0, 0),
(366, 6, 850, 'item', 5, 163, 1),
(367, 6, 900, 'item', 3, 42, 0),
(368, 6, 920, 'money', 200000000, 0, 0),
(369, 6, 940, 'item', 1, 256, 0),
(370, 6, 950, 'points', 200000, 0, 0),
(371, 6, 970, 'raid_tokens', 50, 0, 0),
(372, 6, 990, 'points', 350000, 0, 1),
(373, 6, 1000, 'item', 5, 273, 0),
(374, 6, 1010, 'item', 10, 42, 0),
(375, 6, 1020, 'points', 250000, 0, 0),
(376, 6, 1030, 'item', 1, 257, 1),
(377, 6, 1040, 'item', 3, 276, 1),
(378, 6, 1050, 'item', 1, 277, 0),
(379, 6, 1060, 'item', 1, 256, 1),
(380, 6, 1070, 'item', 3, 279, 0),
(381, 6, 1080, 'item', 10, 42, 1),
(382, 6, 1090, 'item', 1, 262, 1),
(383, 7, 20, 'money', 10000000, 0, 0),
(384, 7, 30, 'raid_tokens', 15, 0, 0),
(385, 7, 40, 'points', 50000, 0, 0),
(386, 7, 50, 'item', 3, 266, 1),
(387, 7, 70, 'money', 50000000, 0, 0),
(388, 7, 80, 'item', 1, 283, 0),
(389, 7, 90, 'points', 100000, 0, 0),
(390, 7, 100, 'item', 5, 285, 1),
(391, 7, 110, 'exp', 100, 0, 0),
(392, 7, 120, 'points', 100000, 0, 0),
(393, 7, 130, 'item', 1, 277, 0),
(394, 7, 140, 'money', 50000000, 0, 0),
(395, 7, 150, 'item', 5, 255, 1),
(396, 7, 170, 'item', 5, 274, 0),
(397, 7, 180, 'item', 2, 283, 0),
(398, 7, 190, 'money', 100000000, 0, 0),
(399, 7, 200, 'points', 300000, 0, 1),
(400, 7, 210, 'exp', 100, 0, 0),
(401, 7, 220, 'item', 3, 247, 0),
(402, 7, 230, 'points', 120000, 0, 0),
(403, 7, 240, 'item', 2, 283, 0),
(404, 7, 250, 'item', 10, 163, 1),
(405, 7, 270, 'raid_tokens', 30, 0, 0),
(406, 7, 280, 'exp', 100, 0, 0),
(407, 7, 290, 'money', 100000000, 0, 0),
(408, 7, 300, 'item', 5, 290, 1),
(409, 7, 320, 'item', 3, 254, 0),
(410, 7, 330, 'item', 5, 251, 0),
(411, 7, 340, 'item', 5, 273, 0),
(412, 7, 350, 'item', 1, 278, 1),
(413, 7, 380, 'exp', 100, 0, 0),
(414, 7, 390, 'item', 5, 42, 0),
(415, 7, 400, 'item', 5, 255, 1),
(416, 7, 420, 'vip', 7, 0, 0),
(417, 7, 380, 'money', 150000000, 0, 0),
(418, 7, 390, 'points', 150000, 0, 0),
(419, 7, 400, 'points', 300000, 0, 1),
(420, 7, 420, 'raid_tokens', 40, 0, 0),
(421, 7, 440, 'money', 200000000, 0, 0),
(422, 7, 450, 'item', 1, 266, 1),
(423, 7, 470, 'item', 1, 283, 0),
(424, 7, 490, 'exp', 100, 0, 0),
(425, 7, 500, 'points', 200000, 0, 1),
(426, 7, 520, 'item', 3, 194, 0),
(427, 7, 540, 'item', 3, 251, 0),
(428, 7, 550, 'item', 1, 276, 1),
(429, 7, 570, 'points', 175000, 0, 0),
(430, 7, 590, 'raid_tokens', 40, 0, 0),
(431, 7, 600, 'points', 200000, 0, 1),
(432, 7, 620, 'exp', 100, 0, 0),
(433, 7, 640, 'money', 200000000, 0, 0),
(434, 7, 650, 'item', 3, 283, 2),
(435, 7, 670, 'raid_tokens', 40, 0, 0),
(436, 7, 690, 'item', 2, 252, 0),
(437, 7, 700, 'item', 2, 266, 1),
(438, 7, 750, 'points', 175000, 0, 0),
(439, 7, 800, 'exp', 100, 0, 0),
(440, 7, 850, 'item', 5, 285, 1),
(441, 7, 900, 'item', 3, 42, 0),
(442, 7, 920, 'money', 200000000, 0, 0),
(443, 7, 940, 'item', 1, 285, 0),
(444, 7, 950, 'points', 200000, 0, 0),
(445, 7, 970, 'raid_tokens', 50, 0, 0),
(446, 7, 990, 'points', 350000, 0, 1),
(447, 7, 1000, 'item', 5, 273, 0),
(448, 7, 1010, 'item', 10, 42, 0),
(449, 7, 1020, 'points', 250000, 0, 0),
(450, 7, 1030, 'item', 1, 257, 1),
(451, 7, 1040, 'item', 3, 276, 1),
(452, 7, 1050, 'item', 1, 277, 0),
(453, 7, 1060, 'item', 1, 256, 1),
(454, 7, 1070, 'item', 3, 279, 0),
(455, 7, 1080, 'item', 10, 42, 1),
(456, 7, 1090, 'item', 1, 261, 1),
(457, 8, 20, 'money', 10000000, 0, 0),
(458, 8, 30, 'raid_tokens', 15, 0, 0),
(459, 8, 40, 'points', 50000, 0, 0),
(460, 8, 50, 'item', 3, 266, 1),
(461, 8, 70, 'money', 50000000, 0, 0),
(462, 8, 80, 'item', 1, 283, 0),
(463, 8, 90, 'points', 100000, 0, 0),
(464, 8, 100, 'item', 5, 285, 1),
(465, 8, 110, 'exp', 100, 0, 0),
(466, 8, 120, 'points', 100000, 0, 0),
(467, 8, 130, 'item', 1, 277, 0),
(468, 8, 140, 'money', 50000000, 0, 0),
(469, 8, 150, 'item', 5, 255, 1),
(470, 8, 170, 'item', 5, 274, 0),
(471, 8, 180, 'item', 2, 283, 0),
(472, 8, 190, 'money', 100000000, 0, 0),
(473, 8, 200, 'points', 300000, 0, 1),
(474, 8, 210, 'exp', 100, 0, 0),
(475, 8, 220, 'item', 3, 247, 0),
(476, 8, 230, 'points', 120000, 0, 0),
(477, 8, 240, 'item', 2, 283, 0),
(478, 8, 250, 'item', 10, 163, 1),
(479, 8, 270, 'raid_tokens', 30, 0, 0),
(480, 8, 280, 'exp', 100, 0, 0),
(481, 8, 290, 'money', 100000000, 0, 0),
(482, 8, 300, 'item', 5, 290, 1),
(483, 8, 320, 'item', 3, 254, 0),
(484, 8, 330, 'item', 5, 251, 0),
(485, 8, 340, 'item', 5, 273, 0),
(486, 8, 350, 'item', 1, 278, 1),
(487, 8, 380, 'exp', 100, 0, 0),
(488, 8, 390, 'item', 5, 42, 0),
(489, 8, 400, 'item', 5, 255, 1),
(490, 8, 420, 'vip', 7, 0, 0),
(491, 8, 380, 'money', 150000000, 0, 0),
(492, 8, 390, 'points', 150000, 0, 0),
(493, 8, 400, 'points', 300000, 0, 1),
(494, 8, 420, 'raid_tokens', 40, 0, 0),
(495, 8, 440, 'money', 200000000, 0, 0),
(496, 8, 450, 'item', 1, 266, 1),
(497, 8, 470, 'item', 1, 283, 0),
(498, 8, 490, 'exp', 100, 0, 0),
(499, 8, 500, 'points', 200000, 0, 1),
(500, 8, 520, 'item', 3, 194, 0),
(501, 8, 540, 'item', 3, 251, 0),
(502, 8, 550, 'item', 1, 276, 1),
(503, 8, 570, 'points', 175000, 0, 0),
(504, 8, 590, 'raid_tokens', 40, 0, 0),
(505, 8, 600, 'points', 200000, 0, 1),
(506, 8, 620, 'exp', 100, 0, 0),
(507, 8, 640, 'money', 200000000, 0, 0),
(508, 8, 650, 'item', 3, 283, 2),
(509, 8, 670, 'raid_tokens', 40, 0, 0),
(510, 8, 690, 'item', 2, 252, 0),
(511, 8, 700, 'item', 2, 266, 1),
(512, 8, 750, 'points', 175000, 0, 0),
(513, 8, 800, 'exp', 100, 0, 0),
(514, 8, 850, 'item', 5, 285, 1),
(515, 8, 900, 'item', 3, 42, 0),
(516, 8, 920, 'money', 200000000, 0, 0),
(517, 8, 940, 'item', 1, 285, 0),
(518, 8, 950, 'points', 200000, 0, 0),
(519, 8, 970, 'raid_tokens', 50, 0, 0),
(520, 8, 990, 'points', 350000, 0, 1),
(521, 8, 1000, 'item', 5, 273, 0),
(522, 8, 1010, 'item', 10, 42, 0),
(523, 8, 1020, 'points', 250000, 0, 0),
(524, 8, 1030, 'item', 1, 257, 1),
(525, 8, 1040, 'item', 3, 276, 1),
(526, 8, 1050, 'item', 1, 277, 0),
(527, 8, 1060, 'item', 1, 256, 1),
(528, 8, 1070, 'item', 3, 279, 0),
(529, 8, 1080, 'item', 10, 42, 1),
(530, 8, 1090, 'item', 1, 261, 1),
(679, 9, 20, 'money', 10000000, 0, 0),
(680, 9, 30, 'raid_tokens', 15, 0, 0),
(681, 9, 40, 'points', 50000, 0, 0),
(682, 9, 50, 'item', 3, 266, 1),
(683, 9, 70, 'money', 50000000, 0, 0),
(684, 9, 80, 'item', 1, 283, 0),
(685, 9, 90, 'points', 100000, 0, 0),
(686, 9, 100, 'item', 5, 285, 1),
(687, 9, 110, 'exp', 100, 0, 0),
(688, 9, 120, 'points', 100000, 0, 0),
(689, 9, 130, 'item', 1, 277, 0),
(690, 9, 140, 'money', 50000000, 0, 0),
(691, 9, 150, 'item', 5, 255, 1),
(692, 9, 170, 'item', 5, 274, 0),
(693, 9, 180, 'item', 2, 283, 0),
(694, 9, 190, 'money', 100000000, 0, 0),
(695, 9, 200, 'points', 300000, 0, 1),
(696, 9, 210, 'exp', 100, 0, 0),
(697, 9, 220, 'item', 3, 247, 0),
(698, 9, 230, 'points', 120000, 0, 0),
(699, 9, 240, 'item', 2, 283, 0),
(700, 9, 250, 'item', 10, 163, 1),
(701, 9, 270, 'raid_tokens', 30, 0, 0),
(702, 9, 280, 'exp', 100, 0, 0),
(703, 9, 290, 'money', 100000000, 0, 0),
(704, 9, 300, 'item', 5, 290, 1),
(705, 9, 320, 'item', 3, 254, 0),
(706, 9, 330, 'item', 5, 251, 0),
(707, 9, 340, 'item', 5, 273, 0),
(708, 9, 350, 'item', 1, 278, 1),
(709, 9, 380, 'exp', 100, 0, 0),
(710, 9, 390, 'item', 5, 42, 0),
(711, 9, 400, 'item', 5, 255, 1),
(712, 8, 420, 'vip', 7, 0, 0),
(713, 9, 380, 'money', 150000000, 0, 0),
(714, 9, 390, 'points', 150000, 0, 0),
(715, 9, 400, 'points', 300000, 0, 1),
(716, 9, 420, 'raid_tokens', 40, 0, 0),
(717, 9, 440, 'money', 200000000, 0, 0),
(718, 9, 450, 'item', 1, 266, 1),
(719, 9, 470, 'item', 1, 283, 0),
(720, 9, 490, 'exp', 100, 0, 0),
(721, 8, 500, 'points', 200000, 0, 1),
(722, 9, 520, 'item', 3, 194, 0),
(723, 9, 540, 'item', 3, 251, 0),
(724, 9, 550, 'item', 1, 276, 1),
(725, 9, 570, 'points', 175000, 0, 0),
(726, 9, 590, 'raid_tokens', 40, 0, 0),
(727, 9, 600, 'points', 200000, 0, 1),
(728, 9, 620, 'exp', 100, 0, 0),
(729, 9, 640, 'money', 200000000, 0, 0),
(730, 9, 650, 'item', 3, 283, 2),
(731, 9, 670, 'raid_tokens', 40, 0, 0),
(732, 9, 690, 'item', 2, 252, 0),
(733, 9, 700, 'item', 2, 266, 1),
(734, 9, 750, 'points', 175000, 0, 0),
(735, 9, 800, 'exp', 100, 0, 0),
(736, 9, 850, 'item', 5, 285, 1),
(737, 9, 900, 'item', 3, 42, 0),
(738, 9, 920, 'money', 200000000, 0, 0),
(739, 9, 940, 'item', 1, 285, 0),
(740, 9, 950, 'points', 200000, 0, 0),
(741, 9, 970, 'raid_tokens', 50, 0, 0),
(742, 9, 990, 'points', 350000, 0, 1),
(743, 9, 1000, 'item', 5, 273, 0),
(744, 9, 1010, 'item', 10, 42, 0),
(745, 9, 1020, 'points', 250000, 0, 0),
(746, 9, 1030, 'item', 1, 257, 1),
(747, 9, 1040, 'item', 3, 276, 1),
(748, 9, 1050, 'item', 1, 277, 0),
(749, 9, 1060, 'item', 1, 256, 1),
(750, 9, 1070, 'item', 3, 279, 0),
(751, 9, 1080, 'item', 10, 42, 1),
(752, 9, 1090, 'item', 1, 261, 1),
(753, 10, 20, 'money', 10000000, 0, 0),
(754, 10, 30, 'raid_tokens', 15, 0, 0),
(755, 10, 40, 'points', 50000, 0, 0),
(756, 10, 50, 'item', 3, 266, 1),
(757, 10, 70, 'money', 50000000, 0, 0),
(758, 10, 80, 'item', 1, 283, 0),
(759, 10, 90, 'points', 100000, 0, 0),
(760, 10, 100, 'item', 5, 285, 1),
(761, 10, 110, 'exp', 100, 0, 0),
(762, 10, 120, 'points', 100000, 0, 0),
(763, 10, 130, 'item', 1, 277, 0),
(764, 10, 140, 'money', 50000000, 0, 0),
(765, 10, 150, 'item', 5, 255, 1),
(766, 10, 170, 'item', 5, 274, 0),
(767, 10, 180, 'item', 2, 283, 0),
(768, 10, 190, 'money', 100000000, 0, 0),
(769, 10, 200, 'points', 300000, 0, 1),
(770, 10, 210, 'exp', 100, 0, 0),
(771, 10, 220, 'item', 3, 247, 0),
(772, 10, 230, 'points', 120000, 0, 0),
(773, 10, 240, 'item', 2, 283, 0),
(774, 10, 250, 'item', 10, 163, 1),
(775, 10, 270, 'raid_tokens', 30, 0, 0),
(776, 10, 280, 'exp', 100, 0, 0),
(777, 10, 290, 'money', 100000000, 0, 0),
(778, 10, 300, 'item', 5, 290, 1),
(779, 10, 320, 'item', 3, 254, 0),
(780, 10, 330, 'item', 5, 251, 0),
(781, 10, 340, 'item', 5, 273, 0),
(782, 10, 350, 'item', 1, 325, 1),
(783, 10, 380, 'exp', 100, 0, 0),
(784, 10, 390, 'item', 5, 42, 0),
(785, 10, 400, 'item', 5, 255, 1),
(786, 10, 380, 'money', 150000000, 0, 0),
(787, 10, 390, 'points', 150000, 0, 0),
(788, 10, 400, 'points', 300000, 0, 1),
(789, 10, 420, 'raid_tokens', 40, 0, 0),
(790, 10, 440, 'money', 200000000, 0, 0),
(791, 10, 450, 'item', 1, 269, 1),
(792, 10, 470, 'item', 1, 283, 0),
(793, 10, 490, 'exp', 100, 0, 0),
(794, 10, 520, 'item', 3, 194, 0),
(795, 10, 540, 'item', 3, 251, 0),
(796, 10, 550, 'item', 1, 276, 1),
(797, 10, 570, 'points', 175000, 0, 0),
(798, 10, 590, 'raid_tokens', 40, 0, 0),
(799, 10, 600, 'points', 200000, 0, 1),
(800, 10, 620, 'exp', 100, 0, 0),
(801, 10, 640, 'money', 200000000, 0, 0),
(802, 10, 650, 'item', 3, 283, 2),
(803, 10, 670, 'raid_tokens', 40, 0, 0),
(804, 10, 690, 'item', 2, 252, 0),
(805, 10, 700, 'item', 2, 266, 1),
(806, 10, 750, 'points', 175000, 0, 0),
(807, 10, 800, 'exp', 100, 0, 0),
(808, 10, 850, 'item', 5, 285, 1),
(809, 10, 900, 'item', 3, 42, 0),
(810, 10, 920, 'money', 200000000, 0, 0),
(811, 10, 940, 'item', 1, 285, 0),
(812, 10, 950, 'points', 200000, 0, 0),
(813, 10, 970, 'raid_tokens', 50, 0, 0),
(814, 10, 990, 'points', 350000, 0, 1),
(815, 10, 1000, 'item', 5, 273, 0),
(816, 10, 1010, 'item', 10, 42, 0),
(817, 10, 1020, 'points', 250000, 0, 0),
(818, 10, 1030, 'item', 1, 325, 1),
(819, 10, 1040, 'item', 3, 276, 1),
(820, 10, 1050, 'item', 1, 277, 0),
(821, 10, 1060, 'item', 1, 256, 1),
(822, 10, 1070, 'item', 3, 279, 0),
(823, 10, 1080, 'item', 10, 42, 1),
(824, 10, 1090, 'item', 1, 262, 1),
(825, 11, 20, 'money', 10000000, 0, 0),
(826, 11, 30, 'raid_tokens', 15, 0, 0),
(827, 11, 40, 'points', 50000, 0, 0),
(828, 11, 50, 'item', 1, 329, 1),
(829, 11, 70, 'money', 50000000, 0, 0),
(830, 11, 80, 'item', 1, 283, 0),
(831, 11, 90, 'points', 100000, 0, 0),
(832, 11, 100, 'item', 2, 269, 1),
(833, 11, 110, 'exp', 100, 0, 0),
(834, 11, 120, 'points', 100000, 0, 0),
(835, 11, 130, 'item', 1, 261, 0),
(836, 11, 140, 'money', 50000000, 0, 0),
(837, 11, 150, 'item', 5, 255, 1),
(838, 11, 170, 'item', 5, 274, 0),
(839, 11, 180, 'item', 2, 283, 0),
(840, 11, 190, 'money', 100000000, 0, 0),
(841, 11, 200, 'points', 300000, 0, 1),
(842, 11, 210, 'exp', 100, 0, 0),
(843, 11, 220, 'item', 3, 247, 0),
(844, 11, 230, 'points', 120000, 0, 0),
(845, 11, 240, 'item', 2, 283, 0),
(846, 11, 250, 'item', 10, 163, 1),
(847, 11, 270, 'raid_tokens', 30, 0, 0),
(848, 11, 280, 'exp', 100, 0, 0),
(849, 11, 290, 'money', 100000000, 0, 0),
(850, 11, 300, 'item', 5, 290, 1),
(851, 11, 320, 'item', 3, 254, 0),
(852, 11, 330, 'item', 5, 251, 0),
(853, 11, 340, 'item', 5, 273, 0),
(854, 11, 350, 'item', 1, 325, 1),
(855, 11, 380, 'exp', 100, 0, 0),
(856, 11, 390, 'item', 5, 42, 0),
(857, 11, 400, 'item', 5, 255, 1),
(858, 11, 380, 'money', 150000000, 0, 0),
(859, 11, 390, 'points', 150000, 0, 0),
(860, 11, 400, 'points', 300000, 0, 1),
(861, 11, 420, 'raid_tokens', 40, 0, 0),
(862, 11, 440, 'money', 200000000, 0, 0),
(863, 11, 450, 'item', 3, 267, 1),
(864, 11, 470, 'item', 1, 283, 0),
(865, 11, 490, 'exp', 100, 0, 0),
(866, 11, 520, 'item', 3, 194, 0),
(867, 11, 540, 'item', 3, 251, 0),
(868, 11, 550, 'item', 1, 276, 1),
(869, 11, 570, 'points', 175000, 0, 0),
(870, 11, 590, 'raid_tokens', 40, 0, 0),
(871, 11, 600, 'points', 200000, 0, 1),
(872, 11, 620, 'exp', 100, 0, 0),
(873, 11, 640, 'money', 200000000, 0, 0),
(874, 11, 650, 'item', 3, 283, 2),
(875, 11, 670, 'raid_tokens', 40, 0, 0),
(876, 11, 690, 'item', 2, 252, 0),
(877, 11, 700, 'item', 2, 266, 1),
(878, 11, 750, 'points', 175000, 0, 0),
(879, 11, 800, 'exp', 100, 0, 0),
(880, 11, 850, 'item', 5, 285, 1),
(881, 11, 900, 'item', 3, 42, 0),
(882, 11, 920, 'money', 200000000, 0, 0),
(883, 11, 940, 'item', 1, 285, 0),
(884, 11, 950, 'points', 200000, 0, 0),
(885, 11, 970, 'raid_tokens', 50, 0, 0),
(886, 11, 990, 'points', 350000, 0, 1),
(887, 11, 1000, 'item', 5, 273, 0),
(888, 11, 1010, 'item', 10, 42, 0),
(889, 11, 1020, 'points', 250000, 0, 0),
(890, 11, 1030, 'item', 1, 325, 1),
(891, 11, 1040, 'item', 3, 276, 1),
(892, 11, 1050, 'item', 1, 277, 0),
(893, 11, 1060, 'item', 1, 256, 1),
(894, 11, 1070, 'item', 1, 259, 0),
(895, 11, 1080, 'item', 10, 42, 1),
(896, 11, 1090, 'item', 1, 262, 1),
(897, 12, 20, 'money', 10000000, 0, 0),
(898, 12, 30, 'raid_tokens', 15, 0, 0),
(899, 12, 40, 'points', 50000, 0, 0),
(900, 12, 50, 'item', 1, 329, 1),
(901, 12, 70, 'money', 50000000, 0, 0),
(902, 12, 80, 'item', 1, 283, 0),
(903, 12, 90, 'points', 100000, 0, 0),
(904, 12, 100, 'item', 2, 269, 1),
(905, 12, 110, 'exp', 100, 0, 0),
(906, 12, 120, 'points', 100000, 0, 0),
(907, 12, 130, 'item', 1, 261, 0),
(908, 12, 140, 'money', 50000000, 0, 0),
(909, 12, 150, 'item', 5, 255, 1),
(910, 12, 170, 'item', 5, 274, 0),
(911, 12, 180, 'item', 2, 283, 0),
(912, 12, 190, 'money', 100000000, 0, 0),
(913, 12, 200, 'points', 300000, 0, 1),
(914, 12, 210, 'exp', 100, 0, 0),
(915, 12, 220, 'item', 3, 247, 0),
(916, 12, 230, 'points', 120000, 0, 0),
(917, 12, 240, 'item', 2, 283, 0),
(918, 12, 250, 'item', 10, 163, 1),
(919, 12, 270, 'raid_tokens', 30, 0, 0),
(920, 12, 280, 'exp', 100, 0, 0),
(921, 12, 290, 'money', 100000000, 0, 0),
(922, 12, 300, 'item', 5, 290, 1),
(923, 12, 320, 'item', 3, 254, 0),
(924, 12, 330, 'item', 5, 251, 0),
(925, 12, 340, 'item', 5, 273, 0),
(926, 12, 350, 'item', 1, 325, 1),
(927, 12, 380, 'exp', 100, 0, 0),
(928, 12, 390, 'item', 5, 42, 0),
(929, 12, 400, 'item', 5, 255, 1),
(930, 12, 380, 'money', 150000000, 0, 0),
(931, 12, 390, 'points', 150000, 0, 0),
(932, 12, 400, 'points', 300000, 0, 1),
(933, 12, 420, 'raid_tokens', 40, 0, 0),
(934, 12, 440, 'money', 200000000, 0, 0),
(935, 12, 450, 'item', 3, 267, 1),
(936, 12, 470, 'item', 1, 283, 0),
(937, 12, 490, 'exp', 100, 0, 0),
(938, 12, 520, 'item', 3, 194, 0),
(939, 12, 540, 'item', 3, 251, 0),
(940, 12, 550, 'item', 1, 276, 1),
(941, 12, 570, 'points', 175000, 0, 0),
(942, 12, 590, 'raid_tokens', 40, 0, 0),
(943, 12, 600, 'points', 200000, 0, 1),
(944, 12, 620, 'exp', 100, 0, 0),
(945, 12, 640, 'money', 200000000, 0, 0),
(946, 12, 650, 'item', 3, 283, 2),
(947, 12, 670, 'raid_tokens', 40, 0, 0),
(948, 12, 690, 'item', 2, 252, 0),
(949, 12, 700, 'item', 2, 266, 1),
(950, 12, 750, 'points', 175000, 0, 0),
(951, 12, 800, 'exp', 100, 0, 0),
(952, 12, 850, 'item', 5, 285, 1),
(953, 12, 900, 'item', 3, 42, 0),
(954, 12, 920, 'money', 200000000, 0, 0),
(955, 12, 940, 'item', 1, 285, 0),
(956, 12, 950, 'points', 200000, 0, 0),
(957, 12, 970, 'raid_tokens', 50, 0, 0),
(958, 12, 990, 'points', 350000, 0, 1),
(959, 12, 1000, 'item', 5, 273, 0),
(960, 12, 1010, 'item', 10, 42, 0),
(961, 12, 1020, 'points', 250000, 0, 0),
(962, 12, 1030, 'item', 1, 325, 1),
(963, 12, 1040, 'item', 3, 276, 1),
(964, 12, 1050, 'item', 1, 277, 0),
(965, 12, 1060, 'item', 1, 256, 1),
(966, 12, 1070, 'item', 1, 259, 0),
(967, 12, 1080, 'item', 10, 42, 1),
(968, 12, 1090, 'item', 1, 262, 1),
(969, 13, 20, 'money', 10000000, 0, 0),
(970, 13, 30, 'raid_tokens', 15, 0, 0),
(971, 13, 40, 'points', 50000, 0, 0),
(972, 13, 50, 'item', 1, 329, 1),
(973, 13, 70, 'money', 50000000, 0, 0),
(974, 13, 80, 'item', 1, 283, 0),
(975, 13, 90, 'points', 100000, 0, 0),
(976, 13, 100, 'item', 2, 269, 1),
(977, 13, 110, 'exp', 100, 0, 0),
(978, 13, 120, 'points', 100000, 0, 0),
(979, 13, 130, 'item', 1, 261, 0),
(980, 13, 140, 'money', 50000000, 0, 0),
(981, 13, 150, 'item', 5, 255, 1),
(982, 13, 170, 'item', 5, 274, 0),
(983, 13, 180, 'item', 2, 283, 0),
(984, 13, 190, 'money', 100000000, 0, 0),
(985, 13, 200, 'points', 300000, 0, 1),
(986, 13, 210, 'exp', 100, 0, 0),
(987, 13, 220, 'item', 3, 247, 0),
(988, 13, 230, 'points', 120000, 0, 0),
(989, 13, 240, 'item', 2, 283, 0),
(990, 13, 250, 'item', 10, 163, 1),
(991, 13, 270, 'raid_tokens', 30, 0, 0),
(992, 13, 280, 'exp', 100, 0, 0),
(993, 13, 290, 'money', 100000000, 0, 0),
(994, 13, 300, 'item', 5, 290, 1),
(995, 13, 320, 'item', 3, 254, 0),
(996, 13, 330, 'item', 5, 251, 0),
(997, 13, 340, 'item', 5, 273, 0),
(998, 13, 350, 'item', 1, 325, 1),
(999, 13, 380, 'exp', 100, 0, 0),
(1000, 13, 390, 'item', 5, 42, 0),
(1001, 13, 400, 'item', 5, 255, 1),
(1002, 13, 380, 'money', 150000000, 0, 0),
(1003, 13, 390, 'points', 150000, 0, 0),
(1004, 13, 400, 'points', 300000, 0, 1),
(1005, 13, 420, 'raid_tokens', 40, 0, 0),
(1006, 13, 440, 'money', 200000000, 0, 0),
(1007, 13, 450, 'item', 3, 267, 1),
(1008, 13, 470, 'item', 1, 283, 0),
(1009, 13, 490, 'exp', 100, 0, 0),
(1010, 13, 520, 'item', 3, 194, 0),
(1011, 13, 540, 'item', 3, 251, 0),
(1012, 13, 550, 'item', 1, 276, 1),
(1013, 13, 570, 'points', 175000, 0, 0),
(1014, 13, 590, 'raid_tokens', 40, 0, 0),
(1015, 13, 600, 'points', 200000, 0, 1),
(1016, 13, 620, 'exp', 100, 0, 0),
(1017, 13, 640, 'money', 200000000, 0, 0),
(1018, 13, 650, 'item', 3, 283, 2),
(1019, 13, 670, 'raid_tokens', 40, 0, 0),
(1020, 13, 690, 'item', 2, 252, 0),
(1021, 13, 700, 'item', 2, 266, 1),
(1022, 13, 750, 'points', 175000, 0, 0),
(1023, 13, 800, 'exp', 100, 0, 0),
(1024, 13, 850, 'item', 5, 285, 1),
(1025, 13, 900, 'item', 3, 42, 0),
(1026, 13, 920, 'money', 200000000, 0, 0),
(1027, 13, 940, 'item', 1, 285, 0),
(1028, 13, 950, 'points', 200000, 0, 0),
(1029, 13, 970, 'raid_tokens', 50, 0, 0),
(1030, 13, 990, 'points', 350000, 0, 1),
(1031, 13, 1000, 'item', 5, 273, 0),
(1032, 13, 1010, 'item', 10, 42, 0),
(1033, 13, 1020, 'points', 250000, 0, 0),
(1034, 13, 1030, 'item', 1, 325, 1),
(1035, 13, 1040, 'item', 3, 276, 1),
(1036, 13, 1050, 'item', 1, 277, 0),
(1037, 13, 1060, 'item', 1, 256, 1),
(1038, 13, 1070, 'item', 1, 259, 0),
(1039, 13, 1080, 'item', 10, 42, 1),
(1040, 13, 1090, 'item', 1, 262, 1),
(1041, 13, 980, 'item', 1, 355, 1),
(1042, 13, 775, 'item', 1, 354, 0),
(1043, 13, 610, 'item', 1, 355, 0),
(1044, 13, 500, 'item', 1, 354, 0),
(1045, 13, 725, 'item', 1, 355, 0),
(1046, 13, 825, 'item', 1, 355, 0),
(1047, 13, 1100, 'item', 1, 355, 0),
(1048, 13, 410, 'item', 1, 354, 0),
(1049, 13, 375, 'item', 1, 354, 0),
(1050, 13, 260, 'item', 1, 354, 0),
(1051, 13, 160, 'item', 2, 356, 0),
(1052, 13, 310, 'item', 5, 356, 0),
(1053, 13, 430, 'item', 7, 356, 0),
(1054, 13, 510, 'item', 10, 356, 0),
(1055, 13, 630, 'item', 10, 356, 0),
(1056, 13, 1000, 'item', 25, 356, 1),
(1057, 13, 930, 'item', 15, 356, 0),
(1058, 13, 710, 'item', 12, 356, 0),
(1059, 14, 20, 'money', 10000000, 0, 0),
(1060, 14, 30, 'raid_tokens', 15, 0, 0),
(1061, 14, 40, 'points', 50000, 0, 0),
(1062, 14, 50, 'item', 1, 329, 1),
(1063, 14, 70, 'money', 50000000, 0, 0),
(1064, 14, 80, 'item', 1, 283, 0),
(1065, 14, 90, 'points', 100000, 0, 0),
(1066, 14, 100, 'item', 2, 269, 1),
(1067, 14, 110, 'exp', 100, 0, 0),
(1068, 14, 120, 'points', 100000, 0, 0),
(1069, 14, 130, 'item', 1, 261, 0),
(1070, 14, 140, 'money', 50000000, 0, 0),
(1071, 14, 150, 'item', 5, 255, 1),
(1072, 14, 170, 'item', 5, 274, 0),
(1073, 14, 180, 'item', 2, 283, 0),
(1074, 14, 190, 'money', 100000000, 0, 0),
(1075, 14, 200, 'points', 300000, 0, 1),
(1076, 14, 210, 'exp', 100, 0, 0),
(1077, 14, 220, 'item', 3, 247, 0),
(1078, 14, 230, 'points', 120000, 0, 0),
(1079, 14, 240, 'item', 2, 283, 0),
(1080, 14, 250, 'item', 10, 163, 1),
(1081, 14, 270, 'raid_tokens', 30, 0, 0),
(1082, 14, 280, 'exp', 100, 0, 0),
(1083, 14, 290, 'money', 100000000, 0, 0),
(1084, 14, 300, 'item', 5, 290, 1),
(1085, 14, 320, 'item', 3, 254, 0),
(1086, 14, 330, 'item', 5, 251, 0),
(1087, 14, 340, 'item', 5, 273, 0),
(1088, 14, 350, 'item', 1, 325, 1),
(1089, 14, 380, 'exp', 100, 0, 0),
(1090, 14, 390, 'item', 5, 42, 0),
(1091, 14, 400, 'item', 5, 255, 1),
(1092, 14, 380, 'money', 150000000, 0, 0),
(1093, 14, 390, 'points', 150000, 0, 0),
(1094, 14, 400, 'points', 300000, 0, 1),
(1095, 14, 420, 'raid_tokens', 40, 0, 0),
(1096, 14, 440, 'money', 200000000, 0, 0),
(1097, 14, 450, 'item', 3, 267, 1),
(1098, 14, 470, 'item', 1, 283, 0),
(1099, 14, 490, 'exp', 100, 0, 0),
(1100, 14, 520, 'item', 3, 194, 0),
(1101, 14, 540, 'item', 3, 251, 0),
(1102, 14, 550, 'item', 1, 276, 1),
(1103, 14, 570, 'points', 175000, 0, 0),
(1104, 14, 590, 'raid_tokens', 40, 0, 0),
(1105, 14, 600, 'points', 200000, 0, 1),
(1106, 14, 620, 'exp', 100, 0, 0),
(1107, 14, 640, 'money', 200000000, 0, 0),
(1108, 14, 650, 'item', 3, 283, 2),
(1109, 14, 670, 'raid_tokens', 40, 0, 0),
(1110, 14, 690, 'item', 2, 252, 0),
(1111, 14, 700, 'item', 2, 266, 1),
(1112, 14, 750, 'points', 175000, 0, 0),
(1113, 14, 800, 'exp', 100, 0, 0),
(1114, 14, 850, 'item', 5, 285, 1),
(1115, 14, 900, 'item', 3, 42, 0),
(1116, 14, 920, 'money', 200000000, 0, 0),
(1117, 14, 940, 'item', 1, 285, 0),
(1118, 14, 950, 'points', 200000, 0, 0),
(1119, 14, 970, 'raid_tokens', 50, 0, 0),
(1120, 14, 990, 'points', 350000, 0, 1),
(1121, 14, 1000, 'item', 5, 273, 0),
(1122, 14, 1010, 'item', 10, 42, 0),
(1123, 14, 1020, 'points', 250000, 0, 0),
(1124, 14, 1030, 'item', 1, 325, 1),
(1125, 14, 1040, 'item', 3, 276, 1),
(1126, 14, 1050, 'item', 1, 277, 0),
(1127, 14, 1060, 'item', 1, 256, 1),
(1128, 14, 1070, 'item', 1, 259, 0),
(1129, 14, 1080, 'item', 10, 42, 1),
(1130, 14, 1090, 'item', 1, 262, 1),
(1131, 15, 20, 'money', 10000000, 0, 0),
(1132, 15, 30, 'raid_tokens', 15, 0, 0),
(1133, 15, 40, 'points', 50000, 0, 0),
(1134, 15, 50, 'item', 3, 266, 1),
(1135, 15, 70, 'money', 50000000, 0, 0),
(1136, 15, 80, 'item', 1, 283, 0),
(1137, 15, 90, 'points', 100000, 0, 0),
(1138, 15, 100, 'item', 5, 285, 1),
(1139, 15, 110, 'exp', 100, 0, 0),
(1140, 15, 120, 'points', 100000, 0, 0),
(1141, 15, 130, 'item', 1, 277, 0),
(1142, 15, 140, 'money', 50000000, 0, 0),
(1143, 15, 150, 'item', 5, 255, 1),
(1144, 15, 170, 'item', 5, 274, 0),
(1145, 15, 180, 'item', 2, 283, 0),
(1146, 15, 190, 'money', 100000000, 0, 0),
(1147, 15, 200, 'points', 300000, 0, 1),
(1148, 15, 210, 'exp', 100, 0, 0),
(1149, 15, 220, 'item', 3, 247, 0),
(1150, 15, 230, 'points', 120000, 0, 0),
(1151, 15, 240, 'item', 2, 283, 0),
(1152, 15, 250, 'item', 10, 163, 1),
(1153, 15, 270, 'raid_tokens', 30, 0, 0),
(1154, 15, 280, 'exp', 100, 0, 0),
(1155, 15, 290, 'money', 100000000, 0, 0),
(1156, 15, 300, 'item', 5, 290, 1),
(1157, 15, 320, 'item', 3, 254, 0),
(1158, 15, 330, 'item', 5, 251, 0),
(1159, 15, 340, 'item', 5, 273, 0),
(1160, 15, 350, 'item', 1, 325, 1),
(1161, 15, 380, 'exp', 100, 0, 0),
(1162, 15, 390, 'item', 5, 42, 0),
(1163, 15, 400, 'item', 5, 255, 1),
(1164, 15, 380, 'money', 150000000, 0, 0),
(1165, 15, 390, 'points', 150000, 0, 0),
(1166, 15, 400, 'points', 300000, 0, 1),
(1167, 15, 420, 'raid_tokens', 40, 0, 0),
(1168, 15, 440, 'money', 200000000, 0, 0),
(1169, 15, 450, 'item', 1, 269, 1),
(1170, 15, 470, 'item', 1, 283, 0),
(1171, 15, 490, 'exp', 100, 0, 0),
(1172, 15, 520, 'item', 3, 194, 0),
(1173, 15, 540, 'item', 3, 251, 0),
(1174, 15, 550, 'item', 1, 276, 1),
(1175, 15, 570, 'points', 175000, 0, 0),
(1176, 15, 590, 'raid_tokens', 40, 0, 0),
(1177, 15, 600, 'points', 200000, 0, 1),
(1178, 15, 620, 'exp', 100, 0, 0),
(1179, 15, 640, 'money', 200000000, 0, 0),
(1180, 15, 650, 'item', 3, 283, 2),
(1181, 15, 670, 'raid_tokens', 40, 0, 0),
(1182, 15, 690, 'item', 2, 252, 0),
(1183, 15, 700, 'item', 2, 266, 1),
(1184, 15, 750, 'points', 175000, 0, 0),
(1185, 15, 800, 'exp', 100, 0, 0),
(1186, 15, 850, 'item', 5, 285, 1),
(1187, 15, 900, 'item', 3, 42, 0),
(1188, 15, 920, 'money', 200000000, 0, 0),
(1189, 15, 940, 'item', 1, 285, 0),
(1190, 15, 950, 'points', 200000, 0, 0),
(1191, 15, 970, 'raid_tokens', 50, 0, 0),
(1192, 15, 990, 'points', 350000, 0, 1),
(1193, 15, 1000, 'item', 5, 273, 0),
(1194, 15, 1010, 'item', 10, 42, 0),
(1195, 15, 1020, 'points', 250000, 0, 0),
(1196, 15, 1030, 'item', 1, 325, 1),
(1197, 15, 1040, 'item', 3, 276, 1),
(1198, 15, 1050, 'item', 1, 277, 0),
(1199, 15, 1060, 'item', 1, 256, 1),
(1200, 15, 1070, 'item', 3, 279, 0),
(1201, 15, 1080, 'item', 10, 42, 1),
(1202, 15, 1090, 'item', 1, 262, 1),
(1203, 16, 20, 'money', 10000000, 0, 0),
(1204, 16, 30, 'raid_tokens', 15, 0, 0),
(1205, 16, 40, 'points', 50000, 0, 0),
(1206, 16, 50, 'item', 3, 266, 1),
(1207, 16, 70, 'money', 50000000, 0, 0),
(1208, 16, 80, 'item', 1, 283, 0),
(1209, 16, 90, 'points', 100000, 0, 0),
(1210, 16, 100, 'item', 5, 285, 1),
(1211, 16, 110, 'exp', 100, 0, 0),
(1212, 16, 120, 'points', 100000, 0, 0),
(1213, 16, 130, 'item', 1, 277, 0),
(1214, 16, 140, 'money', 50000000, 0, 0),
(1215, 16, 150, 'item', 5, 255, 1),
(1216, 16, 170, 'item', 5, 274, 0),
(1217, 16, 180, 'item', 2, 283, 0),
(1218, 16, 190, 'money', 100000000, 0, 0),
(1219, 16, 200, 'points', 300000, 0, 1),
(1220, 16, 210, 'exp', 100, 0, 0),
(1221, 16, 220, 'item', 3, 247, 0),
(1222, 16, 230, 'points', 120000, 0, 0),
(1223, 16, 240, 'item', 2, 283, 0),
(1224, 16, 250, 'item', 10, 163, 1),
(1225, 16, 270, 'raid_tokens', 30, 0, 0),
(1226, 16, 280, 'exp', 100, 0, 0),
(1227, 16, 290, 'money', 100000000, 0, 0),
(1228, 16, 300, 'item', 5, 290, 1),
(1229, 16, 320, 'item', 3, 254, 0),
(1230, 16, 330, 'item', 5, 251, 0),
(1231, 16, 340, 'item', 5, 273, 0),
(1232, 16, 350, 'item', 1, 354, 1),
(1233, 16, 380, 'exp', 100, 0, 0),
(1234, 16, 390, 'item', 5, 42, 0),
(1235, 16, 400, 'item', 5, 255, 1),
(1236, 16, 380, 'money', 150000000, 0, 0),
(1237, 16, 390, 'points', 150000, 0, 0),
(1238, 16, 400, 'points', 300000, 0, 1),
(1239, 16, 420, 'raid_tokens', 40, 0, 0),
(1240, 16, 440, 'money', 200000000, 0, 0),
(1241, 16, 450, 'item', 1, 269, 1),
(1242, 16, 470, 'item', 1, 283, 0),
(1243, 16, 490, 'exp', 100, 0, 0),
(1244, 16, 520, 'item', 3, 194, 0),
(1245, 16, 540, 'item', 3, 251, 0),
(1246, 16, 550, 'item', 1, 276, 1),
(1247, 16, 570, 'points', 175000, 0, 0),
(1248, 16, 590, 'raid_tokens', 40, 0, 0),
(1249, 16, 600, 'points', 200000, 0, 1),
(1250, 16, 620, 'exp', 100, 0, 0),
(1251, 16, 640, 'money', 200000000, 0, 0),
(1252, 16, 650, 'item', 3, 283, 2),
(1253, 16, 670, 'raid_tokens', 40, 0, 0),
(1254, 16, 690, 'item', 2, 252, 0),
(1255, 16, 700, 'item', 2, 266, 1),
(1256, 16, 750, 'points', 175000, 0, 0),
(1257, 16, 800, 'exp', 100, 0, 0),
(1258, 16, 850, 'item', 5, 285, 1),
(1259, 16, 900, 'item', 3, 42, 0),
(1260, 16, 920, 'money', 200000000, 0, 0),
(1261, 16, 940, 'item', 1, 285, 0),
(1262, 16, 950, 'points', 200000, 0, 0),
(1263, 16, 970, 'raid_tokens', 50, 0, 0),
(1264, 16, 990, 'points', 350000, 0, 1),
(1265, 16, 1000, 'item', 5, 273, 0),
(1266, 16, 1010, 'item', 10, 42, 0),
(1267, 16, 1020, 'points', 250000, 0, 0),
(1268, 16, 1030, 'item', 1, 354, 1),
(1269, 16, 1040, 'item', 3, 276, 1),
(1270, 16, 1050, 'item', 1, 277, 0),
(1271, 16, 1060, 'item', 1, 256, 1),
(1272, 16, 1070, 'item', 3, 279, 0),
(1273, 16, 1080, 'item', 10, 42, 1),
(1274, 16, 1090, 'item', 1, 262, 1),
(1275, 17, 20, 'money', 10000000, 0, 0),
(1276, 17, 30, 'raid_tokens', 15, 0, 0),
(1277, 17, 40, 'points', 50000, 0, 0),
(1278, 17, 50, 'item', 1, 329, 1),
(1279, 17, 70, 'money', 50000000, 0, 0),
(1280, 17, 80, 'item', 1, 283, 0),
(1281, 17, 90, 'points', 100000, 0, 0),
(1282, 17, 100, 'item', 2, 269, 1),
(1283, 17, 110, 'exp', 100, 0, 0),
(1284, 17, 120, 'points', 100000, 0, 0),
(1285, 17, 130, 'item', 1, 261, 0),
(1286, 17, 140, 'money', 50000000, 0, 0),
(1287, 17, 150, 'item', 5, 255, 1),
(1288, 17, 170, 'item', 5, 274, 0),
(1289, 17, 180, 'item', 2, 283, 0),
(1290, 17, 190, 'money', 100000000, 0, 0),
(1291, 17, 200, 'points', 300000, 0, 1),
(1292, 17, 210, 'exp', 100, 0, 0),
(1293, 17, 220, 'item', 3, 247, 0),
(1294, 17, 230, 'points', 120000, 0, 0),
(1295, 17, 240, 'item', 2, 283, 0),
(1296, 17, 250, 'item', 10, 163, 1),
(1297, 17, 270, 'raid_tokens', 30, 0, 0),
(1298, 17, 280, 'exp', 100, 0, 0),
(1299, 17, 290, 'money', 100000000, 0, 0),
(1300, 17, 300, 'item', 5, 290, 1),
(1301, 17, 320, 'item', 3, 254, 0),
(1302, 17, 330, 'item', 5, 251, 0),
(1303, 17, 340, 'item', 5, 273, 0),
(1304, 17, 350, 'item', 1, 325, 1),
(1305, 17, 380, 'exp', 100, 0, 0),
(1306, 17, 390, 'item', 5, 42, 0),
(1307, 17, 400, 'item', 5, 255, 1),
(1308, 17, 380, 'money', 150000000, 0, 0),
(1309, 17, 390, 'points', 150000, 0, 0),
(1310, 17, 400, 'points', 300000, 0, 1),
(1311, 17, 420, 'raid_tokens', 40, 0, 0),
(1312, 17, 440, 'money', 200000000, 0, 0),
(1313, 17, 450, 'item', 3, 267, 1),
(1314, 17, 470, 'item', 1, 283, 0),
(1315, 17, 490, 'exp', 100, 0, 0),
(1316, 17, 520, 'item', 3, 194, 0),
(1317, 17, 540, 'item', 3, 251, 0),
(1318, 17, 550, 'item', 1, 276, 1),
(1319, 17, 570, 'points', 175000, 0, 0),
(1320, 17, 590, 'raid_tokens', 40, 0, 0),
(1321, 17, 600, 'points', 200000, 0, 1),
(1322, 17, 620, 'exp', 100, 0, 0),
(1323, 17, 640, 'money', 200000000, 0, 0),
(1324, 17, 650, 'item', 3, 283, 2),
(1325, 17, 670, 'raid_tokens', 40, 0, 0),
(1326, 17, 690, 'item', 2, 252, 0),
(1327, 17, 700, 'item', 2, 266, 1),
(1328, 17, 750, 'points', 175000, 0, 0),
(1329, 17, 800, 'exp', 100, 0, 0),
(1330, 17, 850, 'item', 5, 285, 1),
(1331, 17, 900, 'item', 3, 42, 0),
(1332, 17, 920, 'money', 200000000, 0, 0),
(1333, 17, 940, 'item', 1, 285, 0),
(1334, 17, 950, 'points', 200000, 0, 0),
(1335, 17, 970, 'raid_tokens', 50, 0, 0),
(1336, 17, 990, 'points', 350000, 0, 1),
(1337, 17, 1000, 'item', 5, 273, 0),
(1338, 17, 1010, 'item', 10, 42, 0),
(1339, 17, 1020, 'points', 250000, 0, 0),
(1340, 17, 1030, 'item', 1, 325, 1),
(1341, 17, 1040, 'item', 3, 276, 1),
(1342, 17, 1050, 'item', 1, 277, 0),
(1343, 17, 1060, 'item', 1, 256, 1),
(1344, 17, 1070, 'item', 1, 259, 0),
(1345, 17, 1080, 'item', 10, 42, 1),
(1346, 17, 1090, 'item', 1, 262, 1),
(1347, 17, 980, 'item', 1, 355, 1),
(1348, 17, 775, 'item', 1, 354, 0),
(1349, 17, 610, 'item', 1, 355, 0),
(1350, 17, 500, 'item', 1, 354, 0),
(1351, 17, 725, 'item', 1, 355, 0),
(1352, 17, 825, 'item', 1, 355, 0),
(1353, 17, 1100, 'item', 1, 355, 0),
(1354, 17, 410, 'item', 1, 354, 0),
(1355, 17, 375, 'item', 1, 354, 0),
(1356, 17, 260, 'item', 1, 354, 0),
(1357, 17, 160, 'item', 2, 356, 0),
(1358, 17, 310, 'item', 5, 356, 0),
(1359, 17, 430, 'item', 7, 356, 0),
(1360, 17, 510, 'item', 10, 356, 0),
(1361, 17, 630, 'item', 10, 356, 0),
(1362, 17, 1000, 'item', 25, 356, 1),
(1363, 17, 930, 'item', 15, 356, 0),
(1364, 17, 710, 'item', 12, 356, 0),
(1365, 18, 20, 'money', 10000000, 0, 0),
(1366, 18, 30, 'raid_tokens', 15, 0, 0),
(1367, 18, 40, 'points', 50000, 0, 0),
(1368, 18, 50, 'item', 1, 329, 1),
(1369, 18, 70, 'money', 50000000, 0, 0),
(1370, 18, 80, 'item', 10, 361, 0),
(1371, 18, 90, 'points', 100000, 0, 0),
(1372, 18, 100, 'item', 2, 269, 1),
(1373, 18, 110, 'exp', 100, 0, 0),
(1374, 18, 120, 'points', 100000, 0, 0),
(1375, 18, 130, 'item', 1, 261, 0),
(1376, 18, 140, 'money', 50000000, 0, 0),
(1377, 18, 150, 'item', 5, 255, 1),
(1378, 18, 170, 'item', 5, 274, 0),
(1379, 18, 180, 'item', 2, 283, 0),
(1380, 18, 190, 'money', 100000000, 0, 0),
(1381, 18, 200, 'points', 300000, 0, 1),
(1382, 18, 210, 'exp', 100, 0, 0),
(1383, 18, 220, 'item', 10, 361, 0),
(1384, 18, 230, 'points', 120000, 0, 0),
(1385, 18, 240, 'item', 2, 283, 0),
(1386, 18, 250, 'item', 10, 163, 1),
(1387, 18, 270, 'raid_tokens', 30, 0, 0),
(1388, 18, 280, 'exp', 100, 0, 0),
(1389, 18, 290, 'money', 100000000, 0, 0),
(1390, 18, 300, 'item', 5, 290, 1),
(1391, 18, 320, 'item', 3, 254, 0),
(1392, 18, 330, 'item', 5, 251, 0),
(1393, 18, 340, 'item', 5, 273, 0),
(1394, 18, 350, 'item', 1, 325, 1),
(1395, 18, 380, 'exp', 100, 0, 0),
(1396, 18, 390, 'item', 5, 42, 0),
(1397, 18, 400, 'item', 5, 255, 1),
(1398, 18, 380, 'money', 150000000, 0, 0),
(1399, 18, 390, 'points', 150000, 0, 0),
(1400, 18, 400, 'points', 300000, 0, 1),
(1401, 18, 420, 'item', 5, 361, 0),
(1402, 18, 440, 'money', 200000000, 0, 0),
(1403, 18, 450, 'item', 3, 267, 1),
(1404, 18, 470, 'item', 1, 283, 0),
(1405, 18, 490, 'exp', 100, 0, 0),
(1406, 18, 520, 'item', 3, 194, 0),
(1407, 18, 540, 'item', 3, 251, 0),
(1408, 18, 550, 'item', 1, 276, 1),
(1409, 18, 570, 'points', 175000, 0, 0),
(1410, 18, 590, 'raid_tokens', 40, 0, 0),
(1411, 18, 600, 'points', 200000, 0, 1),
(1412, 18, 620, 'exp', 100, 0, 0),
(1413, 18, 640, 'money', 200000000, 0, 0),
(1414, 18, 650, 'item', 3, 283, 2),
(1415, 18, 670, 'raid_tokens', 40, 0, 0),
(1416, 18, 690, 'item', 2, 252, 0),
(1417, 18, 700, 'item', 2, 266, 1),
(1418, 18, 750, 'points', 175000, 0, 0),
(1419, 18, 800, 'exp', 100, 0, 0),
(1420, 18, 850, 'item', 5, 285, 1),
(1421, 18, 900, 'item', 3, 42, 0),
(1422, 18, 920, 'money', 200000000, 0, 0),
(1423, 18, 940, 'item', 1, 285, 0),
(1424, 18, 950, 'points', 200000, 0, 0),
(1425, 18, 970, 'raid_tokens', 50, 0, 0),
(1426, 18, 990, 'points', 350000, 0, 1),
(1427, 18, 1000, 'item', 5, 273, 0),
(1428, 18, 1010, 'item', 10, 42, 0),
(1429, 18, 1020, 'points', 250000, 0, 0),
(1430, 18, 1030, 'item', 1, 325, 1),
(1431, 18, 1040, 'item', 3, 276, 1),
(1432, 18, 1050, 'item', 1, 277, 0),
(1433, 18, 1060, 'item', 10, 361, 1),
(1434, 18, 1070, 'item', 1, 259, 0),
(1435, 18, 1080, 'item', 10, 42, 1),
(1436, 18, 1090, 'item', 1, 262, 1);

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `bp_category_user`
--

CREATE TABLE `bp_category_user` (
  `id` int(11) NOT NULL,
  `bp_category_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `points` int(11) NOT NULL DEFAULT 0,
  `crimes` bigint(20) NOT NULL DEFAULT 0,
  `attacks` bigint(20) NOT NULL DEFAULT 0,
  `mugs` bigint(20) NOT NULL DEFAULT 0,
  `busts` bigint(20) NOT NULL DEFAULT 0,
  `backalley` bigint(20) NOT NULL DEFAULT 0,
  `trains` bigint(20) NOT NULL DEFAULT 0,
  `challenge_ids_serialized` text DEFAULT NULL,
  `prize_ids_serialized` text DEFAULT NULL,
  `is_premium` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `businesses`
--

CREATE TABLE `businesses` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `rating` int(11) NOT NULL,
  `employees` int(11) NOT NULL,
  `intelligence` int(11) NOT NULL,
  `cost` decimal(15,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `business_applications`
--

CREATE TABLE `business_applications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `business_id` int(11) NOT NULL,
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `business_logs`
--

CREATE TABLE `business_logs` (
  `id` int(11) NOT NULL,
  `business_id` int(11) NOT NULL,
  `log_type` enum('join','leave','earnings') NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `business_roles`
--

CREATE TABLE `business_roles` (
  `role_id` int(11) NOT NULL,
  `business_id` int(11) NOT NULL,
  `role_name` varchar(255) NOT NULL,
  `intelligence_requirement` int(11) NOT NULL,
  `IntelligencePayout` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Data dump for tabellen `business_roles`
--

INSERT INTO `business_roles` (`role_id`, `business_id`, `role_name`, `intelligence_requirement`, `IntelligencePayout`) VALUES
(1, 3, 'Cleaner', 10, 1);

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `business_upgrades`
--

CREATE TABLE `business_upgrades` (
  `upgrade_id` int(11) NOT NULL,
  `business_id` int(11) DEFAULT NULL,
  `upgrade_name` varchar(255) NOT NULL,
  `upgrade_price` bigint(20) NOT NULL,
  `upgrade_effect` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `busts_log`
--

CREATE TABLE `busts_log` (
  `id` int(11) NOT NULL,
  `buster_id` int(11) NOT NULL,
  `jailed_id` int(11) NOT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `buyptmarketlog`
--

CREATE TABLE `buyptmarketlog` (
  `owner` int(11) NOT NULL DEFAULT 0,
  `buyer` int(11) NOT NULL,
  `amount` bigint(20) NOT NULL DEFAULT 0,
  `price` bigint(20) NOT NULL DEFAULT 0,
  `type` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `carlot`
--

CREATE TABLE `carlot` (
  `id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `image` varchar(40) NOT NULL,
  `description` text NOT NULL,
  `cost` int(11) NOT NULL,
  `buyable` int(11) NOT NULL DEFAULT 1,
  `discount` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Data dump for tabellen `carlot`
--

INSERT INTO `carlot` (`id`, `name`, `image`, `description`, `cost`, `buyable`, `discount`) VALUES
(1, 'VW-Minivan', 'http://i.imgur.com/NplLsGQ.png', 'A car used to travel to cities and race for bets.', 125000, 1, 10),
(2, 'Yamaha-YZF', 'http://i.imgur.com/vsAboNI.png', 'A car used to travel to cities and race for bets.', 250000, 1, 15),
(3, 'Mustang-Tunado', 'http://i.imgur.com/ujOelhw.png', 'A car used to travel to cities and race for bets.', 500000, 1, 22),
(4, 'Big Mack', 'http://i.imgur.com/jnxwY2T.png', 'A car used to travel to cities and race for bets.', 5800000, 1, 35),
(5, 'Bugatti-Veyron', 'http://i.imgur.com/5IztHPd.png', 'A car used to travel to cities and race for bets.', 28500000, 1, 55),
(6, 'CCXR-Trevita', 'http://i.imgur.com/xISZ7Wm.png', 'A car used to travel to cities and race for bets.', 50100000, 1, 70),
(7, 'Bugatti Veyron', 'images/Cars/Bugatti Veyron.png', 'A car used to travel to cities and race for bets.', 4900000, 0, 0);

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `cars`
--

CREATE TABLE `cars` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `max_worth` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Data dump for tabellen `cars`
--

INSERT INTO `cars` (`id`, `name`, `image_path`, `max_worth`) VALUES
(1, 'Renault Clio Sport', 'images/cars/renaultcliosport.jpeg', 5000),
(2, 'Audi A3', 'images/cars/audia3.jpg', 6000),
(3, 'BMW M3', 'images/cars/bmw-m3.jpg', 15000),
(4, 'Cadillac Escalade', 'images/cars/escalade.gif', 30000),
(5, 'Nissan Skyline', 'images/cars/nissan.jpg', 40000),
(6, 'Porsche 911', 'images/cars/porsche.jpg', 55000),
(7, 'GT 40', 'images/cars/fordgt40.jpg', 80000),
(8, 'Lamborghini Murcielago', 'images/cars/land.jpg', 110000),
(9, 'Ferrari Enzo', 'images/cars/ferrarienzo.jpg', 170000),
(10, 'TVR Speed 12', 'images/cars/tvr12.jpg', 210000),
(11, 'Mclaren F1', 'images/cars/mcf1.jpg', 250000),
(12, 'Bugatti Veyron', 'images/cars/BuggatiVeyron.jpg', 300000),
(13, 'Mercedes SLK McLaren', 'images/cars/mercedes.jpg', 330000);

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `cash5050game`
--

CREATE TABLE `cash5050game` (
  `owner` int(11) NOT NULL DEFAULT 0,
  `amount` bigint(20) NOT NULL DEFAULT 0,
  `id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `cash5050log`
--

CREATE TABLE `cash5050log` (
  `id` int(11) NOT NULL,
  `better` smallint(6) NOT NULL,
  `matcher` smallint(6) NOT NULL,
  `winner` smallint(6) NOT NULL,
  `amount` int(11) NOT NULL,
  `betterip` varchar(30) NOT NULL,
  `matcherip` varchar(30) NOT NULL,
  `timestamp` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `cashlottery`
--

CREATE TABLE `cashlottery` (
  `userid` int(11) NOT NULL,
  `tickets` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `cc`
--

CREATE TABLE `cc` (
  `id` int(11) NOT NULL,
  `leader` int(11) NOT NULL COMMENT 'ID of the crime leader',
  `wmaster` int(11) DEFAULT NULL COMMENT 'ID of the weapons master',
  `emaster` int(11) DEFAULT NULL COMMENT 'ID of the explosives master',
  `gdriver` int(11) DEFAULT NULL COMMENT 'ID of the getaway driver',
  `weapons` varchar(255) DEFAULT 'None' COMMENT 'Type of weapon used',
  `explosives` varchar(255) DEFAULT 'None' COMMENT 'Type of explosive used',
  `car` varchar(255) DEFAULT 'None' COMMENT 'Car used in the crime',
  `percentages` varchar(50) NOT NULL COMMENT 'Percentages in the format "leader-wmaster-emaster-gdriver"',
  `cardam` int(11) DEFAULT 0 COMMENT 'Car damage percentage',
  `type` tinyint(4) NOT NULL COMMENT '1 for terrorism, 2 for execute, 3 for assassinate',
  `carid` int(11) DEFAULT NULL COMMENT 'ID of the car used by the getaway driver',
  `cctype` enum('terrorism','execute','assassinate') NOT NULL COMMENT 'Type of crime',
  `leaderperc` int(11) NOT NULL COMMENT 'Percentage reward for the leader',
  `wmasterperc` int(11) NOT NULL COMMENT 'Percentage reward for the weapons master',
  `emasterperc` int(11) NOT NULL COMMENT 'Percentage reward for the explosives master',
  `driverperc` int(11) NOT NULL COMMENT 'Percentage reward for the getaway driver',
  `wmasterready` enum('Ready','Not Ready') DEFAULT 'Not Ready' COMMENT 'Weapons master readiness',
  `emasterready` enum('Ready','Not Ready') DEFAULT 'Not Ready' COMMENT 'Explosives master readiness',
  `driverready` enum('Ready','Not Ready') DEFAULT 'Not Ready' COMMENT 'Getaway driver readiness'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `chats`
--

CREATE TABLE `chats` (
  `id` int(11) UNSIGNED NOT NULL,
  `type` enum('direct','group','gang') NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `owner_id` int(11) DEFAULT NULL,
  `gang_id` bigint(20) UNSIGNED DEFAULT NULL,
  `last_message_id` bigint(20) UNSIGNED DEFAULT NULL,
  `last_message_at` datetime DEFAULT NULL,
  `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `chat_invites`
--

CREATE TABLE `chat_invites` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `chat_id` bigint(20) UNSIGNED NOT NULL,
  `inviter_id` bigint(20) UNSIGNED NOT NULL,
  `invitee_id` bigint(20) UNSIGNED DEFAULT NULL,
  `invite_code` char(16) DEFAULT NULL,
  `status` enum('pending','accepted','declined','revoked','expired') NOT NULL DEFAULT 'pending',
  `expires_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `chat_participants`
--

CREATE TABLE `chat_participants` (
  `chat_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `role` enum('owner','admin','member') NOT NULL DEFAULT 'member',
  `joined_at` datetime NOT NULL DEFAULT current_timestamp(),
  `notifications` enum('all','mentions','none') NOT NULL DEFAULT 'all',
  `is_muted` tinyint(1) NOT NULL DEFAULT 0,
  `mute_until` datetime DEFAULT NULL,
  `is_pinned` tinyint(1) NOT NULL DEFAULT 0,
  `last_read_message_id` int(11) UNSIGNED DEFAULT NULL,
  `last_read_at` datetime DEFAULT NULL,
  `soft_deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `chat_rating`
--

CREATE TABLE `chat_rating` (
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `rating_action` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `cities`
--

CREATE TABLE `cities` (
  `id` int(11) NOT NULL,
  `country` int(11) NOT NULL,
  `name` varchar(75) NOT NULL DEFAULT '',
  `levelreq` int(11) NOT NULL DEFAULT 0,
  `rmonly` int(11) NOT NULL DEFAULT 0,
  `landleft` int(11) NOT NULL,
  `landprice` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `pres` tinyint(1) NOT NULL DEFAULT 0,
  `description` varchar(100) NOT NULL,
  `owned_points` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Data dump for tabellen `cities`
--

INSERT INTO `cities` (`id`, `country`, `name`, `levelreq`, `rmonly`, `landleft`, `landprice`, `price`, `pres`, `description`, `owned_points`) VALUES
(1, 1, 'Storm City', 1, 0, 0, 0, 20000, 0, ' A city with towering crime families, hiding secrets in the ancient forests.', 500),
(39, 1, 'Broken City', 100, 0, 0, 0, 50000, 0, ' A broken city. will you find your way around?', 1000),
(40, 1, 'Fractured Falls', 200, 0, 0, 0, 350000, 0, '', 2000),
(41, 1, 'Ruined Ridge', 350, 0, 0, 0, 1250000, 0, '', 2250),
(42, 1, 'Ravaged Ruins', 500, 0, 0, 0, 2500000, 0, '', 2500),
(43, 2, 'Admin Only', 1, 0, 0, 0, 1, 0, '', 1000),
(44, 1, 'Twilight Harbor', 600, 0, 0, 0, 7500000, 0, '', 3000),
(45, 1, 'Starfall Enclave', 750, 0, 0, 0, 12500000, 0, '', 3250),
(46, 1, 'Crimson Canyons', 1000, 0, 0, 0, 25000000, 0, '', 3500),
(47, 1, 'Bloodstone Bay', 1500, 0, 0, 0, 50000000, 0, '', 4250),
(48, 1, 'Daggerpoint City', 2500, 0, 0, 0, 100000000, 0, '', 5000),
(49, 1, 'Ironcliff Heights', 3000, 0, 0, 0, 200000000, 0, '', 6500);

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `cityevents`
--

CREATE TABLE `cityevents` (
  `id` int(11) NOT NULL,
  `to` int(11) NOT NULL,
  `timesent` int(11) NOT NULL,
  `text` text CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `viewed` tinyint(1) NOT NULL DEFAULT 1,
  `extra` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `citygame`
--

CREATE TABLE `citygame` (
  `id` int(11) NOT NULL,
  `event_type` enum('text','money','points','item','credits','raidtokens','jail','hospital','shadyDealer','injuredStranger') DEFAULT NULL,
  `description_template` text DEFAULT NULL,
  `min_value` int(11) DEFAULT NULL,
  `max_value` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `probability` float DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Data dump for tabellen `citygame`
--

INSERT INTO `citygame` (`id`, `event_type`, `description_template`, `min_value`, `max_value`, `item_id`, `probability`) VALUES
(1, 'hospital', 'You tripped over a branch whilst walking around the maze.', 300, 300, 0, 0.3),
(2, 'text', 'You walk through the maze and find nothing!', 0, 0, 0, 15),
(3, 'text', 'After hours of searching, you come up with nothing. Keep searching you might find something soon!', 0, 0, 0, 15),
(4, 'credits', 'You bumped into a mysterious person who handed you [credits_amount] gold.', 1, 3, 0, 0.5),
(5, 'raidtokens', 'You help out a local and in gratitude, they give you [raidtokens_amount]', 1, 3, 0, 1),
(7, 'jail', 'Attempted to break into a car and got arrested.', 300, 300, 0, 1),
(8, 'money', 'Whilst walking past the pub, You stumble across $[money_amount].', 10000, 20000, 0, 1),
(9, 'points', 'Whilst peeking into a discarded wallet, you find [points_amount] points.', 10, 100, 0, 1),
(10, 'item', 'You find a(n) [item_name] when searching the pawn brokers', 1, 1, 225, 0.35),
(11, 'item', 'You find a(n) [item_name] when searching the pawn brokers', 1, 1, 226, 0.35),
(12, 'item', 'You find a(n) [item_name] when searching the pawn brokers', 1, 1, 227, 0.35),
(16, 'item', 'You find a(n) [item_name] when searching the STreets', 1, 1, 228, 0.1),
(17, 'item', 'You find a(n) [item_name] when searching the pawn brokers', 1, 1, 209, 0.3),
(18, 'item', 'You find a(n) [item_name] when searching the pawn brokers', 1, 1, 210, 0.3),
(19, 'item', 'You find a(n) [item_name] when searching the pawn brokers', 1, 1, 211, 0.3),
(20, 'item', 'You find a(n) [item_name] when searching the pawn brokers', 1, 1, 212, 0.3),
(21, 'raidtokens', 'You Begin Searching the alleyway and you have discovered [raidtokens_amount]', 1, 4, 0, 1),
(22, 'raidtokens', 'You Begin Searching the abandoned warehouse and you have discovered [raidtokens_amount]', 1, 4, 0, 0.5),
(23, 'hospital', 'You got into a fight in the maze and got your ass kicked', 600, 600, 0, 0.1),
(24, 'hospital', 'You got beat up real bad, Gonna take a long time to recover.', 1200, 1200, 0, 0.05),
(25, 'text', 'After hours of searching, you come up with nothing. Keep searching you might find something soon!', 0, 0, 0, 5),
(27, 'money', 'Whilst walking past the club, You stumble across [money_amount].', 20000, 70000, 0, 1),
(28, 'text', 'After hours of searching, you come up with nothing. Keep searching you might find something soon!', 0, 0, 0, 10),
(31, 'money', 'You stumbled across [money_amount] when searching the strip club', 40000, 100000, 0, 1),
(32, 'money', 'You stumbled across [money_amount] when searching a Plane Hangar', 20000, 75000, 0, 1),
(33, 'item', 'You find a(n) [item_name] when mugging a mobster', 1, 1, 8, 0.2),
(34, 'item', 'You find a(n) [item_name] when visiting a bodyguard', 1, 1, 9, 0.2),
(35, 'jail', 'Attempted to break into a car and got arrested.', 300, 300, 0, 1),
(36, 'item', 'You find a(n) [item_name] when searching the pawn brokers', 1, 1, 273, 0.1),
(37, 'item', 'You find a(n) [item_name] when searching the pawn brokers', 1, 1, 274, 0.1),
(38, 'item', 'You find a(n) [item_name] when searching the pawn brokers', 1, 1, 275, 0.1),
(39, 'item', 'You find a(n) [item_name] when searching the pawn brokers', 1, 1, 318, 0.01),
(40, 'item', 'You find a(n) [item_name] when searching the pawn brokers', 1, 1, 319, 0.01),
(41, 'item', 'Out the corner of your eyes, you spot a color you\'re not used to see, it\'s a peculiar oval shaped item, picking it up you find out it\'s a [item_name].', 1, 1, 336, -1.5),
(42, 'item', 'Out the corner of your eyes, you spot a color you\'re not used to see, it\'s a peculiar oval shaped item, picking it up you find out it\'s a [item_name].', 1, 1, 337, -0.6),
(43, 'item', 'Out the corner of your eyes, you spot a color you\'re not used to see, it\'s a peculiar oval shaped item, picking it up you find out it\'s an [item_name].', 1, 1, 338, -0.3),
(44, 'item', 'You find a [item_name] when searching the alleys, you can use it to summon Don Egghopper.', 1, 1, 344, -0.1),
(45, 'item', 'Out the corner of your eyes, you spot a shiny object, it\'s a peculiar oval shaped item, picking it up you find out it\'s a [item_name].', 1, 1, 348, -0.05);

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `itemid` int(11) DEFAULT NULL,
  `userid` int(11) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `date_commented` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `contactlist`
--

CREATE TABLE `contactlist` (
  `id` int(11) NOT NULL,
  `playerid` int(11) NOT NULL,
  `contactid` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `notes` varchar(60) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Data dump for tabellen `contactlist`
--

INSERT INTO `contactlist` (`id`, `playerid`, `contactid`, `type`, `notes`) VALUES
(0, 4, 97, 1, '');

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `countries`
--

CREATE TABLE `countries` (
  `id` int(11) NOT NULL,
  `name` varchar(75) NOT NULL DEFAULT '',
  `levelreq` int(11) NOT NULL DEFAULT 0,
  `rmonly` int(11) NOT NULL DEFAULT 0,
  `show` int(11) NOT NULL DEFAULT 1
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Data dump for tabellen `countries`
--

INSERT INTO `countries` (`id`, `name`, `levelreq`, `rmonly`, `show`) VALUES
(1, 'Uk', 1, 0, 1);

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `name` varchar(500) NOT NULL,
  `duration` int(11) NOT NULL,
  `needed` int(11) NOT NULL,
  `strength` int(11) NOT NULL,
  `defense` int(11) NOT NULL,
  `speed` int(11) NOT NULL,
  `gcse` int(11) NOT NULL,
  `cost` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Data dump for tabellen `courses`
--

INSERT INTO `courses` (`id`, `name`, `duration`, `needed`, `strength`, `defense`, `speed`, `gcse`, `cost`) VALUES
(1, 'Mobster 101: Introduction to the Underworld', 3, 0, 50000, 50000, 50000, 1, 100000),
(2, 'Basic Brawn: Strengthening Foundations', 5, 2, 300000, 0, 0, 2, 325000),
(3, 'Street Survival: Essentials of Defense', 5, 2, 0, 300000, 0, 3, 325000),
(4, 'Speedy Schemes: Fundamentals of Agility', 5, 2, 0, 0, 300000, 1, 325000),
(14, 'Muscle Building Mastery: Intermediate Strength Training', 7, 4, 2000000, 0, 0, 1, 5000000),
(15, 'Evasive Maneuvers: Advancing Speed Techniques', 7, 4, 0, 0, 2000000, 1, 5000000),
(16, 'Guardian\'s Guide: Intermediate Defensive Strategies', 7, 4, 0, 2000000, 0, 1, 5000000),
(17, 'Tactical Thinking: Intermediate Combat Planning', 14, 6, 5000000, 5000000, 5000000, 1, 30000000),
(18, 'Elite Enforcer: Advanced Strength and Conditioning', 20, 8, 50000000, 0, 0, 1, 75000000),
(19, 'Swift Shadows: Mastering Speed and Stealth', 20, 8, 0, 0, 50000000, 1, 75000000),
(20, 'Elite Brute: Mastering Defense', 20, 8, 0, 50000000, 0, 1, 75000000),
(21, 'Combat Mastery', 31, 10, 250000000, 250000000, 250000000, 1, 1500000000);

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `crafter_cooldown`
--

CREATE TABLE `crafter_cooldown` (
  `user_id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `creditsmarket`
--

CREATE TABLE `creditsmarket` (
  `id` int(11) NOT NULL,
  `owner` int(11) NOT NULL,
  `amount` bigint(20) NOT NULL,
  `price` bigint(20) NOT NULL,
  `type` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `crewinvites`
--

CREATE TABLE `crewinvites` (
  `playerid` int(11) NOT NULL,
  `crewid` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `crewranks`
--

CREATE TABLE `crewranks` (
  `id` int(11) NOT NULL,
  `crew` int(11) NOT NULL,
  `title` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `members` int(11) NOT NULL DEFAULT 0,
  `crime` int(11) NOT NULL DEFAULT 0,
  `vault` int(11) NOT NULL DEFAULT 0,
  `crewranks` int(11) NOT NULL DEFAULT 0,
  `massmail` int(11) NOT NULL DEFAULT 0,
  `applications` int(11) NOT NULL DEFAULT 0,
  `appearance` int(11) NOT NULL DEFAULT 0,
  `invite` int(11) NOT NULL DEFAULT 0,
  `houses` int(11) NOT NULL DEFAULT 0,
  `upgrade` int(11) NOT NULL DEFAULT 1,
  `gforum` int(11) NOT NULL DEFAULT 0,
  `polls` int(11) NOT NULL DEFAULT 0,
  `gangwars` int(11) NOT NULL,
  `ganggrad` int(11) NOT NULL,
  `color` varchar(7) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `crews`
--

CREATE TABLE `crews` (
  `id` int(11) NOT NULL,
  `tmstats` bigint(20) NOT NULL DEFAULT 0,
  `leader` varchar(75) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `publicpage` text NOT NULL,
  `name` varchar(75) NOT NULL DEFAULT '',
  `tag` char(3) NOT NULL DEFAULT '',
  `banner` varchar(255) NOT NULL,
  `boughtbanner` int(11) NOT NULL,
  `exp` int(11) NOT NULL,
  `level` int(11) NOT NULL DEFAULT 1,
  `moneyvault` bigint(20) NOT NULL DEFAULT 0,
  `pointsvault` int(11) NOT NULL DEFAULT 0,
  `crime` int(11) NOT NULL,
  `ending` varchar(255) NOT NULL,
  `crimestarter` int(11) NOT NULL,
  `ghouse` int(11) NOT NULL DEFAULT 0,
  `capacity` int(11) NOT NULL DEFAULT 15,
  `tax` int(11) NOT NULL DEFAULT 0,
  `tColor1` varchar(7) NOT NULL DEFAULT '#000000',
  `tColor2` varchar(7) NOT NULL DEFAULT '#000000',
  `tColor3` varchar(7) NOT NULL DEFAULT '#000000',
  `formattedTag` enum('Yes','No') NOT NULL DEFAULT 'No',
  `bbattackwon` int(11) NOT NULL DEFAULT 0,
  `bbattacklost` int(11) NOT NULL DEFAULT 0,
  `snapshot_time` int(11) NOT NULL,
  `respect` int(11) NOT NULL DEFAULT 1000,
  `dailyKills` int(11) NOT NULL DEFAULT 0,
  `dailyMugs` int(11) NOT NULL DEFAULT 0,
  `dailyBusts` int(11) NOT NULL DEFAULT 0,
  `dailyCrimes` float(11,4) NOT NULL DEFAULT 0.0000,
  `upgrade1` int(11) NOT NULL DEFAULT 0,
  `upgrade2` int(11) NOT NULL DEFAULT 0,
  `upgrade3` int(11) NOT NULL DEFAULT 0,
  `upgrade4` int(11) NOT NULL DEFAULT 0,
  `upgrade5` int(11) NOT NULL DEFAULT 0,
  `upgrade6` int(11) NOT NULL DEFAULT 0,
  `upgrade7` int(11) NOT NULL DEFAULT 0,
  `upgrade8` int(11) NOT NULL DEFAULT 0,
  `upgrade9` int(11) NOT NULL DEFAULT 0,
  `upgrade10` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `crimeranks`
--

CREATE TABLE `crimeranks` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `crimeid` int(11) NOT NULL,
  `count` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `crimes`
--

CREATE TABLE `crimes` (
  `id` int(11) NOT NULL,
  `name` varchar(40) NOT NULL,
  `nerve` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Data dump for tabellen `crimes`
--

INSERT INTO `crimes` (`id`, `name`, `nerve`) VALUES
(1, 'Looting', 1),
(2, 'Steal a Bike', 3),
(3, 'Carjacking', 5),
(4, 'Beat up a Bully', 10),
(5, 'Rob a House', 25),
(38, 'Rob a Jewellery Store ', 50),
(39, 'Rob a casino', 75),
(40, 'Rob a bank', 100),
(41, 'Fraud the goverment', 150),
(42, 'Forge Art', 200),
(43, 'Traffic Illegal Arms', 250),
(44, 'Hack a Corporation', 300),
(45, 'Smuggle Drugs Internationally', 350),
(46, 'Assassinate a High-Profile Target', 400),
(47, 'Kill a high member embassy official', 500),
(48, 'Rob part of the crown jewels', 600),
(49, 'Assasinate a white house cabinet member', 750),
(50, 'Orchestrate a Major Cyber Attack on Wall', 1000),
(51, 'Ghost Hunting', 1000),
(52, 'Follow the papertrail', 1);

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `cron_logs`
--

CREATE TABLE `cron_logs` (
  `id` int(11) NOT NULL,
  `script` varchar(255) NOT NULL,
  `error` text NOT NULL,
  `timestamp` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `customitems`
--

CREATE TABLE `customitems` (
  `userid` mediumint(9) NOT NULL,
  `itemid` smallint(6) NOT NULL,
  `name` varchar(50) NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `daily_eco`
--

CREATE TABLE `daily_eco` (
  `id` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `credits` int(11) NOT NULL,
  `points` int(11) NOT NULL,
  `money` bigint(20) NOT NULL,
  `raidtokens` int(11) NOT NULL DEFAULT 0,
  `users` int(11) NOT NULL,
  `inactive_users` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `daily_user_stats`
--

CREATE TABLE `daily_user_stats` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `strength` bigint(20) NOT NULL,
  `defense` bigint(20) NOT NULL,
  `speed` bigint(20) NOT NULL,
  `agility` bigint(20) NOT NULL DEFAULT 0,
  `record_date` date NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `deflog`
--

CREATE TABLE `deflog` (
  `id` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL DEFAULT 0,
  `gangid` int(11) NOT NULL DEFAULT 0,
  `attacker` int(11) NOT NULL DEFAULT 0,
  `defender` int(11) NOT NULL DEFAULT 0,
  `winner` int(11) NOT NULL DEFAULT 0,
  `gangexp` int(11) NOT NULL,
  `active` int(11) NOT NULL,
  `respect` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `display_cabinet`
--

CREATE TABLE `display_cabinet` (
  `userid` int(11) NOT NULL,
  `itemid` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `dond`
--

CREATE TABLE `dond` (
  `uid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `easter_log`
--

CREATE TABLE `easter_log` (
  `user_id` int(11) NOT NULL,
  `who` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `reward` varchar(250) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `easter_store`
--

CREATE TABLE `easter_store` (
  `id` int(11) NOT NULL,
  `egg_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `points` int(11) NOT NULL,
  `maze` int(11) NOT NULL,
  `achievement` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Data dump for tabellen `easter_store`
--

INSERT INTO `easter_store` (`id`, `egg_id`, `quantity`, `item_id`, `points`, `maze`, `achievement`) VALUES
(1, 336, 5, 0, 20, 0, 0),
(2, 336, 10, 0, 0, 0, 1),
(3, 336, 20, 0, 0, 50, 0),
(4, 336, 50, 0, 250, 0, 0),
(5, 337, 5, 339, 0, 0, 0),
(6, 337, 10, 340, 0, 0, 0),
(7, 337, 25, 341, 0, 0, 0),
(8, 338, 1, 333, 0, 0, 0),
(9, 338, 5, 334, 0, 0, 0),
(10, 338, 10, 335, 0, 0, 0),
(11, 338, 250, 0, 0, 0, 2),
(12, 338, 100, 347, 0, 0, 0);

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `effects`
--

CREATE TABLE `effects` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `drugid` int(11) NOT NULL,
  `timeleft` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `ent_trans`
--

CREATE TABLE `ent_trans` (
  `id` int(11) NOT NULL,
  `sender` mediumint(9) NOT NULL,
  `receiver` mediumint(9) NOT NULL,
  `points` bigint(20) NOT NULL,
  `money` bigint(20) NOT NULL,
  `timestamp` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `to` mediumint(9) NOT NULL,
  `timesent` int(11) NOT NULL,
  `text` text CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `viewed` tinyint(1) NOT NULL DEFAULT 1,
  `extra` mediumint(9) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `eventslog`
--

CREATE TABLE `eventslog` (
  `id` int(11) NOT NULL,
  `to` int(11) NOT NULL,
  `timesent` int(11) NOT NULL,
  `text` text NOT NULL,
  `viewed` tinyint(1) NOT NULL DEFAULT 1,
  `extra` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `eventsmain`
--

CREATE TABLE `eventsmain` (
  `id` int(11) NOT NULL,
  `to` int(11) NOT NULL,
  `timesent` int(11) NOT NULL,
  `text` text CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `viewed` tinyint(1) NOT NULL DEFAULT 1,
  `extra` int(11) DEFAULT NULL,
  `theid` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `fiftyfifty`
--

CREATE TABLE `fiftyfifty` (
  `id` int(11) NOT NULL,
  `userid` smallint(6) NOT NULL,
  `amnt` bigint(20) NOT NULL,
  `currency` enum('cash','points','credits') NOT NULL,
  `betterip` varchar(255) DEFAULT NULL,
  `timestamp` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `fiftyfiftylogs`
--

CREATE TABLE `fiftyfiftylogs` (
  `id` int(11) NOT NULL,
  `better` smallint(6) NOT NULL,
  `taker` smallint(6) NOT NULL,
  `amnt` int(11) NOT NULL DEFAULT 0,
  `winner` smallint(6) NOT NULL,
  `betterip` varchar(255) NOT NULL,
  `matcherip` varchar(255) NOT NULL,
  `timestamp` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `fireworks_log`
--

CREATE TABLE `fireworks_log` (
  `user_id` int(11) NOT NULL,
  `who` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `reward` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `forumfollows`
--

CREATE TABLE `forumfollows` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `ftid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `forumpermissions`
--

CREATE TABLE `forumpermissions` (
  `id` int(11) NOT NULL,
  `fid` int(11) NOT NULL,
  `gid` int(11) NOT NULL,
  `canview` tinyint(1) NOT NULL DEFAULT 0,
  `canviewthreads` tinyint(1) NOT NULL DEFAULT 0,
  `canonlyviewownthreads` tinyint(1) NOT NULL DEFAULT 0,
  `canpostthreads` tinyint(1) NOT NULL DEFAULT 0,
  `canpostreplys` tinyint(1) NOT NULL DEFAULT 0,
  `caneditposts` tinyint(1) NOT NULL DEFAULT 0,
  `candeleteposts` tinyint(1) NOT NULL DEFAULT 0,
  `candeletethreads` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `forumpostrates`
--

CREATE TABLE `forumpostrates` (
  `id` int(11) NOT NULL,
  `fpid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `rate` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `forumreplyrates`
--

CREATE TABLE `forumreplyrates` (
  `id` int(11) NOT NULL,
  `postid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `rate` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `forums`
--

CREATE TABLE `forums` (
  `id` int(11) NOT NULL,
  `name` varchar(120) NOT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `type` char(1) NOT NULL DEFAULT 'c',
  `parent` int(11) DEFAULT NULL,
  `disporder` smallint(6) NOT NULL DEFAULT 0,
  `threadcount` int(11) NOT NULL DEFAULT 0,
  `postcount` int(11) NOT NULL DEFAULT 0,
  `lastpost` int(11) DEFAULT NULL,
  `lastposter` int(11) DEFAULT NULL,
  `lastposttime` timestamp NULL DEFAULT NULL,
  `lastpostsubject` varchar(120) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `forum_browsers`
--

CREATE TABLE `forum_browsers` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `age` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `forum_forums`
--

CREATE TABLE `forum_forums` (
  `ff_id` int(11) NOT NULL,
  `ff_name` varchar(255) NOT NULL,
  `ff_desc` varchar(255) NOT NULL,
  `ff_posts` int(11) NOT NULL DEFAULT 0,
  `ff_topics` int(11) NOT NULL DEFAULT 0,
  `ff_lp_time` int(11) NOT NULL DEFAULT 0,
  `ff_lp_poster_id` int(11) NOT NULL DEFAULT 0,
  `ff_lp_poster_name` mediumtext NOT NULL,
  `ff_lp_t_id` int(11) NOT NULL DEFAULT 0,
  `ff_lp_t_name` varchar(255) NOT NULL,
  `ff_auth` enum('public','gang','staff') NOT NULL DEFAULT 'public',
  `ff_owner` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `freplies`
--

CREATE TABLE `freplies` (
  `sectionid` tinyint(4) NOT NULL,
  `topicid` mediumint(9) NOT NULL,
  `postid` int(11) NOT NULL,
  `playerid` mediumint(9) NOT NULL,
  `timesent` int(11) NOT NULL,
  `body` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `rateup` smallint(6) NOT NULL DEFAULT 0,
  `ratedown` smallint(6) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `ftopics`
--

CREATE TABLE `ftopics` (
  `sectionid` tinyint(4) NOT NULL,
  `forumid` mediumint(9) NOT NULL,
  `playerid` mediumint(9) NOT NULL,
  `timesent` int(11) NOT NULL,
  `lastreply` int(11) NOT NULL,
  `views` mediumint(9) NOT NULL DEFAULT 0,
  `subject` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `body` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `reported` tinyint(1) NOT NULL DEFAULT 0,
  `reporter` mediumint(9) NOT NULL DEFAULT 0,
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `rateup` smallint(6) NOT NULL DEFAULT 0,
  `ratedown` smallint(6) NOT NULL DEFAULT 0,
  `lastposter` smallint(6) NOT NULL,
  `lastupdated` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `game`
--

CREATE TABLE `game` (
  `setting` varchar(200) NOT NULL,
  `value` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Data dump for tabellen `game`
--

INSERT INTO `game` (`setting`, `value`) VALUES
('zombies', '46'),
('min_money', '100'),
('max_money', '5000'),
('min_points', '1'),
('max_points', '6'),
('min_exp', '50'),
('max_exp', '600'),
('item_rate', '3'),
('findable_items', '1,2,3');

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `gamebonus`
--

CREATE TABLE `gamebonus` (
  `ID` int(11) NOT NULL,
  `Title` varchar(50) NOT NULL,
  `Description` varchar(100) NOT NULL,
  `Target` int(11) NOT NULL,
  `Current` int(11) NOT NULL,
  `Time` int(11) NOT NULL,
  `Timetoadd` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `gameevents`
--

CREATE TABLE `gameevents` (
  `cashlottery` text NOT NULL,
  `ptslottery` text NOT NULL,
  `tophitman` text NOT NULL,
  `topleveler` text NOT NULL,
  `cashlotteryid` int(11) NOT NULL,
  `ptslotteryid` int(11) NOT NULL,
  `tophitmanid` int(11) NOT NULL,
  `toplevelerid` int(11) NOT NULL,
  `dailytrains` text NOT NULL,
  `dailymugs` text NOT NULL,
  `dailytrainsid` int(11) NOT NULL,
  `dailymugsid` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `game_updates`
--

CREATE TABLE `game_updates` (
  `id` int(11) NOT NULL,
  `update_text` text NOT NULL,
  `update_posted` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `gangapps`
--

CREATE TABLE `gangapps` (
  `applicant` int(11) NOT NULL,
  `gangid` int(11) NOT NULL,
  `date` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `gangarmory`
--

CREATE TABLE `gangarmory` (
  `itemid` int(11) NOT NULL,
  `gangid` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `gangcontest`
--

CREATE TABLE `gangcontest` (
  `userid` int(11) NOT NULL,
  `gangid` int(11) NOT NULL,
  `mugs` int(11) NOT NULL DEFAULT 0,
  `crimes` int(11) NOT NULL DEFAULT 0,
  `busts` int(11) NOT NULL DEFAULT 0,
  `kills` int(11) NOT NULL DEFAULT 0,
  `exp` decimal(65,0) NOT NULL DEFAULT 0,
  `backalley` int(11) NOT NULL DEFAULT 0,
  `total_mugs` bigint(20) NOT NULL DEFAULT 0,
  `total_crimes` bigint(20) NOT NULL DEFAULT 0,
  `total_busts` bigint(20) NOT NULL DEFAULT 0,
  `total_kills` bigint(20) NOT NULL DEFAULT 0,
  `total_exp` int(11) NOT NULL DEFAULT 0,
  `total_backalley` int(11) NOT NULL DEFAULT 0,
  `total_tax` bigint(20) NOT NULL,
  `tax` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `gangcontest_snapshots`
--

CREATE TABLE `gangcontest_snapshots` (
  `userid` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `gangid` int(11) NOT NULL,
  `mugs` int(11) NOT NULL DEFAULT 0,
  `crimes` int(11) NOT NULL DEFAULT 0,
  `busts` int(11) NOT NULL DEFAULT 0,
  `kills` int(11) NOT NULL DEFAULT 0,
  `exp` int(11) NOT NULL DEFAULT 0,
  `tax` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `gangcrime`
--

CREATE TABLE `gangcrime` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `duration` int(11) NOT NULL,
  `reward` int(11) NOT NULL,
  `members` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Data dump for tabellen `gangcrime`
--

INSERT INTO `gangcrime` (`id`, `name`, `duration`, `reward`, `members`) VALUES
(1, 'Confront a Deceiver', 1, 10000, 2),
(2, 'Orchestrate a Ticket Scheme', 1, 25000, 2),
(3, 'Undermine a Rival Gang', 3, 75000, 3),
(4, 'Covert Cargo Operation (Drugs)', 4, 110000, 3),
(5, 'Smuggle Weapons', 4, 180000, 4),
(6, 'Rob A Bank', 7, 275000, 6),
(7, 'Jewellery Heist', 11, 450000, 8),
(8, 'Hack The CIA', 14, 600000, 8),
(9, 'Steal A Tank', 18, 750000, 10);

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `gangcrimes`
--

CREATE TABLE `gangcrimes` (
  `id` int(11) NOT NULL,
  `crime` text NOT NULL,
  `minonline` int(11) NOT NULL DEFAULT 0,
  `mingang` int(11) NOT NULL DEFAULT 0,
  `success` text NOT NULL,
  `failed` text NOT NULL,
  `gangexp` int(11) NOT NULL,
  `money` int(11) NOT NULL,
  `time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `gangevents`
--

CREATE TABLE `gangevents` (
  `id` int(11) NOT NULL,
  `gang` int(11) NOT NULL,
  `timesent` int(11) NOT NULL,
  `text` text NOT NULL,
  `extra` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `ganginvites`
--

CREATE TABLE `ganginvites` (
  `playerid` int(11) NOT NULL,
  `gangid` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `gangmail`
--

CREATE TABLE `gangmail` (
  `gmailid` int(11) NOT NULL,
  `gangid` int(11) NOT NULL,
  `playerid` int(11) NOT NULL,
  `timesent` int(11) NOT NULL,
  `subject` varchar(500) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `body` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `is_pinned` tinyint(1) DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `gangpolls`
--

CREATE TABLE `gangpolls` (
  `id` int(11) NOT NULL,
  `gangid` int(11) NOT NULL,
  `votes` int(11) NOT NULL DEFAULT 0,
  `yes` int(11) NOT NULL DEFAULT 0,
  `no` int(11) NOT NULL DEFAULT 0,
  `question` text NOT NULL,
  `yanswer` varchar(100) NOT NULL,
  `nanswer` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `gangs`
--

CREATE TABLE `gangs` (
  `id` int(11) NOT NULL,
  `tmstats` bigint(20) NOT NULL DEFAULT 0,
  `leader` varchar(75) NOT NULL DEFAULT '',
  `description` text DEFAULT NULL,
  `publicpage` text DEFAULT NULL,
  `name` varchar(75) NOT NULL DEFAULT '',
  `tag` char(3) NOT NULL DEFAULT '',
  `banner` varchar(255) DEFAULT NULL,
  `boughtbanner` int(11) NOT NULL DEFAULT 0,
  `exp` int(11) NOT NULL DEFAULT 0,
  `level` int(11) NOT NULL DEFAULT 1,
  `moneyvault` bigint(20) NOT NULL DEFAULT 0,
  `pointsvault` int(11) NOT NULL DEFAULT 0,
  `crime` int(11) NOT NULL DEFAULT 0,
  `ending` varchar(255) DEFAULT NULL,
  `crimestarter` int(11) NOT NULL DEFAULT 0,
  `ghouse` int(11) NOT NULL DEFAULT 0,
  `capacity` int(11) NOT NULL DEFAULT 15,
  `tax` int(11) NOT NULL DEFAULT 0,
  `tColor1` varchar(7) NOT NULL DEFAULT '#000000',
  `tColor2` varchar(7) NOT NULL DEFAULT '#000000',
  `tColor3` varchar(7) NOT NULL DEFAULT '#000000',
  `formattedTag` enum('Yes','No') NOT NULL DEFAULT 'No',
  `bbattackwon` int(11) NOT NULL DEFAULT 0,
  `bbattacklost` int(11) NOT NULL DEFAULT 0,
  `snapshot_time` int(11) NOT NULL DEFAULT 0,
  `respect` int(11) NOT NULL DEFAULT 1000,
  `dailyKills` int(11) NOT NULL DEFAULT 0,
  `dailyMugs` int(11) NOT NULL DEFAULT 0,
  `dailyBusts` int(11) NOT NULL DEFAULT 0,
  `dailyCrimes` float(11,4) NOT NULL DEFAULT 0.0000,
  `upgrade1` int(11) NOT NULL DEFAULT 0,
  `upgrade2` int(11) NOT NULL DEFAULT 0,
  `upgrade3` int(11) NOT NULL DEFAULT 0,
  `upgrade4` int(11) NOT NULL DEFAULT 0,
  `upgrade5` int(11) NOT NULL DEFAULT 0,
  `upgrade6` int(11) NOT NULL DEFAULT 0,
  `upgrade7` int(11) NOT NULL DEFAULT 0,
  `upgrade8` int(11) NOT NULL DEFAULT 0,
  `upgrade9` int(11) NOT NULL DEFAULT 0,
  `upgrade10` int(11) NOT NULL DEFAULT 0,
  `upgrade_agility` int(11) NOT NULL DEFAULT 0,
  `upgrade_crimecash` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `gangtargetlist`
--

CREATE TABLE `gangtargetlist` (
  `id` int(11) NOT NULL,
  `gangid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `notes` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `gangwars`
--

CREATE TABLE `gangwars` (
  `warid` int(11) NOT NULL,
  `gang1` int(11) NOT NULL DEFAULT 0,
  `gang2` int(11) NOT NULL DEFAULT 0,
  `gang1score` int(11) NOT NULL DEFAULT 0,
  `gang2score` int(11) NOT NULL DEFAULT 0,
  `bet` int(11) NOT NULL DEFAULT 0,
  `timesent` int(11) NOT NULL DEFAULT 0,
  `timeending` int(11) NOT NULL DEFAULT 0,
  `accepted` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `gang_comp_leaderboard`
--

CREATE TABLE `gang_comp_leaderboard` (
  `id` int(11) NOT NULL,
  `gang_id` int(11) NOT NULL,
  `daily_missions_complete` bigint(20) NOT NULL DEFAULT 0,
  `weekly_missions_complete` bigint(20) NOT NULL DEFAULT 0,
  `daily_mugs_complete` bigint(20) NOT NULL DEFAULT 0,
  `weekly_mugs_complete` bigint(20) NOT NULL DEFAULT 0,
  `daily_busts_complete` bigint(20) NOT NULL DEFAULT 0,
  `weekly_busts_complete` bigint(20) NOT NULL DEFAULT 0,
  `daily_attacks_complete` bigint(20) NOT NULL DEFAULT 0,
  `weekly_attacks_complete` bigint(20) NOT NULL DEFAULT 0,
  `daily_ba_complete` bigint(20) NOT NULL DEFAULT 0,
  `weekly_ba_complete` bigint(20) NOT NULL DEFAULT 0,
  `daily_crimes_complete` bigint(20) NOT NULL DEFAULT 0,
  `weekly_crimes_complete` bigint(20) NOT NULL DEFAULT 0,
  `serialised_prizes_claimed` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `gang_loans`
--

CREATE TABLE `gang_loans` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `idto` smallint(5) UNSIGNED NOT NULL,
  `gang` smallint(6) NOT NULL,
  `item` smallint(6) NOT NULL,
  `quantity` smallint(5) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `gang_missions`
--

CREATE TABLE `gang_missions` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `kills` int(11) DEFAULT 0,
  `busts` int(11) DEFAULT 0,
  `crimes` int(11) DEFAULT 0,
  `mugs` int(11) DEFAULT 0,
  `backalleys` int(11) NOT NULL DEFAULT 0,
  `reward` int(11) DEFAULT 0,
  `time` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Data dump for tabellen `gang_missions`
--

INSERT INTO `gang_missions` (`id`, `name`, `kills`, `busts`, `crimes`, `mugs`, `backalleys`, `reward`, `time`) VALUES
(1, 'Crimes', 0, 0, 10000000, 0, 0, 500000, 168),
(2, 'Kills', 90000, 0, 0, 0, 0, 250000, 168),
(3, 'Busts', 0, 90000, 0, 0, 0, 250000, 168),
(4, 'Mugs', 0, 0, 0, 90000, 0, 250000, 168),
(5, 'Backalleys', 0, 0, 0, 0, 90000, 250000, 168);

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `gang_territory_zone`
--

CREATE TABLE `gang_territory_zone` (
  `id` int(11) NOT NULL,
  `city_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `owned_by_gang_id` int(11) NOT NULL,
  `daily_points_payout` int(11) NOT NULL,
  `daily_money_payout` int(11) NOT NULL,
  `daily_raid_tokens_payout` int(11) NOT NULL,
  `daily_exp_payout` int(11) NOT NULL,
  `daily_item_payout` int(11) NOT NULL,
  `shield_time` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `gang_territory_zone_battle`
--

CREATE TABLE `gang_territory_zone_battle` (
  `id` int(11) NOT NULL,
  `gang_territory_zone_id` int(11) NOT NULL,
  `attacking_gang_id` int(11) NOT NULL,
  `defending_gang_id` int(11) NOT NULL,
  `strength_defending_user_id` int(11) NOT NULL,
  `speed_defending_user_id` int(11) NOT NULL,
  `defense_defending_user_id` int(11) NOT NULL,
  `strength_attacking_user_id` int(11) NOT NULL,
  `speed_attacking_user_id` int(11) NOT NULL,
  `defense_attacking_user_id` int(11) NOT NULL,
  `time_started` bigint(20) NOT NULL,
  `winning_gang_id` int(11) NOT NULL,
  `attacking_total_stats` bigint(20) NOT NULL,
  `attacking_speed` bigint(20) NOT NULL,
  `attacking_strength` bigint(20) NOT NULL,
  `attacking_defense` bigint(20) NOT NULL,
  `defending_total_stats` bigint(20) NOT NULL,
  `defending_speed` bigint(20) NOT NULL,
  `defending_strength` bigint(20) NOT NULL,
  `defending_defense` bigint(20) NOT NULL,
  `initial_wait` int(11) NOT NULL DEFAULT 0,
  `is_complete` tinyint(1) NOT NULL,
  `is_strength_defending_user` int(11) NOT NULL,
  `is_strength_attacking_user` int(11) NOT NULL,
  `is_defense_defending_user` int(11) NOT NULL,
  `is_defense_attacking_user` int(11) NOT NULL,
  `is_speed_defending_user` int(11) NOT NULL,
  `is_speed_attacking_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `gang_territory_zone_battle_log`
--

CREATE TABLE `gang_territory_zone_battle_log` (
  `id` int(11) NOT NULL,
  `gang_territory_zone_battle_id` int(11) NOT NULL,
  `attacking_gang_id` int(11) NOT NULL,
  `defending_gang_id` int(11) NOT NULL,
  `is_first_attack` tinyint(1) NOT NULL,
  `damage` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `gang_territory_zone_history`
--

CREATE TABLE `gang_territory_zone_history` (
  `id` int(11) NOT NULL,
  `gang_territory_zone_id` int(11) NOT NULL,
  `gang_id` int(11) NOT NULL,
  `takeover_time` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `gang_vault_log`
--

CREATE TABLE `gang_vault_log` (
  `id` int(11) NOT NULL,
  `gang_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` varchar(200) NOT NULL,
  `action` varchar(200) NOT NULL,
  `added` bigint(20) NOT NULL,
  `balance` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `garage`
--

CREATE TABLE `garage` (
  `id` int(11) NOT NULL,
  `owner` int(11) NOT NULL DEFAULT 0,
  `car` varchar(100) NOT NULL DEFAULT '',
  `damage` varchar(100) NOT NULL DEFAULT '',
  `origion` varchar(100) NOT NULL DEFAULT '',
  `location` varchar(100) NOT NULL DEFAULT '',
  `upgrades` varchar(100) NOT NULL DEFAULT '0-0-0-0-0-0-0-0',
  `status` enum('0','1','2','3','4') NOT NULL DEFAULT '0',
  `worth` int(11) NOT NULL DEFAULT 0,
  `shiptime` varchar(100) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Data dump for tabellen `garage`
--

INSERT INTO `garage` (`id`, `owner`, `car`, `damage`, `origion`, `location`, `upgrades`, `status`, `worth`, `shiptime`) VALUES
(1, 1, 'Lamborghini Murcielago', '15', '1', '1', '0-0-0-0-0-0-0-0', '0', 14667, ''),
(2, 1, 'Mercedes SLK McLaren', '9', '1', '1', '0-0-0-0-0-0-0-0', '0', 73333, ''),
(3, 1, 'Porsche 911', '0', '1', '1', '0-0-0-0-0-0-0-0', '0', 55000, ''),
(5, 2, 'Cadillac Escalade', '47', '1', '1', '0-0-0-0-0-0-0-0', '0', 1277, ''),
(6, 1, 'Lamborghini Murcielago', '0', '1', '1', '0-0-0-0-0-0-0-0', '0', 110000, ''),
(7, 1, 'Porsche 911', '0', '1', '1', '0-0-0-0-0-0-0-0', '0', 55000, ''),
(30, 1, 'Cadillac Escalade', '0', '1', '1', '0-0-0-0-0-0-0-0', '0', 30000, ''),
(31, 1, 'Bugatti Veyron', '47', '1', '1', '0-0-0-0-0-0-0-0', '0', 12766, '');

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `gcrimelog`
--

CREATE TABLE `gcrimelog` (
  `id` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL DEFAULT 0,
  `gangid` int(11) NOT NULL DEFAULT 0,
  `text` varchar(500) NOT NULL,
  `userid` int(11) NOT NULL,
  `reward` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `gcusers`
--

CREATE TABLE `gcusers` (
  `userid` int(11) NOT NULL,
  `typing` tinyint(1) NOT NULL DEFAULT 0,
  `lastseen` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `gfreplies`
--

CREATE TABLE `gfreplies` (
  `sectionid` int(11) NOT NULL,
  `topicid` int(11) NOT NULL,
  `postid` int(11) NOT NULL,
  `playerid` int(11) NOT NULL,
  `timesent` int(11) NOT NULL,
  `body` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `reported` int(11) NOT NULL DEFAULT 0,
  `reporter` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `gftopics`
--

CREATE TABLE `gftopics` (
  `sectionid` int(11) NOT NULL,
  `forumid` int(11) NOT NULL,
  `playerid` int(11) NOT NULL,
  `timesent` int(11) NOT NULL,
  `lastreply` int(11) NOT NULL,
  `views` int(11) NOT NULL DEFAULT 0,
  `subject` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `body` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `locked` int(11) NOT NULL DEFAULT 0,
  `sticky` int(11) NOT NULL DEFAULT 0,
  `reported` int(11) NOT NULL DEFAULT 0,
  `reporter` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `ghouses`
--

CREATE TABLE `ghouses` (
  `id` int(11) NOT NULL,
  `name` varchar(75) NOT NULL DEFAULT '',
  `awake` int(11) NOT NULL DEFAULT 0,
  `cost` bigint(20) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Data dump for tabellen `ghouses`
--

INSERT INTO `ghouses` (`id`, `name`, `awake`, `cost`) VALUES
(1, 'Abandoned Garage', 5, 10000000),
(2, 'Deserted Shed in the Woods', 10, 25000000),
(3, 'Old Farmhouse Needing Renovation', 16, 100000000),
(4, 'Modest Suburban Home', 22, 500000000),
(5, 'Charming Downtown Loft', 30, 2750000000),
(6, 'Beachfront Villa', 40, 5500000000),
(7, 'Luxurious Mountain Retreat', 50, 8500000000),
(8, 'Secluded Gated Community', 55, 15000000000),
(9, 'Hidden Bomb Shelter', 60, 20000000000);

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `gifts`
--

CREATE TABLE `gifts` (
  `userid` int(11) NOT NULL,
  `giftsid` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `gift_codes`
--

CREATE TABLE `gift_codes` (
  `id` int(11) NOT NULL,
  `code` varchar(17) NOT NULL DEFAULT '',
  `money` int(11) NOT NULL DEFAULT 0,
  `points` int(11) NOT NULL DEFAULT 0,
  `credits` int(11) NOT NULL DEFAULT 0,
  `raidpoints` int(11) NOT NULL DEFAULT 0,
  `cityturns` int(11) NOT NULL DEFAULT 0,
  `items` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Data dump for tabellen `gift_codes`
--

INSERT INTO `gift_codes` (`id`, `code`, `money`, `points`, `credits`, `raidpoints`, `cityturns`, `items`) VALUES
(1, 'AE9F4-FG12H-9OI82', 1000000, 2500, 30, 5000, 50, NULL);

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `globalchat`
--

CREATE TABLE `globalchat` (
  `id` int(11) NOT NULL,
  `playerid` int(11) NOT NULL,
  `timesent` int(11) NOT NULL,
  `body` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `global_bets`
--

CREATE TABLE `global_bets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `bet_amount` decimal(10,2) NOT NULL,
  `bet_side` varchar(10) NOT NULL,
  `result` varchar(10) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `gmusers`
--

CREATE TABLE `gmusers` (
  `userid` int(11) NOT NULL,
  `typing` tinyint(1) NOT NULL DEFAULT 0,
  `lastseen` int(11) NOT NULL,
  `gang` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `growing`
--

CREATE TABLE `growing` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `cityid` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `croptype` varchar(75) NOT NULL,
  `cropamount` int(11) NOT NULL,
  `timeplanted` int(11) NOT NULL,
  `timedone` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `grpgusers`
--

CREATE TABLE `grpgusers` (
  `id` int(11) NOT NULL,
  `loginame` varchar(75) NOT NULL DEFAULT '',
  `username` varchar(30) NOT NULL,
  `password` mediumtext NOT NULL,
  `usergroup` smallint(6) NOT NULL DEFAULT 0,
  `gender` enum('Male','Female','HaxX') NOT NULL DEFAULT 'Male',
  `level` int(11) NOT NULL DEFAULT 1,
  `exp` bigint(20) NOT NULL DEFAULT 0,
  `money` bigint(20) NOT NULL DEFAULT 25000,
  `bank` bigint(20) NOT NULL DEFAULT 100000,
  `banklog` bigint(20) NOT NULL DEFAULT 0,
  `whichbank` int(11) NOT NULL DEFAULT 0,
  `hp` int(11) NOT NULL DEFAULT 50,
  `energy` int(11) NOT NULL DEFAULT 10,
  `stamina` tinyint(4) NOT NULL DEFAULT 10,
  `max_stamina` tinyint(4) NOT NULL DEFAULT 10,
  `nerve` int(11) NOT NULL DEFAULT 5,
  `workexp` int(11) NOT NULL DEFAULT 0,
  `strength` bigint(20) NOT NULL DEFAULT 10,
  `defense` bigint(20) NOT NULL DEFAULT 10,
  `speed` bigint(20) NOT NULL DEFAULT 10,
  `agility` bigint(20) NOT NULL DEFAULT 10,
  `total` bigint(20) NOT NULL DEFAULT 0,
  `train` int(11) NOT NULL DEFAULT 0,
  `captcha` int(11) NOT NULL DEFAULT 0,
  `captcha_timestamp` int(11) NOT NULL DEFAULT 0,
  `todayskills` int(11) NOT NULL DEFAULT 0,
  `todaysexp` bigint(20) NOT NULL DEFAULT 0,
  `battlewon` int(11) NOT NULL DEFAULT 0,
  `battlelost` int(11) NOT NULL DEFAULT 0,
  `battlemoney` int(11) NOT NULL DEFAULT 0,
  `crimesucceeded` bigint(20) NOT NULL DEFAULT 0,
  `crimefailed` int(11) NOT NULL DEFAULT 0,
  `crimemoney` bigint(20) NOT NULL DEFAULT 0,
  `busts` int(11) NOT NULL DEFAULT 0,
  `caught` int(11) NOT NULL DEFAULT 0,
  `points` bigint(20) NOT NULL DEFAULT 1000,
  `rating` int(11) NOT NULL DEFAULT 0,
  `rmdays` int(11) NOT NULL DEFAULT 3,
  `news` int(11) NOT NULL DEFAULT 1,
  `posts` int(11) NOT NULL DEFAULT 0,
  `signuptime` int(11) NOT NULL DEFAULT 0,
  `lastactive` int(11) NOT NULL DEFAULT 0,
  `awake` int(11) NOT NULL DEFAULT 100,
  `email` varchar(75) NOT NULL DEFAULT '',
  `jail` int(11) NOT NULL DEFAULT 0,
  `hospital` int(11) NOT NULL DEFAULT 0,
  `hwho` varchar(55) NOT NULL DEFAULT '0',
  `hwhen` varchar(30) DEFAULT NULL,
  `hhow` varchar(30) DEFAULT NULL,
  `house` int(11) NOT NULL DEFAULT 0,
  `gang` int(11) NOT NULL DEFAULT 0,
  `grank` int(11) NOT NULL DEFAULT 0,
  `quote` mediumtext DEFAULT NULL,
  `signature` mediumtext DEFAULT NULL,
  `notepad` mediumtext DEFAULT NULL,
  `avatar` varchar(200) NOT NULL DEFAULT 'http://chaoscity.co.uk/images/noavatar.png',
  `country` int(11) NOT NULL DEFAULT 1,
  `city` int(11) NOT NULL DEFAULT 1,
  `admin` int(11) NOT NULL DEFAULT 0,
  `gm` int(11) NOT NULL DEFAULT 0,
  `fm` int(11) NOT NULL DEFAULT 0,
  `cm` int(11) NOT NULL DEFAULT 0,
  `eo` int(11) NOT NULL DEFAULT 0,
  `st` int(11) NOT NULL DEFAULT 0,
  `searchdowntown` int(11) NOT NULL DEFAULT 100,
  `slots_left1` int(11) NOT NULL DEFAULT 250,
  `luckydip` int(11) NOT NULL DEFAULT 1,
  `roulette` int(11) NOT NULL DEFAULT 1,
  `spins` int(11) NOT NULL DEFAULT 20,
  `voted1` int(11) NOT NULL DEFAULT 0,
  `job` int(11) NOT NULL DEFAULT 0,
  `ip` varchar(255) NOT NULL,
  `signupip` varchar(255) NOT NULL,
  `eqweapon` int(11) NOT NULL DEFAULT 0,
  `eqarmor` int(11) NOT NULL DEFAULT 0,
  `eqshoes` int(11) NOT NULL DEFAULT 0,
  `eqoffhand` int(11) NOT NULL DEFAULT 0,
  `eqgloves` int(11) NOT NULL DEFAULT 0,
  `eqsidearm` int(11) NOT NULL DEFAULT 0,
  `weploaned` tinyint(1) NOT NULL DEFAULT 0,
  `armloaned` tinyint(1) NOT NULL DEFAULT 0,
  `shoeloaned` tinyint(1) NOT NULL DEFAULT 0,
  `glovesloaned` tinyint(1) NOT NULL DEFAULT 0,
  `offhandloaned` tinyint(1) NOT NULL DEFAULT 0,
  `potseeds` int(11) NOT NULL DEFAULT 0,
  `marijuana` int(11) NOT NULL DEFAULT 0,
  `activate` varchar(32) DEFAULT NULL,
  `gangleader` int(11) NOT NULL DEFAULT 0,
  `gangmail` int(11) NOT NULL DEFAULT 0,
  `reported` int(11) NOT NULL DEFAULT 0,
  `reporter` int(11) NOT NULL DEFAULT 0,
  `polled1` int(11) NOT NULL DEFAULT 0,
  `threadtime` int(11) NOT NULL DEFAULT 0,
  `posttime` int(11) NOT NULL DEFAULT 0,
  `tag` varchar(100) NOT NULL DEFAULT '0',
  `pet` int(11) NOT NULL DEFAULT 0,
  `gameevents` int(11) NOT NULL DEFAULT 0,
  `gradient?` int(11) NOT NULL DEFAULT 0,
  `colours` varchar(50) DEFAULT NULL,
  `timeschanged` int(11) NOT NULL DEFAULT 0,
  `gcses` int(11) NOT NULL DEFAULT 0,
  `credits` varchar(99) NOT NULL DEFAULT '0',
  `referrals` int(11) NOT NULL DEFAULT 0,
  `refcount` int(11) NOT NULL DEFAULT 0,
  `expcount` int(11) NOT NULL DEFAULT 0,
  `ban/freeze` int(11) NOT NULL DEFAULT 0,
  `csmuggling` int(11) NOT NULL DEFAULT 6,
  `protection` int(11) NOT NULL DEFAULT 7,
  `gangthreadtime` int(11) NOT NULL DEFAULT 0,
  `invincible` int(11) NOT NULL DEFAULT 0,
  `drugused` int(11) NOT NULL DEFAULT 0,
  `drugtime` int(11) NOT NULL DEFAULT 0,
  `music` int(11) NOT NULL DEFAULT 1,
  `volume` int(11) NOT NULL DEFAULT 30,
  `gangcrimes` int(11) NOT NULL DEFAULT 0,
  `chase` int(11) NOT NULL DEFAULT 1,
  `gangpoll` int(11) NOT NULL DEFAULT 0,
  `relationship` int(11) NOT NULL DEFAULT 0,
  `relplayer` int(11) NOT NULL DEFAULT 0,
  `ip1` varchar(255) DEFAULT NULL,
  `ip2` varchar(255) DEFAULT NULL,
  `ip3` varchar(255) DEFAULT NULL,
  `ip4` varchar(255) DEFAULT NULL,
  `ip5` varchar(255) DEFAULT NULL,
  `firstlogin` int(11) NOT NULL DEFAULT 1,
  `doors` int(11) NOT NULL DEFAULT 5,
  `mugsucceeded` int(11) NOT NULL DEFAULT 0,
  `mugfailed` int(11) NOT NULL DEFAULT 0,
  `mugmoney` int(11) NOT NULL DEFAULT 0,
  `collected` int(11) NOT NULL DEFAULT 0,
  `dailytrains` int(11) NOT NULL DEFAULT 0,
  `max_hp` int(11) NOT NULL DEFAULT 0,
  `image_name` mediumtext DEFAULT NULL,
  `starterpack` int(11) NOT NULL DEFAULT 1,
  `img_name` int(11) NOT NULL DEFAULT 0,
  `boxes_opened` int(11) NOT NULL DEFAULT 0,
  `roulette_spin` int(11) NOT NULL DEFAULT 0,
  `pointsmarket` int(11) NOT NULL DEFAULT 1,
  `sendmoney` int(11) NOT NULL DEFAULT 1,
  `senditem` int(11) NOT NULL DEFAULT 1,
  `pmarket` int(11) NOT NULL DEFAULT 0,
  `sendpoints` int(11) NOT NULL DEFAULT 1,
  `sendp` int(11) NOT NULL DEFAULT 0,
  `device_id` int(11) NOT NULL DEFAULT 0,
  `pg` int(11) NOT NULL DEFAULT 0,
  `slots_left` int(11) NOT NULL DEFAULT 100,
  `prayer` int(11) NOT NULL DEFAULT 1,
  `cardvalue` int(11) NOT NULL DEFAULT 5,
  `cardtype` int(11) NOT NULL DEFAULT 0,
  `firstlogin1` int(11) NOT NULL DEFAULT 0,
  `turns` int(11) NOT NULL DEFAULT 10,
  `psmuggling` int(11) NOT NULL DEFAULT 6,
  `muggedmoney` bigint(20) NOT NULL DEFAULT 0,
  `backalleywins` int(11) NOT NULL DEFAULT 0,
  `delay` int(11) NOT NULL DEFAULT 0,
  `apoints` int(11) NOT NULL DEFAULT 0,
  `epoints` int(11) NOT NULL DEFAULT 0,
  `new_updates` int(11) NOT NULL DEFAULT 0,
  `globalchat` int(11) NOT NULL DEFAULT 0,
  `donationmonth` int(11) NOT NULL DEFAULT 0,
  `badges` varchar(60) NOT NULL DEFAULT '0,0,0,0,0,0,0,0,0,0,0,0,0,0',
  `badges_claimed` varchar(60) NOT NULL DEFAULT '0,0,0,0,0,0,0,0,0,0,0,0,0,0',
  `browser` mediumtext DEFAULT NULL,
  `gndays` int(11) NOT NULL DEFAULT 0,
  `gradient` int(11) NOT NULL DEFAULT 0,
  `drugs` varchar(35) NOT NULL DEFAULT '0',
  `pinvestment` int(11) NOT NULL DEFAULT 0,
  `pinvestmentdays` int(11) NOT NULL DEFAULT 0,
  `pbank` int(11) NOT NULL DEFAULT 0,
  `koth` int(11) NOT NULL DEFAULT 0,
  `loth` bigint(20) NOT NULL DEFAULT 0,
  `lastpayment` int(11) NOT NULL DEFAULT 0,
  `petMenu` enum('yes','no') NOT NULL DEFAULT 'no',
  `robInfo` varchar(20) NOT NULL DEFAULT '0|0',
  `blocked` int(11) NOT NULL DEFAULT 0,
  `notes` mediumtext DEFAULT NULL,
  `nerref` tinyint(1) NOT NULL DEFAULT 0,
  `ngyref` tinyint(1) NOT NULL DEFAULT 0,
  `nerreftime` int(11) NOT NULL DEFAULT 0,
  `ngyreftime` int(11) NOT NULL DEFAULT 0,
  `forumnoti` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `jobcis` mediumint(9) NOT NULL DEFAULT 0,
  `jobMoney` bigint(20) NOT NULL DEFAULT 0,
  `apban` tinyint(4) NOT NULL DEFAULT 0,
  `crimes` enum('crime','newcrimes') NOT NULL DEFAULT 'crime',
  `prestige` tinyint(4) NOT NULL DEFAULT 0,
  `prestige_tokens` int(11) NOT NULL DEFAULT 0,
  `prestige_exp` int(11) NOT NULL DEFAULT 0,
  `prestige_gym` int(11) NOT NULL DEFAULT 0,
  `prestige_attack` int(11) NOT NULL DEFAULT 0,
  `prestige_crime` int(11) NOT NULL DEFAULT 0,
  `prestige_500` int(11) NOT NULL DEFAULT 0,
  `prestige_600` int(11) NOT NULL DEFAULT 0,
  `prestige_700` int(11) NOT NULL DEFAULT 0,
  `prestige_800` int(11) NOT NULL DEFAULT 0,
  `prestige_900` int(11) NOT NULL DEFAULT 0,
  `prestige_1000` int(11) NOT NULL DEFAULT 0,
  `prestige_1100` int(11) NOT NULL DEFAULT 0,
  `prestige_1200` int(11) NOT NULL DEFAULT 0,
  `prestige_1300` int(11) NOT NULL DEFAULT 0,
  `prestige_1400` int(11) NOT NULL DEFAULT 0,
  `pdimgname` tinyint(1) NOT NULL DEFAULT 0,
  `cur_gangcrime` smallint(6) NOT NULL DEFAULT 0,
  `hideemojis` tinyint(1) NOT NULL DEFAULT 0,
  `uninfo` varchar(200) NOT NULL DEFAULT '3|ff0000~00ff00~0000ff|100|no|0~ff0000|0',
  `killcomp` int(11) NOT NULL DEFAULT 0,
  `mprotection` int(11) NOT NULL DEFAULT 0,
  `aprotection` int(11) NOT NULL DEFAULT 0,
  `exppill` int(11) NOT NULL DEFAULT 0,
  `bbdonator` int(11) NOT NULL DEFAULT 0,
  `limiteditems1` int(11) NOT NULL DEFAULT 5,
  `menuorder` varchar(255) NOT NULL DEFAULT '0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,29,30',
  `totaltax` int(11) NOT NULL DEFAULT 0,
  `limiteditems2` int(11) NOT NULL DEFAULT 5,
  `limiteditems3` int(11) NOT NULL DEFAULT 5,
  `limiteditems4` int(11) NOT NULL DEFAULT 0,
  `battlemult` int(11) NOT NULL DEFAULT 0,
  `crimemult` int(11) NOT NULL DEFAULT 0,
  `both` int(11) NOT NULL DEFAULT 0,
  `energyboost` int(11) NOT NULL DEFAULT 0,
  `nerveboost` int(11) NOT NULL DEFAULT 0,
  `bankboost` int(11) NOT NULL DEFAULT 0,
  `crimeexpboost` int(11) NOT NULL DEFAULT 0,
  `votetokens` int(11) NOT NULL DEFAULT 0,
  `polled1active` int(11) NOT NULL DEFAULT 1,
  `donate_token` int(11) NOT NULL DEFAULT 1,
  `dprivacy` int(11) DEFAULT 0,
  `verified` int(11) DEFAULT 0,
  `activation_code` varchar(11) DEFAULT NULL,
  `actions` int(11) NOT NULL DEFAULT 0,
  `moth` mediumint(9) NOT NULL DEFAULT 0,
  `motd` mediumint(9) NOT NULL DEFAULT 0,
  `tamt` bigint(20) NOT NULL DEFAULT 0,
  `fbuserid` bigint(20) NOT NULL DEFAULT 0,
  `fbtoken` longtext DEFAULT NULL,
  `fbemail` varchar(250) NOT NULL DEFAULT '0',
  `server_var` longtext DEFAULT NULL,
  `email_verified` int(11) NOT NULL DEFAULT 0,
  `email_verify_code` varchar(255) DEFAULT NULL,
  `staminaboost` int(11) NOT NULL DEFAULT 0,
  `marriagetime` int(11) NOT NULL DEFAULT 0,
  `dailycheck` int(11) NOT NULL DEFAULT 0,
  `dailiesdone` int(11) NOT NULL DEFAULT 0,
  `gangwait` int(11) NOT NULL DEFAULT 0,
  `speed60` int(11) NOT NULL DEFAULT 0,
  `strength60` int(11) NOT NULL DEFAULT 0,
  `defence60` int(11) NOT NULL DEFAULT 0,
  `agility60` int(11) NOT NULL DEFAULT 0,
  `king` int(11) NOT NULL DEFAULT 0,
  `queen` int(11) NOT NULL DEFAULT 0,
  `claimed` int(11) NOT NULL DEFAULT 0,
  `hidden` int(11) NOT NULL DEFAULT 0,
  `dailyrespect` int(11) NOT NULL DEFAULT 0,
  `hideevents` int(11) NOT NULL DEFAULT 0,
  `crimeauto` int(11) NOT NULL DEFAULT 0,
  `dailymugs` int(11) NOT NULL DEFAULT 0,
  `sortablemenu` int(11) NOT NULL DEFAULT 0,
  `relationshipdays` int(11) NOT NULL DEFAULT 0,
  `shared_bank` bigint(20) NOT NULL DEFAULT 0,
  `sharedcontribution` bigint(20) NOT NULL DEFAULT 0,
  `actionpoints` int(11) NOT NULL DEFAULT 25,
  `profilewall` int(11) NOT NULL DEFAULT 1,
  `refcomp` int(11) NOT NULL DEFAULT 0,
  `dailyClockins` int(11) NOT NULL DEFAULT 0,
  `dailytime` int(11) NOT NULL DEFAULT 0,
  `totaltime` int(11) NOT NULL DEFAULT 0,
  `bustcomp` int(11) NOT NULL DEFAULT 0,
  `bustpill` int(11) NOT NULL DEFAULT 0,
  `halloween` int(11) NOT NULL DEFAULT 0,
  `eventmugs` int(11) NOT NULL DEFAULT 1,
  `eventkills` int(11) NOT NULL DEFAULT 1,
  `eventbusts` int(11) NOT NULL DEFAULT 1,
  `luckydip2` int(11) NOT NULL DEFAULT 1,
  `psmuggling2` int(11) NOT NULL DEFAULT 5,
  `outofjail` int(11) NOT NULL DEFAULT 0,
  `christmasraffle` int(11) NOT NULL DEFAULT 0,
  `fbi` int(11) NOT NULL DEFAULT 0,
  `fbitime` int(11) NOT NULL DEFAULT 0,
  `pack1` int(11) NOT NULL DEFAULT 0,
  `pack1time` int(11) NOT NULL DEFAULT 0,
  `pack1timetill` int(11) NOT NULL DEFAULT 0,
  `work_stat_1` int(11) NOT NULL DEFAULT 0,
  `work_stat_2` int(11) NOT NULL DEFAULT 0,
  `work_stat_3` int(11) NOT NULL DEFAULT 0,
  `current_employer` int(11) DEFAULT NULL,
  `wage` int(11) NOT NULL DEFAULT 0,
  `business_role_id` int(11) DEFAULT NULL,
  `intelligence` int(11) DEFAULT 0,
  `raidtokens` int(11) NOT NULL DEFAULT 0,
  `raidwins` int(11) DEFAULT 0,
  `raidlosses` int(11) DEFAULT 0,
  `raidsjoined` int(11) DEFAULT 0,
  `raidshosted` int(11) DEFAULT 0,
  `raidpoints` int(11) NOT NULL DEFAULT 0,
  `cityturns` int(11) NOT NULL DEFAULT 0,
  `raidstrength` int(11) NOT NULL DEFAULT 1,
  `raiddefense` int(11) NOT NULL DEFAULT 1,
  `raidagility` int(11) NOT NULL DEFAULT 1,
  `raidluck` int(11) NOT NULL DEFAULT 1,
  `raidstrength_tier` int(11) DEFAULT 1,
  `raiddefense_tier` int(11) DEFAULT 1,
  `raidagility_tier` int(11) DEFAULT 1,
  `raidluck_tier` int(11) DEFAULT 1,
  `nightvision` int(11) NOT NULL DEFAULT 0,
  `diamonds` int(11) NOT NULL DEFAULT 0,
  `emerald` int(11) NOT NULL DEFAULT 0,
  `sapphire` int(11) NOT NULL DEFAULT 0,
  `opal` int(11) NOT NULL DEFAULT 0,
  `amethyst` int(11) NOT NULL DEFAULT 0,
  `aquamarine` int(11) NOT NULL DEFAULT 0,
  `crafter_cooldown` datetime DEFAULT NULL,
  `crew` int(11) NOT NULL DEFAULT 0,
  `crank` int(11) NOT NULL DEFAULT 0,
  `raidcomp` int(11) NOT NULL DEFAULT 0,
  `killcomp1` int(11) NOT NULL DEFAULT 0,
  `rtsmuggling` int(11) NOT NULL DEFAULT 7,
  `is_jail_bot` tinyint(1) NOT NULL DEFAULT 0,
  `jail_bot_credits` int(11) NOT NULL DEFAULT 50,
  `is_jail_bots_active` tinyint(1) NOT NULL DEFAULT 0,
  `is_mobile_disabled` tinyint(1) NOT NULL DEFAULT 0,
  `ffban` int(11) NOT NULL DEFAULT 0,
  `relationshipended` int(11) NOT NULL DEFAULT 0,
  `macro_token` varchar(100) DEFAULT NULL,
  `box_hunt_count` int(11) NOT NULL DEFAULT 0,
  `qtime` int(11) NOT NULL DEFAULT 0,
  `ktime` int(11) NOT NULL DEFAULT 0,
  `last_attack_time` bigint(20) DEFAULT NULL,
  `last_mug_time` bigint(20) DEFAULT NULL,
  `is_ads_disabled` tinyint(1) NOT NULL DEFAULT 0,
  `forgot_password` varchar(255) NOT NULL DEFAULT '0',
  `gtachance` varchar(100) NOT NULL DEFAULT '5-5-5-5-5-5',
  `lastgta` varchar(100) NOT NULL DEFAULT '',
  `is_auto_user` tinyint(1) NOT NULL DEFAULT 0,
  `is_chat_disabled` int(11) NOT NULL DEFAULT 0,
  `is_quest_user` int(11) NOT NULL DEFAULT 0,
  `mission_count` int(11) DEFAULT 0,
  `gtzb_count` int(11) NOT NULL DEFAULT 0,
  `skill_ids` varchar(255) DEFAULT NULL,
  `specialization_level` int(11) DEFAULT 0,
  `skill_points` int(11) DEFAULT 0,
  `specialization_exp` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `hangar`
--

CREATE TABLE `hangar` (
  `userid` int(11) NOT NULL,
  `planeid` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `hearts_log`
--

CREATE TABLE `hearts_log` (
  `user_id` int(11) NOT NULL,
  `who` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `reward` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `heists`
--

CREATE TABLE `heists` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `cost` int(11) NOT NULL,
  `reward` int(11) NOT NULL,
  `type` enum('terrorism','execute','assassinate') NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `hitlist`
--

CREATE TABLE `hitlist` (
  `id` int(11) NOT NULL,
  `target` int(11) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `bounty` int(11) NOT NULL,
  `from` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `hourly_rewards`
--

CREATE TABLE `hourly_rewards` (
  `id` int(11) NOT NULL,
  `last_payout` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `houses`
--

CREATE TABLE `houses` (
  `id` int(11) NOT NULL,
  `name` varchar(75) NOT NULL DEFAULT '',
  `awake` int(11) NOT NULL DEFAULT 0,
  `cost` bigint(20) NOT NULL DEFAULT 0,
  `buyable` int(11) NOT NULL DEFAULT 1,
  `maxdealers` int(11) NOT NULL,
  `image` varchar(10000) NOT NULL,
  `houselevel` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Data dump for tabellen `houses`
--

INSERT INTO `houses` (`id`, `name`, `awake`, `cost`, `buyable`, `maxdealers`, `image`, `houselevel`) VALUES
(1, 'Small House', 250, 150000, 1, 100, 'mlordsimages/bowieknife.png', 0),
(36, 'Small Villa', 500, 2500000, 1, 150, 'mlordsimages/villa1.png', 0),
(37, 'Large Villa', 750, 25000000, 1, 200, 'mlordsimages/largevilla1.png', 0),
(38, 'Small Mansion', 1000, 250000000, 1, 300, 'mlordsimages/smallmansion.png', 0),
(39, 'Large Mansion', 1500, 850000000, 1, 400, 'mlordsimages/largemansion.png', 0),
(40, 'Estate Manor', 2250, 2000000000, 1, 400, 'mlordsimages/estatemanor.png', 0),
(41, 'Royal Palace', 3000, 5500000000, 1, 400, 'mlordsimages/royalpalace.png', 0),
(42, 'Prestige 1 House', 3800, 9000000000, 1, 400, 'mlordsimages/royalpalace.png', 1),
(43, 'Prestige 2 House', 5000, 15000000000, 1, 400, 'mlordsimages/royalpalace.png', 2),
(44, 'Prestige 3 House', 6500, 35000000000, 1, 400, 'mlordsimages/royalpalace.png', 3),
(45, 'Prestige 4 House', 8000, 70000000000, 1, 400, 'mlordsimages/royalpalace.png', 4),
(46, 'Prestige 5 House', 10000, 125000000000, 1, 400, 'mlordsimages/royalpalace.png', 5),
(47, 'Prestige 6 House', 12000, 325000000000, 1, 400, 'images/prestige6house.png', 6),
(48, 'Prestige 7 House', 14000, 500000000000, 1, 400, 'images/prestige7house.png', 7),
(49, 'Prestige 8 House', 16000, 750000000000, 1, 400, 'images/prestige8house.png', 8);

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `ignorelist`
--

CREATE TABLE `ignorelist` (
  `id` int(11) NOT NULL,
  `blocker` int(11) NOT NULL,
  `blocked` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Data dump for tabellen `ignorelist`
--

INSERT INTO `ignorelist` (`id`, `blocker`, `blocked`) VALUES
(8, 1214, 134),
(9, 445, 181),
(10, 85, 175),
(17, 424, 437),
(25, 424, 480),
(26, 1214, 1065),
(27, 85, 242),
(28, 85, 18);

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `inventory`
--

CREATE TABLE `inventory` (
  `userid` int(11) NOT NULL,
  `itemid` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `ipbans`
--

CREATE TABLE `ipbans` (
  `id` int(11) NOT NULL,
  `ip` varchar(60) NOT NULL,
  `end` int(11) DEFAULT NULL,
  `userid` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `ipn`
--

CREATE TABLE `ipn` (
  `id` int(11) NOT NULL,
  `itemname` varchar(200) NOT NULL,
  `date` int(11) NOT NULL,
  `itemnumber` int(11) NOT NULL,
  `creditsbought` int(11) NOT NULL,
  `paymentstatus` varchar(200) NOT NULL,
  `paymentamount` int(11) NOT NULL,
  `currency` varchar(200) NOT NULL,
  `txnid` varchar(200) NOT NULL,
  `receiveremail` varchar(200) NOT NULL,
  `payeremail` varchar(200) NOT NULL,
  `first` varchar(200) NOT NULL,
  `last` varchar(200) NOT NULL,
  `quantity` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `itemmarket`
--

CREATE TABLE `itemmarket` (
  `id` int(11) NOT NULL,
  `itemid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `cost` int(11) NOT NULL,
  `currency` enum('money','points') DEFAULT NULL,
  `qty` mediumint(9) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `items`
--

CREATE TABLE `items` (
  `id` int(11) NOT NULL,
  `itemname` varchar(75) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `category` varchar(255) NOT NULL,
  `cost` bigint(20) NOT NULL DEFAULT 0,
  `image` varchar(100) NOT NULL DEFAULT '',
  `offense` int(11) NOT NULL DEFAULT 0,
  `defense` int(11) NOT NULL DEFAULT 0,
  `speed` int(11) NOT NULL DEFAULT 0,
  `agility` int(11) NOT NULL DEFAULT 0,
  `heal` int(11) NOT NULL DEFAULT 0,
  `buyable` int(11) NOT NULL DEFAULT 0,
  `level` int(11) NOT NULL,
  `type` varchar(100) NOT NULL,
  `donator` int(11) NOT NULL DEFAULT 0,
  `petupgrades` int(11) NOT NULL DEFAULT 0,
  `reduce` int(11) NOT NULL DEFAULT 0,
  `drug` varchar(500) NOT NULL DEFAULT '0',
  `drugdes` int(11) NOT NULL DEFAULT 0,
  `drugspe` int(11) NOT NULL DEFAULT 0,
  `drugstime` int(11) NOT NULL,
  `rare` int(11) NOT NULL DEFAULT 0,
  `city` int(11) NOT NULL,
  `danda` int(11) NOT NULL,
  `buyable1` int(11) NOT NULL DEFAULT 0,
  `bonuses` varchar(255) NOT NULL,
  `awake_boost` int(11) NOT NULL DEFAULT 0,
  `searchable` tinyint(4) NOT NULL DEFAULT 0,
  `consumable` tinyint(1) NOT NULL DEFAULT 0,
  `shareable` tinyint(4) NOT NULL DEFAULT 1,
  `max` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Data dump for tabellen `items`
--

INSERT INTO `items` (`id`, `itemname`, `description`, `category`, `cost`, `image`, `offense`, `defense`, `speed`, `agility`, `heal`, `buyable`, `level`, `type`, `donator`, `petupgrades`, `reduce`, `drug`, `drugdes`, `drugspe`, `drugstime`, `rare`, `city`, `danda`, `buyable1`, `bonuses`, `awake_boost`, `searchable`, `consumable`, `shareable`, `max`) VALUES
(1, 'Bowie Knife', 'Bowie knife, used for slicing your opponant.', 'weapon', 15000, 'css/images/NewGameImages/bowie_knife.png', 5, 0, 0, 0, 0, 1, 0, '', 0, 0, 0, '0', 0, 0, 0, 0, 1, 0, 0, '', 0, 0, 0, 1, NULL),
(2, 'Body Armour', 'Starting off on the streets, you don\'t have much to offer...', 'armor', 15000, 'css/images/NewGameImages/body_armor.png', 0, 5, 0, 0, 0, 1, 0, '', 0, 0, 0, '0', 0, 0, 0, 0, 1, 0, 0, '', 0, 0, 0, 1, NULL),
(3, 'Army Boots', 'Boots of Tiny Destruction.', 'shoes', 15000, 'css/images/NewGameImages/army_boots.png', 0, 0, 5, 0, 0, 1, 0, '', 0, 0, 0, '0', 0, 0, 0, 0, 1, 0, 0, '', 0, 0, 0, 1, NULL),
(4, 'Awake Pill', 'This will refill your awake to 100% Use it wisely! ', 'consumable', 0, 'css/images/NewGameImages/awake_pill.png', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 1, 1, NULL),
(8, 'Mug Protection [1Hour]', 'You will be protected from mugs for 60 minutes after use.', 'consumable', 0, 'css/images/NewGameImages/mugprotection.png', 0, 0, 0, 0, 0, 0, 0, 'protection', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 1, 0, NULL),
(9, 'Attack Protection [1Hour]', 'You will be protected from Attacks for 60 minutes after use.\n\n(However this wont protect you from hitlisted attacked)', 'consumable', 0, 'css/images/NewGameImages/attackprotection.png', 0, 0, 0, 0, 0, 0, 0, 'protection', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 1, 0, NULL),
(10, 'Double Exp [1Hour]', 'You will receive double exp on crimes for 60 minutes.', '', 0, 'css/images/NewGameImages/doubleexp.png', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 1, 0, NULL),
(11, 'Medi Cert 25%', 'Will reduce hospital time by 25%', 'consumable', 10000, 'css/images/NewGameImages/medi_cert.png', 0, 0, 0, 0, 100, 1, 0, '', 0, 0, 25, '0', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 1, NULL),
(12, 'Medi Cert 50%', 'Will reduce hospital time by 50%', 'consumable', 19500, 'css/images/NewGameImages/medi_cert.png', 0, 0, 0, 0, 100, 1, 0, '', 0, 0, 50, '0', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 1, NULL),
(13, 'Medi Cert 75%', 'Will reduce hospital time by 75%', '', 27000, 'css/images/NewGameImages/medi_cert.png', 0, 0, 0, 0, 100, 1, 0, '', 0, 0, 75, '0', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 1, NULL),
(14, 'Medi Cert 100%', 'Will reduce hospital time by 100%', '', 35000, 'css/images/NewGameImages/medi_cert.png', 0, 0, 0, 0, 100, 1, 0, '', 0, 0, 100, '0', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 1, 1, NULL),
(27, 'Meth', 'Will increase your speed by 25% for 15 minutes.', '', 250000, 'images/ms/pharmacy/Meth.png', 0, 0, 0, 0, 1, 1, 0, '', 0, 0, 0, '0', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 1, 1, NULL),
(28, 'Adrenalin', 'Will increase your defense by 25% for 15 minutes.', '', 250000, 'css/images/NewGameImages/adrenaline.png', 0, 0, 0, 0, 1, 1, 0, '', 0, 0, 0, '0', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 1, 1, NULL),
(29, 'PCP', 'Will increase your strength by 25% for 15 minutes.', '', 250000, 'images/ms/pharmacy/PCP.png', 0, 0, 0, 0, 1, 1, 0, '', 0, 0, 0, '0', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 1, 1, NULL),
(38, 'Golden Ticket', 'Lets you travel to any city you want to go to.', '', 0, 'css/images/NewGameImages/goldenticket.png', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 1, 1, NULL),
(42, 'Mystery Box', 'Inside you may find many riches!', '', 0, 'css/images/NewGameImages/mysterybox.png', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 1, 1, NULL),
(43, 'Invisible Cloak', 'Your city will be in disguise.', 'armor', 0, 'images/newimage/inviscloak.png', 0, 25, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 1, NULL),
(44, 'Custom Item Set', 'A 3 piece set of gear.\r\n\r\nYou choose the name & the image. It will always have the highest % modification. And will be altered as more items become available in game.', '', 0, 'images/newimage/customset.png', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 1, NULL),
(51, '30 Day RM Pack', 'Contains \n\n- 30 Respected Mobster Days.\n- $150,000 Cash.\n- 1000 Points', '', 0, 'css/images/NewGameImages/30dayrm.png', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 1, 0, NULL),
(68, 'Nerve Booster + 25', 'The Nerve Booster will boost your nerve by 25.', '', 0, 'css/images/NewGameImages/nerve251.png', 1, 0, 0, 0, 0, 0, 0, 'booster', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, 'maxnerve:25', 0, 0, 0, 1, NULL),
(69, 'Energy Booster + 25', 'The Energy Booster will boost your Energy by 25.', '', 0, 'css/images/NewGameImages/enery25.png', 0, 1, 0, 0, 0, 0, 0, 'booster', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, 'maxenergy:25', 0, 0, 0, 1, NULL),
(103, '60 Day RM Pack', 'Contains \n\n- 60 Respected Mobster Days.\n- $300,000 Cash.\n- 1500 Points', '', 0, 'css/images/NewGameImages/60dayrm.png', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 1, 0, NULL),
(104, '90 Day Rm Pack', 'Contains \n\n- 90 Respected Mobster Days.\n- $450,000 Cash.\n- 2500 Points', '', 0, 'css/images/NewGameImages/90dayrm.png', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 1, 0, NULL),
(105, 'Custom Shoes', 'Custom Items', 'shoes', 0, '', 0, 0, 80, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 1, NULL),
(106, 'Custom Weapon', 'Custom Item', 'weapon', 0, '', 80, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 1, NULL),
(107, 'Custom Armour', 'Custom Items', 'armor', 0, '', 0, 80, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 1, NULL),
(154, 'City Bomb', '', '', 0, '', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 1, NULL),
(155, 'Heart', 'Send one of these to your friends for you both to receive some Freebies!', '', 0, 'css/images/NewGameImages/heart.png', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 0, 1, NULL),
(163, 'Police Badge [1 Hour]', 'Successfully avoid jail for 1 hour when you are mugging & breaking people out of jail', '', 0, 'css/images/NewGameImages/badge.png', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 1, 0, NULL),
(168, 'FBI Informant', 'Using this item on a user will cause the user to go to jail for 30 Minutes and will be unable to buyout of jail. \r\n\r\nA user can use the Escape FBI item to get out quicker.', '', 0, 'css/images/NewGameImages/fbiinformant.jpg', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 1, 1, NULL),
(169, 'Escape FBI', 'You can use this item to escape FBI Custody should you find yourself stuck! ', '', 0, 'css/images/NewGameImages/fbiinformant.jpg', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 1, 1, NULL),
(194, 'Raid Speedup', 'Immediately end the raid for you and all your raid participants. \r\n\r\nAny user is able to use the Raid Speedup to immediately finish your raid.', '', 0, '/images/raidspeedup.png', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 0, 1, NULL),
(196, 'Night Vision Goggles', 'These will allow you to see a users true location for 15 minutes. \n\nThey will also half the cost of travelling by 50%', '', 0, '/images/nightvision.png', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 1, 1, NULL),
(197, 'City Nuke', 'Blow up a city of your choice killing all civilians within the city. ', '', 0, '', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 1, 1, NULL),
(198, 'Snowball', 'Christmas 2023 Snowball. Throw these at your friends!', '', 0, '/images/snowball.png', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 1, NULL),
(209, 'Diamond (Common)', 'This Diamond can be used in the crafter', 'crafting', 0, 'diamondstone.png', 0, 0, 0, 0, 0, 0, 1, 'Gems', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 1, 0, 1, NULL),
(210, 'Ruby (Common)', 'This Ruby can be used in the crafter\r\n', 'crafting', 0, 'ruby.jpg', 0, 0, 0, 0, 0, 0, 1, 'Gems', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 1, 0, 1, NULL),
(211, 'Emerald (Common)', 'This Emerald can be used in the crafter', 'crafting', 0, 'emeraldstone.png', 0, 0, 0, 0, 0, 0, 1, 'Gems', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 1, 0, 1, NULL),
(212, 'Sapphire (Common)', 'This Saffire can be used in the crafter', 'crafting', 0, 'sapphire.png', 0, 0, 0, 0, 0, 0, 1, 'Gems', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 1, 0, 1, NULL),
(213, 'Iron Sword', 'Iron Sword, used for chopping your opponent into pieces.', 'weapon', 250000, 'images/ironsword1.png', 10, 0, 0, 0, 0, 1, 0, '', 0, 0, 0, '0', 0, 0, 0, 0, 39, 0, 0, '', 0, 0, 0, 1, NULL),
(214, 'Cape', 'Cape, Used to defend yourself!', 'armor', 250000, 'images/cape.png', 0, 10, 0, 0, 0, 1, 50, '', 0, 0, 0, '0', 0, 0, 0, 0, 39, 0, 0, '', 0, 0, 0, 1, NULL),
(215, 'Viking Boots', 'Viking Boots, Made for stomping!', 'shoes', 250000, 'images/vikingboots.png', 0, 0, 10, 0, 0, 1, 50, '', 0, 0, 0, '0', 0, 0, 0, 0, 39, 0, 0, '', 0, 0, 0, 1, NULL),
(216, 'Axe', 'Chop Chop', 'weapon', 2500000, 'images/axe.png', 13, 0, 0, 0, 0, 1, 250, '', 0, 0, 0, '0', 0, 0, 0, 0, 40, 0, 0, '', 0, 0, 0, 1, NULL),
(217, 'Eclipse Armour', 'pretty sold armour\r\n', 'armor', 2500000, 'images/eclipsearmour.png', 0, 13, 0, 0, 0, 1, 0, '', 0, 0, 0, '0', 0, 0, 0, 0, 40, 0, 0, '', 0, 0, 0, 1, NULL),
(218, 'Arcane Runners', 'quick style runners', 'shoes', 2500000, 'images/arcaneboots.png', 0, 0, 13, 0, 0, 1, 0, '', 0, 0, 0, '0', 0, 0, 0, 0, 40, 0, 0, '', 0, 0, 0, 1, NULL),
(219, 'Mystic Warhammer', 'strike down with the warhammer', 'weapon', 25000000, 'images/mysticwarhammer.png', 17, 0, 0, 0, 0, 1, 1, '', 0, 0, 0, '0', 0, 0, 0, 0, 41, 0, 0, '', 0, 0, 0, 1, NULL),
(220, 'Mystic Robes', 'Protect yourself with this magical robe', 'armor', 25000000, 'images/mysticrobe.png', 0, 17, 0, 0, 0, 1, 1, '', 0, 0, 0, '0', 0, 0, 0, 0, 41, 0, 0, '', 0, 0, 0, 1, NULL),
(221, 'Mystic Moccasins', 'nice style mocassions', 'shoes', 25000000, 'images/mysticmocassions.png', 0, 0, 17, 0, 0, 1, 1, '', 0, 0, 0, '0', 0, 0, 0, 0, 41, 0, 0, '', 0, 0, 0, 1, NULL),
(222, 'FlameThrower', 'Very powerful weapon. burn your opponant', 'weapon', 250000000, 'images/flamethrower.png', 22, 0, 0, 0, 0, 1, 1, '', 0, 0, 0, '0', 0, 0, 0, 0, 42, 0, 0, '', 0, 0, 0, 1, NULL),
(223, 'Flamestride Shoes', 'Make flame when moving, these shoes are awesome', 'shoes', 250000000, 'images/flamestrideshoes.png', 0, 0, 22, 0, 0, 1, 1, '', 0, 0, 0, '0', 0, 0, 0, 0, 42, 0, 0, '', 0, 0, 0, 1, NULL),
(224, 'Obsidian Scale Armour', 'Scaled armour with a twist', 'armor', 250000000, 'images/scalearmour.png', 0, 22, 0, 0, 0, 1, 0, '', 0, 0, 0, '0', 0, 0, 0, 0, 42, 0, 0, '', 0, 0, 0, 1, NULL),
(225, 'Diamond (Uncommon)', 'This Diamond can be used in the crafter', 'crafting', 0, 'diamondstone22.png', 0, 0, 0, 0, 0, 0, 1, 'Gems', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 1, 0, 1, NULL),
(226, 'Ruby (Uncommon)', 'This Ruby can be used in the crafter', 'crafting', 0, 'ruby2.png', 0, 0, 0, 0, 0, 0, 1, 'Gems', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 1, 0, 1, NULL),
(227, 'Emerald (Uncommon)', 'This Emerald can be used in the crafter', 'crafting', 0, 'emeraldstone2.png', 0, 0, 0, 0, 0, 0, 1, 'Gems', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 1, 0, 1, NULL),
(228, 'Sapphire (Uncommon)', 'This Sapphire can be used in the crafter', 'crafting', 0, 'sapphirestone2.png', 0, 0, 0, 0, 0, 0, 1, 'Gems', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 1, 0, 1, NULL),
(229, 'Super Booster', 'The Super Booster will give you +50 Energy & Nerve Boost!', '', 0, 'css/images/NewGameImages/booster1.png', 1, 0, 0, 0, 0, 0, 0, 'booster', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, 'maxnerve:50|maxenergy:50', 0, 0, 0, 1, NULL),
(230, 'Exotic Booster', 'The Exotic Booster will give you +100 Energy & Nerve Boost!', '', 0, 'css/images/NewGameImages/exoticbooster.png', 1, 0, 0, 0, 0, 0, 0, 'booster', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, 'maxnerve:100|maxenergy:100', 0, 0, 0, 1, NULL),
(231, 'Heroic Booster', 'The Heroic Booster will give you +150 Energy + Nerve Boost!', '', 0, 'css/images/NewGameImages/heroicbooster.png', 1, 0, 0, 0, 0, 0, 0, 'booster', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, 'maxnerve:150|maxenergy:150', 0, 0, 0, 1, NULL),
(232, 'Venomous Dagger', 'The Venomous Dagger is a sleek and deadly weapon, its blade forged from the darkest depths of the underworld and coated with a potent poison harvested from the most venomous creatures known to man. The blade itself seems to glisten with a malevolent sheen, hinting at the lethal power it holds within.', 'weapon', 500000000, 'images/venomousdagger.png', 30, 0, 0, 0, 0, 1, 500, '', 0, 0, 0, '0', 0, 0, 0, 0, 44, 0, 0, '', 0, 0, 0, 1, NULL),
(233, 'Venomous Armor', 'Forged in the depths of the underworld alongside the Venomous Dagger, the Venomous Armor is a sinister ensemble designed to provide both protection and intimidation on the battlefield. Crafted from hardened blackened steel and adorned with venomous motifs, this armor radiates an aura of malevolence that strikes fear into the hearts of your enemies.', 'armor', 500000000, 'images/venemousarmour.png', 0, 30, 0, 0, 0, 1, 0, '', 0, 0, 0, '0', 0, 0, 0, 0, 44, 0, 0, '', 0, 0, 0, 1, NULL),
(234, 'Serpentstride Boots', 'Completing the ensemble, the Serpentstride Boots are a pair of sleek, blackened leather boots adorned with sinuous patterns reminiscent of coiling serpents. Crafted from the supple hides of venomous creatures and reinforced with enchanted metal plates, these boots provide both comfort and protection for the wearer.', 'shoes', 500000000, 'images/serpentstrideboots.png', 0, 0, 30, 0, 0, 1, 1, '', 0, 0, 0, '0', 0, 0, 0, 0, 44, 0, 0, '', 0, 0, 0, 1, NULL),
(235, 'Serenity Serum', 'Will increase your strength, Defense and Speed in battle by 50% for 15 minutes.', '', 25000000, 'images/serenityserum.png', 0, 0, 0, 0, 1, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 1, 1, NULL),
(236, 'Starfall Blade', 'A celestial weapon, harnessing cosmic power for devastating strikes.', 'weapon', 750000000, 'images/starfallblade.png', 40, 0, 0, 0, 0, 1, 700, '', 0, 0, 0, '0', 0, 0, 0, 0, 45, 0, 0, '', 0, 0, 0, 1, NULL),
(237, 'Galaxystride Boots', 'Propel through space with unmatched agility and speed.', 'shoes', 750000000, 'images/galaxystrideboots.png', 0, 0, 40, 0, 0, 1, 700, '', 0, 0, 0, '0', 0, 0, 0, 0, 45, 0, 0, '', 0, 0, 0, 1, NULL),
(238, 'Nebula Plate', 'Infused with cosmic resilience, adapting defenses dynamically.', 'armor', 750000000, 'images/nebulaplate.png', 0, 40, 0, 0, 0, 1, 700, '', 0, 0, 0, '0', 0, 0, 0, 0, 45, 0, 0, '', 0, 0, 0, 1, NULL),
(239, 'Eclipse Edge', 'A blade that thrives in shadow, granting its wielder stealth and precision in darkness.', 'weapon', 1250000000, 'images/eclipseedge.png', 55, 0, 0, 0, 0, 1, 1000, '', 0, 0, 0, '0', 0, 0, 0, 0, 46, 0, 0, '', 0, 0, 0, 1, NULL),
(240, 'Stardust Vestment', 'Woven from cosmic dust, this armor offers unmatched protection and a connection to the stars.', 'armor', 1250000000, 'images/stardustvestment.png', 0, 55, 0, 0, 0, 1, 1000, '', 0, 0, 0, '0', 0, 0, 0, 0, 46, 0, 0, '', 0, 0, 0, 1, NULL),
(241, 'Cometwalk Cleats', 'Infused with celestial energy, these boots leave trails of light, enhancing speed and agility.', 'shoes', 1250000000, 'images/cometwalkcleats.png', 0, 0, 55, 0, 0, 1, 1000, '', 0, 0, 0, '0', 0, 0, 0, 0, 46, 0, 0, '', 0, 0, 0, 1, NULL),
(242, 'Diamond (Rare)', 'This Rare Diamond can be used in the crafter', 'crafting', 0, '/rarediamond.png', 0, 0, 0, 0, 0, 0, 1, 'Gems', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 0, 1, NULL),
(243, 'Ruby (Rare)', 'This Rare Ruby can be used in the crafter', 'crafting', 0, 'rareruby.png', 0, 0, 0, 0, 0, 0, 1, 'Gems', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 0, 1, NULL),
(244, 'Emerald (Rare)', 'This Rare Emerald can be used in the crafter', 'crafting', 0, 'rareemerald.png', 0, 0, 0, 0, 0, 0, 1, 'Gems', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 0, 1, NULL),
(245, 'Sapphire (Rare)', 'This Rare Sapphire can be used in the crafter', 'crafting', 0, 'raresapphire.png', 0, 0, 0, 0, 0, 0, 1, 'Gems', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 0, 1, NULL),
(246, 'Diamond (Ultra Rare)', 'This Ultra Rare Diamond can be used in the crafter', 'crafting', 0, 'ultrararediamond1.png', 0, 0, 0, 0, 0, 0, 1, 'Gems', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 0, 1, NULL),
(247, 'Ruby (Ultra Rare)', 'This Ultra Rare Ruby can be used in the crafter', 'crafting', 0, 'ultrarareruby.png', 0, 0, 0, 0, 0, 0, 1, 'Gems', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 0, 1, NULL),
(248, 'Emerald (Ultra Rare)', 'This Ultra Rare Emerald can be used in the crafter', 'crafting', 0, 'ultrarareemerald.png', 0, 0, 0, 0, 0, 0, 1, 'Gems', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 0, 1, NULL),
(249, 'Sapphire (Ultra Rare)', 'This Ultra Rare Sapphire can be used in the crafter', 'crafting', 0, 'ultrararesapphire.png', 0, 0, 0, 0, 0, 0, 1, 'Gems', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 0, 1, NULL),
(250, 'Advanced Booster', 'The Advanced Booster will give you +200 Energy & Nerve Boost!', '', 0, 'css/images/NewGameImages/advancedbooster.png', 1, 0, 0, 0, 0, 0, 0, 'booster', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, 'maxnerve:200|maxenergy:200', 0, 0, 0, 1, NULL),
(251, 'Raid Pass', 'Use your Raid Pass to guarantee success in the next Raid you host', '', 0, 'css/images/NewGameImages/raid-pass.png', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 1, 1, NULL),
(252, 'Raid Booster', 'Use your Raid Booster to boost your raid earnings and increase your chances of raid drops for the next raid you host!', '', 0, 'css/images/NewGameImages/raid-booster.png', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 1, 1, NULL),
(253, 'Gold Rush Token', 'Use your Gold Rush Token to trigger gold rush in the BA!', '', 0, 'css/images/NewGameImages/gold-rush-token.png', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 1, 0, NULL),
(254, 'Crime Potion', 'Use your Crime Potion and gain a 10% Crime EXP boost for an hour!', '', 0, 'css/images/NewGameImages/crime-potion.png', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 1, 1, NULL),
(255, 'Crime Booster', 'Use your Crime Booster and gain a 20% Crime EXP boost for an hour!', '', 0, 'css/images/NewGameImages/crime-booster.png', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 1, 0, NULL),
(256, 'Nerve Vial', 'Use your Nerve Vial and double your nerve for 30 minutes with refills only costing 50% of the additional nerve!', '', 0, 'css/images/NewGameImages/nerve-vial.png', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 1, 1, NULL),
(257, 'Gang Double EXP Pill', 'Use your Gang Double EXP Pill and everyone in your gang will receive double crime EXP for 4-hours. STACKS WITH ALL OTHER EXP BOOSTS EXCEPT FROM SERVER WIDE DOUBLE EXP.', '', 0, 'css/images/NewGameImages/gang-dep.png', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 1, 0, NULL),
(258, 'Leveling Crate', 'Open to find wonders inside that will be sure to help you boost those levels!', '', 0, 'css/images/NewGameImages/leveling-crate.png', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 1, 0, NULL),
(259, 'Chaos Infinity Stone (Ultra Rare)', 'This Ultra Rare Chaos Infinity Stone can be used in the crafter', 'crafting', 0, 'css/images/NewGameImages/chaos-infinity-stone.png', 0, 0, 0, 0, 0, 0, 1, 'Gems', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 0, 1, NULL),
(260, 'Genesis Infinity Stone (Ultra Rare)', 'This Ultra Rare Genesis Infinity Stone can be used in the crafter', 'crafting', 0, 'css/images/NewGameImages/genesis-infinity-stone.png', 0, 0, 0, 0, 0, 0, 1, 'Gems', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 0, 1, NULL),
(261, 'Nuclear Infinity Stone (Ultra Rare)', 'This Ultra Rare Nuclear Infinity Stone can be used in the crafter', 'crafting', 0, 'css/images/NewGameImages/nuclear-infinity-stone.png', 0, 0, 0, 0, 0, 0, 1, 'Gems', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 0, 1, NULL),
(262, 'Space Infinity Stone (Ultra Rare)', 'This Ultra Rare Space Infinity Stone can be used in the crafter', 'crafting', 0, 'css/images/NewGameImages/space-infinity-stone.png', 0, 0, 0, 0, 0, 0, 1, 'Gems', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 0, 1, NULL),
(263, 'Time Infinity Stone (Ultra Rare)', 'This Ultra Rare Time Infinity Stone can be used in the crafter', 'crafting', 0, 'css/images/NewGameImages/time-infinity-stone.png', 0, 0, 0, 0, 0, 0, 1, 'Gems', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 0, 1, NULL),
(264, 'Galactic Booster', 'The Galactic Booster will give you +275 Energy & Nerve Boost!', '', 0, 'css/images/NewGameImages/galactic-booster.png', 1, 0, 0, 0, 0, 0, 0, 'booster', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, 'maxnerve:275|maxenergy:275', 0, 0, 0, 1, NULL),
(265, 'Voidglass (Rare)', 'This Rare Voidglass can be used in the crafter', 'crafting', 0, 'css/images/NewGameImages/voidglass.png', 0, 0, 0, 0, 0, 0, 1, 'Gems', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 0, 1, NULL),
(266, 'Hourglass Gem (Rare)', 'This Rare Hourglass Gem can be used in the crafter', 'crafting', 0, 'css/images/NewGameImages/hourglass-gem.png', 0, 0, 0, 0, 0, 0, 1, 'Gems', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 0, 1, NULL),
(267, 'Lifewood Crystal (Rare)', 'This Rare Lifewood Crystal can be used in the crafter', 'crafting', 0, 'css/images/NewGameImages/lifewood-crystal.png', 0, 0, 0, 0, 0, 0, 1, 'Gems', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 0, 1, NULL),
(268, 'Radiantium Core (Rare)', 'This Rare Radiantium Core can be used in the crafter', 'crafting', 0, 'css/images/NewGameImages/radiantium-core.png', 0, 0, 0, 0, 0, 0, 1, 'Gems', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 0, 1, NULL),
(269, 'Starshard Prism (Rare)', 'This Rare Starshard Prism can be used in the crafter', 'crafting', 0, 'css/images/NewGameImages/starshard-prism.png', 0, 0, 0, 0, 0, 0, 1, 'Gems', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 0, 1, NULL),
(270, 'Stone (Rare)', 'This Rare Stone can be used in the crafter', 'crafting', 0, 'css/images/NewGameImages/stone.png', 0, 0, 0, 0, 0, 0, 1, 'Gems', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 0, 1, NULL),
(271, 'Sofa', 'Add a luxurious Sofa to your house and gain a +50 Awake Boost!', 'house', 0, 'css/images/NewGameImages/sofa.png', 0, 0, 0, 0, 0, 0, 1, 'House', 0, 0, 0, '0', 0, 0, 0, 0, 0, 0, 0, '', 50, 0, 0, 0, 5),
(272, 'Fireplace', 'Add a luxurious Fireplace to your house and gain a +75 Awake Boost!', 'house', 0, 'css/images/NewGameImages/fireplace.png', 0, 0, 0, 0, 0, 0, 1, 'House', 0, 0, 0, '0', 0, 0, 0, 0, 0, 0, 0, '', 75, 0, 0, 0, 5),
(273, 'Metal (Rare)', 'This Rare Metal can be used in the crafter', 'crafting', 0, 'css/images/NewGameImages/metal.png', 0, 0, 0, 0, 0, 0, 1, 'Gems', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 0, 1, NULL),
(274, 'Leather (Rare)', 'This Rare Leather can be used in the crafter', 'crafting', 0, 'css/images/NewGameImages/leather.png', 0, 0, 0, 0, 0, 0, 1, 'Gems', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 0, 1, NULL),
(275, 'Wood (Rare)', 'This Rare Wood can be used in the crafter', 'crafting', 0, 'css/images/NewGameImages/wood.png', 0, 0, 0, 0, 0, 0, 1, 'Gems', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 0, 1, NULL),
(276, 'Research Token', 'Use your research token to knock 1 day off of your research time', '', 0, 'css/images/NewGameImages/research-token.png', 0, 0, 0, 0, 0, 0, 1, 'Gems', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 1, 0, NULL),
(277, 'Mission Pass', 'Use your Mission Pass and you\'ll be able to reset a mission you have completed today and earn your prizes over again!', '', 0, 'css/images/NewGameImages/mission-pass.png', 0, 0, 0, 0, 0, 0, 1, 'Gems', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 1, 1, NULL),
(278, 'Sound System', 'Add a luxurious Sound System to your house and gain a +100 Awake Boost!', 'house', 0, 'css/images/NewGameImages/soundsystem.png', 0, 0, 0, 0, 0, 0, 1, 'House', 0, 0, 0, '0', 0, 0, 0, 0, 0, 0, 0, '', 100, 0, 0, 0, 5),
(279, 'Protein Bar', 'Eat your Protein Bar and gain a 20% Gym boost for 15 minutes!\n\n', '', 0, 'css/images/NewGameImages/gym-protein-bar.png', 0, 0, 0, 0, 0, 0, 1, 'Gems', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 1, 1, NULL),
(281, 'Gym Super Pills', 'Eat your Gym Super Pills and gain a 10% Awake boost for an 15 minutes!\n\n', '', 0, 'css/images/NewGameImages/gym-super-pills.png', 0, 0, 0, 0, 0, 0, 1, 'Gems', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 1, 1, NULL),
(282, 'Gym Crate', 'Open to find wonders inside that will be sure to help you boost those stats!', '', 0, 'css/images/NewGameImages/gym-crate.png', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 1, 0, NULL),
(283, 'Gold Rush Token Chest', 'Open the chest to find 10 Gold Rush Tokens inside', '', 0, 'css/images/NewGameImages/grt-chest.png', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 1, 1, NULL),
(284, 'Ghost Vacuum', 'If your going Ghost hunting, you\'ll need a good vacuum to suck them away!', '', 0, 'css/images/NewGameImages/ghost-vacuum.png', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 1, 0, NULL),
(285, 'Dracula Blood Bag', 'Raiding Dracula isn\'t easy, but this Blood Bag will be sure to lure him out', '', 0, 'css/images/NewGameImages/dracula-blood-bag.png', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 0, 1, NULL),
(286, 'Halloween Crate', 'Open to find all kinds of spooky treats!', '', 0, 'css/images/NewGameImages/halloween-crate.png', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 1, 1, NULL),
(287, 'Draculas Coffin', 'Add a little halloween spook to your house and gain a +200 Awake Boost!', 'house', 0, 'css/images/NewGameImages/draculas-coffin.png', 0, 0, 0, 0, 0, 0, 1, 'House', 0, 0, 0, '0', 0, 0, 0, 0, 0, 0, 0, '', 200, 0, 0, 0, 10),
(288, 'Cotton Candy', 'Take a sweet bite of Cotton Candy and see if your lucky enough to gain an EXP boost!', '', 0, 'css/images/NewGameImages/cotton-candy.png', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 1, 1, NULL),
(289, 'Draculas Loot Crate', 'Open Draculas Loot Crate and see whats inside!', '', 0, 'css/images/NewGameImages/draculas-loot-chesr.png', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 1, 1, NULL),
(290, 'Toffee Apple', 'Use your Toffee Apple and get the item reward instantly from your next City Goon attack', '', 0, 'css/images/NewGameImages/toffee-apple.png', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 1, 1, NULL),
(292, 'Trick or Treat Pass', 'Use your Trick or Treat pass to get 10 minutes of uninterrupted tricking or treating.', '', 0, 'css/images/NewGameImages/trick-or-treat-pass.png', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 1, 1, NULL),
(293, 'Draculas Statue', 'Add a little halloween spook to your house and gain a +200 Awake Boost!', 'house', 0, 'css/images/NewGameImages/dracula-statue.png', 0, 0, 0, 0, 0, 0, 1, 'House', 0, 0, 0, '0', 0, 0, 0, 0, 0, 0, 0, '', 200, 0, 0, 0, 10),
(294, 'Black Friday Crate', 'Open to find all kinds of treats!', '', 0, 'css/images/NewGameImages/black-friday-crate.png', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 1, 1, NULL),
(295, 'Christmas Gift', 'Give the gift of Christmas this year', '', 0, 'css/images/NewGameImages/christmas-gift.png', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 0, 1, NULL),
(296, 'Bloodstone Reaper', '', 'weapon', 2500000000, 'css/images/NewGameImages/bloodstone-reaper.png', 65, 0, 0, 0, 0, 1, 1500, '', 0, 0, 0, '0', 0, 0, 0, 0, 47, 0, 0, '', 0, 0, 0, 1, NULL),
(297, 'Comet Vanguard', '', 'armor', 2500000000, 'css/images/NewGameImages/comet-vanguard.png', 0, 65, 0, 0, 0, 1, 1500, '', 0, 0, 0, '0', 0, 0, 0, 0, 47, 0, 0, '', 0, 0, 0, 1, NULL),
(298, 'Comet Vanguard Boots', '', 'shoes', 2500000000, 'css/images/NewGameImages/comet-vanguard-boots.png', 0, 0, 65, 0, 0, 1, 1500, '', 0, 0, 0, '0', 0, 0, 0, 0, 47, 0, 0, '', 0, 0, 0, 1, NULL),
(299, 'Black Mamba Rifle', '', 'weapon', 4000000000, 'css/images/NewGameImages/black-mamba-rifle.png', 75, 0, 0, 0, 0, 1, 2500, '', 0, 0, 0, '0', 0, 0, 0, 0, 48, 0, 0, '', 0, 0, 0, 1, NULL),
(300, 'Black Mamba Body Armour', '', 'armor', 4000000000, 'css/images/NewGameImages/black-mamba-body-armour.png', 0, 75, 0, 0, 0, 1, 2500, '', 0, 0, 0, '0', 0, 0, 0, 0, 48, 0, 0, '', 0, 0, 0, 1, NULL),
(301, 'Black Mamba Boots', '', 'shoes', 4000000000, 'css/images/NewGameImages/black-mamba-boots.png', 0, 0, 75, 0, 0, 1, 2500, '', 0, 0, 0, '0', 0, 0, 0, 0, 48, 0, 0, '', 0, 0, 0, 1, NULL),
(302, 'Ironcliffe Launcher', '', 'weapon', 6000000000, 'css/images/NewGameImages/ironcliffe-launcher.png', 85, 0, 0, 0, 0, 1, 3000, '', 0, 0, 0, '0', 0, 0, 0, 0, 49, 0, 0, '', 0, 0, 0, 1, NULL),
(303, 'Ironcliff Body Suit', '', 'armor', 6000000000, 'css/images/NewGameImages/ironcliffe-bodysuit.png', 0, 85, 0, 0, 0, 1, 3000, '', 0, 0, 0, '0', 0, 0, 0, 0, 49, 0, 0, '', 0, 0, 0, 1, NULL),
(304, 'Ironcliff Boots', '', 'shoes', 6000000000, 'css/images/NewGameImages/ironcliffe-boots.png', 0, 0, 85, 0, 0, 1, 3000, '', 0, 0, 0, '0', 0, 0, 0, 0, 49, 0, 0, '', 0, 0, 0, 1, NULL),
(305, 'Double Gym Injection', 'Shoot Up & Double Your Gym Efforts For 30 Minutes!\r\n\r\n', '', 0, 'css/images/NewGameImages/double-gym-injection.png', 0, 0, 0, 0, 0, 0, 1, 'Gems', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 1, 1, NULL),
(306, 'New Year Box', 'Open to find all kinds of treats!', '', 0, 'css/images/NewGameImages/new-year-box.png', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 1, 1, NULL),
(307, 'Fingerless Gloves', '', 'gloves', 15000, 'css/images/NewGameImages/fingerless-gloves.png', 0, 0, 0, 5, 0, 1, 0, '', 0, 0, 0, '0', 0, 0, 0, 0, 1, 0, 0, '', 0, 0, 0, 1, NULL),
(308, 'Worn Work Gloves', '', 'gloves', 250000, 'css/images/NewGameImages/worn-work-gloves.png', 0, 0, 0, 10, 0, 1, 0, '', 0, 0, 0, '0', 0, 0, 0, 0, 39, 0, 0, '', 0, 0, 0, 1, NULL),
(309, 'Racing Gloves', '', 'gloves', 2500000, 'css/images/NewGameImages/racing-gloves.png', 0, 0, 0, 15, 0, 1, 0, '', 0, 0, 0, '0', 0, 0, 0, 0, 40, 0, 0, '', 0, 0, 0, 1, NULL),
(310, 'MMA Gloves', '', 'gloves', 25000000, 'css/images/NewGameImages/mma-gloves.png', 0, 0, 0, 20, 0, 1, 0, '', 0, 0, 0, '0', 0, 0, 0, 0, 41, 0, 0, '', 0, 0, 0, 1, NULL),
(311, 'Leather Gloves', '', 'gloves', 250000000, 'css/images/NewGameImages/mobsters-leather-gloves.png', 0, 0, 0, 25, 0, 1, 0, '', 0, 0, 0, '0', 0, 0, 0, 0, 42, 0, 0, '', 0, 0, 0, 1, NULL),
(312, 'Assassin Gloves', '', 'gloves', 500000000, 'css/images/NewGameImages/assassin-gloves.png', 0, 0, 0, 30, 0, 1, 0, '', 0, 0, 0, '0', 0, 0, 0, 0, 44, 0, 0, '', 0, 0, 0, 1, NULL),
(313, 'Chain Mail Gloves', '', 'gloves', 750000000, 'css/images/NewGameImages/chainmail-gloves.png', 0, 0, 0, 40, 0, 1, 0, '', 0, 0, 0, '0', 0, 0, 0, 0, 45, 0, 0, '', 0, 0, 0, 1, NULL),
(314, 'Steel Knuckle Gloves', '', 'gloves', 1250000000, 'css/images/NewGameImages/steel-knuckle-gloves.png', 0, 0, 0, 55, 0, 1, 0, '', 0, 0, 0, '0', 0, 0, 0, 0, 46, 0, 0, '', 0, 0, 0, 1, NULL),
(315, 'Comet Vanguard Gloves', '', 'gloves', 2500000000, 'css/images/NewGameImages/comet-vanguard-gloves.png', 0, 0, 0, 65, 0, 1, 0, '', 0, 0, 0, '0', 0, 0, 0, 0, 47, 0, 0, '', 0, 0, 0, 1, NULL),
(316, 'Black Mamba Gloves', '', 'gloves', 4000000000, 'css/images/NewGameImages/black-mamba-gloves.png', 0, 0, 0, 75, 0, 1, 0, '', 0, 0, 0, '0', 0, 0, 0, 0, 48, 0, 0, '', 0, 0, 0, 1, NULL),
(317, 'Ironcliffe Gloves', '', 'gloves', 6000000000, 'css/images/NewGameImages/ironcliffe-gloves.png', 0, 0, 0, 85, 0, 1, 0, '', 0, 0, 0, '0', 0, 0, 0, 0, 49, 0, 0, '', 0, 0, 0, 1, NULL),
(318, 'CPU (Rare)', 'This Rare CPU can be used in the crafter', 'crafting', 0, 'css/images/NewGameImages/cpu.png', 0, 0, 0, 0, 0, 0, 1, 'Gems', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 0, 1, NULL),
(319, 'Plastic (Rare)', 'This Rare Plastic can be used in the crafter', 'crafting', 0, 'css/images/NewGameImages/plastic.png', 0, 0, 0, 0, 0, 0, 1, 'Gems', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 0, 1, NULL),
(320, 'TV', 'Add a luxurious TV to your house and gain a +50 Awake Boost!', 'house', 0, 'css/images/NewGameImages/tv.png', 0, 0, 0, 0, 0, 0, 1, 'House', 0, 0, 0, '0', 0, 0, 0, 0, 0, 0, 0, '', 50, 0, 0, 0, 5),
(321, 'Hitman Statue', 'Add a luxurious Hitman Statue to your house and gain a +300 Awake Boost!', 'house', 0, 'css/images/NewGameImages/hitman-statue.png', 0, 0, 0, 0, 0, 0, 1, 'House', 0, 0, 0, '0', 0, 0, 0, 0, 0, 0, 0, '', 300, 0, 0, 0, 5),
(322, 'Love Potion', 'Feel the Love with a Love Potion, once you drink you won\'t need any energy to attack for the next 2 minutes.\r\n\r\n', '', 0, 'css/images/NewGameImages/love-potion.png', 0, 0, 0, 0, 0, 0, 1, 'Gems', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 1, 1, NULL),
(324, 'Perfume', 'A quick spray of perfume can work wonders. Use it and you\'ll earn double EXP on your next mission.', '', 0, 'css/images/NewGameImages/perfume.png', 0, 0, 0, 0, 0, 0, 1, 'Gems', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 1, 1, NULL),
(325, 'Heart Bed', 'Add a luxurious Heart Bed to your house and gain a +100 Awake Boost!', 'house', 0, 'css/images/NewGameImages/heart-bed.png', 0, 0, 0, 0, 0, 0, 1, 'House', 0, 0, 0, '0', 0, 0, 0, 0, 0, 0, 0, '', 100, 0, 0, 0, 5),
(326, 'Love Heart Lock Box', 'Open to find all kinds of treats!', '', 0, 'css/images/NewGameImages/love-heart-box.png', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 1, 1, NULL),
(327, 'Golden Chest', 'Open to find all kinds of treats!', '', 0, 'css/images/NewGameImages/golden-chest.png', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 1, 1, NULL),
(328, 'Cosmic Booster', 'The Cosmic Booster will give you +400 Energy & Nerve Boost!', '', 0, 'css/images/NewGameImages/cosmic-booster.png', 1, 0, 0, 0, 0, 0, 0, 'booster', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, 'maxnerve:400|maxenergy:400', 0, 0, 0, 1, NULL),
(329, 'Dark Matter Core (Rare)', 'This Dark Matter Core can be used in the crafter', 'crafting', 0, 'css/images/NewGameImages/Dark-Matter-Core.png', 0, 0, 0, 0, 0, 0, 1, 'Gems', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 0, 1, NULL),
(330, 'Gravity Stabilizer (Rare)', 'This Rare Gravity Stabilizer can be used in the crafter', 'crafting', 0, 'css/images/NewGameImages/Gravity-Stabilizer.png', 0, 0, 0, 0, 0, 0, 1, 'Gems', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 0, 1, NULL),
(331, 'Nebula Fiber (Rare)', 'This Rare Nebula Fiber can be used in the crafter', 'crafting', 0, 'css/images/NewGameImages/Nebula-Fiber.png', 0, 0, 0, 0, 0, 0, 1, 'Gems', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 0, 1, NULL),
(332, 'Quantum Coil (Rare)', 'This Rare Quantum Coil can be used in the crafter', 'crafting', 0, 'css/images/NewGameImages/Quantum-Coil.png', 0, 0, 0, 0, 0, 0, 1, 'Gems', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 0, 1, NULL),
(333, 'Nerve Tonic', 'Old-school remedy to take the edge off. Replenishes 100 nerve.', 'consumable', 0, 'css/images/2025/nerve_tonic.png', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 1, 1, NULL),
(334, 'Balls of Steel', 'A rare candy for the fearless. Replenishes 250 nerve.', 'consumable', 0, 'css/images/2025/balls_of_steel.png', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 1, 1, NULL),
(335, 'Easter Booster', 'The Easter Booster will give you a +100 Energy & Nerve Boost, and 10% exp boost when doing Crimes.', '', 0, 'css/images/2025/easter_booster.png', 1, 0, 0, 0, 0, 0, 0, 'booster', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, 'maxnerve:100|maxenergy:100', 0, 0, 0, 1, NULL),
(336, 'Common Easter Egg', 'A common easter egg found during Easter 2025', '', 0, 'css/images/2025/common_easter_egg.png', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, NULL),
(337, 'Rare Easter Egg', 'A rare easter egg found during Easter 2025', '', 0, 'css/images/2025/rare_easter_egg.png', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, NULL),
(338, 'Ultra Rare Easter Egg', 'An ultra rare easter egg found during Easter 2025', '', 0, 'css/images/2025/ultra_rare_easter_egg.png', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, NULL),
(339, 'Common Gem Bag', 'A small bag containing a variety of common & uncommon gems. Obtained during Easter 2025 by trading in Easter Eggs.', '', 0, 'css/images/2025/common_gem_bag.png', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 1, 1, NULL),
(340, 'Rare Gem Bag', 'A medium bag containing a variety of gems ranging from common to rare. Obtained during Easter 2025 by trading in Easter Eggs.', '', 0, 'css/images/2025/rare_gem_bag.png', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 1, 1, NULL),
(341, 'Ultra Rare Gem Bag', 'A large bag containing a variety of gems ranging from common to ultra rare. Obtained during Easter 2025 by trading in Easter Eggs.', '', 0, 'css/images/2025/ultra_rare_gem_bag.png', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 1, 1, NULL),
(342, 'Easter Crate', 'Open to unbox your easter loot', '', 0, 'css/images/2025/easter_crate.png', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 1, 0, NULL),
(343, 'Easter Statue', 'Add a little easter vibe to your house and gain a +225 Awake Boost!', 'house', 0, 'css/images/2025/easter_statue.png', 0, 0, 0, 0, 0, 0, 1, 'House', 0, 0, 0, '0', 0, 0, 0, 0, 0, 0, 0, '', 225, 0, 0, 0, 5),
(344, 'Rare Egg Basket', 'Don Egghopper has a sensitive nose, these eggs are some of the finest, it will definitely lure him out!', '', 0, 'css/images/2025/rare_easter_basket.png', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 0, 0, NULL),
(345, 'Easter Egg Bead', 'This charm has an innate affinity with easter eggs from Don Egghopper\'s realm. Break it to benefit from 2 hours double Easter Egg drop rate in Maze.', '', 0, 'css/images/2025/easter_bead.png', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 1, 0, NULL),
(346, 'Maze Boost', 'On use gives you 25 maze turns. Doubles the rate at which you replenish maze turns (2 per minute), also increases the lower boundary for replenishing maze turns from 30 to 50 while active. Has a 10 day duration.\r\n\r\nThis is an Easter 2025 Limited Item', '', 0, 'css/images/2025/maze_boost.png', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 1, 0, NULL),
(347, 'Super Easter Booster', 'The Easter Booster will give you a +150 Energy & Nerve Boost, 15% exp boost and 10% money boost when doing Crimes.', '', 0, 'css/images/2025/super_easter_booster_item.png', 1, 0, 0, 0, 0, 0, 0, 'booster', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, 'maxnerve:150|maxenergy:150', 0, 0, 0, 1, NULL),
(348, 'Golden Easter Egg', 'A Golden Easter Egg, hammer it open and see what is inside!', '', 0, 'css/images/2025/gold_egg.png', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 1, 0, NULL),
(349, 'Egg Key (Part 1)', 'This seems to be half of a key, if we find the other one we might be able to search the Egglab for treasures.', '', 0, 'css/images/2025/egg_key_1.png', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, NULL),
(350, 'Egg Key (Part 2)', 'This seems to be half of a key, if we find the other one we might be able to search the Egglab for treasures.', '', 0, 'css/images/2025/egg_key_2.png', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, NULL),
(351, 'Cleaner\'s Supply Crate', 'Looks like an unmarked janitor’s box, but it\'s filled with deadly goods.', '', 0, 'css/images/2025/supply_crate.png', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 1, 0, NULL),
(352, 'Polished Brass Butler Bell', 'Summon help… or signal when it’s time to disappear.\n\nAwake +100', '', 0, 'css/images/2025/polished_brass_butler_bell.png', 0, 0, 0, 0, 0, 0, 0, 'House', 0, 0, 0, '0', 0, 0, 0, 0, 0, 0, 0, '', 100, 0, 0, 0, 5),
(353, 'Immaculate Leather Armchair', 'For sitting back and watching problems get cleaned up.\n\nAwake +200', '', 0, 'css/images/2025/immaculate_leather_armchair.png', 0, 0, 0, 0, 0, 0, 0, 'House', 0, 0, 0, '0', 0, 0, 0, 0, 0, 0, 0, '', 200, 0, 0, 0, 5),
(354, 'Crystal Decanter Set', 'Nothing cleans the soul like a stiff drink after a messy job.\n\nAwake +150', '', 0, 'css/images/2025/crystal_decanter_set.png', 0, 0, 0, 0, 0, 0, 0, 'House', 0, 0, 0, '0', 0, 0, 0, 0, 0, 0, 0, '', 150, 0, 0, 0, 5),
(355, 'The Cleaner\'s Laundry Rack', 'Pressed suits… and blood-stained shirts waiting for a wash.\n\nAwake +250', '', 0, 'css/images/2025/laundry_rack.png', 0, 0, 0, 0, 0, 0, 0, 'House', 0, 0, 0, '0', 0, 0, 0, 0, 0, 0, 0, '', 250, 0, 0, 0, 5),
(356, 'Building Pass', 'A pass to the building complex that The Janitor cleans, use it to sneak up behind him.', '', 0, 'css/images/2025/building_pass.png', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 1, 0, NULL),
(357, 'Raid Statue', 'Symbol of your countless victories in dangerous raids!\r\n\r\n+150 Awake, +1% Drop Rate Chance In Raids', 'house', 0, 'css/images/2025/raid_statue.png', 0, 0, 0, 0, 0, 0, 1, 'House', 0, 0, 0, '0', 0, 0, 0, 0, 0, 0, 0, '', 150, 0, 0, 0, 5),
(358, 'Advancement Chest', 'Filled to the brim with what you need for a boost to your respectful image.', '', 0, 'css/images/2025/advancement_chest.png', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 1, 0, NULL),
(359, 'Kingpin\'s Hoard', 'A treasure chest with the spoils of organized crime.', '', 0, 'css/images/2025/kingpins_hoard.png', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 1, 0, NULL),
(360, 'Statue of Samhain', 'Stories of Samhain still haunt Storm City till this day!\r\n\r\nProvides +225 Awake', 'house', 0, 'css/images/2025/samhain-statue.png', 0, 0, 0, 0, 0, 0, 1, 'House', 0, 0, 0, '0', 0, 0, 0, 0, 0, 0, 0, '', 225, 0, 0, 0, 10),
(361, 'Halloween Candies', 'It\'s been said that Samhain likes food as offerings, candy might count.', '', 0, 'css/images/2025/halloween_candies.png', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 0, 1, NULL),
(362, 'Halloween Crate', 'Open to unbox your halloween loot', '', 0, 'css/images/2025/halloween_crate.png', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, '0', 0, 0, 0, 1, 0, 0, 0, '', 0, 0, 1, 0, NULL);

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `item_daily_limit`
--

CREATE TABLE `item_daily_limit` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `use_date` varchar(255) NOT NULL,
  `mug_protection` int(11) NOT NULL DEFAULT 0,
  `attack_protection` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `item_sell`
--

CREATE TABLE `item_sell` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `itemid` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `when` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `item_temp_use`
--

CREATE TABLE `item_temp_use` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `raid_pass` int(11) NOT NULL DEFAULT 0,
  `raid_booster` int(11) NOT NULL DEFAULT 0,
  `crime_potion_time` int(11) NOT NULL DEFAULT 0,
  `crime_booster_time` int(11) NOT NULL DEFAULT 0,
  `nerve_vial_time` int(11) NOT NULL DEFAULT 0,
  `gang_double_exp_time` bigint(20) NOT NULL DEFAULT 0,
  `gang_double_exp_hours` int(11) NOT NULL DEFAULT 0,
  `mission_passes` int(11) NOT NULL DEFAULT 0,
  `gym_10_multiplier_time` bigint(20) NOT NULL DEFAULT 0,
  `crime_15_multiplier_time` bigint(20) NOT NULL DEFAULT 0,
  `supercrime_time` bigint(20) NOT NULL DEFAULT 0,
  `gym_protein_bar_time` bigint(20) NOT NULL DEFAULT 0,
  `gym_super_pills_time` bigint(20) NOT NULL DEFAULT 0,
  `ghost_vacuum_time` bigint(20) NOT NULL DEFAULT 0,
  `toffee_apples` int(11) NOT NULL DEFAULT 0,
  `trick_or_treat_pass_time` bigint(20) NOT NULL DEFAULT 0,
  `double_gym_time` bigint(20) NOT NULL DEFAULT 0,
  `love_potions` int(11) NOT NULL DEFAULT 0,
  `love_potions_time` bigint(20) NOT NULL DEFAULT 0,
  `perfume` int(11) NOT NULL DEFAULT 0,
  `easter_bead` int(11) NOT NULL DEFAULT 0,
  `maze_boost` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `jobinfo`
--

CREATE TABLE `jobinfo` (
  `userid` smallint(5) UNSIGNED NOT NULL,
  `dailyClockins` tinyint(1) NOT NULL,
  `lastClockin` int(11) NOT NULL,
  `addedPercent` tinyint(4) NOT NULL,
  `lastQuitTime` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `jobs`
--

CREATE TABLE `jobs` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `money` int(11) NOT NULL,
  `level` int(11) NOT NULL DEFAULT 0,
  `prestige` int(11) NOT NULL,
  `points` int(11) NOT NULL,
  `raidtoken` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Data dump for tabellen `jobs`
--

INSERT INTO `jobs` (`id`, `name`, `money`, `level`, `prestige`, `points`, `raidtoken`) VALUES
(1, 'Wheeler Dealer', 5000, 5, 0, 25, 2),
(2, 'Bodyguard', 25000, 50, 0, 50, 2),
(3, 'Master Thief', 50000, 100, 0, 75, 2),
(4, 'Highend Real Estate Manager', 75000, 200, 0, 100, 2),
(5, 'Bookmaker Shareholder', 100000, 300, 0, 125, 2),
(26, 'Professional Poker Player', 500000, 400, 0, 150, 2),
(27, 'Casino Bodyguard', 1000000, 500, 0, 200, 2),
(28, 'Blackmailer', 2000000, 1000, 0, 250, 2),
(29, 'Mob Associate', 3000000, 1500, 0, 300, 0),
(30, 'Nightclub Enforcer', 5000000, 2000, 0, 350, 0),
(31, 'Cartel Liaison', 7000000, 2500, 0, 400, 0),
(32, 'Offshore Bank Operative', 9000000, 3000, 0, 450, 0),
(33, 'Cybercrime Architect', 12000000, 3500, 0, 500, 0),
(34, 'Black Market Tycoon', 14000000, 4000, 0, 600, 0),
(35, 'Shadow Syndicate Boss', 16000000, 4500, 0, 700, 0),
(36, 'Global Underworld Chairman', 18000000, 5000, 0, 800, 0),
(37, 'Blacksite Commander', 20000000, 5500, 0, 900, 0),
(38, 'Global Arms Broker', 25000000, 6000, 0, 1000, 0),
(39, 'Underworld Diplomat', 30000000, 6500, 0, 1200, 0),
(40, 'Mastermind of the Syndicate', 35000000, 7000, 0, 1400, 0),
(41, 'Syndicate Strategist', 40000000, 7500, 0, 1600, 0),
(42, 'Underworld Executor', 50000000, 8000, 0, 1800, 0),
(43, 'Blood Pact Broker', 60000000, 8500, 0, 2000, 0),
(44, 'Architect of Chaos', 70000000, 9000, 0, 2500, 0);

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `job_applications`
--

CREATE TABLE `job_applications` (
  `application_id` int(11) NOT NULL,
  `business_id` int(11) DEFAULT NULL,
  `player_id` int(11) DEFAULT NULL,
  `application_status` enum('pending','accepted','rejected') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `land`
--

CREATE TABLE `land` (
  `userid` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `city` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `limited_store_pack`
--

CREATE TABLE `limited_store_pack` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `item_id` int(11) NOT NULL DEFAULT 0,
  `item_quantity` int(11) NOT NULL,
  `available` int(11) NOT NULL DEFAULT 0,
  `times_purchased` int(11) NOT NULL DEFAULT 0,
  `per_person_limit` int(11) NOT NULL DEFAULT 0,
  `gold_cost` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Data dump for tabellen `limited_store_pack`
--

INSERT INTO `limited_store_pack` (`id`, `name`, `item_id`, `item_quantity`, `available`, `times_purchased`, `per_person_limit`, `gold_cost`) VALUES
(1, 'Nerve Vial Pack', 256, 1, 20, 7, 2, 500),
(2, 'Gang Double EXP Pack', 257, 1, 20, 12, 3, 750),
(3, 'Leveling Crate', 258, 1, 30, 20, 5, 1000),
(4, 'Research Token', 276, 1, 100, 100, 10, 150),
(5, 'Mission Pass', 277, 1, 0, 48, 10, 200),
(6, 'Gym Crate', 282, 1, 50, 50, 5, 500),
(7, 'Dracula Coffin', 287, 1, 175, 154, 10, 50),
(8, 'Halloween Crate', 286, 1, 100, 74, 10, 400),
(9, 'Black Friday Crate', 294, 1, 150, 115, 20, 600),
(10, 'New Year Pack', 306, 1, 250, 208, 150, 800),
(11, 'Love Heart Lock Box', 326, 1, 150, 32, 50, 1000),
(12, 'Golden Chest', 327, 1, 200, 62, 100, 900),
(13, 'Easter Crate', 342, 1, 300, 32, 100, 750),
(14, 'Cleaner\'s Supply Crate', 351, 1, 200, 94, 50, 800),
(15, 'Advancement Chest', 358, 1, 100, 40, 10, 600),
(16, 'Kingpin\'s Hoard', 359, 1, 100, 41, 10, 750),
(17, 'Halloween Crate', 362, 1, 300, 29, 100, 500);

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `limited_store_pack_purchase`
--

CREATE TABLE `limited_store_pack_purchase` (
  `id` int(11) NOT NULL,
  `limited_store_pack_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `purchases` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `loot`
--

CREATE TABLE `loot` (
  `id` int(11) NOT NULL,
  `boss_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `min_money` int(11) DEFAULT NULL,
  `max_money` int(11) DEFAULT NULL,
  `min_points` int(11) DEFAULT NULL,
  `max_points` int(11) DEFAULT NULL,
  `drop_rate` float DEFAULT NULL,
  `min_raidpoints` int(11) NOT NULL DEFAULT 0,
  `max_raidpoints` int(11) NOT NULL DEFAULT 0,
  `bonus` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Data dump for tabellen `loot`
--

INSERT INTO `loot` (`id`, `boss_id`, `item_id`, `min_money`, `max_money`, `min_points`, `max_points`, `drop_rate`, `min_raidpoints`, `max_raidpoints`, `bonus`) VALUES
(1, 1, NULL, 10000, 100000, 10, 200, 0, 1, 10, 0),
(82, 1, 8, 0, 0, 0, 0, 0.03, 0, 0, 0),
(83, 1, 10, 0, 0, 0, 0, 0.01, 0, 0, 0),
(84, 1, 68, 0, 0, 0, 0, 0.05, 0, 0, 1),
(85, 1, 69, 0, 0, 0, 0, 0.05, 0, 0, 1),
(86, 1, 209, 0, 0, 0, 0, 0.005, 0, 0, 0),
(87, 1, 210, 0, 0, 0, 0, 0.005, 0, 0, 1),
(88, 1, 212, 0, 0, 0, 0, 0.01, 0, 0, 1),
(89, 1, 211, 0, 0, 0, 0, 0.01, 0, 0, 0),
(90, 17, NULL, 50000, 350000, 50, 300, 0, 4, 25, 0),
(91, 17, 8, 0, 0, 0, 0, 0.03, 0, 0, 0),
(92, 17, 10, 0, 0, 0, 0, 0.01, 0, 0, 0),
(93, 17, 68, 0, 0, 0, 0, 0.05, 0, 0, 1),
(94, 17, 69, 0, 0, 0, 0, 0.05, 0, 0, 1),
(95, 17, 209, 0, 0, 0, 0, 0.005, 0, 0, 0),
(96, 17, 210, 0, 0, 0, 0, 0.005, 0, 0, 1),
(97, 17, 212, 0, 0, 0, 0, 0.01, 0, 0, 1),
(98, 17, 211, 0, 0, 0, 0, 0.01, 0, 0, 0),
(101, 18, NULL, 100000, 500000, 100, 450, 0, 10, 50, 0),
(102, 18, 225, 0, 0, 0, 0, 0.001, 0, 0, 0),
(103, 18, 226, 0, 0, 0, 0, 0.001, 0, 0, 0),
(104, 18, 227, 0, 0, 0, 0, 0.001, 0, 0, 0),
(105, 18, 228, 0, 0, 0, 0, 0.001, 0, 0, 0),
(106, 18, 209, 0, 0, 0, 0, 0.01, 0, 0, 0),
(107, 18, 210, 0, 0, 0, 0, 0.01, 0, 0, 0),
(108, 18, 211, 0, 0, 0, 0, 0.01, 0, 0, 0),
(109, 18, 212, 0, 0, 0, 0, 0.01, 0, 0, 0),
(110, 19, 231, 0, 0, 0, 0, 0.001, 0, 0, 0),
(111, 19, 229, 0, 0, 0, 0, 0.01, 0, 0, 0),
(112, 19, 230, 0, 0, 0, 0, 0.01, 0, 0, 0),
(113, 19, 194, 0, 0, 0, 0, 0.001, 0, 0, 0),
(114, 19, NULL, 100000, 1000000, 100, 1000, 0, 10, 100, 0),
(115, 19, 225, 0, 0, 0, 0, 0.01, 0, 0, 0),
(116, 19, 227, 0, 0, 0, 0, 0.01, 0, 0, 0),
(117, 19, 228, 0, 0, 0, 0, 0.01, 0, 0, 0),
(118, 19, 226, 0, 0, 0, 0, 0.01, 0, 0, 0),
(119, 20, 231, 0, 0, 0, 0, 0.005, 0, 0, 0),
(120, 20, 229, 0, 0, 0, 0, 0.01, 0, 0, 0),
(121, 20, 230, 0, 0, 0, 0, 0.01, 0, 0, 0),
(122, 20, 194, 0, 0, 0, 0, 0.002, 0, 0, 0),
(123, 20, NULL, 100000, 1750000, 275, 1400, 0, 40, 160, 0),
(124, 20, 251, 0, 0, 0, 0, 0.01, 0, 0, 0),
(125, 20, 252, 0, 0, 0, 0, 0.01, 0, 0, 0),
(126, 20, 253, 0, 0, 0, 0, 0.01, 0, 0, 0),
(127, 20, 254, 0, 0, 0, 0, 0.08, 0, 0, 0),
(128, 20, 255, 0, 0, 0, 0, 0.08, 0, 0, 0),
(129, 19, 270, 0, 0, 0, 0, 0.001, 0, 0, 0),
(130, 19, 273, 0, 0, 0, 0, 0.001, 0, 0, 0),
(131, 19, 274, 0, 0, 0, 0, 0.001, 0, 0, 0),
(132, 19, 275, 0, 0, 0, 0, 0.001, 0, 0, 0),
(133, 20, 273, 0, 0, 0, 0, 0.01, 0, 0, 0),
(134, 20, 274, 0, 0, 0, 0, 0.01, 0, 0, 0),
(135, 20, 275, 0, 0, 0, 0, 0.01, 0, 0, 0),
(136, 18, 273, 0, 0, 0, 0, 0.001, 0, 0, 0),
(137, 18, 274, 0, 0, 0, 0, 0.001, 0, 0, 0),
(138, 18, 275, 0, 0, 0, 0, 0.001, 0, 0, 0),
(139, 20, 270, 0, 0, 0, 0, 0.01, 0, 0, 0),
(140, 20, 265, 0, 0, 0, 0, 0.001, 0, 0, 0),
(141, 21, 285, 0, 0, 0, 0, 0.005, 0, 0, 0),
(142, 21, 229, 0, 0, 0, 0, 0.01, 0, 0, 0),
(143, 21, 230, 0, 0, 0, 0, 0.01, 0, 0, 0),
(144, 21, 194, 0, 0, 0, 0, 0.002, 0, 0, 0),
(145, 21, NULL, 200000, 1750000, 350, 2000, 0, 70, 200, 0),
(146, 21, 251, 0, 0, 0, 0, 0.01, 0, 0, 0),
(147, 21, 252, 0, 0, 0, 0, 0.01, 0, 0, 0),
(148, 21, 283, 0, 0, 0, 0, 0.01, 0, 0, 0),
(149, 21, 254, 0, 0, 0, 0, 0.08, 0, 0, 0),
(150, 21, 255, 0, 0, 0, 0, 0.08, 0, 0, 0),
(151, 21, 273, 0, 0, 0, 0, 0.01, 0, 0, 0),
(152, 21, 274, 0, 0, 0, 0, 0.01, 0, 0, 0),
(153, 21, 275, 0, 0, 0, 0, 0.01, 0, 0, 0),
(154, 21, 270, 0, 0, 0, 0, 0.01, 0, 0, 0),
(155, 21, 265, 0, 0, 0, 0, 0.001, 0, 0, 0),
(156, 21, 277, 0, 0, 0, 0, 0.002, 0, 0, 0),
(157, 22, 231, 0, 0, 0, 0, 0.005, 0, 0, 0),
(158, 22, 229, 0, 0, 0, 0, 0.01, 0, 0, 0),
(159, 22, 230, 0, 0, 0, 0, 0.01, 0, 0, 0),
(160, 22, 194, 0, 0, 0, 0, 0.002, 0, 0, 0),
(161, 22, NULL, 75000, 1250000, 150, 1000, 0, 20, 100, 0),
(162, 22, 251, 0, 0, 0, 0, 0.01, 0, 0, 0),
(163, 22, 252, 0, 0, 0, 0, 0.01, 0, 0, 0),
(165, 22, 254, 0, 0, 0, 0, 0.08, 0, 0, 0),
(166, 22, 255, 0, 0, 0, 0, 0.08, 0, 0, 0),
(167, 22, 273, 0, 0, 0, 0, 0.01, 0, 0, 0),
(168, 22, 274, 0, 0, 0, 0, 0.01, 0, 0, 0),
(169, 22, 275, 0, 0, 0, 0, 0.01, 0, 0, 0),
(170, 22, 270, 0, 0, 0, 0, 0.01, 0, 0, 0),
(171, 22, 265, 0, 0, 0, 0, 0.001, 0, 0, 0),
(172, 22, 290, 0, 0, 0, 0, 0.005, 0, 0, 0),
(173, 22, 285, 0, 0, 0, 0, 0.005, 0, 0, 0),
(174, 19, 318, 0, 0, 0, 0, 0.001, 0, 0, 0),
(175, 19, 319, 0, 0, 0, 0, 0.001, 0, 0, 0),
(176, 20, 266, 0, 0, 0, 0, 0.001, 0, 0, 0),
(177, 20, 267, 0, 0, 0, 0, 0.001, 0, 0, 0),
(178, 20, 268, 0, 0, 0, 0, 0.001, 0, 0, 0),
(179, 20, 269, 0, 0, 0, 0, 0.001, 0, 0, 0),
(180, 23, NULL, 200000, 1750000, 500, 2500, 0, 50, 200, 0),
(212, 24, NULL, 500000, 1750000, 1500, 5000, 0, 120, 400, 0),
(213, 24, 231, NULL, NULL, NULL, NULL, 0.005, 0, 0, 0),
(214, 24, 229, NULL, NULL, NULL, NULL, 0.01, 0, 0, 0),
(215, 24, 230, NULL, NULL, NULL, NULL, 0.01, 0, 0, 0),
(216, 24, 194, NULL, NULL, NULL, NULL, 0.004, 0, 0, 0),
(217, 24, 251, NULL, NULL, NULL, NULL, 0.01, 0, 0, 0),
(218, 24, 252, NULL, NULL, NULL, NULL, 0.01, 0, 0, 0),
(219, 24, 253, NULL, NULL, NULL, NULL, 0.01, 0, 0, 0),
(220, 24, 254, NULL, NULL, NULL, NULL, 0.08, 0, 0, 0),
(221, 24, 255, NULL, NULL, NULL, NULL, 0.08, 0, 0, 0),
(222, 24, 273, NULL, NULL, NULL, NULL, 0.01, 0, 0, 0),
(223, 24, 274, NULL, NULL, NULL, NULL, 0.01, 0, 0, 0),
(224, 24, 270, NULL, NULL, NULL, NULL, 0.01, 0, 0, 0),
(225, 24, 265, NULL, NULL, NULL, NULL, 0.001, 0, 0, 0),
(226, 24, 266, NULL, NULL, NULL, NULL, 0.002, 0, 0, 0),
(227, 24, 267, NULL, NULL, NULL, NULL, 0.001, 0, 0, 0),
(228, 24, 268, NULL, NULL, NULL, NULL, 0.001, 0, 0, 0),
(229, 24, 269, NULL, NULL, NULL, NULL, 0.001, 0, 0, 0),
(230, 24, 333, NULL, NULL, NULL, NULL, 0.01, 0, 0, 0),
(231, 24, 334, NULL, NULL, NULL, NULL, 0.001, 0, 0, 0),
(232, 23, 38, NULL, NULL, NULL, NULL, 0.001, 0, 0, 0),
(233, 23, 194, NULL, NULL, NULL, NULL, 0.01, 0, 0, 0),
(234, 23, 230, NULL, NULL, NULL, NULL, 0.05, 0, 0, 0),
(235, 23, 250, NULL, NULL, NULL, NULL, 0.001, 0, 0, 0),
(236, 23, 251, NULL, NULL, NULL, NULL, 0.01, 0, 0, 0),
(237, 23, 252, NULL, NULL, NULL, NULL, 0.01, 0, 0, 0),
(238, 23, 256, NULL, NULL, NULL, NULL, 0.1, 0, 0, 0),
(239, 23, 266, NULL, NULL, NULL, NULL, 0.001, 0, 0, 0),
(240, 23, 276, NULL, NULL, NULL, NULL, 0.0005, 0, 0, 0),
(241, 23, 319, NULL, NULL, NULL, NULL, 0.001, 0, 0, 0),
(242, 23, 333, NULL, NULL, NULL, NULL, 0.1, 0, 0, 0),
(243, 23, 334, NULL, NULL, NULL, NULL, 0.05, 0, 0, 0),
(244, 23, 336, NULL, NULL, NULL, NULL, 0.1, 0, 0, 0),
(245, 23, 337, NULL, NULL, NULL, NULL, 0.05, 0, 0, 0),
(246, 23, 344, NULL, NULL, NULL, NULL, 0.005, 0, 0, 0),
(247, 23, 345, NULL, NULL, NULL, NULL, 0.001, 0, 0, 0),
(248, 24, 337, NULL, NULL, NULL, NULL, 0.1, 0, 0, 0),
(249, 24, 338, NULL, NULL, NULL, NULL, 0.001, 0, 0, 0),
(250, 24, 344, NULL, NULL, NULL, NULL, 0.005, 0, 0, 0),
(251, 24, 345, NULL, NULL, NULL, NULL, 0.001, 0, 0, 0),
(252, 24, 231, NULL, NULL, NULL, NULL, 0.01, 0, 0, 0),
(253, 23, 231, NULL, NULL, NULL, NULL, 0.005, 0, 0, 0),
(254, 24, 277, 0, 0, 0, 0, 0.005, 0, 0, 0),
(255, 23, 253, NULL, NULL, NULL, NULL, 0.01, 0, 0, 0),
(256, 23, 348, NULL, NULL, NULL, NULL, 0.005, 0, 0, 0),
(257, 24, 348, NULL, NULL, NULL, NULL, 0.01, 0, 0, 0),
(258, 25, 231, 0, 0, 0, 0, 0.008, 0, 0, 0),
(259, 25, 229, 0, 0, 0, 0, 0.01, 0, 0, 0),
(260, 25, 230, 0, 0, 0, 0, 0.01, 0, 0, 0),
(261, 25, 194, 0, 0, 0, 0, 0.004, 0, 0, 0),
(262, 25, NULL, 110000, 2000000, 400, 2000, 0, 125, 250, 0),
(263, 25, 251, 0, 0, 0, 0, 0.01, 0, 0, 0),
(264, 25, 252, 0, 0, 0, 0, 0.01, 0, 0, 0),
(265, 25, 253, 0, 0, 0, 0, 0.015, 0, 0, 0),
(266, 25, 254, 0, 0, 0, 0, 0.1, 0, 0, 0),
(267, 25, 255, 0, 0, 0, 0, 0.1, 0, 0, 0),
(268, 25, 273, 0, 0, 0, 0, 0.01, 0, 0, 0),
(269, 25, 274, 0, 0, 0, 0, 0.01, 0, 0, 0),
(270, 25, 275, 0, 0, 0, 0, 0.01, 0, 0, 0),
(271, 25, 270, 0, 0, 0, 0, 0.01, 0, 0, 0),
(272, 25, 265, 0, 0, 0, 0, 0.001, 0, 0, 0),
(273, 25, 266, 0, 0, 0, 0, 0.001, 0, 0, 0),
(274, 25, 267, 0, 0, 0, 0, 0.001, 0, 0, 0),
(275, 25, 268, 0, 0, 0, 0, 0.001, 0, 0, 0),
(276, 25, 269, 0, 0, 0, 0, 0.001, 0, 0, 0),
(289, 25, 337, NULL, NULL, NULL, NULL, 0.2, 0, 0, 0),
(290, 25, 338, NULL, NULL, NULL, NULL, 0.005, 0, 0, 0),
(292, 25, 38, NULL, NULL, NULL, NULL, 0.001, 0, 0, 0),
(293, 25, 333, NULL, NULL, NULL, NULL, 0.1, 0, 0, 0),
(294, 25, 334, NULL, NULL, NULL, NULL, 0.01, 0, 0, 0),
(295, 26, 283, NULL, NULL, NULL, NULL, 0.001, 0, 0, 0),
(296, 26, 253, NULL, NULL, NULL, NULL, 0.01, 0, 0, 0),
(297, 26, 356, NULL, NULL, NULL, NULL, 0.01, 0, 0, 0),
(298, 26, 334, NULL, NULL, NULL, NULL, 0.01, 0, 0, 0),
(299, 26, 194, NULL, NULL, NULL, NULL, 0.002, 0, 0, 0),
(300, 26, 251, NULL, NULL, NULL, NULL, 0.01, 0, 0, 0),
(301, 26, 229, NULL, NULL, NULL, NULL, 0.02, 0, 0, 0),
(302, 26, 230, NULL, NULL, NULL, NULL, 0.01, 0, 0, 0),
(303, 26, 231, NULL, NULL, NULL, NULL, 0.005, 0, 0, 0),
(304, 26, 246, NULL, NULL, NULL, NULL, 0.01, 0, 0, 0),
(305, 26, 247, NULL, NULL, NULL, NULL, 0.01, 0, 0, 0),
(306, 26, 248, NULL, NULL, NULL, NULL, 0.01, 0, 0, 0),
(307, 26, 249, NULL, NULL, NULL, NULL, 0.01, 0, 0, 0),
(308, 26, 256, NULL, NULL, NULL, NULL, 0.05, 0, 0, 0),
(309, 26, 279, NULL, NULL, NULL, NULL, 0.005, 0, 0, 0),
(310, 26, 319, NULL, NULL, NULL, NULL, 0.02, 0, 0, 0),
(311, 26, NULL, 150000, 1500000, 250, 2000, NULL, 40, 250, 0);

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `lottery`
--

CREATE TABLE `lottery` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `luckyboxes`
--

CREATE TABLE `luckyboxes` (
  `boxnumber` int(11) NOT NULL,
  `playerid` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `maillog`
--

CREATE TABLE `maillog` (
  `id` int(11) NOT NULL,
  `to` varchar(75) NOT NULL DEFAULT '',
  `from` varchar(75) NOT NULL DEFAULT '',
  `timesent` int(11) NOT NULL DEFAULT 0,
  `subject` text NOT NULL,
  `msgtext` text NOT NULL,
  `reported` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `messages`
--

CREATE TABLE `messages` (
  `id` int(11) UNSIGNED NOT NULL,
  `chat_id` int(11) UNSIGNED NOT NULL,
  `sender_id` int(11) UNSIGNED DEFAULT NULL,
  `type` enum('text','system','image','file','audio','video','poll') NOT NULL DEFAULT 'text',
  `body` mediumtext DEFAULT NULL,
  `body_plain` mediumtext DEFAULT NULL,
  `metadata_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata_json`)),
  `reply_to_message_id` int(11) UNSIGNED DEFAULT NULL,
  `edited_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `message_reads`
--

CREATE TABLE `message_reads` (
  `message_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `read_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `mission`
--

CREATE TABLE `mission` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `crimes` int(11) NOT NULL,
  `kills` int(11) NOT NULL,
  `mugs` int(11) NOT NULL,
  `busts` int(11) NOT NULL,
  `backalleys` int(11) NOT NULL,
  `raids` int(11) NOT NULL DEFAULT 0,
  `payCrimes` int(11) NOT NULL,
  `payKills` int(11) NOT NULL,
  `payMugs` int(11) NOT NULL,
  `payBusts` int(11) NOT NULL,
  `payBackalleys` int(11) NOT NULL,
  `payRaids` int(11) NOT NULL DEFAULT 0,
  `time` int(11) NOT NULL,
  `between` int(11) NOT NULL,
  `category` int(11) NOT NULL,
  `exp_level` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Data dump for tabellen `mission`
--

INSERT INTO `mission` (`id`, `name`, `crimes`, `kills`, `mugs`, `busts`, `backalleys`, `raids`, `payCrimes`, `payKills`, `payMugs`, `payBusts`, `payBackalleys`, `payRaids`, `time`, `between`, `category`, `exp_level`) VALUES
(1, 'Starter Mission', 500, 125, 100, 0, 0, 0, 400, 1250, 650, 0, 0, 0, 86400, 86400, 0, 30),
(2, 'Rookie Mission', 1250, 250, 150, 50, 0, 0, 750, 2000, 750, 200, 0, 0, 86400, 86400, 0, 35),
(3, 'Basic Mission', 3750, 500, 300, 200, 0, 0, 1250, 3500, 1150, 5000, 0, 0, 86400, 86400, 0, 40),
(4, 'Normal Mission', 10000, 675, 400, 200, 0, 0, 3750, 6000, 2500, 5000, 0, 0, 86400, 86400, 0, 45),
(5, 'Hardened Mission', 17500, 1000, 500, 300, 0, 0, 8000, 14000, 4000, 7000, 0, 0, 86400, 86400, 0, 60),
(6, 'Veteran Mission', 27500, 1875, 750, 500, 0, 0, 12500, 18500, 6000, 10000, 0, 0, 86400, 86400, 0, 70),
(7, 'Impossible Mission', 40000, 2500, 1500, 1000, 0, 0, 15000, 25000, 12000, 20000, 0, 0, 86400, 86400, 0, 100),
(8, 'Starter Kills', 0, 50, 0, 0, 0, 0, 0, 500, 0, 0, 0, 0, 86400, 86400, 1, 30),
(9, 'Rookie Kills', 0, 100, 0, 0, 0, 0, 0, 1100, 0, 0, 0, 0, 86400, 86400, 1, 35),
(10, 'Basic Kills', 0, 125, 0, 0, 0, 0, 0, 1700, 0, 0, 0, 0, 86400, 86400, 1, 40),
(11, 'Normal Kills', 0, 350, 0, 0, 0, 0, 0, 7000, 0, 0, 0, 0, 86400, 86400, 1, 45),
(12, 'Hardened Kills', 0, 700, 0, 0, 0, 0, 0, 15000, 0, 0, 0, 0, 86400, 86400, 1, 50),
(13, 'Veteran Kills', 0, 2500, 0, 0, 0, 0, 0, 35000, 0, 0, 0, 0, 86400, 86400, 1, 60),
(14, 'Impossible Kills', 0, 5000, 0, 0, 0, 0, 0, 75000, 0, 0, 0, 0, 86400, 86400, 1, 70),
(15, 'Starter Crimes', 500, 0, 0, 0, 0, 0, 500, 0, 0, 0, 0, 0, 86400, 86400, 2, 30),
(16, 'Rookie Crimes', 5000, 0, 0, 0, 0, 0, 1500, 0, 0, 0, 0, 0, 86400, 86400, 2, 35),
(17, 'Basic Crimes', 20000, 0, 0, 0, 0, 0, 5000, 0, 0, 0, 0, 0, 86400, 86400, 2, 40),
(18, 'Normal Crimes', 40000, 0, 0, 0, 0, 0, 10000, 0, 0, 0, 0, 0, 86400, 86400, 2, 45),
(19, 'Hardened Crimes', 75000, 0, 0, 0, 0, 0, 22000, 0, 0, 0, 0, 0, 86400, 86400, 2, 50),
(20, 'Veteran Crimes', 200000, 0, 0, 0, 0, 0, 60000, 0, 0, 0, 0, 0, 86400, 86400, 2, 60),
(21, 'Impossible Crimes', 350000, 0, 0, 0, 0, 0, 110000, 0, 0, 0, 0, 0, 86400, 86400, 2, 70),
(22, 'Starter Busts', 0, 0, 0, 100, 0, 0, 0, 0, 0, 2000, 0, 0, 86400, 86400, 3, 30),
(23, 'Rookie Busts', 0, 0, 0, 250, 0, 0, 0, 0, 0, 4000, 0, 0, 86400, 86400, 3, 35),
(24, 'Basic Busts', 0, 0, 0, 500, 0, 0, 0, 0, 0, 8500, 0, 0, 86400, 86400, 3, 40),
(25, 'Normal Busts', 0, 0, 0, 1000, 0, 0, 0, 0, 0, 18000, 0, 0, 86400, 86400, 3, 45),
(26, 'Hardened Busts', 0, 0, 0, 2500, 0, 0, 0, 0, 0, 35000, 0, 0, 86400, 86400, 3, 50),
(27, 'Veteran Busts', 0, 0, 0, 4000, 0, 0, 0, 0, 0, 60000, 0, 0, 86400, 86400, 3, 60),
(28, 'Starter Mugs', 0, 0, 100, 0, 0, 0, 0, 0, 400, 0, 0, 0, 86400, 86400, 5, 30),
(29, 'Rookie Mugs', 0, 0, 250, 0, 0, 0, 0, 0, 1000, 0, 0, 0, 86400, 86400, 5, 35),
(30, 'Normal Mugs', 0, 0, 500, 0, 0, 0, 0, 0, 4000, 0, 0, 0, 86400, 86400, 5, 40),
(31, 'Hardened Mugs', 0, 0, 1000, 0, 0, 0, 0, 0, 9000, 0, 0, 0, 86400, 86400, 5, 45),
(32, 'Veteran Mugs', 0, 0, 2500, 0, 0, 0, 0, 0, 18000, 0, 0, 0, 86400, 86400, 5, 50),
(33, 'Impossible Mugs', 0, 0, 5000, 0, 0, 0, 0, 0, 40000, 0, 0, 0, 86400, 86400, 5, 70),
(34, 'Starter Backalleys', 0, 0, 0, 0, 50, 0, 0, 0, 0, 0, 500, 0, 86400, 86400, 6, 30),
(35, 'Rookie Backalleys', 0, 0, 0, 0, 100, 0, 0, 0, 0, 0, 1000, 0, 86400, 86400, 6, 35),
(36, 'Normal Backalleys', 0, 0, 0, 0, 250, 0, 0, 0, 0, 0, 2500, 0, 86400, 86400, 6, 40),
(37, 'Hardened Backalleys', 0, 0, 0, 0, 500, 0, 0, 0, 0, 0, 10000, 0, 86400, 86400, 6, 45),
(38, 'Veteran Backalleys', 0, 0, 0, 0, 1000, 0, 0, 0, 0, 0, 15000, 0, 86400, 86400, 6, 50),
(39, 'Impossible Backalleys', 0, 0, 0, 0, 2500, 0, 0, 0, 0, 0, 25000, 0, 86400, 86400, 6, 60),
(40, 'Impossible Busts', 0, 0, 0, 6000, 0, 0, 0, 0, 0, 80000, 0, 0, 86400, 86400, 3, 70),
(41, 'Unthinkable Mission', 50000, 3500, 2000, 2000, 0, 0, 25000, 35000, 15000, 30000, 0, 0, 86400, 86400, 0, 150),
(42, 'Starter Raids', 0, 0, 0, 0, 0, 10, 0, 0, 0, 0, 0, 500, 86400, 86400, 7, 5),
(43, 'Rookie Raids', 0, 0, 0, 0, 0, 50, 0, 0, 0, 0, 0, 1000, 86400, 86400, 7, 10),
(44, 'Normal Raids', 0, 0, 0, 0, 0, 150, 0, 0, 0, 0, 0, 2500, 86400, 86400, 7, 15),
(45, 'Hardened Raids', 0, 0, 0, 0, 0, 250, 0, 0, 0, 0, 0, 5000, 86400, 86400, 7, 20),
(46, 'Veteran Raids', 0, 0, 0, 0, 0, 500, 0, 0, 0, 0, 0, 10000, 86400, 86400, 7, 30),
(47, 'Impossible Raids', 0, 0, 0, 0, 0, 1000, 0, 0, 0, 0, 0, 20000, 86400, 86400, 7, 40);

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `missionlog`
--

CREATE TABLE `missionlog` (
  `id` int(11) NOT NULL,
  `text` text NOT NULL,
  `timestamp` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `missions`
--

CREATE TABLE `missions` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `crimes` int(11) NOT NULL DEFAULT 0,
  `mugs` int(11) NOT NULL DEFAULT 0,
  `kills` int(11) NOT NULL DEFAULT 0,
  `busts` int(11) NOT NULL DEFAULT 0,
  `backalleys` int(11) NOT NULL DEFAULT 0,
  `raids` int(11) NOT NULL DEFAULT 0,
  `timestamp` int(11) NOT NULL,
  `mid` int(11) NOT NULL,
  `completed` enum('successful','failed','no','cancel') NOT NULL DEFAULT 'no',
  `partner` int(11) NOT NULL DEFAULT 0,
  `crimes_paid` int(11) NOT NULL DEFAULT 0,
  `kills_paid` int(11) NOT NULL DEFAULT 0,
  `mugs_paid` int(11) NOT NULL DEFAULT 0,
  `busts_paid` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `missions_in_progress`
--

CREATE TABLE `missions_in_progress` (
  `id` int(11) NOT NULL,
  `mid` mediumint(9) NOT NULL,
  `userid` smallint(6) NOT NULL,
  `requirements` varchar(255) NOT NULL,
  `done` varchar(255) NOT NULL DEFAULT '',
  `status` enum('inprogress','completed','cancelled') NOT NULL DEFAULT 'inprogress',
  `timestamp` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `mission_count_tracking`
--

CREATE TABLE `mission_count_tracking` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `count` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `mission_daily_payout_logs`
--

CREATE TABLE `mission_daily_payout_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` varchar(200) NOT NULL,
  `missions_complete` int(11) NOT NULL DEFAULT 0,
  `total_points_earned` int(11) NOT NULL DEFAULT 0,
  `total_profit_earned` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `mlottowinners`
--

CREATE TABLE `mlottowinners` (
  `id` smallint(6) NOT NULL,
  `userid` smallint(6) NOT NULL,
  `won` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `moth`
--

CREATE TABLE `moth` (
  `ID` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `kills` int(11) NOT NULL,
  `time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `muglog`
--

CREATE TABLE `muglog` (
  `id` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `mugger` int(11) NOT NULL,
  `mugged` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `active` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `multi`
--

CREATE TABLE `multi` (
  `id` int(11) NOT NULL,
  `acc1` int(11) DEFAULT NULL,
  `acc2` int(11) NOT NULL,
  `time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `newmissions`
--

CREATE TABLE `newmissions` (
  `id` mediumint(9) NOT NULL,
  `requirements` varchar(255) NOT NULL,
  `type` varchar(25) NOT NULL,
  `name` varchar(50) NOT NULL,
  `tounlock` mediumint(9) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `new_halloween_payout_logs`
--

CREATE TABLE `new_halloween_payout_logs` (
  `id` int(11) NOT NULL,
  `find_date` varchar(255) NOT NULL,
  `item` varchar(200) NOT NULL,
  `count` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `new_tournaments`
--

CREATE TABLE `new_tournaments` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `max_players` int(11) NOT NULL,
  `current_status` enum('Registration','Ongoing','Finished') NOT NULL,
  `winner_id` int(11) DEFAULT NULL,
  `creation_date` datetime NOT NULL,
  `start_time` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `numbergame`
--

CREATE TABLE `numbergame` (
  `number` int(11) NOT NULL,
  `userid` int(11) NOT NULL DEFAULT 0,
  `numbers` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `ofthes`
--

CREATE TABLE `ofthes` (
  `userid` smallint(6) NOT NULL,
  `baotd` mediumint(9) NOT NULL DEFAULT 0,
  `botd` mediumint(9) NOT NULL DEFAULT 0,
  `motd` int(11) NOT NULL DEFAULT 0,
  `kotd` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `operations`
--

CREATE TABLE `operations` (
  `id` int(11) NOT NULL,
  `category` varchar(255) NOT NULL,
  `crimes` bigint(20) NOT NULL,
  `mugs` bigint(20) NOT NULL,
  `busts` bigint(20) NOT NULL,
  `online_attacks` bigint(20) NOT NULL,
  `offline_attacks` bigint(20) NOT NULL,
  `full_energy_trains` bigint(20) NOT NULL,
  `city_boss_wins` bigint(20) NOT NULL,
  `backalleys` bigint(20) NOT NULL,
  `raids` bigint(20) NOT NULL DEFAULT 0,
  `money_reward` bigint(20) NOT NULL,
  `points_reward` bigint(20) NOT NULL,
  `exp_reward` bigint(20) NOT NULL,
  `premium_cost` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Data dump for tabellen `operations`
--

INSERT INTO `operations` (`id`, `category`, `crimes`, `mugs`, `busts`, `online_attacks`, `offline_attacks`, `full_energy_trains`, `city_boss_wins`, `backalleys`, `raids`, `money_reward`, `points_reward`, `exp_reward`, `premium_cost`) VALUES
(1, 'crimes_points', 1000, 0, 0, 0, 0, 0, 0, 0, 0, 0, 250, 50, 0),
(3, 'crimes_points', 5000, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1250, 50, 0),
(4, 'crimes_points', 20000, 0, 0, 0, 0, 0, 0, 0, 0, 0, 6250, 50, 0),
(5, 'crimes_points', 75000, 0, 0, 0, 0, 0, 0, 0, 0, 0, 25000, 50, 0),
(6, 'crimes_points', 400000, 0, 0, 0, 0, 0, 0, 0, 0, 0, 80000, 50, 0),
(7, 'crimes_points', 750000, 0, 0, 0, 0, 0, 0, 0, 0, 0, 160000, 50, 0),
(8, 'crimes_points', 1200000, 0, 0, 0, 0, 0, 0, 0, 0, 0, 300000, 50, 0),
(9, 'crimes_cash', 1000, 0, 0, 0, 0, 0, 0, 0, 0, 500000, 0, 50, 0),
(10, 'crimes_cash', 5000, 0, 0, 0, 0, 0, 0, 0, 0, 10000000, 0, 50, 0),
(11, 'crimes_cash', 20000, 0, 0, 0, 0, 0, 0, 0, 0, 12500000, 0, 50, 0),
(12, 'crimes_cash', 75000, 0, 0, 0, 0, 0, 0, 0, 0, 50000000, 0, 50, 0),
(13, 'crimes_cash', 350000, 0, 0, 0, 0, 0, 0, 0, 0, 160000000, 0, 50, 0),
(14, 'crimes_cash', 750000, 0, 0, 0, 0, 0, 0, 0, 0, 320000000, 0, 50, 0),
(15, 'crimes_cash', 1500000, 0, 0, 0, 0, 0, 0, 0, 0, 600000000, 0, 50, 0),
(16, 'mugs_points', 0, 1000, 0, 0, 0, 0, 0, 0, 0, 0, 2000, 50, 0),
(17, 'mugs_points', 0, 5000, 0, 0, 0, 0, 0, 0, 0, 0, 10000, 50, 0),
(18, 'mugs_points', 0, 10000, 0, 0, 0, 0, 0, 0, 0, 0, 20000, 50, 0),
(19, 'mugs_points', 0, 25000, 0, 0, 0, 0, 0, 0, 0, 0, 50000, 50, 0),
(20, 'mugs_points', 0, 50000, 0, 0, 0, 0, 0, 0, 0, 0, 100000, 50, 0),
(21, 'mugs_points', 0, 75000, 0, 0, 0, 0, 0, 0, 0, 0, 125000, 50, 0),
(22, 'mugs_points', 0, 90000, 0, 0, 0, 0, 0, 0, 0, 0, 150000, 50, 0),
(23, 'crimes_premium', 20000, 0, 0, 0, 0, 0, 0, 0, 0, 10000000, 6250, 100, 1),
(24, 'crimes_premium', 75000, 0, 0, 0, 0, 0, 0, 0, 0, 50000000, 25000, 100, 1),
(25, 'crimes_premium', 350000, 0, 0, 0, 0, 0, 0, 0, 0, 160000000, 80000, 100, 1),
(26, 'crimes_premium', 750000, 0, 0, 0, 0, 0, 0, 0, 0, 320000000, 160000, 100, 1),
(27, 'crimes_premium', 1500000, 0, 0, 0, 0, 0, 0, 0, 0, 600000000, 300000, 100, 1),
(28, 'mugs_cash', 0, 1000, 0, 0, 0, 0, 0, 0, 0, 2000000, 0, 50, 0),
(29, 'mugs_cash', 0, 5000, 0, 0, 0, 0, 0, 0, 0, 5000000, 0, 50, 0),
(30, 'mugs_cash', 0, 10000, 0, 0, 0, 0, 0, 0, 0, 20000000, 0, 50, 0),
(31, 'mugs_cash', 0, 25000, 0, 0, 0, 0, 0, 0, 0, 50000000, 0, 50, 0),
(32, 'mugs_cash', 0, 50000, 0, 0, 0, 0, 0, 0, 0, 100000000, 0, 50, 0),
(33, 'mugs_cash', 0, 75000, 0, 0, 0, 0, 0, 0, 0, 125000000, 0, 50, 0),
(34, 'mugs_cash', 0, 90000, 0, 0, 0, 0, 0, 0, 0, 150000000, 0, 50, 0),
(35, 'mugs_premium', 0, 1000, 0, 0, 0, 0, 0, 0, 0, 2000000, 2000, 100, 1),
(36, 'mugs_premium', 0, 5000, 0, 0, 0, 0, 0, 0, 0, 5000000, 10000, 100, 1),
(37, 'mugs_premium', 0, 10000, 0, 0, 0, 0, 0, 0, 0, 20000000, 20000, 100, 1),
(38, 'mugs_premium', 0, 25000, 0, 0, 0, 0, 0, 0, 0, 50000000, 50000, 100, 1),
(39, 'mugs_premium', 0, 50000, 0, 0, 0, 0, 0, 0, 0, 100000000, 100000, 100, 1),
(40, 'mugs_premium', 0, 75000, 0, 0, 0, 0, 0, 0, 0, 125000000, 125000, 100, 1),
(41, 'mugs_premium', 0, 90000, 0, 0, 0, 0, 0, 0, 0, 150000000, 150000, 100, 1),
(42, 'busts_points', 0, 0, 1000, 0, 0, 0, 0, 0, 0, 0, 2000, 50, 0),
(43, 'busts_points', 0, 0, 5000, 0, 0, 0, 0, 0, 0, 0, 10000, 50, 0),
(44, 'busts_points', 0, 0, 15000, 0, 0, 0, 0, 0, 0, 0, 30000, 50, 0),
(45, 'busts_points', 0, 0, 30000, 0, 0, 0, 0, 0, 0, 0, 60000, 50, 0),
(46, 'busts_points', 0, 0, 50000, 0, 0, 0, 0, 0, 0, 0, 100000, 50, 0),
(47, 'busts_points', 0, 0, 75000, 0, 0, 0, 0, 0, 0, 0, 125000, 50, 0),
(48, 'busts_points', 0, 0, 100000, 0, 0, 0, 0, 0, 0, 0, 150000, 50, 0),
(49, 'busts_premium', 0, 0, 1000, 0, 0, 0, 0, 0, 0, 2000000, 2000, 100, 1),
(50, 'busts_premium', 0, 0, 5000, 0, 0, 0, 0, 0, 0, 10000000, 10000, 100, 1),
(51, 'busts_premium', 0, 0, 15000, 0, 0, 0, 0, 0, 0, 30000000, 30000, 100, 1),
(52, 'busts_premium', 0, 0, 30000, 0, 0, 0, 0, 0, 0, 60000000, 60000, 100, 1),
(53, 'busts_premium', 0, 0, 50000, 0, 0, 0, 0, 0, 0, 100000000, 100000, 100, 1),
(54, 'busts_premium', 0, 0, 75000, 0, 0, 0, 0, 0, 0, 125000000, 125000, 100, 1),
(55, 'busts_premium', 0, 0, 100000, 0, 0, 0, 0, 0, 0, 150000000, 150000, 100, 1),
(56, 'busts_cash', 0, 0, 1000, 0, 0, 0, 0, 0, 0, 2000000, 0, 50, 0),
(57, 'busts_cash', 0, 0, 5000, 0, 0, 0, 0, 0, 0, 10000000, 0, 50, 0),
(58, 'busts_cash', 0, 0, 15000, 0, 0, 0, 0, 0, 0, 30000000, 0, 50, 0),
(59, 'busts_cash', 0, 0, 30000, 0, 0, 0, 0, 0, 0, 60000000, 0, 50, 0),
(60, 'busts_cash', 0, 0, 50000, 0, 0, 0, 0, 0, 0, 100000000, 0, 50, 0),
(61, 'busts_cash', 0, 0, 75000, 0, 0, 0, 0, 0, 0, 125000000, 0, 50, 0),
(62, 'busts_cash', 0, 0, 100000, 0, 0, 0, 0, 0, 0, 150000000, 0, 50, 0),
(63, 'online_attacks_premium', 0, 0, 0, 100, 0, 0, 0, 0, 0, 1000000, 1000, 100, 1),
(64, 'online_attacks_premium', 0, 0, 0, 500, 0, 0, 0, 0, 0, 5000000, 5000, 100, 1),
(65, 'online_attacks_premium', 0, 0, 0, 1000, 0, 0, 0, 0, 0, 10000000, 10000, 100, 1),
(66, 'online_attacks_premium', 0, 0, 0, 2500, 0, 0, 0, 0, 0, 25000000, 25000, 100, 1),
(67, 'online_attacks_premium', 0, 0, 0, 5000, 0, 0, 0, 0, 0, 50000000, 50000, 100, 1),
(68, 'online_attacks_premium', 0, 0, 0, 7500, 0, 0, 0, 0, 0, 75000000, 75000, 100, 1),
(69, 'online_attacks_premium', 0, 0, 0, 10000, 0, 0, 0, 0, 0, 100000000, 100000, 100, 1),
(70, 'online_attacks_points', 0, 0, 0, 100, 0, 0, 0, 0, 0, 0, 1000, 50, 0),
(71, 'online_attacks_points', 0, 0, 0, 500, 0, 0, 0, 0, 0, 0, 5000, 50, 0),
(72, 'online_attacks_points', 0, 0, 0, 1000, 0, 0, 0, 0, 0, 0, 10000, 50, 0),
(73, 'online_attacks_points', 0, 0, 0, 2500, 0, 0, 0, 0, 0, 0, 25000, 50, 0),
(74, 'online_attacks_points', 0, 0, 0, 5000, 0, 0, 0, 0, 0, 0, 50000, 50, 0),
(75, 'online_attacks_points', 0, 0, 0, 7500, 0, 0, 0, 0, 0, 0, 75000, 50, 0),
(76, 'online_attacks_points', 0, 0, 0, 10000, 0, 0, 0, 0, 0, 0, 100000, 50, 0),
(77, 'online_attacks_cash', 0, 0, 0, 100, 0, 0, 0, 0, 0, 1000000, 0, 50, 0),
(78, 'online_attacks_cash', 0, 0, 0, 500, 0, 0, 0, 0, 0, 5000000, 0, 50, 0),
(79, 'online_attacks_cash', 0, 0, 0, 1000, 0, 0, 0, 0, 0, 10000000, 0, 50, 0),
(80, 'online_attacks_cash', 0, 0, 0, 2500, 0, 0, 0, 0, 0, 25000000, 0, 50, 0),
(81, 'online_attacks_cash', 0, 0, 0, 5000, 0, 0, 0, 0, 0, 50000000, 0, 50, 0),
(82, 'online_attacks_cash', 0, 0, 0, 7500, 0, 0, 0, 0, 0, 75000000, 0, 50, 0),
(83, 'online_attacks_cash', 0, 0, 0, 10000, 0, 0, 0, 0, 0, 100000000, 0, 50, 0),
(84, 'backalley_premium', 0, 0, 0, 0, 0, 0, 0, 1000, 0, 5000000, 5000, 100, 1),
(85, 'backalley_premium', 0, 0, 0, 0, 0, 0, 0, 2500, 0, 10000000, 10000, 100, 1),
(86, 'backalley_premium', 0, 0, 0, 0, 0, 0, 0, 5000, 0, 20000000, 20000, 100, 1),
(87, 'backalley_premium', 0, 0, 0, 0, 0, 0, 0, 7500, 0, 30000000, 30000, 100, 1),
(88, 'backalley_premium', 0, 0, 0, 0, 0, 0, 0, 15000, 0, 60000000, 60000, 100, 1),
(89, 'backalley_premium', 0, 0, 0, 0, 0, 0, 0, 30000, 0, 100000000, 100000, 100, 1),
(90, 'backalley_premium', 0, 0, 0, 0, 0, 0, 0, 50000, 0, 150000000, 150000, 100, 1),
(91, 'backalley_points', 0, 0, 0, 0, 0, 0, 0, 1000, 0, 0, 5000, 50, 0),
(92, 'backalley_points', 0, 0, 0, 0, 0, 0, 0, 2500, 0, 0, 10000, 50, 0),
(93, 'backalley_points', 0, 0, 0, 0, 0, 0, 0, 5000, 0, 0, 20000, 50, 0),
(94, 'backalley_points', 0, 0, 0, 0, 0, 0, 0, 7500, 0, 0, 30000, 50, 0),
(95, 'backalley_points', 0, 0, 0, 0, 0, 0, 0, 15000, 0, 0, 60000, 50, 0),
(96, 'backalley_points', 0, 0, 0, 0, 0, 0, 0, 30000, 0, 0, 100000, 50, 0),
(97, 'backalley_points', 0, 0, 0, 0, 0, 0, 0, 50000, 0, 0, 150000, 50, 0),
(98, 'backalley_cash', 0, 0, 0, 0, 0, 0, 0, 1000, 0, 5000000, 0, 50, 0),
(99, 'backalley_cash', 0, 0, 0, 0, 0, 0, 0, 2500, 0, 10000000, 0, 50, 0),
(100, 'backalley_cash', 0, 0, 0, 0, 0, 0, 0, 5000, 0, 20000000, 0, 50, 0),
(101, 'backalley_cash', 0, 0, 0, 0, 0, 0, 0, 7500, 0, 30000000, 0, 50, 0),
(102, 'backalley_cash', 0, 0, 0, 0, 0, 0, 0, 15000, 0, 60000000, 0, 50, 0),
(103, 'backalley_cash', 0, 0, 0, 0, 0, 0, 0, 30000, 0, 100000000, 0, 50, 0),
(104, 'backalley_cash', 0, 0, 0, 0, 0, 0, 0, 50000, 0, 150000000, 0, 50, 0),
(105, 'raids_premium', 0, 0, 0, 0, 0, 0, 0, 0, 100, 1000000, 1000, 100, 1),
(106, 'raids_premium', 0, 0, 0, 0, 0, 0, 0, 0, 1000, 5000000, 5000, 100, 1),
(107, 'raids_premium', 0, 0, 0, 0, 0, 0, 0, 0, 1500, 10000000, 10000, 100, 1),
(108, 'raids_premium', 0, 0, 0, 0, 0, 0, 0, 0, 2500, 25000000, 25000, 100, 1),
(109, 'raids_premium', 0, 0, 0, 0, 0, 0, 0, 0, 3000, 50000000, 50000, 100, 1),
(110, 'raids_premium', 0, 0, 0, 0, 0, 0, 0, 0, 5000, 75000000, 75000, 100, 1),
(111, 'raids_premium', 0, 0, 0, 0, 0, 0, 0, 0, 10000, 100000000, 100000, 100, 1),
(112, 'raids_points', 0, 0, 0, 0, 0, 0, 0, 0, 100, 0, 1000, 50, 0),
(113, 'raids_points', 0, 0, 0, 0, 0, 0, 0, 0, 1000, 0, 5000, 50, 0),
(114, 'raids_points', 0, 0, 0, 0, 0, 0, 0, 0, 1500, 0, 10000, 50, 0),
(115, 'raids_points', 0, 0, 0, 0, 0, 0, 0, 0, 2500, 0, 25000, 50, 0),
(116, 'raids_points', 0, 0, 0, 0, 0, 0, 0, 0, 3000, 0, 50000, 50, 0),
(117, 'raids_points', 0, 0, 0, 0, 0, 0, 0, 0, 5000, 0, 75000, 50, 0),
(118, 'raids_points', 0, 0, 0, 0, 0, 0, 0, 0, 10000, 0, 100000, 50, 0),
(119, 'raids_cash', 0, 0, 0, 0, 0, 0, 0, 0, 100, 1000000, 0, 50, 0),
(120, 'raids_cash', 0, 0, 0, 0, 0, 0, 0, 0, 1000, 5000000, 0, 50, 0),
(121, 'raids_cash', 0, 0, 0, 0, 0, 0, 0, 0, 1500, 10000000, 0, 50, 0),
(122, 'raids_cash', 0, 0, 0, 0, 0, 0, 0, 0, 2500, 25000000, 0, 50, 0),
(123, 'raids_cash', 0, 0, 0, 0, 0, 0, 0, 0, 3000, 50000000, 0, 50, 0),
(124, 'raids_cash', 0, 0, 0, 0, 0, 0, 0, 0, 5000, 75000000, 0, 50, 0),
(125, 'raids_cash', 0, 0, 0, 0, 0, 0, 0, 0, 10000, 100000000, 0, 50, 0);

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `otdwinners`
--

CREATE TABLE `otdwinners` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `type` varchar(25) NOT NULL,
  `howmany` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `oth`
--

CREATE TABLE `oth` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `type` enum('leveler','killer') NOT NULL,
  `amnt` bigint(20) NOT NULL,
  `timestamp` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `ownedbusinesses`
--

CREATE TABLE `ownedbusinesses` (
  `ownership_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `business_id` int(11) NOT NULL,
  `purchase_date` datetime NOT NULL,
  `status` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `rating` int(11) NOT NULL,
  `employees` int(11) NOT NULL,
  `intelligence` int(11) NOT NULL,
  `cost` decimal(15,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `vault` int(11) NOT NULL DEFAULT 0,
  `earnedtoday` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `ownedproperties`
--

CREATE TABLE `ownedproperties` (
  `id` mediumint(9) NOT NULL,
  `userid` smallint(6) NOT NULL,
  `houseid` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `pack_logs`
--

CREATE TABLE `pack_logs` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `pack` text NOT NULL,
  `credits_before` int(11) NOT NULL,
  `credits_now` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `pagetracker`
--

CREATE TABLE `pagetracker` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `page` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `payment_tracker`
--

CREATE TABLE `payment_tracker` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `amount` float NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'credits',
  `txn` varchar(255) NOT NULL,
  `time` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `personalnotes`
--

CREATE TABLE `personalnotes` (
  `noter` mediumint(9) NOT NULL,
  `noted` mediumint(9) NOT NULL,
  `note` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `petcrimes`
--

CREATE TABLE `petcrimes` (
  `id` int(11) NOT NULL,
  `name` varchar(40) NOT NULL,
  `nerve` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Data dump for tabellen `petcrimes`
--

INSERT INTO `petcrimes` (`id`, `name`, `nerve`) VALUES
(1, 'Steal A Newspaper', 1),
(2, 'Bite Another Pet', 2),
(3, 'Steal Pet Food', 3),
(4, 'Attack A Police Dog', 4),
(5, 'Kill A Cat', 5),
(16, 'Kill a Grandma', 100),
(15, 'Kill Another Pet', 75),
(8, 'Bite a Teen', 10),
(9, 'Bite a Postman', 15),
(10, 'Take a cute selfie', 20),
(11, 'Attack the Admin', 25),
(12, 'Attack a Grandma', 30),
(13, 'Attack a Police Officer', 45),
(14, 'Attack Owner', 50),
(17, 'Kill Owner', 125),
(18, 'Kill a Mobster', 150),
(19, 'Kill a Mob Boss', 200);

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `pethouses`
--

CREATE TABLE `pethouses` (
  `id` int(11) NOT NULL,
  `name` varchar(75) NOT NULL DEFAULT '',
  `awake` int(11) NOT NULL DEFAULT 0,
  `cost` bigint(20) NOT NULL DEFAULT 0,
  `buyable` int(11) NOT NULL DEFAULT 1
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Data dump for tabellen `pethouses`
--

INSERT INTO `pethouses` (`id`, `name`, `awake`, `cost`, `buyable`) VALUES
(2, 'Medium Cage', 500, 2500000, 1),
(1, 'Small Cage', 250, 150000, 1),
(44, 'Large Cage', 750, 25000000, 1),
(45, 'Small Outhouse', 1000, 250000000, 1),
(46, 'Medium Outhouse', 1500, 850000000, 1);

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `petladder`
--

CREATE TABLE `petladder` (
  `id` int(11) NOT NULL,
  `pet_id` int(11) NOT NULL,
  `attacks` int(11) NOT NULL DEFAULT 0,
  `exp` int(11) NOT NULL DEFAULT 0,
  `gym` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `petmarket`
--

CREATE TABLE `petmarket` (
  `petid` int(11) NOT NULL,
  `userid` smallint(5) UNSIGNED NOT NULL,
  `cost` bigint(20) NOT NULL,
  `currency` enum('money','points') NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `pets`
--

CREATE TABLE `pets` (
  `id` int(11) NOT NULL,
  `petid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `level` int(11) NOT NULL DEFAULT 1,
  `exp` int(11) NOT NULL DEFAULT 0,
  `energy` int(11) NOT NULL DEFAULT 10,
  `nerve` int(11) NOT NULL DEFAULT 5,
  `awake` int(11) NOT NULL DEFAULT 100,
  `house` int(11) NOT NULL DEFAULT 0,
  `str` int(11) NOT NULL DEFAULT 10,
  `spe` int(11) NOT NULL DEFAULT 10,
  `def` int(11) NOT NULL DEFAULT 10,
  `pname` varchar(25) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'No Name',
  `hp` int(11) NOT NULL DEFAULT 50,
  `jail` int(11) NOT NULL DEFAULT 0,
  `busts` int(11) NOT NULL DEFAULT 0,
  `hospital` int(11) NOT NULL DEFAULT 0,
  `leash` int(11) NOT NULL DEFAULT 0,
  `raid_leash` tinyint(1) NOT NULL DEFAULT 0,
  `drugstime` int(11) NOT NULL DEFAULT 0,
  `maxawake` int(11) NOT NULL DEFAULT 100,
  `coloredname` varchar(15) NOT NULL DEFAULT 'FFFFFF|FFFFFF',
  `attacksWon` int(11) NOT NULL DEFAULT 0,
  `attacksLost` int(11) NOT NULL DEFAULT 0,
  `loaned` int(11) NOT NULL DEFAULT 0,
  `onmarket` tinyint(1) NOT NULL DEFAULT 0,
  `nerref` tinyint(1) NOT NULL DEFAULT 0,
  `nerreftime` int(11) NOT NULL DEFAULT 0,
  `avi` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `petshop`
--

CREATE TABLE `petshop` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `picture` varchar(50) NOT NULL DEFAULT '/images/xxx.png',
  `str` int(11) NOT NULL DEFAULT 10,
  `spe` int(11) NOT NULL DEFAULT 10,
  `def` int(11) NOT NULL DEFAULT 10,
  `cost` int(11) NOT NULL DEFAULT 500000
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Data dump for tabellen `petshop`
--

INSERT INTO `petshop` (`id`, `name`, `picture`, `str`, `spe`, `def`, `cost`) VALUES
(1, 'Lion', 'images/lion.png', 10, 10, 10, 500000),
(2, 'Rhino', 'images/rhino.png', 10, 10, 10, 500000),
(4, 'Horse', 'images/horse.png', 10, 10, 10, 500000),
(5, 'Tiger', 'images/tiger.png', 10, 10, 10, 500000),
(6, 'Gorilla', 'images/gorilla.png', 10, 10, 10, 500000),
(8, 'Elephant', 'images/elephant.png', 10, 10, 10, 500000);

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `pettracks`
--

CREATE TABLE `pettracks` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `petid` varchar(255) NOT NULL,
  `cashbet` int(11) NOT NULL,
  `petspeed` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `planes`
--

CREATE TABLE `planes` (
  `id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `image` varchar(40) NOT NULL,
  `description` text NOT NULL,
  `cost` int(11) NOT NULL,
  `buyable` int(11) NOT NULL DEFAULT 1
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Data dump for tabellen `planes`
--

INSERT INTO `planes` (`id`, `name`, `image`, `description`, `cost`, `buyable`) VALUES
(1, 'Piper Cub', 'images/Airplanes/Piper Cub.png', 'An airplane to travel to different cities.', 800000, 1),
(2, 'FA-18 Hornet', 'images/Airplanes/FA-18 Hornet.png', 'An airplane to travel to different cities.', 1400000, 1),
(3, 'Concorde', 'images/Airplanes/Concorde.png', 'An airplane to travel to different cities.', 1950000, 1),
(4, 'Private Jet', 'images/Airplanes/Private Jet.png', 'An airplane to travel to different cities.', 2500000, 1),
(5, 'F-117A Nighthawk', 'images/Airplanes/F-117A Nighthawk.png', 'An airplane to travel to different cities.', 3250000, 1),
(6, 'Lockheed AC-130', 'images/Airplanes/Lockheed AC-130.png', 'An airplane to travel to different cities.', 4600000, 1);

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `plottowinners`
--

CREATE TABLE `plottowinners` (
  `id` smallint(6) NOT NULL,
  `userid` smallint(6) NOT NULL,
  `won` mediumint(9) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `pms`
--

CREATE TABLE `pms` (
  `id` int(11) NOT NULL,
  `to` int(11) NOT NULL,
  `from` int(11) NOT NULL,
  `timesent` int(11) NOT NULL DEFAULT 0,
  `subject` text NOT NULL,
  `msgtext` text NOT NULL,
  `reported` tinyint(1) NOT NULL DEFAULT 0,
  `viewed` tinyint(1) NOT NULL DEFAULT 1,
  `parent` int(11) NOT NULL DEFAULT 0,
  `bomb` tinyint(1) NOT NULL DEFAULT 0,
  `bombed` tinyint(1) NOT NULL DEFAULT 0,
  `check` tinyint(1) NOT NULL DEFAULT 0,
  `starred` tinyint(1) NOT NULL DEFAULT 0,
  `outboxhidden` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `pointsmarket`
--

CREATE TABLE `pointsmarket` (
  `owner` int(11) NOT NULL DEFAULT 0,
  `amount` bigint(20) NOT NULL DEFAULT 0,
  `price` bigint(20) NOT NULL DEFAULT 0,
  `type` int(11) NOT NULL,
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `poker_livechat`
--

CREATE TABLE `poker_livechat` (
  `gameID` int(11) NOT NULL DEFAULT 0,
  `updatescreen` int(11) DEFAULT 0,
  `c1` text DEFAULT NULL,
  `c2` text DEFAULT NULL,
  `c3` text DEFAULT NULL,
  `c4` text DEFAULT NULL,
  `c5` text DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `poker_players`
--

CREATE TABLE `poker_players` (
  `ID` int(11) NOT NULL,
  `username` varchar(12) DEFAULT '',
  `email` varchar(70) DEFAULT '',
  `password` varchar(40) DEFAULT '',
  `avatar` varchar(80) DEFAULT '',
  `datecreated` int(11) DEFAULT 0,
  `lastlogin` int(11) DEFAULT 0,
  `ipaddress` varchar(20) DEFAULT '',
  `sessname` varchar(32) DEFAULT '',
  `banned` tinyint(1) DEFAULT 0,
  `approve` tinyint(1) DEFAULT 0,
  `lastmove` int(11) DEFAULT 0,
  `waitimer` int(11) DEFAULT 0,
  `code` varchar(16) DEFAULT '',
  `GUID` varchar(32) DEFAULT '',
  `vID` int(11) DEFAULT 0,
  `gID` int(11) DEFAULT 0,
  `timetag` int(11) DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Data dump for tabellen `poker_players`
--

INSERT INTO `poker_players` (`ID`, `username`, `email`, `password`, `avatar`, `datecreated`, `lastlogin`, `ipaddress`, `sessname`, `banned`, `approve`, `lastmove`, `waitimer`, `code`, `GUID`, `vID`, `gID`, `timetag`) VALUES
(10, 'Chaos', '', '', '', 0, 0, '', '', 0, 0, 1725649279, 1725649301, '', '1', 0, 0, 1725649291),
(12, 'Xazin', '', '', '', 0, 0, '', '', 0, 0, 1743602271, 0, '', '1059', 18, 0, 1743602720),
(11, 'DaveTheDon', '', '', '', 0, 0, '', '', 0, 0, 1725649269, 1725636507, '', '180', 7, 0, 1725649286),
(13, 'herion', '', '', '', 0, 0, '', '', 0, 0, 0, 0, '', '1034', 0, 0, 0),
(14, 'Jafii', '', '', '', 0, 0, '', '', 0, 0, 1745782366, 1745782435, '', '1061', 0, 0, 1745782424);

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `poker_poker`
--

CREATE TABLE `poker_poker` (
  `gameID` int(11) NOT NULL,
  `tablename` varchar(64) DEFAULT '',
  `tabletype` varchar(1) DEFAULT '',
  `tournament_type` varchar(1) NOT NULL DEFAULT 'r',
  `tablelow` int(11) DEFAULT 0,
  `tablelimit` varchar(15) DEFAULT '',
  `sbamount` int(11) DEFAULT 100,
  `bbamount` int(11) DEFAULT 200,
  `blind_multiplier` decimal(5,2) NOT NULL DEFAULT 0.00,
  `ante` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  `ante_multiplier` decimal(5,2) NOT NULL DEFAULT 0.00,
  `tablestyle` varchar(20) DEFAULT '',
  `gamestyle` varchar(1) DEFAULT 't',
  `move` tinyint(4) DEFAULT 0,
  `dealer` tinyint(4) DEFAULT 0,
  `hand` tinyint(4) DEFAULT 0,
  `pot` int(11) DEFAULT 0,
  `bet` int(11) DEFAULT 0,
  `lastbet` varchar(15) DEFAULT '',
  `lastmove` int(11) DEFAULT 0,
  `card1` varchar(40) DEFAULT '',
  `card2` varchar(40) DEFAULT '',
  `card3` varchar(40) DEFAULT '',
  `card4` varchar(40) DEFAULT '',
  `card5` varchar(40) DEFAULT '',
  `p1name` varchar(12) DEFAULT '',
  `p1pot` varchar(10) DEFAULT '',
  `p1bet` varchar(10) DEFAULT '',
  `p1card1` varchar(40) DEFAULT '',
  `p1card2` varchar(40) DEFAULT '',
  `p2name` varchar(12) DEFAULT '',
  `p2pot` varchar(10) DEFAULT '',
  `p2bet` varchar(10) DEFAULT '',
  `p2card1` varchar(40) DEFAULT '',
  `p2card2` varchar(40) DEFAULT '',
  `p3name` varchar(12) DEFAULT '',
  `p3pot` varchar(10) DEFAULT '',
  `p3bet` varchar(10) DEFAULT '',
  `p3card1` varchar(40) DEFAULT '',
  `p3card2` varchar(40) DEFAULT '',
  `p4name` varchar(12) DEFAULT '',
  `p4pot` varchar(10) DEFAULT '',
  `p4bet` varchar(10) DEFAULT '',
  `p4card1` varchar(40) DEFAULT '',
  `p4card2` varchar(40) DEFAULT '',
  `p5name` varchar(12) DEFAULT '',
  `p5pot` varchar(10) DEFAULT '',
  `p5bet` varchar(10) DEFAULT '',
  `p5card1` varchar(40) DEFAULT '',
  `p5card2` varchar(40) DEFAULT '',
  `p6name` varchar(12) DEFAULT '',
  `p6pot` varchar(10) DEFAULT '',
  `p6bet` varchar(10) DEFAULT '',
  `p6card1` varchar(40) DEFAULT '',
  `p6card2` varchar(40) DEFAULT '',
  `p7name` varchar(12) DEFAULT '',
  `p7pot` varchar(10) DEFAULT '',
  `p7bet` varchar(10) DEFAULT '',
  `p7card1` varchar(40) DEFAULT '',
  `p7card2` varchar(40) DEFAULT '',
  `p8name` varchar(12) DEFAULT '',
  `p8pot` varchar(10) DEFAULT '',
  `p8bet` varchar(10) DEFAULT '',
  `p8card1` varchar(40) DEFAULT '',
  `p8card2` varchar(40) DEFAULT '',
  `p9name` varchar(12) DEFAULT '',
  `p9pot` varchar(10) DEFAULT '',
  `p9bet` varchar(10) DEFAULT '',
  `p9card1` varchar(40) DEFAULT '',
  `p9card2` varchar(40) DEFAULT '',
  `p10name` varchar(12) DEFAULT '',
  `p10pot` varchar(10) DEFAULT '',
  `p10bet` varchar(10) DEFAULT '',
  `p10card1` varchar(40) DEFAULT '',
  `p10card2` varchar(40) DEFAULT '',
  `msg` varchar(150) DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `poker_settings`
--

CREATE TABLE `poker_settings` (
  `setting` varchar(12) DEFAULT '',
  `Xkey` varchar(12) DEFAULT '',
  `Xvalue` text DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `poker_sitelog`
--

CREATE TABLE `poker_sitelog` (
  `ID` int(10) UNSIGNED NOT NULL,
  `player` varchar(12) NOT NULL DEFAULT '',
  `log` text NOT NULL,
  `dt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `poker_stats`
--

CREATE TABLE `poker_stats` (
  `ID` int(11) NOT NULL,
  `player` varchar(12) DEFAULT '',
  `rank` varchar(12) DEFAULT '',
  `winpot` int(11) DEFAULT 0,
  `gamesplayed` int(11) DEFAULT 0,
  `tournamentsplayed` int(11) DEFAULT 0,
  `tournamentswon` int(11) DEFAULT 0,
  `handsplayed` int(11) DEFAULT 0,
  `handswon` int(11) DEFAULT 0,
  `bet` int(11) DEFAULT 0,
  `checked` int(11) DEFAULT 0,
  `called` varchar(11) DEFAULT '0',
  `allin` varchar(11) DEFAULT '0',
  `fold_pf` varchar(11) DEFAULT '0',
  `fold_f` varchar(11) DEFAULT '0',
  `fold_t` varchar(11) DEFAULT '0',
  `fold_r` int(11) DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `poker_styles`
--

CREATE TABLE `poker_styles` (
  `style_id` int(11) NOT NULL,
  `style_name` varchar(20) DEFAULT '',
  `style_lic` varchar(60) DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `poker_userchat`
--

CREATE TABLE `poker_userchat` (
  `gameID` int(11) NOT NULL DEFAULT 0,
  `updatescreen` int(11) DEFAULT 0,
  `c1` text DEFAULT NULL,
  `c2` text DEFAULT NULL,
  `c3` text DEFAULT NULL,
  `c4` text DEFAULT NULL,
  `c5` text DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `poll`
--

CREATE TABLE `poll` (
  `ID` int(11) NOT NULL,
  `question` text NOT NULL,
  `1` varchar(255) NOT NULL,
  `2` varchar(255) NOT NULL,
  `3` varchar(255) NOT NULL,
  `4` varchar(255) NOT NULL,
  `1_r` int(11) NOT NULL,
  `2_r` int(11) NOT NULL,
  `3_r` int(11) NOT NULL,
  `4_r` int(11) NOT NULL,
  `end` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `poll1`
--

CREATE TABLE `poll1` (
  `optionid` int(11) NOT NULL,
  `optionname` varchar(500) NOT NULL,
  `votes` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `polls`
--

CREATE TABLE `polls` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `options` varchar(255) NOT NULL,
  `votes` varchar(255) NOT NULL,
  `voters` int(11) NOT NULL DEFAULT 0,
  `finish` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `poll_votes`
--

CREATE TABLE `poll_votes` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `option` enum('1','2','3','4') NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `tid` int(11) NOT NULL,
  `replyto` int(11) NOT NULL,
  `fid` int(11) NOT NULL,
  `subject` varchar(120) NOT NULL,
  `uid` int(11) NOT NULL,
  `createdat` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `message` text NOT NULL,
  `ipaddress` varchar(30) DEFAULT NULL,
  `longipaddress` int(11) DEFAULT NULL,
  `edituid` int(11) DEFAULT NULL,
  `edittime` timestamp NULL DEFAULT NULL,
  `visible` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `prestige_levels`
--

CREATE TABLE `prestige_levels` (
  `id` int(11) NOT NULL,
  `level` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `prestige_skull`
--

CREATE TABLE `prestige_skull` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `skull` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `profile_actions`
--

CREATE TABLE `profile_actions` (
  `id` int(11) NOT NULL,
  `short_text` varchar(255) NOT NULL,
  `confirm_text` varchar(255) NOT NULL,
  `event_text` varchar(255) NOT NULL,
  `active` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Data dump for tabellen `profile_actions`
--

INSERT INTO `profile_actions` (`id`, `short_text`, `confirm_text`, `event_text`, `active`) VALUES
(1, 'Slap Them!', 'You have slapped {name} on the head! I bet that hurt.', '{attacker} has just bitch slapped you on the back of the head!', 1),
(2, 'Handcuff Them!', 'You Grab {name} and put them in handcuffs', 'Oh No, {attacker} snuck up behind you and put handcuffs on you!', 1),
(3, 'Hug Them!', 'You Grab {name} and hug them tightly!\n', '{attacker} walks up to you and hugs you tightly!\r\n\r\n', 1),
(4, 'Grab Ass', 'You Sneak up to {name} and take a good feel of that firm ass\r\n\r\n\r\n', '{attacker} Sneaks up to you and takes a good feel of that firm ass!\r\n\r\n', 1),
(5, 'Ignore them!', 'You turn around and completely walk away from {name}.\r\n\r\n\r\n', '{attacker} just turns around and walks away, completely ignoring you!\r\n', 1);

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `protection_suits`
--

CREATE TABLE `protection_suits` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `cost` int(11) NOT NULL,
  `protection` int(11) NOT NULL,
  `cooldown` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `ptslottery`
--

CREATE TABLE `ptslottery` (
  `userid` int(11) NOT NULL,
  `tickets` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `quest_season`
--

CREATE TABLE `quest_season` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `is_active` int(11) NOT NULL DEFAULT 0,
  `medal` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Data dump for tabellen `quest_season`
--

INSERT INTO `quest_season` (`id`, `name`, `description`, `is_active`, `medal`) VALUES
(1, 'Family Business', 'Welcome to the family. The boss, Don Luca Moretti, has some \"family business\" that needs handling. Before he puts you to the task, he\'s got a series of tasks to gauge your reliability & loyalty.', 1, 'family-business-medal.png'),
(2, 'Rise of the Shadow Boss', 'A power struggle brewing within the Moretti family. Help The Don uncover the treacherous plans and secure the families future.', 1, 'shadow-boss-medal.png'),
(3, 'Blood in the Boardroom', 'The Moretti family is expanding its reach into legitimate businesses — but not everyone in the corporate world plays fair. Don Luca needs a reliable fixer to handle hostile takeovers, sabotage competitors, and deal with boardroom traitors.', 1, 'blood-in-the-boardroom.png'),
(4, 'Turf Wars', 'The Moretti family is pushing into rival gang territory. Every block taken is a statement. But nothing is given — it’s earned in blood and bullets.', 0, 'turf-wars.png');

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `quest_season_mission`
--

CREATE TABLE `quest_season_mission` (
  `id` int(11) NOT NULL,
  `quest_season_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `requirements` text NOT NULL,
  `payouts` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Data dump for tabellen `quest_season_mission`
--

INSERT INTO `quest_season_mission` (`id`, `quest_season_id`, `name`, `description`, `requirements`, `payouts`) VALUES
(1, 1, 'A simple favour', 'Don Luca wants you to deliver this sealed package, asking for it to be delivered discreetly to a trusted associate, Vinny the Fish, who hangs out at a bar called The Rusty Nail. Find The Rust Nail and hand him the package, but be sure to keep an eye out for police patrols, now they know that you are working for the family they\'ll be sure to harass you if they find you.', '{\"vinny_the_fish_delivery\":1}', '{\"money\":100000,\"exp\":10}'),
(2, 1, 'The Friendly Visit', 'A small General Pharmacy owner named Marco has been late with his payments. Don Luca wants you to \"remind\" Marco why he needs the family\'s protection.', '{\"pharmacy_protection\":1}', '{\"money\":250000,\"exp\":10}'),
(3, 1, 'Loose Lips', 'Word gets back to Don Luca that a low-level thug, Jimmy “The Mouth”, has been talking to the police. Don Luca wants you to deal with him discreetly.', '{\"attack_player\":952}', '{\"money\":250000,\"exp\":25}'),
(4, 1, 'The Money Connection', 'Don Luca wants to know that he can trust you to be a reliable earner for the family, run some crimes and earn the family some cash.', '{\"crime_cash\":250000000}', '{\"money\":5000000,\"exp\":50}'),
(5, 1, 'The Street Sweeper', 'Words is getting round town that the family aren\'t keeping up to their protection for local businesses, head to the Backalley and get rid of some of the vermin lurking around and giving us a bad name.', '{\"backalley\":5000}', '{\"money\":500000,\"exp\":100,\"items\":[{\"id\":277,\"quantity\":1}]}'),
(6, 1, 'The Team Player', 'You\'ve proven that you can make shit happen on your own, but now it\'s time to show Don Luca that you can work as part of a team. Complete some raids to prove this.', '{\"raids\":1000}', '{\"money\":5000000,\"exp\":100,\"items\":[{\"id\":283,\"quantity\":10}]}'),
(7, 2, 'The Whispered Threat', 'Don Luca has heard rumors that Made Man Salvatore Ricci is plotting against him. He wants you to follow Salvatore and find out more about his plans.\n', '{\"follow_salvatore\":1}', '{\"points\":25000,\"exp\":50}'),
(8, 2, 'The War Chest', 'As expected Salvatore Ricci is plotting to kill and overthrow Don Luca. War is expensive and this one won\'t be any different. Don Luca has tasked you with getting the War Chest ready for a long and difficult war.\n', '{\"crime_cash\":1000000000}', '{\"points\":50000,\"exp\":100}'),
(9, 2, 'Destroy Their Operations', 'It\'s time to start striking back, Don Luca wants you to hit Salvatore Ricci where hit hurts the most, his pockets. Start raiding his operations and causing some disruption to his money.', '{\"raids\":2500}', '{\"points\":25000,\"exp\":100,\"items\":[{\"id\":253,\"quantity\":10}]}'),
(10, 2, 'Weaken Their Forces', 'Now you\'ve hit their pockets, it\'s time to start weakening their forces. Hit these sewer rats where they spend most of their time, in the backalleys.', '{\"backalley\":15000}', '{\"points\":50000,\"exp\":100,\"items\":[{\"id\":283,\"quantity\":10}]}'),
(11, 2, 'Steal The Books', 'To continue to sabotage their operations, the family needs access to his finances. Head to his accountants office and find steal his books.', '{\"steal_books\":1}', '{\"money\":50000000,\"exp\":100,\"items\":[{\"id\":271,\"quantity\":1}]}'),
(12, 2, 'Defend The Turf', 'Salvatore has had enough of being on the back foot and his men have started to enter our territories and cause disruption on our streets. Take care of these goons!', '{\"city_goons\":100}', '{\"money\":70000000,\"exp\":100,\"items\":[{\"id\":290,\"quantity\":10}]}'),
(13, 2, 'Free The Boys', 'Clearing Salvatore\'s men from our territories have proven successful, however it\'s come at a price. Plenty of our men have been locked up during the process. Don Luca want\'s you to free them, they\'ll be needed for the war ahead.', '{\"busts\":15000}', '{\"money\":100000000,\"exp\":100,\"items\":[{\"id\":318,\"quantity\":5}]}'),
(14, 2, 'The Final Hit', 'Salvatore\'s men have deserted him and he\'s on the run, find the coward and put in the hit for Don Luca, closing this chapter!', '{\"attack_player\":1001}', '{\"points\":100000,\"exp\":100,\"items\":[{\"id\":321,\"quantity\":1}]}'),
(15, 3, 'Paper Trail', 'A corrupt accountant is skimming off the top. Find the paper trail he’s left behind and make sure he forgets where it leads.', '{\"whitecollar_fraud\":100,\"maze\":500}', '{\"money\":1500000,\"exp\":250}'),
(16, 3, 'Hostile Takeover', 'There’s a tech startup refusing the Don\'s generous offer of protection. Pay them a visit, “convince” them, and secure their compliance.', '{\"attacks\":1500}', '{\"money\":500000,\"exp\":400}'),
(17, 3, 'The Consultant', 'An ex-consultant has started blabbing to investors. Track him down, get him to talk — and then ensure he stays quiet.', '{\"backalley\":20000,\"interrogate_phil\":1}', '{\"money\":500000,\"exp\":550,\"items\":[{\"id\":231,\"quantity\":1}]}'),
(18, 3, 'The Merger', 'Two rival businesses are planning to merge — threatening Don Luca’s influence. Sabotage the merger negotiations without leaving a trace.', '{\"maze\":500,\"raids\":1000}', '{\"money\":150000000,\"exp\":1000}'),
(19, 4, 'The Setup', 'Coordinate a small-time heist without lifting a weapon yourself. Plan every detail and let the crew do the dirty work.', '{\"raids\":5000}', '{\"money\":2500000,\"points\":500000,\"exp\":1000}'),
(20, 4, 'Strings Attached', 'Grease the right palms in the police department. You’ll need them blind to the next moves', '{\"backalley\":2500,\"maze\":2500,\"raids\":1000}', '{\"money\":1000000,\"items\":[{\"id\":231,\"quantity\":5}]'),
(21, 4, 'The Fall Guy', 'Set up a rival enforcer to take the fall for a murder he didn’t commit — one that you did. Timing is everything.', '{\"maze\":1000,\"backalley\":500,\"crime_cash\":1500000000}', '{\"money\":1500000000, \"points\":100000}'),
(22, 4, 'Game of Kings', 'Plan and execute a massive, multi-target operation. Kidnapping, robbery, and political blackmail — all in a single night. Don Luca wants to see if you can run the board.', '{\"raids\": 1000, \"attacks\": 1000, \"mastermind_ops\":1}', '{\"money\": 500000000,\"items\": [{\"id\": 305, \"quantity\": 2}, {\"id\": 279, \"quantity\": 2]}');

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `quest_season_mission_user`
--

CREATE TABLE `quest_season_mission_user` (
  `id` int(11) NOT NULL,
  `quest_season_id` int(11) NOT NULL,
  `quest_season_mission_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `progress` text NOT NULL,
  `is_complete` int(11) NOT NULL DEFAULT 0,
  `is_paid_out` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `quest_season_user`
--

CREATE TABLE `quest_season_user` (
  `id` int(11) NOT NULL,
  `quest_season_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `progress` text NOT NULL,
  `is_complete` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `raffle`
--

CREATE TABLE `raffle` (
  `id` smallint(6) NOT NULL,
  `numTickets` smallint(6) NOT NULL,
  `buyCurrency` enum('money','points') NOT NULL,
  `buyPrice` int(11) NOT NULL,
  `prizes` varchar(255) NOT NULL,
  `prizeCurrency` enum('money','points') NOT NULL,
  `maxTickets` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `raffleentries`
--

CREATE TABLE `raffleentries` (
  `rid` smallint(6) NOT NULL,
  `ticketNum` smallint(6) NOT NULL,
  `userid` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `raid_battle_logs`
--

CREATE TABLE `raid_battle_logs` (
  `id` int(11) NOT NULL,
  `raid_id` int(11) DEFAULT NULL,
  `battle_log` text DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `raid_participants`
--

CREATE TABLE `raid_participants` (
  `id` int(11) NOT NULL,
  `raid_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `leashed_pet_id` int(11) NOT NULL DEFAULT 0,
  `joined_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `ranks`
--

CREATE TABLE `ranks` (
  `id` int(11) NOT NULL,
  `gang` int(11) NOT NULL,
  `title` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `members` int(11) NOT NULL DEFAULT 0,
  `crime` int(11) NOT NULL DEFAULT 0,
  `vault` int(11) NOT NULL DEFAULT 0,
  `ranks` int(11) NOT NULL DEFAULT 0,
  `massmail` int(11) NOT NULL DEFAULT 0,
  `applications` int(11) NOT NULL DEFAULT 0,
  `appearance` int(11) NOT NULL DEFAULT 0,
  `invite` int(11) NOT NULL DEFAULT 0,
  `houses` int(11) NOT NULL DEFAULT 0,
  `upgrade` int(11) NOT NULL DEFAULT 1,
  `gforum` int(11) NOT NULL DEFAULT 0,
  `polls` int(11) NOT NULL DEFAULT 0,
  `gangwars` int(11) NOT NULL,
  `ganggrad` int(11) NOT NULL,
  `color` varchar(7) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `rating`
--

CREATE TABLE `rating` (
  `user` int(11) NOT NULL,
  `rater` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `rayz_logs`
--

CREATE TABLE `rayz_logs` (
  `user_id` int(11) NOT NULL,
  `who` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `reward` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `redeemed_codes`
--

CREATE TABLE `redeemed_codes` (
  `id` int(11) NOT NULL,
  `code_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `timestamp` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `referrals`
--

CREATE TABLE `referrals` (
  `id` int(11) NOT NULL,
  `when` int(11) NOT NULL,
  `referrer` int(11) NOT NULL,
  `referred` int(11) NOT NULL,
  `credited` int(11) NOT NULL,
  `viewed` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `rel_comp_leaderboard`
--

CREATE TABLE `rel_comp_leaderboard` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `two_user_id` int(11) NOT NULL,
  `daily_crimes_complete` int(11) NOT NULL DEFAULT 0,
  `overall_crimes_complete` int(11) NOT NULL DEFAULT 0,
  `crimes_milestone_collected` int(11) NOT NULL DEFAULT 0,
  `daily_attacks_complete` int(11) NOT NULL DEFAULT 0,
  `overall_attacks_complete` int(11) NOT NULL DEFAULT 0,
  `attacks_milestone_collected` int(11) NOT NULL DEFAULT 0,
  `daily_ba_complete` bigint(20) NOT NULL DEFAULT 0,
  `overall_ba_complete` bigint(20) NOT NULL DEFAULT 0,
  `ba_milestone_collected` int(11) NOT NULL DEFAULT 0,
  `daily_mugs_complete` int(11) NOT NULL DEFAULT 0,
  `overall_mugs_complete` int(11) NOT NULL DEFAULT 0,
  `mugs_milestone_collected` int(11) NOT NULL DEFAULT 0,
  `daily_busts_complete` bigint(20) NOT NULL DEFAULT 0,
  `overall_busts_complete` bigint(20) NOT NULL DEFAULT 0,
  `busts_milestone_complete` int(11) NOT NULL DEFAULT 0,
  `daily_activity_complete` bigint(20) NOT NULL DEFAULT 0,
  `overall_activity_complete` bigint(20) NOT NULL DEFAULT 0,
  `activity_milestones_collected` int(11) NOT NULL DEFAULT 0,
  `daily_raids_complete` bigint(20) NOT NULL DEFAULT 0,
  `overall_raids_complete` bigint(20) NOT NULL DEFAULT 0,
  `raids_milestone_collected` int(11) NOT NULL DEFAULT 0,
  `daily_vampire_teeth` int(11) NOT NULL DEFAULT 0,
  `overall_vampire_teeth` int(11) NOT NULL DEFAULT 0,
  `vampire_teeth_milestone_collected` int(11) NOT NULL,
  `contest_token` int(11) NOT NULL DEFAULT 0,
  `serialised_prizes_claimed` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `rel_requests`
--

CREATE TABLE `rel_requests` (
  `reqid` int(11) NOT NULL,
  `player` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `from` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `removeptmarketlog`
--

CREATE TABLE `removeptmarketlog` (
  `id` int(11) NOT NULL,
  `owner` int(11) NOT NULL DEFAULT 0,
  `amount` bigint(20) NOT NULL DEFAULT 0,
  `price` bigint(20) NOT NULL DEFAULT 0,
  `type` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `rentalmarket`
--

CREATE TABLE `rentalmarket` (
  `id` mediumint(9) NOT NULL,
  `owner` smallint(6) NOT NULL,
  `houseid` tinyint(4) NOT NULL,
  `costperday` bigint(20) NOT NULL,
  `currency` enum('money','points') NOT NULL,
  `days` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `rentedproperties`
--

CREATE TABLE `rentedproperties` (
  `id` mediumint(9) NOT NULL,
  `owner` smallint(6) NOT NULL,
  `renter` smallint(6) NOT NULL,
  `houseid` tinyint(4) NOT NULL,
  `days` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `research_type`
--

CREATE TABLE `research_type` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `level` int(11) NOT NULL,
  `cost` int(11) NOT NULL,
  `duration_in_days` int(11) NOT NULL,
  `type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Data dump for tabellen `research_type`
--

INSERT INTO `research_type` (`id`, `name`, `description`, `level`, `cost`, `duration_in_days`, `type`) VALUES
(1, 'Resource Allocation I', '+2% Crime Cash', 1, 5000000, 1, 'economy'),
(2, 'Fitness Optimization I', '+5% Gym Bonus', 2, 25000000, 3, 'economy'),
(4, 'Criminal Mastery I', '+5% Crime EXP', 2, 25000000, 3, 'economy'),
(5, 'Backalley Mapping I', '+1 Max BA Level', 3, 100000000, 5, 'economy'),
(6, 'Gold Rush Mastery I', '+5 turns when using GR Token', 3, 100000000, 5, 'economy'),
(7, 'Criminal Mastery II', '+5% Crime EXP', 3, 100000000, 5, 'economy'),
(8, 'Backalley Mapping II', '+1 Max BA Level', 4, 200000000, 7, 'economy'),
(9, 'Criminal Mastery III', '+5% Crime EXP', 4, 200000000, 7, 'economy'),
(10, 'Fitness Optimization II', '+5% Gym Bonus', 4, 200000000, 7, 'economy'),
(11, 'Pet Fitness I', '+5% Pet Gym Bonus', 4, 200000000, 7, 'economy'),
(12, 'Criminal Mastery IIII', '+10% Crime EXP', 5, 300000000, 10, 'economy'),
(13, 'Backalley Mapping III', '+1 Max BA Level', 5, 300000000, 10, 'economy'),
(14, 'Search Mastery I', '+25% Search Cash', 5, 300000000, 10, 'economy'),
(15, 'Gold Rush Mastery II', '+5 turns when using GR Token', 5, 300000000, 10, 'economy'),
(16, 'Crime Star Access I', '+1 Max Star for each crime', 5, 300000000, 10, 'economy'),
(17, 'Hit Mastery I', '+0.5% Critical Hit Chance', 1, 100000000, 3, 'combat'),
(18, 'Damage Optimization I', '+1,000 Max Damage', 2, 250000000, 6, 'combat'),
(19, 'Gym Perfection I', '+5% Gym Bonus', 2, 250000000, 6, 'combat'),
(20, 'Counter Evasion I', '+0.1% Counter Attack Chance', 3, 750000000, 9, 'combat'),
(21, 'Hit Mastery II', '+0.5% Critical Hit Chance', 3, 750000000, 9, 'combat'),
(22, 'Damage Optimization II', '+1,000 Max Damage', 3, 750000000, 9, 'combat'),
(23, 'Accuracy Mastery I', '+1% Base Hit Chance', 4, 1000000000, 15, 'combat'),
(24, 'Hit Mastery III', '+0.5% Critical Hit Chance', 4, 1000000000, 15, 'combat'),
(25, 'Damage Optimization III', '+2,000 Max Damage', 4, 1000000000, 15, 'combat'),
(26, 'Counter Evasion II', '+0.1% Counter Attack Chance', 4, 1000000000, 15, 'combat'),
(27, 'Gym Perfection II', '+5% Gym Bonus', 5, 1500000000, 21, 'combat'),
(28, 'Damage Optimization III', '+5,000 Max Damage', 5, 1500000000, 21, 'combat'),
(29, 'Hit Mastery VI', '0.5% Critical Hit Chance', 5, 1500000000, 21, 'combat'),
(30, 'Accuracy Mastery II', '+1% Base Hit Chance', 5, 1500000000, 21, 'combat'),
(31, 'Counter Evasion III', '+0.1% Counter Attack Chance\r\n', 5, 1500000000, 21, 'combat'),
(32, 'Criminal Mastery VI', '+5% Crime EXP', 6, 750000000, 15, 'economy'),
(33, 'Backalley Mapping VI', '+1 Max BA Level', 6, 750000000, 15, 'economy'),
(34, 'Backalley Mapping VI', '+1 Max BA Level', 6, 750000000, 15, 'economy'),
(35, 'Resource Allocation II', '+2% Crime Cash', 6, 750000000, 15, 'economy'),
(36, 'Fitness Optimization III', '+5% Gym Bonus', 6, 750000000, 15, 'economy'),
(37, 'Search Mastery II', '+25% Search Cash', 6, 750000000, 15, 'economy'),
(38, 'Pet Gym Mastery I', '+10% Pet Gym Gains', 1, 100000000, 3, 'pet'),
(39, 'Pet Crime EXP Mastery I', '+5% Pet Crime EXP Gains', 2, 200000000, 7, 'pet'),
(40, 'Pet Gym Mastery II', '+10% Pet Gym Gains', 2, 200000000, 7, 'pet'),
(41, 'Pet Crime Cash Mastery I', 'Unlock Earning Cash From Pet Crimes', 3, 500000000, 14, 'pet'),
(42, 'Pet Crime EXP Mastery II', '+5% Pet Crime EXP Gains', 3, 500000000, 14, 'pet'),
(43, 'Pet Gym Mastery III', '+10% Pet Gym Gains', 3, 500000000, 14, 'pet'),
(44, 'Backalley Mapping VII', '+1 Max BA Level', 7, 1500000000, 21, 'economy'),
(45, 'Backalley Mapping VII', '+1 Max BA Level', 7, 1500000000, 21, 'economy'),
(46, 'Backalley Mapping VII', '+1 Max BA Level', 7, 1500000000, 21, 'economy'),
(47, 'Search Mastery III', '+25% Search Cash', 7, 1500000000, 21, 'economy'),
(48, 'Fitness Optimization IIII', '+5% Gym Bonus', 7, 1500000000, 21, 'economy'),
(49, 'Resource Allocation III', '+2% Crime Cash', 7, 1500000000, 21, 'economy'),
(50, 'Resource Allocation III', '+2% Crime Cash', 7, 1500000000, 21, 'economy');

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `rmstore`
--

CREATE TABLE `rmstore` (
  `id` int(11) NOT NULL,
  `limiteditems1` int(11) NOT NULL DEFAULT 0,
  `limiteditems2` int(11) NOT NULL DEFAULT 0,
  `limiteditems3` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `rps`
--

CREATE TABLE `rps` (
  `id` int(11) NOT NULL,
  `p1` int(11) NOT NULL,
  `p2` int(11) NOT NULL,
  `rounds` int(11) NOT NULL DEFAULT 3,
  `wager` int(11) DEFAULT NULL,
  `current_round` int(11) NOT NULL,
  `p1_turn` varchar(11) NOT NULL,
  `p2_turn` varchar(11) NOT NULL,
  `p1_wins` int(11) NOT NULL DEFAULT 0,
  `p2_wins` int(11) NOT NULL DEFAULT 0,
  `processed` int(11) NOT NULL DEFAULT 0,
  `last` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `rps_challenges`
--

CREATE TABLE `rps_challenges` (
  `id` int(11) NOT NULL,
  `challenger` int(11) NOT NULL,
  `challenged` int(11) NOT NULL,
  `state` enum('issued','accepted','declined','completed') NOT NULL,
  `challenger_move` enum('rock','paper','scissors') DEFAULT NULL,
  `challenged_move` enum('rock','paper','scissors') DEFAULT NULL,
  `winner` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `scheduledevents`
--

CREATE TABLE `scheduledevents` (
  `id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'gym',
  `start` int(11) NOT NULL,
  `end` int(11) NOT NULL,
  `multiplier` float NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `school`
--

CREATE TABLE `school` (
  `id` int(11) NOT NULL,
  `coursename` varchar(75) NOT NULL,
  `duration` int(11) NOT NULL,
  `cost` int(11) NOT NULL,
  `strength` int(11) NOT NULL,
  `defense` int(11) NOT NULL,
  `speed` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `searches`
--

CREATE TABLE `searches` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `params` text NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `send_logs`
--

CREATE TABLE `send_logs` (
  `id` int(11) NOT NULL,
  `fromid` int(11) NOT NULL,
  `toid` int(11) NOT NULL,
  `what` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `serverbosses`
--

CREATE TABLE `serverbosses` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `hp_current` int(11) DEFAULT NULL,
  `hp_on_spawn` int(11) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT NULL,
  `despawn_timer` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `serverconfig`
--

CREATE TABLE `serverconfig` (
  `radio` varchar(5) NOT NULL DEFAULT '',
  `serverdown` text NOT NULL,
  `messagefromadmin` text NOT NULL,
  `admin` text NOT NULL,
  `polled1` varchar(100) NOT NULL DEFAULT 'unactive',
  `appsopen` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `sessions`
--

CREATE TABLE `sessions` (
  `userid` int(11) NOT NULL,
  `sessionid` varchar(100) NOT NULL,
  `ip` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `settings`
--

CREATE TABLE `settings` (
  `key` varchar(255) NOT NULL,
  `value` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `skilltrees`
--

CREATE TABLE `skilltrees` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `icon` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `skilltree_nodes`
--

CREATE TABLE `skilltree_nodes` (
  `id` int(11) NOT NULL,
  `treeid` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `icon` varchar(255) NOT NULL,
  `parent` int(11) DEFAULT NULL,
  `rewards` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`rewards`))
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `smartads`
--

CREATE TABLE `smartads` (
  `id` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `desc` text NOT NULL,
  `timeleft` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `snapshot`
--

CREATE TABLE `snapshot` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `level` int(11) NOT NULL,
  `money` int(11) NOT NULL,
  `bank` int(11) NOT NULL,
  `points` int(11) NOT NULL,
  `rmdays` int(11) NOT NULL,
  `apoints` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `snowball_log`
--

CREATE TABLE `snowball_log` (
  `user_id` int(11) NOT NULL,
  `who` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `reward` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `spentcredits`
--

CREATE TABLE `spentcredits` (
  `id` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `spender` int(11) NOT NULL,
  `spent` text NOT NULL,
  `amount` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `spylog`
--

CREATE TABLE `spylog` (
  `id` int(11) NOT NULL,
  `spyid` int(11) NOT NULL DEFAULT 0,
  `strength` bigint(20) NOT NULL DEFAULT 0,
  `defense` bigint(20) NOT NULL DEFAULT 0,
  `speed` bigint(20) NOT NULL DEFAULT 0,
  `agility` bigint(20) NOT NULL DEFAULT 0,
  `bank` bigint(20) NOT NULL DEFAULT 0,
  `points` int(11) NOT NULL DEFAULT 0,
  `age` int(11) NOT NULL DEFAULT 0,
  `uid` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `staffapps`
--

CREATE TABLE `staffapps` (
  `id` int(11) NOT NULL,
  `timeon` text NOT NULL,
  `pastexp` text NOT NULL,
  `better` text NOT NULL,
  `userid` int(11) NOT NULL,
  `staffrole` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `staff_logs`
--

CREATE TABLE `staff_logs` (
  `logid` int(11) NOT NULL,
  `player` int(11) NOT NULL,
  `text` text NOT NULL,
  `timestamp` int(11) NOT NULL,
  `extra` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `styles`
--

CREATE TABLE `styles` (
  `style` int(11) NOT NULL,
  `colornum` int(11) NOT NULL,
  `value` varchar(10) NOT NULL,
  `style_name` varchar(255) NOT NULL,
  `style_lic` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `support_replies`
--

CREATE TABLE `support_replies` (
  `id` int(10) UNSIGNED NOT NULL,
  `ticket` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `user` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `message` text NOT NULL,
  `time` int(10) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `support_tickets`
--

CREATE TABLE `support_tickets` (
  `id` int(10) UNSIGNED NOT NULL,
  `user` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `subject` varchar(255) NOT NULL DEFAULT '',
  `message` text NOT NULL,
  `time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `last` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `assigned` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `admin` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `closed` tinyint(3) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `tab_counts`
--

CREATE TABLE `tab_counts` (
  `session_id` varchar(255) NOT NULL,
  `tab_count` int(11) NOT NULL,
  `last_updated` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `threads`
--

CREATE TABLE `threads` (
  `id` int(11) NOT NULL,
  `fid` int(11) NOT NULL,
  `subject` varchar(120) NOT NULL,
  `uid` int(11) NOT NULL,
  `createdat` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `firstpost` int(11) NOT NULL,
  `lastpost` int(11) NOT NULL,
  `lastposter` int(11) NOT NULL,
  `lastposttime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `replies` int(11) NOT NULL DEFAULT 0,
  `closed` tinyint(1) NOT NULL DEFAULT 0,
  `visible` smallint(6) NOT NULL DEFAULT 0,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  `sticky` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `throne`
--

CREATE TABLE `throne` (
  `id` int(11) NOT NULL,
  `who` int(11) NOT NULL,
  `when` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `ticketreplies`
--

CREATE TABLE `ticketreplies` (
  `ticketid` int(11) NOT NULL,
  `replyid` int(11) NOT NULL,
  `timesent` int(11) NOT NULL,
  `playerid` int(11) NOT NULL,
  `body` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `tickets`
--

CREATE TABLE `tickets` (
  `ticketid` int(11) NOT NULL,
  `playerid` int(11) NOT NULL,
  `timesent` int(11) NOT NULL,
  `status` varchar(100) NOT NULL,
  `subject` varchar(500) NOT NULL,
  `body` text NOT NULL,
  `viewed` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `tournament_participants`
--

CREATE TABLE `tournament_participants` (
  `id` int(11) NOT NULL,
  `tournament_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `current_round` int(11) DEFAULT NULL,
  `opponent_id` int(11) DEFAULT NULL,
  `result` enum('Win','Lose','Pending') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `trades`
--

CREATE TABLE `trades` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `item1` int(11) DEFAULT NULL,
  `item1quantity` int(11) DEFAULT 0,
  `item2` int(11) DEFAULT NULL,
  `item2quantity` int(11) DEFAULT 0,
  `item3` int(11) DEFAULT NULL,
  `item3quantity` int(11) DEFAULT 0,
  `item4` int(11) DEFAULT NULL,
  `item4quantity` int(11) DEFAULT 0,
  `item5` int(11) DEFAULT NULL,
  `item5quantity` int(11) DEFAULT 0,
  `item6` int(11) DEFAULT NULL,
  `item6quantity` int(11) DEFAULT 0,
  `itemreward1` int(11) DEFAULT NULL,
  `itemreward2` int(11) DEFAULT NULL,
  `itemreward3` int(11) DEFAULT NULL,
  `itemreward4` int(11) DEFAULT NULL,
  `itemreward5` int(11) DEFAULT NULL,
  `itemreward6` int(11) DEFAULT NULL,
  `crafting_start_time` datetime DEFAULT NULL,
  `crafting_duration` int(11) DEFAULT 0,
  `cooldown_duration` int(11) DEFAULT 0,
  `type` int(11) NOT NULL,
  `trade_group_name` varchar(255) DEFAULT NULL,
  `inventory_limit` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `training_dummy`
--

CREATE TABLE `training_dummy` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `health` int(11) NOT NULL,
  `level` int(11) NOT NULL,
  `speed` bigint(20) NOT NULL,
  `strength` bigint(20) NOT NULL,
  `defense` bigint(20) NOT NULL,
  `agility` bigint(20) NOT NULL,
  `reward_item_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Data dump for tabellen `training_dummy`
--

INSERT INTO `training_dummy` (`id`, `name`, `image`, `health`, `level`, `speed`, `strength`, `defense`, `agility`, `reward_item_id`) VALUES
(1, 'Dave', '/css/images/NewGameImages/dave.png', 100, 1, 100, 100, 100, 100, 42),
(2, 'Gregg', '/css/images/NewGameImages/gregg.png', 100, 1, 10000, 10000, 10000, 10000, 163),
(3, 'King V', '/css/images/NewGameImages/kingv.png', 100, 1, 5000000, 5000000, 5000000, 5000000, 270),
(4, 'Patrick', '/css/images/NewGameImages/patrick.png', 100, 1, 50000000, 50000000, 50000000, 50000000, 281),
(5, 'Paul', '/css/images/NewGameImages/paul.png', 100, 1, 1000000000, 1000000000, 1000000000, 1000000000, 251),
(6, 'Vlad', '/css/images/NewGameImages/vlad.png', 100, 1, 5000000000, 5000000000, 5000000000, 5000000000, 279),
(7, 'Hoodson', '/css/images/NewGameImages/hoodson.png', 1000, 1, 7000000000, 7000000000, 7000000000, 7000000000, 283),
(8, 'Dracula', 'css/images/NewGameImages/boss-dracula.png', 1000, 1, 1000000, 1000000, 1000000, 1000000, 285);

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `training_dummy_user`
--

CREATE TABLE `training_dummy_user` (
  `id` int(11) NOT NULL,
  `training_dummy_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 1,
  `level` int(11) NOT NULL,
  `exp` bigint(20) NOT NULL,
  `is_fight_available` int(11) NOT NULL,
  `last_fight_time` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `transactions`
--

CREATE TABLE `transactions` (
  `transaction_id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `itemid` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `transferlog`
--

CREATE TABLE `transferlog` (
  `id` int(11) NOT NULL,
  `to` int(11) NOT NULL,
  `from` int(11) NOT NULL,
  `item` int(11) NOT NULL DEFAULT 0,
  `money` int(11) NOT NULL DEFAULT 0,
  `points` int(11) NOT NULL DEFAULT 0,
  `credits` int(11) NOT NULL DEFAULT 0,
  `timestamp` int(11) NOT NULL,
  `toip` varchar(30) NOT NULL,
  `fromip` varchar(30) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `uni`
--

CREATE TABLE `uni` (
  `id` int(11) NOT NULL,
  `playerid` int(11) NOT NULL,
  `courseid` int(11) NOT NULL,
  `finish` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `updates`
--

CREATE TABLE `updates` (
  `name` varchar(75) NOT NULL DEFAULT '',
  `lastdone` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `upgrades`
--

CREATE TABLE `upgrades` (
  `upgrade_id` int(11) NOT NULL,
  `upgrade_name` varchar(255) NOT NULL,
  `cost` int(11) NOT NULL,
  `duration` int(11) NOT NULL,
  `effect` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Data dump for tabellen `upgrades`
--

INSERT INTO `upgrades` (`upgrade_id`, `upgrade_name`, `cost`, `duration`, `effect`) VALUES
(1, 'Small Swimming Pool', 100000000, 6, 'This item will give you NOTHING right now.');

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `usercars`
--

CREATE TABLE `usercars` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `carid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `user_badges`
--

CREATE TABLE `user_badges` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `badge_id` int(11) NOT NULL,
  `timestamp` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `user_ba_stats`
--

CREATE TABLE `user_ba_stats` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `level` int(11) NOT NULL DEFAULT 1,
  `exp` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `attempts` bigint(20) NOT NULL DEFAULT 0,
  `gold_rush_credits` int(11) NOT NULL DEFAULT 0,
  `zombie_rush_credits` int(11) NOT NULL DEFAULT 0,
  `turns` int(11) NOT NULL DEFAULT 0,
  `wins` int(11) NOT NULL DEFAULT 0,
  `losses` int(11) NOT NULL DEFAULT 0,
  `points_gained` int(11) NOT NULL DEFAULT 0,
  `cash_gained` bigint(20) NOT NULL DEFAULT 0,
  `items_gained` int(11) NOT NULL DEFAULT 0,
  `exp_gained` bigint(20) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `user_bets`
--

CREATE TABLE `user_bets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `bet_amount` decimal(10,2) NOT NULL,
  `bet_side` varchar(10) NOT NULL,
  `result` varchar(10) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `user_blackjack`
--

CREATE TABLE `user_blackjack` (
  `user_id` int(11) NOT NULL,
  `hand` varchar(50) NOT NULL,
  `dealer_hand` varchar(50) NOT NULL,
  `bet_amount` int(11) NOT NULL,
  `game_state` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `user_buildings`
--

CREATE TABLE `user_buildings` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `building` varchar(50) NOT NULL,
  `level` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `user_comp_leaderboard`
--

CREATE TABLE `user_comp_leaderboard` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `daily_crimes_complete` int(11) NOT NULL DEFAULT 0,
  `overall_crimes_complete` int(11) NOT NULL DEFAULT 0,
  `crimes_milestone_collected` int(11) NOT NULL DEFAULT 0,
  `daily_attacks_complete` int(11) NOT NULL DEFAULT 0,
  `overall_attacks_complete` int(11) NOT NULL DEFAULT 0,
  `attacks_milestone_collected` int(11) NOT NULL DEFAULT 0,
  `daily_ba_complete` bigint(20) NOT NULL DEFAULT 0,
  `overall_ba_complete` bigint(20) NOT NULL DEFAULT 0,
  `ba_milestone_collected` int(11) NOT NULL DEFAULT 0,
  `daily_mugs_complete` int(11) NOT NULL DEFAULT 0,
  `overall_mugs_complete` int(11) NOT NULL DEFAULT 0,
  `mugs_milestone_collected` int(11) NOT NULL DEFAULT 0,
  `daily_busts_complete` bigint(20) NOT NULL DEFAULT 0,
  `overall_busts_complete` bigint(20) NOT NULL DEFAULT 0,
  `busts_milestone_complete` int(11) NOT NULL DEFAULT 0,
  `daily_activity_complete` bigint(20) NOT NULL DEFAULT 0,
  `overall_activity_complete` bigint(20) NOT NULL DEFAULT 0,
  `activity_milestones_collected` int(11) NOT NULL DEFAULT 0,
  `daily_raids_complete` bigint(20) NOT NULL DEFAULT 0,
  `overall_raids_complete` bigint(20) NOT NULL DEFAULT 0,
  `raids_milestone_collected` int(11) NOT NULL DEFAULT 0,
  `daily_vampire_teeth` int(11) NOT NULL DEFAULT 0,
  `overall_vampire_teeth` int(11) NOT NULL DEFAULT 0,
  `vampire_teeth_milestone_collected` int(11) NOT NULL DEFAULT 0,
  `contest_token` int(11) NOT NULL DEFAULT 0,
  `serialised_prizes_claimed` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `user_gifts`
--

CREATE TABLE `user_gifts` (
  `userid` mediumint(9) NOT NULL,
  `item` varchar(25) NOT NULL,
  `qty` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `user_gradients`
--

CREATE TABLE `user_gradients` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `start_color` varchar(7) NOT NULL,
  `end_color` varchar(7) NOT NULL,
  `is_bold` tinyint(1) DEFAULT 0,
  `is_italic` tinyint(1) DEFAULT 0,
  `glow` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `user_item_drop_log`
--

CREATE TABLE `user_item_drop_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `crime_potion_drop` int(11) NOT NULL DEFAULT 0,
  `crime_booster_drop` int(11) NOT NULL DEFAULT 0,
  `nerve_vial_drop` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `user_logs`
--

CREATE TABLE `user_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `event_type` enum('text','money','points','item','credits') NOT NULL,
  `description` text NOT NULL,
  `timestamp` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `user_operations`
--

CREATE TABLE `user_operations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `operations_id` int(11) NOT NULL,
  `is_premium_required` int(11) NOT NULL DEFAULT 0,
  `is_premium_purchased` tinyint(1) NOT NULL DEFAULT 0,
  `crimes` bigint(20) NOT NULL DEFAULT 0,
  `mugs` int(11) NOT NULL DEFAULT 0,
  `busts` int(11) NOT NULL DEFAULT 0,
  `online_attacks` int(11) NOT NULL DEFAULT 0,
  `offline_attacks` int(11) NOT NULL DEFAULT 0,
  `full_energy_trains` int(11) NOT NULL DEFAULT 0,
  `city_boss_wins` int(11) NOT NULL DEFAULT 0,
  `backalleys` int(11) NOT NULL DEFAULT 0,
  `raids` int(11) NOT NULL DEFAULT 0,
  `is_complete` tinyint(4) NOT NULL DEFAULT 0,
  `is_skipped` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `user_preferences`
--

CREATE TABLE `user_preferences` (
  `user_id` int(11) NOT NULL,
  `carousel_order` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `user_prestige_skills`
--

CREATE TABLE `user_prestige_skills` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `energy_boost_level` int(11) NOT NULL DEFAULT 0,
  `crime_cash_boost_level` int(11) NOT NULL DEFAULT 0,
  `mission_point_boost_level` int(11) NOT NULL DEFAULT 0,
  `mission_exp_boost_level` int(11) NOT NULL DEFAULT 0,
  `ba_point_boost_level` int(11) NOT NULL DEFAULT 0,
  `hourly_searches_boost_level` int(11) NOT NULL DEFAULT 0,
  `ba_raidtokens_unlock` tinyint(1) NOT NULL DEFAULT 0,
  `speed_attack_unlock` tinyint(1) NOT NULL DEFAULT 0,
  `super_mugs_unlock` tinyint(1) NOT NULL DEFAULT 0,
  `super_busts_unlock` tinyint(1) NOT NULL DEFAULT 0,
  `ba_gold_rush_unlock` int(11) NOT NULL DEFAULT 0,
  `crime_cash_unlock` int(11) NOT NULL DEFAULT 0,
  `throne_points_unlock` tinyint(1) NOT NULL DEFAULT 0,
  `travel_cost_unlock` tinyint(1) NOT NULL DEFAULT 0,
  `ba_cash_unlock` tinyint(1) NOT NULL DEFAULT 0,
  `training_dummy_cash_unlock` tinyint(1) NOT NULL DEFAULT 0,
  `crime_exp_unlock` tinyint(1) NOT NULL DEFAULT 0,
  `unlock_points_spent` int(11) NOT NULL DEFAULT 0,
  `boosts_spent` int(11) NOT NULL DEFAULT 0,
  `reset_points` int(11) NOT NULL DEFAULT 0,
  `research_cash_unlock` int(11) NOT NULL DEFAULT 0,
  `research_cash_boost_level` int(11) NOT NULL DEFAULT 0,
  `last_reset` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `user_research_type`
--

CREATE TABLE `user_research_type` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `research_type_id` int(11) NOT NULL,
  `duration_in_days` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `user_resources`
--

CREATE TABLE `user_resources` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `stone` int(11) DEFAULT 0,
  `wood` int(11) DEFAULT 0,
  `iron` int(11) DEFAULT 0,
  `food` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `user_resource_clockins`
--

CREATE TABLE `user_resource_clockins` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `resource` varchar(50) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `today` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `user_resource_plots`
--

CREATE TABLE `user_resource_plots` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `resource` varchar(50) NOT NULL,
  `level` int(11) DEFAULT 1,
  `last_action` timestamp NOT NULL DEFAULT current_timestamp(),
  `today_actions` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `user_santas_grotto`
--

CREATE TABLE `user_santas_grotto` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `gifts_donated` int(11) NOT NULL DEFAULT 0,
  `exp` int(11) NOT NULL DEFAULT 0,
  `level` int(11) NOT NULL DEFAULT 1,
  `todays_gifts_found` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `vlog`
--

CREATE TABLE `vlog` (
  `id` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL DEFAULT 0,
  `gangid` int(11) NOT NULL DEFAULT 0,
  `text` varchar(500) NOT NULL,
  `userid` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `voters`
--

CREATE TABLE `voters` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `poll_id` int(11) NOT NULL,
  `choice` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `votes`
--

CREATE TABLE `votes` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `site` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `wallcomments`
--

CREATE TABLE `wallcomments` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `posterid` int(11) NOT NULL,
  `msg` varchar(160) NOT NULL,
  `timestamp` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Begrænsninger for dumpede tabeller
--

--
-- Indeks for tabel `5050log`
--
ALTER TABLE `5050log`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `active_gang_missions`
--
ALTER TABLE `active_gang_missions`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `active_raids`
--
ALTER TABLE `active_raids`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `idx_active_raids_summoned_by` (`summoned_by`),
  ADD KEY `idx_active_raids_completed` (`completed`);

--
-- Indeks for tabel `activityqueue`
--
ALTER TABLE `activityqueue`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `activityrewards`
--
ALTER TABLE `activityrewards`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `activity_contest`
--
ALTER TABLE `activity_contest`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `addptmarketlog`
--
ALTER TABLE `addptmarketlog`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `ads`
--
ALTER TABLE `ads`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `advent_calendar`
--
ALTER TABLE `advent_calendar`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `attackladder`
--
ALTER TABLE `attackladder`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `attacklog`
--
ALTER TABLE `attacklog`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_attacklog_attack` (`attacker`),
  ADD KEY `idx_attacklog_defender` (`defender`),
  ADD KEY `idx_attacklog_winner` (`winner`);

--
-- Indeks for tabel `attack_turn_log`
--
ALTER TABLE `attack_turn_log`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `attack_v2`
--
ALTER TABLE `attack_v2`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `attlog`
--
ALTER TABLE `attlog`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attacker` (`attacker`);

--
-- Indeks for tabel `autoclick_detection`
--
ALTER TABLE `autoclick_detection`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_autoclick_user_time` (`userid`,`timestamp`);

--
-- Indeks for tabel `badges`
--
ALTER TABLE `badges`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `baltlepass_users`
--
ALTER TABLE `baltlepass_users`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `bank_log`
--
ALTER TABLE `bank_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_bank_log_userid` (`userid`);

--
-- Indeks for tabel `bans`
--
ALTER TABLE `bans`
  ADD PRIMARY KEY (`banid`),
  ADD KEY `idx_bans_type` (`type`),
  ADD KEY `idx_bans_id` (`id`);

--
-- Indeks for tabel `battlepass`
--
ALTER TABLE `battlepass`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `battle_members`
--
ALTER TABLE `battle_members`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `bloodbath`
--
ALTER TABLE `bloodbath`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `bosses`
--
ALTER TABLE `bosses`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `bp_category`
--
ALTER TABLE `bp_category`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `bp_category_challenges`
--
ALTER TABLE `bp_category_challenges`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_bcc_bci` (`bp_category_id`),
  ADD KEY `idx_bcc_type` (`type`),
  ADD KEY `idx_bcc_amount` (`amount`);

--
-- Indeks for tabel `bp_category_prizes`
--
ALTER TABLE `bp_category_prizes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_bcp_bci` (`bp_category_id`),
  ADD KEY `idx_bcp_cost` (`cost`);

--
-- Indeks for tabel `bp_category_user`
--
ALTER TABLE `bp_category_user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_bcu_bci` (`bp_category_id`),
  ADD KEY `idx_bcu_ui` (`user_id`);

--
-- Indeks for tabel `busts_log`
--
ALTER TABLE `busts_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `buster_id` (`buster_id`),
  ADD KEY `jailed_id` (`jailed_id`);

--
-- Indeks for tabel `carlot`
--
ALTER TABLE `carlot`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `cars`
--
ALTER TABLE `cars`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `cc`
--
ALTER TABLE `cc`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `chats`
--
ALTER TABLE `chats`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_chats_gang` (`gang_id`),
  ADD KEY `idx_chats_type_last` (`type`,`last_message_at`,`id`),
  ADD KEY `idx_chats_owner` (`owner_id`),
  ADD KEY `idx_chats_gang` (`gang_id`);

--
-- Indeks for tabel `chat_invites`
--
ALTER TABLE `chat_invites`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `chat_participants`
--
ALTER TABLE `chat_participants`
  ADD PRIMARY KEY (`chat_id`,`user_id`),
  ADD KEY `fk_cp_lastread` (`last_read_message_id`),
  ADD KEY `idx_cp_user` (`user_id`,`chat_id`),
  ADD KEY `idx_cp_chat_role` (`chat_id`,`role`),
  ADD KEY `idx_cp_lastread` (`chat_id`,`last_read_message_id`);

--
-- Indeks for tabel `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `cityevents`
--
ALTER TABLE `cityevents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `to` (`to`);

--
-- Indeks for tabel `citygame`
--
ALTER TABLE `citygame`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `creditsmarket`
--
ALTER TABLE `creditsmarket`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `crimeranks`
--
ALTER TABLE `crimeranks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_crimeranks_userid` (`userid`),
  ADD KEY `idx_crimeranks_crimeid` (`crimeid`);

--
-- Indeks for tabel `crimes`
--
ALTER TABLE `crimes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_crimes_nerve` (`nerve`);

--
-- Indeks for tabel `cron_logs`
--
ALTER TABLE `cron_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `timestamp` (`timestamp`);

--
-- Indeks for tabel `daily_eco`
--
ALTER TABLE `daily_eco`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `daily_user_stats`
--
ALTER TABLE `daily_user_stats`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks for tabel `deflog`
--
ALTER TABLE `deflog`
  ADD PRIMARY KEY (`id`),
  ADD KEY `winner` (`winner`),
  ADD KEY `defender` (`defender`),
  ADD KEY `attacker` (`attacker`),
  ADD KEY `gangid` (`gangid`);

--
-- Indeks for tabel `easter_store`
--
ALTER TABLE `easter_store`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_events_to` (`to`,`viewed`);

--
-- Indeks for tabel `eventslog`
--
ALTER TABLE `eventslog`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `eventsmain`
--
ALTER TABLE `eventsmain`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `fiftyfifty`
--
ALTER TABLE `fiftyfifty`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `fiftyfiftylogs`
--
ALTER TABLE `fiftyfiftylogs`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `forumpermissions`
--
ALTER TABLE `forumpermissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `Forum Deleted` (`fid`);

--
-- Indeks for tabel `forums`
--
ALTER TABLE `forums`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_forums_disporder` (`disporder`);

--
-- Indeks for tabel `forum_browsers`
--
ALTER TABLE `forum_browsers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `userid_UNIQUE` (`userid`);

--
-- Indeks for tabel `forum_forums`
--
ALTER TABLE `forum_forums`
  ADD PRIMARY KEY (`ff_id`);

--
-- Indeks for tabel `ftopics`
--
ALTER TABLE `ftopics`
  ADD PRIMARY KEY (`forumid`);

--
-- Indeks for tabel `gamebonus`
--
ALTER TABLE `gamebonus`
  ADD PRIMARY KEY (`ID`);

--
-- Indeks for tabel `game_updates`
--
ALTER TABLE `game_updates`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `gangarmory`
--
ALTER TABLE `gangarmory`
  ADD UNIQUE KEY `itemid` (`itemid`,`gangid`);

--
-- Indeks for tabel `gangcrime`
--
ALTER TABLE `gangcrime`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `gangcrimes`
--
ALTER TABLE `gangcrimes`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `gangevents`
--
ALTER TABLE `gangevents`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `ganginvites`
--
ALTER TABLE `ganginvites`
  ADD KEY `idx_ganginvites_playerid` (`playerid`);

--
-- Indeks for tabel `gangmail`
--
ALTER TABLE `gangmail`
  ADD PRIMARY KEY (`gmailid`);

--
-- Indeks for tabel `gangs`
--
ALTER TABLE `gangs`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `gangtargetlist`
--
ALTER TABLE `gangtargetlist`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `gang_comp_leaderboard`
--
ALTER TABLE `gang_comp_leaderboard`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `gang_loans`
--
ALTER TABLE `gang_loans`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `gang_missions`
--
ALTER TABLE `gang_missions`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `gang_territory_zone`
--
ALTER TABLE `gang_territory_zone`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `gang_territory_zone_battle`
--
ALTER TABLE `gang_territory_zone_battle`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `gang_territory_zone_battle_log`
--
ALTER TABLE `gang_territory_zone_battle_log`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `gang_territory_zone_history`
--
ALTER TABLE `gang_territory_zone_history`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `gang_vault_log`
--
ALTER TABLE `gang_vault_log`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `garage`
--
ALTER TABLE `garage`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `gcrimelog`
--
ALTER TABLE `gcrimelog`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `ghouses`
--
ALTER TABLE `ghouses`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `gift_codes`
--
ALTER TABLE `gift_codes`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `globalchat`
--
ALTER TABLE `globalchat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `timesent` (`timesent`),
  ADD KEY `id` (`id`);

--
-- Indeks for tabel `grpgusers`
--
ALTER TABLE `grpgusers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_grpgusers_gang` (`gang`),
  ADD KEY `idx_grpgusers_jail` (`jail`),
  ADD KEY `idx_grpgusers_hospital` (`hospital`);

--
-- Indeks for tabel `heists`
--
ALTER TABLE `heists`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `hitlist`
--
ALTER TABLE `hitlist`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `houses`
--
ALTER TABLE `houses`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `ignorelist`
--
ALTER TABLE `ignorelist`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `userid` (`userid`,`itemid`);

--
-- Indeks for tabel `ipbans`
--
ALTER TABLE `ipbans`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `ipn`
--
ALTER TABLE `ipn`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `itemmarket`
--
ALTER TABLE `itemmarket`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `item_daily_limit`
--
ALTER TABLE `item_daily_limit`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_idl_user_id` (`user_id`);

--
-- Indeks for tabel `item_sell`
--
ALTER TABLE `item_sell`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `item_temp_use`
--
ALTER TABLE `item_temp_use`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `jobinfo`
--
ALTER TABLE `jobinfo`
  ADD KEY `idx_jobinfo_userid` (`userid`);

--
-- Indeks for tabel `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `job_applications`
--
ALTER TABLE `job_applications`
  ADD PRIMARY KEY (`application_id`);

--
-- Indeks for tabel `land`
--
ALTER TABLE `land`
  ADD PRIMARY KEY (`userid`);

--
-- Indeks for tabel `limited_store_pack`
--
ALTER TABLE `limited_store_pack`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `limited_store_pack_purchase`
--
ALTER TABLE `limited_store_pack_purchase`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `loot`
--
ALTER TABLE `loot`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `maillog`
--
ALTER TABLE `maillog`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_msg_chat_created` (`chat_id`,`created_at`,`id`),
  ADD KEY `idx_msg_sender_created` (`sender_id`,`created_at`,`id`);
ALTER TABLE `messages` ADD FULLTEXT KEY `ftx_msg_body` (`body_plain`);

--
-- Indeks for tabel `mission`
--
ALTER TABLE `mission`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `missionlog`
--
ALTER TABLE `missionlog`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `missions`
--
ALTER TABLE `missions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_missions_userid` (`userid`),
  ADD KEY `idx_missions_completed` (`completed`);

--
-- Indeks for tabel `missions_in_progress`
--
ALTER TABLE `missions_in_progress`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `mission_count_tracking`
--
ALTER TABLE `mission_count_tracking`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_mission_count_tracking_user_id` (`id`);

--
-- Indeks for tabel `mission_daily_payout_logs`
--
ALTER TABLE `mission_daily_payout_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `mlottowinners`
--
ALTER TABLE `mlottowinners`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `moth`
--
ALTER TABLE `moth`
  ADD PRIMARY KEY (`ID`);

--
-- Indeks for tabel `muglog`
--
ALTER TABLE `muglog`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mugger` (`mugger`),
  ADD KEY `mugged` (`mugged`);

--
-- Indeks for tabel `multi`
--
ALTER TABLE `multi`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `newmissions`
--
ALTER TABLE `newmissions`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `new_halloween_payout_logs`
--
ALTER TABLE `new_halloween_payout_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `new_tournaments`
--
ALTER TABLE `new_tournaments`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `numbergame`
--
ALTER TABLE `numbergame`
  ADD PRIMARY KEY (`number`);

--
-- Indeks for tabel `operations`
--
ALTER TABLE `operations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `otdwinners`
--
ALTER TABLE `otdwinners`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `oth`
--
ALTER TABLE `oth`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `ownedbusinesses`
--
ALTER TABLE `ownedbusinesses`
  ADD PRIMARY KEY (`ownership_id`);

--
-- Indeks for tabel `ownedproperties`
--
ALTER TABLE `ownedproperties`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `pack_logs`
--
ALTER TABLE `pack_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `pagetracker`
--
ALTER TABLE `pagetracker`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `petcrimes`
--
ALTER TABLE `petcrimes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nerve` (`nerve`);

--
-- Indeks for tabel `pethouses`
--
ALTER TABLE `pethouses`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `petladder`
--
ALTER TABLE `petladder`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `pets`
--
ALTER TABLE `pets`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `petshop`
--
ALTER TABLE `petshop`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `planes`
--
ALTER TABLE `planes`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `plottowinners`
--
ALTER TABLE `plottowinners`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `pms`
--
ALTER TABLE `pms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indeks for tabel `pointsmarket`
--
ALTER TABLE `pointsmarket`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `poker_livechat`
--
ALTER TABLE `poker_livechat`
  ADD PRIMARY KEY (`gameID`);

--
-- Indeks for tabel `poker_players`
--
ALTER TABLE `poker_players`
  ADD PRIMARY KEY (`ID`);

--
-- Indeks for tabel `poker_poker`
--
ALTER TABLE `poker_poker`
  ADD PRIMARY KEY (`gameID`);

--
-- Indeks for tabel `poker_sitelog`
--
ALTER TABLE `poker_sitelog`
  ADD PRIMARY KEY (`ID`);

--
-- Indeks for tabel `poker_stats`
--
ALTER TABLE `poker_stats`
  ADD PRIMARY KEY (`ID`);

--
-- Indeks for tabel `poker_styles`
--
ALTER TABLE `poker_styles`
  ADD PRIMARY KEY (`style_id`);

--
-- Indeks for tabel `poker_userchat`
--
ALTER TABLE `poker_userchat`
  ADD PRIMARY KEY (`gameID`);

--
-- Indeks for tabel `poll`
--
ALTER TABLE `poll`
  ADD PRIMARY KEY (`ID`);

--
-- Indeks for tabel `poll_votes`
--
ALTER TABLE `poll_votes`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `Thread Deleted` (`tid`);

--
-- Indeks for tabel `profile_actions`
--
ALTER TABLE `profile_actions`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `quest_season`
--
ALTER TABLE `quest_season`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `quest_season_mission`
--
ALTER TABLE `quest_season_mission`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `quest_season_mission_user`
--
ALTER TABLE `quest_season_mission_user`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `quest_season_user`
--
ALTER TABLE `quest_season_user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_qsu_qsi` (`quest_season_id`);

--
-- Indeks for tabel `raid_battle_logs`
--
ALTER TABLE `raid_battle_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `raid_participants`
--
ALTER TABLE `raid_participants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `raid_id` (`raid_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks for tabel `ranks`
--
ALTER TABLE `ranks`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `rayz_logs`
--
ALTER TABLE `rayz_logs`
  ADD PRIMARY KEY (`user_id`);

--
-- Indeks for tabel `redeemed_codes`
--
ALTER TABLE `redeemed_codes`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `referrals`
--
ALTER TABLE `referrals`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `referral` (`referrer`,`referred`),
  ADD KEY `idx_referrals_viewed` (`viewed`);

--
-- Indeks for tabel `rel_comp_leaderboard`
--
ALTER TABLE `rel_comp_leaderboard`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ucl_user_id` (`user_id`);

--
-- Indeks for tabel `rel_requests`
--
ALTER TABLE `rel_requests`
  ADD PRIMARY KEY (`reqid`);

--
-- Indeks for tabel `removeptmarketlog`
--
ALTER TABLE `removeptmarketlog`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `rentalmarket`
--
ALTER TABLE `rentalmarket`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `rentedproperties`
--
ALTER TABLE `rentedproperties`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `research_type`
--
ALTER TABLE `research_type`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `rmstore`
--
ALTER TABLE `rmstore`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `rps`
--
ALTER TABLE `rps`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `rps_challenges`
--
ALTER TABLE `rps_challenges`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `scheduledevents`
--
ALTER TABLE `scheduledevents`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `school`
--
ALTER TABLE `school`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `searches`
--
ALTER TABLE `searches`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `send_logs`
--
ALTER TABLE `send_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `serverbosses`
--
ALTER TABLE `serverbosses`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `sessions`
--
ALTER TABLE `sessions`
  ADD UNIQUE KEY `userid` (`userid`);

--
-- Indeks for tabel `skilltrees`
--
ALTER TABLE `skilltrees`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `skilltree_nodes`
--
ALTER TABLE `skilltree_nodes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `treeid` (`treeid`);

--
-- Indeks for tabel `smartads`
--
ALTER TABLE `smartads`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `snapshot`
--
ALTER TABLE `snapshot`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `snowball_log`
--
ALTER TABLE `snowball_log`
  ADD PRIMARY KEY (`user_id`);

--
-- Indeks for tabel `spentcredits`
--
ALTER TABLE `spentcredits`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `spylog`
--
ALTER TABLE `spylog`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `staffapps`
--
ALTER TABLE `staffapps`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `staff_logs`
--
ALTER TABLE `staff_logs`
  ADD PRIMARY KEY (`logid`);

--
-- Indeks for tabel `support_replies`
--
ALTER TABLE `support_replies`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `tab_counts`
--
ALTER TABLE `tab_counts`
  ADD PRIMARY KEY (`session_id`);

--
-- Indeks for tabel `threads`
--
ALTER TABLE `threads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fid` (`fid`);

--
-- Indeks for tabel `throne`
--
ALTER TABLE `throne`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `ticketreplies`
--
ALTER TABLE `ticketreplies`
  ADD PRIMARY KEY (`ticketid`);

--
-- Indeks for tabel `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`ticketid`);

--
-- Indeks for tabel `tournament_participants`
--
ALTER TABLE `tournament_participants`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `trades`
--
ALTER TABLE `trades`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `training_dummy`
--
ALTER TABLE `training_dummy`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `training_dummy_user`
--
ALTER TABLE `training_dummy_user`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`transaction_id`);

--
-- Indeks for tabel `transferlog`
--
ALTER TABLE `transferlog`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `uni`
--
ALTER TABLE `uni`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `upgrades`
--
ALTER TABLE `upgrades`
  ADD PRIMARY KEY (`upgrade_id`);

--
-- Indeks for tabel `usercars`
--
ALTER TABLE `usercars`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `user_badges`
--
ALTER TABLE `user_badges`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `user_ba_stats`
--
ALTER TABLE `user_ba_stats`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_ba_stats_user_id` (`user_id`);

--
-- Indeks for tabel `user_bets`
--
ALTER TABLE `user_bets`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `user_buildings`
--
ALTER TABLE `user_buildings`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `user_comp_leaderboard`
--
ALTER TABLE `user_comp_leaderboard`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ucl_user_id` (`user_id`);

--
-- Indeks for tabel `user_gradients`
--
ALTER TABLE `user_gradients`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `user_item_drop_log`
--
ALTER TABLE `user_item_drop_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_uidl_user_id` (`user_id`);

--
-- Indeks for tabel `user_logs`
--
ALTER TABLE `user_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `timestamp` (`timestamp`);

--
-- Indeks for tabel `user_operations`
--
ALTER TABLE `user_operations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `user_preferences`
--
ALTER TABLE `user_preferences`
  ADD PRIMARY KEY (`user_id`);

--
-- Indeks for tabel `user_prestige_skills`
--
ALTER TABLE `user_prestige_skills`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ups_user_id` (`user_id`);

--
-- Indeks for tabel `user_research_type`
--
ALTER TABLE `user_research_type`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `user_resources`
--
ALTER TABLE `user_resources`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `user_resource_clockins`
--
ALTER TABLE `user_resource_clockins`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `user_resource_plots`
--
ALTER TABLE `user_resource_plots`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `user_santas_grotto`
--
ALTER TABLE `user_santas_grotto`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `vlog`
--
ALTER TABLE `vlog`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `voters`
--
ALTER TABLE `voters`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `votes`
--
ALTER TABLE `votes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_votes_userid` (`userid`);

--
-- Indeks for tabel `wallcomments`
--
ALTER TABLE `wallcomments`
  ADD PRIMARY KEY (`id`);

--
-- Brug ikke AUTO_INCREMENT for slettede tabeller
--

--
-- Tilføj AUTO_INCREMENT i tabel `5050log`
--
ALTER TABLE `5050log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `active_gang_missions`
--
ALTER TABLE `active_gang_missions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `active_raids`
--
ALTER TABLE `active_raids`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `activityqueue`
--
ALTER TABLE `activityqueue`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `activityrewards`
--
ALTER TABLE `activityrewards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `activity_contest`
--
ALTER TABLE `activity_contest`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `addptmarketlog`
--
ALTER TABLE `addptmarketlog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `ads`
--
ALTER TABLE `ads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `advent_calendar`
--
ALTER TABLE `advent_calendar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `attackladder`
--
ALTER TABLE `attackladder`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `attacklog`
--
ALTER TABLE `attacklog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `attack_turn_log`
--
ALTER TABLE `attack_turn_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `attack_v2`
--
ALTER TABLE `attack_v2`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `attlog`
--
ALTER TABLE `attlog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `autoclick_detection`
--
ALTER TABLE `autoclick_detection`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `badges`
--
ALTER TABLE `badges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Tilføj AUTO_INCREMENT i tabel `baltlepass_users`
--
ALTER TABLE `baltlepass_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `bank_log`
--
ALTER TABLE `bank_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `bans`
--
ALTER TABLE `bans`
  MODIFY `banid` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `battlepass`
--
ALTER TABLE `battlepass`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Tilføj AUTO_INCREMENT i tabel `battle_members`
--
ALTER TABLE `battle_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `bloodbath`
--
ALTER TABLE `bloodbath`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `bosses`
--
ALTER TABLE `bosses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- Tilføj AUTO_INCREMENT i tabel `bp_category`
--
ALTER TABLE `bp_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Tilføj AUTO_INCREMENT i tabel `bp_category_challenges`
--
ALTER TABLE `bp_category_challenges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2012;

--
-- Tilføj AUTO_INCREMENT i tabel `bp_category_prizes`
--
ALTER TABLE `bp_category_prizes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1437;

--
-- Tilføj AUTO_INCREMENT i tabel `bp_category_user`
--
ALTER TABLE `bp_category_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `busts_log`
--
ALTER TABLE `busts_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `carlot`
--
ALTER TABLE `carlot`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Tilføj AUTO_INCREMENT i tabel `cars`
--
ALTER TABLE `cars`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Tilføj AUTO_INCREMENT i tabel `cc`
--
ALTER TABLE `cc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `chats`
--
ALTER TABLE `chats`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `chat_invites`
--
ALTER TABLE `chat_invites`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `cities`
--
ALTER TABLE `cities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- Tilføj AUTO_INCREMENT i tabel `cityevents`
--
ALTER TABLE `cityevents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `citygame`
--
ALTER TABLE `citygame`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- Tilføj AUTO_INCREMENT i tabel `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Tilføj AUTO_INCREMENT i tabel `creditsmarket`
--
ALTER TABLE `creditsmarket`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `crimeranks`
--
ALTER TABLE `crimeranks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `crimes`
--
ALTER TABLE `crimes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- Tilføj AUTO_INCREMENT i tabel `cron_logs`
--
ALTER TABLE `cron_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `daily_eco`
--
ALTER TABLE `daily_eco`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `daily_user_stats`
--
ALTER TABLE `daily_user_stats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `deflog`
--
ALTER TABLE `deflog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `easter_store`
--
ALTER TABLE `easter_store`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Tilføj AUTO_INCREMENT i tabel `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `eventslog`
--
ALTER TABLE `eventslog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `eventsmain`
--
ALTER TABLE `eventsmain`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `fiftyfifty`
--
ALTER TABLE `fiftyfifty`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Tilføj AUTO_INCREMENT i tabel `fiftyfiftylogs`
--
ALTER TABLE `fiftyfiftylogs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `forumpermissions`
--
ALTER TABLE `forumpermissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `forums`
--
ALTER TABLE `forums`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Tilføj AUTO_INCREMENT i tabel `forum_browsers`
--
ALTER TABLE `forum_browsers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `forum_forums`
--
ALTER TABLE `forum_forums`
  MODIFY `ff_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `ftopics`
--
ALTER TABLE `ftopics`
  MODIFY `forumid` mediumint(9) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `gamebonus`
--
ALTER TABLE `gamebonus`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `game_updates`
--
ALTER TABLE `game_updates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `gangcrime`
--
ALTER TABLE `gangcrime`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Tilføj AUTO_INCREMENT i tabel `gangcrimes`
--
ALTER TABLE `gangcrimes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `gangevents`
--
ALTER TABLE `gangevents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `gangmail`
--
ALTER TABLE `gangmail`
  MODIFY `gmailid` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `gangs`
--
ALTER TABLE `gangs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `gangtargetlist`
--
ALTER TABLE `gangtargetlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `gang_comp_leaderboard`
--
ALTER TABLE `gang_comp_leaderboard`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `gang_loans`
--
ALTER TABLE `gang_loans`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `gang_missions`
--
ALTER TABLE `gang_missions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Tilføj AUTO_INCREMENT i tabel `gang_territory_zone`
--
ALTER TABLE `gang_territory_zone`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `gang_territory_zone_battle`
--
ALTER TABLE `gang_territory_zone_battle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `gang_territory_zone_battle_log`
--
ALTER TABLE `gang_territory_zone_battle_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `gang_territory_zone_history`
--
ALTER TABLE `gang_territory_zone_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `gang_vault_log`
--
ALTER TABLE `gang_vault_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `garage`
--
ALTER TABLE `garage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- Tilføj AUTO_INCREMENT i tabel `gcrimelog`
--
ALTER TABLE `gcrimelog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `ghouses`
--
ALTER TABLE `ghouses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Tilføj AUTO_INCREMENT i tabel `gift_codes`
--
ALTER TABLE `gift_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tilføj AUTO_INCREMENT i tabel `globalchat`
--
ALTER TABLE `globalchat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `grpgusers`
--
ALTER TABLE `grpgusers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `heists`
--
ALTER TABLE `heists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `hitlist`
--
ALTER TABLE `hitlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Tilføj AUTO_INCREMENT i tabel `houses`
--
ALTER TABLE `houses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- Tilføj AUTO_INCREMENT i tabel `ignorelist`
--
ALTER TABLE `ignorelist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- Tilføj AUTO_INCREMENT i tabel `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `ipbans`
--
ALTER TABLE `ipbans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tilføj AUTO_INCREMENT i tabel `ipn`
--
ALTER TABLE `ipn`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `itemmarket`
--
ALTER TABLE `itemmarket`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=363;

--
-- Tilføj AUTO_INCREMENT i tabel `item_daily_limit`
--
ALTER TABLE `item_daily_limit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `item_sell`
--
ALTER TABLE `item_sell`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `item_temp_use`
--
ALTER TABLE `item_temp_use`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- Tilføj AUTO_INCREMENT i tabel `job_applications`
--
ALTER TABLE `job_applications`
  MODIFY `application_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `land`
--
ALTER TABLE `land`
  MODIFY `userid` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `limited_store_pack`
--
ALTER TABLE `limited_store_pack`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Tilføj AUTO_INCREMENT i tabel `limited_store_pack_purchase`
--
ALTER TABLE `limited_store_pack_purchase`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `loot`
--
ALTER TABLE `loot`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=312;

--
-- Tilføj AUTO_INCREMENT i tabel `maillog`
--
ALTER TABLE `maillog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `mission`
--
ALTER TABLE `mission`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- Tilføj AUTO_INCREMENT i tabel `missionlog`
--
ALTER TABLE `missionlog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `missions`
--
ALTER TABLE `missions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `missions_in_progress`
--
ALTER TABLE `missions_in_progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `mission_count_tracking`
--
ALTER TABLE `mission_count_tracking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `mission_daily_payout_logs`
--
ALTER TABLE `mission_daily_payout_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `mlottowinners`
--
ALTER TABLE `mlottowinners`
  MODIFY `id` smallint(6) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `moth`
--
ALTER TABLE `moth`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `muglog`
--
ALTER TABLE `muglog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `multi`
--
ALTER TABLE `multi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `newmissions`
--
ALTER TABLE `newmissions`
  MODIFY `id` mediumint(9) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `new_halloween_payout_logs`
--
ALTER TABLE `new_halloween_payout_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `new_tournaments`
--
ALTER TABLE `new_tournaments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `numbergame`
--
ALTER TABLE `numbergame`
  MODIFY `number` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `operations`
--
ALTER TABLE `operations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=126;

--
-- Tilføj AUTO_INCREMENT i tabel `otdwinners`
--
ALTER TABLE `otdwinners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `oth`
--
ALTER TABLE `oth`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `ownedbusinesses`
--
ALTER TABLE `ownedbusinesses`
  MODIFY `ownership_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `ownedproperties`
--
ALTER TABLE `ownedproperties`
  MODIFY `id` mediumint(9) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `pack_logs`
--
ALTER TABLE `pack_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `pagetracker`
--
ALTER TABLE `pagetracker`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `petcrimes`
--
ALTER TABLE `petcrimes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Tilføj AUTO_INCREMENT i tabel `pethouses`
--
ALTER TABLE `pethouses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- Tilføj AUTO_INCREMENT i tabel `petladder`
--
ALTER TABLE `petladder`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `pets`
--
ALTER TABLE `pets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `petshop`
--
ALTER TABLE `petshop`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Tilføj AUTO_INCREMENT i tabel `planes`
--
ALTER TABLE `planes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Tilføj AUTO_INCREMENT i tabel `plottowinners`
--
ALTER TABLE `plottowinners`
  MODIFY `id` smallint(6) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `pms`
--
ALTER TABLE `pms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `pointsmarket`
--
ALTER TABLE `pointsmarket`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `poker_players`
--
ALTER TABLE `poker_players`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Tilføj AUTO_INCREMENT i tabel `poker_poker`
--
ALTER TABLE `poker_poker`
  MODIFY `gameID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `poker_sitelog`
--
ALTER TABLE `poker_sitelog`
  MODIFY `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `poker_stats`
--
ALTER TABLE `poker_stats`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `poker_styles`
--
ALTER TABLE `poker_styles`
  MODIFY `style_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `poll`
--
ALTER TABLE `poll`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `poll_votes`
--
ALTER TABLE `poll_votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `profile_actions`
--
ALTER TABLE `profile_actions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Tilføj AUTO_INCREMENT i tabel `quest_season`
--
ALTER TABLE `quest_season`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Tilføj AUTO_INCREMENT i tabel `quest_season_mission`
--
ALTER TABLE `quest_season_mission`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Tilføj AUTO_INCREMENT i tabel `quest_season_mission_user`
--
ALTER TABLE `quest_season_mission_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `quest_season_user`
--
ALTER TABLE `quest_season_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `raid_battle_logs`
--
ALTER TABLE `raid_battle_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `raid_participants`
--
ALTER TABLE `raid_participants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `ranks`
--
ALTER TABLE `ranks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `rayz_logs`
--
ALTER TABLE `rayz_logs`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `redeemed_codes`
--
ALTER TABLE `redeemed_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `referrals`
--
ALTER TABLE `referrals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `rel_comp_leaderboard`
--
ALTER TABLE `rel_comp_leaderboard`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `rel_requests`
--
ALTER TABLE `rel_requests`
  MODIFY `reqid` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `removeptmarketlog`
--
ALTER TABLE `removeptmarketlog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `rentalmarket`
--
ALTER TABLE `rentalmarket`
  MODIFY `id` mediumint(9) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `rentedproperties`
--
ALTER TABLE `rentedproperties`
  MODIFY `id` mediumint(9) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `research_type`
--
ALTER TABLE `research_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- Tilføj AUTO_INCREMENT i tabel `rmstore`
--
ALTER TABLE `rmstore`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `rps`
--
ALTER TABLE `rps`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `rps_challenges`
--
ALTER TABLE `rps_challenges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `scheduledevents`
--
ALTER TABLE `scheduledevents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Tilføj AUTO_INCREMENT i tabel `school`
--
ALTER TABLE `school`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `searches`
--
ALTER TABLE `searches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `send_logs`
--
ALTER TABLE `send_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `serverbosses`
--
ALTER TABLE `serverbosses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tilføj AUTO_INCREMENT i tabel `skilltrees`
--
ALTER TABLE `skilltrees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tilføj AUTO_INCREMENT i tabel `skilltree_nodes`
--
ALTER TABLE `skilltree_nodes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- Tilføj AUTO_INCREMENT i tabel `smartads`
--
ALTER TABLE `smartads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `snapshot`
--
ALTER TABLE `snapshot`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `snowball_log`
--
ALTER TABLE `snowball_log`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `spentcredits`
--
ALTER TABLE `spentcredits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `spylog`
--
ALTER TABLE `spylog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `staffapps`
--
ALTER TABLE `staffapps`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `staff_logs`
--
ALTER TABLE `staff_logs`
  MODIFY `logid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tilføj AUTO_INCREMENT i tabel `support_replies`
--
ALTER TABLE `support_replies`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `support_tickets`
--
ALTER TABLE `support_tickets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `threads`
--
ALTER TABLE `threads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `throne`
--
ALTER TABLE `throne`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `ticketreplies`
--
ALTER TABLE `ticketreplies`
  MODIFY `ticketid` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `tickets`
--
ALTER TABLE `tickets`
  MODIFY `ticketid` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `tournament_participants`
--
ALTER TABLE `tournament_participants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `trades`
--
ALTER TABLE `trades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `training_dummy`
--
ALTER TABLE `training_dummy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Tilføj AUTO_INCREMENT i tabel `training_dummy_user`
--
ALTER TABLE `training_dummy_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `transactions`
--
ALTER TABLE `transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `transferlog`
--
ALTER TABLE `transferlog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `uni`
--
ALTER TABLE `uni`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `upgrades`
--
ALTER TABLE `upgrades`
  MODIFY `upgrade_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tilføj AUTO_INCREMENT i tabel `usercars`
--
ALTER TABLE `usercars`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `user_badges`
--
ALTER TABLE `user_badges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `user_ba_stats`
--
ALTER TABLE `user_ba_stats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `user_bets`
--
ALTER TABLE `user_bets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `user_buildings`
--
ALTER TABLE `user_buildings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `user_comp_leaderboard`
--
ALTER TABLE `user_comp_leaderboard`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `user_gradients`
--
ALTER TABLE `user_gradients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `user_item_drop_log`
--
ALTER TABLE `user_item_drop_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `user_logs`
--
ALTER TABLE `user_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `user_operations`
--
ALTER TABLE `user_operations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `user_prestige_skills`
--
ALTER TABLE `user_prestige_skills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `user_research_type`
--
ALTER TABLE `user_research_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `user_resources`
--
ALTER TABLE `user_resources`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `user_resource_clockins`
--
ALTER TABLE `user_resource_clockins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `user_resource_plots`
--
ALTER TABLE `user_resource_plots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `user_santas_grotto`
--
ALTER TABLE `user_santas_grotto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `vlog`
--
ALTER TABLE `vlog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `voters`
--
ALTER TABLE `voters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `votes`
--
ALTER TABLE `votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `wallcomments`
--
ALTER TABLE `wallcomments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Begrænsninger for dumpede tabeller
--

--
-- Begrænsninger for tabel `chats`
--
ALTER TABLE `chats`
  ADD CONSTRAINT `fk_chats_owner` FOREIGN KEY (`owner_id`) REFERENCES `grpgusers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Begrænsninger for tabel `chat_participants`
--
ALTER TABLE `chat_participants`
  ADD CONSTRAINT `fk_cp_chat` FOREIGN KEY (`chat_id`) REFERENCES `chats` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cp_lastread` FOREIGN KEY (`last_read_message_id`) REFERENCES `messages` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cp_user` FOREIGN KEY (`user_id`) REFERENCES `grpgusers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Begrænsninger for tabel `forumpermissions`
--
ALTER TABLE `forumpermissions`
  ADD CONSTRAINT `Forum Deleted` FOREIGN KEY (`fid`) REFERENCES `forums` (`id`) ON DELETE CASCADE;

--
-- Begrænsninger for tabel `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `Thread Deleted` FOREIGN KEY (`tid`) REFERENCES `threads` (`id`) ON DELETE CASCADE;

--
-- Begrænsninger for tabel `skilltree_nodes`
--
ALTER TABLE `skilltree_nodes`
  ADD CONSTRAINT `skilltree_nodes_ibfk_1` FOREIGN KEY (`treeid`) REFERENCES `skilltrees` (`id`);

--
-- Begrænsninger for tabel `threads`
--
ALTER TABLE `threads`
  ADD CONSTRAINT `fk_threads_forums` FOREIGN KEY (`fid`) REFERENCES `forums` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
