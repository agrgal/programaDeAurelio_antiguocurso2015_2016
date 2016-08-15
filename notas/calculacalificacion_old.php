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

//**************************************************************************************
// Cambiado tb_misconceptosevaluativos.descripcion por tb_misconceptosevaluativos.nombre
// También en $notas[""]
//**************************************************************************************

$Sql= "SELECT tb_notas.IDnota, tb_notas.alumno, tb_notas.indicadores, tb_notas.ce, tb_notas.nota, tb_notas.modificadornota, tb_misconceptosevaluativos.IDcev, tb_misconceptosevaluativos.evaluacion, tb_misconceptosevaluativos.nombre, 
tb_misconceptosevaluativos.peso, tb_misinstrumentosevaluativos.IDiev, tb_misconceptosevaluativos.iev, tb_misinstrumentosevaluativos.abreviatura, tb_misinstrumentosevaluativos.nombre AS nombreiev,
tb_misinstrumentosevaluativos.porcentaje, tb_misinstrumentosevaluativos.notaminima FROM tb_notas INNER JOIN tb_misconceptosevaluativos ON tb_notas.ce = tb_misconceptosevaluativos.IDcev INNER JOIN tb_misinstrumentosevaluativos ON tb_misconceptosevaluativos.iev = tb_misinstrumentosevaluativos.IDiev WHERE tb_misconceptosevaluativos.evaluacion='".$_POST["evaluacion"]."' AND tb_misconceptosevaluativos.asignacion='".$_POST["asignacion"]."' AND tb_notas.alumno='".$_POST["alumno"]."'"; // incluir filtro asignacion

