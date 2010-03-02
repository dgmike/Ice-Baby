<?php
/**
 * Faz validações de objetos de formulários (campos de email, senhas, checkbox, radio buttons etc.)
 * @author ldmotta
 */
class Validate {
    var $_error_email = 'O e-mail digitado não é válido!';
    var $_error_empty = '- %s é um campo obrigatório!';
    var $_error_match = 'Senhas não conferem!';

    /**
     * is_valid_mail() Verifica se um email passado como parametro em $mail, é válido
     * @param string $email Email a ser validado
     * @return bool True caso seja um email válido ou null
     */
    public function is_valid_mail($email) {
		$pattern = '/^([a-z0-9])(([-a-z0-9._])*([a-z0-9]))*\@([a-z0-9])(([a-z0-9-])*([a-z0-9]))+(\.([a-z0-9])([-a-z0-9_-])?([a-z0-9])+)+$/i';
    	if (preg_match($pattern, $email)) return true;
    }

    /**
     * Verifica se um campo passado como parametro, é vazio
     * @param string $campo Valor a ser analizado
     * @return bool True caso seja vazio
     */
    public function is_empty($campo){
        return (bool)!preg_match('/^[a-zA-z0-9]/', $campo);
    }

    /**
     * Verifica de duas strings passadas como parametro são iguais
     * @param string $campo Valor da primeira string
     * @param string $campo2 Valor da segunda string
     * @return bool True caso seja igual
     */
    public function is_match($campo, $campo2){
        return (bool)($campo==$campo2);
    }

    /**
     * Exibe uma mensagem de erro personalizada
     * @param string $label Nome do label do campo para ser exibido junto com a
     * mensagem de erro
     */
    public function error_empty($label){
        return sprintf($this->_error_empty, $label);
    }

    /**
     * Exibe uma mensagem de erro personalizada
     */
    public function error_email(){
        return $this->_error_email;
    }

    /**
     * Exibe uma mensagem de erro personalizada
     */
    public function error_match(){
        return $this->_error_match;
    }


}
