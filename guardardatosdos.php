<?php
include("./funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("./clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

/* $bd="encyclonumber"; */ //debe de haberse cargado en mysql_inc.php

session_start(); /* empiezo una sesión */

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


if (!isset($_SESSION['contador'])) {$_SESSION['contador']=0;} 
if (isset($_POST['contador']) && $_POST['contador']>=0) {$_SESSION['contador']=$_POST['contador'];}

// Ahora sí, reconocimiento de botones
if (isset($_POST['boton']) && $_POST['boton']=='Primero') {$_SESSION['contador']=0;}
if (isset($_POST['boton']) && $_POST['boton']=='Atrás') {$_SESSION['contador']--;}
if (isset($_POST['boton']) && $_POST['boton']=='Adelante') {$_SESSION['contador']++;}
if (isset($_POST['boton']) && $_POST['boton']=='Último') {$_SESSION['contador']=1000;}
if (isset($_POST['boton']) && $_POST['boton']=='Grabar') {$_SESSION['contador']=$_SESSION['contador'];}

$iz = "left: 300px;" ; // posición de los campos a la izquierda

?>
<html>
<head>
<title>Introduce datos del alumnado</title>
<meta http-equiv="Content-Type" content="Type=text/html; charset=iso-8859-15">
<script language="javascript" type="text/javascript" src="./tinymce/jscripts/tiny_mce/tiny_mce.js"> </script>
<script language="javascript" type="text/javascript">
tinyMCE.init({
        // General options
        mode : "textareas",
        theme : "advanced",
        language: 'es',
        plugins : "autoresize,autolink,lists,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
        width: '100%',
        height: 150,
        autoresize_min_height: 100,
        autoresize_max_height: 300,

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

        // disparador de un evento on change
        handle_event_callback : "grabarcadena",   

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
<!-- Incluyo las diferentes hojas de estilo -->
<?php include_once("./listas_de_css.php"); ?>
</head>
<body>
<!-- Capas de presentación
<div id="grupo" style="position: absolute; left: 0px; top: 0px; width: 960; height: auto; z-index: 1; line-height: 14px"><img name="fondo" src="./imagenes_plantilla/plantilla.png" width="960" height="auto" border="0" alt=""></div>-->

<!-- Capa de menú: navegación de la página -->
<?php include_once("./lista.php"); ?>

<?php echo '
<script type="text/javascript" language="javascript">
 
var READY_STATE_UNINITIALIZED=0; 
var READY_STATE_LOADING=1; 
var READY_STATE_LOADED=2;
var READY_STATE_INTERACTIVE=3; 
var READY_STATE_COMPLETE=4;
 
var peticion_http;

var anterior=""; // texto anterior a los 1000 caracteres
var actual=""; // texto actual, al cargarse....

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
    }
  }
}

function muestraContenido2() {
  if(peticion_http.readyState == READY_STATE_COMPLETE) {
    if(peticion_http.status == 200) {
      // document.getElementById("presentardatos2").innerHTML = peticion_http.responseText;
      var recupera = peticion_http.responseText;
      // alert(recupera);
      escribecadena(recupera);
    }

  }
}

// *************************
// Listado de funciones
// *************************

function recuperacadenaotraevaluacion() {
  cargaContenido("./scriptsphp/recuperadatosotraevaluacion.php", "POST", muestraContenido2, val2);
}

function grabarcadena() {           
  // Reconocer items marcados
  reconocemarcados();
  // alert(valores);
  cargaContenido("./scriptsphp/insertaactualizadatos.php", "POST", muestraContenido, valores);
}

