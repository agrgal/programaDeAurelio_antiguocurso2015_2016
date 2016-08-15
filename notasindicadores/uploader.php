<?php
include_once("../funciones/seguridad/mysql_inc.php");
include_once("../funciones/adodb_time_inc.php");
include_once("../funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("../clases/class.micalendario.php");
// $calendario= New micalendario(); // variable de calendario.

$profesor=$_POST["profesor"];

if (!empty($_FILES)) {
	$tempFile = $_FILES['Filedata']['tmp_name'];
	$file_name = $_FILES['Filedata']['name'];
        $tipos = explode('.',$file_name);
        $tipo = strtolower(trim(end($tipos))); // último valor
	// $targetPath = $_SERVER['DOCUMENT_ROOT'] . $_REQUEST['folder'] . '/';
	// $targetFile =  str_replace('//','/',$targetPath) . $file_name;
        $targetFile = "..".$_REQUEST['folder']."/".$file_name;	
        // Sólo lo sube si es del mismo tipo
        if ($tipo!="csv" && $tipo!="txt") {
		echo "Tipo de fichero incorrecto: ".$tipo;
	} else {
	 	if (move_uploaded_file($tempFile,$targetFile)){
			// y hecho ésto, se puede recoger en un array los datos
                        truncar($bd,$profesor); // BORRAR sólo las filas de un profesor
			chmod($targetFile,777);
			$lineas=file($targetFile);
                        $texto='<div id="presentardatos">';
                        $texto.='<table class="tabla" style="margin: 10px auto;"><tr><th>ID</th><th>Descripción</th><th>competencia</th></tr>';
                        foreach($lineas as $valor) {
			   $linea=explode('***',$valor); // 5 asteriscos de separador
                           $texto.='<tr>';
			   $texto.='<td style="text-align: center;">'.$linea[0].'</td>';
			   $texto.='<td style="text-align: left;">'.$linea[1].'</td>';
			   $texto.='<td style="text-align: center;">'.$linea[3].'('.$linea[4].')</td>';
                           $texto.='</tr>';
                           insertdato($linea[0],$linea[1],$linea[2],$linea[3],$linea[4],$bd,$profesor);
			}
			echo $texto.'</table></div>';
		        // y hecho ésto, se puede recoger en un 
		} else {
			echo 'Tu archivo falló al cargarse';
		}
        }
}

function truncar($base,$pro) {
    $link=Conectarse($base); 
    $Sql="DELETE FROM tb_misindicadores WHERE profesor='".$pro."'";
    $result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result
    mysql_free_result($result);
}

function insertdato($id,$descripcion,$competencia,$nombre,$abreviatura,$base,$pro) {
    $descripcion=iconv("UTF-8","ISO-8859-1",$descripcion);
    $competencia=iconv("UTF-8","ISO-8859-1",$competencia);

    //1º) introduce el dato en la tabla...
    $Sql="INSERT INTO tb_misindicadores (descripcion, competencia, profesor) VALUES (";
    if ($descripcion<>'') {$Sql.="'".$descripcion."', ";} else {$Sql.="'-',";}
    if ($competencia<>'') {$Sql.="'".$competencia."', ";} else {$Sql.="'-',";}
    if ($pro<>'') {$Sql.="'".$pro."', ";} else {$Sql.="'-',";}
    $Sql=substr($Sql,0,strlen($Sql)-2); 
    $Sql.=")"; 	

    //2º) conecta...
    $link=Conectarse($base);
    $result=mysql_query($Sql,$link); //ejecuta la consulta
    mysql_free_result($result);}
?>
