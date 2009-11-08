<?php
error_reporting(E_ALL);

function ice_error($code, $message, $method='GET')
{
    $error_class = new Error;
    $error_class->error_code    = $code;
    $error_class->error_message = $message;
    $callback = array($error_class, strtolower($method));
    call_user_func( $callback );
}

function app($urls, $url=null, $method = null)
{
    if (null===$method) {
        $method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
    }
    foreach ($urls as $regexp => $className) {
        $regexp = '@'.str_replace('@', '\@', $regexp).'@';
        if (preg_match($regexp, $url, $args)) {
            $class = new $className;
            call_user_func(array($class, strtolower($method)));
            return;
        }
    }
    ice_error(404, 'Page Not Found', $method);
}
