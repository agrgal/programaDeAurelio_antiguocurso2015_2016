<?php
include_once("../funciones/seguridad/mysql_inc.php");
include_once("../funciones/adodb_time_inc.php");
include_once("../funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("../clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

if ($_POST['lee']<>'') {
       // 0º) consigue saber la acción
       $anno=substr($_POST['lee'],-4); //extrae el año
       $action=substr($_POST['lee'],0,1); //accion 0->no borra 1->sí borra
       // 1º) Reconoce el fichero
       $lineas=file("../ficheros/alumnos.csv");
       $campos=explode(",",$lineas[0]); //array con el nombre de los campos
       $marcador=0; $existefecha=0;
       // echo '<ul>';
       foreach ($campos as $num_campos => $nombre) {   
         if (trim(strtolower($nombre))=="nombre") {$marcador++; $ordnombre=$num_campos;}
         if (trim(strtolower($nombre))=="primer apellido") {$marcador++; $ordpa=$num_campos;}
         if (trim(strtolower($nombre))=="segundo apellido") {$marcador++; $ordsa=$num_campos;}
         if (trim(strtolower($nombre))=="unidad") {$marcador++; $ordunidad=$num_campos;}
	 if (trim(strtolower($nombre))=="fecha nacimiento") {$ordfecha=$num_campos; $existefecha=1;}
         // echo '<li><b>'.$nombre.' ('.$num_campos.') -- Campos correctos: '.$marcador.'</b></li>';
       }
       // echo '</ul>';
       // 2º) Es un fichero válido, contiene los 4 campos.
       if ($marcador==4) {
          // echo '<p>Fichero correcto. Tiene al menos 4 campos válidos</p>';
          // 2a) Inicializa varios arrays con datos
          $datos[]=array();
	  // 2b) almacena en arrays cada valor de cada campo
          $i=0;
          foreach ($lineas as $num_linea => $linea) { // recorre cada linea de los datos
            $valores=explode(",",$linea); // convierte cada linea en un array
            if ($num_linea>0 && $valores[$ordunidad]<>'') { // línea que no contiene el nombre de los campos y existe en un curso       
	       $datos['nombre'][$i]=$valores[$ordnombre];	
               $datos['pa'][$i]=$valores[$ordpa];
	       $datos['sa'][$i]=$valores[$ordsa];
               $datos['unidad'][$i]=$valores[$ordunidad];
               if ($existefecha==1) {$datos['fnac'][$i]=$valores[$ordfecha];} else {$datos['fnac'][$i]="0000-00-00";}
	       $i++; //aumento el contador
            }
          }
       } // fin del if

       // 3º) Borra los datos anteriores
       if ($action=="1") { //borrar datos anteriores
          borratabla($bd,"alumnos");
          $cadena="Borrados datos anteriores e incorporados los nuevos";
       } else {$cadena="Incorporados datos nuevos";}

       // 4º) Una vez introducidos los datos en la variable "datos", pasar a la BD
       foreach ($datos['nombre'] as $puntero => $nombre) {
          // En la variable $_POST['lee'] se almacena el curso actual
          escribealumno($bd,$nombre,$datos['pa'][$puntero],$datos['sa'][$puntero],$datos['fnac'][$puntero],$datos['unidad'][$puntero],$anno,"1");         
       } 

       // for ($i=10;$i<=100;$i++) {          
       //  $cadena.='<p>'.dado_Id($bd,$i,"apll2","alumnos","Idalumno").'</p>';
       // }

       echo $cadena;

} else {
  // echo 'No tienes nada';
}
?>
