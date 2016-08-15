<?php
include("./funciones/funciones.php"); /* incluye el directorio de funciones */

include_once("./clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

/* $bd="encyclonumber"; */ //debe de haberse cargado en mysql_inc.php

session_start(); /* empiezo una sesi�n */

if ($_SESSION['administracion']<1) {
   echo header("Location: ./index.php");
}

// Si es el administrador, para que pueda tener un profesor
// if ($_SESSION['administracion']==3) 
//   {$_SESSION['profesor']=dado_Id($bd,"31667329D","idprofesor","tb_profesores","DNI");} //me pongo yo

// Asigno a la variable de sesi�n profesor el valor de la asignaci�n elegida
if ($_SESSION['administracion']==3 && isset($_POST['asignacion']) && strlen($_POST['asignacion'])>0) {
   $_SESSION['profesor']=dado_Id($bd,$_POST['asignacion'],"profesor","tb_asignaciones","idasignacion");
}

// Selecciona variables de sesi�n en funci�n de si obtiene variables POST
if (isset($_POST['evaluacion']) && strlen($_POST['evaluacion'])>0) {$_SESSION['evaluacion']=$_POST['evaluacion']; $_SESSION['contador']=0;}
if (isset($_POST['asignacion']) && strlen($_POST['asignacion'])>0) {$_SESSION['asignacion']=$_POST['asignacion']; $_SESSION['contador']=0;}
// Actualmente activas la asignacion y la evaluaci�n
   $evaluacion = dado_Id($bd,$_SESSION['evaluacion'],"nombreeval","tb_edicionevaluaciones","ideval");
   $_SESSION['materia'] = dado_Id($bd,$_SESSION['asignacion'],"materia","tb_asignaciones","idasignacion"); // guardo la materia de la asignacion
   $asignacion = iconv("UTF-8","ISO-8859-1",dado_Id($bd,$_SESSION['asignacion'],"descripcion","tb_asignaciones","idasignacion"));
   $materia = dado_Id($bd,$_SESSION['materia'],"Materias","tb_asignaturas","idmateria");
   $_SESSION['tutorada'] = dado_Id($bd,$_SESSION['asignacion'],"tutorada","tb_asignaciones","idasignacion"); 
// En caso que venga de ser administrador y el profesor no est� definido

// Haber rellenado antes los datos iniciales..
if (isset($_SESSION['profesor'])) { //si se han activado todas las variables de sesi�n
   $visualizacion=1;
} else { header ("Location: ./index.php");} // Si no existe un profesor, vuelve al �ndice

// reconocemos si estoy o no en una tutor�a
$tutoria="";
if ($_SESSION['tutorada']==1)  // no soy administrador
{
$tutoria=" - TUTOR�A"; if ($_SESSION['administracion']<3) {$_SESSION['administracion']=2;}
} else {
$tutoria=""; if ($_SESSION['administracion']<3) {$_SESSION['administracion']=1;}
}

$iz = "left: 300px;" ; // posici�n de los campos a la izquierda

?>
<html>
<head>
<title>Edita y elige asignaciones. Elige evaluaci�n.</title>

<meta http-equiv="Content-Type" content="Type=text/html; charset=iso-8859-15">
<!-- Inclyo las diferentes hojas de estilo -->
<?php include_once("./listas_de_css.php"); ?>

</head>

<body>
<!-- Capas de presentaci�n
<div id="grupo" style="position: absolute; left: 0px; top: 0px; width: 960; height: auto; z-index: 1; line-height: 14px"><img name="fondo" src="./imagenes_plantilla/plantilla.png" width="960" height="auto" border="0" alt=""></div>-->

<!-- Capa de men�: navegaci�n de la p�gina -->
<?php include_once("./lista.php"); ?>

<!-- ************************ -->
<!-- Incluir el script jquery -->
<!-- ************************ -->
<script language="javascript" src="./funciones/jquery-1.9.1.js"></script>
<script src="./funciones/jquery-ui-1.10.2.custom.js"></script>

<?php echo '
<script type="text/javascript" language="javascript">
 
var READY_STATE_UNINITIALIZED=0; 
var READY_STATE_LOADING=1; 
var READY_STATE_LOADED=2;
var READY_STATE_INTERACTIVE=3; 
var READY_STATE_COMPLETE=4;
 
var peticion_http;
var peticion_http2; // SEGUNDA PETICION

var admin = '.$_SESSION['administracion'].';

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

// ***************************************************************
// La segunda petici�n permite que se muestren dos listas a la vez
// ***************************************************************
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
      // document.getElementById("cero").innerHTML = peticion_http.responseText;
      var lista = document.getElementById("cursos");
      var datos = eval("(" + peticion_http.responseText + ")"); 
      // lista.options.length = 0;
      var i=1;
      // alert(datos["unidad"][4]);
      for(var codigo in datos["unidad"]) {   // c�difo es el n�mero de orden    
        lista.options[i] = new Option(datos["unidad"][codigo],datos["unidad"][codigo]); 
        i++;
      } 
    }
  }
}

