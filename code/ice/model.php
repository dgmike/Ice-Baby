<?php
require_once 'model_result.php';

class Model extends PDO
{
    public $_table = null;
    public $_key = null;
    public $str = null;
    public $hasMany = array();

    public function __construct($dns=null, $username=null, $password=null,
            array $driver_options = array())
    {
        if (!$this->_table) {
            $this->_table = strtolower(get_class($this));
        }
        if (!$this->_key) {
            $this->_key = 'id_'.$this->_table;
        }
        if (!$this->str) {
            $this->str = ":id_{$this->_table}:";
        }
        $items = array('dns', 'username', 'password', 'driver_options');
        foreach ($items as $item) {
            if (null==$$item) {
                $item = strtoupper($item);
                if (defined("DB_$item")) {
                    if ($item == 'DRIVER_OPTIONS') {
                        $driver_options = unserialize(DB_DRIVER_OPTIONS);
                    } else {
                        ${strtolower($item)} = constant("DB_$item");
                    }
                }
            }
        }
        parent::__construct($dns, $username, $password, $driver_options);
    }

    public function get($id)
    {
        $sql = 'SELECT * FROM '.$this->_table.' WHERE '.$this->_key.' = ?';
        $stmt = $this->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Model_Result', array($stmt, $this));
        $stmt->execute(array($id));
        return $stmt->fetch();
    }

    public function select($where=array(), $fields='*', $limit=null, $offset=null)
    {
        $_where = array();
        if ('string'==gettype($where)) {
            $_where[] = $where;
        } elseif (in_array(gettype($where), array('array', 'object'))) {
            foreach ($where as $key=>$value) {
                $key = trim($key);
                if (strpos(trim($key), ' ')===false) {
                    $key .= ' = ';
                }
                if ($_where AND !preg_match('@^(and|or)\b@i', $key)) {
                    $key = "AND $key";
                }
                $_where[] = "$key '$value'";
            }
        }
        if (!$fields) {
            $fields = '*';
        }
        if (in_array(gettype($fields), array('object', 'array'))) {
            $fields = implode(', ', (array) $fields);
        }
        $sql = 'SELECT '.$fields.' FROM '.$this->_table;
        if ($_where) {
            $sql .= ' WHERE '.implode(' ', $_where);
        }
        if ($offset) {
            $offset = (int) $offset;
        }
        if ($limit) {
            $limit = (int) $limit;
        }
        if ($limit AND $offset) {
            $offset .= ',';
        }
        if ($offset OR $limit) {
            $sql .= " LIMIT $offset$limit";
        }
        $stmt = $this->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Model_Result', array($stmt, $this));
        $stmt->execute();
        return $stmt->fetch();
    }

    public function page($fields = '*', $page = 1, $filter = null, $per_page = 20)
    {
        $limit = $per_page;
        return $this->select($filter, $fields, $limit, $offset);
    }
}
