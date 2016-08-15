<?php
include_once("../funciones/seguridad/mysql_inc.php");
include_once("../funciones/adodb_time_inc.php");
include_once("../funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("../clases/class.micalendario.php");
// $calendario= New micalendario(); // variable de calendario.

        $le=array();
        $le['rows'] = array(); // defino el array de datos

        $asignacion = $_POST["asignacion"];
        $datosasignacion = obtenerdatosasignacion($bd,$asignacion);

        $link=Conectarse($bd); // y me conecto. //dependiendo del tipo recupero uno u otro.
	// $Sql="SELECT idindicador, nombre, abreviatura FROM tb_listacompetencias ORDER BY idcompetencia";
        $Sql="SELECT tb_misinstrumentosevaluativos.IDiev, tb_misinstrumentosevaluativos.nombre AS nombreIE , tb_misinstrumentosevaluativos.abreviatura, tb_misinstrumentosevaluativos.porcentaje, tb_misinstrumentosevaluativos.notaminima, tb_misinstrumentosevaluativos.evaluacion, tb_listaevaluaciones.nombre AS nombreEVA, tb_listaevaluaciones.idlistaevaluaciones FROM tb_misinstrumentosevaluativos INNER JOIN tb_listaevaluaciones ON tb_misinstrumentosevaluativos.evaluacion = tb_listaevaluaciones.idlistaevaluaciones WHERE asignacion='".$asignacion."' ORDER BY evaluacion";
	$result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result
        $ii=0;
	while ($row=mysql_fetch_array($result)) {
                $nombreIE = iconv("ISO-8859-1","UTF-8",$row["nombreIE"]);
                $abreviatura = iconv("ISO-8859-1","UTF-8",$row["abreviatura"]);
                $porcentaje = iconv("ISO-8859-1","UTF-8",$row["porcentaje"]);
                $notaminima = iconv("ISO-8859-1","UTF-8",$row["notaminima"]);
                $nombreEVA = iconv("ISO-8859-1","UTF-8",$row["nombreEVA"]);
                $le['rows'][] = array (
                   'id' => $row['IDiev'],
                   'cell' => array($row['IDiev'],$nombreIE,$abreviatura,$porcentaje,$notaminima,$nombreEVA)
                ); 
                $ii++;
		}
	mysql_free_result($result);

        $le['page'] = 1;
        $le['total'] = $ii;

        // Datos codificados en fichero
        $nombre_fichero="../ficheros/misinstrumentosevaluativos_".iconv("ISO-8859-1","UTF-8",$datosasignacion["descripcion"]).".csv";
        
        //1º) Si el fichero existe, borrarlo
	if (file_exists($nombre_fichero)) {
		chmod($nombre_fichero, 777); 
		unlink($nombre_fichero);
        }

        //2º) Abre el fichero como escritura y graba los datos.
	$fp=fopen($nombre_fichero,'w'); // Se intenta crear si no existe
        foreach ($le['rows'] as $key => $valor) {
            if (!(is_null($valor['cell'][1]))) {
 		fwrite($fp,$valor['cell'][0].'***'.$valor['cell'][1].'***'.trim($valor['cell'][2]).'***'.trim($valor['cell'][3]).'***'.trim($valor['cell'][4]).'***'.trim($valor['cell'][5]).PHP_EOL); // 3 asteriscos de separador
            }
        }
        fclose($fp);
        chmod($nombre_fichero, 777); 

        echo $nombre_fichero;
