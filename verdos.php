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

if (isset($_SESSION['tutevaluacion']) && strlen($_SESSION['tutevaluacion'])>0 ) {
    $visualizacion=1;
} else {$visualizacion=0;}

// obtiene arrays, por si hay que usarlos más de una vez
$alumno=obteneralumnosasignacion($bd,$_SESSION['asignacion']); // array para introducir datos de los alumnos
$aluii=count($alumno['idalumno']);

$asignaciones = obtenerasignaciones($bd,$alumno['idalumno'],$_SESSION['tutevaluacion']); // pasa un array con los id de todos los alumnos
// obtiene un array con las distintas asignaciones que ESA EVALUACIÓN, han sido calificadas
$jj=count($asignaciones);

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
<title>Filtro DOS: demás asiganciones de esos alumnos, por evaluación.</title>
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
    <?php if (strlen($_SESSION['tutorada'])>0 && $visualizacion==1) { 		
	if ($_SESSION['contador']>$jj-1) {$_SESSION['contador']=$jj-1;} //Si supera este valor
	if ($_SESSION['contador']<0) {$_SESSION['contador']=0;} //Si es menor que cero    
    // Averiguar qué le pasa a la variable materia. ¿Por qué no las pone todas cuando avanzo botones?

    ?>
    <a name="ancla"></a>
    <h2 style="text-align: center;">Alumnado de <?php echo $alumno['cadenaclases'] ?></h2>
    <h2 style="text-align: center;"><?php echo 'Evaluación: '.dado_Id($bd,$_SESSION['tutevaluacion'],"nombreeval","tb_edicionevaluaciones","ideval"); ?></h2>  
     <form name="verdos" action="./verdos.php#ancla" method="get"> <!-- Principio del form -->     
     <p style="text-align: center;">
     <select name="select" class="botones" id="select2" style="text-align: left;"
	  onChange = "window.location.href = this.options[selectedIndex].value; this.selectedIndex=0;">
	  <option value="">Elige una asignación</option>
		<?php 
                $j=0;
                foreach ($asignaciones as $key => $valor) {
                        $cadena=obtenerdatosasignacion($bd,$valor);
			echo '<option value="./verdos.php?contador='.$j.'#ancla">';
			echo $cadena['materia'].' - '.cambiarnombre($cadena['profesor']).'</option>';
                        $j++;
                }
		?>
     </select>
	<br>
	<input name="boton" class="botones" id="boton" value="Primero" type="submit" alt="Ir al primer registro" title="Ir al primer registro"  >
	<input name="boton" class="botones" id="boton" value="Atrás" type="submit" alt="Ir al registro anterior" title="Ir al registro anterior">
        <?php $cad = obtenerdatosasignacion($bd,$asignaciones[$_SESSION['contador']]); ?>
	<?php echo " ".$cad['profesor']." (".$cad['materia'].") "; ?>
	<input name="asig1" class="botones" id="asig1" type="hidden" value="<?php echo $asignaciones[$_SESSION['contador']]; ?>" >
	<input name="boton" class="botones" id="boton" value="Adelante" type="submit" title="Ir al siguiente registro" alt="Ir al siguiente registro">
	<input name="boton" class="botones" id="boton" value="Último" type="submit" title="Ir al último registro" alt="Ir al último registro">
	<br></p>
     	<p style="text-align: center;">Información en PDF:&nbsp;
        <!-- <a href="./ver/verdospdf.php" class="botones" id="pdf" alt="Genera PDF">&nbsp;PDF&nbsp;</a>
	<a href="./ver/verdospdftodos.php?salto=0" class="botones" id="pdf" alt="Genera PDF">&nbsp;TODAS (sin salto de página)&nbsp;</a>
	<a href="./ver/verdospdftodos.php?salto=1" class="botones" id="pdf" alt="Genera PDF">&nbsp;TODAS (con salto de página)&nbsp;</a>  -->
        <a href="./ficheros/verdospdf.php" class="botones" id="pdf" alt="Genera PDF">&nbsp;PDF&nbsp;</a>
	<a href="./ficheros/verdospdftodos.php?salto=0" class="botones" id="pdf" alt="Genera PDF">&nbsp;TODAS (sin salto de página)&nbsp;</a>
	<a href="./ficheros/verdospdftodos.php?salto=1" class="botones" id="pdf" alt="Genera PDF">&nbsp;TODAS (con salto de página)&nbsp;</a> 
	</p><br><hr width="80%"><br><br>
    
    <!-- Final de la botonera de alumnado -->
    <!-- Principio de la información -->	
    <?php

    // obtengo alumnos de la asignación y los alumnos que NO EStÁN EN ELLA.
    $a2=obteneralumnosasignacion($bd,$asignaciones[$_SESSION['contador']]);
    $numa2=count($a2['idalumno']);
    $noestan=array_diff($a2['idalumno'],$alumno['idalumno']); // se quedan los del primer array QUE NO ESTÁN EN EL SEGUNDO.
    $numnoestan=count($noestan);

$link=Conectarse($bd); // y me conecto. //dependiendo del tipo recupero uno u otro.
   
$Sql='SELECT items, observaciones,alumno, profesor,materia,asignacion  from tb_evaluacion inner join tb_asignaciones ON tb_evaluacion.asignacion=tb_asignaciones.idasignacion WHERE eval ="'.$_SESSION['tutevaluacion'].'" AND asignacion="'.$asignaciones[$_SESSION['contador']].'" AND (items<>"" OR observaciones<>"") ORDER BY alumno';

// $Sql='SELECT items, asignacion, alumno, profesor, materia, observaciones FROM tb_evaluacion WHERE unidad= "'.$_SESSION['tutoria'].'" AND materia="'.$materia['idmateria'][$_SESSION['contador']].'" AND eval ="'.$_SESSION['tutevaluacion'].'" AND (items<>"" OR observaciones<>"") ORDER BY profesor, alumno';
    
$result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result
    
    // $n=0; // nº de alumnos que no está en la asignación

    while ($row=mysql_fetch_array($result)) {    

    if (in_array($row['alumno'],$alumno['idalumno'])) { // si el alumno SI está en la lista original del tutor

    echo '<div name="uno" id="presentardatos">';
	// echo '<h2> Profesor/a: '.dado_Id($bd,$row['profesor'],"Empleado","tb_profesores","idprofesor").' - Alumno/a: '.dado_Id($bd,$row['alumno'],"alumno","tb_alumno","idalumno").'</h2>';
        // Incluir una foto
          incluyefoto($row['alumno'],"0px","1%","relative","right");
        // ================== 
	echo '<h2> Profesor/a: '.cambiarnombre($cad['profesor']).' - Alumno/a: '.dado_Id($bd,$row['alumno'],"alumno","tb_alumno","idalumno").' ['.dado_Id($bd,$row['alumno'],"unidad","tb_alumno","idalumno").']'.'</h2>';
        $itemsobtenidos=explode("#",$row['items']);
        $complementos="";
	echo '<p style="width: 100%;">';
	foreach ($itemsobtenidos as $it) {
           $encontrar=array_search($it,$items['iditem']);
	   if (!is_null($encontrar) && is_numeric($encontrar) && $items['positivo'][$encontrar]<=1) {	
		echo $items['item'][$encontrar].' ['.strtoupper(substr($items['grupo'][$encontrar],0,3)).']. ';
	   } else if (!is_null($encontrar) && is_numeric($encontrar) && $items['positivo'][$encontrar]>1) { // complementos
                $complementos.=$items['item'][$encontrar].' ['.strtoupper(substr($items['grupo'][$encontrar],0,3)).']. ';
           } // fin del if
	} // fin del foreach
	if  ($complementos<>"") { echo '</p><p>Datos complementarios: '.$complementos.'</p>';} // imprime aspectos complementarios si existen	
        echo '<p style="width: 100%;">';
	if ($row['observaciones']<>"") {echo '<p><span style="color: #770000; font-weight:bold;">Observaciones:&nbsp;</span>'.$row['observaciones'].'</p>';}
        // echo '<hr width="10%">';
    echo '</div>';

    } // fin del if que comprueba si está o no, un alumno en la lista original del tutor.
    
    // else { $n++; }
    
    } // fin del while 

    mysql_free_result($result);	 


    // Número Alumnos de la ASIGNACIÓN que está en $_SESSION['contador']

    echo '<div id="presentardatos">';
    echo '<h2><b>En mi tutoría, Nº de alumnos CON esta asignatura y este profesora/a: </b><span style="color: #882222;">'.($numa2-$numnoestan).' - '.(round(100*($numa2-$numnoestan)/$aluii,2)).'% </span></h2>';
    echo '<h2><b>En mi tutoría, Nº de alumnos que NO TIENEN esta asignatura y este profesora/a: </b><span style="color: #882222;">'.($aluii-$numa2+$numnoestan).'</span></h2>';
    echo '</div>';
	
    $incorporar="";
    if ($numnoestan>0) { $incorporar='<div id="presentardatos"><h2 style="text-align: justify;"><strong>En esta asignatura y con este profesor/a hay '.$numnoestan.' alumnos/as más que NO PERTENECEN a esta tutoría: </strong><span style="color: #882222;">';}
    foreach ($noestan as $key => $valor) {
        $incorporar.=cambiarnombre($a2['alumno'][$key])." - ".$a2['unidad'][$key]." // ";
    }
    if ($incorporar<>"") {
      $incorporar=substr($incorporar,0,strlen($incorporar)-4)."</span></h2></div>"; 
      echo $incorporar;
    }

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
