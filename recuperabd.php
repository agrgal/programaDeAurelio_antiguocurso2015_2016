<?
include_once("./funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("./clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

$iz = "left: 120px;" ; // posición de los campos a la izquierda

session_start();

// if ($_SESSION["cargado"]=="ya") {
//   header("Location: ./edicionrecuperaprofesores.php");
// }
//datos del arhivo
$nombre_archivo = $_FILES['userfile']['name']; 

?>
<html>
<head>
<title>Recupera datos de una COPIA DE SEGURIDAD</title>
<meta http-equiv="Content-Type" content="Type=text/html; charset=iso-8859-15">

<!-- Incluyo las diferentes hojas de estilo -->
<?php include_once("./listas_de_css.php"); ?>
</head>
<body>
<!-- Titular -->
<div id="contenedor">
<div id="titular" onmouseover="javascript: ocultar()"></div>

<!-- Capa de menú: navegación de la página -->
<?php include_once("./lista.php"); ?>

<!-- Capa de información -->
<div id="informacion" onmouseover="javascript: ocultar()"> 
<h1>Recupera un fichero copia de la base de datos</h1>
<p>Elige un fichero copia de la base de datos que hayas creado anteriormente. </p>
<?php if (!isset($nombre_archivo) || strlen($nombre_archivo)<=0 || $_SESSION['cargadotres']=="ya") { $_SESSION['cargadotres']="no"; // recupera un archivo?>
<form action="./recuperabd.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="MAX_FILE_SIZE" value="50000000"> <!-- Pongo unos 50 MB-->
    <p>Enviar un nuevo archivo: 
    <input name="userfile" type="file">
    <input type="submit" value="Enviar"></p>
</form> 
<?php } else { // Si lo encuentra, lo pone en la carpeta FICHEROS con el nombre profesores.CSV
  //datos del arhivo
  $tipo_archivo = $_FILES['userfile']['type'];
  $tamano_archivo = $_FILES['userfile']['size'];
  $ruta_temporal = $_FILES['userfile']['tmp_name'];

  echo '<div id="listacampos"><ul>';
  // compruebo si las características del archivo son las que deseo
  // echo '<p>'.$nombre_archivo.' - '.strpos($nombre_archivo, ".sql").' - '.$ruta_temporal.'</p>';
  if (strpos($nombre_archivo, ".sql")<=0 || is_null(strpos($nombre_archivo, ".sql"))) {
    echo "<li>La extensión del archivo no es correcta.Sólo se permiten archivos de extensión SQL.</li>";
  } else {

    // 1º) Si encuentra un archivo de ese tipo lo borra
    $nombre="copiabd.sql";
    $ruta="ficheros";    
    $enlace="./".$ruta."/".$nombre;         
    if (file_exists($enlace)==true) { // Si anteriormente hay un fichero con ese nombre, lo borra
	 chmod($enlace,0777);
         unlink($enlace);
         echo '<li>Borrada una copia previa del fichero...</li>';
    }
 
   // 2º) Carga el fichero
    if (is_uploaded_file($ruta_temporal)) {
       echo '<li>Cargado el fichero con el nombre de COPIABD.SQL a la carpeta FICHEROS...</li>';
       copy($ruta_temporal,$enlace); // carga el fichero en la carpeta FICHEROS    
       chmod($enlace,0777);

    // 3º) Reconoce el fichero
       // $lineas=file("./ficheros/profesores.csv");
       $_SESSION['cargadotres']="ya";
    
    // 4º) Borra la base de datos y la vuelve a crear con el mismo nombre...
	       $link=Conectarse($bd); // y me conecto. //dependiendo del tipo recupero uno u otro.
	       $Sql="DROP DATABASE IF EXISTS ".$bd;
	       // echo $Sql;
	       $result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result
	       mysql_free_result($result);
       echo '<li>Borrada la base de datos antigua...</li>';

     // CREARLA
	       $retornaresultado=crear($bd); // y me conecto. //dependiendo del tipo recupero uno u otro.
	       // $Sql="CREATE DATABASE ".$bd;
	       // echo $Sql;
	       // $result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result
	       // mysql_free_result($result);	
       echo '<li>Vuelvo a crear la base de datos...</li>';	
    
    // 5º) Restaura los valores desde el fichero
       $ejecuta = "mysql -u".$mysql_login." -p".$mysql_pass." ".$bd." < ".$enlace; // con '<' restaura...
       exec($ejecuta);
       // echo $ejecuta;
       echo '<li>Tablas y datos restaurados...</li>';
       
    // 6º) Vuelve a borrar el fichero
       unlink($enlace); 

 
    } // fin del if de reconocimiento y carga del fichero  
    
  } // fin del if que reconoce si es un archivo correcto

  echo '</ul></div>'; 
 
} // fin del else principal

unset($nombre_archivo); 
?>



</div> <!-- fin de la capa de información -->

</div> <!-- Fin de la capa del contenedor -->

<div id="fecha">
  	<p>
	<? 
	echo 'Fecha: '.$calendario->fechaformateada($calendario->fechadehoy());
	echo '. Hora: '.$calendario->horactual();
	?></p>
</div>

</body>
</html>

