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
}
