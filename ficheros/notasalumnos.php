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

$alumno=obteneralumnosasignacion($bd,$_POST['asignacion']); // array para introducir datos de los alumnos
$numalu=count($alumno['idalumno']);

$title1 = $_POST["titulouno"];
$title2 = $_POST["titulodos"];
$horafecha='Fecha: '.$calendario->fechaformateada($calendario->fechadehoy());
$horafecha.=' - Hora: '.$calendario->horactual();
$h=$horafecha;

$cabecera='<table style="width: 100%;"><tr><td rowspan="2" style="width: 20% text-align: left;">';
$cabecera.='<img width="15%" heigth="auto" src="../imagenes_plantilla/logo.png"></td>';
$cabecera.='<td style="width: 80%; text-align: left;"><h1 class="titulo" style="font-size:13px;">'.$title1.' - '.$title2.'</h1></td></tr>';
$cabecera.='<tr><td style="width: 80%; text-align: left;"><h1 class="titulo" style="font-size:15px;">'.$_POST["evaluacion"].'</h1></td></tr></table>';

$recupera = json_decode($_POST["contenido"],true); // recupera los datos de notas
$resultados = json_decode($_POST["resultados"],true); // recupera los datos de calificaciones finales

$calificaciones=$resultados["calificaciones"]; // en esta variable guardo el JSON de los resultados finales

$nf=array(); // notas finales
foreach ($calificaciones as $key => $valor) {
    $nf[$valor["id"]]["notamedia"]=$valor["notamedia"];
    $nf[$valor["id"]]["notaseneca"]=$valor["notaseneca"];
    $nf[$valor["id"]]["notarecuperacion"]=$valor["notarecuperacion"];
    // $escribe.='<p>'.$valor["alumno"].' - '.$nf[$valor["id"]]["notamedia"].'</p>';
}

$datos=array(); // array de datos

foreach ($alumno["idalumno"] as $key2 => $idalumno) {
    foreach ($recupera as $key => $valor) { // por cada valor de lo recuperado
       if ($idalumno==$valor["idalumno"]) { // Si son idénticos, es que hay datos    
          $datos["idalumno"][]=$idalumno;
          $datos["nombre"][]=$valor["nombre"];
          $datos["nota"][]=$valor["nota"];
          $datos["mod"][]=$valor["mod"];
          $datos["alumno"][]=$valor["alumno"];
          $datos["abreviatura"][]=$valor["abreviatura"];
          $datos["peso"][]=$valor["peso"];
          $datos["notaminima"][]=$valor["notaminima"];
          $datos["porcentaje"][]=$valor["porcentaje"];
       } // fin del if   
    }
} // fin del foreach de cada alumno. Están ordenados alfabéticamente

$distintosalumnos = array_unique($datos["idalumno"]);

foreach ($distintosalumnos as $key => $idalumno) {
          $clave = array_search($idalumno,$datos["idalumno"]);          
          $foto = obtenfoto($idalumno);
          $escribe.= '<table width="100%" class="tabla2"><thead>';
	  $escribe.= '<tr>';
	  $escribe.= '<th width="5%" style="display:none;"><img id="fotografia" style="border: 1px solid black; display: auto;" height="40px" src="'.$foto.'"></th>';
          // $escribe.= '<th width="15%" style="display:none;">'.$foto.'</th>';
	  $escribe.= '<th width="78%" style="text-align: left; text-indent: 1em;" >'.cambiarnombre($datos["alumno"][$clave]).'</th>';
	  $escribe.= '<th width="5%">Nota</th>';
	  $escribe.= '<th width="4%">Mod.</th>';
	  $escribe.= '<th width="4%">Min</th>';
          $escribe.= '<th width="4%">Peso</th>';
	  $escribe.= '</tr>';
	  $escribe.= '</thead><tbody>';
          $ii=0;
          foreach ($datos["idalumno"] as $key => $valor) {
		if ($valor==$idalumno) {
		if ($ii%2==1) { $clase=' class="cerotd"'; } else { $clase=' class="ceroimpartd"'; } 
                if ($datos["nota"][$key]<$datos["notaminima"][$key] && $datos["mod"][$key]=="N") 
 		   { $cnota="darkred"; } else { $cnota="darkgreen"; } 
                if ($datos["mod"][$key]=="N") { $cmod="black"; } 
                   else if ($datos["mod"][$key]=="?") { $cmod="darkred"; } 
                   else { $cmod="blue"; } 
		$escribe.= '<tr>';
		$escribe.= '<td '.$clase.' style="text-align: center;">'.($ii+1).'</td>';
		// $escribe.= '<td '.$clase.' style="text-align: left;">'.cambiarnombre($valor["alumno"]).'</td>';
		$escribe.= '<td '.$clase.' style="text-align: left;">'.$datos["nombre"][$key].' ('.$datos["abreviatura"][$key].' - '.$datos["porcentaje"][$key].'%)</td>';
		$escribe.= '<td '.$clase.' style="text-align: center;"><font color="'.$cnota.'">'.$datos["nota"][$key].'</font></td>';
		$escribe.= '<td '.$clase.' style="text-align: center;"><font color="'.$cmod.'">'.$datos["mod"][$key].'</font></td>';
		$escribe.= '<td '.$clase.' style="text-align: center;">'.$datos["notaminima"][$key].'</td>';
		$escribe.= '<td '.$clase.' style="text-align: center;">'.$datos["peso"][$key].'</td>';
		$escribe.= '</tr>';
		$ii++;
                } // fin del if
          } 
          $escribe.="'</tbody></table>";
          // Resultados
          if (max($nf[$idalumno]["notaseneca"],$nf[$idalumno]["notarecuperacion"])<5) 
             { $colorsuspenso = 'bgcolor="tomato"'; } else { $colorsuspenso = 'bgcolor="lightgreen"'; }           
          $escribe.= '<table width="100%" class="tabla2"><thead>';
	  $escribe.= '<tr>';
	  $escribe.= '<th width="100%" '.$colorsuspenso.' style="text-align: right; text-indent: 1em; font-size: 14px;" >Nota cal.: '.$nf[$idalumno]["notamedia"].' // '.utf8_encode("Nota en la evaluación:").' '.$nf[$idalumno]["notaseneca"].' // '.utf8_encode("Nota rec.:").' '.$nf[$idalumno]["notarecuperacion"].'</th>';
          $escribe.= '</tr>';
	  $escribe.="'</thead></table><br>";          
} // fin del foreach cada alumno


// $escribe.= '<p id="calificaciones">'.$_POST["resultados"].'</p>';
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
$mpdf->Output('../ficheros/notasalumnos.pdf','F');
// $path='../temporal/A.pdf';
// $mpdf->Output($path,'F');
// header("Location: ../ficheros/descargaficheropdf.php?fichero=notasalumnos.pdf");
// exit;
echo './ficheros/descargaficheropdf.php?fichero=notasalumnos.pdf';
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
          // $fotografia = substr($fotografia,1,strlen($fotografia)-1);
          return $fotografia;
}
?>
