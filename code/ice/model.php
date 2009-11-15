<?php
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

    public function select()
    {
        $sql = 'SELECT * FROM '.$this->_table;
        $stmt = $this->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Model_Result', array($stmt, $this));
        $stmt->execute();
        return $stmt->fetch();
        
    }
}

class Model_Result
{
    private $_stmt = null;
    private $_model = null;
    private $_data = array();

    public function __construct($stmt, $model)
    {
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Model_Result', array($stmt, $model));
        $this->_stmt = $stmt;
        $this->_model = $model;
    }

    public function __set($key, $value)
    {
        $this->_data[$key] = $value;
    }

    public function __get($key)
    {
        return isset($this->_data[$key]) ? $this->_data[$key] : '';
    }

    public function __call($method, $args) 
    {
        $return = call_user_func_array(array($this->_stmt, $method), $args);
        if ($return instanceof Model_Result) {
            $this->_data = $return->data();
        }
        return $return;
    }

    public function data()
    {
        return $this->_data;
    }

    public function rows()
    {
        $sql = $this->_stmt->queryString;
        $db = new Model;
        $r = $db->query($sql);
        $c = count($r->fetchAll());
        return $c;
    }

    public function __toString()
    {
        $string = $this->_model->str;
        preg_match_all('@:\w+:@', $string, $matches);
        foreach ($matches[0] as $item) {
            $valor  = $this->_data[substr($item, 1, -1)];
            $string = str_replace($item, $valor, $string);
        }
        return $string;
    }

    public function setStr($string)
    {
        $this->_model->str = $string;
    }
}
