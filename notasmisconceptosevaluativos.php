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

// variable que obtiene datos
$datosasignacion=obtenerdatosasignacion($bd,$_SESSION['asignacion']); 
$listaevaluaciones = obtenerlistaevaluaciones($bd); // obtiene la lista de evaluaciones
$misindicadores=obtenermisindicadores($bd,$_SESSION['profesor']); // obtiene MIS INDICADORES

if (!isset($_SESSION['listaevaluacion'])) { 
    $_SESSION['listaevaluacion']=$listaevaluaciones['idlistaevaluaciones'][0];
    // si no tenemos una lista de evaluación elige la primera existente.
}

$misiev= obtenermisinstrumentosevaluativos($bd,$_SESSION['asignacion'],$_SESSION['listaevaluacion']); 

?>
<html>
<head>
<title>Edita Mis Conceptos Evaluativos</title>
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

    // ******************************************************************
    $(document).ready(function(){ // principio del document ready  

    $("#peso").numeric("."); // con jquery.numeric.js los convierte en cajas de texto numérico
    $("#numpeso").numeric(".");

    // define la caja de dialogo donde aparece el peso de los indicadores
    var objeto; // variable global donde se almacena el objeto 
    $("#dialogonumeroPESOINDICADOR").dialog({
         autoOpen: false,  modal: true, width: "350", height: "auto",
         buttons: {
	    "Aceptar": function() {
                 if ($("#numpeso").val()>0 && $("#numpeso").val()<=10) {
                        objeto.parent().children("td[class='sino']").children("img[name='estado']").attr("src","./imagenes/otros/ok.png"); // evita que si cierro el diálogo se quede marcado...
		 	objeto.text(Math.round(10*$("#numpeso").val())/10);
	                modificacampoindicadores();
                 	$(this).dialog("close");
                 } else {
                 	alert ("Valor no válido");
                 }
	     }
	 }
    });    

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
		asignalistaevaluacion();
		// muestrainstrumentosevaluativos();
          }
    });

    //C) Al pulsar en la imagen SIGUIENTE, selecciona un valor posterior del combo LISTAEVALUACIONES
    $("#siguiente").click(function(e){ // pulso en ANTERIOR
	  var Element = $("#listaevaluaciones > option:selected").next('option').val(); // valor anterior
          if (Element>=0) {
          	$("#listaevaluaciones").val(Element);
		$("#listaevaluacion").val(Element);
                asignalistaevaluacion();
		// muestrainstrumentosevaluativos();
          }
    });

    // ======================================================= 
    // D) On focus, aparece también el data picker
    // ======================================================= 
    $('input').filter('.datepick').mouseover(function(){
	$(this).datepicker("show");
    });

    $('#fechainipre,#fechafinpre,#fechainireal,#fechafinreal').change(function(){
	// muestraanotaciones();
    });

     $('#fechainipre,#fechafinpre,#fechainireal,#fechafinreal').keyup(function(event) {
        // alert(event.which);
        if (event.which == 46 || event.which == 8 || event.which == 27) { // esc, suprimir o borrar atrás.
            $(this).val("");
            // muestraanotaciones(); 
        }
    });
  
    // ============================================================== 
    // E) Define el calendario en el imput de fecha
    // ============================================================== 
    $('input').filter('.datepick').datepicker({ // lo define por la clase de los inputs
	    changeMonth: true,
	    changeYear: true,
	    showOn: 'button',
	    buttonImage: 'imagenes/otros/fecha.png',
            alignment: 'topRight',
	    buttonImageOnly: true,
            yearRange: '-1:+1',
	   });

    $("img[class='ui-datepicker-trigger']").each(function()  { // estilo del botón de la fecha
         $(this).attr('style', 'position:relative; top:12px; left:10px; width:30px; height: 30px;');
    });

    // F) Al cargar la página, recarga la lista de anotaciones
    $(window).load(function() {
      var evaluar = $("#listaevaluacion").val(); //variable de sesión. Se define en mostrarnotasinstrumentosevaluativos. Se pone en el campo listaevaluacion
      $("#listaevaluaciones").val(evaluar); // cambia en función de eso el SELECT
      muestraindicadores();
      muestraconceptosevaluativos(); // recarga los datos.
      $("#copiarh3").unbind('click'); // No se puede hacer click en él. Sólo con el botón guardar.
    });

    // G) Al cambiar el SELECT listaevaluaciones, cambia la tabla...
    $("#listaevaluaciones").change(function() {
       $("#listaevaluacion").val($("#listaevaluaciones").val());
       muestraconceptosevaluativos();      
    });
    
 // H) Comprobar si se ha pulsado en el acordeon
    $("#ins").click(function(){
             muestraindicadores();
    });

    // ===================================================================== 
    // I) Al pulsar sobre la tabla, detecta si lo hemos hecho
    //    sobre la papelera o sobre editar. Hay que hacerlo así porque, 
    //    al generar la tabla dinámicamente, no detecta img como objeto ¿¿??
    // ===================================================================== 
    $("#tablaindicadores").click(function(e){ 
        var parentTRTI = $(e.target).closest('tr'); // Get the parent row. Definida como variable global al principio
        var textodescripcion = $("td[class='textodescripcion']", parentTRTI).html(); // Retrieve the id content
        var textoabreviatura = $("td[class='textoabreviatura']", parentTRTI).text(); // Retrieve the id content
        var peso = $("td[class='peso']", parentTRTI).text(); // Retrieve the id content
        /* // var columna = parentTR 
        var columna = $('td', parentTR).index(e.target);
        // var columna = $(this).parent().children().index($(this)); 
        alert(id + " -- " + columna); */
        var identificacion = $(e.target).attr("id");
        var nombre = $(e.target).attr("name");
        var fuente = $(e.target).attr("src");
        // alert("He pulsado sobre la imagen. "+fuente);
        if (nombre=="estado" && fuente=="./imagenes/otros/no.png") {
           // $(e.target).attr("src","./imagenes/otros/ok.png");
           objeto = $("td[class='peso']", parentTRTI); // este es el objeto destino de lo que captará dialog
           $("#dialogonumeroPESOINDICADOR").dialog("open"); // abro el diálogo
        } else if (nombre=="estado" && fuente=="./imagenes/otros/ok.png") {
	   $(e.target).attr("src","./imagenes/otros/no.png");
           $("td[class='peso']", parentTRTI).text("0"); // Cambia el contenido
           modificacampoindicadores();
        };
    });

    // ===================================================================== 
    // J) Al pulsar sobre la tabla, detecta si lo hemos hecho
    //    sobre la papelera o sobre editar. Hay que hacerlo así porque, 
    //    al generar la tabla dinámicamente, no detecta img como objeto ¿¿??
    // ===================================================================== 
    $("#tabladatos").click(function(e){ 
        var parentTR = $(e.target).closest('tr'); // Get the parent row
        var textonombre = $("td[class='textonombre']", parentTR).html(); // Retrieve the id content
        var textodescripcion = $("td[class='textonombre']", parentTR).attr("title"); // Retrieve the id content
        var textoabreviatura = $("td[class='textoabreviatura']", parentTR).text(); // Retrieve the id content
        var textopeso = $("td[class='textopeso']", parentTR).text(); // Retrieve the id content
        var textofechainipre = $("td[class='textofechainipre']", parentTR).text(); // Retrieve the id content
        var textofechafinpre = $("td[class='textofechafinpre']", parentTR).text(); // Retrieve the id content
        var textofechainireal = $("td[class='textofechainireal']", parentTR).text(); // Retrieve the id content
        var textofechafinreal = $("td[class='textofechafinreal']", parentTR).text(); // Retrieve the id content
        var textoiev = $("td[class='textoiev']", parentTR).text(); // Retrieve the id content       
	var textoindicadores = $("td[class='textoindicadores']", parentTR).text(); // Retrieve the id content 
        /* // var columna = parentTR 
        var columna = $('td', parentTR).index(e.target);
        // var columna = $(this).parent().children().index($(this)); 
        alert(id + " -- " + columna); */
        var identificacion = $(e.target).attr("id");
        var nombre = $(e.target).attr("name");
        // alert(nombre);
        if (nombre=="papelera") { borrar(identificacion,textonombre); }
        if (nombre=="editar") { editar(identificacion, textonombre, textodescripcion, textoabreviatura, textopeso, textofechainipre, textofechafinpre, textofechainireal, textofechafinreal, textoiev, textoindicadores); }
        if (nombre=="copiar") { copiar (identificacion, textonombre, textodescripcion, textoabreviatura, textopeso, textofechainipre, textofechafinpre, textofechainireal, textofechafinreal, textoiev, textoindicadores); }
    });

