<?php include_once('config.php');

include_once('ice/app.php');

class AppTest extends UnitTestCase
{
    public function testSimpleUrl()
    {
        ob_start(); app(array('.*' => 'BufferConstructTest'), '');
        $contents = ob_get_contents(); ob_end_clean();
        $this->assertEqual('Hello!', $contents, 'Foi chamada a classe BufferConstructTest. %s');
    }
}