function muestraContenido2() {
  if(peticion_http.readyState == READY_STATE_COMPLETE) {

    if(peticion_http.status == 200) {
      // document.getElementById("cero").innerHTML = peticion_http.responseText;
      var lista = document.getElementById("alumnos");
      var datos = eval("(" + peticion_http.responseText + ")"); 
      lista.options.length = 0; // borra la lista anterior
      var i=0;
      // alert(datos["unidad"][4]);
      for(var codigo in datos["alumno"]) {   // c�dido es el n�mero de orden    
        lista.options[i] = new Option(datos["alumno"][codigo],datos["idalumno"][codigo]); 
        i++;
      }
    }
  }
}

function muestraContenido3() {
  if(peticion_http.readyState == READY_STATE_COMPLETE) {
    if(peticion_http.status == 200) {
      // document.getElementById("cero").innerHTML = peticion_http.responseText;
      var lista = document.getElementById("materias");
      var datos = eval("(" + peticion_http.responseText + ")"); 
      // lista.options.length = 0; // borra la lista anterior
      var i=1;
      // alert(datos["unidad"][4]);
      for(var codigo in datos["materia"]) {   // c�dido es el n�mero de orden    
        lista.options[i] = new Option(datos["materia"][codigo],datos["idmateria"][codigo]); 
        i++;
      } 
    }
  }
}

function muestraContenido5() {
  if(peticion_http.readyState == READY_STATE_COMPLETE) {
    if(peticion_http.status == 200) {
      // document.getElementById("cero").innerHTML = peticion_http.responseText;
      var datos = eval("(" + peticion_http.responseText + ")");
      var tabla = document.getElementById("tabladatos");
      var numfilas = tabla.rows.length;
      // borra filas
      if (numfilas>1) {
        while (numfilas>1) {
           tabla.deleteRow(numfilas);
           numfilas = tabla.rows.length;
       }
      }
      // empieza a insertar filas
      for(var i in datos) {
          // alert(i);
          var row = tabla.insertRow(numfilas);
          var celda1 = row.insertCell(0);
	  var celda2 = row.insertCell(1);
	  var celda3 = row.insertCell(2);
	  var celda4 = row.insertCell(3);
          var celda5 = row.insertCell(4); 
          var mostrar ="";
          if (admin==3) {
             mostrar = datos[i].profesor+" - "+datos[i].materia;
          } else {
	     mostrar = datos[i].materia;
          } 
          celda1.innerHTML="<center>"+(parseInt(i)+1)+"-"+datos[i].idasignacion+"</center>";
          celda2.innerHTML="<center><a onClick=\"escogeasignacion("+datos[i].idasignacion+",\'"+datos[i].descripcion+"\',\'"+datos[i].materia+"\',\'"+datos[i].tutorada+"\');\">"+mostrar+"</a></center>";
          celda3.innerHTML="<center><a onClick=\"escogeasignacion("+datos[i].idasignacion+",\'"+datos[i].descripcion+"\',\'"+datos[i].materia+"\',\'"+datos[i].tutorada+"\');\">"+datos[i].descripcion+"</a></center>";
          celda4.innerHTML="<center>"+datos[i].tutorada+"</center>"; 
          // imagenes
          var pasamos = datos[i].idasignacion;
          var pasamos2 = "\'"+datos[i].descripcion+"\'"; // no funciona como no sea con comillas simples
          var pasamos3 = "\'"+datos[i].materia+"\'"; // no funciona como no sea con comillas simples 
	  var pasamos3a = datos[i].idmateria; // lo que paso es la identificacion de la materia
  	  var pasamos4 = "\'"+datos[i].datos+"\'"; // no funciona como no sea con comillas simples
          var pasamos5 = "\'"+datos[i].tutorada+"\'"; // no funciona como no sea con comillas simples
          var pasamos6 = datos[i].idprofesor; // lo que paso es la identificacion del profesor
          celda5.innerHTML= "<center><img src=\"./imagenes/otros/papelera.png\" width=\"100%\" height=\"auto\" onclick=\"borrafila("+pasamos+","+pasamos2+","+pasamos3+");\"><img src=\"./imagenes/otros/editar.png\" width=\"100%\" height=\"auto\" onclick=\"editaasignacion("+pasamos+","+pasamos2+","+pasamos3a+","+pasamos4+","+pasamos5+","+pasamos6+");\"></center>"; 
          var numfilas = tabla.rows.length;         
      }
      listamaterias(); // recarga la lista de las materias
      if (admin==3) {listaprofesores();}
    } 
  }
}

function muestraContenido6() {
  if(peticion_http.readyState == READY_STATE_COMPLETE) {
    if(peticion_http.status == 200) {
      // document.getElementById("cero").innerHTML = peticion_http.responseText;
      var texto = peticion_http.responseText;
      alert(texto); 
      // recuperadatos();
      location.reload(); // no s� por qu�, pero no se pueden hacer dos peticiones seguidas ��?? 
      // tengo que refrescar la p�gina �?     
    }
  }
}

