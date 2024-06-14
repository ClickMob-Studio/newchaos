<?php

/*
 * Gang wars
 */
define('GWAR_DURATION', 3);
define('GWAR_MIN_POINTS', 10);
define('GWAR_MAX_POINTS', 50000);
define('GWAR_MIN_MONEY', 10000);
define('GWAR_MAX_MONEY', 50000000);
define('GWAR_START_WPOINTS', 0);
define('GWAR_FINISH_WPOINTS', 1000);
define('GWAR_MIN_GANG_MEMBERS', 3);
define('GWAR_MIN_GANG_MEMBERS_LEVEL', 250);
define('GWAR_MAX_RUNNING_WARS', 5);
define('GWAR_LOW_MAX_ATTACKS_DIFF', 40);
define('GWAR_MEDIUM_MAX_ATTACKS_DIFF', 13);
define('GWAR_LOW_MAX_ATTACKS_COUNT', 3);
define('GWAR_MEDIUM_MAX_ATTACKS_COUNT', 3);
define('GWAR_HIGH_MAX_ATTACKS_COUNT', 3);

/*
 * 5050games
 */
define('GAME5050_BETS_LIMIT', 5);

/*
 * Skill consts
 */
define('SK_UCONNECTIONS_ID', 1);
define('SK_GCONNECTIONS_ID', 2);
define('SK_CRUSHING_ID', 3);
define('SK_WARLORD_ID', 4);
define('SK_CORRUPT_WARDEN_ID', 5);
define('SK_CORRUPT_WARDEN_BANK_BONUS', 200000000);

/*
 * Generic configuration
 */
define('MAX_LVL', 500);
define('RESET_LVL', 250);
define('DEFAULT_MAX_BANK', 1000000000);
define('VAULT_MAX_MONEY', 100000000000); //maximul limit for deposite in vault
define('GUARDS_GANG_ID', 2);
define('MUG_MAX_XP', 5000);
define('CRITICAL_TRAINING', 1);
define('MARIJUANA_RATE', 200);

/*
 * Captcha settings
 */
define('CAPTCHA_FREQUENCY', 750); //captcha frequency
define('CAPTCHA_SOFTLIMIT', 10); //number or times refresh or try allow
define('CAPTCHAEQ_FREQUENCY', 1000); //equation captcha frequency on gym and crim page
define('CAPTCHAEQ_DIGIT', 1); //equation captcha digit
define('CAPTCHAEQ_SOFTLIMIT', 0); //number or times refresh or try allow

/*
 * Generic
 */
define('DAY_SEC', 86400);   

/*
 * Points constants
 */
define('MAX_POINTS', 500000); //Maximum points a player can store
define('GANG_MAX_POINTS', 7000000); //Max point limit for gang

/*
 * Monitoring
 */
define('MONITOR_USER_ID', 1000); //number or times refresh or try allow
define('ADMIN_USER_ID', 2000); //number or times refresh or try allow
define('ANONYMOUS_GUARD_ID', 1);

/*
 * Auction
 */
define('AUCTION_MAX_BID', 2000000000); //Max bid a user can bid for any auction

/*
 * Personal shop
 */
define('PSHOP_ITEM_MIN_COST', 100); //Item minimum cost for personal shop. Put -1 for no limit
define('PSHOP_ITEM_MAX_COST', -1); //Item maximum cost for personal shop. Put -1 for no limit
define('PSHOP_ITEM_MIN_POINT', 5); //Item maximum points for personal shop. Put -1 for no limit
define('PSHOP_ITEM_MAX_POINT', -1); //Item maximum points for personal shop. Put -1 for no limit

define('PSHOP_LAND_MIN_COST', 100); //Land minimum cost for personal shop. Put -1 for no limit
define('PSHOP_LAND_MAX_COST', -1); //Land maximum cost for personal shop. Put -1 for no limit
define('PSHOP_LAND_MIN_POINT', 5); //Land maximum points for personal shop. Put -1 for no limit
define('PSHOP_LAND_MAX_POINT', -1); //Land maximum points for personal shop. Put -1 for no limit

define('RPSHOP_ITEM_MIN_COST', 100); //Item minimum cost for personal rp shop. Put -1 for no limit
define('RPSHOP_ITEM_MAX_COST', -1); //Item maximum cost for personal rp shop. Put -1 for no limit
define('RPSHOP_ITEM_MIN_POINT', 5); //Item minimum points for personal rp shop. Put -1 for no limit
define('RPSHOP_ITEM_MAX_POINT', -1); //Item maximum points for personal rp shop. Put -1 for no limit

/*
 *  Banking constants
 */
define('BANK_INTEREST_NORMAL', 0.005);
define('BANK_INTEREST_RP', 0.01);
define('MAX_BANK_INTEREST', 3000000);
define('BANK_INTEREST_VALIDITY_TIME', 432000);

/*
 *  Referrals
 */
define('REFERRAL_VALIDITY_TIME', 2592000);

/*
 * Technical support team email
 */
define('TECH_SUPPORT_EMAIL', 'tiagojbastos@gmail.com');

/*
 * Memcached settings
 */
define('MC_EXPIRE', 15); //number of days cache expires. should not be more than 30.

define('MEDIA_FILES_PATH', 'files/'); // relative path to folder (relative to root of the GeneralForces)

// this is mostly for future CDN usage.
// Leave empty for local storing
// for s3 it can be something like: http://media.generalforces.com or http://media.generalforces.com.s3.amazonaws.com etc..
define('FILES_HOSTER', '');

//Suggestion reviewed
define('REVIEWED_SUGGESTION_LIST', 25);
