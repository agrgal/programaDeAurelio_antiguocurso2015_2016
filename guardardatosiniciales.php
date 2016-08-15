<?php
include("./funciones/funciones.php"); /* incluye el directorio de funciones */

include_once("./clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

/* $bd="encyclonumber"; */ //debe de haberse cargado en mysql_inc.php

session_start(); /* empiezo una sesión */

if ($_SESSION['administracion']<1) {
   echo header("Location: ./index.php");
}

if (isset($_GET['unidad']) && strlen($_GET['unidad'])>0) {$_SESSION['unidad']=$_GET['unidad']; $_SESSION['contador']=0;}
if (isset($_GET['materia']) && strlen($_GET['materia'])>0) {$_SESSION['materia']=$_GET['materia']; $_SESSION['contador']=0;}
if (isset($_GET['evaluacion']) && strlen($_GET['evaluacion'])>0) {$_SESSION['evaluacion']=$_GET['evaluacion']; $_SESSION['contador']=0;}
if ($_SESSION['administracion']==3) 
   {$_SESSION['profesor']=dado_Id($bd,"31667329D","idprofesor","tb_profesores","DNI");} //me pongo yo
if ((isset($_GET['unidad'])) && (strlen($_GET['unidad'])>0) && ($_SESSION['administracion']==3)) {
  $_SESSION['tutoria']=trim($_GET['unidad']);  
}

$iz = "left: 300px;" ; // posición de los campos a la izquierda

?>
<html>
<head>
<title>Edita las EVALUACIONES que existen</title>
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
	<h1>Registro de evaluación del alumnado</h1>
		  
		  <!-- *********************************************-->
		  <!-- Formulario de introducción de datos -->
		  <!-- Visualización de datos -->
		  <!-- *********************************************-->

		  <form name="editarevaluacion" action="./guardardatos.php" method="get">

 	 
			  <!-- Profesor  -->
			  <h2 style="text-align: justify; margin-left: 50px;"><?php echo 'Profesor/a: '.dado_Id($bd,$_SESSION['profesor'],'Empleado','tb_profesores','idprofesor').'<br>'; ?></h2>

			 <p style="text-align: justify;">	                  
			  <!-- Curso  --> 
                          <select name="select" class="botones" id="selectcurso"
			  onChange = "window.location.href = this.options[selectedIndex].value; this.selectedIndex=0;">
			   <option value="">Elige un curso</option>
			   <?php
			   $link=Conectarse($bd); // y me conecto. //dependiendo del tipo recupero uno u otro.
			   $Sql="SELECT DISTINCT unidad FROM tb_alumno WHERE unidad<>'' ORDER BY unidad";
			   $result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado 

			   while ($row=mysql_fetch_array($result)) {
			   echo '<option value="./guardardatosiniciales.php?unidad='.$row['unidad'].'">'.$row['unidad'].'</option>';
			   }
			   mysql_free_result($result); 
			   ?>
			  </select>
			  &nbsp;&nbsp; Curso: <!-- Curso  -->
			  <input name="item" style="min-width: 7em;" type="text" class="cajones" value="<?php echo $_SESSION['unidad']; ?>">
			  <!-- Evaluación  -->
	                   <p style="text-align: justify;">
 	 	          <select name="select" class="botones" id="selectevaluacion"
			  onChange = "window.location.href = this.options[selectedIndex].value; this.selectedIndex=0;">
			   <option value="">Elige una evaluación</option>
			   <?php
			   $link=Conectarse($bd); // y me conecto. //dependiendo del tipo recupero uno u otro.
			   $Sql="SELECT DISTINCT ideval,nombreeval FROM tb_edicionevaluaciones ORDER BY ideval";
			   $result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado
			   while ($row=mysql_fetch_array($result)) {
			   echo '<option value="./guardardatosiniciales.php?evaluacion='.$row['ideval'].'">'.$row['nombreeval'].'</option>';
			   }
			   mysql_free_result($result); 
			   ?>
			  </select>	  
                          &nbsp;&nbsp;Evaluación: <!-- Evaluación  -->
			  <input name="item" style="min-width: 10em;" type="text" class="cajones" value="<?php echo dado_Id($bd,$_SESSION['evaluacion'],'nombreeval','tb_edicionevaluaciones','ideval'); ?>"></p>

			  <!-- Materia  -->
	                  <p style="text-align: justify;">
 	 	          <select name="select" class="botones" id="selectmateria"
			  onChange = "window.location.href = this.options[selectedIndex].value; this.selectedIndex=0;">
			   <option value="">Elige una materia</option>
			   <?php
			   $link=Conectarse($bd); // y me conecto. //dependiendo del tipo recupero uno u otro.
			   $Sql="SELECT DISTINCT idmateria,Materias,Abr FROM tb_asignaturas ORDER BY Materias";
			   $result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado
			   while ($row=mysql_fetch_array($result)) {
			   echo '<option value="./guardardatosiniciales.php?materia='.$row['idmateria'].'">'.$row['Materias'].'</option>';
			   }
			   mysql_free_result($result); 
			   ?>
			  </select></p>			  
			  <p style="text-align: justify; margin-left: 50px;"> Materia: <!-- Materia  -->
			  <input name="item" style="min-width: 40em;" type="text" class="cajones"value="<?php echo dado_Id($bd,$_SESSION['materia'],'Materias','tb_asignaturas','idmateria'); ?>"></p>

<br>
<div style="width: 80%;" id="presentardatos2">
<h2 style="text-align: center;">Por favor, verifica que todos los datos <span style="color: rgb(155,0,0);">sean correctos antes de continuar.</span></h2>
<h2 style="text-align: center;">Comprueba que sean correctos tu nombre, la evaluación escogida,<br>la asignatura y el curso en cuestión.</h2>
</div>
<br> 


			  <?php // require("botonera.php"); ?>
		    </form>
</div>

</body>
</html>
