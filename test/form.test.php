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
}
