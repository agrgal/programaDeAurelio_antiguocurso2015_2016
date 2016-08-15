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
$listaevaluaciones = obtenerlistaevaluaciones($bd); // obtiene la lista de evaluaciones
if (!isset($_SESSION['listaevaluacion'])) { // Si no tengo una evaluación, entonces
    $_SESSION['listaevaluacion']=$listaevaluaciones['idlistaevaluaciones'][0];
    // si no tenemos una lista de evaluación elige la primera existente.
}
// Antes de elegir la lista de conceptos evaluativos...
$listaconceptosevaluativos = obtenermisconceptosevaluativos($bd,$_SESSION['asignacion'],$_SESSION['listaevaluacion']);
$datosasignacion=obtenerdatosasignacion($bd,$_SESSION['asignacion']); 
$alumno=obteneralumnosasignacion($bd,$_SESSION['asignacion']); // array para introducir datos de los alumnos
$ii=count($alumno['idalumno']);

?>
<html>
<head>
<title>Edita Mis Conceptos Evaluativos</title>
<meta http-equiv="Content-Type" content="Type=text/html; charset=iso-8859-15">

<?php include_once("./listas_de_css.php"); ?>
<link href="css/inputs.css" rel="stylesheet" type="text/css"> <!-- especialmente la de input -->
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

    $("#innotaseneca").numeric("."); // con jquery.numeric.js los convierte en cajas de texto numérico
    $("#innotarecuperacion").numeric("."); // con jquery.numeric.js los convierte en cajas de texto numérico    

    // define la caja de dialogo donde aparece el peso de los indicadores
    var objeto; // variable global donde se almacena el objeto 
    $("#dialogoINDICADOR").dialog({
         autoOpen: false,  modal: true, width: "350", height: "auto",
         buttons: {
	    "Aceptar": function() {
		objeto.text($("input[name=notaindicador]:checked").val());
                $("input[name=notaindicador]").filter('[value="Bien"]').prop("checked", true);
                recorreindicadores(); // cambiar el campo INDICADORES
                $(this).dialog("close");
	     }
	 }
    });

   $("#dialogoCALIFICACIONES").dialog({
         autoOpen: false,  modal: true, width: "350", height: "auto",
         buttons: {
	    "Aceptar": function() {
		modificacalificacion();
                $(this).dialog("close");
	     }
	 }
    });

   $("#dialogoMENSAJE").dialog({
         autoOpen: false,  modal: true, width: "350", height: "auto",
         buttons: {
	    "Aceptar": function() {
                $(this).dialog("close");
	     }
	 }
    });

    // 0) SELECTORES...

	$("#selectoralumno").slider({
		orientation: "horizontal",
                min: 0, max: 100, range: "min", value: 0,
		change: function(event, ui) { // al cambiar el valor del select
		    $("#alumno").val($("#selectoralumno").slider("value"));       
                    $("#nombrealumno").html($('#alumno>option:selected').text());
                    // cambia el tab de este apartado con el nombre del alumno
                    $("#calificacionh3").html("Calificación para "+$('#nombrealumno').html()+" esta evaluación");
		    $("#idalumno").html($('#alumno>option:selected').attr("title"));   
                    $("#selectoralumno").attr("title",$("#selectoralumno").slider("value"));
		    $("#salumno").val($("#selectoralumno").slider("value"));
                    muestraindicadores(); // muestra los indicadores
                    mostrarnota();
                    if($("#indicadores").val()!="") { // aseguro reconoce los indicadores una segunda vez...
	                 notaindicadores($("#indicadores").val()); 
                    }
                    // asignalistaevaluacion(); // ¿¿Recarga la página?? NO, entra en bucle...
                    // fotografia
		    compruebafoto(); // por el chrome...
		} // ¿Cambiar por el SLIDE?
        });

	$("#selectornota").slider({
		orientation: "horizontal",
                min: 0,  max: 10, range: "min", value: 5, step: 0.1,
		change: function(event, ui) { // al cambiar el valor del select
 		$("#notaalumno").html($("#selectornota").slider("value")); 
		$("#modificadornota").html("N"); // Si selecciona nota, la pone a normal automáticamente
                $("input[name=modificarnota]").filter('[value="N"]').prop("checked", true);
		$("#selectornota2").spinner("value",parseInt(ui.value));
                colorcelda();  
		} // ¿Cambiar por el CHANGE?
        });    
 
        $("#selectornota2").spinner({ max:10, min:0,
		spin: function( event, ui ) {
		$("#selectornota").slider("value",ui.value);
		} // fin del spin
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
          }
    });

    //B2) Al pulsar en la imagen ATRAS, selecciona un valor anterior del combo LISTAEVALUACIONES
    $("#atrasce").click(function(e){ // pulso en ANTERIOR
	  var Element = $("#listaconceptosevaluativos > option:selected").prev('option').val(); // valor anterior
          if (Element>=0) {
                $("#listaconceptosevaluativos").val(Element);
                $("#listaconceptoevaluativo").val(Element);
                asignalistaevaluacion();
          }
    });

    //C) Al pulsar en la imagen SIGUIENTE, selecciona un valor posterior del combo LISTAEVALUACIONES
    $("#siguiente").click(function(e){ // pulso en ANTERIOR
	  var Element = $("#listaevaluaciones > option:selected").next('option').val(); // valor anterior
          if (Element>=0) {
          	$("#listaevaluaciones").val(Element);
		$("#listaevaluacion").val(Element);
                asignalistaevaluacion();
          }
    });

    //C2) Al pulsar en la imagen ATRAS, selecciona un valor anterior del combo LISTAEVALUACIONES
    $("#siguientece").click(function(e){ // pulso en ANTERIOR
	  var Element = $("#listaconceptosevaluativos > option:selected").next('option').val(); // valor anterior
          if (Element>=0) {
                $("#listaconceptosevaluativos").val(Element);
                $("#listaconceptoevaluativo").val(Element);
                asignalistaevaluacion();
          }
    });

    // D) al pulsar una imagen que aumente o no el dato de los alumnos
    $("#satras").click(function(e){ // pulso en ANTERIOR
        var valor = $("#selectoralumno").slider('value');
        $("#selectoralumno").slider('value',(valor-1));
        compruebafoto(); // por el chrome
    });
    $("#sprimero").click(function(e){ // pulso en ANTERIOR
        var valor = $("#selectoralumno").slider('option','min');
        $("#selectoralumno").slider('value',valor);
        compruebafoto(); // por el chrome
    });
    $("#sdelante").click(function(e){ // pulso en ANTERIOR
        var valor = $("#selectoralumno").slider('value');
        $("#selectoralumno").slider('value',(valor+1));
        compruebafoto(); // por el chrome
    });
    $("#sultimo").click(function(e){ // pulso en ANTERIOR
        var valor = $("#selectoralumno").slider('option','max');
        $("#selectoralumno").slider('value',valor);
        compruebafoto(); // por el chrome
    });

    // $("#idalumno").change(function(e){ // imposible porque change no funciona con esto
    // Pero parece que sólo funciona con Firefox...
    $("#idalumno").bind('DOMNodeInserted', function(e) {
        // Fuerzo a que recargue la foto... MÉTODO MÁS SEGURO...
	compruebafoto();
    });


    // E) Al cargar la página, recarga la lista de evaluaciones y conceptos evaluativos.
    $(window).load(function() {
      // ******************
      // Evaluaciones
      // ******************
      var evaluar = $("#listaevaluacion").val(); //variable de sesión. Se define en 
      var evaluaciones=[];
      $("#listaevaluaciones option").each(function(){ // recorro los valores del select
	 evaluaciones.push($(this).val()); // mete los téminos en un vector
      });
      if (jQuery.inArray(evaluar,evaluaciones)>=0) { // Si está en la lista lo cambia
         $("#listaevaluaciones").val(evaluar); // cambia en función de eso el SELECT      
      } else { // si no lo está pone el primero.
         $("#listaevaluaciones").val(evaluaciones[0]); // cambia en función de eso el SELECT      
         $("#listaevaluacion").val(evaluaciones[0]); 
         // asignalistaevaluacion();
      }  
      // *****************************
      // Conceptos evaluativos
      // *****************************
      
      var ce = $("#listaconceptoevaluativo").val(); //variable de sesión. Se define anteriormente
      var terminos=[];
      $("#listaconceptosevaluativos option").each(function(){ // recorro los valores del select
	 terminos.push($(this).val()); // mete los téminos en un vector
      });
      if (terminos[0]!=null) {
	      if (jQuery.inArray(ce,terminos)>0) { // Si está en la lista lo cambia
		 $("#listaconceptosevaluativos").val(ce); // cambia en función de eso el SELECT      
	      } else { // si no lo está pone el primero.
		 $("#listaconceptosevaluativos").val(terminos[0]); // cambia en función de eso el SELECT      
		 $("#listaconceptoevaluativo").val(terminos[0]); 
		 // asignalistaevaluacion();
	      }
	      if ($("#listaconceptosevaluativos option").length<=0) { asignalistaevaluacion(); } 
	      // así al principio 
	      // recarga la lista de los conceptos evaluativos, poniendo los de la evaluación primero.
      } // No es nulo el primero... 
      else { // Y si es nulo
	  $("#muestrainformacion").hide();
	  alert("En esta evaluación no hay conceptos evaluativos. Escoge otra evaluación.");
      } // Si es nulo
      
      // ********** Máximo y mínimo del combo alumnos para ponérselo al slider *************+
      var minalumno=1000; var maxalumno=0;
      $("#alumno option").each(function(){ // recorro los valores del select
	 var idalumno = parseInt($(this).val()); // mete los téminos en un vector
         // alert(idalumno);
         if (idalumno>=maxalumno) { maxalumno = idalumno; }
         if (idalumno<=minalumno) { minalumno = idalumno; }
      });
      $("#selectoralumno").slider( "option", "max", maxalumno);
      $("#selectoralumno").slider( "option", "min", minalumno);
      // alert(minalumno+" - " + maxalumno);

      // *****************************
      // Alumnos
      // *****************************
      $("#selectoralumno").slider("value",$("#salumno").val()); // pone en el select el valor 
      $("#alumno").val($("#selectoralumno").slider("value"));  // selecciona el primero de ellos    
      $("#nombrealumno").html($('#alumno>option:selected').text()); // coloco el texto correspondiente 
      $("#idalumno").html($('#alumno>option:selected').attr("title")); // coloco id alumno.

      // muestroindicadores
      muestraindicadores(); // muestra los indicadores   
      // Muestra las notas
      mostrarnota();   

      // Segunda vez, por si puede cargar de nuevo
      if($("#indicadores").val()!="") { // los pone en la tabla...
	  notaindicadores($("#indicadores").val()); // los pone en la tabla...
      } 
      
      compruebafoto(); // para que funcione bien la carga con el chrome...
      // $("#muestrainformacion").accordion( "option", "active", false ); //desactiva todos los tabs
      // $("#copiarh3").unbind('click'); // No se puede hacer click en él. Sólo con el botón guardar.
    });

    // F) Al cambiar el SELECT listaevaluaciones, cambia la tabla...
    $("#listaevaluaciones").change(function() {
       $("#listaevaluacion").val($("#listaevaluaciones").val());
       asignalistaevaluacion();
       // muestrainstrumentosevaluativos();   
    });

    // F2) Al cambiar el SELECT listaevaluaciones, cambia la tabla...
    $("#listaconceptosevaluativos").change(function() {
       $("#listaconceptoevaluativo").val($("#listaconceptosevaluativos").val());
       asignalistaevaluacion();
       // muestrainstrumentosevaluativos(); 
    });

    // G) Al hacer click sobre el grupo de modificadores de notas 
    $(".modificarnota").click(function() {
        // alert($("input[name=modificarnota]:checked").val());
        $("#modificadornota").html($("input[name=modificarnota]:checked").val());
    });

    // H) Al hacer click en el primer tab del acordeon
    $(".insertarh3").click(function(e){
          var active = $("#muestrainformacion").accordion("option", "active");
          if (active==0) { // Si es el 1º
             muestraindicadores(); // muestra los indicadores
             mostrarnota();
          }
    });

    // ======================================================================== 
    // I) Al pulsar sobre la tabla indicadores, abre el diálogo para modificar 
    // la nota del indicador
    // ======================================================================= 
    $("#tablaindicadores").click(function(e){ 
        var parentTRTI = $(e.target).closest('tr'); // Get the parent row. Definida como variable global al principio
        var textodescripcion = $("td[class='textodescripcion']", parentTRTI).html(); // Retrieve the id content
        var textonotaindicador = $("td[class='textonotaindicador']", parentTRTI).text(); // Retrieve the id content
        /* // var columna = parentTR 
        var columna = $('td', parentTR).index(e.target);
        // var columna = $(this).parent().children().index($(this)); 
        alert(id + " -- " + columna); */
        var identificacion = $(e.target).attr("id");
        objeto = $("td[class='textonotaindicador']", parentTRTI); // este es el objeto destino
        // $('input[name=notaindicador]:nth(2)').attr('checked',true);
        $("#dialogoINDICADOR").dialog("open"); // abro el diálogo
     });

     // permite recargar los indicadores al recargar la página
     $("#tablaindicadores").ready(function(e){ 
	muestraindicadores(); // muestra los indicadores
     });

     // J) Cambia el valor en indicadores. Aseguro que los reconocerá
     $("#muestrainformacion").mouseenter(function(){
        // Segunda vez, por si puede cargar de nuevo
        if($("#indicadores").val()!="") { // si hay algo en el campo indicadores, 
	  notaindicadores($("#indicadores").val()); // recarga de nuevo la página...
        }
     });

     $("#selectoralumno").mouseleave(function(){
        // Segunda vez, por si puede cargar de nuevo
        if($("#indicadores").val()!="") { // si hay algo en el campo indicadores, 
	  notaindicadores($("#indicadores").val()); // recarga de nuevo la página...
        }
     });

     // K) al pulsar el 2º tab, muestra la calificacion de este alumno
     $("#calificacionh3").click(function(){
   	calculacalificacion(); // la vuelve a calcular de todas formas
        var active = $("#muestrainformacion").accordion("option", "active");
        if (active!=1) {
        	$("#dialogoMENSAJE").dialog("open");
        }
     });

     // K2) al pulsar sobre el 3º tab, se muestra la tabla resumen con las notas
     $("#tabladenotas").click(function(){
        muestratabladenotas();
     }); 

     // L) al pulsar el 4º tab, muestra la lista de calificaciones
     $("#listacalificacion").click(function(){
	calculacalificacion();
	listacalificaciones();
     });

     // M) Al pulsar sobre la celda correspondiente a la calificación, se abre el diálogo...
     $("#calificacion").click(function(e){
          // alert("he pulsado");
          var alumno = $('#alumno>option:selected').attr("title");
          recuperacalificacionunalumno(alumno); // recupera los datos de ese alumno de notas, para cambiarlas
          $("#dialogoCALIFICACIONES").dialog("open"); // abro el diálogo de cambio de esas calificaciones
     }); 

     // N) Pulso sobre la tabla y obtengo el id...
     $("#tablacalificaciones").click(function(e){ 
        var parentTRTI = $(e.target).closest('tr'); // Get the parent row. D
        var textoid = $("td[class='id']", parentTRTI).html(); // Retrieve the id content
        // alert(textoid);
	recuperacalificacionunalumno(textoid); // recupera el id del alumno y pone las notas
        $("#dialogoCALIFICACIONES").dialog("open"); // abro el diálogo de cambio de esas calificaciones
     });

     // O) Imprimir calificaciones
     $("#imprimir").click(function(){ 
	imprimelistacalificaciones(); // llama a la función correspondiente...
     });

     // P) Imprimir calificaciones de un alumno
     $("#imprimir2").click(function(){ 
	imprimecalificacionunalumno(); // llama a la función correspondiente...
     });

     // P2) Imprimir calificaciones de un alumno
     $("#imprimir4").click(function(){ 
	imprimecalificaciontodoslosalumnos(); // llama a la función correspondiente...
     });

     // Q) Imprimir notas de los alumnos
     $("#imprimir3").click(function(){ 
	imprimenotasalumnos(); // llama a la función correspondiente...
     });

     // R) Enviar a excel
     $("#excelexport").click(function(event) {
        // contenido del botón enviar
        // alert("envio");
        // BORRAR últimas dos columnas.... Un futuro...
        var contenido = $("<div>").append( $("#tablacalificaciones").eq(0).clone()).html();
	// $("#datos_a_enviar").val( $("<div>").append( $("#tablacalificaciones").eq(0).clone()).html());
        contenido = contenido.toString().replace(/\./g,','); // reemplaza puntos por comas
        contenido = contenido.split("Nota cal, ").join("Nota cal. ");
        contenido = contenido.split("Nota eva, ").join("Nota eva. ");
        contenido = contenido.split("Nota rec, ").join("Nota rec. "); // Excepciones
        $("#datos_a_enviar").val(contenido);
	$("#FormularioExportacion").submit();
     });

     // R2) Enviar a excel
     $("#excelexport2").click(function(event) {
        // contenido del botón enviar
        // alert("envio");
	$("#datos_a_enviar2").val( $("<div>").append( $("#datostabladenotas").eq(0).clone()).html());
	$("#FormularioExportacion2").submit();
     });

