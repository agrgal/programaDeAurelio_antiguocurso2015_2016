<?php
include_once("../funciones/seguridad/mysql_inc.php");
include_once("../funciones/adodb_time_inc.php");
include_once("../funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("../clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

     $idcev = $_POST["idcev"];
     $nombre = iconv("UTF-8","ISO-8859-1",$_POST["nombre"]); // pasarlo a la base de datos
     $peso = $_POST["peso"];
     $iev = $_POST["iev"];
     $indicadores = $_POST["indicadores"];
     $asignacion = $_POST["asignacion"];
     $evaluacion = $_POST["evaluacion"];
     $descripcion = $_POST["descripcion"];

     // FECHAS
     $fec1=explode("/",$_POST["fechainipre"]);
     $fechainipre = gmdate("Y-m-d",mktime(12, 0, 0, $fec1[1], $fec1[0], $fec1[2])); // cambio  a formato DATE mysql
     
     $fec2=explode("/",$_POST["fechainireal"]);
     $fechainireal = gmdate("Y-m-d",mktime(12, 0, 0, $fec2[1], $fec2[0], $fec2[2])); // cambio  a formato DATE mysql
     
     $fec3=explode("/",$_POST["fechafinpre"]);
     $fechafinpre = gmdate("Y-m-d",mktime(12, 0, 0, $fec3[1], $fec3[0], $fec3[2])); // cambio  a formato DATE mysql
    
     $fec4=explode("/",$_POST["fechafinreal"]);
     $fechafinreal = gmdate("Y-m-d",mktime(12, 0, 0, $fec4[1], $fec4[0], $fec4[2])); // cambio  a formato DATE mysql

     // Ordenar las fechas 
     if ($fechainipre>$fechafinpre) {
         $fecha = $fechainipre; $fechainipre = $fechafinpre; $fechafinpre = $fecha;
     }
     if ($fechainireal>$fechafinreal) {
         $fecha = $fechainireal; $fechainireal = $fechafinreal; $fechafinreal = $fecha;
     }
    
     // Inserta o edita
     if (empty($evaluacion) || empty($asignacion) || empty($nombre) ) {
        $devuelve = "Anotación y/o fecha vacíos. Introduce datos.";
     } else {
	     if (is_null($idcev) or empty($idcev)) {
		$Sql="INSERT INTO tb_misconceptosevaluativos (nombre,peso,iev,indicadores,fechainipre,fechafinpre,fechainireal,fechafinreal,asignacion,evaluacion,descripcion) VALUES (";
		if ($nombre<>'') {$Sql.="'".$nombre."', ";} else {$Sql.="'-',";}
		if ($peso<>'') {$Sql.="'".$peso."', ";} else {$Sql.="'-',";}
		if ($iev<>'') {$Sql.="'".$iev."', ";} else {$Sql.="'-',";}
		if ($indicadores<>'') {$Sql.="'".$indicadores."', ";} else {$Sql.="'-',";}
		if ($fechainipre<>'') {$Sql.="'".$fechainipre."', ";} else {$Sql.="'-',";}
		if ($fechafinpre<>'') {$Sql.="'".$fechafinpre."', ";} else {$Sql.="'-',";}
		if ($fechainireal<>'') {$Sql.="'".$fechainireal."', ";} else {$Sql.="'-',";}
		if ($fechafinreal<>'') {$Sql.="'".$fechafinreal."', ";} else {$Sql.="'-',";}
		if ($asignacion<>'') {$Sql.="'".$asignacion."', ";} else {$Sql.="'-',";}
		if ($evaluacion<>'') {$Sql.="'".$evaluacion."', ";} else {$Sql.="'-',";}
		if ($descripcion<>'') {$Sql.="'".$descripcion."', ";} else {$Sql.="'', ";}
		$Sql=substr($Sql,0,strlen($Sql)-2); /* Quitar la última coma */
		$Sql.=")"; 	
	     } else { // si no, hacemos un UPDATE
		$Sql="UPDATE tb_misconceptosevaluativos SET ";
		$Sql.="nombre='".$nombre."', ";	
		$Sql.="peso='".$peso."', ";
		$Sql.="iev='".$iev."', ";
		$Sql.="indicadores='".$indicadores."', ";
		$Sql.="fechainipre='".$fechainipre."', ";	
		$Sql.="fechafinpre='".$fechafinpre."', ";
		$Sql.="fechainireal='".$fechainireal."', ";
		$Sql.="fechafinreal='".$fechafinreal."', ";
		$Sql.="asignacion='".$asignacion."', ";	
		$Sql.="evaluacion='".$evaluacion."', ";
		$Sql.="descripcion='".$descripcion."', ";
		$Sql= substr($Sql,0,strlen($Sql)-2); /* Quitar la última coma */
		$Sql.=" WHERE IDcev='".trim($idcev)."'"; 
	     } // fin del if

             $link=Conectarse($bd);
	     $result=mysql_query($Sql,$link); //ejecuta la consulta
	     mysql_free_result($result);

             $devuelve = "Dato introducido o cambiado.";
     } // fin del if 

     echo $devuelve;
?>
