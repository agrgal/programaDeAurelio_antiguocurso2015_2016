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

?>
<html>
<head>
<title>Listado de alumnos/as de mi asignación</title>
<meta http-equiv="Content-Type" content="Type=text/html; charset=iso-8859-15">

<!-- Incluir el script jquery -->
<script language="javascript" src="./funciones/jquery.js"></script>
<script language="javascript">
  var xx=0; var yy=0;

  $(document).ready(function(){ 
    // muestra la capa de información para acceder al fichero de subir imágenes 
    $("img").click(function(e){ 
       $("#nombrealumno").html($(this).attr('alt'));
       $("#f1").attr("action","./veralumnado.php#"+$(this).attr('id'));
       // oculta el div si estaba abierto
       $("#cargarfoto").hide();
       // lo pone en modo enviar foto
       $("#archivo").show();
       $("#Enviar").val("Enviar");
       $("#borrar").attr('checked', false); // desmarca
       // Calcula la posición donde debe mostrarse
       var arriba = $("#informacion").css("top");
       var arriba2 = $("#cargarfoto").css("height");
          xx = e.pageX; // posición X
          yy = e.pageY-parseInt(arriba)-parseInt(arriba2)/2; // posición Y
       $("#cargarfoto").css("width","500px").css("left","200px").css("top",yy).slideDown("slow");
       $("#idal").val($(this).attr('id'));
       $("#copiadefoto").attr("src",$(this).attr("src")); // en la copia de foto pone la misma fuente que donde hago click
       
    });
    // Selecciono borrar
    $("#borrar").click(function() {
       if ($("#borrar").is(':checked')) {
           // ocultar caja de subida de ficheros
           $("#archivo").hide();
           $("#Enviar").val("Borrar foto existente");
       } else {
           // muestra caja de subida de ficheros
           $("#archivo").show();
           $("#Enviar").val("Enviar");
       }
    });     
  });

   function ocultav() {
      $("#cargarfoto").hide();
   }
