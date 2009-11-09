<?php
if (!class_exists('Error')) {

class Error
{
    public $error_code;
    public $error_message;

    function get()
    {
        print <<<EOF
<!DOCTYPE html>
<html>
    <head>
        <title>{$this->error_code} {$this->error_message}</title>
    </head>
    <body>
        <h1>{$this->error_code} - {$this->error_message}</h1>
        <p>Please make contact with your administrator.</p>
    </body>
</html>
EOF;
    }
    public function __call($method, $args)
    { 
        $this->get();
    }
}

} // CLASS EXISTS ERROR
