<?php
include_once("../funciones/seguridad/mysql_inc.php");
include_once("../funciones/adodb_time_inc.php");
include_once("../funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("../clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

if ($_POST['lee']<>'') {
      // $_POST['lee']-> en esta variable se guarda el dato de la clase escogida
      $datos=array();
      $datos=explode('#***#',$_POST['lee']);
      $identificacion=0;
      $eval=$datos[0];
      $asignacion=$datos[1];
      $opinion=$datos[2];
      $actuaciones=$datos[3];
      $mejora=$datos[4];
      $identificacion=$datos[5];
      if ($identificacion==0) { // escribe un nuevo dato INSERT
	$Sql="INSERT INTO tb_opiniongeneral (eval,asignacion,opinion,actuaciones,mejora) VALUES (";
	if ($eval<>'') {$Sql.="'".$eval."', ";} else {$Sql.="'-',";}
	if ($asignacion<>'') {$Sql.="'".$asignacion."', ";} else {$Sql.="'-',";}
	if ($opinion<>'') {$Sql.="'".$opinion."', ";} else {$Sql.="'-',";}
	if ($actuaciones<>'') {$Sql.="'".$actuaciones."', ";} else {$Sql.="'-',";}
	if ($mejora<>'') {$Sql.="'".$mejora."', ";} else {$Sql.="'-', ";}
	$Sql=substr($Sql,0,strlen($Sql)-2); // Quitar la última coma
	$Sql.=")"; 
        $mensaje="Nueva opinión general guardada";
     } else if ($identificacion>0) {// si no, hacemos un UPDATE
	$Sql="UPDATE tb_opiniongeneral SET ";
	$Sql.="eval='".$eval."', ";	
	$Sql.="asignacion='".$asignacion."', ";
	$Sql.="opinion='".$opinion."', ";
	$Sql.="actuaciones='".$actuaciones."', ";
	$Sql.="mejora='".$mejora."', ";
	$Sql= substr($Sql,0,strlen($Sql)-2); // Quitar la última coma
	$Sql.=" WHERE idopiniongeneral='".$identificacion."'"; 
        $mensaje="Opinión ya existente actualizada";
     } // fin del if
	$link=Conectarse($bd);
	$result=mysql_query($Sql,$link); //ejecuta la consulta
	 mysql_free_result($result);      
      echo $mensaje;
      // echo $opinion." - ".$bd." - ".$Sql; 
      // echo $_POST['lee'];  
      // echo 'lo tiene';
} else {
      echo 'No tienes nada';
}


