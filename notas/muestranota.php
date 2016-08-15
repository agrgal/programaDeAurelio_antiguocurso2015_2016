<?php
include_once("../funciones/seguridad/mysql_inc.php");
include_once("../funciones/adodb_time_inc.php");
include_once("../funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("../clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.
    
     $link=Conectarse($bd); // y me conecto. //dependiendo del tipo recupero uno u otro.
     $Sql="SELECT IDnota, ce, alumno, nota, indicadores, modificadornota FROM tb_notas WHERE ce='".$_POST["ce"]."' AND alumno='".$_POST["alumno"]."'";
    
     $result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result
     $row=mysql_fetch_array($result);

     $datos_json[]='"IDnota":"'.$row['IDnota'].'"';
     $datos_json[]='"indicadores":"'.$row['indicadores'].'"';
     $datos_json[]='"nota":"'.$row['nota'].'"';
     $datos_json[]='"modificadornota":"'.$row['modificadornota'].'"';

     mysql_free_result($result);

     echo "{".implode(",", $datos_json)."}"; 

     // echo $Sql;
?>
