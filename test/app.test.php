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
}
