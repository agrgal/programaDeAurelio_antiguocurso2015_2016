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

// Necesito algunas variables
// obtiene arrays, por si hay que usarlos más de una vez
$alumno=obteneralumnosasignacion($bd,$_SESSION['asignacion']); // array para introducir datos de los alumnos
$ii=count($alumno['idalumno']);

// $asignaciones = obtenerasignaciones($bd,$alumno['idalumno'],$_SESSION['tutevaluacion']); // pasa un array con los id de todos los alumnos, sólo a las que tienen datos de los alumnos 
$asignaciones = obtenerasignacionesdos($bd,$alumno['idalumno'],$_SESSION['tutevaluacion']); // pasa un array con los id de todos los alumnos, todas las asignaciones INCLUSO la que no tienen datos
// obtiene un array con las distintas asignaciones que ESA EVALUACIÓN, han sido calificadas
$jj=count($asignaciones);

// ordenar las asignaciones por materias
$vectorordenado=array(); //crea un array
foreach($asignaciones as $valor) {
   $cad=obtenerdatosasignacion($bd,$valor); //obtiene los datos de la asignación
   $vectorordenado[$cad['idmateria'].$cad['idprofesor']]=$valor; // nuevo valor al array PERO con el parámetro idmateria como clave primera y idprofesor segunda
}
ksort($vectorordenado); //ordena el vector por CLAVE, que son los índices de las materias y de los profesores.

// $items=obteneritemsporSQL($bd,'SELECT * FROM tb_itemsevaluacion WHERE positivo<=1 ORDER BY grupo,iditem');
// $kk=count($items['iditem']);

// Empiezo el pdf
// Declaro variables
$title1 = 'Clase: '.$alumno['cadenaclases'];
$title2 = 'Evaluación: '.dado_Id($bd,$_SESSION['tutevaluacion'],"nombreeval","tb_edicionevaluaciones","ideval");
$horafecha='Fecha: '.$calendario->fechaformateada($calendario->fechadehoy());
$horafecha.=' - Hora: '.$calendario->horactual();

$cabecera='<table style="width: 100%;"><tr><td rowspan="2" style="width: 20% text-align: left;">';
$cabecera.='<img width="15%" heigth="auto" src="../imagenes_plantilla/logo.png"></td>';
$cabecera.='<td rowspan="2" style="width: 5% text-align: left;"></td>';
$cabecera.='<td style="width: 80%; text-align: center;"><h1 class="titulo" style="font-size:15px;">'.$title1.' - '.$title2.'</h1></tr>';
$cabecera.='<tr><td style="width: 80%; text-align: center;"><h1 class="subtitulo" style="font-size:13px;">Opinión general de la clase</h1></td></tr></tr></table>';

$escribe=""; // inicializo la variable que escribirá las tablas
$profesor=array(); // array de profesores.Inicializa

// $cabecera.='<div style="position: absolute; left:0; right: 0; top: 0; bottom: 0;"><img src="../imagenes_plantilla/logo.png" style="width: 25mm; height: 8mm; margin: 0;"/></div>';


