<?php
require_once 'model_result.php';

class Model extends PDO
{
    public $_table = null;
    public $_key = null;
    public $str = null;

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

    public function select($where=array())
    {
        $_where = array();
        foreach ($where as $key=>$value) {
            $_where[] = "$key = '$value'";
        }
        $sql = 'SELECT * FROM '.$this->_table;
        if ($_where) {
            $sql .= ' WHERE '.implode(' AND ', $_where);
        }
        $stmt = $this->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Model_Result', array($stmt, $this));
        $stmt->execute();
        return $stmt->fetch();
    }
}
