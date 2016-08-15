<?php
include_once("../funciones/seguridad/mysql_inc.php");
include_once("../funciones/adodb_time_inc.php");
include_once("../funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("../clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.
    
        $le=array();

	$profesor = $_POST["profesor"];         

        $link=Conectarse($bd); // y me conecto. //dependiendo del tipo recupero uno u otro.
        $Sql='SELECT idindicador, descripcion, profesor, competencia, nombre, abreviatura FROM tb_misindicadores INNER JOIN tb_listacompetencias ON tb_misindicadores.competencia = tb_listacompetencias.idcompetencia WHERE profesor="'.$profesor.'" ORDER BY descripcion';
        $ii=0;
	$result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result
	while ($row=mysql_fetch_array($result)) {
                $le["nombre"][$ii] = iconv("ISO-8859-1","UTF-8",$row["nombre"]);
                $le["abreviatura"][$ii] = iconv("ISO-8859-1","UTF-8",$row["abreviatura"]);
                // necesario para quitar caracteres extraños en la abreviatura de la competencia...
 		$le["abreviatura"][$ii] = ereg_replace('[^ A-Za-z0-9_-ñÑ]', '', $le["abreviatura"][$ii]);
                $le["descripcion"][$ii] = iconv("ISO-8859-1","UTF-8",$row["descripcion"]);
		$le["idindicador"][$ii] = $row["idindicador"];
		$ii++;
		}
	mysql_free_result($result);
       
      foreach ($le['idindicador'] as $key => $valor) {
            $datos_json[]='"'.$valor.'":{"nombre":"'.$le['nombre'][$key].'","abreviatura":"'.$le['abreviatura'][$key].'","descripcion":"'.$le['descripcion'][$key].'"}';
      }
      echo "{".implode(",", $datos_json)."}"; 

// echo $Sql;
?>