window.onload = function() {
  // al cargar la página pongo aquí el contenido del campo observaciones
  anterior = tinyMCE.get(\'observaciones\').getContent(); // valores del textarea
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
	<a name="anclajenombre" id="a"></a>
        <div id="cero">
          <?php // para propósitos de testeo
		/* foreach ($alumno['idalumno'] as $key => $valor) {
			echo $key.'.- '.$valor.' - '.$alumno['alumno'][$key].' - '.$alumno['unidad'][$key].'<br>';
                }
                echo $alumno['cadenaclases'].'<br>'; */
          ?>
        </div>
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
		  <!-- *********************************************-->
		  <!-- Formulario de introducción de datos -->
		  <!-- Visualización de datos -->
		  <!-- *********************************************-->

<form name="guardardatosdos" action="./guardardatosdos.php#anclajenombre" method="post">
<?php 
if ($visualizacion==1) { // activadas todas las opciones de visualizacion
	// detección de la variable de contador --> echo '<p>'.$comprueba.' -  '.$_SESSION['contador'].'</p>';        
        // modificación del contador     
	if ($_SESSION['contador']>$ii-1) {$_SESSION['contador']=$ii-1;} //Si supera este valor
	if ($_SESSION['contador']<0) {$_SESSION['contador']=0;} //Si es menor que cero

  
?>
        <!-- ============================ -->
        <!-- Se incluye fotografía -->
        <!-- ============================ -->
        <?php //Incluir fotografía
           incluyefoto($alumno['idalumno'][$_SESSION['contador']],"-5px","2%","absolute","none");  
        ?>
        <!-- ============================ -->
 
        <!-- onChange = "window.location.href = this.options[selectedIndex].value; this.selectedIndex=0;"> -->
        <p style="text-align: center;">	
        <select name="select" class="botones" id="select2" style="text-align: left;"
	  onChange="cambiarcombo();">
	  <option value="">Elige un alumno/a</option>
		<?php 
                $j=0;
                foreach ($alumno['idalumno'] as $key => $valor) {
                   echo '<option value="'.$j.'">';
                   echo $alumno['alumno'][$key].' ['.$alumno['unidad'][$key].']'.'</option>';		
                   $j++;
                }
		?>
        </select> 
	&nbsp;&nbsp;&nbsp;&nbsp;

	<!-- Elige evaluacion -->
        <select name="select" class="botones" id="selectevaluacion" style="text-align: left;"
	onChange = "obtienecadenaotraevaluacion();">
	<option value="">Elige datos de una evaluación anterior</option>
		<?php
		$link=Conectarse($bd); // y me conecto. //dependiendo del tipo recupero uno u otro.
		$Sql="SELECT DISTINCT ideval,nombreeval FROM tb_edicionevaluaciones ORDER BY ideval";
		$result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado
		while ($row=mysql_fetch_array($result)) {
		   if ($row['ideval']<>$_SESSION['evaluacion']) { // excepto la evaluación actual
                      echo '<option value="'.$row['ideval'].'">'.$row['nombreeval'].'</option>';
                   }
		}
		mysql_free_result($result); 
		?>
	</select>
	</p>


        <!-- <div id="presentardatos2"></div> <!--Sirve para visualizar los resultados de la función que se  -->
        <p style="text-align: center;">
	<input name="boton" class="botones" id="boton" value="Primero" type="submit" alt="Ir al primer registro" title="Ir al primer registro"  >
	<input name="boton" class="botones" id="boton" value="Atrás" type="submit" alt="Ir al registro anterior" title="Ir al registro anterior">
	
        <?php echo " ".$alumno['alumno'][$_SESSION['contador']]." (".$alumno['idalumno'][$_SESSION['contador']].") "; ?>	
        <input name="al1" class="botones" id="al1" type="hidden" value="<?php echo $alumno['idalumno'][$_SESSION['contador']]; ?>" >
        <input name="asi" class="botones" id="asi" type="hidden" value="<?php echo $_SESSION['asignacion']; ?>" >  
	<input name="pro" class="botones" id="pro" type="hidden" value="<?php echo $_SESSION['profesor']; ?>" >
	<input name="eva" class="botones" id="eva" type="hidden" value="<?php echo $_SESSION['evaluacion']; ?>" >
        <input name="contador" class="botones" id="contador" width="20px" type="hidden" value="<?php echo $_SESSION['contador']; ?>" >
 
	<input name="boton" class="botones" id="boton" value="Adelante" type="submit" title="Ir al siguiente registro" alt="Ir al siguiente registro">
	<input name="boton" class="botones" id="boton" value="Último" type="submit" title="Ir al último registro" alt="Ir al último registro">
	<input name="boton" class="botones" id="grabar" value="Grabar" type="submit" title="Grabar datos" alt="Grabar datos">
        <!-- <a href="./ver/verdospdfprofesor.php" class="botones" id="pdf" alt="Genera PDF">&nbsp;PDF&nbsp;</a> -->
        <a href="./ficheros/verdospdfprofesor.php" class="botones" id="pdf" alt="Genera PDF">&nbsp;PDF&nbsp;</a> 
	<br>
	</p> 
	<div style="margin: 10px 5px 1px 5px;"><p style="text-align: center; color: #664F14;">Observaciones (3.000 caracteres - unas 600 palabras): &nbsp;&nbsp;</p></div>
        <div style="margin: 10px 5px 1px 5px;">
        
        <?php 
          $recobs = recuperaobservaciones($bd,$_SESSION['asignacion'],$alumno['idalumno'][$_SESSION['contador']],$_SESSION['evaluacion']);
        ?>
                  
	<p style="text-align:center;"><textarea name="observaciones" onchange="grabarcadena();" class="cajones" maxlength="3000" alt="3.000 caracteres cómo máximo" vertical-align: middle; font-size:14px;" height="auto" id="observaciones"><?php echo utf8_encode($recobs);?></textarea></p>
	</div>	


	<!-- Dentro de un div una lista de acciones -->

        <div float="none" id="divgrupos">
              <?php 
                $listagrupos=array();
	 	for ($i=0;$i<=2;$i++) {
                $grupo=obtenergrupos($bd,$i);
                foreach ($grupo['grupo'] as $agrupacion) {
                   $listagrupos['grupo'][]=$agrupacion;
                   $listagrupos['tipo'][]=$i;  
                } } // guarda todo en la variable listagrupos
		$cadenadivs=implode("***",$listagrupos['grupo']);
		// lista de bullets
                echo '<ul id="grupos">'; 
                foreach($listagrupos['grupo'] as $titulo) {
                     // echo '<li><a id="lista'.$titulo.'" onClick="muestradiv('.'$titulo'.')">'.$titulo.'</a></li>';
   		     // echo "<li><a onClick='muestradiv(\"$titulo\")'>".$titulo."</a></li>"; // IMPORTANTE-> pasa parametro a una funcion en javascript	
                     echo "<li><a onClick='muestradiv(\"$titulo\",\"$cadenadivs\")'>".$titulo."</a></li>"; // IMPORTANTE-> pasa dos parametros a una funcion en javascript
                }
                echo '</ul>';
              ?>          
        </div>
        <!-- Dentro de cada div se introduciran los items a tratar -->
        <?php

        $yapuestos = recuperacadenaarray($bd,$_SESSION['asignacion'],$alumno['idalumno'][$_SESSION['contador']],$_SESSION['evaluacion']);

        echo '<div id="contenedor" style="border: 0px none black; float:none; height:auto; overflow:auto; ">';

        foreach($listagrupos['grupo'] as $key => $titulo) {
        if ($key>0) {echo '<div id="'.$titulo.'" style="display: none; border: 0px none black; width: 70%; float:left;">';}
        if ($key==0) {echo '<div id="'.$titulo.'" style="display: ; border: 0px none black; width: 70%; float:left;" >';}
	    // echo '<p>'.$titulo.'</p>';
            // poner los items de cada div
            // esto tiene que ver con la creación de arrays en javascript. Esto sí, porque así sabe cuales son positivos y negativos en cada caso
                $nom='nom'.chr($key+97); //los nombres empiezan por a minuscula
		// echo $nom.'<br>';
		$script=jsarraytodonada($bd,$titulo,$nom);
		// echo $script.'<br>';
		echo '<script>'.$script.'</script>'; // Escribe un script con el array de nombre noma, con los items a elegir		
		$script=jsarray($bd,$titulo,$nom,0);
		// echo $script.'<br>';
		echo '<script>'.$script.'</script>'; // Escribe un script con el array de nombre noman, con los items a elegir de caracter negativo
		$script=jsarray($bd,$titulo,$nom,1);
		// echo $script.'<br>';
		echo '<script>'.$script.'</script>'; // // Escribe un script con el array de nombre nompa, con los items a elegir de caracter positivo
            
           echo '<br><table class="tabla" style="text-align: left; width: 80%; margin-left: auto; margin-right: auto;" border="1" cellpadding="2" cellspacing="2"><tbody>';
	   echo '<tr><th style="width: 95%; font-weight: bold; text-align: center;">';
           echo '<a href="javascript:mv('.strtolower($nom).',0)">'.$titulo.'</a>';
           if ($listagrupos['tipo'][$key]<=1) { //Solo si son positivos o negativos
              echo ' (<a href="javascript:mv('.strtolower($nom).'p,1)">P</a>,';
              echo '<a href="javascript:mv('.strtolower($nom).'n,1)">N</a>)</th>'; 
           }
           echo '<th style="width: 20px; font-weight: bold; text-align: center;">-</th></tr>';
	
	for ($j=0;$j<count($items['iditem']);$j++) { // recorro todos los valores de los items, que se obtienen al principio
		 if ($items['grupo'][$j]==$titulo) { // si pertenece a ese grupo
		 echo '<tr>';
		 if ($items['positivo'][$j]==0) { $cl='style="color: rgb(200,0,0);"'; } else { $cl='';}
		 echo '<td '.$cl.'>'.$items['item'][$j].'</td>';
		 if (in_array($items['iditem'][$j],$yapuestos,false)) {
			$chk="checked"; $stl='style="background-color: #553241;"';
		 } else {
			$chk=""; $stl='';
		 } //si los encuentra en la cadena los muestra marcados.
		 // pero si se ha dado a marcar...
		 /* if (($titulo==$grupomarcado) && ($items['positivo'][$j]==$sentidomarcado)) {
			$chk="checked"; $stl='style="background-color: #553241;"';
                 } elseif (($titulo==$grupomarcado) && ($items['positivo'][$j]<>$sentidomarcado)) {
			$chk=""; $stl='';
		 } */
		 // echo '<td id="td'.$items['iditem'][$j].'" '.$stl.'><input onclick="Grabar()" id="'.$items['iditem'][$j].'" name="'.$items['iditem'][$j].'" type="Checkbox" value="Sí" '.$chk.'></td>';
                 echo '<td id="td'.$items['iditem'][$j].'" '.$stl.'>';
                 echo "<input onclick='grabarcadena();' id='".$items['iditem'][$j]."' name='".$items['iditem'][$j]."' type='Checkbox' value='Sí' ".$chk.">";
                 // echo $items['iditem'][$j];
                 echo '</td>'; 	
		 echo '</tr>';
                 }
	} //fin del for
	echo '</tbody></table>';  
	
        echo '</div>';

        } // fin del foreach 

        echo '<div id="presentardatos" style="width:25%; float:left;"> ';
          echo '<h2 style="text-align: justify;">Pulsa en el menú superior para cambiar de apartado.</h2><br>';
          echo '<h2 style="text-align: justify;">Puedes ir marcando items uno a uno, o pulsar en la P para marcar todos los positivos, o en la N para todos los negativos</h2><br>';
          echo '<h2 style="text-align: justify;">Pulsa en <a OnClick="clearup()">CLEAR</a> si quieres borrar TODOS LOS ITEMS DE TODOS LOS APARTADOS Y LAS OBSERVACIONES PARA ESTE/A ALUMNO/A</h2><br>'; 
        echo '</div>';

	echo '</div>';

        ?>
        <!-- Fin de introducir cada div -->


<?php  //Acaba la visualización
} else { // si no se puede visualizar
        echo '<h2>Imposible visualizar los datos</h2>';
} ?>
        
</form> <!-- Fin del form -->
</div> <!-- FIN DE LA Capa de información -->

<!-- ****************** -->
<!--       Script       -->
<!-- ****************** -->
<script type="text/javascript" language="javascript">

var cambiar=0;

var valores="";
var val2="";

function ocultadiv(cadena) {
   var trozos = cadena.split("***");
   for(var i=0; i< trozos.length; i++) {
      // alert(trozos[i]);
      var div = document.getElementById(trozos[i]);
      div.style.display='none';
   }
}

function muestradiv(divdado,cadena) {
   ocultadiv(cadena); // oculta todos los divs
   var div = document.getElementById(divdado);
   div.style.display='';
}

function mv(valores,c) { // esta función cambia el valor del check según clickea o no 
     for (j=0;j<document.guardardatosdos.elements.length;j++) {
       if(document.guardardatosdos.elements[j].type == "checkbox") {
	 for (i in valores) {
	   if(document.guardardatosdos.elements[j].id == valores[i]) {
	     if(c==1) {  
                  var elcambio = myXOR(document.guardardatosdos.elements[j].checked,1);            
		  document.guardardatosdos.elements[j].checked= elcambio;
                  } else {	
		  document.guardardatosdos.elements[j].checked=cambiar;                
	          } 
      } } } }

     if (c==0) {cambiar = myXOR(cambiar,1); }
     // SimularClick();	
     grabarcadena();
}

function myXOR(a,b) {
  // return ( a || b ) && !( a && b );
  return (!a && b) || (a && !b); 	
}


// Me permite escribir los datos recuperados de otra evaluacion
function escribecadena(datos) {
  // alert(datos);
  // 1º.- hace un borrado. Si no se pueden pisar datos
  // marcar todos como uncheck
  for (j=0;j<document.guardardatosdos.elements.length;j++) {
       if(document.guardardatosdos.elements[j].type == "checkbox") {
          if (document.guardardatosdos.elements[j].checked==1) { 
		document.guardardatosdos.elements[j].checked=0;
          }
       }
  } // fin del for
  // borra las observaciones
  tinyMCE.get('observaciones').setContent('');
  // 2º.- parte la cadena y hace los arrays
  var datosprocesados = datos.split('***');
  var items = datosprocesados[0].split('#');
  var obs = datosprocesados[1];
  // sale si no obtiene datos
  if ((datosprocesados[0].length<=0) && (obs.length<=0)) { 
     alert("Lo siento. No hay datos en esta evaluación para este alumno/a, por lo menos a tu nombre y en la materia elegida.");
     return;
  }
  // 3º.- marca los datos correspondientes
  if (datosprocesados[0].length>0) {
	  for (i=0;i<items.length;i++) { //recorro todos los items de la serie
	    document.getElementById(items[i]).checked=1;
	  } // fin del for
  } // fin del if
  // 4º.- graba las observaciones
  if (obs.length>0) {tinyMCE.get('observaciones').setContent(obs);}
  // 5º.- graba los datos
  grabarcadena(); // graba los datos 
}

// Necesito una función que ahora reconozca todos los valores marcados y los guarde de forma 
// asíncrona, junto con el valor de observaciones. 
function reconocemarcados() {
    valores="";
    for (j=0;j<document.guardardatosdos.elements.length;j++) {
       if(document.guardardatosdos.elements[j].type == "checkbox") {
          if (document.guardardatosdos.elements[j].checked==1) { 
             valores=valores+document.guardardatosdos.elements[j].id+"#";
             var idcelda="td"+document.guardardatosdos.elements[j].id;
             var celda= document.getElementById(idcelda);
             celda.style.backgroundColor="#553241";
          } else {
             var idcelda="td"+document.guardardatosdos.elements[j].id;
             var celda= document.getElementById(idcelda);
             celda.style.backgroundColor="#ffffff";  
          }          		
       }
    }
    valores = valores.substring(0,valores.length-1);
    var obs = tinyMCE.get('observaciones').getContent(); // valores del textarea
    var asi = document.guardardatosdos.asi.value;
    // var unidad = document.guardardatosdos.uni.value;
    // var profesor = document.guardardatosdos.pro.value;
    // var materia = document.guardardatosdos.mat.value;	
    var evaluacion = document.guardardatosdos.eva.value;
    valores = valores+"***"+obs;
    var alumno = document.guardardatosdos.al1.value;
    valores = asi+"***"+alumno+"***"+evaluacion+"***"+valores;
    // alert(valores); // envía parte de la cadena a grabar para escribir datos con los items de los alumnos y las observaciones	
} // fin de la función de reconocemarcados


function clearup() {
if(confirm("¿Estás seguro que quieres BORRAR TODOS LOS ITEMS MARCADOS DE TODOS LOS APARTADOS y el campo OBSERVACIONES para este/a ALUMNO/A?")) {
// marcar todos como uncheck
for (j=0;j<document.guardardatosdos.elements.length;j++) {
       if(document.guardardatosdos.elements[j].type == "checkbox") {
          if (document.guardardatosdos.elements[j].checked==1) { 
		document.guardardatosdos.elements[j].checked=0;
          }
       }
} // fin del for
// borra las observaciones
tinyMCE.get('observaciones').setContent('');
grabarcadena(); // graba los datos
} // fin del if de confirmación
}

function cambiarcombo() {
  // 1º.- obtiene el valor del combo
  var cc = document.getElementById("select2").value;
  // alert(cc);
  // 2º.- el valor del campo contador
  document.getElementById("contador").value=cc;
  // 3º.- recargo la página
  document.getElementById("grabar").click(); // simula el darle al botón grabar que permite lanzar el formulario POST y recargar la página
  // recargo también la variable de contador; así puede funcionar el combo.   
}

function obtienecadenaotraevaluacion() {
  val2="";
  // 1º.- obtiene el valor del combo
  var eva = document.getElementById("selectevaluacion").value;
  if (eva==0) {return;}
  // alert(eva);
  // Advertencia cachonda
  var primera=confirm('Esta herramienta de copia de datos procedentes de otras evaluaciones debe usarse con MODERACIÓN.\nUna sobredosis puede causar irritación de Jefatura de Estudios y malestar en la Dirección [:-)].\nEn general, se recomienda su uso responsable, la revisión de los datos y que estos reflejen fielmente la situación de cada alumno/a en cada momento. GRACIAS. Para proseguir, ACEPTAR.');
  if (primera==false) {return;}
  // Confirmación
  var indice = document.getElementById("selectevaluacion").selectedIndex;
  var textoselect = document.getElementById("selectevaluacion").options[indice].text;
  var confirmacion = confirm('¿De verdad quieres recuperar datos de la evaluación \"'+textoselect+'\"?\n ¡¡TODOS LOS DATOS ESCRITOS HASTA AHORA, PARA ESTE ALUMNO/A, SE ELIMINARÁN!!');
  if (confirmacion==false) {return;}
  // 2º.- obtengo el valor del alumno, materia, etc.
  var al1 = document.getElementById("al1").value;
  // var uni = document.getElementById("uni").value;
  // var pro = document.getElementById("pro").value;
  // var mat = document.getElementById("mat").value; 
  var asi = document.guardardatosdos.asi.value; 
  val2 = asi+"***"+al1+"***"+eva;
  // alert(val2);
  recuperacadenaotraevaluacion();    
}

// estadísticas para tinyMCE
function getStats(id) {
    var body = tinymce.get(id).getBody(), text = tinymce.trim(body.innerText || body.textContent);
    return {
        chars: text.length,
        words: text.split(/[\w\u2019\'-]+/).length
    };
} 

</script>
<!-- ****************** -->
<!-- Fin de los scripts -->
<!-- ****************** -->

</body>
</html>




