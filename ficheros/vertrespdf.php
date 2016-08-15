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
$alumno=obteneralumnosasignacion($bd,$_SESSION['asignacion']); // array para introducir datos de los alumnos
$ii=count($alumno['idalumno']);

$asignaciones = obtenerasignaciones($bd,$alumno['idalumno'],$_SESSION['tutevaluacion']); // pasa un array con los id de todos los alumnos
// obtiene un array con las distintas asignaciones que ESA EVALUACIÓN, han sido calificadas
$jj=count($asignaciones);

$items=obteneritems($bd);
$kk=count($items['iditem']);
// Empiezo el pdf

// Declaro variables
// $title1 = $items['item'][$_SESSION['contador']].' ['.trim(substr($items['grupo'][$_SESSION['contador']],0,3)).'] - Clase: '.$_SESSION['tutoria'].' - Evaluación: '.dado_Id($bd,$_SESSION['tutevaluacion'],"nombreeval","tb_edicionevaluaciones","ideval");
$title1 = 'Evaluación: '.dado_Id($bd,$_SESSION['tutevaluacion'],"nombreeval","tb_edicionevaluaciones","ideval");
$title2= 'Clases: '.$alumno['cadenaclases'];
$horafecha='Fecha: '.$calendario->fechaformateada($calendario->fechadehoy());
$horafecha.=' - Hora: '.$calendario->horactual();
$cab=$items['item'][$_SESSION['contador']].' ['.trim(substr($items['grupo'][$_SESSION['contador']],0,3)).']';
$h=$horafecha;

$cabecera='<table style="width: 100%;"><tr><td rowspan="2" style="width: 20% text-align: left;">';
$cabecera.='<img width="15%" heigth="auto" src="../imagenes_plantilla/logo.png"></td>';
$cabecera.='<td rowspan="2" style="width: 5% text-align: left;"></td>';
$cabecera.='<td style="width: 80%; text-align: center;"><h1 class="titulo" style="font-size:15px;">'.$title1.' - '.$title2.'</h1></tr>';
$cabecera.='<tr><td style="width: 80%; text-align: center;"><h1 class="subtitulo" style="font-size:13px;">'.$cab.'</h1></td></tr></tr></table>';

$escribe=""; 

// Recupero los datos
$link=Conectarse($bd); // y me conecto. //dependiendo del tipo recupero uno u otro.
// $Sql='SELECT items, profesor, materia, alumno, observaciones FROM tb_evaluacion WHERE unidad= "'.$_SESSION['tutoria'].'" AND eval ="'.$_SESSION['tutevaluacion'].'" ORDER BY alumno, materia, profesor';
$Sql='SELECT items, observaciones,alumno, profesor,materia,asignacion  from tb_evaluacion inner join tb_asignaciones ON tb_evaluacion.asignacion=tb_asignaciones.idasignacion WHERE eval ="'.$_SESSION ['tutevaluacion'].'" AND (items<>"" OR observaciones<>"") ORDER BY alumno,materia,profesor';

$result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result

$nn=0;

$cad2=array(); // en esta cadena meto datos de alumno, profesor y materia SI

    // encuentro el item en su cadena items.
    while ($row=mysql_fetch_array($result)) { // por cada elemento encontrado
	    $it=explode('#',$row['items']); // array con los datos
	      if (in_array($items['iditem'][$_SESSION['contador']],$it) && in_array($row['asignacion'],$asignaciones) && in_array($row['alumno'],$alumno['idalumno'])) { //filtro según asignación, item y alumnos
            // Si el item de la sesión ESTÁ en el resultado de SQL Y SI la asignación está en la lista de asignaciones de mi tutoría Y SI el alumno está en mi lista de TUTORÍA.
	       $cad2['profesor'][$nn]=dado_Id($bd,$row['profesor'],"Empleado","tb_profesores","idprofesor");
	       $cad2['alumno'][$nn]=$row['alumno'];
               $cad2['clase'][$nn]=$alumno['unidad'][$row['alumno']]; 
	       $cad2['materia'][$nn]=dado_Id($bd,$row['materia'],"Materias","tb_asignaturas","idmateria");	
	       $nn++;
	    } // fin del if
    } // fin del while
    mysql_free_result($result);	       

    // por cada alumno 
    $alumnado=array(); 
    $escribedos=array();
    $alumnado=array_unique($cad2['alumno']); //comprueba repetidos

    foreach ($cad2['alumno'] as $clave => $alumno2) { // recorro primero todos los valores dados
         $escribedos[$alumno2].=cambiarnombre($cad2['profesor'][$clave]).' - '.$cad2['materia'][$clave]." // "; // y obtengo la cadena con los profes/materias que han opinado eso.
    }

    foreach ($alumnado as $key => $valor) { // ahora con los valores no repetidos
          // $pdf->SetFont('Times','BU',13);
	  // $pdf->Cell(0,10,cambiarnombre(dado_Id($bd,$valor,"alumno","tb_alumno","idalumno")).' ('.dado_Id($bd,$valor,"unidad","tb_alumno","idalumno").')',0,1); // alumnos
          $escribe.='<h2 class="cabecera">'.cambiarnombre(dado_Id($bd,$valor,"alumno","tb_alumno","idalumno")).' ('.dado_Id($bd,$valor,"unidad","tb_alumno","idalumno").')</h2>';
	  // $pdf->SetFont('Times','',13);
          //$pdf->Cell(0,10,substr($escribe[$valor],0,strlen($escribe[$valor])-4),0,1); // profesores-materias
          // $escribe.='<p>'.$escribedos[$valor].'</p>';
          $escribe.='<p class="textonormal">'.substr($escribedos[$valor],0,strlen($escribedos[$valor])-4).'</p>';
          // $pdf->Ln(2); 
          // $pdf->Image("../fpdf17/hr-jon-lucas2.jpg",80,NULL,50); 
          $escribe.='<p style="text-align: center;"><img width="15%" heigth="auto" src="../imagenes_plantilla/hr-jon-lucas2.jpg"></p>';
          // $pdf->Ln(2);
    } 

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
$mpdf->Output('../ficheros/informeporitem.pdf','F');
// $path='../temporal/A.pdf';
// $mpdf->Output($path,'F');
header("Location: ../ficheros/descargaficheropdf.php?fichero=informeporitem.pdf");
exit;

?>

