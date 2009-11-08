<?php
error_reporting(E_ALL);

function app($urls, $url=null, $method = null)
{
    foreach ($urls as $regexp => $className) {
        $regexp = '@'.str_replace('@', '\@', $regexp).'@';
        if (preg_match($regexp, $url, $args)) {
            $class = new $className;
            call_user_method(strtolower($method), $class);
        }
    }
}
