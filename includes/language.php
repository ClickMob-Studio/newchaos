<?php
$addons->get_hooks(
    array(),
    array(
        'page'     => 'includes/language.php',
        'location'  => 'definitions'
    )
);

// More
ops_define('STATS_MSWG_FILE_SIZE', 'File size is too big');


// All Page Headings
// -----------------------------------------------------------

ops_define('HOME', 'Welcome To ' . TITLE); // TITLE should be ops_defined as a constant
ops_define('LOGIN', 'Login');
ops_define('CREATE', 'Create Player');
ops_define('LOBBY', 'Game Lobby');
ops_define('RANKINGS', 'Player Rankings');
ops_define('MY_PLAYER', 'My Player');
ops_define('RULES', 'Poker Rules');
ops_define('FAQ', 'Frequently Asked Questions');
ops_define('ADMIN', 'Admin');
ops_define('LOGOUT', 'Log Out');
ops_define('SITOUT', 'Sit Out Page');

// Main Menu
// -----------------------------------------------------------

ops_define('MENU_HOME', 'Home');
ops_define('MENU_LOGIN', 'Player Login');
ops_define('MENU_CREATE', 'Create Player');
ops_define('MENU_LOBBY', 'Game Lobby');
ops_define('MENU_RANKINGS', 'Player Rankings');
ops_define('MENU_MYPLAYER', 'My Player');
ops_define('MENU_RULES', 'Poker Rules');
ops_define('MENU_FAQ', 'FAQ');
ops_define('MENU_ADMIN', 'Admin');
ops_define('MENU_LOGOUT', 'Log Out');

// Table Types & Poker Page
// -----------------------------------------------------------

ops_define('SITNGO', 'Sit \'n Go');
ops_define('TOURNAMENT', 'Tournament');
ops_define('DEALER_INFO', 'DEALER INFORMATION');
ops_define('TABLEPOT', 'TABLEPOT:');
ops_define('BUTTON_LEAVE', 'LEAVE TABLE');
ops_define('BUTTON_SEND', 'Send');

// Top 6 players mod
// -----------------------------------------------------------

ops_define('PLACE_POSI_1', 'st');
ops_define('PLACE_POSI_2', 'nd');
ops_define('PLACE_POSI_3', 'rd');
ops_define('PLACE_POSI', 'th');
ops_define('PLACE', 'Place');

// Login Page
// -----------------------------------------------------------

ops_define('BOX_LOGIN', 'Player Login');
ops_define('LOGIN_USER', 'Player Name:');
ops_define('LOGIN_PWD', 'Password:');
ops_define('BUTTON_LOGIN', 'Login');
ops_define('LOGIN_NEW_PLAYER', 'Click here to create a new player');
ops_define('LOGIN_MSG_APPROVAL', 'Account pending approval');
ops_define('LOGIN_MSG_BANNED', 'This account has been banned!');
ops_define('LOGIN_MSG_INVALID', 'Invalid Login!');

// Sitout Page
// -----------------------------------------------------------

ops_define('SITOUT_TIMER', 'Sit Out Timer');

// Create Player Page
// -----------------------------------------------------------

ops_define('BOX_CREATE_NEW_PLAYER', 'Create Your Player');
ops_define('BOX_CREATE_NEW_AVATAR', 'Choose Your Avatar');
ops_define('CREATE_PLAYER_NAME', 'Player Name:');
ops_define('CREATE_PLAYER_PWD', 'Password:');
ops_define('CREATE_PLAYER_CONFIRM', 'Confirm:');
ops_define('CREATE_PLAYER_EMAIL', 'Email Address:');
ops_define('CREATE_PLAYER_CHAR_LIMIT', '[ 5-10 chars ]');
ops_define('CREATE_MSG_IP_BANNED', 'Your IP Address is banned!');
ops_define('CREATE_MSG_MISSING_DATA', 'Missing fields, please try again.');
ops_define('CREATE_MSG_AUTHENTICATION_ERROR', 'Authentication Error!');
ops_define('CREATE_MSG_ALREADY_CREATED', 'You have already created a player!');
ops_define('CREATE_MSG_INVALID_EMAIL', 'This email address is not valid.');
ops_define('CREATE_MSG_USERNAME_TAKEN', 'Username already taken. Please try again.');
ops_define('CREATE_MSG_USERNAME_MWCHECK', 'Username has too many m\'s or w\'s in it!');
ops_define('CREATE_MSG_USERNAME_CHARS', 'Usernames can contain letters, numbers and underscores.');
ops_define('CREATE_MSG_USERNAME_LENGTH', 'Your username must be 5-10 characters long.');
ops_define('CREATE_MSG_PASSWORD_CHARS', 'Passwords can contain letters, numbers and underscores.');
ops_define('CREATE_MSG_PASSWORD_LENGTH', 'Your password must be 5-10 characters long.');
ops_define('CREATE_MSG_PASSWORD_CHECK', 'Your password and confirmation must match!');
ops_define('CREATE_MSG_CHOOSE_AVATAR', 'Please select an avatar');
ops_define('CREATE_APPROVAL_EMAIL_CONTENT', 'Thank you for applying to join our poker game. Please click the link to activate your player:');
ops_define('CREATE_APPROVAL_EMAIL_ALERT', 'An activation email has been sent to the address you gave us.');
ops_define('CREATE_PLAYER_SUBMIT_LABEL', 'Create Account');
ops_define('BUTTON_SUBMIT', 'Submit');

