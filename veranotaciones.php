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
<title>Anotaciones sobre los alumnos/a de mi tutoría</title>
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
      muestrajuegoasignaciones(); // al cargar las opciones para esas asignaciones
      muestraanotaciones();
    });

    // ======================================================= 
    // C) Al cambiar el select muestro otra vez los datos
    // ======================================================= 
    $("#select2").change(function() {
      muestraanotaciones();
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
// Función recargar: llamada por el textarea para cambiar caracteres
// ================================================================== 
    function recargar() {
      $('#fanotacion').submit(); 
    }
    
// ================================================================== 
// Función mostrar: Genera la tabla con los datos...
// ================================================================== 
    function muestraanotaciones() { 
         var fecha1 =  $('#fechaini').val();
	 var fecha2 =  $('#fechafinal').val();
         var asignacion = $('#select2').val();
	 var alumno = $('#alumno').val();
      var posting = $.post( "./anotaciones/mostraranotacionesver.php", { 
             fecha1: fecha1,
             fecha2: fecha2,
             asignacion: asignacion,
             alumno: alumno,
         });
      posting.done(function(data,status){
         // alert (data);
         var datos = $.parseJSON(data); // en la variable datos paso los datos tipo JSON
         // alert (datos);
   	 $("#tabladatos").html(""); // Borra todo
         $("#tabladatos").show();        
         if (data.length>2) {
                 var introducir ="";
		 for (var i in datos) {
                     $("#sql").val(datos[i].sql[0]); // pone en el input el valor del SQL
                     introducir = introducir + '<div class="presentardatos2"><h2>'+datos[i].asignacion[0]+'</h2>'+
                     '<h2>'+datos[i].descripcion[0]+'</h2>';
                     for (var j in datos[i].idanotacion) {
                          introducir = introducir +'<div class="presentardatos"><p>'+ 
                          '<span style="color: #088A08;">Anotación nº</span> '+datos[i].idanotacion[j]+
                          '. <span style="color: #088A08;">Fecha: </span>'+                   
                          datos[i].fecha[j]+
                          "</p><div style='width: 90%; margin: 20px auto; border: 0px solid black;'>"+
			  decodificar(datos[i].anotacion[j]) +'</div></div>';
                     }
                     introducir = introducir +'</div><br>'; // fin de lo que se muestra
		 } // fin del for  
  		 $("#tabladatos").html(""); // Borra todo
                 $("#tabladatos").append(introducir);

         } else {
                 $("#sql").val("");
	         $("#tabladatos").append('<h1 style="text-align: center;">No hay datos</h1>');
         } // fin del if
         imprimir(); // cambia el valor de href para poder hacer la llamada e imprimir
      });
      }

// ================================================================== 
// Función muestra juego asignaciones: añade al select
// ================================================================== 
    function muestrajuegoasignaciones() {       
	 var alumno = $('#alumno').val();
         var posting = $.post( "./anotaciones/juegoasignacionesenanotaciones.php", { 
             alumno: alumno,
         });
         posting.done(function(data,status){
		 // alert (data);
		 var datos = $.parseJSON(data); // en la variable datos paso los datos tipo JSON
		 // alert (datos);
                 $("#select2").html("");
                 $("#select2").append('<option value="0">Todas las opciones</option>');
		 if (data.length>2) {
                     for (var i in datos.asignacion) { // relleno 
                         $("#select2").append('<option value="'+datos.asignacionnum[i]+'">'+datos.asignacion[i]+'</option>');
                     }
                    
		 } // fin del if */
         });
         } 

// ================================================================== 
// Función Imprime el contenido
// ================================================================== 
    function imprimir() {       
	 var sql = $('#sql').val();
         // alert(sql);
         $("#imprimir").attr("href",'./ficheros/veranotacionespdf.php?sql='+sql);
         $("#imprimir").attr("alt",'Imprime');
         $("#imprimir").attr("title",'Imprime');
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
    <!-- ================================================================== -->
    <!-- <p><?php echo "contador: ".$_SESSION['contador']; ?></p> -->

    <? incluyefoto($alumnos['idalumno'][$_SESSION['contador']],"-5px","5%","absolute",""); ?>
   
    <form id="fanotacion" action="./veranotaciones.php" method="post">
  
    <!-- 2º) Información general de la asignación -->
    <div id="contienealumno" style="border: 0px solid black; width: 90%;"> 
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
   </div> <!--Contiene a los divs donde aparece el nombre de los alumnos.-->

    <input id="contador" name="contador" type="hidden" value="<?php echo $_SESSION['contador']; ?>">
    <input id="alumno" name="alumno" type="hidden" value="<?php echo $alumnos['idalumno'][$_SESSION['contador']]; ?>">
    <input id="asignacion" name="asignacion" type="hidden" value="<?php echo $_SESSION['asignacion']; ?>">
    <!-- <input id="envio" name="envio" type="submit" value="Enviar"> -->
    <!-- ================================================================== -->   
    </form>	

<!-- ================================================================== -->
<!-- ================================================================== -->

    <!-- DIV TABs -->
    <div class="presentardatos" style="width: 95%; overflow: auto; margin: 1em auto;">
        <!-- Pestañas de las TABS TABs --> 
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
        <!-- Filtro por ASIGNACION -->
        <tr style="vertical-align:middle;"><td colspan="2">
          <b>Filtra por una asignacion:&nbsp;&nbsp;</b>
          <select name="select" class="botones" id="select2" style="text-align: left;">
	  <option value="">Elige una asignación</option>
          </select>
          <input id="sql" type="hidden" size="100"></td> <!-- Ocultar el valor del FILTRO -->
          <td>
          <a id="imprimir" href=""><img src="./imagenes/otros/print.png" width="50px" height="auto"></a>
        </td></tr>
	</table>
	<br>

        <!-- Tabla de los datos en sí... -->
    <div id="tabladatos" style="width: 95%; overflow: auto; margin: 1em auto;">
    </div>
        </div> <!-- Tabs-2-->
    </div> <!-- PRESENTAR DATOS-->

<!-- ================================================================== -->
<!-- ================================================================== -->
</div> <!-- Fin de la capa de información -->
<!-- ================================================================== -->
<!-- ================================================================== -->

<br><br>
</body>
</html>


