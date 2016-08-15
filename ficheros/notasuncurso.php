<?php
include_once("../funciones/seguridad/mysql_inc.php");
include_once("../funciones/adodb_time_inc.php");
include_once("../funciones/funciones.php"); /* incluye el directorio de funciones */
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

$cabecera='<table style="width: 100%;"><tr><td rowspan="2" style="width: 20% text-align: left;">';
$cabecera.='<img width="15%" heigth="auto" src="../imagenes_plantilla/logo.png"></td>';
$cabecera.='<td style="width: 80%; text-align: left;"><h1 class="titulo" style="font-size:13px;">'.$title1.' - '.$title2.'</h1></td></tr>';
$cabecera.='<tr><td style="width: 80%; text-align: left;"><h1 class="titulo" style="font-size:15px;">'.$_POST["evaluacion"].'</h1></td></tr></table>';

$recupera = json_decode($_POST["contenido"],true);

// Empieza la tabla...
$escribe.= '<table width="100%" class="tabla2"><thead>';
  $escribe.= '<tr>';
  $escribe.= '<th width="5%">N</th>';
  $escribe.= '<th width="8%">'.utf8_encode("Foto").'</th>';
  $escribe.= '<th width="51%">Nombre</th>';
  $escribe.= '<th width="12%">Nota calculada</th>';
  $escribe.= '<th width="12%">'.utf8_encode("Nota evaluación").'</th>';
  $escribe.= '<th width="12%">'.utf8_encode("Nota recuperación").'</th>';
  $escribe.= '</tr>';
$escribe.= '</thead><tbody>';
// Cuerpo de la tabla...
foreach ($recupera["calificaciones"] as $key => $valor) {
  $foto = obtenfoto($valor["id"]);
  if ($key%2==1) { $clase=' class="cerotd"'; } else { $clase=' class="ceroimpartd"'; } 
  $escribe.= '<tr>';
  $escribe.= '<td '.$clase.' style="text-align: center;">'.($key+1).'</td>';
  $escribe.= '<td '.$clase.' style="text-align: center;"><img id="fotografia" style="border: 1px solid black; display: auto;" height="40px" src="'.$foto.'"></td>';
  $escribe.= '<td '.$clase.' style="text-align: left;">'.cambiarnombre($valor["alumno"]).'</td>';
  // color del texto nota media 
  if (is_numeric($valor["notamedia"]) && $valor["notamedia"]>=0 && $valor["notamedia"]<5) 
     { $colornm="red";  } else { $colornm="black";  } 
  $escribe.= '<td '.$clase.' style="text-align: center;font-size: 1.1em;"><font color="'.$colornm.'">'.$valor["notamedia"].'</font></td>';
  // color del texto nota séneca 
  if (is_numeric($valor["notaseneca"]) && $valor["notaseneca"]>=0 && $valor["notaseneca"]<5) 
     { $colorns="red";  } else { $colorns="black";  } 
  $escribe.= '<td '.$clase.' style="text-align: center; font-size: 1.1em;"><font color="'.$colorns.'">'.$valor["notaseneca"].'</font></td>';

  // color del texto nota recuperación 
  if (is_numeric($valor["notarecuperacion"]) && $valor["notarecuperacion"]>=5 && $valor["notaseneca"]<5) 
     { $colornr="blue";  } 
  else if ($valor["notaseneca"]<5 && $valor["notarecuperacion"]<=5) { $colornr="red";  } 
  else if ($valor["notaseneca"]>=5) { $colornr="darkgreen";  } 
  else { $colornr="black";  } 

  if ($valor["notaseneca"]<5 || (is_numeric($valor["notarecuperacion"]) && $valor["notarecuperacion"]>0)) {
  $escribe.= '<td '.$clase.' style="text-align: center; font-size: 1.1em;"><font color="'.$colornr.'">'.$valor["notarecuperacion"].'</font></td>';
  } else {
  $escribe.='<td></td>';
  }// fin del if

  $escribe.= '</tr>'; // fin de fila
}

$escribe.= '<tr><td class="estadisticas" colspan="6" style="text-indent: 1em;">Nota cal. aprobados: '.$recupera["nmaprobados"].' ('.$recupera["nmporaprobados"].'%) - Nota cal. suspensos: '.$recupera["nmsuspensos"].' ('.$recupera["nmporsuspensos"].'%) - No calificados: '.$recupera["nmno"].'</td></tr>';

$escribe.= '<tr><td class="estadisticas" colspan="6" style="text-indent: 1em;">Nota eval. aprobados: '.$recupera["nsaprobados"].' ('.$recupera["nsporaprobados"].'%) - Nota cal. suspensos: '.$recupera["nssuspensos"].' ('.$recupera["nsporsuspensos"].'%) - No calificados: '.$recupera["nsno"].'</td></tr>';

$escribe.= '<tr><td class="estadisticas" colspan="6" style="text-indent: 1em;">Nota cal. recuperacion: '.$recupera["nraprobados"].' ('.$recupera["nrporaprobados"].'%) - Nota cal. suspensos: '.$recupera["nrsuspensos"].' ('.$recupera["nrporsuspensos"].'%) - No calificados: '.$recupera["nrno"].'</td></tr>';

$escribe.= '<tr><td class="estadisticas" colspan="6" style="text-indent: 1em;">Total aprobados: '.$recupera["ntaprobados"].' ('.$recupera["ntporaprobados"].'%) - Nota cal. suspensos: '.$recupera["ntsuspensos"].' ('.$recupera["ntporsuspensos"].'%) - No calificados: '.$recupera["ntno"].'</td></tr>';

$escribe.='</tbody></table>'; // fin de la tabla

$escribe.

// $escribe.= '<p id="calificaciones">'.$recupera["ntporaprobados"].'</p>';
// $escribe.= '<p id="calificaciones">'.$_POST["contenido"].'</p>';

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
$mpdf->Output('../ficheros/notasuncurso.pdf','F');
// $path='../temporal/A.pdf';
// $mpdf->Output($path,'F');
// header("Location: ../ficheros/descargaficheropdf.php?fichero=notasuncurso.pdf");
// exit;
echo './ficheros/descargaficheropdf.php?fichero=notasuncurso.pdf';
// echo "Fichero descargado";
?>

<?php 

function obtenfoto($alumno) {
          $fotografia="../imagenes/fotos/".$alumno;
	  /*$extension=NULL;
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
          $fotografia = substr($fotografia,1,strlen($fotografia)-1); */
          return $fotografia;
}
?>