// *****************************************
// �OJO! Utiliza una segunda petici�n: http2
// *****************************************

function muestraContenido7() {
  if(peticion_http2.readyState == READY_STATE_COMPLETE) {
    if(peticion_http2.status == 200) {
      // document.getElementById("cero").innerHTML = peticion_http2.responseText;
      var lista = document.getElementById("selectevaluacion");
      var datos = eval("(" + peticion_http2.responseText + ")"); 
      // lista.options.length = 0; // borra la lista anterior
      var i=1;
      for(var codigo in datos["nombreeval"]) {   // c�dido es el n�mero de orden    
        lista.options[i] = new Option(datos["nombreeval"][codigo],datos["ideval"][codigo]); 
        i++;
      } 
    }
  }
}

function muestraContenido8() {
  if(peticion_http2.readyState == READY_STATE_COMPLETE) {
    if(peticion_http2.status == 200) {
      // document.getElementById("cero").innerHTML = peticion_http2.responseText;
      var lista = document.getElementById("selectprofesor");
      var datos = eval("(" + peticion_http2.responseText + ")"); 
      // lista.options.length = 0; // borra la lista anterior
      var i=1;
      for(var codigo in datos["Empleado"]) {   // c�dido es el n�mero de orden    
        lista.options[i] = new Option(datos["Empleado"][codigo],datos["idprofesor"][codigo]); 
        i++;
      }
    }
  }
}


// ***************************************************************************************
// ***************************************************************************************

function listacursos() {
  // alert("Llego");
  cargaContenido("./scriptsphp/petcursos.php", "POST", muestraContenido, "Consigue");
}

function listaalumnos(curso) {
  // alert("Llego");
  cargaContenido("./scriptsphp/petalumnos.php", "POST", muestraContenido2, curso);
}

function listamaterias() {
 // alert("Llego");
 cargaContenido("./scriptsphp/petmaterias.php", "POST", muestraContenido3, "Consigue"); 
}

function obtenerasignaciones(administrador) {
 // alert("obtenida asignacion"+asignacion);
 if (administrador<3) { 
   cargaContenido("./scriptsphp/petobtenerasignacion.php", "POST", muestraContenido5, '.$_SESSION['profesor'].');
 } else {
   cargaContenido("./scriptsphp/petobtenerasignacion.php", "POST", muestraContenido5, 0); 
 }
 listaevaluaciones(); 
}

function borrafila(valor,quien,que) {
   // alert(valor+" - "+quien+" - "+que);
   var asignacioncerrada = document.getElementById("cerrarasig").value;
   var administracion = document.getElementById("administracion").value;
   // alert(administracion);
   if (administracion!=3 && asignacioncerrada=="true") {
      alert("Asignaci�n cerrada. No se pueden borrar asignaciones. S�lo editarlas.");
      return;
   }
   var confirmar = confirm ("�Est�s seguro/a de querer borrar a \'"+quien+"\' impartiendo \'"+que+"\'?");
   if (confirmar) { cargaContenido("./scriptsphp/petborrarasignacion.php", "POST", muestraContenido6, valor); }
}

function listaevaluaciones() {
 // alert("Llego");
 cargaContenido2("./scriptsphp/petevaluaciones.php", "POST", muestraContenido7, "Consigue");
}

function listaprofesores() {
 // alert("Llego");
 cargaContenido2("./scriptsphp/petprofesores.php", "POST", muestraContenido8, "Consigue");
 // Utiliza la segunda petici�n en paralelo. por eso CONTENIDO2
}



window.onload=obtenerasignaciones('.$_SESSION['administracion'].');

// *************************
// Funciones a�adidas 
// *************************

</script>'; ?>


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
<!-- ****************** -->
	<a name="anclajenombre" id="a"></a>
	<h1>Asignaciones y Evaluaci�n</h1>
	<p>Profesor/a: <?php echo cambiarnombre(dado_Id($bd,$_SESSION['profesor'],"Empleado","tb_profesores","idprofesor"));?></p>

<!-- *********************************************-->
<!-- Cerrar o no nuevas asignaciones -->
<!-- *********************************************-->
        <?php
        // Si he pulsado el bot�n, proceso contrario
        $nombre_fichero="./configuracion/config.txt"; // -> nombre del fichero
        // Leer el fichero config.txt
        $recupera=leerconfig($nombre_fichero);
        extract ($recupera,EXTR_OVERWRITE); // extrae datos de un array y las claves las convierte en variables con el valor asignado
        $cerrarasignacion=trim($cerrarasignacion); //variable de cerrar asignaci�n
        ?>

<!-- *********************************************-->
<!-- Formulario de introducci�n de datos -->
<!-- Visualizaci�n de datos -->
<!-- *********************************************-->

