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

        $conceptosevaluativos=array();        

        // $Sql="SELECT IDiev, nombre, abreviatura, porcentaje, notaminima, asignacion, evaluacion FROM tb_misinstrumentosevaluativos WHERE asignacion='".$_POST["asignacion"]."' AND evaluacion='".$_POST["evaluacion"]."' ORDER BY nombre";

	$Sql="SELECT tb_misconceptosevaluativos.IDcev, tb_misconceptosevaluativos.nombre AS nombrece, tb_misconceptosevaluativos.peso, tb_misconceptosevaluativos.iev, tb_misconceptosevaluativos.fechainipre, tb_misconceptosevaluativos.fechafinpre, tb_misconceptosevaluativos.fechainireal, tb_misconceptosevaluativos.fechafinreal, tb_misconceptosevaluativos.asignacion, tb_misconceptosevaluativos.evaluacion, tb_misconceptosevaluativos.descripcion, tb_misconceptosevaluativos.indicadores, tb_misinstrumentosevaluativos.IDiev, tb_misinstrumentosevaluativos.nombre AS nombreie, tb_misinstrumentosevaluativos.abreviatura, tb_misinstrumentosevaluativos.porcentaje, tb_misinstrumentosevaluativos.notaminima FROM tb_misconceptosevaluativos INNER JOIN tb_misinstrumentosevaluativos ON tb_misconceptosevaluativos.iev = tb_misinstrumentosevaluativos.IDiev WHERE tb_misconceptosevaluativos.asignacion='".$_POST["asignacion"]."' AND tb_misconceptosevaluativos.evaluacion='".$_POST["evaluacion"]."' ORDER BY nombrece";
  

        $link=Conectarse($bd); // y me conecto. //dependiendo del tipo recupero uno u otro.      
	$result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result
	$ii=0; // contador 
	while ($row=mysql_fetch_array($result)) {
		$conceptosevaluativos['idcev'][$ii]=$row['IDcev'];
		// $anotaciones['fecha'][$ii]=$row['fecha'];
                // $fec = explode("-",$row['fecha']);
                // $instrumentosevaluativos['fecha'][$ii]=gmdate("d/m/Y",mktime(12,0,0,$fec[1], $fec[2], $fec[0]));
                // Mirar htmlentities o strip_tags.
                $conceptosevaluativos['nombreie'][$ii]=iconv("ISO-8859-1","UTF-8",$row['nombreie'])." [porcentaje: ".round($row['porcentaje'],2)."% - Nota mínima: ".round($row['notaminima'],2)."]";
                $conceptosevaluativos['abreviatura'][$ii]=iconv("ISO-8859-1","UTF-8",$row['abreviatura']);
		$conceptosevaluativos['iev'][$ii]=$row['iev'];
		$conceptosevaluativos['nombre'][$ii]=iconv("ISO-8859-1","UTF-8",$row['nombrece']);
                $conceptosevaluativos['peso'][$ii]=round($row['peso'],1);
		$conceptosevaluativos['asignacion'][$ii]=$row['asignacion'];
                $conceptosevaluativos['evaluacion'][$ii]=$row['evaluacion'];
		$conceptosevaluativos['descripcion'][$ii]=iconv("ISO-8859-1","UTF-8",strip_tags($row['descripcion'])); 
		$conceptosevaluativos['indicadores'][$ii]=$row['indicadores']; 
               
                // ponemos las fechas
                $fec = explode("-",$row['fechainipre']);
                $conceptosevaluativos['fechainipre'][$ii]=gmdate("d/m/Y",mktime(12,0,0,$fec[1], $fec[2], $fec[0]));
                $fec = explode("-",$row['fechainireal']);
                $conceptosevaluativos['fechainireal'][$ii]=gmdate("d/m/Y",mktime(12,0,0,$fec[1], $fec[2], $fec[0]));
                $fec = explode("-",$row['fechafinpre']);
                $conceptosevaluativos['fechafinpre'][$ii]=gmdate("d/m/Y",mktime(12,0,0,$fec[1], $fec[2], $fec[0]));
                $fec = explode("-",$row['fechafinreal']);
                $conceptosevaluativos['fechafinreal'][$ii]=gmdate("d/m/Y",mktime(12,0,0,$fec[1], $fec[2], $fec[0]));

                // echo $ii;
		$ii++;
		}
	mysql_free_result($result);

      foreach ($conceptosevaluativos['idcev'] as $key => $valor) {
            $cadena = "";
            // $datos_json[]='"idanotacion":"'.$valor.'"';
	    // $datos_json[]='"fecha":"'.$anotaciones['fecha'][$key].'"';
            // $datos_json[]='"anotacion":"'.$anotaciones['anotacion'][$key].'"';
            $cadena='"'.$valor.'":{"nombre":"'.$conceptosevaluativos['nombre'][$key].'",';
            $cadena.='"abreviatura":"'.$conceptosevaluativos['abreviatura'][$key].'",';
            $cadena.='"nombreie":"'.$conceptosevaluativos['nombreie'][$key].'",';
            $cadena.='"iev":"'.$conceptosevaluativos['iev'][$key].'",';
            $cadena.='"peso":"'.$conceptosevaluativos['peso'][$key].'",';
            $cadena.='"asignacion":"'.$conceptosevaluativos['asignacion'][$key].'",';
            $cadena.='"evaluacion":"'.$conceptosevaluativos['evaluacion'][$key].'",';
            $cadena.='"descripcion":"'.$conceptosevaluativos['descripcion'][$key].'",';
            $cadena.='"indicadores":"'.$conceptosevaluativos['indicadores'][$key].'",';
            $cadena.='"fechainipre":"'.$conceptosevaluativos['fechainipre'][$key].'",';
            $cadena.='"fechainireal":"'.$conceptosevaluativos['fechainireal'][$key].'",';
            $cadena.='"fechafinpre":"'.$conceptosevaluativos['fechafinpre'][$key].'",';
            $cadena.='"fechafinreal":"'.$conceptosevaluativos['fechafinreal'][$key].'",';
 	    $cadena = substr($cadena,0,strlen($cadena)-1); // quitar la como y las comillas
	    $datos_json[]=$cadena.'}';
      }

      echo "{".implode(",", $datos_json)."}"; 

      // echo $Sql;
?>
