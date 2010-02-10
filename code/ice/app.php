<?php
error_reporting(E_ALL);

include_once 'default_controller.php';

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
 * Starts an application, pass yours urls by associatve
 * array. Where the key is an regexp to url defined
 * and the value is the class to use like a controller.
 *
 * Ex:
 * <?php
 * app(array(
 *     '^/home/posts/?$'      => 'Posts',
 *     '^/home/categories/?$' => 'Categories',
 * ));
 *
 * @param array  $urls   URLs to map your application
 * @param string $url    You can pass the url defining where the user is
 * @param string $method The method POST|GET|PUT|INSERT... to use in your Controller
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
