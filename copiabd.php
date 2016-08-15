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
<title>Copiar BASE DE DATOS a un fichero</title>
<meta http-equiv="Content-Type" content="Type=text/html; charset=iso-8859-15">
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

<!-- Capa de información -->
<div id="informacion" onmouseover="javascript: ocultar()">
<?php
       $nombre="copiabd.sql";
       $ruta="ficheros";    
       $enlace="./".$ruta."/".$nombre;  
       $ejecuta = "mysqldump -u".$mysql_login." -p".$mysql_pass." ".$bd." > ".$enlace;
       exec($ejecuta);
       // cambiar permisos y descarga el fichero
       if (file_exists($enlace)) {
           // cambia permisos del fichero
	   chmod($enlace,0777); //permisos completos de lectura y escritura
	   // echo '<a href="'.$ruta.'" class="a_demo_two">Pulsa para descargarte el fichero copia de seguridad de la base de datos></a>';
           echo '<p style="text-align: center;"><a href="./ficheros/descargafichero.php?fichero='.$nombre.'" class="a_demo_four" style="color: black;" >Pulsa para descargarte el fichero copia de seguridad de la base de datos</a></p>'; 
           echo '<br>';
           echo '<p style="text-align: center;"><a href="./copiabd.php" class="a_demo_four" style="color: black;">Recarga la página. Imprescindible si quieres descargar de nuevo el fichero</a></p>';	           
       }       
       // echo $ejecuta;
?>

</div>

</body>
</html>
