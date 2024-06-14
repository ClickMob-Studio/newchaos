<?php
/**
 * discription: This file is использоватьd to maintain russian language of site.
 *
 * TODO: In no way, don't использовать it in production :)
 * I've added for testing purposes only!
 * Bakyt
 *
 * @author: Harish<harish282@gmail.com>
 * @name: Russian
 * @package: includes
 * @subpackage: lang
 * @access: Public
 * @copyright: icecubegaming <http://www.icecubegaming.com>
 */

/*
 * NOTE:  for variable inside the string использовать printf format i.e
 *  % - a literal percent character. No argument is required.
 * b - the argument is treated as an integer, and presented as a binary number.
 * c - the argument is treated as an integer, and presented as the character with that ASCII value.
 * d - the argument is treated as an integer, and presented as a (signed) decimal number.
 * e - the argument is treated as scientific notation (e.g. 1.2e+2).
 * u - the argument is treated as an integer, and presented as an unsigned decimal number.
 * f - the argument is treated as a float, and presented as a floating-point number (locale aware).
 * F - the argument is treated as a float, and presented as a floating-point number (non-locale aware).
 * o - the argument is treated as an integer, and presented as an octal number.
 * s - the argument is treated as and presented as a string.
 * x - the argument is treated as an integer and presented as a hexadecimal number (with lowercase letters).
 * X - the argument is treated as an integer and presented as a hexadecimal number (with uppercase letters).
 */

/**common words **/
define('COM_MULTIPLE_LANGUAGE_SELECTION', 'PS: You can select multiple spoken languages by ctrl clicking languages.');
define('COM_ACTION', 'Action');
define('COM_FOR', 'For');
define('COM_BUYER', 'Buyer');
define('COM_ACTIONS', 'Actions');
define('COM_BET', 'Bet');
define('COM_SUBJECT', 'Subject');
define('COM_BUY', 'Buy');
define('COM_SELL', 'Sell');
define('COM_DESCRIPTION', 'Description');
define('COM_DETAILS', 'Details');
define('COM_MORE_DETAILS', 'More Details');

define('COM_NAME', 'Имя');
define('COM_ATTACK', 'Атака');
define('COM_CRIMES', 'Crimes');
define('COM_MUG', 'Mug');
define('COM_SPY', 'Spy');
define('COM_YES', 'Yes');
define('COM_NO', 'No');
define('COM_NONE', 'None');
define('COM_ALL', 'All');
define('COM_BAN', 'Ban');
define('COM_WARNING', 'Warning');
define('COM_DAYS', 'Days');
define('COM_DATE', 'Date');
define('COM_TIME', 'Time');
define('COM_MINUTES', 'minutes');
define('COM_INMATE', 'Inmate');
define('COM_ID', 'id');
define('COM_USERNAME', 'Имя пользователя');
define('COM_USER_ID', 'User ID');
define('COM_HOUSE', 'hoиспользовать');
define('COM_BANK', 'Bank');
define('COM_MONEY', 'деньги');
define('COM_TOTAL', 'Total');
define('COM_STRENGTH', 'strength');
define('COM_DEFENSE', 'defense');
define('COM_SPEED', 'speed');
define('COM_AMOUNT', 'amount');
define('COM_BACK', 'Back');
define('COM_CONTINUE', 'Continue');
define('COM_ADD', 'Add');
define('COM_SAVE', 'Save');
define('COM_UPDATE', 'Update');
define('COM_EDIT', 'Edit');
define('COM_REMOVE', 'Remove');
define('COM_DELETE', 'Delete');
define('COM_REFRESH', 'Refresh');
define('COM_SEARCH', 'Search');
define('COM_FIND', 'Find');
define('COM_RESET', 'Reset');
define('COM_CREATE', 'Create');
define('COM_RANK', 'Rank');
define('COM_NAME', 'Имя');
define('COM_LEVEL', 'Level');
define('COM_AGE', 'Age');
define('COM_EXP', 'Exp');
define('COM_MEMBERS', 'Members');
define('COM_LEADER', 'Leader');
define('COM_RANK', 'Rank');
define('COM_TRY', 'Try');
define('COM_IMPORTANT', 'Important');
define('COM_VIEW', 'View');
define('COM_EMAIL', 'Email');
define('COM_CAREFUL', 'careful');
define('COM_UPDATE_LEVEL', 'Update Level');
define('COM_JOIN_GANG', 'Join Gang');
define('COM_STOP', 'Stop');
define('COM_START', 'Start');
define('COM_WITHDRAW', 'Withdraw');
define('COM_DEPOSIT', 'Deposit');
define('COM_RECOVERED', 'Recovered');
define('COM_LOG', 'Log');
define('COM_DRAW', 'Draw');
define('COM_WON', 'Won');
define('COM_LOST', 'Lost');
define('COM_FAILED', 'Failed');
define('COM_CANCELED', 'Canceled');
define('COM_PMAIL', 'Pmail');
define('COM_MIN', 'Min');
define('COM_MAX', 'Max');
define('COM_CITY', 'City');
define('COM_TOTALPOINTS', 'Total Points');
define('COM_TODAYPOINTS', 'Today\'s Points');

define('TOTAL', 'Total');
define('COM_ONLINE', 'online');
define('COM_OFFLINE', 'offline');
define('ONLINE', 'Online');
define('OFFLINE', 'Offline');
define('COM_HP', 'HP');
define('COM_XP', 'XP');
define('COM_PREFERENCES', 'Preferences');
define('COM_LOGOUT', 'Logout');
define('COM_JAIL', 'Jail');
define('COM_CELL', 'Cell');
define('COM_HOSPITAL', 'Hospital');
define('COM_ACCEPT', 'Accept');
define('COM_PRISON', 'Заключенный');
define('COM_WEAPON', 'Weapon');
define('COM_ARMOR', 'Armor');
define('COM_ITEMS', 'Items');
define('COM_LOAN', 'Loan');
define('COM_RETURN', 'Return');
define('COM_PRISONER', 'Заключенныйer');
define('COM_STATUS', 'Status');
define('COM_LAND', 'Land');
define('COM_FERTILIZER', 'Fertilizer');
define('COM_RESULT', 'Result');
define('COM_UNBLOCK', 'Unblock');
define('COM_ORDER', 'Order');
define('COM_DISABLED', 'Disabled');
define('COM_SUCCESS', 'SUCCESS');
define('COM_ERROR', 'Error');
define('COM_PREV', 'Prev');
define('COM_NEXT', 'Next');
define('COM_STATS', 'Stats');
define('COM_READY', 'Ready');
define('COM_MUST_ENTER_NUMERIC_ID', 'Ты must enter a numeric id');
define('COM_MAIN_LANGUAGE', 'Main язык');
define('COM_SPOKEN_LANGUAGES', 'Spoken языки');

define('PROTECTED_BY_GUARDS', 'Protected by Guards');
define('HOSPITALIZED', 'Hospitalized');
define('NOT_AUTHORIZED', 'Ты are not authorized to be here.');
define('PREVIOUS_PAGE', 'Previous Page');
define('NEXT_PAGE', 'Next Page');
define('LAST_ACTIVE', 'Last Active');
define('IMPORTANT_MESSAGE', 'Important Message');
define('MESSAGE', 'Message');
define('AUTHORIZE', 'Authorize');
define('NO_RECORDS_FOUND', 'No records found.');
define('ANONYMOUS', 'Anonymous');
define('SETTINGS', 'Settings');
define('INVALID_INPUT', 'Invalid Input!');

//24hour
define('HOUR24_HEAD_MSG', 'Users Online In The Last 24 Hours');
define('HOUR24_NO_USER', 'No использоватьrs active in the last 24 hours.');

//5050Game
define('GAME5050_REFRESH', '50/50 Chance Money Game - <a href="%s">Refresh Page');
define('GAME5050_GAME_RULES', 'Game rules');
define('GAME5050_GAME_RULE_1', '2 inmates bet the same amount of деньги.');
define('GAME5050_GAME_RULE_2', 'A winner is randomly picked. The winner receives all the деньги.');
define('GAME5050_ADD_BET', 'Add a new bet');
define('GAME5050_AMOUNT_BID', 'Amount of деньги to bid.');
define('GAME5050_MIN_MAX_BET', 'minimum $1,000 / max $100,000,000 bet');
define('GAME5050_MAKE_BET', 'Make Bet');
define('GAME5050_CUR_BETS', 'Current Bets');
define('GAME5050_NO_BET', 'No bets found.');
define('GAME5050_INMATE', 'Inmate');
define('GAME5050_BET_AMOUNT', 'Bet Amount');
define('GAME5050_TAKE_BET', 'Take Bet');
define('GAME5050_REMOVE_BET', 'Remove Bet');
define('GAME5050_BET_NOT_AVAILABLE', 'This bet isn\'t available anymore.');
define('GAME5050_BET_NOT_REMOVEABLE', 'Ты можешьnot remove another player\'s bet.');
define('GAME5050_BET_CANT_REMOVED', 'Bet could not be removed.');
define('GAME5050_BET_REMOVED', 'Ты removed your bet.');
define('GAME5050_CANT_TAKE_OWN', 'Ты можешьnot take your own bet.');
define('GAME5050_NOT_ENOUGH_MONEY', 'Ты do not have enough деньги to match their bet.');
define('GAME5050_YOU_LOST', 'Ты have lost.');
define('GAME5050_YOU_WON', 'Ты have won!');
define('GAME5050_NOTIFY_WON', 'Ты won the $%d bid (Bet was %d).');
define('GAME5050_NOTIFY_LOST', 'Ты lost the $%d bid (Bet was %d).');
define('GAME5050_NOT_MUCH_MONEY', 'Ты don\'t have that much деньги.');
define('GAME5050_NOT_VALID_MONEY', 'Please enter a valid amount of деньги.');
define('GAME5050_ADDED_MONEY', 'Ты have added %d деньги.');
define('GAME5050_BET_IS_LIMITED', 'Ты можешьnot make more than %s bets at the same time.');

//5050Game1
define('GAME15050_REFRESH', '50/50 Chance Points Game - <a href="%s">Refresh Page');
define('GAME15050_GAME_RULES', 'Game rules');
define('GAME15050_GAME_RULE_1', '2 inmates bet the same amount of point.');
define('GAME15050_GAME_RULE_2', 'A winner is randomly picked. The winner receives all the point.');
define('GAME15050_GAME_RULE_3', 'Ты можешь have a maximum of %s bets posted at the same time.');
define('GAME15050_ADD_BET', 'Add a new bet');
define('GAME15050_AMOUNT_BID', 'Amount of points to bid.');
define('GAME15050_MIN_MAX_BET', 'minimum 10 / max 10,000 points bet');
define('GAME15050_MAKE_BET', 'Make Bet');
define('GAME15050_CUR_BETS', 'Current Bets');
define('GAME15050_NO_BET', 'No bets found.');
define('GAME15050_INMATE', 'Inmate');
define('GAME15050_BET_AMOUNT', 'Bet Amount');
define('GAME15050_TAKE_BET', 'Take Bet');
define('GAME15050_REMOVE_BET', 'Remove Bet');
define('GAME15050_NOTIFY_WON', 'Ты won the %d bid (Bet was %d).');
define('GAME15050_NOTIFY_LOST', 'Ты lost the %d bid (Bet was %d).');
define('GAME15050_NOT_VALID_POINTS', 'Please enter a valid amount of points.');
define('GAME15050_NOT_MUCH_POINTS', 'Ты don\'t have that much points.');
define('GAME15050_ADDED_POINTS', 'Ты have added %d points.');
define('GAME15050_NOT_ENOUGH_POINTS', 'Ты don\'t have enough points to match their bet.');

//announcement
define('ANNOUNCE_PAGE_HEAD', 'Last 5 Announcements');

//astore
define('ASTORE_PAGE_HEAD', 'Armor');
define('ASTORE_WELCOME_MSG',
    'Welcome to Crazy Riley\'s Armor Emporium! Please take as much time as you would like to browse through my selection of goods.');
define('ASTORE_REQ_SEC_LEVEL', 'Requires Security Level');
define('ASTORE_NOT_REAL_ITEM', 'That isn\'t a real item.');
define('ASTORE_CANT_BUY', 'Ты можешьnot buy this item.');
define('ASTORE_REQ_SEC_LEVEL_TO_BUY', 'Requires Security Level %d to buy a %s');
define('ASTORE_PURCHASED_ITEM', 'Ты have purchased a %s');
define('ASTORE_NOT_ENOUGH_MONEY', 'Ты do not have enough деньги to buy a %s');

//store
define('STORE_PAGE_HEAD', 'Weapons');
// for quest 2
define('STORE_WRONG_INGREDIENTS',
    'I don\'t think your current inventory items will ever make a bomb. Come back to me when you have more interesting components.');
define('STORE_ASSEMBLE', 'Weaponsmith');
define('STORE_ASSEMBLE_TRY', 'Have a little talk');
define('STORE_DYNAMITE_BUY_SUCCESS_Q2', 'The weaponsmith takes the components and the $1,000,000,000 из you, looking eager to build some impressive material.<br>
<p>Ты leave him a few hours to his job, hanging around in the prison yard.</p>
<br><p>
When you come back, he has a smile on his face:
"Hey buddy. I have finished our puppy. Take good care of it."
And then he handles a small cereal box, which looks kinda innocent.
</p><br>
<p>
Ты think it might be a good time to go back to meet the shady looking stranger again.
</p>');
define('STORE_DYNAMITE_BUY_FAILED_MONEY_Q2',
    'I think I could make the bomb with what you have here. But I still need to earn something из this, got me ? $1,000,000,000 will be enough to cover my tracks and make sure I look the other way, right ? Come back when you have them with you.');
// end quest 2
define('STORE_WELCOME_MSG',
    'Welcome to Bob\'s Weapon Emporium! Please take as much time as you would like to browse through my selection of goods.');
define('STORE_DYNAMITE_BUY_FAILED',
    'Ты tell the weapon dealer you need some dynamite, and tells him that big brother sent you. He seems a little afraid, and tells you that he можешь only sell dynamite for $550M since it is a really dangerous material to smuggle into the prison. Once you have the деньги on you, you можешь come back and ask for it again.');
define('STORE_DYNAMITE_BUY_FAILED_MONEY',
    'Ты do not have the деньги anymore. Please come back with the деньги once you have it.');
define('STORE_DYNAMITE_BUY_SUCCESS',
    'Ты tell the weapon dealer you need some dynamite, and tells him that big brother sent you. He seems a little afraid, and asks for the $550M first. Ты hand them over, and he goes into the stock room. When he comes back, he has a little donut bag with the dynamite in it. Ты swiftly take it, and hide it where people will not find it (it will not show in inventory).');

//DB Table items
define('CHAINSAW_NAME', 'Chainsaw');
define('CHAINSAW_DESC', 'This will help cutting through any material, flesh, metal, or anything.');
define('VCAPE_NAME', 'Vampire cape');
define('VCAPE_DESC',
    'Ты will definitely look scary wearing this cape. At least, enough time to deal a hit to your opponent.');
define('PAN_LID_NAME', 'Pan lid');
define('PAN_LID_DESC',
    'There is nothing like a good ol\' pan lid when you need to parry your opponent kitchen tools. It можешь also be использоватьd to hide your night pot contents.');
define('MATRESS_NAME', 'Matress');
define('MATRESS_DESC', 'A large matress, good enough to stop some hits and provide a nice but bulky shield.');
define('ROTT_NAME', 'Rottweiler');
define('ROTT_DESC',
    'A self-defense dog, trained to attack anyone coming at you with bad intentions. Can be использоватьd for attack as well as defense.');
define('BGUARD_NAME', 'Body guard');
define('BGUARD_DESC', 'A hired body guard who will give his life to protect yours (which is definitely worth more).');
define('SLINGSHOT_NAME', 'Slingshot');
define('SLINGSHOT_DESC', 'A simple slingshot, good to shoot a rock between your opponent\'s eyes.');
define('GLASSPIECE_NAME', 'Piece of glass');
define('GLASSPIECE_DESC', 'A bottle remnant that will give you a deadly advantage in melee fights.');
define('IBAR_NAME', 'Iron Bar');
define('IBAR_DESC', 'A sturdy iron bar, the definite choice for crushing things.');
define('AXE_NAME', 'Axe');
define('AXE_DESC',
    'A lumberjack axe. Ты wonder how it has come into a prison, but then you realize how good this will be into a fight.');
define('TOWEL_NAME', 'Towel');
define('TOWEL_DESC', 'It\'s just a towel.');
define('AWAKE_PILL_NAME', 'Awake Pill');
define('AWAKE_PILL_DESC', 'A пилюля that refills your awake.');
define('SHARPENED_PIECE_OF_PLASTIC_NAME', 'Sharpened Piece of Plastic');
define('SHARPENED_PIECE_OF_PLASTIC_DESC', 'Knife-sharp piece of plastic.');
define('COAT_HANGER_NAME', 'Coat Hanger');
define('COAT_HANGER_DESC', 'Well, you wouldn\'t want a creased shirt would you?');
define('SHARPENED_SPOON_NAME', 'Sharpened Spoon');
define('SHARPENED_SPOON_DESC', 'Stolen из the cafeteria.');
define('SHANK_NAME', 'Shank');
define('SHANK_DESC', 'Metal sharpened like a knife, with a bottom tightly wrapped with a piece of cloth as a handle.');
define('KNIFE_NAME', 'Knife');
define('KNIFE_DESC', 'A weapon with a handle and blade with a sharp point.');
define('CHAIN_NAME', 'Chain');
define('CHAIN_DESC', 'Series of metal rings fitted into one another to make a flexible ligament.');
define('KNUCKLE_DUSTERS_NAME', 'Knuckle Dusters');
define('KNUCKLE_DUSTERS_DESC', 'Iron bar with finger holes in it to give weight to a punch.');
define('MEAT_CLEAVER_NAME', 'Meat Cleaver');
define('MEAT_CLEAVER_DESC', 'Butcher\'s tool for chopping meat; has a tanged metal blade fitted into a wooden handle.');
define('SCREWDRIVER_NAME', 'Screwdriver');
define('SCREWDRIVER_DESC',
    'A handy tool for driving screws. But nobody said it couldn\'t be использоватьd as a weapon...');
define('GUARDS_GUN_RUBBER_BULLETS_NAME', 'Guards Gun [Rubber Bullets]');
define('GUARDS_GUN_RUBBER_BULLETS_DESC', 'A gun that the Guards carry which fires only rubber bullets.');
define('9MM_PISTOL_NAME', '9mm Pistol');
define('9MM_PISTOL_DESC', 'A firearm that is held and fired with one hand.');
define('PILLOW_NAME', 'Pillow');
define('PILLOW_DESC', 'Lovely soft item where your head lays on those cold nights.');
define('DINNER_TRAY_NAME', 'Dinner Tray');
define('DINNER_TRAY_DESC', 'Wooden tray stolen из the cafe, ready to be slipped under your shirt.');
define('PADDED_SHIRT_NAME', 'Padded Shirt');
define('PADDED_SHIRT_DESC', 'Shirt with carefully placed metal plates inside and around it.');
define('BIBLE_NAME', 'Bible');
define('BIBLE_DESC', 'Use it under your shirt for stopping knifes or rubber bullets.');
define('STAB_PROOF_VEST_NAME', 'Stab Proof Vest');
define('STAB_PROOF_VEST_DESC', 'A vest capable of resisting the impact of a knife or other sharp objects.');
define('BULLET_PROOF_VEST_NAME', 'Bullet Proof Vest');
define('BULLET_PROOF_VEST_DESC', 'A vest capable of resisting the impact of a bullet.');
define('LIQUID_ARMOUR_VEST_NAME', 'Liquid Armour Vest');
define('LIQUID_ARMOUR_VEST_DESC',
    'Free-flowing goo, when something hits it at high velocity, the bits cluster into a rigid barrier.');
define('BATON_NAME', 'Baton');
define('BATON_DESC', 'A baton stolen из a clumsy guard.');
define('TAZER_NAME', 'Tazer');
define('TAZER_DESC',
    'A powerful weapon that discharges thousands of electric volts into the Inmates, which caиспользоватьs horrible pain.');
define('RAZER_BLADE_NAME', 'Razer Blade');
define('RAZER_BLADE_DESC',
    'This weapon is made of razor-sharp blades and has killed 9 Inmates and 3 Guards. Inmates kill only to GET their hands on this legendary weapon.');
define('IRON_ARMOR_NAME', 'Iron Armor');
define('IRON_ARMOR_DESC', 'Nothing goes through this armor.');
define('SHOTGUN_NAME', 'Shotgun');
define('SHOTGUN_DESC', 'This weapon is из a cruel ex-warden that has killed dozens of innocent Inmates.');
define('RIOT_SHIELD_NAME', 'Riot Shield');
define('RIOT_SHIELD_DESC', 'A Riot Shield.');
define('GOLD_DESERT_EAGLE_NAME', 'Gold Desert Eagle');
define('GOLD_DESERT_EAGLE_DESC', 'Gold plated hand gun with medium capacity, effective at close range.');
define('SWAT_VEST_NAME', 'SWAT vest');
define('SWAT_VEST_DESC', 'Taken из the SWAT facilities, this vest offers the best protection currently on the market.');
define('GUARD_PROTECTION_NAME', 'Guard Protection');
define('GUARD_PROTECTION_DESC', 'It protects you during one hour из attacks and mugging.');
define('HEALTH_PILL_NAME', 'Health Pill');
define('HEALTH_PILL_DESC',
    'The пилюля is использоватьd to reduce hospital time by 10 minutes or get the prisoner out of hospital if less then 10 minutes are left. Only one пилюля можешь be использоватьd per hour.');
define('GUARD_PORTABLE_CANNON_NAME', 'Guard Portable Cannon');
define('GUARD_PORTABLE_CANNON_DESC',
    'A portable можешьnon, coming in handy when guards need to destroy any kind of rebellion.');
define('HEALTH_PILL_NAME', 'Health Pill');
define('HEALTH_PILL_DESC',
    'The пилюля is использоватьd to reduce hospital time by 10 minutes or get the prisoner out of hospital if less then 10 minutes are left. Only one пилюля можешь be использоватьd per hour.');
define('SEMI_AUTOMATIC_RIFLE_NAME', 'Semi-automatic Rifle');
define('SEMI_AUTOMATIC_RIFLE_DESC',
    'A semi-automatic, or self-loading firearm is a gun that requires only a trigger pull for each round that is fired, unlike a single-action revolver, a pump-action firearm, a bolt-action firearm, or a lever-action firearm, which require the shooter to manually chamber each successive round. Included in a rifle, it gives a considerable advantage over the opponent single-shot guns or rifles.');
define('REINFORCED_KEVLAR_VEST_NAME', 'Reinforced Kevlar Vest');
define('REINFORCED_KEVLAR_VEST_DESC',
    'A reinforced kevlar vest is simply a kevlar vest with additional composite protection materials added, for better defense against impacts and damage.');
define('DOUBLE_BARRELED_SHOTGUN_NAME', 'Double-barreled Shotgun');
define('DOUBLE_BARRELED_SHOTGUN_DESC',
    'A double-barreled shotgun is a shotgun with two parallel barrels, allowing two shots to be fired in quick succession. Simply said, it allows you to kill twice as fast compared to a common shotgun.');
define('ARMY_JACKET_NAME', 'Army jacket');
define('ARMY_JACKET_DESC',
    'An army jacket is the standard army torso equipment, reinforced with protection and electronic devices.');
define('FULLY_AUTOMATIC_RIFLE_NAME', 'Fully automatic Rifle');
define('FULLY_AUTOMATIC_RIFLE_DESC',
    'An automatic firearm is a firearm that automatically extracts and ejects the fired cartridge case, and loads a new case, usually through the energy of the fired round. The term можешь be использоватьd to refer to semi-automatic firearms, which fire one shot per pull of the trigger, or fully automatic firearms, which will continue to load and fire ammunition until the trigger (or other activating device) is released or until the ammunition is exhausted. Fully automatic weapons tend to be restricted to military and police organizations in most developed countries, becaиспользовать of their dangerousness.');
define('MODERN_ARMY_GRADE_SUIT_NAME', 'Modern Army grade suit');
define('MODERN_ARMY_GRADE_SUIT_DESC',
    'The modern army suit equips the currently deployed modern army troops, and delivers the best electronic hi-tech devices for enemy recognition, targeting and precognition. It also is reinforced with extremely sturdy components.');
define('SNIPER_RIFLE_NAME', 'Sniper Rifle');
define('SNIPER_RIFLE_DESC',
    'A sniper rifle is a rifle использоватьd to ensure accurate placement of bullets at longer ranges than small arms. With this rifle, you will be able to deal a hit with extreme accuracy and range.');
define('STEALTH_FINE_ALLOY_SUIT_NAME', 'Stealth fine alloy suit');
define('STEALTH_FINE_ALLOY_SUIT_DESC',
    'Why having the best defense, if the enemy simply можешьnot see you ? This is the philosophy behind the stealh fine allow suit. It is equipped with advanced optical devices that almost entirely hides you из the eyes of your enemies, making any hit targeted against you considerably harder.');
define('GATLING_GUN_NAME', 'Gatling Gun');
define('GATLING_GUN_DESC',
    'A Gatling gun is a gun with multiple firing pins and breeches connected to multiple rotating barrels. It takes incredible strength and experience to operate it, but it also delivers considerable damage compared to other firefarms.');
define('EXO_SKELETON_NAME', 'Exo-skeleton');
define('EXO_SKELETON_DESC',
    'Powered exoskeletons are designed to assist and protect the wearer. They may be designed, for example, to assist and protect soldiers and construction workers, or to aid the survival of people in other dangerous environments. This armor is still at a prototype stage, but it works incredibly well compared to conventional armors.');
define('GUARD_PORTABLE_BUNGALOW_NAME', 'Guard Portable Bungalow');
define('GUARD_PORTABLE_BUNGALOW_DESC',
    'Nothing можешь pierce through a bungalow. I mean it. For Заключенный Guards only.');

//description.hp
define('DESC_NO_ITEM_ID', 'No item id specified on item description page.');
define('DESC_SELL_VALUE', 'Sell Value');
define('DESC_SHOP_COST', 'Shop Cost');
define('DESC_ATTACK_MODIFIER', 'Атака Modifier');
define('DESC_DEFENSE_MODIFIER', 'Defense Modifier');
define('DESC_REQ_LEVEL', 'Required Level');
define('DESC_REQ_SEC_LEVEL', 'Required Security Level');

//attack
define('ATK_LESS_ENERGY', 'Ты need to have at least 25% of your energy if you want to attack someone.');
define('ATK_USER_IN_SHOWER', 'Ты можешьnot attack anyone when you are in the showers.');
define('ATK_USER_IN_HOSPITAL', 'Ты можешьnot attack anyone when you are hospitalized.');
define('ATK_CANT_YOURSELF', 'Ты можешьnot attack yourself.');
define('ATK_CANT_PG', 'Ты можешьnot attack prison guards !');
define('ATK_CANT_GANG_MEMBER', 'Ты можешьnot attack same gang members.');
define('ATK_CANT_IN_SHOWER', 'Ты можешьnot attack someone that is in the showers.');
define('ATK_CANT_IN_HOSPITAL', 'Ты можешьnot attack a hospitalized inmate.');
define('ATK_CANT_PROTECTED_BY_GUARD', 'Ты можешьnot attack an inmate protected by guards.');
define('ATK_CANT_UNCONSCIOUS', 'Ты можешьnot attack someone that is already unconscious.');
define('ATK_CANT_LESS_LEVEL',
    'Ты можешьnot attack someone that is level 3 or less becaиспользовать you are higher than level 4.');
define('ATK_OPPONENT_SPEED_1', 'Тыr opponent speed is %s compared to yours, so you are able to attack first !');
define('ATK_OPPONENT_SPEED_2', 'Тыr %s opponent speed caught you off-guard, so you are attacked first !');
define('ATK_CRITICAL_HIT', 'CRITICAL HIT !');
define('ATK_OPPONENT_HIT', '%s hits you for %s damage');
define('ATK_WITH_STRENGTH', 'with %s strength');
define('ATK_USING_WEAPON', 'using their %s.');
define('ATK_YOUR_HIT', 'Ты hit %s for %s damage using your %s.');
define('ATK_YOUR_HIT_MODED_STRENGTH', 'Тыr hits pierce through the %s enemy defense.');
define('ATK_YOUR_HIT_NORMAL_STRENGTH', 'Тыr hits have a hard time cutting through the %s enemy defense.');
define('ATK_GANG_WAR_ADD_FAILED_1', 'Ты aren\'t at war with this prisoner\'s gang.');
define('ATK_GANG_WAR_ADD_FAILED_2', 'War has already ended.');
define('ATK_YOU_HOSPITALIZED', 'Ты were hospitalized by %s for %d minutes.');
define('ATK_PG_HOSPITALIZED',
    'Ты hospitalized %s which was a prison guard ! For this brilliant effort, you will be more respected by other prisoners (at least for some time) !');
define('ATK_YOU_HOSPITALIZED_BY_USER', 'Ты were hospitalized by %s for %d minutes. $%d were stolen из you.');
define('ATK_HOSPITALIZED_1',
    'Ты hospitalized %s. Ты gain %d exp and stole $%d ($%d goes to gang) из %s. Тыr gang gain %d exp.');
define('ATK_HOSPITALIZED_2', 'Ты hospitalized %s. Ты gain %d exp and stole $%d из %s.');
define('ATK_YOU_ATTACKED_AND_HOSPITALIZED',
    'Ты attacked and were hospitalized by %s for 20 minutes. $%d were stolen из you.');
define('ATK_YOU_WERE_ATTACKED',
    'Ты were attacked by %s who lost the fight, you won %d exp and stole $%d ($%d went to your gang). Тыr gang gain %d exp.');
define('ATK_YOU_WERE_ATTACKED_1', 'Ты were attacked by %s who lost the fight, you won %d exp and stole $%d');
define('ATK_HOSPITALIZED_3', '%s Hospitalized you and stole $%d из you.');
define('ATK_CONTRIBUTE_TO_CONTRACT',
    'This attack will contributed to the your contract. Hospital time done is %d minutes.');
define('ATK_PAGE_HEAD', 'Fight Cell');
define('ATK_FIGHT_WITH', 'Ты are in a fight with %s.');

//bank
define('BANK_PAGE_HEAD', 'Bank - max amount ($%s) - max interest ($6,000,000)');
define('BANK_WELCOME_MSG_1', 'Welcome to the bank. Ты currently have $%s in your account.');
define('BANK_WELCOME_MSG_2', 'Ты will make $%s из interest next rollover.');
define('BANK_OPEN_ACT', 'Open a new bank account');
define('BANK_OPEN_ACT_MSG', 'Ты do not currently have an account with us. Would you like to open one for $5,000?');
define('BANK_OPEN_ACT_OPEN_MSG', 'Ты successfully opened a new bank account !');
define('BANK_DEPOSITED', 'Money deposited.');
define('BANK_NO_ACT', 'Ты можешьnot deposit деньги without having a bank account.');
define('BANK_NOT_VALID_AMT', 'Please enter a valid and positive amount.');
define('BANK_NOT_ENOUGH_AMT', 'Ты do not have enough деньги to deposit $%s.');
define('BANK_DEPOSITE_FAILED_MAX_AMT', 'The max amount on bank is $%s. Thanks.');
define('BANK_DEPOSITE_FAILED_1', 'Error while depositing the деньги, please refresh and try again.');
define('BANK_DEPOSITE_FAILED_2',
    'Money could not be deposited becaиспользовать you were mugged or attacked in the meantime.');
define('BANK_WITHDRAWN', 'Money withdrawn.');
define('BANK_WITHDRA_NO_ACT', 'Ты можешьnot withdraw деньги without having a bank account.');
define('BANK_WITHDRA_NOT_ENOUGHT_MONEY', 'Ты do not have enough деньги in the bank to withdraw $%s');
define('BANK_WITHDRA_NOT_ENOUGHT_POKER_MONEY', 'Ты do not have enough poker деньги to withdraw $%s');
define('BANK_POKER_MSG', 'Ты currently have %s in your poker account. Ты можешь deposit %s amount  in Poker');
define('BANK_WITHDRAW_POKER_MONEY', 'Withdraw Poker Money');
define('BANK_DEPOSIT_POKER_MONEY', 'Deposit Poker Money');
define('BANK_POKER_DEPOSIT_LIMIT_OVER', 'Тыr  Poker deposit limit is over');
define('BANK_POKER_DEPOSIT_LIMIT_EXCEED', 'Тыr deposit limit exceed');
define('BANK_POKER_DEPOSIT_ONLY', 'Ты можешь deposit only $%d ');
define('BANK_POKER_DEPOSITED', 'Poker Money deposited .');
define('BANK_POKER_WITHDRAWN', 'Poker Money withdrawn.');
define('BANK_MONEY_CHANGED', 'Тыr hand деньги has changed in the meantime, so less деньги was deposited.');
define('BANK_NOT_MONEY_TO_OPEN', 'Ты do not have enough деньги to open a bank account, you need $%s.');
define('BANK_NOT_HAVT_ACT', 'Ты do not currently have an opened bank account.');
define('BANK_ACT_CLOSED', 'Ты successfully closed your bank account.');

//Ban User
define('USER_BAN_SHOUTBOX', 'Ты have been ban из shoutbox for %d days');
define('USER_WARN_SHOUTBOX', 'Ты have been warn becaиспользовать of your behaviour in shoutbox');
define('USER_BAN_APPLIED', 'Ban/Warn applied to использоватьr');
define('USER_BAN_HEAD', 'Ban/Warn User');
define('USER_BACK_SHOUT', 'Back to Shout');
define('USER_BAN_TO_ADMIN', 'To admin');
define('USER_BAN_TO_USER', 'To использоватьr');
define('USER_PAST_BANS', 'Past Bans/Warnings');
define('USER_BAN_REASON', 'Reason');
define('USER_BAN_DAYS_LEFT', 'Days Left');
define('USER_BAN_MOD', 'Mod');

define('USER_BAN_CHAT', 'Ты have been ban из chat for %d days');
define('USER_WARN_CHAT', 'Ты have been warn becaиспользовать of your behaviour in chat');
define('USER_BACK_CHAT', 'Back to Чат');
define('USER_SELECT_ID', 'Select User Id');

//использоватьr
define('USER_NOT_BELONG_GANG', 'Sorry, but you do not belong to any gang.');
define('USER_NOT_REQUIRED_GANG_PERM', 'Sorry, but you do not have the required gang permission level.');
define('USER_LEFT_GANG', 'Ты have left your gang !');
define('USER_DELETED_GANG', 'Ты have deleted your gang.');
define('USER_REGULAR', 'Regular Заключенныйer');
define('USER_RESPECTED', 'Respected Заключенныйer');
define('USER_MODERATOR', 'Moderator');
define('USER_SUPER_MODERATOR', 'Super Moderator');
define('USER_ADMIN', 'Admin');
define('USER_CANT_ACTIVATE_SKILL', 'Ты можешьnot activate this skill.');
define('USER_ERROR_ACTIVATING_SKILL', 'Error while activating the skill. Please refresh and try again.');
define('USER_ERROR_DISACTIVATING_SKILL', 'Error while disactivating the skill. Please refresh and try again.');
define('USER_ERROR_INCREASING_SKILL', 'Error while increasing the skill level. Please refresh and try again.');
define('USER_CANT_RAISE_SKILL_LEVEL', 'Ты можешьnot raise this skill level.');
define('USER_NOT_ENOUGH_POINTS_RAISE_SEC_SKILL', 'Ты do not have enough security points to raise this security skill.');
define('USER_CANT_QUIT_JOB', 'Ты можешьnot quit your job, you do not have any !');
define('USER_INVALID_JOB', 'Invalid job specified.');
define('USER_NOT_MATCH_JOB_REQUIRMENT', 'Ты do not match the requirements to become a %s.');
define('USER_NOT_MUG_YOURSELF', 'Ты можешьnot mug yourself.');
define('USER_NOT_MUG_IN_SHOWERS', 'Ты можешьnot mug inmates when you are in the showers.');
define('USER_HOSPITALIZED_NOT_MUG', 'Ты можешьnot mug inmates when you are hospitalized.');
define('USER_NOT_MUG_PG', 'Ты можешьnot mug prison guards !');
define('USER_NOT_MUG_ENOUGH_NERVE', 'Ты need to have at least 10 nerve to mug an inmate.');
define('USER_NOT_MUG_HOSPITALIZED', 'Ты можешьnot mug a hospitalized inmate.');
define('USER_NOT_MUG_PROTECTED_PG', 'Ты можешьnot mug an inmate protected by guards.');
define('USER_NOT_MUG_INMATE_SHOWERS', 'Ты можешьnot mug an inmate which is in the showers.');
define('USER_NOT_MUG_HIGHER_LEVEL',
    'Ты можешьnot mug someone that is level 1 becaиспользовать you are higher than level 3.');
define('USER_MUGGED_SOAP', 'Ты mugged a piece of soap. Not really использоватьful, so you drop it and run away.');
define('USER_MUG_OUT_OF_NERVE', 'Ты stumble while trying to mug. For some reason, you suddently were out of nerve.');
define('USER_MUGGED_BY', 'Ты were mugged by %s. $%s were taken из you.');
define('USER_CRITICAL_MUG', 'CRITICAL MUG');
define('USER_MUGGED', 'Ты mugged %s for $%s ($%s goes to gang) and %s EXP');
define('USER_MUGGED_CAUGHT',
    'Ты were going to be mugged by %s, but your speed was higher and you saw him coming. Ты won %s EXP.');
define('USER_MUGGED_FAILED_MSG', 'Their speed is higher than yours, so you failed.');
define('USER_MUST_IN_SAME_PRISON', 'Ты must be in the same prison as the person you are targeting.');
define('USER_ALREADY_BANNED', 'User is already banned.');
define('USER_CANT_BAN_ADMIN', 'Ты можешьnot ban an admin without revoking his rights first.');
define('USER_CANT_BANNED', 'User could not be banned.');
define('USER_CANT_UNBANNED', 'User could not be unbanned. Please try again later.');
define('USER_RECEIVED_WARNING', 'Ты have received a warning for : %s Repeated warnings можешь lead you to a ban.');
define('USER_CANT_PMAIL_EMPTY_SUBJECT', 'Ты можешьnot send a pmail with empty subject.');
define('USER_CANT_PMAIL_EMPTY_TEXT', 'Ты можешьnot send a pmail with empty text.');
define('USER_CONFIRMATION_ERROR', 'Confirmation error for given использоватьr and code !');
define('USER_LOST_LEVEL', 'Ты have lost one level.');
define('USER_RAISED_LEVEL', 'Ты have raised one level.');
define('USER_REACHED_MAX_LEVEL',
    'Ты have reached the maximum level (%s). Ты можешьnot raise your level anymore из now on.');
define('USER_RESIGN_FROM_GANG', 'Ты have to resign из your gang first.');
define('USER_HAVENT_INVITED', 'Ты do not have any invitations из this gang.');
define('USER_GANG_INVITATION_CANT_DELETED', 'Gang invitation could not be deleted.');
define('USER_LEVE_GANG_BEFORE_JOIN_NEW', 'Ты must leave your current gang before joining a new gang.');
define('USER_JOIND_UR_GANG', '%s (id-%s) just joined your gang.');
define('USER_CANT_LEAVE_GANG_LEADING',
    'Ты можешьnot be removed из a gang you are leading. The current gang leader has to be changed first.');
define('USER_BORROWED_ITEM_RETURNED',
    'A borrowed %s is returned back to your gang becaиспользовать you are no longer in it.');
define('USER_CANT_DEL_GANG_NOT_LEADER', 'Ты можешьnot delete your gang becaиспользовать you are not the leader.');
define('USER_CANT_DEL_GANG_ACTIVE_WAR', 'Ты можешьnot delete your gang if you are in an active war.');
define('USER_CANT_REFILL_HP_ALREADY_MAX', 'Ты можешьnot refill your hp if it is already at max value.');
define('USER_CANT_REFILL_AWAKE_ALREADY_MAX', 'Ты можешьnot refill your awake if it is already at max value.');
define('USER_CANT_REFILL_ENERGY_ALREADY_MAX', 'Ты можешьnot refill your energy if it is already at max value.');
define('USER_CANT_REFILL_NERVE_ALREADY_MAX', 'Ты можешьnot refill your nerve if it is already at max value.');
define('USER_ALREADY_IN_CITY', 'Ты are already in %s.');
define('USER_NOT_LVL_TO_GO_CITY', 'Ты are not high level enough to go there.');
define('USER_NOT_MONEY_TO_GUARDS', 'Ты do not have enough деньги to bribe the guards.');
define('USER_NOT_HAVE_ITEM', 'Ты do not have any %s.');
define('USER_NOT_HAVE_LVL_USE_WEAPON',
    'Ты do not met the level requirements to использовать this weapon. Required level : %s');
define('USER_NOT_HAVE_LVL_USE_ARMOR',
    'Ты do not met the level requirements to использовать this armor. Required level : %s');
define('USER_HAVENT_EQUIPPED_WEAPON', 'Ты do not have any equipped weapon.');
define('USER_HAVENT_EQUIPPED_ARMOR', 'Ты do not have any equipped armor.');
define('USER_ALLOWED_HEALTH_PILL_HOUR', 'Ты are only allowed to использовать one Health Pill per hour');
define('USER_USE_HEALTH_PILL_IN_HOSPITAL', 'Ты можешь only использовать Health Pills while you are in hospital');
define('USER_SAME_GANG_MEMBER_CANT_VOTE', 'Users belonging to the same gang можешьnot vote on each other.');
define('USER_ALREADY_VOTED', 'Ты have given your rating to this использоватьr for today.');
define('USER_INVALID_RATING', 'Invalid rating. Please refresh and try again.');
define('USER_NOT_MONEY_BUY_STEROID_COCKTAIL', 'Ты do not have enough деньги to buy Steroid Cocktail.');
define('USER_HAVE_NOT_ENOUGH_MONEY', 'Ты don\'t have enough деньги.');
define('USER_INVALID_ID', 'User id is not valid.');
define('USER_POINTS_MAX_ERROR', 'Ты можешьnot have more than %s points');

//bbcode
define('BBCODE_WRONG_PARAM_1', 'Paramater array not an array.');
define('BBCODE_WRONG_PARAM_2', 'Имя parameter is required.');
define('BBCODE_WRONG_PARAM_3', 'Имя можешь only contain letters.');
define('BBCODE_WRONG_PARAM_4', 'HtmlBegin paremater not specified!');
define('BBCODE_ERROR_MSG_1',
    'Ты didn\'t specify the HtmlEnd parameter, and your HtmlBegin parameter is too complex to change to an HtmlEnd parameter.  Please specify HtmlEnd.');
define('BBCODE_ERROR_MSG_2', 'The name you specified is already in использовать.');

//best
define('BEST_HOUSE', 'Best Hoиспользовать');
define('BEST_MORE_MONEY', 'More Money');
define('BEST_MORE_POINTS', 'More Points');
define('BEST_MORE_STATS', 'More Stats');
define('BEST_MORE_SHARES', 'More Shares');
define('BEST_MORE_CROP', 'More Crop');

//blackjack
define('BLACKJACK_LESS_MONEY', 'Ты можешьnot bet less than $100.');
define('BLACKJACK_MORE_MONEY', 'Ты можешьnot bet more than $%s');
define('BLACKJACK_NOT_ENOUGH_MONEY', 'Ты do not have that much деньги to bet.');
define('BLACKJACK_WON', 'Ты won %s with a Blackjack !');
define('BLACKJACK', 'Blackjack');
define('BLACKJACK_YOU_WON', 'Ты Won!');
define('BLACKJACK_YOU_BUSTED', 'Ты Busted!');
define('BLACKJACK_A_DRAW', 'A Draw!');
define('BLACKJACK_YOU_LOST', 'Ты Lost!');
define('BLACKJACK_CANT_REFRESH', 'Ты можешьnot refresh.');
define('BLACKJACK_BUSTED', 'Ты busted with %s and lost %s, better luck next time.');
define('BLACKJACK_BUSTED_BOTH', 'Ты and the dealer both busted with %s and %s, better luck next time.');
define('BLACKJACK_BUSTED_DEALER', 'The dealer busted with %s, you won %s</span>!');
define('BLACKJACK_TIED', 'Ты and the dealer both tied with %s and %s, better luck next time.');
define('BLACKJACK_LOST', 'Ты lost %s with %s, the dealer had %s, better luck next time.');
define('BLACKJACK_WON_1', 'Ты won %s with %s, the dealer had %s!');
define('BLACKJACK_PLACE_BET', 'Place Тыr Bet');
define('BLACKJACK_MAX_BET', 'Max Bet');
define('BLACKJACK_MAX_GAMES', 'Max Games per day');
define('BLACKJACK_AVAIL_GAMES', 'Available games');
define('BLACKJACK_TABLE', 'The Table');
define('BLACKJACK_DEALER_CARDS', 'Dealer\'s Cards');
define('BLACKJACK_YOUR_CARDS', 'Тыr Cards');
define('BLACKJACK_PLAY_AGAIN', 'Play Again');
define('BLACKJACK_HIT', 'Hit');
define('BLACKJACK_STAND', 'Stand');

//brokerage
define('BROK_VALID_AMT', 'Please enter a valid amount of shares to buy.');
define('BROK_MSG_1',
    'Due to current market regulations, you можешь only buy shares of stocks that are selling at $10 or more.');
define('BROK_CANT_BUY_SHARE',
    'Sorry, but you можешьnot buy so much shares. Max total shares you можешь own: 1,000,000.');
define('BROK_NOT_ENOUGH_MONEY', 'Ты do not have enough деньги.');
define('BROK_SHARE_BOUGHT',
    'Ты have bought %s shares for a total of $%s ($%s per share X %s shares + $%s transaction fee)');
define('BROK_VALID_AMT_SELL', 'Please enter a valid amount of shares to sell.');
define('BROK_NOT_ENOUGH_SHARE', 'Ты do not have enough shares.');
define('BROK_SHARE_SOLD',
    'Ты have sold %s shares for a total of $%s ($%s per share X %s shares - $%s transaction fee)');
define('BROK_STOCK_MARKET', 'Stock Market');
define('BROK_WELCOME_1',
    'Welcome to the stock market ! We are here to help further your wealth, so if there is anything we можешь do, just let us know!');
define('BROK_WELCOME_2',
    'Please keep in mind that we will be charging a 10% transaction fee on your stock transactions, when you buy or sell.');
define('BROK_WELCOME_3', 'Thanks for your interest in our services !');
define('BROK_STOCKS', 'Stocks');
define('BROK_NO_STOCK', 'No stocks found.');
define('BROK_COMPANY', 'Company Имя');
define('BROK_OWNED_SHARES', 'Owned Shares (Total value)');
define('BROK_SHARES_COST', 'Cost per Share');
define('BROK_NUM_SHARES_BUY_SELL', 'Number of shares to buy / sell');
define('BROK_TRANSACTION_TYPE', 'Transaction type');
define('BROK_RSS_FLUX', 'Stocks rss flux');
define('BROK_LOGS', 'Stock market logs');

//bus
define('BUS_MOVED_TO_DEST', 'Ты successfully paid $500 and arrived at your destination.');
define('BUS_HEAD', 'Transfer');
define('BUS_NO_CITIES', 'Sorry, but you можешьnot leave your prison at the moment.');
define('BUS_MSG_1',
    'Tired of %s? For $500 you можешь bribe the guards and get a bus ticket to any prison you want to go. If you do not have the cash on hand, it will automatically be taken из your bank.');
define('BUS_LVL_REQ', 'Lvl Req');
define('BUS_BRIBE_GUARDS', 'Bribe Guards');
define('BUS_NOT_MATCH_LVL', 'Ты do not match the level requirements to go there');

//cities
define('CITY_PANAMA', 'Panama');
define('CITY_ALCATRAZ', 'Alcatraz');
define('CITY_GUANTANAMO_BAY', 'Guantanamo Bay');
define('CITY_LONG_BAY', 'Long Bay');
define('CITY_UTAH', 'Utah');
define('CITY_DORCHESTER', 'Dorchester');
define('CITY_SAN_QUENTIN', 'San Quentin');
define('CITY_ATTICA', 'Attica');
define('CITY_MCNEIL_ISLAND', 'McNeil Island');
define('CITY_SING_SING', 'Sing Sing');

//buydrugs
define('DRUGS_STRANGER', 'The stranger has nothing to tell you at the moment.');
// Messages for first quest
define('DRUGS_STRANGER_1',
    'Ты talk with the shady looking stranger for a while. He says something that might be interesting : Apparently, somebody is looking for friends to find the way out of hell, to paradise. The guy in question is called Big Brother. It might be interesting    to start looking for him ? Big brother... Who could that be ?');
define('DRUGS_STRANGER_2', 'The stranger does not want anything из you.');
define('DRUGS_STRANGER_3',
    'Ты hand over the donut bag with the dynamite in it. The shady looking stranger picks it из you and tells you the next  part of the plan : Ты are supposed to come to him again on <font class="red">a wednesday, at 1 am</font>, well equipped, for the escape tempt, becaиспользовать this is the time when an escape has the most chances of success, according to big brother. He tells you that you will need to be equipped with a <font class="red">Riot shield</font> and a <font class="red">Gold desert eagle</font>. Ты also need to bring <font class="red">5 Awake пилюляs</font>, <font class="red">5 Guard protections</font> and <font class="red">1 Health пилюля</font>, so that you можешь использовать those during the escape. Write everything down, becaиспользовать he will not tell you again ! He finishes the talk by telling you that the fewer people knowing about the escape or also trying to escape, the bigger the chances of it succeeding for you ! (Inmates attempting to escape on the same day as you will make the chances of escaping drop). <br>');
define('DRUGS_STRANGER_SUCCESS', 'Ты meet up with the shady looking stranger and big brother. Big brother verifies that you have
            all your equipment, and tells you to follow him. <br>He leads you to the kitchens, where another inmate is waiting for you.
            He gets the dynamite out of his bag, and places the explosive charge against the south wall of the kitchen. Then you run to cover with
            Big brother and the other inmate. 30 seconds later, the whole wall explodes, and reveals the back of the prison yard. And now,
            you understand : This part of the yard has noone looking at this time of the night. However, the sound of the dynamite has alerted
            the guards, and an alarm siren goes off. Ты all run to the fence and big brother gets wire cutters out of his bag, before cutting
            a hole large enough for everyone to go through. For some reason, the fence isn\'t electrified anymore, he probably had a friend taking
            care of it. Then you start running. Ты are out of prison.<br><br>Then, all goes wrong : From nowhere to be seen, 5 federal cars drive directly towards
            your group and stop almost in front of you while you were running away. A guy comes out of one of the car, pointing a M16 at you, and tells you this :<br><br>"<b>Hi sirs, I am special agent Mohane.
            Nice attempt out there, but thanks to our snitch, we heard of the escape plan a long time ago. Well done tough. Now, please, hand over your weapon and equipment
            if you do not want to die right now.</b>". <br>Ты feel obliged, and release your gold desert eagle and your riot shield, and also all пилюляs that you
            brought along. Then the feds arrest you, and bring you back to Panama. Ты are back in hell.
            <br><br><font color="darkgreen"><b>Success : Even tough you are caught, you have succeeded in going out of the prison, at least for a while. <br>
            For this brillant achievement, the following happens :
            <ul>
                <li>Ты are brought back to Panama.</li>
                <li>Ты are level 1 again (1 experience point).</li>
                <li>Ты lost 85% of all your stats.</li>
                <li>Ты earned one security level.</li>
            </ul>

            Security levels grant you the following advantages :
            <ul>
                <li>Ты will have one star per security level next to your name, even if you have a special использоватьrname.</li>
                <li>+20% to all xp received per security level.</li>
                <li>+10% to all bonus to stats gained in training per security level.</li>
                <li>+3 security skill points that you можешь invest in the security skills on your cell page.</li>
                <li>Access to new cell and item. Each security level opens up new opportunities for cells and items you можешь buy. However
                some of them may only be unlocked if you reach the level needed to discover the item (for example, if the item is held
                in a higher prison.</li>
            </ul>
            </font>');
// Messages for the second quest
define('DRUGS_STRANGER_1_Q2', 'Ты may have an opportunity to escape again. I found a paper on a dead inmate, he was going to build a special bomb, able to break the hardest wall of the prison since it has been reinforced after your first escape. The rest will be handled.
The paper only has 5 riddles on it.<p style="color:darkgreen">Here is the quest riddle text:</p>
        <ul style="color:darkgreen">
                <li>We need 5 Fibonacci chains</li>
                <li>A godly power</li>
                <li>An electric device to detonate</li>
                <li>4 metallic cases</li>
                <li>and byte-my-energy-пилюля !</li>
            </ul>
    <p>
    Ты might want to check with our weapon smith friend once you think you have worked this out.
    </p>');
define('DRUGS_STRANGER_2_Q2', 'The stranger does not want anything из you.');
define('DRUGS_STRANGER_3_Q2',
    'Ты hand over the bomb to the stranger.<br>He tells you that you need to come back next wednesday, at 1 am, so that you можешь attempt the escape again with this amazing bomb. He will handle all the preparation in between.<br>He finishes the talk by remembering you that the fewer people knowing about the escape or also trying to escape, the bigger the chances of it succeeding for you ! (Inmates attempting to escape on the same day as you will make the chances of escaping drop).');
define('DRUGS_MSG_9_Q2', 'Ты meet up with the shady looking stranger for your second attempt. The stranger gives a call to another inmate to verify something, and tells you to follow him. <br>He leads you to the showers, where a lot of inmate are currently gathered waiting to be sacked by prison guards.<br>
Once you are there, everything starts to move fast:<br>
<br>
Ты see the stranger waving at an inmate, apparently giving a mutual agreed-upon sign.<br>
Then the inmate starts yelling and trying to grab the prison guards attention.<br>
Eventually, the inmate manages to start a fight and during the confusion, the stranger slips the bomb pack to another inmate, who runs to the showers wall to put it on the floor before activating it.<br>
A few seconds later, you start to hear the faint ticking of the bomb timer, and the stranger tells you he will go back to his place now, before wishing you luck.<br>
<br>
And, then, all goes wrong. A prison guard notices the ticking, and looks at the pack. Ты notice him, and try to run towards him to shut him, but he yells before you get a chance to silencing him. Then all the guards are on you, and some of them run towards the bomb. Then one of them has the great idea of shoving the bomb down the showers можешьalization (guessing it would be safer than just leaving it there).<br>
<br>
The bomb explodes in the можешьalizations, giving up an interesting flooding in the prison yard. But no escape chance. At least, not this time.<br>
<br>
When they finally have finished worrying about the bomb, they catch you and throw you back into your cell.

            <br><br><font color="red">Failure : Ты were caught before leaving the prison. Ты можешь try escaping again in the same time range next week if you want. Next time, try to be the first inmate attempting the escape, becaиспользовать chances drop drastically with multiple escapes the same day.<br></font>');
define('DRUGS_STRANGER_SUCCESS_Q2', 'Ты meet up with the shady looking stranger for your second attempt. The stranger gives a call to another inmate to verify something, and tells you to follow him. <br>He leads you to the showers, where a lot of inmate are currently gathered waiting to be sacked by prison guards.
Once you are there, everything starts to move fast:
<br><br>
Ты see the stranger waving at an inmate, apparently giving a mutual agreed-upon sign.<br>
Then the inmate starts yelling and trying to grab the prison guards attention.<br>
Eventually, the inmate manages to start a fight and during the confusion, the stranger slips the bomb pack to another inmate, who runs to the showers wall to put it on the floor before activating it.<br>
A few seconds later, you start to hear the faint ticking of the bomb timer, and the stranger tells you he will go back to his place now, before wishing you luck.
<br><br>
Ты wait in the showers, far away из the bomb, pretending you are one of the showers guy while the fight keeps on with the guards.
<br><br>
Then, the bomb suddendly explodes. Some of the inmates / guards are caught in the explosion, some are confиспользоватьd, and everyone starts to panic.<br>
During the confusion, you run through the wall hole, and finally see the outside.<br>
<br>
When you come out, you see that the showers wall was actually outside of the main prison fence, so you можешь run away directly. A normal bomb would never have gotten through this reinforced wall tough.<br>
<br>
Sadly, your run do not last long. Once again, for some unknown reason, some guards were waiting for you on the edge of the prison area.<br>
Ты look, and to your dismay, you see Mohane again.<br>
He stands there, far away, smiling at you, and keeping you in sight of his sniper rifle.<br>
<br>
He walks up to you, and catches you again.<br>
"Ты will never learn, will you ?"<br>
<br>
He doesn\'t need to talk any further... Ты have to follow him and the other guards, back to the prison.

            <br><br><font color="darkgreen"><b>Success : Even tough you are caught, you have succeeded in going out of the prison, at least for a while. <br>
            For this brillant achievement, the following happens :
            <ul>
                <li>Ты are brought back to Panama.</li>
                <li>Ты are level 1 again (1 experience point).</li>
                <li>Ты lost 25% of all your stats.</li>
                <li>Ты earned one security level.</li>
            </ul>
<br>
            Security levels grant you the following advantages :
            <ul>
                <li>Ты will have one star per security level next to your name, even if you have a special использоватьrname.</li>
                <li>+20% to all xp received per security level.</li>
                <li>+10% to all bonus to stats gained in training per security level.</li>
                <li>+3 security skill points that you можешь invest in the security skills on your cell page.</li>
                <li>Access to new cell and item. Each security level opens up new opportunities for cells and items you можешь buy. However
                some of them may only be unlocked if you reach the level needed to discover the item (for example, if the item is held
                in a higher prison.</li>
            </ul>
            </font>');

define('DRUGS_MSG_1', 'What are you trying to do ?');
define('DRUGS_MSG_2', 'The time has not come. Come back here on wednesday, at 1 am. Right now we are %s, at %s.');
define('DRUGS_MSG_3', 'Ты need 5 awake пилюляs for the escape attempt.');
define('DRUGS_MSG_4', 'Ты need 1 health пилюля for the escape attempt.');
define('DRUGS_MSG_5', 'Ты need 5 guard protections for the escape attempt.');
define('DRUGS_MSG_6',
    'Ты need to equip a gold desert eagle that belongs to you as a weapon. (Ты можешьnot be loaned the weapon).');
define('DRUGS_MSG_7',
    'Ты need to equip a riot shield that belongs to you as an armor. (Ты можешьnot be loaned the armor).');
define('DRUGS_MSG_8', 'Some of your items have gone missing. Come back here with all items.');
define('DRUGS_MSG_9',
    'Ты meet up with the shady looking stranger and big brother. Big brother verifies that you have all your equipment, and tells you to follow him. <br>He leads you to the kitchens, where another inmate is waiting for you. He gets the dynamite out of his bag, and places the explosive charge against the south wall of the kitchen. Then you run to cover with Big brother and the other inmate. 30 seconds later, the whole wall explodes, and reveals the back of the prison yard. And suddenly, a cohort of prison guards rush through the opened hole in the wall. Something went wrong, you have no clue what happened.<br>They arrest you, and bring you back to your cell, just after having confiscated your gold desert eagle, your riot shield, and your пилюляs. <br><br><font color="red">Failure : Ты were caught before leaving the prison. Ты можешь try escaping again in the same time range as soon as you bring all the required items again. Next time, try to be the first inmate attempting the escape, becaиспользовать chances drop drastically with multiple escapes the same day.<br></font>');
define('DRUGS_DONT_HAVE', 'Ты don\'t have any.');
define('DRUGS_SOLD_WEED', 'Ты sold all your poppy and got $%s');
define('DRUGS_BUY_ITEMS', 'Ты should buy one or more items');
define('DRUGS_NOT_ENOUGH_MONEY', 'Ты do not have enough деньги!');
define('DRUGS_NOT_EXIST', 'The drug you picked does not exist !');
define('DRUGS_PURCHASED', 'Ты have purchased %d %s for $%s.');
define('DRUGS_BUY', 'Buy %s.');
define('DRUGS_NUM', 'Number of Items');
define('DRUGS_COST', 'Each %s costs $%s');
define('DRUGS_HEAD', 'Shady-Looking Stranger');
define('DRUGS_MSG_BASE',
    'Hey there buddy. Want to buy some cocaine? It\'ll make you faster and help you pull off those bigger crimes! Best of all it will last you 15 minutes! Cocaine is only $5,000, so what are you waiting for? Or perhaps you want to get into the drug dealing business yourself... For $5,000 I will give enough seeds to plant an acre of sweet sticky weed. I will also buy weed at 200 bucks an ounce.');
define('DRUGS_HAVE_TALK', 'Have a little talk');
define('DRUGS_GIVE_DYNAMITE', 'Give Dynamite');
define('DRUGS_GIVE_BOMB', 'Give the bomb');
define('DRUGS_START_ESCAPE', 'Start escape');
define('DRUGS_BUY_COCAINE', 'Buy Cocaine');
define('DRUGS_BUY_MARIJUANA', 'Buy Poppy Seeds');
define('DRUGS_SELL_WEED', 'Sell all Weed');
define('DRUGS_LEAVING', 'Ты are a bad man, I\'m leaving!');
define('DRUGS_BUY_ITEM', 'Buy Item');

//changedesc
define('CHANGEDESC_CHANGED_GANG_MSG', 'Ты have changed the gang message.');
define('CHANGEDESC_CHANGED_PVT_GANG_MSG', 'Ты have changed the private gang message.');
define('CHANGEDESC_CHANGE_GANG_MSG', 'Change Gang Message');
define('CHANGEDESC_CAN_USE_BBCODE', 'Ты можешь использовать %s');
define('CHANGEDESC_CHANGE_PVT_GANG_MSG', 'Change Private Gang Message');

//changegangname
define('CGN_NOT_ENOUGH_MONEY',
    'Ты don\'t have enough деньги to change the Gang Имя and Tag. Ты need at least $100,000');
define('CGN_NAME_LEAST_CHARS', 'Тыr Gang\'s name must be at least 3 characters long.');
define('CGN_NAME_MAX_CHARS', 'Тыr Gang\'s name is limited to 20 characters');
define('CGN_TAG_LEAST_CHARS', 'Тыr Gang\'s tag must be at least 1 characters long.');
define('CGN_TAG_MAX_CHARS', 'Тыr Gang\'s tag is limited to 3 characters.');
define('CGN_NAME_EXIST', 'The Gang name you chose is already taken.');
define('CGN_TAG_EXIST', 'The tag you chose is already taken.');
define('CGN_WITHDRAW_MONEY', '<span style="color:red">Withdraw</span><b>$%s</b> to change Gang Имя');
define('CGN_NAME_CHANGED', 'Ты have successfully changed the Имя and Tag of your gang !');
define('CGN_CHANGE_NAME', 'Change Имя and Tag');
define('CGN_MSG_1', 'Ты можешь change your Gang Имя and Tag.');
define('CGN_MSG_2',
    'Changing the Имя and Tag will cost $250,000. It will be taken из your gang vault, so make sure you have enough деньги first.');
define('CGN_GANG_NAME', 'Gang Имя');
define('CGN_GANG_TAG', 'Gang Tag');

//changeleader
define('CGL_ALREADY_LEADER', 'Ты are already the gang leader !');
define('CGL_DECLARED_LEADER', '%s was declared new leader of the %s gang.');
define('CGL_CHANGE_LEADER', 'Change Leader');
define('CGL_NO_OTHER_MEMBER', 'There are no other members in your gang.');
define('CGL_MEMBER', 'Member');
define('CGL_PROMOTE', 'Promote to Leader');

//citizens
define('CITIZENS_TOTAL_USERS', 'Total Users');
define('CITIZENS_NO_SUCH_PAGE', 'No such page');

//city
define('CITY_BEST_INMATE', 'Best Inmates In %s');
define('CITY_NUM_INMATE', '%s Inmates on %s');
define('CITY_PLACES', 'Places To Go');
define('CITY_SHOPS', 'Shops');
define('CITY_HEADQUARTERS', 'Headquarters');
define('CITY_CELL_315', 'Cell 315');
define('CITY_GAMES_ROOM', 'Games Room');
define('CITY_YOUR_CELL', 'Тыr Cell');
define('CITY_REHABILITATION', 'Rehabilitation');
define('CITY_BUSINESS_WING', 'Business Wing');
define('CITY_GROUPS', 'Groups');
define('CITY_INFORMATION', 'Information');
define('CITY_COMMUNITY', 'Community');

//leftlinks
define('LINK_CELL', 'Cell');
define('LINK_CITYNAME', '<!_-cityname-_!>');
define('LINK_INVENTORY', 'Inventory');
define('LINK_BANK', 'Bank');
define('LINK_YOUR_GANG', 'Тыr Gang');
define('LINK_GYM', 'Gym');
define('LINK_CRIMES', 'Crimes');
define('LINK_SEARCH', 'Search');
define('LINK_HOSPITAL_HOSPITAL', 'Hospital <!_-hospital-_!>');
define('LINK_SHOWERS_JAIL', 'Showers <!_-jail-_!>');
define('LINK_MAIL', '<!_-mail-_!>');
define('LINK_EVENTS', '<!_-events-_!>');
define('LINK_SPY_LOGS', 'Spy Logs');
define('LINK_CHAT', 'Чат');
define('LINK_FORUMS', 'Forums');
define('LINK_SHOUT_BOX', 'Shout Box');
define('LINK_RP_STORE', 'RP Store');
define('LINK_REFERRALS', 'Referrals');
define('LINK_PRIZES', 'Prizes');
define('LINK_SUGGESTIONS', 'Suggestions');
define('LINK_NEW_INMATES_TUTORIAL', 'Новый Inmates Tutorial');
define('LINK_RULES', 'Rules');
define('LINK_STAFF_TOP_INMATES', 'Staff/Top Inmates');
define('LINK_FAQ', 'FAQ');
define('LINK_ARMOURS_SALES', 'Armours Sales');
define('LINK_WEAPON_SALES', 'Weapon Sales');
define('LINK_POINT_SHOP', 'Point Shop');
define('LINK_PHARMACY', 'Pharmacy');
define('LINK_REAL_ESTATE_AGENCY', 'Real Estate Agency');
define('LINK_HALL_OF_FAME', 'Hall Of Fame');
define('LINK_WORLD_STATS', 'World Stats');
define('LINK_PRISON_WARDENS', 'Заключенный Wardens');
define('LINK_PRISONERS_LIST', 'Заключенныйers List');
define('LINK_PRISONERS_ONLINE', 'Заключенныйers Online');
define('LINK_POLICE_BUS', 'Police Bus');
define('LINK_ITEM_GUIDE', 'Item Guide');
define('LINK_LOTTERY', 'Lottery');
define('LINK_POINTS_LOTTERY', 'Points Lottery');
define('LINK_SLOT_MACHINE', 'Slot Machine');
define('LINK_50_50_MONEY_GAME', '50/50 Money Game');
define('LINK_50_50_POINTS_GAME', '50/50 Points Game');
define('LINK_SIMPLE_DICE', 'Simple Dice');
define('LINK_BLACKJACK', 'Blackjack');
define('LINK_POOL_TABLE', 'Pool Table');
define('LINK_TABLE_TENNIS', 'Table Tennis');
define('LINK_HOCKEY_GAME', 'Hockey Game');
define('LINK_FREE_GAMES', 'Free-Games');
define('LINK_MOVE_CELL', 'Move Cell');
define('LINK_LAND_MANAGEMENT', 'Land Management');
define('LINK_ANNOUNCEMENTS', 'Announcements');
define('LINK_SHADY_LOOKING_STRANGER', 'Shady-Looking Stranger');
define('LINK_SEARCH_THE_PRISON_YARD', 'Search the Заключенный Yard');
define('LINK_JOB_CENTER', 'Job Center');
define('LINK_GANG_LIST', 'Gang List');
define('LINK_GANG_WARS', 'Gang Wars');
define('LINK_WAR_OF_FAME', 'War Of Fame');
define('LINK_GANG_WARS_RULES', 'Gang Wars Rules');
define('LINK_SUPPORT', '<!_-support-_!>');
define('LINK_RUSSIAN_ROULETTE', 'Knife Game');
define('LINK_YOUR_ENEMIES', 'Тыr enemies');
define('LINK_YOUR_FRIENDS', 'Тыr friends');
define('LINK_MARKET', 'Market');
define('LINK_STOCK_MARKET', 'Stock Market');
define('LINK_STOCK_MARKET_LOGS', 'Stock Market Logs');
define('LINK_HITLIST', 'Hitlist');
define('LINK_ATTACK_LOGS', 'Атака Logs');
define('LINK_SKILLS', 'Skills');
define('LINK_POKER', 'Poker');
define('LINK_MUG_LOGS', 'Mug Logs');
define('LINK_TRANSACTION_LOGS', 'Transaction Logs');
define('LINK_5050_LOGS', '50/50 Stat page');
define('LINK_POST_ADS', 'Заключенный Ads');
define('LINK_MAILBOX_TEXT', 'Mailbox');
define('LINK_EVENTS_TEXT', 'Events');
define('LINK_SUPPORT_CENTER_TEXT', 'Support center');
define('LINK_MONITOR_INMATES', 'Monitor Inmates');
define('LINK_FAN_SITE', 'Fan Site');
define('LINK_FORUM', 'Forum');

//admin links
define('ALINK_DOTPROJECT', 'Dotproject');
define('ALINK_MARQUEE_MAINTENANCE', 'Marquee/Maintenance');
define('ALINK_LOGS_CHECK', 'Logs Check');
define('ALINK_PLAYER_ITEMS', 'Player Items');
define('ALINK_RM_OPTIONS', 'RM Options');
define('ALINK_PLAYER_OPTIONS', 'Player Options');
define('ALINK_SEND_ANNOUNCEMENT', 'Отправить Announcement');
define('ALINK_MANAGE_REFERRALS', 'Manage Referrals');
define('ALINK_CHECK_BANS', 'Check Bans');
define('ALINK_CODER_LINKS', 'Coder Links');
define('ALINK_ACTION_TRACKING', 'Action Tracking');
define('ALINK_PLAYERS_SAME_IP_GANG', 'Players on same IP & Gang');
define('ALINK_BOTCHECK_USER_TRIES', 'Botcheck User tries');
define('ALINK_REPORTED_ADS', 'Reported Ads');
define('ALINK_FORUM', 'Manage Forum');

//modirator
define('MLINK_MANAGE_REFERRALS', 'Manage Referrals');
define('MLINK_CHECK_MULTIS', 'Check on Multi\'s');
define('MLINK_LOGS_CHECK', 'Logs Check');
define('MLINK_FREEZING', 'Freezing');
define('MLINK_BANS', 'Bans');
define('MLINK_MODERATING_RULES', 'Moderating rules');
define('MLINK_FROZEN_PLAYERS', 'Frozen Players');

//conf
define('CONF_ERROR', 'Confirmation error.');
define('CONF_ERROR_USER', 'Confirmation error for requested использоватьr.');
define('CONF_VALIDATED', 'Тыr account has been validated successfully! Redirecting to login page in 3 seconds.');

//cpassword
define('CPWD_WRONG_OLD_PWD', 'Ты entered the wrong old password.');
define('CPWD_WRONG_PWD_LEN', 'The password you chose has %d characters. Ты need to have between 4 and 20 characters.');
define('CPWD_WRONG_PWD', 'Тыr passwords don\'t match. Please try again.');
define('CPWD_CHANGE_PWD', 'Change Password');
define('CPWD_OLD_PWD', 'Old Password');
define('CPWD_NEW_PWD', 'Новый Password');
define('CPWD_CONFIRM_PWD', 'Confirm Password');

//create gang
define('CRTGANG_ALREADY_BELONG',
    'Ты already belong to a gang ! Ты need to quit your current gang before creating another one.');
define('CRTGANG_NOT_ENOUGH_MONEY', 'Ты don\'t have enough деньги to start a gang. Ты need at least $2,500,000');
define('CRTGANG_GANG_CREATED', 'Ты have successfully created a gang!');
define('CRTGANG_CREATE_GANG', 'Create Gang');
define('CRTGANG_MSG_1', 'Well, it looks like you haven\'t join or created a gang yet.');
define('CRTGANG_MSG_2',
    'Creating a gang costs $2,500,000. If you don\'t have enough, or would like to join someone else gang, check out the <a href="gang_list.php">Gang List</a> for other gangs to join.');

//crimes
define('CRIME_IN_SHOWER', 'Ты можешьnot do crimes if you are in showers.');
define('CRIME_IN_HOSPITAL', 'Ты можешьnot do crimes if you are in the hospital.');
define('CRIME_NOT_ENOUGH_POINTS', 'Ты do not have enough points.');
define('CRIME_REFILLED_NERVE', 'Ты spent 10 points and refilled your nerve.');
define('CRIME_NOT_ENOUGH_NERVE', 'Ты do not have enough nerve for that crime.');
define('CRIME_NOT_SUCCESS_MESSAGE',
    'We currently do not have a success message for this crime :( Ты можешь help  us by submitting your idea for a message in the crime section of the forums!');
define('CRIME_NOT_CAUGHT_MESSAGE',
    'We currently do not have a "Ты got caught" message for this crime :( Ты можешь help  us by submitting your idea for a message in the crime section of the forums!');
define('CRIME_NOT_FAILURE_MESSAGE',
    'We currently do not have a failure message for this crime :( Ты можешь help  us by submitting your idea for a message in the crime section of the forums!');
define('CRIME_CAUGHT', 'Ты were caught.');
define('CRIME_CAUGHT_SHOWER', 'Ты were hauled off to shower for 10 minutes.');
define('CRIME_SUCCESS', 'Success! Ты receive %s exp and $%s.');
define('CRIME_FAILED', 'Ты failed.');
define('CRIME_HEAD', 'Crimes');
define('CRIME_NAME', 'Имя');
define('CRIME_REQUIRED_NERVE', 'Required Nerve');
define('CRIME_REFILL_NERVE', 'Refill nerve');
define('CRIME_CAREFUL', 'Be careful: Тыr nerve will be refilled without confirmation.');
define('CRIME_MSG_1', 'PS: With higher speed you will be able to do higher crimes.');
define('CRIME_MSG_2',
    'example: with less than 1000 speed, you probably will only be able to succeed in nerve 1 and sometimes nerve 2 crimes');

define('CRIMES_PICKPOCKET_KID_NAME', 'Pickpocket Kid');
define('CRIME_PICKPOCKET_KID_STEXT',
    'When walking around the prison, you find to your delight, a kid. He couldn\'t be more than 14, then you remembered, it was bring your kid to prison day. Ты almost laugh. This was too easy. Ты walk silently pass the kid, and delve into his deep pockets.');
define('CRIME_PICKPOCKET_KID_FTEXT',
    'When walking around the prison, you find to your delight, a kid. He couldn\'t be more than 14, then you remembered, it was bring your kid to prison day. Ты almost laugh. This was too easy. Ты walk silently by, and notice just in time that "The Slasher" is beside the kid. Ты quickly divert yourself back into the prison.');
define('CRIME_PICKPOCKET_KID_CTEXT',
    'When walking about the prison, you find to your delight, a kid. He couldn\'t be more than 14, then you remembered, it was bring your kid to prison day. Ты almost laugh. This was too easy. Ты walk on past, and delve into the pocket of the kid. Ты feel a sharp pain in your back and you fall to the ground. Ты wake up, covered in blood. you go to the showers to clean it off.');
define('CRIMES_SHOPLIFT_SMALL_CELL_NAME', 'Shoplift Small Cell');
define('CRIME_SHOPLIFT_SMALL_CELL_STEXT',
    'Ты are walking through the corridors down to your friends cell, when you see a small cell, just a bog standard cell. Ты go and take a peak at all the known "hiding spots" of the room, Score! Ты found some cash.');
define('CRIME_SHOPLIFT_SMALL_CELL_FTEXT',
    'Ты are walking through the corridors down to your friends cell, when you see a small cell, just a bog standard cell. Ты go and take a peak at all the known "hiding spots" of the room. Ты here footsteps coming down the corridor. Ты rush out the room, and back to your cell. Fortunately, nobody saw you.');
define('CRIME_SHOPLIFT_SMALL_CELL_CTEXT',
    'Ты are walking through the corridors down to your friends cell, when you see a small cell, just a bog standard cell. Ты go and take a peak at all the known "hiding spots" of the room. Ты hear footsteps, but your sure there just going to pass on by. The footsteps stop by the cell. The Guard sees you. "Well laddy, shoplifting eh? Well get ready for a beating." Ты wake up in the showers, covered in blood.');
define('CRIMES_SHOPLIFT_NORMAL_CELL_NAME', 'Shoplift Normal Cell');
define('CRIME_SHOPLIFT_NORMAL_CELL_STEXT',
    'Ты are visiting a friend in another block. While walking the corridors you find a open cell, and upon a further check, there\'s nobody in it either. Ты find yourself smiling. Ты run into the cell, and rummage through the belongings, trying to find something worthwhile. Success! Ты find a wad of cash.');
define('CRIME_SHOPLIFT_NORMAL_CELL_FTEXT',
    'Ты are visiting a friend in another block. While walking the corridors you find a open cell, and upon a further check, there\'s nobody in it either. Ты find yourself smiling. Ты run into the cell, and rummage through the belongings, trying to find something worthwhile. you here footsteps. Ты leg it out of the cell, and into your friends.');
define('CRIME_SHOPLIFT_NORMAL_CELL_CTEXT',
    'Ты are visiting a friend in another block. While walking the corridors you find a open cell, and upon a further check, there\'s nobody in it either. Ты find yourself smiling. Ты run into the cell, and rummage through the belongings, trying to find something worthwhile. Ты don\'t hear the cell mate returning. Ты wake up in the showers, covered in blood and bruises.');
define('CRIMES_SHOPLIFT_THE_PIMP_CELL_NAME', 'Shoplift the Pimp Cell');
define('CRIME_SHOPLIFT_THE_PIMP_CELL_STEXT',
    'Ты are visiting a corrupt guard, who was taking a shift in A block. Ты find him walking along the corridor, and you ask him what he\'s doing. "Going to teach one of these pimps a thing or two, you down?" Ты say you are. He nods his head in acknowledgment. He goes into the cell with his baton in hand. Ты creep behind him silently, lurking in the shadows. He hits the Pimp hard, in the head, and in the chest. Ты hurriedly search for contraband and cash.');
define('CRIME_SHOPLIFT_THE_PIMP_CELL_FTEXT',
    'Ты are visiting a corrupt guard, who was taking a shift in A block. Ты find him walking along the corridor, and you ask him what he\'s doing. "Going to teach one of these pimps a thing or two, you down?" Ты say you are. He nods his head in acknowledgment. He goes into the cell with his baton in hand. Ты chicken out, and run away. What a wuss.');
define('CRIME_SHOPLIFT_THE_PIMP_CELL_CTEXT',
    'Ты are visiting a corrupt guard, who was taking a shift in A block. Ты find him walking along the corridor, and you ask him what he\'s doing. "Going to teach one of these pimps a thing or two, you down?" Ты say you are. He nods his head in acknowledgment. He goes into the cell with his baton in hand. Ты silently creep into the cell. you then find something big and black hit your head, hard. It was the guard! he double crossed you! Ты find yourself in the showers, dazed and confиспользоватьd.');
define('CRIMES_LOTTERY_SCAM_NAME', 'Lottery Scam');
define('CRIME_LOTTERY_SCAM_STEXT',
    'Ты get a call из a good friend of yours in the Lottery business, he says that he has some numbers that were sure to win. So you think that, why not give it a go. Ты get a ticket with the "sure to win" numbers. you get some cash.');
define('CRIME_LOTTERY_SCAM_FTEXT',
    'Ты get a call из a good friend of yours in the Lottery business, he says that he has some numbers that were sure to win. So you think that, why not give it a go. Ты get a ticket with the sure to win numbers. Ты didn\'t even get one number on the lottery.');
define('CRIME_LOTTERY_SCAM_CTEXT',
    'Ты get a call из a good friend of yours who is in the Lottery business, he says that he has some numbers that were sure to win. So you think what the hell. Ты put the numbers on, and find out that the warden has confiscated your ticket. Ты get shoved in to the showers.');
define('CRIMES_ROB_A_GUN_NAME', 'Rob A Gun');
define('CRIME_ROB_A_GUN_STEXT',
    'Ты are walking to your friends cell and find that the Armory has been left open. Ты walk creepily past the Armory and see that it is deserted. Ты walk into the cramped room. Ты see lots of guns and explosives, but the type of gun your most interested in is a 9mm. Ты search for one, and find it in a steel drawer. Ты take it, and 3 boxes of ammo, you conceal them underneath your shirt for safe keeping.');
define('CRIME_ROB_A_GUN_FTEXT',
    'Ты are walking to your friends cell and find that the Armory has been left open. Ты walk creepily past the Armory and see that it is deserted. Ты walk into the cramped room. Ты see lots of guns and explosives, but then you hear loud clinks of shoes on metal. Guards! you run hard out of the armory, hoping that no one saw your face.');
define('CRIME_ROB_A_GUN_CTEXT',
    'Ты are walking to your friends cell and find that the Armory has been left open. Ты walk creepily past the Armory and see that it is deserted. Ты walk into the cramped room. Ты see lots of guns and explosives. Тыr so astounded by the amount of guns and explosives that you fail to see that 2 guards are at the door, smiling there heads off as one of them smacks you hard over the head, you black out, and find yourself in the showers, with 2 cracked ribs and several bruises and cuts.');
define('CRIMES_SCAM_OTHERS_INMATES_NAME', 'Scam others Inmates');
define('CRIME_SCAM_OTHERS_INMATES_STEXT',
    'Ты are just digging in to your complimentary slop at the dinner hall when you hear a bigger inmate saying he would kill for a Macdonald\'s. Ты go sit next to him, he looks at you, wide eyed and all that. He then asks you "what the hell are you doing?" Ты reply coldly that, if he wanted some Macdonald\'s he would cut it out. He does, and he says that he would kill for a bigmac, so you say that\'s going to take 100 bucks. He almost dies at the price, but he says ok, and next week, he gets a bigmac in his cell.');
define('CRIME_SCAM_OTHERS_INMATES_FTEXT',
    'Ты are just digging in to your complimentary slop at the dinner hall when you hear a bigger inmate saying he would kill for a Macdonald\'s. Ты go sit next to him, he looks at you, wide eyed and all that. He then asks you "what the hell are you doing?" Ты reply coldly that, if he wanted some Macdonald\'s he would cut it out. He does, and he says that he would kill for a bigmac, so you say that\'s going to take 100 bucks. He says he doesn\'t got that much, so you just walk away.');
define('CRIME_SCAM_OTHERS_INMATES_CTEXT',
    'Ты are just digging in to your complimentary slop at the dinner hall when you hear a bigger inmate saying he would kill for a Macdonald\'s. Ты go sit next to him, he looks at you, wide eyed and all that. He then asks you "what the hell are you doing?" Ты reply coldly that, if he wanted some Macdonald\'s he would cut it out. He does, and he says that he would kill for a bigmac, so you say that\'s going to take 100 bucks. So just as you get the bigmac из your guard friend, he beats you over the head for having contraband. It would have been so embarrassing if you hadn\'t got the bigmac all over your front.');
define('CRIMES_DRUG_DEAL_NAME', 'Drug Deal');
define('CRIME_DRUG_DEAL_STEXT',
    'Ты are in your cell and you hear footsteps approaching your cell. you freeze, is it a guard? A few more seconds pass and you notice its only one of your hooked customers. "Regular?" He nods his head vigorously. "Cash up-front or nothing" you reply, he then proceeds to hand you a wad of notes, and in turn, you hand him a little baggie of cocaine. "Have fun now, and don\'t kill yourself" you mumble.');
define('CRIME_DRUG_DEAL_FTEXT',
    'Ты are in your cell and you hear footsteps approaching your cell. you freeze, is it a guard? A few more seconds pass and you notice its only one of your hooked customers. "Regular?". He nods his head vigorously. "Cash up-front or nothing" you reply. He shakes his head. "No cash, no service, homeboy". He walks away, mumbling to himself.');
define('CRIME_DRUG_DEAL_CTEXT',
    'Ты are in your cell and you hear footsteps approaching your cell. you freeze, is it a guard? A few more seconds pass and you notice its only one of your hooked customers. "Regular?. He nods his head vigorously. "Cash up-front or nothing" you reply. Then you notice the wire around the collar of his shirt, and the beads of sweat dripping down his face. Ты hand him a можешьdy bar and say "There you go.". He walks out dumbfounded. Then a guard walks into your cell and mentions that a little birdie told him that you did some contraband selling. Ты wake up in the Showers, covered in blood and bruises. "At least they didn\'t get to steal my stash" you think to yourself.');
define('CRIMES_PICKPOCKET_A_GUARD_NAME', 'Pickpocket a Guard');
define('CRIME_PICKPOCKET_A_GUARD_STEXT',
    'Ты walk past a cell on your way back to your own pitiful accommodation. Ты see the guard has been having an argument with the inmate. you see an opportunity. you see his fat wallet, jutting out of his back pocket. Ты silently creep past the guard, relieving him of his wallet as you do so.');
define('CRIME_PICKPOCKET_A_GUARD_FTEXT',
    'Ты walk past a cell on your way back to your own pitiful accommodation. Ты see the guard has been having an argument with the inmate. you see an opportunity. you see his fat wallet, jutting out of his back pocket. Ты silently creep past the guard and think better of it becaиспользовать that baton looks like its seen a couple of smashed in-heads.');
define('CRIME_PICKPOCKET_A_GUARD_CTEXT',
    'Ты walk past a cell on your way back to your own pitiful accommodation. Ты see the guard has been having an argument with the inmate. you see an opportunity. you see his fat wallet, jutting out of his back pocket. Ты silently creep past the guard, almost reliving the guard of his wallet but then, as your hands grasp the wallet, the wallet clinks to the ground. Ты freeze. The guard spins round, violently smashing you on the head with his baton.');
define('CRIMES_PICKPOCKET_THE_DIRECTOR_NAME', 'Pickpocket the Director');
define('CRIME_PICKPOCKET_THE_DIRECTOR_STEXT',
    'Ты are sitting in the front office contemplating what the Director wants with you. Then you see him walking towards you, and he passes you and continues to talk to his secretary. "too perfect you whisper". Ты lightly take his wallet.');
define('CRIME_PICKPOCKET_THE_DIRECTOR_FTEXT',
    'Ты are sitting in the front office contemplating what the Director wants with you. Then you see him walking towards you, and then he passes you and continues to talk to his secretary. you reach out, and think better of it, becaиспользовать if you are caught, you would get beaten senseless.');
define('CRIME_PICKPOCKET_THE_DIRECTOR_CTEXT',
    'Ты are sitting in the front office contemplating what the Director wants with you. Then you see him walking towards you, and he passes you and continues to talk to his secretary. "Too perfect you whisper". Ты lightly tap his back pocket and find to your horror, your hand was wrapped with a massive hand. "What do we have here then?" the Director growled.');
define('CRIMES_GAMBLING_NAME', 'Gambling');
define('CRIME_GAMBLING_STEXT',
    'Тыr out in the Yard you decide to make a little cash. Ты walk towards the gambling addicts and pull out your Dice. Ты call out $200 a roll. They pull out a wad of cash and set it on the ground. Ты roll and win. No one notices your trick dice and you walk away with the деньги.');
define('CRIME_GAMBLING_FTEXT',
    'Тыr out in the Yard you decide to make a little cash. Ты walk towards the gambling addicts and pull out your Dice. The guards notice a rather large crowd starting to gather around. Ты notice out of the corner of your eye the guards heading your way. Ты put the dice in your shoes and walk away.');
define('CRIME_GAMBLING_CTEXT',
    'Тыr out in the Yard you decide to make a little cash. Ты walk towards the gambling addicts and pull out your Dice. Ты call out $200 a roll. They pull out a wad of cash and set it on the ground. Ты roll and win. One of the losers hit you on the head with something hard and heavy and you wake up bloody in the showers.');
define('CRIMES_STEALING_DIRECTOR_CIGARETTES_NAME', 'Stealing Director Cigarettes');
define('CRIME_STEALING_DIRECTOR_CIGARETTES_STEXT',
    'Тыr walking back to your cell when you walk by the director\'s office and notice boxes of cigarette cartons. Ты look around and do not see the director and realize that you можешь make a hefty profit selling cigarettes. Ты quickly walk in the Directors office and grab 3 carton of cigarettes and walk quietly to your cell where you stash the cartons.');
define('CRIME_STEALING_DIRECTOR_CIGARETTES_FTEXT',
    'Тыr walking back to your cell when you walk by the director\'s office and notice boxes of cigarette cartons. Ты look around and do not see the director and realize that you можешь make a hefty profit selling cigarettes. Ты then suddenly see a guard out of the corner of your eye and decide not to do anything and walk away.');
define('CRIME_STEALING_DIRECTOR_CIGARETTES_CTEXT',
    'Тыr walking back to your cell when you walk by the director\'s office and notice boxes of cigarette cartons. Ты look around and do not see the director and realize that you можешь make a hefty profit selling cigarettes. Ты quickly walk in the Directors office and grab 3 carton of cigarettes. As you are about to leave the director comes into the office and finds you with the cartons of cigarette\'s and throws you in the shower.');
define('CRIMES_STEAL_YOUR_CELLIES_PILLOW_NAME', 'Steal your Cellies Pillow ');
define('CRIME_STEAL_YOUR_CELLIES_PILLOW_STEXT',
    'Ты можешь\'t sleep. Ты slowly slide down off your bunk and grab at your cellmates throat and mouth. Ты tell him to stay quiet and that your taking his пилюляow. He nods quickly, out of fear, and you yank the пилюляow out из under his head.');
define('CRIME_STEAL_YOUR_CELLIES_PILLOW_FTEXT',
    'Ты можешь\'t sleep. Тыr cellmate has an extra пилюляow. Ты decide to take one of your cellmates пилюляows as he is sleeping. Ты try and sneak off your bunk, your feet hit the floor and wake up your cellie. Ты tell you have to pee. Ты failed.');
define('CRIME_STEAL_YOUR_CELLIES_PILLOW_CTEXT',
    'Ты можешь\'t sleep. Ты want a пилюляow but you are unsure about your new cellmate. Ты decide to give it a shot. Ты grab at the пилюляow as your cellmate sleeps, he wakes up and holds you down as he calls for the guards. Ты wake up in the showers with a пилюляowcase over your head, thinking "very funny".');
define('CRIMES_STEAL_YOUR_CELLIES_SHOES_NAME', 'Steal your Cellies Shoes ');
define('CRIME_STEAL_YOUR_CELLIES_SHOES_STEXT',
    'Тыr cellmate walks back into your cell after buying new shoes off another inmate. Ты have had the same shoes for seven years. Тыr cellie has only been incarcerated two years. Ты grab him by the head and put his head into the wall, you tell him your taking his shoes, he doesn\'t deserve them. ');
define('CRIME_STEAL_YOUR_CELLIES_SHOES_FTEXT',
    'Тыr cellmate walks back into your cell after buying new shoes off another inmate. Тыr old shoes are worn out. Тыr taking your cellies shoes you tell yourself. Ты strike at your cellie with such force you missed and slammed into the wall behind him. Ты sit on your bunk as your cellie laughs at you.');
define('CRIME_STEAL_YOUR_CELLIES_SHOES_CTEXT',
    'Тыr cellmate walks back into your cell after buying new shoes off another inmate. Ты grab your cellie by the wrist and tell him your taking his shoes. He agrees and doesn\'t want any trouble. A guard passes and sees you, you wake up in the showers bloody and confиспользоватьd.');
define('CRIMES_SMUGGLE_CANDY_NAME', 'Smuggle Candy ');
define('CRIME_SMUGGLE_CANDY_STEXT',
    'Ты need some cash but don\'t really have many friends on the outside, so you ask your homie to bring some можешьdy when she visits, She Arrives and slides a bag of можешьdy across the table. Ты take it and slide it into your jumpsuit. ');
define('CRIME_SMUGGLE_CANDY_FTEXT',
    'Ты ask your homie to bring some some "можешьdy" to you in prison. Тыr homie doesn\'t even get passed the security check point at the front gate. Тыr homie thought you meant coke when you said можешьdy.');
define('CRIME_SMUGGLE_CANDY_CTEXT',
    'Тыr homie visited this morning and gave you some можешьdy. A guard sees you eating можешьdy in your cell. Ты wake up in the showers in the corner with можешьdy in your mouth. ');
define('CRIMES_SMUGGLE_SMOKES_NAME', 'Smuggle Smokes ');
define('CRIME_SMUGGLE_SMOKES_STEXT',
    'Ты know you можешь get a hell of a lot of деньги if you sell some smokes. But how можешь you get some ? Ты get desperate and call your homie. Тыr homie visits two days later with four packs of smokes. He puts them on the floor and kicks them to you. Success!');
define('CRIME_SMUGGLE_SMOKES_FTEXT',
    'Ты know you можешь get a hell of a lot of деньги if you sell some smokes. Ты get desperate and call your homie. Тыr homie shows up that day with two packs of smokes, he tells you it going to cost you $50. Ты tell him your not paying that much and he leaves. ');
define('CRIME_SMUGGLE_SMOKES_CTEXT',
    'Ты know you можешь get a hell of a lot of деньги if you sell some smokes. Ты get desperate and call your homie. Ты hear a voice on the phone telling you your phone conversation has been monitored and recorded. Ты close your eyes and prepare to be thrown into the showers.');
define('CRIMES_SMUGGLE_DRUGS_NAME', 'Smuggle Drugs ');
define('CRIME_SMUGGLE_DRUGS_STEXT',
    'Ты know the cost of drugs has gone up lately. So you decide your going to start selling to make some extra cash. Ты call your homie and he is glad to help. Baggies of drugs and just as the guard turn his head SCORE, you know you will get a pretty penny for this stuff');
define('CRIME_SMUGGLE_DRUGS_FTEXT',
    'Ты know the cost of drugs has gone up lately. So you decide your going to start selling to make some extra cash. Ты call your homie but a police officer answers his phone, he just got busted for possession and trafficking. Тыr not getting any drugs any time soon.');
define('CRIME_SMUGGLE_DRUGS_CTEXT',
    'Ты know the cost of drugs has gone up lately. So you decide your going to start selling to make some extra cash. Ты call your homie and he shows up with the goods. Ты enter the room & you see guards approaching him. He is been recognised из other contraband charges at the prison. How could you be so stupid to использовать the same guy, you find yourself in the showers with a headache.');
define('CRIMES_MAKE_A_SHANK_NAME', 'Make a Shank ');
define('CRIME_MAKE_A_SHANK_STEXT',
    'Ты don\'t feel safe anymore and you decide its time to protect yourself with a weapon. Ты take your toothbrush and quietly sharpen it against the cold concrete wall. Success, now this a weapon.');
define('CRIME_MAKE_A_SHANK_FTEXT',
    'Ты don\'t feel safe anymore and you decide its time to protect yourself with a weapon. Ты begin to sharpen your toothbrush against the wall when a guards flashlight is shining in your face. The guard takes your almost formed shank and tells you he doesn\'t feel like doing paperwork so he is not busting you. But your punishment is you won\'t have a toothbrush for a while.');
define('CRIME_MAKE_A_SHANK_CTEXT',
    'Ты don\'t feel safe anymore and you decide its time to protect yourself with a weapon. Ты start to sharpen it against a wall and just then your cell door opens and 4 guards rush in. What timing for a contraband search...random, yea right somebody ratted you out, you wake up in the showers feeling like a failure');
define('CRIMES_STEAL_SOME_SMOKES_NAME', 'Steal Some Smokes ');
define('CRIME_STEAL_SOME_SMOKES_STEXT',
    'Тыr walking around the yard when you spot a pack of cigarettes next to another prisoner on a bench. Ты notice he is looking at everyone else in front of him, you sneak up look around and grab them, Success!');
define('CRIME_STEAL_SOME_SMOKES_FTEXT',
    'Тыr walking around the yard when you spot a pack of cigarettes next to another prisoner on a bench. Ты run up to him trying to surprise him, but you trip and smash your face against the bench');
define('CRIME_STEAL_SOME_SMOKES_CTEXT',
    'Тыr walking around the yard when you spot a pack of cigarettes next to another prisoner on a bench. Ты approach the prisoner but are spotted by the guards. Ты find yourself in the showers with cuts on your head and arms.');
define('CRIMES_MAKE_PRISON_ALCOHOL_NAME', 'Make Заключенный alcohol ');
define('CRIME_MAKE_PRISON_ALCOHOL_STEXT',
    'Ты know your можешь get great деньги из smokes, drugs and even можешьdy but nobody in the prison has alcohol. Ты ask around and find somebody who knows how to make it, you break into the prisons food room and get the supplies. Success! The alcohol is fermenting in your cell right now.');
define('CRIME_MAKE_PRISON_ALCOHOL_FTEXT',
    'Ты know your можешь get great деньги из smokes, drugs and even можешьdy but nobody in the prison has alcohol. Ты ask around but можешь\'t find anyone that has any idea how to make it.');
define('CRIME_MAKE_PRISON_ALCOHOL_CTEXT',
    'Ты know your можешь get great деньги из smokes, drugs and even можешьdy but nobody in the prison has alcohol. Ты try and break into the prisons food room by yourself and as the door opens an alarm sounds, you wake up in the showers with cold water hitting your face.');
define('CRIMES_THROW_URINE_AT_A_GUARD_NAME', 'Throw Urine at a Guard ');
define('CRIME_THROW_URINE_AT_A_GUARD_STEXT',
    'Ты hate that guard that always hassles you. Тыr going to show him...At night just after lights out, he walks by and your ready, you toss a urine and feces cocktail at his face and he falls to the ground not knowing what cell it came из.');
define('CRIME_THROW_URINE_AT_A_GUARD_FTEXT',
    'Ты hate that guard that always hassles you. Тыr going to show him...At night just after lights out, you hear footsteps it must be him, and raise your cocktail and see he is walking with another guard. Ты put your cocktail away and wait until the time is right.');
define('CRIME_THROW_URINE_AT_A_GUARD_CTEXT',
    'Ты hate that guard that always hassles you. Тыr going to show him...Ты hear him coming you get ready and toss it at him. Ты throw the mixture and it misses him, he quickly sprays you in the face with his mace and you wake up in the showers in a puddle of your own urine.');
define('CRIMES_ATTACK_A_TOP_INMATE_NAME', 'Атака a Top Inmate ');
define('CRIME_ATTACK_A_TOP_INMATE_STEXT',
    'Ты decide your going to prove yourself, you plan to attack a top inmate at the prison. Ты know you might need some help so your posse moves into position and attacks. Ты get away with some cash, he didn\'t go down easy.');
define('CRIME_ATTACK_A_TOP_INMATE_FTEXT',
    'Ты decide your going to prove yourself, you plan to attack a top inmate at the prison. Ты assemble a group to attack a top inmate. The inmate is waiting for you. He beats you and your entire posse, your lucky to escape with your life.');
define('CRIME_ATTACK_A_TOP_INMATE_CTEXT',
    'Ты decide your going to prove yourself, you plan to attack a top inmate at the prison. The guards somehow catch wind of your plan and throw you into the showers.');
define('CRIMES_BREAK_INTO_WARDENS_OFFICE_NAME', 'Break into the Wardens Office ');
define('CRIME_BREAK_INTO_THE_WARDENS_OFFICE_STEXT',
    'Ты catch wind that the warden leaves his door unlocked while he eats with some of the guards on break. Ты hatch a plan to get into his office, you grab a broom and pretend to be cleaning, you walk towards where you\'ve heard his office is. Ты find it "the director" you open the door. Success! Smokes, contraband and prison files, you hear footsteps and get out of there with what you можешь put in your shirt. ');
define('CRIME_BREAK_INTO_THE_WARDENS_OFFICE_FTEXT',
    'Ты catch wind that the warden leaves his door unlocked while he eats with some of the guards on break. Ты grab a broom and pretend to be cleaning, you walk towards where you\'ve heard his office is. Ты можешь\'t seem to find this damn office, a guard see you and asks what your doing in this area. Ты tell him your lost and he escorts you back to your cell');
define('CRIME_BREAK_INTO_THE_WARDENS_OFFICE_CTEXT',
    'Ты catch wind that the warden leaves his door unlocked while he eats with some of the guards on break. Ты wait until the shift change and walk towards the wardens office, you open the door and see the warden at his desk...Ты wake up in the showers bruised and beaten.');
define('CRIMES_ATTACK_THE_WARDEN_NAME', 'Атака The Warden ');
define('CRIME_ATTACK_THE_WARDEN_STEXT',
    'The warden has threatened you for the last time, you hear he has an interview with channel 4 news about the prison life. Ты plan to attack him just before they turn the cameras on. The time has come and you approach the warden из behind. He has no idea what hit him, he falls to the ground and reaches for his radio, you kick it away and kick him in the head before running to your cell. Ты got away before anyone saw you.');
define('CRIME_ATTACK_THE_WARDEN_FTEXT',
    'The warden has threatened you for the last time, you hear he has an interview with channel 4 news about the prison life. Тыr going to attack him before the cameras are on, before you get there. The warden calls for a lockdown for the safety of him and the Новыйs Crew. Better luck next time.');
define('CRIME_ATTACK_THE_WARDEN_CTEXT',
    'The Warden has threatened you for the last time, you hear he has an interview with channel 4 news about the prison life. Ты plan to attack him just before they turn the cammeras on. Ты approach the warden but see the camera is on already you are stunned. The warden turns around and with a swift hit из his baton you fall to the floor, you wake up in the showers missing teeth and bloody.');
define('CRIMES_PUT_A_HIT_ON_THE_WARDENS_SON_NAME', 'Put a hit on the Wardens Son ');
define('CRIME_PUT_A_HIT_ON_THE_WARDENS_SON_STEXT',
    'The warden seems to be after you for some reason, you decide to go after his family and take out his son. Ты call your homie and tell them what to do. Тыr homie is scared but agrees. The next week you notice the warden hasn\'t been around. Ты ask a passing guard and he tells you the warden had a family emergency. Ты можешьnot stop smiling. Тыr homies came through and were successfull. ');
define('CRIME_PUT_A_HIT_ON_THE_WARDENS_SON_FTEXT',
    'The warden seems to be after you for some reason, you decide to go after his family and take out his son. Ты ask your homie to take care of him, but he declines out of fear. Ты return to your cell angry.');
define('CRIME_PUT_A_HIT_ON_THE_WARDENS_SON_CTEXT',
    'The warden seems to be after you for some reason, you decide to go after his family and take out his son. Тыr homie declines and so you ask your cellie if he knows how you можешь get it done. That night your cellie isnt in the cell, and coder himself walks in. He tells you he knows everything. Ты wake up in the showers bleeding из just about every orifice. Ты можешьnot see, your eyes are swollen shut. And you are in horrible pain.');

//drugs description

define('DRUG_SPEED_MODIFIER', 'Speed Modifier');
define('DRUG_STRENGTH_MODIFIER', 'Strength Modifier');
define('DRUG_HP_GAIN', 'HP Gain');
define('DRUG_NO_GYM', 'Something to get your speed higher. But carefull you will not be in condition to train in gym.');
define('DRUG_NO_GYM_1',
    'Something to get your strength higher. But carefull you will not be in condition to train in gym.');
define('DRUG_NO_GYM_2',
    'Something to get your strength, defence and speed higher. But carefull you will not be in condition to train in gym.');

//dice
define('DICE_GAME', 'Dice Game');
define('DICE_GAME_ALL_PLAYED', 'Sorry you have already played 150 times today!');
define('DICE_GAME_WIN', 'Ты win 350 dollars!');
define('DICE_GAME_LOSE', 'Ты lose 50 dollars!');
define('DICE_NOT_ENOUGH_MONEY', 'Ты need at least $50 to play.');
define('DICE_ROLLS_LEFT', '%d rolls left to play');
define('DICE_MSG_1', 'Want some деньги? Guess the number that will be rolled on the dice....');
define('DICE_MSG_2', 'Win $350 if you guess right and lose $50 if you guess wrong!');
define('DICE_MSG_3', 'Click the side of the dice you would like to guess....');

//directory
define('DIR_MMORPG_RELATE_SITES', 'MMORPG and Other Game Related Websites');
define('DIR_MMORPG_SITES', 'MMORPG Sites');
define('DIR_WORLD_OF_WARCRAFT', 'World of Warcraft');
define('DIR_TEXT_1',
    'The largest online role playing game on the internet in terms of monthly suscribers and currently holds the Guiness World Record for the most popular MMORPG.');
define('DIR_OTHER_RELATED_SITES', 'Other Game Related Sites');
define('DIR_ARCADE_SITES', 'Arcade Sites');
define('DIR_FREEARCADE', 'FreeArcade');
define('DIR_TEXT_2',
    'One of the largest free online arcade games. Members have the ability to create their own small page which they можешь add the games they like and then использовать as a personal bookmark.');
define('DIR_GENERAL_GAMING_SITES', 'General Gaming Sites');
define('DIR_MONOPOLY_MAN', 'Monopoly-Man');
define('DIR_TEXT_3',
    'Monopoly boardgame information. Includes Monopoly game rules, the history of the game, and where to play online.');

//donatordone
define('DONATION_CANCELLED', 'Ты have можешьcelled your donation.');
define('DONATION_COMPLETED',
    'Thank you for your payment to www.prisonstruggle.com. Тыr transaction has been completed, and a receipt for your purchase has been emailed to you. Ты may log into your account at <a href="http://www.paypal.com">www.paypal.com</a> to view details of this transaction. Тыr donator pack should be credited, if not, contact an admin for assistance.');

//walliedonation
define('WALLIE_DONATION', 'Thank you for your payment to www.prisonstruggle.com.');
define('WALLIE_MAILSUBJECT', 'Got Cash из Wallie.');

define('PAYMENT_NEWNAME', 'Новый Имя for %d, its a %s');
define('PAYMENT_CREDITED', '%s account was credited with the pack  %s.');
define('PAYMENT_YOUCREDITED', 'Тыr %s pack valued at $ %s has been successfully credited to you.');
define('PAYMENT_USERBOUGHT', 'Ты bought the pack %s.');
define('PAYMENT_SOMEONECONTACT', 'Someone will contact you soon about your new использоватьrname. Thanks');
define('PAYMENT_RECEIVESTUFF', 'Ты will receive your stuff soon. Thanks');
define('PAYMENT_SPECIALDELIVERED', 'Special Delivered');

define('PBC_SEPARATOR', '@');
define('PAYPAL_SEPARATOR', '|');
define('RPSTORE_NORMAL_SUBSCRIPTION', 'Заключенный Struggle Normal RP Subscription');
define('RPSTORE_VIP_SUBSCRIPTION', 'Заключенный Struggle VIP RP Subscription');

//downtown
define('DOWNTOWN', 'Down Town');
define('DOWNTOWN_ALREADY_SEARCHED', 'Ты have already searched the prison yard as much as you можешь today.');
define('DOWNTOWN_LESS_2', 'Тыr lucky day. Ты found a Health Pill!');
define('DOWNTOWN_NOTIFY_LESS_2', 'Ты found a Health Pill!');
define('DOWNTOWN_LESS_10', 'Ты found 5 fertilizers !');
define('DOWNTOWN_LESS_20', 'Ты found 2 generic steroids!');
define('DOWNTOWN_LESS_25', 'Ты found a Towel!');
define('DOWNTOWN_LESS_30', 'Ты found a Pillow!');
define('DOWNTOWN_LESS_33', 'Ты found a Shank!');
define('DOWNTOWN_LESS_36', 'Ты found a Padded Shirt!');
define('DOWNTOWN_LESS_38', 'Ты found an old collectors bronze coin valued at $10,000!');
define('DOWNTOWN_LESS_40', 'Ты found a cheap silver ring which you traded in for 10 points');
define('DOWNTOWN_LESS_41',
    'As you were out in the Yard, a piece of paper was brought to your attention, it turned out to be the deeds to an acre of land in Panama!');
define('DOWNTOWN_LESS_42',
    'As you were out in the Yard, a piece of paper was brought to your attention, it turned out to be the deeds to an acre of land in Alcatraz!');
define('DOWNTOWN_LESS_43',
    'As you were out in the Yard, a piece of paper was brought to your attention, it turned out to be the deeds to an acre of land in Guantanamo Bay!');
define('DOWNTOWN_LESS_44_1', 'Ты found a Baton that some guard left behind!');
define('DOWNTOWN_LESS_44_2', 'OH MY! Ты found a Liquid Armour Vest!');
define('DOWNTOWN_LESS_44_5', 'GOOD! Ты found an old collectors silver coin valued at $30,000!');
define('DOWNTOWN_LESS_44_8', 'EXCELLENT! Ты found an old collectors gold coin valued at $50,000!');
define('DOWNTOWN_LESS_44_9', 'Тыr lucky day. Ты found an Awake Pill!');
define('DOWNTOWN_NOTIFY_LESS_44_2', 'Ты found a Liquid Armour Vest!');
define('DOWNTOWN_NOTIFY_LESS_44_5', 'Ты found an old collectors silver coin valued at $30,000!');
define('DOWNTOWN_NOTIFY_LESS_44_8', 'Ты found an old collectors gold coin valued at $50,000!');
define('DOWNTOWN_NOTIFY_LESS_44_9', 'Ты found an Awake Pill!');
define('DOWNTOWN_NOT_FIND', 'Ты didn\'t find anything.');
define('DOWNTOWN_FIND', 'Ты found $%s!');
define('DOWNTOWN_SEARCH', 'Тыr search yields some results !');
define('DOWNTOWN_HEAD', 'Search Заключенный Yard');
define('DOWNTOWN_YOU_FOUND_TOTAL', 'Ты found a total of $%s searching the prison yard !');
define('DOWNTOWN_NO_RESULTS', 'No results');

//dprpstore
define('DPRPSTORE_HEAD', 'RP Store by PHONE');
define('DPRPSTORE_MSG_1',
    'Only if you have no way of using paypal, использовать phone donations. Becaиспользовать we get a low percentage из phone donations.');
define('DPRPSTORE_MSG_2', 'For those days you will gain energy, nerve and HP twice as quick.');
define('DPRPSTORE_MSG_3', 'For those days you will gain 4% bank interest instead of 2%.');
define('DPRPSTORE_MSG_4', 'Different Color Имя and voting sites receive more points.');
define('DPRPSTORE_BUYING_FOR', 'Ты are buying for Ты');
define('DPRPSTORE_BUY_FOR', 'Buy for ID');
define('DPRPSTORE_CHANGE_ID', 'Change ID');
define('DPRPSTORE_BUYING_FOR_PLAYER', 'Buying for player');
define('DPRPSTORE_CHANGE_BACK_ID', 'Change Back to ID');
define('DPRPSTORE_ID_NOT_EXISTS', 'That id doesn\'t exists');
define('DPRPSTORE_PACKAGE', 'Package');
define('DPRPSTORE_RP_DAYS', 'RP days');
define('DPRPSTORE_MONEY', 'Money');
define('DPRPSTORE_POINTS', 'Points');
define('DPRPSTORE_ITEMS', 'Items');
define('DPRPSTORE_COST', 'Cost');
define('DPRPSTORE_PHONE_DONATE', 'Phone Donate');
define('DPRPSTORE_DAY_RP_PACK', 'Day RP Pack');
define('DPRPSTORE_AWAKE_PILLS', 'Awake Pills');
define('DPRPSTORE_AWAKE_PILL', 'Awake Pill');
define('DPRPSTORE_GUARD_PROTECTION', 'Guard Protection');
define('DPRPSTORE_9MM_PISTOL', '9mm Pistol');
define('DPRPSTORE_ACRES_PACK', '2 Acres Pack');
define('DPRPSTORE_ACRES_IN_PANAMA', '2 Acres of Land in Panama');
define('DPRPSTORE_SURVIVAL_PACK', 'Beginner\'s Survival Pack');
define('DPRPSTORE_QUES', 'Question');
define('DPRPSTORE_QUESTION', 'Why are Daopay prices higher than paypal?');
define('DPRPSTORE_RESP', 'Answer');
define('DPRPSTORE_RESPONSE_1',
    'Becaиспользовать major percentage goes to phone companies, and DAOPAY, our partner also takes some percentage!');
define('DPRPSTORE_RESPONSE_2',
    'So ЗаключенныйStruggle only receives the rest of donation! Due to that we had to raise prices.');
define('DPRPSTORE_PLEASE_READ', 'Please Read');
define('DPRPSTORE_NOTES', 'By donating to Заключенный Struggle you are agreeing to the following terms');
define('DPRPSTORE_NOTE_1', 'No refunds.');
define('DPRPSTORE_NOTE_2',
    'Just becaиспользовать you have bought a package из us, it doesn\'t mean you можешь go around breaking rules. So be warned, you можешь still get banned for breaking them.');
define('DPRPSTORE_NOTE_3', 'If you don\'t get your package, please contact one of the staff, preferably an admin.');
define('DPRPSTORE_NOTE_4', 'If you try refunding your деньги through paypal, we will ban your account.');

//drugs
define('DRUGS_UNDER_EFFECT', 'Ты already are under a drug effect !');
define('DRUGS_NOT_HAVE_COCAINE', 'Ты do not have any Cocaine.');
define('DRUGS_NOT_HAVE_STEROIDS', 'Ты do not have any Steroids.');
define('DRUGS_NOT_HAVE_NO_DOZE', 'Ты do not have any No-Doze.');
define('DRUGS_SNORTED_COCAINE', 'Ты snorted some Cocaine.');
define('DRUGS_POPPED_STEROIDS', 'Ты popped some Steroids.');
define('DRUGS_POPPED_NO_DOZE', 'Ты popped some No-Doze.');
define('DRUGS_NOTHING', 'Ты have nothing to do here.');

//effects
define('EFFECT', 'Effect');
define('EFFECT_NOT_SELECTED', 'No effect selected.');
define('EFFECT_COCAINE', '+30% to speed attribute');
define('EFFECT_GENERIC_STEROIDS', '+15% to strength attribute');
define('EFFECT_STEROID_COCKTAIL',
    'Something to get your strength, defence and speed higher. But carefull you will not be in condition to train in gym.');
define('EFFECT_STEROID_COCKTAIL_STRENGTH', '+ %s to strength attribute');
define('EFFECT_STEROID_COCKTAIL_DEFENSE', '+ %s to defense attribute');
define('EFFECT_STEROID_COCKTAIL_SPEED', '+ %s to speed attribute');

//error
define('ERROR', 'Error');
define('ERROR_MSG_1', 'We are sorry, but an error occured and was forwarded to our technical team.');
define('ERROR_MSG_2', 'We sincerely apologize for the inconvenience, and ask you to try again in a few minutes.');
define('ERROR_THANKS', 'Thanks');
define('ERROR_PRISON_STAFF', 'Заключенный Struggle Staff');

//events
define('EVENTS_NOT_DELETED', 'No Events were deleted for использоватьr %s.');
define('EVENTS_ALL_DELETED', 'All your events have been deleted.');
define('EVENTS_DELETED', 'Event deleted !');
define('EVENT_DELETED', 'Event deleted !');
define('EVENTS_SELECT_ONE_TO_DELETE', 'Please select at least one event to delete.');
define('EVENTS_LOG_HEAD', 'Event Log (every event with more than 3 days will be deleted)');
define('EVENTS_SHOW_FOR', 'Show Events for');
define('EVENTS_SHOW_OPTION_1', 'Last 24 hours');
define('EVENTS_SHOW_OPTION_2', 'Last 2 days');
define('EVENTS_SHOW_OPTION_3', 'Last 3 days');
define('EVENTS_SHOW_ALL', 'All');
define('EVENTS_ALL_TYPES', 'All Types');
define('EVENTS_DELETE_MY_ALL', 'Delete All My Events');
define('EVENTS_RECEIVED', 'Received');
define('EVENTS_EVENT', 'Event');
define('EVENTS_SURE_TO_DELETE_SELECTED', 'Are you sure that you want to delete the selected events ?');
define('EVENTS_CANT_DELETE', 'Event %s could not be deleted.');

//FAQ
define('FAQ', 'FAQ');
define('FAQ_HOW_I_PLAY', 'How do I play?');
define('FAQ_HOW_I_PLAY_ANS',
    'Start off by reading the game Tutorial, all the information you need to get started is in there. If you have any problems simply post your questions on the in-game forum board, shout box or chat, all можешь be found in the Menu sidebar.');
define('FAQ_HOW_I_JOIN_GANG', 'How do I join a gang?');
define('FAQ_HOW_I_JOIN_GANG_ANS_1',
    'Ты можешь join a gang in two ways. The first is to actually go to the gangs page where you will find a section just above the members in the gang that allows you to apply for that particular gang with a reason as to why you should be accepted.');
define('FAQ_HOW_I_JOIN_GANG_ANS_2',
    'An alternative to this is to actually receive gang invitations из gang leaders. These invitations if received pop up for you to accept or decline the offer.');
define('FAQ_HOW_I_INVITE', 'I have started my own gang, how do I send Invites?');
define('FAQ_HOW_I_INVITE_ANS',
    'In the ?Тыr Gang? link located in the left sidebar you are given a few options. Click on the ?Invite Player? link and input the использоватьrs screen name of who you would like to invite.');
define('FAQ_HOW_I_ATTACK', 'How do I attack other inmates?');
define('FAQ_HOW_I_ATTACK_ANS_1',
    'Ты можешь attack other prisoners by going onto the profile page of the использоватьr you?d like to attack and then by clicking the ?Атака? link located under the ?Actions? section of the page.');
define('FAQ_HOW_I_ATTACK_ANS_2',
    '<b>Remember:</b> Ты можешь only attack Inmates who are in the same Заключенный as you. To attack others who are not in your Заключенный you have to travel to the Заключенный they are in. ');
define('FAQ_CAN_I_ERASE_ACT', 'Can I erase this account and start over?');
define('FAQ_CAN_I_ERASE_ACT_ANS',
    'Once an account has been opened you можешьnot erase that account. If you create another account it will show as a multi-account which will result in the account(s) being frozen.');
define('FAQ_HOW_I_MOVE_LVLS', 'How do I move up levels?');
define('FAQ_HOW_I_MOVE_LVLS_ANS_1',
    'To move up levels you must gain ?EXP? points. These можешь be attained in the following ways');
define('FAQ_HOW_I_MOVE_LVLS_ANS_2', 'Committing successful crimes via the crimes page located in the left sidebar.');
define('FAQ_HOW_I_MOVE_LVLS_ANS_3',
    'Атакаing other Inmates. The amount of EXP points gained for this is determined upon your own level and the level of the inmate you are attacking. The higher level you attack above your level, the more EXP points you will gain.');
define('FAQ_HOW_I_MOVE_LVLS_ANS_4',
    'Mugging other inmates. This можешь be done in the same way as attacking, however instead of clicking ?Атака? in the ?Actions? section, click ?Mug?.');
define('FAQ_HOW_I_GET_POINTS', 'How do I get points?');
define('FAQ_HOW_I_GET_POINTS_ANS', 'Points можешь be acquired in many ways such as');
define('FAQ_HOW_I_GET_POINTS_ANS_1',
    'Voting daily for Заключенный Struggle. These links are reset everyday when the rollover happens allowing you to vote once every 24 hours.');
define('FAQ_HOW_I_GET_POINTS_ANS_2',
    'Buying points via the points market located in the prison page which можешь be found in the left menu sidebar.');
define('FAQ_HOW_I_GET_POINTS_ANS_3', 'Donating to Заключенный Struggle.');
define('FAQ_HOW_I_GET_POINTS_ANS_4',
    'Winning a 50-50 bet placed via the prison page located in the left menu side bar under the ?Cell 315? section.');
define('FAQ_HOW_I_CHANGE_PRISONS', 'How do I change prisons?');
define('FAQ_HOW_I_CHANGE_PRISONS_ANS',
    'Новый prisons come available depending upon your characters level. To move из one prison to another you must go to the prison page which you are currently in found via the left menu side bar and then clicking on ?Police Bus? under the ?Headquarters? section.');
define('FAQ_HOW_I_SET_AVATAR', 'How do I set my Avatar?');
define('FAQ_HOW_I_SET_AVATAR_ANS',
    'An avatar можешь be set via the ?Preferences? section located in the left sidebar. Once on this page there is a box which allows you to paste an external link in. Please be sure to make sure to add the ?http://? to the start of the link.');
define('FAQ_HOW_I_INCREASE_AWAKE', 'How do I increase my ?Awake??');
define('FAQ_HOW_I_INCREASE_AWAKE_ANS_1',
    'The Awake bar refills at a slower rate than the energy and nerve bar but можешь be refilled instantly by using an ?Awake Pill? which if you have will be located in your inventory.');
define('FAQ_HOW_I_INCREASE_AWAKE_ANS_2',
    'To actually increase your awakes over statistic which will help you for actions such as training in the gym you need to upgrade your Cell. This можешь be done via the Заключенный page located in the left sidebar and then clicking ?Move Cell? under the ?Тыr Cell? section.');
define('FAQ_HOW_I_GROW_WEED', 'How do I grow Weed?');
define('FAQ_HOW_I_GROW_WEED_ANS_1',
    'Weed можешь be grown only if you have ?Land? and ?Weed Seeds?. Land можешь be acquired 3 ways. Ты можешь buy land из the ?Land Market? located in your Заключенный page. Ты можешь also find Land by searching in the Заключенный Yard which можешь be done once daily again via your Заключенный page under the ?Rehabilitation? section or the other way is to donate to Заключенный Struggle and buy land.');
define('FAQ_HOW_I_GROW_WEED_ANS_2',
    'Once you have Land, you then need Weed Seeds which можешь be bought из the ?Shady-Looking Stranger? again under the ?Rehabilitation? section.');
define('FAQ_BENEFITS_RESPECTED_PRISONERS', 'What benefits do ?Respected Заключенныйers? get?');
define('FAQ_BENEFITS_RESPECTED_PRISONERS_1', 'Энергия is refilled twice as quick');
define('FAQ_BENEFITS_RESPECTED_PRISONERS_2', 'Nerve is refilled twice as quick');
define('FAQ_BENEFITS_RESPECTED_PRISONERS_3', 'HP is gained twice as quick');
define('FAQ_BENEFITS_RESPECTED_PRISONERS_4', 'Awake is gained twice as quick');
define('FAQ_BENEFITS_RESPECTED_PRISONERS_5', 'Gain 4% bank interest instead of 2%');
define('FAQ_BENEFITS_RESPECTED_PRISONERS_6', 'Red, Bold name');
define('FAQ_BENEFITS_RESPECTED_PRISONERS_7', 'Money and Points depending upon which package chosen');
define('FAQ_BENEFITS_RESPECTED_PRISONERS_8', 'Receive more points when voting on a daily basis');
define('FAQ_WHEN_NEW_DAY_START', 'What time does a new day start?');
define('FAQ_WHEN_NEW_DAY_START_ANS',
    'The game will rollover and allow you to vote and search the prison yard again at 10:00am server time.');

//fields
define('FIELD_PLANTED_ACRES', 'Ты have planted %s acres of marijuana.');
define('FIELD_VALID_AMT_LAND', 'Please enter a valid amount of land.');
define('FIELD_NOT_ENOUGH_MARIJUANA',
    'Ты do not have enough marijuana seeds to plant that many acres of weed. Ты need 100 seeds per acre.');
define('FIELD_NOT_ENOUGH_LAND', 'Ты do not have that many acres of land.');
define('FIELD_NOT_ENOUGH_FERTILIZER', 'Ты do not have enough fertilizer.');
define('FIELD_NOT_REQ_LVL', 'Ты do not match the required level to plant in this prison.');
define('FIELD_NOT_GROWN', 'None of your acres of land was grown enough to be harvested.');
define('FIELD_NOT_HARVEST_ANOTHER_LAND', 'Sorry, but you можешьnot harvest another prisoner\'s land.');
define('FIELD_NOT_FINISHED_GROWING', 'This crop hasn\'t finished growing yet.');
define('FIELD_RECEIVED_MARIJUANA', 'Ты have received %s ounces of marijuana.');
define('FIELD_HAVE_ONLY_FERTILIZER', 'Ты have only %s fertilizer(s).');
define('FIELD_NOT_HAVE_FERTILIZER', 'Ты do not have enough fertilizer to fertilize %s acres');
define('FIELD_CAN_APPLY_FERTILIZER', 'Ты можешь apply only %s fertilizer(s).');
define('FIELD_FERTILIZER_APPLIED', 'Ты successfully fertilized %d acres of land.');
define('FIELD_HEAD', 'Land Management');
define('FIELD_MANAGE_LAND', 'Here is where you можешь manage your acres of land.');
define('FIELD_PLANT', 'Plant');
define('FIELD_NOT_OWN_LAND', 'Ты currently do not own any acres of land.');
define('FIELD_HAVE_MARIJUANA_SEEDS', 'Ты currently have %s marijuana seeds, which is enough to grow %s acres of weed.');
define('FIELD_HAVE_NO_MARIJUANA_SEEDS', 'Ты currently have no marijuana seeds.');
define('FIELD_FERTILIZER_ID', '99');
define('FIELD_HAVE_FERTILIZER', 'Ты currently have %s fertilizer(s).');
define('FIELD_NOT_FERTILIZER', 'Ты currently have no fertilizer.');
define('FIELD_EXCESSIVE_FERTILIZER', 'Ты можешьnot fertilize more than %d acres');
define('FIELD_PRISON', 'Заключенный');
define('FIELD_LAND_AVAILABLE', 'Land available');
define('FIELD_ACRES_TO_GROW', 'Acres to grow');
define('FIELD_APPLIED_FERTILIZER', 'Applied Fertilizer');
define('FIELD_ADD_FERTILIZER', 'Add Fertilizer');
define('FIELD_PLANT_SEEDS', 'Plant Seeds');
define('FIELD_FERTILIZER', 'Fertilizer');
define('FIELD_CURRENTLY_GROWING', 'Currently Growing');
define('FIELD_NOT_HAVE_ANY_GROWING_LAND', 'Ты currently do not have any growing acres of land.');
define('FIELD_TIME_PLANTED', 'Time Planted');
define('FIELD_CROP_TYPE', 'Crop Type');
define('FIELD_ACRES_PLANTED', 'Acres Fertilized / Planted');
define('FIELD_TOTAL_PLANTS_LEFT', 'Total Plants Left');
define('FIELD_ON_ALL_ACRES', 'on all acres');
define('FIELD_TIME_LEFT', 'Time Left');
define('FIELD_HARVEST_GROWN_ACRES', 'Harvest Grown Acres');
define('FIELD_UNTIL_HARVEST', 'Until Harvest');
define('FIELD_FULLY_FERTILIZER', 'Fully fertilized');
define('FIELD_NO_FERTILIZER', 'No fertilizer left');

define('GROWING_LAND_ERR_PLANTING', 'Error while trying to plant the land. Please try again later.');

//footer
define('TOTAL_PRISONERS', 'Total Заключенныйers');
define('PRISONERS_ONLINE', 'Заключенныйers Online');
define('PRISONERS_ONLINE_DAY', 'Заключенныйers Online (24 Hours)');
define('PAGE_GENERATED', 'This page was generated in %s seconds');
define('GAME_COPYRIGHT', 'Game Copyright 2007-2008 - prisonstruggle@gmail.com');

//forgot
define('ACCOUNT_RECOVERY', 'Account Recovery');
define('SEND_REQ_TO_EMAIL',
    'Just send that request из the email you have registered in the game and with the использоватьrname that you are using. Thanks');
define('PRISONSTRUGGLE_EMAIL', 'Заключенныйstruggle Email');

//forgottonpwd
define('FORGOTTEN_PWD', 'Forgotten your password?');
define('FORGOTTEN_CLICK_HERE',
    'Please click <a href="#" onclick="document.getElementById(\'password_reset_box\').style.display=\'block\'; return false;">Here</a> to reset it');

//gameAirHockey
define('AIR_HOCKEY', 'Air Hockey');

//gamePool
define('GAME_POOL', 'Pool');
define('GAME_TABLE_TENNIS', 'Table Tennis');

//gang
define('GANG', 'Gang');
define('GANGS', 'Gangs');
define('GANG_NO_MSGS', 'Тыr gang currently has no message.');
define('GANG_NO_PVT_MSGS', 'Тыr gang currently has no private message.');
define('GANG_PVT', 'Private');
define('GANG_INFO', 'Gang Info');
define('GANG_MGT', 'Gang Management');
define('GANG_INVITE_PRISONER', 'Invite Заключенныйer');
define('GANG_MANAGE_MEMBERS', 'Manage Gang Members');
define('GANG_CHANGE_MESSAGE', 'Change Gang Message');
define('GANG_MEMBERSHIP_REQ', 'Membership Requests');
define('GANG_RANKS', 'Gang Ranks');
define('GANG_MEMBER_RANKS', 'Member Ranks');
define('GANG_TAXES', 'Gang Taxes');
define('GANG_MASS_MAIL', 'Gang Mass Mail');
define('GANG_MASS_PAYMENT', 'Gang Mass Payment');
define('GANG_ACTIONS', 'Gang Actions');
define('GANG_VIEW', 'View Gang');
define('GANG_LEAVE', 'Leave Gang');
define('GANG_DEFENSE_LOG', 'Defense Log');
define('GANG_ARMORY', 'Armory');
define('GANG_VAULT', 'Vault');
define('GANG_CRIMES', 'Gang Crimes');
define('GANG_VAULT_LOG', 'Vault Log');
define('GANG_MEMBER_LOG', 'Member Log');
define('GANG_MEMBER_CONTRIBUTIONS', 'Member Contributions');
define('GANG_LEADER', 'Gang Leader');
define('GANG_CHANGE_LEADER', 'Change Leader');
define('GANG_CHANGE_NAME', 'Change Gang Имя and Tag');
define('GANG_DELETE', 'Delete Gang');
define('GANG_WARS', 'Gang Wars');
define('GANG_WAR_ROOM', 'War Room');
define('GANG_ACTIVE_WARS', 'Active Wars');
define('GANG_FINISHED_WARS', 'Finished Wars');
define('GANG_WAR_LOGS', 'War Logs');
define('GANG_SURE_TO_DELETE',
    'Are you sure you want to delete gang? All vault деньги and points will be lost, all armory will be lost, all Inmates on your gang will stay gangless.');
define('GANG_LEAVE', 'Leave Gang');
define('GANG_POINTS_MAX_ERROR', 'Ты можешьnot store more than %s points on the gang vault.');

//gang_lit

define('GANG_LIST', 'Gang List');

//gangarmory
define('GANG_ARMORY_TAKEN', 'Ты have successfully taken %s %s(s) из the regiment armory.');
define('GANG_NOT_ENOUGH_ITEM_TO_DEPOSIT', 'Ты do not have enough %ss to deposit (Max: %s).');
define('GANG_ARMORY_ADDED_ITEM', 'Ты have added %s %s(s) to the regiment armory.');
define('GANG_ARMORY', 'Armory');
define('GANG_VAULT_ITEMS', 'Items In Vault');
define('GANG_VAULT_NO_ITEM', 'No items found in gang vault.');
define('GANG_ARMORY_ITEM_STACK', 'Item Stack');
define('GANG_ARMORY_AVAILABLE', 'Available');
define('GANG_ARMORY_LOANED', 'Loaned');
define('GANG_ARMORY_QTY_WITHDRAW', 'Quantity to withdraw');
define('GANG_ARMORY_TAKE', 'Take');
define('GANG_ARMORY_LOAN_RESTORE', 'Loan/Restore Item');
define('GANG_ARMORY_ADD_ITEM', 'Add Items To Vault');
define('GANG_ARMORY_OWNED_QTY', 'Owned quantity');
define('GANG_ARMORY_QTY_DEPOSIT', 'Quantity to deposit');
define('GANG_ARMORY_DEPOSIT', 'Deposit');

//poker
define('POKER_MONEY_NOT_ENOUGH', 'Ты do not have enough poker деньги.');

//gang_attack_log
define('GANG_ATTACK_LOG', 'Атака Log');
define('GANG_ATTACK_NO_LOG', 'No gang attack log entries found.');
define('ATK_ATTACKER', 'Атакаer');
define('ATK_DEFENDER', 'Defender');
define('ATK_WINNER', 'Winner');

// gang at war of fame
define('GANG_WAR_OF_FAME', 'All Time Gang War Of Fame');
define('GANG_WAR_NO_RANKED_GANGS', 'There are currently no ranked gangs.');
define('GANG_WAR_OF_FAME_POINTS', 'War Of Fame Points');

//gangcrime
define('GANG_CRIME_INVALID_ID', 'Invalid crime id.');
define('GANG_CRIME_INVALID_TYPE', 'Invalid crime type.');
define('GANG_CRIME_ALREADY_STARTED', 'Тыr gang has already started another crime, please wait till it ends.');
define('GANG_CRIME_NEED_ONLINE_MEMBERS', 'Тыr gang need at least %d online members to start this crime !');
define('GANG_CRIME_NEED_MIN_MEMBERS', 'Тыr gang need at least %d members to start this crime !');
define('GANG_CRIME_STARTED', 'Gang crime started!');
define('GANG_CRIME_INVALID_FEE', 'Invalid member fee. Must be in between 0 and 100.');
define('GANG_CRIME_NOT_LEADER', 'Ты можешьnot set member fee becaиспользовать you are not the leader.');
define('GANG_CRIME_FEE_SAVED', 'Gang member fee saved successfully.');
define('GANG_CRIME_STOPED', 'Gang crime Stopped!');
define('GANG_CRIME_STARTING_FEE', 'Starting member fee');
define('GANG_CRIME_MEMBER_FEE', 'Member Fee');
define('GANG_CRIME_REWARD_TXT', '% of the gang crime reward.');
define('GANG_CRIME_RECENT', 'Recent Crimes (Last 15 crimes)');
define('GANG_CRIME', 'Crime');
define('GANG_CRIME_END_TIME', 'End Time');
define('GANG_CRIME_RESULT', 'Result');
define('GANG_CRIME_MESSAGE', 'Message');
define('GANG_CRIME_STARTED_BY', 'Started By');
define('GANG_CRIME_SURE_TO_STOP', 'Are you sure you would like to stop this crime ?');
define('GANG_CRIME_HEAD', '%s -  Crimes - %s Total Members / %s Online');
define('GANG_CRIME_NAME', 'Имя');
define('GANG_CRIME_MIN_ONLINE', 'Min Online');
define('GANG_CRIME_MIN_MEMBERS', 'Min Members');
define('GANG_CRIME_MIN_DURATION', 'Duration');
define('GANG_CRIME_HAPPENING', 'Happening');
define('GANG_CRIME_WAITING', 'Waiting');
define('GANG_CRIME_SUCCESS', 'Success');
define('GANG_CRIME_STOPPED', 'Stopped');
define('GANG_CRIME_FAILED', 'Failed');

define('GANGCRIMES_STEALING_THE_PORN_STASH_CRIME', 'Stealing the porn stash');
define('GANGCRIMES_STEALING_THE_PORN_STASH_SUCCESS',
    'Ты were successful in stealing the porn stash из the director\'s office.');
define('GANGCRIMES_STEALING_THE_PORN_STASH_FAILED', 'Ты failed to steal the porn stash из the director\'s office.');
define('GANGCRIMES_ROBBING_THE_CIGARETTE_DELIVERY_TRUCK_CRIME', 'Robbing the cigarette delivery truck');
define('GANGCRIMES_ROBBING_THE_CIGARETTE_DELIVERY_TRUCK_SUCCESS',
    'Ты were successful in stealing cigarette boxes из the delivery truck.');
define('GANGCRIMES_ROBBING_THE_CIGARETTE_DELIVERY_TRUCK_FAILED',
    'Ты failed to rob the delivery truck. Maybe your gang should train before trying to pull something like this off.');
define('GANGCRIMES_ATTACKING_THE_GUARDS_CRIME', 'Атакаing the guards');
define('GANGCRIMES_ATTACKING_THE_GUARDS_SUCCESS',
    'Ты successfully beat the crap out of the guards. That should teach them not to mess with your gang again.');
define('GANGCRIMES_ATTACKING_THE_GUARDS_FAILED',
    'Ты failed to attack the guards. Тыr gang better do something quick to get your respect back.');
define('GANGCRIMES_ROB_A_CIGARETTE_CRIME', 'Rob a cigarette');
define('GANGCRIMES_ROB_A_CIGARETTE_SUCCESS', 'Ты were successful in robbing a cigarette из... a kid.');
define('GANGCRIMES_ROB_A_CIGARETTE_FAILED', 'Ты failed to steal a cigarette, what a loser.');
define('GANGCRIMES_ASSASSINATE_VIP_PRISONER_CRIME', 'Assassinate VIP prisoner');
define('GANGCRIMES_ASSASSINATE_VIP_PRISONER_SUCCESS',
    'Ты successfully assassinated the VIP prisoner. That showed him the difference between rapping about prison and being in one.');
define('GANGCRIMES_ASSASSINATE_VIP_PRISONER_FAILED',
    'Ты failed your assassination attempt. Ты better watch your back из his crew.');
define('GANGCRIMES_RIOT_IN_THE_PRISON_CRIME', 'Riot in the prison');
define('GANGCRIMES_RIOT_IN_THE_PRISON_SUCCESS',
    'Ты successfully caиспользоватьd a riot in the prison. This should get the director\'s attention that your gang means business.');
define('GANGCRIMES_RIOT_IN_THE_PRISON_FAILED',
    'Ты failed to caиспользовать a riot. The guards have it in for your gang.');

//ganginvites
define('GANG_JOINED', 'Ты have joined the %s gang !');
define('GANG_DECLINED_INVITATION', 'Ты have declined the invitation из the %s gang.');
define('GANG_INVITATIONS', 'Gang Invitations');
define('GANG_NO_INVITATIONS', 'No gang invitations found.');
define('GANG_INVITATION_DECISION', 'Decision');
define('GANG_INVITATION_ACCEPT', 'Accept');
define('GANG_INVITATION_DECLINE', 'Decline');

//gangjoin
define('GANG_JOIN', 'Gang Join');
define('GANG_JOINED_ANOTHER_GANG',
    'This prisoner has joined another gang in the meantime. His request was automatically deleted.');
define('GANG_JOIN_ACCEPTED_MEMBERSHIP', '%s accepted your membership application. Ты are now a %s gang member !');
define('GANG_JOIN_ACCEPTED_INMATE', 'Ты have accepted the inmate in your gang.');
define('GANG_JOIN_DECLINED_MEMBERSHIP', '%s declined your membership application.');
define('GANG_JOIN_DECLINED_INMATE', 'Ты have declined the inmate membership application.');
define('GANG_JOIN_MEMBERSHIP_REQUESTS', 'Membership Requests');
define('GANG_JOIN_NO_MEMBERSHIP_REQUESTS', 'No membership requests found.');
define('GANG_JOIN_REASON', 'Reason');
define('GANG_JOIN_DECISION', 'Decision');
define('GANG_JOIN_ACCEPT', 'Accept');
define('GANG_JOIN_DECLINE', 'Decline');

//ganglog
define('GANG_DEFENSE_LOG', 'Defense Log');
define('GANG_DEFENSE_NO_LOG', 'No gang defense log entries found.');

//gangmassmail
define('GANG_MASS_MAIL_SENT', 'Тыr mass mail was successfully sent !');
define('MASS_MAIL', 'Mass Mail');
define('GANG_MASS_MAIL_SEND_HEAD', 'Here you можешь send a mass mail to prisoners in your gang.');
define('GANG_MASS_NEW_MESSAGE', 'Новый Message');
define('GANG_MASS_MAIL_TARGET_RANK', 'Target Rank');
define('MAIL_SUBJECT', 'Subject');
define('MAIL_MESSAGE', 'Message');
define('MAIL_SEND', 'Отправить');

//gangMassPayment
define('GANG_MASSPAY', 'Mass Payment');
define('GANG_MASSPAY_INVALID_AMT', 'Invalid amount.');
define('GANG_MASSPAY_NOT_ENOUGH_MONEY',
    'There is not enough деньги in the gang vault to send this payment. Ты need at least $%s.');
define('GANG_MASSPAY_CANT_SEND',
    'Could not send the payment becaиспользовать the vault amount has changed. Please refresh and try again.');
define('GANG_MASSPAY_RECEIVED_FROM_GANG', 'Ты have received a $%s payment из your gang !');
define('GANG_MASSPAY_SUCCESSFULLY_SENT',
    'Тыr mass payment was successfully sent ! $%s were deducted из the gang vault and divided between %s gang member(s).');
define('GANG_NO_ANY_MEMBER_PAYMENT', 'There aren\'t any member to send the payment to.');
define('GANG_MASSPAY_MSG_1',
    'Here you можешь send a mass payment to your gang members. Be careful, 50% goes to gang member hand and 50% to bank.');
define('GANG_MASSPAY_NEW_PAYMENT', 'Новый Payment');
define('GANG_MASSPAY_TOTAL_MONEY', 'Total gang деньги');
define('GANG_MASSPAY_TARGET_RANK', 'Target Rank (# members)');
define('GANG_MASSPAY_AMOUNT', 'Amount per member');
define('GANG_MASSPAY_SEND', 'Отправить Payment');

//gangmembercontributions
define('GANG_CONTRIB', 'Member contributions');
define('GANG_NO_CONTRIB', 'There are currently no gang member contributions.');
define('GANG_CONTRIB_NOT_RESET', 'Only the gang leader можешь reset member contributions.');
define('GANG_CONTRIB_RESET', 'Memeber contributions have successfully been reset.');
define('ATTACK_MONEY', 'Атака Money');
define('ATTACK_XP', 'Атака XP');
define('DEFENSE_MONEY', 'Defense Money');
define('DEFENSE_XP', 'Defense XP');
define('MUG_MONEY', 'Mug Money');
define('MONEY_BALANCE', 'Money Balance');
define('POINTS_BALANCE', 'Points Balance');
define('CRIMES_STARTED', 'Crimes Started');
define('GANG_CRIME_MONEY', 'Gang Crime Money');
define('WAR_POINTS', 'War Points');

//gagnmemberlog
define('GANG_MEMBER_LOG', 'Member Log');
define('GANG_MEMBER_NO_LOG', 'No gang member log entries found.');
define('GANG_MEMBER_LOG_ACTION_TAKEN_BY', 'Action taken by');
define('GANG_MEMBER_LOG_INVITED_JOINED', '<span style="color:darkgreen;">was invited and joined</span> the gang.');
define('GANG_MEMBER_LOG_JOINED', '<span style="color:darkgreen;">joined</span> the gang.');
define('GANG_MEMBER_LOG_LEFT', '<span style="color:red;">left</span> the gang.');
define('GANG_MEMBER_LOG_KICK', 'is <span style="color:red;">kicked </span> out of the gang.');
define('GANG_MEMBER_LOG_REQUESTED', 'requested to join the gang.');
define('GANG_MEMBER_LOG_REQUESTED_DECLINED', '\'s request to join the gang <span style="color:red;">declined</span>.');
define('GANG_MEMBER_LOG_UNKNOWN', ': Unknown action.');

//gangmembers
define('GANG_MEMBER_RANKS_UPDATED', 'Ranks have successfully been updated.');
define('GANG_MEMBER_PRISONER', 'Заключенныйer');
define('GANG_MEMBER_LAST_ACTIVE', 'Last Active');
define('GANG_RANK', 'Gang Rank');

//gangPermissions
define('GANG_RANK_DELETED', 'The gang rank was successfully deleted !');
define('GANG_PERM_UPDATED', 'The gang permissions were successfully updated !');
define('GANG_RANK_EXISTS', 'That rank name already exists.');
define('GANG_RANK_ADDED', 'Gang rank successfully added.');
define('GANG_ADD_RANK', 'Add new Rank');
define('GANG_RANK_PERMS', 'Rank Permissions');
define('GANG_NO_RANKS', 'No gang ranks found.');
define('GANG_DELETE_RANKS', 'Delete Gang Ranks');
define('GANG_INVITE_PLAYERS', 'Invite players');
define('GANG_MEMBERSHIP_REQUESTS', 'Membership requests');
define('GANG_TAX', 'Gang Tax');

//gangtax
define('GANG_TAX', 'Gang Tax');
define('GANG_TAX_INVALID_AMT', 'Invalid tax amount.');
define('GANG_TAX_CHANGED', 'Tax changed to %s%%.');
define('GANG_TAX_DEFINE', 'Define Gang Tax');
define('GANG_TAX_APPLIED_TO_ATKS_MUGS', 'This tax will be applied to all attacks and mugs');

//gangvault
define('GANG_VAULT_NOT_VALID_AMT', 'Please enter a valid amount.');
define('GANG_VAULT_NOT_ENOUGH_MONEY', 'There isn\'t enough деньги in the vault.');
define('GANG_VAULT_ACTION_CANT_PERFORMED', 'Action could not be performed, please refresh and try again.');
define('GANG_VAULT_MONEY_WITHDRAWN', 'Money withdrawn.');
define('GANG_VAULT_NOT_ENOUGH_MONEY', 'Ты do not have enough деньги to deposit.');
define('USER_NOT_ENOUGH_POINTS', 'Ты do not have enough points to deposit');
define('GANG_VAULT_DEPOSIT_MAX_ERR', 'Ты можешьnot deposit more than $%s.');
define('GANG_VAULT_MONEY_DEPOSITED', 'Money deposited');
define('GANG_VAULT_POINTS_DEPOSITED', 'Points deposited.');
define('GANG_VAULT_NOT_ENOUGH_POINTS', 'There aren\'t enough points in the vault.');
define('GANG_VAULT_MONEY', 'Money');
define('GANG_VAULT_POINTS', 'Points');
define('GANG_VAULT_WELCOME', 'Welcome to the деньги vault. There is currently  $%s in the gang vault.');
define('GANG_VAULT_WELCOME_1', 'Welcome to the points vault. There are currently %s points in the gang vault.');

//gangvaultlog
define('GANG_VAULT_LOG', 'Vault Log');
define('GANG_VAULT_LOG_CHANGENAME', '$%s to change Gang Имя');
define('GANG_VAULT_LOG_RECOVERED', 'a %s to armory из %s');
define('GANG_VAULT_LOG_DEPOSITARM', '%s %s to armory');
define('GANG_VAULT_LOG_WITHDRAWARM', '%s %s из armory');
define('GANG_VAULT_LOG_DEPOSITM', '%s into gang vault');
define('GANG_VAULT_LOG_DEPOSITP', '%s points into gang vault');
define('GANG_VAULT_LOG_WITHDRAWM', '%s из gang vault');
define('GANG_VAULT_LOG_WITHDRAWP', '%s points из gang vault');

//gangWarActive
define('GANG_NO_ACTIVE_WARS', 'There are currently no active gang wars.');
define('GANG_WAR_WITH', 'War with');
define('GANG_WAR_TIME_LEFT', 'Time left');
define('GANG_WAR_POINTS_TRIBUTE', 'Points tribute');
define('GANG_WAR_MONEY_TRIBUTE', 'Money tribute');
define('GANG_WAR_YOUR_POINTS', 'Тыr war points');
define('GANG_WAR_ENEMY_POINTS', 'Enemy war points');
define('GANG_WAR_MEMBERS', 'War Members');
define('GANG_WAR_ALREADY_ENDED', 'This war was already ended.');
define('GANG_WAR_DRAW', 'The war against %s has ended to a draw. Ты have earned your tribute back.');
define('GANG_WAR_ENDED',
    'The war against %s has ended. Тыr gang was victorious ! War Tribute earned: $%s and %s. The war MVM (Самый valuable member) was %s , who contributed for %s points');
define('GANG_WAR_ENDED_1', 'The war against %s has ended. Тыr gang was defeated and lost the war tribute.');
define('GANG_WAR_ENDED_2',
    'The war with %s has ended, the war MVM (Самый valuable member) was %s , who contributed for %s points');
define('GANG_WAR_ENDED_UNI_1',
    'The war against %s has ended. Тыr gang was victorious ! The war MVM (Самый valuable member) was %s , who contributed for %s points');
define('GANG_WAR_ENDED_UNI_2', 'The war against %s has ended. Тыr gang was defeated.');
define('GANG_WAR_ENDED_UNI_3',
    'The war with %s has ended, the war MVM (Самый valuable member) was %s , who contributed for %s points');
define('GANG_WAR_CANT_START_NOT_ENOUGH_MEM', '%s можешьnot start a war at the moment (Not enough members: Min = %s.');
define('GANG_WAR_CANT_START_ONE_LEVEL_MEMBER', '%s можешьnot start a war at the moment (Need one level 10 member).');
define('GANG_WAR_CANT_START_MAX_WARS',
    '%s можешьnot start a war at the moment (Too many wars started in last 10 days. Max =%s.)');
define('GANG_WAR_CANT_WITHOUT_NEGOCIATIONS', 'Ты можешьnot start a war without opening negociations first.');
define('GANG_WAR_NEGOCIATIONS_NOT_VALIDATED', 'The negociation wasn\'t validated before starting the war.');
define('GANG_WAR_CANT_WITH_OWN_GANG', 'Ты можешьnot start a war with your own gang.');
define('GANG_WAR_ALREADY_WITH_GANG', 'Ты already are at war with this gang.');
define('GANG_WAR_CANT_CREATE_NEW_WAR', 'Could not create a new war. Please try again later.');
define('GANG_WAR_NOT_CONTRIBUTE', 'Тыr attack did not contribute to %s\'s war effort.');
define('GANG_WAR_ATK_MADE_WIN', 'Тыr attack made the %s gang win a war ! Congratulations !');
define('GANG_WAR_ATK_CONTRIBUTED', 'Тыr attack contributed to the %s gang\'s war effort ! Keep on !');
define('GANG_WAR_ATK_CONTRIBUTED_NEGATIVELY',
    'Тыr attack contributed negatively to the %s gang\'s war effort. Be more careful next time.');
define('GANG_WAR_NEGOTIATIONS_NOT_PROPOSE_SELF', 'Ты можешьnot send a war proposition to your own gang.');
define('GANG_WAR_NEGOTIATIONS_ALREADY', 'Ты already have a war negociation opened with the %s gang.');
define('GANG_WAR_NEGOTIATIONS_CANT_CREATE', 'Could not create a new war negociation. Please try again later.');
define('GANG_WAR_NEGOTIATIONS_CANT_CANCEL_NOT_START', 'Ты можешьnot можешьcel a negociation that you did not start.');
define('GANG_WAR_NEGOTIATIONS_CANT_ACCEPT_STARTED', 'Ты можешьnot accept a negociation that you started.');
define('GANG_WAR_NEGOTIATIONS_NOT_MATCH_TRIBUTE',
    'Тыr gang does not match the tribute requirements : $%s and %s points.');
define('GANG_WAR_NEGOTIATIONS_PROBLEM_NOTIFY_LEADER',
    'Problem while trying to notify the other gang leader for tribute requirements.');
define('GANG_WAR_NEGOTIATIONS_ACCEPT_OFFER',
    'The %s gang tried to accept your war offer, but could not becaиспользовать your gang did not match the tribute requirements : $%s and %s points.');
define('GANG_WAR_NEGOTIATIONS_NOT_MATCH_TRIBUTE_REQ',
    'The other gang does not match the tribute requirements : $%s and %s points. A notification was sent to the gang leader.');
define('GANG_WAR_NEGOTIATIONS_PRBLEM_REMOVE_TRIBUTE', 'Problem while trying to remove tribute из targetGang.');
define('GANG_WAR_NEGOTIATIONS_NOT_START_UNILATERAL',
    'Ты можешьnot start an unilateral war with a negociation you did not start.');
define('GANG_WAR_NEGOTIATIONS_NOT_START_UNILATERAL_1',
    'Ты можешьnot start an unilateral war if the target gang did not answer to your proposal.');
define('GANG_WAR_NEGOTIATIONS_CANT_REFUSE', 'Ты можешьnot refиспользовать this negociation.');
define('GANG_WAR_ALREADY', 'Ты already are at war with the %s gang.');

//gangWarFinished
define('GANG_WAR_FINISHED', 'Finished Wars');
define('GANG_NO_WAR_FINISHED', 'There are currently no finished wars.');
define('GANG_WAR_TIMELINE', 'Timeline');
define('GANG_WAR_RESULT', 'Result');

//gangWarLogs
define('GANG_WAR_LOGS', 'War Logs');
define('GANG_NO_WAR_LOGS', 'There are currently no war logs.');
define('GANG_ATTACKER_EARNED_WAR_POINTS', 'Атакаer Earned War Points');

//gangWarMembers
define('GANG_INVALID_WAR', 'Invalid war.');
define('GANG_WAR_NOT_AUTH_SEE_INFO', 'Ты aren\'t authorized to see info about this war.');
define('GANG_NO_WAR_MEMBERS', 'There are currently no members or former members at war with any gang.');
define('GANG_NO_WAR_ENEMY_MEMBERS', 'There are currently no members or former members in the enemy gang.');

//gangWarOfFame
define('GANG_WAR_OF_FAME_HEAD', 'Gang War Of Fame');

//gangWarRoom
define('GANG_WAR_ROOM_POINT_BETWEEN', 'Ты must enter a point tribute between %s and %s.');
define('GANG_WAR_ROOM_MONEY_BETWEEN', 'Ты must enter a деньги tribute between %s and %s.');
define('GANG_WAR_ROOM_SENT_PROPOSITION', 'Ты have successfully sent a war proposition to %s');
define('GANG_WAR_ROOM_CANCELED_PROPOSITION', 'Ты have можешьceled your war proposition to %s');
define('GANG_WAR_ROOM_ACCEPTED_PROPOSITION', 'Ты have accepted the war proposition из %s. The war has started !');
define('GANG_WAR_ROOM_REFUSED_PROPOSITION',
    'Ты have refиспользоватьd the war proposition из %s. They можешь still start a war without tributes.');
define('GANG_WAR_ROOM_STARTED_PROPOSITION', 'Ты have started a war with %s.');
define('GANG_WAR_ROOM', 'War Room');
define('GANG_WAR_NEGOTIATIONS', 'War Negotiations');
define('GANG_NO_WAR_NEGOTIATIONS', 'There are currently no opened negotiations.');
define('GANG_WAR_NEGOTIATIONS_WITH', 'Negotiation with');
define('GANG_WAR_NEGOTIATIONS_DATE', 'Negotiation opening date');
define('GANG_WAR_NEGOTIATIONS_STATUS', 'Status');
define('GANG_WAR_ACCEPT', 'Accept War');
define('GANG_WAR_REFUSE', 'Refиспользовать War');
define('GANG_WAR_START_CONFIRM_1', 'Do you really want to start the war even if the other gang refиспользоватьd ?');
define('GANG_WAR_START_CONFIRM_2', 'Ты will not be able to earn any tribute из this war.');
define('GANG_WAR_START', 'Start the War');
define('GANG_WAR_CANCEL_NEGOTIATION_CONFIRM', 'Do you really want to можешьcel this war negociation ?');
define('GANG_WAR_CANCEL_NEGOTIATION', 'Cancel negotiation');
define('GANG_WAR_START_NEW', 'Start a new war');
define('GANG_WAR_START_GANG', 'On Gang');
define('GANG_WAR_START_POINTS', 'Gang Points at war');
define('GANG_WAR_START_MONEY', 'Gang Money at war');

define('GANG_WAR_RULES', 'Gang War Rules');
define('GANG_WAR_RULES_1',
    'Wars are between two gangs and last 3 days. Either gang leader may declare war by going to the war room and sending a proposal to the other gang\'s leader.  To propose war, select the opposing gang из the drop down menu and enter in the number of points and деньги you would like to wager.  (Note: these funds must be in the gang vault before sending or accepting a proposal.)');
define('GANG_WAR_RULES_2',
    'If the enemy gang accepts your proposal, the funds wagered are placed in escrow until the war ends. The winning gang earns 90%, 5% is taken by the guards as a bribe to allow inmates to fight, and 5% goes to the War of Fame prize pool. If the enemy gang refиспользоватьs the war or does not make a decision within 2 days, then you можешь можешьcel the war or choose to continue.  If you continue, no prize or tribute will be taken.');
define('GANG_WAR_RULES_3',
    'In order to enter a war, gangs must have a member level 10 or higher and at least 4 total members.  Gangs may participate in more than one war at a time, but only 5 wars in any 10 day period.');
define('GANG_WAR_RULES_4',
    'The goal of a war is to accumulate 1,000 war points by successfully attacking enemy prisoners. If neither gang reaches 1,000 war points within 3 days, the gang with more points will be declared the winner. Ты may attack each enemy player up to 3 times per day, unless that prisoner is a lot lower level then you.  The maximum number of attacks per day will decrease as your opponent\'s level decreases compared to yours.  The number of war points earned for succeeding will depend on how your level compares to your opponent\'s level?more points will be awarded for killing players higher level than yourself, and less points will be received for defeating players lower level than yourself.  If the defending prisoner wins the combat, the attacker\'s gang will lose some war points.');
define('GANG_WAR_RULES_5',
    'At the start of every war, a list of each gang\'s current members will be saved and можешь be viewed at any time on the "Active Wars" page. Only players on these lists will be able to participate in the war. Players who leave either gang after the start of a war will still earn points for defeating enemy prisoners, and the other gang may continue to earn points by attacking them.  Conversely, players who join either gang after the start of a war will not be able to earn points for that war, and defeating them will not give points to the opposing gang.');
define('GANG_WAR_RULES_6_1',
    'Upon completion of a war, the winning gang можешь earn points towards the War of Fame list, a hall of fame for gangs.  The number of points awarded will be equal to the war points earned by the losing gang. This means that defeating stronger enemies will earn you more points. (Note: if another gang initiates war against you after you have declined their proposal, you may prevent them из earning War of Fame  points by not attacking any of their members.)  The first day of every month, prizes will be awarded to the top 10 gangs, and the War of Fame will be reset for the next month.');
define('GANG_WAR_RULES_6_2',
    'In order to earn points for the War of Fame, both gangs must meet the following requirements');
define('GANG_WAR_RULES_6_3', 'Have at least 10 total members, with a minimum of 3 members below level 13..');
define('GANG_WAR_RULES_6_4', 'Have not fought each other in the past 10 days.');
define('GANG_WAR_RULES_6_5', 'Have at least 10,000 gang EXP.');
define('GANG_WAR_RULES_6_6', 'Here are the prizes');
define('GANG_WAR_RULES_6_7',
    '1st gang: 20% of prize pool, 5000EXP, 1000 points to gang vault, 10 awake пилюляs to regiment armory, last 15 level использоватьrs receive 10 RP days (only if they are below level 15).');
define('GANG_WAR_RULES_6_8',
    '2nd gang: 15% of prize pool, 2000EXP, 500 points to gang vault, 5 awake пилюляs to regiment armory, last 10 level использоватьrs receive 10 RP days (only if they are below level 15).');
define('GANG_WAR_RULES_6_9',
    '3rd gang: 15% of prize pool, 1000EXP ,last 5 level использоватьrs receive 10 RP days (only if they are below level 15).');
define('GANG_WAR_RULES_6_10', '4th and 5th gang: 10% of prize pool.');
define('GANG_WAR_RULES_6_11', 'From 6th to 10th gang: 7.5% of prize pool.');
define('GANG_WAR_RULES_7',
    'If you have any questions about these rules please direct them to our Game Admins and they will happily assist you in any way they можешь.');
define('GANG_WAR_RULES_8', 'These rules are subject to change and staff interpretation at any time.');

//gangWars
define('GANG_WAR_STARTING_DATE', 'Starting Date');
define('GANG_WAR_ENDING_DATE', 'Ending Date');

//gym
define('GYM', 'Gym');
define('GYM_CANNOT_TRAIN_HOSPITALIZED', 'Ты можешьnot train at the gym if you are in the hospital.');
define('GYM_CANNOT_TRAIN_SHOWERS', 'Ты можешьnot train at the gym if you are in the showers.');
define('GYM_CANNOT_TRAIN_AWAKE', 'Ты можешьnot train at the gym when you are not awake.');
define('GYM_CANNOT_TRAIN_DRUG', 'Ты можешьnot train at the gym if you are under a drug effect.');
define('GYM_NOT_VALID_AMT', 'Please enter a valid amount.');
define('GYM_NOT_ENOUGH_ENERGY', 'Ты do not have enough energy.');
define('GYM_NOT_ENOUGH_POINTS', 'Ты do not have enough points, silly buns !');
define('GYM_NOT_INVALID_TRAINING', 'Invalid training type.');
define('GYM_NOT_INVALID_ATTEMPT', 'Invalid training attempt.');
define('GYM_TRAINED', 'Ты trained with %s energy and received %s %s.');
define('GYM_TRAINED_CRITICAL', 'CRITICAL SUCCESS:  You trained with %s energy and received %s %s.');
define('GYM_REFILLED_ENERGY', 'Ты spent 10 points and refilled your energy.');
define('GYM_INVALID_REFILLING_ATTEMPT', 'Invalid refilling attempt.');
define('GYM_ABLE_TO_TRAIN', 'Ты are able to train %s times.');
define('GYM_TRAIN_STRENGTH', 'Train Strength');
define('GYM_TRAIN_DEFENSE', 'Train Defense');
define('GYM_TRAIN_SPEED', 'Train Speed');
define('GYM_REFILL_ENERGY', 'Refill energy');
define('GYM_REFILL_ENERGY_CAREFUL', 'Careful: It will refill energy without confirmation.');
define('GYM_ATTRIBUTES', 'Attributes');
define('STRENGTH', 'Strength');
define('DEFENSE', 'Defense');
define('SPEED', 'Speed');

//halloffame
define('HALLOFFAME', 'Hall Of Fame');
define('HALLOFFAME_GOOD_RATING', 'Good Rating');
define('HALLOFFAME_BAD_RATING', 'Bad Rating');
define('PRISONER', 'Заключенныйer');
define('RATING', 'Rating');
define('MONEY', 'Money');
define('POINTS', 'Points');

//header
define('LOGGED_OUT', 'Ты have been logged out.');
define('USER_BANNED_RESON',
    'Banned for %s. Do not create new accounts to take care of bans, or we will never unban. We rarely unban, but you можешь trying emailing at prisonstruggle@gmail.com. We normally answer within 1-3 business days.');
define('USER_FROZEN', 'Ты were frozen until %s');
define('REASON', 'Reason');
define('MAINTENANCE_MODE', 'Maintenance mode');
define('MAINTENANCE_MODE_MSG',
    'We are sorry for the inconvenience. The game should be back soon. Only admins are allowed to login at the moment.');
define('SERVER_DOWN', 'SERVER DOWN');
define('SERVER_TIME', 'Server Time');
define('ENERGY', 'Эн-гия');
define('AWAKE', 'Awake');
define('NERVE', 'Нерв');
define('NEW_ANNOUNCEMENT', 'Новый Announcement');
define('HEADER_UPGRADE_INMATE', 'Upgrade Inmate');
define('HEADER_REFER_FOR_POINTS', 'Refer for points');
define('HEADER_VOTE_FOR_POINTS', 'Голосовать для очков');
define('HEADER_GANG_INVITATIONS', 'Ты have new gang invitations');
define('HEADER_VIEW_GANG_INVITATIONS', 'View Gang Invites');
define('HEADER_WRONG_MONEY_CORRECTED', 'Something was wrong with your деньги, corrected.');
define('HEADER_NEW_PRISONER', 'It looks like you are a Новый Заключенныйer. Check out the %s');
define('HEADER_TUTORIAL', 'Tutorial');
define('HEADER_GUARDS_PROTECTION_MSG', 'Ты have the guards protection for %s');
define('HEADER_CURRENTLY_IN_SHOWERS', 'Ты are currently in showers for %s more minutes.');
define('HEADER_HOSPITAL_MSG', 'Ты are in the hospital for %s.');
define('HEADER_WARNING_MSG',
    'Ты are navigating or refreshing too fast ! If you keep on, your account will be investigated by an administrator.');

//hitlist
define('HITLIST', 'Hitlist');
define('HITLIST_AVAILABLE', 'Available');
define('HITLIST_ACCEPTED', 'Accepted');
define('HITLIST_COMPLETED', 'Completed');
define('HITLIST_FAILED', 'Failed');
define('HITLIST_BUYER_TARGET_MUST_DIFFERENT', 'The buyer and target must be different.');
define('HITLIST_REWARD_MIN_MONEY', 'Money reward must not be less than $%s.');
define('HITLIST_HOSPITAL_TIME_BETWEEN_ERR', 'Hospital time must be a non zero positive number between 10 and 200.');
define('HITLIST_CANT_CONTRACT_ADMIN', 'Ты можешьnot put a contract on Admin.');
define('HITLIST_POST_MAX_CONTRACTS_ERR', 'A buyer можешь only post up to %s different contracts at the same time.');
define('HITLIST_NOT_ENOUGH_MONEY', 'Ты don\'t have enough деньги.');
define('HITLIST_NOT_ACCESS_TO_DELETE', 'Ты have not access to delete this contract.');
define('HITLIST_PROVIDER_BUYER_MUST_DIFFERENT', 'The provider and buyer must be different.');
define('HITLIST_PROVIDER_TARGET_MUST_DIFFERENT', 'The provider and target must be different.');
define('HITLIST_PROVIDER_MAX_CONTRACT', 'A provider можешь accept up to %s different provider at the same time.');
define('HITLIST_PROVIDER_HAVE_CONTRACT_ON_TARGET', 'Ты already have an open contract for this target.');
define('HITLIST_CANT_AFFORD_SAFTEY_FEE', 'Ты можешьnot afford the $%s safety fee.');
define('HITLIST_CONTRACT_NOT_AVAILABLE', 'This contract is not available.');
define('HITLIST_NOT_AUTH_COMPLETE_CONTRACT', 'Ты are not authorized to complete this contract.');
define('HITLIST_HITMAN_CONTRACT', 'Hitman Contract');
define('HITLIST_CONTRACT_FAILED_NOTIFY_BUYER',
    'Тыr contract on %s has been failed. The $%s деньги reward has been re-credited and your contract has been removed.');
define('HITLIST_CONTRACT_FAILED_NOTIFY_PROVIDER',
    'Ты have failed the contract you accepted against %s becaиспользовать the time limit expired. The $%s Safety fee its going to go for guards.');
define('HITLIST_CONTRACT_COMPLETED_NOTIFY_BUYER',
    'Тыr contract on %s has been completed by an anonymous hitman. The $%s деньги reward has been awarded and your contract has been removed.');
define('HITLIST_CONTRACT_COMPLETED_NOTIFY_PROVIDER',
    'Ты have completed the contract you accepted against %s. The $%s safety деньги has been refunded to your bank account. The $%s деньги reward has been credited on your bank account. The $%s bribe has been given to prison guards.');
define('HITLIST_CONTRACT_CREATED', 'Contract created successfully.');
define('HITLIST_CONTRACT_ACCEPTED', 'Contract accepted successfully.');
define('HITLIST_TARGET', 'Target');
define('HITLIST_HP_TIME_REQUIRED', 'Hospital Time Required');
define('HITLIST_MONEY_REWARDED', 'Money Rewarded');
define('HITLIST_SAFETY_FEE', 'Safety Fee');
define('HITLIST_TIME_LIMIT', 'Time Limit');
define('HITLIST_NO_ACTIVE_CONTRACTS', 'There are currently no active contracts.');
define('HITLIST_ENTER_TARGET', 'Please enter target использоватьrname.');
define('HITLIST_HP_TIME_NON_ZERO', 'Hospital time must be non zero positive number between 10 and 1000.');
define('HITLIST_WRONG_MONEY', 'Please enter positive whole number for деньги.');
define('HITLIST_REWARD_MONEY_NOT_LESS', 'Reward деньги must not be less than $%s');
define('HITLIST_WELCOME', 'Welcome to the hitlist !');
define('HITLIST_RULES', 'Here are the hitlist rules');
define('HITLIST_RULES_1', 'Hitman contracts involve a buyer, a provider and a target.');
define('HITLIST_RULES_2',
    'The buyer adds new contracts on other prisoners (targets) by choosing the hospital time required to validate the hit, the target id and the деньги reward (any amount). Once the contract is placed, the buyer деньги reward is taken из the buyer bank and placed on escrow. Once the hit is added, a time limit is automatically calculated for the hit. The contract is now opened for providers (hitmans). The contract must be accepted by a provider before it starts.');
define('HITLIST_RULES_3',
    'The provider (hitman) has a list of available contracts and accepted contracts. Each contract only shows the target, the hospital time, reward, and time limit. The provider можешь accept any contract he wants, he можешь even have multiple contracts accepted at the same time. To accept a contract, the provider must have the equivalent of %s%% of the contract reward on his bank account. When he accepts the contract, this \'Safety fee\' is taken из his account and also placed on escrow until the end of the contract. On the accepted contracts page, he можешь see all the contracts he was assigned, and directly attempt to make a hit on the target, if the target is available.');
define('HITLIST_RULES_4',
    'The contract only starts when the provider accepted it and has enough bank деньги to afford the safety fee. There можешь only be one provider per contract. The buyer knows nothing about the provider (he doesnt know his name etc).');
define('HITLIST_RULES_5',
    'The provider must hit the target enough time to reach the hospital time asked by the buyer.');
define('HITLIST_RULES_6',
    'If the time limit expires before the target has been hit for enough hospital time, the contract ends. The деньги is sent back to the buyer, the contract is removed из the hitlist, and both the buyer and provider get events about the failure. The %s%% Safety fee is taken by guards and might later be added as a lottery prize.');
define('HITLIST_RULES_7',
    'If the hospital time is reached before the time limit, the contract is completed, and the reward is sent to the provider, deducted из a bribing fee of %s%%. Safety fee is refunded to the contract provider(hitman). The buyer and provider get events about the success.');
define('HITLIST_POSTED_CONTRACTS', 'Posted Contracts');
define('HITLIST_ACCEPTED_CONTRACTS', 'Accepted Contracts');
define('HITLIST_ACTIVE_CONTRACTS', 'Active contracts');
define('HITLIST_CREATE_CONTRACT', 'Create new contract');
define('HITLIST_TARGET_ID', 'Target id');
define('HITLIST_HP_MIN_MAX_TEXT', 'min 10 minutes, max 200 minutes.');
define('HITLIST_MONEY_REWARD', 'Money reward');
define('HITLIST_CONTRACT_DELETED', 'Contract deleted successfully.');
define('HITLIST_HP_TIME_DONE', 'Hospital Time Done');
define('HITLIST_TIME_LEFT', 'Time Left');
define('HITLIST_STATUS', 'Status');
define('HITLIST_SURE_TO_CANCEL_CONTRACT', 'Are you sure? Ты want to можешьcel this contract?');

//home.php
define('SITE_TITLE', 'MMORPG Free Online Browser Based Game | ЗаключенныйStruggle.com');
define('HOME_USERNAME', 'Username');
define('HOME_PASSWORD', 'Password');
define('HOME_SECURITY_CODE', 'Security Code');
define('HOME_PAGE', 'Homepage');
define('HOME_MSG_1', '<strong>[massive multiplayer online role play game]</strong> based on the real inmate life.');
define('HOME_MSG_2',
    'Ты\'ve just been sent down for the second time, and its time to start the struggle of making a name for yourself by dominating the cells on the inside, but still keeping it locked down on the outside world.');
define('HOME_MSG_3',
    'Ты have the ability to murder, mug, join inside gangs, create your own inside gang or just stay as a lone serial killer taking out anyone that comes in your way to the top. Ты thought it was bad last time?');
define('HOME_MSG_4', 'Заключенный just got a whole lot worse!!');
define('HOME_REGISTER', 'Register');
define('HOME_LEGAL', 'Legal');
define('HOME_CONTACT', 'Contact');
define('HOME_FORUM', 'Forum');
define('HOME_DIRECTORY', 'Directory');

//hospital
define('HOSPITAL', 'Hospital');
define('HOSPITAL_SEARCH_INMATE', 'Search an inmate');
define('HOSPITAL_SEARCH_MSG', 'Ты можешь enter an inmate id or characters');
define('HOSPITAL_EMPTY', 'The hospital is currently empty.');
define('HOSPITAL_TIME_LEFT', 'Time Left');
define('HOSPITAL_ATTACKED_BY', 'Атакаed By');
define('HOSPITAL_LOST_TO', 'Lost To');
define('ROULETTE', 'Roulette');

//hoиспользовать
define('HOUSE_REQ_SEC_LVL_TO_BUY', 'Requires Security Level %s to buy that Cell.');
define('HOUSE_SOLD',
    'Ты have sold your Cell for 75% of what it was worth. That amount will go towards the purchase of the new Cell.');
define('HOUSE_NOT_ENOUGH_MONEY', 'Ты do not have enough деньги to buy that Cell.');
define('HOUSE_PURCHASED', 'Ты have purchased and moved into %s.');
define('HOUSE_NOT_HAVE_CELL', 'Ты do not have a Cell to sell.');
define('HOUSE_SOLD_CELL', 'Ты have sold your Cell for 75%% of its real value - $%s');
define('HOUSE_HEAD', 'Cell informations');
define('HOUSE_MSG_1',
    'Cells allow you to better sleep at night, and enjoy a better comfort in general. It also lets you gain a little more respect из your peers.');
define('HOUSE_MSG_2',
    'On top of the initial buying cost that is использоватьd to cover the guard corruption costs and the working hands to transport your stuff, <br> you need to pay an additional daily fee to have them \'look the other way\' as long as you are enjoying yourself in your own little place.<br><br> The daily fee is equal to 0.2% of the cell total value, so for example, with an Overnight cell, it will be $15 per day.<br>    If you do not have the daily fee in your деньги or bank when the guards comes by to collect it, you will be thrown out of your cell, and will recover 75% of your cell\'s worth in your bank.');
define('HOUSE_MSG_3', 'Please remember : Тыr guards take care of you.');
define('HOUSE_MOVE_CELL', 'Move Cell');
define('HOUSE_DAILY_BRIBE', 'Daily bribe');
define('HOUSE_SELL_CELL', 'Sell Тыr Cell');
define('HOUSE_SELL_CELL_FOR', 'for 75%% of its cost - $%s');
define('HOUSE_TYPE', 'Type');
define('HOUSE_MOVE', 'Move');
define('HOUSE_COST', 'Cost');
define('HOUSE_REQ_SEC_LVL', 'Requires Security Level %s');
define('HOUSE_MOVE_ADD', 'Move In/Add');

//hoиспользовать table
define('HOUSE_OVERNIGHT_CELL', 'Overnight Cell');
define('HOUSE_ISOLATION_CELL', 'Isolation Cell');
define('HOUSE_CELL_WITH_BEACH_POSTER', 'Cell with Beach Poster');
define('HOUSE_CELL_WITH_WINDOW', 'Cell with window');
define('HOUSE_ADD_MATTRESS_TO_CELL', 'Add Mattress to Cell');
define('HOUSE_ADD_TOILET_TO_CELL', 'Add Toilet to Cell');
define('HOUSE_ADD_SINK_TO_CELL', 'Add Sink to Cell');
define('HOUSE_ADD_BED_TO_CELL', 'Add Bed to Cell');
define('HOUSE_ADD_T.V._TO_CELL', 'Add T.V. to Cell');
define('HOUSE_ADD_PLASMA_T.V._TO_CELL', 'Add Plasma T.V. to Cell');
define('HOUSE_ADD_SOFT_KINGSIZE_BED_TO_CELL', 'Add Soft Kingsize Bed to Cell');
define('HOUSE_ADD_DVD_PLAYER_TO_CELL', 'Add DVD Player to Cell');
define('HOUSE_ADD_MINI_BAR_TO_CELL', 'Add Mini Bar to Cell');
define('HOUSE_ADD_COMPUTER_TO_CELL', 'Add Computer to Cell');
define('HOUSE_ADD_JACUZZI_TO_CELL', 'Add Jacuzzi to Cell');
define('HOUSE_ADD_POOL_TABLE_TO_CELL', 'Add Pool Table to Cell');
define('HOUSE_ADD_FIBER_OPTICS_INTERNET_CONNECTION_TO_THE_COMPUTER',
    'Add Fiber Optics Internet Connection to the Computer');
define('HOUSE_ADD_MASTER_PAINTING_TO_CELL', 'Add Master Painting to Cell');
define('HOUSE_ADD_MEZZANINE_TO_CELL', 'Add Mezzanine to Cell');
define('HOUSE_ADD_FOUNTAIN_TO_CELL', 'Add Fountain to Cell');
define('HOUSE_ADD_GOLDEN_STATUE_TO_CELL', 'Add Golden Statue to Cell');

//index
define('USER_CUSTOMIZE_TITLE', 'Customize your links');
define('USER_CUSTOMIZE',
    'When you start your inmate life, do not forget to customize your left menu links, on <a href="preferences.php">preferences</a> page !');
define('USER_INFO_SAVED', 'Тыr information has been saved correctly, thanks.');
define('USER_BECOME_RESPECTED_PRISONER', 'Become a Respected Заключенныйer');
define('USER_NOT_RESPECTED_PRISONER', 'Ты are not a Respected Заключенныйer?');
define('USER_STUFF_LOSING', 'Check the Stuff you are losing...');
define('USER_UPGRADE_NOW', 'Upgrade Now!');
define('USER_STARTING_PRICE', 'Starting as low as $3.00');
define('USER_GAIN_ENERGY_TWICE', 'Gain energy twice as quick.');
define('USER_GAIN_NERVE_TWICE', 'Gain nerve twice as quick.');
define('USER_GAIN_HP_TWICE', 'Gain HP twice as quick.');
define('USER_GAIN_AWAKE_TWICE', 'Gain awake twice as quick.');
define('USER_GAIN_EXTRA_BANK_INTEREST', 'Gain 4% bank interest instead of 2%');
define('USER_COLOR_NAME', 'Different Color Имя');
define('USER_MONEY_POINTS_ON_PACKAGE', 'Money and Points on package');
define('USER_VOTING_MORE_POINTS', 'Voting sites receive more points');
define('USER_WRITE_NOTES_INMATE', 'Write notes on every Inmate');
define('USER_CURRENT_JOB', 'Current Job');
define('USER_SEEDS', 'Seeds');
define('USER_MARIJUANA', 'Marijuana');
define('USER_SECURITY_LEVEL', 'Security Level');
define('USER_SECURITY_POINTS', 'Security Points');
define('USER_SECURITY_BONUS', 'Security Bonus');
define('USER_SUBSCRIPTION_STATUS', 'Subscription Status');
define('USER_SUBSCRIPTION_STATUS_INACTIVE', 'Inactive');
define('USER_SUBSCRIPTION_STATUS_NORMAL_RESPECTED', 'Normal Respected Member');
define('USER_SUBSCRIPTION_STATUS_VIP_RESPECTED', 'VIP Respected Member');
define('INDEX_RANKINGS', 'Rankings (updated every hour)');
define('USER_STRENGTH_RANK', 'Strength Rank');
define('USER_DEFENSE_RANK', 'Defense Rank');
define('USER_SPEED_RANK', 'Speed Rank');
define('USER_TOTAL_RANK', 'Total Rank');
define('USER_LEVEL_RANK', 'Level Rank');
define('USER_BATTLE_STATS', 'Battle Stats');
define('USER_MONEY_GAIN', 'Money Gain');
define('USER_CRIME_STATS', 'Crime Stats');
define('CRIME_SUCCEEDED', 'Succeeded');
define('CRIME_FAILED_TXT', 'Failed');
define('USER_MUG_STATS', 'Mug Stats');
define('USER_TOTAL_MONEY_EARNED_TODAY', 'Total деньги earned today');
define('USER_NOTE_BOOK', 'Note Book');
define('USER_CHECK_RP_NOTEBOOK', 'Check my RP notebook');
define('USER_PUT_MONEY_JOB_BANK', 'Put the деньги из job directly to bank account');

//inventory
define('USER_POPPED_AWAKE_PILL', 'Ты popped an awake пилюля.');
define('INVENTORY_USED_MAX_GP', 'Ты already использоватьd 3 guard protections today.');
define('INVENTORY_REQUESTED_GP', 'Ты successfully requested a guard protection.');
define('USER_POPPED_HEALTH_PILL', 'Ты popped a health пилюля.');
define('USER_UNEQUIPPED_WEAPON', 'Ты have unequipped your weapon.');
define('USER_UNEQUIPPED_ARMOR', 'Ты have unequipped your armor.');
define('INVENTORY_NO_WEAPON_SELECTED', 'No weapon selected.');
define('USER_REQ_SEC_LVL_EQUIP', 'Requires Security Level %s to equip a %s');
define('USER_EQUIPED_ITEM', 'Ты have successfully equipped a %s.');
define('INVENTORY_NO_ARMOR_SELECTED', 'No armor selected.');
define('INVENTORY_RID_OF_COCAINE', 'Ты successfully got rid of your Cocaine.');
define('INVENTORY_RID_OF_STEROIDS', 'Ты successfully got rid of your Steroids.');
define('INVENTORY_RID_OF_NO_DOZE', 'Ты successfully got rid of your No-Doze.');
define('INVENTORY_HEAD', 'Тыr Inventory');
define('INVENTORY_COLLECTED', 'Everything you have collected.');
define('FERTILIZER_DESC',
    'Fertilizer is cherished by many farmers, and for good reasons. Each fertilizer unit you own можешь be использоватьd on a single acre of land, to protect it из any bad elements during a whole growing cycle, effectively making sure this land will not have dying plants and instead grow more plants during this time.');
define('INVENTORY_EQUIPPED', 'Equipped');
define('INVENTORY_UNEQUIP', 'Unequip');
define('USER_NOT_HAVE_WEAPON_EQUIPPED', 'Ты do not have any weapon equipped.');
define('USER_NOT_HAVE_ARMOR_EQUIPPED', 'Ты do not have any armor equipped.');
define('INVENTORY_MARKET', 'Market');
define('INVENTORY_SEND', 'Отправить');
define('INVENTORY_EQUIP', 'Equip');
define('INVENTORY_USE', 'Use');
define('INVENTORY_THROW_AWAY', 'Throw Away');
define('INVENTORY_SURE_WANA_THROW_DRUG', 'Are you sure you wanna throw away all this drug ?');
define('INVENTORY_WEAPONS', 'Weapons');
define('INVENTORY_ARMOR', 'Armor');
define('INVENTORY_LOANED_WEAPONS', 'Loaned Weapons из Gang');
define('INVENTORY_LOANED_ARMOR', 'Loaned Armor из Gang');
define('INVENTORY_MISC', 'Misc.');
define('DRUGS', 'Drugs');
define('INVENTORY_CANT_USE_WEAPON', 'Ты можешь not equip a weapon with 0 offence.');
define('INVENTORY_CANT_USE_ARMON', 'Ты можешь not equip a armor with 0 defense.');

//invite
define('INVITE_INVALID_USERNAME', 'Invalid использоватьrname specified.');
define('INVITE_INVITED_TO_GANG', '%s was succesfully invited to the gang !');
define('INVITE_INMATES', 'Invite Inmates');
define('INVITE', 'Invite');

//Item Guide
define('ITEMGUIDE', 'Item Guide');
define('ITEMGUIDE_INFORMATION', 'All the information you need about the available Weapons and Armor.');
define('ITEMGUIDE_SELL_COST', 'Sell Cost');
define('ITEMGUIDE_RPSTORE', 'RPStore');

//item
define('ITEM_INVALID_QTY', 'Invalid quantity.');
define('ITEM_BAD_USER_INFO', 'Bad использоватьr information, please try again later.');
define('ITEM_NOT_ENOUGH_ITEMS', 'Ты do not have enough items to deposit.');
define('ITEM_CANT_DEPOSIT_ITEMS', 'Could not deposit items, Please refresh and try again.');
define('ITEM_NOT_ENOUGH_ARMORY_ITEMS', 'There aren\'t enough items in armory to take this quantity.');
define('ITEM_CANT_TAKEN', 'Items could not be taken. Please try again later.');

//itemmarket
define('ITEMMARKET_ERR_404', 'Error(404): Page does not exists.');
define('ITEMMARKET_TAKEN_OFF', 'Ты have taken your %s off the market.');
define('ITEMMARKET_NOT_ENOUGH_MONEY_AFFORD_ITEM', 'Ты do not have enough деньги to afford this item.');
define('ITEMMARKET_SOLD_TO', '%s was sold to %s');
define('ITEMMARKET_ITEM_SOLD', 'Item Sold');
define('ITEMMARKET_BOUGHT_ITEM', 'Ты have bought a %s.');
define('ITEMMARKET', 'Item Market');
define('ITEMMARKET_SALES_GOES_BANK_HAND', 'All деньги из sales goes 50% to your bank and %50 to your hand.');
define('ITEMMARKET_ITEMS_WILL_REMOVED',
    'Items left after 15 days are removed and put back into the seller\'s inventory.');
define('ITEMMARKET_SHOW_CHEAPEST', 'Show only the cheapest 5 items!');
define('ITEMMARKET_SHOW_MY_ITEMS', 'Show items placed by me!');
define('ITEMMARKET_SHOW_ALL', 'Show all the items!');
define('ITEMMARKET_VIEW_ALL', 'View All');
define('ITEM_NAME', 'Item Имя');
define('ITEMMARKET_COST', 'Cost');
define('ITEMMARKET_REMOVE_ITEM', 'Remove Item');
define('ITEMMARKET_ALREADY_REMOVED', 'Item already removed / bought из market.');

//landmarket
define('LANDMARKET_WRONG_AMT', 'Ты don\'t have so much land on market or amount wrong.');
define('LANDMARKET_TAKEN_LAND', 'Ты have taken %s acres of land off the market.');
define('LANDMARKET_LAND_REMOVED', 'Land already removed.');
define('LANDMARKET_NOT_SELLING_REQ_LAND', 'They are not selling that many acres of Land.');
define('LANDMARKET_ENTER_VALID_LAND_AMT', 'Please enter a valid amount of land to buy.');
define('LANDMARKET_BOUGHT_LAND', 'Ты have bought %s acre(s) of land for %s');
define('LANDMARKET_SOLD_LAND', 'Ты sold %s acres of land to %s');
define('LANDMARKET_REAL_ESTATE', 'Real Estate');
define('LANDMARKET_REMOVED_BUYED', 'Land already removed/buyed');
define('LANDMARKET_NOT_HAVE_THAT_MANY', 'Ты don\'t have that many land.');
define('LANDMARKET_ENTER_VALID_ACRES', 'Please enter a valid amount of acres.');
define('LANDMARKET_ENTER_VALID_AMT', 'Please enter a valid amount of деньги.');
define('LANDMARKET_ADDED_LAND', 'Ты have added %s acres of land to the market a price of $%s per acre.');
define('LANDMARKET', 'Land Market');
define('LANDMARKET_HEAD', 'Here you можешь buy / sell land on the land market.');
define('LANDMARKET_LAND_AMT', 'Amount of land');
define('LANDMARKET_PRICE', 'Price per acre');
define('LANDMARKET_ADD_LAND', 'Add Land');
define('LANDMARKET_LAND_AMT_ACRES', 'Land Amount (acres)');
define('LANDMARKET_REMOVE_LAND', 'Remove Land');

//fertilizer market
define('FERTILIZERMARKET', 'Fertilizer Market');
define('FERTILIZERMARKET_PRICE', 'Price($)');
define('FERTILIZERMARKET_UPDATE_PRICE', 'Update Price');
define('FERTILIZERMARKET_TOTAL', 'Total Fertilizer');
define('FERTILIZERMARKET_AVAILABLE', 'Available Fertilizer');
define('FERTILIZERMARKET_ADD', 'Add Fertilizer');
define('FERTILIZERMARKET_SHOW_CHEAPEST', 'Show only the cheapest 5 items!');
define('FERTILIZERMARKET_SHOW_MY_ITEMS', 'Show items placed by me!');
define('FERTILIZERMARKET_SHOW_ALL', 'Show all the items!');
define('FERTILIZERMARKET_OWNED_QTY', 'Owned Quantity');
define('FERTILIZERMARKET_WRONG_AMT', 'Ты don\'t have so much land on market or amount wrong.');
define('FERTILIZERMARKET_ENTER_VALID_AMT', 'Please enter a valid amount in price.');
define('FERTILIZERMARKET_ENTER_VALID_FERTILIZER', 'Please enter a valid amount in fertilizer.');
define('FERTILIZERMARKET_EXCESSIVE_FERTILIZER', 'Ты don\'t have that many fertilizer.');
define('FERTILIZERMARKET_NOT_HAVE_THAT_MANY', 'Ты don\'t have any fertilizer in Market.');
define('FERTILIZERMARKET_INSUFFICIENT_MONEY', 'Ты don\'t have enough деньги to purchase fertilizer.');
define('FERTILIZERMARKET_ADDED_LAND', 'Fertilizer added in market.');
define('FERTILIZERMARKET_FERTILIZER_UPDATED', 'Fertilizer updated in market.');
define('FERTILIZERMARKET_PRICE_UPDATED', 'Price updated in market.');
define('FERTILIZERMARKET_PURCHASED', 'Fertilizer purchased by %s.');
define('FERTILIZERMARKET_REMOVED', 'Fertilizer removed из market.');

//point market
define('POINTMARKET_WRONG_ID', 'Wrong id');
define('POINTMARKET_WRONT_AMT', 'Ты do not have that much points on market or amount is wrong.');
define('POINTMARKET_TAKEN_OFF', 'Ты have taken %s points off the market.');
define('POINTMARKET_ALREADY_REMOVED', 'Points already removed.');
define('POINTMARKET_NOT_SELLING_REQ_POINTS', 'They are not selling that many points.');
define('POINTMARKET_ENTER_VALID_POINT_AMT', 'Please enter a valid amount of points to buy.');
define('POINTMARKET_BOUGHT_POINTS', 'Ты have bought %s point(s) for $%s.');
define('POINTMARKET_SOLD', 'Ты sold %s points to $%s');
define('POINTMARKET_POINTS_ADDED', 'Point Market');
define('POINTMARKET_ALREADY_REMOVED_BUYED', 'Points already removed/buyed');
define('POINTMARKET_NOT_HAVE_THAT_MANY', 'Ты do not have that many points.');
define('POINTMARKET_ENTER_VALID_POINTS', 'Please enter a valid amount of points.');
define('POINTMARKET_ENTER_VALID_AMT', 'Please enter a valid amount of деньги.');
define('POINTMARKET_MAX_SELL_A_DAY',
    'Sorry, but due to temporary market regulation rules, the max amount of points you можешь sell per day is set at %s points.');
define('POINTMARKET_MAX_SELL_PRICE',
    'Sorry, but due to temporary market regulation rules, the max price is set at $%s.');
define('POINTMARKET_CANT_ADD_MORE', 'Ты already have 3 or more group of points for selling! Ты можешьnot add more!');
define('POINTMARKET_NOT_ENOUGH_POINTS', 'Ты do not have enough points.');
define('POINTMARKET_POINTS_ADDED', 'Ты have added %s points to the market a price of $%s per point.');
define('POINTMARKET_MSG_1', 'Here you можешь buy / sell points on the points market.');
define('POINTMARKET_POINTS_AMOUNT', 'Amount of points');
define('POINTMARKET_POINT_PRICE', 'Price per point');
define('POINTMARKET_ADD_POINTS', 'Add Points');
define('POINTMARKET_POINTS_AMT', 'Points Amount');
define('POINTMARKET_REMOVE_POINTS', 'Remove Points');

//job
define('JOB_QUITTED', 'Ты quit your job !');
define('JOB_TOOK', 'Ты took a new job : Ты are now a %s ! Congratulations !');
define('JOB_CURRENT', 'Current Job');
define('JOB_CURRENT_A', 'Ты are currently a %s');
define('JOB_MAKE_MONEY', 'Ты make $%s a day.');
define('JOB_QUIT', 'Quit Job');
define('JOB_CENTER', 'Job Center');
define('JOB_NO_AVAILABLE', 'There are no available jobs.');
define('JOB', 'Job');
define('JOB_REQUIREMENTS', 'Requirements');
define('JOB_PAYMENT', 'Daily Payment');
define('JOB_APPLY', 'Apply For Job');
define('JOB_TAKE', 'Take Job');

define('JOBS_HOOKER', 'Hooker');
define('JOBS_CASINO_DEALER', 'Casino dealer');
define('JOBS_SERVER', 'Server');
define('JOBS_COOK', 'Cook');
define('JOBS_CLEANER', 'Cleaner');
define('JOBS_NARC_SNITCH', 'Narc (Snitch)');
define('JOBS_TATTOO_ARTIST', 'Tattoo artist');
define('JOBS_SNEAK_SNEAKS_IN_OUTSIDE_FOOD', 'Sneak (Sneaks in outside food)');
define('JOBS_WEAPON_MAKER', 'Weapon maker');
define('JOBS_DRUG_RUNNER_SNEAKS_IN_DRUGS_FROM_OUTSIDE', 'Drug runner (Sneaks in Drugs из outside)');
define('JOBS_COLLECTOR_COLLECTS_MONEY_FOR_THE_HITMAN', 'Collector (collects деньги for the Hitman)');
define('JOBS_HITMAN', 'Hitman');
define('JOBS_LAUNDRY_FOLDER', 'Laundry Folder');
define('JOBS_PRISON_YARD_BOSS', 'Заключенный Yard Boss');
define('JOBS_PRISON_BOSS', 'Заключенный Boss');

//legal
define('LEGAL_INFORMATION', 'Legal Information');
define('LEGAL_USE_AGREEMENT', 'Terms of Use Agreement');
define('LEGAL_BACK_TO_REGISTER', 'Back to Register');
define('LEGAL_READ_TERMS', 'Read This Terms of Use Agreement Before Accessing Website.');
define('LEGAL_EFFECTIVE_DATE', 'Effective Date: This Terms of Use Agreement was last updated on November 15th 2007');
define('LEGAL_TERM_1',
    'This Terms of Use Agreement sets forth the standards of использовать of the ЗаключенныйStruggle.com Online Service for Registered Members. By using the ЗаключенныйStruggle.com website you (the ?Member?) agree to these terms and conditions. If you do not agree to the terms and conditions of this agreement, you should immediately cease all usage of this website. We reserve the right, at any time, to modify, alter, or update the terms and conditions of this agreement without prior notice. Modifications shall become effective immediately upon being             posted at ЗаключенныйStruggle.com website. Тыr continued использовать of the Service after amendments are posted constitutes an acknowledgement and acceptance of the Agreement and its modifications. Except as provided in this paragraph, this Agreement may not be amended.');
define('LEGAL_DESCRIPTION_SERVICE', 'Description of Service');
define('LEGAL_DESCRIPTION_SERVICE_DESC',
    'ЗаключенныйStruggle.com is providing Member with online game access. On the occasion that Member wishes to buy any in-game virtual goods or account upgrades, they may do so through the ЗаключенныйStruggle.com upgrade page. Anyone over the age of 13 is allowed to play the ЗаключенныйStruggle.com online game while following the in-game rules.');
define('LEGAL_DISCLAIMER_WARRANTIES', 'Disclaimer of Warranties');
define('LEGAL_DISCLAIMER_WARRANTIES_DESC',
    'ЗаключенныйStruggle.com makes no representations or warranties of any kind, express or implied, regarding the использовать or the results of this web site in terms of its correctness, accuracy, reliability, or otherwise. ЗаключенныйStruggle.com shall have no liability for any interruptions in the использовать of this Website. ЗаключенныйStruggle.com disclaims all warranties with regard to the information provided, including the implied warranties of merchantability and fitness for a particular purpose, and non-infringement. Some jurisdictions do not allow the exclusion of implied warranties; therefore the above-referenced exclusion is inapplicable.');
define('LEGAL_LIMITATION_LIABILITY', 'Limitation of Liability');
define('LEGAL_LIMITATION_LIABILITY_DESC',
    'ЗаключенныйStruggle.com sites shall not be liable for any damages whatsoever, and in particular ЗаключенныйStruggle.com shall not be liable for any special, indirect, consequential or incidental damages, or damages for loss profits, loss of revenue or loss of использовать arising out of or related to this website or the information contained within it. Whether such damages arise in contract, negligence, tort, under statute, in equity, at law, or otherwise, even if ЗаключенныйStruggle.com has been advised of the possibility of such damages. Some jurisdictions do not allow for the limitation or exclusion of liability for incidental or consequential damages, therefore some of the above limitations is inapplicable.');
define('LEGAL_INDEMNIFICATION', 'Indemnification');
define('LEGAL_INDEMNIFICATION_DESC',
    'Member agrees to indemnify and hold ЗаключенныйStruggle.com its parents, subsidiaries, affiliates, officers and employees, harmless из any claim or demand, including reasonable attorneys? fees and costs, made by any third party due to or arising out of Member?s использовать of the Service, the violation of this Agreement, or infringement by Member, or other использоватьr of the Service using Member?s computer, of any intellectual property or any other right of any person or entity.');
define('LEGAL_MEMBERS_ACCOUNT', 'Members Account');
define('LEGAL_MEMBERS_ACCOUNT_DESC',
    'All Members of the Service shall receive a password and an account. Members are entirely responsible for any and all activities which occur under their account whether authorized or not authorized. Member agrees to notify ЗаключенныйStruggle.com Staff of any unauthorized использовать of Member?s account or any other breach of security known or should be known to the Member. Member?s right to использовать the Service is personal to the Member. Member agrees not to resell or make any commercial использовать of the Service without the express written consent of  ЗаключенныйStruggle.com');
define('LEGAL_MODIFICATIONS_INTERRUPTION', 'Modifications and Interruption to Service');
define('LEGAL_MODIFICATIONS_INTERRUPTION_DESC',
    'ЗаключенныйStruggle.com reserves the right to modify or discontinue the Service with or without notice to the Member. ЗаключенныйStruggle.com shall not be liable to Member or any third party should ЗаключенныйStruggle.com exercise its right to modify or discontinue the Service. Member acknowledges and accepts that ЗаключенныйStruggle.com does not guarantee continuous, uninterrupted or secure access to our website and operation of our website may be interfered with or adversely affected by numerous factors or circumstances outside of our control.');
define('LEGAL_THIRD_PARTY_SITES', 'Third-Party Sites');
define('LEGAL_THIRD_PARTY_SITES_DESC',
    'Our website may require_once(links to other sites on the Internet that are owned and operated by online merchants and other third parties. Ты acknowledge that we are not responsible for the availability of, or the content located on or through, any third-party site. Ты should contact the site administrator or webmaster for those third-party sites if you have any concerns regarding such links or the content located on such sites. Тыr использовать of those third-party sites is subject to the terms of использовать and privacy policies of each site, and we are not responsible therein. We encourage all Members to review said privacy policies of third-parties? sites.');
define('LEGAL_COPYRIGHT', 'Copyright and Trademark Information');
define('LEGAL_COPYRIGHT_DESC_1',
    'All content included or available on this site, including site design, text, graphics, interfaces, and the selection and arrangements thereof is ?2007-2008 ЗаключенныйStruggle.com, will all rights reserved, or is the property of ЗаключенныйStruggle.com and/or     third parties protected by intellectual property rights. Any использовать of materials on the website, including reproduction for purposes other         than those noted above, modification, distribution, or replication, any form of data extraction or data mining, or other commercial             exploitation of any kind, without prior written permission of an authorized officer of ЗаключенныйStruggle.com is strictly prohibited. Members agree that they will not использовать any robot, spider, or other automatic device, or manual process to monitor or copy our web pages or the content contained therein without prior written permission of an authorized officer of ЗаключенныйStruggle.com');
define('LEGAL_COPYRIGHT_DESC_2',
    'All other trademarks displayed on the ЗаключенныйStruggle.com website are the trademarks of their respective owners, and constitute neither an endorsement nor a recommendation of those Vendors. In addition, such использовать of trademarks or links to the web sites of Vendors is not intended to imply, directly or indirectly, that those Vendors endorse or have any affiliation with ЗаключенныйStruggle.com');
define('LEGAL_OTHER_TERMS', 'Other Terms');
define('LEGAL_OTHER_TERMS_DESC',
    'If any provision of this Terms of Use Agreement shall be unlawful, void or unenforceable for any reason, the other provisions (and any partially-enforceable provision) shall not be affected thereby and shall remain valid and enforceable to the maximum possible extent. Ты agree that this Terms of Use Agreement and any other agreements referenced herein may be assigned by ЗаключенныйStruggle.com, in our sole discretion, to a third party in the event of a merger or acquisition. This Terms of Use Agreement shall apply in addition to, and shall not be superseded by, any other written agreement between us in relation to your participation as a Member. Member agrees that by accepting this Terms of Use Agreement, Member is consenting to the использовать and disclosure of their personally identifiable information and other practices described in our Privacy Policy Statement.');
define('LEGAL_PRIVACY', 'ЗаключенныйStruggle.com Website Privacy Statement');
define('LEGAL_LAST_UPDATE', 'This document was last updated on November 15th 2007');
define('LEGAL_ACKNOWLEDGEMENT', 'ACKNOWLEDGEMENT AND ACCEPTANCE OF TERMS');
define('LEGAL_ACKNOWLEDGEMENT_1',
    'ЗаключенныйStruggle.com is committed to protecting your privacy. This Privacy Statement sets forth our current privacy practices with regard to the information we collect when you or your computer interact with our website. By accessing ЗаключенныйStruggle.com you acknowledge and fully understand ЗаключенныйStruggle.com?s Privacy Statement and freely consent to the information collection and использовать practices described in this Website Privacy Statement.');
define('LEGAL_PARTICIPATING', 'PARTICIPATING MERCHANT POLICIES');
define('LEGAL_PARTICIPATING_DESC',
    'Related services and offerings with links из this website, including vendor sites, have their own privacy statements that можешь be viewed by clicking on the corresponding links within each respective website. Online merchants and others who participate in ЗаключенныйStruggle.com. services are encouraged to participate in industry privacy initiatives and to take a responsible attitude towards consumer privacy. However, since we do not have direct control over the policies or practices of participating merchants and other third parties, we are not responsible for the privacy practices or contents of those sites. We recommend and encourage that you always review the privacy policies of merchants and other third parties before you provide any personal information or complete any transaction with such parties.');
define('LEGAL_INFORMATION_COLLECT', 'INFORMATION WE COLLECT AND HOW WE USE IT');
define('LEGAL_INFORMATION_COLLECT_DESC',
    'ЗаключенныйStruggle.com collects certain information из and about its использоватьrs three ways: directly из our Web Server logs, the использоватьr, and with Cookies.');
define('LEGAL_WEB_SERVER_LOGS', 'Web Server Logs.');
define('LEGAL_WEB_SERVER_LOGS_1',
    'When you visit our Website, we may track information to administer the site and analyze its usage. Examples of information we may track include.');
define('LEGAL_WEB_SERVER_LOGS_2', 'Тыr Internet protocol address.');
define('LEGAL_WEB_SERVER_LOGS_3', 'The kind of browser or computer you использовать.');
define('LEGAL_WEB_SERVER_LOGS_4', 'Number of links you click within the site.');
define('LEGAL_WEB_SERVER_LOGS_5', 'State or country из which you accessed the site.');
define('LEGAL_WEB_SERVER_LOGS_6', 'Date and time of your visit.');
define('LEGAL_WEB_SERVER_LOGS_7', 'Имя of your Internet service provider.');
define('LEGAL_WEB_SERVER_LOGS_8', 'Web page you linked to our site из.');
define('LEGAL_WEB_SERVER_LOGS_9', 'Pages you viewed on the site.');
define('LEGAL_USE_COOKIES', 'Use of Cookies');
define('LEGAL_USE_COOKIES_1',
    'ЗаключенныйStruggle.com may использовать cookies to personalize or enhance your использоватьr experience. A cookie is a small text file that is placed on your hard disk by a Web page server. Cookies можешьnot be использоватьd to run programs or deliver virиспользоватьs to your computer. Cookies are uniquely assigned to you, and можешь only be read by a Web Server in the domain hat issued the cookie to you.');
define('LEGAL_USE_COOKIES_2',
    'One of the primary purposes of cookies is to provide a convenience feature to save you time. For example, if you personalize a web page, or navigate within a site, a cookie helps the site to recall your specific information on subsequent visits. Hence, this simplifies the process of delivering relevant content and eases site navigation by providing and saving your preferences and login information as well as providing personalized functionality.');
define('LEGAL_USE_COOKIES_3',
    'ЗаключенныйStruggle.com reserves the right to share aggregated site statistics with partner companies, but does not allow other companies to place cookies on our website unless there is a temporary, overriding customer value (such as merging into ЗаключенныйStruggle.com a site that relies on third-party cookies).');
define('LEGAL_USE_COOKIES_4',
    'Ты have the ability to accept or decline cookies. Самый Web browsers automatically accept cookies, but you можешь usually modify your browser setting to decline cookies. If you reject cookies by changing your browser settings then be aware that this may disable some of the functionality on our Website.');
define('LEGAL_PERSONAL_INFORMATION', 'Personal Information Users');
define('LEGAL_PERSONAL_INFORMATION_1',
    'Visitors to our website можешь register to purchase services. When you register, we will request some personal information such as name, address, email, telephone number or facsimile number, account number and other relevant information. If you are purchasing a service, we will request financial information. Any financial information we collect is использоватьd only to bill you for the services you purchased. If you purchase by credit card, this information may be forwarded to your credit card provider. For other types of registrations, we will ask for the relevant information. Ты may also be asked to disclose personal information to us so that we можешь provide assistance and         information to you. For example, such data may be warranted in order to provide online technical support and troubleshooting.');
define('LEGAL_PERSONAL_INFORMATION_2',
    'We will not disclose personally identifiable information we collect    из you to third parties without your permission except to the extent necessary including');
define('LEGAL_PERSONAL_INFORMATION_3', 'To fulfill your service requests for services');
define('LEGAL_PERSONAL_INFORMATION_4', 'To protect ourselves из liability');
define('LEGAL_PERSONAL_INFORMATION_5', 'To respond to legal process or comply with law, or');
define('LEGAL_PERSONAL_INFORMATION_6', 'In connection with a merger, acquisition, or liquidation of the company.');
define('LEGAL_WEB_BEACONS', 'USE OF WEB BEACONS OR GIF FILES');
define('LEGAL_WEB_BEACONS_1',
    'ЗаключенныйStruggle.com web pages may contain electronic images known as Web beacons ? sometimes also called single-pixel gifs ? that allow ЗаключенныйStruggle.com to count использоватьrs who have visited those pages and to deliver co-branded services. ЗаключенныйStruggle.com may require_once(Web beacons in promotional e-mail messages or newsletters in order to determine whether messages have been opened and acted upon.');
define('LEGAL_WEB_BEACONS_2',
    'Some of these Web beacons may be placed by third party service providers to help determine the effectiveness of our advertising campaigns or email communications. These Web beacons may be использоватьd by these service providers to place a persistent cookie on your computer. This allows the service provider to recognize your computer each time you visit certain pages or emails and compile anonymous information in relation to those page views, which in turn enables us and our service providers to learn which advertisements and emails bring you to our website and how you использовать the site. ЗаключенныйStruggle.com prohibits Web beacons из being использоватьd to collect or access your personal information.');
define('LEGAL_ACCESSING_WEB_ACCOUNT', 'ACCESSING WEB ACCOUNT INFORMATION');
define('LEGAL_ACCESSING_WEB_ACCOUNT_DESC',
    'We will provide you with the means to ensure that personally identifiable information in your web account file is correct and current. Ты may review this information by contacting us by sending an email to our support attendant ? prisonstruggle@gmail.com.');
define('LEGAL_CHANGES_THIS_STATEMENT', 'CHANGES TO THIS STATEMENT');
define('LEGAL_CHANGES_THIS_STATEMENT_DESC',
    'ЗаключенныйStruggle.com has the discretion to occasionally update this privacy statement. When we do, we will also revise the ?updated? date at the top of this Privacy page. We encourage you to periodically review this privacy statement to stay informed about how we are helping to protect the personal information we collect. Тыr continued использовать of the service constitutes your agreement to this privacy statement and any updates.');
define('LEGAL_CONTACTING_US', 'CONTACTING US');
define('LEGAL_CONTACTING_US_DESC',
    'If you have questions regarding our Privacy Statement, its implementation, failure to adhere to this Privacy Statement and/or our general practices, please contact us at - prisonstruggle@gmail.com.');

//loanarmory
define('LOANARMORY_NO_ITEM_PICKED', 'No item picked.');
define('LOANARMORY_BACK_TO_ARMORY', 'Back to armory');
define('LOANARMORY_LOAN_AWAKE_PILL',
    'So you want to loan a Awake пилюля? What would you do if its использоватьd and how можешь it be returned?');
define('LOANARMORY_LOAN_HEALTH_PILL',
    'So you want to loan a Health пилюля? What would you do if its использоватьd and how можешь it be returned?');
define('LOANARMORY_LOAN_GP_PILL',
    'So you want to loan a Guard Protection? What would you do if its использоватьd and how можешь it be returned?');
define('LOANARMORY_BACK_TO_CELL', 'Back to cell');
define('LOANARMORY_NO_ITEM_FOUND!', 'No such item found!');
define('LOANARMORY_NO_ITEM_FOUND_GANG!', 'Selected item not found in your regiment armory!');
define('LOANARMORY_NOT_IN_GANG!', 'That person is not a member of your Gang.');
define('LOANARMORY_RECEIVED_LOAN', 'Ты received a %s из - %s as loan');
define('LOANARMORY', 'Loan Armory');
define('LOANARMORY_GIVEN_LOAN', 'Ты have loaned a %s to your gang member %s!');
define('LOANARMORY_TO_MEMBER', 'Loan An Item To The Gang Member');
define('LOANARMORY_SELECT_MEMBER', 'Select a gang member to loan a %s.');
define('LOANARMORY_LOAN_RESTORE_ITEM', 'Loan/Restore a %s');
define('GANG_POSITION', 'Gang Position');

//login
define('ERROR_MESSAGE', 'Error Message');
define('LOGIN_ERROR_SECURITY_CODE', 'Sorry, you did not enter the security code correctly.');
define('LOGIN_FORGOTTEN_PWD', 'Forgotten your password?');
define('LOGIN_CLICK_HERE', 'Please click %sHere%s to reset it');
define('LOGIN_USERNAME_PWD_INVALID', 'Sorry, your использоватьrname and password combination are invalid.');
define('LOGIN_ACT_NOT_VALIDATED', 'Тыr account isn\'t validated, please check your email.');

//lottery
define('LOTTERY_BOUGHT_MAX', 'Ты have already bought 5 tickets today.');
define('LOTTERY_NOT_ENOUGH_MONEY', 'Ты do not have enough деньги to buy any tickets.');
define('LOTTERY_BOUGHT_TICKET', 'Ты have bought a lottery ticket.');
define('LOTTERY_HEAD', 'Daily Lottery');
define('LOTTERY_MSG',
    'Do you want to buy a ticket for the daily lottery? Ты можешь buy up to 5 tickets a day for $1000 a ticket. The more people that enter, the more that the winner will win. If your ticket is drawn at the end of the day, you win 90% of the ticket revenue!');
define('LOTTERY_BUY_TICKET', 'Buy Ticket');
define('LOTTERY_BOUGHT_TODAY', 'There have been %s Lotto Tickets bought today.');
define('LOTTERY_CURRENTLY_WORTH', 'Lotto is currently worth %s.');
define('LOTTERY_YOU_BOUGHT_TODAY', 'Ты have bought %s Lotto Ticket(s) today.');
define('LOTTERY_WINNERS', 'Lottery Winners - Last 10');
define('LOTTERY_PLAYER', 'Player');
define('LOTTERY_AMOUNT', 'Amount');

//managegang
define('GANG_CANT_KICK_YOURSELF', 'Ты можешьnot kick yourself out of the gang.');
define('GANG_CANT_KICK_LEADER', 'Ты можешьnot kick the gang leader out of the gang.');
define('GANG_USER_KICKED', 'Ты have kicked %s out of the gang.');
define('GANG_MANAGE_MEMBERS_HEAD', 'Manage Members');
define('GANG_HAS_NO_MEMBERS', 'Тыr gang currently has no members.');
define('GANG_MEMBER', 'Member');
define('GANG_KICK', 'Kick');

//manageR
define('MANAGER_POINTS_CREDITED', 'Ты have been credited 15 points for referring %s. Keep up the good work!');
define('MANAGER_REFERRAL', 'Referral');
define('MANAGER_RECEIVED_NO_POINTS',
    'Unfortunately you have received no points for referring %s. This could be a result of many different things, such as you abusing the referral system, or the player you referred only signing up, but never actually playing.');
define('MANAGER_DENIED', 'Ты have denied the referral.');
define('MANAGER_ACCEPTED', 'Ты have accepted the referral.');
define('MANAGER_MANAGE_REFERRALS', 'Manage Referrals');
define('MANAGER_RULES_SUPER_MODS', 'Rules to Super Mods');
define('MANAGER_RULES_SUPER_MODS_1', 'Only credit/deny after 4 days of register.');
define('MANAGER_RULES_SUPER_MODS_2',
    'CHECK THE IP\'s of both players (and if there are other refs of same players below look at those too).');
define('MANAGER_RULES_SUPER_MODS_3', 'Total stats of new player at least 40.');
define('MANAGER_RULES_SUPER_MODS_4', '# of logins of new player at least 2.');
define('MANAGER_RULES_SUPER_MODS_5', 'In case of doubt check email to see if isn\'t strange');
define('MANAGER_MIN_CONDITIONS',
    'Don\'t discuss with other moderators or normal использоватьrs the minimum conditions to have their referral credit');
define('MANAGER_NEW_GUY', 'Новый Guy');
define('MANAGER_REFERRED', 'referred');
define('MANAGER_OLD_GUY', 'Old Guy');
define('MANAGER_DECISION', 'Decision');
define('MANAGER_CREDIT', 'Credit');
define('MANAGER_DENY', 'Deny');
define('MANAGER_USER_OR_REFERRER_NOT_EXISTS', 'The использоватьr %s or referrer %s do not exist anymore !');

//market
define('MARKET', 'Market');

//massmail
define('MASSMAIL_ANNOUNCEMENT', 'Announcement');
define('MASSMAIL_SEND_ANNOUNCEMENT', 'Here you можешь send add an announcement.');
define('MASSMAIL_NEW_MESSAGE', 'Новый Message');

//megaslots
define('MEGASLOTS_NOT_MORE_TURNS', 'Ты don\'t have more turns to play slots.');
define('MEGASLOTS_BROKE_MACHINE',
    'Тыr slot machine will not be repaired until %s. Ты committed 30 offenses today and broke your slot machine handle clear off!');
define('MEGASLOTS_BROKE_MACHINE_HANDLE',
    'Ты went so fast, that you broke the handle off of the slot machine. The repair guy will install the new handle in %s minutes. Ты committed 30 offenses today and broke your slot machine handle clear off!');
define('MEGASLOTS_ANTICHEAT_GATEWAY',
    'Anticheat gateway message: Please slow down so you don\'t break the handle off your slot machine. Do not hyperclick the level link, press  F5 or использовать bots. Those are against game rules.');
define('MEGASLOTS_NOT_ENOUGH_MONEY', 'Ты don\'t have enough деньги to play Mega Slots. (Ты need $10,000).');
define('MEGASLOTS_LUCKY_DRAW',
    'Ты broke even. Just for that very lucky draw, we will bonus you $5000 bucks, for a total of $15k!');
define('MEGASLOTS_LOST_BETS',
    'Ты lost 65% of your bets. That stinks, but here is an extra $2500 for your troubles, that equals $7500. Ты lost $2500.');
define('MEGASLOTS_WON_BETS',
    'Now that is lucky! Ты won 100% of the bets! Ты get a $100K bonus for a grand total of $120k! Don\'t spend it all in one place!');
define('MEGASLOTS_LOST_BETS_1', 'Ты came out with $%s and it could be worse! Ты lost $%s.');
define('MEGASLOTS_WON_BETS_1', 'Ты came out with $%s and that is pretty dang good! Ты won $%s extra!');
define('MEGASLOTS_DONT_HAVE_PULLS',
    'Ты don\'t have enough pulls to play Mega Slots. Thank you for playing "Mega Slots"!');
define('MEGASLOTS_RESULTS', 'Mega Slots Results');
define('MEGASLOTS_MACHINE', 'Slot Machine');
define('MEGASLOTS_TURNS_LEFT', '%s turns left');
define('MEGASLOTS_MSG_1',
    'So, you fancy a try at Mega Slots? It just $10,000 a pull, so have at it. Counts as 100 slot points.');
define('MEGASLOTS_MSG_2', 'Новый hidden bonиспользоватьs! Odds in winning a hidden bonus are 1:5000, but worth it!');
define('MEGASLOTS_PULL_LEVER', 'Pull Lever');
define('MEGASLOTS_BACK_TO_REGULAR_SLOTS', 'Back to regular slots');
define('MEGASLOTS_NOT_BOT',
    'Ты may <strong>not</strong> speedclick, использовать bots, or the F5 key. Please play fair.');

//modRules
define('MODRULES', 'Moderating Rules');
define('MODRULES_GENERAL_RULES', 'Новый General Rules');
define('MODRULES_RULES_1', 'Please read these thoroughly, any questions? Please ask.');
define('MODRULES_RULES_1_1',
    '<b>Чат</b> - to delete click the [D] on the left hand side of the line.  The warn/ban system is available via the Ban a Player link at the bottom of the chat screen.  Enter their ID to view their history or to add a warn ban.  Please ensure you add a note for Admin and a clear note for the player as this will appear in their event.  Both sides must be completed before issuing the warning or ban.');
define('MODRULES_RULES_1_2',
    '<b>Shoutbox</b> ? to delete click the ?delete? on the shout itself.  The warn/ban system is basically the same as chat, but можешь be found by the ?ban? button on the shout itself.  If there are repeated posts (swearing for example) place the ban on the worst shout as this is the one that will be logged with the ban for Admin to view.');
define('MODRULES_RULES_1_3',
    'If you need to check someone?s Shout history and they do not have a shout available to click on (they want to know when their ban ends) click any ?ban? button on the Shoutbox and change the first number in the address to their ID, this will display their history.');
define('MODRULES_RULES_2',
    'If you do need to apply a warning or a ban, please check their history and increase on it.  So it should read 1 warn, 2 day ban, 3 day ban and so on.  If they have one warn and it was months ago you можешь apply a second warning.  However, if they have bans, you must increase on it.');
define('MODRULES_RULES_2_1',
    'If they receive too many bans, this можешь be put in a support ticket for review for a game ban.');
define('MODRULES_RULES_2_2',
    'There is a warn button on the Suggestions and Support section, this sends an event to the player, provide the reason for the warning to the player either in the support ticket, suggestion or via pmail if it is someone else?s suggestion.  If the player continues please open a support ticket.');

define('MODRULES_FORUMS_ARE_DIFFERENT', 'Forums are different, each one needs the following:');
define('MODRULES_FORUMS_ARE_DIFFERENT_1',
    'Add Infraction (the red ?football referee style? button) - these keep a log of the rule breaking, if they have too many raise a support ticket to request a ban. Remember to add notes for Admin.  The player section gets emailed to them.');
define('MODRULES_FORUMS_ARE_DIFFERENT_2',
    'To delete, tick the box on the post you want to delete and scroll to the bottom of the page and select ?delete? из the drop down menu, then click GO.  When deleting, you get two options, ?Soft? delete which leaves the subject line and a message giving your reason for deletion. And physically remove, which should only be использоватьd if the subject line also needs to be erased. If someone has spammed loads, go to their profile, and then view all posts, you можешь tick each one and delete in one go.');

//define('MODRULES_WARN_FOR_RULE_BREAK', 'Заключенныйers to be given 1 warn for rule breaking, if a warning is already registered, a ban is to be given, starting with a 2-day ban and increasing each time (3, 4, 5 etc), if they continue to break the rules.  If person offends excessively on a regular basis, open a Support Ticket to request a game ban.');

define('MODRULES_REMEMBER_1',
    'Remember: If you ban in the Shoutbox or chat, check the other one as the offender may do the same there after receiving the warning or ban. If it is a bad case they may also do the same in Ad?s or Suggestions, they rarely touch the forums, but worth a check.');
define('MODRULES_FONT_TOO_BIG', 'Font Too Big/Long/Wide');
define('MODRULES_POST_TOO_WIDE',
    'If the post is too wide, delete it or edit it as necessary, as it stretches the window out of proportion.  Ты можешь then edit on the shoutbox so you are not suffering as you можешь still see it.');
define('MODRULES_OVERSIZED_SHOUT',
    'Oversized shout due to large font or excessive gaps etc.  Use common sense as to what is too big for the content.  A long post of normal sized text is fine if they haven\'t written an essay, but a same sized post with size 60 font for one word is not.  Again, you можешь использовать edit to adjust the font size or spacing.');
//define('MODRULES_BREAKING_RULE_WARN', 'There is no need to использовать edit apart из that.  If something is breaking a rule, warn/ban.');

define('MODRULES_REASONS_TO_WARN', 'Reasons to warn/ban');
define('MODRULES_SPAMMING', 'Spamming');
define('MODRULES_POSTING_SAME_POST', 'Posting the same post over and over');

define('MODRULES_PAGE_ACCEPTABLE',
    'Every 30 shouts is acceptable for same/similar posts.  If they post too soon, post a warning to them on the Shoutbox or pmail them (or both if you have time).  If they do it again, then you можешь go to the ban screen and send a warning or ban as necessary. <b>A \'verbal\' warning must be given before a system warning or ban</b> unless they are spamming on purpose of course.');

define('MODRULES_FLOODING', 'Flooding');
define('MODRULES_POSTING_DIFFERENT_THINGS',
    'Posting different things over and over, so there are multiple posts one after the other');

define('MODRULES_POSTING_DIFFERENT_THINGS_1',
    'Players are allowed to использовать the Shoutbox for chat, it was primarily implemented for this reason.  Therefore chatters можешь post every other post as long as it is not \'spam\'.  Double posters are to be encouraged to edit to combine their posts.  If they are \'spamming\' please see the above section.');

define('MODRULES_ADVERTISING', 'Advertising');
define('MODRULES_ADVERTISING_1', 'Advertising another browser based game, via direct link or discussion');
define('MODRULES_ADVERTISING_2',
    'If unsure, delete and google it, then ban them, if there is no time, ban them. This можешь be checked later and the ban lifted by Admin.  Warnings are not to be given, just bans.');

define('MODRULES_SWEARING', 'Swearing/Inappropriate Language');
define('MODRULES_SWEARING_1', 'Swearing is to be treated in context.<br>
If they say ?oh shit? for example, delete and give a verbal warning, if they do it again the same day send a warning or ban as necessary for their account history.<br>
If they say ?f you? as a personal attack on someone then go straight to the ban screen and sending a warning or ban as necessary. <br>
This includes any variation in the spelling or starred out letters, of example azz instead of ass. Or b!tch or b*tch instead of bitch. <br> <br>
Inappropriate language covers things such as racism, sexual content, and homophobic comments, religious and political discussions.  Delete it.  And verbally warn them, if they continue warn/ban.<br>
    ');

define('MODRULES_OTHER_LANGUAGES', 'Other languages');
define('MODRULES_OTHER_LANGUAGES_1',
    'Please remind them that it is English only on the shoutbox, chat and forums, except for the \'other languages\' section.');
define('MODRULES_COMPLAIN_ABOUT_WARN',
    'If someone wishes to complain about a warning or ban that you or another mod has given, please direct them to the Support Centre.');

define('MODRULES_DISRESPECT', 'Disrespecting Mods');
define('MODRULES_DISRESPECT_1',
    'If a player has been continuously disrespectful to you and you have issued them with verbal warnings to использовать the Support Centre and they still continue, you may issue them with a WARNING for Disrespecting Mods, and if they continue to be disrespectful you may ban them for 2 days for this reason.  Please then submit your report to the Support Centre regarding this.  Please give them ample opportunity to использовать the Support Centre for their complaint etc before warning and banning via the system.');

define('MODRULES_VERBAL_WARN',
    'In all cases if you verbally warn ?no spamming? for example that warning goes to all on chat/shout (depending on where posted) if that warning is still visible then there is no need to verbally warn again for that action.  Please использовать common sense with this.');

define('MODRULES_SUGGESTIONS', 'Suggestions');
define('MODRULES_SUGGESTIONS_1',
    'If you come across a new suggestion that has already been suggested (e.g. we should be able to sell coke and steroids), please leave a note to say CLOSED and the reason, before closing it so the player understands why it was closed.');

define('MODRULES_SUPPORT_CENTRE', 'Support Centre');
define('MODRULES_SUPPORT_CENTRE_1',
    'If anyone has a problem that needs to be escalated to Admin/Coders, ask them to open a support ticket.');
define('MODRULES_SUPPORT_CENTRE_1_1',
    'Please read closed tickets when you have time, it is full of использоватьful information and answers to regularly asked questions.');
define('MODRULES_SUPPORT_CENTRE_2',
    'If you are unsure of the correct response, please leave it for someone else to deal. If it is a complaint or a fault in the game or something that needs to be raised to a coder/admin, please bump it to level 2. Please ensure you leave them open if they need investigating.');
define('MODRULES_SUPPORT_CENTRE_2_1',
    'Please also ensure that player ID?s are included if they are talking about another player(s).');

define('MODRULES_DEALING_WITH_SUPPORT', 'Dealing with Support');
define('MODRULES_BANS', 'Bans');
define('MODRULES_BANS_1', 'If someone mails asking why someone was banned reply with something like this');
define('MODRULES_BANS_2',
    '&quot;Bans можешь only be discussed between account holder and admin via email.  Banned account holders need to email prisonstruggle@gmail.com&quot;');
define('MODRULES_BANS_3',
    'If the player has created a new account and has contacted us in game you можешь reply the same as above but also offer them the option to start over on the new account as a second chance, but they must read the rules and follow them.');

define('MODRULES_DONATIONS', 'Donations');
define('MODRULES_DONATIONS_1',
    'If a player has not received the pack they have donated for, please issue them with a response similar to this.');
define('MODRULES_DONATIONS_2',
    '&quot;If you made the purchase via PayPal please forward the receipt to \'prisonstruggle@gmail.com\', please put \'PAYPAL\' in the subject line for a quicker response.  The usual reason for delay is the payment hasn\'t cleared on your PayPal account yet, you можешь check this in the PayPal account.');
define('MODRULES_DONATIONS_3', 'If you made the payment via DAOPAY please contact them directly');
define('MODRULES_DONATIONS_4',
    'Please accept our sincere apologies for any inconvenience this may caиспользовать.&quot;');
define('MODRULES_DONATIONS_5',
    'Please ensure you ask them to put PAYPAL in the subject line so Admin можешь deal with these as a priority.');
define('MODRULES_SCAMMERS', 'Scammers');
define('MODRULES_SCAMMERS_1',
    'If it is an isolated case, pmail the scammer with a warning to return what he took safely or complete the deal.  Advise him of the rules and warn him he will be banned if he does not.  If it is more complicated, raise the level of support.');
define('MODRULES_COMPLAINTS', 'Complaints');
define('MODRULES_COMPLAINTS_1',
    'If a complaint is made against a member of staff, please raise the support level.  Do <b>not</b> close unless they player is satisfied with the explanation of their warning or ban.  Admin will analyze the complaint and advise as necessary.');

define('MODRULES_FREEZING', 'Freezing');
define('MODRULES_FREEZING_1',
    'This is only to be использоватьd under exceptional circumstances and only if a Smod or Admin is not available, such as:');
define('MODRULES_FREEZING_1_1', 'Serial scammers that continue to scam despite warnings of a game ban.');
define('MODRULES_FREEZING_1_2', 'Noobs less than an hour old that purposely registered to spam advertisements.');
define('MODRULES_FREEZING_1_3',
    'Players that можешьnot be handled with chat/shout ban, if they are breaking the rules in other areas such as support, suggestions, ad?s and refиспользовать to stop.');
define('MODRULES_FREEZING_2',
    'If it is a week day a 24 hour freeze should suffice, if it is a weekend (Admin do not usually work at weekends) increase the length to cover until Monday to ensure coverage until Admin return (24-72 hours).  IF you использовать this feature, you MUST open a support ticket regarding this and what needs investigating.  This is not to be использоватьd in place of usual warn/ban system.  All Freezing is recorded in the logs, please do not abиспользовать this feature or it will be removed.');

//multiMods
define('MULTIMODS_SURE_TO_MARK_SEEN', 'Ты sure you want to mark this log as seen?');
define('MULTIMODS_SURE_TO_REPORT', 'Ты sure you want to report them?');
define('MULTIMODS_VERIFICATION_TOOL', 'Multi Verification Tool');
define('MULTIMODS_HOME_PAGE', 'Go To Multi Home Page');
define('MULTIMODS_READ_FIRST', 'Read First');
define('MULTIMODS_NORMAL_MULTI', 'Normal multi case');
define('MULTIMODS_SENDER_BELOW_LEVEL', 'Отправитьer its below level 5');
define('MULTIMODS_SENDER_BELOW_STATS', 'Отправитьer its below 1000 stats');
define('MULTIMODS_SENDER_NEVER_DONATED', 'Отправитьer never donated');
define('MULTIMODS_SEND_BIGGER', 'number of things send bigger than 10');
define('MULTIMODS_SENDING_POINTS', 'Отправитьing 23 points можешь be a sign of multi.');
define('MULTIMODS_MARK_AS_SEEN', 'Mark as Seen');
define('MULTIMODS_MARK_ALL_SEEN', 'Mark all movement between this to id\'s as seen');
define('MULTIMODS_NEW_MOVEMENTS_APPEAR_FUTURE', 'new movements will appear on future logs');
define('MULTIMODS_REPORT', 'Report');
define('MULTIMODS_ALL_REPORTED', 'All logs between this two ids are reported to the admin');
define('MULTIMODS_CHECK_HISTORY', 'Check History');
define('MULTIMODS_CHECK_ALL_MARKET_SEEN', 'Check all logs that were not market as seen');
define('MULTIMODS_NUMBER_OF_SENTS', 'The number of sents, total points, etc - Are since the beggining of game');
define('MULTIMODS_SHOWS_MARKED_UNMARKED', 'shows the marked as seen and the unmarked');
define('MULTIMODS_IP_Time', 'IP/Time');
define('MULTIMODS_SENDER', 'Отправитьer');
define('MULTIMODS_RECEIVER', 'Receiver');
define('MULTIMODS_SEND', 'Отправить');
define('MULTIMODS_IP_NO_PERMISSION', 'IP: no permission to see');
define('MULTIMODS_TOTAL_STATS', 'Total stats');
define('MULTIMODS_DONATED', 'Donated');
define('MULTIMODS_NUMBER_SENTS', 'Number of Sents');
define('MULTIMODS_TOTAL_POINTS_SENT', 'Total Points Sent');
define('MULTIMODS_TOTAL_MONEY_SENT', 'Total Money Sent');

//my5050log
define('MY5050LOG_NO_LOG', 'No 50/50 log entries found.');
define('MY5050LOG_HEAD', '50/50 logs categories');
define('MY5050LOG_SUBMENU_MONEY', 'Money games');
define('MY5050LOG_SUBMENU_POINTS', 'Points games');
define('MY5050LOG_CONTENT_HEAD', '50/50 %s statistics');
define('MY5050LOG_SHOW_STATS_TODAY',
    'Ты можешь show your stats today if you want. Click <a href="%s">here</a> to show them now.');
define('MY5050LOG_NOT_ENOUGH_ENTRIES',
    'Ты do not have enough entries (minimum: %d) to show your stats at the moment. Feel free to come back when you have played more.');
define('MY5050LOG_ALREADY_REVIEWED',
    'Ты already have reviewed your stats or spied an inmate today. Feel free to come back tomorrow');
define('MY5050LOG_TOTAL_GAMES_PLAYED', 'Total %s Games Played');
define('MY5050LOG_TOTAL_GAMES_WON', 'Total %s Games Won');
define('MY5050LOG_NOT_ENOUGH_MONEY_TO_SPY', 'Ты don\'t have enough деньги to spy');
define('MY5050LOG_CAN_ONLY_SPY_INMATES', 'Ты можешь only spy inmates with at least %d entries in 50/50 logs');
define('MY5050LOG_SPY_BY', 'Spy %s games');
define('MY5050LOG_RESET_NOTE', 'PS: The daily possible check for 50/50 stats is reset at midnight, server time.');
define('MY5050LOG_COSTS', 'It costs $%s per target использоватьr\'s level');
define('MY5050LOG_SUCCESS_SPY', 'Ты have successfully spied %s for $%s');

//myattlog
define('MYATTLOG_NO_LOG', 'No attack log entries found.');
define('MYATTLOG_HEAD', 'Personal Атака Logs');

//mymugglog
define('MYMUGLOG_NO_LOG', 'No mug log entries found.');
define('MYMUGLOG_HEAD', 'Personal Mug Logs');
define('MYMUGLOG_NOTICE',
    'Personal mug logs only last 10 days, so you will never see entries из mugs older than 10 days.');

//mytransactionlog
define('MTL_SENDER', 'Отправитьer');
define('MTL_RECIPIENT', 'Recipient');
define('MTL_TYPE', 'Type');
define('MTL_AMOUNT', 'Amount');
define('MTL_NO_LOG', 'No transaction log entries found.');
define('MTL_HEAD', 'Personal Transaction Logs');
define('MTL_NOTE',
    'PS: All data is taken из logs, so transactions will not take into account actions older than 120 hours. ');

//online
define('ONLINE_USERS', 'Users Online In The Last 15 Minutes');

//peeps
define('PEEPS_PARANOID_ONE',
    'Don\'t worry, oh paranoid one... you have not blocked yourself. Need to see the prison counselor?');
define('PEEPS_DID_NOT_BLOCK', 'Ты did not block this использоватьr.');
define('PEEPS_UNBLOCKED_USER', 'Ты just unblocked использоватьr - %s');
define('PEEPS_MASS_MAIL_SENT', 'Тыr mass mail was successfully sent !');
define('FRIENDS', 'Friends');
define('PEEPS_MASS_MAIL_FRIENDS_TEXT', 'Here you можешь send a mass mail to your friends.');
define('PEEPS_FRIENDS_MASS_MAIL', 'Friends mass mail');
define('ENEMIES', 'Enemies');
define('PEEPS_MASS_MAIL_ENEMIES_TEXT', 'Here you можешь send a mass mail to your enemies.');
define('PEEPS_ENEMIES_MASS_MAIL', 'Enemies mass mail');
define('PEEPS_FRIENDS_LIST', 'Тыr Friends List');
define('PEEPS_ENEMY_LIST', 'Тыr Enemy List');
define('MAIL_COMPOSE', 'Compose');
define('MAIL_INBOX', 'Inbox');
define('MAIL_GANG_INBOX', 'Gang Mass Mail Inbox');
define('MAIL_SAVED_BOX', 'Saved Box');
define('MAIL_PURGE_INBOX', 'Purge Inbox');
define('MAIL_VIEW_ENEMIES', 'View Enemies');
define('MAIL_VIEW_FRIENDS', 'View Friends');
define('MAIL_VIEW_BLOCKED', 'View Blocked');
define('MAIL_ABUSE_LOG', 'Abиспользовать Log');
define('MAIL_SEND_PMAIL', 'Отправить PMail');

//pharmacy
define('PHARMACY_NOT_ALLOWED_TO_BUY_HEALTH_PILLS', 'Ты aren\'t allowed to buy Health Pills.');
define('PHARMACY_NOT_PICK_REAL_DRUG', 'Ты didn\'t pick a real drug.');
define('PHARMACY_NOT_PURCHASED_DRUG', 'Ты have purchased some %s.');
define('PHARMACY', 'Pharmacy');
define('PHARMACY_MSG_1',
    'How may I help you? We offer quite a bit of medical supplies here for all your medical needs. I am of course assuming that these drugs won\'t be abиспользоватьd...');
define('PHARMACY_CANT_ORDER_COCKTAIL', 'Ты можешьnot order another cocktail while you are under its effects !');
define('PHARMACY_COCKTAIL_ORDERED', 'Тыr Steroid Cocktail order was successfully completed.');
define('PHARMACY_WRONG_STRENGTH', 'Please enter a non zero whole number for strength.');
define('PHARMACY_WRONG_DEFENSE', 'Please enter a non zero whole number for defense.');
define('PHARMACY_WRONG_SPEED', 'Please enter a non zero whole number for speed.');
define('PHARMACY_ENTER_AT_LEAST_ONE', 'At least one of strength, defense and speed should be non zero whole number.');
define('PHARMACY_ORDER_STEROID_COCKTAIL', 'Order a Steroid Cocktail');
define('PHARMACY_CHOOSE_STAT',
    'Please choose your wanted amount of strength, defense and speed. The amount will NOT count towards weapon / armor bonus.');
define('PHARMACY_COCKTAIL_LAST_IN_HOUR', 'The cocktail will last for 60 minutes.');
define('PHARMACY_COCKTAIL_MSG_1', 'The total price will show on the bottom.');

//pms
define('PMS_ALL_MAIL_DELETED', 'All mails in Inbox deleted successfully!');
define('PMS_ALL_READ_MAIL_DELETED', 'All read mails in Inbox deleted successfully!');
define('PMS_INVALID_MAIL_ID', 'Invalid mail id!');
define('PMS_MAIL_DELETED', 'Mail deleted successfully!');
define('PMS_MAIL_SAVED', 'Mail saved successfully!');
define('PMS_MESSAGE_NOT_EXISTS', 'This message no longer exists.');
define('PMS_CANT_FIND_SENDER', 'Cannot find the sender. Perhaps the sender got deleted or banned.');
define('PMS_NOT_CHOOSE_RECIPIENT', 'Ты did not choose a recipient.');
define('PMS_NOT_PMAIL_YOURSELF', 'Ты можешьnot send yourself a pmail, you silly goose!');
define('PMS_WAIT_SOME_SECS', 'Ты have to wait some seconds to be able to mail again...');
define('PMS_NOT_TYPE_SUBJECT', 'Ты did not type a subject.');
define('PMS_NOT_TYPE_MESSAGE', 'Ты did not type a message.');
define('PMS_SUCCESSFULLY_SENT', 'Message successfully sent to %s');
define('PMS_ID_NOT_EXIST', 'I am sorry but the Id you specified does not exist...');
define('PMS_NOT_SPECIFY_POST', 'Ты did not specify a post.');
define('PMS_YOUR_REPORT_SENT', 'Thank you, your report was sent. Please do not resend.');
define('PMS_INVALID_USER_ID', 'Invalid использоватьr id!');
define('PMS_SENDER_UNBLOCKED', 'Отправитьer unblocked successfully!');
define('PMS_WHY_TRYING_BLOCK_YOURSELF', 'Why are you trying to block yourself? Need to see the prison counselor?');
define('PMS_ALREADY_BLOCKED_USER', 'Ты already blocked this использоватьr.');
define('PMS_USER_NOT_EXIST', 'User does not exist.');
define('PMS_SENDER_BLOCKED', 'Отправитьer blocked successfully!');
define('PMS_SELECTED_MAILS_DELETED', 'Selected mails deleted successfully!');
define('PMS_SELECTED_MAILS_SAVED', 'Selected mails saved successfully!');
define('PMS_SELECT_DESELECT_ALL', 'Select/Deselect All');
define('PMS_SURE_TO_DELETE_THIS_MAIL', 'Are you sure? Do you want to delete this mail!');
define('PMS_SURE_TO_SAVE_THIS_MAIL', 'Are you sure? Do you want to save this mail!');
define('PMS_DELETE_SELECTED', 'Delete Selected');
define('PMS_SAVE_SELECTED', 'Save Selected');
define('PMS_MUST_FILL_OUT_SUBJECT', 'Ты must fill out a subject, silly buns!');
define('PMS_CANT_SEND_BLANK_MSG', 'Ты можешьnot send a blank message. Duh.');
define('PMS_NOT_CHOSEN_RECIPIENT', 'Ты have not chosen a recipient!');
define('PMS_ARE_YOU_BOT', 'Uh, are you a bot?');
define('PMS_SELECT_ATLEAST_ONE_PMAIL', 'Uh, Select atleast one PMail!');
define('PMS_SURE_TO_ACTION_SELECTED', 'Are you sure? Do you want to %s selected items!');
define('PMS_INBOX_HEAD', 'Pmail Inbox (Inbox cleaned every 7 days!)');
define('PMS_FILTER_ALL', 'From All');
define('PMS_FILTER_FRIENDS', 'From Friends');
define('PMS_FILTER_ENEMIES', 'From Enemies');
define('PMS_NO_MESSAGE', 'Ты have no messages at this time.');
define('PMS_CHOOSE_ENEMY', 'Choose an Enemy');
define('PMS_CHOOSE_FRIEND', 'Choose a Friend');
define('PMS_NO_ENEMIES', 'No Enemies');
define('PMS_NO_FRIENDS', 'No Friends');
define('PMS_SEND_TO', 'Отправить to');
define('PMS_SEND_MESSAGE', 'Отправить Message');
define('PMS_DEL', 'Del');
define('PMS_SAV', 'Sav');
define('PMS_VERIFY_PURGING', 'Verify the purging of your inbox!');
define('PMS_PURGE_ALL', 'Delete all mails in Inbox?');
define('PMS_PURGE_ALL_READ', 'Delete only read mails in Inbox?');
define('PMS_SURE_TO_PURGE_ALL', 'Are you sure? This will delete ALL pmails in your inbox!');
define('PMS_SURE_TO_PURGE_ALL_READ', 'Are you sure? This will delete all READ pmails in your inbox!');
define('PMS_READ_PMAIL', 'Read Pmail');
define('PMS_SENDER', 'Отправитьer');
define('PMS_PREVIOUS_MESSAGE', 'Previous message');
define('PMS_NEXT_MESSAGE', 'Next message');
define('PMS_REPLY', 'Reply');
define('PMS_SURE_REPORT_ABUSE', 'Are you sure? Do you want to report abиспользовать this mail!');
define('PMS_REPORT_ABUSE', 'Report Abиспользовать');
define('PMS_SURE_UNBLOCK_SENDER', 'Are you sure? Do you want to unblock sender!');
define('PMS_SURE_BLOCK_SENDER', 'Are you sure? Do you want to block sender!');
define('PMS_UNBLOCK_SENDER', 'Unblock Отправитьer');
define('PMS_BLOCK_SENDER', 'Block Отправитьer');
define('PMS_REPLY_TO', 'Reply to');
define('PMS_IN_REPLY_TO', 'In reply to');
define('PMS_PREVIOUS_MESSAGE_BELOW', 'Previous message below');
define('PMS_REPORT_INAPPROPRIATE_PMAIL', 'Report Inappropriate Pmail');
define('PMS_DESCRIBE_PROBLEM', 'Please describe the problem');
define('PMS_SEND_REPORT', 'Отправить Report to Admin!');
define('PMS_ADMIN_AUTO_RESPONDER', 'Admin Auto-responder');

//point lottery
define('PLOTTERY_BOUGHT_MAX', 'Ты have already bought 5 tickets today.');
define('PLOTTERY_NOT_ENOUGH_POINTS', 'Ты don\'t have enough points to buy any tickets.');
define('PLOTTERY_BOUGHT', 'Ты have bought a lottery ticket.');
define('PLOTTERY_HEAD', 'Daily Points Lottery');
define('PLOTTERY_MSG',
    'Do you want to buy a ticket for the daily points lottery? Ты можешь buy up to 5 tickets a day for 5 points each. The more people that enter, the more that the winner will win. If your ticket is drawn at the end of the day, you win 90% of the ticket revenue!');
define('PLOTTERY_BUY_TICKET', 'Buy Ticket');
define('PLOTTERY_BOUGHT_TODAY', 'There have been %s Points Lottery Tickets bought today.');
define('PLOTTERY_WORTH', 'Lotto is currently worth %s points');
define('PLOTTERY_YOU_BOUGHT', 'Ты have bought %s Points Lottery    Ticket(s) today.');
define('PLOTTERY_WINNERS', 'Points Lottery Winners - Last 10');

//preferences
define('PREF_SAVED', 'Тыr preferences have been saved.');
define('PREF_NAME_TOO_SMALL', 'That name is too small.');
define('PREF_NAME_TOO_LONG', 'That name is too long. Limit is 15 characters.');
define('PREF_NOT_ENOUGH_MOENY', 'Ты don\'t have enough деньги to change name.');
define('PREF_NAME_EXISTS', 'That name already exists...');
define('PREF_NAME_CHANGED', 'Тыr name have been changed.');
define('PREF_ACCOUNT', 'Account Preferences');
define('PREF_AVATAR_LOC', 'Avatar Image Location');
define('PREF_QUOTE', 'Quote');
define('PREF_PROFILE_SIGNATURE', 'Profile Signature');
define('PREF_PREVIEW', 'Preview');
define('PREF_YOU_CAN_USE', 'Ты можешь использовать');
define('PREF_BBCODE', 'Big ЗаключенныйStruggle BBCode');
define('PREF_CHARACTERS_ONLY', 'a-Z, 0-9 Characters Only');
define('PREF_CHANGE_NAME_COST', 'It will cost you $100,000 to change name');
define('PREF_CHANGE_PASSWORD', 'Change Password');
define('PREF_CLICK_TO_CHANGE_PASSWORD', 'Click here to change password');
define('PREF_CUSTOMIZE_LEFT_MENU', 'Customize Left Menu');
define('PREF_LINK', 'Link');
define('PREF_SAVE', 'Save Preferences');

//prisonBBCode
define('PBBCODE', 'ЗаключенныйStruggle BBCode');
define('PBBCODE_SMALL', 'Small ЗаключенныйStruggle BBCode');
define('PBBCODE_BIG', 'Big ЗаключенныйStruggle BBCode');
define('PBBCODE_BOLD_TEXT', 'Bold text');
define('PBBCODE_ITALIC_TEXT', 'Italic text');
define('PBBCODE_UNDERLINEX_TEXT', 'Underlinex text');
define('PBBCODE_RED_TEXT', 'Red text');
define('PBBCODE_20_POINT_TEXT', '20-point text');
define('PBBCODE_RED_BG_TEXT', 'Text with a red background');
define('PBBCODE_LINE_THROUGH_TEXT', 'Text with a line through it');
define('PBBCODE_CENTERED_TEXT', 'Centered text');
define('PBBCODE_SAME_AS_SMALL', 'Same as small prisonstruggle BBCode, and more 3 tags');
define('PBBCODE_EMAIL_ME', 'Email me!');
define('PBBCODE_A_LINK', 'A link');
define('PBBCODE_ANOTHER_LINK', 'Another link');

//prize
define('PRIZE_CHECK', 'Prizes - Check <a href="stafftop.php">Staff/Top    Inmates</a>');
define('PRIZE_CONGRATS', 'Congrats to Overlord that won the 1st PlayStation');
define('PRIZE_PLAY_STATIONS',
    'Заключенный Struggle decided to spice things up by giving away <span style="color: red">TWO Playstation3\'s</span>.');
define('PRIZE_TOP_DONATOR',
    'One Playstation will be given to the <span style="color: red">\'Top    Donator\'</span> of the game. Donations are key for helping this game which is free continue to be online. We feel its only fair that we можешь start giving back to the players who help us out so much - <span style="color: red">Contest ended !</span>');
define('PRIZE_SECOND_PLAYSTATION',
    'And the second Playstation will be given to the <span style="color: red">использоватьr who referrs most players</span>');
define('PRIZE_ALL_REFERRALS',
    'All referrals will be checked manually to cut down on multi-accounts and there will be certain requirements the использоватьr has to achieve for them to be classed as a valid referral.');
define('PRIZE_WAITING', 'So what are you waiting for?!');

//profiles

define('PROFILE_USER_NOT_EXIST', 'That использоватьr does not exist.');
define('PROFILE_BBROTHER_TEXT_1',
    'Ты tell Erik that the shady looking stranger has sent you to talk to him. He seems a little suspicious, but his extensive spying make him trust you, at least for now. He tells you he is planning a prison escape. However, to give you a chance of joining them, he is asking you to bring the shady looking stranger some <font color="red">dynamite</font>. He tells you that the simplest way to get <font color="red">dynamite</font> is to go to the store, and simply ask for it. Once you have it, you are supposed to bring it to the shady looking stranger, which will give you the next part of the plan.');
define('PROFILE_BBROTHER_TEXT_2', 'Ps: Once you try to escape and succeed, the following will happen');
define('PROFILE_BBROTHER_TEXT_3', 'Ты are level 1 again (1 experience point).');
define('PROFILE_BBROTHER_TEXT_4', 'Ты lose 85% of all your stats.');
define('PROFILE_BBROTHER_TEXT_5', 'Ты earn one security level.');
define('PROFILE_SEC_LVL_ADVANTAGES', 'Security levels grant you the following advantages');
define('PROFILE_SEC_LVL_ADVANTAGES_1',
    'Ты will have one star per security level next to your name, even if you have a special использоватьrname.');
define('PROFILE_SEC_LVL_ADVANTAGES_2', '+20% to all xp received per security level.');
define('PROFILE_SEC_LVL_ADVANTAGES_3', '+10% to all bonus to stats gained in training per security level.');
define('PROFILE_SEC_LVL_ADVANTAGES_4',
    '+3 security skill points that you можешь invest in some special skills on your preferences page.');
define('PROFILE_SEC_LVL_ADVANTAGES_5',
    'Access to new cell and item. Each security level opens up new opportunities for cells and items you можешь buy. However some of them may only be unlocked if you reach the level needed to discover the item (for example, if the item is held in a higher prison.');
define('PROFILE_MAKE_SURE_BEFORE_ESCAPE', 'So, make sure you really want to do it before going on with the escape.');
define('PROFILE_RATING_SAVED', 'Тыr rating has been saved.');
define('PROFILE', 'Profile');
define('PROFILE_TYPE', 'Type');
define('PROFILE_CURRENT_RATING', 'Current rating');
define('PROFILE_LIKE', 'I like this prisoner');
define('PROFILE_DISLIKE', 'I do not like this prisoner');
define('PROFILE_IN_SHOWER', 'This использоватьr is currently holding the soap in the showers');
define('PROFILE_IN_HOSPITAL', 'This использоватьr is in Hospital');
define('PROFILE_TALK', 'Talk with him');
define('PROFILE_MESSAGE', 'Message');
define('PROFILE_SEND_MONEY', 'Отправить Money');
define('PROFILE_SEND_POINTS', 'Отправить Points');
define('PROFILE_INVITE', 'Invite to Gang');
define('PROFILE_NOTES', 'Тыr Notes');
define('PROFILE_SIGNATURE', 'Profile Signature');
define('PROFILE_ADD_TO_ENEMIES', 'Add to enemies');
define('PROFILE_REMOVE_FROM_ENEMIES', 'Remove из enemies');
define('PROFILE_ADD_TO_FRIENDS', 'Add to friends');
define('PROFILE_REMOVE_FROM_FRIENDS', 'Remove из friends');
define('PROFILE_FAVORITE_QUOTE', 'Favorite Quote');

//putonmarket
define('PUTONMARKET_NO_ITEM', 'No item picked.');
define('PUTONMARKET_MAX_ITEM_ERR', 'Ты можешьnot put more than 5 items on the market.');
define('PUTONMARKET_WRONG_MONEY', 'Please enter a valid amount of деньги.');
define('PUTONMARKET_WRONG_ITEM_NUM', 'Please enter a valid number of items.');
define('PUTONMARKET_NOT_ENOUGH_ITEM', 'Ты do not have enough of those.');
define('PUTONMARKET_ITEM_ADDED', 'Ты have added %s %s to the market at a price of $%s each.');
define('PUTONMARKET_ITEM', 'Put An Item On The Market');
define('PUTONMARKET_SELLING_ITEM', 'Ты are selling %s');
define('PUTONMARKET_ITEM_NOS', 'Number of Items');
define('PUTONMARKET_COST', 'Cost');
define('PUTONMARKET_MAX_ITEM_TEXT',
    'maximum 5 items per использоватьr is allowed to be put in market, you already have %s items.');

//realestate
define('REALESTATE_WRONG_LAND_AREA', 'There is not that much land available.');
define('REALESTATE_WRONG_LAND_AMT', 'Please enter a valid amount of land.');
define('REALESTATE_LAND_PURCHASED', 'Ты have bought %s acres of land in %s for $%s.');
define('REALESTATE_LAND_SOLD', 'Ты have sold %s acres of land in %s for $%s');
define('REALESTATE_AGENCY', 'Real Estate Agency Of Generica');
define('REALESTATE_WELCOME',
    'Welcome to REAG! If we have any land left available, you можешь purchase it из here or sell it.');
define('REALESTATE_LAND_AVAILABLE', 'Land available из REAG in %s: %d acres');
define('REALESTATE_BUY_LAND', 'Buy Land At $%s Per Acre');
define('REALESTATE_SELL_LAND', 'Sell Land At $%s Per Acre');

//refer
define('REFER_EARN_POINTS', 'Refer To Earn Points');
define('REFER_LINK', 'Тыr Referer Link');
define('REFER_UPDATE',
    'UPDATE: Ты will recieve your points only after we filter out multis.    We do this manually now, to check account per account. This could take anywhere из 2 days until 7 days, becaиспользовать of this instead of 10 points    we are giving 15 points, but rest assured that you will recieve your points.');
define('REFER_READ', 'Read');
define('REFER_TOO_MANY_DENIED_REFERRALS',
    'If you have too many denied referrals becaиспользовать of cheating you will be banned!');
define('REFER_EARN_MONEY', 'Refer To Earn Money');
define('REFER_DONATION', 'Every donation made by your referals gives you %s of the amount donated.');
define('REFER_DONATION_REFERALS',
    'Every donation made by referals of your referals gives you <span style="color: orange">7%</span> of the amount donated.');
define('REFER_DONATION_REFERALS_REFERALS',
    'Every donation made by referals of referals of your referals gives you %s of the amount donated.');
define('REFER_MIN_AMOUNT',
    'The min amount for a request is <span style="color: orange">$20</span>.    Just send a message to <span style="color: orange">prisonstruggle@gmail.com</span> with <span style="color: orange">"деньги request"</span> in the subject. In the body of e-mail put your <span style="color: orange">player id</span>.');
define('REFER_ADMINS_ANALYSE',
    'Admins will analyse carefully every referall when you request the деньги...So do not try to cheat or you will be banned forever');
define('REFER_PLAYERS_REFERRED', 'Players Ты Have Referred');
define('REFER_REAL_MONEY', 'Real Money From Referrals');
define('REFER_EXCHANGE_REAL_MONEY', 'Exchange Real Money in RP Store');

//referMoney
define('REFER_MONEY', 'Refer Money');
define('REFER_RP_DAY_PACK', 'The %sRP days pack из your refer деньги was successfully credited to you !');
define('REFER_RP_DAY_PACK_TO_ID', 'The %sRP days pack из your refer деньги was successfully credited to id: %s');
define('REFER_RP_DAY_PACK_FOR_YOU',
    'A %sRP days pack was successfully credit to you. %s bought it with his refer деньги');
define('REFER_NOT_ENOUGH_MONEY', 'Ты don\'t have enough refer деньги!');
define('REFER_AWAKE_PACK', 'The %s Awake pack из your refer деньги was successfully credited to you');
define('REFER_AWAKE_PACK_TO_ID', 'The %s Awake pack из your refer деньги was successfully credited to id: %s');
define('REFER_AWAKE_PACK_FOR_YOU',
    'A %s Awake pack was successfully credited to you. %s" bought it with his refer деньги');
define('REFER_PROTECTION_PACK', 'The %s Protection pack из your refer деньги was successfully credited to you');
define('REFER_PROTECTION_PACK_TO_ID',
    'The %s Protection pack из your refer деньги was successfully credited to id: %s');
define('REFER_PROTECTION_PACK_FOR_YOU',
    'A %s Protection pack was successfully credited to you. %s bought it with his refer деньги');
define('REFER_POINT_PACK', 'The %s points pack из your refer деньги was successfully credited to you');
define('REFER_POINT_PACK_TO_ID', 'The %s points pack из your refer деньги was successfully credited to id: %s');
define('REFER_POINT_PACK_FOR_YOU',
    'A %s points pack was successfully credited to you. %s bought it with his refer деньги');
define('REFER_PACK', 'The %s %s pack из your refer деньги was successfully credited to you');
define('REFER_PACK_TO_ID', 'The %s %s pack из your refer деньги was successfully credited to id: %s');
define('REFER_PACK_FOR_YOU',
    'A %s %s points pack was successfully credited to you. %s bought it with his refer деньги');
define('REFER_KILLER', 'killer');
define('REFER_PISTOL', 'pistol');
define('REFER_ACRES', 'acres');
define('REFER_BEG', 'Beg');
define('REFER_ULTIMATE', 'Ultimate');
define('REFER_INSANE', 'insane');
define('REFER_RP_REFER_MONEY', 'RP Refer Money - Real Money');
define('REFER_MSG_1', 'For those days you will gain energy, nerve and HP twice as quick.');
define('REFER_MSG_1', 'For those days you will gain energy, nerve and HP twice as quick.');
define('REFER_BUY_WITH_REFER_MONEY', 'Buy with Refer Money');
define('REFER_SURE_TO_BUY', 'Are you sure you want to buy that pack?');
define('REFER_KILLER_PACK', 'Killer Pack');
define('REFER_ACRES_PACK', 'Acres Pack');
define('REFER_ACRES_LAND_PANAMA', 'Acres of Land in Panama');
define('REFER_ULTIMATE_PACK', 'Ultimate Pack');
define('REFER_INSANE_TRAINING_PACK', 'Insane Training Pack');

// Packs
define('PACK_NOT_AVAILABLE', 'The pack is no longer available.');
define('HA_PACK', 'Halloween Pack');
define('HA_PACK_2', 'Halloween Pack 2');
define('HA_PACK_LACK_BANK', 'Ты need 250,000,000 bank деньги to buy this pack.');
define('HA_INGAME_BANK_MONEY', 'ingame bank деньги');

//register
define('REGISTER_USERNAME_EXISTS',
    'I\'m sorry but the использоватьrname you choose has already been taken.  Please pick another one.');
define('REGISTER_USERNAME_CHARS',
    'The использоватьrname you chose has %s characters. Ты need to have between 4 and 15 characters.');
define('REGISTER_PWD_CHARS', 'The password you chose has %s characters. Ты need to have between 4 and 20 characters.');
define('REGISTER_PWD_NOT_MATCH', 'Тыr passwords don\'t match. Please try again.');
define('REGISTER_WRONG_EMAIL', 'The e-mail address you entered was invalid.');
define('REGISTER_EMAIL_ALREADY_REGISTERED', 'The e-mail address you entered was invalid.');
define('REGISTER_IP_ALREADY_REGISTERED',
    'That IP already has one registration... If you want to set another account for a different player send an e-mail explaining the request to prisonstruggle@gmail.com');
define('REGISTER_MULTIPLE_ACT_ONE_IP',
    'There is more than one account on this IP. CAREFUL, if you try to cheat all accounts will be banned');
define('REGISTER_ACT_CREATED_1',
    'Тыr account has been created successfully! Please check your email to confirm the account.');
define('REGISTER_ACT_CREATED_2', 'If you are yahoo or didn\'t receive the email, check bulk/spam box');
define('REGISTER_ACT_CREATED_3', 'Redirecting to login page in 10 seconds.');
define('REGISTER_ACT_CREATED_4', 'Click here if you don\'t want to wait');
define('REGISTER', 'Register');
define('REGISTER_LEGAL_INFO', 'Legal Information - Terms of Use Agreement');
define('REGISTER_USERNAME', 'Username');
define('REGISTER_PWD', 'Password');
define('REGISTER_CONFIRM_PWD', 'Confirm    Password');
define('REGISTER_EMAIL', 'Email address');
define('REGISTER_HUMAN_CONFIRMATION', 'Human Confirmation');
define('REGISTER_REQUIRE_JS', 'This site requires a Javascript enabled browser!');
define('REGISTER_CONFIRMATION_EMAIL', 'Confirmation email will be send to here');
define('REGISTER_BACK_HOMEPAGE', 'Back to ЗаключенныйStruggle Homepage');

//resetиспользоватьrpassword
define('RUPWD_NO_USER', 'Sorry, no использоватьr with specified email is found.');
define('RUPWD_INDEX_PAGE', 'Index page');
define('RUPWD_USER_NOT_VALIDATED', 'Тыr account isn\'t validated, please check your email.');
define('RUPWD_CHECK_BULK',
    'If you are yahoo or didn\'t receive the email, <span style="color: red">check bulk/spam box</span>');
define('RUPWD_WRITE_YOUR_EMAIL',
    'If you didn\'t receive any email из us. <span style="color: red">Write your email</span> in this field');
define('RUPWD_CHECK_YOUR_EMAIL',
    'Please check your email and follow the instructions. Please send an email to prisonstruggle@gmail.com if the email never arrives.');
define('RUPWD_THANK_YOU', 'Thank you!');
define('RUPWD_MAIN_PAGE', 'Main page');
define('RUPWD_MAIL_ERR', 'There has been a mail error sending to %s');
define('RUPWD_MAIL_SENT_WITH_PWD', 'Another email is sent with a new password.');
define('RUPWD_LOGIN_AND_CHANGE_PASSWORD',
    'Once you receive you можешь login and change password by yourself. Please send an email to prisonstruggle@gmail.com if the email never arrives.');
define('RUPWD_LOGIN_IN', 'Log in');

//retrunitem
define('RITEM_NO_ITEM', 'No item picked.');
define('RITEM_MEMBER_NOT_HAVE_ITEM', 'Selected gang member don\'t have that item.');
define('RITEM_RECOVERED_LOANED_ITEM', 'Ты have recovered a loaned %s to the regiment armory!');
define('RITEM_RETURNED_LOANED_ITEM', 'Ты returned back a loaned %s to your Gang');
define('RITEM_LOANED_ITEM_TAKEN', 'A loaned %s is taken back to your Gang by -%s- Check your Inventory');
define('RITEM_ITEM_NOT_BELONG_GANG', 'The item you are trying to return doesn\'t belong to your gang!');
define('RITEM_HEAD', 'Return A Loaned Item To The regiment armory');
define('RITEM_MSG_1', 'A %s is returned back из %s to the regiment armory.');
define('RETURN_ITEM', 'Return Item');

define('ROULETTE_HIT',
    'Ты are being hit by a professional killer, pointing a gun to your head is the last thing you should do now.');
define('ROULETTE_ENTER_SHOTS_NUM', 'Ты need to enter the number of shots you\'d like to bet on.');
define('ROULETTE_WRONG_MONEY_MIN', 'Ты можешьnot bet less than $100.');
define('ROULETTE_WRONG_MONEY_MAX', 'Ты можешьnot bet more than $1,000,000.');
define('ROULETTE_NOT_HAVE_MONEY', 'Ты don\'t have $%s to bet.');
define('ROULETTE_WON', 'Congratulations, you just won %s.');
define('ROULETTE_REMAINING_PLAYS', 'Remaining Plays');
define('ROULETTE_ENTER_BET', 'Enter Bet');
define('ROULETTE_NUMBER_OF_BULLETS', 'Number of Bullets');
define('ROULETTE_MAX_BET', 'Max Bet');
define('ROULETTE_HOW_IT_WORKS', 'How it works');
define('ROULETTE_RULE_1',
    'Russian Roulette is the practice of placing a single round in a revolver, spinning the cylinder and closing it into the firearm without looking, aiming the revolver at one\'s own head in a suicidal fashion, and pulling the trigger. The number of rounds placed in the revolver можешь vary, though as a rule there will always be at least one empty chamber. As a gambling game, toy guns are often использоватьd to simulate the practice. The number of deaths caиспользоватьd by this practice is unknown.');
define('ROULETTE_RULE_2', 'The more you place bullets in the gun, the better your prize will be.');
define('ROULETTE_WRONG_BULLET_NUM', 'Ты selected a wrong amount of bullets. Please refresh and try again.');
define('ROULETTE_BULLET_REFLECTED',
    'The gun fired. Luckily, the bullet reflected on your head, so you only lost a fraction of your health. Ты are sent to hospital for 5 minutes (or will stay longer if you were there already). Ты also lost <span style="color: #00FF00; font-weight: bold">%s</span>.');
define('ROULETTE_PLAYED_MAX', 'Sorry, Ты можешь only play 24 times in a day.');

//RPnames
define('RPNAMES_STORE', 'RP Store Имяs');
define('RPNAMES_EXAMPLES', 'All names present in here are just examples.');
define('RPNAMES_CUSTOMIZED', 'All names можешь be customized to your taste.');
define('RPNAMES_BUILD_THE_USERNAME',
    'We will build the использоватьrname according to использоватьrs request and the final decision its always из the player.');
define('RPNAMES_EXCEPT_IN_CHAT', 'All names work every where except in chat/forum.');
define('RPNAMES_HTML_USERNAME', 'HTML Username');
define('RPNAMES_SIMPLE_JS_USERNAME', 'Simple JavaScript Username');
define('RPNAMES_ADVANCE_JS_USERNAME', 'Advance JavaScript Username');
define('RPNAMES_CUSTOM_IMAGE_USERNAME', 'Custom Image Username');
define('EXAMPLES', 'Examples');
define('OPTIONS', 'Options');
define('RPNAMES_OPTIONS_1', 'Ты could select the color that appear when the moиспользовать its over your name.');
define('RPNAMES_OPTIONS_2', 'Ты можешь select one or several colors for your name.');
define('RPNAMES_OPTIONS_3', 'Ты можешь select one or more symbols for your name.');
define('RPNAMES_OPTIONS_4', 'Ты можешь join several type of names (for example, shaking and numbers).');
define('RPNAMES_OPTIONS_5', 'Ты можешь decide the speed of rotating letters/numbers/symbols.');
define('RPNAMES_OPTIONS_6',
    'Ты можешь give suggestion to our coder how you would want a name (for example, the first letter rotates withing the alphabet letters, only half name shakes, and many other things), then we will try and make it happen - NOTE: For more complex changes you have to buy the    advance javascript использоватьrname.');
define('RPNAMES_OPTIONS_7', 'Ты можешь select the colors for your name.');
define('RPNAMES_OPTIONS_8', 'Ты можешь decide the speed of changes in the использоватьrname.');
define('RPNAMES_OPTIONS_9',
    'Ты можешь suggest a completely different использоватьrname, but try talking to coder-adm before.');
define('RPNAMES_OPTIONS_10', 'Ты можешь ask for what you want (colors, styles, background, ...)');
define('RPNAMES_OPTIONS_11', 'This does not include animated images, if you want one contact coder-adm.');
define('RPNAMES_TERMS', 'Terms and Conditions');
define('RPNAMES_TERMS_1',
    'Usernames можешьnot be bought with referral деньги and will not contribute to referral деньги.');
define('RPNAMES_TERMS_2', 'Usernames можешь take about 2-3 days to be delivered.');
define('RPNAMES_TERMS_3',
    'After the final decision of the player concerning the использоватьrname it можешьnot be changed unless player buy a new использоватьrname.');
define('RPNAMES_TERMS_4',
    'When player requests something new, we will try to make it, but можешьnot promise that its doable.');
define('RPNAMES_TERMS_5',
    'In the case of a player don\'t like any of the proposals the old normal name можешь be kept, but the деньги will not be refunded.');

//rpstore
define('RPSTORE_EXAMPLES', 'Examples in использовать');
define('RPSTORE_NORMAL_RESPECTED', 'Normal Respected Заключенныйers member');
define('RPSTORE_VIP_RESPECTED', 'VIP Respected Заключенныйers member');
define('RPSTORE_JUST', 'Just');
define('RPSTORE_MONTHLY', 'Monthly');
define('RPSTORE_RP_DAYS', 'RP Days');
define('RPSTORE_SUBSCRIPTION_STATUS', 'Subscription Status');
define('RPSTORE_SUBSCRIPTION_REOCURRING_PAYMENTS',
    'Subscriptions are automatically reocurring payments, billed every month.');
define('RPSTORE_SUBSCRIPTION_GAIN_ENERGY_TWICE', 'For those days you will gain energy, nerve twice as quick.');
define('RPSTORE_SUBSCRIPTION_GAIN_AWAKE_TWICE', 'For those days you will gain awake, HP twice as quick.');
define('RPSTORE_SUBSCRIPTION_GAIN_POINTS', 'For those days you will gain more    points when voting.');
define('RPSTORE_PAYMENTS_WITH_PAYPAL', 'Make payments with PayPal - it\'s fast, free and secure!');

//rules
define('RULES', 'Rules');
define('RULES_TEXT_1', 'No need to say that any kind of refresher or macro its not allowed.');
define('RULES_TEXT_2',
    'No direct advertising of other games or websites. The использовать of advertising banners is strictly prohibited anywhere.');
define('RULES_TEXT_3',
    'No multi-accounting; you are only allowed one account per person. If more than one person is sharing the same computer, please inform the staff via the support center. Otherwise your accounts will be banned and deleted. Getting an account re-instated for this offense is near impossible so please do it right the first time.');
define('RULES_TEXT_4', 'Accounts on same location (IP) CANNOT');
define('RULES_TEXT_5', 'Отправить деньги, point or items between those account;');
define('RULES_TEXT_6', 'Join same gang;');
define('RULES_TEXT_7',
    'Spam is not tolerated (we consider advertising for other games as spam). Please использовать common sense when talking to other players, if you start swearing you will be banned! Asking for personal or contact information of any kind is also strictly prohibited. Massive использовать of caps можешь also be considered as swearing and / or    spamming.');
define('RULES_TEXT_8',
    'Scamming is forbidden on other players and gangs, ingame or through an external tool (website , instant messaging...) as long as it is related to the game and можешь affect it in any way.');
define('RULES_TEXT_9',
    'Exploitation of any bugs or holes in game mechanics will not be tolerated. We will использовать our discretion as to what we feel is cheating and we will take immediate action against the accounts we feel are violating the rules.');
define('RULES_TEXT_10',
    'We employ an in-hoиспользовать staff that develops, runs, and administers the games. We are not accepting any applications at this time.');
define('RULES_TEXT_11', 'The использовать of nudity and racism anywhere on this site is prohibited.');
define('RULES_TEXT_12', 'Ты may not sell accounts or in-game forms of virtual property');
define('RULES_ASK_FOR_MORE_INFO', 'Ask for more info');
define('RULES_TEXT_13',
    'Under no circumstances does purchasing a package из our donator area make you above the rules. We appreciate all of the donations and purchases; however, we wish to create a fair environment to play in and will take action where it is needed.');
define('RULES_TEXT_14',
    'If you have any questions about these rules please direct them to our Game Admins and they will happily assist you in any way they можешь.');
define('RULES_TEXT_15', 'These rules are subject to change and staff interpretation at any time.');

//search
define('SEARCH_SLOTS', 'Search Slots');
define('SEARCH_SLOT', 'Slot');
define('SEARCH_EMPTY', 'Empty');
define('SEARCH_PRISONER', 'Заключенныйer Search');
define('SEARCH_PRISONER_MEET_CRITERIA', 'Find prisoners that meet your search criteria.');
define('SEARCH_OTHER_FIELDS_IGNORED', 'All other fields will be ignored');
define('SEARCH_TO', 'to');
define('SEARCH_INCLUSIVE', 'inclusive');
define('SEARCH_CHOOSE_LEVEL', 'Choose Level');
define('SEARCH_CHOOSE_MONEY', 'Choose Money');
define('SEARCH_AND_MORE', 'and more');
define('SEARCH_ATTACKABLE', 'Атакаable');
define('SEARCH_ALL_PRISONS', 'All Заключенныйs');
define('SEARCH_ALL_GANGS', 'All Gangs');
define('SEARCH_NO_GANG', 'No Gang');
define('SEARCH_NOT_GANG_MATES', 'Do Not Search Gang Mates');
define('SEARCH_RESET_INITIAL', 'Reset to initial values');
define('SEARCH_RESET_PAGE', 'Reset to page start values');
define('SEARCH_LIMITED', 'Search limited to first 50 results');
define('SEARCH_WITH_NAME', 'with the name');
define('SEARCH_RESULTS', 'Search Results');
define('SEARCH_NO_RESULTS', 'No results found.');
define('SEARCH_INVALID_SAVED_NAME', 'Invalid saved name.');
define('SEARCH_INVALID_SLOT', 'Invalid slot number.');
define('SEARCH_INVALID_INPUT', 'Invalid input.');
define('SEARCH_CANT_CREATE_SLOT', 'Could not create a new search slot entry. Please try again later.');
define('SEARCH_NOT_FOUND', 'Search not found. Please try again later.');

//sellitem
define('SELLITEM_NO_ITEM_TO_SELL', 'Ты don\'t have any %s to sell!.');
define('SELLITEM_WRONG_ITEM_NUM', 'Please enter a valid amount of items.');
define('SELLITEM_NOT_ENOUGH_ITEMS', 'Ты don\'t have enough %ss.');
define('SELLITEM_SOLD', 'Ты have sold %s %s for $%s each.');
define('SELLITEM_HEAD', 'Sell Item');
define('SELLITEM_NUM_ITEMS_TO_SELL', 'Number of Items to sell');
define('SELLITEM_SURE_TO_SELL', 'Are you sure that you want to sell %s for $%s each?');
define('SELLITEM_BACK_TO_INVENTORY', 'Back to Inventory');

//senditem
define('SENDITEM_NOT_YOURSELF', 'Ты можешьnot send to yourself.');
define('SENDITEM_NOT_VALID_USER_ID', 'Ты must enter a valid использоватьr id !');
define('SENDITEM_NOT_HAVE_ANY', 'Ты don\'t have any of those.');
define('SENDITEM_NOT_VALID_ITEMS_NUM', 'Ты must enter a valid amount of items.');
define('SENDITEM_NOT_POSITIVE_ITEMS_NUM', 'Please enter a positive amount of items to send.');
define('SENDITEM_NOT_ENOUGH_ITEMS', 'Ты do not have enough %s.');
define('SENDITEM_RECEIVE', 'Ты receive %s %s из %s');
define('SENDITEM_SENT', 'Ты have sent %s %s to %s');
define('SENDITEM', 'Отправить Item');
define('SENDITEM_ITEM', 'Отправить %s');
define('SENDITEM_ITEM_NUM', 'Number of Items');
define('SENDITEM_USER_ID', 'User ID');

//sendденьги
define('SENDMONEY_TRANSFERED', 'Ты have successfully transferred $%s to %s.');
define('SENDMONEY_RECEIVED', 'Ты received $%s из %s');
define('SENDMONEY', 'Отправить Money');
define('SENDMONEY_NOT_ENOUGH_MONEY', 'Ты don\'t have enough деньги to do that!');
define('SENDMONEY_NOT_USERNAME', 'USER ID, not использоватьrname! And enter the amount of деньги as a number.');
define('SENDMONEY_AMT', 'Amount Of Money');
define('SENDMONEY_DONT_USE_COMA', 'Do not insert the comma, использовать 10000 not 10,000.');

//sendpoints
define('SENDPOINTS', 'Отправить Points');
define('SENDPOINTS_NOT_ENOUGH', 'Ты do not have enough points.');
define('SENDPOINTS_TRANSFERED', 'Ты have successfully transferred %s points to %s.');
define('SENDPOINTS_RECEIVED', 'Ты received %s points из %s');
define('SENDPOINTS_NOT_USERNAME', 'USER ID, not использоватьrname! And enter the amount of points as a number.');
define('SENDPOINTS_AMOUNT', 'Amount Of Points');

//showers
define('SHOWERS_PERSON_NOT_EXIST', 'That person does not exist.');
define('SHOWERS_PERSON_NOT_SHOWER', 'That person is not in shower.');
define('SHOWERS_YOU_IN_SHOWER', 'Ты можешь not bust when you are in shower.');
define('SHOWERS_YOU_IN_HOSPITAL', 'Ты можешь not bust when you are in hospital.');
define('SHOWERS_YOU_CAUGHT', 'Ты were caught. Ты were hauled off to shower for 20 minutes.');
define('SHOWERS_SUCCESS', 'Success! Ты receive %s exp and $%s');
define('SHOWERS_BUSTED_OUT_MEANTIME', 'The prisoner was busted out of the shower in the meantime.');
define('SHOWERS_NOT_ENOUGH_NERVE', 'Ты don\'t have enough nerve for that crime.');
define('SHOWERS', 'Shower');
define('SHOWERS_TIME_LEFT', 'Time Left');

//slots
define('SLOTS_NOT_HAVE_MONEY', 'Ты don\'t have enough деньги to play slots.');
define('SLOTS_SPIN_RESULTS', 'Spin Results');
define('SLOTS_WON', 'Congratulations, you have won');
define('SLOTS_LOST', 'Ты didn\'t win anything, sorry.');
define('SLOTS_MSG_1', 'So, you fancy a try at the slot machine?    Well, it\'s just $100 a pull, so have at it.');
define('SLOTS_PLAY_MEGA_SLOTS', 'Play Mega Slots');
define('SLOTS_PLAY_FAIR', 'Ты may not speedclick, использовать bots, or the F5 key. Please play fair.');

//spendpoints
define('USER_ALREADY_SEARCH_PRISON_YARD',
    'Ты already search the prison yard too many times today, come back tomorrow!');
define('USER_STILL_CAN_SEARCH_PRISON_YARD', 'Ты still можешь search prison yard, no need to waste points mate!');
define('USER_STILL_CAN_SEARCH_PRISON_YARD', 'Ты still можешь search prison yard, no need to waste points mate!');
define('POINT_SHOP', 'Point Shop');
define('SPENDPOINTS_WELCOME_MSG_1', 'Welcome to the Point Shop, here you можешь spend your points on various things.');
define('SPENDPOINTS_REFILL_YARD', 'Refill Search the Заключенный Yard');

//spy
define('SPY_NOT_FIND_POINTS', 'Тыr Private Investigator could not find their points out.');
define('SPY_NOT_FIND_BANK', 'Тыr Private Investigator could not find their bank out.');
define('SPY_NOT_FIND_STRENGTH', 'Тыr Private Investigator could not find their strength out.');
define('SPY_NOT_FIND_DEFENSE', 'Тыr Private Investigator could not find their defense out.');
define('SPY_NOT_FIND_SPEED', 'Тыr Private Investigator could not find their speed out.');
define('SPY_NOT_FIND_WEAPON', 'Тыr Private Investigator could not find their weapon out.');
define('SPY_NOT_FIND_ARMOR', 'Тыr Private Investigator could not find their armor out.');
define('SPY_NO_WEAPON', 'No weapon equipped');
define('SPY_NO_ARMOR', 'No armor equipped');
define('SPY_FOUND', 'Тыr Private Investigator found out the following about');
define('SPY_VIEW_LOG', 'View Spylog');
define('SPY', 'Spy');
define('SPY_SURE_TO_INVESTIGATE', 'Are you sure that you want to hire a Private Investigator to spy on %s for $%s?');

//spylog
define('SPY_LOG', 'Spy Log');
define('SPY_LOG_WHEN', 'When');

//stafftop
define('STAFFTOP_HEAD', 'Staff and Special Inmates');
define('STAFFTOP_ADMINS', 'Admins');
define('STAFFTOP_ADMINS_NOT_PMAIL_1', 'Don\'t pmail us saying you were mugged or attacked online.');
define('STAFFTOP_ADMINS_NOT_PMAIL_2',
    'Don\'t pmail us saying your points/деньги disappeared unless you are absolutely sure that you didn\'t send/deposit/использовать them.');
define('STAFFTOP_ADMINS_NOT_PMAIL_3', 'Don\'t ask to be mod or admin, we will not reply.');
define('STAFFTOP_ADMINS_NOT_PMAIL_4', 'Doubts about how game works, ask in shout/forum/chat.');
define('STAFFTOP_ADMINISTRATORS', 'Administrators');
define('STAFFTOP_CODERS', 'Coders');
define('STAFFTOP_NOTE', 'Note: There are no defined order for the names!');
define('SUPPORT', 'Support');
define('SUPER_MODERATORS', 'Super Moderators');
define('MODERATORS', 'Moderators');
define('BEST_INMATE', 'Best Inmate');
define('BEST_LEVEL', 'Best Level');
define('TOP_DONATORS', 'Top Donators');
define('TOP_REFERRALS', 'Новый Contest Top Referrals');

//stocklogs
define('STOCKLOGS_HEAD', 'Stock Market Log');
define('STOCKLOGS_NO_LOGS', 'There are currently no stock market logs.');
define('STOCKLOGS_TRANSACTION_TIME', 'Transaction time');
define('STOCKLOGS_COMPANY', 'Company');
define('STOCKLOGS_QUANTITY', 'Quantity');
define('STOCKLOGS_UNIT_PRICE', 'Unit Price');
define('STOCKLOGS_AGO', 'ago');
define('STOCKLOGS_STOCK_MARKET', 'Stock market');

//suggestion
define('SUGGESTION_INVALID_SEARCH_USER', 'Invalid search использоватьr specified !');
define('SUGGESTION_INVALID_SEL_RANGE', 'Invalid selection range specified.');
define('SUGGESTION_INVALID_THREAD_ID', 'Invalid thread id specified.');
define('SUGGESTION_INVALID_ENTRY_ID', 'Invalid entry id specified.');
define('SUGGESTION_NOT_SUPPORT', 'Sorry, but you do not have required support rights.');
define('SUGGESTION_RULES_VIOLATION', 'Rules violation on support center');
define('SUGGESTION_USER_BANNED', 'The использоватьr with id %s was successfully banned.');
define('SUGGESTION_USER_UNBANNED', 'The использоватьr with id %s was successfully unbanned.');
define('SUGGESTION_USER_WARNED', 'The использоватьr with id %s was successfully warned.');
define('SUGGESTION_SUCCESSFULLY_VOTE', 'Ты have successfully added your (+1) vote to this suggestion.');
define('SUGGESTION_SUCCESSFULLY_NVOTE', 'Ты have successfully added your (-1) vote to this suggestion.');
define('SUGGESTION_PROMOTED', 'The suggestion was successfully promoted to a higher level.');
define('SUGGESTION_DEMOTED', 'The suggestion was successfully demoted to a lower level.');
define('SUGGESTION_CLOSED', 'The suggestion was successfully closed.');
define('SUGGESTION_OPENED', 'The suggestion was successfully opened.');
define('SUGGESTION_REOPENED', 'The suggestion was successfully reopened.');
define('SUGGESTION_DELETED', 'The suggestion was successfully deleted.');
define('SUGGESTION_MAX_REACHED',
    'Ты можешьnot have more than %s opened suggestions at any given time. Please close or delete suggestions first.');
define('SUGGESTION_TITLE_EMPTY', 'Ты must enter a title for your suggestion.');
define('SUGGESTION_TEXT_EMPTY', 'Ты must enter a text for your suggestion.');
define('SUGGESTION_TITLE_CHARS', 'Тыr suggestion title must be between %s and %s characters.');
define('SUGGESTION_TEXT_CHARS', 'Тыr suggestion text must be between %s and %s characters.');
define('SUGGESTION_ENTRY_TEXT_EMPTY', 'Ты must enter a text for your entry.');
define('SUGGESTION_ENTRY_TEXT_CHARS', 'Тыr entry must be between %s and %s characters.');
define('SUGGESTION_ENTRY_ADDED', 'Тыr suggestion entry was added to the thread.');
define('SUGGESTION_ENTRY_DELETED', 'The suggestion entry was successfully deleted.');
define('SUGGESTION_SECTION_CHANGED', 'The suggestion section was successfully changed.');
define('SUGGESTION_PREVIOUS', 'Previous %s suggestions');
define('SUGGESTION_NEXT', 'Next %s suggestions');
define('SUGGESTION_ENTRY_PREVIOUS', 'Previous %s entries');
define('SUGGESTION_ENTRY_NEXT', 'Next %s entries');
define('SUGGESTION_REPLYING_TO', 'Replying to');
define('SUGGESTION_AUTHOR', 'Author');
define('SUGGESTION_TITLE', 'Title');
define('SUGGESTION_SECTION', 'Section');
define('SUGGESTION_CONTENT', 'Content');
define('SUGGESTION_SUBJECT', 'Subject');
define('SUGGESTION_ADD_THREAD_ENTRY', 'Add a new entry to %sSuggestion thread%s');
define('SUGGESTION_ADD_ENTRY', 'Add suggestion entry');
define('SUGGESTION_SUBMIT_NEW', 'Submit a new suggestion');
define('SUGGESTION_ADD_TICKET', 'Add ticket');
define('SUGGESTION_CLOSED_SUGGESTIONS', 'Closed suggestions');
define('SUGGESTION_NO_CLOSED_SUGGESTIONS', 'Ты do not have any closed suggestions.');
define('SUGGESTION_CLOSED_STATUS', 'Closed status');
define('SUGGESTION_SCORE', 'Score');
define('SUGGESTION_VIEW_THREAD', 'View suggestion thread');
define('SUGGESTION_REOPEN_THREAD', 'Reopen suggestion thread');
define('SUGGESTION_DELETE', 'Delete suggestion');
define('SUGGESTION_MENU', 'Suggestion navigation menu');
define('SUGGESTION_DIRECT_LINKS', 'Direct links');
define('SUGGESTION_VIEW_MY_OPENED', 'View my opened suggestions');
define('SUGGESTION_VIEW_MY_CLOSED', 'View my closed suggestions');
define('SUGGESTION_SUBMIT_NEW_SUGGESTION', 'Submit a new suggestion');
define('SUGGESTION_VIEW_ALL_OPENED', 'View all opened suggestions');
define('SUGGESTION_MOST_LIKED', 'Самый liked first');
define('SUGGESTION_MOST_RECENT', 'Самый recent first');
define('SUGGESTION_SUPPORT_LINKS', 'Support links');
define('SUGGESTION_VIEW_ALL_CLOSED', 'View all closed suggestions');
define('SUGGESTION_WELCOME', 'Welcome to Заключенный Struggle suggestion center');
define('SUGGESTION_WELCOME_MSG_1', 'Here you можешь track and submit new suggestions.');
define('SUGGESTION_WELCOME_MSG_2',
    'Ты можешь only have a maximum of 3 suggestions opened at a given time. If you would like to submit a new suggestion and are already passed the limit, just close or delete an old suggestion.');
define('SUGGESTION_WELCOME_MSG_3', 'Ты можешь only vote one time for each suggestion different из your own.');
define('SUGGESTION_WELCOME_MSG_4',
    'Ты have the choice to give either a positive (+1) or negative (-1) vote. If you do not know whether you like the suggestion or not, just do not vote.');
define('SUGGESTION_WELCOME_MSG_5',
    'Ты можешь <b>only</b> put <b>one</b> suggestion per thread. Any thread with more than one suggestion will be systematically closed or deleted as it will be regarded as a workaround to the above 3 limit.');
define('SUGGESTION_WELCOME_MSG_6',
    'Suggestions with enough positive votes will have a better chance of being accepted and maybe developped by the Заключенный Struggle team, so we are giving you the chance to actually be part of prison struggle<br> improvement ! Ты will be notified if your suggestion is actually chosen.');
define('SUGGESTION_WELCOME_MSG_7', 'We thank you in advance for your interest.');
define('SUGGESTION_OPENED_SUGGESTIONS', 'Opened suggestions');
define('SUGGESTION_NO_OPENED_SUGGESTIONS', 'Ты do not have any opened suggestions.');
define('SUGGESTION_LAST_UPDATE_BY', 'Last Update By');
define('SUGGESTION_REPLY_TO', 'Reply to suggestion');
define('SUGGESTION_VIEW', 'View suggestion');
define('SUGGESTION_CLOSE', 'Close suggestion');
define('SUGGESTION_NO_CLOSED_FOR_LEVEL', 'There are no closed suggestion accessible to your level.');
define('SUGGESTION_SUPPORT_LEVEL', 'Support Level');
define('SUGGESTION_SEARCH_OPTIONS', 'Search options');
define('SUGGESTION_FIND_CREATED_BY', 'Find suggestions created by');
define('SUGGESTION_SEARCH_NOTE', 'PS: Ты можешь enter an id or an использоватьrname');
define('SUGGESTION_FILTER', 'Filter');
define('SUGGESTION_SEARCH_IN', 'Search in');
define('SUGGESTION_ALL_SECTIONS', 'All Sections');
define('SUGGESTION_LEVEL', 'Suggestion Level');
define('SUGGESTION_I_LIKE', 'I like this suggestion');
define('SUGGESTION_I_DISLIKE', 'I dislike this suggestion');
define('SUGGESTION_PROMOTE', 'Promote suggestion');
define('SUGGESTION_DEMOTE', 'Demote suggestion');
define('SUGGESTION_DETAILS', 'Suggestion details');
define('SUGGESTION_CHANGE', 'Change section');
define('SUGGESTION_SCORE_TEXT', 'Suggestion score');
define('SUGGESTION_CREATION_TIME', 'Creation time');
define('SUGGESTION_LAST_UPDATE_TIME', 'Last Update time');
define('SUGGESTION_CLOSURE_TIME', 'Closure time');
define('SUGGESTION_CLOSED_BY', 'Closed by');
define('SUGGESTION_INITIAL', 'Initial suggestion');
define('SUGGESTION_THREAD', 'Suggestion thread');
define('SUGGESTION_NO_ENTRIES', 'This suggestion currently has no entries.');
define('SUGGESTION_REPLY', 'Reply');
define('SUGGESTION_REOPEN_TICKET_THREAD', 'Reopen ticket thread');
define('SUGGESTION_DELETE_ENTRY', 'Delete entry');
define('SUGGESTION_WARN_USER', 'Warn User');
define('SUGGESTION_BAN_USER', 'Ban User');
define('SUGGESTION_UNBAN_USER', 'Unban User');
define('SUGGESTION_CANT_REPLY_LANG', 'Вы не можете ответить так как не говорите на "%s" языке.');
define('SUGGESTION_ENTRY_PRIVATE', 'The suggestion request entry was successfully changed to private.');
define('SUGGESTION_ENTRY_PUBLIC', 'The suggestion entry was successfully  changed to public.');

define('SUGGESTION_THREAD_ENTRY_CANT_ADDED',
    'The thread entry could not be added. Please try again later and contact an administrator.');
define('SUGGESTION_CANT_DEL_ENTRY_NOT_CREATED', 'Ты можешьnot delete an entry you did not add.');
define('SUGGESTION_CANT_DELETED',
    'The thread entry could not be deleted. Please try again later and contact an administrator.');
define('SUGGESTION_TICKET_ENTRY_DELETED', 'The ticket entry and thread have been deleted.');
define('SUGGESTION_ALREADY_VOTED', 'Тыr vote has already been given for this suggestion.');
define('SUGGESTION_VOTE_CANT_ADDED', 'Тыr vote could not be added. Please try again later.');
define('SUGGESTION_VOTE_CANT_DELETED', 'Тыr vote could not be deleted. Please try again later.');
define('SUGGESTION_CANT_CHANGE_CATEGORY', 'Ты можешьnot change category for a support request you did not create.');
define('SUGGESTION_CANT_REOPEN_OPENED', 'Ты можешьnot reopen a support request that is already opened.');
define('SUGGESTION_CANT_REOPEN_NOT_CREATED', 'Ты можешьnot reopen a support request you did not create.');
define('SUGGESTION_CANT_CLOSE_CLOSED', 'Ты можешьnot close a support request that is already closed.');
define('SUGGESTION_CANT_CLOSE_NOT_CREATED', 'Ты можешьnot close a support request you did not create.');
define('SUGGESTION_YOUR_CLOSED', 'Тыr suggestion #%s was closed.');
define('SUGGESTION_NOT_HAVE_ACCESS', 'Ты do not have access to this support request.');
define('SUGGESTION_CANNOT_PROMOTE', 'Ты можешьnot promote a thread to a level higher than 3.');
define('SUGGESTION_REVIEWED_BY_ADMIN',
    'It means it will get reviewed by the admin team for integration and final decision.');
define('SUGGESTION_INTEGRATED_INTO_GAME',
    'It means the suggestion will be integrated into the game ! Congratulations !.');
define('SUGGESTION_PROMOTED_TO_LEVEL', 'Тыr suggestion #%s have been promoted to level %s !');
define('SUGGESTION_CANNOT_DEMOTE', 'Ты можешьnot demote a thread to a level lower than 1.');
define('SUGGESTION_THREAD_CANT_ADDED',
    'The thread could not be added. Please try again later and contact an administrator.');
define('SUGGESTION_THREAD_CANT_DELETED',
    'The thread could not be deleted. Please try again later and contact an administrator.');
define('SUGGESTION_THREAD_DELETED',
    'Тыr suggestion #%s was deleted. Please open a support ticket if you have any questions.');
define('SUGGESTION_CANT_REPLY_CLOSED', 'Ты можешьnot reply to a closed thread.');
define('SUGGESTION_CANT_REPLY', 'Ты можешьnot reply to this suggestion.');

//support_category table
define('SCAT_GENERAL', 'General');
define('SCAT_GRAPHICS', 'Graphics');
define('SCAT_NEW_FEATURES', 'Новый Features');
define('SCAT_IMPROVEMENTS', 'Improvements');
define('SCAT_GANGS', 'Gangs');

//support
define('SUPPORT_PRIORITY_RAISED', 'The support request priority was successfully raised.');
define('SUPPORT_PRIORITY_LOWERED', 'The support request priority was successfully lowered.');
define('SUPPORT_LEVEL_PROMOTED', 'The support request was successfully promoted to a higher support level.');
define('SUPPORT_LEVEL_DEMOTED', 'The support request was successfully demoted to a lower support level.');
define('SUPPORT_CLOSED', 'The support request was successfully closed.');
define('SUPPORT_REOPENED', 'The support request was successfully reopened.');
define('SUPPORT_TITLE_EMPTY', 'Ты must enter a title for your support ticket.');
define('SUPPORT_TEXT_EMPTY', 'Ты must enter a text for your support ticket.');
define('SUPPORT_TITLE_CHARS', 'Тыr ticket title must be between %s and %s characters.');
define('SUPPORT_TEXT_CHARS', 'Тыr ticket text must be between %s and %s characters.');
define('SUPPORT_ENTRY_TEXT_CHARS', 'Тыr entry must be between %s and %s characters.');
define('SUPPORT_REQUEST_OPENED', 'A new support request was opened with id: %s.');
define('SUPPORT_ENTRY_TEXT_EMPTY', 'Ты must enter a text for your entry.');
define('SUPPORT_ENTRY_ADDED', 'Тыr support request entry was added to the thread.');
define('SUPPORT_ENTRY_DELETED', 'The support request entry was successfully deleted.');
define('SUPPORT_ENTRY_PRIVATE', 'The support request entry was successfully changed to private.');
define('SUPPORT_ENTRY_PUBLIC', 'The support request entry was successfully  changed to public.');
define('SUPPORT_DELETED', 'The support request was successfully deleted.');
define('SUPPORT_PREVIOUS', 'Previous %s requests');
define('SUPPORT_NEXT', 'Next %s requests');
define('SUPPORT_ADD_THREAD_ENTRY', 'Add a new entry to %sTicket thread%s');
define('SUPPORT_PRIVATE', 'Private');
define('SUPPORT_ADD_REQUEST', 'Add a new support request');
define('SUPPORT_CLOSED_TICKETS', 'Closed support tickets');
define('SUPPORT_NO_CLOSED_TICKETS', 'Ты do not have any closed support tickets.');
define('SUPPORT_LEVEL', 'Support Level');
define('SUPPORT_VIEW_TICKET_THREAD', 'View ticket thread');
define('SUPPORT_REOPEN_TICKET_THREAD', 'Reopen ticket thread');
define('SUPPORT_MENU', 'Support navigation menu');
define('SUPPORT_VIEW_MY_OPENED', 'View my opened requests');
define('SUPPORT_VIEW_MY_CLOSED', 'View my closed requests');
define('SUPPORT_NEW_SUPPORT_REQUEST', 'Open a new support request');
define('SUPPORT_VIEW_ALL_OPENED', 'View all opened requests');
define('SUPPORT_VIEW_ALL_CLOSED', 'View all closed requests');
define('SUPPORT_WELCOME', 'Welcome to Заключенный Struggle support center');
define('SUPPORT_WELCOME_1', 'Here you можешь manage your support tickets, and create new support requests.');
define('SUPPORT_WELCOME_2', 'A member of our staff will try to answer to your requests as soon as possible.');
define('SUPPORT_WELCOME_3',
    'Please understand that support answers можешь be delayed if your requests are complex, or involve<br>    some technical expertise.');
define('SUPPORT_WELCOME_4', 'We thank you in advance for your cooperation.');
define('SUPPORT_NO_OPENED_TICKETS', 'Ты have not any opened tickets.');
define('SUPPORT_OPENED_TICKETS', 'Opened support tickets');
define('SUPPORT_LAST_UPDATE', 'Last Update');
define('SUPPORT_OPEN_NEW_TICKET', 'Open a new support request ?');
define('SUPPORT_REPLY', 'Reply to support request');
define('SUPPORT_VIEW', 'View support request');
define('SUPPORT_CLOSE', 'Close support request');
define('SUPPORT_CLOSED_TICKETS', 'Closed support tickets');
define('SUPPORT_CLOSED_TICKETS_FOR_LEVEL', 'There are no closed support tickets accessible to your support level.');
define('SUPPORT_TICKETS_REOPEN', 'Reopen ticket thread');
define('SUPPORT_TICKETS_DELETE', 'Delete support request');
define('SUPPORT_FIND_BY', 'Find support requests created by');
define('SUPPORT_NO_OPENED_TO_SEC_LVL', 'There are no opened support tickets accessible to your support level.');
define('SUPPORT_PRIORITY', 'Priority');
define('SUPPORT_RAISE', 'Raise support request priority');
define('SUPPORT_LOWER', 'Lower support request priority');
define('SUPPORT_PROMOTE', 'Promote support request');
define('SUPPORT_DEMOTE', 'Demote support request');
define('SUPPORT_REOPEN', 'Reopen support request');
define('SUPPORT_DELETE', 'Delete support request');
define('SUPPORT_TICKET_DETAILS', 'Ticket details');
define('SUPPORT_TICKET_THREAD', 'Ticket thread');
define('SUPPORT_NO_TICKET_THREAD', 'This ticket thread currently has no entries.');
define('SUPPORT_POSTED', 'Posted');
define('SUPPORT_MAKE_PRIVATE', 'Make it Private');
define('SUPPORT_MAKE_PUBLIC', 'Make it Public');
define('SUPPORT', 'Support');
define('SUPPORT_YOUR_CLOSED', 'Тыr support request #%s was closed.');
define('SUPPORT_CANNOT_RAISE', 'Ты можешьnot raise a thread priority to a value higher than 10.');
define('SUPPORT_CANNOT_LOWER', 'Ты можешьnot lower a thread priority to a value below 1.');
define('SUPPORT_CANNOT_DELETE_NOT_OPENED', 'Ты можешьnot delete a support request you did not open.');
define('SUPPORT_DELETED_YOURS',
    'Тыr support request #%s was deleted. Please submit a new request if you have any question.');
define('SUPPORT_CANT_REPLY_NOT_START', 'Ты можешьnot reply to a thread you did not start.');
define('SUPPORT_NEW_REPLY_IN',
    'There is a new reply to your support request #%s. Ты можешь check the answer in the support center.');
define('SUPPORT_USER_CANT_ADDED',
    'The support использоватьr could not be added. Please try again later and contact an administrator.');
define('SUPPORT_USER_CANT_DELETED',
    'The support использоватьr could not be deleted. Please try again later and contact an administrator.');
define('SUPPORT_MAX_THREAD_REACHED', 'Ты можешь only have up to %d tickets opened at the same time on support center.');
define('SUPPORT_CANT_REPLY_LANG', 'Вы не можете ответить так как не говорите на "%s" языке.');

//subscribe_return
define('SUBSCRIBE_CANCELLED', 'Ты have можешьcelled your subscription.');
define('SUBSCRIBE_SUBSCRIPTION_THANKS',
    'Thank you for your subscription to www.prisonstruggle.com. Тыr transaction has been completed, and a receipt for your purchase has been emailed to you. Ты may log into your account at <a href="http://www.paypal.com">www.paypal.com</a> to view details of this transaction. Тыr subscription should be activated, if not, please contact an admin for assistance.');
define('SUBSCRIBE_VIEW_ERR',
    'Ты shouldn\'t access this page directly. Тыr actions have been reported to administrators for further investigation.');

//toBanUser
define('TBU_USER_UNBANNED', 'User %s successfully unbanned');
define('TBU_USER_BANNED', 'User %s successfully banned');
define('TBU_USER_CANT_TARGET_SELF', 'Ты можешьnot target yourself.');
define('TBU_USER_CANT_TARGET_ADMIN', 'Ты можешьnot target admin.');
define('TBU_BAN_UNBAN', 'Ban/Unban Player');
define('TBU_BAN_REASON', 'Reason for Ban');
define('TBU_BAN_PLAYER', 'Ban Player');
define('TBU_UNBAN_PLAYER', 'UnBan Player');

//toFreezeUser
define('TFU_USER_UNFROZEN', 'User %s successfully un-frozen');
define('TFU_USER_FROZEN', 'User %s successfully frozen');
define('TFU_USER_CANT_TARGET_SELF', 'Ты можешьnot target yourself.');
define('TFU_USER_CANT_TARGET_ADMIN', 'Ты можешьnot target admin.');
define('TFU_FREEZE_UNFREEZE', 'Freeze/Unfreeze Player');
define('TFU_FREEZE_REASON', 'Reason for Freeze');
define('TFU_FREEZE_PLAYER', 'Freeze Player');
define('TFU_UNFREEZE_PLAYER', 'UnFreeze Player');

//tos
define('TOS_TERMS', 'Terms & Conditions');
define('TOS_TERMS_1', 'Entrants must be 16 years or older.');
define('TOS_TERMS_2',
    'The использоватьr who wins any of the promotions may choose which version    of the Playstation3 they would like [For Example PAL/NTSC]');
define('TOS_TERMS_3', 'Change and termination of the Services');
define('TOS_TERMS_4',
    'The Provider можешь, at its sole discretion, decide to change the content of the Services. Furthermore, the Provider may terminate the provision    of the Services and the supply of the Products without notice or restrictions.');
define('TOS_NOTICES', 'Notices');
define('TOS_NOTICES_1',
    'Notices by the Provider will be posted on the Website. We therefore ask Ты to look on a regular basis on the Website of the Provider.');
define('TOS_CHANGES_TERMS', 'Changes Terms and Conditions');
define('TOS_CHANGES_TERMS_1',
    'These Terms and Conditions and all other agreements, entered into between Ты and the Provider, may be amended из time to time by the Provider. The applicability of amendments as to the Services and the Products, have effect as of the moment such amendments are posted by the Provider on its Website. We therefore advise Ты to look on a    regular basis on the Website of the Provider.');
define('TOS_SUSPENSION', 'Change, Suspension and можешьcellation');
define('TOS_SUSPENSION_1',
    'From time to time, the Provider may suspend the Services for (amongst    others) maintenance and upgrades, In the event of (the threat of)    abиспользовать and failures, the Provider may, at its own discretion, suspend or можешьcel the Services. Of such suspension or можешьcellation, the    Provider will inform Ты via its Website.');
define('TOS_PRIZES', 'Prizes');
define('TOS_PRIZES_1',
    'Заключенныйstruggle.com is not responsible for late, misdirected or damaged communication of any kind including computer failure. All decisions are final.');

//tutorials
define('TUTORIALS_NEWBIES', 'Новыйbies Tutorial');
define('TUTORIALS_CAN_ATTACK_MUG_SPY',
    'Hi Inmate, in this game you можешь attack, mug, spy, do crimes, train at gym and many other things... but starting из the top');
define('TUTORIALS_VOTE_EVERYDAY', 'Vote Everyday');
define('TUTORIALS_VOTE_1',
    'This is ultimately important becaиспользовать this gets you needed funds and points to start getting along in prison, but to make sure you get the most out of this voting system, you need to vote at all of the sites. Simple?');
define('TUTORIALS_SEARCH_YARD', 'Search Заключенный Yard');
define('TUTORIALS_SEARCH_YARD_1',
    'Remember to search prison yard. This is excruciatingly important becaиспользовать as a newbie, you will need all the cash you можешь get. So remember to do this every day to insure you get the most out of this option.');
define('TUTORIALS_TOP_BARS', 'Top bars');
define('TUTORIALS_HP', 'Тыr health in the game. If its low you will easily    die in a combat;');
define('TUTORIALS_ENERGY', 'Ты need energy to do attacks on others Inmates    and to train;');
define('TUTORIALS_AWAKE',
    'The more awake you have, the more you можешь train at gym. To increase awake you need better cells;');
define('TUTORIALS_NERVE', 'Ты использовать nerve to do crimes and mug.');
define('TUTORIALS_BASICS', 'Basics');
define('TUTORIALS_STRENGTH', 'for attacking other Inmates;');
define('TUTORIALS_DEFENCE', 'for defending yourself on fights against the rest of prisoners;');
define('TUTORIALS_SPEED_1', 'If bigger than adversary gives you the first hit on a fight;');
define('TUTORIALS_SPEED_2', 'Helps in the chance of succeeding in doing crimes;');
define('TUTORIALS_SPEED_3', 'Helps in the chance of succeeding busting prisoners out of showers;');
define('TUTORIALS_GANGS', 'Join a gang if you want to be in a rewarding community experience;');
define('TUTORIALS_MARKET',
    'Go to Market for good deals on Defense Armor and Strength Weapons - Panama for your level;');
define('TUTORIALS_WEAPONS', 'Go to Armor or Weapon Stores for Armor and Weapons - Panama for your level;');
define('TUTORIALS_CRIMES', 'Do to earn more exp to level up and some деньги;');
define('TUTORIALS_MUG', 'Mug somebody for some of there cash;');
define('TUTORIALS_ATTACK', 'Атака other Inmates to send them to hospital for 20 minutes and gain exp;');
define('TUTORIALS_SPY', 'Spy on people to see how much they have in bank, and what their skills are.');
define('TUTORIALS_CELL',
    'The better your cell, the more awake you have. The more awake you have, the more you можешь train at gym.');
define('TUTORIALS_LEFT_MENU', 'Left menu');
define('TUTORIALS_MISCELLANEOUS', 'Miscellaneous');
define('TUTORIALS_MISCELLANEOUS_1', 'these links are in your prison - Panama for your level');
define('TUTORIALS_LAND', 'If you want to grow weed for profit, buy some land из real estate agent;');
define('TUTORIALS_STOCKS', 'Make деньги из investing in these;');
define('TUTORIALS_CELL_315', 'Make some деньги in the gambling cell;');
define('TUTORIALS_JOBS', 'Jobs');
define('TUTORIALS_JOBS_1', 'Get jobs из this place;');
define('TUTORIALS_WEED_SEEDS', 'Weed Seeds');
define('TUTORIALS_WEED_SEEDS_1', 'Plant them to grow Weed Plants.');
define('TUTORIALS_COCAINE', 'Cocaine');
define('TUTORIALS_COCAINE_1', 'Increase your Speed by 30% and you можешь buy it for 5k;');
define('TUTORIALS_GENERIC_STEROIDS', 'Generic Steroids');
define('TUTORIALS_GENERIC_STEROIDS_1', 'Increase your Strength by 15% and you можешь buy it for 2.5k;');
define('TUTORIALS_AWAKE_PILLS', 'Awake Pills');
define('TUTORIALS_AWAKE_PILLS_1',
    'Make?s your awake go up to maximum - again the more awake you have, the more you можешь train at gym.');
define('TUTORIALS_MSG_1',
    'If you have any doubts or something to say go to the <span style="color: #FF9933">chat</span> or to the <span style="color: #FF9933">forums</span>. Ты можешь also contact the administrators. <span style="color: #FF9933">Their ids are, 2168 for coder_adm and 2167 for Jordan.</span>');
define('TUTORIALS_MSG_2',
    'Go to prize page to <span style="color: #FF9933">check the amazing prizes we are giving - two PlayStation 3</span>. Check also the referrals page, when <span style="color: #FF9933">you можешь win real деньги just for referring players</span>.');
define('TUTORIALS_MSG_3',
    'Finally <span style="color: #FF9933">go to RPStore and support this game by donating</span>.');
define('TUTORIALS_MSG_4', 'Thanks and have a good time playing!');
define('TUTORIALS_MSG_5', 'Collaboration on tutorial: Bigmac.');

//использоватьrads
define('USERADS_POSTED', 'Ты have successfully posted an ad.');
define('USERADS_REPORTED', 'Ты have successfully reported the advertisment.');
define('USERADS_CONTENT_EMPTY', 'Please enter content.');
define('USERADS_CONTENT_LENGTH', 'Ad content length must be less than %s');
define('USERADS_ADS', 'Заключенный Ads');
define('USERADS_MY_ADS', 'My Заключенный Ads');
define('USERADS_WELCOME', 'Welcome to the prison ads counter !');
define('USERADS_RULES', 'Here are the rules');
define('USERADS_POST_MAX', 'Ты можешь post up to %s ads at the same time.');
define('USERADS_RULES_1',
    'Ads are rolling, meaning you можешь never know for sure when your ad will show or not. They will show in the ads bar, on top of the page.');
define('USERADS_RULES_2',
    'Posting an ad costs $%s for %s minutes. Ты must have the $%s ad fee in your bank account. \'If you choose to remove the ad, you will not get your деньги back, so make sure to type it right the first time.\'');
define('USERADS_POST_NEW', 'Post new Ad');
define('USERADS_CONTENT', 'Ad content');
define('USERADS_POST', 'Post ad');

define('USERADS_AD_LENGTH', 'There must be less then %s characters for an ad.');
define('USERADS_ALL_SLOTS_BOOKED', 'Sorry, all the time slots booked in between.');
define('USERADS_POST_MAX_ADS', 'Ты можешь only post up to %s different ads at the same time.');
define('USERADS_ALREADY_REPORTED', 'Ты already reported.');
define('USERADS_AUTHORIZED_REPORT', 'Ты could not report an authorized Ad.');

define('USERADS_CREATED', 'Time Created');
define('USERADS_FINISHED', 'Time Finished');
define('USERADS_NO_DETECTED', 'There are currently no использоватьr ads reported.');
define('USERADS_NO_ADS', 'Ты do not have any prison ad running. ');
define('USERADS_SURE_TO_DELETE',
    'Are you sure you would like to stop this ad campaign ? Ты will not get the invested деньги back.');
define('USERADS_SURE_TO_AUTHORIZE', 'Are you sure? Ты want to authorize this ad.');
define('USERADS_DELETED', 'Ad successfully deleted.');
define('USERADS_AUTHORIZED', 'Ad successfully authorized.');
define('USERADS_NO_PS_ADS', 'There aren\'t any prison ads running.');

//использоватьrnotes
define('USERNOTES_USER_NOT_EXISTS', 'That использоватьr doesn\'t exist');
define('USERNOTES_USER_MSG_1', 'Тыr RP notes on использоватьrs (total: %s)');
define('USERNOTES_NOTE', 'Note');
define('ACCESS_DENIED', 'Access Denied');

//использоватьrskills
define('USER_SKILL_NOT_EXISTS', 'This skill does not exist.');
define('USER_SKILL_ADDED_POINT', 'Ты have successfully added one point to the skill : %s.');
define('USER_SKILL_ACTIVATED', 'Ты have successfully activated the skill : %s.');
define('SECURITY_SKILLS', 'Security Skills');
define('SECURITY_SKILLS_MSG_1', 'Ты need to travel to last prison before gaining access to those');
define('NO_SECURITY_SKILLS_FOUND', 'No security skills found.');
define('SKILL_NAME', 'Skill Имя');
define('SKILL_DESC', 'Skill Description');
define('SKILL_USAGES', 'Usages');
define('SKILL_TYPE', 'Type');
define('ACTIVE', 'Active');
define('ADD_SECURITY_POINT', 'Add Security Point');
define('ACTIVATE', 'Activate');

//viewgang
define('GANG_NO_SELECTED', 'No gang selected.');
define('GANG_JOIN_REASON', 'Reason to join %s');

//viewpm
define('MAILBOX', 'Mailbox');
define('BACK_TO_MAILBOX', 'Back To Mailbox');

//viewstuff
define('STUFF_STAFF', 'Заключенный Wardens (Staff)');
define('STUFF_OWNER', 'Заключенный Struggle Owner');
define('STUFF_OWNER_PROGRAMMER', 'Заключенный Struggle Owner and Programmer');
define('STUFF_PROGRAMMER', 'Programmer');
define('STUFF_SUPPORT', 'Support');

define('VOTE', 'Vote');
define('VOTE_VOTED_TODAY', 'Ты already voted in this site today...');
define('VOTE_SITE', 'Site');
define('VOTE_RP_POINTS', 'RP Points');
define('VOTE_VOTEDTODAY', 'Voted Today');
define('VOTE_TOPGAMESITES', 'Vote at TopGameSites');
define('VOTE_MMORPG150', 'Vote for us at MMORPG150');
define('VOTE_MPOG', 'MMORPG & MPOG Top list');
define('VOTE_GTOP100', 'Gtop100');
define('VOTE_MMOG', 'MMOG Top 50 Games');
define('VOTE_GAMES200', 'Vote on Oz-games200');
define('VOTE_AFREEGAMING', 'Vote on AFreeGaming');
define('VOTE_MMO', 'Vote on MMORPG & MMO Top 100');
define('VOTE_WITHOUT_RECEIVING_POINTS',
    'If you want to vote <u>without receiving any points</u>...and <u>help prisonstruggle.com grow stronger</u>, follow the links bellow');

//worldstats
define('WORLDSTATS', 'World Stats');
define('WORLDSTATS_NO_REAL_TIME',
    'World stats aren\'t shown in real time, so you might have to wait a little to see your actions reflect on the statistics.');
define('WORLDSTATS_AVERAGE_STRENGTH', 'Average strength per prisoner');
define('WORLDSTATS_AVERAGE_SPEED', 'Average speed per prisoner');
define('WORLDSTATS_AVERAGE_DEFENSE', 'Average defense per prisoner');
define('WORLDSTATS_TOTAL_POINTS', 'Total points in game');
define('WORLDSTATS_AVG_POINTS', 'Average points per prisoner');
define('WORLDSTATS_TOTAL_HAND_MONEY', 'Total hand деньги in game');
define('WORLDSTATS_AVG_HAND_MONEY', 'Average hand деньги per prisoner');
define('WORLDSTATS_TOTAL_BANKED_MONEY', 'Total banked деньги in game');
define('WORLDSTATS_AVG_BANKED_MONEY', 'Average banked деньги per prisoner');
define('WORLDSTATS_TOTAL_DANGEROUS', 'Total dangerous prisoners');
define('WORLDSTATS_HIGH_SEC_LEVEL', 'Highest security level reached');
define('WORLDSTATS_MOST_USED_WEAPON', 'Самый использоватьd weapon');
define('WORLDSTATS_MOST_USED_WEAPON_DESC', 'Самый frequently equipped weapon.');
define('WORLDSTATS_MOST_USED_ARMOR', 'Самый использоватьd armor');
define('WORLDSTATS_MOST_USED_ARMOR_DESC', 'Самый frequently equipped armor.');
define('WORLDSTATS_COMMON_ITEM', 'Самый common item');
define('WORLDSTATS_RARER_ITEM', 'Rarer item');
define('WORLDSTATS_DANGEROUS', 'Самый Dangerous');
define('WORLDSTATS_DANGEROUS_DESC',
    'Заключенныйer with the highest security level + level (Security level counting as 200 normal levels).');
define('WORLDSTATS_RESPECTED', 'Самый Respected');
define('WORLDSTATS_RESPECTED_DESC', 'Заключенныйer with the highest number of RP days.');
define('WORLDSTATS_FASTEST_LEVELER', 'Fastest leveler');
define('WORLDSTATS_FASTEST_LEVELER_DESC',
    'Заключенныйer with the highest number of levels earned per days since signup time.');
define('WORLDSTATS_ATHLETE', 'Athlete');
define('WORLDSTATS_ATHLETE_DESC', 'Заключенныйer with the highest total of strength + defense + speed.');
define('WORLDSTATS_FASTEST_ATHLETE', 'Fastest rising Athlete');
define('WORLDSTATS_FASTEST_ATHLETE_DESC',
    'Заключенныйer with the highest total of strength + defense + speed earned per days since signup time.');
define('WORLDSTATS_CRIME_MASTERMIND', 'Crime mastermind');
define('WORLDSTATS_CRIME_MASTERMIND_DESC',
    'Заключенныйer with the highest difference between succeeded and failed crimes.');
define('WORLDSTATS_WAR_MASTER', 'War master');
define('WORLDSTATS_WAR_MASTER_DESC', 'Заключенныйer with the highest difference between succeeded and failed fights.');
define('WORLDSTATS_RICHIE', 'Richie the rich');
define('WORLDSTATS_RICHIE_DESC', 'Заключенныйer with the highest personal wealth in $.');
define('WORLDSTATS_VAULT_MAN', 'Vault man');
define('WORLDSTATS_VAULT_MAN_DESC', 'Заключенныйer with the highest number of items stored in vault.');
define('WORLDSTATS_LAZY_HEAD', 'Lazy Head');
define('WORLDSTATS_LAZY_HEAD_DESC', 'Заключенныйer owning the highest quantity of пилюляows.');
define('WORLDSTATS_HITCHHIKER', 'Hitchhiker');
define('WORLDSTATS_PRS', 'Public relation specialist');
define('WORLDSTATS_PRS_DESC', 'Заключенныйer which has the highest total of pmails sent + pmails received + friends.');
define('WORLDSTATS_MOST_LIKED', 'Самый Liked');
define('WORLDSTATS_MOST_LIKED_DESC', 'Заключенныйer which has the highest rating by other prisoners.');
define('WORLDSTATS_MOST_HATED', 'Самый Hated');
define('WORLDSTATS_MOST_HATED_DESC', 'Заключенныйer which has the lowest rating by other prisoners.');
define('WORLDSTATS_MOST_RELATED', 'Самый Related');
define('WORLDSTATS_MOST_RELATED_DESC', 'Заключенныйer which is on the highest number of friend lists.');
define('WORLDSTATS_MOST_BLACKLISTED', 'Самый Blacklisted');
define('WORLDSTATS_MOST_BLACKLISTED_DESC', 'Заключенныйer which is on the highest number of enemy lists.');
define('WORLDSTATS_LONE_WOLF', 'Lone wolf');
define('WORLDSTATS_LONE_WOLF_DESC',
    'Заключенныйer which has the lowest total of pmails sent + pmails received + friends.');
define('WORLDSTATS_TOTAL_PRISONERS', 'Total prisoners');
define('WORLDSTATS_TOTAL_RESPECTED_PRISONERS', 'Total respected prisoners');
define('WORLDSTATS_AVG_POPULATION', 'Average population per prison');
define('WORLDSTATS_PREFERRED_JOB', 'Preferred job');
define('WORLDSTATS_PREFERRED_CELL', 'Preferred cell');
define('WORLDSTATS_PREFERRED_SKILL', 'Preferred security skill');
define('WORLDSTATS_TOTAL_CRIMES_COMMITED', 'Total crimes commited');
define('WORLDSTATS_AVG_CRIMES_COMMITED', 'Average crimes commited per prisoner');

//abиспользоватьlog
define('ABUSE_LOG', 'Abиспользовать Log');
define('COM_RECIPIENT', 'Recipient');
define('ABUSE_LOG_READ', 'Read');
define('ABUSE_LOG_GO_BACK', 'Go back, the report ID was not sent.');
define('ABUSE_LOG_ABUSE_REPORTS', 'Current Pmail Abиспользовать Reports');
define('ABUSE_LOG_INCORRECT_REPORT', 'Incorrect report ID.');
define('ABUSE_LOG_WARNING_NO_SWEARING',
    'Warning: No Swearing, no racism, no sexual content, no homophobic comments in game, pmails included. Next time you may be banned.');
define('ABUSE_LOG_USER_BANNED', 'User banned.');
define('GO_BACK', 'Go back');
define('ABUSE_LOG_REPORT_MESSAGE', 'Report Message');
define('ABUSE_LOG_OPEN_REPORT', 'Open Report');
define('ABUSE_LOG_CLOSE_REPORT', 'Close Report');
define('ABUSE_LOG_DELETE_REPORT', 'Delete Report');

//dontplay
define('DONTPLAY_CHECK_USERS', 'Check Users');
define('DONTPLAY_NUM_LOGINS', 'Num Logins');
define('DONTPLAY_TOTAL_STATS', 'Total Stats');
define('DONTPLAY_CHECK_SENDS', 'Check Отправитьs');

//coder
define('CODER_CONTROL_LINKS', 'Control Links');
define('CODER_CHECK_IP', 'Check Ip\'s');
define('CODER_CHECK_IP_DESC',
    'No matter if Inmates has same IP\'s, as long as both players are playing (have good stats) and not sending деньги/points/items between them.');
define('CODER_DONT_PLAY', 'Don\'t Play');
define('CODER_DONT_PLAY_DESC',
    'Show player that were active soon but don\'t have stats - they could be fake accounts to send points/деньги to major account.');
define('CODER_BEST_PLAYER', 'Best Player');
define('CODER_BEST_PLAYER_DESC', 'Show best players on several items (hoиспользовать, деньги, stats, etc.).');
define('CODER_SEE_PLAYER_GENERAL', 'See Player General');
define('CODER_SEE_PLAYER_GENERAL_DESC', 'Show the main table on a использоватьr, put id in из of idu=');
define('CODER_RESET_USER_PASSWORD', 'Reset User Password');
define('CODER_RESET_USER_PASSWORD_DESC', 'Use this to reset the password of использоватьrs');
define('CODER_PAYMENTS', 'Payments summary');
define('CODER_PAYMENTS_DESC',
    'Gives an overview of all payments, with top 25 donators, payments per year / month, etc.');
define('CODER_SUBSCRIPTIONS', 'User subscriptions');
define('CODER_SUBSCRIPTIONS_DESC', 'Shows a listing of subscriptions.');
define('CODER_SUBSCRIPTIONS_PAYMENTS', 'User subscription payments');
define('CODER_SUBSCRIPTIONS_PAYMENTS_DESC', 'Shows a listing of subscription payments.');
define('CODER_BY_NAME', 'By Имя');
define('CODER_BY_ID', 'By Id');

//multi
define('MULTI_SURE_TO_MARK_READ', 'Ты sure you want to mark this log as seen?');
define('MULTI_SURE_TO_BAN_BOTH', 'Ты sure you want ban them both?');
define('MULTI_SURE_TO_BAN_FIRST', 'Ты sure you want ban The first player?');
define('MULTI_VERIFICATION_TOOL', 'Multi Verification Tool');
define('MULTI_GO_HOME', 'Go To Multi Home Page');
define('MULTI_MSG_1',
    '<span style="color: yellow;">Stats, Money Donated and Level</span> are shown caиспользовать players could be on same IP,    but both playing (not beeing multi), so in that case they aren\'t banned.');
define('MULTI_MSG_2', 'Отправитьing 23 points normally its multi.');
define('MULTI_MSG_3',
    'Check the IP for other multi in same IP (when banning other on same IP, copy paste the Case id из the banned page).');
define('MULTI_BAN_BOTH', 'Ban Both');
define('MULTI_BAN_FIRST', 'Ban First');
define('MULTI_CHECK_THEM', 'Check Them');

//payments
define('PAYPAL_PAYMENTS', 'PayPal Payments');
define('YEAR', 'Year');
define('MONTH', 'Month');
define('DAY', 'Day');
define('PLAYER', 'Player');
define('TYPE', 'Type');
define('PAYMENTS_REF', 'Ref');
define('TOP_DONATORS', 'Top 25 Donators');

//errlog
define('ERRLOG_INVALID_TYPE', 'Invalid error type passed.');
define('ERRLOG_FILE_DELETED', 'Error file successfully deleted !');
define('ERRLOG_DELETE_FILE', 'Delete current error log');
define('ERRLOG_NO_ERRORS', 'No errors found.');

//emailconfTelmo
define('CONFIRM_EMAILS', 'Confirm Emails');

//control
define('CONTROL_USER_HAD', 'That использоватьr had %s of those, now they are all gone.');
define('CONTROL_USER_HAD_HAS', 'That использоватьr had %s of those, and now has %s of them.');
define('CONTROL_TAKE_ALL', 'Take All');
define('CONTROL_USERS_ITEMS', '%s\'s Items');
define('CONTROL_CHANGED_MESSAGE', 'Ты have changed the message из the admin.');
define('CONTROL_CHANGED_SERVER_DOWN_TEXT', 'Ты have changed the server down text.');
define('CONTROL_ADDED_RP_DAYS', 'Ты have added %s RP Days to Id-%s.');
define('CONTROL_NOT_FIND_USER', 'Could not find использоватьr %s.');
define('CONTROL_ADDED_POINTS', 'Ты have added %s points to Id-%s');
define('CONTROL_ADDED_MONEY', 'Ты have added $%s деньги to Id-%s');
define('CONTROL_ADDED_BANKMONEY', 'Ты have added $%s bank деньги to Id-%s');
define('CONTROL_PANEL', 'Control Panel');
define('CONTROL_PANEL_TEXT_1',
    'Here you можешь give players points, RP Days, RP Weapons and Armor. In order to ban people you have to do it manually through MySQL or any other database server you использовать.');
define('CONTROL_CHANGE_MESSAGE', 'Change Message From The Admin');
define('CONTROL_CHANGE_SERVER_DOWN_TEXT', 'Change Server Down Text');
define('CONTROL_ADD_RP_DAYS', 'Add RP Days');
define('CONTROL_HOW_MANY_RP_DAYS', 'How Many RP Days');
define('CONTROL_ADD_POINTS', 'Add Points');
define('CONTROL_HOW_MANY_POINTS', 'How Many Points');
define('CONTROL_GIVE_POINTS', 'Give Points');
define('CONTROL_ADD_MONEY', 'Add Money');
define('CONTROL_MONEY_AMOUNT', 'Money Amount');
define('CONTROL_ADD_BANK_MONEY', 'Add Money to Bank');
define('CONTROL_FREEZE_PLAYER', 'Freeze a Player');
define('CONTROL_FREEZE_REASON', 'Reason for Freezing');
define('HOUR', 'hour');
define('HOURS', 'hours');
define('DURATION', 'Duration');
define('CONTROL_UNFREEZE_PLAYER', 'Unfreeze a Player');
define('CONTROL_BAN_PLAYER', 'Ban a Player');
define('CONTROL_BAN_REASON', 'Reason for Banning');
define('CONTROL_UNBAN_PLAYER', 'Unban a Player');
define('CONTROL_BAN_IP', 'Ban an IP');
define('CONTROL_BAN_IP_REASON', 'Reason for Banning');
define('CONTROL_IP', 'IP');
define('CONTROL_UNBAN_IP', 'Unban a IP');
define('CONTROL_GIVE_ADMIN_STATUS', 'Give Admin Status');
define('CONTROL_CHANGE_ADMIN_STATUS', 'Change Admin Status');
define('CONTROL_REVOKE_ADMIN_STATUS', 'Revoke Admin Status');
define('CONTROL_PRESIDENTIAL_ELECTION', 'Presidential Election');
define('CONTROL_ELECT_PRESIDENT', 'Elect President');
define('CONTROL_IMPEACH_PRESIDENT', 'Impeach President');
define('CONTROL_CONGRESSIONAL_ELECTIONS', 'Congressional Elections');
define('CONTROL_ELECT_CONGRESS', 'Elect Congress');
define('CONTROL_IMPEACH_CONGRESS', 'Impeach Congress');
define('CONTROL_LIST_ITEMS', 'List Of All Items');
define('CONTROL_ADD_ITEM_DB', 'Add Новый Item To Database');
define('CONTROL_ITEMNAME', 'itemname');
define('CONTROL_DESCRIPTION', 'description');
define('CONTROL_ITEM_COST', 'cost');
define('CONTROL_ITEM_IMAGE', 'image');
define('CONTROL_ITEM_OFFENSE', 'offense');
define('CONTROL_ITEM_DEFENSE', 'defense');
define('CONTROL_ITEM_HEAL', 'heal');
define('CONTROL_ITEM_BUYABLE', 'buyable');
define('CONTROL_ITEM_LEVEL', 'level');
define('CONTROL_ADD_ITEM', 'Add Item');
define('CONTROL_GIVE_ITEM', 'Give Item');
define('CONTROL_USERNAME', 'Username');
define('CONTROL_ITEM_NUMBER', 'Item Number');
define('CONTROL_TAKE_ITEM', 'Take Item');
define('CONTROL_VIEW_PITEMS', 'View A Player\'s Items');
define('CONTROL_LIST_ITEM', 'List Items');
define('CONTROL_FIND_PLAYERS', 'Find Players');
define('CONTROL_BY_NAME', 'По Имя');
define('CONTROL_BY_ID', 'По Id');
define('CONTROL_MANAGE_REFERRALS', 'Manage Referrals');
define('CONTROL_RULES_ADMINS', 'Rules to Admins');
define('CONTROL_RULES_ADMINS_1', 'Only credit/deny after 5 days of register.');
define('CONTROL_RULES_ADMINS_2', 'Check the IP\'s of both players.');
define('CONTROL_RULES_ADMINS_3', 'Total stats of new player at least 100.');
define('CONTROL_RULES_ADMINS_4', '# of logins of new player at least 8.');
define('CONTROL_RULES_ADMINS_5', 'Last seen at least 2 days ago.');
define('CONTROL_RULES_ADMINS_6',
    'Always give a reason, but a generic one like referal don\'t play or referal on same IP');
define('CONTROL_RULES_ADMINS_7', 'Check email to see if isn\'t strange');
define('CONTROL_NEW_GUY_REFERRED', 'Новый Guy (referred)');
define('CONTROL_OLD_GUY_REFERRER', 'Old Guy (referrer)');
define('CONTROL_DECISION', 'Decision');
define('CONTROL_FIRST_IP', 'First IP');
define('CONTROL_LAST_IP', 'Last IP');
define('CONTROL_LOGINS', 'Logins');
define('CONTROL_LAST_SEEN', 'Last Seen');
define('CONTROL_REGISTER_DATE', 'Register Date');
define('CONTROL_TOTAL_STATS', 'Total Stats');
define('CONTROL_CREDIT', 'Credit');
define('CONTROL_DENY', 'Deny');
define('CONTROL_REASONS', 'Reasons');
define('CONTROL_BANNED_PLAYERS', 'Banned Players');
define('CONTROL_BANNED_IPS', 'Banned IP\'s');
define('CONTROL_PANEL_TEXT_2',
    'Here you можешь activate / disable action tracking for crimes and gym. If action tracking is enabled, every crime and gym will generate a log for admin review.');
define('CONTROL_ACTION_TRACKING', 'Action Tracking');
define('CONTROL_ACTION_TRACKING_ENABLE', 'Enable Action Tracking Logs?');
define('CONTROL_ACTION_TRACKING_ENABLED', 'Ты have enabled the action tracker log из the admin.');
define('CONTROL_ACTION_TRACKING_DISABLED', 'Ты have disabled the action tracker log из the admin.');

//freezes
define('FREEZES_FROZEN_PLAYERS', 'Frozen Players');
define('FREEZES_WHO_FROZE', 'Who Froze');

//использоватьr_payments
define('SUBSCRIPTION_PAYMENTS', 'Subscription Payments');
define('PAYMENT_AMOUNT', 'Payment Amount');
define('PAYMENT_DATE', 'Payment Date');
define('NO_SUBSCRIPTION_PAYMENTS', 'No subscription payments yet, sorry.');

//check
define('SEE_PLAYER', 'See player');
define('CHECK_FROM', 'From');
define('CHECK_SENDS', 'Отправитьs');
define('CHECK_ITEM', 'Item');
define('CHECK_USERS', 'использоватьrs');
define('CHECK_IDS', 'Check id\'s');

//massemail
define('MASSEMAIL_PLAIN_TEXT', 'Mass emails are plain text format.');
define('MASSEMAIL_TIPS',
    'Use {использоватьrname} and  {login} in message content to print использоватьrname and login');
define('MASSEMAIL_SEND_TO_ALL', 'Отправить to ALL Players');
define('MASSEMAIL_SEND_TO_INACTIVE', 'Отправить to Inactive Players');
define('MASSEMAIL_SENT', 'emails were sent');
define('MASSEMAIL_FAIL', 'emails were fail');
define('MASSEMAIL_NO_USERS', 'There are no использоватьrs in the database.');

//seePlayer
define('VALID_REFERRALS', 'Valid Referralsase');
define('DENIED_REFERRALS', 'Denied Referrals');
define('PENDENT_REFERRALS', 'Pendent Referrals');
define('CHOOSE', 'Choose');
define('PLAYERS_FOUND', 'Players found');
define('PLAYER_DETAILS', 'Player Details');
define('REAL_MONEY', 'Real Money');
define('REFERALS', 'Referals');

//использоватьr_subs
define('SUBSCRIPTIONS', 'Subscriptions');
define('SUBSCRIPTION_TYPE', 'Subscription Type');
define('SUBSCRIPTION_STATUS', 'Subscription Status');
define('STARTING_DATE', 'Starting date');
define('ENDING_DATE', 'Ending date');
define('NO_SUBSCRIPTIONS', 'No subscriptions yet, sorry.');

//logscheck
define('SEND_LOGS', 'Отправить Logs');
define('VAULT_LOGS', 'Vault Logs');
define('MUG_LOGS', 'Mug Logs');
define('LOGS_5050', '5050 Logs');
define('MARKET_5050', 'Market Logs');
define('LOGS_SHARES', 'Shares');
define('LOGS_LAND', 'Land');
define('LOGS_EVENTS', 'Events');

define('LOGS_BANS_WARNS', 'Bans/Warns logs');

define('LOGS_BAN_FORUMS', 'Forum bans');
define('LOGS_CHAT_BANS', 'Чат bans');
define('LOGS_WARNS', 'Warns');

define('LOGS_SHOUT_CHAT', 'Shout/Чат Logs');
define('LOGS_SHOUT', 'Shouts');
define('LOGS_CHAT', 'Чат');

define('LOGS_INVENTORY', 'Inventory');
define('LOGS_PAYMENT', 'Payment Logs');

define('LOGS_PAYPAL_PAYMENT', 'Paypal');
define('LOGS_PHONE_PAYMENT', 'Phone');
define('LOGS_REAL_PAYMENT', 'Refer');

define('LOGS_PMAIL', 'Pmail Logs');

define('LOGS_INVALID_TIMESTAMP', 'Specified Timestamp is invalid');
define('LOGS_MUST_ONE_ID', 'At least one ID must be specified');
define('LOGS_ID_INVALID', 'Specified ID To is invalid');
define('LOGS_ID_FORM_INVALID', 'Specified ID From is invalid');
define('LOGS_USER_ID_INVALID', 'Specified User ID is invalid');
define('LOGS_GANG_ID_INVALID', 'Specified Gang ID is invalid');
define('LOGS_RESULT_INVALID', 'Selected Result value is invalid');
define('LOGS_TARGET_USER_ID_INVALID', 'Specified Target User ID is invalid');
define('LOGS_EMPTY_USER_PUT_ID', 'User Put ID можешьnot be empty');
define('LOGS_USER_PUT_ID_INVALID', 'Specified User Put ID is invalid');
define('LOGS_USER_TAKE_ID_INVALID', 'Specified User Take ID is invalid');
define('LOGS_AMOUNT_INVALID', 'Specified Amount is invalid');
define('LOGS_TYPE_INVALID', 'Specified Type is invalid');
define('LOGS_EMPTY_ID', 'ID можешьnot be empty');
define('LOGS_INVALID_ID', 'Specified ID is invalid');
define('LOGS_UNKNOWN_TYPE', 'Unknown logs type');
define('LOGS_PAGES', 'Logs Pages');
define('LOGS_SUB_PAGES', 'Sub Pages');
define('LOGS_ID_PUT', 'ID Put');
define('LOGS_ID_TAKE', 'ID Take');
define('LOGS_AMOUNT', 'Amount');
define('LOGS_TYPE', 'Type');
define('LOGS_TIMESTAMP', 'Timestamp');
define('LOGS_PUT_ID_MUST', 'At least ID Put or both(ID Put, ID Take) must be specified');
define('LOGS_TOTAL_ROWS_FOUND', 'Total Rows Found');
define('LOGS_TOTAL_ROWS_SHOWN', 'Total Rows Shown');
define('LOGS_READABLE_TIME', 'Readable Time');
define('LOGS_READABLE_DATE', 'Readable Date');
define('LOGS_FILTER', 'Filter');
define('LOGS_ONE_FIELD_MUST', 'At least one field(ID, Timestamp) must be specified');
define('LOGS_TARGET_USER_ID', 'Target User ID');
define('LOGS_ONE_ID_MUST', 'At least one ID must be specified');
define('LOGS_ID_TO', 'ID To');
define('LOGS_ID_FROM', 'ID From');
define('LOGS_GANG_ID', 'Gang ID');
define('LOGS_TEXT', 'Text');
define('LOGS_ID_MUST', 'ID must be specified in order to filter');
define('STOCK_LOGS', 'Stock Logs');
define('GROWING', 'Growing');
define('LOGS_REASON', 'Reason');

///использоватьr_tries
define('USERS_TRIES_REFRESHES', 'Users Tries and Refreshes');
define('USERS_TRIES_ALL_SEL_DELETED', 'All selected records deleted successfully.');
define('USERS_TRIES_ALL_DELETED', 'All records deleted successfully.');
define('USERS_TRIES', 'Tries');
define('USERS_REFRESHES', 'Refreshes');
define('DELETE_SELECTED', 'Delete Selected');
define('DELETE_ALL', 'Delete All');
define('SURE_TO_DEL_SELECTED', 'Are you sure. Ты want to delete selected records?');
define('SURE_TO_DEL_ALL', 'Are you sure. Ты want to delete all records?');
define('NO_USER_DETECTED', 'There are currently no использоватьrs detected.');
define('USERS_TRIES_EQ_BOT', 'Equation');
define('USERS_TRIES_NORMAL_BOT', 'Normal');

//telmo
define('QUERY_USERS', 'Query Users');
define('CRIMESUCCEEDED', 'crimesucceeded');
define('CRIMEFAILED', 'crimefailed');
define('BATTLEWON', 'битваwon');
define('BATTLELOST', 'битваlost');
define('RMDAYS', 'rmdays');
define('EQWEAPON', 'eqweapon');
define('EQARMOR', 'eqarmor');
define('REALMONEY', 'realденьги');
define('NUMLOGINS', 'numLogins');
define('SEND_TO', 'send to');
define('RECEIVE_FROM', 'receive из');

//использоватьr_ip_gang
define('USERS_SAME_IP_GANG', 'Users on same IP and in same Gang');
define('RUN_DETECTION_SCRIPT', 'Run detection script');
define('RUNNING_DETECTION_SCRIPT', 'Detecting использоватьrs on same Im in same gang. Please wait....');

//resetpassword
define('RESETPASSWORD_NEW_PWD', 'Ты have successfully changed the password for использоватьr %s, with id= %s, to %s.');
define('RESETPASSWORD_PWD_CHANGED', 'Тыr password has been changed.');
define('RESETPASSWORD_CHECK_USERID', 'There seems to be a problem. Please check использоватьrid!');
define('RESET_USER_PASSWORD', 'Reset User Password');
define('RESETPASSWORD_ENTER_USER_ID_PWD',
    'Please enter the User Id and new password to reset the использоватьr password.');

//AjaxPHP3
define('AJAX_USER_NOT_FOUND', 'User not found!');
define('AJAX_CANNOT_TARGET_YOURSELF', 'Ты можешьnot target yourself.');
define('AJAX_WAITING_FOR_NAME', 'Waiting for name...');
define('AJAX_WAITING', 'Waiting...');

//chat
define('CHAT_LANG', 'Чат');
define('CHAT_BANNED', 'Banned из Чат');
define('CHAT_YOU_BANNED', 'Ты are banned из here %s more days.');
define('CHAT_RULE_1',
    'No Swearing, no racism, no sexual content, no homophobic comments, no religious or political discussions that may offend others sensibility;');
define('CHAT_RULE_2', 'Respect smods and mods. Any problem about warnings/ban or rules talk to coder-adm;');
define('CHAT_RULE_3', 'Please help newbs.');
define('CHAT_SAY', 'Say');
define('CHAT_CHANNEL', 'Channel');

//shoutBox
define('SHOUTBOX_BANNED', 'Banned из Shout Box and Forum');
define('SHOUTBOX_WHAT_TO_DO', 'What are you trying to do?');
define('GO_BACK_TO_SHOUT_BOX', 'Go Back to Shout Box');
define('SHOUTBOX_EDITIED', 'Shout successfully edited...');
define('SHOUTBOX_NEW', 'Отправить a Новый Shout');
define('SHOUTBOX_DELETED', 'Shout successfully deleted...');
define('SHOUTBOX', 'Shout Box');
define('SHOUTBOX_WAIT', 'Ты have to wait some seconds to be able to shout again...');
define('SHOUTBOX_SENT', 'Shout successfully sent...');
define('SHOUTBOX_SEND_ANOTHER', 'Отправить Another Shout');
define('SHOUTBOX_EDIT', 'Edit Shout');
define('SHOUT', 'Shout');
define('SHOUT_CAN_USE', 'Ты можешь also использовать %s');
define('SHOUT_LANG', 'Shout Box - %s');
define('SHOUT_TO', 'Shout to %s');
define('SHOUTS', 'Shouts');
define('USER_HAS_N_SHOUTS', 'This использоватьr has %s shouts');
define('USER_TIME_SENT', 'Time Sent');
define('SHOUT_DELETED_BY_OWNER', 'Shout Deleted by Owner');
define('SHOUT_DELETED_BY_MOD', 'Shout Deleted by MOD');

//daopay
define('DAOPAY_PACK_CREDITED', 'Тыr pack has been successfully credited ! Redirecting to your cell in 2 seconds.');
define('DAOPAY_PIN_INVALID', 'The PIN code you entered is not valid! Error:1xPS');
define('DAOPAY_UNABLE_VALIDATE_PIN', 'Could not validate the PIN code. Error:%s');

//emailconf
define('EMAILCONF_VERIFICATION_SENT',
    'Verification email was re-sent to you. Please <span style="color:red;">check spam/bulk folder</span><br><br>If the email don\'t arrive. In less than 24 hours we will manually confirm your account.<br>Thanks');
define('EMAILCONF_ACT_VERIFIED', 'Тыr account is already verified!<br>Thanks');
define('EMAILCONF_NOT_REGISTERED', 'That email its not registered');
define('PS_HOMEPAGE', 'ЗаключенныйStruggle HomePage');

//captcha
define('CAPTCHA_MSG_1',
    'In order to ensure that you are in fact in front of your PC, we require that you enter the displayed code in the empty box next to it. Thanks for your understanding.');
define('CAPTCHA_MSG_2',
    'Warning: Ты have already made a lot of %s. If you keep on, the administrator will be warned and will investigate your account');
define('CAPTCHAEQ_MSG_1',
    'In order to ensure that you are in fact in front of your PC, we require that you enter the result of the displayed operation in the empty box next to it. Thanks for your understanding.');

//auction
define('AUCTION_GENERIC', 'Generic');
define('AUCTION_DRUGS', 'Drugs');
define('AUCTION_EMPTY_ITEM', 'Auction item must not be empty.');
define('AUCTION_WRONG_WINNERS_NUM', 'Auction winners must be positive numbers.');
define('AUCTION_WRONG_MIN_BID', 'Auction minimum bid must be non zero positive number.');
define('AUCTION_WRONG_TOP_BIDS', 'Auction top bids must be non zero positive number.');
define('AUCTION_WRONG_PERIOD', 'Auction period must be non zero positive number.');
define('AUCTION_INVALID_OWNER', 'Ты are not authorized to complete this action.');
define('AUCTION_PS', 'Заключенный Auction');
define('AUCTION_PS_MENU', 'Заключенный Auction Menu');
define('AUCTIONS', 'Auctions');
define('AUCTION_POST_ITEM', 'Post Item');
define('AUCTION_ADMIN_OPTIONS', 'Admin Options');
define('AUCTION_ITEM', 'Auction Item');
define('AUCTION_ITEM_DESC', 'Item Description');
define('AUCTION_MY_BIDS', 'My Bids');
define('AUCTION_FINISHED_BIDS', 'Finished Bids');
define('AUCTION_PERIOD', 'Time period');
define('AUCTION_NUM_WINNERS', 'Number of winners');
define('AUCTION_REPEAT', 'Delay before restart');
define('AUCTION_MIN_BID', 'Minimum Bid');
define('AUCTION_CURR_BID', 'Highest bid');
define('AUCTION_CURR_BIDDER', 'Highest Bidder');
define('AUCTION_ANONYMOUS', 'Allow anonymous');
define('AUCTION_SAVED', 'Auction saved successfully.');
define('AUCTION_DELETED', 'Auction deleted successfully.');
define('AUCTION_STARTED', 'Started');
define('AUCTION_NO_ACTIVE', 'There is no active auction.');
define('AUCTION_NO_EXPIRED', 'There is no finished auction.');
define('AUCTION_BID', 'Bid');
define('AUCTION_EDIT_BID', 'Edit Bid');
define('AUCTION_SURE_TO_DELETE', 'Are you sure you would like to delete this auction ?');
define('AUCTION_INVALID_ID', 'Invalid auction!');
define('AUCTION_INVALID_BID_AMOUNT', 'Bid amount must not be less then minimum bid amount.');
define('AUCTION_BID_AMOUNT_LESS_LAST', 'Bid amount must not be less then or equal to last bid amount $%s.');
define('AUCTION_BID_AMOUNT', 'Bid Amount');
define('AUCTION_BID_ANONYMOUS', 'Bid anonymous');
define('AUCTION_LAST_BIDDER', 'Last Bidder');
define('AUCTION_WRONG_BID', 'Auction bid must be non zero positive number.');
define('AUCTION_BID_SAVED', 'Тыr bid has been successfully saved.');
define('AUCTION_MY_LAST_BID', 'Тыr Bid');
define('AUCTION_VIEW', 'View Bids');
define('AUCTION_NO_BIDS', 'No one has entered a bid for this auction item yet.');
define('AUCTION_YOU_WIN',
    'Congratulations! Ты won the bid for auction %s. Please click %s here %s to Claim your victory.');
define('AUCTION_AUCTION_EXPIRED_MSG', 'A acution with id: %d is expired. Please click %s here %s to view this bid.');
define('AUCTION_CLAIM_VIC', 'Claim your victory.');
define('AUCTION_NOT_WINNER', 'Sorry, Ты are not valid winner of this bid.');
define('AUCTION_ALREADY_CLAIM', 'Ты already claimed.');
define('AUCTION_CANT_CLAIM', 'Sorry! Ты можешь not claim this bid as it expires the 24 hrs limit.');
define('AUCTION_USER_CLAIMED', '%s has claimed his bid. Please click %s here %s to view details.');
define('AUCTION_CLAIMED', 'Ты have claimed your victory successfully. Ты will get your bid item soon.');
define('AUCTION_FINE_TOOK', 'Ты failed to claim your bid against %s bid %s. $%s fine is taken из your bank.');
define('AUCTION_FINE_FAILED', '%s failed to claim and fine his bid against %s bid %s.');
define('AUCTION_YOU_FINE_FAILED',
    'Ты failed to claim your bid against %s bid %s. Ты were also not having to pay fine. Admin is initimated for this.');
define('AUCTION_TOATL_BIDS', 'Total Bids');
define('AUCTION_REPEATED', 'Repeated auction');
define('AUCTION_NUM_TOP', 'Default Top Average Bids');
define('AUCTION_NUM_TOP_WRONG', 'Average bid should be a non zero positive number.');
define('AUCTION_SETTINGS_SAVED', 'Settings saved successfully.');
define('AUCTION_TOP_BIDS', 'Top %s Average Bids');
define('AUCTION_TOP_BIDS_USED', 'Top Bids Used');
define('WINNER', 'Winner');
define('CLAIMED', 'Claimed');
define('PUNISHED', 'Punished');
define('UNKNOWN_YET', 'Unknown Yet!');

//использоватьr monitor
define('USER_MONITOR', 'Inmate Monitoring');
define('USER_MONITOR_RULES', 'Inmate Monitoring Rules');
define('MONITOR_RULES',
    'Ты можешь hire one of our guards to keep watch on an inmate or entire gang logins. He will send you a pmail whenever the targeted inmate / gang logins.');
define('MONITOR_FOR', 'Ты want to monitor');
define('MONITOR_INMATE', 'An Inmate');
define('MONITOR_A_GANG', 'A Gang');
define('MONITOR_HIRE_FOR_GANG', 'Monitor for your entire gang');
define('MONITOR_FOR_DAYS', 'Duration (in days)');
define('MONITOR_INVALID_DAYS', 'Invalid monitoring days.');
define('MONITOR_INVALID_TARGET', 'Target not specified.');
define('MONITOR_CANT_YOURSELF', 'Ты можешь not monitor yourself.');
define('MONITOR_SAVED', 'Ты have successfully hired the prison guard to monitor inmates.');
define('MONITOR_DELETED', 'Ты have successfully stopped the prison guard to monitor inmates.');
define('MONITOR_ALREADY_EXIST', 'Ты можешьnot start monitoring an inmate or gang you already monitor.');
define('MONITOR_USER', 'User monitored');
define('MONITOR_GANG', 'Gang Monitored');
define('MONITOR_SURE_TO_DELETE', 'Are you sure you would like to stop this monitoring ?');
define('MONITOR_MY', 'My Monitoring');
define('MONITOR_ENTIRE_GANG', 'Entire gang');
define('MONITOR_PMAIL_SUBJECT', 'Monitoring %s : Login');
define('MONITOR_PMAIL', 'A prison guard has notified you that inmate %s has logged in at time %s.');
define('MONITOR_NO_USER', 'There are currently no inmates or gangs monitored');
define('MONITOR_GL_START_SUBJECT', 'Monitoring %s: Started');
define('MONITOR_GL_START_BODY',
    'Тыr gang leader %s has started monitoring %s. Ты will receive login notifications из now on.');
define('MONITOR_GL_STOP_SUBJECT', 'Monitoring %s: Stopped');
define('MONITOR_GL_STOP_BODY',
    'Тыr gang leader %s has stopped monitoring %s. Ты will no longer receive login notifications.');
define('YOURSELF', 'Тыrself');
define('GANG_NOT_FOUND', 'Gang not found');
define('GANG_CANT_TARGET', 'Ты можешьnot target your gang.');

//Max Points notifications
define('MAXPOINTS_USER_NOTIFY',
    'The pack \'%s\' из rp store couldn\'t be entirely credited to you, becaиспользовать you hit the %s points limitation. Ты were only credited with %s points.');
define('MAXPOINTS_ADMIN_NOTIFY',
    'The pack \'%s\' из rp store couldn\'t be entirely credited to id %s, becaиспользовать he hits the %s points limitation. He was only credited with %s points.');
define('MAXPOINTS_LOTTERY_NOTIFY',
    'The points lottery winnings were capped to your %s points limit. Ты можешьnot have more than %s points.');
define('MAXPOINTS_USER_NOTIFY_1',
    'The points winnings were capped to your %s points limit. Ты можешьnot have more than %s points.');

//Personal shop
define('PSHOP_HEAD', 'Заключенный Personal Shop');
define('PSHOP_TEXT1', 'Welcome to personal shop.');
define('PSHOP_ADD_ITEM', 'Add item to personal shop');
define('PSHOP_ITEM_SELL', 'Please select item to sell');
define('PSHOP_NUM_ITEMS', 'Number of items');
define('PSHOP_COST', 'Cost');

//skills

define('SKILLS_UNDERWORLD_CONNECTIONS', 'Underworld connections');
define('SKILLS_UNDERWORLD_CONNECTIONS_DESC',
    'Ты have the possibility to do 3 attacks or 3 mugs per day per point invested on anyone in any prison. To do so, activate the skill, and next attack or mug you perform will work even if the inmate is in a different prison.');
define('SKILLS_GUARD_CONNECTIONS', 'Guard connections');
define('SKILLS_GUARD_CONNECTIONS_DESC',
    'Guard protections you использовать last 1 more hour per point invested, every time you activate the skill. Ты можешь activate the skill one time per day.');
define('SKILLS_CRIPPLING_STRIKES', 'Crippling Strikes');
define('SKILLS_CRIPPLING_STRIKES_DESC',
    'Inmates you attack stay in hospital for 10 more minutes per point invested each time you attack and beat them.');
define('SKILLS_WARLORD', 'Warlord');
define('SKILLS_WARLORD_DESC',
    'During wars, attacked players contribute for 1.5 x base war points (base skill with 1 point) + 0.5x per additional point.');
define('SKILLS_CORRUPT_WARDEN', 'Corrupt Warden');
define('SKILLS_CORRUPT_WARDEN_DESC', 'Raise the bank limit by $200,000,000 per point invested.');

//support_roles
define('SKILLS_PRISON_SECRETARY', 'Заключенный Secretary');
define('SKILLS_PRISON_EXPERT', 'Заключенный Expert');
define('SKILLS_PRISON_MANAGER', 'Заключенный Manager');

define('HOSP_TIP', '%s%s%s left');
define('HOSPITAL_PROFILE', 'This user is on Hospital, %s%s%s left');
define('DRUGS_CONVERT_MARIJUANA', 'Convert All Weed to Points');
define('TRADE_MARIJUANA_FAILED', 'You do not have enough marijuana for the conversion (minimum: %s)');
define('TRADE_MARIJUANA_SUCESS', 'You successfully converted %s marijuana units to %s point(s).');
define('TOTAL_MARIJUANA', 'Total Marijuana: %s');
define('TRADE_MARIJUANA_CONFIRMATION', 'Are you sure you would like to convert all your weed to points ?');
