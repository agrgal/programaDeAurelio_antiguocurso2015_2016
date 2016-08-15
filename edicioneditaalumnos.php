<?
include_once("./funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("./clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.
$iz = "left: 120px;" ; // posición de los campos a la izquierda

session_start();

?>
<html>
<head>
<title>Edita información de ALUMNOS/AS</title>
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

var guardaidalumno;
 
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
      // document.getElementById("presentardatos2").innerHTML = peticion_http.responseText;
      var lista = document.getElementById("cursos");
      var lista2 = document.getElementById("cursos2");
      var datos = eval("(" + peticion_http.responseText + ")"); 
      lista.options.length = 0;
      lista.options[0] = new Option("Escoge un curso", "");
      lista2.options[0] = new Option("Escoge un curso", ""); 	
      var i=1;
      for(var codigo in datos) {
        lista.options[i] = new Option(datos[codigo], codigo);
        lista2.options[i] = new Option(datos[codigo], codigo); 
        i++;
      }
    }
  }
}

function muestraContenido2() {
  if(peticion_http.readyState == READY_STATE_COMPLETE) {
    if(peticion_http.status == 200) {
      // document.getElementById("inf").innerHTML = peticion_http.responseText;
      var lista = document.getElementById("alumnos");
      var datos = eval("(" + peticion_http.responseText + ")"); 
      lista.options.length = 0;
      var i=0;
      for(var codigo in datos) {
        lista.options[i] = new Option(datos[codigo], codigo);
        i++;
      }
      // Lo siguiente permite, que si hay un valor guardado en guardaidalumno lo muestre. 
      for (i=0;i<lista.length;i++) {
         if (lista[i].value==guardaidalumno) {lista[i].selected=true;}
      }
    }
  }
}

function muestraContenido3() {
  if(peticion_http.readyState == READY_STATE_COMPLETE) {
    if(peticion_http.status == 200) {
      // document.getElementById("inf").innerHTML = peticion_http.responseText;
      var datos = eval("(" + peticion_http.responseText + ")"); 
      var lista2 = document.getElementById("cursos2");
      // Conseguido. Escribir ahora los valores...
      document.getElementById("nombre").innerHTML=datos["apellidos"]+", "+datos["nombre"]; // cabecera
      lista2.value=datos["unidad"];      
      document.getElementById("nm").value=datos["apellidos"]+", "+datos["nombre"];
    }
  }
}

function muestraContenido4() {
  if(peticion_http.readyState == READY_STATE_COMPLETE) {
    if(peticion_http.status == 200) {
      document.getElementById("nombre").innerHTML = peticion_http.responseText;
      listalumnos();
    }
  }
}

function muestraContenido5() {
  if(peticion_http.readyState == READY_STATE_COMPLETE) {
    if(peticion_http.status == 200) {
      document.getElementById("nombre").innerHTML = peticion_http.responseText;
      cancelar();
      listalumnos();
    }
  }
}

function muestraContenido6() {
  if(peticion_http.readyState == READY_STATE_COMPLETE) {
    if(peticion_http.status == 200) {
      document.getElementById("nombre").innerHTML = peticion_http.responseText;
      cancelar();
      listalumnos();
    }
  }
}

// **************************************
// FUNCIONES DE NAVEGACION
// **************************************
function botonprimero() {
  var lista = document.getElementById("alumnos");
  lista.value = lista.options[0].value;
}

function botonanterior() {
  var lista = document.getElementById("alumnos");
  var indice = lista.selectedIndex;
  // alert(indice);
  if (indice>=1) {indice=indice-1; lista.value = lista.options[indice].value;}
}

function botonsiguiente() {
  var lista = document.getElementById("alumnos");
  var indice = lista.selectedIndex;
  if (indice<lista.length-1) {indice=indice+1;}
  lista.value = lista.options[indice].value;
}

function botonultimo() {
  var lista = document.getElementById("alumnos");
  lista.value = lista.options[lista.length-1].value;
}

// **************************************
// FUNCIONES DE LOS BOTONES Y LOS SELECTS
// **************************************
function listacursos() {
  // alert(valoranno);
  cargaContenido("./alumnos/cursos.php", "POST", muestraContenido, "Consigue");
}

function listalumnos() {
   // muestra la lista de los alumnos
   document.getElementById("dos").style.visibility="visible";
   document.getElementById("dos").style.display="";
   document.getElementById("tres").style.visibility="visible";
   document.getElementById("tres").style.display="";
   document.getElementById("cuatro").style.visibility="visible";
   document.getElementById("cuatro").style.display="";
   // rellena los datos
   var lista=document.getElementById("cursos");
   var texto = lista.options[lista.selectedIndex].text; // valor de la clase
   // alert(texto);
   cargaContenido("./alumnos/alumnosdeunaclase.php", "POST", muestraContenido2, texto);
}

function informacionalumno() {
   var lista=document.getElementById("alumnos");
   var valor = lista.options[lista.selectedIndex].value; // ID del alumno
   document.getElementById("number").value=valor;
   // alert(valor);
   cargaContenido("./alumnos/informacionalumno.php", "POST", muestraContenido3, valor);
}

