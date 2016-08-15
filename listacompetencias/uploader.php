<?php
include_once("../funciones/seguridad/mysql_inc.php");
include_once("../funciones/adodb_time_inc.php");
include_once("../funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("../clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

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
                        truncar($bd);
			chmod($targetFile,777);
			$lineas=file($targetFile);
                        $texto='<div id="presentardatos">';
                        $texto.='<table class="tabla" style="margin: 10px auto;"><tr><th>ID</th><th>Nombre</th><th>Abreviatura</th></tr>';
                        foreach($lineas as $valor) {
			   $linea=explode(';',$valor);
                           $texto.='<tr>';
			   $texto.='<td style="text-align: center;">'.$linea[0].'</td>';
			   $texto.='<td style="text-align: left;">'.$linea[1].'</td>';
			   $texto.='<td style="text-align: center;">'.$linea[2].'</td>';
                           $texto.='</tr>';
                           insertdato($linea[0],$linea[1],$linea[2],$bd);
			}
			echo $texto.'</table></div>';
		        // y hecho ésto, se puede recoger en un 
		} else {
			echo 'Tu archivo falló al cargarse';
		}
        }
}

function truncar($base) {
    $link=Conectarse($base); 
    $Sql="TRUNCATE TABLE tb_listacompetencias";
    $result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result
    mysql_free_result($result);
}

function insertdato($id,$nombre,$abreviatura,$base) {
    $nombre=iconv("UTF-8","ISO-8859-1",$nombre);
    $abreviatura=iconv("UTF-8","ISO-8859-1",$abreviatura);

    //1º) comprueba que existe el id
    $Sql="INSERT INTO tb_listacompetencias (nombre, abreviatura) VALUES (";
    if ($nombre<>'') {$Sql.="'".$nombre."', ";} else {$Sql.="'-',";}
    if ($abreviatura<>'') {$Sql.="'".$abreviatura."', ";} else {$Sql.="'-',";}
    $Sql=substr($Sql,0,strlen($Sql)-2); 
    $Sql.=")"; 	

    //2º) comprueba que existe el id
    $link=Conectarse($base);
    $result=mysql_query($Sql,$link); //ejecuta la consulta
    mysql_free_result($result);}
?>
