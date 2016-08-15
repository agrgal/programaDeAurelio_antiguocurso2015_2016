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
$alumno=obteneralumnosasignacion($bd,trim($_SESSION['asignacion'])); // array para introducir datos de los alumnos
$ii=count($alumno['idalumno']);
$items=obteneritems($bd);

// obtengo la informacion de la asignacion
$infasignacion=obtenerdatosasignacion($bd,$_SESSION['asignacion']);
// $cl1=clasesimplicadasasignacion($bd,$_SESSION['asignacion']);

// Empiezo el pdf
// Declaro variables
$title1 = 'Evaluación: '.dado_Id($bd,$_SESSION['evaluacion'],"nombreeval","tb_edicionevaluaciones","ideval");
$title2= 'Clases: '.$alumno['cadenaclases'];
$horafecha='Fecha: '.$calendario->fechaformateada($calendario->fechadehoy());
$horafecha.=' - Hora: '.$calendario->horactual();
$h=$horafecha;

$cabecera='<table style="width: 100%;"><tr><td style="width: 20% text-align: left;">';
$cabecera.='<img width="15%" heigth="auto" src="../imagenes_plantilla/logo.png"></td>';
$cabecera.='<td style="width: 5% text-align: left;"></td>';
$cabecera.='<td style="width: 80%; text-align: center;"><h1 class="titulo" style="font-size:15px;">'.$infasignacion['materia'].'</h1>';
$cabecera.='<h2 class="subtitulo" style="font-size:14px;">'.$title1.' - '.$title2.'</h2></td></tr></table>';

$escribe=""; // inicializo la variable que escribirá las tablas

for ($j=0;$j<$ii;$j++) { 

// Recupero de la base de datos

$link=Conectarse($bd); // y me conecto. //dependiendo del tipo recupero uno u otro.

# $Sql='SELECT items, profesor, materia, observaciones FROM tb_evaluacion WHERE unidad= "'.$_SESSION['unidad'].'" AND materia="'.$_SESSION['materia'].'" AND eval ="'.$_SESSION['evaluacion'].'" AND alumno ="'.# $alumno['idalumno'][$j].'" AND items<>"" ORDER BY profesor';

$Sql='SELECT items, asignacion, alumno, observaciones FROM tb_evaluacion WHERE asignacion= "'.$_SESSION['asignacion'].'" AND eval ="'.$_SESSION['evaluacion'].'" AND alumno ="'.$alumno['idalumno'][$j].'" AND (items<>"" OR observaciones<>"")';

$result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result

$totalFilas = mysql_num_rows($result); 

if ($totalFilas>0) { //principio del if. Me pone el nombre del alumno 

$escribe.='<h2 class="cabecera">'.($j+1).'.- '.cambiarnombre($alumno['alumno'][$j]).'</h2>';

$escribe.='<p class="textonormal">';

// Hago el bucle
while ($row=mysql_fetch_array($result)) {

$itemsobtenidos=explode("#",$row['items']);
   $escribealumno="";
   $complementos="";
   foreach ($itemsobtenidos as $it) {
       $encontrar=array_search($it,$items['iditem']);
       if (!is_null($encontrar) && is_numeric($encontrar) && $items['positivo'][$encontrar]<=1) {	
	 $escribealumno.=$items['item'][$encontrar].' ['.strtoupper(substr($items['grupo'][$encontrar],0,3)).']. ';
       } else if (!is_null($encontrar) && is_numeric($encontrar) && $items['positivo'][$encontrar]>1) { // complementos
         $complementos.=$items['item'][$encontrar].' ['.strtoupper(substr($items['grupo'][$encontrar],0,3)).']. ';
       } // fin del if
   } // fin del foreach

if ($escribealumno<>"") {
  $escribe.="<b><i>Items:</i></b> ".$escribealumno."<br>";
}

if ($complementos<>"") {
  $escribe.="<b><i>Datos complementarios:</i></b> ".$complementos."<br>";
}

if ($row['observaciones']<>"") {
  $escribe.="<b><i>Observaciones:</i></b> ".strip_tags($row['observaciones'])."<br>";
}

} // fin del while  

mysql_free_result($result); // acaba por cada profesor

$escribe.='<p style="text-align: center;"><img width="15%" heigth="auto" src="../imagenes_plantilla/hr-jon-lucas2.jpg"></p>';

} // fin del if
else {

} // por si quiero poner algo para los que estén vacíos... 

} // fin del for 

$escribe.='</p>';

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
$mpdf->Output('../ficheros/informeparaprofesor.pdf','F');
// $path='../temporal/A.pdf';
// $mpdf->Output($path,'F');
header("Location: ../ficheros/descargaficheropdf.php?fichero=informeparaprofesor.pdf");
exit;
?>

