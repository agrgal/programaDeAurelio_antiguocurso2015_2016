<?php
include_once("../funciones/seguridad/mysql_inc.php");
include_once("../funciones/adodb_time_inc.php");
include_once("../funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("../clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

        $le=array();
        $le['rows'] = array(); // defino el array de datos

        $link=Conectarse($bd); // y me conecto. //dependiendo del tipo recupero uno u otro.
	$Sql="SELECT idcompetencia, nombre, abreviatura FROM tb_listacompetencias ORDER BY idcompetencia";
	$result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result
        $ii=0;
	while ($row=mysql_fetch_array($result)) {
                $nn = iconv("ISO-8859-1","UTF-8",$row["nombre"]);
                $aa = iconv("ISO-8859-1","UTF-8",strtoupper($row["abreviatura"]));
                $le['rows'][] = array (
                   'idcompetencia' => $row['idcompetencia'],
                   'cell' => array($row['idcompetencia'], $nn, $aa)
                ); 
                $ii++;
		}
	mysql_free_result($result);

        $le['page'] = 1;
        $le['total'] = $ii;

        // Datos codificados en fichero
        $nombre_fichero="../ficheros/listacompetencias.csv";
        
        //1º) Si el fichero existe, borrarlo
	if (file_exists($nombre_fichero)) {
		chmod($nombre_fichero, 777); 
		unlink($nombre_fichero);
        }

        //2º) Abre el fichero como escritura y graba los datos.
	$fp=fopen($nombre_fichero,'w'); // Se intenta crear si no existe
        foreach ($le['rows'] as $key => $valor) {
            if (!(is_null($valor['cell'][1]))) {
 		fwrite($fp,$valor['cell'][0].';'.$valor['cell'][1].';'.trim($valor['cell'][2]).PHP_EOL);
            }
        }
        fclose($fp);
        chmod($nombre_fichero, 777); 

        echo $nombre_fichero;
?>