<form name="guardarasignaciones" action="./guardarasignaciones.php" method="post">
<?php 
if ($visualizacion==1) { // activadas todas las opciones de visualizacion  
   
?>

  <div id="cero" style="z-index:20;"></div>

  <div id="divprincipal" class="presentardatos2">
    <input name="evaluacion" id="evaluacion" class="cajones" type="hidden" value="<?php echo $_SESSION['evaluacion']; ?>">
    <input name="asignacion" id="asignacion" class="cajones" type="hidden" value="<?php echo $_SESSION['asignacion']; ?>">
    <input name="cerrarasig" id="cerrarasig" class="cajones" type="hidden" value="<?php echo $cerrarasignacion; ?>">
    <input name="administracion" id="administracion" class="cajones" type="hidden" value="<?php echo $_SESSION['administracion']; ?>">
    <h2 id="evaluacionmuestra"><?php echo "Evaluaci�n elegida: ".$evaluacion; ?></h2>
    <h2 id="asignacionmuestra">
    <?php  echo "Asignaci�n elegida: ".$asignacion." (".$materia.")".$tutoria; ?>
    </h2>
    <!-- ************************************************************************************************************************-->
    <!-- Oculto el bot�n dentro del P�RRAFO que no se ve para poder activarlo desde JAVASCRIPT (QUEDA MEJOR). al escoger EVALUACI�N o ASIGNACI�N -->
    <!-- Poner la propiedad display a "" si se quiere ver -->
    <p style="text-align:center; display:none; "><input name="boton" class="botones" width="40%" id="enviar" value="Guarda asignaci�n y evaluaci�n" title="Guarda asignaci�n y evaluaci�n" alt="Guarda asignaci�n y evaluaci�n" src="./imagenes/otros/guardarasignacion.png" type="image"></p>
    <!-- ************************************************************************************************************************-->
    <!-- <h2 style="text-align: center;"><input name="boton" class="botones" width="auto" id="enviar" value="Guarda asignaci�n y evaluaci�n" type="submit" title="Guarda asignaci�n y evaluaci�n" alt="Guarda asignaci�n y evaluaci�n"></h2> -->
 
   <!-- *********************************************-->
   <!-- Escoger evaluaci�n -->
   <!-- *********************************************-->
   <div id="dos" class="presentardatos" style="padding: 0px 0px 0px 0px;">
    <h2 style="font-size:14px;">Elige evaluaci�n de la lista de evaluaciones:
    <select id="selectevaluacion" class="botones" style="text-align: left;" onChange="escogeevaluacion(this.selectedIndex);" onFocus="listaevaluaciones();">
        <option>Escoge evaluaci�n</option>
     </select></h2>
   </div> 

   <!-- *********************************************-->
   <!-- Escoger asignacion -->
   <!-- *********************************************-->
   <div id="uno" class="presentardatos">    
     <!-- *****************************************-->
     <!-- Bot�n de nueva asignaci�n bloqueado o no -->
     <?php if ($cerrarasignacion=="true" && $_SESSION['administracion']<3) { // Si cerrar asignaci�n EST� ACTIVADA  ?>
     <div id="cero" style="position: absolute; top: 22px; right: 30px;">
	<!-- <img src="./imagenes/otros/annadir.png" width="38px" height="auto" onClick="editaasignacion(0,'',0,'','',0);"> -->
        <a href="#" class="a_demo_two" style="color:black;" title="Bloqueadas nuevas asignaciones">Nuevas Asignaciones Bloqueadas</a>
     </div>
     <?php } else { ?>
     <div id="cero" style="position: absolute; top: 22px; right: 30px;">
	<!-- <img src="./imagenes/otros/annadir.png" width="38px" height="auto" onClick="editaasignacion(0,'',0,'','',0);"> -->
        <a href="#" class="a_demo_four" style="color:black;" title="Pulsar para obtener una nueva asignaci�n" onClick="editaasignacion(0,'',0,'','',0);">Nueva asignaci�n</a>
     </div>
     <?php } ?>
     <!-- *****************************************-->


    <h2>Elige asignaci�n de la lista de asignaciones</h2>
    <br>
    <table id="tabladatos" style="margin:2px auto; height: auto; text-align: center; width: 95%;" border="1" cellpadding="1" cellspacing="1" class="tabla">
      <tr><th style="width: 5%; font-weight: bold; text-align: center;">N-Id</th><th style="width: 30%; font-weight: bold; text-align: center;">Materia</th><th style="width: 50%; font-weight: bold; text-align: center;">Descripci�n</th><th style="width: 5%; font-weight: bold; text-align: center;">Tutorada</th><th style="width: 5%; font-weight: bold; text-align: center;">AC</th></tr>
      <!-- <tr>
        <td id="celda1"></td>
	<td id="celda2"></td>
	<td id="celda3"></td>
        <td id="celda4"></td>
        <td id="celda5"></td>  
      </tr> -->
      </table>
  </div>   
  </div> <!--- Fin presentar datos escogidos: fin del divprincipal -->


  <!--- Introduce datos -->
  <div id="tres" class="presentardatos2" style="display:none; position: absolute; top: 100px; left: 5%; overflow: hidden;" >
    <h2 style="text-align: center;">Tabla de asignaciones</h2>
    <input id="identificacion" class="cajones" type="hidden" value="0">
    <input id="profesor" class="cajones" type="hidden" value="<?php echo $_SESSION['profesor']; ?>">   
    <div id="cero" style="position: absolute; top: 20px; right: 120px;">
	<!-- <img src="./imagenes/otros/guardar.png" width="50px" height="auto" onClick="cadenaguardar();"> -->
        <a href="#" class="a_demo_two" title="Pulsar para Guardar. Si usas GOOGLE CHROME manten pulsado el bot�n unos segundos para activar" style="color:black;" onClick="cadenaguardar();">Guardar</a>
    </div>
    <div id="cero2" style="position: absolute; top: 20px; right: 55px;">
	<a href="#" class="a_demo_two" style="color:black;" title="Sal de la ventana sin guardar" onClick="location.reload();">Salir</a>
        <!-- <img src="./imagenes/otros/salir.png" width="50px" height="auto" onClick="location.reload();"> -->
    </div>
   
 <div id="contenedor" style="border: 1px none black; float:none; height:auto; overflow:auto; ">
      <h2>Elige descripci�n (BORRA lo que haya y escribe al menos 10 caracteres): </h2>
      <textarea name="descripcion" class="cajones" id="descripcion" style="font-size:1.5em;" rows="2" cols="55" maxlength="100" type="text" title="Descripci�n" alt="Descripci�n">Introduce un texto de m�s de 10 caracteres</textarea>  
      <h2>Adem�s, soy tutor del grupo en esta asignaci�n:
      <select name="tutorada" id="tutorada" class="cajones" style="width: 10%; font-size: 1em;">
          <option value="1">S�</option>
          <option value="0" selected="selected">No</option> 
      </select></h2>
    </div>	
    
       <?php 
       // Oportunidad de crear una asignaci�n por parte del administrador
       if ($_SESSION['administracion']==3) {// En caso contrario no lo har� 
       ?>
          <div id="contenedor" style="border: 1px none black; float:none; height:auto; overflow:auto; ">
             <h2>Elige profesor: </h2>
             <select name="selectprofesor" id="selectprofesor" class="cajones" style="width: 90%; font-size: 1.5em;" OnFocus="listaprofesores();">
                <option value="0">Elige un profesor...</option>
             </select>  
          </div>
       <?php } ?>
   
    <div id="contenedor" style="border: 1px none black; float:none; height:auto; overflow:auto; ">
      <h2>Elige materia: </h2>
      <select name="materias" id="materias" class="cajones" style="width: 90%; font-size: 1.5em;" OnFocus="listamaterias();">
            <option value="0">Elige una materia...</option>
      </select>   
    </div>
    <br>
    <div id="contenedor" style="border: 1px none black; float:none; height:auto; overflow:auto; ">
        <h2>Elecci�n de curso y/o alumnos/as por separado: </h2> 
        <div id="contuno" class="presentardatos" style="width:35%; float:left; margin-left:10px; margin-right:5px;">
          <select name="cursos" id="cursos" class="cajones" size="10" style="width: 90%; font-size: 1.5em;" OnClick="cargaralumnado();">
            <option value="0">Curso...</option>
          </select>
       </div>
       <div id="contdos" class="presentardatos" style="width:55%; float:left; margin-left:5px; margin-right:5px;">
          <select name="alumnos" id="alumnos" class="cajones" size="13"  multiple="multiple" id="curso" style="width:90%; font-size:1.2em;">
            <option>Alumno/a...</option>
          </select>
       </div>

   </div>
  <!-- Botones -->
   <div id="contenedor" style="border: 1px none black; float:none; height:auto; overflow:auto; ">  
        <h2 style="text-align: center; margin: 10px 20px;">
        <a href="#" class="a_demo_two" style="color:black;" title="Guarda Curso Completo" onClick="seleccionacurso();">Selecciono Curso Completo</a>&nbsp;&nbsp;&nbsp;
        <a href="#" class="a_demo_two" style="color:black;" title="Guarda Alumnado Seleccionado" onClick="seleccionaalumno();">Selecciono alumnos/as por separado</a>
        </h2>
        <!-- <input name="boton" class="imagenuno" width="15%" id="guardacurso" title="Guarda Curso Completo" alt="Guarda Curso Completo" onClick="seleccionacurso();" type="button">
	<input name="boton" class="imagendos" width="15%" id="guardaalumnos" title="Guarda Alumnado Seleccionado" alt="Guarda Alumnado Seleccionado" onClick="seleccionaalumno();" type="button">
         <img width="25%" id="guardacurso" value="Guarda Curso Completo" title="Guarda Curso Completo" alt="Guarda Curso Completo" onClick="seleccionacurso();" src="./imagenes/otros/annade_clase.png">
        <img width="25%" id="guardaalumnos" value="Guarda Alumnado Seleccionado" title="Guarda Alumnado Seleccionado" alt="Guarda Alumnado Seleccionado" onClick="seleccionaalumno();" src="./imagenes/otros/annade_alumno.png"> -->
        <!-- <input name="boton" class="botones" width="auto" id="guardacurso" value="Guarda Curso Completo" title="Guarda Curso Completo" alt="Guarda Curso Completo" onClick="seleccionacurso();">
	<input name="boton" class="botones" width="auto" id="guardaalumnos" value="Guarda Alumnado Seleccionado" title="Guarda Alumnado Seleccionado" alt="Guarda Alumnado Seleccionado" onClick="seleccionaalumno();"> -->
    <div>

  <!-- Selecci�n -->
  <br>
  <div id="contenedor" class="presentardatos" style="float:none; height:auto; overflow:auto; ">
      <h2>Lista de seleccionados (para BORRAR doble click en un elemento de la lista): </h2>
      <select name="seleccionados" id="seleccionados" size="10" class="cajones" style="width: 90%; font-size: 1.5em;" ondblclick="borrardato(this);">
      </select>    
  </div>

  </div>	


<?php
} else { // si no se puede visualizar
        echo '<h2>Imposible visualizar los datos</h2>';
        echo $_SESSION['profesor'];
} ?>
        
