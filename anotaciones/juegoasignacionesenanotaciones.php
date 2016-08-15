<?php
include_once("../funciones/seguridad/mysql_inc.php");
include_once("../funciones/adodb_time_inc.php");
include_once("../funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("../clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.        

        $anotaciones=array();        
        $link=Conectarse($bd); // y me conecto. //dependiendo del tipo recupero uno u otro.
	$Sql="SELECT DISTINCT asignacion FROM tb_anotaciones WHERE alumno='".$_POST["alumno"]."' ORDER BY asignacion";
	$result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result	
        $ii=0; // contador 
	while ($row=mysql_fetch_array($result)) {
                $infasignacion=obtenerdatosasignacion($bd,$row['asignacion']);
                $anotaciones['asignacion'][$ii]=iconv("ISO-8859-1","UTF-8",cambiarnombre($infasignacion["profesor"])." - ".$infasignacion["materia"]);
                $anotaciones['asignacionnum'][$ii]=$row['asignacion'];
                // echo $ii;
		$ii++;
		}
	mysql_free_result($result);

 
        // ordenar el array según el número de asignacion
        $muestra=array();
        foreach ($anotaciones['asignacionnum'] as $key => $num) {
           $muestra[$num]['asignacion'][]=$anotaciones['asignacion'][$key];
        }

        sort($muestra); // ordena
        
        $datos_json = json_encode($anotaciones); // 

        echo $datos_json;
      
// echo $Sql;
?>
