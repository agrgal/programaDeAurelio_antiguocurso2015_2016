<?
include_once("./funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("./clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.
$iz = "left: 120px;" ; // posición de los campos a la izquierda

session_start();

?>
<html>
<head>
<title>Edita ASIGNATURAS</title>
<meta http-equiv="Content-Type" content="Type=text/html; charset=iso-8859-15">

<!-- Incluyo las diferentes hojas de estilo -->
<?php include_once("./listas_de_css.php"); ?>
</head>
<body>
<!-- Titular -->
<div class="grupo"></div>

<?php 

$nombredecampo="fec"; //nombre del campo de fecha
echo '
<script type="text/javascript" language="javascript">
 
var READY_STATE_UNINITIALIZED=0; 
var READY_STATE_LOADING=1; 
var READY_STATE_LOADED=2;
var READY_STATE_INTERACTIVE=3; 
var READY_STATE_COMPLETE=4;

var guardaidasignatura;
var estado ="normal";
 
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
      // document.getElementById("inf2").innerHTML = peticion_http.responseText;
      var lista = document.getElementById("asignaturas");
      var datos = eval("(" + peticion_http.responseText + ")"); 
      lista.options.length = 0;
      var i=0;
      for(var codigo in datos) {
        lista.options[i] = new Option(datos[codigo], codigo);
        i++;
      }
      if (estado=="actualizar") { estado="normal"; lista.value=guardaidasignatura; }
      if (estado=="borrar") { estado="normal"; cancelar();}
      if (estado=="insertar") { estado="normal"; cancelar();}      
    }
  }
}

function muestraContenido3() {
  if(peticion_http.readyState == READY_STATE_COMPLETE) {
    if(peticion_http.status == 200) {
      // document.getElementById("inf").innerHTML = peticion_http.responseText;
      var datos = eval("(" + peticion_http.responseText + ")"); 
      // var lista2 = document.getElementById("cursos2");
      // Conseguido. Escribir ahora los valores...
      document.getElementById("nombre").innerHTML=datos["materias"]+" ("+datos["abr"]+")"; // cabecera
      document.getElementById("abr").value=datos["abr"];
      document.getElementById("materias").value=datos["materias"];
    }
  }
}

function muestraContenido4() {
  if(peticion_http.readyState == READY_STATE_COMPLETE) {
    if(peticion_http.status == 200) {
      document.getElementById("nombre").innerHTML = peticion_http.responseText;
      listaasignaturas();      
    }
  }
}

function muestraContenido5() {
  if(peticion_http.readyState == READY_STATE_COMPLETE) {
    if(peticion_http.status == 200) {
      document.getElementById("nombre").innerHTML = peticion_http.responseText;
      listaasignaturas(); 
    }
  }
}

function muestraContenido6() {
  if(peticion_http.readyState == READY_STATE_COMPLETE) {
    if(peticion_http.status == 200) {
      document.getElementById("nombre").innerHTML = peticion_http.responseText;
      listaasignaturas(); 
    }
  }
}

// **************************************
// FUNCIONES DE NAVEGACION
// **************************************
function botonprimero() {
  var lista = document.getElementById("asignaturas");
  lista.value = lista.options[0].value;
}

function botonanterior() {
  var lista = document.getElementById("asignaturas");
  var indice = lista.selectedIndex;
  // alert(indice);
  if (indice>=1) {indice=indice-1; lista.value = lista.options[indice].value;}
}

function botonsiguiente() {
  var lista = document.getElementById("asignaturas");
  var indice = lista.selectedIndex;
  if (indice<lista.length-1) {indice=indice+1;}
  lista.value = lista.options[indice].value;
}

function botonultimo() {
  var lista = document.getElementById("asignaturas");
  lista.value = lista.options[lista.length-1].value;
}

// **************************************
// FUNCIONES DE LOS BOTONES Y LOS SELECTS
// **************************************
function listaasignaturas() {
  // alert(valoranno);
  cargaContenido("./asignaturas/asignaturasdeuncurso.php", "POST", muestraContenido, "Consigue");
}

function informacionasignatura() {
   // Recoge el valor de la lista
   var lista=document.getElementById("asignaturas");
   var valor = lista.options[lista.selectedIndex].value; // ID del alumno   
   document.getElementById("number").value=valor;
   guardaidasignatura=valor;
   // alert(valor);
   cargaContenido("./asignaturas/informacionasignatura.php", "POST", muestraContenido3, valor);
}

function actualizarasignatura() {
   var cadena=document.getElementById("number").value;
   guardaidasignatura=cadena; // en esa variable guarda la cadena;
   estado="actualizar";
   // alert(guardaidasignatura);
   cadena  =cadena + "#" + document.getElementById("materias").value;
   cadena  =cadena + "#" + document.getElementById("abr").value;
   // alert(cadena);
   cargaContenido("./asignaturas/cambiaasignatura.php", "POST", muestraContenido4, cadena);
}