// ===================================================================== 
// K) Comprobar si se ha pulsado en el acordeon
// ===================================================================== 
    $(".insertarh3").click(function(e){
          var active = $("#muestrainformacion").accordion("option", "active");
          if (active==1 || active==2) { // Si es el 2º o el 3º (guardar/copiar), inicializa datos.
		// alert("Pulsa y se colapsa");
                $('#nombre').val("");
                $('#IDcev').val("");
		$('#peso').val("");
                // $('#iev').val();
                $('#fechainipre').val("");
		$('#fechainireal').val("");
	        $('#fechafinpre').val("");
		$('#fechafinreal').val("");
		$('#indicadores').val("");
                // vaciar tabla indicadores
                $("#tablaindicadores tbody tr").each(function(index) {
	              var id = $(this).children("td[class='id']").text();
		      $(this).children("td[class='peso']").text("0");	
                      $(this).children("td[class='sino']").children("img[name='estado']").attr("src","./imagenes/otros/no.png");
		}); 
		tinyMCE.get("descripcion").setContent(""); // descripción
                // Otros cambios
                $("#insertareditar").html("Introduce nuevo concepto evaluativo");
                // $("#insertareditar").attr("onClick","insertar();"); 
                $("#ins").html("Introducir un nuevo concepto evaluativo");               
          } // fin del active 
    });

    // ============================================================== 
    // L) Función de subida de fichero
    // ============================================================== 
    $('#file_upload').uploadify({
		// 'buttonClass' : 'cajones',
                // 'buttonImage' : './imagenes/otros/subir2.png',
                'buttonText': 'Sube fichero',
                'progressData' : 'speed',
                // 'uploader'  : './ficheros/uploadify.swf',
                'swf'  : './ficheros/uploadify.swf',
		'uploader'    : './notasconceptosevaluativos/uploader.php',
		'cancelImg' : './imagenes/otros/cancel.png',
		'auto'      : true,
		'formData':  {'folder': '/ficheros', 'asignacion': $('#asignacion').val(), 'evaluacion': $('#listaevaluacion').val(),},
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


    // ******************************************************************
    }); // Fin del document ready
    // ******************************************************************


    
