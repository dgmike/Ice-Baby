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
 * @param mixed $method method do access url, by default is GET
 * @access public
 * @return void
 */
function app(array $urls=array(), $url=null, $method=null)
{

}
