<?php
require_once 'model_result.php';

class Model
{
    static $_pdo = null;
    public $_table = null;
    public $_key = null;
    public $_str = null;
    public $_hasMany = array();
    public $_multipleJoin = array();
    public $_relatedJoin = array();

    public function __construct($dns=null, $username=null, $password=null,
            array $driver_options = array())
    {
        if (!$this->_table) {
            $this->_table = strtolower(get_class($this));
        }
        if (!$this->_key) {
            $this->_key = 'id_'.$this->_table;
        }
        if (!$this->_str) {
            $this->_str = ":id_{$this->_table}:";
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
        if (!self::$_pdo) {
            self::$_pdo = new PDO($dns, $username, $password, $driver_options);
        }
    }

    public function __call($method, $params)
    {
        return call_user_func_array(array(self::$_pdo, $method), $params);
    }

    public function get($id)
    {
        $sql = 'SELECT * FROM '.$this->_table.' WHERE '.$this->_key.' = ?';
        $stmt = self::$_pdo->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS,
            'Model_Result', array($stmt, $this));
        $stmt->execute(array($id));
        return $stmt->fetch();
    }

    public function _where($where = array())
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
                self::$_pdo->quote($value);
                $_where[] = "$key '$value'";
            }
        }
        return $_where ? ' WHERE '.implode(' ', $_where) : '';
    }

    public function select($where = array(), $fields = '*', $limit = null,
                            $offset = null)
    {
        if (!$fields) {
            $fields = '*';
        }
        if (in_array(gettype($fields), array('object', 'array'))) {
            $fields = implode(', ', (array) $fields);
        }
        $sql = 'SELECT '.$fields.' FROM '.$this->_table;
        $sql .= $this->_where($where);
        if (!is_null($offset)) {
            $offset = (int) $offset;
        }
        if (!is_null($limit)) {
            $limit = (int) $limit;
        }
        if (!is_null($limit) AND !is_null($offset)) {
            $offset .= ',';
        }
        if (!is_null($offset) OR !is_null($limit)) {
            $sql .= " LIMIT $offset$limit";
        }
        $stmt = self::$_pdo->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS,
            'Model_Result', array($stmt, $this));
        $stmt->execute();
        return $stmt->fetch();
    }

    public function page($fields = '*', $page = 1, $filter = null,
                         $per_page = 20)
    {
        $offset = ($page-1) * $per_page;
        $limit  = $per_page;
        $total  = $this->select($filter, 'count(*) as C');
        if ($total) {
            $pages  = ceil($total->C/$per_page);
        } else {
            $pages = 0;
        }
        $return = $this->select($filter, $fields, $limit, $offset);
        if ($return) {
            $return->pages($pages);
        }
        return $return;
    }

    public function insert($data = array(), $table = null)
    {
        $sql   = 'INSERT INTO %s (%s) VALUES (%s)';
        $data  = (array) $data;
        if (null === $table) {
            $table = $this->_table;
        }
        $keys = implode(', ', array_keys($data));
        $data = array_values($data);
        $vals = implode(', ', array_fill(0, count($data), '?'));
        $sql  = sprintf($sql, $table, $keys, $vals);
        $stmt = self::$_pdo->prepare($sql);
        return $stmt->execute($data) OR print_r($stmt->errorInfo());
    }

    public function update($data, array $where=array(), $table = null)
    {
        $sql = "UPDATE %s SET %s %s"; # table, key=value, where
        if (!$table) {
            $table = $this->_table;
        }
        $data  = (array) $data;
        $_data = array();
        if (isset($data[$this->_key])) {
            $where[$this->_key] = $data[$this->_key];
            unset($data[$this->_key]);
        }
        foreach (array_keys($data) as $key) {
            $_data[] = "$key = ?";
        }
        $where = $this->_where($where);
        $sql   = sprintf($sql, $table, implode(', ', $_data), $where);
        $stmt  = self::$_pdo->prepare($sql);
        $stmt->execute(array_values($data));
    }

    public function save($data, array $where = array(), $table = null)
    {
        if (isset($data[$this->_key])) {
            $this->update($data, $where, $table);
        } else {
            $this->insert($data, $table);
        }
    }

    public function remove($where)
    {
        $sql = 'DELETE FROM '.$this->_table.$this->_where($where);
        $stmt = self::$_pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function delete($id)
    {
        $sql = 'DELETE FROM '.$this->_table.' WHERE '.$this->_key.' = ?';
        $stmt = self::$_pdo->prepare($sql);
        return $stmt->execute(array($id));
    }
}