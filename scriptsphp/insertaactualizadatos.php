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
      // datos[0]-> asignacion
      // 1-> alumno; 2-> evaluacion; 3-> cadena items ; 4-> observaciones
      // convertir observaciones a ISO-8859-1
      // $datos[4]=iconv("UTF-8","ISO-8859-1",$datos[4]);
      // detecta caracteres que no escribe y los sustituye por los correspondientes en HTML
      $modificar=str_replace("'", "&prime;", $datos[4]);
      $modificar=str_replace("\\", "&#92;", $modificar);
      escribecadena($bd,$calendario->fechadehoy(),$datos[0],$datos[1],$datos[2],$datos[3],$modificar);
      // Para visualizar la cadena  
      // $b = $bd." - ";
      // $b.= $calendario->fechadehoy()." - ";      
      // echo '<p>'.iconv("ISO-8859-1", "UTF-8",  $cadena).'</p>';
      // foreach ($datos as $a) {
         // $b.=$a." - "; 
      // }
      // echo $b;
      echo htmlentities($modificar);
} else {
      echo 'No tienes nada';
}
?>
