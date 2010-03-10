<?php
require_once 'model_result.php';

/**
 * Model 
 *
 * Extends this Model for each table you use
 * 
 * @package 
 * @version $id$
 * @copyright 1997-2005 The PHP Group
 * @author Michael Granados <michaelgranados@gmail.com> 
 * @license PHP Version 3.0 {@link http://www.php.net/license/3_0.txt}
 */
class Model
{
    static $_pdo = null;
    public $_table = null;
    public $_key = null;
    public $_str = null;
    public $_hasMany = array(); // deprecated
    public $_multipleJoin = array(); // usa tabela tercearia
    public $_relatedJoin = array();  // um para um
    
    public function __get($key)
    {
        return (property_exists($this, $key)) ? $this->$key : '';
    }

    /**
     * __construct - creates the Model Object
     * 
     * @param string $dns           String to connect @see PDO
     * @param string $username      Username to connect on database @see PDO
     * @param string $password      Password to connect on database @see PDO
     * @param array $driver_options Driver options @see PDO
     * @access public
     * @return void
     */
    public function __construct($dns=null, $username=null, $password=null, array $driver_options = array())
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
    /**
     * __call - Redirect to PDO
     * 
     * If you needs a PDO method, just use this object
     *
     * @param mixed $method 
     * @param mixed $params 
     * @access public
     * @return PDO_result
     */
    public function __call($method, $params)
    {
        return call_user_func_array(array(self::$_pdo, $method), $params);
    }

    /**
     * get - Get a row of table
     *
     * @TODO id will accept array where the $this->_key is an array
     * 
     * @param int $id ID you want to select
     * @access public
     * @return Model_Result
     */
    public function get($id, $oneLevel = true)
    {
        $sql = 'SELECT * FROM '.$this->_table.' WHERE '.$this->_key.' = ?';
        $stmt = self::$_pdo->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS,
            'Model_Result', array($stmt, $this, $oneLevel));
        $stmt->execute(array($id));
		
		#Tratando os erros de SQL
		$error = $stmt->errorInfo();
		if(isset($error[1])){
			throw new Exception ("<h1>SQL Error</h1> ({$error[1]}) {$error[2]}");
		}
		
