<?php

class Model extends PDO
{
    public $_table = null;
    public $_key = null;

    public function __construct()
    {
        if (!$this->_table) {
            $this->_table = strtolower(get_class($this));
        }
        if (!$this->_key) {
            $this->_key = 'id_'.$this->_table;
        }
    }
}
