<?php
include_once("../funciones/seguridad/mysql_inc.php");
include_once("../funciones/adodb_time_inc.php");
include_once("../funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("../clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

if ($_POST['lee']<>'') {
      // $_POST['lee']-> en esta variable se guarda el dato de la clase escogida
      $datos=array();
      $datos=explode("#",$_POST['lee']); // en la variable array se guardan los datos a actualizar
        $Sql="UPDATE tb_profesores SET ";
	$Sql.="Empleado='".iconv("UTF-8","ISO-8859-1",$datos[1])."', ";
	$Sql.="DNI='".$datos[2]."', ";
	$Sql.="IDEA='".$datos[3]."', ";
	$Sql.="tutorde='".$datos[4]."', ";
	$Sql.="email='".$datos[5]."', ";
	$Sql.="administrador='".$datos[6]."', ";
	$Sql= substr($Sql,0,strlen($Sql)-2); /* Quitar la última coma */
	$Sql.=" WHERE idprofesor='".$datos[0]."'";            
        // echo $Sql;
	$link=Conectarse($bd);
	$result=mysql_query($Sql,$link); //ejecuta la consulta
	mysql_free_result($result);
      // echo '<p>'.iconv("ISO-8859-1", "UTF-8",  $cadena).'</p>';
      echo $datos[1]." (Actualizado)";
} else {
  // echo 'No tienes nada';
}
?>
