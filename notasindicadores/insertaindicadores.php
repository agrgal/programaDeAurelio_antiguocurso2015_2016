<?php
include_once("../funciones/seguridad/mysql_inc.php");
include_once("../funciones/adodb_time_inc.php");
include_once("../funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("../clases/class.micalendario.php");
// $calendario= New micalendario(); // variable de calendario.

     $descripcion = iconv("UTF-8","ISO-8859-1",$_POST["descripcion"]);
     $competencia = iconv("UTF-8","ISO-8859-1",$_POST["competencia"]);
     $profesor = iconv("UTF-8","ISO-8859-1",$_POST["profesor"]);
     $id = $_POST["id"];
     if (empty($descripcion) || empty($competencia) || empty($profesor)) {
        $devuelve = "Indicador no válido. ¿Se te ha olvidado la descripción? ¿Hay un profesor/a?";
     } else {
	     if (is_null($id) or empty($id)) {
		$Sql="INSERT INTO tb_misindicadores (descripcion, profesor, competencia) VALUES (";
		if ($descripcion<>'') {$Sql.="'".$descripcion."', ";} else {$Sql.="'-',";}
		if ($profesor<>'') {$Sql.="'".$profesor."', ";} else {$Sql.="'-',";}
		if ($competencia<>'') {$Sql.="'".$competencia."', ";} else {$Sql.="'-',";}
		$Sql=substr($Sql,0,strlen($Sql)-2); 
		$Sql.=")"; 	
	     } else { // si no, hacemos un UPDATE
		$Sql="UPDATE tb_misindicadores SET ";
		$Sql.="descripcion='".$descripcion."', ";	
		$Sql.="profesor='".$profesor."', ";
		$Sql.="competencia='".$competencia."', ";
		$Sql= substr($Sql,0,strlen($Sql)-2);
		$Sql.=" WHERE idindicador='".trim($id)."'"; 
	     } // fin del if

             $link=Conectarse($bd);
	     $result=mysql_query($Sql,$link); //ejecuta la consulta
	     mysql_free_result($result);

             $devuelve = "Dato introducido o cambiado.";
     } // fin del if 

     echo $devuelve; 
     // echo $descripcion." - ".$competencia." - ".$id." - ".$profesor;
     // echo $Sql;
?>
