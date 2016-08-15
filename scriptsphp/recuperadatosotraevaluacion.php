<?php
include_once("../funciones/seguridad/mysql_inc.php");
include_once("../funciones/adodb_time_inc.php");
include_once("../funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("../clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

if ($_POST['lee']<>'') {
      // $_POST['lee']-> en esta variable se guarda el dato de la clase escogida
      $datos=array();
      $datos=explode("***",$_POST['lee']); // en la variable array se guardan los datos a actualizar
      // datos[0]->asignacion
      // 1-> alumno; 2-> evaluacion; 
      // convertir observaciones a ISO-8859-1
      $yapuestos = implode("#",recuperacadenaarray($bd,$datos[0],$datos[1],$datos[2]));	
      // escribecadena($bd,$calendario->fechadehoy(),$datos[0],$datos[1],$datos[2],$datos[3],$datos[4],$datos[5],$datos[6]);
      $recobs = recuperaobservaciones($bd,$datos[0],$datos[1],$datos[2]); 
      // if (strlen($recobs)>10000) {$recobs=substr($recobs,0,10000);}
      // $recobs= iconv( "ISO-8859-1","UTF-8", $recobs);
      // Para visualizar la cadena  
      $b=$yapuestos."***".$recobs;
      echo $b;
} else {
      echo 'No tienes nada';
}
?>
