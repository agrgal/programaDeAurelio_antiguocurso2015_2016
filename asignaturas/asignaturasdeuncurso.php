<?php
include_once("../funciones/seguridad/mysql_inc.php");
include_once("../funciones/adodb_time_inc.php");
include_once("../funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("../clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

if ($_POST['lee']<>'') {
      // $_POST['lee']-> en esta variable se guarda el dato de la clase escogida
      // $datos=explode("#",$_POST['lee']);
      $curso=$_POST['lee'];
      $asignaturas=obtenerasignaturas($bd);
      foreach ($asignaturas['Materias'] as $key => $valor) {
          $datos_json[] = '"'.$asignaturas['idmateria'][$key].'":'.'"'.$valor.' ('.$asignaturas['Abr'][$key].')"';
          // $datos_json[] = '"'.$valor.'":'.'"'.$alumnos['apll1'][$key].'"';
          // $cadena.=$alumnos['apll1'][$key];
      }
      // echo '<p>'.iconv("ISO-8859-1", "UTF-8",  $cadena).'</p>';
      echo "{".implode(",", $datos_json)."}";
      // echo $curso;

} else {
  // echo 'No tienes nada';
}
?>
