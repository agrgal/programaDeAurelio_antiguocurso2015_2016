<?
include("./funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("./clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

/* $bd="encyclonumber"; */ //debe de haberse cargado en mysql_inc.php

if (isset($_POST['emails'])) {$correos=$_POST['emails'];}
if (isset($_POST['asunto'])) {$asunto=$_POST['asunto'];}
if (isset($_POST['cuerpo'])) {$cuerpo=$_POST['cuerpo'];}

$iz = "left: 120px;" ; // posición de los campos a la izquierda

session_start();

// obtiene arrays, por si hay que usarlos más de una vez
$alumno=obteneralumnosasignacion($bd,$_SESSION['asignacion']); // array para introducir datos de los alumnos
$aluii=count($alumno['idalumno']);

$informacion=obtenerdatosasignacion($bd,$_SESSION['asignacion']); //información de la asignación

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

if ($correos<>"" && $asunto<>"" && $cuerpo<>"") { // si la variable NO está vacía

$arraycorreos = explode("###",$correos);
$numcorreos = count($arraycorreos);

$cabecera ='<p style="text-align: center;"><img width="300px" heigth="auto" src="./imagenes_plantilla/iesseritium.png"></p>';
$cabecera.='<p style="text-align: center;">Alumnado de <strong>'.$alumno["cadenaclases"].'</strong></p>';
$cabecera.='<p style="text-align: center;">Evaluación: <strong>'.dado_Id($bd,$_SESSION['tutevaluacion'],"nombreeval","tb_edicionevaluaciones","ideval").'</strong></p> ';
$cabecera.='<p style="text-align: center;">Asunto: <strong>'.$asunto.'</strong></p>';
$pie = '<hr width="80%">';
$pie.= '<p style="text-align: center;">Este es un mensaje automático. Por favor, no responder al remitente del mensaje.</p>';
$pie.= '<p style="text-align: center;">No imprimas este mensaje si no es absolutamente necesario. Contribuye así a la reducción de la huella de carbono.</p>';
$pie.= '<p style="text-align: center;"><img width="100px" heigth="auto" src="./imagenes_plantilla/huellacarbono.png"></p>';

// ******************************************************************* 

// error_reporting(E_ALL);
// error_reporting(E_STRICT);

date_default_timezone_set('Europe/Madrid');

require_once('./phpmailer/class.phpmailer.php'); // lama a la clase PHPMAILER
//include("class.smtp.php"); // optional, gets called from within class.phpmailer.php if not already loaded

$mail             = new PHPMailer();

$fechahora="<p>Enviado el <strong>".$calendario->fechaformateada($calendario->fechadehoy())."</strong> a las <strong>".$calendario->horactual()."</strong></p>";

$body             = $cabecera.'<p>'.$cuerpo.'</p>'.$fechahora.$pie;
$body             = eregi_replace("[\]",'',$body);

$detutor = "MENSAJE DE TUTORÍA. Tutor/a: ".cambiarnombre($informacion['profesor'])." - Clase/clases: ".$alumno["cadenaclases"];
$asunto = $detutor.": ".strtoupper($asunto);

// $address = $_SESSION['correo']; 
// $quien=cambiarnombre(dado_Id($bd,$_SESSION['profesor'],"Empleado","tb_profesores","idprofesor")); 


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
  
  $n=0;
  $muestracorreos="";
  foreach ($arraycorreos as $valor) { // $valor es la identificación del profesor
     $cadenacorreo=dado_Id($bd,$valor,"email","tb_profesores","idprofesor");  
     $cadenanombre=cambiarnombre(dado_Id($bd,$valor,"Empleado","tb_profesores","idprofesor")); 
     $mail->AddAddress($cadenacorreo, $cadenanombre);
     // Si lo quiero con copia oculta, funciona, pero después puede llegar a la bandeja de SPAM
     // if ($n==0) {$mail->AddAddress($valor, "Este");}
     // if ($n>0) {$mail->AddBCC($valor);}
     $muestracorreos.=$cadenacorreo."(".$cadenanombre."), ";
     $n++;
  }
  $muestracorreos=substr($muestracorreos,0,strlen($muestracorreos)-2);
 
  $mail->SetFrom($remitente, "ies seritium");
  // $mail->AddReplyTo('name@yourdomain.com', 'First Last');
  $mail->Subject    = $asunto;
  // $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automatically
  $mail->MsgHTML($body);
  // $mail->AddAttachment('images/phpmailer.gif');      // attachment
  // $mail->AddAttachment('./imagenes_plantilla/iesseritium.png'); // attachment
  $mail->Send();
  // echo '<div>';

  echo '<div id="presentardatos"><h2>Mensaje enviado con éxito</h2></div>';
  ?>
        <div id="presentardatos">
          <h2>Enviado a: <?php echo $muestracorreos;?></h2>
          <h2>Asunto: <?php echo $asunto;?></h2>
          <h2>Cuerpo del mensaje: </h2>
          <p><?php echo $body;?></p>
        </div>
  <?php
} catch (phpmailerException $e) {
  echo '<div id="presentardatos"><h2>Mailer Error: ' .$e->errorMessage().'</h2></div>'; 
} catch (Exception $e) {
  echo '<div id="presentardatos"><h2>' .$e->getMessage().'</h2></div>'; 
   //Boring error messages from anything else!
}
// *******************************************

} else { // si la variable está vacía...
  echo '<div id="presentardatos"><h2>O bien no has seleccionado correos, o no has escrito un asunto o un cuerpo del mensaje. Regresa a la página anterior y vuelve a intentarlo.</h2></div>';
}


// error_reporting(E_ALL);
// error_reporting(E_STRICT);

/* date_default_timezone_set('Europe/Madrid');


*/
?>
<br>

<a href="javascript:history.back()" class="a_demo_four" style="color:black;" >Vuelve a la página anterior</a> 

</div> <!-- Fin de capa de información -->
</body>
</html>
