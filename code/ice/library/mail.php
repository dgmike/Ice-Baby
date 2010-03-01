<?php
if (!defined('BASE_URL')) exit('No direct script access allowed');

class Mail  {

    /**
     * Envia email com autenticação smpt, utilizando a biblioteca xpertmailer
     * @param string $de Email do remetente
     * @param string $para Email de destino
     * @param string $assunto Assunto da mensagem
     * @param string $mensagem Mensagem em si, podendo ser texto puro ou html
     * @param string $name Nome do remetente para exibição no email
     * @param string $resppara Email para onde será enviado um cópia
     * @param string $attachfile Endereço do arquivo que será anexado ao email
     * @param string $style Estilo do conteúdo do email (text/html)
     * @return bool Retorna true caso o email tenha sido enviado corretamente ou false
     */
    public function mandaEmail($de, $para, $assunto, $mensagem, $name='',$resppara=null, $attachfile=null, $style="html")
    {
        if(is_null($resppara)){
            $strparms="From: $de";
        }else{
            $strparms="From: $de\r\nReply-to: $resppara\r\n";
        }
        $usenativemail = USENATIVEMAIL;
        if($usenativemail){
            return mail($para, $assunto, $mensagem, $strparms, "-f $de");
        }else{
            // path to smtp.php file from XPM2 package
            require_once 'smtp/smtp.php';
            $mail = new SMTP;
            $mail->Delivery('relay');
            $mail->Relay(smtp_server, smtp_username, smtp_password, smtp_port, smtp_auth, smtp_ssl);
            $mail->From($de, $name);
            $mail->AddTo($para);
            if($attachfile) $mail->AttachFile($attachfile);
            if($resppara) $mail->addcc($resppara);
            if ($style=="html"){
                $mail->Html($mensagem, 'UTF-8');
            }else{
                $mail->Text($mensagem, 'UTF-8');
            }
            $send = $mail->Send($assunto);
            return $send;
        }
    }
}


?>