// ******************************************************************
    }); // Fin del document ready
// ******************************************************************

    
// ******************************************************************
// *** Funciones fuera del document ready
// ******************************************************************

// F1) Asignar variable de sesión lista EVALUACION, con llamada a script
function asignalistaevaluacion() {
         var le = $('#listaevaluaciones').val();
         var lce = $('#listaconceptoevaluativo').val();
         var alumno = $('#salumno').val();
          var posting = $.post( "./notas/asignalistaevaluacion.php", { 
              listaevaluacion: le,
              listaconceptoevaluativo: lce,
              sesionalumno: alumno,
          });
          posting.done(function(data,status) { 
              // alert(data);
              location.reload();
          });
} 

// F2) Mostrar indicadores del concepto evaluativo
function muestraindicadores() {
     var lce = $('#listaconceptoevaluativo').val();
     // alert(lce);
     var posting = $.post( "./notas/mostrarindicadorce.php", { 
           listaconceptoevaluativo: lce,
     });
     posting.done(function(data,status) { 
         var datos = $.parseJSON(data); // en la variable datos paso los datos tipo JSON
         // alert (data);
         if (data.length>2) {
  		 $("#tablaindicadores").find("tr:gt(0)").remove(); // Borra todo menos la primera fila.
                 $("#tablaindicadores").show();
		 for (var i in datos) {
                     var introducir = '<tr><td class="id" style="text-align: center;">' + i +
		      '</td><td class="textonombre" style="text-align: center;">' + decodificar(datos[i].nombre)+' ('+decodificar(datos[i].abreviatura)+')'+
                      '</td><td class="textonotaindicador" style="text-align: center;">Sin Calificar' +
                      '</td></tr>';
		     $("#tablaindicadores").append(introducir);
                     // alert(introducir);
		 } // fin del for  
         } else {
                 $("#tablaindicadores").find("tr:gt(0)").remove(); // Borra todo menos la primera fila.
                 $("#tablaindicadores").show();
	         $("#tablaindicadores").append('<tr><td colspan="3"><h2 style="text-align: center;">No hay datos</h2></td></tr>');
         } // fin del if
     });
}  

