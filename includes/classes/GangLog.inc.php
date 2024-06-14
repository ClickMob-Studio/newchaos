<?php
error_reporting(0);
    /**
     * discription: This class is used to manage auctions placed by users.
     *
     * @author: Harish<harish282@gmail.com>
     * @name: Auction
     * @package: includes
     * @subpackage: classes
     * @final: Final
     * @access: Public
     * @copyright: icecubegaming <http://www.icecubegaming.com>
     */
    interface GangLogSearch
    {
        public function query();
    }
        class GangLogByTimeStamp implements GangLogSearch
        {
            public $major;
            public $minor;

            public function __construct($minor, $major)
            {
                $this->minor = $minor;
                $this->major = $major;
            }

            public function query()
            {
                if ($this->major != '') {
                    return 'timestamp > ' . $this->minor . ' and timestamp<' . $this->major;
                }

                return 'timestamp > ' . $this->minor . ' ';
            }
        }
        class SingleSearch implements GangLogSearch
        {
            public $key;
            public $param;

            public function SingleSearch(array $arr)
            {
                $this->key = key($arr);
                $this->param = $arr[$this->key];
            }

            public function query()
            {
                return $this->key . "='" . $this->param . "' ";
            }
        }
        class GangLogByAttackerId extends SingleSearch
        {
            public function __construct($id)
            {
                parent::SingleSearch(['attacker' => $id]);
            }
        }
        class GangLogByDefenderId extends SingleSearch
        {
            public function __construct($id)
            {
                parent::SingleSearch(['defender' => $id]);
            }
        }
         class GangLogByGangAttackerId extends SingleSearch
         {
             public function __construct($id)
             {
                 parent::SingleSearch(['gangidatt' => $id]);
             }
         }
        class GangLogByGangDefenderId extends SingleSearch
        {
            public function __construct($id)
            {
                parent::SingleSearch(['gangid' => $id]);
            }
        }

    class GangLog extends BaseObject
    {
        public static $idField = 'id'; //id field
        public static $dataTable = 'ganglog'; // table implemented
               public $args = [];

        /**
         * Constructor.
         */
        public function __construct($id = null)
        {
            if ($id > 0) {
                try {
                    parent::__construct($id);
                } catch (CheatingException $e) {
                } catch (SoftException $e) {
                }
            }
        }

        public function MakeQuery($order)
        {
            $little = '';
            if (!empty($this->args)) {
                foreach ($this->args as $key => $value) {
                    $little .= $value->query() . ' and ';
                }
                $little = substr($little, 0, strlen($little) - 4);

                $sql = sprintf('select * from ' . self::$dataTable . ' where %s', $little);
                $objs = self::GetPaginationResults($sql . ($order != ' ' ? ' order by ' . $order : ''));
                foreach ($objs as $key => $value) {
                    $value->time = $value->timestamp;
                    $objs[$key] = $value;
                }

                return $objs;
            }

            return [];
        }

        public function Search(GangLogSearch $search)
        {
            $this->args[] = $search;
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
                                'timestamp',
                                'gangid',
                                'attacker',
                                'defender',
                                'winner',
                'gangidatt',
                                'expwon',
                                'moneywon',
                                'atkexp',
                'atkmoney',
                'jointattack',
                'attackers',
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
