<?php
include_once("../funciones/seguridad/mysql_inc.php");
include_once("../funciones/adodb_time_inc.php");
include_once("../funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("../clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

if ($_POST['lee']<>'') {
      // $_POST['lee']-> en esta variable se guarda el dato de la clase escogida
      $datos=array();
      $datos=explode("#",$_POST['lee']); // en la variable array se guardan los datos a actualizar
      escribeasignatura($bd,$datos[0],trim(substr($datos[1],0,3))); // acorta la abreviatura a sólo tres
      // echo '<p>'.iconv("ISO-8859-1", "UTF-8",  $cadena).'</p>';
      echo $datos[0]." (Insertada)";
} else {
  // echo 'No tienes nada';
}
?>
