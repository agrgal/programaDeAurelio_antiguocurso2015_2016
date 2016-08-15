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

// Empiezo el pdf
// Declaro variables
$title1 = 'Evaluación: '.dado_Id($bd,$_SESSION['tutevaluacion'],"nombreeval","tb_edicionevaluaciones","ideval");
$title2 = 'Datos por alumno_a de la/las clase/s de '.$alumno['cadenaclases'];
$horafecha='Fecha: '.$calendario->fechaformateada($calendario->fechadehoy());
$horafecha.=' - Hora: '.$calendario->horactual();
$h=$horafecha;

$cabecera='<table style="width: 100%;"><tr><td rowspan="2" style="width: 20% text-align: left;">';
$cabecera.='<img width="15%" heigth="auto" src="../imagenes_plantilla/logo.png"></td>';
$cabecera.='<td rowspan="2" style="width: 5% text-align: left;"></td>';
$cabecera.='<td style="width: 80%; text-align: center;"><h1 class="titulo" style="font-size:15px;">'.$title1.'</h1></tr>';
$cabecera.='<tr><td style="width: 80%; text-align: center;"><h1 class="subtitulo" style="font-size:13px;">'.$title2.'</h1></td></tr></tr></table>';

$escribe="";

// Recupero de la base de datos

foreach ($alumno['alumno'] as $clave => $alum) {

// Busca los items de este alumno
$link=Conectarse($bd); // y me conecto. //dependiendo del tipo recupero uno u otro.

// antiguo --> $Sql='SELECT items, profesor, materia, observaciones FROM tb_evaluacion WHERE unidad= "'.$_SESSION['tutoria'].'" AND alumno="'.$alumno['idalumno'][$clave].'" AND eval ="'.$_SESSION['tutevaluacion'].'" AND (items<>"" OR observaciones<>"") ORDER BY materia, profesor';

$Sql='SELECT items, observaciones,profesor,materia  from tb_evaluacion inner join tb_asignaciones ON tb_evaluacion.asignacion=tb_asignaciones.idasignacion WHERE alumno="'.$alumno['idalumno'][$clave].'" AND eval ="'.$_SESSION['tutevaluacion'].'" AND (items<>"" OR observaciones<>"") ORDER BY materia,profesor';

$result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result

$totalFilas = mysql_num_rows($result); 

if ($totalFilas>0) { //principio del if. Me pone el nombre del alumno 

// Escribe salto de página, siempre y cuando no sea el primero...
if (isset($_GET['salto']) && $_GET['salto']==1 && $clave>0) { $escribe.="<PAGEBREAK>";}

// $pdf->Cabecera(cambiarnombre($alum).' - '.$alumno['unidad'][$clave]);
$escribe.='<h2 class="cabecera">'.($clave+1).'.- '.cambiarnombre($alum).' - '.$alumno['unidad'][$clave].'</h2>';

while ($row=mysql_fetch_array($result)) {

$escribe.='<h2 class="cabecerados">'.'Profesor/a: '.cambiarnombre(dado_Id($bd,$row['profesor'],"Empleado","tb_profesores","idprofesor")).' - Materia: '.dado_Id($bd,$row['materia'],"Materias","tb_asignaturas","idmateria").'</h2>';

$escribe.='<p class="textonormal">';

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

$construye=array();

if ($escribealumno<>"") {
  $construye[0]="<b><i>Items:</i></b> ".$escribealumno;
}

if ($complementos<>"") {
  $construye[1]="<b><i>Datos complementarios:</i></b> ".$complementos;
}

if ($row['observaciones']<>"") {
  $construye[2]="<b><i>Observaciones:</i></b> ".strip_tags($row['observaciones']);
}

$escribe.=implode(" // ",$construye);
unset($construye);

} // fin del while


$escribe.='</p>';

$escribe.='<p style="text-align: center;"><img width="15%" heigth="auto" src="../imagenes_plantilla/hr-jon-lucas2.jpg"></p>';
// $pdf->Multicell(0,5,"----- o -----",0,'C');

} // fin del if de total filas
    
mysql_free_result($result);	

} // fin del for each de cada alumno_a

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
$mpdf->Output('../ficheros/informebtodoslosalumnos.pdf','F');
// $path='../temporal/A.pdf';
// $mpdf->Output($path,'F');
header("Location: ../ficheros/descargaficheropdf.php?fichero=informebtodoslosalumnos.pdf");
exit;

?>

