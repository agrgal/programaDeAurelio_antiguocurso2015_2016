<?php
include("./funciones/funciones.php"); /* incluye el directorio de funciones */

include_once("./clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

/* $bd="encyclonumber"; */ //debe de haberse cargado en mysql_inc.php

session_start(); /* empiezo una sesión */

if ($_SESSION['administracion']<1) {
   echo header("Location: ./index.php");
}

// Si es el administrador, para que pueda tener un profesor
// if ($_SESSION['administracion']==3) 
//   {$_SESSION['profesor']=dado_Id($bd,"31667329D","idprofesor","tb_profesores","DNI");} //me pongo yo

// Haber rellenado antes los datos iniciales..
if (isset($_SESSION['profesor'])) { //si se han activado todas las variables de sesión
   $visualizacion=1;
} else { header ("Location: ./guardardatosasignacion.php");}

$iz = "left: 300px;" ; // posición de los campos a la izquierda

?>
<html>
<head>
<title>Modifica mis datos</title>

<meta http-equiv="Content-Type" content="Type=text/html; charset=iso-8859-15">
<!-- Inclyo las diferentes hojas de estilo -->
<?php include_once("./listas_de_css.php"); ?>
</head>
<body>
<!-- Capas de presentación
<div id="grupo" style="position: absolute; left: 0px; top: 0px; width: 960; height: auto; z-index: 1; line-height: 14px"><img name="fondo" src="./imagenes_plantilla/plantilla.png" width="960" height="auto" border="0" alt=""></div>-->

<!-- Capa de menú: navegación de la página -->
<?php include_once("./lista.php"); ?>

<?php echo '
<script type="text/javascript" language="javascript">
 
var READY_STATE_UNINITIALIZED=0; 
var READY_STATE_LOADING=1; 
var READY_STATE_LOADED=2;
var READY_STATE_INTERACTIVE=3; 
var READY_STATE_COMPLETE=4;
 
var peticion_http;

function cargaContenido(url, metodo, funcion, query) {
  peticion_http = inicializa_xhr(); 
  if(peticion_http) {
    peticion_http.onreadystatechange = funcion;
    peticion_http.open(metodo, url, true);
    peticion_http.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    // alert(query);
    var query_string = "lee="+query;
    peticion_http.send(query_string);
  }
}
 
function inicializa_xhr() {
  if(window.XMLHttpRequest) {
    return new XMLHttpRequest();
  }
  else if(window.ActiveXObject) {
    return new ActiveXObject("Microsoft.XMLHTTP");
  }
}
 
function muestraContenido() {
  if(peticion_http.readyState == READY_STATE_COMPLETE) {
    if(peticion_http.status == 200) {
      // document.getElementById("cero").innerHTML = peticion_http.responseText;
      alert(peticion_http.responseText);
      location.reload(); // recargar página
    }
  }
}

// *************************
// Listado de funciones
// *************************

function ccontrasenna(valor) {
  cargaContenido("./scriptsphp/petccontrasenna.php", "POST", muestraContenido, valor);
}

function ccorreo(valor) {
  cargaContenido("./scriptsphp/petccorreo.php", "POST", muestraContenido, valor);
}

function enviacorreo(valor) {
  cargaContenido("./scriptsphp/petenviocorreodeprueba.php", "POST", muestraContenido, valor);
}

// *************************
// Funciones añadidas 
// *************************

</script>'; ?>


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
	<a name="anclajenombre" id="a"></a>
        <?php 
           $datosasignacion=obtenerdatosasignacion($bd,$_SESSION['asignacion']); 
        ?> 
	<h1><span style="color: #1111FF; font-weight:blod;">Profesor: </span><?php echo cambiarnombre(dado_Id($bd,$_SESSION['profesor'],"Empleado","tb_profesores","idprofesor")); ?></h1>

		  <!-- *********************************************-->
		  <!-- Formulario de introducción de datos -->
		  <!-- Visualización de datos -->
		  <!-- *********************************************-->

<form name="guardardatosdos" action="./guardardatosdos.php#anclajenombre" method="post">
<?php 
if ($visualizacion==1) { // activadas todas las opciones de visualizacion
?>
  <div id="cero"></div>
  <!-- Div datos -->
  <div id="presentardatos2">
     <input name="profesor" class="botones" id="profesor" value="<?php echo $_SESSION['profesor'];?>" type="hidden">
     <h2>Tu login en IDEA es: <?php echo dado_Id($bd,$_SESSION['profesor'],"IDEA","tb_profesores","idprofesor"); ?></h3>
     <?php 
     $contrasenna=trim(strtoupper(dado_Id($bd,$_SESSION['profesor'],"DNI","tb_profesores","idprofesor")));
     $tutorde=dado_Id($bd,$_SESSION['profesor'],"tutorde","tb_profesores","idprofesor");
     if ($tutorde<>"") {
        echo '<h2>En Séneca estás registrado/a como tutor/a de: ';
        echo $tutorde."</h2>";
     } // fin del if
     ?>
  </div>

  <!-- Div cambiar contraseña -->
  <div id="presentardatos2">
    <h2>Inicialmente tu DNI+Letra es la contraseña. Pero puedes cambiarla aquí.</h2>
    <h2>Escribe la contraseña actual:&nbsp;<input name="passantigua" id="passantigua" style="min-width: 9em; maxlength: 9;" type="Password" class="cajones" value=""></h2> 
    <h2>Escribe la contraseña nueva (de 4 a 9 cars.):&nbsp;<input name="passnueva" id="passnueva" style="min-width: 9em; maxlength: 9;" type="Password" class="cajones" value=""></h2>
    <h2>Confirma la contraseña nueva:&nbsp;<input name="passconf" id="passconf" style="min-width: 9em; maxlength: 9;" type="Password" class="cajones" value=""></h2>
    <input name="cambiarcont" class="botones" id="cambiarcont" value="Cambiar contraseña" type="button" alt="Cambiar contraseña" title="Cambiaecontraseña" onClick="cambiarcontrasenna('<?php echo $contrasenna;?>')">
  </div>

  <!-- Correo -->
  <div id="presentardatos2">
    <?php $correo=dado_Id($bd,$_SESSION['profesor'],"email","tb_profesores","idprofesor"); 
       $_SESSION['correo']=$correo; 
       $_SESSION['asunto']="email de prueba";
       $_SESSION['body']='<p style="text-align: center;"><img src="./imagenes_plantilla/iesseritium.png"></p>';
       $_SESSION['body'].="<p>Este es un <strong>mensaje de prueba</strong>. Si lo recibes en tu bandeja de correo, has validado correctamente tu email en la web.</p>";       
    ?>
    <h2>Modifica el correo:&nbsp;</h2><p><input name="correo" id="correo" style="min-width: 40em; maxlength: 200; " type="text" class="cajones" value="<?php echo $correo;?>"></p>
    <input name="modcorreo" class="botones" id="modcorreo" value="Modifica correo" type="button" alt="Modifica correo" title="Modifica correo" onClick="cambiarcorreo()";>
    <!-- <input name="envcorreo" class="botones" id="envcorreo" value="Envia correo de prueba" type="button" alt="Envia correo de prueba" title="Envia correo de prueba" onClick="";> -->
    <a href="./emaildeprueba.php" class="botones" id="emaildeprueba" alt="Envia email de prueba">&nbsp;Email de prueba&nbsp;</a>

  </div>
  
<?php  //Acaba la visualización
} else { // si no se puede visualizar
        echo '<h2>Imposible visualizar los datos</h2>';
} ?>
        
</form> <!-- Fin del form -->

</div> <!-- FIN DE LA Capa de información -->

<!-- ****************** -->
<!--       Script       -->
<!-- ****************** -->
<script type="text/javascript" language="javascript">

var cambiar=0;

var valores="";
var val2="";

function ocultadiv(cadena) {
   var trozos = cadena.split("***");
   for(var i=0; i< trozos.length; i++) {
      // alert(trozos[i]);
      var div = document.getElementById(trozos[i]);
      div.style.display='none';
   }
}

function muestradiv(divdado,cadena) {
   ocultadiv(cadena); // oculta todos los divs
   var div = document.getElementById(divdado);
   div.style.display='';
}

function cambiarcontrasenna(contbd) {
  //1º) Comprueba contraseña antigua
  var contantigua = document.getElementById("passantigua").value;
  contantigua=contantigua.toUpperCase();
  contbd=contbd.toUpperCase();
  if (!(contbd==contantigua)) {
     alert("Contraseña actual incorrecta");
     alert("Contraseña de la base de datos: "+contbd);
     alert("Contraseña antigua: "+contantigua);
     return;
  }
  // alert("Introduces bien la contraseña actual");  
  // 2º) Las contraseñas nueva y de confirmación son iguales
  var contnueva = document.getElementById("passnueva").value;
  var contconf = document.getElementById("passconf").value;
  var longitud = contnueva.length;
  if (!(contnueva==contconf) || longitud<4 || longitud>9 ) {
     alert("Contraseña no confirmada o longitud inadecuada. Inténtalo de nuevo. ");
     return;
  }

  // 4º) Modifica la contraseña
  var profesor = document.getElementById("profesor").value;
  ccontrasenna(profesor+"***"+contnueva);
}

function cambiarcorreo() {
  // 1º) Muestra correo
  var correo = document.getElementById("correo").value;
  // 2º) Modifica el correo
  var profesor = document.getElementById("profesor").value;
  ccorreo(profesor+"******"+correo); // 6 asteriscos. Más difícil que esté en un correo
}



</script>
<!-- ****************** -->
<!-- Fin de los scripts -->
<!-- ****************** -->

</body>
</html>




