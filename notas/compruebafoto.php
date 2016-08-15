<?php
include_once("../funciones/seguridad/mysql_inc.php");
include_once("../funciones/adodb_time_inc.php");
include_once("../funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("../clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

          $fotografia="../imagenes/fotos/".$_POST["alumno"];
	  $extension=NULL;
	  if (file_exists($fotografia.".jpeg")) { $extension=".jpeg";}
	  if (file_exists($fotografia.".jpg")) { $extension=".jpg";}
	  if (file_exists($fotografia.".png")) { $extension=".png";}
	  if (file_exists($fotografia.".gif")) { $extension=".gif";}
	  if ($extension!=NULL) {
               $fotografia.=$extension; // añado la extensión	
          } else {
               // $fotografia="../imagenes/fotos/boygirl2.png"; // foto cuando no existe...
               $fotografia="";
          }
          $fotografia = substr($fotografia,1,strlen($fotografia)-1);
          echo $fotografia; // asi quito el primer punto 
?>
