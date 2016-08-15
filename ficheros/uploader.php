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
	$targetPath = $_SERVER['DOCUMENT_ROOT'] . $_REQUEST['folder'] . '/';
	$targetFile =  str_replace('//','/',$targetPath) . $file_name;	
        // Sólo lo sube si es del mismo tipo
        if ($tipo!="csv" && $tipo!="txt") {
		echo "Tipo de fichero incorrecto: ".$tipo;
	} else {
	 	if (move_uploaded_file($tempFile,$targetFile)){
			// y hecho ésto, se puede recoger en un array los datos
                        truncar();
			chmod($targetFile,777);
			$lineas=file($targetFile);
                        $texto="";
                        foreach($lineas as $valor) {
			   $linea=explode(';',$valor);
                           $texto.=$linea[0]."->".$linea[1]." (".$linea[2].")<br>";
			}
			echo $texto;
		        // y hecho ésto, se puede recoger en un 
		} else {
			echo 'Tu archivo falló al cargarse: '.$tempFile ;
		}
        }
}

function truncar() {
    $link=Conectarse($bd); 
    $Sql="SELECT idcompetencia FROM tb_listacompetencias WHERE idcompetencia='".$id."'";
    $result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result
    $total = mysql_num_rows($result);
    mysql_free_result($result);
}

function insertdato($id,$nombre,$abreviatura) {
    //1º) comprueba que existe el id
    $Sql="SELECT idcompetencia FROM tb_listacompetencias WHERE idcompetencia='".$id."'";
    $result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result
    $total = mysql_num_rows($result);
    mysql_free_result($result);

    if ($total<=0) {
		$Sql="INSERT INTO tb_listacompetencias (nombre, abreviatura) VALUES (";
		if ($nombre<>'') {$Sql.="'".$nombre."', ";} else {$Sql.="'-',";}
		if ($abreviatura<>'') {$Sql.="'".$abreviatura."', ";} else {$Sql.="'-',";}
		$Sql=substr($Sql,0,strlen($Sql)-2); 
		$Sql.=")"; 	
	     } else { // si no, hacemos un UPDATE
		$Sql="UPDATE tb_listacompetencias SET ";
		$Sql.="nombre='".$nombre."', ";	
		$Sql.="abreviatura='".$abreviatura."', ";
		$Sql= substr($Sql,0,strlen($Sql)-2);
		$Sql.=" WHERE idcompetencia='".trim($id)."'"; 
	     } // fin del if

     $link=Conectarse($bd);
     $result=mysql_query($Sql,$link); //ejecuta la consulta
     mysql_free_result($result);
}
?>
