<?php
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
                ice_error(501, "Class ({$className}) Not Found", $method);
                return false;
            }
            $class = new $className;
            if (!is_callable(array($class, $method))) {
                ice_error(501, "Method ({$method}) Not Found", $method);
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

#Carrega automaticamente os controles conforme são instanciados

function ice_autoload($class, $routes){
	
	if(is_array($routes)):

		$path_info = preg_replace('@/admin/?@', '', $_SERVER['PATH_INFO']);
		$path_info = preg_replace('@s/?$@', '/', $path_info);
		
		foreach($routes as $regex => $className):
			if($className == $class):
				$parsePath = explode('/',$path_info);
				$controleArquivo = trim(strtolower($parsePath[0]));
				$ultimaLetra = substr($parsePath[0], -1);
				
				$cleaned_parse = array_filter($parsePath);
				$aKeys = array_keys($cleaned_parse);
				
				$controleArquivo = $aKeys ? $parsePath[$aKeys[0]] : "";
				
				#Desplurariza a palavra caso ela esteja no plural
				if($ultimaLetra == 's')
					$controleArquivo = substr($parsePath[0], 0, -1);
				
				$path_controller = 'app/controller/' . $controleArquivo . '.php';
				
				if($controleArquivo == ""):
					require_once('app/controller/home.php');
				elseif(file_exists($path_controller)):
					require_once($path_controller);
				else:
					ice_error(501, "Controller ({$controleArquivo}) Not Found");
				endif;
				
			endif;
		endforeach;
	endif;
}

#Carrega helpers e librarys
function ice_autoload_componnets($load){
	if(is_array($load)):
		foreach($load as $component => $array_compoents):
			foreach($array_compoents as $comp_name):
				$component_singular = substr($component, 0, -1);
		
				$path_component_pub = "app/{$component_singular}/{$comp_name}.php";
				$path_component_ice = "ice/{$component_singular}/{$comp_name}.php";
		
				if(file_exists($path_component_pub)):
					include_once($path_component_pub);
				elseif(file_exists($path_component_ice)):
					include_once($path_component_ice);
				else:
					ice_error('501', ucFirst($component_singular) . ' <strong> ' . $comp_name . '</strong> Not Found');
				endif;
			endforeach;
		endforeach;
	endif;
}