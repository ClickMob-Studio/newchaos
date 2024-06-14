<?php

/**
 * discription: This class is used to manage world stats.
 *
 * @author: Harish<harish282@gmail.com>
 * @name: WorldStats
 * @package: includes
 * @subpackage: classes
 * @final: Final
 * @access: Public
 * @copyright: icecubegaming <http://www.icecubegaming.com>
 */
final class WorldStats extends BaseObject
{
    public static $idField = 'id'; //id field
    public static $dataTable = 'worldstats'; // table implemented
    public static $categories = [];

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct(1);
    }

    public static function GetCategories()
    {
        self::$categories['Attributes'] = [
            'avg_strength' => ['label' => WORLDSTATS_AVERAGE_STRENGTH, 'refcolumn' => 'SELECT AVG(`strength`) FROM `grpgusers`', 'type' => 'Number'],
            'avg_speed' => ['label' => WORLDSTATS_AVERAGE_SPEED, 'refcolumn' => 'SELECT AVG(`speed`) FROM `grpgusers`', 'type' => 'Number'],
            'avg_defense' => ['label' => WORLDSTATS_AVERAGE_DEFENSE, 'refcolumn' => 'SELECT AVG(`defense`) FROM `grpgusers`', 'type' => 'Number'],
            'tot_points' => ['label' => WORLDSTATS_TOTAL_POINTS, 'refcolumn' => 'SELECT SUM(`points`) FROM `grpgusers`', 'type' => 'Number'],
            'avg_points' => ['label' => WORLDSTATS_AVG_POINTS, 'refcolumn' => 'SELECT AVG(`points`) FROM `grpgusers`', 'type' => 'Number'],
            'tot_money' => ['label' => WORLDSTATS_TOTAL_HAND_MONEY, 'refcolumn' => 'SELECT SUM(`money`) FROM `grpgusers`', 'type' => 'Money'],
            'avg_money' => ['label' => WORLDSTATS_AVG_HAND_MONEY, 'refcolumn' => 'SELECT AVG(`money`) FROM `grpgusers`', 'type' => 'Money'],
            'tot_bank_money' => ['label' => WORLDSTATS_TOTAL_BANKED_MONEY, 'refcolumn' => 'SELECT SUM(`bank` )FROM `grpgusers`', 'type' => 'Money'],
            'avg_bank_money' => ['label' => WORLDSTATS_AVG_BANKED_MONEY, 'refcolumn' => 'SELECT AVG(`bank`) FROM `grpgusers`', 'type' => 'Money'],
            //'tot_user_wo_sec' => array('label' => 'Total prisoners without security level', 'refcolumn' => 'SELECT COUNT(id) FROM `grpgusers` WHERE `securityLevel` = 0', 'type' => 'Number'),
            //'tot_dang_user' => array('label' => WORLDSTATS_TOTAL_DANGEROUS, 'refcolumn' => 'SELECT COUNT(id) FROM `grpgusers` WHERE `securityLevel` != 0', 'desc' => 'Total number of prisoners with at least one security level.'),
            //'max_sec_level' => array('label' => WORLDSTATS_HIGH_SEC_LEVEL, 'refcolumn' => 'SELECT MAX(`securityLevel`) FROM `grpgusers`', 'type' => 'Number')
        ];

        self::$categories['Items'] = [
            'most_used_weapon' => ['label' => WORLDSTATS_MOST_USED_WEAPON, 'refcolumn' => 'SELECT CONCAT(`items`.`itemname`,\'|\',`items`.`id`) as `itemname`, `eqweapon`,count(`eqweapon`) as most_used FROM `items`, grpgusers WHERE `items`.id = grpgusers.eqweapon AND `eqweapon` > 0 GROUP BY `eqweapon` ORDER BY most_used DESC LIMIT 1', 'type' => 'Item', 'desc' => WORLDSTATS_MOST_USED_WEAPON_DESC],
            'most_used_armor' => ['label' => WORLDSTATS_MOST_USED_ARMOR, 'refcolumn' => 'SELECT CONCAT(`items`.`itemname`,\'|\',`items`.`id`) as `itemname`, `eqarmor`,count(`eqarmor`) as most_used FROM `items`, grpgusers WHERE `items`.id = grpgusers.eqarmor AND `eqarmor` > 0 GROUP BY `eqarmor` ORDER BY most_used DESC LIMIT 1', 'type' => 'Item', 'desc' => WORLDSTATS_MOST_USED_ARMOR_DESC],
            'most_com_item' => ['label' => WORLDSTATS_COMMON_ITEM, 'refcolumn' => 'SELECT CONCAT(`items`.`itemname`,\'|\',`items`.`id`) as `itemname`, `itemid`, SUM(`quantity`) as most_purchased FROM `items`, `inventory` WHERE `items`.id = `inventory`.itemid GROUP BY `itemid` ORDER BY most_purchased DESC LIMIT 1', 'type' => 'Item'],
            'rarer_item' => ['label' => WORLDSTATS_RARER_ITEM, 'refcolumn' => 'SELECT CONCAT(`items`.`itemname`,\'|\',`items`.`id`) as `itemname`, `itemid`, SUM(`quantity`) as most_purchased FROM `items`, `inventory` WHERE `items`.id NOT IN (79, 80) AND `items`.id = `inventory`.itemid GROUP BY `itemid` ORDER BY most_purchased ASC LIMIT 1', 'type' => 'Item'],
        ];

        self::$categories['Achievements'] = [
            'poth' => ['label' => 'Solider of the hour', 'refcolumn' => 'SELECT poth FROM worldstats WHERE id = 1', 'type' => 'User', 'desc' => 'Most active player for the last hour'],
            'most_dangerous' => ['label' => WORLDSTATS_DANGEROUS, 'refcolumn' => 'SELECT id, (`securityLevel` * 200 + `level`) as most_dangerous FROM `grpgusers` WHERE `id` NOT IN (1, 2,204) AND `id` NOT IN (SELECT `id` FROM `bans`) AND admin = 0  ORDER BY most_dangerous DESC LIMIT 1', 'type' => 'User', 'desc' => WORLDSTATS_DANGEROUS_DESC],
            'most_respected' => ['label' => WORLDSTATS_RESPECTED, 'refcolumn' => 'SELECT id FROM `grpgusers` WHERE `id` NOT IN (1000, 1984) AND `id` NOT IN (SELECT `id` FROM `bans`) AND admin = 0  ORDER BY `rmdays` DESC LIMIT 1', 'type' => 'User', 'desc' => WORLDSTATS_RESPECTED_DESC],
            'fastest_leveler' => ['label' => WORLDSTATS_FASTEST_LEVELER, 'refcolumn' => 'SELECT id, ((`securityLevel` * 200 + `level`) * 10000) / (UNIX_TIMESTAMP(NOW()) - `signuptime`) as fastest_leveler FROM `grpgusers` WHERE `level` != 1 AND `id` NOT IN (1000, 1984) AND `id` NOT IN (SELECT `id` FROM `bans`) AND admin = 0  ORDER BY fastest_leveler DESC LIMIT 1', 'type' => 'User', 'desc' => WORLDSTATS_FASTEST_LEVELER_DESC],
            'athlete' => ['label' => WORLDSTATS_ATHLETE, 'refcolumn' => 'SELECT id, (`strength`+`defense`+`speed`) as athlete FROM `grpgusers` WHERE `id` NOT IN (1, 2,204) AND `id` NOT IN (SELECT `id` FROM `bans`) AND admin = 0  ORDER BY athlete DESC LIMIT 1', 'type' => 'User', 'desc' => WORLDSTATS_ATHLETE_DESC],
            'fastest_rising_athlete' => ['label' => WORLDSTATS_FASTEST_ATHLETE, 'refcolumn' => 'SELECT id, ((`strength`+`defense`+`speed`) / (UNIX_TIMESTAMP(NOW()) - `signuptime`)) as fastest_rising_athlete FROM `grpgusers` WHERE `id` NOT IN (1000, 1984) AND `id` NOT IN (SELECT `id` FROM `bans`) AND admin = 0  ORDER BY fastest_rising_athlete DESC LIMIT 1', 'type' => 'User', 'desc' => WORLDSTATS_FASTEST_ATHLETE_DESC],
            'crime_mastermind' => ['label' => WORLDSTATS_CRIME_MASTERMIND, 'refcolumn' => 'SELECT id, (`crimesucceeded` - `crimefailed`) as crime_mastermind FROM `grpgusers` WHERE `id` NOT IN (1, 2,204) AND `id` NOT IN (SELECT `id` FROM `bans`) AND admin = 0  ORDER BY crime_mastermind DESC LIMIT 1', 'type' => 'User', 'desc' => WORLDSTATS_CRIME_MASTERMIND_DESC],
            'war_master' => ['label' => WORLDSTATS_WAR_MASTER, 'refcolumn' => 'SELECT id, (`battlewon` - `battlelost`) as war_master FROM `grpgusers` WHERE `id` NOT IN (1, 2,204) AND `id` NOT IN (SELECT `id` FROM `bans`) AND admin = 0  ORDER BY war_master DESC LIMIT 1', 'type' => 'User', 'desc' => WORLDSTATS_WAR_MASTER_DESC],
            'richie_the_rich' => ['label' => WORLDSTATS_RICHIE, 'refcolumn' => 'SELECT id, (`money` + `bank`) as richie_the_rich FROM `grpgusers` WHERE `id` NOT IN (1, 2,204) AND `id` NOT IN (SELECT `id` FROM `bans`) AND admin = 0  ORDER BY richie_the_rich DESC LIMIT 1', 'type' => 'User', 'desc' => WORLDSTATS_RICHIE_DESC],
            'vault_man' => ['label' => WORLDSTATS_VAULT_MAN, 'refcolumn' => 'SELECT `userid`, SUM(`quantity`) as totalitem FROM `inventory` WHERE `userid` NOT IN (1, 2,204) AND `userid` NOT IN (SELECT `id` FROM `bans`) OR userid != 1 OR userid != 2 OR userid != 204  GROUP BY `userid` ORDER BY totalitem DESC LIMIT 1', 'type' => 'User', 'desc' => WORLDSTATS_VAULT_MAN_DESC],
            'lazy_head' => ['label' => WORLDSTATS_LAZY_HEAD, 'refcolumn' => 'SELECT `userid`, SUM(`quantity`) as totalitem FROM `inventory` WHERE `itemid`=' . Item::getAwakePillId() . ' AND `userid` NOT IN (SELECT `id` FROM `bans`) GROUP BY `userid`  ORDER BY totalitem DESC LIMIT 1', 'type' => 'User', 'desc' => WORLDSTATS_LAZY_HEAD_DESC],
            'hitchhiker' => ['label' => WORLDSTATS_HITCHHIKER, 'refcolumn' => 'SELECT `userid`, SUM(`quantity`) as totalitem FROM `inventory` WHERE `itemid`=29 AND `userid` NOT IN (SELECT `id` FROM `bans`)  GROUP BY `userid` ORDER BY totalitem DESC LIMIT 1', 'type' => 'User', 'desc' => '342'],
            'pub_rel_spec' => ['label' => WORLDSTATS_PRS, 'refcolumn' => 'SELECT id, ((SELECT COUNT(`to`) FROM `pms` WHERE `pms`.`to` = `grpgusers`.id ) + (SELECT COUNT(`from`) FROM `pms` WHERE `pms`.`from` = `grpgusers`.id ) + (SELECT COUNT(`self`) FROM `Friends` WHERE `Friends`.`self` = `grpgusers`.id )) AS pub_rel_spec FROM `grpgusers` WHERE `id` NOT IN (SELECT `id` FROM `bans`) AND `id` NOT IN (1000, 1984) AND admin = 0   ORDER BY pub_rel_spec DESC LIMIT 1', 'type' => 'User', 'desc' => WORLDSTATS_PRS_DESC],
            'most_liked' => ['label' => WORLDSTATS_MOST_LIKED, 'refcolumn' => 'SELECT id FROM grpgusers WHERE `id` NOT IN (1, 2,204) AND `id` NOT IN (SELECT `id` FROM `bans`) AND admin = 0  ORDER BY rate DESC LIMIT 1', 'type' => 'User', 'desc' => WORLDSTATS_MOST_LIKED_DESC],
            'most_hated' => ['label' => WORLDSTATS_MOST_HATED, 'refcolumn' => 'SELECT id FROM grpgusers WHERE `id` NOT IN (1, 2,204) AND `id` NOT IN (SELECT `id` FROM `bans`) AND admin = 0  ORDER BY rate ASC LIMIT 1', 'type' => 'User', 'desc' => WORLDSTATS_MOST_HATED_DESC],
            'most_related' => ['label' => WORLDSTATS_MOST_RELATED, 'refcolumn' => 'SELECT self, COUNT(friend) as totalfriends FROM Friends  WHERE `self` NOT IN (SELECT `id` FROM `bans`) GROUP BY self ORDER BY totalfriends DESC LIMIT 1', 'type' => 'User', 'desc' => WORLDSTATS_MOST_RELATED_DESC],
            'most_blacklisted' => ['label' => WORLDSTATS_MOST_BLACKLISTED, 'refcolumn' => 'SELECT self, COUNT(`enemy`) as totalenemies FROM `Enemies` WHERE `self` NOT IN (SELECT `id` FROM `bans`) GROUP BY self ORDER BY totalenemies DESC LIMIT 1', 'type' => 'User', 'desc' => WORLDSTATS_MOST_BLACKLISTED_DESC],
            'lone_wolf' => ['label' => WORLDSTATS_LONE_WOLF, 'refcolumn' => 'SELECT id, ((SELECT COUNT(`to`) FROM `pms` WHERE `pms`.`to` = `grpgusers`.id ) + (SELECT COUNT(`from`) FROM `pms` WHERE `pms`.`from` = `grpgusers`.id ) + (SELECT COUNT(`self`) FROM `Friends` WHERE `Friends`.`self` = `grpgusers`.id )) AS pub_rel_spec FROM `grpgusers` WHERE `id` NOT IN (1000, 1984) AND `id` NOT IN (SELECT `id` FROM `bans`) AND admin = 0  ORDER BY pub_rel_spec ASC LIMIT 1', 'type' => 'User', 'desc' => WORLDSTATS_LONE_WOLF_DESC],
            'prison_punk' => ['label' => WORLDSTATS_PRISON_PUNK, 'refcolumn' => 'SELECT id, battlelost FROM `grpgusers` WHERE `id` NOT IN (1, 2,204) AND `id` NOT IN (SELECT `id` FROM `bans`) AND admin = 0  ORDER BY battlelost DESC LIMIT 1', 'type' => 'User', 'desc' => WORLDSTATS_PRISON_PUNK_DESC],
            'prison_terror' => ['label' => WORLDSTATS_PRISON_TERROR, 'refcolumn' => 'SELECT id, battlewon FROM `grpgusers` WHERE `id` NOT IN (1, 2,204) AND `id` NOT IN (SELECT `id` FROM `bans`) AND admin = 0  ORDER BY battlewon DESC LIMIT 1', 'type' => 'User', 'desc' => WORLDSTATS_PRISON_TERROR_DESC],
            'ultimate_hitman' => ['label' => WORLDSTATS_ULTIMATE_HITMAN, 'refcolumn' => 'SELECT provider,COUNT(provider) as contract, status FROM `hitman` WHERE `provider` NOT IN (1000, 1984) AND `provider` NOT IN (SELECT `id` FROM `bans`) AND status=3 GROUP BY provider ORDER BY contract DESC LIMIT 1', 'type' => 'User', 'desc' => WORLDSTATS_ULTIMATE_HITMAN_DESC],

            'most_jails' => ['label' => WORLDSTATS_MOST_JAILS, 'refcolumn' => 'SELECT id, jails FROM `grpgusers2` WHERE `id` NOT IN (1, 2,204) AND `id` NOT IN (SELECT `id` FROM `bans`) ORDER BY jails DESC LIMIT 1', 'type' => 'User', 'desc' => WORLDSTATS_JAILS],
            'most_busted' => ['label' => WORLDSTATS_MOST_BUSTED, 'refcolumn' => 'SELECT id, busts FROM `grpgusers2` WHERE `id` NOT IN (1, 2,204) AND `id` NOT IN (SELECT `id` FROM `bans`) ORDER BY busts DESC LIMIT 1', 'type' => 'User', 'desc' => WORLDSTATS_BUSTED],
            'most_mugs' => ['label' => WORLDSTATS_MOST_MUGS, 'refcolumn' => 'SELECT id, number_of_muggs FROM `grpgusers2` WHERE `id` NOT IN (1, 2,204) AND `id` NOT IN (SELECT `id` FROM `bans`) ORDER BY number_of_muggs DESC LIMIT 1', 'type' => 'User', 'desc' => WORLDSTATS_BUSTED],
        ];

        self::$categories['Generic information'] = [
            'total_user' => ['label' => WORLDSTATS_TOTAL_PRISONERS, 'refcolumn' => 'SELECT COUNT(userid) as total FROM `working`', 'type' => 'Number'],
            'total_res_user' => ['label' => WORLDSTATS_TOTAL_RESPECTED_PRISONERS, 'refcolumn' => 'SELECT COUNT(id) as total FROM `grpgusers` WHERE `rmdays`>0', 'type' => 'Number'],
            'avg_population' => ['label' => WORLDSTATS_AVG_POPULATION, 'refcolumn' => 'SELECT AVG(a.cityusers) FROM (SELECT COUNT(id) AS cityusers FROM grpgusers GROUP BY city) AS a', 'type' => 'Number'],
            //'pref_job' => ['label' => WORLDSTATS_PREFERRED_JOB, 'refcolumn' => 'SELECT job_role.name, grpgusers.job, count(job_role.id) AS pref_job FROM grpgusers, user_job_progress, job_role WHERE user_job_progress.user_id = grpgusers.id AND user_job_progress.job_role_id = job_role.id GROUP BY job ORDER BY pref_job LIMIT 1', 'type' => 'Job'],
            'pref_cell' => ['label' => WORLDSTATS_PREFERRED_CELL, 'refcolumn' => 'SELECT houses.name, grpgusers.house, count(grpgusers.house) AS pref_house FROM grpgusers, houses WHERE houses.id = grpgusers.house GROUP BY house ORDER BY pref_house DESC LIMIT 1', 'type' => 'Cell'],
            'tot_crime_cmtd' => ['label' => WORLDSTATS_TOTAL_CRIMES_COMMITED, 'refcolumn' => 'SELECT SUM(`crimefailed`+`crimesucceeded`) FROM `grpgusers`', 'type' => 'Number'],
            'avg_crime_cmtd' => ['label' => WORLDSTATS_AVG_CRIMES_COMMITED, 'refcolumn' => 'SELECT AVG(`crimefailed`+`crimesucceeded`) FROM `grpgusers`', 'type' => 'Number'],
        ];

        return self::$categories;
    }

    public static function UpdateTempStats()
    {
        $save_data = [];
        $categories = self::GetCategories();
        foreach ($categories as $data) {
            foreach ($data as $field => $details) {
                $save_data[$field] = MySQL::GetSingle($details['refcolumn']);
            }
        }

        DBi::$conn->query('TRUNCATE TABLE ' . self::GetDataTable());
        self::AddRecords($save_data, self::GetDataTable());

        return true;
    }

    /**
     * Funtions return the left links for given user.
     *
     * @return array
     */
    public static function GetWorldStats()
    {
        return self::XGet(self::GetDataTableFields(), self::GetDataTable(), 'id=1');
    }

    public static function GetByField($field)
    {
        if (empty($field)) {
            return false;
        }

        return MySQL::GetSingle('SELECT ' . $field . ' FROM ' . self::GetDataTable() . ' WHERE id = 1');
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
            'avg_strength',
            'avg_speed',
            'avg_defense',
            'tot_points',
            'avg_points',
            'tot_money',
            'avg_money',
            'tot_bank_money',
            'avg_bank_money',
            'tot_user_wo_sec',
            'tot_dang_user',
            'max_sec_level',
            'most_used_weapon',
            'most_used_armor',
            'most_com_item',
            'rarer_item',
            'most_dangerous',
            'most_respected',
            'fastest_leveler',
            'athlete',
            'fastest_rising_athlete',
            'crime_mastermind',
            'war_master',
            'richie_the_rich',
            'vault_man',
            'lazy_head',
            'hitchhiker',
            'pub_rel_spec',
            'most_liked',
            'most_hated',
            'most_related',
            'most_blacklisted',
            'lone_wolf',
            'total_user',
            'total_res_user',
            'avg_population',
            'pref_job',
            'pref_cell',
            'tot_crime_cmtd',
            'avg_crime_cmtd',
            'prison_punk',
            'prison_terror',
            'ultimate_hitman',
            'most_jails',
            'most_busted',
            'most_mugs',
            'poth',

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