// F3) Recorrer los indicadores y extrae su valor
function recorreindicadores () {
   var cadena = "";
   $("#tablaindicadores tr").each(function(){
      var id = parseInt($("td[class='id']", $(this)).text()); // indicadores
      var notaindicador = $("td[class='textonotaindicador']", $(this)).text();
      if (id>=0) {
          switch(notaindicador)
          {
	    case "Mal": cadena = cadena + id +"*0-"; break;
            case "Regular": cadena = cadena + id +"*1-"; break;
            case "Bien": cadena = cadena + id +"*2-"; break;
            case "Excelente": cadena = cadena + id +"*3-"; break;
          } // fin del switch           
      } // fin del if    
   });
   cadena = cadena.substring(0,cadena.length-1); // quita el último guión
   $("#indicadores").val(cadena); // Lo pone en el campo indicadores.
}  

// ******************************************
// F4) Función mostrarnota. Recupera una nota
// ******************************************
function mostrarnota() { 
         // alert("Hola");
         var ce = $('#listaconceptosevaluativos').val();
         var alumno = $('#alumno>option:selected').attr("title");
         // alert(ce+" "+alumno);
         // llamada al script php
         var posting = $.post( "./notas/muestranota.php", { 
             ce: ce,
             alumno: alumno,
         });
         posting.done(function(data,status){
            // alert("El programa responde: "+data);
	    var datos = $.parseJSON(data); // en la variable datos paso los datos tipo JSON
            // alert(datos.IDnota);
            if (parseInt(datos.IDnota)>=0) {
               $('#IDnota').val(datos.IDnota);
               $("#selectornota").slider("value",parseFloat(datos.nota));
               $("#indicadores").val(datos.indicadores); // campo indicadores
               $("#modificadornota").html(datos.modificadornota); // Modificador nota 1
               $("input[name=modificarnota]").filter('[value="'+datos.modificadornota+'"]').prop("checked", true); // Modificador nota 2
               notaindicadores(datos.indicadores); // los pone en la tabla...
            } else { // valores de inicio en la nota, si es que no lo encuentra...
               $('#IDnota').val(""); // importante borrar el valor de aquí...
               $("#selectornota").slider("value",0);
               $("#indicadores").val(""); // campo indicadores
	       $("#modificadornota").html("?"); // Modificador nota 1
               $("input[name=modificarnota]").filter('[value="?"]').prop("checked", true); // Modificador nota 2
            }         
            colorcelda();   
         });
} // Insertar un dato. Fin de la función 

// *****************************************
// F5) Función insertar. Inserta un dato 
// *****************************************
function insertar() { 
         // alert("Hola");
         var IDnota = $('#IDnota').val();
         var ce = $('#listaconceptosevaluativos').val();
         var alumno = $('#alumno>option:selected').attr("title");
 	 var nota = $("#selectornota").slider("value");
         var indicadores = $("#indicadores").val();
         var modificarnota = $("input[name=modificarnota]:checked").val();
         // alert(IDnota+" "+ce+" alumno: "+alumno+" // Notas: "+nota+" "+indicadores+" "+modificarnota);
         // llamada al script php
         var posting = $.post( "./notas/insertarnota.php", { 
	     idnota: IDnota,
             ce: ce,
             alumno: alumno,
             nota: nota,
             indicadores: indicadores,
             modificarnota: modificarnota,
         });
         posting.done(function(data,status){
            alert("El programa responde: "+data);
	    // location.reload(); // No, porque si no recarga
            calculacalificacion(); // EXPERIMENTAL. Calcula ya la calificacion...
            asignalistaevaluacion(); // guarda variables de sesión y recarga
         });
} // Insertar un dato. Fin de la función 

