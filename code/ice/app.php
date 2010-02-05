<?php
error_reporting(E_ALL);

include_once 'default_controller.php';

function noCache()
{
    header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
    header("Expires: " . gmdate( "D, j M Y H:i:s", time() ) . " GMT"); // always modified 
    header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
    header("Cache-Control: post-check=0, pre-check=0", FALSE); 
    header("Pragma: no-cache");
}

/**
 * Runs an error type
 *
 * @uses Error
 * @param int    $code    Error code
 * @param string $message Mensagem de erro
 * @param string $method  GET or POST method of error
 *
 * return void
 */
function ice_error($code, $message, $method='GET')
{
    $error_class = new Error;
    $error_class->error_code    = $code;
    $error_class->error_message = $message;
    $callback = array($error_class, strtolower($method));
    call_user_func( $callback );
}

/**
 * Starts an application, pass yours urls by 
 *
 *
 */
function app($urls, $url=null, $method = null)
{
    if ('array' !== gettype($urls)) {
        ice_error(500, 'Argument invalid');
        return false;
    }
    if (null===$url) {
        $url = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
    }
    if (null===$method) {
        $method = isset($_SERVER['REQUEST_METHOD']) ?
                    $_SERVER['REQUEST_METHOD'] : 
                    'GET';
    }
    $method = strtolower($method);
    foreach ($urls as $regexp => $className) {
        $regexp = '@'.str_replace('@', '\@', $regexp).'@';
        if (preg_match($regexp, $url, $args)) {
            if (!class_exists($className)) {
                ice_error(501, 'Class Not Found', $method);
                return false;
            }
            $class = new $className;
            if (!is_callable(array($class, $method))) {
                ice_error(501, 'Method Not Found', $method);
                return false;
            }
            if ($args) {
                array_shift($args);
            }
            call_user_func_array(array($class, $method), $args);
            return;
        }
    }
    ice_error(404, 'Page Not Found', $method);
}
