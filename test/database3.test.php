<?php
include_once 'config.php';

class Database3 extends UnitTestCase
{
    public function setUp()
    {
        createDatabase();
    }

    public function testRelatedJoin()
    {
        $madmin = new Admin;
        $admin  = $madmin->get(1);
        $this->assertEqual('Model_Result', get_class($admin->roles),
            'Pelo menos temos um Model_Result. %s');
        $this->assertEqual(2, $admin->roles->rows(),
            'O resultado do join deve ser 2. %s');
        $this->assertEqual('bob', $admin->login,
            'Estamos certos do usuário. %s');
        $this->assertEqual('admin', $admin->roles->name,
            'Bob é administrador. %s');
        $admin->roles->fetch();
        $this->assertEqual('editor', $admin->roles->name,
            'Bob também é editor. %s');

        $admin = $madmin->get(2);
        $this->assertEqual('tim', $admin->login,
            'Pegamos o Tim. %s');
        $this->assertEqual(1, $admin->roles->rows(),
            'O tim é apenas administrador. %s');
        $this->assertEqual('admin', $admin->roles->name,
            'Ele é administrador. %s');

        $admin = $madmin->get(3);
        $this->assertEqual('jay', $admin->login,
            'Agora, com o Jay. %s');
        $this->assertFalse($admin->roles, 'O Jay não tem privilegios. %s');
    }

    public function testRelatedJoinInRules()
    {
        $mrole = new Role;
        $rule  = $mrole->get(1);
        $this->assertEqual('admin', $rule->name,
            'Apenas os editores. %s');
        $this->assertEqual('bob', $rule->admins->login,
            'O primeiro administrador é o BOB. %s');
        $rule->admins->mk_related_joins();
        $this->assertEqual('admin', $rule->admins->roles->name,
            'Basta usar mk_related_joins para ter acesso ao seus filhos. %s');
    }

    public function testRemoveRelatedJoin()
    {
       $madmin = new Admin;
       $admin  = $madmin->get(1);
       $this->assertEqual('admin', $admin->roles->name,
           'Apenas verificando a existencia da regra. %s');
       $admin->removeRole(1);
       $this->assertEqual(1, $admin->roles->rows(),
           'Removida a regra. %s');
       $this->assertEqual('editor', $admin->roles->name,
           'Não existe nada alem de editor. %s');
    }

    public function testRemoveRelatedJoinObject()
    {
       $madmin = new Admin;
       $admin  = $madmin->get(1);
       $mrole  = new Role;
       $role   = $mrole->get(1);
       $this->assertEqual('admin', $admin->roles->name,
           'Apenas verificando a existencia da regra. %s');
       $admin->removeRole($role);
       $this->assertEqual(1, $admin->roles->rows(),
           'Removida a regra. %s');
       $this->assertEqual('editor', $admin->roles->name,
           'Não existe nada alem de editor. %s');
    }

    public function testAddRelatedJoin()
    {
        $madmin = new Admin;
        $admin  = $madmin->get(3);
        $this->assertFalse($admin->roles, 'Nao existem regras. %s');
        $admin->addRole(1);
        $this->assertEqual(1, $admin->roles->rows(),
            'Adicionou mais uma regra. %s');
        $this->assertEqual('admin', $admin->roles->name,
            'Adicinou a regra de administrador. %s');
    }
}
