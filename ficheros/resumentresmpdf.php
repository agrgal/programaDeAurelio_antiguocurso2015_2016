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

$mpdf=new mPDF('es', 'A4-L', 0, '', 10, 10, 25, 15, 5, 5); // The last parameters are all margin values in millimetres: left-margin, right-margin, top-margin, bottom-margin, header-margin, footer-margin.

$mpdf->allow_charset_conversion=true;  // Set by default to TRUE
$mpdf->charset_in='windows-1252';
//==============================================================
//==============================================================
//==============================================================

// Necesito algunas variables
// obtiene arrays, por si hay que usarlos m�s de una vez
$alumno=obteneralumnosasignacion($bd,$_SESSION['asignacion']); // array para introducir datos de los alumnos
$ii=count($alumno['idalumno']);

$asignaciones = obtenerasignaciones($bd,$alumno['idalumno'],$_SESSION['tutevaluacion']); // pasa un array con los id de todos los alumnos
// obtiene un array con las distintas asignaciones que ESA EVALUACI�N, han sido calificadas
$jj=count($asignaciones);

$items=obteneritemsporSQL($bd,'SELECT * FROM tb_itemsevaluacion WHERE positivo<=1 ORDER BY grupo,iditem');
$kk=count($items['iditem']);

// Empiezo el pdf
// Declaro variables
$title1 = 'Evaluaci�n: '.dado_Id($bd,$_SESSION['tutevaluacion'],"nombreeval","tb_edicionevaluaciones","ideval");
$title2 = 'Clase: '.$alumno['cadenaclases'];
$horafecha='Fecha: '.$calendario->fechaformateada($calendario->fechadehoy());
$horafecha.=' - Hora: '.$calendario->horactual();
$h=$horafecha;

$cabecera='<table style="width: 100%;"><tr><td rowspan="2" style="width: 20% text-align: left;">';
$cabecera.='<img width="15%" heigth="auto" src="../imagenes_plantilla/logo.png"></td>';
$cabecera.='<td rowspan="2" style="width: 5% text-align: left;"></td>';
$cabecera.='<td style="width: 80%; text-align: center;"><h1 class="titulo" style="font-size:15px;">'.$title1.' - '.$title2.'</h1></tr>';
$cabecera.='<tr><td style="width: 80%; text-align: center;"><h1 class="subtitulo" style="font-size:13px;">Estad�stica de Items</h1></td></tr></tr></table>';

$escribe=""; 

$headertabla=array();
$datostabla=array();

// inicializa las cabeceras de las tablas 

// if (isset($_GET['salto']) && $_GET['salto']<>1) { $pdf->AddPage('L'); } // Si no salta, al menos pone un AddPage

// foreach ($asignaciones as $key => $asg1) { // por cada asignaci�n 

// $cad = obtenerdatosasignacion($bd,$asg1);

// inicializo la matriz datostabla, la pongo a cero
foreach ($items['item'] as $clave => $valor) {
   $datostabla[$clave][0]= $valor; // cabeceras de cada item
   for ($j=0;$j<$ii;$j++) { 
	$datostabla[$clave][$j+1]=0;
   }
} 

if (isset($_GET['salto']) && $_GET['salto']==1 && $key>0) { $escribe.='<PAGEBREAK>'; }

// $pdf->Cabecera($mat2); //materia que presenta
$headertabla[0]="Todas las materias";

for ($j=0;$j<$ii;$j++) { // principio del for

$headertabla[$j+1]=$alumno['idalumno'][$j]; // cabecera con los n�meros de cada alumno.

// Recupero de la base de datos
$link=Conectarse($bd); // y me conecto. //dependiendo del tipo recupero uno u otro.
//$Sql='SELECT items, profesor, materia, observaciones FROM tb_evaluacion WHERE unidad= "'.$_SESSION['tutoria'].'" AND materia="'.$materia['idmateria'][$k].'" AND eval ="'.$_SESSION['tutevaluacion'].'" AND alumno ="'.$alumno['idalumno'][$j].'" AND items<>"" ORDER BY profesor';

$Sql='SELECT items FROM tb_evaluacion WHERE eval ="'.$_SESSION['tutevaluacion'].'" AND alumno ="'.$alumno['idalumno'][$j].'" AND items<>""';

$result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result

// $pdf->Write(10,$Sql);

// Hago el bucle
while ($row=mysql_fetch_array($result)) { // por cada profesor tambi�n
$itemsobtenidos=explode("#",$row['items']);
// pongo profesorado
$clave=0;
   foreach ($itemsobtenidos as $it) {
       $encontrar=array_search($it,$items['iditem']); // si encuentra el item en la cadena $items['iditem'] devuelve la clave
       if (!is_null($encontrar) && is_numeric($encontrar)) {	
	   // $datostabla[$encontrar][$j+1]=1;
           // encuentra la clave en $items
           $datostabla[$encontrar][$j+1]++; //sumo uno al dato de la tabla
       }
   } // fin del foreach

} // fin del while  
mysql_free_result($result); // acaba por cada profesor

} // fin del for, por cada alumno 

