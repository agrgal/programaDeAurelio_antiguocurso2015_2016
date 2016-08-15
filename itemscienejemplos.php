<?php
include("./funciones/funciones.php"); /* incluye el directorio de funciones */

include_once("./clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

/* $bd="encyclonumber"; */ //debe de haberse cargado en mysql_inc.php

$iz = "left: 120px;" ; // posición de los campos a la izquierda

session_start(); /* empiezo una sesión */

if ($_SESSION['administracion']<3 || $_GET['boton']=="No") {
   echo header("Location: ./index.php");
}

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
    <h1>Introduce cien datos de ejemplo en la base de datos tb_itemsevaluacion</h1>
    <?php if (isset($_GET['boton']) AND $_GET['boton']=="Sí") {	
	// 1º) Borra los datos de la tabla
        $link=Conectarse($bd); // y me conecto. //dependiendo del tipo recupero uno u otro.
        $Sql='truncate table tb_itemsevaluacion';
        $result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado en $result
	mysql_free_result($result);

	// 2º Crear un array con varios grupos
        $grupos=array("Rendimiento","Asistencia","Comportamiento","Resultados","Respeto a compañeros/as","Respeto a profesores/as","Retrasos","Partes","Amonestaciones");
        foreach ($grupos as $word) {
	     for ($i=0;$i<10;$i++) {
		 $link=Conectarse($bd); // y me conecto. //dependiendo del tipo recupero uno u otro.
		 if ($i%3<>0) {$valor=1;} else {$valor=0;}
                 $Sql="INSERT INTO tb_itemsevaluacion (item, grupo, positivo) VALUES ('".$word." ".($i+1)."','".$word."',".$valor.")";
		 echo '<p>'.$Sql.'</p>';
		 $result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado en $result
                 mysql_free_result($result);
             }
        } //doble for
        
    ?>	
	
    <?php } else { ?>

    <form name="validarejemplos" action="./itemscienejemplos.php" method="get">
	  <p>¡Cuidado! Si pulsas que SÍ, se borrarán los datos de la tabla y se incluirán 100 de ejemplo</p>
  	  <p style="text-align: center;">
	  <input name="boton" class="botones" id="boton" value="Sí" type="submit">
	  <input name="boton" class="botones" id="boton" value="No" type="submit">
	  </p>
    </form>

    <?php } // fin del if ?>

</div>
<br><br>
</body>
</html>