// *****************************************
// F6) Función insertar. Inserta un dato 
// *****************************************
function notaindicadores(lista) {
   // alert(lista);
   var parejas = lista.split("-"); // divido primero el asterisco
        for (var i in parejas) {
             var datos = parejas[i].split("*"); // y ahora obtengo ID y calificacion
             // 2º) recorrer la tablaindicadores 
             // alert("ID: "+datos[0]+" - nota: "+datos[1]);
             $("#tablaindicadores tbody tr").each(function() {
	       var id = $(this).children("td[class='id']").text();
               if (id==datos[0]) { //si coinciden los indicadores
                        if(datos[1]==="0") {cadena = "Mal";}
			if(datos[1]==="1") {cadena = "Regular";}
			if(datos[1]==="2") {cadena = "Bien";}
			if(datos[1]==="3") {cadena = "Excelente";}
		   $(this).children("td[class='textonotaindicador']").text(cadena);	                  
               }                 
             });   
   }
}

// *****************************************
// F7) color celda
// *****************************************
function colorcelda() {
    var valorcelda = parseFloat($("#notaalumno").text());
    // alert(valorcelda);
    if (valorcelda<5) {
       // alert("catear");
       $("#notaalumno").css("color",$("#idalumno").css("background-color")); // color texto
       $("#notaalumno").css("background-color",$("#idalumno").css("color")); 
    } else {
       $("#notaalumno").css("color",$("#idalumno").css("color")); // color de la celda de al lado
       $("#notaalumno").css("background-color",$("#idalumno").css("background-color")); 
    }
}

// *****************************************
// F8) calcula calificación
// *****************************************
function calculacalificacion() {
         var alumno = $('#alumno>option:selected').attr("title");
         var le = $('#listaevaluaciones').val();
         var asignacion = $('#asignacion').val();
         // llamada al script php
         var posting = $.post( "./notas/calculacalificacion.php", { 
             alumno: alumno,
             evaluacion: le,
             asignacion: asignacion,
         });
         posting.done(function(data,status){
            // $("#dialogoMENSAJE").dialog("open");
            // $("#infnotas").html(data); // MUESTRA TODOS LOS DATOS
            // alert("El programa responde: "+data);
	    // location.reload(); // No, porque si no recarga
            var datos = $.parseJSON(data);
            // Nota total
            $("#notatotal").text(datos.notatotal); // Escribe la nota total en el lugar de la tabla...
            $("#calificacion").text(datos.notaseneca);
            // ****************************
            // Por instrumentos evaluativos
            // ****************************

            if (datos.poriev[0].nombreiev.length>0) {
  		 $("#tablanotasiev").find("tr:gt(0)").remove(); // Borra todo menos la primera fila.
                 $("#tablanotasiev").show();
		 for (var i in datos.poriev) {
                     // color nota
                     if (datos.poriev[i].nm>(datos.poriev[i].notaiev/datos.poriev[i].pesoiev)) 
                         { colornota="FF0000"; italica="italic"; } else { colornota="000000"; italica="normal";}
                     // 
                     var mostrar = "Porcentaje: "+decodificar(datos.poriev[i].por)+"% -- Nota mínima: "+decodificar(datos.poriev[i].nm); // Muestra porcentaje y notaminima...
                     var mostrar2 = "Suma de puntos: "+decodificar(datos.poriev[i].notaiev)+" -- Suma de pesos: "+decodificar(datos.poriev[i].pesoiev)+" -- puntos/pesos: "+(decodificar(datos.poriev[i].notaiev)/decodificar(datos.poriev[i].pesoiev))+" -- Formula de cálculo: ("+decodificar(datos.poriev[i].por)+"/100)*("+decodificar(datos.poriev[i].notaiev)+"/"+decodificar(datos.poriev[i].pesoiev)+")"; // Muestra la suma de las notas, de los pesos, y los cálculos hechos...
                     var mediacal = decodificar(datos.poriev[i].notaiev)/decodificar(datos.poriev[i].pesoiev);
                     var introducir = '<tr><td class="textonombreiev" title="'+ mostrar + '" style="text-align: left;">' + decodificar(datos.poriev[i].nombreiev)+ ' ('+decodificar(datos.poriev[i].por)+'% - min: '+decodificar(datos.poriev[i].nm)+')'+
                      '</td><td class="textoabreviatura"  title="'+ mostrar + '" style="text-align: center;">'+ decodificar(datos.poriev[i].abreviatura)+
                      '</td><td class="textonotaiev" title="'+ mostrar2 + '" style="text-align: center;"><font color="'+colornota+'" style="font-style:'+italica+';">'+ decodificar(datos.poriev[i].nota)+'</font>'+
                      '</td><td class="textonotaiev" style="text-align: center;"><font color="'+colornota+'" style="font-style:'+italica+';">'+ Math.round(mediacal*100)/100 +'</font>'+
                      '</td></tr>';
		     $("#tablanotasiev").append(introducir);
                     // alert(introducir);
		 } // fin del for  
         } else {
                 $("#tablanotasiev").find("tr:gt(0)").remove(); // Borra todo menos la primera fila.
                 $("#tablanotasiev").show();
	         $("#tablanotasiev").append('<tr><td colspan="4"><h2 style="text-align: center;">No hay datos</h2></td></tr>');
         } // fin del if

            // ****************************
            // Listado de competencias
            // ****************************
            if (datos.COMPETENCIAS[0].descripcion.length>0) {
  		 $("#tablanotascompetencias").find("tr:gt(0)").remove(); // Borra todo menos la primera fila.
                 $("#tablanotascompetencias").show();
		 for (var i in datos.COMPETENCIAS) {
                     var mostrar = decodificar(datos.COMPETENCIAS[i].nota1)+" sobre 4";
                     var introducir = '<tr><td class="textodescripcioncompetencia" style="font-size: 1.2em;text-align: left; text-indent: 1em;">' + decodificar(datos.COMPETENCIAS[i].descripcion)+" ("+decodificar(datos.COMPETENCIAS[i].abreviatura)+")"+
                      '</td><td class="textonotaiev" title="'+mostrar+'" style="text-align: center; font-size: 1.2em;">'+ decodificar(datos.COMPETENCIAS[i].nota2)+
                      '</td></tr>';
		     $("#tablanotascompetencias").append(introducir);
                     // alert(introducir);
		 } // fin del for  
         } else {
                 $("#tablanotascompetencias").find("tr:gt(0)").remove(); // Borra todo menos la primera fila.
                 $("#tablanotascompetencias").show();
	         $("#tablanotascompetencias").append('<tr><td colspan="2"><h2 style="text-align: center;">No hay datos</h2></td></tr>');
         } // fin del if 
            
         });
} // FIN de calculacalificacion

// *****************************************
// F9) Comprobar foto
// *****************************************
function compruebafoto() {
         var alumno = $('#alumno>option:selected').attr("title");
         // alert(alumno);
         var posting = $.post( "./notas/compruebafoto.php", { 
             alumno: alumno,
         });
         posting.done(function(data,status){
             if (data.length>0) {
                $("#insertareditardiv").css("top","130px");
                $("#fotografia").attr("src",data);
                $("#fotografia2").attr("src",data);
                var ancho = $("#fotografia").width();
                var derecha = $("#foto").css("right");
                derecha = (170 - ancho)/2; // para poder centrarla, más o menos
                $("#foto").css("right",derecha);
                // alert(ancho+" "+alto+" "+derecha);
                // alert("hola");
                $("#fotografia").show();
                $("#fotografia2").show();
             } else {
                $("#fotografia").hide();
                $("#fotografia2").hide();
                $("#insertareditardiv").css("top","65px");
             } 
         });
} // fin de comprobar foto

