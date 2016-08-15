<?php
include("./funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("./clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

/* $bd="encyclonumber"; */ //debe de haberse cargado en mysql_inc.php

$iz = "left: 120px;" ; // posición de los campos a la izquierda

session_start(); /* empiezo una sesión */

?>
<html>
<head>
<title>Edita lista de competencias</title>
<meta http-equiv="Content-Type" content="Type=text/html; charset=iso-8859-15">

<!-- Incluyo las diferentes hojas de estilo -->
<style type="text/css">
	.flexigrid div.fbutton .add {
		background: url(imagenes/otros/annadir.png) no-repeat center left;
                background-size: 15px 15px;
	}

	.flexigrid div.fbutton .delete {
	    background: url(imagenes/otros/papelera.png) no-repeat center left;
            background-size: 15px 15px;
	}

	.flexigrid div.fbutton .edit {
		background: url(imagenes/otros/editar.png) no-repeat center left;
                background-size: 15px 15px;
	}

        .flexigrid div.fbutton .save {
		background: url(imagenes/otros/guardar.png) no-repeat center left;
                background-size: 15px 15px;
	}

        .flexigrid div.fbutton .download {
		background: url(imagenes/otros/menos.png) no-repeat center left;
                background-size: 15px 15px;
	}

	.flexigrid div.fbutton .subir {
		background: url(imagenes/otros/subir.png) no-repeat center left;
                background-size: 15px 15px;
	}
</style>

<?php include_once("./listas_de_css.php"); ?>
<link rel="stylesheet" type="text/css" href="css/flexigrid/flexigrid.pack.css" />
<link rel="stylesheet" type="text/css" href="css/uploadify/uploadify.css" />

<!-- ************************ -->
<!-- Incluir el script jquery -->
<!-- ************************ -->
<script language="javascript" src="./funciones/jquery-1.9.1.js"></script>
<script src="./funciones/jquery-ui-1.10.2.custom.js"></script>
<script src="./funciones/ui.datepicker-es.js"></script>
<script src="./funciones/flexigrid.js"></script>
<!-- Incluir los scripts de uploader -->
<script type="text/javascript" src="./funciones/jquery.uploadify.min.js"></script>
<script type="text/javascript" src="./funciones/jquery.uploadify.js"></script>
<script type="text/javascript" src="./funciones/swfobject.js"></script>

<script language="javascript">

  $(document).ready(function(){ // principio del document ready  

    // ============================================================== 
    // A) Ocultar el div donde se editan / añaden competencias 
    // ============================================================== 
    $('#ocul').click(function(){
      $('#listacompetencia').hide("fast");
    });

    // ============================================================== 
    // B) Función de subida de fichero
    // ============================================================== 
    $('#file_upload').uploadify({
		// 'buttonClass' : 'cajones',
                // 'buttonImage' : './imagenes/otros/subir2.png',
                'buttonText': 'Sube fichero',
                'progressData' : 'speed',
                // 'uploader'  : './ficheros/uploadify.swf',
                'swf'  : './ficheros/uploadify.swf',
		'uploader'    : './listacompetencias/uploader.php',
		'cancelImg' : './imagenes/otros/cancel.png',
		'auto'      : true,
		'formData':  {'folder': '/ficheros'},
		// 'scriptData' : {'texto': $("#mitexto").val()},
		'onUploadSuccess' : function(file, data, response) {
                    // alert(data);	
 		    $("#fotosWrapper").append(data);
                    $("#subirfichero").delay(2500).slideUp(0,function() {
	                 location.reload(); // al final hay que hacer ésto
                     }); // Lo hace desaparecer a los 2.5 segundos
		}
                // Ver la documentación en: http://www.uploadify.com/documentation/
    });

    // ============================================================== 
    // C) Definición del flexigrid
    // ============================================================== 
    $("#datos").flexigrid({
                url : 'listacompetencias/mostrarlistacompetenciasxml.php', // llama al script php
                dataType : 'xml', // codificacion
                colModel : [  // modelo de columnas de datos
                    {display : 'ID',  name : 'idcompetencia', width:40, sortable :true, 
                    align : 'center', process: procMe
                    }, 
                    {display : 'Nombre', name : 'nombre',  width:650, sortable :true, align : 'left', process: procMe }, 
                    {display : 'Abreviatura', name : 'abreviatura',  sortable :true, width:130, align : 'center', process: procMe }, 
                    ],
                buttons : [ {name : 'Añadir', bclass : 'add', onpress : test }, 
                            {name : 'Borrar', bclass : 'delete', onpress : test}, 
                            {name : 'Editar', bclass : 'edit', onpress : test},
			    {name : 'Guardar fichero en una carpeta', bclass : 'save', onpress : guardar},
                            // {name : 'Descargar', bclass : 'download', onpress : descargar},
			    {name : 'Subir fichero al servidor', bclass : 'subir', onpress : subir},
                          ],
                searchitems : [ {
                    display : 'ID',
                    name : 'idcompetencia',
                    }, {
                        display : 'Nombre',
                        name : 'nombre',
                        isdefault : true
                    }, {
                        display : 'Abreviatura',
                        name : 'abreviatura',
                    } ],
                sortname : 'idcompetencia',
                sortorder : 'asc',
                usepager : true,
                title : 'Listado de competencias',
                useRp : true,
                rp : 10,
                showTableToggleBtn : false,
                resizable: true,
                singleSelect: true,
                width : 'auto',
                height : 'auto'
            }); 

            function procMe( celDiv, ident ) {
                $(celDiv).click( function() {
                  parentDIV = $(celDiv).closest('tr');
		  var id = $("td[abbr='idcompetencia']", parentDIV).text(); // Retrieve the id content
                  var nom1 = $("td[abbr='nombre']", parentDIV).text();
                  var abr1 = $("td[abbr='abreviatura']", parentDIV).text();
                  $('#nombre').val(nom1);
                  $('#abreviatura').val(abr1);
                  $("#insertareditar").html("Guardar competencia");
                  $("#insertareditar").attr("onClick","insertar('"+id+"');");
                  $('#listacompetencia').slideDown();
                });
            }   

            function test(com, grid) {
                if (com == 'Borrar') {
		    parentTR = $('.trSelected', grid).closest('tr'); // Get the parent row
                    var id = $("td[abbr='idcompetencia']", parentTR).text(); // Retrieve the id content
                    var textoanotacion = $("td[abbr='nombre']", parentTR).text();
                    var abreviatura = $("td[abbr='abreviatura']", parentTR).text();
                    borrar(id,textoanotacion+" ("+abreviatura+")");
                    // confirm('Delete ' + $('.trSelected', grid).closest('tr') + ' items?')
                } else if (com == 'Añadir') {
                    $('#nombre').val("");
                    $('#abreviatura').val("");
                    $("#insertareditar").html("Insertar Nueva competencia");
                    $("#insertareditar").attr("onClick","insertar('');");
                    $('#listacompetencia').slideToggle("slow");
                } else if (com == 'Editar') {
		    parentTR = $('.trSelected', grid).closest('tr'); // Get the parent row
                    var id = $("td[abbr='idcompetencia']", parentTR).text(); // Retrieve the id content
                    var nom1 = $("td[abbr='nombre']", parentTR).text();
                    var abr1 = $("td[abbr='abreviatura']", parentTR).text();
                    // alert(id);
                    $('#nombre').val(nom1);
                    $('#abreviatura').val(abr1);
                    // cambiar el onclick del botón      
                    $("#insertareditar").html("Guardar competencia");
                    $("#insertareditar").attr("onClick","insertar('"+id+"');");
                    $('#listacompetencia').slideToggle("slow");
                }
             } // fin de la función test

             function guardar(com,grid) {     
                   var posting = $.post( "./listacompetencias/guardarfichero.php", { 
                   });
                   posting.done(function(data,status){
                         if (status=="success") {
				window.location.href = './ficheros/listacompetencias.csv';
                         }
                   }); 
             } // fin de la función guardar


             function subir(com,grid) {
                   $("#fotosWrapper").html("");
	           $('#listacompetencia').hide("fast"); // oculta si acaso la edición
                   $("#subirfichero").show("slow");
                   // alert("subir");
             }

  }); // fin de document ready


  // ================================================================== 
  // Función insertar: Pasa datos para insertar o modificar un dato
  // ================================================================== 
    function insertar(identificacion) { 
         // alert("llego");
         var nombre = $('#nombre').val();
         var abreviatura = $('#abreviatura').val();
         // alert (fecha +" " +anot+" - "+asignacion+ " - " + alumno +" id: "+identificacion);
         var posting = $.post( "./listacompetencias/insertalistacompetencias.php", { 
             nombre: nombre,
             abreviatura: abreviatura,
             id: identificacion,
         });
         posting.done(function(data,status){
            alert("El programa responde: "+data);
            // Haga lo que haga, muestra los datos correctos de los tabs...
            // cambiar el onclick del botón      
	    location.reload(); // al final hay que hacer ésto
         }); 
    } // Insertar un dato

    // ======================================================= 
    // D) Al pulsar sobre la papelera, borra el dato.
    // ======================================================= 
    function borrar(idn,textoanotacion) {
         // alert(idn);
         if (confirm("¿Estas seguro de borrar el dato "+idn+": '"+textoanotacion+"'?")) {
	 var posting = $.post( "./listacompetencias/borrarlistacompetencias.php", { 
             id: idn,
         });
         posting.done(function(data,status){
                // alert (status);
                if (status=="success") {
                   location.reload(); // al final hay que hacer ésto
                } else {
                   alert("El procedimiento ha fallado");
                }
         });
         } // fin de la confirmación
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

  <!-- <div id="enlace" style="display: auto";>
     <a id="pulsaenlace" href="./ficheros/listacompetencias.csv">Pulsa aquí</a>
  </div> -->
  
  <div style="width: 90%; margin: 2px auto;">
      <table id="datos" class="tabla">
      </table>
  </div>

  <div id="listacompetencia" class="presentardatos2" style="text-align: center; overflow: auto; width: 75%; display: none;">
  <div id="cero" style="position: absolute; border: 0px solid black; top: 20px; left: 10%; width: auto; height:60px; overflow: hidden;">
  <a id="insertareditar" style="margin: 10px; top: 10px; color: black;" class="a_demo_four" onclick="insertar('');">Insertar Nueva evaluación</a></div>
  <div id="cero" style="position: absolute; border: 0px solid black; top: 20px; right: 15%; width: auto; height:60px; overflow: hidden;">
  <a id="ocul" style="margin: 10px; top: 10px; color: black;" class="a_demo_four" onclick="ocultar();">Ocultar</a>
  </div> 
   <table style="width: 100%; "><tr style="vertical-align:middle; height: 60px;">
   <tr style="vertical-align:middle;">
     <td style="width: 100%; border: 0px solid black; text-align: left;">
     <b>Introduce la nueva competencia:</b>
     <input id="nombre" class="botones" type="text" style="text-align: left;" size="38" maxlength="250">
   </td>
   </tr>
   <tr style="vertical-align:middle;">
     <td style="width: 100%; border: 0px solid black; text-align: left;">
     <b>Y su abreviatura:</b>
     <input id="abreviatura" class="botones" type="text" style="text-align: left;" size="5" maxlength="3">
   </td>
   </tr>
   </table>
  </div>

  <!-- ===================== -->
  <!-- Para subir un fichero -->
  <!-- ===================== -->
  <div id="subirfichero" class="presentardatos2" style="text-align: center; overflow: auto; width: 75%; display: none;">
  <h2 style="text-align: center;">Para subir un fichero, pulsa el botón de carga
  <input id="file_upload" name="file_upload" type="file" multiple="true"></h2>
  <!-- <input type="text" size="25" name="mensaje" id="mitexto" /> -->
  <div id="fotosWrapper">El sistema responde: </div>
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

