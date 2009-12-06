<?php
define('TEST_ICE_BASE_URL', 'http://localhost/Ice-Baby/test');

$path = dirname(dirname(__FILE__))
        .DIRECTORY_SEPARATOR.'code';

define('DB_DNS', 'sqlite:'
                .dirname(__FILE__)
                .DIRECTORY_SEPARATOR.'banco.db');


set_include_path($path.PATH_SEPARATOR.get_include_path());

include_once dirname(dirname(__FILE__))
             .DIRECTORY_SEPARATOR.'simpletest'
             .DIRECTORY_SEPARATOR.'autorun.php';

function get_content()
{
    $content = ob_get_contents();
    ob_end_clean();
    return $content;
}

class BufferConstructTest
{
    public function __construct()
    {
        print 'Hello!';
    }

    public function __call($method, $args) { }
}

class BufferMethodTest
{
    public function __call($method, $args)
    {
        print $method;
    }
}

class BufferMethodArgsTest
{
    public function __call($method, $args)
    {
        print "Called $method\n";
        var_export($args);
    }
}

class BufferNotMethod
{
}

class Error
{
    public $error_code;
    public $error_message;

    public function __construct()
    {
        print "Classe Erro\n";
    }

    public function get() 
    {
        print $this->error_code.' - '.$this->error_message;
    }
}

/* Creates a base database */
function createDatabase()
{
    $file = dirname(__FILE__).DIRECTORY_SEPARATOR.'banco.db';
    Model::$_pdo = null;
    if (file_exists($file)) {
        unlink($file);
    }
    $conn = new PDO(DB_DNS);
    $conn->query('CREATE TABLE user (
        id_user INTEGER PRIMARY KEY,
        nome TEXT,
        idade INTEGER
    )');
    $conn->query("INSERT INTO user (nome, idade) VALUES ('Alice', 20)");
    $conn->query("INSERT INTO user (nome, idade) VALUES ('Michael', 21)");
    $conn->query("INSERT INTO user (nome, idade) VALUES ('Rafael', 24)");
    $conn->query("INSERT INTO user (nome, idade) VALUES ('Elcio', 26)");
    $conn->query("INSERT INTO user (nome, idade) VALUES ('Diego', 21)");
    $conn->query("INSERT INTO user (nome, idade) VALUES ('Julio', 18)");

    $conn->query('CREATE TABLE telephone (
        id_telephone INTEGER PRIMARY KEY,
        id_user INTEGER,
        number TEXT
    )');

    $conn->query("INSERT INTO telephone (id_user, number) 
                    VALUES (1, '555-3869')");
    $conn->query("INSERT INTO telephone (id_user, number) 
                    VALUES (1, '555-7845')");
    $conn->query("INSERT INTO telephone (id_user, number) 
                    VALUES (1, '555-2346')");
    $conn->query("INSERT INTO telephone (id_user, number) 
                    VALUES (1, '555-4876')");
    $conn->query("INSERT INTO telephone (id_user, number) 
                    VALUES (2, '555-4200')");
    $conn->query("INSERT INTO telephone (id_user, number) 
                    VALUES (2, '555-1983')");
    $conn->query("INSERT INTO telephone (id_user, number) 
                    VALUES (3, '555-1983')");
    $conn->query("INSERT INTO telephone (id_user, number) 
                    VALUES (4, '555-6165')");
    $conn->query("INSERT INTO telephone (id_user, number) 
                    VALUES (4, '555-4613')");
    $conn->query("INSERT INTO telephone (id_user, number) 
                    VALUES (5, '555-1452')");
    $conn->query("INSERT INTO telephone (id_user, number) 
                    VALUES (5, '555-1876')");
    $conn->query("INSERT INTO telephone (id_user, number) 
                    VALUES (5, '555-0125')");
    $conn->query("INSERT INTO telephone (id_user, number) 
                    VALUES (6, '555-0012')");

    $conn->query('CREATE TABLE admin (
        id_admin INTEGER PRIMARY KEY,
        login text
    )');

    $conn->query("INSERT INTO admin (login) VALUES ('bob')");
    $conn->query("INSERT INTO admin (login) VALUES ('tim')");
    $conn->query("INSERT INTO admin (login) VALUES ('jay')");

    $conn->query('CREATE TABLE role (
        id_role INTEGER PRIMARY KEY,
        name text
    )');

    $conn->query("INSERT INTO role (name) VALUES ('admin')");
    $conn->query("INSERT INTO role (name) VALUES ('editor')");

    $conn->query('CREATE TABLE admin_role (
        id_admin INTEGER KEY,
        id_role INTEGER KEY
    )');

    $conn->query("INSERT INTO admin_role (id_admin, id_role) VALUES (1, 1)");
    $conn->query("INSERT INTO admin_role (id_admin, id_role) VALUES (1, 2)");
    $conn->query("INSERT INTO admin_role (id_admin, id_role) VALUES (2, 1)");
}
