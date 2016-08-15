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

    // $("#porcentaje").numeric("."); // con jquery.numeric.js los convierte en cajas de texto numérico

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
		// muestrainstrumentosevaluativos();
          }
    });

    //C) Al pulsar en la imagen SIGUIENTE, selecciona un valor posterior del combo LISTAEVALUACIONES
    $("#siguiente").click(function(e){ // pulso en ANTERIOR
	  var Element = $("#listaevaluaciones > option:selected").next('option').val(); // valor anterior
          if (Element>=0) {
          	$("#listaevaluaciones").val(Element);
		$("#listaevaluacion").val(Element);
		// muestrainstrumentosevaluativos();
          }
    });

    // E) Al cargar la página, recarga la lista de anotaciones
    $(window).load(function() {
      var evaluar = $("#listaevaluacion").val(); //variable de sesión. Se define en mostrarnotasinstrumentosevaluativos. Se pone en el campo listaevaluacion
      $("#listaevaluaciones").val(evaluar); // cambia en función de eso el SELECT
      // muestrainstrumentosevaluativos(); // recarga los datos.
      // $("#copiarh3").unbind('click'); // No se puede hacer click en él. Sólo con el botón guardar.
    });

    // F) Al cambiar el SELECT listaevaluaciones, cambia la tabla...
    $("#listaevaluaciones").change(function() {
       $("#listaevaluacion").val($("#listaevaluaciones").val());
       // muestrainstrumentosevaluativos();      
    });

    // ******************************************************************
    }); // Fin del document ready
    // ******************************************************************

    
// ******************************************************************
// *** Funciones fuera del document ready
// ******************************************************************


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
	 <h3 class="insertarh3">Presentación de los conceptos evaluativos de esta evaluación</h3>
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
        <!-- <a id="copiadatos" style="margin: 10px; top: 15px; color: black;" class="a_demo_two" onclick="copiadatos();">Copia datos a esta evaluación</a>
        <a id="muevedatos" style="margin: 10px; top: 15px; color: black;" class="a_demo_two" onclick="muevedatos();">Mueve datos a esta evaluación</a> -->
	</div>

	</div>
<!-- ************************ -->
<!-- Guardar en un fichero    -->
<!-- ************************ -->
	<h3 class="insertarh3">Guarda los instrumentos evaluativos en un fichero</h3>
	<div id="guardar" style="border: 0px solid red; position: relative;">
	    <div id="cero3" style="position: relative; border: 0px solid black; top: 5px; width: auto; height:50px; overflow: hidden;">
                <!--  <a id="guardar" style="margin: 10px; top: 15px; color: black;" class="a_demo_two" onclick="guardar();">Guarda copia en CSV de los datos</a> -->
             </div>
	</div>
<!-- ******************************* -->
<!-- Recupera datos desde fichero    -->
<!-- ******************************* -->
	<h3 class="insertarh3">Recupera datos desde fichero</h3>
	<div id="recuperar" style="border: 0px solid red; position: relative; overflow: hidden;">
	  <!-- <h2 style="text-align: center;">Para subir un fichero, pulsa el botón de carga</h2>
	  <table style="width: auto; border: 0px solid black; margin: 1px auto;"><tr><td>
	  <input id="file_upload" name="file_upload" type="file" multiple="true">
	  </td></tr></table>
	  <!-- <input type="text" size="25" name="mensaje" id="mitexto" /> 
	  <div id="fotosWrapper">El sistema responde: </div> -->
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