// ******************************************************************
// *** Funciones fuera del document ready
// ******************************************************************

// =================================================
// F1) Mostrar indicadores
// =================================================
    function muestraindicadores() {
          // obtiene los datos de la tabla de indicadores
          var profesor = $('#profesor').val();
          var posting = $.post( "./notasindicadores/mostrarindicadores.php", { 
              profesor: profesor,
          });
          posting.done(function(data,status) {
              var datosindicadores = $.parseJSON(data); // en la variable datos paso los datos tipo JSON
              // alert(data);
		  if (data.length>2) {
			$("#tablaindicadores").find("tr:gt(0)").remove(); // Borra todo menos la primera fila.
                 	$("#tablaindicadores").show();
		     for (var i in datosindicadores) {
		     // alert(datos[i].fecha);
                     var introducir = '<tr><td class="id" style="text-align: center;">' + i +
		      '</td><td class="textodescripcion" style="text-align: center;">' + decodificar(datosindicadores[i].descripcion)+
		      '</td><td class="textoabreviatura" title="'+decodificar(datosindicadores[i].nombre)+'" style="text-align: center; padding: 0.5em 0.2em;">' + decodificar(datosindicadores[i].abreviatura) + 
		      '</td><td class="sino" style="text-align: center;"><img name="estado" id="'+i+'" src="./imagenes/otros/no.png" width="15px" height="auto">' +
		      '</td><td class="peso" style="text-align: center;">0'+
		      '</td></tr>';
		     $("#tablaindicadores").append(introducir);
		      } // fin del for  
              } else {
                 $("#tablaindicadores").find("tr:gt(0)").remove(); // Borra todo menos la primera fila.
                 $("#tablaindicadores").show();
	         $("#tablaindicadores").append('<tr><td colspan="8"><h2 style="text-align: center;">No hay datos</h2></td></tr>');
	      } // fin del if 
          }); // fin del posting done
    }

