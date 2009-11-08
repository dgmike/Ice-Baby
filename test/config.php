<?php
$path = dirname(dirname(__FILE__))
        .DIRECTORY_SEPARATOR.'code';

set_include_path($path.PATH_SEPARATOR.get_include_path());

include_once dirname(dirname(__FILE__))
             .DIRECTORY_SEPARATOR.'simpletest'
             .DIRECTORY_SEPARATOR.'autorun.php';

class BufferConstructTest
{
    public function __construct()
    {
        print 'Hello!';
    }

    public function __call($method, $args) { }
}
