<?php
include_once 'config.php';

class Database extends UnitTestCase
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
            'Retornou um objeto de resultado de PDO. %s');
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
        $result->_str = ':id_user: - :nome:';
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
        $users = $muser->select(array('id_user <' => 3));
        $this->assertEqual(2, $users->rows(),
            'Escolhido menor que três, logo dois. %s');
    }

    public function testSelectMinorEqual()
    {
        $muser = new User;
        $users = $muser->select(array('id_user <=' => 3));
        $this->assertEqual(3, $users->rows(),
            'Escolhido menor-igual a três, logo três. %s');
    }

    public function testSelectMultiSearch()
    {
        $muser = new User;
        $users = $muser->select(array('id_user >' => 3, 'id_user <' => 5));
        $this->assertEqual(1, $users->rows(),
            'Entre três e cinco: um. %s');
    }

    public function testAndOr()
    {
        $muser = new User;
        $users = $muser->select(array('id_user =' => 2, 'OR id_user =' => 5));
        $this->assertEqual(2, $users->rows(),
            'Apenas os que desejamos: dois. %s');
    }

    public function testWhereString()
    {
        $muser = new User;
        $users = $muser->select("nome = 'Michael'");
        $this->assertEqual(1, $users->rows(),
            'Apenas os que desejamos: Michael. %s');
    }

    public function testSelectField()
    {
        $muser = new User;
        $users = $muser->select(array('id_user <' => 4), 'count(*) as c');
        $this->assertEqual(3, $users->c, 
            'Pode-se passar, como segundo parametro, os fields que pretende'
            .'pegar. %s');
        $users = $muser->select(null, array('max(idade) i', 'nome', 'idade'));
        $this->assertEqual(26, $users->i, 
            'Pode-se passar um array de fields. %s');
    }

    public function testSubElement()
    {
        $muser = new User;
        $user = $muser->get(1);
        $this->assertEqual('Alice', $user->nome,
            'O nome do usuário vindo do banco de dados. %s');
        $this->assertEqual('Model_Result', get_class($user->telephone),
            'O que deveria ter em telephone não é um model_result? %s');
    }

    public function testSelectLimit()
    {
        $muser = new User;
        $users = $muser->select(null, '*', 3);
        $this->assertEqual(3, $users->rows(),
            'A quantidade passando pelo limit. %s');
    }

    public function testSelectLimitOffset()
    {
        $muser = new User;
        $users = $muser->select(null, '*', 3, 2);
        $this->assertEqual(3, $users->rows(),
            'A quantidade passando pelo limit. %s');
        $this->assertEqual('Rafael', $users->nome,
            'O primeiro escolhido é o Rafael devido ao offset. %s');
    }
}
