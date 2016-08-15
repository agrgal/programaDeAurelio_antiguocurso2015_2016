<?
include_once("./funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("./clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

$iz = "left: 120px;" ; // posición de los campos a la izquierda

session_start();

?>
<html>
<head>
<title>Edita información de PROFESORES/AS</title>
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

var guardaidprofesor;

var peticion_http;
var peticion_http2;
 
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

function cargaContenido2(url, metodo, funcion, query) {
  peticion_http2 = inicializa_xhr(); 
  if(peticion_http2) {
    peticion_http2.onreadystatechange = funcion;
    peticion_http2.open(metodo, url, true);
    peticion_http2.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    // alert(query);
    var query_string = "lee="+query;
    peticion_http2.send(query_string);
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
      // document.getElementById("inf").innerHTML = peticion_http.responseText;
      // alert(peticion_http.responseText);
      var lista = document.getElementById("profesores");
      var datos = eval("(" + peticion_http.responseText + ")"); 
      lista.options.length = 0;
      var i=0;
      for(var codigo in datos) {
        lista.options[i] = new Option(datos[codigo], codigo);
        i++;
      }
      // Lo siguiente permite, que si hay un valor guardado en guardaidprofesor lo muestre. 
      for (i=0;i<lista.length;i++) {
         if (lista[i].value==guardaidprofesor) {lista[i].selected=true;}
      }
      // document.getElementById("primero").click(); //activa la lista al primer elemento
    }
  }
}

function muestraContenido2() {
  if(peticion_http.readyState == READY_STATE_COMPLETE) {
    if(peticion_http.status == 200) {
      // document.getElementById("inf").innerHTML = peticion_http.responseText;
      var datos = eval("(" + peticion_http.responseText + ")"); 
      document.getElementById("nombre").innerHTML = datos["Empleado"];
      document.getElementById("nm").value = datos["Empleado"];
      document.getElementById("DNI").value = datos["DNI"];
      document.getElementById("IDEA").value = datos["IDEA"];
      document.getElementById("email").value = datos["email"];
      document.getElementById("tutorde").value = datos["tutorde"];
      document.getElementById("administrador").value = datos["administrador"];
    }
  }
}

function muestraContenido4() {
  if(peticion_http.readyState == READY_STATE_COMPLETE) {
    if(peticion_http.status == 200) {
      document.getElementById("nombre").innerHTML = peticion_http.responseText;
      listaprofesores();
    }
  }
} 

function muestraContenido5() {
  if(peticion_http.readyState == READY_STATE_COMPLETE) {
    if(peticion_http.status == 200) {
      document.getElementById("nombre").innerHTML = peticion_http.responseText;
      cancelar();
      listaprofesores();
    }
  }
}

function muestraContenido6() {
  if(peticion_http.readyState == READY_STATE_COMPLETE) {
    if(peticion_http.status == 200) {
      document.getElementById("nombre").innerHTML = peticion_http.responseText;
      cancelar();
      listaprofesores();
    }
  }
}

function muestraContenido7() {
  if(peticion_http2.readyState == READY_STATE_COMPLETE) {
    if(peticion_http2.status == 200) {
      // document.getElementById("nombre").innerHTML = peticion_http2.responseText;
      document.getElementById("numadministradores").value = peticion_http2.responseText;
      }
  }
}

// **************************************
// FUNCIONES DE NAVEGACION
// **************************************
function botonprimero() {
  var lista = document.getElementById("profesores");
  lista.value = lista.options[0].value;
}

function botonanterior() {
  var lista = document.getElementById("profesores");
  var indice = lista.selectedIndex;
  // alert(indice);
  if (indice>=1) {indice=indice-1; lista.value = lista.options[indice].value;}
}

function botonsiguiente() {
  var lista = document.getElementById("profesores");
  var indice = lista.selectedIndex;
  if (indice<lista.length-1) {indice=indice+1;}
  lista.value = lista.options[indice].value;
}

