<?php
include_once("../funciones/seguridad/mysql_inc.php");
include_once("../funciones/adodb_time_inc.php");
include_once("../funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("../clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

     $nombre = iconv("UTF-8","ISO-8859-1",$_POST["nombre"]);
     $abreviatura = iconv("UTF-8","ISO-8859-1",strtoupper($_POST["abreviatura"]));
     $id = $_POST["id"];
     if (empty($nombre) || empty($abreviatura)) {
        $devuelve = "Competencia no válida.";
     } else {
	     if (is_null($id) or empty($id)) {
		$Sql="INSERT INTO tb_listacompetencias (nombre, abreviatura) VALUES (";
		if ($nombre<>'') {$Sql.="'".$nombre."', ";} else {$Sql.="'-',";}
		if ($abreviatura<>'') {$Sql.="'".$abreviatura."', ";} else {$Sql.="'-',";}
		$Sql=substr($Sql,0,strlen($Sql)-2); 
		$Sql.=")"; 	
	     } else { // si no, hacemos un UPDATE
		$Sql="UPDATE tb_listacompetencias SET ";
		$Sql.="nombre='".$nombre."', ";	
		$Sql.="abreviatura='".$abreviatura."', ";
		$Sql= substr($Sql,0,strlen($Sql)-2);
		$Sql.=" WHERE idcompetencia='".trim($id)."'"; 
	     } // fin del if

             $link=Conectarse($bd);
	     $result=mysql_query($Sql,$link); //ejecuta la consulta
	     mysql_free_result($result);

             $devuelve = "Dato introducido o cambiado.";
     } // fin del if 

     echo $devuelve; 
     // echo $nombre." - ".$abreviatura." - ".$id;
     // echo $Sql;
?>
