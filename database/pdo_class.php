<?php

require_once __DIR__ . '/../includes/cache.php';

class database
{
    protected $last_query;
    protected $conn;
    private $db;
    private $stmt;
    var $num_queries = 0;
    var $queries = "";
    static $inst = null;
    static function getInstance()
    {
        if (self::$inst == null)
            self::$inst = new database();
        return self::$inst;
    }
    private function __construct()
    {
        mb_internal_encoding('UTF-8');
        mb_regex_encoding('UTF-8');
        mysqli_report(MYSQLI_REPORT_STRICT);

        $dbConfig = Config::db();
        $dsn = 'mysql:host=' . $dbConfig->host . '; dbname=' . $dbConfig->database . '; charset=utf8';
        $options = array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
        );
        try {
            $this->db = new PDO($dsn, $dbConfig->username, $dbConfig->password, $options);
        } catch (PDOException $e) {
            exit('<p><strong>CONSTRUCT ERROR</strong></p>' . $e->getMessage());
        }
        $this->query("SET collation_connection = 'utf8mb4_general_ci'");
        $this->execute();
    }
    public function __destruct()
    {
        if (!$this->db)
            return null;
        $this->db = null;
        return null;
    }
    public function query($query)
    {
        $this->last_query = $query;
        $this->num_queries++;
        try {
            $this->stmt = $this->db->prepare($query);
        } catch (PDOException $e) {
            echo '<p><strong>QUERY ERROR</strong></p>' . $e->getMessage();
            error_log($e->getMessage() . ' - ' . $_SERVER['PHP_SELF'] . ' - ' . __FILE__, 0);
            exit;
        }
    }
    public function prepare($query)
    {
        try {
            $this->db->prepare($query);
        } catch (PDOException $e) {
            echo '<p><strong>QUERY (PREPARE) ERROR</strong></p>' . $e->getMessage();
            error_log($e->getMessage() . ' - ' . $_SERVER['PHP_SELF'] . ' - ' . __FILE__, 0);
            exit;
        }
    }
    public function bind($param, $value, $type = null)
    {
        if (is_null($type))
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
                    break;
            }
        try {
            $this->stmt->bindValue($param, $value, $type);
        } catch (PDOException $e) {
            exit('<p><strong>BIND ERROR</strong></p>' . $e->getMessage());
        }
    }
    public function execute(array $binds = null)
    {
        if (!isset($this->stmt))
            return false;
        try {
            if (!empty($binds) && count($binds) > 0)
                return $this->stmt->execute($binds);
            else
                return $this->stmt->execute();
        } catch (PDOException $e) {
            echo "<p><strong>EXECUTION ERROR</strong></p>" . $e->getMessage() . " " . $this->last_query;
            error_log($e->getMessage() . ' - ' . $_SERVER['PHP_SELF'] . ' - ' . __FILE__, 0);
            exit;
        }
    }
    public function fetch_row($shifted = false)
    {
        if (!isset($this->stmt))
            return null;
        try {
            $this->execute();
            $ret = $this->stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($shifted === true)
                $ret = array_shift($ret);
            return $ret;
        } catch (PDOException $e) {
            exit('<p><strong>FETCH ROW ERROR</strong></p>' . $e->getMessage());
        }
    }
    public function fetch_single()
    {
        if (!isset($this->stmt))
            return null;
        try {
            $this->execute();
            return $this->stmt->fetchColumn(0);
        } catch (PDOException $e) {
            exit('<p><strong>FETCH SINGLE ERROR</strong></p>' . $e->getMessage());
        }
    }
    public function fetch_object()
    {
        if (!isset($this->stmt))
            return null;
        try {
            $this->execute();
            return $this->stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            exit('<p><strong>FETCH OBJECT ERROR</strong></p>' . $e->getMessage());
        }
    }
    public function affected_rows()
    {
        try {
            return $this->stmt->rowCount();
        } catch (PDOException $e) {
            exit('<p><strong>AFFECTED ROWS ERROR</strong></p>' . $e->getMessage());
        }
    }
    public function num_rows()
    {
        try {
            return $this->stmt->fetchColumn();
        } catch (PDOException $e) {
            exit('<p><strong>NUM ROWS ERROR</strong></p>' . $e->getMessage());
        }
    }
    public function insert_id()
    {
        try {
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            exit('<p><strong>LAST INSERT ID ERROR</strong></p>' . $e->getMessage());
        }
    }
    public function query_error()
    {
        if (!isset($_SESSION['userid']))
            $_SESSION['userid'] = 0;
        if ($_SESSION['userid'] == 2)
            echo "<p><strong>QUERY ERROR:</strong> " . $this->error . "<br />Query was " . $this->last_query . "</p><br /><br />";
        exit("An error has been detected");
    }
    public function escape($str)
    {
        return $str;
    }
    public function tableExists($table)
    {
        try {
            $result = $this->db->query("SELECT 1 FROM `" . $table . "` LIMIT 1");
        } catch (Exception $e) {
            return false;
        }
        return $result !== false;
    }
    public function startTrans()
    {
        return $this->db->beginTransaction();
    }
    public function endTrans()
    {
        return $this->db->commit();
    }
    public function cancelTransaction()
    {
        return $this->db->rollBack();
    }
    public function error()
    {
        echo "<pre>";
        var_dump($this->stmt->debugDumpParams());
        echo "</pre>";
    }
    // Helper function(s)
    public function truncate(array $tables = null)
    {
        if (!count($tables))
            return false;
        $this->startTrans();
        foreach ($tables as $table) {
            $this->query('TRUNCATE TABLE ?');
            $this->execute(array($table));
        }
        $this->endTrans();
    }
}
$db = database::getInstance();