</form> <!-- Fin del form -->
<!-- ****************** -->
</div> <!-- FIN DE LA Capa de informaci�n -->
<!-- ****************** -->

<!-- ****************** -->
<!--       Script       -->
<!-- ****************** -->
<script type="text/javascript" language="javascript">

// **********************************************************************
// al hacer clic en la primera lista, se cargan los alumnos en la segunda
// **********************************************************************
function cargaralumnado() { 
  // 1�) Obtiene el curso
  var curso = document.getElementById("cursos").value;
  // 2�) Carga la lista de alumnos
  listaalumnos(curso);
}

// ************************************************************
// Sirve para seleccionar un curso y a�adir a la lista inferior
// ************************************************************
function seleccionacurso() {
  // 1�) Carga la lista de seleccionados
  var lista = document.getElementById("seleccionados");  
  var longitud = lista.options.length;
  // 2�) Selecciona el valor del combo de cursos 
  var listacursos = document.getElementById("cursos");  
  var idcurso = listacursos.selectedIndex;
  var curso = listacursos.options[idcurso].value;
  if (curso==0) {return;} 
  // alert (curso);
  // 3�) Comprueba que no exista en el combo
  var existe = compruebadato(curso,"seleccionados");
  // 4�) Si existe no lo escribe, y si no lo a�ade al final
  if (existe==0) {
     lista.options[longitud] = new Option (curso,curso);
     ordena(lista);
     alert("A�ado el curso "+curso+" a la lista");
  } else {
     alert(curso+" ya a�adido");
  }  
}

