<?php include_once('config.php');

include_once('ice/app.php');

class AppTest extends UnitTestCase
{
    public function testCallClass()
    {
        ob_start(); app(array('.*' => 'BufferConstructTest'), '');
        $contents = get_content();
        $this->assertEqual('Hello!', $contents, 'Foi chamada a classe BufferConstructTest. %s');
    }

    public function testCallGetPostAny()
    {
        ob_start(); app(array('.*' => 'BufferMethodTest'), '', 'GET');
        $contents = get_content();
        $this->assertEqual('get', $contents, 'Foi chamado o metodo get pelo REQUEST. %s');

        ob_start(); app(array('.*' => 'BufferMethodTest'), '', 'POST');
        $contents = get_content();
        $this->assertEqual('post', $contents, 'Foi chamado o metodo post pelo REQUEST. %s');

        ob_start(); app(array('.*' => 'BufferMethodTest'), '', 'ANY');
        $contents = get_content();
        $this->assertEqual('any', $contents, 'Foi chamado o metodo any pelo REQUEST. %s');
    }

    public function testError()
    {
        ob_start(); app(array('invalida' => 'BufferMethodTest'), '');
        $contents = get_content();
        $this->assertEqual("Classe Erro\n404 - Page Not Found", $contents, 'A url nao foi encontrada na lista desejada. %s');
    }
}

class Ice_ErrorTest extends UnitTestCase
{
    public function testErrorDefault()
    {
        ob_start(); ice_error(404, 'Page Not Found', 'GET');
        $contents = get_content();
        $this->assertEqual($contents, "Classe Erro\n404 - Page Not Found", 'Página não encontrada. %s');
    }
}
