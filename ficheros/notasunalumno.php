<?php
include_once("../funciones/seguridad/mysql_inc.php");
include_once("../funciones/adodb_time_inc.php");
include_once("../funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("../clases/class.micalendario.php");
include_once("../clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

set_time_limit(90); // tiempo en segundos de espera de ejecución del script

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

$title1 = $_POST["titulouno"];
$title2 = $_POST["titulodos"];
$horafecha='Fecha: '.$calendario->fechaformateada($calendario->fechadehoy());
$horafecha.=' - Hora: '.$calendario->horactual();
$h=$horafecha;

$cabecera='<table style="width: 100%;"><tr><td rowspan="3" style="width: 30%; text-align: left;">';
$cabecera.='<img width="28%" heigth="auto" src="../imagenes_plantilla/logo.png"></td>';
$cabecera.='<td style="width: 70%; text-align: left;"><h1 class="titulo" style="font-size:16px;">'.$title1.' - '.$title2.'</h1></td></tr></table>';

$recupera = json_decode($_POST["contenido"],true);

if ($recupera["notatotal"]<5) 
   { $colorsuspenso = 'bgcolor="tomato"'; } else { $colorsuspenso = 'bgcolor="lightgreen"'; } 

if ($recupera["notaseneca"]<5) 
   { $colorsuspensoseneca = 'bgcolor="tomato"'; } else { $colorsuspensoseneca = 'bgcolor="lightgreen"'; } 

$escribe.='<br><br><h1 class="titulo" style="font-size:18px; text-align: left;">Alumno/a: '.cambiarnombre($_POST["alumno"]).'</h1>';
$escribe.='<h1 class="titulo" style="font-size:18px; text-align: left;">'.cambiarnombre($_POST["evaluacion"]).'</h1><br>';
$escribe.= '<table width="60%" class="tabla2"><thead>';
  $escribe.= '<tr>';
  $escribe.= '<th width="10%">Nota</th>';
  $escribe.= '<th '.$colorsuspenso.' width="20%">'.$recupera["notatotal"].'</th>';
  $escribe.= '<th width="10%">'.utf8_encode("Calificación").'</th>';
  $escribe.= '<th '.$colorsuspensoseneca.'  width="20%">'.$recupera["notaseneca"].'</th>';
  $escribe.= '</tr>';
$escribe.= '</thead></table>';

$foto = obtenfoto($_POST["idalumno"]);

$escribe.='
<div id="foto" style="margin: 1px 1px 1px 25px; position: absolute; top:100px; right:35px; height: 85px; float: none; border:0px solid black; padding: 2px;">
<img id="fotografia" style="border: 2px solid #C37508; display: auto;" height="100px" src="'.$foto.'"></div> ';

$escribe.='<br><br><h1 class="titulo" style="font-size:18px; text-align: left;">'.utf8_encode("Lista de instrumentos evaluativos").'</h1>';
$escribe.= '<table width="100%" class="tabla2"><thead>';
  $escribe.= '<tr>';
  $escribe.= '<th width="60%">Nombre Instrumento Evaluativo</th>';
  $escribe.= '<th width="14%">Abreviatura</th>';
  $escribe.= '<th width="13%">'.utf8_encode("Contribución nota").'</th>';
  $escribe.= '<th width="13%">Media</th>';
  $escribe.= '</tr>';
$escribe.= '</thead><tbody>';

foreach ($recupera["poriev"] as $key => $valor) {
  if ($key%2==1) { $clase=' class="cerotd"'; } else { $clase=' class="ceroimpartd"'; } 
  if (!empty($valor["nota"])) {
  $escribe.= '<tr>';
  $escribe.= '<td '.$clase.' style="text-align: left;">'.$valor["nombreiev"].' ('.$valor["por"].'% - min: '.$valor["nm"].')</td>';
  $escribe.= '<td '.$clase.' style="text-align: center;">'.$valor["abreviatura"].'</td>';
  $mediacal = round($valor["notaiev"]/$valor["pesoiev"],2);
  // colores
    if ($mediacal<$valor["nm"]) 
     { $colorsuspenso = 'bgcolor="tomato"'; } else { $colorsuspenso = ''; }
  $escribe.= '<td '.$clase.' '.$colorsuspenso.' style="text-align: center;">'.$valor["nota"].'</td>';  
  $escribe.= '<td '.$clase.' '.$colorsuspenso.' style="text-align: center;">'.$mediacal.'</td>';
  $escribe.= '</tr>';
  }
}

$escribe.='</tbody></table>'; // fin de la tabla

$escribe.='<br><br><h1 class="titulo" style="font-size:18px; text-align: left;">'.utf8_encode("Lista de Indicadores y su calificación").'</h1>';
$escribe.= '<table width="100%" class="tabla2"><thead>';
  $escribe.= '<tr>';
  $escribe.= '<th width="60%">Competencia</th>';
  $escribe.= '<th width="20%">'.utf8_encode("Apreciación").'</th>';
  $escribe.= '</tr>';
$escribe.= '</thead><tbody>';

foreach ($recupera["COMPETENCIAS"] as $key => $valor) {
  if ($key%2==1) { $clase=' class="cerotd"'; } else { $clase=' class="ceroimpartd"'; } 
  if (!empty($valor["descripcion"])) {
  $escribe.= '<tr>';
  $escribe.= '<td '.$clase.' style="text-align: left;">'.$valor["descripcion"].' ('.$valor["abreviatura"].') </td>';
  $escribe.= '<td '.$clase.' style="text-align: center;">'.$valor["nota2"].'</td>';
  $escribe.= '</tr>';
  }
}

$escribe.='</tbody></table>'; // fin de la tabla

$mpdf->SetDisplayMode('fullpage');
$mpdf->shrink_tables_to_fit=0;

// LOAD a stylesheet
$stylesheet = file_get_contents('../css/verprint.css');
$mpdf->WriteHTML($stylesheet,1); // The parameter 1 tells that this is css/style only and no body/html/text

$mpdf->SetHTMLHeader($cabecera); 
include_once('watermark.php');
$mpdf->SetHTMLFooter('<p class="footer">Página  {PAGENO}/{nbpg} - '.$horafecha.'</p>');

$mpdf->WriteHTML(utf8_decode($escribe)); // importante poner la decodificación en UTF-8

// $mpdf->WriteHTML($escribe);
$mpdf->Output('../ficheros/notasunalumno.pdf','F');
// $path='../temporal/A.pdf';
// $mpdf->Output($path,'F');
// header("Location: ../ficheros/descargaficheropdf.php?fichero=notasunalumno.pdf");
// exit;
echo './ficheros/descargaficheropdf.php?fichero=notasunalumno.pdf';
?>

<?php 

function obtenfoto($alumno) {
          $fotografia="../imagenes/fotos/".$alumno;
	  $extension=NULL;
	  if (file_exists($fotografia.".jpeg")) { $extension=".jpeg";}
	  if (file_exists($fotografia.".jpg")) { $extension=".jpg";}
	  if (file_exists($fotografia.".png")) { $extension=".png";}
	  if (file_exists($fotografia.".gif")) { $extension=".gif";}
	  if ($extension!=NULL) {
               $fotografia.=$extension; // añado la extensión	
          } else {
               // $fotografia="../imagenes/fotos/boygirl2.png"; // foto cuando no existe...
               $fotografia="";
          }
          //$fotografia = substr($fotografia,1,strlen($fotografia)-1); 
          return $fotografia;
}
?>

