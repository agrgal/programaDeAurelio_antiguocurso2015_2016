<?php
include_once("../funciones/seguridad/mysql_inc.php");
include_once("../funciones/adodb_time_inc.php");
include_once("../funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("../clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

     $porcentaje=$_POST["porcentaje"];
     $id = $_POST["id"];

     if (empty($porcentaje) || empty($id)) {
        $devuelve = "No has introducido correctamente los datos";
     } else {
		$Sql="UPDATE tb_misinstrumentosevaluativos SET ";
		$Sql.="porcentaje='".$porcentaje."', ";
		$Sql= substr($Sql,0,strlen($Sql)-2); /* Quitar la última coma */
		$Sql.=" WHERE IDiev='".trim($id)."'"; 
             $link=Conectarse($bd);
	     $result=mysql_query($Sql,$link); //ejecuta la consulta
	     mysql_free_result($result);

             $devuelve = "Dato introducido o cambiado.";
     } // fin del if 

     echo $devuelve;
?>