// *****************************************
// F10) Lista de calificaciones
// *****************************************
function listacalificaciones() {
   // alert("lista de calificaciones");
   var le = $('#listaevaluaciones').val();
   var asignacion = $('#asignacion').val();    
   // alert("Hola");
   var posting = $.post( "./notas/listacalificaciones.php", { // aquí hay que poner un obtiene calificación
             evaluacion: le,
             asignacion: asignacion,
   });
   posting.done(function(data,status){ 
           // alert(data);
           var datos = $.parseJSON(data); 
           // alert(datos.calificaciones[0].alumno); 
           if (datos.calificaciones[0].alumno.length>0) {
  		 $("#tablacalificaciones").find("tr:gt(0)").remove(); // Borra todo menos la primera fila.
                 $("#tablacalificaciones").show();
                 // Estadísticas al final de la tabla...
		 // alert(datos.nmaprobados+" - "+datos.nmsuspensos);
		 for (var i in datos.calificaciones) {
                     // alert(datos.calificaciones[i].alumno); 
		     // colores
                     var num = parseInt(i)+1;
                     var par = parseInt(num%2);
                     //alert(num);
                     if (par==0) { colorfila="DFDFDF"; } else {  colorfila="FFFFFF"; } // o gris o blanco
                     // color texto
		     if (datos.calificaciones[i].notamedia<5) { colornotamedia="FA2222"; } else {  colornotamedia="black"; }
                     if (datos.calificaciones[i].notaseneca<5) { colornotaseneca="FA2222"; } else {  colornotaseneca="black"; }
                     // color fila
                     if (datos.calificaciones[i].notamedia<5) { colorfilanm="F5A9A9"; } else {  colorfilanm=colorfila; }
                     if (datos.calificaciones[i].notaseneca<5) { colorfilans="F5A9A9"; } else {  colorfilans=colorfila; }
                     // color recuperacion
	 	     if (datos.calificaciones[i].notarecuperacion!="-" && datos.calificaciones[i].notarecuperacion>0 && datos.calificaciones[i].notarecuperacion<5) { colornotarecuperacion="FA2222"; } else if (datos.calificaciones[i].notarecuperacion!="-" && datos.calificaciones[i].notarecuperacion>=5) { colornotarecuperacion="2222FA"; }
                     else { colornotarecuperacion="black"; }
                     // tabla
                     var introducir = '<tr><td class="N" style="text-align: center; background-color: '+colorfila+'; ">' + num  +
                      '</td><td class="textonombre" style="text-align: left; background-color: '+colorfila+';text-indent: 1em;">'+ decodificar(datos.calificaciones[i].alumno)+
                      '</td><td class="textonotamedia" style="text-align: center; background-color: '+colorfilanm+';"><font color="'+colornotamedia+'">'+ decodificar(datos.calificaciones[i].notamedia)+'</font>'+
                      '</td><td class="textonotaseneca" style="text-align: center; background-color: '+colorfilans+';"><font color="'+colornotaseneca+'">'+ decodificar(datos.calificaciones[i].notaseneca)+'</font>'+
                      '</td><td class="textonotarecuperacion" style="text-align: center; background-color: '+colorfila+';"><font color="'+colornotarecuperacion+'">'+ decodificar(datos.calificaciones[i].notarecuperacion)+'</font>'+
                      // Los siguientes datos de la tabla no deben verse... DISPLAY: NONE;
                      '</td><td class="id" style="text-align: center; background-color: '+colorfila+';display: none;">'+ decodificar(datos.calificaciones[i].id)+
                      '</td><td class="idcal" style="text-align: center; background-color: '+colorfila+';display: none;">'+ decodificar(datos.calificaciones[i].idcalificacion)+
                      '</td></tr>';
		     $("#tablacalificaciones").append(introducir);
                     // alert(introducir);
		 } // fin del for  
// Estadísticas...
var introducir = '<tr><td colspan="5" height="30px" style="text-indent: 1em;">Nota cal. aprobados: '+decodificar(datos.nmaprobados)+' ('+decodificar(datos.nmporaprobados)+'%) - Nota cal. suspensos: '+decodificar(datos.nmsuspensos)+' ('+decodificar(datos.nmporsuspensos)+'%) - No calificados: '+decodificar(datos.nmno)+'</td></tr>';
$("#tablacalificaciones").append(introducir);
var introducir = '<tr><td colspan="5" height="30px" style="text-indent: 1em;">Nota eva. aprobados: '+decodificar(datos.nsaprobados)+' ('+decodificar(datos.nsporaprobados)+'%) - Nota eva. suspensos: '+decodificar(datos.nssuspensos)+' ('+decodificar(datos.nsporsuspensos)+'%) - No calificados: '+decodificar(datos.nsno)+'</td></tr>';
$("#tablacalificaciones").append(introducir);
var introducir = '<tr><td colspan="5" height="30px" style="text-indent: 1em;">Nota rec. aprobados: '+decodificar(datos.nraprobados)+' ('+decodificar(datos.nrporaprobados)+'%) - Nota rec. suspensos: '+decodificar(datos.nrsuspensos)+' ('+decodificar(datos.nrporsuspensos)+'%) - No calificados: '+decodificar(datos.nrno)+'</td></tr>';
$("#tablacalificaciones").append(introducir);
var introducir = '<tr><td colspan="5" height="30px" style="text-indent: 1em;">Total aprobados: '+decodificar(datos.ntaprobados)+' ('+decodificar(datos.ntporaprobados)+'%) - Total suspensos: '+decodificar(datos.ntsuspensos)+' ('+decodificar(datos.ntporsuspensos)+'%) - Total no calificados: '+decodificar(datos.ntno)+'</td></tr>';
$("#tablacalificaciones").append(introducir);

         } else {
                 $("#tablacalificaciones").find("tr:gt(0)").remove(); // Borra todo menos la primera fila.
                 $("#tablacalificaciones").show();
	         $("#tablacalificaciones").append('<tr><td colspan="6"><h2 style="text-align: center;">No hay datos</h2></td></tr>');
         } // fin del if */
   }); // fin del posting 

} // fin de lista calificaciones



// *****************************************************************************************
// F11) Calificaciones de un alumno. Recupera datos de un alumno de notamedia y recuperación
// *****************************************************************************************

function recuperacalificacionunalumno(alumn) {
   var le = $('#listaevaluaciones').val();
   var asignacion = $('#asignacion').val();    
   var posting = $.post( "./notas/listacalificacionunalumno.php", { // aquí hay que poner un obtiene calificación
             evaluacion: le,
             asignacion: asignacion,
             alumno: alumn,
   });
   posting.done(function(data,status){ 
       // alert(data);
       var datos = $.parseJSON(data);  
       if (datos.calificacion.alumno.length>2) {
           $("#muestradatosalumno").html("Id: "+datos.calificacion.id+" - Nombre: "+decodificar(datos.calificacion.alumno)+" - Nota que le sale de media: "+datos.calificacion.notamedia); // Muestro losa datos del alumno...
           $("#innotaseneca").val(datos.calificacion.notaseneca); // Muestro los datos de notas para uno y otro
           $("#innotarecuperacion").val(datos.calificacion.notarecuperacion); 
           $("#inidcalificacion").val(datos.calificacion.idcalificacion); 
           $("#inidalumno").val(datos.calificacion.id); 
           $("#innotamedia").val(datos.calificacion.notamedia); 
       } // fin del if
   });
} // fin de recuperacalificacionunalumno

