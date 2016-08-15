<?php
include_once("../funciones/seguridad/mysql_inc.php");
include_once("../funciones/adodb_time_inc.php");
include_once("../funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("../clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

if ($_POST['lee']<>'') {
      // $_POST['lee']-> en esta variable se guarda el dato de la clase escogida
      $cursos=obtenercursos($bd);
      $i=0;
      foreach ($cursos['unidad'] as $key => $valor) {
          $cadena['unidad'][$i]=$valor;
          $i++;
      } 
      // echo '<p>'.iconv("ISO-8859-1", "UTF-8",  $cadena).'</p>';
      // echo "{".implode(",", $datos_json)."}";
      echo json_encode($cadena); 
      // echo $bd;
      // echo $calendario->horactual();

} else {
  // echo 'No tienes nada';
}
?>
