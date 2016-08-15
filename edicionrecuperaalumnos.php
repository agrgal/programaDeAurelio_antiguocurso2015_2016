<?
include_once("./funciones/funciones.php"); /* incluye el directorio de funciones */

include_once("./clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

$iz = "left: 120px;" ; // posición de los campos a la izquierda

session_start();

//datos del arhivo
$nombre_archivo = $_FILES['userfile']['name']; 

?>
<html>
<head>
<title>Recupera ALUMNOS/AS de una lista de texto</title>
<meta http-equiv="Content-Type" content="Type=text/html; charset=iso-8859-15">

<!-- Incluyo las diferentes hojas de estilo -->
<?php include_once("./listas_de_css.php"); ?>
</head>
<body>

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

       $("#boton1").click(function(){ // Añade y borra los anteriores
           var como = 1;
           var posting = $.post( "./alumnos/annadeborra.php", { 
	     lee: como,
         });
         posting.done(function(data,status){
            alert("El programa responde: "+data);
	    location.reload(); // No, porque si no recarga
         });
       });

       $("#boton2").click(function(){ // Solo anexa
           var como = 0;
           var posting = $.post( "./alumnos/annadeborra.php", { 
	     lee: como,
         });
         posting.done(function(data,status){
            alert("El programa responde: "+data);
	    location.reload(); // No, porque si no recarga
         });
       });


    });

</script>


<!-- Titular -->
<div id="contenedor">
<div id="titular" onmouseover="javascript: ocultar()"></div>

<!-- Capa de menú: navegación de la página -->
<?php include_once("./lista.php"); ?>

