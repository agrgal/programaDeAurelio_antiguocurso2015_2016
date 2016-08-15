<?php
include("./funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("./clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

/* $bd="encyclonumber"; */ //debe de haberse cargado en mysql_inc.php

$iz = "left: 120px;" ; // posición de los campos a la izquierda

session_start(); /* empiezo una sesión */

if ($_SESSION['administracion']<1) {
   echo header("Location: ./index.php");
}

// Haber rellenado antes los datos iniciales..
if (isset($_SESSION['asignacion']) && isset($_SESSION['profesor'])) { //si se han activado las variables de sesión
   $visualizacion=1;
} else { header ("Location: ./guardarasignaciones.php");}

$listaevaluaciones = obtenerlistaevaluaciones($bd); // obtiene la lista de evaluaciones

?>
<html>
<head>
<title>Edita Mis Instrumentos Evaluativos</title>
<meta http-equiv="Content-Type" content="Type=text/html; charset=iso-8859-15">

<?php include_once("./listas_de_css.php"); ?>
<link rel="stylesheet" type="text/css" href="css/uploadify/uploadify.css" />

<!-- ************************ -->
<!-- Incluir el script jquery -->
<!-- ************************ -->
<script language="javascript" src="./funciones/jquery-1.9.1.js"></script>
<script src="./funciones/jquery-ui-1.10.2.custom.js"></script>
<script src="./funciones/jquery.numeric.js"></script>
<script src="./funciones/ui.datepicker-es.js"></script>
<!-- Incluir los scripts de uploader -->
<script type="text/javascript" src="./funciones/jquery.uploadify.min.js"></script>
<script type="text/javascript" src="./funciones/jquery.uploadify.js"></script>
<script type="text/javascript" src="./funciones/swfobject.js"></script>

<script language="javascript">

    var vIndices = new Array();
    var vPorcentajes = new Array();

    // ******************************************************************
    $(document).ready(function(){ // principio del document ready  

    $("#porcentaje").numeric("."); // con jquery.numeric.js los convierte en cajas de texto numérico
    $("#notaminima").numeric("."); // con jquery.numeric.js los convierte en cajas de texto numérico

    // A) Función que permite que sea un acordeón: zonas desplegables
    $(function() {
	$("#muestrainformacion").accordion({
             collapsible: true,
             heightStyle: "content",
        });
     });

    //B) Al pulsar en la imagen ATRAS, selecciona un valor anterior del combo LISTAEVALUACIONES
    $("#atras").click(function(e){ // pulso en ANTERIOR
	  var Element = $("#listaevaluaciones > option:selected").prev('option').val(); // valor anterior
          if (Element>=0) {
                $("#listaevaluaciones").val(Element);
                $("#listaevaluacion").val(Element);
		muestrainstrumentosevaluativos();
          }
    });

    //C) Al pulsar en la imagen SIGUIENTE, selecciona un valor posterior del combo LISTAEVALUACIONES
    $("#siguiente").click(function(e){ // pulso en ANTERIOR
	  var Element = $("#listaevaluaciones > option:selected").next('option').val(); // valor anterior
          if (Element>=0) {
          	$("#listaevaluaciones").val(Element);
		$("#listaevaluacion").val(Element);
		muestrainstrumentosevaluativos();
          }
    });

    // E) Al cargar la página, recarga la lista de anotaciones
    $(window).load(function() {
      var evaluar = $("#listaevaluacion").val(); //variable de sesión. Se define en mostrarnotasinstrumentosevaluativos. Se pone en el campo listaevaluacion
      $("#listaevaluaciones").val(evaluar); // cambia en función de eso el SELECT
      muestrainstrumentosevaluativos(); // recarga los datos.
      $("#copiarh3").unbind('click'); // No se puede hacer click en él. Sólo con el botón guardar.
    });

    // F) Al cambiar el SELECT listaevaluaciones, cambia la tabla...
    $("#listaevaluaciones").change(function() {
       $("#listaevaluacion").val($("#listaevaluaciones").val());
       muestrainstrumentosevaluativos();      
    });

    // ===================================================================== 
    // G) Al pulsar sobre la tabla, detecta si lo hemos hecho
    //    sobre la papelera o sobre editar. Hay que hacerlo así porque, 
    //    al generar la tabla dinámicamente, no detecta img como objeto ¿¿??
    // ===================================================================== 
    $("#tabladatos").click(function(e){ 
        var parentTR = $(e.target).closest('tr'); // Get the parent row
        var textonombre = $("td[class='textonombre']", parentTR).html(); // Retrieve the id content
        var textoabreviatura = $("td[class='textoabreviatura']", parentTR).text(); // Retrieve the id content
        var textoporcentaje = $("td[class='textoporcentaje']", parentTR).text(); // Retrieve the id content
        var textonotaminima = $("td[class='textonotaminima']", parentTR).text(); // Retrieve the id content
        /* // var columna = parentTR 
        var columna = $('td', parentTR).index(e.target);
        // var columna = $(this).parent().children().index($(this)); 
        alert(id + " -- " + columna); */
        var identificacion = $(e.target).attr("id");
        var nombre = $(e.target).attr("name");
        if (nombre=="papelera") { borrar(identificacion,textonombre+" ("+textoabreviatura+")"); }
        if (nombre=="editar") { editar(identificacion,textonombre,textoabreviatura,textoporcentaje,textonotaminima); }
        if (nombre=="copiar") {copiar(identificacion,textonombre,textoabreviatura,textoporcentaje,textonotaminima);  }
    });

    // H) Comprobar si se ha pulsado en el acordeon
    $(".insertarh3").click(function(e){
          var active = $("#muestrainformacion").accordion("option", "active");
          if (active==1) { // Si es el 2º, que es el que está expandido, colapsa
		// alert("Pulsa y se colapsa");
                $('#nombre').val("");
                $('#abreviatura').val("");
	        $('#porcentaje').val("");
	        $('#notaminima').val(""); // Valores a cero, en todo caso...
                $("#insertareditar").html("Introduce nuevo instrumento evaluativo");
                $("#insertareditar").attr("onClick","insertar('');"); 
                $("#ins").html("Introducir un nuevo instrumento evaluativo");               
          }
    });

    // ============================================================== 
    // Función de subida de fichero
    // ============================================================== 
    $('#file_upload').uploadify({
		// 'buttonClass' : 'cajones',
                // 'buttonImage' : './imagenes/otros/subir2.png',
                'buttonText': 'Sube fichero',
                'progressData' : 'speed',
                // 'uploader'  : './ficheros/uploadify.swf',
                'swf'  : './ficheros/uploadify.swf',
		'uploader'    : './notasinstrumentosevaluativos/uploader.php',
		'cancelImg' : './imagenes/otros/cancel.png',
		'auto'      : true,
		'formData':  {'folder': '/ficheros', 'asignacion': $('#asignacion').val(), },
		// 'scriptData' : {'texto': $("#mitexto").val()},
		'onUploadSuccess' : function(file, data, response) {
 		    // alert(data);
                    $("#fotosWrapper").append(data);
                    $("#recuperar").delay(5000).slideUp(0,function() {
	                 location.reload(); // al final hay que hacer ésto
                     }); // Lo hace desaparecer a los 2.5 segundos
		}
                // Ver la documentación en: http://www.uploadify.com/documentation/
    });

    }); // Fin del document ready
    // ******************************************************************

    
    // ******************************************************************
    // *** Funciones fuera del document ready
    // ******************************************************************

    // F1) Función insertar. Inserta un dato 
    function insertar(identificacion) { 
         var nombre =  $('#nombre').val();
         var abreviatura = $('#abreviatura').val();
         var porcentaje =  $('#porcentaje').val();
         var notaminima = $('#notaminima').val();
         var evaluacion = $('#listaevaluaciones').val();
         var asignacion = $('#asignacion').val();
         // alert (nombre + " id:"+identificacion+ " "+abreviatura+" " + porcentaje+" "+notaminima);
         // alert(evaluacion);
         // detectar si son o no números correctos
         if (!nombre || !abreviatura || !porcentaje || !notaminima ) { // testea si son valores vacíos
		alert("Es obligatorio introducir todos los campos. Usa 0 si no quieres nota mínima. El porcentaje debe ser mayor que cero");
                return false; // salgo de la función
         }
	 if (porcentaje<=0 || porcentaje>100 || notaminima<0 || notaminima>10) {
		alert("Rango de datos numéricos introducidos incorrectos");
                return false; // salgo de la función
         }
         // Comprobaciones terminadas
         abreviatura = abreviatura.substr(0,3).toUpperCase(); // 3 caracteres y a mayúsculas
         // llamada al script php
         var posting = $.post( "./notasinstrumentosevaluativos/insertanotasinstrumentosevaluativos.php", { 
             nombre: nombre,
             abreviatura: abreviatura,
             asignacion: asignacion,
             porcentaje: porcentaje,
             evaluacion: evaluacion,
             notaminima: notaminima,
             id: identificacion,
         });
         posting.done(function(data,status){
            alert("El programa responde: "+data);
	    location.reload();
         });          
    } // Insertar un dato

    // F2) Función Borrar. Borra un dato 
    function borrar(idn,texto) {
         // alert(idn);
         if (confirm("¿Estas seguro de borrar el dato "+idn+": '"+texto+"'?")) {
	 var posting = $.post( "./notasinstrumentosevaluativos/borrarinstrumentosevaluativos.php", { 
             id: idn,
         });
         posting.done(function(data,status){
                // alert (status);
                if (status=="success") {
                   muestrainstrumentosevaluativos(); // vuelve a mostrar la tabla
                } else {
                   alert("El procedimiento ha fallado");
                }
         });
         } // fin de la confirmación
     }

    // F3) Función Editar. Edita un dato 
    function editar(idn,textonombre,textoabreviatura,textoporcentaje,textonotaminima){
         // alert(idn+"  "+fecha+"  "+textoanotacion);
         // valores en la edición
         $('#nombre').val(textonombre);
         $('#abreviatura').val(textoabreviatura);
	 $('#porcentaje').val(textoporcentaje);
	 $('#notaminima').val(textonotaminima);
         // cambiar el onclick del botón      
            $("#insertareditar").html("Guardar instrumento evaluativo");
            $("#insertareditar").attr("onClick","insertar('"+idn+"');");
            $("#muestrainformacion").accordion( "option", "active", 1 ); // activa segundo panel
            $("#ins").html("Edita y cambia un instrumento evaluativo existente");    
    }

    // ***********************************************************************************
    // Este bloque copia o mueve items
    // ***********************************************************************************
    // F3B) Función Copiar. Cambia o copia un dato a otra evaluación
    function copiar(idn,textonombre,textoabreviatura,textoporcentaje,textonotaminima){
         // alert(idn+"  "+fecha+"  "+textoanotacion);
         // valores en la edición
         $('#nombre').val(textonombre);
         $('#abreviatura').val(textoabreviatura);
	 $('#porcentaje').val(textoporcentaje);
	 $('#notaminima').val(textonotaminima);
         // cambiar el onclick del botón      
            $("#muevedatos").attr("onClick","muevedatos('"+idn+"');"); // para editar
            $("#muestrainformacion").accordion( "option", "active", 2 ); // activa segundo panel 
    }

    // F3B_1) Función Copiar. Cambia o copia un dato a otra evaluación
    function copiadatos () {
        if ($("#listaevaluaciones").val()==$("#listaevaluaciones2").val()) { // salir
		alert("Ambas evaluaciones no pueden ser iguales");
                return;
        }
	$("#listaevaluaciones").val($("#listaevaluaciones2").val()); // selecciono en el principal el valor del secundario
        insertar(); // Sólo tengo que insertar.
    }

    // F3B_2) Función Copiar. Cambia o copia un dato a otra evaluación
    function muevedatos(id) {
        if ($("#listaevaluaciones").val()==$("#listaevaluaciones2").val()) { // salir
		alert("Ambas evaluaciones no pueden ser iguales");
                return;
        }
        $("#listaevaluaciones").val($("#listaevaluaciones2").val()); // selecciono en el principal el valor del secundario
        insertar(id); // Sólo tengo que insertar como edición
    }
    // ***********************************************************************************
    // fin
    // ***********************************************************************************


    // F4) Función normalizar porcentajes
    function normalizar() {
	// alert("Normalizar");
        // 1º) Obtiene los ID's y los porcentajes
           // --> Se hace en muestradatos. Defino las variables globales al principio del script.
        // 2º) Calcula
        var vNuevoPorcentajes = new Float32Array();
        var suma = 0;
        for (var i in vIndices) { // calcula la suma
	    // alert("Indice: "+vIndices[i]+" - Porcentaje: "+vPorcentajes[i]);
            suma = suma + parseFloat(vPorcentajes[i]);
        }
        var vNuevoPorcentajes = []; // por si acaso, reinicia el array
        for (var i in vIndices) {
	    vNuevoPorcentajes.push(100*vPorcentajes[i]/suma); // calcula los nuevos porcentajes
            // alert("Indice: "+vIndices[i]+" - Porcentaje: "+vNuevoPorcentajes[i]);
        }
        // 3º) Edita        
        // llamada al script php
         for (var i in vIndices) { // por cada valor del índice
         var posting = $.post( "./notasinstrumentosevaluativos/normalizanotasinstrumentosevaluativos.php", { 
             porcentaje: vNuevoPorcentajes[i],
             id: vIndices[i],
         });
         posting.done(function(data,status){
            if (status=="success") {
               muestrainstrumentosevaluativos(); // en cada caso, recarga datos
            }
         });         
         } // fin del loop
    }

// F5) Guardar fichero
    function guardar() { 
           // alert("Hola");    
	   var posting = $.post( "./notasinstrumentosevaluativos/guardarficheromisinstrumentosevaluativos.php", { 
		asignacion: $('#asignacion').val(),
		evaluacion: $('#listaevaluacion').val(),
	   });
	   posting.done(function(data,status){
		 if (status=="success") {
                        // alert(data);
			// window.location.href = './ficheros/misinstrumentosevaluativos.csv';
                        window.location.href = data.substr(1,data.length); // hay que quitarle un punto.
		 }
	   });
    } // fin de la función guardar

    

      // ================================================================================= 
      // FULTIMA) Muestra los INSTRUMENTOS EVALUATIVOS por EVALUACIONES  y según ANOTACION
      // ************* Ponerla FUERA del documment ready para que funcione mejor.
      // ================================================================================= 
    function muestrainstrumentosevaluativos() { 
         var asignacion = $('#asignacion').val();
	 var evaluacion = $('#listaevaluaciones').val();
         // alert(asignacion+" - "+evaluacion);
      var posting = $.post( "./notasinstrumentosevaluativos/mostrarnotasinstrumentosevaluativos.php", { 
             asignacion: asignacion,
             evaluacion: evaluacion,
      });
      posting.done(function(data,status){
         var datos = $.parseJSON(data); // en la variable datos paso los datos tipo JSON
         // alert (data);
         if (data.length>2) {
  		 $("#tabladatos").find("tr:gt(0)").remove(); // Borra todo menos la primera fila.
                 $("#tabladatos").show();
                 vIndices=[]; vPorcentajes=[]; // Vacío los arrays de índices y porcentajes
		 for (var i in datos) {
		     // alert(datos[i].fecha);
                     var introducir = '<tr><td class="id" style="text-align: center;">' + i +
		      '</td><td class="textonombre" style="text-align: center;">' + decodificar(datos[i].nombre)+
		      '</td><td class="textoabreviatura" style="text-align: center; padding: 0.5em 0.2em;">' + decodificar(datos[i].abreviatura) + 
		      '</td><td class="textoporcentaje" style="text-align: center;">' + datos[i].porcentaje+
		      '</td><td class="textonotaminima" style="text-align: center; padding: 0.5em 0.2em;">' + datos[i].notaminima + 
		      // '</td><td class="textoanotacion" alt="'+datos[i].anotacion+'" style="text-align: left; padding: 0.5em 0.5em;">' + datos[i].anotacion2 + 
                      '</td><td style="text-align: center;"><img name="papelera" id="'+i+'" src="./imagenes/otros/papelera.png" width="50%" height="auto">' +
                      '</td><td style="text-align: center;"><img name="editar" id="'+i+'" src="./imagenes/otros/editar.png" width="50%" height="auto">' +
                      '</td><td style="text-align: center;"><img name="copiar" id="'+i+'" src="./imagenes/otros/guardar.png" width="50%" height="auto">' +
		      '</td></tr>';
		     $("#tabladatos").append(introducir);
                     vIndices.push(i); // Introduce valores de índices
                     vPorcentajes.push(datos[i].porcentaje); // Introduce valores de porcentajes
		 } // fin del for  

         } else {
                 $("#tabladatos").find("tr:gt(0)").remove(); // Borra todo menos la primera fila.
                 $("#tabladatos").show();
	         $("#tabladatos").append('<tr><td colspan="8"><h2 style="text-align: center;">No hay datos</h2></td></tr>');
         } // fin del if
      }); 
      }  // FIN DE MOSTRAR INSTRUMENTOS EVALUATIVOS */
</script>

<script language="javascript">
// Esta función permite DECODIFICAR contenido html pasado por htmlentities a html.
function decodificar(dd) {   
   var decoded = $("<div/>").html(dd).text();
   // alert(decoded);
   var decoded = decoded.replace('<p','<p style="margin: 0px 0px;" ');
   return decoded;
}
</script>
<!--
// ================================================================== 
// Acabamos el script de jquery...
// ================================================================== --> 

</head>
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

<!-- ================================================================== -->
<!-- ================================================================== -->
<!-- Capa de información -->
<div id="informacion" onmouseover="javascript: ocultar()">
<!-- ================================================================== -->
<!-- ================================================================== -->

<?php 
    // variable que obtiene datos
    $datosasignacion=obtenerdatosasignacion($bd,$_SESSION['asignacion']); 
    $alumno=obteneralumnosasignacion($bd,$_SESSION['asignacion']); // array para introducir datos de los alumnos
    $ii=count($alumno['idalumno']);
?> 
<!-- ================================== -->
<!-- Muestra los datos de la asignación -->
<!-- ================================== -->
	<p><span style="color: #1111FF; font-weight:blod;">Profesor: </span><?php echo cambiarnombre($datosasignacion["profesor"]); ?>
        -
        <span style="color: #1111FF; font-weight:blod;">Materia: </span><?php echo $datosasignacion["materia"].' '.$datosasignacion["tutorada"];?></p>
        <p><span style="color: #1111FF; font-weight:blod;">Descripción: </span><?php echo $datosasignacion["descripcion"]; ?>
        -
        <span style="color: #1111FF; font-weight:blod;">Evaluación: </span>
        <?php echo dado_Id($bd,$_SESSION['evaluacion'],"nombreeval","tb_edicionevaluaciones","ideval"); ?>
        -
        <span style="color: #1111FF; font-weight:blod;">Clases: </span>
        <?php echo $alumno['cadenaclases']; ?></p>

<!-- == variables de sesión == -->
<input id="profesor" class="botones" type="hidden" style="text-align: left;" size="38" maxlength="250" value="<?php echo $_SESSION['profesor'];?>">
<input id="asignacion" class="botones" type="hidden" style="text-align: left;" size="38" maxlength="250" value="<?php echo $_SESSION['asignacion'];?>">
<input id="listaevaluacion" class="botones" type="hidden" style="text-align: left;" size="38" maxlength="250" value="<?php echo $_SESSION['listaevaluacion'];?>">
<!-- ================================================================== -->

<!-- ================================== -->
<!-- ********** Evaluaciones ********** -->
<!-- ================================== -->
<div id="evaluaciones" class="presentardatos" style="overflow: auto; width: 80%;">
<table width="100%"><tr style="vertical-align:middle;">
     <td width="5%" style="border: 0px solid black;"><img id="atras" src="./imagenes/otros/back.png" width="30px"></td>
     <td width="80%" style="vertical-align:middle; border: 0px solid black; font-weight: bold; font-size: 1.4em;">
	<!-- Escribir aqui la evaluacion -->
        <select id="listaevaluaciones" class="botones" style="font-size: 1.1em; margin: 0px 0px;">
           <?php 
		foreach ($listaevaluaciones['idlistaevaluaciones'] as $key => $valor) {
                   echo '<option value="'.$valor.'">'.$listaevaluaciones['nombre'][$key].' ('.$calendario->fechaformateada($listaevaluaciones['fechaini'][$key]).' - '.$calendario->fechaformateada($listaevaluaciones['fechafin'][$key]).')</option>';
                }
           ?>
        </select>
     </td>
     <td width="5%" style="border: 0px solid black;"><img id="siguiente" src="./imagenes/otros/next.png" width="30px"></td>
</tr></table>        
</div>
<!-- ================================================================== -->

<div id="muestrainformacion" style="overflow: auto; width: 95%; margin: 0px auto;">
<!-- ***************** -->
<!-- Mostrar datos     -->
<!-- ***************** -->
	 <h3 class="insertarh3">Presentación de los instrumentos evaluativos de esta evaluación</h3>
         <div id="datos" style="border: 0px solid red; position: relative;">
		<table id="tabladatos" style="display: auto; margin:2px auto; height: auto; text-align: center; width: 98%; table-layout: fixed;" border="1" cellpadding="1" cellspacing="1" class="tabla">
		<tr style="vertical-align: middle;">
                <th style="width: 5%; font-weight: bold; text-align: center;">N-Id</th>
                <th style="width: 48%; font-weight: bold; text-align: center;">Nombre</th>
                <th style="width: 15%; font-weight: bold; text-align: center;">Abreviatura</th>
                <th style="width: 8%; font-weight: bold; text-align: center;">%</th>
                <th style="width: 8%; font-weight: bold; text-align: center;">Nota Minima</th>
                <th style="width: 6%; font-weight: bold; text-align: center;">Borrar</th>
                <th style="width: 6%; font-weight: bold; text-align: center;">Editar</th>
                <th style="width: 6%; font-weight: bold; text-align: center; word-wrap:break-word;">Mov/Cop</th></tr>
		</table>
                <div id="cero" style="position: relative; border: 0px solid black; top: 5px; width: auto; height:50px; overflow: hidden;">
                <a id="normalizar" style="margin: 10px; top: 15px; color: black;" class="a_demo_two" onclick="normalizar();">Normalizar porcentajes</a>
                </div>
        </div>   
<!-- ***************** -->
<!-- Introducir datos  -->
<!-- ***************** -->
	<h3 class="insertarh3" id="ins">Introducir un nuevo instrumento evaluativo</h3>
	<div id="insertar" style="border: 0px solid red; position: relative;">
        <!-- Botón insertar -->
        <div id="cero" style="position: absolute; border: 0px solid black; right: 2%; bottom: 15%; width: auto; height:60px; overflow: hidden;">
        <a id="insertareditar" style="margin: 10px; top: 15px; color: black;" class="a_demo_four" onclick="insertar('');">Insertar nuevo instrumento evaluativo </a>
         </div>
        <p style="padding: 0px 0px;">Porcentaje: introduce un número entre 0 y 100. Nota mínima: entre 0 (inactivo) y 10. Para decimales, usa un punto (.)</p>
	<input id="IDiev" class="botones" type="hidden" style="text-align: left;" size="60" maxlength="200">
	<div style="border: 0px solid black; padding: 0px 5px 25px 5px;"><p>
        Nombre:&nbsp;
	<input id="nombre" class="botones" type="text" style="text-align: left;" size="60" maxlength="200">
	<br>Abreviatura:&nbsp;
	<input id="abreviatura" class="botones" type="text" style="text-align: center;" size="3" maxlength="3">
	<br>Porcentaje:&nbsp;
	<input id="porcentaje" class="botones" type="text" style="text-align: center;" size="5" maxlength="5">
        &nbsp;&nbsp;&nbsp;&nbsp; Nota mínima:&nbsp;
	<input id="notaminima" class="botones" type="text" style="text-align: center;" size="5" maxlength="5">
        </p></div> 
	</div>
<!-- ***************** -->
<!-- Copiar/mover datos  -->
<!-- ***************** -->
	<h3 class="insertarh3" id="copiarh3">Copia valor a otra evaluación o Muévelo (Pulsa <img name="copiar" id="'+i+'" src="./imagenes/otros/guardar.png" width="15px" height="15px" align="absmiddle"> en la tabla)</h3>
	<div id="copiarmover" style="border: 0px solid red; position: relative;">
	<!-- Escribir aqui la evaluacion -->
        <p>A esta evaluación...
        <select id="listaevaluaciones2" class="botones" style="font-size: 1.1em; margin: 0px 0px;">
           <?php 
		foreach ($listaevaluaciones['idlistaevaluaciones'] as $key => $valor) {
                   echo '<option value="'.$valor.'">'.$listaevaluaciones['nombre'][$key].' ('.$calendario->fechaformateada($listaevaluaciones['fechaini'][$key]).' - '.$calendario->fechaformateada($listaevaluaciones['fechafin'][$key]).')</option>';
                }
           ?>
        </select><p>
        <div id="cero" style="position: relative; border: 0px solid black; top: 5px; width: auto; height:50px; overflow: hidden;">
        <a id="copiadatos" style="margin: 10px; top: 15px; color: black;" class="a_demo_two" onclick="copiadatos();">Copia datos a esta evaluación</a>
        <a id="muevedatos" style="margin: 10px; top: 15px; color: black;" class="a_demo_two" onclick="muevedatos();">Mueve datos a esta evaluación</a>
	</div>

	</div>
<!-- ************************ -->
<!-- Guardar en un fichero    -->
<!-- ************************ -->
	<h3 class="insertarh3">Guarda los instrumentos evaluativos en un fichero</h3>
	<div id="guardar" style="border: 0px solid red; position: relative;">
	    <div id="cero3" style="position: relative; border: 0px solid black; top: 5px; width: auto; height:50px; overflow: hidden;">
                <a id="guardar" style="margin: 10px; top: 15px; color: black;" class="a_demo_two" onclick="guardar();">Guarda copia en CSV de los datos</a>
             </div>
	</div>
<!-- ******************************* -->
<!-- Recupera datos desde fichero    -->
<!-- ******************************* -->
	<h3 class="insertarh3">Recupera datos desde fichero</h3>
	<div id="recuperar" style="border: 0px solid red; position: relative; overflow: hidden;">
	  <h2 style="text-align: center;">Para subir un fichero, pulsa el botón de carga</h2>
	  <table style="width: auto; border: 0px solid black; margin: 1px auto;"><tr><td>
	  <input id="file_upload" name="file_upload" type="file" multiple="true">
	  </td></tr></table>
	  <!-- <input type="text" size="25" name="mensaje" id="mitexto" /> -->
	  <div id="fotosWrapper">El sistema responde: </div>
 	</div>
<!-- ***************** -->
<!-- Otro              -->
<!-- ***************** -->
	<!-- <h3 class="insertarh3">Section 4</h3>
	<div>
	<p>
	Cras dictum. Pellentesque habitant morbi tristique senectus et netus
	et malesuada fames ac turpis egestas. Vestibulum ante ipsum primis in
	faucibus orci luctus et ultrices posuere cubilia Curae; Aenean lacinia
	mauris vel est.
	</p>
	<p>
	Suspendisse eu nisl. Nullam ut libero. Integer dignissim consequat lectus.
	Class aptent taciti sociosqu ad litora torquent per conubia nostra, per
	inceptos himenaeos.
	</p>
	</div> -->
</div>


  <!-- ===================== -->
  <!-- Para subir un fichero -->
  <!-- ===================== -->
  <!-- <div id="subirfichero" class="presentardatos2" style="text-align: center; overflow: auto; width: 75%; display: none;">
  <h2 style="text-align: center;">Para subir un fichero, pulsa el botón de carga</h2>
  <table style="width: auto; border: 0px solid black; margin: 1px auto;"><tr><td>
  <input id="file_upload" name="file_upload" type="file" multiple="true">
  </td></tr></table>
  <!-- <input type="text" size="25" name="mensaje" id="mitexto" />
  <div id="fotosWrapper">El sistema responde: </div>
  </div> -->

<!-- ================================================================== -->
<!-- ================================================================== -->
</div> <!-- Fin de la capa de información -->
<!-- ================================================================== -->
<!-- ================================================================== -->

<br><br>
</body>
</html>

<!-- ======================================== -->
<!-- Para el área de texto -->
<!-- ======================================== -->
<script language="javascript" type="text/javascript" src="./tinymce/jscripts/tiny_mce/tiny_mce.js"> </script>
<script language="javascript" type="text/javascript">
tinyMCE.init({
        // General options
        mode : "textareas",
        theme : "advanced",
        language: 'es',

        plugins : "autoresize,autolink,lists,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
        width: '90%',
        height: '150',
        autoresize_min_height: 100,
        autoresize_max_height: 300,

        // Theme options
        theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,forecolor,backcolor,|,bullist,numlist,|,undo,redo,|,link,unlink,anchor,cleanup,help,code,|,charmap,|,sub,sup,",
        theme_advanced_buttons2 : "formatselect,fontselect,fontsizeselect",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "justify",
        theme_advanced_statusbar_location : "bottom",
        theme_advanced_resizing : true,

        // Skin options
        skin : "default",
        // skin : "o2k7",
        // skin_variant : "silver",

        // Example content CSS (should be your site CSS)
        content_css : "./css/paratinymce.css",
        theme_advanced_font_sizes: "10px,12px,13px,14px,16px,18px,20px",
        font_size_style_values : "10px,12px,13px,14px,16px,18px,20px",

        // Drop lists for link/image/media/template dialogs
        template_external_list_url : "js/template_list.js",
        external_link_list_url : "js/link_list.js",
        external_image_list_url : "js/image_list.js",
        media_external_list_url : "js/media_list.js",

        // disparador de un evento on change
        handle_event_callback : "comprobar",   

        charLimit : 3000, // this is a default value which can get modified later
	//set up a new editor function 
	setup : function(ed) {
	 //peform this action every time a key is pressed
	 ed.onKeyUp.add(function(ed, e) {
	 //define local variables
	 var tinymax, tinylen, htmlcount;
	 //manually setting our max character limit
	 tinymax = ed.settings.charLimit;
	//grabbing the length of the curent editors content
	// tinylen = ed.getContent().length;
         var body =ed.getBody(), text = tinymce.trim(body.innerText || body.textContent);
         tinylen=text.length;
         numwords=text.split(/[\w\u2019\'-]+/).length;
        //setting up the text string that will display in the path area
	 htmlcount = "Número de caracteres: " + tinylen + "/" + tinymax+ " -- Número de palabras: "+numwords+" ";
	 //if the user has exceeded the max turn the path bar red.
	 if (tinylen > tinymax){
          htmlcount = "<span style='font-size:20px;'>¡¡Llegó al límite!! </span><span style='font-weight:bold; color: #f00;'>" + htmlcount + "</span>";
	  if (anterior.length>tinymax) { anterior.substring(0,anterior.length-2);}
          tinyMCE.activeEditor.setContent(anterior);
                  // Este código poner el cursor en el último caracter.
		  var root = ed.dom.getRoot();  // This gets the root node of the editor window
		  var lastnode = root.childNodes[root.childNodes.length - 1]; 
		  if (tinymce.isGecko) {
		    // But firefox places the selection outside of that tag, so we need to go one level deeper:
		    lastnode = lastnode.childNodes[lastnode.childNodes.length - 1];
		  }
		  // Now, we select the node
		  ed.selection.select(lastnode);
		  // And collapse the selection to the end to put the caret there:
		  ed.selection.collapse(false);
         } else {
	   // Variable contenida de forma general en la raíz de SCRIPTS
           anterior = tinyMCE.activeEditor.getContent(); // guarda en esta variable el contenido del editor 
         }
	 //enable to override the limit for various editors here
	 // tinyMCE.get('observaciones').settings.charLimit = tinymax; 
	 //this line writes the html count into the path row of the active editor
	 tinymce.DOM.setHTML(tinymce.DOM.get(tinyMCE.activeEditor.id + '_path_row'), htmlcount); 
         });
	}      

});
</script>

<!-- ======================================== -->

