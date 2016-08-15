<?php
include_once("../funciones/seguridad/mysql_inc.php");
include_once("../funciones/adodb_time_inc.php");
include_once("../funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("../clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

if ($_POST['lee']<>'') {
      // $_POST['lee']-> en esta variable se guarda el dato de la clase escogida
      $ID=$_POST['lee'];
      $profesores=obtenerprofesores($bd);
      foreach ($profesores['idprofesor'] as $key => $valor) {
         if ($valor==$ID) { 
             $datos_json[]='"idprofesor":"'.$valor.'"';
	     $datos_json[]='"Empleado":"'.trim($profesores['Empleado'][$key]).'"';
             $datos_json[]='"DNI":"'.trim($profesores['DNI'][$key]).'"';
	     $datos_json[]='"IDEA":"'.trim($profesores['IDEA'][$key]).'"';
	     $datos_json[]='"tutorde":"'.trim($profesores['tutorde'][$key]).'"';
             $datos_json[]='"email":"'.trim($profesores['email'][$key]).'"';
             $datos_json[]='"administrador":"'.trim($profesores['administrador'][$key]).'"';
         }
      }
      // echo '<p>'.iconv("ISO-8859-1", "UTF-8",  $cadena).'</p>';
      echo "{".implode(",", $datos_json)."}";
      // echo $datos_json;

} else {
  // echo 'No tienes nada';
}
?>