function actualizaralumno() {
   var cadena=document.getElementById("number").value;
   guardaidalumno=cadena; // en esa variable guarda la cadena;
   // alert(guardaidalumno);
   cadena  =cadena + "#" + document.getElementById("nm").value;
   cadena  =cadena + "#" + document.getElementById("cursos2").value;
   // alert(cadena);
   cargaContenido("./alumnos/cambiaalumno.php", "POST", muestraContenido4, cadena);
}

function insertaalumno() {  
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
     document.getElementById("nm").value="";
     // cambia el nombre del botón
     document.getElementById("annadir").value="Grabar";
     // valor del cambio de lo que pone en el nombre
     document.getElementById("nombre").innerHTML="Inserta nuevo alumno";
  }
  if (nom=="Grabar") {
     // Inserta lo que haya. Empieza por el nombre     
     var cadena  = document.getElementById("nm").value;
     cadena  =cadena + "#" + document.getElementById("cursos2").value;
     // alert(cadena);
     cargaContenido("./alumnos/insertaunalumno.php", "POST", muestraContenido5, cadena);
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
  // Pone el valor del alumno que corresponda
  informacionalumno();
}

function borraralumno() {
  // ¿Estás seguro?
  var cadena  = document.getElementById("nm").value;
  cadena  =cadena + " (" + document.getElementById("cursos2").value + ")";
  var pregunta=confirm("¿De verdad que deseas borrar a "+cadena+"? Esta acción no se podrá deshacer. Se perderán los datos del alumno/a si los tuviese.");
  // Si lo está accede a la función
  if (pregunta) {
     var pasar=document.getElementById("number").value;
     cargaContenido("./alumnos/borraralumno.php", "POST", muestraContenido6, pasar);
  }  
}

window.onload=listacursos;

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
<h1>Edita información de ALUMNOS/AS</h1>
<p>Elige primero un curso; posteriormente, elige a un alumno y puedes editar su información, añadir uno nuevo o borrar uno existente.</p>
<div id="uno" class="presentardatos">
   <h1 id="inf"></h1>
   </h2>
   <!-- <h1 id="inf"></h1> -->
   <h2>Escoge un valor de curso: 
     <select id="cursos" name="cursos" class="botones" onChange="listalumnos(); botonprimero();" style="text-align: left;">
        <option value="">Elige un curso</option>
     </select>
   </h2>
</div>
<div id="dos" class="presentardatos" style="visibility: hidden; display: none;">
   <h2 id="listadealumnos">Escoge después un alumno/a concreto: 
     <select id="alumnos" class="botones" style="text-align: left;" onChange="informacionalumno();" onFocus="informacionalumno();">
        <option>Escoge alumno/a</option>
     </select>
     <p style="text-align: center;">
     <input id="primero" class="botones" value="<<" onClick="botonprimero(); informacionalumno();" size="2">
     <input id="anterior" class="botones" value="<" onClick="botonanterior(); informacionalumno();" size="1">
     <input id="siguiente" class="botones" value=">" onClick="botonsiguiente(); informacionalumno();" size="1">
     <input id="ultimo" class="botones" value=">>" onClick="botonultimo(); informacionalumno();" size="2">
     </p>
   </h2>
</div>
<!-- Información de cada alumno/a -->
<div id="tres" class="presentardatos" style="visibility: hidden; display: none;">
  <div id="ponernombre" class="presentardatos2" style="width: 50%"><h2 id="nombre" style="text-align: center;">Nombre del alumno</h2></div>
  </br>
  <h2><input name="number" id="number" class="cajones" type="hidden"></h2>
  <h2>Cambia nombre-apellidos (formato 1er APLL 2º APLL [coma] NOMBRE)</h2>
  <h2>&nbsp;&nbsp;&nbsp;
     <input name="nm" id="nm" class="cajones" type="text" size="50" alt="¡¡IMPORTANTE!! Ten en cuenta que deberás poner 1er apellido-2º apellido-[coma]-Nombre. Que no se te olvide la coma." title="¡¡IMPORTANTE!! Ten en cuenta que deberás poner 1er apellido-2º apellido-[coma]-Nombre. Que no se te olvide la coma.">
  </h2>
  <h2>Cambia de clase: 
     <select id="cursos2" class="botones" style="text-align: left;">
        <option value="">Elige un curso</option>
     </select>
  </h2>
  </br>
</div>
<!-- Botones -->
<div id="cuatro" class="presentardatos" style="visibility: hidden; display: none;">
  <input id="annadir" class="botonesdos" value="Añadir nuevo" onClick="insertaalumno();">
  <input id="actualizar" class="botonesdos" value="Actualizar existente" onClick="actualizaralumno();">
  <input id="borrar" class="botonesdos" value="Borrar" onClick="borraralumno();">
  <input id="cancelar" style="visibility: hidden; display: none;" class="botonesdos" value="Cancelar" onClick="cancelar();">
</div>
</div> <!-- fin de la capa de información -->

</div> <!-- Fin de la capa del contenedor -->

</body>
</html>
