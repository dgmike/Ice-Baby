<?php
include 'config.php';
include 'ice/model.php';

define('DB_DNS', 'sqlite://'
                .dirname(__FILE__)
                .DIRECTORY_SEPARATOR.'banco.db');

class User extends Model { }

class ModelTest extends UnitTestCase
{
    public function setUp()
    {
        createDatabase();
    }

    public function testConnect()
    {
        $user = new User;
        $this->assertEqual('user', $user->_table, 'A tabela foi definida. %s');
        $this->assertEqual('id_user', $user->_key, 
                'A chave primaria foi definida. %s');
    }

    public function testSimpleQuery()
    {
        $user = new User;
        $result = $user->query('SELECT count(*) as C FROM user');
        $this->assertEqual(get_class($result), 'PDOStatement', 
            'Retornou um objeto de resultado de PDO');
        $this->assertEqual(6, $result->fetchObject()->C,
            'Deveria retornar seis. %s');
    }

    public function testGet()
    {
        $user = new User;
        $result = $user->get(1);
        $this->assertEqual(get_class($result), 'Model_Result',
            'O retorno do get é um Model_Result. %s');
        $this->assertEqual('Alice', $result->nome, 
            'Deveria retornar o primeiro resultado');
    }

    public function testSelect()
    {
        $user = new User;
        $result = $user->select();
        $this->assertEqual(get_class($result), 'Model_Result',
            'O retorno do get é um Model_Result. %s');
        $this->assertEqual('Alice', $result->nome, 
            'O nome da primeira pessoa deve ser Alice. %s');
        $result->fetch();
        $this->assertEqual('Michael', $result->nome, 
            'O nome da segunda pessoa deve ser Michael. %s');
    }

    public function testResultEqualsReturnFetch()
    {
        $user = new User;
        $result = $user->select();
        $this->assertEqual('Alice', $result->nome,
            'O primeiro usuario deve ser Alice. %s');
        $returned = $result->fetch();
        $this->assertEqual($result, $returned, 
            'O result deve ser igual ao returned');
    }

    public function testCount()
    {
        $user = new User;
        $result = $user->select();
        $this->assertEqual(6, $result->rows(), 
            'O rows deve vir do PDOStatement, logo deve ser seis. %s');
    }

    public function testToString()
    {
        $user = new User;
        $result = $user->get(1);
        $this->assertEqual(1, $result->id_user, 
            'O codigo do primeiro usuario. %s');
        $this->assertEqual('Alice', $result->nome,
            'O nome do primeiro usuario. %s');
        $this->assertEqual('1', (string) $result, 
            'O nome conforme definido em $str. %s');
    }

    public function testSetString()
    {
        $user = new User;
        $result = $user->get(1);
        $this->assertEqual(1, $result->id_user, 
            'O codigo do primeiro usuario. %s');
        $this->assertEqual('Alice', $result->nome,
            'O nome do primeiro usuario. %s');
        $this->assertEqual('1', (string) $result, 
            'O nome conforme definido em $str. %s');
        $result->str = ':id_user: - :nome:';
        $this->assertEqual('1', (string) $result, 
            'O nome conforme definido em $str. %s');
        $result->setStr(':id_user: - :nome:');
        $this->assertEqual('1 - Alice', (string) $result, 
            'O nome conforme definido em $str. %s');
    }

    public function testSelectSingleParams()
    {
        $muser = new User;
        $users = $muser->select(array('nome' => 'Michael'));
        $this->assertEqual(2, $users->id_user,
            'Pode-se fazer filtros com array. %s');
        $users = $muser->select(array('nome' => 'Alice'));
        $this->assertEqual(1, $users->id_user,
            'Pode-se fazer filtros com array. %s');
    }

    public function testSelectDiferent()
    {
        $muser = new User;
        $users = $muser->select(array('nome !=' => 'Alice'));
        $this->assertEqual(5, $users->rows(),
            'Fizemos um diferente, logo deve ser cinco. %s');
    }

    public function testSelectMinor()
    {
        $muser = new User;
        $users = $muser->select(array('id_user <' => 3),
            'Escolhido menor que três, logo dois. %s');
    }

    public function testSelectMinorEqual()
    {
        $muser = new User;
        $users = $muser->select(array('id_user <=' => 3),
            'Escolhido menor-igual a três, logo três. %s');
    }
}
