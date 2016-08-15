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
if (isset($_SESSION['asignacion']) && isset($_SESSION['profesor'])) { // No es necesaria la evaluación
// if (isset($_SESSION['evaluacion']) && isset($_SESSION['asignacion']) && isset($_SESSION['profesor'])) { //si se han activado todas las variables de sesión
   $visualizacion=1;
} else { header ("Location: ./guardarasignaciones.php");}

// obtiene arrays, por si hay que usarlos más de una vez
$infasignacion=obtenerdatosasignacion($bd,$_SESSION['asignacion']);
$alumnos=obteneralumnosasignacion($bd,$_SESSION['asignacion']); 
$ii=count($alumnos['idalumno']);
// $items=obteneritems($bd);

if (!isset($_SESSION['contador'])) {$_SESSION['contador']=0;} 
if (isset($_POST['contador'])) {$_SESSION['contador']=$_POST['contador'];}

// Ahora sí, reconocimiento de botones
if (isset($_SESSION['contador']) && $_SESSION['contador']<0) {$_SESSION['contador']=0;}
if (isset($_SESSION['contador']) && $_SESSION['contador']>($ii-1)) {$_SESSION['contador']=($ii-1);}
// El último es la cuenta menos 1, ya que empieza en cero.

?>
<html>
<head>
<title>Anotaciones sobre los alumnos/a de mi asignación</title>
<meta http-equiv="Content-Type" content="Type=text/html; charset=iso-8859-15">

<!-- Incluir el script jquery -->
<script language="javascript" src="./funciones/jquery-1.9.1.js"></script>
<script src="./funciones/jquery-ui-1.10.2.custom.js"></script>
<script src="./funciones/ui.datepicker-es.js"></script>

