<?php
	function enviar_email_smtp($destinatario, $assunto, $corpo){
        $path = 'lib/phpmailer/class.phpmailer.php';
        while (!file_exists($path))
            $path = "../".$path;
        require_once($path);
        $body = $corpo;
        $mail = new PHPMailer();
        $mail -> IsSMTP();
        $mail -> Host = "smtp.server.com.br"; 
        $mail -> SMTPAuth = true;
        $mail -> Host = "smtp.server.com.br";
        $mail -> Username = "naoresponda@server.com.br";
        $mail -> Password = "password";
   
        $mail -> SetFrom("naoresponda@server.com.br", utf8_decode("Server"));
        $mail -> AddReplyTo("naoresponda@server.com.br", "naoresponda@server.com.br");
        $mail -> Subject = utf8_decode($assunto);
        $mail -> MsgHTML($body);
          
        $mail -> AddAddress($destinatario, $destinatario);
        return $mail -> Send();
    }
	
	function enviar_email_convencional($destinatario, $assunto, $corpo){
		$headers = "From: Name <naoresponda@dominio.com.br>\n";
        $headers .= "Content-type: text/html;
        charset=utf8rn
        ";
        return mail($destinatario, $assunto, $corpo, $headers);
	}
	
	function enviar_email($destinatario, $assunto, $corpo){
		return enviar_email_smtp($destinatario, $assunto, $corpo);
	}
?>