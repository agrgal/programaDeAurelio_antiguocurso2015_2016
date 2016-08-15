<?php
include_once("../funciones/seguridad/mysql_inc.php");
include_once("../funciones/adodb_time_inc.php");
include_once("../funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("../clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

if ($_POST['lee']<>'') {
      $cursos=obtenercursos($bd);
      foreach ($cursos['unidad'] as $key => $valor) {
          $datos_json[] = '"'.$cursos['unidad'][$key].'":'.'"'.$valor.'"';
      }
      // echo '<p>'.iconv("ISO-8859-1", "UTF-8",  $cadena).'</p>';
      echo "{".implode(",", $datos_json)."}";

} else {
  // echo 'No tienes nada';
}
?>