// *****************************************************************************************
// F12) Calificaciones de un alumno. Recupera datos de un alumno de notamedia y recuperación
// *****************************************************************************************
function modificacalificacion() {
    var idcalificacion = $("#inidcalificacion").val();
    // alert(idcalificacion+ " " + $("#muestradatosalumno").text());
    var le = $('#listaevaluaciones').val();
    var asignacion = $('#asignacion').val();  
    var alumno = $("#inidalumno").val();
    var notaseneca = parseInt($("#innotaseneca").val());
    var notarecuperacion = parseInt($("#innotarecuperacion").val());
    var notamedia = $("#innotamedia").val();
    if (notarecuperacion>10 || notarecuperacion<0 || notaseneca>10 || notaseneca<0) {
       alert ("Cuidado. Hay notas no válidas fuera del rango [0-10].");
       return;
    } // Si no, sale
    var posting = $.post( "./notas/modificacalificacion.php", { // aquí hay que poner un obtiene calificación
             evaluacion: le,
             asignacion: asignacion,
             alumno: alumno,
             idcalificacion: idcalificacion,
             notaseneca: notaseneca,
             notarecuperacion: notarecuperacion,
             notamedia: notamedia,
    });
    posting.done(function(data,status){ 
        calculacalificacion(); // recarga los datos...
        listacalificaciones(); // y la lista de las calificaciones
        alert(data);
    });

} // fin de modificacalificacion

// *****************************************************************************************
// F13) Imprime lista de calificaciones
// *****************************************************************************************
function imprimelistacalificaciones () {
   var le = $('#listaevaluaciones').val();
   var asignacion = $('#asignacion').val();    
   // alert(le + " " + asignacion);
   var posting = $.post( "./notas/listacalificaciones.php", { // aquí hay que poner un obtiene calificación
      evaluacion: le,
      asignacion: asignacion,
   });
   posting.done(function(data,status){ 
    	var titulouno = $("#titulo1").html();
  	var titulodos = $("#titulo2").html();
        // alert(titulouno+" // "+titulodos);
        var evaluacion = $("#listaevaluaciones > option:selected").text();
        var posting2 = $.post( "./ficheros/notasuncurso.php", {
             contenido: data,
             titulouno: titulouno,
             titulodos: titulodos,
             evaluacion: evaluacion,             
        });
        posting2.done(function(data2,status2){ 
	    window.open(data2,"_blank");
            // alert(data2);
        });
   });
} // fin de imprime lista de calificaciones

// *****************************************************************************************
// F14) Imprime lista de calificaciones
// *****************************************************************************************
function imprimecalificacionunalumno() {
         // alert("Hola");
         var alumno = $('#alumno>option:selected').attr("title");
         var le = $('#listaevaluaciones').val();
         var asignacion = $('#asignacion').val();
         // llamada al script php
         var posting = $.post( "./notas/calculacalificacion.php", { 
             alumno: alumno,
             evaluacion: le,
             asignacion: asignacion,
         });
         posting.done(function(data,status){
             var titulouno = $("#titulo1").html();
  	     var titulodos = $("#titulo2").html();
             var alumno = $('#alumno>option:selected').text();
             var idalumno = $('#alumno>option:selected').attr("title");
             var evaluacion = $("#listaevaluaciones > option:selected").text();
             var posting2 = $.post( "./ficheros/notasunalumno.php", {
                contenido: data,
             	titulouno: titulouno,
             	titulodos: titulodos,
             	evaluacion: evaluacion,  
                alumno: alumno,  
                idalumno: idalumno,         
             });
             posting2.done(function(data2,status2){ 
	        window.open(data2,"_blank");
             });
         });
}

// *****************************************************************************************
// F15) Imprime lista de notas
// *****************************************************************************************
function imprimenotasalumnos() {
         // alert("print notas");
         var le = $('#listaevaluaciones').val();
         var asignacion = $('#asignacion').val();
         // llamada al script php
         var posting = $.post( "./notas/mostrarnotas.php", { 
             evaluacion: le,
             asignacion: asignacion,
         });
         posting.done(function(data,status){
		// alert(data);
                var titulouno = $("#titulo1").html();
  	        var titulodos = $("#titulo2").html();
                var evaluacion = $("#listaevaluaciones > option:selected").text();
		var asignacion = $('#asignacion').val();
                var posting3 = $.post( "./notas/listacalificaciones.php", {
			evaluacion: le,  // evaluación pero código, no texto
                        asignacion: asignacion,
                }); 
                posting3.done(function(data3,status3){ 
                        var posting2 = $.post( "./ficheros/notasalumnos.php", {
		        contenido: data,
                        resultados: data3,
		     	titulouno: titulouno,
		     	titulodos: titulodos,
		     	evaluacion: evaluacion,  
		        asignacion: asignacion,
		        // alumno: alumno,  
		        // idalumno: idalumno,         
		        });
		         posting2.done(function(data2,status2){ 
			  window.open(data2,"_blank");
		        });
                });                
         }); 
}


// *****************************************************************************************
// F16) Imprime calificaciones todos los alumnos
// *****************************************************************************************
function imprimecalificaciontodoslosalumnos() {
	alert("si puedo lo hago en algún momento...");
}

// *****************************************************************************************
// F17) Muestra una tabla resumen con tods las notas...
// *****************************************************************************************
function muestratabladenotas() {
	// alert("print notas");
         var le = $('#listaevaluaciones').val();
         var asignacion = $('#asignacion').val();
         // llamada al script php
         var posting = $.post( "./notas/mostrartablaconnotas.php", { 
             evaluacion: le,
             asignacion: asignacion,
         });
         posting.done(function(data,status){
             // alert(data);
             $("#datostabladenotas").html(data);
         });
} // fin de muestra tabla de notas

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

<!-- ================================== -->
<!-- Muestra los datos de la asignación -->
<!-- ================================== -->
	<p id="titulo1"><span style="color: #1111FF; font-weight:blod;">Profesor: </span><?php echo cambiarnombre($datosasignacion["profesor"]); ?>
        -
        <span style="color: #1111FF; font-weight:blod;">Materia: </span><?php echo $datosasignacion["materia"].' '.$datosasignacion["tutorada"];?></p>
        <p id="titulo2"><span style="color: #1111FF; font-weight:blod;">Descripción: </span><?php echo $datosasignacion["descripcion"]; ?>
        -
        <span style="color: #1111FF; font-weight:blod;">Clases: </span>
        <?php echo $alumno['cadenaclases']; ?></p>

<!-- == variables de sesión == -->
<input id="profesor" class="botones" type="hidden" style="text-align: left;" size="38" maxlength="250" value="<?php echo $_SESSION['profesor'];?>">
<input id="asignacion" class="botones" type="hidden" style="text-align: left;" size="38" maxlength="250" value="<?php echo $_SESSION['asignacion'];?>">
<input id="listaevaluacion" class="botones" type="hidden" style="text-align: left;" size="38" maxlength="250" value="<?php echo $_SESSION['listaevaluacion'];?>">
<input id="listaconceptoevaluativo" class="botones" type="hidden" style="text-align: left;" size="38" maxlength="250" value="<?php echo $_SESSION['listaconceptoevaluativo'];?>">
<input id="salumno" class="botones" type="hidden" style="text-align: left;" size="38" maxlength="250" value="<?php echo $_SESSION['sesionalumno'];?>">
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

<!-- =========================================== -->
<!-- ********** Conceptos evaluativos ********** -->
<!-- =========================================== -->
<div id="conceptosevaluativos" class="presentardatos" style="overflow: auto; width: 80%;">
<table width="100%"><tr style="vertical-align:middle;">
     <td width="5%" style="border: 0px solid black;"><img id="atrasce" src="./imagenes/otros/back.png" width="30px"></td>
     <td width="80%" style="vertical-align:middle; border: 0px solid black; font-weight: bold; font-size: 1.4em;">
	<!-- Escribir aqui el concepto evaluativo -->
        <select id="listaconceptosevaluativos" class="botones" style="text-align: left; font-size: 0.8em; margin: 0px 0px;">
           <?php 
		foreach ($listaconceptosevaluativos['IDcev'] as $key => $valor) {
                   echo '<option value="'.$valor.'" title="'.$listaconceptosevaluativos['descripcion'][$key].'">'.$listaconceptosevaluativos['nombrece'][$key].' ('.$listaconceptosevaluativos['abreviatura'][$key].')</option>';
                }
           ?>
        </select>
     </td>
     <td width="5%" style="border: 0px solid black;"><img id="siguientece" src="./imagenes/otros/next.png" width="30px"></td>
