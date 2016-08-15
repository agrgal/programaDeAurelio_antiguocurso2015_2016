<?
include("./funciones/funciones.php"); /* incluye el directorio de funciones */

include_once("./clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

/* $bd="encyclonumber"; */ //debe de haberse cargado en mysql_inc.php

$iz = "left: 120px;" ; // posición de los campos a la izquierda

session_start();

$bd2="bdseritium20copia";

?>
<html>
<head>
<title>Plantilla de documento</title>
<meta http-equiv="Content-Type" content="Type=text/html; charset=iso-8859-15">
<!-- Inclyo las diferentes hojas de estilo -->
<?php include_once("./listas_de_css.php"); ?>
</head>
<body>
<!-- Capas de presentación
<div id="grupo" style="position: absolute; left: 0px; top: 0px; width: 960; height: auto; z-index: 1; line-height: 14px"><img name="fondo" src="./imagenes_plantilla/plantilla.gif" width="960" height="auto" border="0" alt=""></div> -->
<div class="grupo"></div>

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

 <?php 

 // 1º) mostrar datos de tb_evaluacionantigua
 // nombre de los campos
 echo '<h1>Campos de tb_evaluacionantigua en '.$bd2.'</h1>';
 $link=Conectarse($bd2);
 $Sql='SELECT * FROM tb_evaluacionantigua'; 
 $result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result
 $campos=array();
 // echo $bd2." ".$campos;
 /* obtener los metadatos de la columna */ 
 $i = 0;
 while ($i < mysql_num_fields($result)) {
    // echo "Información de la columna $i:<br />\n";
    $metadatos = mysql_fetch_field($result, $i);
    if (!$metadatos) {
        echo "No hay información disponible<br />\n";
    }
    $campos[]=$metadatos->name;
    $i++;
 }

 foreach ($campos as $key =>  $valor) {
    echo "<p>Clave: ".$key." - Valor: ".$valor."</p>";
 }
 mysql_free_result($result);

 // 2º) Obtiene distintas asignaciones
 echo '<h1>Parejas PROFESOR-MATERIA '.$bd2.'</h1>';
 $link=Conectarse($bd2);
 $Sql='SELECT DISTINCT profesor, materia FROM tb_evaluacionantigua WHERE profesor>0 AND materia>0 AND eval<>10 ORDER BY profesor, materia'; 
 $result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result
 $parejas=array();
 $ii=0; // contador 
 while ($row=mysql_fetch_array($result)) {
        $parejas['profesor'][]=$row['profesor'];
        $parejas['materia'][]=$row['materia'];
        $parejas['alumnos'][]="";
	$ii++;
	}
 mysql_free_result($result);

 foreach ($parejas['profesor'] as $key => $valor) {
        $materia = dado_Id($bd2,$parejas['materia'][$key],"Materias","tb_asignaturas","idmateria");
        $profesor = cambiarnombre(dado_Id($bd,$valor,"Empleado","tb_profesores","idprofesor"));
	echo '<p>'.($key+1).'.- '.$profesor.' - '.$materia.'</p>';
 }

 // 3º) TRUNCAR la tabla de asignaciones
 echo '<h1>Truncar la tabla asignaciones</h1>';  
 $link=Conectarse($bd2);
 $Sql='TRUNCATE tb_asignaciones'; 
 $result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result
 mysql_free_result($result);

 // 4º) Obtener cadenas de alumnos 
 echo '<h1>Obtener cadenas de alumnos por cada pareja</h1>';  
 foreach ($parejas['profesor'] as $key => $valor) { // por cada pareja
     $link=Conectarse($bd2);
     $Sql='SELECT DISTINCT alumno FROM tb_evaluacionantigua WHERE profesor="'.$valor.'" AND materia="'.$parejas['materia'][$key].'" ORDER BY profesor, materia, alumno';
     $result=mysql_query($Sql,$link);
     $num=0;
     while ($row=mysql_fetch_array($result)) {
 	$parejas['alumnos'][$key].=$row['alumno']."#";
        $num++;
     }
     mysql_free_result($result);
     $materia = dado_Id($bd2,$parejas['materia'][$key],"Materias","tb_asignaturas","idmateria");
     $profesor = cambiarnombre(dado_Id($bd2,$valor,"Empleado","tb_profesores","idprofesor"));
     $parejas['alumnos'][$key]=distintosgrupos($bd2,$parejas['alumnos'][$key]); //convierte la cadena a una cadena donde se pueden separar por grupos
     echo '<p>'.($key+1).'.- '.$profesor.' - '.$materia.': ('.$num.') '.'@@@'.$parejas['alumnos'][$key].'@@@</p>';
 } // fin del for por cada pareja

// 5º) Guarda los datos de asignación

foreach ($parejas['alumnos'] as $key =>$valor) { // por cada valor de parejas->alumnos
   $asignacion=array();
   $asignacion=explode("***",$valor);
   foreach ($asignacion as $cadenavalor) {
	// $materia = dado_Id($bd2,$parejas['materia'][$key],"Materias","tb_asignaturas","idmateria");
        // $profesor = cambiarnombre(dado_Id($bd,$parejas['profesor'][$key],"Empleado","tb_profesores","idprofesor"));
        // echo '<p>'.$profesor.' - '.$materia.': ('.$cadenavalor.')</p>';
	$materia=$parejas['materia'][$key];
        $profesor=$parejas['profesor'][$key];
        $datos=$cadenavalor;
        $num=explode("#",$cadenavalor);
        if (count($num)>1) { $unidad="Alumnos/as sueltos de ".dado_Id($bd2,$num[0],"unidad","tb_alumno","idalumno"); } else { $unidad=$num[0]; }
        $descripcion=iconv("ISO-8859-1","UTF-8",dado_Id($bd2,$parejas['materia'][$key],"Materias","tb_asignaturas","idmateria"))." - ".$unidad;
        $unidadtutoria=trim(dado_Id($bd2,$profesor,"tutorde","tb_profesores","idprofesor"));
        if (strpos(trim($unidad),$unidadtutoria)===false) { $tutorada=0; } else {$tutorada=1;}        
        // echo '<div id="presentardatos">';
        //   echo '<p>'.$profesor.' - '.$materia.' - '.$datos.' - '.$descripcion.' - '.$unidadtutoria.': '.$tutorada.'</p>';
        // echo '</div>';    
        $Sql="INSERT INTO tb_asignaciones (profesor,materia,datos,descripcion,tutorada) VALUES (";
	if ($profesor<>'') {$Sql.="'".$profesor."', ";} else {$Sql.="'-',";}
	if ($materia<>'') {$Sql.="'".$materia."', ";} else {$Sql.="'-',";}
	if ($datos<>'') {$Sql.="'".$datos."', ";} else {$Sql.="'-',";}
	if ($descripcion<>'') {$Sql.="'".$descripcion."', ";} else {$Sql.="'-',";}
	if (strval($tutorada)<>'') {$Sql.="'".strval($tutorada)."', ";} else {$Sql.="'-',";}
	$Sql=substr($Sql,0,strlen($Sql)-2); // Quitar la última coma
	$Sql.=")";    
  	echo '<p>'.$Sql.'</p>';
        if ($datos<>'') {
        $link=Conectarse($bd2);
	$result=mysql_query($Sql,$link); //ejecuta la consulta
	mysql_free_result($result); }
   }
}


// Borro la tabla tb_evaluación
// 3º) TRUNCAR la tabla de asignaciones
 echo '<h1>Truncar la tabla evaluacion</h1>';  
 $link=Conectarse($bd2);
 $Sql='TRUNCATE tb_evaluacion'; 
 $result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result
 mysql_free_result($result);

// 6º) Recorro los distintos registros de evaluación 
$Sql="Select * FROM tb_evaluacionantigua";
$link=Conectarse($bd2);
$result=mysql_query($Sql,$link);
$evaluacion=array();
$num=0;
     while ($row=mysql_fetch_array($result)) {
 	$evaluacion['idevaluacion'][]=$row['idevaluacion'];
        $evaluacion['fecha'][]=$row['fecha'];
	$evaluacion['unidad'][]=$row['unidad'];
	$evaluacion['profesor'][]=$row['profesor'];
        $evaluacion['materia'][]=$row['materia'];
	$evaluacion['alumno'][]=$row['alumno'];
	$evaluacion['eval'][]=$row['eval'];
        $evaluacion['items'][]=$row['items'];
	$evaluacion['observaciones'][]=$row['observaciones'];
        $evaluacion['asignacion'][]=encuentraasignacion($bd2,$row['profesor'],$row['materia'],$row['alumno'],trim($row['unidad']));
        $num++;
     }
mysql_free_result($result);

// 7º) Me pone la asigancion
echo "<p>Máximo: ".max($evaluacion['asignacion'])."</p>";
foreach ($evaluacion['asignacion'] as $key => $valor) {
     $materia = dado_Id($bd2,$evaluacion['materia'][$key],"Materias","tb_asignaturas","idmateria");
     $profesor = cambiarnombre(dado_Id($bd2,$evaluacion['profesor'][$key],"Empleado","tb_profesores","idprofesor"));       
     echo '<div id="presentardatos">';
        echo '<p>'.($key+1).' Prof:'.$profesor.' - Mat:'.$materia.' - Alumno: '.$evaluacion['alumno'][$key].' - Asig: '.$valor.'</p>';
        echo '<p>'.$evaluacion['items'][$key].' - '.$evaluacion['observaciones'][$key].'</p>';
     echo '</div>';  
     if ($valor>0) {
     // escribecadena($bd2,$evaluacion['fecha'][$key],$valor,$evaluacion['alumno'][$key],$evaluacion['eval'][$key],$evaluacion['items'][$key],iconv("ISO-8859-1","UTF-8", $evaluacion['observaciones'][$key]));
     escribecadena($bd2,$evaluacion['fecha'][$key],$valor,$evaluacion['alumno'][$key],$evaluacion['eval'][$key],$evaluacion['items'][$key],'<p>'.htmlentities($evaluacion['observaciones'][$key]).'</p>');
     }
}


// ***********************************************
// VOY POR AQUI **********************************
// ***********************************************

// 8º) Inserta nueva evaluación, en la tabla nueva...


 ?>
</div>

</body>
</html>

<?php
// Según profesor, materia, alumno y/o unidad encuentra la asignación correspondiente
function encuentraasignacion ($based, $pro,$mat,$alumno,$unidad) {
$Sql2='SELECT idasignacion,datos FROM tb_asignaciones WHERE profesor="'.$pro.'" AND materia="'.$mat.'"';
$link2=Conectarse($based);
$result2=mysql_query($Sql2,$link2);
$asig=0;
  while ($row2=mysql_fetch_array($result2)) {
        $dentrode=array();
        $dentrode=explode("#",$row2['datos']);
        if (in_array($alumno,$dentrode) || in_array(trim($unidad),$dentrode)) {  
            $asig=$row2['idasignacion'];
        }
        unset($dentrode);
  }
mysql_free_result($result2);
return $asig;
}





// la función obtiene una cadena y retorna los grupos
function distintosgrupos($based,$cadena) {
  //0.- acondiciona cadena
  if (substr($cadena,-1)=="#") { $cadena=substr($cadena,0,strlen($cadena)-1);}
  //1.- obtiene el array grupos con cada alumno y su grupo
  $alumnado=array();
  $grupo=array();
  $grupos=array();
  $alumnado=explode("#",$cadena);
  $contar=0;
  foreach ($alumnado as $valor) {
      $grupo['grupo'][]=dado_Id($based,$valor,"unidad","tb_alumno","idalumno");
      $grupo['alumno'][]=$valor;
      $contar++;
  }
  // 2º) Por cada grupo, obtiene el número de miembros
  $contar=array_count_values($grupo['grupo']); // selecciona los distintos $key->Nombre del grupo $valor->cuenta
  foreach ($contar as $key=>$valor) {
	$grupos['grupo'][]=$key;
        $grupos['numalu'][]=$valor;
        $grupos['totalalu'][]=totalalumnos($based,$key); // número total de alumnos de la unidad              
  } 
  // 3º) Devuelve cadenas.1º) Grupos completos
  $retorna="";
  foreach ($grupos['grupo'] as $key => $valor) {
      // echo '<p>'.$valor.' - '.$grupos['numalu'][$key].' -'.$grupos['totalalu'][$key].'</p>';
      if (abs($grupos['numalu'][$key]-$grupos['totalalu'][$key])<5) { $retorna.=$valor."***";}
      else { // ¿QUE HAGO AQUI PARA OBTENER CADENAS DE GRUPOS DISTINTOS?
      foreach ($grupo['alumno'] as $key2 =>$valor2) {
         if ($grupo['grupo'][$key2]==$valor)  { $retorna.=$valor2."#"; }
         } // por cada alumno QUE NO cumpla la condición
      $retorna=substr($retorna,0,strlen($retorna)-1)."***"; // quita la última almohadilla y pone los tres asteriscos
      } // fin del if que comprueba la diferencia de alumnos
  } //fin de lforeach que recorre los distintos grupos
  return $retorna;
}


function totalalumnos($based,$unidad) {
   $link=Conectarse($based);
   $Sql='SELECT DISTINCT alumno FROM tb_alumno WHERE unidad="'.trim($unidad).'"';
   $result=mysql_query($Sql,$link);
   $num = mysql_num_rows($result);
   mysql_free_result($result);
   return $num;
}


?>
