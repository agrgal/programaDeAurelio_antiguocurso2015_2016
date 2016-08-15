<?php
include_once("../funciones/seguridad/mysql_inc.php");
include_once("../funciones/adodb_time_inc.php");
include_once("../funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("../clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

if ($_POST['lee']<>'') {
      // $_POST['lee']-> en esta variable se guarda el dato de la clase escogida
      $ID=$_POST['lee'];
      $datos_json[]='"materias":"'.iconv("ISO-8859-1","UTF-8",dado_Id($bd,$ID,"Materias","tb_asignaturas","idmateria")).'"';
      $datos_json[]='"abr":"'.iconv("ISO-8859-1","UTF-8",dado_Id($bd,$ID,"Abr","tb_asignaturas","idmateria")).'"';
      // echo '<p>'.iconv("ISO-8859-1", "UTF-8",  $cadena).'</p>';
      echo "{".implode(",", $datos_json)."}"; 
      // echo $ID;
} else {
  // echo 'No tienes nada';
}
?>

<?php 
// pequeña función que cue


?>
