<?php
include_once("../funciones/seguridad/mysql_inc.php");
include_once("../funciones/adodb_time_inc.php");
include_once("../funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("../clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

if ($_POST['lee']<>'') {
      // $_POST['lee']-> en esta variable se guarda el dato de la clase escogida
      $ID=$_POST['lee'];
      $mostrar = cambiarnombre(dado_Id($bd,$ID,"Empleado","tb_profesores","idprofesor"));
      // Borrado de ese dato
      $Sql='DELETE FROM tb_profesores WHERE idprofesor="'.$ID.'"';
      $link=Conectarse($bd);
      $result=mysql_query($Sql,$link); //ejecuta la consulta
      mysql_free_result($result);
      // echo '<p>'.iconv("ISO-8859-1", "UTF-8",  $cadena).'</p>';
      echo "Dato borrado: ".iconv("UTF-8","ISO-8859-1", $mostrar); 
      // echo "ID: ".$ID;
} else {
  // echo 'No tienes nada';
}
?>