// F2) Asignar variable de sesión lista EVALUACION, con llamada a script
function asignalistaevaluacion() {
         var le = $('#listaevaluaciones').val();
          var posting = $.post( "./notasconceptosevaluativos/asignalistaevaluacion.php", { 
              listaevaluacion: le,
          });
          posting.done(function(data,status) { 
              // alert(data);
              location.reload();
          });
} 

// F3) Modifica los valores ID y peso de los indicadores en el campo INDICADORES
function modificacampoindicadores() {
    // var loquetiene = $("#indicadores").text();
    var cadena = "";
    $("#tablaindicadores tbody tr").each(function(index) {
	var id = $(this).children("td[class='id']").text();
        var peso = $(this).children("td[class='peso']").text();
        if (peso>0) { cadena = cadena + id + "-" + peso + "*"; }
    });   
    // alert(cadena);
    $("#indicadores").val(cadena.substr(0,cadena.length-1)); // menos el último asterisco
} // fin de la función 3.

// ***************************************
// F4) Función insertar. Inserta un dato 
// ***************************************
function insertar() { 
         // alert("Hola");
         var idcev = $('#IDcev').val();
         var nombre =  $('#nombre').val();
         var peso = $('#peso').val();
         var iev =  $('#iev').val();
	 var fechainipre =  $('#fechainipre').val();
	 var fechainireal =  $('#fechainireal').val();
	 var fechafinpre =  $('#fechafinpre').val();
	 var fechafinreal =  $('#fechafinreal').val();
	 var indicadores =  $('#indicadores').val();
         var asignacion =  $('#asignacion').val();
	 var evaluacion =  $('#listaevaluaciones').val(); // del combo, no de la variable de SESIÓN
	 var descripcion =  tinyMCE.get("descripcion").getContent();
         // alert (nombre + " id:"+idcev+ " "+iev+" peso: "+peso+"  "+ descripcion + "  " +fechainipre+ "  "+fechafinreal);
         // alert(evaluacion);
         // detectar si son o no números correctos
         if (!nombre || !peso || !iev ) { // testea si son valores vacíos
		alert("Es obligatorio introducir nombre, peso e instrumento evaluativo");
                return false; // salgo de la función
         }
	 if (peso<=0 || peso>10) {
		alert("Peso o importancia incorrecto");
                return false; // salgo de la función
         }
         // llamada al script php
         var posting = $.post( "./notasconceptosevaluativos/insertace.php", { 
	     idcev: idcev,
             nombre: nombre,
	     peso: peso, 
             iev: iev,
             fechainipre: fechainipre,
	     fechainireal: fechainireal,
	     fechafinpre: fechafinpre,
	     fechafinreal: fechafinreal,
	     indicadores: indicadores,
             asignacion: asignacion,
	     evaluacion: evaluacion,
             descripcion: descripcion,
         });
         posting.done(function(data,status){
            alert("El programa responde: "+data);
	    location.reload();
         });
} // Insertar un dato. Fin de la función 