<script language="javascript">

  $(document).ready(function(){ // principio del document ready  

    var xx=0; var yy=0; 
    
    // ============================================= 
    // A) Recorrer los distintos valores de alumnado
    // =============================================   
    $("#primero").click(function(e){ //pulso en PRIMERO
       $("#contador").val("0");
       $('#fanotacion').submit();
    });
    $("#atras").click(function(e){ // pulso en ANTERIOR
       var valor = $("#contador").val();
       valor = parseInt(valor)-1;
       $("#contador").val(valor);
       $('#fanotacion').submit();
    });
    $("#siguiente").click(function(e){ // pulso en SIGUIENTE
       var valor = $("#contador").val();
       valor = parseInt(valor)+1;
       $("#contador").val(valor);
       $('#fanotacion').submit();
    });
    $("#ultimo").click(function(e){ // pulso en ÚLTIMO
       $("#contador").val("1000");
       $('#fanotacion').submit();
    });

    // ======================================================= 
    // B) Al cargar la página, recarga la lista de anotaciones
    // ======================================================= 
    $(window).load(function() {
      muestraanotaciones();
    });

    // ======================================================= 
    // C) Al pulsar sobre la tabla, detecta si lo hemos hecho
    //    sobre la papelera o sobre editar. Hay que hacerlo así porque, 
    //    al generar la tabla dinámicamente, no detecta img como objeto ¿¿??
    // ======================================================= 
    $("#tabladatos").click(function(e){ 
        var parentTR = $(e.target).closest('tr'); // Get the parent row
        var textoanotacion = $("td[class='textoanotacion']", parentTR).html(); // Retrieve the id content
        var textoanotacion2 = $("td[class='textoanotacion']", parentTR).text(); // Retrieve the id content
        // var textoanotacion = $("td[class='textoanotacion']", parentTR).css("alt"); // Retrieve the id content
        var textofecha = $("td[class='textofecha']", parentTR).html(); // Retrieve the id content
        /* // var columna = parentTR 
        var columna = $('td', parentTR).index(e.target);
        // var columna = $(this).parent().children().index($(this)); 
        alert(id + " -- " + columna); */
        var identificacion = $(e.target).attr("id");
        var nombre = $(e.target).attr("name");
        if (nombre=="papelera") { borrar(identificacion,textoanotacion2); }
        if (nombre=="editar") { editar(identificacion,textofecha,textoanotacion); }
    });

    // ======================================================= 
    // D) Al pulsar sobre la papelera, borra el dato.
    // ======================================================= 
    function borrar(idn,textoanotacion) {
         // alert(idn);
         if (confirm("¿Estas seguro de borrar el dato "+idn+": '"+textoanotacion+"'?")) {
	 var posting = $.post( "./anotaciones/borraranotaciones.php", { 
             id: idn,
         });
         posting.done(function(data,status){
                // alert (status);
                if (status=="success") {
                   muestraanotaciones(); // vuelve a mostrar la tabla
                } else {
                   alert("El procedimiento ha fallado");
                }
         });
         } // fin de la confirmación
     }

    // ======================================================= 
    // E) Al pulsar sobre editar, prepara la edición del dato.
    // ======================================================= 
    function editar(idn,fecha,textoanotacion) {
         // alert(idn+"  "+fecha+"  "+textoanotacion);
         // valores en la edición
         $('#fechados').val(fecha);
         tinyMCE.get("textanotacion").setContent(textoanotacion);
         // cambiar el onclick del botón      
            $("#insertareditar").html("Guardar anotación");
            $("#insertareditar").attr("onClick","insertar('"+idn+"');");
            $("#atab1").html('&nbsp;Editar la asignación "'+idn+'"');
         // Muestra lo que pone 
            $("#atab1").click(); // simulo el click en el tab
    }

    // ======================================================================== 
    // F) Si pulso sobre mostrar datos, inicializo la edición como introducción
    // ========================================================================
    $("#atab2").click(function() {
      // alert("He de inicializar la pestaña de edición o introducción de datos");
      $("#insertareditar").html("Nueva anotación");
      $("#insertareditar").attr("onClick","insertar('');");
      $("#atab1").html('&nbsp;Insertar anotación');
      $('#fechados').val("");
      tinyMCE.get("textanotacion").setContent("");
    });    

    $("#atab1").click(function() {
        var fechaini = $('#fechados').val();
        if (fechaini=="") {
	   $("#fechados").datepicker('setDate', new Date());
        }
    });

    // ======================================================= 
    // G) On focus, aparece también el data picker
    // ======================================================= 
    $('input').filter('.datepick').mouseover(function(){
	$(this).datepicker("show");
    });

    $('#fechaini,#fechafinal').change(function(){
	muestraanotaciones();
    });

    $('#fechaini,#fechafinal').keyup(function(event) {
        // alert(event.which);
        if (event.which == 46 || event.which == 8 || event.which == 27) { // esc, suprimir o borrar atrás.
            $(this).val("");
            muestraanotaciones(); 
        }
    });
    // ========================================================== 
    // H) Al cambiar el alumno en el select, aparece en la página
    // ========================================================== 
    // 
    $("#alumnado2").click(function(){ // Pulso sobre el nombre y aparece la lista de ellos
        $("#seleccion").val($("#contador").val()); // el valor de la seleccion es el del contador
        $("#nombresalumnos").slideToggle("slow");
    });

    $("#seleccion").change(function(){ // al cambiar el select de nombres de alumnado
        $("#contador").val(($("#seleccion").val())); // valor del contador, el de la seleccion
        $('#fanotacion').submit(); // recarga la página
    });

    // ============================================================== 
    // I) Llamo a jquery ui para poner el div tabs como de pestañas
    // ============================================================== 
    $("#tabs").tabs();

    // ============================================================== 
    // J) Define el calendario en el imput de fecha
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

// ============================================================== 
// Acaba el document ready
// ============================================================== 

    }); // fin del document ready
 
// ================================================================== 
// Función comprobar: llamada por el textarea para cambiar caracteres
// ================================================================== 

    function comprobar() {
      // Sin nada. Sólo para que cuente las palabras y los caracteres.
    }

// ================================================================== 
// Función recargar: llamada por el textarea para cambiar caracteres
// ================================================================== 

    function recargar() {
      $('#fanotacion').submit(); 
    }
    
// ================================================================== 
// Función insertar: Pasa datos para insertar o modificar un dato
// ================================================================== 
    function insertar(identificacion) { 
         var fecha =  $('#fechados').val();
         var anot = tinyMCE.get("textanotacion").getContent();
         var asignacion = $('#asignacion').val();
	 var alumno = $('#alumno').val();
         // alert (fecha +" " +anot+" - "+asignacion+ " - " + alumno +" id: "+identificacion);
         var posting = $.post( "./anotaciones/insertaanotacion.php", { 
             fecha: fecha,
             anotacion: anot,
             asignacion: asignacion,
             alumno: alumno,
             id: identificacion,
         });
         posting.done(function(data,status){
            alert("El programa responde: "+data);
            // Haga lo que haga, muestra los datos correctos de los tabs...
            // cambiar el onclick del botón      
	    $('#fanotacion').submit(); // al final hay que hacer ésto
          });         
    } // Insertar un dato

