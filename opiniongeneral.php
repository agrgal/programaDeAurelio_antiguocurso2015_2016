<?
include("./funciones/funciones.php"); /* incluye el directorio de funciones */

include_once("./clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

/* $bd="encyclonumber"; */ //debe de haberse cargado en mysql_inc.php

$iz = "left: 120px;" ; // posición de los campos a la izquierda

session_start();

if ($_SESSION['administracion']<1) {
   echo header("Location: ./index.php");
}

// Si es el administrador, para que pueda tener un profesor
// if ($_SESSION['administracion']==3) 
//   {$_SESSION['profesor']=dado_Id($bd,"31667329D","idprofesor","tb_profesores","DNI");} //me pongo yo

// Haber rellenado antes los datos iniciales..
if (isset($_SESSION['evaluacion']) && isset($_SESSION['asignacion']) && isset($_SESSION['profesor'])) { //si se han activado todas las variables de sesión
   $visualizacion=1;
} else { header ("Location: ./guardarasignaciones.php");}

// obtiene arrays, por si hay que usarlos más de una vez
$alumno=obteneralumnosasignacion($bd,$_SESSION['asignacion']); // array para introducir datos de los alumnos
$ii=count($alumno['idalumno']);
$items=obteneritems($bd);

?>
<html>
<head>
<title>Opinión general de un curso</title>
<meta http-equiv="Content-Type" content="Type=text/html; charset=iso-8859-15">
<script language="javascript" type="text/javascript" src="./tinymce/jscripts/tiny_mce/tiny_mce.js"> </script>

<script language="javascript" type="text/javascript">
tinyMCE.init({
        // General options
        mode : "textareas",
        theme : "advanced",
        language: 'es',
        plugins : "autolink,lists,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

        // Theme options
        theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,forecolor,backcolor,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,cleanup,help,code,|,insertdate,inserttime,preview,|,charmap,|,sub,sup,",
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
<div id="grupo" style="position: absolute; left: 0px; top: 0px; width: 960; height: auto; z-index: 1; line-height: 14px"><img name="fondo" src="./imagenes_plantilla/plantilla.gif" width="960" height="auto" border="0" alt=""></div> -->
<div class="grupo"></div>

<!-- Capa de menú: navegación de la página -->
<?php include_once("./lista.php"); ?>

<?php echo'
<script type="text/javascript" language="javascript">
 
var READY_STATE_UNINITIALIZED=0; 
var READY_STATE_LOADING=1; 
var READY_STATE_LOADED=2;
var READY_STATE_INTERACTIVE=3; 
var READY_STATE_COMPLETE=4;
 
var peticion_http;

function cargaContenido(url, metodo, funcion, query) {
  peticion_http = inicializa_xhr(); 
  if(peticion_http) {
    peticion_http.onreadystatechange = funcion;
    peticion_http.open(metodo, url, true);
    peticion_http.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    // alert(query);
    var query_string = "lee="+encodeURIComponent(query);
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
      // document.getElementById("cero").innerHTML = peticion_http.responseText;
      alert(peticion_http.responseText);
    }
  }
}

// *************************
// Listado de funciones
// *************************

function cargaopinion(val) {
  // alert("dos"+val);
  cargaContenido("./scriptsphp/petguardaopinion.php", "POST", muestraContenido, val);
}

// *************************
// Funciones añadidas 
// *************************

</script>'; ?>

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
  <div id="cero"></div>
 <?php 
           $datosasignacion=obtenerdatosasignacion($bd,$_SESSION['asignacion']); 
        ?> 
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

 <?php
   // Busca la opinión general
   $link=Conectarse($bd);
   $Sql='SELECT * FROM tb_opiniongeneral WHERE eval="'.$_SESSION['evaluacion'].'" AND asignacion="'.$_SESSION['asignacion'].'"';
   $result=mysql_query($Sql,$link);
   $ii=0;
   while ($row=mysql_fetch_array($result)) {
      $opinion=$row['opinion'];
      $mejora=$row['mejora'];
      $actuaciones=$row['actuaciones'];
      $id=$row['idopiniongeneral'];
      $ii++;      
   } // fin del while
   mysql_free_result($result); 
   if ($id<=0) { $id=0; }

 ?>

 <form name="editarevaluacion" action="" method="">
 <div id="presentardatos">
    <h2 style="font-size:1.9em; margin:0.2em 0px 0.6em 0px; text-align:center; ">Opinión general del curso</h2>
    <p style="text-align:center;"><textarea name="opinion" id="opinion" width="100%" cols="auto" rows="5"><?php echo $opinion; ?></textarea></p>
 </div>
 <div id="presentardatos">
    <h2 style="font-size:1.9em; margin:0.2em 0px 0.6em 0px; text-align:center; ">Actuaciones llevadas a cabo</h2>
    <p style="text-align:center;"><textarea name="actuaciones" id="actuaciones" width="100%" cols="auto" rows="5"><?php echo $actuaciones; ?></textarea></p>
 </div>
 <div id="presentardatos">
    <h2 style="font-size:1.9em; margin:0.2em 0px 0.6em 0px; text-align:center; ">Propuestas de mejora</h2>
    <p style="text-align:center;"><textarea name="mejora" id="mejora" width="100%" cols="auto" rows="5"><?php echo $mejora; ?></textarea></p>
 </div>
 <input id="eval" name="eval" value="<?php echo $_SESSION['evaluacion'];?>" type="hidden">
 <input id="asignacion" name="asignacion" value="<?php echo $_SESSION['asignacion'];?>" type="hidden">
 <input id="idopiniongeneral" name="idopiniongeneral" value="<?php echo $id;?>" type="hidden">
    
    <div id="botonguardar" style="position: absolute; top:1px; right: 40px;">
    <a href="#" class="a_demo_two" style="color: black; font-size: 1.3em;" onclick="guardardatos();">Guardar datos</a>
    </div>
  </form>

</div>

<!-- ****************** -->
<!--       Script       -->
<!-- ****************** -->
<script type="text/javascript" language="javascript">
function guardardatos() {
 var opinion = tinyMCE.get('opinion').getContent();
 // alert(opinion);
 var actuaciones = tinyMCE.get('actuaciones').getContent();
 // alert(actuaciones);
 var mejora = tinyMCE.get('mejora').getContent();
 // alert(mejora);
 var valores="";
 var evaluacion = document.getElementById("eval").value;
 var asignacion = document.getElementById("asignacion").value;
 var id = document.getElementById("idopiniongeneral").value;
 valores = valores + evaluacion + "#***#";
 valores = valores + asignacion + "#***#";
 valores = valores + opinion + "#***#";
 valores = valores + actuaciones + "#***#";
 valores = valores + mejora+ "#***#";
 valores=valores+id;
 // alert(valores);
 cargaopinion(valores); 
}


</script>
<!-- ****************** -->
<!-- Fin de los scripts -->
<!-- ****************** -->

</body>
</html>