// ================================================================================= 
// F5 Muestra los CONCEPTOS EVALUATIVOS por EVALUACIONES  y según ASIGNACIÓN
// ************* Ponerla FUERA del documment ready para que funcione mejor.
// ================================================================================= 
    function muestraconceptosevaluativos() { 
         var asignacion = $('#asignacion').val();
	 var evaluacion = $('#listaevaluaciones').val();
         // alert(asignacion+" - "+evaluacion);
      var posting = $.post( "./notasconceptosevaluativos/mostrarnotasconceptosevaluativos.php", { 
             asignacion: asignacion,
             evaluacion: evaluacion,
      });
      posting.done(function(data,status){
         var datos = $.parseJSON(data); // en la variable datos paso los datos tipo JSON
         // alert(data);
         if (data.length>2) {
  		 $("#tabladatos").find("tr:gt(0)").remove(); // Borra todo menos la primera fila.
                 $("#tabladatos").show();
		 for (var i in datos) {
		     // alert(datos[i].fecha);                     
                     var introducir = '<tr><td class="id" style="text-align: center;">' + i +
                     // varias columnas ocultas donde guardar información
		      '</td><td class="textofechainipre" style="display: none;">' + decodificar(datos[i].fechainipre)+
		      '</td><td class="textofechafinpre" style="display: none;">' + decodificar(datos[i].fechafinpre)+
		      '</td><td class="textofechainireal" style="display: none;">' + decodificar(datos[i].fechainireal)+
		      '</td><td class="textofechafinreal" style="display: none;">' + decodificar(datos[i].fechafinreal)+
		      '</td><td class="textoiev" style="display: none;">' + decodificar(datos[i].iev)+
                      '</td><td class="textoindicadores" style="display: none;">' + decodificar(datos[i].indicadores)+
		      '</td><td class="textonombre" title="'+decodificar(datos[i].descripcion)+'" style="text-align: center;">' + decodificar(datos[i].nombre)+
		      '</td><td class="textoabreviatura" title="' + decodificar(datos[i].nombreie) + '" style="text-align: center; padding: 0.5em 0.2em;">' + decodificar(datos[i].abreviatura) + 
		      '</td><td class="textopeso" style="text-align: center;">' + datos[i].peso+
                      '</td><td style="text-align: center;"><img name="papelera" id="'+i+'" src="./imagenes/otros/papelera.png" width="50%" height="auto">' +
                      '</td><td style="text-align: center;"><img name="editar" id="'+i+'" src="./imagenes/otros/editar.png" width="50%" height="auto">' +
                      '</td><td style="text-align: center;"><img name="copiar" id="'+i+'" src="./imagenes/otros/guardar.png" width="50%" height="auto">' +
		      '</td></tr>';
		     $("#tabladatos").append(introducir);
		 } // fin del for  

         } else {
                 $("#tabladatos").find("tr:gt(0)").remove(); // Borra todo menos la primera fila.
                 $("#tabladatos").show();
	         $("#tabladatos").append('<tr><td colspan="7"><h2 style="text-align: center;">No hay datos</h2></td></tr>');
         } // fin del if
      });
}  // FIN DE MOSTRAR CONCEPTOS EVALUATIVOS 


