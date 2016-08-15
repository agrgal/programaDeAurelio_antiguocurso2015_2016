<?php
include("./funciones/funciones.php"); /* incluye el directorio de funciones */

include_once("./clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

/* $bd="encyclonumber"; */ //debe de haberse cargado en mysql_inc.php

session_start(); /* empiezo una sesión */

if ($_SESSION['administracion']<3) {
   echo header("Location: ./index.php");
}

$iz = "left: 300px;" ; // posición de los campos a la izquierda

if (!isset($_GET['iditem']) OR $_GET['iditem']<=0 )
	{ $iditem = primero($bd,"tb_itemsevaluacion","iditem"); } /* si es cero o no existe, el valor 1, si no, valor 0 */
else { $iditem = $_GET['iditem'];} 

if (isset($_GET['boton'])) {
/* He pulsado el botón adelante */
if ($_GET['boton']=='Adelante') {
	$iditem = siguiente($bd,$iditem,"tb_itemsevaluacion","iditem"); /* busco el siguiente dentro del grupo */}
/* He pulsado el botón atrás */
if ($_GET['boton']=='Atrás') {
	$iditem = anterior($bd,$iditem,"tb_itemsevaluacion","iditem"); /* busco el anterior dentro del grupo */}
/* He pulsado el botón primero */
if ($_GET['boton']=='Primero') {
	$iditem = primero($bd,"tb_itemsevaluacion","iditem"); /* busco el primero dentro del grupo */}
/* He pulsado el botón último */
if (($_GET['boton']=='Último')) {
	$iditem = ultimo($bd,"tb_itemsevaluacion","iditem"); /* busco el último dentro del grupo */ }
if (($_GET['boton']=='Terminado') OR ($_GET['boton']=='Volver atrás')) {
	$iditem = $iditem; }
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
<div id="grupo" style="position: absolute; left: 0px; top: 0px; width: 960; height: auto; z-index: 1; line-height: 14px"><img name="fondo" src="./imagenes_plantilla/plantilla.png" width="960" height="auto" border="0" alt=""></div> -->

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
	<h1>Edición de los distintos items de recogida de información</h1>
		  
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
		  $Sql="SELECT iditem,item, grupo, positivo from tb_itemsevaluacion ORDER BY grupo, iditem";
		  $result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado en $result
		  $ii=1; // contador 
                  echo '<p>';
		  while ($row=mysql_fetch_array($result)) {
			if ($row['positivo']==0) { $cl="rgb(255,15,15)"; } else {$cl="black";}
			echo '(<a href="./edititems.php?iditem='.$row['iditem'].'">'.$ii.'</a>) <span style="color:'.$cl.';">'.$row['item'].' ['.strtoupper(substr($row['grupo'],0,2)).']. </span>';
                        $ii++;
		  }
                  echo '</p>';
		  mysql_free_result($result);
	
		  ?>	  
		  <form name="editarevaluacion" action="./edititems.php" method="get">
		  	   <a name="anclaje" id="a"></a>
			   <p style="text-align: center;"><select name="select" class="botones" id="select2" style="text-align: left;"
			   onChange = "window.location.href = this.options[selectedIndex].value; this.selectedIndex=0;">
			   		<option value="">Elige uno de los items</option>
					<?php 
					$link=Conectarse($bd); // y me conecto. //dependiendo del tipo recupero uno u otro.
					$Sql="SELECT iditem,item,grupo, positivo FROM tb_itemsevaluacion ORDER BY grupo, iditem";
					$result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado 						// en $result
					while ($row=mysql_fetch_array($result)) {
					 echo '<option value="./edititems.php?iditem='.$row['iditem'].'">';
					 echo $row['item'].' ['.strtoupper($row['grupo']).']. '.'</option>';
					}
					mysql_free_result($result);
					?>
			  </select>
			  </p>
			  <?php // require("botonera.php"); ?>
		  	  <p><input name="iditem" type="hidden" class="cajones" value="<?php echo $iditem; ?>"></p>

			  <!-- Nombre  -->
			  <p style="text-align: center;">Item:
			  <input name="item" style="min-width: 55em;" type="text" class="cajones" value="<?php echo trim(dado_Id($bd,$iditem,"item","tb_itemsevaluacion","iditem")); ?>"></p>
			  
			   <!-- Grupo  -->
			  <p style="text-align: center;">Grupo:
			  <input name="grupo" style="min-width: 25em;" type="text" class="cajones" value="<?php echo trim(dado_Id($bd,$iditem,"grupo","tb_itemsevaluacion","iditem")); ?>"></p>

			  <!-- Positivo  -->
			  <p style="text-align: center;">Positivo (1 positivo, 0 negativo, 2 complementos):
			  <input name="positivo" style="min-width: 1em;" type="text" class="cajones" value="<?php echo trim(dado_Id($bd,$iditem,"positivo","tb_itemsevaluacion","iditem")); ?>"></p>
			 
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
			 $iditem = $_GET['iditem'];
		  ?>	
		  <p>Item: <?php echo $_GET['item']; ?></p>
		   				  
		  <!-- Conectara a la base de datos y modificar -->
		  <?php $link=Conectarse($bd); // me conecto a la base de datos.
			    
				if ($_GET['boton']=="Modificar") {	

				$Sql="UPDATE tb_itemsevaluacion SET ";
				$Sql.="item='".ucfirst($_GET["item"])."', ";
			        $Sql.="grupo='".strtoupper($_GET["grupo"])."', ";
				$Sql.="positivo='".$_GET["positivo"]."', ";
				// $Sql.="provincia='".$_GET["provincia"]."', ";
				$Sql= substr($Sql,0,strlen($Sql)-2); /* Quitar la última coma */
				$Sql.=" WHERE iditem='".$_GET["iditem"]."'"; 				
				} /* fin del if */
				
				if ($_GET['boton']=="Aceptar") {	
				/* parte de añadir datos */	
				$Sql="INSERT INTO tb_itemsevaluacion (item, grupo, positivo) VALUES (";
				if ($_GET["item"]<>'') {$Sql.="'".ucfirst($_GET["item"])."', ";} else {$Sql.="'-',";}
				if ($_GET["grupo"]<>'') {$Sql.="'".strtoupper($_GET["grupo"])."', ";} else {$Sql.="'-',";}
				if ($_GET["positivo"]<>'') {$Sql.="'".$_GET["positivo"]."', ";} else{$Sql.="'-',";}				
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
			   		 action="./edititems.php" method="get"> 
  			   	  	 <p style="text-align: center;" >
					 <input name="iditem" value="<?php echo $iditem; ?>" size="5" maxlength="5" type="hidden">	
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
		  <form name="introducir_datos" action="./edititems.php" method="get">
		  
			  <p style="text-align: center;">
			  <input name="boton" class="botones" id="boton" value="Aceptar" type="submit">
			  <input name="boton" class="botones" id="boton" value="Borrar" type="reset">
			  </p>
			  
		  	   <!-- Item  -->
			  <p style="text-align: center;">Item:  
			  <input name="item" style="min-width: 55em; <?php echo $iz; ?>" type="text" class="cajones"  size="55" maxlength="55"></p>

			   <!-- Grupo  -->
			  <p style="text-align: center;">Grupo:
			  <input name="grupo" style="min-width: 15em; <?php echo $iz; ?>" type="text" class="cajones"  size="40" maxlength="40"></p>	

			  <!-- Grupo  -->
			  <p style="text-align: center;">Positivo (1 positivo, 0 negativo):
			  <input name="positivo" style="min-width: 2em; <?php echo $iz; ?>" type="text" class="cajones"  size="40" maxlength="40"></p>		 
			    
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
		  <p>¿Estás absolutamente seguro que quieres borrar este registro: <?php echo dado_Id($bd,$iditem,"item","tb_itemsevaluacion","iditem"); ?>?</p>
		  <form name="introducir_datos" action="./edititems.php" method="get">
		  	  <p style="text-align: center;">
			  <input name="iditem" value="<?php echo $iditem ?>" size="5" maxlength="5" type="hidden">	
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
			 	// if ((dado_Id_ies($_GET['iditem'],4)==quien_es($_SESSION['login'])) OR ($_SESSION['modo']==1))
			 	// { // si coincide el que presentó el registro con quien se ha logueado, o es un administrador...
			 	 $link=Conectarse($bd); // me conecto a la base de datos.
				 $iditem = anterior($bd,$_GET['iditem'],"tb_itemsevaluacion","iditem"); /* volveré al anterior */
		     	 $Sql="DELETE FROM tb_itemsevaluacion WHERE iditem='".$_GET['iditem']."'";
			  	 echo "<BR><p>".$Sql."</p>";
			  	 $result=mysql_query($Sql,$link); 
			  	// } 
			  	// else {echo "<BR><p>Imposible borrar el registro. No tienes permiso para ello</p>";}
			  ?>
	  	  	<form name="datos_modificados" action="./edititems.php" method="get"> 
			   	  <p style="text-align: center;" >
				  <input name="iditem" value="<?php echo $iditem; ?>" size="5" maxlength="5" type="hidden">	
				  <input name="boton" class="botones" id="boton" value="Volver atrás" type="submit"></p>
			</form>
		  <?php }  // fin de los dos if's del principio ?>
</div>

</body>
</html>
