<?php
include_once("../funciones/seguridad/mysql_inc.php");
include_once("../funciones/adodb_time_inc.php");
include_once("../funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("../clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.
    
        $le=array();
        $le['rows'] = array(); // defino el array de datos

        $link=Conectarse($bd); // y me conecto. //dependiendo del tipo recupero uno u otro.
	$Sql="SELECT idlistaevaluaciones, nombre, fechaini, fechafin FROM tb_listaevaluaciones ORDER BY idlistaevaluaciones";
	$result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result
        $ii=0;
	while ($row=mysql_fetch_array($result)) {
                $nn = iconv("ISO-8859-1","UTF-8",$row["nombre"]);
                $fec = explode("-",$row['fechaini']);
                $fechaini=gmdate("d/m/Y",mktime(12,0,0,$fec[1], $fec[2], $fec[0]));
                $fec = explode("-",$row['fechafin']);
                $fechafin=gmdate("d/m/Y",mktime(12,0,0,$fec[1], $fec[2], $fec[0]));		
                $le['rows'][] = array (
                   'idlistaevaluaciones' => $row['idlistaevaluaciones'],
                   'cell' => array($row['idlistaevaluaciones'], $nn, $fechaini, $fechafin)
                ); 
                $ii++;
		}
	mysql_free_result($result);

        $le['page'] = 1;
        $le['total'] = $ii;
       
        echo json_encode($le); // enviar datos json

// echo $Sql;
?>
