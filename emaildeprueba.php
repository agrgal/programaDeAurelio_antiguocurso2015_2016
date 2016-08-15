<?
include("./funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("./clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

/* $bd="encyclonumber"; */ //debe de haberse cargado en mysql_inc.php

$iz = "left: 120px;" ; // posición de los campos a la izquierda

session_start();
?>
<html>
<head>
<title>Pantalla de Envío de Mensajes</title>
<meta http-equiv="Content-Type" content="text/html;">
<!-- Inclyo las diferentes hojas de estilo -->
<?php include_once("./listas_de_css.php"); ?>
</head>
<body>
<!-- Capas de presentación
<div id="grupo" style="position: absolute; left: 0px; top: 0px; width: 960; height: auto; z-index: 1; line-height: 14px"><img name="fondo" src="./imagenes_plantilla/plantilla.gif" width="960" height="auto" border="0" alt=""></div> -->
<div class="grupo"></div>

<!-- Capa de menú: navegación de la página -->
<?php include_once("./lista.php"); ?>

<!-- Capa de estado: información útil -->
<div id="fecha">
	<p style="text-align: center;">
	<? 
	echo 'Fecha: '.$calendario->fechaformateada($calendario->fechadehoy());
	echo '<br>Hora: '.$calendario->horactual();
	?></p>
</div>

<div id="informacion" onmouseover="javascript: ocultar()">
<?php

// error_reporting(E_ALL);
// error_reporting(E_STRICT);

date_default_timezone_set('Europe/Madrid');

require_once('./phpmailer/class.phpmailer.php'); // lama a la clase PHPMAILER
//include("class.smtp.php"); // optional, gets called from within class.phpmailer.php if not already loaded

$mail             = new PHPMailer();

$fechahora="<p>Enviado el ".$calendario->fechaformateada($calendario->fechadehoy())." a las ".$calendario->horactual()."</p>";

$body             = $_SESSION['body'].$fechahora;
$body             = eregi_replace("[\]",'',$body);


$address = $_SESSION['correo']; 
$quien=cambiarnombre(dado_Id($bd,$_SESSION['profesor'],"Empleado","tb_profesores","idprofesor")); 
$mail->IsSMTP(); // telling the class to use SMTP

try {
  // echo '<div id="presentardatos2">';
  $mail->Host       = $remitenteSMTPHOST; // sets the SMTP server
  $mail->SMTPDebug  = 0;                     // enables SMTP debug information (for testing)
  $mail->SMTPAuth   = true;                  // enable SMTP authentication
  $mail->SMTPSecure = $remitenteSMTPSecure; // secure transfer enabled REQUIRED for GMail
  $mail->Port       = $remitentepuerto;                // set the SMTP port for the GMAIL server
  $mail->Username   = $remitente; // SMTP account username
  $mail->Password   = $remitentepass;        // SMTP account password
  // $mail->AddReplyTo('name@yourdomain.com', 'First Last'); 
  $mail->AddAddress($address, $quien);
  $mail->SetFrom($remitente, $quien);
  // $mail->AddReplyTo('name@yourdomain.com', 'First Last');
  $mail->Subject    = $_SESSION['asunto'];
  // $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automatically
  $mail->MsgHTML($body);
  // $mail->AddAttachment('images/phpmailer.gif');      // attachment
  $mail->AddAttachment('./imagenes_plantilla/iesseritium.png'); // attachment
  $mail->Send();
  // echo '<div>';

  echo '<div id="presentardatos"><h2>Mensaje enviado con éxito</h2></div>';
  ?>
        <div id="presentardatos">
          <h2>Enviado a: <?php echo $_SESSION['correo'];?></h2>
          <h2>Asunto: <?php echo $_SESSION['asunto'];?></h2>
          <p><?php echo $_SESSION['body'];?></p>
        </div>
  <?php
} catch (phpmailerException $e) {
  echo '<div id="presentardatos"><h2>Mailer Error: ' .$e->errorMessage().'</h2></div>'; 
} catch (Exception $e) {
  echo '<div id="presentardatos"><h2>' .$e->getMessage().'</h2></div>'; 
   //Boring error messages from anything else!
}

?>
<br>
<a href="javascript:history.back()" class="a_demo_four" style="color:black;" >Vuelve a la página anterior</a> 

</div> <!-- Fin de capa de información -->
</body>
</html>