</script>
<!-- ======================================== -->

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
    <?php if ($_SESSION['tutorada']==1) { ?>

    <?php 

    $nombrefic =$_FILES["archivo"]["name"];
    $tipo=$_FILES["archivo"]["type"];
    $ruta=$_FILES["archivo"]["tmp_name"];
    $filesize=$_FILES["archivo"]["size"];
    $max=1000000; // Máximo de 1Mb

    // Mensaje de superar el tamaño
    if ($filesize>$max) { 
       echo '<div id="presentardatos2" style="width:400px;">';
       echo "<h2 style='text-align: center;'>El fichero ha superado el tamaño máximo</h2>";
       echo '</div>';
    }

    $extension = strtolower(end(explode(".", $nombrefic)));
    $nombreyruta="./imagenes/fotos/".$_POST["idal"].".";

    if ((isset($_POST["borrar"]) && $_POST["borrar"]=="1") || (!is_null($_POST["idal"]))) {
            // $llega="Llega hasta aquí. ".$nombreyruta.".".$nombrefic;
	    if (file_exists($nombreyruta."gif")) { unlink($nombreyruta."gif"); }
	    if (file_exists($nombreyruta."jpg")) { unlink($nombreyruta."jpg"); }
	    if (file_exists($nombreyruta."png")) { unlink($nombreyruta."png"); }
    } // Borrar el fichero.

    $nombreyruta.=$extension;

    if (is_uploaded_file($ruta) && $filesize<=$max) { // Si hemos cargado un fichero y si es menor que 1Mb

    $imageinfo=getimagesize($_FILES["archivo"]["tmp_name"]); // obtiene tamaño de la foto

    move_uploaded_file($_FILES["archivo"]["tmp_name"], $nombreyruta);

    $ancho=$imageinfo[0];
    $alto=$imageinfo[1];
    $tipo=$imageinfo[2];
     
    if($ancho >= 250) {
        $nancho=250;
        $nalto=$nancho*$alto/$ancho;
    } else {
        $nancho=$ancho;
        $nalto=$alto;
    }
     
    $img_nueva=imagecreatetruecolor($nancho,$nalto);    

    // Si existe la imagen anterior...
         
    switch($extension)
    {
    case "gif":
        $img_actual=imagecreatefromgif($nombreyruta);
        imagecopyresampled($img_nueva,$img_actual,0,0,0,0,$nancho,$nalto,$ancho,$alto);
        imagegif($img_nueva,$nombreyruta);
        break;
    case "jpg":
        $img_actual=imagecreatefromjpeg($nombreyruta);
        imagecopyresampled($img_nueva,$img_actual,0,0,0,0,$nancho,$nalto,$ancho,$alto);
        imagejpeg($img_nueva,$nombreyruta);
        break;
    case "png":
        $img_actual=imagecreatefrompng($nombreyruta);
        imagecopyresampled($img_nueva,$img_actual,0,0,0,0,$nancho,$nalto,$ancho,$alto);
        imagepng($img_nueva,$nombreyruta);
        break;
    } 

    chmod ($nombreyruta, 0777); 

    // Refresco de página
    header("location:".$_SERVER["PHP_SELF"]);  

    } // fin del if de saber si hay imagen 
    ?>


    <?php 
       $infasignacion=obtenerdatosasignacion($bd,$_SESSION['asignacion']);
       $alumnos=obteneralumnosasignacion($bd,$_SESSION['asignacion']); 
    ?>   

    <h1 style="text-align: center;">Alumnado de <?php echo $alumnos['cadenaclases']; ?></h1>
    <p><?php // echo $llega; ?></p>
    <div id="cero" style="position: absolute; border: 0px solid black; top: -15px; right: 17%; width: auto; height:120px; overflow: hidden;">
    <a style="margin: 10px; top: 15px; color: black;" class="a_demo_four" onclick="location.reload(true);">Refrescar</a>
    </div>
    <table class="tabla" style="text-align: left; width: 65%; margin-left: auto; margin-right: auto;" border="1" cellpadding="2" cellspacing="2">
	<caption style="caption-side: bottom;">Proviene de la asignación <?php echo $infasignacion['descripcion']." - ".$infasignacion['materia']." - ".$infasignacion['profesor']; ?> </caption>
	<tbody>
	<tr>
	<th style="width: 10%; font-weight: bold; text-align: center;">Nº:</th>
        <th style="width: 10%; font-weight: bold; text-align: center;">Id:</th>  
	<th style="width: 70%; font-weight: bold; text-align: center;">Nombre del alumno/a</th>
        <th style="width: 10%; font-weight: bold; text-align: center;">Fotografía</th>
	</tr>
    <?php 
    $numalu=count($alumnos["alumno"]); 
    if ($numalu>=1) {
    $ii=1;

    foreach ($alumnos["idalumno"] as $key => $valor) {
       $nombre=cambiarnombre($alumnos["alumno"][$key]);
       $unidad=$alumnos["unidad"][$key];
       $poner=$nombre." [".$unidad."]";
       $fotografia="./imagenes/fotos/".$valor;
       $extension="";
       if (file_exists($fotografia.".jpg")) { $extension=".jpg";}
       if (file_exists($fotografia.".png")) { $extension=".png";}
       if (file_exists($fotografia.".gif")) { $extension=".gif";}
       $fotografia.=$extension; // añado la extensión	
       if ($extension=="") { $fotografia="./imagenes/fotos/boygirl2.png"; }
       $foto='<img id ="'.$valor.'" src="'.$fotografia.'" width="50px" height="auto" title="'.$poner.'" alt="'.$poner.'">';
       echo '<tr><td style="vertical-align: center; width: 168px; text-align: center;">'.$ii.'<a name="'.$valor.'"></a></td><td style="vertical-align: center; width: 168px; text-align: center;">'.$valor.'</td><td style="vertical-align: center; width: 168px; text-align: justify; text-indent:10px;">'.$poner.'</td><td style="vertical-align: center;  text-align: center;">'.$foto.'</td></tr>';
       $ii++;
    }
    } else {
      echo '<p>No hay almacenado ningún dato de curso</p>';

    } // fin del if de numalu

    } // fin del if principal ?>
	</tbody>
	</table>    

<!-- Capa para cargar la foto -->
<div id="cargarfoto" class="presentardatos2" style="display:none; position: absolute; top: 100px; left: 5%; overflow: hidden;" ondblclick="ocultav();">
   <form id="f1" action="./veralumnado.php" method="post" enctype="multipart/form-data">
        <h1 style="text-align:center; margin-left:-10px;">Elige una fotografía o selecciona borrar</h1>
        <p id="nombrealumno" name="nombrealumno" style="text-align:center; padding-left:10px;"></p>
        <input id="idal" name="idal" type="hidden">
        <p style="text-align:center; padding-left:10px;">
        <table style="border: 0px solid black;">
        <tr style="text-align:center;"><td><img id="copiadefoto" src="" width="auto" height="50px"></td>
	<td><input id="borrar" name="borrar" type="checkbox" value="1">Selecciona para borrar la fotografía actual</td></tr>
        </table></p>
        <p><input id="archivo" name="archivo" style="margin-left:-10px; margin-top:-10px;" type="file" class="botones" size="30" maxlength="50"></p>
        <p style="text-align:center;">
            <input id="Enviar" value="Enviar" type="submit">
   	    <input id="Cancelar" value="Cancelar" type="button" onclick="ocultav();">
        </p>
    </form>
</div>
<!-- Fin de la capa que tiene que abrirse para cargar la foto-->
		
</div>
<br><br>
</body>
</html>

