<?php
include("./funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("./clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

session_start(); /* empiezo una sesi�n */

if ($_SESSION['administracion']<1) {
   echo header("Location: ./index.php");
}

if (isset($_GET['tutevaluacion']) && strlen($_GET['tutevaluacion'])>0) {$_SESSION['tutevaluacion']=$_GET['tutevaluacion'];}

$iz = "left: 300px;" ; // posici�n de los campos a la izquierda

?>
<html>
<head>
<title>Elige una EVALUACI�N para tutor�a</title>
<meta http-equiv="Content-Type" content="Type=text/html; charset=iso-8859-15">
<!-- Inclyo las diferentes hojas de estilo -->
<?php include_once("./listas_de_css.php"); ?>
</head>
<body>
<!-- Capas de presentaci�n
<div id="grupo" style="position: absolute; left: 0px; top: 0px; width: 960; height: auto; z-index: 1; line-height: 14px"><img name="fondo" src="./imagenes_plantilla/plantilla.png" width="960" height="auto" border="0" alt=""></div>-->

<!-- Capa de men�: navegaci�n de la p�gina -->
<?php include_once("./lista.php"); ?>

<!-- Capa de estado: informaci�n �til -->
<div id="fecha">
	<p style="text-align: center;">
	<? 
	echo 'Fecha: '.$calendario->fechaformateada($calendario->fechadehoy());
	echo '<br>Hora: '.$calendario->horactual();
	?></p>
</div>

<!-- Capa de informaci�n -->
<div id="informacion" onmouseover="javascript: ocultar()">
	<h1>Tutor�as: elige evaluaci�n.</h1>
		  
		  <!-- *********************************************-->
		  <!-- Formulario de introducci�n de datos -->
		  <!-- Visualizaci�n de datos -->
		  <!-- *********************************************-->

		  <form name="editarevaluacion" action="./guardardatosinicialestutoria.php" method="get">
 	 
			  <!-- Profesor  -->
			  <h2 style="text-align: justify; margin-left: 50px;">
			  <?php 
                                // Vaciar datos de items vac�os en la tabla. Cada vez que entra-
				$iv=vaciaritemsnulos($bd);
				if ($iv<>"") { echo '<i>Nota: </i>'.$iv; }
                          ?>
                          </h2>
			  <h2 style="text-align: justify; margin-left: 50px;"><?php echo 'Profesor/a: '.dado_Id($bd,$_SESSION['profesor'],'Empleado','tb_profesores','idprofesor').'<br>'; ?></h2>

			  <!-- Evaluaci�n  -->
	                   <p style="text-align: justify;">
 	 	          <select name="select" class="botones" id="selectevaluacion" style="text-align: left;"
			  onChange = "window.location.href = this.options[selectedIndex].value; this.selectedIndex=0;">
			   <option value="">Elige una evaluaci�n</option>
			   <?php
			   $link=Conectarse($bd); // y me conecto. //dependiendo del tipo recupero uno u otro.
			   $Sql="SELECT DISTINCT ideval,nombreeval FROM tb_edicionevaluaciones ORDER BY ideval";
			   $result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado
			   while ($row=mysql_fetch_array($result)) {
			   echo '<option value="./guardardatosinicialestutoria.php?tutevaluacion='.$row['ideval'].'">'.$row['nombreeval'].'</option>';
			   }
			   mysql_free_result($result); 
			   ?>
			  </select>	  
                          &nbsp;&nbsp;Evaluaci�n: <!-- Evaluaci�n  -->
			  <input name="item" style="min-width: 10em;" type="text" class="cajones" value="<?php echo dado_Id($bd,$_SESSION['tutevaluacion'],'nombreeval','tb_edicionevaluaciones','ideval'); ?>"></p>

			
<br><br> 


			  <?php // require("botonera.php"); ?>
		    </form>
</div>

</body>
</html>
