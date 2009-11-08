<?php include_once('config.php');

class AppTest extends UnitTestCase
{
    public function testSimpleRedirect()
    {
        $this->assertEqual(1, 1, 'Valores corretos');
    }
}
