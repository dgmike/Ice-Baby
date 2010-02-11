<?php

class Ice
{
    /**
     * error - runs a error page default
     * @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html
     * 
     * @param int    $code    Status code definition
     * @param string $message Message send to Error class Controller
     * @param string $method  Method used when triing to access the page
     * @static
     * @access public
     * @return void
     */
    static public function error($code, $message, $method = 'GET')
    {
        $error_class = new Error;
        $error_class->error_code    = $code;
        $error_class->error_message = $message;
        $callback = array($error_class, strtolower($method));
        call_user_func( $callback );
    }

    /**
     * run - run your application
     * 
     * @param array $urls   Array of urls and models to map the Controllers
     * @param mixed $url    Url where the user is
     * @param mixed $method method that access url
     * @static
     * @access public
     * @return void
     */
    static public function run(array $urls = array(), $url = '', $method = 'get')
    {
        $method = strtolower($method);
        foreach ($urls as $regexp => $className) {
            $regexp = sprintf('@%s@', str_replace('@', '\@', $regexp));
            if (preg_match($regexp, $url, $args)) {
                if (!class_exists($className)) {
                    return Ice::error(501, 'Class Not Found', $method);
                }
                $class = new $className;
                if (!is_callable(array($class, $method))) {
                    return Ice::error(501, 'Method Not Found', $method);
                }
                if ($args) {
                    array_shift($args);
                }
                call_user_func_array(array($class, $method), $args);
            }
        }
        return Ice::error(404, 'Page Not Found', $method);
    }
}
