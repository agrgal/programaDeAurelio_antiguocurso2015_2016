<?php
include_once("../funciones/seguridad/mysql_inc.php");
include_once("../funciones/adodb_time_inc.php");
include_once("../funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("../clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

// ****************************
// 1ª) Parte: obtiene los datos
// ****************************

$alumnos = obteneralumnosasignacion($bd,$_POST["asignacion"]); // obtengo los alumnos de la asignacion

$alumnosordenados= array();
foreach ($alumnos["alumno"] as $key => $valor) {
  $alumnosordenados[$key] = iconv("ISO-8859-1","UTF-8",$valor); // convierto ANTES para ordenar mejor
}

asort($alumnosordenados); // ordeno estos valores. CONSERVO las claves ¡¡Importante!!

$ii=0;

$nmsuspensos=0; $nmaprobados=0; $nssuspensos=0; $nsaprobados=0; $nrsuspensos=0; $nraprobados=0;

$ntaprobados = 0; $ntsuspensos = 0;

$cadena='"calificaciones":{';

foreach ($alumnosordenados as $key => $valor) { // por cada alumno de la asignacion

        $link=Conectarse($bd); // y me conecto. //dependiendo del tipo recupero uno u otro.

	$Sql= "SELECT tb_miscalificaciones.idcalificacion, tb_miscalificaciones.notamedia, tb_miscalificaciones.notaseneca, tb_miscalificaciones.notarecuperacion, tb_alumno.alumno FROM tb_miscalificaciones INNER JOIN tb_alumno ON tb_miscalificaciones.alumno = tb_alumno.idalumno WHERE tb_miscalificaciones.evaluacion='".$_POST["evaluacion"]."' AND tb_miscalificaciones.asignacion='".$_POST["asignacion"]."' AND tb_miscalificaciones.alumno='".$alumnos["idalumno"][$key]."' ORDER BY tb_alumno.alumno ASC";
   
   $result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result
   $row=mysql_fetch_array($result);

            $total++;
            $cadena.='"'.$ii.'":{"id":"'.$alumnos["idalumno"][$key].'",';
            if (!is_null($row["notamedia"])) 
                  { $cadena.= '"notamedia":"'.$row["notamedia"].'",'; } else { $cadena.= '"notamedia":"-",'; }

	    // Estadisticas nota calculada
            if (is_numeric($row["notamedia"]) && $row["notamedia"]>=0 && $row["notamedia"]<5) 
                  { $nmsuspensos++;}
            else if (is_numeric($row["notamedia"]) && $row["notamedia"]>=0 && $row["notamedia"]>=5) 
                  { $nmaprobados++;}

            if (!is_null($row["notaseneca"])) 
                  { $cadena.= '"notaseneca":"'.$row["notaseneca"].'",'; } else { $cadena.= '"notaseneca":"-",'; }

	    // Estadisticas nota seneca
            if (is_numeric($row["notaseneca"]) && $row["notaseneca"]>=0 && $row["notaseneca"]<5) 
                  { $nssuspensos++;}
            else if (is_numeric($row["notaseneca"]) && $row["notaseneca"]>=0 && $row["notaseneca"]>=5) 
                  { $nsaprobados++;}

            if (!is_null($row["notarecuperacion"])) 
                  { $cadena.= '"notarecuperacion":"'.$row["notarecuperacion"].'",'; } else { $cadena.= '"notarecuperacion":"-",'; }

	    // Estadisticas nota recuperacion
            if (is_numeric($row["notarecuperacion"]) && $row["notarecuperacion"]>=0 && $row["notarecuperacion"]<5) 
                  { $nrsuspensos++;}
            else if (is_numeric($row["notarecuperacion"]) && $row["notarecuperacion"]>=0 && $row["notarecuperacion"]>=5) 
                  { $nraprobados++;}

	    // Estadística nota total
            $nt = max($row["notarecuperacion"],$row["notaseneca"]);

            if (is_numeric($nt) && $nt>=0 && $nt<5) { $ntsuspensos++;}
            else if (is_numeric($nt) && $nt>=0 && $nt>=5) { $ntaprobados++;}
                 

            $cadena.= '"alumno":"'.$valor.'",'; 
            $cadena.= '"idcalificacion":"'.$row["idcalificacion"].'",'; 
 	    $cadena = substr($cadena,0,strlen($cadena)-1); // quitar la como y las comillas
	    $cadena.='},';

   mysql_free_result($result);

   $ii++; // aumenta el contador

} // fin del foreach alumnos

   $cadena = substr($cadena,0,strlen($cadena)-1); // quitar la coma y las comillas
   $datos_json[]=$cadena.'}'; // datos.poriev[i].abreviatura

   // Datos estadísticos nota calculada
   $datos_json[]='"nmaprobados":"'.$nmaprobados.'"';
   $datos_json[]='"nmsuspensos":"'.$nmsuspensos.'"';
   $datos_json[]='"nmno":"'.($ii-$nmsuspensos-$nmaprobados).'"';
   $datos_json[]='"nmporaprobados":"'.round(100*$nmaprobados/$ii,1).'"';
   $datos_json[]='"nmporsuspensos":"'.round(100*$nmsuspensos/$ii,1).'"';

   // Datos estadísticos nota seneca
   $datos_json[]='"nsaprobados":"'.$nsaprobados.'"';
   $datos_json[]='"nssuspensos":"'.$nssuspensos.'"';
   $datos_json[]='"nsno":"'.($ii-$nssuspensos-$nsaprobados).'"';
   $datos_json[]='"nsporaprobados":"'.round(100*$nsaprobados/$ii,1).'"';
   $datos_json[]='"nsporsuspensos":"'.round(100*$nssuspensos/$ii,1).'"';

   // Datos estadísticos nota recuperacion
   $datos_json[]='"nraprobados":"'.$nraprobados.'"';
   $datos_json[]='"nrsuspensos":"'.$nrsuspensos.'"';
   $datos_json[]='"nrno":"'.($ii-$nrsuspensos-$nraprobados).'"';
   $datos_json[]='"nrporaprobados":"'.round(100*$nraprobados/$ii,1).'"';
   $datos_json[]='"nrporsuspensos":"'.round(100*$nrsuspensos/$ii,1).'"';

   // Datos estadísticos nota total
   $datos_json[]='"ntaprobados":"'.$ntaprobados.'"';
   $datos_json[]='"ntsuspensos":"'.$ntsuspensos.'"';
   $datos_json[]='"ntno":"'.($ii-$ntsuspensos-$ntaprobados).'"';
   $datos_json[]='"ntporaprobados":"'.round(100*$ntaprobados/$ii,1).'"';
   $datos_json[]='"ntporsuspensos":"'.round(100*$ntsuspensos/$ii,1).'"';
   

   echo "{".implode(",", $datos_json)."}";

   // echo $Sql;

?>