// Game Lobby Page
// -----------------------------------------------------------

ops_define('TABLE_HEADING_NAME', 'Table');
ops_define('TABLE_HEADING_PLAYERS', 'Players');
ops_define('TABLE_HEADING_TYPE', 'Type');
ops_define('TABLE_HEADING_BUYIN', 'Buy In');
ops_define('TABLE_HEADING_SMALL_BLINDS', 'Small Blinds');
ops_define('TABLE_HEADING_BIG_BLINDS', 'Big Blinds');
ops_define('TABLE_HEADING_STATUS', 'Table Status');
ops_define('NEW_GAME', 'New Game');
ops_define('PLAYING', 'Playing');

// My Player & Player Rankings Pages
// -----------------------------------------------------------

ops_define('PLAYER_PROFILE', 'Player Profile');
ops_define('PLAYER_IS_BROKE', 'Your Player Is Broke!!');
ops_define('PLAYER_STATS', 'Player Statistics:');
ops_define('PLAYER_CHOOSE_AVATAR', 'Choose Avatar');
ops_define('PLAYER_CHANGE_PWD', 'Change Password');
ops_define('BOX_GAME_STATS', 'Answer:');
ops_define('BOX_MOVE_STATS', 'Administrator');
ops_define('BOX_HAND_STATS', 'VIP Account');
ops_define('BOX_FOLD_STATS', 'Terms & Conditions');
ops_define('BOX_STD_AVATARS', 'Standard Avatars');
ops_define('BOX_CUSTOM_AVATARS', 'Custom Avatars');
ops_define('STATS_GAME', 'Game Statistics');
ops_define('STATS_HAND', 'Hand Statistics');
ops_define('STATS_MOVE', 'Move Statistics');
ops_define('STATS_FOLD', 'Fold Statistics');
ops_define('STATS_PLAYER_NAME', 'Player Name:');
ops_define('STATS_PLAYER_RANKING', 'Player Ranking:');
ops_define('STATS_PLAYER_CREATED', 'Player Created:');
ops_define('STATS_PLAYER_BANKROLL', 'Bankroll:');
ops_define('STATS_PLAYER_LOGIN', 'Last login:');
ops_define('STATS_PLAYER_GAMES_PLAYED', 'Games Played:');
ops_define('STATS_PLAYER_TOURNAMENTS_PLAYED', 'Tournaments Played:');
ops_define('STATS_PLAYER_TOURNAMENTS_WON', 'Tournaments Won:');
ops_define('STATS_PLAYER_TOURNAMENTS_RATIO', 'Tournament Win Ratio:');
ops_define('STATS_PLAYER_HANDS_PLAYED', 'Hands Played:');
ops_define('STATS_PLAYER_HANDS_WON', 'Hands Won:');
ops_define('STATS_PLAYER_HAND_RATIO', 'Hand Win Ratio:');
ops_define('STATS_PLAYER_FOLD_RATIO', 'Fold Ratio:');
ops_define('STATS_PLAYER_CHECK_RATIO', 'Check Ratio:');
ops_define('STATS_PLAYER_CALL_RATIO', 'Call Ratio:');
ops_define('STATS_PLAYER_RAISE_RATIO', 'Raise Ratio:');
ops_define('STATS_PLAYER_ALLIN_RATIO', 'All In Ratio:');
ops_define('STATS_PLAYER_FOLD_PREFLOP', 'Fold Pre-Flop:');
ops_define('STATS_PLAYER_FOLD_FLOP', 'Fold After Flop');
ops_define('STATS_PLAYER_FOLD_TURN', 'Fold After Turn:');
ops_define('STATS_PLAYER_FOLD_RIVER', 'Fold After River:');
ops_define('STATS_PLAYER_OLD_PWD', 'Old Password:');
ops_define('STATS_PLAYER_NEW_PWD', 'New Password:');
ops_define('STATS_PLAYER_CONFIRM_PWD', 'Confirm Password:');
ops_define('STATS_PLAYER_PWD_CHAR_LIMIT', '[ 5-10 chars ]');
ops_define('BUTTON_STATS_PLAYER_CREDIT', 'Click here to renew your initial credit');
ops_define('BUTTON_UPLOAD', 'Upload');
ops_define('STATS_MSG_FILE_FORMAT', 'Your image must be less than or equal to 2mb!');
ops_define('STATS_MSG_MISSING_DATA', 'Your image must be in jpg format!');
ops_define('STATS_MSG_PWD_CHARS', 'Passwords can only contain letters and numbers.');
ops_define('STATS_MSG_PWD_LENGTH', 'Your password must be 5-10 chars long.');
ops_define('STATS_MSG_PWD_CONFIRM', 'Your new password and confirm fields must match.');
ops_define('STATS_MSG_PWD_INCORRECT', 'Your old password was incorrect!');