// ================================================================================= 
// F6) al pulsar sobre el botón de editar...
// ================================================================================= 
function editar(idn, textonombre, textodescripcion, textoabreviatura, textopeso, textofechainipre, textofechafinpre, textofechainireal, textofechafinreal, textoiev, textoindicadores) {
        // alert(textonombre+" - "+textodescripcion);
        // alert(textoabreviatura+" - "+textopeso);
        // alert(textofechainipre+" - "+ textofechafinpre+" - "+ textofechainireal+" - "+ textofechafinreal);
        // alert(textoiev+ " "+textoindicadores);
        // valores en la edición
        $('#nombre').val(textonombre);
        $('#IDcev').val(idn); // Introduzco un campo con el id, que ocultaré
        $('#peso').val(textopeso);
        $('#iev').val(textoiev);
        tinyMCE.get("descripcion").setContent(textodescripcion); // textarea
        $('#fechainipre').val(textofechainipre);
	$('#fechafinpre').val(textofechafinpre);
	$('#fechainireal').val(textofechainireal);
	$('#fechafinreal').val(textofechafinreal);
        // el problema de los indicadores...
        $('#indicadores').val(textoindicadores); // recupero el campo indicadores, ...¡¡PERO HAY QUE MODIFICAR  LA TABLA DE INDICADORES!!
        // 1º) Obtener arrays con los pares ID indicador - Peso.
        var parejas = textoindicadores.split("*"); // divido primero el asterisco
        for (var i in parejas) {
             var datos = parejas[i].split("-"); // y ahora obtengo ID y peso
             // 2º) recorrer la tablaindicadores 
             // alert("ID: "+datos[0]+" - peso: "+datos[1]);
             $("#tablaindicadores tbody tr").each(function(index) {
	       var id = $(this).children("td[class='id']").text();
               if (id==datos[0]) { //si coinciden los indicadores
		   $(this).children("td[class='peso']").text(datos[1]);	
                   $(this).children("td[class='sino']").children("img[name='estado']").attr("src","./imagenes/otros/ok.png");
               }                 
             });   
        }
        // cambiar el onclick del botón      
            $("#insertareditar").html("Guardar concepto evaluativo");
            // $("#insertareditar").attr("onClick","insertar('"+idn+"');"); // no es necesario
            $("#muestrainformacion").accordion( "option", "active", 1 ); // activa segundo panel
            $("#ins").html("Edita y cambia un concepto evaluativo existente"); 
}

// FIN DEL SCRIPT

// =========================================
// F7) Función Borrar. Borra un dato 
// =========================================
    function borrar(idn,texto) {
         // alert(idn);
         if (confirm("¿Estas seguro de borrar el dato "+idn+": '"+texto+"'?")) {
	 var posting = $.post( "./notasconceptosevaluativos/borrarconceptosevaluativos.php", { 
             id: idn,
         });
         posting.done(function(data,status){
                // alert (status);
                if (status=="success") {
                   muestraconceptosevaluativos(); // vuelve a mostrar la tabla
                } else {
                   alert("El procedimiento ha fallado");
                }
         });
         } // fin de la confirmación
     }

// ***********************************************************************************
// Este bloque copia o mueve items
// ***********************************************************************************
    // F8B) Función Copiar. Cambia o copia un dato a otra evaluación
    function copiar (idn, textonombre, textodescripcion, textoabreviatura, textopeso, textofechainipre, textofechafinpre, textofechainireal, textofechafinreal, textoiev, textoindicadores) {
         // alert(idn+"  "+fecha+"  "+textoanotacion);
         // valores en la edición
         $('#nombre').val(textonombre);
         $('#IDcev').val(idn); // Introduzco un campo con el id, que ocultaré
         $('#peso').val(textopeso);
         $('#iev').val(textoiev);
         tinyMCE.get("descripcion").setContent(textodescripcion); // textarea
         $('#fechainipre').val(textofechainipre);
	 $('#fechafinpre').val(textofechafinpre);
	 $('#fechainireal').val(textofechainireal);
	 $('#fechafinreal').val(textofechafinreal);
         // el problema de los indicadores...
         $('#indicadores').val(textoindicadores); 
         // cambiar el onclick del botón      
            // $("#muevedatos").attr("onClick","muevedatos('"+idn+"');"); // para editar
            $("#muestrainformacion").accordion( "option", "active", 2 ); // activa segundo panel 
    }

    // F8B_1) Función Copiar. Cambia o copia un dato a otra evaluación
    function copiadatos () {
        if ($("#listaevaluaciones").val()==$("#listaevaluaciones2").val()) { // salir
		alert("Ambas evaluaciones no pueden ser iguales");
                return;
        }
	$("#listaevaluaciones").val($("#listaevaluaciones2").val()); // selecciono en el principal el valor del secundario
        $('#IDcev').val(""); // borro el campo ID, y así inserta uno nuevo, o sea, copia...
        insertar(); // Sólo tengo que insertar.
    }

    // F8B_2) Función Copiar. Cambia o copia un dato a otra evaluación
    function muevedatos() {
        if ($("#listaevaluaciones").val()==$("#listaevaluaciones2").val()) { // salir
		alert("Ambas evaluaciones no pueden ser iguales");
                return;
        }
        $("#listaevaluaciones").val($("#listaevaluaciones2").val()); // selecciono en el principal el valor del secundario
        insertar(); // Sólo tengo que insertar; puesta la Identificación y mueve de evaluación
    }