// Selecciono distintas asignaciones, sus profesores y materias
$profesor="";
$datos=array();
$nn=0;
foreach($asignaciones as $valor) {
   $datos=obtenerdatosasignacion($bd,$valor); // datos de cada asignacion
   $profesor.=cambiarnombre($datos['profesor'])." (".$datos['materia'].") - ";
   unset($datos);
   $nn++; // suma uno al contador de profesores
}
$profesor=substr($profesor,0,strlen($profesor)-3);

// Estad�sticas
$positivos=0; $negativos=0;
foreach ($items['item'] as $clave => $valor) {
   $cuenta=0; $cuenta2=0; 
   for ($j=0;$j<$ii;$j++) {       
	if ($datostabla[$clave][$j+1]>0) { $cuenta++;}  
	if ($items['positivo'][$clave]==0 && $datostabla[$clave][$j+1]>0) { $negativo++;}
	if ($items['positivo'][$clave]==1 && $datostabla[$clave][$j+1]>0) { $positivo++;}
	$cuenta2+=$datostabla[$clave][$j+1];
   }
   $datostabla[$clave][0].=' ('.$cuenta.'-'.round((100*$cuenta/$ii),1).'% -'.round((100*$cuenta2/($ii*$nn)),1).'%)';
}

// Valoraci�n de la clase.
if (($positivo+$negativo)>0) {$valoracion=$positivo/($positivo+$negativo); } else {$valoracion=0;}
$valoracion = round(100*(2*$valoracion-1),2).'%';

/****************************************/
// escribe la cabecera
$porcentaje=round(74/$ii);
$escribe.='<table class="tablaest"><thead><tr>';
$titulo=str_replace(" ", "<br>", $headertabla[0]); // fuerza los saltos de l�nea entre palabras
$escribe.='<th class="ceroth">'.$titulo.'<br><br><span class="textonormal">(N� de alumnos: '.$ii.')</span></th>';
// $escribe.='<th class="ceroth"></th>';
$anchonombres="12px"; if ($ii>30) { $anchonombres="10px"; } //ancho de las celdas de los nombres
for ($j=0;$j<$ii;$j++) {   
  $escribe.='<th style="width: '.$porcentaje.'%;"><img src="../temporal/'.$headertabla[$j+1].'.png" width="'.$anchonombres.'"></th>'; //utilizo las im�genes giradas
}
$escribe.='</tr></thead><tbody>'; // termina de escribir la cabecera

// escribe los datos
foreach ($datostabla as $key => $vector) {

$escribe.='<tr>';
if ($key%2==1) { $escribe.='<td class="cerotd">'.$vector[0].'</td>'; } else { $escribe.='<td class="ceroimpartd">'.$vector[0].'</td>'; } 
for ($j=0;$j<$ii;$j++) { 
  if ($j%2==1) { $clase=""; } else { $clase='class="impartd"'; } // clases distintas seg�n el n� de alumnos.
  if ($vector[$j+1]>=1) { $x=$vector[$j+1]; } else { $x="";} 
  $escribe.='<td '.$clase.' style="width: '.$porcentaje.'%;">'.$x.'</td>';
}
$escribe.='</tr>';

} // fin del foreach
$escribe.='</tbody></table>'; 

// Estad�sticas y equipo educativo
$valoracion="Valoraci�n de la clase [-100%,+100%]= ".$valoracion;

$escribe.='<p class="textonormal">'.$valoracion." // Equipo educativo: ".$profesor.'</p>';

// } // fin del foreach de cada asignaci�n 

$mpdf->SetDisplayMode('fullpage');
$mpdf->shrink_tables_to_fit=0;

// LOAD a stylesheet
$stylesheet = file_get_contents('../css/verprint.css');
$mpdf->WriteHTML($stylesheet,1);	// The parameter 1 tells that this is css/style only and no body/html/text

$mpdf->SetHTMLHeader($cabecera); 
$mpdf->SetHTMLFooter('<p class="footer">P�gina  {PAGENO}/{nbpg} - '.$horafecha.' - Leyenda matem�tica: N� alumnos - % alumnos calificados con el item - %prevalencia</p>');

// $mpdf->WriteHTML(utf8_encode($escribe));
include_once('watermark.php');
$mpdf->WriteHTML($escribe);
$mpdf->Output('../ficheros/estadisticas.pdf','F');
// $path='../temporal/A.pdf';
// $mpdf->Output($path,'F');
header("Location: ../ficheros/descargaficheropdf.php?fichero=estadisticas.pdf");
exit;

?>

