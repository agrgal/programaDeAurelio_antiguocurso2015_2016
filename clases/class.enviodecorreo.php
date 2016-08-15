<?php
class correo{
	var $destinatario;
	var $remitente;
	var $asunto;
	var $mensaje;

function enviar() {
	$headers = "From: ".$this->remitente."\r\nReply-To: ". $this->remitente;
	ob_start(); 
	$message = ob_get_clean();
	$mail_sent = @mail( $this->destinatario, $this->asunto, $this->mensaje, $headers);
	// echo $destinatario;
	// echo $mail_sent ? "Correo Enviado con éxito" : "No se ha podido enviar el correo";
	}
}

/*
$g = new correo();
$g->destinatario = 'direccion@correo.com';
$g->remitente = 'remitente@correo.com';
$g->asunto = 'Este es el asunto del mensaje';
$g->mensaje = 'Este es el cuerpo del mensaje.';
$g->enviar();
*/
?>
