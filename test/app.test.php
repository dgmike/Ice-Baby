<?php include_once('config.php');

include_once('ice/app.php');

class AppTest extends UnitTestCase
{
    public function get_content()
    {
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }

    public function testCallClass()
    {
        ob_start(); app(array('.*' => 'BufferConstructTest'), '');
        $contents = $this->get_content();
        $this->assertEqual('Hello!', $contents, 'Foi chamada a classe BufferConstructTest. %s');
    }

    public function testCallGetPostAny()
    {
        ob_start(); app(array('.*' => 'BufferMethodTest'), '', 'GET');
        $contents = $this->get_content();
        $this->assertEqual('get', $contents, 'Foi chamado o metodo get pelo REQUEST. %s');

        ob_start(); app(array('.*' => 'BufferMethodTest'), '', 'POST');
        $contents = $this->get_content();
        $this->assertEqual('post', $contents, 'Foi chamado o metodo post pelo REQUEST. %s');

        ob_start(); app(array('.*' => 'BufferMethodTest'), '', 'ANY');
        $contents = $this->get_content();
        $this->assertEqual('any', $contents, 'Foi chamado o metodo any pelo REQUEST. %s');
    }
}
