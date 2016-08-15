<?php
include_once("../funciones/seguridad/mysql_inc.php");
include_once("../funciones/adodb_time_inc.php");
include_once("../funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("../clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

        $link=Conectarse($bd); // y me conecto. //dependiendo del tipo recupero uno u otro.
	$Sql="DELETE FROM tb_listaevaluaciones WHERE idlistaevaluaciones='".$_POST['id']."'";
	$result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result
	mysql_free_result($result); 

        echo 'hecho';

?>
