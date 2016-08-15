<?php
include("./funciones/funciones.php"); /* incluye el directorio de funciones */

include_once("./clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

/* $bd="encyclonumber"; */ //debe de haberse cargado en mysql_inc.php

$iz = "left: 120px;" ; // posición de los campos a la izquierda

session_start(); /* empiezo una sesión */

if ($_SESSION['administracion']<2) {
   echo header("Location: ./index.php");
}

if (isset($_SESSION['tutevaluacion']) && strlen($_SESSION['tutevaluacion'])>0) {
    $visualizacion=1;
} else {$visualizacion=0;}

// obtiene arrays, por si hay que usarlos más de una vez
$alumno=obteneralumnosasignacion($bd,$_SESSION['asignacion']); // array para introducir datos de los alumnos
$ii=count($alumno['idalumno']);
$items=obteneritems($bd);

// Ahora sí, reonocimiento de botones
if (isset($_GET['boton']) && $_GET['boton']=='Primero') {$_SESSION['contador']=0;}
if (isset($_GET['boton']) && $_GET['boton']=='Atrás') {$_SESSION['contador']--;}
if (isset($_GET['boton']) && $_GET['boton']=='Adelante') {$_SESSION['contador']++;}
if (isset($_GET['boton']) && $_GET['boton']=='Último') {$_SESSION['contador']=1000;}
if (isset($_GET['contador']) && $_GET['contador']>=0) {$_SESSION['contador']=$_GET['contador'];}

?>
<html>
<head>
<title>Filtro UNO: por alumno, dada evaluación, todas las asignaciones</title>
<meta http-equiv="Content-Type" content="Type=text/html; charset=iso-8859-15">
<!-- Inclyo las diferentes hojas de estilo -->
<?php include_once("./listas_de_css.php"); ?>
</head>
<body>
<!-- Capas de presentación
<div id="grupo" style="position: absolute; left: 0px; top: 0px; width: 960; height: auto; z-index: 1; line-height: 14px"><img name="fondo" src="./imagenes_plantilla/plantilla.png" width="960" height="auto" border="0" alt=""></div>-->

<!-- Capa de menú: navegación de la página -->
<?php include_once("./lista.php"); ?>

<!-- Capa de estado: información útil -->
<div id="fecha">
	<p style="text-align: center;">
	<? 
	echo 'Fecha: '.$calendario->fechaformateada($calendario->fechadehoy());
	echo '<br>Hora: '.$calendario->horactual();
	?></p>
</div>

<!-- Capa de información -->
<div id="informacion" onmouseover="javascript: ocultar()">
    <?php if ($_SESSION['tutorada']==1 && $visualizacion==1) { 		
	if ($_SESSION['contador']>$ii-1) {$_SESSION['contador']=$ii-1;} //Si supera este valor
	if ($_SESSION['contador']<0) {$_SESSION['contador']=0;} //Si es menor que cero

    ?>
    <a name="ancla"></a>

        <!-- ============================ -->
        <!-- Se incluye fotografía -->
        <!-- ============================ -->
        <?php //Incluir fotografía
           incluyefoto($alumno['idalumno'][$_SESSION['contador']],"75px","1%","absolute","none");  
        ?>
        <!-- ============================ -->

    <h1 style="text-align: center;">Alumnado de <?php echo $alumno['cadenaclases'].' - Evaluación: '.dado_Id($bd,$_SESSION['tutevaluacion'],"nombreeval","tb_edicionevaluaciones","ideval"); ?></h1>  
     <form name="veruno" action="./veruno.php#ancla" method="get"> <!-- Principio del form -->     
     <p style="text-align: center;">
     <select name="select" class="botones" id="select2" style="text-align: left;"
	  onChange = "window.location.href = this.options[selectedIndex].value; this.selectedIndex=0;">
	  <option value="">Elige un alumno/a</option>
                <?php 
                $j=0;
                foreach ($alumno['idalumno'] as $key => $valor) {
                   echo '<option value="./veruno.php?contador='.$j.'#ancla">';
                   echo $alumno['alumno'][$key].' ['.$alumno['unidad'][$key].']'.'</option>';		
                   $j++;
                }
		?>
     </select>

	<br>
	<input name="boton" class="botones" id="boton" value="Primero" type="submit" alt="Ir al primer registro" title="Ir al primer registro"  >
	<input name="boton" class="botones" id="boton" value="Atrás" type="submit" alt="Ir al registro anterior" title="Ir al registro anterior">
	<?php echo " ".$alumno['alumno'][$_SESSION['contador']]." (".$alumno['idalumno'][$_SESSION['contador']].") "; ?>
	<input name="al1" class="botones" id="al1" type="hidden" value="<?php echo '&nbsp;&nbsp;'.$alumno['idalumno'][$_SESSION['contador']].'&nbsp;&nbsp;'; ?>" >
	<input name="boton" class="botones" id="boton" value="Adelante" type="submit" title="Ir al siguiente registro" alt="Ir al siguiente registro">
	<input name="boton" class="botones" id="boton" value="Último" type="submit" title="Ir al último registro" alt="Ir al último registro">
	<br>
        </p>
     	<p style="text-align: center;">Informes A:&nbsp;
        <!-- <a href="./ver/verunopdf.php" class="botones" id="pdf" alt="Genera PDF">&nbsp;PDF&nbsp;</a> -->
        <a href="./ficheros/verunopdf.php" class="botones" id="pdf" alt="Genera PDF">&nbsp;PDF&nbsp;</a>
	<!--a href="./ver/verunopdftodos.php?salto=0" class="botones" id="pdf" alt="Genera PDF">&nbsp;TODOS (sin salto de página)&nbsp;</a> -->
        <a href="./ficheros/verunopdftodos.php?salto=0" class="botones" id="pdf" alt="Genera PDF">&nbsp;TODOS (sin salto de página)&nbsp;</a>
	<!-- <a href="./ver/verunopdftodos.php?salto=1" class="botones" id="pdf" alt="Genera PDF">&nbsp;TODOS (con salto de página)&nbsp;</a>  -->
        <a href="./ficheros/verunopdftodos.php?salto=1" class="botones" id="pdf" alt="Genera PDF">&nbsp;TODOS (con salto de página)&nbsp;</a> 
	</p>

     	<p style="text-align: center;">Informes B:&nbsp;
        <!-- <a href="./ver/verunopdfagrupar.php" class="botones" id="pdf" alt="Genera PDF">&nbsp;PDF&nbsp;(2)&nbsp;</a> -->
        <a href="./ficheros/verunopdfagrupar.php" class="botones" id="pdf" alt="Genera PDF">&nbsp;PDF&nbsp;(2)&nbsp;</a>
	<!-- <a href="./ver/verunopdfagrupartodos.php?salto=0" class="botones" id="pdf" alt="Genera PDF">&nbsp;TODOS (2) (sin salto de página)&nbsp;</a>-->
        <a href="./ficheros/verunopdfagrupartodos.php?salto=0" class="botones" id="pdf" alt="Genera PDF">&nbsp;TODOS (2) (sin salto de página)&nbsp;</a>
	<!-- <a href="./ver/verunopdfagrupartodos.php?salto=1" class="botones" id="pdf" alt="Genera PDF">&nbsp;TODOS (2) (con salto de página)&nbsp;</a> -->
        <a href="./ficheros/verunopdfagrupartodos.php?salto=1" class="botones" id="pdf" alt="Genera PDF">&nbsp;TODOS (2) (con salto de página)&nbsp;</a>
        </p>
        <br><hr width="80%"><br><br>
    
    <!-- Final de la botonera de alumnado -->
    <!-- Principio de la información -->	
    <?php
    $link=Conectarse($bd); // y me conecto. //dependiendo del tipo recupero uno u otro.
    // Más antigua --> $Sql='SELECT items, profesor, materia, observaciones FROM tb_evaluacion WHERE unidad= "'.$_SESSION['tutoria'].'" AND alumno="'.$alumno['idalumno'][$_SESSION['contador']].'" AND eval ="'.$_SESSION['tutevaluacion'].'" AND items<>"" ORDER BY materia, profesor';
    // Anterior, sin asignación --> $Sql='SELECT items, profesor, materia, observaciones FROM tb_evaluacion WHERE unidad= "'.$_SESSION['tutoria'].'" AND alumno="'.$alumno['idalumno'][$_SESSION['contador']].'" AND eval ="'.$_SESSION['tutevaluacion'].'" AND (items<>"" OR observaciones<>"") ORDER BY materia, profesor';
    $Sql='SELECT items, observaciones,profesor,materia  from tb_evaluacion inner join tb_asignaciones ON tb_evaluacion.asignacion=tb_asignaciones.idasignacion WHERE alumno="'.$alumno['idalumno'][$_SESSION['contador']].'" AND eval ="'.$_SESSION['tutevaluacion'].'" AND (items<>"" OR observaciones<>"") ORDER BY materia,profesor';
    // Observar qu ecombina dos tablas, la de asignaciones y la de evaluación, para obtener los distintos profesores y materias.

    $result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result
    while ($row=mysql_fetch_array($result)) {
    echo '<div name="uno" id="presentardatos">';
	echo '<h2> Profesor/a: '.dado_Id($bd,$row['profesor'],"Empleado","tb_profesores","idprofesor").' - Materia: '.dado_Id($bd,$row['materia'],"Materias","tb_asignaturas","idmateria").'</h2>';
	// ****************************** POR AQUI VOY ***********************************
        $itemsobtenidos=explode("#",$row['items']);
        $complementos="";
        echo '<p>';
	// items positivos y negativos
        foreach ($itemsobtenidos as $it) {
           $encontrar=array_search($it,$items['iditem']);
	   if (!is_null($encontrar) && is_numeric($encontrar) && $items['positivo'][$encontrar]<=1) {	
		echo $items['item'][$encontrar].' ['.strtoupper(substr($items['grupo'][$encontrar],0,3)).']. ';
	   } else if (!is_null($encontrar) && is_numeric($encontrar) && $items['positivo'][$encontrar]>1) { // complementos
           $complementos.=$items['item'][$encontrar].' ['.strtoupper(substr($items['grupo'][$encontrar],0,3)).']. ';
           } // fin del if
	} // fin del foreach
 	if  ($complementos<>"") { echo '</p><p>Datos complementarios: '.$complementos.'</p>';} // implime aspectos neutros si existen
	echo '</p>';

	if ($row['observaciones']<>"") {echo '<p><span style="color: #770000; font-weight:bold;">Observaciones:&nbsp;</span>'.$row['observaciones'].'</p>';}
        // echo '<hr width="10%">';
    echo '</div>';
    } // fin del while
    mysql_free_result($result);	       
    ?>
    </form> <!-- Final del form -->
<?php 
    } else { 
      echo '<h2>No has seleccionado una evaluación previamente o no te has identificado como tutor/a de un curso</h2>';
      echo '<p style="text-align: center;"><a style="padding: 5px 10px;" class="botones" href="./guardardatosinicialestutoria.php">Datos iniciales de tutoría</a><a style="padding: 5px 10px;" class="botones" href="./index.php">Identificarse como tutor/a</a></p>';	
    }// fin del if
    
    ?>
</div>
<br><br>
</body>
</html>
