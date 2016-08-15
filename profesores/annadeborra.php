<?php
include_once("../funciones/seguridad/mysql_inc.php");
include_once("../funciones/adodb_time_inc.php");
include_once("../funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("../clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

if ($_POST['lee']<>'') {
       // 0º) consigue saber la acción
       $recibidos=explode("***",$_POST['lee']);
       $action=$recibidos[0]; //accion 0->no borra 1->sí borra
       $dniadministrador=trim(strtolower($recibidos[1]));
       // 1º) Reconoce el fichero
       $lineas=file("../ficheros/profesores.csv");
       $campos=explode(";",$lineas[0]); //array con el nombre de los campos
       $marcador=0; $existefecha=0;
       // echo '<ul>';
       foreach ($campos as $num_campos => $nombre) {   
         // $nombre=iconv( "UTF-8", "ISO-8859-1",  $nombre); // convierte a ISO-8859-1 
         if (trim(strtolower($nombre))=="empleado/a") {$marcador++; $ordempleado=$num_campos;}
         if (trim(strtolower($nombre))=="dni/pasaporte") {$marcador++; $orddni=$num_campos;}
	 if (trim(strtolower($nombre))=="idea") {$ordidea=$num_campos; $existeidea=1;} 
	 if (trim(strtolower($nombre))=="tutorde") {$ordtutorde=$num_campos; $existetutorde=1;} 
         if (trim(strtolower($nombre))=="email") {$ordemail=$num_campos; $existeemail=1;} 
         // echo '<li><b>'.$nombre.' ('.($num_campos+1).') -- Campos correctos: '.$marcador.'</b></li>';
       }
       // echo '</ul>';
       // 2º) Es un fichero válido, contiene los 4 campos.
       if ($marcador==2) {
          // echo '<p>Fichero correcto. Tiene al menos 4 campos válidos</p>';
          // 2a) Inicializa varios arrays con datos
          $datos[]=array();
	  // 2b) almacena en arrays cada valor de cada campo
          // $cadena2="";
          $i=0;
          $hayadministrador=0;
          foreach ($lineas as $num_linea => $linea) { // recorre cada linea de los datos
            $valores=explode(";",$linea); // convierte cada linea en un array
            if ($num_linea>0 && trim($valores[$ordempleado])<>'') { // línea que no contiene el nombre de los campos y existe en un curso       
	       $datos['empleado'][$i]=$valores[$ordempleado];	
               $datos['dni'][$i]=$valores[$orddni];
               // $cadena2.=$datos['dni'][$i].'<br>';
               if ($existeidea==1) {$datos['idea'][$i]=$valores[$ordidea];} else { $datos['idea'][$i]=""; }
	       if ($existetutorde==1) {$datos['tutorde'][$i]=$valores[$ordtutorde];} else { $datos['tutorde'][$i]=""; }
	       if ($existeemail==1) {$datos['email'][$i]=$valores[$ordemail];} else { $datos['email'][$i]=$datos['dni'][$i]."@prueba.es";}
               if (strtoupper($dniadministrador)==$datos['dni'][$i]) { $datos['administrador'][$i]=1; $hayadministrador=1; } else { $datos['administrador'][$i]=0; } 
	       $i++; //aumento el contador
            }
          } 
       } // fin del if
	
       // 3º) Borra los datos anteriores
       if ($action==1 && $hayadministrador==1) { //borrar datos anteriores
          borratabla($bd,"tb_profesores");
          $cadena="Borrados datos anteriores e incorporados los nuevos. Administrador ".strtoupper($dniadministrador);
       } else if ($action==1 && $hayadministrador==0) { 
	  $cadena="No hay datos de administrador al crear una nueva tabla de profesores/as. SALGO";
          exit;
       }
       else {$cadena="Incorporados datos nuevos";}

       // 4º) Una vez introducidos los datos en la variable "datos", pasar a la BD
       foreach ($datos['empleado'] as $puntero => $empleado) {
          // En la variable $_POST['lee'] se almacena el curso actual
          // escribeprofesores($basededatos,$EMPLEADO,$DNI,$IDEA,$TUTORDE,$EMAIL,$ADMINISTRADOR) 
          escribeprofesores($bd,$empleado,$datos['dni'][$puntero],$datos['idea'][$puntero],$datos['tutorde'][$puntero],$datos['email'][$puntero],$datos['administrador'][$puntero]);         
       }

       // Optimiza la tabla
       optimizatabla($bd,"tb_profesores");

       echo $cadena;
       // echo $recibidos[0]." --- ".$recibidos[1];

} else {
  // echo 'No tienes nada';
}
?>
