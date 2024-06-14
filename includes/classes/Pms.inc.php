<?php

/**
 * discription: This class is used to manage Pms.
 *
 * @author: Harish<harish282@gmail.com>
 * @name: Pms
 * @package: includes
 * @subpackage: classes
 * @final: Final
 * @access: Public
 * @copyright: icecubegaming <http://www.icecubegaming.com>
 */
final class Pms extends BaseObject
{
    const INBOX = 1; //box =1 is for inbox
    const SAVED = 2; //box=2 is for saved mails
    const GANG_MASS = 3; //box=3 is for gang mass mails
    const MODERATOR = 4; //box=4 is for moderator mails

    public static $idField = 'id'; //id field
    public static $dataTable = 'pms'; // table implemented

    /**
     * Constructor.
     */
    public function __construct()
    {
    }

    public static function Seen(User $user, $id)
    {
        DBi::$conn->query('UPDATE `pms` SET `viewed` = "2" WHERE `id`="' . $id . '" AND `to` ="' . $user->id . '"');

        return true;
    }

    public static function filterwords($text, $user)
    {
        $filterWords = ['4r5e', '5h1t', '5hit', 'a55', 'anal', 'anus', 'ar5e', 'arrse', 'arse', 'ass-fucker', 'assfucker', 'assfukka', 'asshole', 'assholes', 'asswhole', 'a_s_s', 'b!tch', 'b00bs', 'b17ch', 'b1tch', 'ballbag', 'balls', 'ballsack', 'bastard', 'beastial', 'beastiality', 'bellend', 'bestial', 'bestiality', 'bi+ch', 'biatch', 'bitch', 'bitcher', 'bitchers', 'bitches', 'bitchin', 'bitching', 'bloody', 'blow job', 'blowjob', 'blowjobs', 'boiolas', 'bollock', 'bollok', 'boner', 'boob', 'boobs', 'booobs', 'boooobs', 'booooobs', 'booooooobs', 'breasts', 'buceta', 'bugger', 'bum', 'bunny fucker', 'butt', 'butthole', 'buttmuch', 'buttplug', 'c0ck', 'c0cksucker', 'carpet muncher', 'cawk', 'chink', 'cipa', 'cl1t', 'clit', 'clitoris', 'clits', 'cnut', 'cock', 'cock-sucker', 'cockface', 'cockhead', 'cockmunch', 'cockmuncher', 'cocks', 'cocksuck', 'cocksucked', 'cocksucker', 'cocksucking', 'cocksucks', 'cocksuka', 'cocksukka', 'cok', 'cokmuncher', 'coksucka', 'coon', 'cox', 'crap', 'cum', 'cummer', 'cumming', 'cums', 'cumshot', 'cunilingus', 'cunillingus', 'cunnilingus', 'cunt', 'cuntlick', 'cuntlicker', 'cuntlicking', 'cunts', 'cyalis', 'cyberfuc', 'cyberfuck', 'cyberfucked', 'cyberfucker', 'cyberfuckers', 'cyberfucking', 'd1ck', 'damn', 'dick', 'dickhead', 'dildo', 'dildos', 'dink', 'dinks', 'dirsa', 'dlck', 'dog-fucker', 'doggin', 'dogging', 'donkeyribber', 'doosh', 'duche', 'dyke', 'ejaculate', 'ejaculated', 'ejaculates', 'ejaculating', 'ejaculatings', 'ejaculation', 'ejakulate', 'f u c k', 'f u c k e r', 'f4nny', 'fag', 'fagging', 'faggitt', 'faggot', 'faggs', 'fagot', 'fagots', 'fags', 'fanny', 'fannyflaps', 'fannyfucker', 'fanyy', 'fatass', 'fcuk', 'fcuker', 'fcuking', 'feck', 'fecker', 'felching', 'fellate', 'fellatio', 'fingerfuck', 'fingerfucked', 'fingerfucker', 'fingerfuckers', 'fingerfucking', 'fingerfucks', 'fistfuck', 'fistfucked', 'fistfucker', 'fistfuckers', 'fistfucking', 'fistfuckings', 'fistfucks', 'flange', 'fook', 'fooker', 'fuck', 'fucka', 'fucked', 'fucker', 'fuckers', 'fuckhead', 'fuckheads', 'fuckin', 'fucking', 'fuckings', 'fuckingshitmotherfucker', 'fuckme', 'fucks', 'fuckwhit', 'fuckwit', 'fudge packer', 'fudgepacker', 'fuk', 'fuker', 'fukker', 'fukkin', 'fuks', 'fukwhit', 'fukwit', 'fux', 'fux0r', 'f_u_c_k', 'gangbang', 'gangbanged', 'gangbangs', 'gaylord', 'gaysex', 'goatse', 'God', 'god-dam', 'god-damned', 'goddamn', 'goddamned', 'hardcoresex', 'hell', 'heshe', 'hoar', 'hoare', 'hoer', 'homo', 'hore', 'horniest', 'horny', 'hotsex', 'jack-off', 'jackoff', 'jap', 'jerk-off', 'jism', 'jiz', 'jizm', 'jizz', 'kawk', 'knob', 'knobead', 'knobed', 'knobend', 'knobhead', 'knobjocky', 'knobjokey', 'kock', 'kondum', 'kondums', 'kum', 'kummer', 'kumming', 'kums', 'kunilingus', 'l3i+ch', 'l3itch', 'labia', 'lust', 'lusting', 'm0f0', 'm0fo', 'm45terbate', 'ma5terb8', 'ma5terbate', 'masochist', 'master-bate', 'masterb8', 'masterbat*', 'masterbat3', 'masterbate', 'masterbation', 'masterbations', 'masturbate', 'mo-fo', 'mof0', 'mofo', 'mothafuck', 'mothafucka', 'mothafuckas', 'mothafuckaz', 'mothafucked', 'mothafucker', 'mothafuckers', 'mothafuckin', 'mothafucking', 'mothafuckings', 'mothafucks', 'mother fucker', 'motherfuck', 'motherfucked', 'motherfucker', 'motherfuckers', 'motherfuckin', 'motherfucking', 'motherfuckings', 'motherfuckka', 'motherfucks', 'muff', 'mutha', 'muthafecker', 'muthafuckker', 'muther', 'mutherfucker', 'n1gga', 'n1gger', 'nazi', 'nigg3r', 'nigg4h', 'nigga', 'niggah', 'niggas', 'niggaz', 'nigger', 'niggers', 'nob', 'nob jokey', 'nobhead', 'nobjocky', 'nobjokey', 'numbnuts', 'nutsack', 'orgasim', 'orgasims', 'orgasm', 'orgasms', 'p0rn', 'pawn', 'pecker', 'penis', 'penisfucker', 'phonesex', 'phuck', 'phuk', 'phuked', 'phuking', 'phukked', 'phukking', 'phuks', 'phuq', 'pigfucker', 'pimpis', 'piss', 'pissed', 'pisser', 'pissers', 'pisses', 'pissflaps', 'pissin', 'pissing', 'pissoff', 'poop', 'porn', 'porno', 'pornography', 'pornos', 'prick', 'pricks', 'pron', 'pube', 'pusse', 'pussi', 'pussies', 'pussy', 'pussys', 'rectum', 'retard', 'rimjaw', 'rimming', 's hit', 's.o.b.', 'sadist', 'schlong', 'screwing', 'scroat', 'scrote', 'scrotum', 'semen', 'sex', 'sh!+', 'sh!t', 'sh1t', 'shag', 'shagger', 'shaggin', 'shagging', 'shemale', 'shi+', 'shit', 'shitdick', 'shite', 'shited', 'shitey', 'shitfuck', 'shitfull', 'shithead', 'shiting', 'shitings', 'shits', 'shitted', 'shitter', 'shitters', 'shitting', 'shittings', 'shitty', 'skank', 'slut', 'sluts', 'smegma', 'smut', 'snatch', 'son-of-a-bitch', 'spac', 'spunk', 's_h_i_t', 't1tt1e5', 't1tties', 'teets', 'teez', 'testical', 'testicle', 'titfuck', 'tits', 'titt', 'tittie5', 'tittiefucker', 'titties', 'tittyfuck', 'tittywank', 'titwank', 'tosser', 'turd', 'tw4t', 'twat', 'twathead', 'twatty', 'twunt', 'twunter', 'v14gra', 'v1gra', 'vagina', 'viagra', 'vulva', 'w00se', 'wang', 'wank', 'wanker', 'wanky', 'whoar', 'whore', 'willies', 'willy', 'xrated', 'xxx'];
        foreach ($filterWords as $bad_word) {
            $text = str_replace($bad_word, '***', strtolower($text));
        }
        User::SNotify(1, User::SGetFormattedName($user) . 'Attempted to use a banned word');

        User::SNotify(2, User::SGetFormattedName($user) . 'Attempted to use a banned word');

        return $text;
    }