</tr></table>        
</div>
<!-- ================================================================== -->

<!-- ================================================================== -->
<!-- ================================================================== -->
<div id="muestrainformacion" style="overflow: auto; width: 95%; margin: 0px auto;"> <!-- capa de información -->
<!-- ================================================================== -->
<!-- ================================================================== -->

<!-- ***************** -->
<!-- Notas un alumno   -->
<!-- ***************** -->
<h3 class="insertarh3">Notas de un concepto evaluativo para un ALUMNO/A</h3>
<div id="datos" style="border: 0px solid red; position: relative;">
<div id="selectoralumnos" style="float: left; width: 23%; height: 30px; border: 0px solid black;">
        <img id="imprimir3" src="./imagenes/otros/print.png" width="30px" height="auto">
	<img id="sprimero" src="./imagenes/otros/go_first.png" width="30px">
	<img id="satras" src="./imagenes/otros/back.png" width="30px">
	<img id="sdelante" src="./imagenes/otros/next.png" width="30px">
	<img id="sultimo" src="./imagenes/otros/go_last.png" width="30px">
</div>
<div id="selectoralumno" style="float: left; width: 60%; vertical-align: middle; height: 10px; border: 1px solid black;"></div>

<!-- ============================ -->
<!-- Se incluye fotografía -->
<!-- ============================ -->
<div id="foto" style="margin: 1px 1px 1px 25px; position: absolute; top:35px; right:55px; height: 85px; float: none; border:0px solid black; padding: 2px;">'
<img id="fotografia" style="border: 2px solid #C37508; display: none;" height="75px">
</div> 

<!-- ============================ -->
<!-- Botón guardar -->
<!-- ============================ -->
<div id="insertareditardiv" style="boder: 0px solid black; position:absolute; top: 130px; right: 10px;">
<a id="insertareditar" style="margin: 10px; top: 5px; right: 5px; color: black;" class="a_demo_four" onclick="insertar();">Guardar</a>
</div>

<!-- comprobación
<p><?php
foreach ($alumno['alumno'] as $key => $valor) {
	echo $alumno['idalumno'][$key].' :'.$valor.' ['.$alumno['unidad'][$key].']<br>';
} ?>
</p>  -->

<div id="datos2" style="overflow: auto; float: left; text-align: left; height: auto; width: 100%; border: 0px black solid; margin-top: 5px;">
           <select id="alumno" style="display: none;"> <!-- para verlo, display none -->
           <?php 
                $ii=0;
		foreach ($alumno['alumno'] as $key => $valor) {
                   echo '<option value="'.$ii.'" title="'.$alumno['idalumno'][$key].'">'.$valor.'</option>';
                   $ii++;
                }
           ?>
           </select>

<input id="IDnota" type="hidden" size="25" class="cajones"> <!-- para verlo, type text -->
          
<table id="tablanotas" style="display: auto; margin:2px 20px; height: auto; text-align: center; width: 80%; table-layout: fixed;"  border="1" cellpadding="1" cellspacing="1" class="tabla">
	<tr style="vertical-align: middle; height: 40px;">
	<th id="idalumno" style="width: 5%; font-weight: bold; text-align: center;">N-Id</th>
	<th id="nombrealumno" style="width: 65%; font-weight: bold; font-size: 1.2em; text-align: left; text-indent: 1em;"></th>
	<th id="notaalumno" style="width: 20%; font-weight: bold; font-size: 1.5em; text-align: center;"></th>
	<th id="modificadornota" style="width: 10%; font-weight: bold; font-size: 1.5em; text-align: center;"></th>
	</tr>
        <tr style="vertical-align: middle; height: 30px; "><th colspan="2" style="text-align: center;">
            <input type="radio" id="r1" name="modificarnota" class="modificarnota" value="N" checked><label for="r1"><span></span>Normal</label>
	    <input type="radio" id="r2" name="modificarnota" class="modificarnota" value="FJ"><label for="r2"><span></span>Falta justificada</label>
            <input type="radio" id="r3" name="modificarnota" class="modificarnota" value="FI"><label for="r3"><span></span>Falta injustificada</label><br>
            <input type="radio" id="r4" name="modificarnota" class="modificarnota" value="?"><label for="r4"><span></span>No calificada</label>
            <input type="radio" id="r5" name="modificarnota" class="modificarnota" value="E"><label for="r5"><span></span>Enfermedad</label>
	    </th><th>            
            <div id="selectornota" style="width: 90%; height: 10px; margin: 0px auto;"></div>
	    </th><th style="text-align: center;"> 
	    <input id="selectornota2" name="selectornota2" size="1"/>
        </th></tr>
</table>
          
<!-- indicadores -->
        <p style="text-align: left; " >Lista de indicadores</p>
        <input id="indicadores" type="hidden" size="80"> <!-- para verlo, display none -->
	<table id="tablaindicadores" style="display: auto; margin:2px auto; height: auto; text-align: center; width: 90%; table-layout: fixed;"  border="1" cellpadding="1" cellspacing="1" class="tabla">
		<tr style="vertical-align: middle;">
                <th style="width: 5%; font-weight: bold; text-align: center;">N-Id</th>
                <th style="width: 60%; font-weight: bold; text-align: center;">Descripción</th>
                <th style="width: 35%; font-weight: bold; text-align: center;">Calificación</th>
                <tr>
         </table>
        </div> <!-- FIN DE DATOS 2...  -->
</div> <!-- FIN DE DATOS...  -->

<!-- ****************************** -->
<!-- Calificación esta evaluación   -->
<!-- ****************************** -->
<h3 id="calificacionh3" class="insertarh3">Calificacion para *** esta evaluación</h3>
<div id="datos3" style="border: 0px solid red; position: relative;">
	<div id="infnotas" style="width: 40%; border: 0px solid black;"></div> <!-- No borrar: recupera datos JSON -->

<!-- ============================ -->
<!-- Se incluye fotografía -->
<!-- ============================ -->
<div id="foto2" style="margin: 1px 1px 1px 25px; position: absolute; top:35px; right:25px; height: 85px; float: none; border:0px solid black; padding: 2px;">'
<img id="fotografia2" style="border: 2px solid #C37508; display: none;" height="75px">
</div> 

<h2>Nota global</h2>

        <!-- Tabla de notas -->
        <table id="tablanotas" style="display: auto; margin:2px 45px; height: auto; text-align: center; width: 75%; table-layout: fixed;" border="1" cellpadding="1" cellspacing="1" class="tabla">
		<tr style="vertical-align: middle;">
                <th style="width: 10%; font-weight: bold; font-size: 1.2em; text-align: center;">Nota</th>
                <th id="notatotal" style="width: 25%; font-weight: bold; font-size: 1.2em; text-align: center;">N</th>  
		<th style="width: 20%; font-weight: bold; font-size: 1.2em; text-align: center;">Calificacion</th>  
		<th id="calificacion" style="width: 45%; font-weight: bold; font-size: 1.2em; text-align: center;"></th>                               
                <tr>
         </table>   

        <!-- Tabla de Instrumentos evaluativos -->
