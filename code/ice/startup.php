<?php
// Register Globals
if (ini_get('register_globals')) {
	ini_set('session.use_cookies', '1');
	ini_set('session.use_trans_sid', '0');

	session_set_cookie_params(0, '/');
	session_start();

	$globals = array($_REQUEST, $_SESSION, $_SERVER, $_FILES);

	foreach ($globals as $global) {
		foreach(array_keys($global) as $key) {
			unset($$key);
		}
	}

	ini_set('register_globals', 'Off');
}

// Magic Quotes Fix
if (ini_get('magic_quotes_gpc')) {
	function clean($data) {
   		if (is_array($data)) {
  			foreach ($data as $key => $value) {
    			$data[$key] = clean($value);
  			}
		} else {
  			$data = stripslashes($data);
		}
		return $data;
	}

	$_GET = clean($_GET);
	$_POST = clean($_POST);
	$_COOKIE = clean($_COOKIE);

	ini_set('magic_quotes_gpc', 'Off');
}
/**
 * Referenciando os arquivos que serão usados pelo sistema
 */

require_once('app.php');
require_once('model.php');
require_once('helper/helper.php');

require_once('library/validate.php');
require_once('library/message.php');
require_once('library/image.php');


/**
 * Carregando a biblioteca de mensagens na inicialização que estará disponível para
 * todas as classes subsequentes.
 * @var $msgObject - Variável que será usada dentro das classes que se utilizarão da biblioteca
 * @example /template/form-usuario.php - line 33/55
 * global $msgObject;
 * $msgObject->add('mensagem', 'tipo');
 * @author ldmotta
 */
$msgObject = new Messages();
