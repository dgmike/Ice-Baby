<?php
/**
 * Faz validações de objetos de formulários (campos de email, senhas, checkbox, radio buttons etc.)
 * @author ldmotta
 */
class Validate {
    var $error_email = 'O e-mail digitado não é válido!';
    var $error_empty = '%s é um campo obrigatório!';
    var $error_match = 'Senhas não conferem!';
    /**
     *
     * @param <string> $email Email a ser validado
     * @return <bool> True caso seja um email válido ou null
     */
    public function is_valid_mail($email) {
		$pattern = '/^([a-z0-9])(([-a-z0-9._])*([a-z0-9]))*\@([a-z0-9])(([a-z0-9-])*([a-z0-9]))+(\.([a-z0-9])([-a-z0-9_-])?([a-z0-9])+)+$/i';
    	if (preg_match($pattern, $email)) return true;
    }
    
    public function is_empty($campo){
        return !preg_match('/^[a-zA-z0-9]/', $campo);
    }

    public function is_match($campo, $campo2){
        return (bool)($campo==$campo2);
    }


}
