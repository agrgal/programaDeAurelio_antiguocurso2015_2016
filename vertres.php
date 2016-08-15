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
$aluii=count($alumno['idalumno']);

$asignaciones = obtenerasignaciones($bd,$alumno['idalumno'],$_SESSION['tutevaluacion']); // pasa un array con los id de todos los alumnos
// obtiene un array con las distintas asignaciones que ESA EVALUACIÓN, han sido calificadas
$jj=count($asignaciones);

$items=obteneritems($bd);
$kk=count($items['iditem']);

// Ahora sí, reonocimiento de botones
if (isset($_GET['boton']) && $_GET['boton']=='Primero') {$_SESSION['contador']=0;}
if (isset($_GET['boton']) && $_GET['boton']=='Atrás') {$_SESSION['contador']--;}
if (isset($_GET['boton']) && $_GET['boton']=='Adelante') {$_SESSION['contador']++;}
if (isset($_GET['boton']) && $_GET['boton']=='Último') {$_SESSION['contador']=1000;}
if (isset($_GET['contador']) && $_GET['contador']>=0) {$_SESSION['contador']=$_GET['contador'];}

?>
<html>
<head>
<title>Filtro TRES: por items, dada evaluación, todas las asignaciones (materia-profesor/a) y alumnos</title>
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
	if ($_SESSION['contador']>$kk-1) {$_SESSION['contador']=$kk-1;} //Si supera este valor
	if ($_SESSION['contador']<0) {$_SESSION['contador']=0;} //Si es menor que cero
    ?>
    <a name="ancla"></a>
    <h2 style="text-align: center;">Alumnado de <?php echo $alumno['cadenaclases'] ?></h2>
    <h2 style="text-align: center;"><?php echo 'Evaluación: '.dado_Id($bd,$_SESSION['tutevaluacion'],"nombreeval","tb_edicionevaluaciones","ideval"); ?></h2>

     <form name="vertres" action="./vertres.php#ancla" method="get"> <!-- Principio del form -->     
     <p style="text-align: center;">
     <select name="select" class="botones" id="select2" style="text-align: left;"
	  onChange = "window.location.href = this.options[selectedIndex].value; this.selectedIndex=0;">
	  <option value="">Elige un item</option>
		<?php 
                $complemento="";
                for ($k=0;$k<$kk;$k++) {		     
		     if ($items['positivo'][$k]<=1) {
                        echo '<option value="./vertres.php?contador='.$k.'#ancla">';
			echo $items['item'][$k].' ['.trim(substr($items['grupo'][$k],0,3)).']</option>';
                     } else if ($items['positivo'][$k]>1) {
                        $complemento.='<option value="./vertres.php?contador='.$k.'#ancla">';
                        $complemento.=$items['item'][$k].' ['.trim(substr($items['grupo'][$k],0,3)).']</option>'; 
			// los guarda en al variable complemento para visualizar al final
                     }
                }
                if ($complemento<>"") {echo $complemento;}
		?>
     </select>
	<br>
	<input name="boton" class="botones" id="boton" value="Primero" type="submit" alt="Ir al primer registro" title="Ir al primer registro"  >
	<input name="boton" class="botones" id="boton" value="Atrás" type="submit" alt="Ir al registro anterior" title="Ir al registro anterior">
	<?php echo '<span style="font-size: 0.9em">'.$items['item'][$_SESSION['contador']].' ('.$items['iditem'][$_SESSION['contador']].')</span>'; ?>
	<input name="item1" class="botones" id="item1" type="hidden" value="<?php echo $items['iditem'][$_SESSION['contador']]; ?>" >
	<input name="boton" class="botones" id="boton" value="Adelante" type="submit" title="Ir al siguiente registro" alt="Ir al siguiente registro">
	<input name="boton" class="botones" id="boton" value="Último" type="submit" title="Ir al último registro" alt="Ir al último registro">
	<a href="./ficheros/vertrespdf.php" class="botones" id="pdf" alt="Genera PDF">&nbsp;PDF&nbsp;</a>
	<br>
	</p><br><hr width="80%"><br><br>
    
    <!-- Final de la botonera de items -->
    <!-- Principio de la información -->	
    <?php
    $link=Conectarse($bd); // y me conecto. //dependiendo del tipo recupero uno u otro.
    // $Sql='SELECT items, profesor, materia, alumno, observaciones FROM tb_evaluacion WHERE unidad= "'.$_SESSION['tutoria'].'" AND eval ="'.$_SESSION['tutevaluacion'].'" ORDER BY alumno, materia, profesor';
    
$Sql='SELECT items, observaciones,alumno, profesor,materia,asignacion  from tb_evaluacion inner join tb_asignaciones ON tb_evaluacion.asignacion=tb_asignaciones.idasignacion WHERE eval ="'.$_SESSION      ['tutevaluacion'].'" AND (items<>"" OR observaciones<>"") ORDER BY alumno,materia,profesor';

    // echo $Sql;
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

    $alumnado=array(); 
    $escribe=array();
    $alumnado=array_unique($cad2['alumno']); //comprueba repetidos

    foreach ($cad2['alumno'] as $clave => $alumno2) { // recorro primero todos los valores dados
         $escribe[$alumno2].=cambiarnombre($cad2['profesor'][$clave]).' - '.$cad2['materia'][$clave]." // "; // y obtengo la cadena con los profes/materias que han opinado eso.
    }

    foreach ($alumnado as $key => $valor) { // ahora con los valores no repetidos
          echo '<div name="uno" id="presentardatos" style="overflow: hidden;">';
        // Incluir una foto
          incluyefoto($valor,"0px","1%","relative","right");
        // ================== 
          echo '<h2>'.dado_Id($bd,$valor,"alumno","tb_alumno","idalumno").' ('.dado_Id($bd,$valor,"unidad","tb_alumno","idalumno").')'.'</h2>'; // su cabecera
          echo '<p>'.substr($escribe[$valor],0,strlen($escribe[$valor])-4).'</p>'; // el valor de antes
          // echo '<p>'.substr($valor,0,strlen($valor)-3).'</p>';
          echo '</div>'; 
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
