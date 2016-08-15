<?php
include_once("../funciones/seguridad/mysql_inc.php");
include_once("../funciones/adodb_time_inc.php");
include_once("../funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("../clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

session_start(); /* empiezo una sesión */

//==============================================================
//==============================================================
//==============================================================

include("../mpdf/mpdf.php");

$mpdf=new mPDF('es', 'A4', 0, '', 10, 10, 25, 15, 5, 5); // The last parameters are all margin values in millimetres: left-margin, right-margin, top-margin, bottom-margin, header-margin, footer-margin.

$mpdf->allow_charset_conversion=true;  // Set by default to TRUE
$mpdf->charset_in='windows-1252';

//==============================================================
//==============================================================
//==============================================================

$Sql = $_GET["sql"];
$itut = array();
$itut = obtenerdatosasignacion($bd,$_SESSION['asignacion']);
$alumno=obteneralumnosasignacion($bd,$_SESSION['asignacion']);

// Empiezo el pdf
// Declaro variables
// $title1 = 'Tutor/a: ';
$title1 = 'Tutor/a: '.cambiarnombre($itut['profesor']);
$title2 = 'Anotaciones de '.$alumno['cadenaclases'];
$horafecha='Fecha: '.iconv( "UTF-8","ISO-8859-1",$calendario->fechaformateadalarga($calendario->fechadehoy()));
$horafecha.=' - Hora: '.$calendario->horactual();
$h=$horafecha;

$cabecera='<table style="width: 100%;"><tr><td rowspan="2" style="width: 20% text-align: left;">';
$cabecera.='<img width="15%" heigth="auto" src="../imagenes_plantilla/logo.png"></td>';
$cabecera.='<td rowspan="2" style="width: 5% text-align: left;"></td>';
$cabecera.='<td style="width: 80%; text-align: center;"><h1 class="titulo" style="font-size:15px;">'.$title1.'</h1></tr>';
$cabecera.='<tr><td style="width: 80%; text-align: center;"><h1 class="subtitulo" style="font-size:13px;">'.$title2.'</h1></td></tr></tr></table>';

if (empty($Sql)) { $escribe="<h1>No hay datos</h1>";} else { $escribe="";}

// Recupero de la base de datos

// Busca los items de este alumno
$link=Conectarse($bd); // y me conecto. //dependiendo del tipo recupero uno u otro.

$result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result

$totalFilas = mysql_num_rows($result); 

if ($totalFilas>0) { //principio del if. Me pone el nombre del alumno 

$escribe=""; // reinicia la variable $escribe.

while ($row=mysql_fetch_array($result)) { 
 $key=array_search($row["alumno"],$alumno["idalumno"]);
 $estealumno=cambiarnombre($alumno["alumno"][$key])." - ".$alumno["unidad"][$key];
 $escribe.='<table style="margin:10px auto; height: auto; text-align: left; width: 90%;" border="1" cellpadding="1" cellspacing="1">';
$escribe.='<thead><tr>';
$escribe.='<th style="padding: 1em 1em; font-weight: bold; text-align: justify; background-color: #dddddd">';
 $infasignacion=obtenerdatosasignacion($bd,$row['asignacion']);
 $escribe.='<b>'.cambiarnombre($infasignacion["profesor"])." - ".$infasignacion["materia"]; 
 $escribe.=' ,el d&iacute;a '.iconv("UTF-8","ISO-8859-1", $calendario->fechaformateadalarga($row['fecha'])).'</b></th></tr>';
 $escribe.='<tr><td id="celda1" style="background-color: #eeeeee; color: black; padding:1em 1em;"><h3>'.iconv("ISO-8859-1", "UTF-8",$row['anotacion']).'</h3></td></tr>';
 $escribe.='</table>';

} // Fin del while

$cabeceraalumno='<h2 class="cabecera">'.$estealumno.'</h2>';
$escribe=$cabeceraalumno.$escribe;

} // fin del if de total filas

    
mysql_free_result($result);


$mpdf->SetDisplayMode('fullpage');
$mpdf->shrink_tables_to_fit=0;

// LOAD a stylesheet
$stylesheet = file_get_contents('../css/verprint.css');
$mpdf->WriteHTML($stylesheet,1);	// The parameter 1 tells that this is css/style only and no body/html/text

$mpdf->SetHTMLHeader($cabecera); 
include_once('watermark.php');
$mpdf->SetHTMLFooter('<p class="footer">Página  {PAGENO}/{nbpg} - '.$horafecha.'</p>');


// $mpdf->WriteHTML(utf8_encode($escribe));
$mpdf->WriteHTML($escribe);
$mpdf->Output('../ficheros/anotacionesalumnos.pdf','F');
// $path='../temporal/A.pdf';
// $mpdf->Output($path,'F');
header("Location: ../ficheros/descargaficheropdf.php?fichero=anotacionesalumnos.pdf");
exit;

?>