    public static function save($ids, User $user)
    {
        if (!is_array($ids) && $ids < 0) {
            throw new FailedResult('Invalid ids.');
        }
        if (!is_array($ids)) {
            $ids = [$ids];
        }

        foreach ($ids as $key => $id) {
            if ($id != '') {
                if (MySQL::GetSingle('SELECT box from `pms` WHERE `id`=' . $id) == Pms::MODERATOR) {
                    unset($ids[$key]);
                }
            }
        }

        $counts = MySQL::GetSingle('SELECT count(`id`) as id from `pms` WHERE `to`="' . $user->id . '" and `box`=' . self::SAVED . ' AND deleted = 0');
        if ($counts >= 25) {
            throw new FailedResult('You have already more than 25 saved pmails!');
        }
        if ($counts + count($ids) >= 25) {
            throw new FailedResult('You can not save more than 25 pmails!');
        }
        $sql = 'UPDATE `pms` SET `box`=' . self::SAVED . ' WHERE `id` IN ("' . implode('","', $ids) . '") AND `to` = "' . $user->id . '"';
        DBi::$conn->query($sql);

        return true;
    }

    public static function deleteWhere($where)
    {
        $sql = 'UPDATE `pms` SET deleted = 1, timedeleted = \'' . time() . '\' WHERE ' . $where;
        DBi::$conn->query($sql);

        return true;
    }

