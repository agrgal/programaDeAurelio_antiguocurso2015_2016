<?php
include_once("../funciones/seguridad/mysql_inc.php");
include_once("../funciones/adodb_time_inc.php");
include_once("../funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("../clases/class.micalendario.php");
require_once("../clases/class.phpmailer.php");
$calendario= New micalendario(); // variable de calendario.

if ($_POST['lee']<>'') {

   // primero hay que incluir la clase phpmailer para poder instanciar
  //un objeto de la misma
  // require "includes/class.phpmailer.php";

  //instanciamos un objeto de la clase phpmailer al que llamamos 
  //por ejemplo mail
  $mail = new phpmailer();

  //Definimos las propiedades y llamamos a los métodos 
  //correspondientes del objeto mail

  //Con PluginDir le indicamos a la clase phpmailer donde se 
  //encuentra la clase smtp que como he comentado al principio de 
  //este ejemplo va a estar en el subdirectorio includes
  // $mail->PluginDir = "clases/";

  //Con la propiedad Mailer le indicamos que vamos a usar un 
  //servidor smtp
  $mail->Mailer = "smtp";

  //Le indicamos que el servidor smtp requiere autenticación
  $mail->IsSMTP(); // enable SMTP
  $mail->SMTPDebug = 0;  // debugging: 1 = errors and messages, 2 = messages only
  $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for GMail
  $mail->Host = 'smtp.gmail.com';
  $mail->SMTPAuth = true;
  $mail->Port=465; // puerto de gmail

  //Le decimos cual es nuestro nombre de usuario y password
  $mail->Username = "instituto.seritium@gmail.com"; 
  $mail->Password = "seri11700767";

  //Indicamos cual es nuestra dirección de correo y el nombre que 
  //queremos que vea el usuario que lee nuestro correo


        $mail->SetFrom("instituto.seritium@gmail.com", "ies seritium");
	$mail->Subject = "Prueba de phpmailer";
	$mail->Body = "<b>Mensaje de prueba mandado con phpmailer en formato html</b>";
        $mail->AltBody = "Mensaje de prueba mandado con phpmailer en formato solo texto";
	$mail->AddAddress("agr1971gal@yahoo.es");
	if(!$mail->Send()) {
		$error = 'Mail error: '.$mail->ErrorInfo; 
		return false;
	} else {
		$error = 'Message sent!';
		return true;
	}
 

      // echo $_POST['lee']; 

} else {
  // echo 'No tienes nada';
}
?>
