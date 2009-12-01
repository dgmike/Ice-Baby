<?php 
include_once('config.php');
include_once('ice/form.php');

class FormTest extends UnitTestCase
{
    function content()
    {
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }

    function testDegub()
    {
        ob_start();
        $form = new Form($debug = true);
        $content = $this->content();
        $this->assertEqual('[FORM INIT. Elements: 0]', $content,
            'Inicializou com degub. %s');
    }

    function testNoDebug()
    {
        ob_start();
        $form = new Form;
        $content = $this->content();
        $this->assertEqual('', $content, 'Não apareceu o debug');
    }

    function testArgsNoDebug()
    {
        ob_start();
        $form = new Form($config = array());
        $content = $this->content();
        $this->assertEqual('', $content, 'Não apareceu o debug');
    }

    function testDebugOnConfig()
    {
        ob_start();
        $form = new Form($config = array('debug' => true));
        $content = $this->content();
        $this->assertEqual('[FORM INIT. Elements: 0]', $content,
            'Inicializou com degub. %s');
    }

    function testAction()
    {
        $form = new Form($config = array('action' => 'url'));
        $content = $form->show();
        $this->assertEqual('<form action="url"></form>', $content,
            'Mostra o form, já que existe action. %s');
        $form = new Form($config = array('action' => ''));
        $content = $form->show();
        $this->assertEqual('<form action=""></form>', $content,
            'URL vazia também vale. %s');
        $form = new Form($config = array('action' => null));
        $content = $form->show();
        $this->assertEqual('', $content,
            'Não vale action=null. %s');
    }

    function testMethod()
    {
        $form = new Form($config = array('method' => 'get'));
        $content = $form->show();
        $this->assertEqual('<form method="get"></form>', $content,
            'Mostra o form, já que existe method. %s');
        $form = new Form($config = array('method' => 'post'));
        $content = $form->show();
        $this->assertEqual('<form method="post"></form>', $content,
            'Mostra o form, já que existe method. %s');
        $form = new Form($config = array('method' => ''));
        $content = $form->show();
        $this->assertEqual('<form method=""></form>', $content,
            'Method vazio também vale. %s');
        $form = new Form($config = array('method' => null));
        $content = $form->show();
        $this->assertEqual('', $content,
            'Não vale method=null. %s');
    }

    function testClass()
    {
        $form = new Form($config = array('class' => 'formulario'));
        $content = $form->show();
        $this->assertEqual('<form class="formulario"></form>', $content,
            'Mostra o form, já que existe class. %s');
        $form = new Form($config = array('class' => ''));
        $content = $form->show();
        $this->assertEqual('<form class=""></form>', $content,
            'Class vazia também vale. %s');
        $form = new Form($config = array('class' => null));
        $content = $form->show();
        $this->assertEqual('', $content,
            'Não vale class=null. %s');
    }

    function testExtra()
    {
        $form = new Form($config = array('extra' => 'id="pagseguro"'));
        $content = $form->show();
        $this->assertEqual('<form id="pagseguro"></form>', $content,
            'Mostra opções extras, string. %s');
        $form = new Form($config = array('extra' => ''));
        $content = $form->show();
        $this->assertEqual('<form ></form>', $content,
            'Para opcoes extra em branco também vale. %s');
        $form = new Form($config = array('extra' => null));
        $content = $form->show();
        $this->assertEqual('', $content,
            'Se extra for null, não apresenta nada. %s');
    }

    function testTodasAsOpcoes()
    {
        $form = new Form($config = array(
            'class'  => 'formulario',
            'extra'  => 'id="usuario"',
            'method' => 'post',
            'action' => '/salvar',
        ));
        $content = $form->show();
        $this->assertEqual('<form action="/salvar" method="post" '.
            'class="formulario" id="usuario"></form>', $content,
            'Guardou e mostrou todos os componentes. %s');
    }

    function testUpload()
    {
        $form = new Form($config = array('upload' => true));
        $content = $form->show();
        $this->assertEqual('<form method="post" enctype="multipart/form-data"></form>',
            $content, 'Usando um formulário de POST para uploads. %s');
    }
}
