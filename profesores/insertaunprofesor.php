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
      escribeprofesores($bd,$datos[0],$datos[1],$datos[2],$datos[3],$datos[4],$datos[5]);
      // echo '<p>'.iconv("ISO-8859-1", "UTF-8",  $cadena).'</p>';
      echo $datos[0]." (Insertado)";
} else {
  // echo 'No tienes nada';
}
?>
