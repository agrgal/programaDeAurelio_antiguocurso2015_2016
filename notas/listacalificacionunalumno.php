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

	$Sql= "SELECT tb_miscalificaciones.idcalificacion, tb_miscalificaciones.notamedia, tb_miscalificaciones.notaseneca, tb_miscalificaciones.notarecuperacion, tb_alumno.alumno, tb_alumno.idalumno FROM tb_miscalificaciones INNER JOIN tb_alumno ON tb_miscalificaciones.alumno = tb_alumno.idalumno WHERE tb_miscalificaciones.evaluacion='".$_POST["evaluacion"]."' AND tb_miscalificaciones.asignacion='".$_POST["asignacion"]."' AND tb_miscalificaciones.alumno='".$_POST["alumno"]."' ORDER BY tb_alumno.alumno ASC";
   
   $result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result
   $row=mysql_fetch_array($result);

            $cadena='"calificacion":{"id":"'.$row["idalumno"].'",';
            $cadena.= '"idcalificacion":"'.$row["idcalificacion"].'",';
            if (!is_null($row["notamedia"])) 
                  { $cadena.= '"notamedia":"'.$row["notamedia"].'",'; } else { $cadena.= '"notamedia":"-",'; }
            if (!is_null($row["notaseneca"])) 
                  { $cadena.= '"notaseneca":"'.$row["notaseneca"].'",'; } else { $cadena.= '"notaseneca":"-",'; }
            if (!is_null($row["notarecuperacion"])) 
                  { $cadena.= '"notarecuperacion":"'.$row["notarecuperacion"].'",'; } else { $cadena.= '"notarecuperacion":"-",'; }
            $cadena.= '"alumno":"'.iconv("ISO-8859-1","UTF-8",cambiarnombre($row["alumno"])).'",'; 
 	    $cadena = substr($cadena,0,strlen($cadena)-1); // quitar la como y las comillas
	    $datos_json[]=$cadena.'}';

   mysql_free_result($result);


   echo "{".implode(",", $datos_json)."}";

   // echo $Sql;

?>
