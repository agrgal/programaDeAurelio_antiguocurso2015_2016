<?php
include_once("../funciones/seguridad/mysql_inc.php");
include_once("../funciones/adodb_time_inc.php");
include_once("../funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("../clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

// ****************************
// 1ª) Parte: obtiene los datos
// ****************************
    
$link=Conectarse($bd); // y me conecto. //dependiendo del tipo recupero uno u otro.

$Sql= "SELECT tb_notas.IDnota, tb_notas.alumno, tb_notas.indicadores, tb_notas.ce, tb_notas.nota, tb_notas.modificadornota, tb_misconceptosevaluativos.IDcev, tb_misconceptosevaluativos.evaluacion, tb_misconceptosevaluativos.nombre, tb_misconceptosevaluativos.asignacion, tb_misconceptosevaluativos.peso, tb_misinstrumentosevaluativos.IDiev, tb_misconceptosevaluativos.iev, tb_misinstrumentosevaluativos.abreviatura, tb_misinstrumentosevaluativos.nombre AS nombreiev, tb_misinstrumentosevaluativos.porcentaje, tb_misinstrumentosevaluativos.notaminima FROM tb_notas INNER JOIN tb_misconceptosevaluativos ON tb_notas.ce = tb_misconceptosevaluativos.IDcev INNER JOIN tb_misinstrumentosevaluativos ON tb_misconceptosevaluativos.iev = tb_misinstrumentosevaluativos.IDiev WHERE tb_misconceptosevaluativos.evaluacion='".$_POST["evaluacion"]."' AND tb_misinstrumentosevaluativos.asignacion='".$_POST["asignacion"]."' ORDER BY tb_notas.alumno, tb_misconceptosevaluativos.nombre";

/* $Sql= "SELECT tb_notas.IDnota, tb_notas.alumno, tb_notas.indicadores, tb_notas.ce, tb_notas.nota, tb_notas.modificadornota, tb_misconceptosevaluativos.IDcev, tb_misconceptosevaluativos.evaluacion, tb_misconceptosevaluativos.descripcion, tb_misconceptosevaluativos.asignacion, tb_misconceptosevaluativos.peso, tb_misinstrumentosevaluativos.IDiev, tb_misconceptosevaluativos.iev, tb_misinstrumentosevaluativos.abreviatura, tb_misinstrumentosevaluativos.nombre AS nombreiev, tb_misinstrumentosevaluativos.porcentaje, tb_misinstrumentosevaluativos.notaminima FROM tb_notas INNER JOIN tb_misconceptosevaluativos ON tb_notas.ce = tb_misconceptosevaluativos.IDcev INNER JOIN tb_misinstrumentosevaluativos ON tb_misconceptosevaluativos.iev = tb_misinstrumentosevaluativos.IDiev"; */
    
     $result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result

     $nota=array(); //define el array de notas 

     $ii=0; // contador 
     while ($row=mysql_fetch_array($result)) {
        $nota['IDnota'][$ii]=$row['IDnota'];
	$nota['nota'][$ii]=$row['nota'];
 	$nota['idalumno'][$ii]=$row['alumno']; 
  	$nota['alumno'][$ii]=iconv("ISO-8859-1","UTF-8",dado_Id($bd,$row['alumno'],"alumno","tb_alumno","idalumno"));
        $nota['modificadornota'][$ii]=$row['modificadornota'];
        $nota['indicadores'][$ii]=$row['indicadores'];
        $nota['IDcev'][$ii]=$row['IDcev'];
        $nota['nombre'][$ii]=iconv("ISO-8859-1","UTF-8",trim($row['nombre']));
        $nota['peso'][$ii]=$row['peso'];
        $nota['iev'][$ii]=$row['iev'];
        $nota['abreviatura'][$ii]=$row['abreviatura'];
        $nota['nombreiev'][$ii]=$row['nombreiev'];
        $nota['porcentaje'][$ii]=$row['porcentaje'];
        $nota['notaminima'][$ii]=$row['notaminima'];
        // echo $ii;
	$ii++;
     }

     mysql_free_result($result);

// ************************
// 3º) Devuelve los datos
// ************************

     $datos_json=array(); // define el array que va a devolver
     
     foreach ($nota['IDnota'] as $key => $valor) {
            $cadena='"'.$valor.'":{"nota":"'.$nota['nota'][$key].'",';
            $cadena.='"mod":"'.$nota['modificadornota'][$key].'",';
            $cadena.='"IDcev":"'.$nota['IDcev'][$key].'",';
            $cadena.='"nombre":"'.$nota['nombre'][$key].'",';
            $cadena.='"idalumno":"'.$nota['idalumno'][$key].'",';
            $cadena.='"alumno":"'.$nota['alumno'][$key].'",';
            $cadena.='"peso":"'.$nota['peso'][$key].'",';
            $cadena.='"abreviatura":"'.$nota['abreviatura'][$key].'",';
            $cadena.='"indicadores":"'.$nota['indicadores'][$key].'",';
            $cadena.='"porcentaje":"'.$nota['porcentaje'][$key].'",';
            $cadena.='"notaminima":"'.$nota['notaminima'][$key].'",';
 	    $cadena = substr($cadena,0,strlen($cadena)-1); // quitar la como y las comillas
	    $datos_json[]=$cadena."}";
      }

      echo "{".implode(",", $datos_json)."}";

      // echo $nota['IDnota'][0];

      // echo $Sql;

?>
