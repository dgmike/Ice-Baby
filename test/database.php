<?php
include 'config.php';
include 'ice/model.php';

class User extends Model {}

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
        $this->assertEqual('id_user', $user->_key, 'A chave primaria foi definida. %s');
    }
}