        return $stmt->fetch();
    }

    /**
     * _where - Makes a where string
     * 
     * Makes a where string to helper the queries. You just need
     * to pass an associative array or the where string.
     *
     * Eg:
     * $table->_where(array(
     *      'name LIKE' => 'Mike',
     *      'age'       => 25,
     *      'OR age'    => 30,
     * ));
     * $table->_where('name LIKE "%Mike%" AND age = 25 OR age = 25');
     *
     * @param array|string $where Where params
     * @access public
     * @return void
     */
    public function _where($where = array())
    {
        $_where = array();
        if ('string'==gettype($where)) {
            $_where[] = $where;
        } elseif (!is_scalar($where)) {
            foreach ($where as $key=>$value) {
                // @TODO englobe array $value`s
                // if ('array' === gettype($value)) {
                //     $_where[] = '(' . $this->_where($value) . ')';
                // }
                $key = trim($key);
                if (strpos(trim($key), ' ')===false AND !is_null($value)) {
                    $key .= ' = ';
                }
                if ($_where AND !preg_match('@^(and|or)\b@i', $key)) {
                    $key = "AND $key";
                }
                if($value != null) {
					self::$_pdo->quote($value); 
					$_where[] = "$key '$value'";
				} else {
					$value = 'is NULL';
					$_where[] = "$key $value";
				} 
                
            }
        }
        return $_where ? ' WHERE '.implode(' ', $_where) : '';
    }

	public function find($where = array(), $fields = '*', $limit = null,
                            $offset = null, $order = null)
	{
		
		return $this->select($where, $fields, $limit, $offset, $order, 'all');	
	}

    /**
     * select - Do a simple select
     * 
     * It just select a resultset from a table
     *
     * @param array  $where  An where to select @see $this->_where
     * @param string $fields Fields to select, allways select the $this->_key
     * @param int    $limit  Limit to your select
     * @param int    $offset Offset to your select
     * @access public
     * @return void
     */
    public function select($where = array(), $fields = '*', $limit = null,
                            $offset = null, $order = null, $fetch = null)
    {
        if (!$fields) {
            $fields = '*';
        }
        if (in_array(gettype($fields), array('object', 'array'))) {
            $fields = implode(', ', (array) $fields);
        }

        $where = is_null($where)?array():$where;
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
        if(!is_null($order)){
            $sql .= " ORDER BY {$order}";
        }
		if (!is_null($offset) OR !is_null($limit)) {
            $sql .= " LIMIT $offset$limit";
        }
		echo $sql;
        $stmt = self::$_pdo->prepare($sql);
        if ($fetch == 'all') {
            $stmt->setFetchMode(PDO::FETCH_OBJ);
        } elseif ($fetch == 'one') {
            $stmt->setFetchMode(PDO::FETCH_CLASS,
                'Model_Result', array($stmt, $this), false);
        } else {
            $stmt->setFetchMode(PDO::FETCH_CLASS,
                'Model_Result', array($stmt, $this));
        }

        $stmt->execute();
        #Tratando os erros de SQL
        $error = $stmt->errorInfo();
        if(isset($error[1])){
            throw new Exception ("<h1>SQL Error</h1> ({$error[1]}) {$error[2]}");
        }

        if ($fetch == 'all') {
            return $stmt->fetchAll();
        }

        return $stmt->fetch();
    }

    /**
     * page - Makes a simple select using easy page limit
     * 
     * Its easy to make a paginable result to your project, just use
     * this method.
     *
     * @param string $fields   String of fields to select, allways select $this->_key
     * @param int    $page     Page to select
     * @param mixed  $filter   Filter to your select, just a where clause @see $this->_where
     * @param int    $per_page Quantity per page
     * @access public
     * @return void
     */
    public function page($fields = '*', $page = 1, $filter = null, $per_page = 20)
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

    /**
     * insert - Insert a new row on the database
     *
     * An easy way to insert new row on table, just pass your data as associative array
     *
     * Eg:
     * $this->insert(array(
     *     'name' => 'Mike',
     *     'age'  => 25,
     * ));
     *
     * @param array  $data  An array to insert 
     * @param string $table Pass the table if your object does not have a table
     * @access public
     * @return void
     */
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
		$error = $stmt->errorInfo();
		if(isset($error[1])){
            throw new Exception ("<h1>SQL Error</h1> ({$error[1]}) {$error[2]}");
        }        
		return $stmt->execute($data);
    }

    /**
     * update - Updates rows of table
     * 
     * Pass the associative array to update one or more rows on the database
     *
     * Eg:
     * $this->update(array(
     *     'name' => 'Rodrigo',
     * ), array('id' => 21));
     *
     * @param mixed $data  The data to put on 
     * @param array $where The where to update @see $this->_where
     * @param mixed $table Table to update, optional. By default @use $this->_table
     * @access public
     * @return void
     */
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

        foreach ($data as $key => $value) {
            $_data[] = "$key = ?";
        }

        $where = $this->_where($where);

        $sql   = sprintf($sql, $table, implode(', ', $_data), $where);

        $stmt  = self::$_pdo->prepare($sql);
        $data = array_values($data);

        $stmt->execute($data);
		
		#Tratando os erros de SQL
		$error = $stmt->errorInfo();
		if(isset($error[1]) AND !is_null($error[1])){
	        throw new Exception ("<h1>SQL Error</h1> ({$error[1]}) {$error[2]}");
		}
	}

    /**
     * save - Saves the data on table. Updating or inserting data
     * 
     * Pass an associative array, if the array has the $this->_key value, just update
     * else create a new record.
     *
     * @param array $data 
     * @param array $where 
     * @param mixed $table 
     * @access public
     * @return void
     */
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
		$error = $stmt->errorInfo();
		if(isset($error[1])){
            throw new Exception ("<h1>SQL Error</h1> ({$error[1]}) {$error[2]}");
        }

        return $stmt->fetch();
    }

    public function delete($id)
    {
        $sql = 'DELETE FROM '.$this->_table.' WHERE '.$this->_key.' = ?';
        $stmt = self::$_pdo->prepare($sql);
		$error = $stmt->errorInfo();
		if(isset($error[1])){
            throw new Exception ("<h1>SQL Error</h1> ({$error[1]}) {$error[2]}");
        }

        return $stmt->execute(array($id));
    }
}
