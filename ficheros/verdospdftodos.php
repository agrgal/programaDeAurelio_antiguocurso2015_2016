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

// Empiezo el pdf
// Declaro variables
$title1 = 'Evaluación: '.dado_Id($bd,$_SESSION['tutevaluacion'],"nombreeval","tb_edicionevaluaciones","ideval");
$title2= 'Clases: '.$alumno['cadenaclases'];
$horafecha='Fecha: '.$calendario->fechaformateada($calendario->fechadehoy());
$horafecha.=' - Hora: '.$calendario->horactual();
$h=$horafecha;

$cabecera='<table style="width: 100%;"><tr><td rowspan="2" style="width: 20% text-align: left;">';
$cabecera.='<img width="15%" heigth="auto" src="../imagenes_plantilla/logo.png"></td>';
$cabecera.='<td rowspan="2" style="width: 5% text-align: left;"></td>';
$cabecera.='<td style="width: 80%; text-align: center;"><h1 class="titulo" style="font-size:15px;">'.$title1.' - '.$title2.'</h1></tr>';
$cabecera.='<tr><td style="width: 80%; text-align: center;"><h1 class="subtitulo" style="font-size:13px;">Por cada asignación (materia-profesor/a)</h1></td></tr></tr></table>';

$escribe="";

foreach ($asignaciones as $clave => $valor) {

$cad=obtenerdatosasignacion($bd,$valor);
// Número Alumnos de la ASIGNACIÓN que está en $_SESSION['contador']
$a2=obteneralumnosasignacion($bd,$valor);
$numa2=count($a2['idalumno']);
// diferencia entre el array principal
$noestan=array_diff($a2['idalumno'],$alumno['idalumno']);
$numnoestan=count($noestan);

if (isset($_GET['salto']) && $_GET['salto']==1 && $clave>0) { $escribe.="<PAGEBREAK>"; }

if ($clave>0) { $escribe.='<br>'; } // salto de línea si no es el primero

$escribe.='<table style="margin:2px auto; height: auto; text-align: justify; width: 100%;" border="1" cellpadding="0" cellspacing="0">';
$escribe.='<thead><tr>';
$escribe.='<th id="nombre" style="padding: 0.5em 0.2em; font-weight: bold; text-align: justify; background-color: #333333; color: white;">';
$escribe.=($clave+1).'.- '.cambiarnombre($cad['profesor'])." (".$cad['materia'].") ".'</th></tr></thead><tbody>';


$n=0; // conteo de alumnos que se ponen

for ($j=0;$j<$ii;$j++) { // por cada alumno/a

// Recupero de la base de datos
$link=Conectarse($bd); // y me conecto. //dependiendo del tipo recupero uno u otro.

// antiguo --> $Sql='SELECT items, profesor, materia, observaciones FROM tb_evaluacion WHERE unidad= "'.$_SESSION['tutoria'].'" AND materia="'.$materia['idmateria'][$clave].'" AND eval ="'.$_SESSION['tutevaluacion'].'" AND alumno ="'.$alumno['idalumno'][$j].'" AND (items<>"" OR observaciones<>"") ORDER BY profesor';

$Sql='SELECT items, observaciones,alumno, profesor,materia,asignacion  from tb_evaluacion inner join tb_asignaciones ON tb_evaluacion.asignacion=tb_asignaciones.idasignacion WHERE eval ="'.$_SESSION['tutevaluacion'].'" AND alumno ="'.$alumno['idalumno'][$j].'" AND asignacion="'.$valor.'" AND (items<>"" OR observaciones<>"") ORDER BY alumno';

$result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result
$totalFilas = mysql_num_rows($result);  

if ($totalFilas>0) { //principio del if. Me pone el nombre del alumno

$n++; // aumenta uno el número de alumnos del que se escribe

if ($n%2==0) { $colorcelda="#dddddd"; } else { $colorcelda="#fafafa"; } // colores de 

$escribe.='<tr><td style="text-align: justify; width:50%; font-weight: bold; font-size:15px; border: 0px solid white; border-top: 1px solid black;padding: 1em 1.5em 0.5em 1.5em; background-color: '.$colorcelda.';">'.cambiarnombre($alumno['alumno'][$j])." - ".$alumno['unidad'][$j].'</td></tr>';

// Hago el bucle
while ($row=mysql_fetch_array($result)) {
   $itemsobtenidos=explode("#",$row['items']);
   // $escribe.='<p text-align="justify"><b></u>Profesor/a </u>'.cambiarnombre(dado_Id($bd,$row['profesor'],"Empleado","tb_profesores","idprofesor")).' </b>:</p>';
   $complementos=""; $escribealumno="";
   foreach ($itemsobtenidos as $it) {
       $encontrar=array_search($it,$items['iditem']);
       if (!is_null($encontrar) && is_numeric($encontrar) && $items['positivo'][$encontrar]<=1) {	
	 $escribealumno.=$items['item'][$encontrar].' ['.strtoupper(substr($items['grupo'][$encontrar],0,3)).']. ';
       } else if (!is_null($encontrar) && is_numeric($encontrar) && $items['positivo'][$encontrar]>1) { // complementos
         $complementos.=$items['item'][$encontrar].' ['.strtoupper(substr($items['grupo'][$encontrar],0,3)).']. ';
       } // fin del if
   } // fin del foreach

$escribe.='<tr><td style="text-align: justify; border: 0px solid white; border-bottom: 1px solid black; padding: 0em 1.5em 1em 2em; background-color: '.$colorcelda.';">';

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

$escribe.='</td></tr>';

} // fin del while  
mysql_free_result($result); // acaba por cada profesor

} // fin del if
else {

} // por si quiero poner algo para los que estén vacíos... 

} // fin del for de cada alumno

$escribe.='</tbody></table>';
// $escribe.='<p style="text-align: center;"><img width="15%" heigth="auto" src="../imagenes_plantilla/hr-jon-lucas2.jpg"></p>';

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
  $escribe.='<p class="textonormal" style="text-align: justify;">'.$incorporar.'</p>';
}

} // fin del foreach

$mpdf->SetDisplayMode('fullpage');
$mpdf->shrink_tables_to_fit=0;

// LOAD a stylesheet
$stylesheet = file_get_contents('../css/verprint.css');
$mpdf->WriteHTML($stylesheet,1);	// The parameter 1 tells that this is css/style only and no body/html/text

$mpdf->SetHTMLHeader($cabecera); 
$mpdf->SetHTMLFooter('<p class="footer">Página  {PAGENO}/{nbpg} - '.$horafecha.'</p>');

// $mpdf->WriteHTML(utf8_encode($escribe));
include_once('watermark.php');
$mpdf->WriteHTML($escribe);
$mpdf->Output('../ficheros/informepormateriaprofesortodos.pdf','F');
// $path='../temporal/A.pdf';
// $mpdf->Output($path,'F');
header("Location: ../ficheros/descargaficheropdf.php?fichero=informepormateriaprofesortodos.pdf");
exit;

?>