// ************************************************************
// Sirve para seleccionar un alumno y a�adir a la lista inferior
// ************************************************************
function seleccionaalumno() {
  // 1�) Carga la lista de seleccionados
  var lista = document.getElementById("seleccionados");  
  var longitud = lista.options.length;
  var curso = document.getElementById("cursos"); 
  // 2�) Selecciona el valor del combo de cursos 
  var listaalumnos = document.getElementById("alumnos");  
  var seleccionados = new Array();
  var j=0;
  var alumnosseleccionados="";
  for(l=0;l<listaalumnos.options.length;l++) {
     if (listaalumnos.options[l].selected) {
        seleccionados[j]= new Array();
        seleccionados[j][0]=listaalumnos.options[l].text;
        seleccionados[j][1]=listaalumnos.options[l].value; 
        seleccionados[j][2]=cursos.options[cursos.selectedIndex].value; 
        // alert(seleccionados[j][0]);
        escribe = seleccionados[j][0]+" ("+seleccionados[j][2]+") ["+seleccionados[j][1]+"]";
        // 3�.- Comprueba que no existe en el combo
        existe = compruebadato(seleccionados[j][1],"seleccionados"); // compruebo no el texto, sino el valor    
        if (existe==0) {
	     lista.options[longitud+j] = new Option (escribe,seleccionados[j][1]);
	     ordena(lista);
	     alumnosseleccionados=alumnosseleccionados+escribe+" // ";
	     // 4�.- A�ado uno al contador 
             j++;
	} else {
             alert("El/la alumno/a "+escribe+" ya est� a�adido/a");
             // si est� no a�ade, ojo.
        } // fin del if        
     }
  } // fin del for  
  if (alumnosseleccionados.length>4) {alert("A�adidos "+j+" alumnos/as: "+alumnosseleccionados.substring(0,alumnosseleccionados.length-4));}
  // tama�o del select
} 

// ************************************************************
// Comprueba si un dato est� en la lista
// ************************************************************
function compruebadato(dato,listado) {
  // alert(dato);
  // alert(listado);
  var lista = document.getElementById(listado);
  var existe=0;
  for(k=0;k<lista.options.length;k++) {
      if (lista.options[k].value==dato) {existe=1;}
  }
  return existe;
}

// ************************************************************
// Borra un dato de la lista
// ************************************************************
function borrardato(listado) {
  // alert(listado);
  var aviso = confirm("�De verdad quieres borrar el dato?");
  if (!aviso) {return;}
  // 1�.- Dato seleccionado.
  listado.remove(listado.selectedIndex);
  // No hace falta ordenar, pues se ordena mientras se selecciona.
}