<br>
<h2>Lista de instrumentos evaluativos</h2>
        <table id="tablanotasiev" style="display: auto; margin:2px auto; height: auto; text-align: center; width: 90%; table-layout: fixed;" border="1" cellpadding="1" cellspacing="1" class="tabla">
	<tr style="vertical-align: middle;">
	<th style="width: 60%; font-weight: bold; font-size: 1.2em; text-align: center;">Nombre Instrumento Evaluativo</th>
	<th style="width: 14%; font-weight: bold; font-size: 1em; text-align: center;">Abreviatura</th>  
	<th style="width: 16%; font-weight: bold; font-size: 1em; text-align: center;">Contribución nota</th>     
	<th style="width: 10%; font-weight: bold; font-size: 1em; text-align: center;">Media</th>                               
	<tr>
         </table> 

        <!-- Tabla de Calificación de competencias -->
<br>
<h2>Lista de Indicadores y su calificación</h2>
        <table id="tablanotascompetencias" style="display: auto; margin:2px auto; height: auto; text-align: center; width: 90%; table-layout: fixed;" border="1" cellpadding="1" cellspacing="1" class="tabla">
	<tr style="vertical-align: middle;">
	<th style="width: 75%; font-weight: bold; font-size: 1.2em; text-align: center;">Competencia</th>
	<th style="width: 25%; font-weight: bold; font-size: 1.2em; text-align: center;">Apreciación</th>                               
	<tr>
         </table> 

      <!-- Imagenes imprimir -->
      <div id="impresora2" style="position: absolute; top: 5px; left: 200px;">
      <img id="imprimir2" src="./imagenes/otros/print.png" width="50px" height="auto">
      <!-- <img id="imprimir4" src="./imagenes/otros/print.png" width="30px" height="auto"> -->
      </div>
     
</div>

<!-- ******************* -->
<!-- Tabla con las notas -->
<!-- ******************* -->
<h3 id="tabladenotas" class="insertarh3">Resumen con las notas de un curso</h3>
<div id="datos5" style="border: 0px solid red; position: relative;">
      <!-- Exportar a excel -->
      <div id="excel" style="position: absolute; top: 10px; right: 4px;">
        <form action="./ficheros/exportaraexcel.php" method="post" target="_blank" id="FormularioExportacion2">
        <img id="excelexport2" src="./imagenes/otros/calc.png" width="50px" height="auto">
        <input type="hidden" id="datos_a_enviar2" name="datos_a_enviar" />
        </form>    
      </div> 
      <div id="datostabladenotas"></div>   
</div>

<!-- ****************************** -->
<!-- Lista de calificaciones    -->
<!-- ****************************** -->
<h3 id="listacalificacion" class="insertarh3">Lista de calificaciones para esta evaluación y asignación</h3>
<div id="datos4" style="border: 0px solid red; position: relative;">

      <!-- tabla calificaciones    -->
      <table id="tablacalificaciones" style="display: auto; margin:2px auto; height: auto; text-align: center; width: 90%; table-layout: fixed;" border="1" cellpadding="1" cellspacing="1" class="tabla">
      <tr style="vertical-align: middle;">
      <th style="width: 5%; font-weight: bold; font-size: 1.2em; text-align: center;">N.</th>
      <th style="width: 50%; font-weight: bold; font-size: 1.2em; text-align: center;">Nombre</th>  
      <th style="width: 15%; font-weight: bold; font-size: 1em; text-align: center;">Nota calculada</th>  
      <th style="width: 15%; font-weight: bold; font-size: 1em; text-align: center;">Nota evaluación</th>
      <th style="width: 15%; font-weight: bold; font-size: 1em; text-align: center;">Nota recuperación</th>
      <th style="width: 1%; display: none; font-weight: bold; font-size: 1em; text-align: center;">IDAL</th>       
      <th style="width: 1%; display: none; font-weight: bold; font-size: 1em; text-align: center;">IDCAL</th>                            
      <tr>

      <!-- Imagenes imprimir -->
      <div id="impresora" style="position: absolute; top: 10px; right: 4px;">
      <img id="imprimir" src="./imagenes/otros/print.png" width="50px" height="auto">
      </div>

      <!-- Exportar a excel -->
      <div id="excel" style="position: absolute; top: 60px; right: 4px;">
        <form action="./ficheros/exportaraexcel.php" method="post" target="_blank" id="FormularioExportacion">
        <img id="excelexport" src="./imagenes/otros/calc.png" width="50px" height="auto">
        <input type="hidden" id="datos_a_enviar" name="datos_a_enviar" />
        </form>    
      </div> 

</div> <!-- Fin de div 4 ????  -->

<!-- ================================================================== -->
<!-- ================================================================== -->
</div> <!-- Fin de la capa de información -->
<!-- ================================================================== -->
<!-- ================================================================== -->

<!-- ================================================================== -->
<!-- ================================================================== -->
<!-- Capa diálogo para obtener datos-->
<!-- ================================================================== -->
<!-- ================================================================== -->

<div id="dialogoMENSAJE" title="Mensaje importante">
	<p style="text-align: justify;">Repasa las notas de los alumnos/as UNA a UNA. Modifica el valor numérico de la evaluación
        teniendo en cuenta el valor calculado y el resto de notas, (y, si lo necesitas, una nota de recuperación) tanto por cada instrumento evaluativo 
        como por cada competencia. El programa no lo puede hacer todo por ti... :-)</p>
</div>

<div id="dialogoINDICADOR" title="Valor de cada Indicador">
   <br>
   <label style="font-size: 1.5em;">Introduce modificador del indicador</label>
   <br><br>
   <div style="text-align: left; width: 70%;">
   <input name="notaindicador" style="font-size: 1.2em; text-align: left; text-indent: 2em;" type="radio" id="ni1" value="Sin Calificar"><label for="ni1"><span></span>Sin Calificar</label><br>
   <input name="notaindicador" style="font-size: 1.2em; text-align: left; text-indent: 2em;" type="radio" id="ni2" value="Mal"><label for="ni2"><span></span>Mal</label><br>
   <input name="notaindicador" style="font-size: 1.2em; text-align: left; text-indent: 2em;" type="radio" id="ni3" value="Regular"><label for="ni3"><span></span>Regular</label><br>
   <input name="notaindicador" style="font-size: 1.2em; text-align: left; text-indent: 2em;" type="radio" id="ni4" CHECKED value="Bien"><label for="ni4"><span></span>Bien</label><br>
   <input name="notaindicador" style="font-size: 1.2em; text-align: left; text-indent: 2em;" type="radio" id="ni5" value="Excelente"><label for="ni5"><span></span>Excelente</label><br>
   </div>
</div>

<div id="dialogoCALIFICACIONES" title="Introduce la nota final y/o de recuperación">
   <br>
   <p style="font-size: 1.2em; text-align: left; font-weight: bold">Escribe un punto (.) como separador digital. El alumno/a aprobará con la máxima nota entre la nota definitiva o la nota de recuperación.</p>
   <p id="muestradatosalumno" style="font-size: 1.2em; text-align: left; font-weight: bold"></p>
   <br>
   <label style="font-size: 1.2em; text-align: left;">Nota Definitiva (0-10) </label>
   <input id="innotaseneca" type="text" style="font-size: 1.2em;" maxlength="5" size="5">
   <hr width="80%">
   <label style="font-size: 1.2em; text-align: left;">Nota Recuperación (0-10) </label>
   <input id="innotarecuperacion" type="text" style="font-size: 1.2em;" maxlength="5" size="5"> 
   <!-- los tres siguientes guardan datos pero no se ven -->
   <input id="inidcalificacion" type="text" style="font-size: 1.2em; display: none;" maxlength="5" size="5"> 
   <input id="inidalumno" type="text" style="font-size: 1.2em; display: none;" maxlength="5" size="5"> 
   <input id="innotamedia" type="text" style="font-size: 1.2em; display: none;" maxlength="5" size="5"> 
</div>

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

