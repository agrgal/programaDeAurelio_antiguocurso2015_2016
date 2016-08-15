<?php
include_once("../funciones/seguridad/mysql_inc.php");
include_once("../funciones/adodb_time_inc.php");
include_once("../funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("../clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

	/* Ver lo de las fechas
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
        } */

	session_start(); /* empiezo una sesión */
        $_SESSION['listaevaluacion']=$_POST["evaluacion"]; // Asigno la variable de sesión siguiente

        $instrumentosevaluativos=array();        
        $link=Conectarse($bd); // y me conecto. //dependiendo del tipo recupero uno u otro.
	$Sql="SELECT IDiev, nombre, abreviatura, porcentaje, notaminima, asignacion, evaluacion FROM tb_misinstrumentosevaluativos WHERE asignacion='".$_POST["asignacion"]."' AND evaluacion='".$_POST["evaluacion"]."' ORDER BY nombre";        
        
	// $Sql="SELECT idanotacion, alumno, asignacion, fecha, anotacion FROM tb_anotaciones WHERE asignacion='".$_POST["asignacion"]."' AND alumno='".$_POST["alumno"]."' AND fecha>='".$fecha1."' AND fecha<='".$fecha2."' ORDER BY fecha, idanotacion";
        // $Sql="SELECT idanotacion, alumno, asignacion, fecha, anotacion FROM tb_anotaciones ORDER BY idanotacion";
	$result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result
	$ii=0; // contador 
	while ($row=mysql_fetch_array($result)) {
		$instrumentosevaluativos['IDiev'][$ii]=$row['IDiev'];
		// $anotaciones['fecha'][$ii]=$row['fecha'];
                // $fec = explode("-",$row['fecha']);
                // $instrumentosevaluativos['fecha'][$ii]=gmdate("d/m/Y",mktime(12,0,0,$fec[1], $fec[2], $fec[0]));
                // Mirar htmlentities o strip_tags.
                $instrumentosevaluativos['nombre'][$ii]=iconv("ISO-8859-1","UTF-8",$row['nombre']);
                $instrumentosevaluativos['abreviatura'][$ii]=iconv("ISO-8859-1","UTF-8",$row['abreviatura']);
		$instrumentosevaluativos['porcentaje'][$ii]=round($row['porcentaje'],2);
                $instrumentosevaluativos['notaminima'][$ii]=round($row['notaminima'],2);
                // echo $ii;
		$ii++;
		}
	mysql_free_result($result);

      foreach ($instrumentosevaluativos['IDiev'] as $key => $valor) {
            // $datos_json[]='"idanotacion":"'.$valor.'"';
	    // $datos_json[]='"fecha":"'.$anotaciones['fecha'][$key].'"';
            // $datos_json[]='"anotacion":"'.$anotaciones['anotacion'][$key].'"';
            $datos_json[]='"'.$valor.'":{"nombre":"'.$instrumentosevaluativos['nombre'][$key].'","abreviatura":"'.$instrumentosevaluativos['abreviatura'][$key].'","porcentaje":"'.$instrumentosevaluativos['porcentaje'][$key].'","notaminima":"'.$instrumentosevaluativos['notaminima'][$key].'"}';
            // $datos_json[]='"'.$valor.'":{"fecha":"'.$anotaciones['fecha'][$key].'","anotacion2":"'.$anotaciones['anotacion2'][$key].'"}';
      }
      echo "{".implode(",", $datos_json)."}"; 

      // echo $Sql;
?>