foreach ($vectorordenado as $key => $valor) { // por cada parámetro de asignación

$link=Conectarse($bd); // y me conecto. //dependiendo del tipo recupero uno u otro.
// recupera los datos
$Sql='SELECT opinion,actuaciones,mejora from tb_opiniongeneral WHERE eval ="'.$_SESSION['tutevaluacion'].'" AND asignacion="'.$valor.'"';

$result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result

if (mysql_num_rows($result)>0) { // Si hay filas afectadas

$cad=obtenerdatosasignacion($bd,$valor); // información de la asignación por la que ha buscado
$profesor[]=$cad['idprofesor']; // matriz de profesores para usarla posteriormente

while ($row=mysql_fetch_array($result)) { // por cada fila de la tabla

$col=max(strlen(html_entity_decode($row['opinion'])),strlen(html_entity_decode($row['actuaciones'])),strlen(html_entity_decode($row['mejora'])));
if ($col>200) {$col1="10%"; $col2="85%";} else {$col1="40%"; $col2="55%";}

$escribe.='<table style="margin:2px auto; height: auto; text-align: center; width: 100%;" border="1" cellpadding="1" cellspacing="1">';
$escribe.='<thead><tr>';
$escribe.='<th colspan="2" id="nombre" style="padding: 0.5em 0.2em; font-weight: bold; text-align: justify; background-color: #dddddd">';
$escribe.='<b>Profesor/a: </b>'.cambiarnombre($cad['profesor']).' - <b>Materia: </b>'.$cad['materia'].'</th>';
// $escribe.='<th id="firma" style="padding: 0.5em 0.2em; width: 33%%; font-weight: bold; text-align: justify;">Firma: </th>';
$escribe.='</tr></thead>';
      if (strlen($row['opinion'])>0) {
      $escribe.='<tr>';
        $escribe.='<td id="celda1" style="width: '.$col1.'; background-color: #888888; color: white; padding:0.5em 0em;"><b>Opinión general</b></td>';
        $escribe.='<td id="" style="width: '.$col2.'; text-align: justify; padding: 0.5em 0.5em;">'.iconv("ISO-8859-1", "UTF-8",$row['opinion']).'</td>';	
      $escribe.='</tr>'; }
      if (strlen($row['actuaciones'])>0) {
      $escribe.='<tr>';
        $escribe.='<td id="celda2" style="width: '.$col1.'; background-color: #888888; color: white; padding:0.5em 0em;"><b>Actuaciones llevadas a cabo</b></td>';
	$escribe.='<td id="" style="width: '.$col2.'; text-align: justify; padding: 0.5em 0.5em;">'.iconv("ISO-8859-1", "UTF-8",$row['actuaciones']).'</td>';
      $escribe.='</tr>'; }
      if (strlen($row['mejora'])>0) {
      $escribe.='<tr>';
	$escribe.='<td id="celda3" style="width: '.$col1.'; background-color: #888888; color: white; padding:0.5em 0em;"><b>Propuestas de mejora</b></td>';
	$escribe.='<td id="" style="width: '.$col2.'; text-align: justify; padding: 0.5em 0.5em;">'.iconv("ISO-8859-1", "UTF-8",$row['mejora']).'</td>';
      $escribe.='</tr>'; }
$escribe.='</table><br>';

} 

mysql_free_result($result); // acaba por cada profesor

} // fin del if 
 
} // fin del foreach de cada asignación

// de nuevo para las firmas
$profesor=array_unique($profesor);
asort($profesor); //quita duplicados y ordena
$j=0;
$ancho=3;
if ($jj<$ancho) {$ancho=$jj; } // si hay menos que el ancho entonces que sea ese núnero
$escribe.='<table style="margin:2px auto; height: auto; text-align: center; width: 100%;" border="0" cellpadding="5" cellspacing="5">';
$escribe.='<thead><tr><th colspan="'.$ancho.'" style="background-color: #dddddd;"><h2 class="subtitulo">Firma de los profesores/as del equipo educativo</h2></th></thead></tr><tr cellpadding="5" cellspacing="5">';
foreach ($profesor as $valor) {
     $columna=$j % ($ancho+1);
     $escribe.='<td style="border: 1px solid black; padding-bottom: 3em;">';
     $escribe.=cambiarnombre(dado_Id($bd,$valor,"Empleado","tb_profesores","idprofesor"))." ";
     $escribe.='</td>';
     if($columna==$ancho-1) {$escribe.='</tr><tr cellpadding="5" cellspacing="5">'; }  
     $j++;
} // fin del foreach de profesores
$escribe.='</tr></table>';   // fin de la tabla 

$mpdf->SetDisplayMode('fullpage');
$mpdf->shrink_tables_to_fit=0;

// LOAD a stylesheet
$stylesheet = file_get_contents('../css/verprint.css');
$mpdf->WriteHTML($stylesheet,1);	// The parameter 1 tells that this is css/style only and no body/html/text

$mpdf->SetHTMLHeader($cabecera); 
$mpdf->SetHTMLFooter('<p class="footer">Página  {PAGENO}/{nbpg} - '.$horafecha.'</p>');

include_once('watermark.php');
// $mpdf->WriteHTML(utf8_encode($escribe));
$mpdf->WriteHTML($escribe);
$mpdf->Output('../ficheros/opiniongeneral.pdf','F');
// $path='../temporal/A.pdf';
// $mpdf->Output($path,'F');
header("Location: ../ficheros/descargaficheropdf.php?fichero=opiniongeneral.pdf");
exit;

?>

