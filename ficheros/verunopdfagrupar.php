<?php
include_once("../funciones/seguridad/mysql_inc.php");
include_once("../funciones/adodb_time_inc.php");
include_once("../funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("../clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

session_start(); /* empiezo una sesi�n */

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
// obtiene arrays, por si hay que usarlos m�s de una vez
$alumno=obteneralumnosasignacion($bd,trim($_SESSION['asignacion'])); // array para introducir datos de los alumnos
$ii=count($alumno['idalumno']);
$items=obteneritems($bd);

// Empiezo el pdf
// Declaro variables
$title1 = 'Evaluaci�n: '.dado_Id($bd,$_SESSION['tutevaluacion'],"nombreeval","tb_edicionevaluaciones","ideval");
$title2= 'Clases: '.$alumno['cadenaclases'];
$horafecha='Fecha: '.$calendario->fechaformateada($calendario->fechadehoy());
$horafecha.=' - Hora: '.$calendario->horactual();
$h=$horafecha;

$cabecera='<table style="width: 100%;"><tr><td rowspan="2" style="width: 20% text-align: left;">';
$cabecera.='<img width="15%" heigth="auto" src="../imagenes_plantilla/logo.png"></td>';
$cabecera.='<td rowspan="2" style="width: 5% text-align: left;"></td>';
$cabecera.='<td style="width: 80%; text-align: center;"><h1 class="titulo" style="font-size:15px;">'.cambiarnombre($alumno['alumno'][$_SESSION['contador']]).' - '.$alumno['unidad'][$_SESSION['contador']].'</h1></tr>';
$cabecera.='<tr><td style="width: 80%; text-align: center;"><h1 class="subtitulo" style="font-size:13px;">'.$title1.'</h1></td></tr></tr></table>';
// $cabecera.='<h2 class="subtitulo" style="font-size:14px;">'.$alumno['unidad'][$_SESSION['contador']].'</h2></td></tr></table>';

$link=Conectarse($bd); // y me conecto. //dependiendo del tipo recupero uno u otro.

// $Sql='SELECT items, profesor, materia, observaciones FROM tb_evaluacion WHERE unidad= "'.$_SESSION['tutoria'].'" AND alumno="'.$alumno['idalumno'][$_SESSION['contador']].'" AND eval ="'.$_SESSION['tutevaluacion'].'" AND (items<>"" OR observaciones<>"") ORDER BY materia, profesor';

$Sql='SELECT items, observaciones,profesor,materia  from tb_evaluacion inner join tb_asignaciones ON tb_evaluacion.asignacion=tb_asignaciones.idasignacion WHERE alumno="'.$alumno['idalumno'][$_SESSION['contador']].'" AND eval ="'.$_SESSION['tutevaluacion'].'" AND (items<>"" OR observaciones<>"") ORDER BY materia,profesor';

$result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result

$escribe="";

$n=0; // n� de profesores

while ($row=mysql_fetch_array($result)) {

$escribe.='<h2 class="cabecera">'.($n+1).'.- '.'Profesor/a: '.cambiarnombre(dado_Id($bd,$row['profesor'],"Empleado","tb_profesores","idprofesor")).' - Materia: '.dado_Id($bd,$row['materia'],"Materias","tb_asignaturas","idmateria").'</h2>';

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

if ($escribealumno<>"") {
  $escribe.="<b><i>Items:</i></b> ".$escribealumno." // ";
}

if ($complementos<>"") {
  $escribe.="<b><i>Datos complementarios:</i></b> ".$complementos." // ";
}

if ($row['observaciones']<>"") {
  $escribe.="<b><i>Observaciones:</i></b> ".strip_tags($row['observaciones']);
}

$final = substr($escribe,-4);
if ($final==" // ") { $escribe=substr($escribe,0,strlen($escribe)-4); }

$escribe.='<p style="text-align: center;"><img width="15%" heigth="auto" src="../imagenes_plantilla/hr-jon-lucas2.jpg"></p>';

$n++; // aumenta el contador de profesores

} // fin del while
    
mysql_free_result($result);	

$escribe.='</p>'; 

$mpdf->SetDisplayMode('fullpage');
$mpdf->shrink_tables_to_fit=0;

// LOAD a stylesheet
$stylesheet = file_get_contents('../css/verprint.css');
$mpdf->WriteHTML($stylesheet,1);	// The parameter 1 tells that this is css/style only and no body/html/text

$mpdf->SetHTMLHeader($cabecera); 
$mpdf->SetHTMLFooter('<p class="footer">P�gina  {PAGENO}/{nbpg} - '.$horafecha.'</p>');

include_once('watermark.php');
// $mpdf->WriteHTML(utf8_encode($escribe));
$mpdf->WriteHTML($escribe);
$mpdf->Output('../ficheros/informebporalumno.pdf','F');
// $path='../temporal/A.pdf';
// $mpdf->Output($path,'F');
header("Location: ../ficheros/descargaficheropdf.php?fichero=informebporalumno.pdf");
exit;
?>