function botonultimo() {
  var lista = document.getElementById("profesores");
  lista.value = lista.options[lista.length-1].value;
}

function ValidaMail(mail) {
	var exr = /^[0-9a-z_\-\.]+@[0-9a-z\-\.]+\.[a-z]{2,4}$/;
	return exr.test(mail);
}


// **************************************
// FUNCIONES DE LOS BOTONES Y LOS SELECTS
// **************************************

function listaprofesores() {
   // muestra la lista de los profesores
   document.getElementById("dos").style.visibility="visible";
   document.getElementById("dos").style.display="";
   document.getElementById("tres").style.visibility="visible";
   document.getElementById("tres").style.display="";
   // alert("texto");
   cargaContenido("./profesores/profesoresdeuninstituto.php", "POST", muestraContenido, "Consigue");
   contaradministradores();
}

function informacionprofesor() {
   var lista=document.getElementById("profesores");
   var valor = lista.options[lista.selectedIndex].value; // ID del profesor
   document.getElementById("number").value=valor;
   // alert(valor);
   cargaContenido("./profesores/informacionprofesor.php", "POST", muestraContenido2, valor);
}

function actualizarprofesor() {
   // validar email
   if(!ValidaMail(document.getElementById("email").value)) {
       alert("Operación cancelada. ¡¡ La dirección de EMail es incorrecta !!");
       document.getElementById("email").value="correo@prueba.es";
       return;
   } 
   var cadena=document.getElementById("number").value;
   guardaidprofesor=cadena; // en esa variable guarda la cadena;
   // alert(guardaidprofesor);
   cadena  =cadena + "#" + document.getElementById("nm").value;
   cadena  =cadena + "#" + document.getElementById("DNI").value;
   cadena  =cadena + "#" + document.getElementById("IDEA").value;
   cadena  =cadena + "#" + document.getElementById("tutorde").value;
   cadena  =cadena + "#" + document.getElementById("email").value;
   cadena  =cadena + "#" + document.getElementById("administrador").value;
   // alert(cadena);
   cargaContenido("./profesores/cambiaprofesor.php", "POST", muestraContenido4, cadena);
}

function insertaprofesor() {  
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
     document.getElementById("DNI").value="";
     document.getElementById("IDEA").value="";
     document.getElementById("tutorde").value="";
     document.getElementById("email").value="aaa@prueba.es";
     document.getElementById("administrador").value="0";
     // cambia el nombre del botón
     document.getElementById("annadir").value="Grabar";
     // valor del cambio de lo que pone en el nombre
     document.getElementById("nombre").innerHTML="Inserta nuevo profesor/a";
  }
  if (nom=="Grabar") {
     // Inserta lo que haya. Empieza por el nombre  
     // validar email
     if(!ValidaMail(document.getElementById("email").value)) {
       alert("Operación cancelada. ¡¡ La dirección de EMail es incorrecta !!");
       document.getElementById("email").value="correo@prueba.es";
       return;
     } 
     var cadena  = document.getElementById("nm").value;
     cadena  =cadena + "#" + document.getElementById("DNI").value;
     cadena  =cadena + "#" + document.getElementById("IDEA").value;
     cadena  =cadena + "#" + document.getElementById("tutorde").value;
     cadena  =cadena + "#" + document.getElementById("email").value;
     cadena  =cadena + "#" + document.getElementById("administrador").value;
     // alert(cadena);
     cargaContenido("./profesores/insertaunprofesor.php", "POST", muestraContenido5, cadena);
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
  // Pone el valor del profesor que corresponda
  informacionprofesor();
}

function contaradministradores() {
     // alert("Llego aquí");
     cargaContenido2("./profesores/contaradministradores.php", "POST", muestraContenido7, "Consigue");
}

