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

$mpdf=new mPDF('es', 'A4-L', 0, '', 10, 10, 25, 15, 5, 5); // The last parameters are all margin values in millimetres: left-margin, right-margin, top-margin, bottom-margin, header-margin, footer-margin.

$mpdf->allow_charset_conversion=true;  // Set by default to TRUE
$mpdf->charset_in='windows-1252';
//==============================================================
//==============================================================
//==============================================================

// Necesito algunas variables
// obtiene arrays, por si hay que usarlos más de una vez
$alumno=obteneralumnosasignacion($bd,$_SESSION['asignacion']); // array para introducir datos de los alumnos
$ii=count($alumno['idalumno']);

$asignaciones = obtenerasignaciones($bd,$alumno['idalumno'],$_SESSION['tutevaluacion']); // pasa un array con los id de todos los alumnos
// obtiene un array con las distintas asignaciones que ESA EVALUACIÓN, han sido calificadas
$jj=count($asignaciones);

$items=obteneritemsporSQL($bd,'SELECT * FROM tb_itemsevaluacion WHERE positivo>1 ORDER BY grupo,iditem');
$kk=count($items['iditem']);

// Empiezo el pdf
// Declaro variables
$title1 = 'Evaluación: '.dado_Id($bd,$_SESSION['tutevaluacion'],"nombreeval","tb_edicionevaluaciones","ideval");
$title2 = 'Clase: '.$alumno['cadenaclases'];
$horafecha='Fecha: '.$calendario->fechaformateada($calendario->fechadehoy());
$horafecha.=' - Hora: '.$calendario->horactual();
$h=$horafecha;

$cabecera='<table style="width: 100%;"><tr><td rowspan="2" style="width: 20% text-align: left;">';
$cabecera.='<img width="15%" heigth="auto" src="../imagenes_plantilla/logo.png"></td>';
$cabecera.='<td rowspan="2" style="width: 5% text-align: left;"></td>';
$cabecera.='<td style="width: 80%; text-align: center;"><h1 class="titulo" style="font-size:15px;">'.$title1.' - '.$title2.'</h1></tr>';
$cabecera.='<tr><td style="width: 80%; text-align: center;"><h1 class="subtitulo" style="font-size:13px;">Tabla resumen ITEMS por materia-profesor</h1></td></tr></tr></table>';

$escribe=""; 

$headertabla=array();
$datostabla=array();

// inicializa las cabeceras de las tablas 

// if (isset($_GET['salto']) && $_GET['salto']<>1) { $pdf->AddPage('L'); } // Si no salta, al menos pone un AddPage

foreach ($asignaciones as $key => $asg1) { // por cada asignación 

 $cad = obtenerdatosasignacion($bd,$asg1);

// inicializo la matriz datostabla, la pongo a cero
foreach ($items['item'] as $clave => $valor) {
   $datostabla[$clave][0]= $valor; // cabeceras de cada item
   for ($j=0;$j<$ii;$j++) { 
	$datostabla[$clave][$j+1]=0;
   }
} 

if (isset($_GET['salto']) && $_GET['salto']==1 && $key>0) { $escribe.='<PAGEBREAK>'; }

// $pdf->Cabecera($mat2); //materia que presenta
$headertabla[0]=$cad['materia'];

for ($j=0;$j<$ii;$j++) { // principio del for

$headertabla[$j+1]=$alumno['idalumno'][$j]; // cabecera con los números de cada alumno.

// Recupero de la base de datos
$link=Conectarse($bd); // y me conecto. //dependiendo del tipo recupero uno u otro.
//$Sql='SELECT items, profesor, materia, observaciones FROM tb_evaluacion WHERE unidad= "'.$_SESSION['tutoria'].'" AND materia="'.$materia['idmateria'][$k].'" AND eval ="'.$_SESSION['tutevaluacion'].'" AND alumno ="'.$alumno['idalumno'][$j].'" AND items<>"" ORDER BY profesor';

$Sql='SELECT items, observaciones,alumno, profesor,materia,asignacion  from tb_evaluacion inner join tb_asignaciones ON tb_evaluacion.asignacion=tb_asignaciones.idasignacion WHERE eval ="'.$_SESSION['tutevaluacion'].'" AND alumno ="'.$alumno['idalumno'][$j].'" AND asignacion="'.$asg1.'" AND items<>"" ORDER BY asignacion';

$result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result

// $pdf->Write(10,$Sql);

// Hago el bucle
while ($row=mysql_fetch_array($result)) { // por cada profesor también
$itemsobtenidos=explode("#",$row['items']);
// pongo profesorado
$clave=0;
   foreach ($itemsobtenidos as $it) {
       $encontrar=array_search($it,$items['iditem']); // si encuentra el item en la cadena $items['iditem'] devuelve la clave
       if (!is_null($encontrar) && is_numeric($encontrar)) {	
	   // $datostabla[$encontrar][$j+1]=1;
           // encuentra la clave en $items
           $datostabla[$encontrar][$j+1]=1;
       }
   } // fin del foreach

} // fin del while  
mysql_free_result($result); // acaba por cada profesor

} // fin del for, por cada alumno 

// escribe la cabecera
$porcentaje=round(74/$ii);
$escribe.='<table class="tabla"><thead><tr>';
$titulo=str_replace(" ", "<br>", $headertabla[0]); // fuerza los saltos de línea entre palabras
$escribe.='<th class="ceroth">'.$titulo.'</th>';
// $escribe.='<th class="ceroth"></th>';
$anchonombres="12px"; if ($ii>30) { $anchonombres="10px"; } //ancho de las celdas de los nombres
for ($j=0;$j<$ii;$j++) {   
  $escribe.='<th style="width: '.$porcentaje.'%;"><img src="../temporal/'.$headertabla[$j+1].'.png" width="'.$anchonombres.'"></th>'; //utilizo las imágenes giradas
}
$escribe.='</tr></thead><tbody>'; // termina de escribir la cabecera

// escribe los datos
foreach ($datostabla as $key => $vector) {

$escribe.='<tr>';
if ($key%2==1) { $escribe.='<td class="cerotd">'.$vector[0].'</td>'; } else { $escribe.='<td class="ceroimpartd">'.$vector[0].'</td>'; } 
for ($j=0;$j<$ii;$j++) { 
  if ($j%2==1) { $clase=""; } else { $clase='class="impartd"'; } // clases distintas según el nº de alumnos.
  if ($vector[$j+1]==1) {$x='<img src="../imagenes_plantilla/ok.png" style="width: 10px; heigth: 10px;">';} 
    // else if ($vector[$j+1]==-1) {$x='<img src="../imagenes_plantilla/no.png" style="width: 10px; heigth: 10px;">';} 
    else {$x="";}
  $escribe.='<td '.$clase.' style="width: '.$porcentaje.'%;">'.$x.'</td>';
}
$escribe.='</tr>';

} // fin del foreach
$escribe.='</tbody></table>'; 

$escribe.='<br><p class="alaizquierda">'.ucfirst($cad['materia'].", impartida por: ".cambiarnombre($cad['profesor'])).'</p>';

} // fin del foreach de cada asignación 

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
$mpdf->Output('../ficheros/resumencomplementos.pdf','F');
// $path='../temporal/A.pdf';
// $mpdf->Output($path,'F');
header("Location: ../ficheros/descargaficheropdf.php?fichero=resumencomplementos.pdf");
exit;

?>