// Admin Panel
// -----------------------------------------------------------

ops_define('ADMIN_MANAGE_TABLES', 'Tables');
ops_define('ADMIN_MANAGE_MEMBERS', 'Members');
ops_define('ADMIN_MANAGE_ADDONS', 'Addons');
ops_define('ADMIN_MANAGE_THEMES', 'Themes');
ops_define('ADMIN_MANAGE_SETTINGS', 'Settings');
ops_define('ADMIN_MANAGE_STYLES', 'Table Styles');
ops_define('ADMIN_SETTINGS_UPDATED', 'Your game settings have been updated!');
ops_define('ADMIN_GENERAL', 'General Settings');
ops_define('ADMIN_SETTINGS_TITLE', 'Browser Page Title:');
ops_define('ADMIN_SETTINGS_EMAIL', 'Require Email Address:');
ops_define('ADMIN_SETTINGS_APPROVAL', 'Approval Mode:');
ops_define('ADMIN_SETTINGS_IPCHECK', 'IP Check:');
ops_define('ADMIN_SETTINGS_LOGIN', 'Bypass Login:');
ops_define('ADMIN_SETTINGS_SESSNAME', 'Session Name:');
ops_define('ADMIN_SETTINGS_AUTODELETE', 'Auto Delete Players:');
ops_define('ADMIN_SETTINGS_STAKESIZE', 'Server Stake Size:');
ops_define('ADMIN_SETTINGS_BROKE_BUTTON', '"Your Broke" Button:');
ops_define('ADMIN_TIMER', 'Timer Settings');
ops_define('ADMIN_SETTINGS_KICK', 'Kick Timer:');
ops_define('ADMIN_SETTINGS_MOVE', 'Move Timer:');
ops_define('ADMIN_SETTINGS_SHOWDOWN', 'Showdown Timer:');
ops_define('ADMIN_SETTINGS_SITOUT', 'Sit Out Timer:');
ops_define('ADMIN_SETTINGS_DISCONNECT', 'Disconnect Timer:');
ops_define('ADMIN_SETTINGS_TITLE_HELP', 'This title will appear in your web browsers page title.');
ops_define('ADMIN_SETTINGS_EMAIL_HELP', 'Select if members need to provide an email address when signing up.');
ops_define('ADMIN_SETTINGS_APPROVAL_HELP', 'Select automatic, email verification or admin approval.');
ops_define('ADMIN_SETTINGS_IPCHECK_HELP', 'Prevent multiple players with identical IP addesses playing at the same table.');
ops_define('ADMIN_SETTINGS_LOGIN_HELP', 'Switch this on if you are using your own session based login system.');
ops_define('ADMIN_SETTINGS_SESSNAME_HELP', 'Your identifying session name from your own login system.');
ops_define('ADMIN_SETTINGS_AUTODELETE_HELP', 'Select if you want the system to delete inactive players.');
ops_define('ADMIN_SETTINGS_STAKESIZE_HELP', 'Switch the server stakes size from tiny stakes to high rollers .');
ops_define('ADMIN_SETTINGS_BROKE_BUTTON_HELP', '"Turn on/off "Your Broke" module and initial free game stake.');
ops_define('ADMIN_SETTINGS_KICK_HELP', 'Controls kicking players repeatedly failing to take their turn.');
ops_define('ADMIN_SETTINGS_MOVE_HELP', 'Controls the time a player has to make their move.');
ops_define('ADMIN_SETTINGS_SHOWDOWN_HELP', 'Controls the time a showdown hand will be displayed for.');
ops_define('ADMIN_SETTINGS_SITOUT_HELP', 'Controls the length of stay on the sit out page.');
ops_define('ADMIN_SETTINGS_DISCONNECT_HELP', 'Controls the time before kicking disconnected players.');
ops_define('BUTTON_SAVE_SETTINGS', 'Save Settings');
ops_define('ADMIN_MEMBERS_NAME', 'Player');
ops_define('ADMIN_MEMBERS_RANK', 'Rank');
ops_define('ADMIN_MEMBERS_EMAIL', 'Email');
ops_define('ADMIN_MEMBERS_CREATED', 'Created');
ops_define('ADMIN_MEMBERS_IPADDRESS', 'IP Address');
ops_define('ADMIN_MEMBERS_APPROVE', 'Status');
ops_define('ADMIN_MEMBERS_BAN', 'Ban');
ops_define('ADMIN_MEMBERS_DELETE', 'Delete');
ops_define('ADMIN_MEMBERS_RESET_STATS', 'Stats');
ops_define('BUTTON_APPROVE', 'Approve');
ops_define('BUTTON_BAN', 'Ban');
ops_define('BUTTON_UNBAN', 'Unban');
ops_define('BUTTON_DELETE', 'Delete');
ops_define('BUTTON_RESET', 'Reset');
ops_define('BUTTON_CREATE_TABLE', 'Create Table');
ops_define('BUTTON_INSTALL', 'Install');
ops_define('ADMIN_TABLES_NAME', 'Table Name');
ops_define('ADMIN_TABLES_TYPE', 'Table Type');
ops_define('ADMIN_TABLES_GAME', 'Style');
ops_define('ADMIN_TABLES_MIN', 'Minimum Buyin');
ops_define('ADMIN_TABLES_MAX', 'Maximum Buyin');
ops_define('ADMIN_TABLES_STYLE', 'Table Style');
ops_define('ADMIN_TABLES_DELETE', 'Delete');
ops_define('ADMIN_TABLES_OPTIONS', 'Options');
ops_define('ADMIN_TABLES_BB', 'Big blind');
ops_define('ADMIN_TABLES_SB', 'Small blind');
ops_define('BUTTON_SAVE', 'Save');
ops_define('BUTTON_BACK', 'Back');
ops_define('ADMIN_TOURNAMENT_TICKET', 'Ticket value');
ops_define('ADMIN_TOURNAMENT_RAKE', 'Rake value');
ops_define('ADMIN_TOURNAMENT_PRIZE_1', 'Prize 1');
ops_define('ADMIN_TOURNAMENT_PRIZE_2', 'Prize 2');
ops_define('ADMIN_TOURNAMENT_PRIZE_3', 'Prize 3');
ops_define('ADMIN_TOURNAMENT_START_DATE', 'Start date and time');
ops_define('ADMIN_STYLES_INSTALLED', 'Installed Table Styles');
ops_define('ADMIN_STYLES_PREVIEW', 'Style Preview');
ops_define('ADMIN_STYLES_NEW_NAME', 'New Style Name');
ops_define('ADMIN_STYLES_CODE', 'Validation Code');
ops_define('ADMIN_MSG_STYLE_INSTALLED', 'This style has already been installed!');
ops_define('ADMIN_MSG_MISSING_DATA', 'Missing data! Please try again.');
ops_define('ADMIN_MSG_INVALID_CODE', 'Invalid style name or license code!');

