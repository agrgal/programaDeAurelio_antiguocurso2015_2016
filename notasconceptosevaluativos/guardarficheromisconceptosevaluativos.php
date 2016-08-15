<?php
include_once("../funciones/seguridad/mysql_inc.php");
include_once("../funciones/adodb_time_inc.php");
include_once("../funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("../clases/class.micalendario.php");
// $calendario= New micalendario(); // variable de calendario.

        $le=array();
        $le['rows'] = array(); // defino el array de datos

        $asignacion = $_POST["asignacion"];
        $evaluacion = $_POST["evaluacion"];

        $datosasignacion = obtenerdatosasignacion($bd,$asignacion);
        $datosevaluaciones = obtenerlistaevaluaciones($bd);

        $link=Conectarse($bd); // y me conecto. //dependiendo del tipo recupero uno u otro.
	$Sql="SELECT tb_misconceptosevaluativos.IDcev, tb_misconceptosevaluativos.nombre AS nombrece, tb_misconceptosevaluativos.peso, tb_misconceptosevaluativos.iev, tb_misconceptosevaluativos.fechainipre, tb_misconceptosevaluativos.fechafinpre, tb_misconceptosevaluativos.fechainireal, tb_misconceptosevaluativos.fechafinreal, tb_misconceptosevaluativos.asignacion, tb_misconceptosevaluativos.evaluacion, tb_misconceptosevaluativos.descripcion, tb_misconceptosevaluativos.indicadores, tb_misinstrumentosevaluativos.IDiev, tb_misinstrumentosevaluativos.nombre AS nombreie, tb_misinstrumentosevaluativos.abreviatura, tb_misinstrumentosevaluativos.porcentaje, tb_misinstrumentosevaluativos.notaminima FROM tb_misconceptosevaluativos INNER JOIN tb_misinstrumentosevaluativos ON tb_misconceptosevaluativos.iev = tb_misinstrumentosevaluativos.IDiev WHERE tb_misconceptosevaluativos.asignacion='".$_POST["asignacion"]."' ORDER BY nombrece";

	$result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result
        $ii=0;
	while ($row=mysql_fetch_array($result)) {
                $nombrece = iconv("ISO-8859-1","UTF-8",$row["nombrece"]);
                $peso = $row["peso"];
                $iev = $row["iev"];
                // $nombreie = $row["nombreie"];
                $fechainipre = $row["fechainipre"];
                $fechafinpre = $row["fechafinpre"];
                $fechainireal = $row["fechainireal"];
                $fechafinreal = $row["fechafinreal"];
                $descripcion = strip_tags(iconv("ISO-8859-1","UTF-8",$row["descripcion"]));
                $indicadores = $row["indicadores"];
                $nombreie = iconv("ISO-8859-1","UTF-8",$row["nombreie"]);
                $abreviatura = iconv("ISO-8859-1","UTF-8",$row["abreviatura"]);

		$clave = array_search($row["evaluacion"],$datosevaluaciones['idlistaevaluaciones']);
                if ($clave>=0) {
	            $nombreevaluacion = iconv("ISO-8859-1","UTF-8",$datosevaluaciones['nombre'][$clave]);
                } // busca la clave de la evaluación

                // $nombreEVA = iconv("ISO-8859-1","UTF-8",$row["nombreEVA"]);
                $le['rows'][] = array (
                   'id' => $row['IDcev'],
                   'cell' => array($row['IDcev'],$nombrece,$peso,$iev,$fechainipre,$fechafinpre,$fechainireal,$fechafinreal,$descripcion,$indicadores,$nombreie,$abreviatura,$nombreevaluacion)
                ); 
                $ii++;
		}
	mysql_free_result($result);

        $le['page'] = 1;
        $le['total'] = $ii;

        // Datos codificados en fichero
        $nombre_fichero="../ficheros/misconceptosevaluativos_".iconv("ISO-8859-1","UTF-8",$datosasignacion["descripcion"]).".csv";
        
        //1º) Si el fichero existe, borrarlo
	if (file_exists($nombre_fichero)) {
		chmod($nombre_fichero, 777); 
		unlink($nombre_fichero);
        }

        //2º) Abre el fichero como escritura y graba los datos.
	$fp=fopen($nombre_fichero,'w'); // Se intenta crear si no existe
        foreach ($le['rows'] as $key => $valor) {
            if (!(is_null($valor['cell'][1]))) {
 		fwrite($fp,$valor['cell'][0].'***'.$valor['cell'][1].'***'.trim($valor['cell'][2]).'***'.trim($valor['cell'][3]).'***'.trim($valor['cell'][4]).'***'.trim($valor['cell'][5]).'***'.trim($valor['cell'][6]).'***'.trim($valor['cell'][7]).'***'.trim($valor['cell'][8]).'***'.trim($valor['cell'][9]).'***'.trim($valor['cell'][10]).'***'.trim($valor['cell'][11]).'***'.trim($valor['cell'][12]).PHP_EOL); // 3 asteriscos de separador
            }
        }
        fclose($fp);
        chmod($nombre_fichero, 777);
        
        // echo $evaluacion." ".$nombreevaluacion;
        echo $nombre_fichero;