// ================================================================== 
// Función mostrar: Genera la tabla con los datos...
// ================================================================== 
    function muestraanotaciones() {       
         var fecha1 =  $('#fechaini').val();
	 var fecha2 =  $('#fechafinal').val();
         var asignacion = $('#asignacion').val();
	 var alumno = $('#alumno').val();
      var posting = $.post( "./anotaciones/mostraranotaciones.php", { 
             fecha1: fecha1,
             fecha2: fecha2,
             asignacion: asignacion,
             alumno: alumno,
         });
      posting.done(function(data,status){
         // alert (data);
         var datos = $.parseJSON(data); // en la variable datos paso los datos tipo JSON
         // alert (datos);
         if (data.length>2) {
  		 $("#tabladatos").find("tr:gt(0)").remove(); // Borra todo menos la primera fila.
                 $("#tabladatos").show();
		 for (var i in datos) {
		     // alert(datos[i].fecha);
                     var introducir = '<tr><td class="id" style="text-align: center;">' + i +
		      '</td><td class="textofecha" style="text-align: center;">' + datos[i].fecha +
		      '</td><td class="textoanotacion" style="text-align: left; padding: 0.5em 0.2em;">' + decodificar(datos[i].anotacion) + 
		      // '</td><td class="textoanotacion" alt="'+datos[i].anotacion+'" style="text-align: left; padding: 0.5em 0.5em;">' + datos[i].anotacion2 + 
                      '</td><td style="text-align: center;"><img name="papelera" id="'+i+'" src="./imagenes/otros/papelera.png" width="50%" height="auto">' +
                      '</td><td style="text-align: center;"><img name="editar" id="'+i+'" src="./imagenes/otros/editar.png" width="50%" height="auto">' +
		      '</td></tr>';
		     $("#tabladatos").append(introducir);
		 } // fin del for  

         } else {
                 $("#tabladatos").find("tr:gt(0)").remove(); // Borra todo menos la primera fila.
                 $("#tabladatos").show();
	         $("#tabladatos").append('<tr><td colspan="5"><h2 style="text-align: center;">No hay datos</h2></td></tr>');
         } // fin del if
      });
    } 
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

<!-- Inclyo las diferentes hojas de estilo -->
<?php include_once("./listas_de_css.php"); ?>

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

<!-- Capa de información -->
<div id="informacion" onmouseover="javascript: ocultar()">

    <!-- 1º) Información general de la asignación-->
    <p><span style="color: #1111FF; font-weight:blod;">Profesor: </span><?php echo cambiarnombre($infasignacion["profesor"]); ?>
        -
        <span style="color: #1111FF; font-weight:blod;">Materia: </span><?php echo $infasignacion["materia"].' '.$infasignacion["tutorada"];?></p>
        <p><span style="color: #1111FF; font-weight:blod;">Descripción: </span><?php echo $infasignacion["descripcion"]; ?>
        -
        <span style="color: #1111FF; font-weight:blod;">Clases: </span>
        <?php echo $alumnos['cadenaclases']; ?></p>
    <!-- ================================================================== -->
    <!-- <p><?php echo "contador: ".$_SESSION['contador']; ?></p> -->

    <? incluyefoto($alumnos['idalumno'][$_SESSION['contador']],"-20px","8%","absolute",""); ?>
   
    <form id="fanotacion" action="./anotaciones.php" method="post">
  
    <!-- 2º) Información general de la asignación -->
    <div id="alumnado" class="presentardatos" style="overflow: auto; width: 80%;">
    <table width="100%"><tr style="vertical-align:middle;">
     <td width="5%" style="border: 0px solid black;"><img id="primero" src="./imagenes/otros/go_first.png" width="30px"></td>
     <td width="5%" style="border: 0px solid black;"><img id="atras" src="./imagenes/otros/back.png" width="30px"></td>
     <td width="80%" style="vertical-align:middle; border: 0px solid black; font-weight: bold; font-size: 1.4em;">
     <?php echo "<div id='alumnado2'>Alumno/a: ".cambiarnombre($alumnos['alumno'][$_SESSION['contador']]).'</div>'; ?>
     </td>
     <td width="5%" style="border: 0px solid black;"><img id="siguiente" src="./imagenes/otros/next.png" width="30px"></td>
     <td width="5%" style="border: 0px solid black;"><img id="ultimo" src="./imagenes/otros/go_last.png" width="30px"></td>
     </tr></table>        
    </div>
    
    <!-- div que muestra el nombre de los alumnos-->
    <!-- <div class="presentardatos" id="nombresalumnos" name="nombresalumnos" style="display: none; position: absolute;  z-index: 18; width: 50%; top: 10px; left: 220px;"> -->
    <div id="nombresalumnos" name="nombresalumnos" class="presentardatos" style="overflow: auto; width: 60%; top: -22px; z-index: 1; display:none;">
 <select id="seleccion" name="seleccion" class="cajones" style="font-size: 1.8em;" >
   <?php // Lista de opciones
    foreach ($alumnos['alumno'] as $key => $valor) {
      echo '<option value="'.$key.'">'.cambiarnombre($valor).'</option>';
    }
   ?>
   </select>
   </div>

    <input id="contador" name="contador" type="hidden" value="<?php echo $_SESSION['contador']; ?>">
    <input id="alumno" name="alumno" type="hidden" value="<?php echo $alumnos['idalumno'][$_SESSION['contador']]; ?>">
    <input id="asignacion" name="asignacion" type="hidden" value="<?php echo $_SESSION['asignacion']; ?>">
    <!-- <input id="envio" name="envio" type="submit" value="Enviar"> -->
    <!-- ================================================================== -->   
    </form>	

