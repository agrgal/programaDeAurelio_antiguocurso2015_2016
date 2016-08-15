<?php
include_once("../funciones/seguridad/mysql_inc.php");
include_once("../funciones/adodb_time_inc.php");
include_once("../funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("../clases/class.micalendario.php");
// $calendario= New micalendario(); // variable de calendario.

$asignacion=$_POST["asignacion"];
$evaluacion=$_POST["evaluacion"];

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
                        truncar($bd,$asignacion); // BORRAR sólo las filas de un profesor, una asignacion
			chmod($targetFile,777);
			$lineas=file($targetFile);
                        $texto='<div id="presentardatos">';
                        $texto.='<table class="tabla" style="margin: 10px auto;"><tr><th>ID</th><th>Nombre</th><th>Peso</th><th>Descripción</th><th>Abreviatura</th><th>Evaluación</th></tr>';
                        foreach($lineas as $valor) {
			   $linea=explode('***',$valor); // 5 asteriscos de separador
                           $texto.='<tr>';
			   $texto.='<td style="text-align: center;">'.$linea[0].'</td>';
			   $texto.='<td style="text-align: center;">'.$linea[1].'</td>';
			   $texto.='<td style="text-align: center;">'.$linea[2].'</td>';
			   $texto.='<td style="text-align: center;">'.$linea[8].'</td>';
			   $texto.='<td style="text-align: center;">'.$linea[11].'</td>';
			   $texto.='<td style="text-align: center;">'.$linea[12].'</td>';
			   // $texto.='<td style="text-align: center;">'.queevaluacion($bd,$linea[5]).'</td>';
                           $texto.='</tr>';
// function insertdato($IDcev,$nombrece,$peso,$iev,$fechainipre, $fechafinpre,$fechainireal,$fechafinreal,$descripcion, $indicadores, $nombreie, $abreviatura,$evaluacion,$base,$asignacion)
                           insertdato($linea[0],$linea[1],$linea[2],$linea[3],$linea[4],$linea[5],$linea[6],$linea[7],$linea[8],$linea[9],$linea[10],$linea[11],queevaluacion($bd,$linea[12]),$bd,$asignacion); // queevaluacion averigua la cadena de texto de la evaluación a qué nombre de la lista de evaluaciones corresponde mejor
			}
			echo $texto.'</table></div>'; 
                        // echo queevaluacion($bd,$linea[5]);
		        // y hecho ésto, se puede recoger en un 
		} else {
			echo 'Tu archivo falló al cargarse';
		}
        }
}

function truncar($base,$asig) {
    $link=Conectarse($base); 
    $Sql="DELETE FROM tb_misconceptosevaluativos WHERE asignacion='".$asig."'";
    $result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result
    mysql_free_result($result);
}

function insertdato($IDcev,$nombrece,$peso,$iev,$fechainipre, $fechafinpre,$fechainireal,$fechafinreal,$descripcion, $indicadores, $nombreie, $abreviatura,$evaluacion,$base,$asignacion) {

    $nombrece=iconv("UTF-8","ISO-8859-1",$nombrece);
    $abreviatura=iconv("UTF-8","ISO-8859-1",$abreviatura);

    //1º) introduce el dato en la tabla...
    if (is_numeric($IDcev) && $IDcev>=0 && !empty($IDcev)) { 
    $Sql="INSERT INTO tb_misconceptosevaluativos (IDcev,nombre,peso,iev,indicadores,fechainipre,fechafinpre,fechainireal,fechafinreal,asignacion,evaluacion,descripcion) VALUES (";
    if ($IDcev<>'') {$Sql.="'".$IDcev."', ";} else {$Sql.="'-',";}
    } else {
    $Sql="INSERT INTO tb_misconceptosevaluativos (nombre,peso,iev,indicadores,fechainipre,fechafinpre,fechainireal,fechafinreal,asignacion,evaluacion,descripcion) VALUES (";
    }
    if ($nombrece<>'') {$Sql.="'".$nombrece."', ";} else {$Sql.="'-',";}
    if ($peso<>'') {$Sql.="'".$peso."', ";} else {$Sql.="'-',";}
    if ($iev<>'') {$Sql.="'".$iev."', ";} else {$Sql.="'-',";}
    if ($indicadores<>'') {$Sql.="'".$indicadores."', ";} else {$Sql.="'-',";}
    if ($fechainipre<>'') {$Sql.="'".$fechainipre."', ";} else {$Sql.="'-',";}
    if ($fechafinpre<>'') {$Sql.="'".$fechafinpre."', ";} else {$Sql.="'-',";}
    if ($fechainireal<>'') {$Sql.="'".$fechainireal."', ";} else {$Sql.="'-',";}
    if ($fechafinreal<>'') {$Sql.="'".$fechafinreal."', ";} else {$Sql.="'-',";}
    if ($asignacion<>'') {$Sql.="'".$asignacion."', ";} else {$Sql.="'-',";}
    if ($evaluacion<>'') {$Sql.="'".$evaluacion."', ";} else {$Sql.="'-',";}
    if ($descripcion<>'') {$Sql.="'".$descripcion."', ";} else {$Sql.="'', ";}
    $Sql=substr($Sql,0,strlen($Sql)-2); /* Quitar la última coma */
    $Sql.=")"; 

    //2º) conecta...
    $link=Conectarse($base);
    $result=mysql_query($Sql,$link); //ejecuta la consulta
    mysql_free_result($result);
}


?>
