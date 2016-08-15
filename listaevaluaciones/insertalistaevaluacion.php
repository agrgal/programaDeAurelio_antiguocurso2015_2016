<?php
include_once("../funciones/seguridad/mysql_inc.php");
include_once("../funciones/adodb_time_inc.php");
include_once("../funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("../clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

     $fec=explode("/",$_POST["fechaini"]);
     $fechaini = gmdate("Y-m-d",mktime(12, 0, 0, $fec[1], $fec[0], $fec[2])); //  formato DATE mysql
     $fec=explode("/",$_POST["fechafin"]);
     $fechafin = gmdate("Y-m-d",mktime(12, 0, 0, $fec[1], $fec[0], $fec[2])); //  formato DATE mysql
     $evaluacion = iconv("UTF-8","ISO-8859-1",$_POST["evaluacion"]);
     $id = $_POST["id"];
     if (empty($fechaini) || empty($fechafin) ||empty($evaluacion)) {
        $devuelve = "Evaluación y/o fecha vacíos. Introduce datos.";
     } else {
	     if (is_null($id) or empty($id)) {
		$Sql="INSERT INTO tb_listaevaluaciones (nombre, fechaini, fechafin) VALUES (";
		if ($evaluacion<>'') {$Sql.="'".$evaluacion."', ";} else {$Sql.="'-',";}
		if ($fechaini<>'') {$Sql.="'".$fechaini."', ";} else {$Sql.="'-',";}
		if ($fechafin<>'') {$Sql.="'".$fechafin."', ";} else {$Sql.="'-',";}
		$Sql=substr($Sql,0,strlen($Sql)-2); 
		$Sql.=")"; 	
	     } else { // si no, hacemos un UPDATE
		$Sql="UPDATE tb_listaevaluaciones SET ";
		$Sql.="nombre='".$evaluacion."', ";	
		$Sql.="fechaini='".$fechaini."', ";
		$Sql.="fechafin='".$fechafin."', ";
		$Sql= substr($Sql,0,strlen($Sql)-2);
		$Sql.=" WHERE idlistaevaluaciones='".trim($id)."'"; 
	     } // fin del if

             $link=Conectarse($bd);
	     $result=mysql_query($Sql,$link); //ejecuta la consulta
	     mysql_free_result($result);

             $devuelve = "Dato introducido o cambiado.";
     } // fin del if 

     echo $devuelve; 
     // echo $fechaini." - ".$fechafin." - ".$id." - ".$evaluacion;
?>
