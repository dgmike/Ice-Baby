<?php
include_once 'config.php';
include_once 'ice/model.php';

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

    public function testUpdate()
    {
        $muser = new User;
        $user  = $muser->get(1);
        $this->assertEqual('Alice', $user->nome, 'Nome da primeira pessoa. %s');
        $muser->update(array(
            'id_user' => '1',
            'nome'    => 'Elias',
        ));
        $user  = $muser->get(1);
        $this->assertEqual('Elias', $user->nome, 'Nome da primeira pessoa. %s');
    }

    public function testSave()
    {
        $muser = new User;
        $this->assertEqual(6, $muser->select(null, 'count(*) as C')->C,
            'Total de usuarios é 6. %s');
        $this->assertEqual('Michael',
            $muser->get(2)->nome,
            'O segundo usuário se chama Michael. %s'
        );
        $muser->save(array(
            'id_user' => '2',
            'nome'    => 'Alex de Azevedo',
        ));
        $this->assertEqual('Alex de Azevedo',
            $muser->get(2)->nome,
            'O usuário dois trocou de nome. %s'
        );
        $muser->save(array(
            'nome'  => 'Maria',
            'idade' => '20',
        ));
        $this->assertEqual(7, $muser->select(null, 'count(*) as C')->C,
            'Total de usuarios é 7. %s');
        $data = $muser->get(7)->data();
        unset($data['telephone']);
        $this->assertEqual(array(
            'id_user' => '7',
            'nome'    => 'Maria',
            'idade'   => '20',
        ), $data, 'O usuário que acaba de ser inserido. %s');
    }

    public function testSaveAlterned()
    {
        $muser = new User;
        $user  = $muser->get(1);
        $this->assertEqual('Alice', $user->nome,
            'O nome do primeiro usuario. %s');
        $user->nome = 'Carlos';
        $user->save();

        $user  = $muser->get(1);
        $this->assertEqual('Carlos', $user->nome,
            'O nome do primeiro usuario apos o save. %s');
    }

    public function testSaveUnset()
    {
        $muser = new User;
        $user  = $muser->get(1);
        $this->assertEqual('Alice', $user->nome,
            'O nome do primeiro usuario. %s');
        $user->nome = 'Carlos';
        unset($user);

        $user  = $muser->get(1);
        $this->assertEqual('Carlos', $user->nome,
            'O nome do primeiro usuario apos o save. %s');
    }

    public function testPagination()
    {
        $mtel = new Telephone;
        $tels = $mtel->page('*', 1, null, 3);
        $this->assertEqual(5, $tels->pages(), 'Total de páginas. %s');
        $this->assertEqual(<<<EOF
<ul class="paginacao">

<li class="primeiro"><a href="?p=1">Primeiro</a></li>
<li class="anterior"><a href="?p=1">Anterior</a></li>

<li><a href="?p=1">1</a></li>
<li class="atual">2</li>
<li><a href="?p=3">3</a></li>
<li><a href="?p=4">4</a></li>
<li><a href="?p=5">5</a></li>

<li class="proximo"><a href="?p=3">Próximo</a></li>
<li class="ultimo"><a href="?p=5">Último</a></li>

</ul>
EOF
        , $tels->pagination(2), 'Mostra a paginacao. %s');
    }
}
