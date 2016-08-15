<?php
include_once("../funciones/seguridad/mysql_inc.php");
include_once("../funciones/adodb_time_inc.php");
include_once("../funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("../clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

if ($_POST['lee']<>'') {
      // $_POST['lee']-> en esta variable se guarda el dato de la clase escogida
      $curso=trim($_POST['lee']);
      $alumnos=obtenerclase($bd,$curso);
      $i=0;
      foreach ($alumnos['idalumno'] as $key => $valor) {
          $cadena['idalumno'][$i]=$valor;
          $cadena['alumno'][$i]=iconv("ISO-8859-1","UTF-8",$alumnos['alumno'][$key]);
          $i++;
      }
      // echo '<p>'.iconv("ISO-8859-1", "UTF-8",  $cadena).'</p>';
      // echo "{".implode(",", $datos_json)."}";
      echo json_encode($cadena); 
      // echo $_POST['lee'];

} else {
  // echo 'No tienes nada';
}
?>
