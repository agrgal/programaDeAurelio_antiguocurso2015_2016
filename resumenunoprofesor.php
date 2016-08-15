<?php
include("./funciones/funciones.php"); /* incluye el directorio de funciones */

include_once("./clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

session_start(); /* empiezo una sesión */

define('FPDF_FONTPATH','./fpdf17/font/');
require("./fpdf17/fpdf.php");

if ($_SESSION['administracion']<2) {
   echo header("Location: ./index.php");
}

class PDF extends FPDF
{

// Page header
function Header()
{
    global $title1;
    global $title2;
    // Logo
    $this->Image('./fpdf17/logo.png',25,10,40);    
    // $this->Ln(4);	
    // Title1
    // Arial bold 15
    // $this->SetFont('Arial','B',14);
    // Move to the right
    // $this->Cell(80);
    // Title1
    // $this->Cell(5,10,$title1,0,0,'L');
    // $this->Ln(8);
    // title2
    // Arial bold 14
    // $this->SetFont('Arial','B',13);
    // Move to the right
    // $this->Cell(80);
    // Title1
    // $this->Cell(30,10,$title2,0,0,'C');
    // Line break
    // $this->Ln(4);
}

function Cabecera($cabecera) {    
   
    // Arial bold 15
    $this->Ln(4);	
    $this->SetFont('Arial','B',15);
    // Calculate width of title and position
    $w = $this->GetStringWidth($cabecera)+6;
    $this->SetX((210-$w)/2);
    // Colors of frame, background and text
    $this->SetDrawColor(0,0,0);
    $this->SetFillColor(220,220,220);
    // $this->SetTextColor(220,50,50);
    // Thickness of frame (1 mm)
    $this->SetLineWidth(1);
    // Title
    $this->Cell($w,9,$cabecera,1,1,'C',true);
    // Line break
    $this->Ln(4);
}

// Better table
function ImprovedTable($header, $data)
{

    global $items;
    global $margen;

    $w=array();
    $ancho=5;

    // Máximo caracteres de los items
    for($i=0;$i<count($header);$i++) {
        $w[$i]=strlen($data[$cl][0]);
    }

    $wmax= 80; // máximo de los arrays

    for($i=0;$i<count($header);$i++) {
        if ($i<1) {$w[$i]=$wmax; } else {$w[$i]=$ancho;}
    }
   
    $this->Ln(20); // empieza la tabla abajo, unas líneas, para que quepan los nombres

    // Header
    $this->SetFont('Arial','B',14);
    for($i=0;$i<count($header);$i++) {         
        if ($i>0) {          
          $this->Image('./temporal/'.$header[$i].'.jpg',-1+round($margen/3)+$wmax+($i+1)*$ancho,10,$w[$i]+2,30); 
          // Borra la imagen de temporal.
          @unlink('./temporal/'.$header[$i].'.jpg');
          }
        else { $this->Cell($w[$i],6,$header[$i],0,0,'C'); }
    }
    $this->Ln();
    
    // Data
    $this->SetFillColor(224,235,255);
    $fill=0; // empiezo rellenando la primera fila
    foreach ($items['iditem'] as $cl => $valor) {
       $fill=!$fill;
       for($i=0;$i<count($header);$i++) {
            if ($i<1) { $jus='L'; } else {$jus='C'; }
            if ($data[$cl][$i]==1) { $this->SetFont('Arial','',10); $poner="X"; } 
            if ($data[$cl][$i]==0) { $this->SetFont('Arial','',10); $poner="";}
            if ($i==0) { $this->SetFont('Arial','',8); $poner=$data[$cl][$i];}
            $this->Cell($w[$i],$ancho,$poner,1,0,$jus,$fill);
       }
       $this->Ln();      
    } // fin del for each
    // Closing line
    
}

// Page footer
function Footer()
{
    global $h;
    // Position at 1.5 cm from bottom
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Arial','I',10);
    // Page number
    $this->Cell(0,10,'Página '.$this->PageNo().'/{nb} - '.$h,0,0,'C');
}

// Empieza lo de HTML

var $B;
var $I;
var $U;
var $HREF;

function PDF($orientation='P', $unit='mm', $size='A4')
{
    // Call parent constructor
    $this->FPDF($orientation,$unit,$size);
    // Initialization
    $this->B = 0;
    $this->I = 0;
    $this->U = 0;
    $this->HREF = '';
}

function WriteHTML($html)
{
    // HTML parser
    $html = str_replace("\n",' ',$html);
    $a = preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
    foreach($a as $i=>$e)
    {
        if($i%2==0)
        {
            // Text
            if($this->HREF)
                $this->PutLink($this->HREF,$e);
            else
                $this->Write(5,$e);
        }
        else
        {
            // Tag
            if($e[0]=='/')
                $this->CloseTag(strtoupper(substr($e,1)));
            else
            {
                // Extract attributes
                $a2 = explode(' ',$e);
                $tag = strtoupper(array_shift($a2));
                $attr = array();
                foreach($a2 as $v)
                {
                    if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
                        $attr[strtoupper($a3[1])] = $a3[2];
                }
                $this->OpenTag($tag,$attr);
            }
        }
    }
}

function OpenTag($tag, $attr)
{
    // Opening tag
    if($tag=='B' || $tag=='I' || $tag=='U')
        $this->SetStyle($tag,true);
    if($tag=='A')
        $this->HREF = $attr['HREF'];
    if($tag=='BR')
        $this->Ln(5);
}

function CloseTag($tag)
{
    // Closing tag
    if($tag=='B' || $tag=='I' || $tag=='U')
        $this->SetStyle($tag,false);
    if($tag=='A')
        $this->HREF = '';
}

function SetStyle($tag, $enable)
{
    // Modify style and select corresponding font
    $this->$tag += ($enable ? 1 : -1);
    $style = '';
    foreach(array('B', 'I', 'U') as $s)
    {
        if($this->$s>0)
            $style .= $s;
    }
    $this->SetFont('',$style);
}

function PutLink($URL, $txt)
{
    // Put a hyperlink
    $this->SetTextColor(0,0,255);
    $this->SetStyle('U',true);
    $this->Write(5,$txt,$URL);
    $this->SetStyle('U',false);
    $this->SetTextColor(0);
}

} // fin de la clase PDF

// Instanciation of inherited class
$pdf = new PDF();
// Necesito algunas variables
// obtiene arrays, por si hay que usarlos más de una vez
$alumno=obtenerclase($bd,trim($_SESSION['unidad'])); // array para introducir datos de los alumnos
$ii=count($alumno['idalumno']);
$items=obteneritems($bd);
$materia = obtenermaterias($bd,$_SESSION['unidad'],$_SESSION['evaluacion']);
$jj=count($materia['idmateria']);

// Empiezo el pdf
// Declaro variables
$title1 = dado_Id($bd,$_SESSION['materia'],"Materias","tb_asignaturas","idmateria").' - Clase: '.$_SESSION['unidad'].' - Evaluación: '.dado_Id($bd,$_SESSION['evaluacion'],"nombreeval","tb_edicionevaluaciones","ideval");
$title2 = "";
$horafecha='Fecha: '.$calendario->fechaformateada($calendario->fechadehoy());
$horafecha.=' - Hora: '.$calendario->horactual();
$h=$horafecha;
// Página
$pdf->AliasNbPages();

$margen=15;
$pdf ->SetLeftMargin($margen);
$pdf ->SetTopMargin(2);

$headertabla=array();
$datostabla=array();

// inicializa las cabeceras de las tablas 

if (isset($_GET['salto']) && $_GET['salto']<>1) { $pdf->AddPage('L'); } // Si no salta, al menos pone un AddPage

// foreach ($materia['materia'] as $k => $mat2) { // por cada materia

$mat2=dado_Id($bd,$_SESSION['materia'],"Materias","tb_asignaturas","idmateria");

// inicializo la matriz datostabla, la pongo a cero
foreach ($items['item'] as $clave => $valor) {
   $datostabla[$clave][0]= $valor; // cabeceras de cada item
   for ($j=0;$j<$ii;$j++) { 
	$datostabla[$clave][$j+1]=0;
   }
}

if (isset($_GET['salto']) && $_GET['salto']==1) { $pdf->AddPage('L'); }

// $pdf->Cabecera($mat2); //materia que presenta
$headertabla[0]=$mat2;

for ($j=0;$j<$ii;$j++) { // principio del for
$headertabla[$j+1]=$alumno['idalumno'][$j]; // cabecera con los números de cada alumno.

// Recupero de la base de datos
$link=Conectarse($bd); // y me conecto. //dependiendo del tipo recupero uno u otro.
$Sql='SELECT items, profesor, materia, observaciones FROM tb_evaluacion WHERE unidad= "'.$_SESSION['unidad'].'" AND materia="'.$_SESSION['materia'].'" AND eval ="'.$_SESSION['evaluacion'].'" AND alumno ="'.$alumno['idalumno'][$j].'" AND items<>"" ORDER BY profesor';

$result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result

// $pdf->Write(10,$Sql);

$profesor="";

// Hago el bucle
while ($row=mysql_fetch_array($result)) { // por cada profesor también
$itemsobtenidos=explode("#",$row['items']);
// pongo profesorado
$clave=0;
   foreach ($itemsobtenidos as $it) {
       $encontrar=array_search($it,$items['iditem']); // si encuentra el item en la cadena $items['iditem'] devuelve la clave

       if (!is_null($encontrar) && is_numeric($encontrar)) {	
	   $datostabla[$encontrar][$j+1]=1;
       }
   } // fin del foreach

} // fin del while  
mysql_free_result($result); // acaba por cada profesor

} // fin del for, por cada alumno

$pdf->Ln(14);
$pdf->ImprovedTable($headertabla, $datostabla); 
$pdf->SetFont('Arial','BI',13);
$pdf->Cell(5,10,$title1,0,0,'L');

// Selecciono profesores
$link=Conectarse($bd); // y me conecto. //dependiendo del tipo recupero uno u otro.
$Sql='SELECT DISTINCT profesor FROM tb_evaluacion WHERE unidad= "'.$_SESSION['unidad'].'" AND materia="'.$_SESSION['materia'].'" AND eval ="'.$_SESSION['evaluacion'].'" AND items<>"" ORDER BY profesor';
$result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result
$profesor="";
while ($row=mysql_fetch_array($result)) {
  $profesor.=cambiarnombre(dado_Id($bd,$row['profesor'],"Empleado","tb_profesores","idprofesor"))." - ";
}
$profesor=substr($profesor,0,strlen($profesor)-3);
$pdf->Ln(6);
$pdf->SetFont('Arial','I',11);
$pdf->Cell(5,10,"Impartida por: ".$profesor,0,0,'L');
mysql_free_result($result); 

// } // fin del foreach de cada materia */

// Envío el PDF
$pdf->Output('Resumen de materias: - '.$title1.'.pdf','I');

?>

