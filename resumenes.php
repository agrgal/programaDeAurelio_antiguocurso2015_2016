<?
include("./funciones/funciones.php"); /* incluye el directorio de funciones */

include_once("./clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

/* $bd="encyclonumber"; */ //debe de haberse cargado en mysql_inc.php

$iz = "left: 120px;" ; // posici�n de los campos a la izquierda

session_start();

if ($_SESSION['administracion']<2) {
   echo header("Location: ./index.php");
}

if (isset($_SESSION['tutevaluacion']) && strlen($_SESSION['tutevaluacion'])>0) {
    $visualizacion=1;
} else {$visualizacion=0;}

// obtiene arrays, por si hay que usarlos m�s de una vez
$alumno=obteneralumnosasignacion($bd,$_SESSION['asignacion']); // array para introducir datos de los alumnos
$ii=count($alumno['idalumno']);

$asignaciones = obtenerasignaciones($bd,$alumno['idalumno'],$_SESSION['tutevaluacion']); 
// obtiene un array con las distintas asignaciones que ESA EVALUACI�N, han sido calificadas
$jj=count($asignaciones);

$items=obteneritems($bd);
$kk=count($items['iditem']);

?>
<html>
<head>
<title>Res�menes en PDF de datos de tutor�a</title>
<meta http-equiv="Content-Type" content="Type=text/html; charset=iso-8859-15">
<!-- Inclyo las diferentes hojas de estilo -->
<?php include_once("./listas_de_css.php"); ?>
</head>
<body>
<!-- Capas de presentaci�n
<div id="grupo" style="position: absolute; left: 0px; top: 0px; width: 960; height: auto; z-index: 1; line-height: 14px"><img name="fondo" src="./imagenes_plantilla/plantilla.png" width="960" height="auto" border="0" alt=""></div>-->

<?php

?>

<!-- Capa de men�: navegaci�n de la p�gina -->
<?php include_once("./lista.php"); ?>

<!-- Capa de estado: informaci�n �til -->
<div id="fecha">
	<p style="text-align: center;">
	<? 
	echo 'Fecha: '.$calendario->fechaformateada($calendario->fechadehoy());
	echo '<br>Hora: '.$calendario->horactual();
	?></p>
</div>

<!-- Capa de informaci�n -->
<div id="informacion" onmouseover="javascript: ocultar()">
 <?php if (strlen($_SESSION['tutorada'])>0 && $visualizacion==1) { 
// Genero las im�genes
	foreach ($alumno['idalumno'] as $clave => $valor) {
            //paso los datos y creo cada imagen
	    $_SESSION['dato']=$alumno['alumno'][$clave];
	    $_SESSION['id']=$alumno['idalumno'][$clave];	
	    require("./etiquetanombre.php"); 
            // desprotejo dando todos los permisos a la imagen...
            chmod("./temporal/".$_SESSION['id'].".png",0777);          
         } // fin del foreach  
// Fin de generar im�genes
?>
<!-- <div style="padding-top:30 px; padding-bottom:30px;  margin: 5px auto; width: 50%;" id="presentardatos"> -->
<div class="presentardatos2" style="width:50%; float:left;">
<h2 style="text-align: center;">Informaci�n en PDF&nbsp;</h2><br>
<p style="text-align: center;">
<!-- <a onClick="window.location.reload()" href="./ver/resumenuno.php?salto=1" class="a_demo_two" id="pdf" alt="Genera PDF" style="color:black;">&nbsp;Tabla resumen, por asignatura&nbsp;</a> -->
<a onClick="window.location.reload()" href="./ficheros/resumenunompdf.php?salto=1" class="a_demo_two" id="pdf" alt="Genera PDF" style="color:black;">&nbsp;Tabla resumen, por asignatura&nbsp;</a></p>
<br><br>
<p style="text-align: center;">
<!-- <a onClick="window.location.reload()" href="./ver/resumenunocomplementos.php?salto=1" class="a_demo_two" id="pdf" alt="Genera PDF" style="color:black;">&nbsp;Tabla resumen, por asignatura (complementos) &nbsp;</a> -->
<a onClick="window.location.reload()" href="./ficheros/resumenunocomplementosmpdf.php?salto=1" class="a_demo_two" id="pdf" alt="Genera PDF" style="color:black;">&nbsp;Tabla resumen, por asignatura (complementos) &nbsp;</a>
</p>
<br><br>
<p style="text-align: center;">
<!-- <a onClick="window.location.reload()" href="./ver/resumentres.php?salto=1" class="a_demo_two" id="pdf" alt="Genera PDF" style="color:black;">&nbsp;Resumen con estad�sticas (sin complementos)&nbsp;</a> -->
<a onClick="window.location.reload()" href="./ficheros/resumentresmpdf.php?salto=1" class="a_demo_two" id="pdf" alt="Genera PDF" style="color:black;">&nbsp;Resumen con estad�sticas (sin complementos)&nbsp;</a>
</p>
<br><br>
<p style="text-align: center;">
<!-- <a onClick="window.location.reload()" href="./ver/resumentrescomplementos.php?salto=1" class="a_demo_two" id="pdf" alt="Genera PDF" style="color:black;">&nbsp;Resumen con estad�sticas (con complementos)&nbsp;</a>-->
<a onClick="window.location.reload()" href="./ficheros/resumentrescomplementosmpdf.php?salto=1" class="a_demo_two" id="pdf" alt="Genera PDF" style="color:black;">&nbsp;Resumen con estad�sticas (con complementos)&nbsp;</a>
</p>
<br><br>
<p style="text-align: center;">
<a onClick="window.location.reload()" href="./ficheros/veropiniongeneralpdf.php" class="a_demo_two" id="pdf" alt="Genera PDF" style="color:black;">&nbsp;Opini�n General de la clase&nbsp;</a>
</p>
<br><br>
</div>

<div class="presentardatos2" style="width:40%; float:right;">
	<h2 style="text-align: center;">Nota</h2>
        <p>Pulsa en algunos de los botones a la izquierda para obtener diversos tipos de informes.</p>
	<p>Paciencia con los dos primeros. Son ficheros densos con mucha informaci�n. Por cada asignatura presenta una tabla con los alumnos/as 
        y los items que se ha marcado para cada uno/a. Puede que tarden un tiempo en descargarse.</p>
        <p>El tercer y cuarto informe son res�menes estad�sticos de los items marcados por los profesores/as.</p>
        <p>Por �ltimo, genera un informe con la opini�n que sobre el grupo tiene el equipo educativo.</p>
        <p>Si usas el navegador GOOGLE CHROME puedes notar que los botones no responden como en otros navegadores. Manten pulsado el bot�n unos segundos o pulsa repetidamente para activarlo.</p>
</div>

<!-- <div id="presentardatos"> 

<h2 style="text-align: center;">Importante: Si se producen errores cuando te descargas M�S DE UN INFORME, pulsa <a href="./resumenes.php" id="pdf" alt="Recarga la p�gina">AQU�</a> para recargar la p�gina y evitarlos, cada vez que generes un informe y antes de generar el siguiente.&nbsp;</h2><h2 style="text-align: center;">Si por casualidad se produce un error, click en ATR�S en el navegador y click en <a href="./resumenes.php" id="pdf" alt="Recarga la p�gina">RECARGAR LA P�GINA</a></h2> -->

<!-- Parrafada antigua
<h2 style="text-align: center;">Importante: ANTES DE generar un 2� informe, pulsa <a href="./resumenes.php" id="pdf" alt="Recarga la p�gina">AQU�</a> para recargar la p�gina y evitar errores&nbsp;</h2><h2 style="text-align: center;">Si por casualidad te equivocas y sale un error, click en ATR�S en el navegador y click en <a href="./resumenes.php" id="pdf" alt="Recarga la p�gina">RECARGAR LA P�GINA</a></h2>
</div>-->


<?php 
    } else { 
      echo '<h2>No has seleccionado una evaluaci�n previamente o no te has identificado como tutor/a de un curso</h2>';
      echo '<p style="text-align: center;"><a style="padding: 5px 10px;" class="botones" href="./guardardatosinicialestutoria.php">Datos iniciales de tutor�a</a><a style="padding: 5px 10px;" class="botones" href="./index.php">Identificarse como tutor/a</a></p>';	
    }// fin del if
    
    ?>



</body>
</html>
