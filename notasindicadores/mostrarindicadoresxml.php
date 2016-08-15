<?php
include_once("../funciones/seguridad/mysql_inc.php");
include_once("../funciones/adodb_time_inc.php");
include_once("../funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("../clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

        $page = isset($_POST['page']) ? $_POST['page'] : 1;
	$rp = isset($_POST['rp']) ? $_POST['rp'] : 5;
	$sortname = isset($_POST['sortname']) ? $_POST['sortname'] : 'idindicador';
	$sortorder = isset($_POST['sortorder']) ? $_POST['sortorder'] : 'asc';
	$query = isset($_POST['query']) ? $_POST['query'] : false;
	$qtype = isset($_POST['qtype']) ? $_POST['qtype'] : false;

        if ($query) { $where = " WHERE $qtype ='".mysql_real_escape_string($query)."'"; }
        else { $where =""; } // Para filtrar con buscar

        $start = (($page-1) * $rp); // para, según rp, poder dar datos de poco a poco
	$limit = "LIMIT $start, $rp";
    
        $le=array();
        $le['rows'] = array(); // defino el array de datos

        $link=Conectarse($bd); // y me conecto. //dependiendo del tipo recupero uno u otro.
	// $Sql="SELECT idindicador, descripcion, competencia FROM tb_misindicadores ".$where." ORDER BY ".$sortname." ".$sortorder." ".$limit;
        $Sql="SELECT idindicador, descripcion, competencia, nombre, abreviatura FROM tb_misindicadores INNER JOIN tb_listacompetencias ON tb_misindicadores.competencia = tb_listacompetencias.idcompetencia ".$where." ORDER BY ".$sortname." ".$sortorder." ".$limit;
	$result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result
        $ii=0;
	while ($row=mysql_fetch_array($result)) {
                $nn = strip_tags(iconv("ISO-8859-1","UTF-8",$row["descripcion"]));
                $aa = iconv("ISO-8859-1","UTF-8",strtoupper($row["abreviatura"]));
                $bb = iconv("ISO-8859-1","UTF-8",strtoupper($row["competencia"]));
                $le['rows'][] = array (
                   'idindicador' => $row['idindicador'],
                   'cell' => array($row['idindicador'], $nn, $aa,$bb)
                ); 
                $ii++;
		}
	mysql_free_result($result);

        // para contar el número total de registros
	$Sql="SELECT idindicador FROM tb_misindicadores";
	$result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result
        $total = mysql_num_rows($result);
	mysql_free_result($result);

        $le['page'] = $page; // la página donde está
        $le['total'] = $total; // número de registro totales
       
        header("Content-type: text/xml");
        $xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
	$xml .= "<rows>";
	$xml .= "<page>".$le['page']."</page>";
	$xml .= "<total>".$le['total']."</total>";
	foreach($le['rows'] as $key => $valor){
		$xml .= "<row idindicador='".$valor['cell'][0]."'>";
		$xml .= "<cell><![CDATA[".$valor['cell'][0]."]]></cell>";
		//$xml .= "<cell><![CDATA[".print_r($_POST,true)."]]></cell>";
		$xml .= "<cell><![CDATA[".$valor['cell'][1]."]]></cell>";
		$xml .= "<cell><![CDATA[".$valor['cell'][2]."]]></cell>";
		$xml .= "<cell><![CDATA[".$valor['cell'][3]."]]></cell>";
		$xml .= "</row>";
	}

	$xml .= "</rows>";
	echo $xml;

// echo $Sql;
?>
