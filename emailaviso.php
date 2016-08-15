<?php
include("./funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("./clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

/* $bd="encyclonumber"; */ //debe de haberse cargado en mysql_inc.php

$iz = "left: 120px;" ; // posición de los campos a la izquierda

session_start(); /* empiezo una sesión */

if ($_SESSION['administracion']<2) {
   echo header("Location: ./index.php");
}

if (isset($_SESSION['tutevaluacion']) && strlen($_SESSION['tutevaluacion'])>0 ) {
    $visualizacion=1;
} else {$visualizacion=0;}

// obtiene arrays, por si hay que usarlos más de una vez
$alumno=obteneralumnosasignacion($bd,$_SESSION['asignacion']); // array para introducir datos de los alumnos
$aluii=count($alumno['idalumno']);

// $asignaciones = obtenerasignaciones($bd,$alumno['idalumno'],$_SESSION['tutevaluacion']); // pasa un array con los id de todos los alumnos
$asignaciones = obtenerasignacionesdos($bd,$alumno['idalumno']); // pasa un array con los id de todos los alumnos
// obtiene un array con las distintas asignaciones que ESA EVALUACIÓN, han sido calificadas
$jj=count($asignaciones);

$profesores=array();
foreach ($asignaciones as $valor) {
  $cadena=obtenerdatosasignacion($bd,$valor);
  $profesores[]=$cadena['idprofesor'];
}
$profesores=array_unique($profesores); //comprueba repetidos
sort($profesores); //ordena

?>
<html>
<head>
<title>Aviso a los profesores/as de mi tutoría</title>
<meta http-equiv="Content-Type" content="text/html;">
<script language="javascript" type="text/javascript" src="./tinymce/jscripts/tiny_mce/tiny_mce.js"> </script>
<script language="javascript" type="text/javascript">
tinyMCE.init({
        // General options
        mode : "textareas",
        theme : "advanced",
        language: 'es',
        plugins : "autolink,lists,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

        // Theme options
        theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,forecolor,backcolor,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,cleanup,help,code,|,insertdate,inserttime,preview,",
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
});

</script>
<!-- Inclyo las diferentes hojas de estilo -->
<?php include_once("./listas_de_css.php"); ?>
</head>
<body>
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
    <?php if (strlen($_SESSION['tutorada'])>0 && $visualizacion==1) { 		
	if ($_SESSION['contador']>$jj-1) {$_SESSION['contador']=$jj-1;} //Si supera este valor
	if ($_SESSION['contador']<0) {$_SESSION['contador']=0;} //Si es menor que cero    
    // Averiguar qué le pasa a la variable materia. ¿Por qué no las pone todas cuando avanzo botones?

    ?>
    <a name="ancla"></a>
    <h2 style="text-align: center;">Alumnado de <?php echo $alumno['cadenaclases'] ?></h2>
    <h2 style="text-align: center;"><?php echo 'Evaluación: '.dado_Id($bd,$_SESSION['tutevaluacion'],"nombreeval","tb_edicionevaluaciones","ideval"); ?></h2>  
  
    <form name="emailaviso" action="./envioemaildeaviso.php" method="post"> <!-- Principio del form -->		
                <div id="presentardatos2">
		<?php 
                $j=0;
                $ancho=4;
                if ($jj<$ancho) {$ancho=$jj; } // si hay menos que el ancho entonces que sea ese núnero
		echo '<table id="tabladatos" style="margin:2px auto; height: auto; text-align: center; width: 80%;" border="0" cellpadding="5" cellspacing="5" class="tabla">';
                echo '<th colspan="'.$ancho.'" ><h2 style="text-align: center; font-size: 15px; ">Selecciona los profesores/as a los que quieras enviar el mensaje</h2></th><tr cellpadding="5" cellspacing="5">';
                foreach ($profesores as $valor) {
                        $columna=($j%$ancho);
                        echo '<td onclick="marcarcasilla(\''.$j.'\');" id="td'.$j.'">';
			echo cambiarnombre(dado_Id($bd,$valor,"Empleado","tb_profesores","idprofesor"))." ";
                        // echo '<input id="'.$j.'" type="checkbox" value="'.$cadena['email'].'" onClick="reconocemarcados();">';
                        echo '<input id="'.$j.'" type="checkbox" value="'.$valor.'" onclick="marcarcasilla(\''.$j.'\');">'; //que el valor sea la identificación del profesor
                        echo '</td>';
                        if($columna==$ancho-1) {echo '</tr><tr cellpadding="5" cellspacing="5">'; }  
                        $j++;
                }
                echo '</tr></table>';  
                ?>
    		<br>
		<p style="text-align: center;">
                <a class="a_demo_two" style="color:black;" onclick="clearup();">Deselecciona todos/as</a>
                &nbsp;&nbsp;
                <a class="a_demo_two" style="color:black;" onclick="setup();" >Selecciona todos/as</a>
                </p><br>
		</div>

		<div id="presentardatos2">
                <!-- correos -->
                <input name="emails" id="emails" class="cajones" style="min-width:90%; maxlength: 100;" type="hidden" value="">
                <h2>Asunto (100 caracteres):&nbsp;&nbsp;
                <input name="asunto" id="asunto" style="min-width:27em; maxlength: 100; font-size: 1.2em; background-color: #ccffcc; " type="text" class="cajones" value="">
                </h2>
                <h2 style="margin-left: 0px; text-align: center;">Cuerpo del mensaje (3.000 caracteres)</h2>
                <div style="margin: 10px 5px 1px 10px;">
                <p style="text-align:center;"><textarea name="cuerpo" class="cajones" rows="14" maxlength="3000" alt="3.000 caracteres cómo máximo" style="vertical-align: middle; font-size:1.2em; background-color: #ccffcc;" height="auto" id="cuerpo"></textarea></p></div>
                <br>
                <input id="enviar" name="enviar" class="botonesdos" type="submit" alt="Enviar correo" value="Enviar correo">
                </div>

    </form> <!-- Final del form -->

<?php 
    } else { 
      echo '<h2>No has seleccionado una evaluación previamente o no te has identificado como tutor/a de un curso</h2>';
      echo '<p style="text-align: center;"><a style="padding: 5px 10px;" class="botones" href="./guardardatosinicialestutoria.php">Datos iniciales de tutoría</a><a style="padding: 5px 10px;" class="botones" href="./index.php">Identificarse como tutor/a</a></p>';	
    }// fin del if
    
    ?>
</div>
<br><br>
</body>
</html>

<!-- ****************** -->
<!--       Script       -->
<!-- ****************** -->
<script type="text/javascript" language="javascript">

var valores="";

function myXOR(a,b) {
  // return ( a || b ) && !( a && b );
  return (!a && b) || (a && !b); 	
}

function marcarcasilla(x) {
  // alert(x);
  for (var j=0;j<document.emailaviso.elements.length;j++) {
    if(document.emailaviso.elements[j].id == x) {
       var c = document.emailaviso.elements[j].checked;
       document.emailaviso.elements[j].checked = myXOR(c,1);
    } // fin del if
  } // fin del for
  reconocemarcados();
}

// Necesito una función que ahora reconozca todos los valores marcados y los guarde de forma 
// asíncrona, junto con el valor de observaciones. 
function reconocemarcados() {
   valores=""; 
   for (var j=0;j<document.emailaviso.elements.length;j++) {
        if(document.emailaviso.elements[j].type == "checkbox") {
           if (document.emailaviso.elements[j].checked==1) { 
              // alert(j+" marcado");
              var idcelda="td"+document.emailaviso.elements[j].id;
              // alert(idcelda);
              var celda= document.getElementById(idcelda);
              celda.style.backgroundColor="#88ff88";
              valores=valores+document.emailaviso.elements[j].value+"###";
           } else {
              // alert(j+" no marcado");
              var idcelda="td"+document.emailaviso.elements[j].id;
              // alert(idcelda);
              var celda= document.getElementById(idcelda);
              celda.style.backgroundColor="#ffffff";
           } 
        } // fin del if checkbox
    }
    valores=valores.substring(0,valores.length-3); // recorta los tres últimos datos
    // alert(valores); // envía parte de la cadena a grabar para escribir datos con los items de los alumnos y las observaciones
    document.getElementById("emails").value=valores;	
} // fin de la función de reconocemarcados


function clearup() {
for (j=0;j<document.emailaviso.elements.length;j++) {
       if(document.emailaviso.elements[j].type == "checkbox") {
          if (document.emailaviso.elements[j].checked==1) { 
		document.emailaviso.elements[j].checked=0;
          }
       }
} // fin del for
reconocemarcados(); // llama a reconocemarcados para cambiar su valor
}

function setup() {
for (j=0;j<document.emailaviso.elements.length;j++) {
       if(document.emailaviso.elements[j].type == "checkbox") {
          if (document.emailaviso.elements[j].checked==0) { 
		document.emailaviso.elements[j].checked=1;
          }
       }
} // fin del for
reconocemarcados(); // llama a reconocemarcados para cambiar su valor
}

</script>
<!-- ****************** -->
<!-- Fin de los scripts -->
<!-- ****************** -->
