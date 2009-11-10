<?php
class Model extends PDO
{
    public $_table = null;
    public $_key = null;

    public function __construct($dns=null, $username=null, $password=null,
            array $driver_options = array())
    {
        if (!$this->_table) {
            $this->_table = strtolower(get_class($this));
        }
        if (!$this->_key) {
            $this->_key = 'id_'.$this->_table;
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
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Model_Result');
        $stmt->execute(array($id));
        return $stmt->fetch();
    }

    public function select()
    {
        $sql = 'SELECT * FROM '.$this->_table;
        $stmt = $this->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Model_Result');
        return $stmt->execute();
        
    }
}

class Model_Result
{
}
