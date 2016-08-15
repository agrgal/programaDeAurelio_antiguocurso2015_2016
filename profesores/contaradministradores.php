<?php
include_once("../funciones/seguridad/mysql_inc.php");
include_once("../funciones/adodb_time_inc.php");
include_once("../funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("../clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

if ($_POST['lee']<>'') {
      // $_POST['lee']-> en esta variable se guarda el dato de la clase escogida
      $Sql='SELECT idprofesor FROM tb_profesores WHERE administrador="1"';          
      // echo $Sql;
      $link=Conectarse($bd);
      $result=mysql_query($Sql,$link); //ejecuta la consulta
      $cuenta=mysql_num_rows($result);
      mysql_free_result($result);
      // echo '<p>'.iconv("ISO-8859-1", "UTF-8",  $cadena).'</p>';
      echo $cuenta;
} else {
  // echo 'No tienes nada';
}
?>
