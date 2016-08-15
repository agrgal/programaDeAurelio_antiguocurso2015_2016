<?php
include_once("../funciones/seguridad/mysql_inc.php");
include_once("../funciones/adodb_time_inc.php");
include_once("../funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("../clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

if ($_POST['lee']<>'') {
      // $_POST['lee']-> en esta variable se guarda el dato de la clase escogida
      $datos=array();
      $datos=explode('***',$_POST['lee']);
      $identificacion=$datos[0]; //identificacion
      $pro=$datos[1]; // profesor
      $des=$datos[2]; // descripción
      $tutorada=$datos[3]; // tutor
      $mat=$datos[4]; // materia
      $cadena=$datos[5]; // cadena
      $comprueba=compruebasiexiste($bd,$pro,$mat,$cadena);  
      // $comprueba=0;    
      // introduce datos
      if ($identificacion==0 && $comprueba==0) { // escribe un nuevo dato INSERT
	$Sql="INSERT INTO tb_asignaciones (profesor,materia,datos,descripcion,tutorada) VALUES (";
	if ($pro<>'') {$Sql.="'".$pro."', ";} else {$Sql.="'-',";}
	if ($mat<>'') {$Sql.="'".$mat."', ";} else {$Sql.="'-',";}
	if ($cadena<>'') {$Sql.="'".$cadena."', ";} else {$Sql.="'-',";}
	if ($des<>'') {$Sql.="'".$des."', ";} else {$Sql.="'-',";}
	if ($tutorada<>'') {$Sql.="'".$tutorada."', ";} else {$Sql.="'-',";}
	$Sql=substr($Sql,0,strlen($Sql)-2); // Quitar la última coma
	$Sql.=")"; 
        $mensaje="Nueva asignación guardada";
     } else if ($identificacion==0 && $comprueba>0) { 
        $mensaje="¡Cuidado! Parece que contigo, con esa asignatura, ya está registrado ese grupo. Lo que debes hacer es actualizar sus datos";
     } else if ($identificacion>0) {// si no, hacemos un UPDATE
	$Sql="UPDATE tb_asignaciones SET ";
	$Sql.="profesor='".$pro."', ";	
	$Sql.="materia='".$mat."', ";
	$Sql.="datos='".$cadena."', ";
	$Sql.="descripcion='".$des."', ";
	$Sql.="tutorada='".$tutorada."', ";
	$Sql= substr($Sql,0,strlen($Sql)-2); // Quitar la última coma
	$Sql.=" WHERE idasignacion='".$identificacion."'"; 
        $mensaje="Asignación ya existente actualizada";
     } // fin del if
	$link=Conectarse($bd);
	$result=mysql_query($Sql,$link); //ejecuta la consulta
	mysql_free_result($result);      
      echo $mensaje;
      // echo $_POST['lee'];  
} else {
  // echo 'No tienes nada';
}

// *******************************************
// comprueba si existe un grupo con esos datos
// *******************************************
function compruebasiexiste($bd,$profesor,$materia,$cadena) { 
   $Sql='SELECT idasignacion FROM tb_asignaciones WHERE profesor="'.$profesor.'" AND materia="'.$materia.'" AND datos ="'.$cadena.'"';
   $link=Conectarse($bd);
   $result=mysql_query($Sql,$link); //ejecuta la consulta
   $row=mysql_fetch_array($result);
	if (!is_null($row['idasignacion']) or !empty($row['idasignacion'])) { // si no lo recupera, el valor por defecto)
					  return $row['idasignacion']; //envia el valor dado
					  } else {
				          return 0;
					  }
} // fin de la función
?>