// Poker Game Language
// -----------------------------------------------------------

ops_define('GAME_LOADING', 'Loading...');
ops_define('GAME_PLAYER_BUYS_IN', 'buys in for');
ops_define('INSUFFICIENT_BANKROLL_SITNGO', 'Your bank roll is not high enough to play on this table!');
ops_define('INSUFFICIENT_BANKROLL_TOURNAMENT', 'Your bank roll is not high enough to play in this tournament!');
ops_define('GAME_STARTING', 'game starting...');
ops_define('GAME_PLAYER_FOLDS', 'folds');
ops_define('GAME_PLAYER_CALLS', 'calls');
ops_define('GAME_PLAYER_CHECKS', 'checks');
ops_define('GAME_PLAYER_RAISES', 'raises');
ops_define('GAME_PLAYER_GOES_ALLIN', 'goes all in!');
ops_define('GAME_PLAYER_POT', 'POT:');
ops_define('GAME_MSG_WON_TOURNAMENT', 'won the last tournament');
ops_define('GAME_MSG_LOST_CONNECTION', 'has lost connection and leaves the table');
ops_define('GAME_MSG_PLAYER_BUSTED', 'has busted and leaves the table');
ops_define('GAME_MSG_PLAYERS_JOINING', 'players joining...');
ops_define('GAME_MSG_LETS_GO', 'Lets go!');
ops_define('GAME_MSG_CHIP_LEADER', 'is chip leader');
ops_define('GAME_MSG_DEALER_BUTTON', 'has the dealer button');
ops_define('GAME_MSG_DEAL_CARDS', 'Dealing the holecards...');
ops_define('GAME_MSG_DEAL_FLOP', 'Dealing the flop...');
ops_define('GAME_MSG_DEAL_TURN', 'Dealing the turn...');
ops_define('GAME_MSG_SHOWDOWN', 'SHOWDOWN!');
ops_define('GAME_MSG_ALLFOLD', 'wins, everyone folded');
ops_define('GAME_MSG_PLAYER_ALLIN', 'is all in');
ops_define('GAME_MSG_DEAL_RIVER', 'Dealing the river...');
ops_define('GAME_MSG_SMALL_BLIND', 'posts small blind');
ops_define('GAME_MSG_BIG_BLIND', 'posts big blind');
ops_define('GAME_MSG_SPLIT_POT', 'split pot');
ops_define('GAME_MSG_SPLIT_POT_RESULT', 'The pot is split between the players who have the best');
ops_define('GAME_MSG_WINNING_HAND', 'Winning hand:');
ops_define('GAME_MSG_PROCESSING', 'Processing...');
ops_define('BUTTON_START', 'Start Game');
ops_define('BUTTON_CALL', 'Call');
ops_define('BUTTON_CHECK', 'Check');
ops_define('BUTTON_FOLD', 'Fold');
ops_define('BUTTON_BET', 'Bet');
ops_define('BUTTON_ALLIN', 'All In');
ops_define('WIN_PAIR', 'pair of'); // e.g. user wins with a pair of 9's
ops_define('WIN_2PAIR', '2 pair'); // e.g. user wins 2 pair 3's & 8's
ops_define('WIN_FULLHOUSE', 'full house'); // e.g. user wins with a full house
ops_define('WIN_SETOF3', 'a set of'); // e.g. user wins with a set of 3's
ops_define('WIN_SETOF4', 'all the'); // e.g. user wins with all the J's
ops_define('WIN_FLUSH', 'high flush'); // e.g. user wins with a K high flush
ops_define('WIN_STRAIGHT_FLUSH', 'straight flush'); // e.g. user wins with a K high straight flush
ops_define('WIN_ROYALFLUSH', 'royal flush'); // e.g. user wins with a royal flush
ops_define('WIN_STRAIGHT', 'high straight'); // e.g. user wins with a J high straight
ops_define('WIN_LOW_STRAIGHT', 'low straight'); // e.g. user wins with a low straight
ops_define('WIN_HIGHCARD', 'highcard'); // e.g. user wins with a k highcard
ops_define('BUTTON_SMALLSCREEN', 'Small Screen'); // Switch to Small Screen
ops_define('BUTTON_WIDESCREEN', 'Wide Screen'); // Switch to Wide Screen
ops_define('GAME_TEXAS', "Money");
ops_define('GAME_TEXASPOINTS', "Points");
ops_define('GAME_OMAHA', "Omaha Hold em");
ops_define('MONEY_PREFIX_LABEL', 'Money Prefix:');
ops_define('MONEY_PREFIX_LABEL_HELP', 'The character that appears before the pot/bet number');
ops_define('MONEY_DECIMAL_LABEL', 'Money Decimal:');
ops_define('MONEY_DECIMAL_LABEL_HELP', 'The decimal character for money');
ops_define('MONEY_THOUSAND_LABEL', 'Money Thousand:');
ops_define('MONEY_THOUSAND_LABEL_HELP', 'The decimal character for each thousand iteration of money');
ops_define('ADMIN_USERS_LABEL', 'Admin Users');
ops_define('ADMIN_USERS_LABEL_HELP', 'Usernames of admins (separated by comma)');
ops_define('REG_WINPOT_LABEL', 'Registration Chips');
ops_define('REG_WINPOT_LABEL_HELP', 'The amount of chips a user gets after creating a new account.');

function ops_define($label, $definition)
{
	if (! defined($label))
	{
		define($label, $definition);
		return true;
	}

	return false;
}
?>