<!-- Capa de información -->
<div id="informacion" onmouseover="javascript: ocultar()"> 
<h1>Recupera una lista de ALUMNOS/AS</h1>
<p>El fichero, en formato CSV o TXT, tiene que tener los siguientes campos (bajados de Séneca o construidos por el usuario): <span style="color: blue;">Alumno/a y Unidad</span>.  La primera fila debe corresponder al nombre de los mismos. Los campos deben estar separados por punto-coma. Cada línea se considera un registro. El fichero tiene que estar codificado en UTF8. El campo <span style="color: blue;">Alumno/a</span> consta de los dos apellidos [coma] nombre.</p>
<?php if (!isset($nombre_archivo) || strlen($nombre_archivo)<=0 || $_SESSION['cargadodos']=="ya") { $_SESSION['cargadodos']="no"; // recupera un archivo ?>
<form action="./edicionrecuperaalumnos.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="MAX_FILE_SIZE" value="1000000">
    <p>Enviar un nuevo archivo: 
    <input name="userfile" type="file">
    <input type="submit" value="Enviar"></p>
</form> 
<?php } else { // Si lo encuentra, lo pone en la carpeta FICHEROS con el nombre ALUMNOS.CSV
  //datos del arhivo
  $tipo_archivo = $_FILES['userfile']['type'];
  $tamano_archivo = $_FILES['userfile']['size'];
  $ruta_temporal = $_FILES['userfile']['tmp_name'];

  //compruebo si las características del archivo son las que deseo
  if (!((strpos($tipo_archivo, "txt") || strpos($tipo_archivo, "csv")) && ($tamano_archivo < 1000000))) {
    echo "La extensión o el tamaño de los archivos no es correcta. <br><br><table><tr><td><li>Se permiten archivos .txt o .csv<br><li>se permiten archivos de 1000 Kb máximo.</td></tr></table>";
  } else {
    // 1º) Carga el fichero
    if (is_uploaded_file($ruta_temporal)) {
       echo '<p>Cargado el fichero ALUMNOS.CSV a la carpeta FICHEROS...</p>';
       // copy($ruta_temporal,"pruebas.csv");
       copy($ruta_temporal,"./ficheros/alumnos.csv"); // carga el fichero en la carpeta FICHEROS
       
       // 2º) Reconoce el fichero
       $lineas=file("./ficheros/alumnos.csv");
       $_SESSION['cargadodos']="ya";
       $campos=explode(";",$lineas[0]); //array con el nombre de los campos
       $marcador=0; 
        echo '<div id="listacampos"><ul>';
       foreach ($campos as $num_campos => $nombre) {   
         // $nombre=iconv("ISO-8859-1", "UTF-8",  $nombre); // convierte a UTF8    
         if (trim(strtolower($nombre))=="alumno/a") {$marcador++; $ordnombre=$num_campos;}
         if (trim(strtolower($nombre))=="unidad") {$marcador++; $ordunidad=$num_campos;}
         echo '<li><b>'.$nombre.' ('.($num_campos+1).') -- Campos correctos: '.$marcador.'</b></li>';
       }
       echo '</ul></div>';
       echo '<p>Aunque el fichero compruebe otros campos NO LOS AÑADIRÁ A LA BASE DE DATOS.</p>';

       // 3º) Es un fichero válido, contiene los 4 campos.
       if ($marcador==2) {
          echo '<p>Fichero correcto. Tiene al menos 2 campos válidos</p>';
          // 3a) Inicializa varios arrays con datos
          $datos[]=array();
	  // 3b) almacena en arrays cada valor de cada campo
          $i=0;
          foreach ($lineas as $num_linea => $linea) { // recorre cada linea de los datos
            $valores=explode(";",$linea); // convierte cada linea en un array
            if ($num_linea>0 && trim($valores[$ordunidad])<>'' && trim($valores[$ordnombre])<>'') { // línea que no contiene el nombre de los campos y existe en un curso       
	       $datos['alumno/a'][$i]=iconv("UTF-8","ISO-8859-1",$valores[$ordnombre]);	
               $datos['unidad'][$i]=iconv("UTF-8","ISO-8859-1",$valores[$ordunidad]);
	       $i++; //aumento el contador
            }
          } 
 
	 // 3c) visualizar los datos en forma de tabla
         ?>
	 <div id="presentardatos2">
         <!-- <form name="introducedatos" action="./edicionrecuperaalumnos.php" method="post"> -->
            <input name="boton" class="botonesdos" id="boton1" value="Añade y Borra lo anterior" alt="Añade y Borra lo anterior" title="Añade y Borra lo anterior"" type="submit">
            <input name="boton" class="botonesdos" id="boton2" value="Anexa" alt="Anexa" title="Anexa" type="submit">
         <!-- </form> -->
         </br>
         <h2 style="text-align: center;" id="complementarios"></h2>
         </div> 
         <?php // fin del formulario de aceptar los datos

          // 3d) visualizar los datos en forma de tabla
          echo '<div style="margin:2% auto; text-align: center; padding: 10px auto; height: auto; width: 100%;" id="presentardatos2">';
          echo '<br><table style="margin:2px auto; height: auto; text-align: center; width: 95%;" border="1" cellpadding="1" cellspacing="1" class="tabla">';
          echo '<tr><th style="width: 5%; font-weight: bold; text-align: center;">Nº</th><th style="width: 75%; font-weight: bold; text-align: center;">Apellidos [coma] Nombre</th><th style="width: 20%; font-weight: bold; text-align: center;">Unidad</th>';
          foreach ($datos['alumno/a'] as $puntero => $nombre) {
             // echo '<b>'.$nombre.' '.$datos['pa'][$puntero].'</b><br>';          
             echo '<tr>';
                echo '<td style="text-align: center;">'.($puntero+1).'</td>';
		echo '<td style="text-align: center;">'.$nombre.'</td>';
		echo '<td style="text-align: center;">'.$datos['unidad'][$puntero].'</td>';
             echo '</tr>';	    
	   } // fin del foreach
         echo '</table>';
         echo '</div>';
         
         // 3d) una vez validado, podemos escribir el formulario de acciones



       } else {
       // fichero con datos no válidos
       echo '<p>Lo siento. El fichero debe tener 2 campos válidos. Inténtalo con otro fichero que cumpla esa condición.</p>';
       } // fin del if de marcadores

    } // fin del if de reconocimiento y carga del fichero    
    
  } // fin del if que reconoce si es un archivo correcto


 
} // fin del else principal

unset($nombre_archivo);

?>

</div> <!-- fin de la capa de información -->

<div id="fecha">
  	<p>
	<? 
	echo 'Fecha: '.$calendario->fechaformateada($calendario->fechadehoy());
	echo '. Hora: '.$calendario->horactual();
	?></p>
</div>

</div> <!-- Fin de la capa del contenedor -->

</body>
</html>


// Fin de los scripts en JAVA
?>