// ********************
// Ordena una lista
// ********************
function ordena(list) {
   var items = list.options.length;
   // alert("ordenar "+items);
   // alert(list.options[0].text);
   // create array and make copies of options in list
   var tmpArray = new Array();
   for(n=0;n<items;n++) {
	tmpArray[n] = new Array();
        tmpArray[n][0]= normalize(list.options[n].text);
	tmpArray[n][1]= list.options[n].value;
        tmpArray[n][2]= list.options[n].text;
        // alert("prueba "+list.options[n].text);
   }
   // sort options using given function
   tmpArray.sort(); // ordena por el cero, pero despu�s escribe el dos
   // make copies of sorted options back to list
   list.options.length=0; // borra el select
   for(n=0;n<items;n++) {
	list.options[n] = new Option(tmpArray[n][2],tmpArray[n][1]);
   }
}

// ****************************************
// Para ordenar la lista, quita los acentos
// ****************************************

var normalize = (function() {
  var from = "����������������������������������������������",
      to   = "AAAAAEEEEIIIIOOOOUUUUaaaaaeeeeiiiioooouuuunncc",
      mapping = {};
 
  for(var i = 0, j = from.length; i < j; i++ )
      mapping[ from.charAt( i ) ] = to.charAt( i );
 
  return function( str ) {
      var ret = [];
      for( var i = 0, j = str.length; i < j; i++ ) {
          var c = str.charAt( i );
          if( mapping.hasOwnProperty( str.charAt( i ) ) )
              ret.push( mapping[ c ] );
          else
              ret.push( c );
      }
      return ret.join( '' );
  }
 
})();


// **********************************
// Guarda una asignaci�n al escogerla
// **********************************
function cadenaguardar() {
  // 1�.- Obtiene la identificacion de la asignaci�n. 0--> Nueva
  var identificacion = document.getElementById("identificacion").value;
  // 2�.- profesor
  var pro = document.getElementById("profesor").value;
  if (admin==3) { // en el caso del modo administrador
     pro = document.getElementById("selectprofesor").value;
  } 
  // 3�.- descripcion
  var des = document.getElementById("descripcion").value;
  if (des.length<10) {
     alert("La descripci�n parece ser nula. Escribe una descripci�n de al menos 10 caracteres");  
     return;
  }
  // 4�.- tutorada
  var tutorada = document.getElementById("tutorada").value;
  // 5�.- materia
  var mat = document.getElementById("materias").value;
  if (mat==0) {
     alert("Parece que no has elegido una materia. Por favor, elige una");
     return;
  }
 // 6�.- Lista de grupos
 var lista = document.getElementById("seleccionados");  
 var longitud = lista.options.length;
 if (longitud<=0) { 
    alert("Parece que no has elegido ni grupos ni alumnos/as sueltos. Por favor, elige algunos.");
    return;
 }
 var componentes="";
 for (i=0;i<longitud;i++) {
   componentes=componentes+lista.options[i].value+"#";
 }
 componentes = componentes.substr(0,componentes.length-1);
 // 7�.- cadena
 var cadena = identificacion+"***"+pro+"***"+des+"***"+tutorada+"***"+mat+"***"+componentes;
 // alert(cadena);  
 // 8.- guardar datos
 // guardaasignacion(cadena); 
          var posting = $.post( "./scriptsphp/petguardaasignacion.php", { 
              lee: cadena,
          });
          posting.done(function(data,status) { 
              alert(data);
              location.reload();
          });
 // 9. Recarga la p�gina
 // location.reload(); 
} // fin de la funci�n cadenaguardar


// ********************
// Editar asignacion
// ********************
function editaasignacion(valor,des,mat,datos,tut,pro) {
   // 1.- Cargar el n�mero cero en el campo de identificaci�n
   var iden = document.getElementById("identificacion");
   iden.value = valor; // fuerzo a que valga el valor. Si 0--> es una nueva
   // 2.- Carga la lista de cursos
   listacursos(); 
   // 3.- Si es distinta de cero 
   if (valor>0) {
      //3a.- Poner el valor de la descripci�n
      document.getElementById("descripcion").value=des;
      //3b.- Poner el valor de la materia
      var mater1 = document.getElementById("materias");
      var nummat=mater1.options.length;
      // alert(nummat);
      for(i=0;i<nummat;i++) {
         if(mater1.options[i].value==mat) { mater1.selectedIndex=i;}
      }   
      //3b1.- Valor del profesor      
      if (admin==3) { // solo en el caso de que estemos en modo administraci�n
         var prof1 = document.getElementById("selectprofesor");
         var numprof=prof1.options.length;
         // alert(nummat);
         for(i=0;i<numprof;i++) {
            if(prof1.options[i].value==pro) { prof1.selectedIndex=i;}
         }   
      }      
      //3c.- Poner el valor de tutorada
      var tutor1 = document.getElementById("tutorada");
      var numtut=tutor1.options.length;
      if (tut=="NO") {tutnum=0;} else {tutnum=1;}
      for(i=0;i<numtut;i++) {
         if(tutor1.options[i].value==tutnum) { tutor1.selectedIndex=i;}
      }
      //3d.- Poner el valor de la cadena en la lista de las cadenas.
      var sel = document.getElementById("seleccionados");
      sel.options.length=0; // borra la lista
      // alert(datos);
      if (datos.length>0) {
      var valores = datos.split("#"); // primera divisi�n de datos
      var ambos=new Array();
      for(i=0;i<valores.length;i++) {   // c�dido es el n�mero de orden   
        ambos = valores[i].split("---"); // 2� divisi�n de datos --> nombre y valor. 
        sel.options[i] = new Option(ambos[0],ambos[1]);
        // sel.options[i] = new Option(valores[i],valores[i]);  
      }
      ordena(sel); // ordena los datos...
      } // porque si no es nuevo...
   } // fin del if
   else { //opciones para nuevo.
      document.getElementById("descripcion").value="Borra este texto y escribe lo que necesites";
      var sel = document.getElementById("seleccionados");
      sel.options.length=0; // borra la lista
   }
   // 4.- Visualiza el div
   // actuar sobre el display de la ventana
   abreventana();
   // 5.- Actualiza profesor si estoy en modo administrador
}