<!-- ================================================================== -->
<!-- ================================================================== -->

    <!-- DIV TABs -->
    <div id="tabs" class="presentardatos" style="width: 95%; overflow: auto; margin: 1em auto;">
        <!-- Pestañas de las TABS TABs -->
	<ul>    
		<li><a id="atab2" href="#tabs-2" style="color: black;" onclick="muestraanotaciones();" >&nbsp;Modifica anotaciones existentes</a></li>
		<li><a id="atab1" href="#tabs-1" style="color: black;">&nbsp;Nueva Anotación</a></li>		
	</ul>
        <!-- Contenido de los TABS -->
	<div id="tabs-1"> <!-- Primer TAB -->
           <!-- 3º) Realiza una anotación -->
           <div id="anotacion" class="presentardatos2" style="text-align: center; overflow: auto; width: 95%;">
           <div id="cero" style="position: absolute; border: 0px solid black; top: 10px; right: 5%; width: auto; height:60px; overflow: hidden;">
           <a id="insertareditar" style="margin: 10px; top: 15px; color: black;" class="a_demo_four" onclick="insertar('');">Insertar Anotación</a>
           </div>  
           <table style="width: 100%; "><tr style="vertical-align:middle; height: 60px;">
           <td style="width: 100%; border: 0px solid black; text-align: left ;">
             <b>Introduce fecha:</b>
             <input id="fechados" name="fechados" class="datepick" type="text" readonly="false" value="" style="width: 8em; height: 1.8em; right: 16%; top: 14%; text-align: center; vertical-align:middle;">
             </tr><tr style="vertical-align:middle;">
             <td style="width: 100%; border: 0px solid black; text-align: center;">
             <textarea name="textanotacion" class="cajones" maxlength="3000" alt="3.000 caracteres cómo máximo" vertical-align: middle; font-size:14px; margin: auto 1em;" height="auto" id="textanotacion" onchange="comprobar();"></textarea>
             </td>
           </tr></table>
          </div>
           <!-- ==== FIN del 3er punto y primer TAB === -->
        </div> <!-- ==== FIN del primer TAB === -->
	<div id="tabs-2">
        
        <!-- Para poner fecha inicial y final, de filtro -->
        <table id="filtrotabladatos" style="display: auto; margin:2px auto; height: auto; text-align: center; width: 95%;" border="0" cellpadding="1" cellspacing="1">
        <tr style="vertical-align:middle; height: 60px;"><td>
        <b>Fecha inicial:&nbsp;</b>
        <input class="datepick" id="fechaini" name="fechaini" type="text" value="" readonly="false" style="width: 8em; height: 1.8em; right: 16%; top: 14%; text-align: center; vertical-align:middle;"></td>
        <td>
        <b>Fecha Final:&nbsp;</b>
        <input id="fechafinal"  name="fechafinal" class="datepick" readonly="false" type="text" value="" style="width: 8em; height: 1.8em; right: 16%; top: 14%; text-align: center; vertical-align:middle;">
        </td><td>
        <!-- <input id="recargar" type="button" value="Recargar" onclick="recargar();"> -->
        <a id="recargar" style="margin: 0px; top: 0px; color: black;" class="a_demo_four" onclick="recargar('');">Recargar</a>
        </td></tr>
	</table>
	<br>

        <!-- Tabla de los datos en sí... -->
          <table id="tabladatos" style="display: none; margin:2px auto; height: auto; text-align: center; width: 95%;" border="1" cellpadding="1" cellspacing="1" class="tabla">
      <tr><th style="width: 5%; font-weight: bold; text-align: center;">N-Id</th><th style="width: 15%; font-weight: bold; text-align: center;">Fecha</th><th style="width: 65%; font-weight: bold; text-align: center;">Anotación</th><th style="width: 7%; font-weight: bold; text-align: center;">Borrar</th><th style="width: 7%; font-weight: bold; text-align: center;">Editar</th></tr>
           </table>
        </div>
    </div>

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