function borrarprofesor() {
  // Contar administradores
  var numad = document.getElementById("numadministradores").value;
  // alert("Reconozco "+numad+ " administradores/as");
  if (numad<=1 && document.getElementById("administrador").value==1) {
     alert("No puedo borrar este dato y quedarnos sin administrador. Actualiza los datos de alguien, hazlo administrador, y después podrás borrar éste.");
     return;
  }
  // ¿Estás seguro?
  var cadena  = document.getElementById("nm").value;
  cadena  =cadena + " (" + document.getElementById("DNI").value + ")";
  var pregunta  =confirm("¿De verdad que deseas borrar a "+cadena+"?");
  // Si lo está accede a la función
  if (pregunta) {
     var pasar=document.getElementById("number").value;
     cargaContenido("./profesores/borrarprofesor.php", "POST", muestraContenido6, pasar);
  }  
}

window.onload=listaprofesores;
// window.onload=contaradministradores;

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
<h1>Edita información de PROFESORES</h1>
<p>Elige primero un profesor/a; puedes editar su información, añadir uno nuevo o borrar uno existente.</p>
<h1 id="inf"></h1>
<input name="numadministradores" id="numadministradores" class="cajones" type="hidden">
<div id="uno" class="presentardatos">
   <h2 id="listadeprofesores">Escoge a un profesor/a concreto: 
     <select id="profesores" class="botones" style="text-align: left;" onChange="informacionprofesor();" onFocus="informacionprofesor();">
        <option>Escoge profesor/a</option>
     </select>
     <p style="text-align: center;">
     <input id="primero" class="botones" value="<<" onClick="botonprimero(); informacionprofesor();" size="2">
     <input id="anterior" class="botones" value="<" onClick="botonanterior(); informacionprofesor();" size="1">
     <input id="siguiente" class="botones" value=">" onClick="botonsiguiente(); informacionprofesor();" size="1">
     <input id="ultimo" class="botones" value=">>" onClick="botonultimo(); informacionprofesor();" size="2">
     </p>
   </h2>
</div>
<!-- Información de cada alumno/a -->
<div id="dos" class="presentardatos" style="visibility: hidden; display: none;">
  <div id="ponernombre" class="presentardatos2" style="width: 50%"><h2 id="nombre" style="text-align: center;">Nombre del profesor</h2></div>
  </br>
  <h2><input name="number" id="number" class="cajones" type="hidden"></h2>
  <h2 style="text-align: left;">Cambia su nombre y apellidos: 
     <input name="nm" id="nm" class="cajones" type="text" size="50">
  </h2>
  <h2>Cambio de DNI (o de contraseña): 
     <input name="DNI" id="DNI" class="cajones" type="text">
  </h2>
  <h2>Cambia de identificación IDEA: 
     <input name="IDEA" id="IDEA" class="cajones" type="text"  size="12">
  </h2>
  <h2>Tutoría por defecto: 
     <select name="tutorde" id="tutorde" class="cajones">
	<?php 
	$cursos=obtenercursos($bd);
	echo '<option value="">No es tutor/a de un curso</option>'; 
	foreach ($cursos['unidad'] as $key => $valor) {	    
	    echo '<option value="'.$valor.'">'.$valor.'</option>'; 
        }
        ?>
     </select>
  </h2>
  <h2 style="text-align: left;">Cambia de correo electrónico: <input name="email" id="email" class="cajones" type="text" size="50"></h2>
  <h2>Administrador: 0--> NO y 1 --> SÍ
     <input style="text-align: center;" name="administrador" id="administrador" class="cajones" type="text"  size="1" value="0">
  </h2>
</div>
<!-- Botones -->
<div id="tres" class="presentardatos" style="visibility: hidden; display: none;">
  <input id="annadir" class="botonesdos" value="Añadir nuevo" onClick="insertaprofesor();">
  <input id="actualizar" class="botonesdos" value="Actualizar existente" onClick="actualizarprofesor();">
  <input id="borrar" class="botonesdos" value="Borrar" onClick="borrarprofesor();">
  <input id="cancelar" style="visibility: hidden; display: none;" class="botonesdos" value="Cancelar" onClick="cancelar();">
</div>
</div> <!-- fin de la capa de información -->
</div> <!-- Fin de la capa del contenedor -->

</body>
</html>
