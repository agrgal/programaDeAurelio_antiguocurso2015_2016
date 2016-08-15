<?
include_once("./funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("./clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

$iz = "left: 120px;" ; // posición de los campos a la izquierda

session_start();

// if ($_SESSION["cargado"]=="ya") {
//   header("Location: ./edicionrecuperaprofesores.php");
// }
//datos del arhivo
$nombre_archivo = $_FILES['userfile']['name']; 

?>
<html>
<head>
<title>Recupera PROFESORES/AS de una lista de texto</title>
<meta http-equiv="Content-Type" content="Type=text/html; charset=iso-8859-15">

<!-- Incluyo las diferentes hojas de estilo -->
<?php include_once("./listas_de_css.php"); ?>
</head>
<body>
<!-- Titular -->
<div id="contenedor">
<div id="titular" onmouseover="javascript: ocultar()"></div>

<!-- Capa de menú: navegación de la página -->
<?php include_once("./lista.php"); ?>

<!-- Capa de información -->
<div id="informacion" onmouseover="javascript: ocultar()"> 
<h1>Recupera una lista de ALUMNOS/AS</h1>
<p>El fichero, en formato CSV o TXT, tiene que tener los siguientes campos (bajados de Séneca o construidos por el usuario): <b>Empleado/a y DNI/Pasaporte. Opcional: IDEA, tutorde (curso de tutoría), email</b> La primera fila debe corresponder al nombre de los mismos. Los campos deben estar separados por PUNTO-COMA y no tiene que haber ningún caracter delimitador de texto.Cada línea se considera un registro. El fichero tiene que estar codificado en UTF8. Este fichero puede obtenerse en base a una descarga en Séneca (con algún tratamiento posterior).</p>
<?php if (!isset($nombre_archivo) || strlen($nombre_archivo)<=0 || $_SESSION['cargado']=="ya") { $_SESSION['cargado']="no"; // recupera un archivo?>
<form action="./edicionrecuperaprofesores.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="MAX_FILE_SIZE" value="1000000">
    <p>Enviar un nuevo archivo: 
    <input name="userfile" type="file">
    <input type="submit" value="Enviar"></p>
</form> 
<?php } else { // Si lo encuentra, lo pone en la carpeta FICHEROS con el nombre profesores.CSV
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
       echo '<p>Cargado el fichero PROFESORES.CSV a la carpeta FICHEROS...</p>';
       // copy($ruta_temporal,"pruebas.csv");
       copy($ruta_temporal,"./ficheros/profesores.csv"); // carga el fichero en la carpeta FICHEROS       
       // 2º) Reconoce el fichero
       $lineas=file("./ficheros/profesores.csv");
       $_SESSION['cargado']="ya";
       $campos=explode(";",$lineas[0]); //array con el nombre de los campos
       $marcador=0; $existepuesto=0;
       // echo '<div id="presentardatos">';
       echo '<div id="listacampos"><ul>';
       foreach ($campos as $num_campos => $nombre) {   
         // $nombre=iconv("ISO-8859-1", "UTF-8",  $nombre); // convierte a UTF8    
         if (trim(strtolower($nombre))=="empleado/a") {$marcador++; $ordempleado=$num_campos;}
         if (trim(strtolower($nombre))=="dni/pasaporte") {$marcador++; $orddni=$num_campos;}
	 if (trim(strtolower($nombre))=="idea") {$ordidea=$num_campos; $existeidea=1;} 
	 if (trim(strtolower($nombre))=="tutorde") {$ordtutorde=$num_campos; $existetutorde=1;} 
         if (trim(strtolower($nombre))=="email") {$ordemail=$num_campos; $existeemail=1;} 
         if (!is_null($nombre)) {echo '<li><b>'.$nombre.' ('.($num_campos+1).') -- Campos correctos OBLIGATORIOS: '.$marcador.'</b></li>'; }
       }
       echo '</ul></div>';
       echo '<p>Aunque el fichero compruebe otros campos NO LOS AÑADIRÁ A LA BASE DE DATOS. En concreto NO AÑADE el campo que reconoce si hay o no admnistradores.</p>';
       // echo '</div>';
	
       // 3º) Es un fichero válido, contiene los 2 campos.
       if ($marcador==2) {
          echo '<p>Fichero correcto. Tiene al menos 2 campos válidos</p>';
          // 3a) Inicializa varios arrays con datos
          $datos[]=array();
	  // 3b) almacena en arrays cada valor de cada campo
          $i=0;
          foreach ($lineas as $num_linea => $linea) { // recorre cada linea de los datos
            $valores=explode(";",$linea); // convierte cada linea en un array
            if ($num_linea>0 && $valores[$ordempleado]<>'') { // línea que no contiene el nombre de los campos y existe en un curso       
	       $datos['empleado'][$i]=iconv("UTF-8","ISO-8859-1",$valores[$ordempleado]);	
               $datos['dni'][$i]=iconv("UTF-8","ISO-8859-1",$valores[$orddni]);
               if ($existeidea==1) {iconv("UTF-8","ISO-8859-1",$datos['idea'][$i]=$valores[$ordidea]);}
	       if ($existetutorde==1) {iconv("UTF-8","ISO-8859-1",$datos['tutorde'][$i]=$valores[$ordtutorde]);}
	       if ($existeemail==1) {iconv("UTF-8","ISO-8859-1",$datos['email'][$i]=$valores[$ordemail]);}
	       $i++; //aumento el contador
            }
          } 
 
	 // 3c) visualizar los datos en forma de tabla
         ?>
	 <div id="presentardatos2">
         <form name="introducedatos" action="./edicionrecuperaprofesores.php" method="post">
            <input name="boton" class="botonesdos" id="boton" value="Añade y Borra lo anterior" alt="Añade y Borra lo anterior" title="Añade y Borra lo anterior" onClick="annadeborra('1');" type="submit">
            <input name="boton" class="botonesdos" id="boton" value="Anexa" alt="Anexa" title="Anexa" onClick="annadeborra('0');" type="submit">
            <h2 style="text-align: center">Introduce DNI+Letra del administrador:&nbsp;&nbsp;
		<select name="dniadministrador" class="cajones" id="dniadministrador" alt="DNI administrador" title="DNI administrador">
		    <option value="">Escoge algún un DNI+Letra</option>
		    <?php 
			foreach ($datos['dni'] as $key => $valor) {
			  echo '<option value="'.trim(strtoupper($valor)).'">'.$datos['empleado'][$key].' ('.$valor.')'.'</option>';
 			}
                    ?>
                </select>
            </h2>
	    <h2 style="text-align: center">
         </form>
         </br>
         <h2 style="text-align: center;" id="complementarios"></h2>
         </div> 
         <?php // fin del formulario de aceptar los datos

          // 3d) visualizar los datos en forma de tabla
          echo '<div style="margin:2% auto; text-align: center; padding: 10px auto; height: auto; width: 100%;" id="presentardatos2">';
          echo '<br><table style="margin:2px auto; height: auto; text-align: center; width: 95%;" border="1" cellpadding="1" cellspacing="1" class="tabla">';
          echo '<tr><th style="width: 5%; font-weight: bold; text-align: center;">Nº</th><th style="width: 35%; font-weight: bold; text-align: center;">Nombre Empleado/a</th><th style="width: 30%; font-weight: bold; text-align: center;">DNI/pasaporte</th><th style="width: 30%; font-weight: bold; text-align: center;">IDEA</th><th style="width: 30%; font-weight: bold; text-align: center;">tutorde</th><th style="width: 30%; font-weight: bold; text-align: center;">email</th></tr>';
          foreach ($datos['empleado'] as $puntero => $nombre) {
             // echo '<b>'.$nombre.' '.$datos['pa'][$puntero].'</b><br>';          
             echo '<tr>';
                echo '<td style="text-align: center;">'.($puntero+1).'</td>';
		echo '<td style="text-align: center;">'.$nombre.' </td>';
		echo '<td style="text-align: center;">'.$datos['dni'][$puntero].' </td>';
		echo '<td style="text-align: center;">'.$datos['idea'][$puntero].' </td>';
		echo '<td style="text-align: center;">'.$datos['tutorde'][$puntero].' </td>';
		echo '<td style="text-align: center;">'.$datos['email'][$puntero].' </td>';
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

</div> <!-- Fin de la capa del contenedor -->

<div id="fecha">
  	<p>
	<? 
	echo 'Fecha: '.$calendario->fechaformateada($calendario->fechadehoy());
	echo '. Hora: '.$calendario->horactual();
	?></p>
</div>

</body>
</html>

<?php 

echo '
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
    var query_string = "lee="+query;
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
    document.getElementById("complementarios").innerHTML = peticion_http.responseText;
    // location.reload();
    }    
  }
}

// ************************
// FUNCIONES DE LOS BOTONES
// ************************
function annadeborra(como) {
  var dniadministrador = document.getElementById("dniadministrador").value;
  if (como=="1") { //caso desde cero
	  if (dniadministrador.length>0) {
		var envia = como+"***"+dniadministrador;
	  	cargaContenido("./profesores/annadeborra.php", "POST", muestraContenido, envia);
	  } else {
		alert("Necesito que elijas un dato DNI+Letra válido, al menos");
		return;
          }
  } // distinto de anexar 
  else { // caso sólo anexar
	if (dniadministrador==null) {
	   alert("No estás enviando un DNI+Letra válido. En los datos anexados no habrá administradores");
           dniadministrador="nohaydni";
        }
	var envia = como;
  	cargaContenido("./profesores/annadeborra.php", "POST", muestraContenido, envia);
  }
}


// *********************************
// NIF VÁLIDO
// *********************************
function nif_valido(abc)
{
	dni=abc.substring(0,abc.length-1);
	let=abc.charAt(abc.length-1);
	if (!isNaN(let)) {
		return false;
	}else{
		cadena = "TRWAGMYFPDXBNJZSQVHLCKET";
		posicion = dni % 23;
		letra = cadena.substring(posicion,posicion+1);
		if (letra!=let.toUpperCase()){
			//alert("Nif no válido");
			return false;
		}
	}
	//alert("Nif válido")
	return true;
}

</script>';
// Fin de los scripts en JAVA
?>
