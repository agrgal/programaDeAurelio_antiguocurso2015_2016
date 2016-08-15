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
// obtiene arrays, por si hay que usarlos más de una vez. Alumnos de la TUTORÍA
$alumno=obteneralumnosasignacion($bd,$_SESSION['asignacion']); // array para introducir datos de los alumnos
$ii=count($alumno['idalumno']);

// conjunto de items
$items=obteneritems($bd);

// Obtengo la lista de asignaciones que cursan los alumnos de MI TUTORíA
$asignaciones = obtenerasignaciones($bd,$alumno['idalumno'],$_SESSION['tutevaluacion']); // pasa un array con los id de todos los alumnos
// obtiene un array con las distintas asignaciones que ESA EVALUACIÓN, han sido calificadas
$jj=count($asignaciones);

// Número Alumnos de la ASIGNACIÓN que está en $_SESSION['contador']
$a2=obteneralumnosasignacion($bd,$asignaciones[$_SESSION['contador']]);
$numa2=count($a2['idalumno']);

$noestan=array_diff($a2['idalumno'],$alumno['idalumno']);
$numnoestan=count($noestan);

// Empiezo el pdf
// Declaro variables
$title1 = 'Evaluación: '.dado_Id($bd,$_SESSION['tutevaluacion'],"nombreeval","tb_edicionevaluaciones","ideval");
$title2= 'Clases: '.$alumno['cadenaclases'];
$horafecha='Fecha: '.$calendario->fechaformateada($calendario->fechadehoy());
$horafecha.=' - Hora: '.$calendario->horactual();
$h=$horafecha;
$cad = obtenerdatosasignacion($bd,$asignaciones[$_SESSION['contador']]);

$cabecera='<table style="width: 100%;"><tr><td style="width: 20% text-align: left;">';
$cabecera.='<img width="15%" heigth="auto" src="../imagenes_plantilla/logo.png"></td>';
$cabecera.='<td style="width: 5% text-align: left;"></td>';
$cabecera.='<td style="width: 80%; text-align: center;"><h1 class="titulo" style="font-size:15px;">'.cambiarnombre($cad['profesor'])." (".$cad['materia'].") ".'</h1></td></tr></table>';

for ($j=0;$j<$ii;$j++) { // por cada alumno

// Recupero de la base de datos
$link=Conectarse($bd); // y me conecto. //dependiendo del tipo recupero uno u otro.

// antiguo--> $Sql='SELECT items, profesor, materia, observaciones FROM tb_evaluacion WHERE unidad= "'.$_SESSION['tutoria'].'" AND materia="'.$materia['idmateria'][$_SESSION['contador']].'" AND eval ="'.$_SESSION['tutevaluacion'].'" AND alumno ="'.$alumno['idalumno'][$j].'" AND (items<>"" OR observaciones<>"") ORDER BY profesor';

$Sql='SELECT items, observaciones,alumno, profesor,materia,asignacion  from tb_evaluacion inner join tb_asignaciones ON tb_evaluacion.asignacion=tb_asignaciones.idasignacion WHERE eval ="'.$_SESSION['tutevaluacion'].'" AND alumno ="'.$alumno['idalumno'][$j].'" AND asignacion="'.$asignaciones[$_SESSION['contador']].'" AND (items<>"" OR observaciones<>"") ORDER BY alumno';

$result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result
$totalFilas = mysql_num_rows($result);  

if ($totalFilas>0) { //principio del if. Me pone el nombre del alumno

$escribe.='<h2 class="cabecera">'.($j+1).'.- '.cambiarnombre($alumno['alumno'][$j])." - ".$alumno['unidad'][$j].'</h2>';

$escribe.='<p class="textonormal">';

while ($row=mysql_fetch_array($result)) {

// $pdf->SetFont('Times','B',13);
// $pdf->Cell(0,5,'Profesor/a: '.cambiarnombre(dado_Id($bd,$row['profesor'],"Empleado","tb_profesores","idprofesor")),0,1);
// $pdf->Ln(1);
// $pdf->SetFont('Times','',13);

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

// $pdf->Multicell(0,5,$escribealumno,0,'J');

if ($escribealumno<>"") {
  $escribe.="<b><i>Items:</i></b> ".$escribealumno."<br>";
}

if ($complementos<>"") {
  $escribe.="<b><i>Datos complementarios:</i></b> ".$complementos."<br>";
}

if ($row['observaciones']<>"") {
  $escribe.="<b><i>Observaciones:</i></b> ".strip_tags($row['observaciones'])."<br>";
}

$escribe.='</p>'; 

} // fin del while  
mysql_free_result($result); // acaba por cada profesor

$escribe.='<p style="text-align: center;"><img width="15%" heigth="auto" src="../imagenes_plantilla/hr-jon-lucas2.jpg"></p>';

} // fin del if

else {

} // por si quiero poner algo para los que estén vacíos...

} // fin del for por cada alumno

// $escribe.= '<p>'.$numnoestan.' <> '.$numa2.'</p>';

$incorporar="";
if ($numnoestan>0 && $ii>0) {
$incorporar.='<b>En mi tutoría, Nº de alumnos CON esta asignatura y este profesora/a: </b>'.($numa2-$numnoestan).' - '.(round(100*($numa2-$numnoestan)/$ii,2)).'%<br>';
$incorporar.='<b>En mi tutoría, Nº de alumnos que NO TIENEN esta asignatura y este profesora/a: </b>'.($ii-$numa2+$numnoestan).'<br>';
}
if ($numnoestan>0) { $incorporar.="<b>En esta asignatura con este profesor/a hay ".$numnoestan." alumnos/as más que no pertenecen a esta tutoría: </b>";}
foreach ($noestan as $key => $valor) {
  $incorporar.=cambiarnombre($a2['alumno'][$key])." - ".$a2['unidad'][$key]." // ";
}
if ($incorporar<>"") {
  $incorporar=substr($incorporar,0,strlen($incorporar)-4);
  $escribe.='<p class="cabecerados" style="text-align: justify;">'.$incorporar.'</p>';
}

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
$mpdf->Output('../ficheros/informepormateriaprofesor.pdf','F');
// $path='../temporal/A.pdf';
// $mpdf->Output($path,'F');
header("Location: ../ficheros/descargaficheropdf.php?fichero=informepormateriaprofesor.pdf");
exit;

?>

