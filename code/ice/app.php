<?php

/**
 * app - Starts the application, use it to map your urls to
 * controllers. You have to pass an associative array to 
 *
 * ex:
 * <code><?php 
 *   app (array(
 *       '^/home/posts/(\d+)/?' => 'Post_View',
 *       '^/home/posts/?$'      => 'Post_Index',
 *       '^/home/categories/?$' => 'Categories_List'
 *       '^/home/?$'            => 'Home_Index',
 *   ));
 * ?></code>
 * 
 * @param array $urls   Array of urls and models to map the Controllers
 * @param mixed $url    Url where the user is, by default is used $_SERVER['PATH_INFO']
 * @param mixed $method method that access url, by default is $_SERVER['REQUEST_METHOD']
 * @access public
 * @return void
 */
function app(array $urls=array(), $url=null, $method=null)
{
    if ('array' !== gettype($urls)) {
        return Ice::error(500, 'Argument Invalid');
    }
    if (null === $url) {
        $url = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
    }
    if (null === $method) {
        $method = isset($_SERVER['REQUEST_METHOD']) ? 
            $_SERVER['REQUEST_METHOD'] : 'GET';
    }
    return Ice::run($url, $url, $method);
}
