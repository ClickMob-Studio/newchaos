<?php
/**
 * Base singleton class for all logs.
 *
 * Examples of usage:
 * LogsBase::getInstance()->setTableName('<tableName>')->GetAllAlternative(array(<options>))
 * or GetAll()
 *
 * @author Bakyt Niyazov
 */
class LogsBase extends BaseObject implements ISingleton
{
    public static $usePaging = false; //boolean to use paging into GetAll and GetAllById
    public static $paginator = null;
    public static $idField = 'id'; //id field

    public static $dataTable = null; // table implemented
    /**
     * Singleton instance.
     *
     * @var obj
     */
    private static $instance;

    /**
     * Constructor.
     */
    public function __construct()
    {
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new LogsBase();
        }

        return self::$instance;
    }

    public function setTableName($tableName)
    {
        self::$dataTable = $tableName;

        return $this;
    }

    /**
     * Returns the fields of table.
     *
     * @todo: use somesort of caching (memcache?)
     *
     * @return array
     */
    public static function GetDataTableFields()
    {
        return MySQL::GetFields(self::$dataTable);
    }

    public function Truncate()
    {
        DBi::$conn->query('TRUNCATE `' . self::$dataTable . '`');
    }

    /**
     * Get result set from Logs table.
     *
     * @param array $options (keys: where, order, limit, page, calcRows)
     *
     * @return array
     */
    public function GetAllAlternate($options)
    {
        $defaults = [
            'where' => '',
            'order' => false,
            'dir' => false,
            'page' => 0,
            'limit' => false,
            'calcRows' => false,
            'fields' => false,
            'quoteFields' => true,
        ];
        $options = array_merge($defaults, $options);
        extract($options, EXTR_OVERWRITE);

        $fields = !$options['fields'] ? self::GetDataTableFields() : $options['fields'];

        return parent::GetAll($fields, self::$dataTable, $where, $page, $limit, $order, $dir, $calcRows, $quoteFields);
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
