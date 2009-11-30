<?php

class Model_Result
{
    private $_stmt    = null;
    private $_model   = null;
    private $_data    = array();
    private $_pages   = null;
    private $_altated = false;

    public function __construct($stmt, $model)
    {
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Model_Result', 
            array($stmt, $model));
        $this->_stmt = $stmt;
        $this->_model = $model;
        foreach ($model->hasMany as $item) {
            $i = ucfirst($item);
            $o = new $i;
            $where = array(
                $model->_key => $this->_data[$model->_key]
            );
            $this->_data[$item] = $o->select($where);
        }
    }

    public function __set($key, $value)
    {
        if ($this->_model) {
            $this->_altated = true;
        }
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

    public function pages($pages=null)
    {
        if ($pages) {
            $this->_pages = $pages;
        }
        return $this->_pages;
    }

    public function tableRow($before=null, $after = null, $fields = null)
    {
        $data       = $this->data();
        $data_trued = array_fill_keys($this->_model->hasMany, true);
        $data       = array_diff_key($data, $data_trued);
        if ($fields !== null) {
            $data = array_intersect_key($data, array_fill_keys($fields, true));
        }
        foreach(array('before', 'after') as $item) {
            foreach ($this->data() as $key=>$value) {
                $$item = str_replace(":{$key}:", $value, $$item);
            }
        }
        $row = implode('</td><td>', $data);
        if ($row) {
            $row = "<td>$row</td>";
        }
        return "<tr>$before$row$after</tr>";
    }

    public function tableRows($before=null, $after = null, $fields = null)
    {
        $table = array();
        do {
            $table[] = $this->tableRow($before, $after, $fields);
        } while ($this->fetch());
        return implode("\n", $table);
    }

    public function save()
    {
        $data = $this->_data;
        $keys = array_fill_keys($this->_model->hasMany, true);
        $data = array_diff_key($data, $keys);
        return $this->_model->save($data);
    }

    public function pagination($page)
    {
        if (!$this->pages() OR $this->pages() == 1) {
            return '<!-- No pages defined for pagination! -->';
        }
        $init = $last = array();
        if ($page > 1) {
            $init = array(
                    '',
                    '<li class="primeiro"><a href="?p=1">Primeiro</a></li>',
                    '<li class="anterior"><a href="?p='.($page-1).'">Anterior</a></li>',
                    ''
                    );
        }
        if ($page < $this->pages()) {
            $last = array(
                    '',
                    '<li class="proximo"><a href="?p='.($page+1).'">Próximo</a></li>',
                    '<li class="ultimo"><a href="?p='.$this->pages().'">Último</a></li>',
                    '',
                    );
        }
        $pages = array();
        foreach (range(1, $this->pages()) as $p) {
            $class = '';
            $link  = '<a href="?p='.$p.'">'.$p.'</a>';
            if ($p==$page) {
                $class = ' class="atual"';
                $link  = $p;
            }
            $pages[] = "<li$class>$link</li>";
        }
        $paginacao = array_merge($init, $pages, $last);
        $paginacao = implode("\n", $paginacao);
        return "<ul class=\"paginacao\">\n$paginacao\n</ul>";
    }

    public function __destruct()
    {
        if ($this->_altated) {
            $this->save();
        }
    }
}