// *********** fin de copiar/mover items **********************

// *********** *******
// F9) Guardar fichero
// *********** *******
function guardar() {     
           var posting = $.post( "./notasconceptosevaluativos/guardarficheromisconceptosevaluativos.php", { 
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
	 <h3 class="insertarh3">Presentación de los conceptos evaluativos de esta evaluación</h3>
         <div id="datos" style="border: 0px solid red; position: relative;">
		<table id="tabladatos" style="display: auto; margin:2px auto; height: auto; text-align: center; width: 98%; table-layout: fixed;" border="1" cellpadding="1" cellspacing="1" class="tabla">
		<tr style="vertical-align: middle;">
                <th style="width: 5%; font-weight: bold; text-align: center;">N-Id</th>
                <th style="width: 61%; font-weight: bold; text-align: center;">Nombre</th>
                <th style="width: 8%; font-weight: bold; text-align: center;">Ins. Eva.</th>
                <th style="width: 8%; font-weight: bold; text-align: center;">Peso</th>
                <th style="width: 6%; font-weight: bold; text-align: center;">Borrar</th>
                <th style="width: 6%; font-weight: bold; text-align: center;">Editar</th>
                <th style="width: 6%; font-weight: bold; text-align: center; word-wrap:break-word;">Mov/Cop</th></tr>
		</table>
        </div>   
<!-- ***************** -->
<!-- Introducir datos  -->
<!-- ***************** -->
	<h3 class="insertarh3" id="ins">Introducir un nuevo concepto evaluativo</h3>
<div id="insertar" style="border: 0px solid red; position: relative;">
        <!-- Botón insertar -->
        <div id="cero" style="position: absolute; border: 0px solid black; right: 4%; top: 7%; width: auto; height:60px; overflow: hidden;">
        <a id="insertareditar" style="margin: 10px; top: 15px; color: black;" class="a_demo_four" onclick="insertar();">Insertar nuevo concepto evaluativo </a>
         </div>
        <!-- Campos -->
        <input id="IDcev" class="botones" type="hidden" style="text-align: left;" size="20" maxlength="200">
<div style="border: 0px solid black; padding: 0px 5px 25px 5px;"><p>
        Nombre:&nbsp;
	<input id="nombre" class="botones" type="text" style="text-align: left;" size="64" maxlength="200">
	<br><br>Importancia o peso:&nbsp;&nbsp;&nbsp;
        <input id="peso" class="botones" type="text" style="text-align: center;" size="3" maxlength="3">
        <br><br>Instrumento evaluativo
        <select id="iev" class="botones">
            <?php            
            foreach ($misiev["IDiev"] as $key => $valor) {
            echo '<option value="'.$valor.'">'.$misiev["nombre"][$key].' ('.$misiev["abreviatura"][$key].' - '.$misiev["porcentaje"][$key].')</option>';
            }
            ?>
        </select>
	<br><br>Descripción:&nbsp;
	<textarea name="descripcion" class="cajones" maxlength="3000" alt="3.000 caracteres cómo máximo" vertical-align: middle; font-size:14px; margin: auto 1em;" height="auto" id="descripcion" onchange="comprobar();"></textarea></p>   
        <!-- Fechas -->    
	<div id="ceroA" style="float: left; border: 0px solid black; left: 2%; bottom: 1%; width: 48%; height:auto; padding: 0px;">
	<p>Fecha de inicio prevista:&nbsp;
        <input class="datepick" id="fechainipre" name="fechainipre" type="text" value="" readonly="false" style="width: 6em; height: 1.8em; right: 16%; top: 14%; text-align: center; vertical-align:middle;"></p>
	<p>Fec. de finalización prevista:&nbsp;
        <input class="datepick" id="fechafinpre" name="fechafinpre" type="text" value="" readonly="false" style="width: 6em; height: 1.8em; right: 16%; top: 14%; text-align: center; vertical-align:middle;"></p>
	</div>
	<div id="ceroB" style="float: right; border: 0px solid black; right: 2%; bottom: 1%; width: 48%; height:auto; padding: 0px;">
	<p>Fecha de inicio real:&nbsp;
        <input class="datepick" id="fechainireal" name="fechainireal" type="text" value="" readonly="false" style="width: 6em; height: 1.8em; right: 16%; top: 14%; text-align: center; vertical-align:middle;"></p>
	<p>Fecha de finalización real:&nbsp;
        <input class="datepick" id="fechafinreal" name="fechafinreal" type="text" value="" readonly="false" style="width: 6em; height: 1.8em; right: 16%; top: 14%; text-align: center; vertical-align:middle;"></p>
	</div>
</div> 

<!-- Indicadores -->
<div id="ceroC" style="clear: both; float: none; position: relative; border: 0px solid black; width: 98%; height: auto; margin: 0px auto; padding-top: 1em; overflow:auto;">
<hr width="80%"> <!-- Línea de separación -->
        <p style="text-align: left; " >Lista de indicadores</p>
        <input id="indicadores" class="botones" type="hidden" style="text-align: center;">
	<table id="tablaindicadores" style="display: auto; margin:2px auto; height: auto; text-align: center; width: 90%; table-layout: fixed;"  border="1" cellpadding="1" cellspacing="1" class="tabla">
		<tr style="vertical-align: middle;">
                <th style="width: 5%; font-weight: bold; text-align: center;">N-Id</th>
                <th style="width: 60%; font-weight: bold; text-align: center;">Descripción</th>
                <th style="width: 14%; font-weight: bold; text-align: center; word-wrap:break-word;">Competencia</th>
                <th style="width: 8%; font-weight: bold; text-align: center;">Elegido</th>
                <th style="width: 5%; font-weight: bold; text-align: center;">Peso</th>
                <tr>
         </table>

</div>



</div> <!-- Fin de insertar -->

<!-- ******************* -->
<!-- Copiar/mover datos  -->
<!-- ******************* -->
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
	<h3 class="insertarh3">Guarda los conceptos evaluativos en un fichero</h3>
	<div id="guardar" style="border: 0px solid red; position: relative;">
	    <div id="cero3" style="position: relative; border: 0px solid black; top: 5px; width: auto; height:50px; overflow: hidden;">
            <a id="guardar" style="margin: 10px; top: 15px; color: black;" class="a_demo_two" onclick="guardar();">Guarda copia en CSV de los datos de todas las evaluaciones</a>
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

</div>


<!-- ================================================================== -->
<!-- ================================================================== -->
</div> <!-- Fin de la capa de información -->
<!-- ================================================================== -->
<!-- ================================================================== -->

<br><br>


<!-- ================================================================== -->
<!-- ================================================================== -->
<!-- Capa diálogo para obtener datos-->
<!-- ================================================================== -->
<!-- ================================================================== -->

<div id="dialogonumeroPESOINDICADOR" title="Peso que tiene el indicador">
   <br>
   <label style="font-size: 1.5em;">Introduce un número del 1 al 10</label>
   <br>
   <input id="numpeso" type="text" style="font-size: 1.5em;">
</div>

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

