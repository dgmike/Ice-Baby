<?php
include 'config.php';
include 'ice/model.php';

define('DB_DNS', 'sqlite://'
                .dirname(__FILE__)
                .DIRECTORY_SEPARATOR.'banco.db');

if (!class_exists('User')) {
    class User extends Model { 
        var $hasMany = array('telephone');
    }
}

if (!class_exists('Telephone')) {
    class Telephone extends Model { 
    }
}

class Database2Test extends UnitTestCase
{
    public function setUp()
    {
        createDatabase();
    }

    public function testPage()
    {
        $muser = new User;
        $users = $muser->page($fields   = 'nome', 
                              $page     = 1, 
                              $filter   = null, 
                              $per_page = 4);
        $this->assertEqual('Model_Result', get_class($users),
            'O que deveria ser um model_result? %s');
        $this->assertEqual(4, $users->rows(),
            'Deveria voltar a quantidade máxima de resultados. %s');
        $this->assertEqual('Alice', $users->nome,
            'O primeiro escolhido é Alice. %s');
        $this->assertEqual(2, $users->pages(),
            'A quantidade de páginas: 2. %s');
        // Página dois
        $users = $muser->page($fields   = 'nome', 
                              $page     = 2, 
                              $filter   = null, 
                              $per_page = 4);
        $this->assertEqual('Model_Result', get_class($users),
            'O que deveria ser um model_result? %s');
        $this->assertEqual(2, $users->rows(),
            'Deveria voltar a quantidade máxima de resultados. %s');
        $this->assertEqual('Diego', $users->nome,
            'O primeiro escolhido é Diego. %s');
        $this->assertEqual(2, $users->pages(),
            'A quantidade de páginas: 2. %s');
    }

    public function testTableRow()
    {
        $muser = new User;
        $users = $muser->select(null, 'nome, idade', 2);
        $this->assertEqual(
            '<tr><td>Alice</td><td>20</td></tr>', $users->tableRow(),
            'A linha do resultado obtido. %s'
        );
    }

    public function testTableRowShowmeWhatINeed()
    {
        $muser = new User;
        $users = $muser->select(null, '*', 2);
        $this->assertEqual(
            '<tr><td>Alice</td></tr>', 
            $users->tableRow(null, null, array('nome')),
            'A linha do resultado obtido. %s'
        );
    }

    public function testTableRowBeforeAfter()
    {
        $muser = new User;
        $users = $muser->select(null, 'nome, idade', 2);
        $this->assertEqual(
            $users->tableRow('<td><a href="?nome=:nome:">Editar</a></td>'),
            '<tr><td><a href="?nome=Alice">Editar</a></td>'.
            '<td>Alice</td><td>20</td></tr>', 
            'A linha do resultado obtido. %s'
        );
        $muser = new User;
        $users = $muser->select(null, 'nome, idade', 2);
        $this->assertEqual(
            $users->tableRow(null,'<td><a href="?nome=:nome:">Editar</a></td>'),
            '<tr><td>Alice</td><td>20</td>'.
            '<td><a href="?nome=Alice">Editar</a></td></tr>', 
            'A linha do resultado obtido. %s'
        );
    }

    public function testTableRows()
    {
        $muser = new User;
        $users = $muser->select(null, 'nome, idade', 2);
        $this->assertEqual(
            $users->tableRows(),
            "<tr><td>Alice</td><td>20</td></tr>\n".
            "<tr><td>Michael</td><td>21</td></tr>",
            'Todas as linhas da tabela gerada. %s'
        );
    }

    public function testTableRowsAfterBeforeAndFields()
    {
        $muser = new User;
        $users = $muser->select(null, '*', 2);
        $this->assertEqual(
            $users->tableRows(
                "<td>#:id_user:</td>",
                "<td><a href='#:id_user:'>editar</a></td>",
                array('nome')
            ),
            "<tr><td>#1</td><td>Alice</td><td><a href='#1'>editar</a></td></tr>\n".
            "<tr><td>#2</td><td>Michael</td><td><a href='#2'>editar</a></td></tr>",
            'Todas as linhas da tabela gerada. %s'
        );
    }

    public function testTableRowsEmptyFields()
    {
        $muser = new User;

        $users = $muser->select(null, 'id_user, nome', 2);
        $this->assertEqual(
            $users->tableRows("<td>#:id_user:</td>", null, null),

            "<tr><td>#1</td><td>1</td><td>Alice</td></tr>\n".
            "<tr><td>#2</td><td>2</td><td>Michael</td></tr>",

            "Gerou as linhas com os dados completos. %s"
        );

        $users = $muser->select(null, 'id_user, nome', 2);
        $this->assertEqual(
            $users->tableRows("<td>#:id_user:</td>", null, array()),

            "<tr><td>#1</td></tr>\n".
            "<tr><td>#2</td></tr>",

            "Gerou as linhas com os dados completos. %s"
        );
    }

    public function testInsert()
    {
        $model = new Model;
        $muser = new User;
        $total = $muser->select(null, 'COUNT(*) as C')->C;
        $this->assertEqual(6, (int) $total,
            'Quantidade defult de usuarios. %s'
        );
        $this->assertTrue(
            $muser->insert(array(
                'nome'  => 'Ricardo',
                'idade' => '29',
            )),
            'Deveria conseguir inserir um usuario. %s'
        );

        $total = $muser->select(null, 'COUNT(*) as C')->C;
        $this->assertEqual(7, (int) $total,
            'Adicionou mais um usuário. %s'
        );
        $data = $muser->get(7)->data();
        unset($data['telephone']);
        $this->assertEqual($data,
            array(
                'id_user' => '7',
                'nome'    => 'Ricardo',
                'idade'   => '29',
            ),
            'Retornou o objeto inserido. %s'
        );
    }

    public function testInsertModel()
    {
        $muser = new User;
        $total = $muser->select(null, 'COUNT(*) as C')->C;
        $this->assertEqual(6, (int) $total,
            'Quantidade defult de usuarios. %s'
        );
        $model = new Model;
        $this->assertTrue(
            $model->insert(array(
               'nome'  => 'Ricardo',
               'idade' => '29',
            ), 'user'),
            'Os dados foram salvos no banco. %s');
        $total = $muser->select(null, 'COUNT(*) as C')->C;
        $this->assertEqual(7, (int) $total,
            'Deve funcionar passando a tabela como segundo parâmetro. %s'
        );
        $data = $muser->get(7)->data();
        unset($data['telephone']);
        $this->assertEqual($data,
            array(
                'id_user' => '7',
                'nome'    => 'Ricardo',
                'idade'   => '29',
            ),
            'Retornou o objeto inserido. %s'
        );
    }
}
