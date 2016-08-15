<?php
include_once("../funciones/seguridad/mysql_inc.php");
include_once("../funciones/adodb_time_inc.php");
include_once("../funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("../clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

     $nombre=iconv("UTF-8","ISO-8859-1",$_POST["nombre"]);
     $abreviatura=iconv("UTF-8","ISO-8859-1",$_POST["abreviatura"]);
     $porcentaje=$_POST["porcentaje"];
     $notaminima=$_POST["notaminima"];
     $evaluacion = $_POST["evaluacion"];
     $asignacion = $_POST["asignacion"];
     $id = $_POST["id"];

     if ( empty($nombre) || empty($abreviatura) || empty($porcentaje)) {
        $devuelve = "No has introducido correctamente los datos";
     } else {
	     if (is_null($id) or empty($id)) {
		$Sql="INSERT INTO tb_misinstrumentosevaluativos (nombre, abreviatura, porcentaje, notaminima, evaluacion, asignacion) VALUES (";
		if ($nombre<>'') {$Sql.="'".$nombre."', ";} else {$Sql.="'-',";}
		if ($abreviatura<>'') {$Sql.="'".$abreviatura."', ";} else {$Sql.="'-',";}
		if ($porcentaje<>'') {$Sql.="'".$porcentaje."', ";} else {$Sql.="'-',";}
		if ($notaminima<>'') {$Sql.="'".$notaminima."', ";} else {$Sql.="'', ";}
		if ($evaluacion<>'') {$Sql.="'".$evaluacion."', ";} else {$Sql.="'', ";}
		if ($asignacion<>'') {$Sql.="'".$asignacion."', ";} else {$Sql.="'', ";}
		$Sql=substr($Sql,0,strlen($Sql)-2); /* Quitar la última coma */
		$Sql.=")"; 	
	     } else { // si no, hacemos un UPDATE
		$Sql="UPDATE tb_misinstrumentosevaluativos SET ";
		$Sql.="nombre='".$nombre."', ";	
		$Sql.="abreviatura='".$abreviatura."', ";
		$Sql.="porcentaje='".$porcentaje."', ";
		$Sql.="notaminima='".$notaminima."', ";
		$Sql.="evaluacion='".$evaluacion."', ";
		$Sql.="asignacion='".$asignacion."', ";
		$Sql= substr($Sql,0,strlen($Sql)-2); /* Quitar la última coma */
		$Sql.=" WHERE IDiev='".trim($id)."'"; 
	     } // fin del if

             $link=Conectarse($bd);
	     $result=mysql_query($Sql,$link); //ejecuta la consulta
	     mysql_free_result($result);

             $devuelve = "Dato introducido o cambiado.";
     } // fin del if 

     echo $devuelve;
?>