/* $Sql= "SELECT tb_notas.IDnota, tb_notas.alumno, tb_notas.indicadores, tb_notas.ce, tb_notas.nota, tb_notas.modificadornota, tb_misconceptosevaluativos.IDcev, tb_misconceptosevaluativos.evaluacion, tb_misconceptosevaluativos.descripcion, 
tb_misconceptosevaluativos.peso, tb_misinstrumentosevaluativos.IDiev, tb_misconceptosevaluativos.iev, tb_misinstrumentosevaluativos.abreviatura, tb_misinstrumentosevaluativos.nombre AS nombreiev,
tb_misinstrumentosevaluativos.porcentaje, tb_misinstrumentosevaluativos.notaminima FROM tb_notas INNER JOIN tb_misconceptosevaluativos ON tb_notas.ce = tb_misconceptosevaluativos.IDcev INNER JOIN tb_misinstrumentosevaluativos ON tb_misconceptosevaluativos.iev = tb_misinstrumentosevaluativos.IDiev WHERE tb_misconceptosevaluativos.evaluacion='".$_POST["evaluacion"]."' AND tb_notas.alumno='".$_POST["alumno"]."'"; */
    
     $result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result
     $nota=array(); //define el array de notas 

     $ii=0; // contador 
     while ($row=mysql_fetch_array($result)) {
        $nota['IDnota'][$ii]=$row['IDnota'];
	$nota['nota'][$ii]=$row['nota'];
        $nota['modificadornota'][$ii]=$row['modificadornota'];
        $nota['indicadores'][$ii]=$row['indicadores'];
        $nota['nombre'][$ii]=strip_tags($row['nombre']);
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
// 2º) Realiza los cálculos
// ************************

   // 2A) Obtiene los distintos valores de abreviatura, porcentaje y notaminima
   $iev = array_unique($nota['iev']); $ponnota=array();
   $abreviaturas=array(); $porcentaje=array(); $notaminima=array(); $sumanotas=array(); $pesonotas=array(); 
   $nombreiev=array();
   // $abreviaturas=$nota['abreviatura'];
   foreach ($iev as $key => $valor) {
      $abreviaturas[]=$nota['abreviatura'][$key];
      $nombreiev[]=$nota['nombreiev'][$key];
      $porcentaje[]=$nota['porcentaje'][$key];
      $notaminima[]=$nota['notaminima'][$key];
      $sumanotas[]=0; // inicializa este array. Suma las notas por cada IEV
      $pesonotas[]=0; // inicializa este array. Suma los pesos por cada IEV
      $ponnota[]=0; // La nota
   } 
   array_values($abreviaturas); // claves en números correlativas
   array_values($nombreiev); // claves en números correlativas
   array_values($porcentaje);
   array_values($notaminima);
   array_values($iev); // clave por la que ordenar...
   array_values($sumanotas);
   array_values($pesonotas);
   array_values($ponnota);

   // 2B) Bucle que suma las notas y los pesos
   foreach ($nota['IDnota'] as $key => $valor) { // Bucle que suma 
       $clave = array_search($nota['iev'][$key],$iev); // clave de la cadena de instrumentos evaluativos
       // CÁLCULOS SEGÚN el MODIFICADOR de la nota.
       if ($nota['modificadornota'][$key]=="N" && $nota['peso'][$key]>0) { // nota NORMAL.
            $sumanotas[$clave]+=$nota['nota'][$key]*$nota['peso'][$key]; 
            // añade la nota a la suma multiplicada por el peso...
            $pesonotas[$clave]+=$nota['peso'][$key]; // añade la nota a la suma
       // Si tenemos una FALTA JUSTIFICADA o POR ENFERMEDAD, le pongo al menos un 6
       } if (($nota['modificadornota'][$key]=="FJ" || $nota['modificadornota'][$key]=="E" ) && $nota['peso'][$key]>0) {
            $sumanotas[$clave]+=6*$nota['peso'][$key]; // añade la nota a la suma multiplicada por el peso...
            $pesonotas[$clave]+=$nota['peso'][$key]; // añade la nota a la suma
       // Si la falta es INJUSTIFICADA, le ponemos al menos un 2
       } if ($nota['modificadornota'][$key]=="FI" && $nota['peso'][$key]>0) {
            $sumanotas[$clave]+=2*$nota['peso'][$key]; // añade la nota a la suma multiplicada por el peso...
            $pesonotas[$clave]+=$nota['peso'][$key]; // añade la nota a la suma       
       } // fin del IF  
       // En cualquier otro caso, si es indeterminada, o si el peso es cero, no ejerce ningún cambio        
   } // fin del foreach

   // 2C) Calcula la NOTA total...
   $notatotal=0; $nohaynotas=0;
   foreach ($pesonotas as $key => $valor) { // por cada peso
       if ($valor>0) { 
           $ponnota[$key]=($sumanotas[$key]/$valor)*($porcentaje[$key]/100); 
           $notatotal+=$ponnota[$key];
       }
       if ($valor==0) { $ponnota[$key]=0; } // Si no hay peso, le pone un cero..
       $nohaynotas+=$ponnota[$key]+$valor; // suma de notas y pesos. Si es cero es que no tiene notas.
   }

   // 2Cbis) Calcula un muestra de la dispersión en la nota
   // si se me ocurre una manera de ponerlo, porque no le veo mucho sentido. Tener en cuenta que se le aplican porcentajes...

   // $datos_json[]='"notatotal_previo":"'. $notatotal.'"'; // Enviar la nota total datos.notatotal

   // 2D) Tener en cuenta la nota mínima
   foreach ($notaminima as $key => $valor) { // por cada notaminima
       if ($valor>($ponnota[$key]/($porcentaje[$key]/100)) && $nohaynotas>0) { // si no supera la nota minima de cada apartado, suspendería
           $notatotal=min($notatotal, 3); // se le pone un 3 como máximo...
       }
   }

   if ($nohaynotas==0) { $notatotal="?"; } else { $notatotal=round($notatotal,2); }// si no hay notas

   // 2E) Mostrar datos

   $datos_json[]='"notatotal":"'. $notatotal.'"'; // Enviar la nota total datos.notatotal
   $datos_json[]='"nohaynotas":"'.$nohaynotas.'"'; // Enviar la nota total datos.notatotal
   
   $cadena='"poriev":{';
   if (count($abreviaturas)>0) {
   foreach ($abreviaturas as $key => $valor) {
            // no supera la nota mínima
            $cadena.='"'.$key.'":{"abreviatura":"'.$valor.'",';
            $cadena.='"nombreiev":"'.iconv("ISO-8859-1","UTF-8",$nombreiev[$key]).'",';
            $cadena.='"por":"'.round($porcentaje[$key],2).'",';
            $cadena.='"nm":"'.$notaminima[$key].'",';
            $cadena.='"notaiev":"'.round($sumanotas[$key],2).'",';
            $cadena.='"pesoiev":"'.round($pesonotas[$key],2).'",';
            $cadena.='"nota":"'.round($ponnota[$key],2).'",';
 	    $cadena = substr($cadena,0,strlen($cadena)-1); // quitar la coma y las comillas
	    $cadena.='},';
   } // fin del foreach
   } else {
     $cadena.='"0":{"nombreiev":"'.$valor.'"},'; // manda el dato 0 vacío...
   }
   $cadena = substr($cadena,0,strlen($cadena)-1); // quitar la coma y las comillas
   $datos_json[]=$cadena.'}'; // datos.poriev[i].abreviatura

   // 2F) Indicadores: Une en una ristra los ID de indicadores con sus notas de 0 a 3
   $cadena='';   
   foreach ($nota['indicadores'] as $key => $valor) {
         if (!empty($valor) && strpos($valor, "*")>0 ) {$cadena.=$valor."-"; } // Importante, si no es nulo y tiene algún asterisco...
   }
   $cadena = substr($cadena,0,strlen($cadena)-1); // quitar la coma y las comillas

   $datos_json[]='"cadenaindicadores":"'.$cadena.'"'; // Enviar la nota total datos.notatotal
   
   // 2G) Convierto la cadena de INDICADORES  en cadena de COMPETENCIAS con nota media.
   $competencia=array(); 
   $parejas = explode("-",$cadena);
   foreach ($parejas as $key => $valor) {
	$separar = explode("*",$valor);
        $competencia["id"][]=dado_Id($bd,$separar[0],"competencia","tb_misindicadores","idindicador");
        $competencia["nota"][]=$separar[1];
   }

   // 2H) Valores de competencia NO repetidos
   $listacompetencias=array();
   $listacompetencias["id"] = array_unique($competencia["id"]); 
   // Por cada valor de esta lista, declara la suma, como el valor cuando lo encuentra y cuenta como el número de valores
   foreach ($listacompetencias["id"] as $key => $valor) {
       $claves = array_keys($competencia["id"], $valor); // encuentra las claves que tienen ese valor
       $suma=0; // inicializa la suma
       foreach ($claves as $cadaclave) {
           $suma+=$competencia["nota"][$cadaclave]; // Suma todo los valores
       }
       $listacompetencias["media"][$key]=$suma/count($claves);
   }

   // 2I) Añade a COMPETENCIAS
   $cadena='"COMPETENCIAS":{';
   // hace el if si es mayor que cero y si no es nulo el primer dato...
   if (count($listacompetencias["id"])>=1 && !is_null($listacompetencias["id"][0])) {
   foreach ($listacompetencias["id"] as $key => $valor) {
        if (!is_null($valor)) {        
        $cadena.='"'.$key.'":{"id":"'.$valor.'",';
        $cadena.='"descripcion":"'.iconv("ISO-8859-1","UTF-8",dado_Id($bd,$valor,"nombre","tb_listacompetencias","idcompetencia")).'",';
        $cadena.='"abreviatura":"'.trim(iconv("ISO-8859-1","UTF-8",dado_Id($bd,$valor,"abreviatura","tb_listacompetencias","idcompetencia"))).'",';
        $cadena.='"nota1":"'.round($listacompetencias["media"][$key],1).'",';
        $cadena.='"nota2":"'.retornacalificacion(round($listacompetencias["media"][$key],1)).'",';
        $cadena = substr($cadena,0,strlen($cadena)-1); // quitar la coma y las comillas       
        $cadena.='},';
        } // fin del if isnull
   } // Fin del foreach
   } else {
     $cadena.='"0":{"descripcion":""},'; // manda el dato 0 vacío...
   }
   $cadena = substr($cadena,0,strlen($cadena)-1); // quitar la coma y las comillas 
   $datos_json[]=$cadena.'}'; // datos.poriev[i].abreviatura

   

   // ******************* POR AQUI ***************************+
   // ******************* POR AQUI ***************************+

   // 2J) Comprueba si existe calificación, y, si lo hace retorna el valor de la tabla; si no, la incluye
   $idcalificacion = checkcalificacion($bd,$_POST["asignacion"],$_POST["alumno"],$_POST["evaluacion"]);
   
   if(!empty($idcalificacion) && !is_null($idcalificacion)) {
     $calificacion = dado_Id($bd,$idcalificacion,"notaseneca","tb_miscalificaciones","idcalificacion");
     // UPDATE de la nota media, pero de nada más, sólo la media
     $Sql="UPDATE tb_miscalificaciones SET ";
     $Sql.="notamedia='".$notatotal."', ";	
     $Sql= substr($Sql,0,strlen($Sql)-2); 
     $Sql.=" WHERE idcalificacion='".trim($idcalificacion)."'"; // fin de la cadena SQL del UPDATE
     $link=Conectarse($bd);
     $result=mysql_query($Sql,$link); //ejecuta la consulta
     mysql_free_result($result); // guarda una calificación por defecto
     // Y envía la nota séneca que tiene guardada...
     $datos_json[]='"notaseneca":"'.$calificacion.'"'; // Enviar la nota total datos.notatotal
   } else {
     $Sql="INSERT INTO tb_miscalificaciones (alumno, evaluacion, asignacion, notamedia, notaseneca) VALUES (";
     if ($_POST["alumno"]<>'') {$Sql.="'".$_POST["alumno"]."', ";} else {$Sql.="'-',";}
     if ($_POST["evaluacion"]<>'') {$Sql.="'".$_POST["evaluacion"]."', ";} else {$Sql.="'-',";}
     if ($_POST["asignacion"]<>'') {$Sql.="'".$_POST["asignacion"]."', ";} else {$Sql.="'-',";}
     if ($notatotal<>'') {$Sql.="'".$notatotal."', ";} else {$Sql.="'-',";}
     if ($notatotal<>'') {$Sql.="'".$notatotal."', ";} else {$Sql.="'-',";} // ambas notas son las mismas
     $Sql=substr($Sql,0,strlen($Sql)-2); 
     $Sql.=")"; 
     $link=Conectarse($bd);
     $result=mysql_query($Sql,$link); //ejecuta la consulta
     mysql_free_result($result); // guarda una calificación por defecto
     $datos_json[]='"notaseneca":"'.$notatotal.'"';	
   }

   // $datos_json[]='"notaseneca":"5.5"';

// ************************
// 3º) Devuelve los datos
// ************************
     
     foreach ($nota['IDnota'] as $key => $valor) {
            $cadena='"'.$valor.'":{"nota":"'.$nota['nota'][$key].'",';
            $cadena.='"mod":"'.$nota['modificadornota'][$key].'",';
            $cadena.='"nombre":"'.$nota['nombre'][$key].'",';
            $cadena.='"peso":"'.$nota['peso'][$key].'",';
            $cadena.='"abreviatura":"'.$nota['abreviatura'][$key].'",';
            $cadena.='"indicadores":"'.$nota['indicadores'][$key].'",';
            $cadena.='"porcentaje":"'.$nota['porcentaje'][$key].'",';
            $cadena.='"notaminima":"'.$nota['notaminima'][$key].'",';
 	    $cadena = substr($cadena,0,strlen($cadena)-1); // quitar la como y las comillas
	    $datos_json[]=$cadena.'}';
      }

      echo "{".implode(",", $datos_json)."}";

      // echo $Sql;

// esta función retorna el valor cualitativo de cada competencia
function retornacalificacion($val) {
  $cualitativo="Sin calificar";
  if ($val==0) { $cualitativo="Mal"; }
  if ($val>0 && $val<1) { $cualitativo= "Mal-Regular"; }  
  if ($val==1) { $cualitativo= "Regular"; }  
  if ($val>1 && $val<2) { $cualitativo= "Regular-Bien"; }  
  if ($val==2) { $cualitativo= "Bien"; }     
  if ($val>2 && $val<3) { $cualitativo= "Bien-Excelente"; } 
  if ($val>=3) { $cualitativo= "Excelente"; } 
  return $cualitativo;
}

// comprueba si existe una calificacion
function checkcalificacion($bd,$asignacion,$alumno,$evaluacion) {
  $Sql="SELECT idcalificacion FROM tb_miscalificaciones WHERE asignacion='".$asignacion."' AND alumno='".$alumno."' AND evaluacion='".$evaluacion."'";
  $link=Conectarse($bd);
  $result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result
  $row=mysql_fetch_array($result);
  mysql_free_result($result);

  if ($row["idcalificacion"]>0 && !empty($row["idcalificacion"]) && !is_null($row["idcalificacion"])) { // Existe el valor y no es cero...
     return $row["idcalificacion"];	
  } else { return NULL;}

} // Fin de la función

?>
