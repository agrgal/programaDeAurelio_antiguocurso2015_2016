<?php
include_once("../funciones/seguridad/mysql_inc.php");
include_once("../funciones/adodb_time_inc.php");
include_once("../funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("../clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

        
        // 1) Obtiene la lista de indicadores como cadena

        $Sql='SELECT IDcev, indicadores FROM tb_misconceptosevaluativos WHERE IDcev="'.$_POST["listaconceptoevaluativo"].'"';

        $ind=array();
        $link=Conectarse($bd); // y me conecto. //dependiendo del tipo recupero uno u otro.
	$result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result
	$ii=0; // contador 
	while ($row=mysql_fetch_array($result)) {
		$ind['IDcev'][$ii]=$row['IDcev'];
                $ind['indicadores'][$ii]=$row['indicadores'];
                // echo $ii;
		$ii++;
		}
	mysql_free_result($result);

        // 2) Obtiene un array con los indicadores
        $indicadores=array();
        $cortar = explode('*', $ind['indicadores'][0]); // corta el primer resultado
        $ii=0;
        foreach ($cortar as $key => $valor) { // por cada valor
	     $cortar2=explode("-",$valor); // obtiene los dos valores
	     $indicadores['IDindicador'][$ii]=$cortar2[0]; // El primero es el ID
             // $indicadores['nombre'][$ii]="Aquí vendría el nombre";
             $ii++; 
        }

        // 3) Obtiene el nombre de los indicadores y su abreviatura
        foreach ($indicadores['IDindicador'] as $key => $valor) {
        $link=Conectarse($bd); // y me conecto. //dependiendo del tipo recupero uno u otro.
	$Sql='SELECT idindicador, descripcion, abreviatura, competencia FROM tb_misindicadores INNER JOIN tb_listacompetencias ON tb_misindicadores.competencia = tb_listacompetencias.idcompetencia WHERE idindicador="'.$valor.'"';
        $result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result
        $row=mysql_fetch_array($result);
	     $indicadores['nombre'][$key]=$row['descripcion'];
             $indicadores['abreviatura'][$key]=trim($row['abreviatura']);
	mysql_free_result($result);
        }

      // 4) Cadena JSON
      foreach ($indicadores['IDindicador'] as $key => $valor) {
            $datos_json[]='"'.$valor.'":{"nombre":"'.$indicadores['nombre'][$key].'","abreviatura":"'.$indicadores['abreviatura'][$key].'"}';
      } 
      
      echo "{".implode(",", $datos_json)."}";

      // echo  $indicadores['nombre'][0];
?>
