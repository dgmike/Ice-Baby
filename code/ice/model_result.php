<?php

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
