<?php
include_once("../funciones/seguridad/mysql_inc.php");
include_once("../funciones/adodb_time_inc.php");
include_once("../funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("../clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

     $fec=explode("/",$_POST["fecha"]);
     $fecha = gmdate("Y-m-d",mktime(12, 0, 0, $fec[1], $fec[0], $fec[2])); // cambio la fecha a formato DATE mysql
     $anotacion = $_POST["anotacion"];
     $asignacion = $_POST["asignacion"];
     $alumno = $_POST["alumno"];
     $id = $_POST["id"];
     if (empty($fecha) || empty($anotacion)) {
        $devuelve = "Anotación y/o fecha vacíos. Introduce datos.";
     } else {
	     if (is_null($id) or empty($id)) {
		$Sql="INSERT INTO tb_anotaciones (fecha, asignacion, alumno, anotacion) VALUES (";
		if ($fecha<>'') {$Sql.="'".$fecha."', ";} else {$Sql.="'-',";}
		if ($asignacion<>'') {$Sql.="'".$asignacion."', ";} else {$Sql.="'-',";}
		if ($alumno<>'') {$Sql.="'".$alumno."', ";} else {$Sql.="'-',";}
		if ($anotacion<>'') {$Sql.="'".$anotacion."', ";} else {$Sql.="'', ";}
		$Sql=substr($Sql,0,strlen($Sql)-2); /* Quitar la última coma */
		$Sql.=")"; 	
	     } else { // si no, hacemos un UPDATE
		$Sql="UPDATE tb_anotaciones SET ";
		$Sql.="fecha='".$fecha."', ";	
		$Sql.="asignacion='".$asignacion."', ";
		$Sql.="alumno='".$alumno."', ";
		$Sql.="anotacion='".$anotacion."', ";
		$Sql= substr($Sql,0,strlen($Sql)-2); /* Quitar la última coma */
		$Sql.=" WHERE idanotacion='".trim($id)."'"; 
	     } // fin del if

             $link=Conectarse($bd);
	     $result=mysql_query($Sql,$link); //ejecuta la consulta
	     mysql_free_result($result);

             $devuelve = "Dato introducido o cambiado.";
     } // fin del if 

     echo $devuelve;
?>