    public static function delete($ids, User $user)
    {
        if (!is_array($ids) && $ids < 0) {
            throw new FailedResult('Invalid ids.');
        }
        if (!is_array($ids)) {
            $ids = [$ids];
        }

        return self::deleteWhere('`id` IN ("' . implode('","', $ids) . '")');
    }

    public static function DeleteAll(User $user, $box = self::INBOX)
    {
        return self::deleteWhere('`to` = "' . $user->id . '" AND `box`=' . $box);
    }

    public static function DeleteAllRead(User $user, $box = self::INBOX)
    {
        return self::deleteWhere('`to` = "' . $user->id . '" AND `box`=' . $box . ' AND `viewed`="2"');
    }

    /**
     * Funtions return all categories.
     *
     * @return array
     */
    public static function GetAllMail(User $user, $box = self::INBOX, $whereClause = '')
    {
        if ($box == self::INBOX) {
            $where = ' deleted = 0 AND `to` = "' . $user->id . '" AND ((box=' . Pms::INBOX . ' and blocked = 0 ) or box=' . Pms::MODERATOR . ')';
        } else {
            $where = 'blocked = 0 AND deleted = 0 AND `to` = "' . $user->id . '" AND `box` = ' . $box;
        }
        if (!empty($whereClause)) {
            $where .= ' AND ' . $whereClause;
        }

        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $where, false, false, 'timesent', 'DESC');
    }

    public static function GetAllMod(User $user, $box = self::INBOX, $whereClause = '')
    {
        $where = 'deleted = 0 AND `to` = "' . $user->id . '" AND `box` = ' . $box;
        if (!empty($whereClause)) {
            $where .= ' AND ' . $whereClause;
        }

        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $where, false, false, 'timesent', 'DESC');
    }

    public static function GetAllSentNotMod(User $user, $whereClause = '', $box = null)
    {
        $where = ' `from` = "' . $user->id . '" AND timesent > ' . (time() - 7 * DAY_SEC);
        $where .= ' And box!=' . Pms::MODERATOR;
        if (!empty($whereClause)) {
            $where .= ' AND ' . $whereClause;
        }

        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $where, false, false, 'timesent', 'DESC');
    }

    public static function GetAllSent(User $user, $whereClause = '', $box = null)
    {
        $where = ' `from` = "' . $user->id . '" AND timesent > ' . (time() - 7 * DAY_SEC);
        if ($box != null) {
            $where .= ' And box=' . $box;
        }
        if (!empty($whereClause)) {
            $where .= ' AND ' . $whereClause;
        }

        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $where, false, false, 'timesent', 'DESC');
    }

    public static function GetAllFromFriends(User $user, $box = self::INBOX)
    {
        $where = ' `from` IN ( SELECT friend FROM Friends WHERE `self`="' . $user->id . '")';

        return self::GetAllMail($user, $box, $where);
    }

    public static function GetAllFromEnemies(User $user, $box = self::INBOX)
    {
        $where = ' `from` IN ( SELECT enemy FROM Enemies WHERE `self`="' . $user->id . '")';

        return self::GetAllMail($user, $box, $where);
    }

    public static function GetMail(User $user, $id)
    {
        $where = ' (`to` = "' . $user->id . '" OR `from` = "' . $user->id . '" ) AND `id` = ' . (int) $id;
        $pms = parent::XGet(self::GetDataTableFields(), self::GetDataTable(), $where);

        return $pms;
    }

    public static function GetEnemies(User $user)
    {
        $where = '`self`="' . $user->id . '"';

        return parent::GetAll(['enemy', 'enemy_name'], 'Enemies', $where, false, false, 'enemy', 'ASC');
    }

    public static function GetFriends(User $user)
    {
        $where = '`self`="' . $user->id . '"';

        return parent::GetAll(['friend', 'friend_name'], 'Friends', $where, false, false, 'friend', 'ASC');
    }

    public static function Add($to, $from, $time, $subject, $msgtext, $box = self::INBOX, $blocked = 0)
    {
        if (empty($msgtext)) {
            throw new FailedResult(PMS_CANT_SEND_BLANK_MSG);
        }
        if ($to == 1500) {
            DBi::$conn->query('INSERT INTO `pms`  SET `to`="2000", `from`="' . $from . '", `timesent`="' . $time . '", `subject`="' . $subject . '", `msgtext`="' . $msgtext . '", `box`="' . Pms::MODERATOR . '", `blocked`="' . $blocked . '"');
        } else {
            $t = new User($from);
            DBi::$conn->query('INSERT INTO `pms`  SET `to`="' . $to . '", `from`="' . $from . '", `timesent`="' . $time . '", `subject`="' . $subject . '", `msgtext`="' . $msgtext . '", `box`="' . $box . '", `blocked`="' . $blocked . '"');
        }

        return true;
    }

    public static function IsUserBlocked(User $user, $id)
    {
        return MySQL::GetSingle('SELECT `block_id` FROM `block` WHERE `user_id`="' . $user->id . '" AND `block_id`="' . $id . '"');
    }

    public static function CountUnread(User $user, $box = 0)
    {
        $where = empty($box) ? '' : ' AND `box` = \'' . $box . '\'';

        return MySQL::GetSingle('SELECT count(`id`) as total FROM `pms` WHERE `to`=\'' . $user->id . '\' AND `viewed`=\'1\' AND blocked = 0 AND deleted = 0 ' . $where);
    }

    /**
     * Function used to get the data table name which is implemented by class.
     *
     * @return string
     */
    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    /**
     * Returns the fields of table.
     *
     * @return array
     */
    protected static function GetDataTableFields()
    {
        return [
            self::$idField,
            'box',
            'to',
            'from',
            'timesent',
            'subject',
            'msgtext',
            'viewed',
            'blocked',
            'deleted',
            'timedeleted',
        ];
    }

    /**
     * Returns the identifier field name.
     *
     * @return mixed
     */
    protected function GetIdentifierFieldName()
    {
        return self::$idField;
    }

    /**
     * Function returns the class name.
     *
     * @return string
     */
    protected function GetClassName()
    {
        return __CLASS__;
    }
}
