<?php
include_once 'config.php';
include_once dirname(dirname(__FILE__))
            .DIRECTORY_SEPARATOR.'simpletest'
            .DIRECTORY_SEPARATOR.'web_tester.php';

class BroserAppTest extends WebTestCase
{
    public function testGet()
    {
        $return = $this->get(TEST_ICE_BASE_URL.'/home.php');
        $this->assertEqual('Hello World!', $return, 'O conteudo da home. %s');
    }

    public function testPost()
    {
        $return = $this->post(TEST_ICE_BASE_URL.'/home.php');
        $this->assertEqual($return, 'Recebi um POST', 'Enviado um POST para a URL. %s');
    }

    public function testOutraPagina()
    {
        $return = $this->get(TEST_ICE_BASE_URL.'/home.php/post/5');
        $this->assertEqual($return, '<h1>Post 5</h1><p>Loren ipsum</p>',
            'Deve chamar uma url com os parâmetros. %s');
    }

    public function testNotFound()
    {
        $return = $this->get(TEST_ICE_BASE_URL.'/home.php/not/found');
        $paginaDeErro = <<<EOF
<!DOCTYPE html>
<html>
    <head>
        <title>404 Page Not Found</title>
    </head>
    <body>
        <h1>404 - Page Not found</h1>
        <p>Please make contact with your administrator.</p>
    </body>
</html>
EOF;
        $this->assertEqual($return, $paginaDeErro, 'Retornou uma página de erro padrão. %s');
    }
}
