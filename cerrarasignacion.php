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
<title>Cierra o abre edición de asignaciones</title>
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

<!-- Capa de información -->
<div id="informacion" onmouseover="javascript: ocultar()">
<?php 
// Si he pulsado el botón, proceso contrario
$nombre_fichero="./configuracion/config.txt"; // -> nombre del fichero
if (isset($_POST['boton']) && isset($_POST['cerrarasignacion'])) {
 if (trim($_POST['cerrarasignacion'])=="true") { $poner="false"; }
 if (trim($_POST['cerrarasignacion'])=="false") { $poner="true"; }
 escribirconfig($nombre_fichero,"cerrarasignacion",$poner);
 // borraconfig($nombre_fichero); 
}
// Leer el fichero config.txt
$recupera=leerconfig($nombre_fichero);
extract ($recupera,EXTR_OVERWRITE); // extrae datos de un array y las claves las convierte en variables con el valor asignado
// la variable que usaré es $cerrarasignación
// $_SESSION['cerrarasignacion']=$cerrarasignacion; // se lo asigno a la variable de sesión del mismo nombre
?>
<form name="editarevaluacion" action="./cerrarasignacion.php" method="post">
	
    <?php // muestro en pantalla el estado actual de la variable
    if (trim($cerrarasignacion)=="true") { // asignacion cerrada
      $mensaje="Pulsa para ABRIR el proceso de asignación";
      $estado="Actualmente un profesor/a NO PUEDE registrar NUEVAS ASIGNACIONES";
    } else if (trim($cerrarasignacion)=="false") {
       $mensaje="Pulsa para CERRAR el proceso de asignación";
       $estado="Actualmente un profesor/a SI PUEDE registrar NUEVAS ASIGNACIONES";
    } else { // en caso que, por cualquier cosa, no valga ni una ni otra
      $cerrarasignacion="true"; // pongo cerrar asignación TRUE, cerrada
      $mensaje="Pulsa para REINICIAR el proceso de asignación";
      // $_SESSION['cerrarasignacion']=$cerrarasignacion; // recargo la variable de sesión
    } 
    ?>
    <div id="presentadatos">
    <h2 style="text-align: center;"><?php echo $estado; ?></h2>
    <input type="hidden" id="cerrarasignacion" name="cerrarasignacion" value="<?php echo $cerrarasignacion; ?>">
    <input name="boton" class="botonesdos" id="boton" value="<?php echo $mensaje; ?>" type="submit" alt="Modifica el estado del proceso de asignacion" title="Modifica el estado del proceso de asignacion"> 
    </div>

</form>

</div>

</body>
</html>