function insertaasignatura() {  
  // según el nombre del botón se hace una cosa u otra
  var nom = document.getElementById("annadir").value
  if (nom=="Añadir nuevo") {
     // anula el botón actualizar y borrar,  y activa cancelar
     document.getElementById("actualizar").style.visibility="hidden";
     document.getElementById("actualizar").style.display="none";
     document.getElementById("borrar").style.visibility="hidden";
     document.getElementById("borrar").style.display="none";
     document.getElementById("cancelar").style.visibility="visible";
     document.getElementById("cancelar").style.display="";
     // vacía los campos y valores iniciales
     document.getElementById("materias").value="";
     document.getElementById("abr").value="";	
     // cambia el nombre del botón
     document.getElementById("annadir").value="Grabar";
     // valor del cambio de lo que pone en el nombre
     document.getElementById("nombre").innerHTML="Inserta nueva asignatura";
  }
  if (nom=="Grabar") {
     // Inserta lo que haya. Empieza por el nombre     
     var cadena  = document.getElementById("materias").value;
     estado="insertar";
     cadena  =cadena + "#" + document.getElementById("abr").value;
     // alert(cadena);
     cargaContenido("./asignaturas/insertaunaasignatura.php", "POST", muestraContenido5, cadena);
     // en muestracontenido5 se llama a cancelar
  }
}

function cancelar() {
  // activa el botón actualizar y borrar
  document.getElementById("actualizar").style.visibility="visible";
  document.getElementById("actualizar").style.display="";
  document.getElementById("borrar").style.visibility="visible";
  document.getElementById("borrar").style.display="";
  // desactiva cancelar de nuevo
  document.getElementById("cancelar").style.visibility="hidden";
  document.getElementById("cancelar").style.display="none";  
  // cambia el valor del botón añadir a Añadir nuevo
  document.getElementById("annadir").value="Añadir nuevo";
  // Pone el valor de la asignatura que corresponda
  informacionasignatura();
}

function borrarasignatura() {
  // ¿Estás seguro?
  var cadena  = document.getElementById("materias").value;
  cadena  =cadena + " (" + document.getElementById("abr").value + ")";
  var pregunta=confirm("¿De verdad que deseas borrar a "+cadena+"? Esta acción no se podrá deshacer. Se perderán los datos de los alumnos con esa asignatura si los tuviese.");
  // Si lo está accede a la función
  if (pregunta) {
     var pasar=document.getElementById("number").value;
     estado="borrar";
     cargaContenido("./asignaturas/borrarasignatura.php", "POST", muestraContenido6, pasar);
  }  
}

window.onload=listaasignaturas;

</script>';
// Fin de los scripts en JAVA
?>

<div id="contenedor">
<div id="titular" onmouseover="javascript: ocultar()"></div>

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
<h1>Edita información de ASIGNATURAS</h1>
<p>Elige primero una asignatura. Puedes editar cómo se llama y/o su abreviatura.</p>
<div id="uno" class="presentardatos">
   <h1 id="inf"></h1>
   </h2>
   <!-- <h1 id="inf"></h1> -->
   <h2 style="text-align: left;">Escoge una asignatura: 
     <select id="asignaturas" style="text-align: left;" name="asignaturas" class="botones" onChange="informacionasignatura();">
        <option value="">Elige una asignatura</option>
     </select>
     <p style="text-align: center;">
     <input id="primero" class="botones" value="<<" onClick="botonprimero(); informacionasignatura();" size="2">
     <input id="anterior" class="botones" value="<" onClick="botonanterior(); informacionasignatura();" size="1">
     <input id="siguiente" class="botones" value=">" onClick="botonsiguiente(); informacionasignatura();" size="1">
     <input id="ultimo" class="botones" value=">>" onClick="botonultimo(); informacionasignatura();" size="2">
     </p>
   </h2>
</div>
<!-- Información de cada asignatura/a -->
<div id="tres" class="presentardatos">
  <div id="ponernombre" class="presentardatos2" style="width: 50%"><h2 id="nombre" style="text-align: center;">Nombre de la asignatura</h2></div>
  </br>
  <h2><input name="number" id="number" class="cajones" type="hidden"></h2>
  <h2>Cambia nombre Asignatura o Materia</h2>
  <h2>&nbsp;&nbsp;&nbsp;
     <input name="materias" id="materias" class="cajones" type="text" size="50">
  </h2>
  <h2>Cambia de Abreviatura: 
     <input name="abr" id="abr" class="cajones" type="text" maxsize="3" size="3">
  </h2>
  </br>
</div>
<!-- Botones -->
<div id="cuatro" class="presentardatos">
  <input id="annadir" class="botonesdos" value="Añadir nuevo" onClick="insertaasignatura();">
  <input id="actualizar" class="botonesdos" value="Actualizar existente" onClick="actualizarasignatura();">
  <input id="borrar" class="botonesdos" value="Borrar" onClick="borrarasignatura();">
  <input id="cancelar" style="visibility: hidden; display: none;" class="botonesdos" value="Cancelar" onClick="cancelar();">
</div>
</div> <!-- fin de la capa de información -->

</div> <!-- Fin de la capa del contenedor -->

</body>
</html>
