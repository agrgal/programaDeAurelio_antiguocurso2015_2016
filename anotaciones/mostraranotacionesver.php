<?php
include_once("../funciones/seguridad/mysql_inc.php");
include_once("../funciones/adodb_time_inc.php");
include_once("../funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("../clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.        

	$fecha1=$_POST["fecha1"];
        $fecha2=$_POST["fecha2"]; 
        if ($fecha1=="") {$fecha1="1900-01-01";} else {
            $fec=explode("/",$_POST["fecha1"]);
            $fecha1 = gmdate("Y-m-d",mktime(12, 0, 0, $fec[1], $fec[0], $fec[2])); // cambio la fecha a DATE mysql
        }
	if ($fecha2=="") {$fecha2="9999-12-31";} else {
            $fec=explode("/",$_POST["fecha2"]);
            $fecha2 = gmdate("Y-m-d",mktime(12, 0, 0, $fec[1], $fec[0], $fec[2])); // cambio la fecha a DATE mysql
        }
        if ($fecha1>$fecha2) {
		$fecintermedia = $fecha1;
                $fecha1=$fecha2;
                $fecha2=$fecintermedia; 
        }
        if ($_POST["asignacion"]!=0 || !(empty($_POST["asignacion"]))) {
		$annade=" AND asignacion='".$_POST["asignacion"]."'";
        }  else { $annade=""; }
        $anotaciones=array();        
        $link=Conectarse($bd); // y me conecto. //dependiendo del tipo recupero uno u otro.
	$Sql="SELECT idanotacion, alumno, asignacion, fecha, anotacion FROM tb_anotaciones WHERE alumno='".$_POST["alumno"]."'".$annade." AND fecha>='".$fecha1."' AND fecha<='".$fecha2."' ORDER BY fecha, idanotacion";
        // $Sql="SELECT idanotacion, alumno, asignacion, fecha, anotacion FROM tb_anotaciones ORDER BY idanotacion";
	$result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result	
        $ii=0; // contador 
	while ($row=mysql_fetch_array($result)) {
		$anotaciones['idanotacion'][$ii]=$row['idanotacion'];
		// $anotaciones['fecha'][$ii]=$row['fecha'];
                // $fec = explode("-",$row['fecha']);               
                // $anotaciones['fecha'][$ii]=gmdate("l, j \d\\e F \d\\e o",mktime(12,0,0,$fec[1], $fec[2], $fec[0]));
                // $anotaciones['fecha'][$ii]=$fec[2]." de ".$meses[$fec[1]-1]. " del ".$fec[0] ;
                $anotaciones['fecha'][$ii]=$calendario->fechaformateadalarga($row['fecha']);
                // $anotaciones['asignacion'][$ii]=htmlentities($row['asignacion']);
                $infasignacion=obtenerdatosasignacion($bd,$row['asignacion']);
                $anotaciones['asignacion'][$ii]=iconv("ISO-8859-1","UTF-8",cambiarnombre($infasignacion["profesor"])." - ".$infasignacion["materia"]);
                $anotaciones['descripcion'][$ii]=iconv("ISO-8859-1","UTF-8",$infasignacion["descripcion"]);
                $anotaciones['asignacionnum'][$ii]=$row['asignacion'];
		$anotaciones['anotacion'][$ii]=htmlentities($row['anotacion']);
                $anotaciones['anotacion2'][$ii]=strip_tags($row['anotacion']);
                // echo $ii;
		$ii++;
		}
	mysql_free_result($result);

 
        // ordenar el array según el número de asignacion
        $muestra=array();
        foreach ($anotaciones['asignacionnum'] as $key => $num) {
	   $muestra[$num]['idanotacion'][]=$anotaciones['idanotacion'][$key];
           $muestra[$num]['fecha'][]=$anotaciones['fecha'][$key];
           $muestra[$num]['asignacion'][]=$anotaciones['asignacion'][$key];
           $muestra[$num]['descripcion'][]=$anotaciones['descripcion'][$key];
           $muestra[$num]['anotacion'][]=$anotaciones['anotacion'][$key];
           $muestra[$num]['anotacion2'][]=$anotaciones['anotacion2'][$key];
           $muestra[$num]['sql'][]=$Sql;
        }

        sort($muestra); // ordena
        
        $datos_json = json_encode($muestra); // 

        echo $datos_json;
      
 // echo $Sql;
?>
