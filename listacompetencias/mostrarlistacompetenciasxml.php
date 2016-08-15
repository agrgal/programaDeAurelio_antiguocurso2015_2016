<?php
include_once("../funciones/seguridad/mysql_inc.php");
include_once("../funciones/adodb_time_inc.php");
include_once("../funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("../clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

        $page = isset($_POST['page']) ? $_POST['page'] : 1;
	$rp = isset($_POST['rp']) ? $_POST['rp'] : 5;
	$sortname = isset($_POST['sortname']) ? $_POST['sortname'] : 'idcompetencia';
	$sortorder = isset($_POST['sortorder']) ? $_POST['sortorder'] : 'asc';
	$query = isset($_POST['query']) ? $_POST['query'] : false;
	$qtype = isset($_POST['qtype']) ? $_POST['qtype'] : false;

        if ($query) { $where = " WHERE $qtype LIKE '%".mysql_real_escape_string($query)."%' "; }
        else { $where =""; } // Para filtrar con buscar

        $start = (($page-1) * $rp); // para, según rp, poder dar datos de poco a poco
	$limit = "LIMIT $start, $rp";
    
        $le=array();
        $le['rows'] = array(); // defino el array de datos

        $link=Conectarse($bd); // y me conecto. //dependiendo del tipo recupero uno u otro.
	$Sql="SELECT idcompetencia, nombre, abreviatura FROM tb_listacompetencias ".$where." ORDER BY ".$sortname." ".$sortorder." ".$limit;
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

        // para contar el número total de registros
	$Sql="SELECT idcompetencia FROM tb_listacompetencias";
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
		$xml .= "<row idcompetencia='".$valor['cell'][0]."'>";
		$xml .= "<cell><![CDATA[".$valor['cell'][0]."]]></cell>";
		//$xml .= "<cell><![CDATA[".print_r($_POST,true)."]]></cell>";
		$xml .= "<cell><![CDATA[".$valor['cell'][1]."]]></cell>";
		$xml .= "<cell><![CDATA[".$valor['cell'][2]."]]></cell>";
		$xml .= "</row>";
	}

	$xml .= "</rows>";
	echo $xml;

// echo $Sql;
?>
