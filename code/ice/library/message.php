<?php 
if (!defined('BASE_URL')) exit('No direct script access allowed');

/**
 * Message:: A classe que escreve o retorno de uma mensagem adicionada na sessão
 * @author ldmotta <ldmotta@gmail.com>
 */

class Messages {
    var $_ci;
    var $_types = array('done', 'error', 'warning', 'confirm', 'message');

    function Messages($params = array()) {
        // checa se existe uma mensagem, se não, inicializa a mensagem no array SESSION
        $messages = isset($_SESSION['messages']) ? $_SESSION['messages'] : '';
        if (empty($messages)) {
            $this->clear();
        }
    }

    /**
     * Limpa todas as mensagens na sessão
     * @return void
     */
    function clear() {
        $messages = array();
        foreach ($this->_types as $type) {
            $messages[$type] = array();
        }
        $_SESSION['messages'] = $messages;
    }

    /**
     * Adiciona uma mensagem ao array sessão com um tipo,
     * por default assume o tipo message
     * @param string $message Mensagem a ser exibida
     * @param string $type Tipo de mensagem que pode ser 'done, error, warning, message', default = message
     * @uses Usado para adicionar mensagens que serão exibidas posteriormente no template
     * @return void
     */
    function add($message, $type = 'message') {
        $messages = (array_key_exists('messages', $_SESSION)) ? $_SESSION['messages'] : '';
        // handle PEAR errors gracefully
        if ($this->is_a($message, 'PEAR_Error')) {
            $message = $message->getMessage();
            $type = 'error';
        } else if (!in_array($type, $this->_types)) {
            // set the type to message if the user specified a type that's unknown
                $type = 'message';
            }
        // don't repeat messages!
        if (is_array($messages) && !in_array($message, $messages[$type]) && is_string($message)) {
            $messages[$type][] = $message;
        }
        $messages = $_SESSION['messages'] = $messages;
    }

    /**
     * Verifica e soma a quantidade de mensagens de um determinado tipo
     * @param string $type Tipo de mensagem
     * @see var $_types na linha 9
     * @return int|false quantidade de mensagens ou false se nenhuma
     */
    function sum($type = null) {
        $messages = $_SESSION['messages'];
        if (!empty($type)) {
            $i = count($messages[$type]);
            return $i;
        }
        $i = 0;
        foreach ($this->_types as $type) {
            $i += count($messages[$type]);
        }
        return $i;
    }

    /**
     * Le da sessão as mensagens de um determinado tipo ou todos os tipos
     * @param string $type Tipo de mensagem @see $_types na linha 9
     * @return string $return mensagens adicionadas na sessão
     */
    // return messages of given type or all types, return false if none, clearing stack
    function get($type = null) {
        $messages = $_SESSION['messages'];
        if (!empty($type)) {
            if (count($messages[$type]) == 0) {
                return false;
            }
            return $messages[$type];
        }
        // return false if there actually are no messages in the session
        $i = 0;
        foreach ($this->_types as $type) {
            $i += count($messages[$type]);
        }
        if ($i == 0) {
            return false;
        }

        // order return by order of type array above
        // i.e. done, error, warning and then informational messages last
        foreach ($this->_types as $type) {
            $return[$type] = $messages[$type];
        }
        $this->clear();
        return $return;
    }

    /**
     * Checks if the object is of this class or has this class as one of its parents
     * @link http://php.net/manual/en/function.is-a.php
     * @param object object <p>
     * The tested object
     * </p>
     * @param class_name string <p>
     * The class name
     * </p>
     * @return bool true if the object is of this class or has this class as one of
     * its parents, false otherwise.
     * </p>
     */
    function is_a ($object, $class_name) {}

    /**
     * Find whether the type of a variable is string
     * @link http://php.net/manual/en/function.is-string.php
     * @param var mixed <p>
     * The variable being evaluated.
     * </p>
     * @return bool true if var is of type string,
     * false otherwise.
     * </p>
     */
    function is_string ($var) {}

} 