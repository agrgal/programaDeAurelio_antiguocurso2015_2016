<?php
include("./funciones/funciones.php"); /* incluye el directorio de funciones */

include_once("./clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

/* $bd="encyclonumber"; */ //debe de haberse cargado en mysql_inc.php

$iz = "left: 120px;" ; // posición de los campos a la izquierda

session_start(); /* empiezo una sesión */

if ($_SESSION['administracion']<3) {
   echo header("Location: ./index.php");
}

if (!isset($_GET['ideval']) OR $_GET['ideval']<=0 )
	{ $ideval = primero($bd,"tb_edicionevaluaciones","ideval"); } /* si es cero o no existe, el valor 1, si no, valor 0 */
else { $ideval = $_GET['ideval'];} 

if (isset($_GET['boton'])) {
/* He pulsado el botón adelante */
if ($_GET['boton']=='Adelante') {
	$ideval = siguiente($bd,$ideval,"tb_edicionevaluaciones","ideval"); /* busco el siguiente dentro del grupo */}
/* He pulsado el botón atrás */
if ($_GET['boton']=='Atrás') {
	$ideval = anterior($bd,$ideval,"tb_edicionevaluaciones","ideval"); /* busco el anterior dentro del grupo */}
/* He pulsado el botón primero */
if ($_GET['boton']=='Primero') {
	$ideval = primero($bd,"tb_edicionevaluaciones","ideval"); /* busco el primero dentro del grupo */}
/* He pulsado el botón último */
if (($_GET['boton']=='Último')) {
	$ideval = ultimo($bd,"tb_edicionevaluaciones","ideval"); /* busco el último dentro del grupo */ }
if (($_GET['boton']=='Terminado') OR ($_GET['boton']=='Volver atrás')) {
	$ideval = $ideval; }
$boton=$_GET['boton'];
} else {
	$boton="Primero";
}/* fin del if de comprobación de que hay botón */

?>
<html>
<head>
<title>Edita las EVALUACIONES que existen</title>
<meta http-equiv="Content-Type" content="text/html;">
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
	<h1>Edición de las distintas evaluaciones que mantenemos o hemos mantenido este año</h1>
		  
		  <!-- *********************************************-->
		  <!-- Formulario de introducción de datos -->
		  <!-- Visualización de datos -->
		  <!-- *********************************************-->
		  <?php // condición para la visualización de datos
		  if (  $boton=="Primero" OR 
		  		$boton=="Atrás" OR
				$boton=="Adelante" OR
				$boton=="No" OR
				$boton=="Último" OR
				$boton=="Terminado" OR
				$boton=="Volver atrás") {

                  $link=Conectarse($bd); // y me conecto. //dependiendo del tipo recupero uno u otro.
		  $Sql="SELECT ideval,nombreeval from tb_edicionevaluaciones ORDER BY ideval";
		  $result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result
		  $ii=1; // contador 
                  echo '<p>';
		  while ($row=mysql_fetch_array($result)) {
			echo '(<a href="./editevaluaciones.php?ideval='.$row['ideval'].'">'.$ii.'</a>) '.$row['nombreeval'].'. ';
                        $ii++;
		  }
                  echo '</p>';
		  mysql_free_result($result);
	
		  ?>	  
		  <form name="editarevaluacion" action="./editevaluaciones.php" method="get">
		  	  <p style="text-align: center;"><select name="select" class="botones" id="select2" style="text-align: left;"
			   onChange = "window.location.href = this.options[selectedIndex].value; this.selectedIndex=0;">
			   		<option value="">Elige una evaluación</option>
					<?php 
					$link=Conectarse($bd); // y me conecto. //dependiendo del tipo recupero uno u otro.
					$Sql="SELECT ideval,nombreeval from tb_edicionevaluaciones ORDER BY ideval";
					$result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result
					while ($row=mysql_fetch_array($result)) {
						echo '<option value="./editevaluaciones.php?ideval='.$row['ideval'].'">';
						echo $row['nombreeval'].'</option>';
					}
					mysql_free_result($result);
					?>
			  </select>
			  </p>
			  <?php // require("botonera.php"); ?>
		  	  <p><input name="ideval" type="hidden" class="cajones" value="<?php echo $ideval; ?>"></p>

			  <!-- Nombre  -->
			  <p style="text-align: center;">Nombre:
			  <input name="nombreeval" style="min-width: 40em; <?php echo $iz; ?>" type="text" class="cajones" value="<?php echo trim(dado_Id($bd,$ideval,"nombreeval","tb_edicionevaluaciones","ideval")); ?>"></p>
			  
			 
			 
			  <?php require("botonera.php"); ?>
			   
		  </form>
		  <?php }  // fin de los dos if's del principio?>
		  <!-- *********************************************-->
		  <!-- Fin del formulario como visualizador de datos-->
		  <!-- *********************************************-->


	 	  <!-- *********************************************-->
		  <!-- Formulario de modificación de datos -->
		  <!-- *********************************************-->
		  <?php // condición para la modificación de datos
		  if (isset($_GET['boton']) AND 
		     ($_GET['boton']=="Modificar" OR $_GET['boton']=="Aceptar") ) {	
			 $ideval = $_GET['ideval'];
		  ?>	
		  <p>Nombre: <?php echo $_GET['nombreeval']; ?></p>
		   				  
		  <!-- Conectara a la base de datos y modificar -->
		  <?php $link=Conectarse($bd); // me conecto a la base de datos.
			    
				if ($_GET['boton']=="Modificar") {	

				$Sql="UPDATE tb_edicionevaluaciones SET ";
				$Sql.="nombreeval='".$_GET["nombreeval"]."', ";
			        // $Sql.="ciudad='".$_GET["ciudad"]."', ";
				// $Sql.="provincia='".$_GET["provincia"]."', ";
				$Sql= substr($Sql,0,strlen($Sql)-2); /* Quitar la última coma */
				$Sql.=" WHERE ideval='".$_GET["ideval"]."'"; 				
				} /* fin del if */
				
				if ($_GET['boton']=="Aceptar") {	
				/* parte de añadir datos */	
				$Sql="INSERT INTO tb_edicionevaluaciones (nombreeval) VALUES (";
				if ($_GET["nombreeval"]<>'') {$Sql.="'".$_GET["nombreeval"]."', ";} else {$Sql.="'-',";}
				// if ($_GET["ciudad"]<>'') {$Sql.="'".$_GET["ciudad"]."', ";} else {$Sql.="'-',";}
				// if ($_GET["provincia"]<>'') {$Sql.="'".$_GET["provincia"]."', ";} else {$Sql.="'-',";}
				$Sql=substr($Sql,0,strlen($Sql)-2); /* Quitar la última coma */
				$Sql.=")"; 
				} /* fin del if */
				
				// TANTO PARA MODIFICAR COMO PARA AÑADIR.
				// echo "<BR><p>".$Sql."</p>";
				$result=mysql_query($Sql,$link); 
				// ejecuta la cadena sql y almacena el resultado el $result
				?>	
			   
			   <form name="datos_modificados" 
			   		 action="./editevaluaciones.php" method="get"> 
  			   	  	 <p style="text-align: center;" >
					 <input name="ideval" value="<?php echo $ideval; ?>" size="5" maxlength="5" type="hidden">	
					 <input name="boton" class="botones" id="boton" value="Terminado" type="submit">
					 </p>
			   </form>
			   
		  <?php }  // fin de los dos if's del principio ?>
		  
		  <!-- *********************************************-->
		  <!-- Fin del formulario como modificador de datos -->
		  <!-- *********************************************-->
		  
		  <!-- *********************************************-->
		  <!-- Formulario para añadir nuevos datos           -->
		  <!-- **********************************************-->
		  <?php // condición para la modificación de datos
		  if (isset($_GET['boton']) AND 
		      $_GET['boton']=="Nuevo") {	
		  ?>	
		  <form name="introducir_datos" action="./editevaluaciones.php" method="get">
		  
			  <p style="text-align: center;">
			  <input name="boton" class="botones" id="boton" value="Aceptar" type="submit">
			  <input name="boton" class="botones" id="boton" value="Borrar" type="reset">
			  </p>
			  
		  	   <!-- Nombre  -->
			  <p style="text-align: center;">Nombre:  <input name="nombreeval" style="min-width: 40em; <?php echo $iz; ?>" type="text" class="cajones"  size="40" maxlength="40"></p>		  
		 
			    
			  <p style="text-align: center;">
			  <input name="boton" class="botones" id="boton" value="Aceptar" type="submit">
			  <input name="boton" class="botones" id="boton" value="Borrar" type="reset">
			  </p>
		  </form>
		   <?php }  // fin de los dos if's del principio ?>
		  <!-- *********************************************-->
		  <!-- Fin del formulario para añadir datos -->
		  <!-- Este formulario aprovecha algo del formulario de modificar datos-->
		  <!-- *********************************************-->
		  
		   <!-- *********************************************-->
		  <!-- Formulario para borrar datos_ previo       -->
		  <!-- **********************************************-->
		  <?php // condición para la modificación de datos
		  if (isset($_GET['boton']) AND 
		      $_GET['boton']=="Borrar") {	
		  ?>	
		  <p>¿Estás absolutamente seguro que quieres borrar este registro: <?php echo dado_Id($bd,$ideval,"nombreeval","tb_edicionevaluaciones","ideval"); ?>?</p>
		  <form name="introducir_datos" action="./editevaluaciones.php" method="get">
		  	  <p style="text-align: center;">
			  <input name="ideval" value="<?php echo $ideval ?>" size="5" maxlength="5" type="hidden">	
			  <input name="boton" class="botones" id="boton" value="Sí" type="submit">
			  <input name="boton" class="botones" id="boton" value="No" type="submit">
			  </p>
		  </form>
		  <?php }  // fin de los dos if's del principio ?>
		  <!-- *********************************************-->
		  <!-- Borrado de datos_previo-->
		  <!-- *********************************************-->

 		  <!-- *********************************************-->
		  <!-- Formulario para borrar datos       -->
		  <!-- **********************************************-->
		  <?php // condición para el borrado de datos
		  if (isset($_GET['boton']) AND $_GET['boton']=="Sí") 
			  {	
			 	// if ((dado_Id_ies($_GET['ideval'],4)==quien_es($_SESSION['login'])) OR ($_SESSION['modo']==1))
			 	// { // si coincide el que presentó el registro con quien se ha logueado, o es un administrador...
			 	 $link=Conectarse($bd); // me conecto a la base de datos.
				 $ideval = anterior($bd,$_GET['ideval'],"tb_edicionevaluaciones","ideval"); /* volveré al anterior */
		     	 $Sql="DELETE FROM tb_edicionevaluaciones WHERE ideval='".$_GET['ideval']."'";
			  	 echo "<BR><p>".$Sql."</p>";
			  	 $result=mysql_query($Sql,$link); 
			  	// } 
			  	// else {echo "<BR><p>Imposible borrar el registro. No tienes permiso para ello</p>";}
			  ?>
	  	  	<form name="datos_modificados" action="./editevaluaciones.php" method="get"> 
			   	  <p style="text-align: center;" >
				  <input name="ideval" value="<?php echo $ideval; ?>" size="5" maxlength="5" type="hidden">	
				  <input name="boton" class="botones" id="boton" value="Volver atrás" type="submit"></p>
			</form>
		  <?php }  // fin de los dos if's del principio ?>
</div>

</body>
</html>
