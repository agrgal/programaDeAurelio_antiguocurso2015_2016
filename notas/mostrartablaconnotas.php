<?php
include_once("../funciones/seguridad/mysql_inc.php");
include_once("../funciones/adodb_time_inc.php");
include_once("../funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("../clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

// *****************************************
// 1ª) Parte: obtiene los datos de las notas
// *****************************************
    
$link=Conectarse($bd); // y me conecto. //dependiendo del tipo recupero uno u otro.

$Sql= "SELECT tb_notas.IDnota, tb_notas.alumno, tb_notas.indicadores, tb_notas.ce, tb_notas.nota, tb_notas.modificadornota, tb_misconceptosevaluativos.IDcev, tb_misconceptosevaluativos.evaluacion, tb_misconceptosevaluativos.nombre, tb_misconceptosevaluativos.asignacion, tb_misconceptosevaluativos.peso, tb_misinstrumentosevaluativos.IDiev, tb_misconceptosevaluativos.iev, tb_misinstrumentosevaluativos.abreviatura, tb_misinstrumentosevaluativos.nombre AS nombreiev, tb_misinstrumentosevaluativos.porcentaje, tb_misinstrumentosevaluativos.notaminima FROM tb_notas INNER JOIN tb_misconceptosevaluativos ON tb_notas.ce = tb_misconceptosevaluativos.IDcev INNER JOIN tb_misinstrumentosevaluativos ON tb_misconceptosevaluativos.iev = tb_misinstrumentosevaluativos.IDiev WHERE tb_misconceptosevaluativos.evaluacion='".$_POST["evaluacion"]."' AND tb_misinstrumentosevaluativos.asignacion='".$_POST["asignacion"]."' ORDER BY tb_notas.alumno, tb_misconceptosevaluativos.nombre";
    
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
// 2º) Datos de los alumnos
// ************************
$alumno=obteneralumnosasignacion($bd,$_POST['asignacion']); // array para introducir datos de los alumnos
$numalumno=count($alumno['idalumno']);

// **************************************
// 3º) Datos de los conceptos evaluativos
// **************************************
$Sql= "SELECT tb_misconceptosevaluativos.IDcev, tb_misconceptosevaluativos.evaluacion, tb_misconceptosevaluativos.nombre, tb_misconceptosevaluativos.asignacion, tb_misconceptosevaluativos.peso, tb_misinstrumentosevaluativos.IDiev, tb_misconceptosevaluativos.iev, tb_misinstrumentosevaluativos.abreviatura, tb_misinstrumentosevaluativos.nombre AS nombreiev, tb_misinstrumentosevaluativos.porcentaje, tb_misinstrumentosevaluativos.notaminima FROM tb_misconceptosevaluativos INNER JOIN tb_misinstrumentosevaluativos ON tb_misconceptosevaluativos.iev = tb_misinstrumentosevaluativos.IDiev WHERE tb_misconceptosevaluativos.evaluacion='".$_POST["evaluacion"]."' AND tb_misinstrumentosevaluativos.asignacion='".$_POST["asignacion"]."' ORDER BY tb_misinstrumentosevaluativos.abreviatura, tb_misconceptosevaluativos.nombre";
    
     $result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result

     $ce=array(); //define el array de notas 

     $jj=0; // contador 
     while ($row=mysql_fetch_array($result)) {
        $ce['IDcev'][$jj]=$row['IDcev'];
        $ce['nombre'][$jj]=iconv("ISO-8859-1","UTF-8",trim($row['nombre']));
        $ce['peso'][$jj]=$row['peso'];
        $ce['iev'][$jj]=$row['iev'];
        $ce['abreviatura'][$jj]=$row['abreviatura'];
        $ce['nombreiev'][$jj]=$row['nombreiev'];
        $ce['porcentaje'][$jj]=$row['porcentaje'];
        $ce['notaminima'][$jj]=$row['notaminima'];
        // echo $ii;
	$jj++;
     }

     mysql_free_result($result);

// *****************************
// 4º) Instrumentos evaluativos
// *****************************
$Sql= "SELECT tb_misinstrumentosevaluativos.evaluacion, tb_misinstrumentosevaluativos.IDiev,  tb_misinstrumentosevaluativos.abreviatura, tb_misinstrumentosevaluativos.nombre AS nombreiev, tb_misinstrumentosevaluativos.porcentaje, tb_misinstrumentosevaluativos.notaminima FROM tb_misinstrumentosevaluativos WHERE tb_misinstrumentosevaluativos.evaluacion='".$_POST["evaluacion"]."' AND tb_misinstrumentosevaluativos.asignacion='".$_POST["asignacion"]."' ORDER BY tb_misinstrumentosevaluativos.abreviatura";
    
     $result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result

     $ie=array(); //define el array de notas 

     $zz=0; // contador 
     while ($row=mysql_fetch_array($result)) {
        $ie['IDiev'][$zz]=$row['IDiev'];
        $ie['nombreiev'][$zz]=iconv("ISO-8859-1","UTF-8",trim($row['nombreiev']));
        $ie['abreviatura'][$zz]=$row['abreviatura'];
        $ie['porcentaje'][$zz]=$row['porcentaje'];
        $ie['notaminima'][$zz]=$row['notaminima'];
        $ie['sumapesos'][$zz]=0; // para que se sumen los pesos
        // echo $ii;
	$zz++;
     }

     mysql_free_result($result);

// ************************
// 5º) Construye la tabla
// ************************

// **************************
// CABECERA
// **************************
$ancho=round(60/($jj+$zz+1));
$tabladatos='<table style="display: auto; margin:2px auto; height: auto; text-align: center; width: 90%; table-layout: fixed;" border="1" cellpadding="1" cellspacing="1" class="tabla"><thead><tr>';
// fila con el nombre de los conceptos evaluativos
$tabladatos.='<th width="40%"  valign="middle" style="text-align: center;style="text-align: center;">Conceptos Evaluativos</th>';
foreach ($ce['IDcev'] as $key =>$valor) {
     $tabladatos.='<th width="'.$ancho.'%" valign="middle" style="text-align: center; word-wrap:break-word;">'.$ce['nombre'][$key].'</th>';
     // $tabladatos.='<th style="-webkit-transform: rotate(90deg); -moz-transform: rotate(90deg);
    // -ms-transform: rotate(90deg);-o-transform: rotate(90deg); transform: rotate(90deg);">'.$ce['nombre']   
     // [$key].'</th>';
}
foreach ($ie['IDiev'] as $key =>$valor) {
     $tabladatos.='<th width="'.$ancho.'%" valign="middle" style="text-align: center; word-wrap:break-word;">'.$ie['nombreiev'][$key].'</th>';
}
// Y la celda total
$tabladatos.='<th rowspan="5" width="'.$ancho.'%" valign="middle" style="text-align: center; word-wrap:break-word;">TOTAL</th>';
$tabladatos.='</tr>'; // Filas con los nombres de los conceptos evaluativos
// filas con los pesos
$tabladatos.='<tr>';
$tabladatos.='<th style="text-align: center;  valign="middle" style="text-align: center;">Pesos</th>';
foreach ($ce['IDcev'] as $key =>$valor) {
     $tabladatos.='<th valign="middle" style="text-align: center;">'.$ce['peso'][$key].'</th>';
}
//********************************
// Suma de los pesos
//********************************
foreach ($ie['IDiev'] as $key =>$valor) {
     $clavesiev=array_keys($ce["iev"],$valor); //subconjunto de claves de ce con el mismo valor de IDiev
     foreach ($clavesiev as $valorclave) {
	$ie['sumapesos'][$key]+=$ce["peso"][$valorclave]; // suma los pesos y los pone en la variable
     }     
     $tabladatos.='<th width="'.$ancho.'%" valign="middle" style="text-align: center;">'.$ie['sumapesos'][$key].'</th>';
}
// *************************************************************************
$tabladatos.='</tr>';
// filas con la abreviatura de los IEV
$tabladatos.='<tr>';
$tabladatos.='<th valign="middle" style="text-align: center;">Abreviatura</th>';
foreach ($ce['IDcev'] as $key =>$valor) {
     $tabladatos.='<th style="text-align: center; valign="middle" style="text-align: center;">'.$ce['abreviatura'][$key].'</th>';
}
foreach ($ie['IDiev'] as $key =>$valor) {
     $tabladatos.='<th width="'.$ancho.'%" valign="middle" style="text-align: center;">'.$ie['abreviatura'][$key].'</th>';
}
$tabladatos.='</tr>';
// filas con el porcentaje 
$tabladatos.='<tr>';
$tabladatos.='<th valign="middle" style="text-align: center;">Porcentaje</th>';
foreach ($ce['IDcev'] as $key =>$valor) {
     $tabladatos.='<th  valign="middle" style="text-align: center;">'.$ce['porcentaje'][$key].'</th>';
}
foreach ($ie['IDiev'] as $key =>$valor) {
     $tabladatos.='<th width="'.$ancho.'%" valign="middle" style="text-align: center;">'.$ie['porcentaje'][$key].'</th>';
}
$tabladatos.='</tr>';
// filas con la nota mínima
$tabladatos.='<tr>';
$tabladatos.='<th valign="middle" style="text-align: center;">Nota mínima</th>';
foreach ($ce['IDcev'] as $key =>$valor) {
     $tabladatos.='<th valign="middle" style="text-align: center;">'.$ce['notaminima'][$key].'</th>';
}
foreach ($ie['IDiev'] as $key =>$valor) {
     $tabladatos.='<th width="'.$ancho.'%" valign="middle" style="text-align: center;">'.$ie['notaminima'][$key].'</th>';
}
$tabladatos.='</tr>';
$tabladatos.='</thead>';
// **************************
// cuerpo
// **************************
$tabladatos.='<tbody>';
// por cada alumno, una fila nueva
foreach ($alumno["idalumno"] as $key => $valor) {
	$tabladatos.='<tr>'; // principio de la fila
        $tabladatos.='<td style="text-align: left; text-indent: 1em; ">'.iconv("ISO-8859-1","UTF-8",cambiarnombre($alumno["alumno"][$key])).'</td>'; 
        // Este script busca y encuentra las notas de cada alumno
        // Se basa en, por cada id de concepto evaluativo, buscar en NOTAS lo que los tienen.
        // También busca en NOTAS las que pertenecen a un alumno
        // Intercepta después las claves de un subconjunto y de otro, y así obtiene 
        // la clave del array nota que pertenece a ese concepto evaluativo y alumno
        $sumanotas=array(); // suma de las notas
        foreach ($ce['IDcev'] as $key2 =>$valor2) { // por cada concepto evaluativo
	    $clavesidcev=array_keys($nota["IDcev"],$valor2); // array de claves en notas con ese valor de cev
            $clavesidalumno=array_keys($nota["idalumno"],$valor); // claves en notas con ese valor de idalumno
            // interseccion
            $claveintersectada=array_intersect($clavesidcev, $clavesidalumno);
            reset($claveintersectada);
            // comprueba que es un sólo elemento
            if (count($claveintersectada)==1) { // Hay un sólo elemento
               // Las notas por su peso, la va sumando y la clave es el número de instrumento evaluativo
               $sumanotas[$ce["iev"][$key2]]+=$nota["nota"][current($claveintersectada)]*$ce["peso"][$key2];
               // coloca la nota en el lugar de la tabla.
               $ponnota = number_format($nota["nota"][current($claveintersectada)],1,',','');
	       $tabladatos.='<td style="text-align: center;">'.$ponnota.'</td>';
            } else if (count($claveintersectada)>1) {
		$tabladatos.='<td style="text-align: center;">Error</td>'; // caso de haber más de uno
            } else {
		$tabladatos.='<td style="text-align: center;">?</td>'; // No hay notas.
            }
            unset($clavesidcev); unset($clavesidalumno); unset($claveintersectada);
        }
        // Ahora, intentamos que se calcule la nota de cada uno
        // Con cada peso...
        $calculo=array();
        foreach ($ie['IDiev'] as $key5 =>$valor5) {
                 if ($ie['sumapesos'][$key5]>0) {
		    $calculo[$key5] = $sumanotas[$valor5]/$ie['sumapesos'][$key5];
                 } else {
		    $calculo[$key5] = $sumanotas[$valor5]/1000;
                 }
		 $tabladatos.='<td style="text-align: center;">'.number_format($calculo[$key5],2,',','').'</td>';
        }
        unset($sumanotas);
        // Y ahora calculamos el TOTAL... Cuidado, tengo que poner el porcentaje...
        $total=0;
        foreach ($ie['IDiev'] as $key6 =>$valor6) {
                $por = (float) ($ie['porcentaje'][$key6]/100);
                $cal = (float) $calculo[$key6];
		$total+=$por*$cal;
        }
	$tabladatos.='<td style="text-align: center;">'.number_format($total,2,',','').'</td>';
        unset($calculo);
        $tabladatos.='</tr>'; // fin de la fila
} // fin del foreach del alumno...
$tabladatos.='</tbody>';
$tabladatos.='</table>';


echo $tabladatos; // devuelve la tabla...

// ************************
// 5º) Devuelve los datos
// ************************

     /* $datos_json=array(); // define el array que va a devolver
     
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

      echo "{".implode(",", $datos_json)."}"; */

      // echo $nota['IDnota'][0];
?>
