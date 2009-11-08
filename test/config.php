<?php
define('TEST_ICE_BASE_URL', 'http://localhost/Ice-Baby/test');

$path = dirname(dirname(__FILE__))
        .DIRECTORY_SEPARATOR.'code';

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