// ********************
// Escoge asignacion
// ********************
function escogeasignacion(valor,texto1,texto2,tut) {
   // alert("asignaci�n escogida "+valor);
   var am = document.getElementById("asignacionmuestra");
   var am2 = document.getElementById("asignacion");
   if (tut=="SI") {var texto3=" - TUTOR�A";} else {var texto3="";}
   am.innerHTML="Asignaci�n elegida: "+texto1+" ("+texto2+")"+texto3;
   am2.value=valor;
   // simula el click en el bot�n de enviar 
   document.getElementById("enviar").click();
}

// ********************
// Escoge evaluaci�n
// ********************
function escogeevaluacion(valor) {
   // alert("evaluaci�n "+valor);
   if (valor<=0) { return; }
   var ev = document.getElementById("evaluacionmuestra");
   var ev2 = document.getElementById("evaluacion");
   var lista = document.getElementById("selectevaluacion");
   ev.innerHTML="Evaluaci�n elegida: "+lista.options[valor].text;
   ev2.value=lista.options[valor].value;
   // simula el click en el bot�n de enviar 
   document.getElementById("enviar").click();
}

// ********************
// funci�n abre ventana
// ********************
function abreventana() {
  var ventana =document.getElementById("tres");
  var con1 =document.getElementById("divprincipal");
  // alert("Arriba: "+con1.offsetWidth);
  var anchonavegador = parseInt(con1.offsetWidth)+parseInt(con1.offsetLeft);
  var altonavegador = window.innerHeight;
  // alert("Alto: "+altonavegador+" - Ancho: "+anchonavegador);
  // Ancho inicial y altura inicial
  ventana.style.width="20";
  ventana.style.height="20";
  // alert("Alto: "+ventana.style.height+" - Ancho: "+ventana.style.width);
  // calcula top y left
  var ancho = parseInt(ventana.style.width);
  var alto = parseInt(ventana.style.height);
  // alert("Alto: "+alto+" - Ancho: "+ancho);
  var arriba = parseInt((altonavegador-alto)/2);
  var izquierda = parseInt((anchonavegador-ancho)/2);
  ventana.style.top = arriba.toString();
  ventana.style.left = izquierda.toString(); 
  // alert("Izquierda: "+izquierda+" - Arriba: "+arriba); 
  ventana.style.display="";  
  tiempodado(1);
}


// ********************
// funci�n temporal
// ********************
function tiempodado(tempo) {
  setTimeout("despliegaventana()", tempo);
}

// ********************
// despliega ventana
// ********************
function despliegaventana() {
  // obtiene los datos
  var ventana =document.getElementById("tres");
  var con1 =document.getElementById("divprincipal");
  var anchonavegador = parseInt(con1.offsetWidth)+parseInt(con1.offsetLeft);
  var altonavegador = window.innerHeight;
  var ancho = parseInt(ventana.style.width);
  var alto = parseInt(ventana.style.height);
  var arriba = parseInt((altonavegador-alto)/2);
  var izquierda = parseInt((anchonavegador-ancho)/2);
  if (ancho<anchonavegador) {
     ancho+=40;
     alto+=40;
     arriba=parseInt((altonavegador-alto)/2);
     izquierda=parseInt((anchonavegador-ancho)/2);
     ventana.style.top = arriba.toString();
     ventana.style.left = izquierda.toString(); 
     ventana.style.width = ancho.toString();
     ventana.style.height = alto.toString();
     // vuelve a ejecutar tiempodado
     tiempodado(1);
  } else {
     ventana.style.height = anchonavegador.toString();
     ventana.style.height = "auto";
  }
}

</script>
<!-- ****************** -->
<!-- Fin de los scripts -->
<!-- ****************** -->

</body>
</html>




