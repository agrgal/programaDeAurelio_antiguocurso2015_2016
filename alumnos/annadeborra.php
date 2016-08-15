<?php
include_once("../funciones/seguridad/mysql_inc.php");
include_once("../funciones/adodb_time_inc.php");
include_once("../funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("../clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

if ($_POST['lee']<>'') {
       // 0º) consigue saber la acción
       $action=$_POST['lee']; //accion 0->no borra 1->sí borra
       // 1º) Reconoce el fichero
       $lineas=file("../ficheros/alumnos.csv");
       $campos=explode(";",$lineas[0]); //array con el nombre de los campos
       $marcador=0; 
       // echo '<ul>';
       foreach ($campos as $num_campos => $nombre) {   
         // $nombre=iconv("ISO-8859-1", "UTF-8",  $nombre); // convierte a UTF8    
         if (trim(strtolower($nombre))=="alumno/a") {$marcador++; $ordnombre=$num_campos;}
         if (trim(strtolower($nombre))=="unidad") {$marcador++; $ordunidad=$num_campos;}
         // echo '<li><b>'.$nombre.' ('.($num_campos+1).') -- Campos correctos: '.$marcador.'</b></li>';
       }
       // echo '</ul>';

       // 2º) Es un fichero válido, contiene los 2 campos.
          if ($marcador==2) {
          // echo '<p>Fichero correcto. Tiene al menos 2 campos válidos</p>';
          // 3a) Inicializa varios arrays con datos
          $datos[]=array();
	  // 3b) almacena en arrays cada valor de cada campo
          $i=0;
          foreach ($lineas as $num_linea => $linea) { // recorre cada linea de los datos
            $valores=explode(";",$linea); // convierte cada linea en un array
            if ($num_linea>0 && trim($valores[$ordunidad])<>'' && trim($valores[$ordnombre])<>'') { // línea que no contiene el nombre de los campos y existe en un curso       
	       $datos['alumno/a'][$i]=$valores[$ordnombre];	
               $datos['unidad'][$i]=$valores[$ordunidad];
               // $cadena.=$datos['alumno/a'][$i]." - ".$datos['unidad'][$i];
	       $i++; //aumento el contador
            }
           } 
          } // fin del if del marcador

       // 3º) Borra los datos anteriores
        if ($action=="1") { //borrar datos anteriores
          borratabla($bd,"tb_alumno");
          $cadena="Borrados datos anteriores e incorporados los nuevos";
       } else {$cadena="Incorporados datos nuevos";}

       // 4º) Una vez introducidos los datos en la variable "datos", pasar a la BD
       foreach ($datos['alumno/a'] as $puntero => $nombre) {
          escribealumno($bd,$nombre,$datos['unidad'][$puntero]);       
       } 

       // Optimiza la tabla
       optimizatabla($bd,"tb_alumno"); 

       echo $cadena;

} else {
  // echo 'No tienes nada';
} 
?>
