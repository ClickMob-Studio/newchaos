<?php

class UserQuestSeasonFive extends CachedObject
{
    public static $dataTable = 'user_quest_season_five';
    public static $idField = 'id';

    public function __construct($id)
    {
        parent::__construct($id);
    }

    public static function GetAll($where = '')
    {
        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $where);
    }

    public static function SGet($id)
    {
        return new UserQuestSeasonFive($id);
    }

    public function getSeason()
    {
        return 'five';
    }

    public function setMissionStatisticsSerialised(array $missionStatistics)
    {
        $this->SetAttribute('mission_statistics_serialised', serialize($missionStatistics));
    }

    public function getMissionStatistics()
    {
        return unserialize($this->mission_statistics_serialised);
    }

    public function getQuestSeason()
    {
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->select('id')
            ->from('quest_season')
            ->where('season = :season')
            ->setParameter('season', $this->getSeason())
            ->setMaxResults(1)
        ;
        $result = $queryBuilder->execute()->fetch();

        if ($result && isset($result['id'])) {
            return new QuestSeason($result['id']);
        }

        return null;
    }

    public function startNextMission()
    {
        $questSeason = $this->getQuestSeason();

        if ($this->current_mission > $questSeason->mission_count) {
            $response = array(
                'success' => false,
                'message' => 'You\'ve complete all missions in the current season. Check back soon for the next season.'
            );

            return $response;
        }

        $missionStatistics = $this->getMissionStatistics();

        if (isset($missionStatistics['mission_' . $this->current_mission])) {
            $response = array(
                'success' => false,
                'message' => 'Your already in the process of completing a mission. Finish it before trying to start a new mission'
            );

            return $response;
        }

        $requirements = array();

        /*
         * MISSION 1
         */
        if ($this->current_mission == 1) {
            $requirements = array(
                'missions' => array(
                    'required' => 1000,
                    'complete' => 0
                ),
                'trains' => array(
                    'required' => 1000,
                    'complete' => 0
                ),
                'attacks' => array(
                    'required' => 100,
                    'complete' => 0
                ),
                'mugs' => array(
                    'required' => 50,
                    'complete' => 0
                ),
                'daily_tasks' => array(
                    'required' => 6,
                    'complete' => 0
                )
            );
        }

        /*
         * MISSION 2
         */
        if ($this->current_mission == 2) {
            $requirements = array(
                'library_hacking' => array(
                    'required' => 1,
                    'complete' => 0
                )
            );
        }

        /*
         * MISSION 3
         */
        if ($this->current_mission == 3) {
            $requirements = array(
                'boss_fights' => array(
                    'required' => 21,
                    'complete' => 0
                )
            );
        }

        /*
         * MISSION 4
         */
        if ($this->current_mission == 4) {
            $requirements = array(
                'season_five_interrogation' => array(
                    'required' => 1,
                    'complete' => 0
                )
            );
        }

        /*
         * MISSION 5
         */
        if ($this->current_mission == 5) {
            $requirements = array(
                'mining_drones' => array(
                    'required' => 75,
                    'complete' => 0
                ),

            );
        }

        /*
         * MISSION 6
         */
        if ($this->current_mission == 6) {
            $requirements = array(
                'regiment_territory_battle' => array(
                    'required' => 10,
                    'complete' => 0
                )
            );
        }

        /*
         * MISSION 7
         */
        if ($this->current_mission == 7) {
            $requirements = array(
                'shadow_snake_intel_folders' => array(
                    'required' => 20,
                    'complete' => 0
                )
            );
        }

        /*
         * MISSION 8
         */
        if ($this->current_mission == 8) {
            $requirements = array(
                'medals' => array(
                    'required' => 5,
                    'complete' => 0
                ),
                'cash' => array(
                    'required' => 1000000000,
                    'complete' => 0
                ),
                'points' => array(
                    'required' => 1000,
                    'complete' => 0
                ),
            );
        }

        /*
         * MISSION 9
         */
        if ($this->current_mission == 9) {
            $requirements = array(
                'boss_fights' => array(
                    'required' => 22,
                    'complete' => 0
                )
            );
        }

        $missionStatistics['mission_' . $this->current_mission] = array(
            'is_complete' => false,
            'requirements' => $requirements
        );

        $this->setMissionStatisticsSerialised($missionStatistics);

        $response = array(
            'success' => true
        );

        return $response;
    }

    public static function getRequirementKeyForDisplay($key)
    {
        $displays = array(
            'missions' => 'Missions',
            'trains' => 'Gym Trains',
            'attacks' => 'Attacks',
            'mugs' => 'Mugs',
            'daily_tasks' => 'Daily tasks',
            'boss_fights' => 'Boss Fight',
            'intel_folders' => 'Intel Folders',
            'shadow_snake_intel_folders' => 'Shadow Snake Intel Folders',
            'alasad_intel_folders' => 'Al Asad Intel Folders',
            'mining_drones' => 'Mining Drones',
            'mining_drone_spy' => 'Spy Drones',
            'drone_parts' => 'Drone Parts',
            'cash' => 'Cash',
            'points' => 'Points',
            'vault' => 'Vault',
            'armour_store' => 'Armour Store',
            'regiment_territory_battle' => 'Regiment Territory Battles',
            'weapon_stashes' => 'Weapon Stashes',
            'medals' => 'Medals',
            'library_hacking' => 'Hacking',
            'season_five_interrogation' => 'Interrogate Shadow Beast',
        );

        return $displays[$key];
    }

    public function getIsSeasonComplete()
    {
        $questSeason = $this->getQuestSeason();

        if ($this->current_mission > $questSeason->mission_count) {
            return true;
        }

        return false;
    }

    public function getIsCurrentMissionComplete()
    {
        $missionStatistics = $this->getMissionStatistics();

        if (isset($missionStatistics['mission_' . $this->current_mission]) && isset($missionStatistics['mission_' . $this->current_mission]['is_complete'])) {
            if  ($missionStatistics['mission_' . $this->current_mission]['is_complete']) {
                return true;
            }
        }

        return false;
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            'id',
            'user_id',
            'current_mission',
            'mission_statistics_serialised',
            'is_season_complete',
        ];
    }

    protected function GetIdentifierFieldName()
    {
        return self::$idField;
    }

    protected function GetClassName()
    {
        return __CLASS__;
    }
}
?>