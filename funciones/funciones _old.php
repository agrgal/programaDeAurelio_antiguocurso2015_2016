<?php 
/* ****************************************************************
Incluyo una función con los datos de conexión
****************************************************************** */
include_once("./funciones/seguridad/mysql_inc.php");

/* ****************************************************************
Incluyo una función con funciones de adodb-time
****************************************************************** */
include_once("./funciones/adodb_time_inc.php");

/* ****************************************************************
Incluye la clase binario
****************************************************************** */
include_once("./clases/class_binario.php");

/* ****************************************************************
Funciones temporales necesarias para el cálculo
****************************************************************** */
include_once("./funciones/temporales.php");

/* ****************************************************************
Esta función crea una base de datos dada
****************************************************************** */
function crear($basededatos)
{ // para conectarse a una base de datos
global $mysql_server, $mysql_login, $mysql_pass; // defino variables como globales
if
// (!($link=mysql_connect("","pepe","pepa")))
(!($link=mysql_connect($mysql_server,$mysql_login,$mysql_pass)))
{
echo "Error conectando a la base de datos.";
exit();
}
// Crear la base de datos
$Sql="CREATE DATABASE IF NOT EXISTS ".$basededatos;
$result=mysql_query($Sql,$link);
if (mysql_errno($link)<>0) {
		echo '<p>Error: '.mysql_errno($link)." - ".mysql_error($link).'</p>';
		echo '<p>Base de datos no ha podido ser creada. Fin del Script.
		  Deberías ponerte en contacto con el administrador del 
		  sistema para resolver este problema.</p>';
		die(); // termina el script.
	} else {
		echo '<p>Base de datos creada con éxito</p>';
	}

return 0;
}


/* ****************************************************************
Esta función conecta a una base de datos en concreto
****************************************************************** */
function Conectarse($basededatos)
{ // para conectarse a una base de datos
global $mysql_server, $mysql_login, $mysql_pass; // defino variables como globales
if
// (!($link=mysql_connect("","pepe","pepa")))
(!($link=mysql_connect($mysql_server,$mysql_login,$mysql_pass)))
{
echo "<p>Error conectando a la base de datos. Datos incorrectos de servidor, login o contraseña</p>";
exit();
}
if (!mysql_select_db($basededatos,$link)) //base de datos:conexión
{
echo "<p>Error cuando selecciono la base de datos. No existe esa base de datos.</p>";
exit();
}
return $link;
}

/* ****************************************************************
Esta función recupera el valor de un campo en concreto...
****************************************************************** */
function dado_Id($basededatos,$Id,$tipo,$tabla,$nombreid) 
	{ // Recupera el valor de la base de datos
	$link=Conectarse($basededatos); // y me conecto. //dependiendo del tipo recupero uno u otro.
	$Sql="SELECT ".$tipo." from ".$tabla." WHERE ".$nombreid."='".$Id."'";
	// echo $Sql;
        $result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result
	$row=mysql_fetch_array($result);
	if (!is_null($row[$tipo]) or !empty($row[$tipo])) { // si no lo recupera, el valor por defecto)
					  return $row[$tipo]; //envia el valor dado
					  } else {
				      return NULL;
					  }
	mysql_free_result($result);
	}
	
/* ****************************************************************
Esta función recupera una clase en concreto
****************************************************************** */
function obtenerclase($basededatos,$unidad) 
	{ // Recupera el valor de la base de datos
	$clase=array();
        $link=Conectarse($basededatos); // y me conecto. //dependiendo del tipo recupero uno u otro.
	$Sql='SELECT idalumno, alumno FROM tb_alumno WHERE unidad="'.trim($unidad).'" ORDER BY alumno';
        // echo $Sql; 
	$result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result
	$ii=0; // contador 
	while ($row=mysql_fetch_array($result)) {
		$clase['idalumno'][$ii]=$row['idalumno'];
		$clase['alumno'][$ii]=$row['alumno'];
		$ii++;
		}
        if (!is_null($clase)) { // si no lo recupera, el valor por defecto)
	   return $clase; //envia el valor dado
	   } else {
	   return NULL;
	}
	mysql_free_result($result);
	}

/* ****************************************************************
Esta función recupera los items
****************************************************************** */
function vaciaritemsnulos ($basededatos) {
    // comprueba si hay items nulos y los vacía
    // echo "comprueba";
    $itemsvacios=array();
    $link=Conectarse($basededatos); // y me conecto. //dependiendo del tipo recupero uno u otro.    
    // $Sql='SELECT id,items,observaciones FROM tb_evaluacion';
    $Sql='SELECT id,items,observaciones FROM tb_evaluacion WHERE items="" AND observaciones=""';
    $result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result
    while ($row=mysql_fetch_array($result)) {
		$itemsvacios['id'][]=$row['id'];
		$ii++;
		// echo $row['id'].'<br>';
	  }	
    mysql_free_result($result);
    // echo $ii;
    // una vez en una cadena, los vacía
    $iv="";
    foreach ($itemsvacios['id'] as $valor) {
	$link=Conectarse($basededatos);
	$Sql='DELETE FROM tb_evaluacion WHERE id="'.$valor.'"';
	// echo $valor.'<br>';
	$result=mysql_query($Sql,$link);
	mysql_free_result($result);
        $iv='Tabla optimizada; borrados datos no relevantes.';
    } 
    return $iv;     
}
/* ****************************************************************
Esta función recupera los items
****************************************************************** */
function obteneritems($basededatos) 
	{ // Recupera el valor de la base de datos
	$conjunto=array();
        $link=Conectarse($basededatos); // y me conecto. //dependiendo del tipo recupero uno u otro.
	$Sql='SELECT * FROM tb_itemsevaluacion ORDER BY grupo,iditem';
	$result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result
	$ii=0; // contador 
	while ($row=mysql_fetch_array($result)) {
		$conjunto['iditem'][$ii]=$row['iditem'];
		$conjunto['item'][$ii]=$row['item'];
		$conjunto['grupo'][$ii]=$row['grupo'];
		$conjunto['positivo'][$ii]=$row['positivo'];
		$ii++;
		}
        if (!is_null($conjunto)) { // si no lo recupera, el valor por defecto)
	   return $conjunto; //envia el valor dado
	   } else {
	   return NULL;
	}
	mysql_free_result($result);
	}

/* ****************************************************************
Esta función recupera los items por $SQL
****************************************************************** */
function obteneritemsporSQL($basededatos,$SQLDame) 
	{ // Recupera el valor de la base de datos
	$conjunto=array();
        $link=Conectarse($basededatos); // y me conecto. //dependiendo del tipo recupero uno u otro.
	$Sql=$SQLDame;
	$result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result
	$ii=0; // contador 
	while ($row=mysql_fetch_array($result)) {
		$conjunto['iditem'][$ii]=$row['iditem'];
		$conjunto['item'][$ii]=$row['item'];
		$conjunto['grupo'][$ii]=$row['grupo'];
		$conjunto['positivo'][$ii]=$row['positivo'];
		$ii++;
		}
        if (!is_null($conjunto)) { // si no lo recupera, el valor por defecto)
	   return $conjunto; //envia el valor dado
	   } else {
	   return NULL;
	}
	mysql_free_result($result);
	}

/* **********************************************************************************************
Esta función recupera las distintas asignaciones que tienen un conjunto de alumnos una evaluacion
************************************************************************************************** */
// en esta variante se obtienen los datos introducidos de los alumnos de mi evaluación, nada más
// si alguien no ha escrito algo de mis alumnos, no aparece como asignación
function obtenerasignaciones($basededatos,$alumnos,$eval) {
  $asignaciones=array(); // array de asignaciones
  foreach ($alumnos as $clave => $valor) {
       $link=Conectarse($basededatos); // y me conecto. //dependiendo del tipo recupero uno u otro. 
       $Sql='SELECT tb_evaluacion.asignacion FROM tb_evaluacion WHERE tb_evaluacion.alumno="'.$valor.'" AND tb_evaluacion.eval="'.$eval.'"';
       $result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result
       while ($row=mysql_fetch_array($result)) {
		$asignaciones[]=$row['asignacion'];             
		}        
       mysql_free_result($result);	
  } // fin del foreach
  $asignaciones=array_unique($asignaciones); //comprueba repetidos 
  sort($asignaciones);
  return $asignaciones;
}

// para esta variante, sin necesidad de introducir un dato...
// busca la asignación y la cruza con la de tutoría.
function obtenerasignacionesdos($basededatos,$alumnos) {
  // 1º) obtiene las distintas asignaciones que existen
  $conjuntoasignaciones=array();	
  $link=Conectarse($basededatos); // y me conecto. //dependiendo del tipo recupero uno u otro.
  $Sql='SELECT DISTINCT tb_asignaciones.idasignacion FROM tb_asignaciones ORDER BY tb_asignaciones.idasignacion';
  $result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result
  $ii=0;
  while ($row=mysql_fetch_array($result)) {
		$conjuntoasignaciones['idasignacion'][$ii]=$row['idasignacion'];
		$ii++;
		}
  mysql_free_result($result);
  // 2º) Por cada valor de la asignacion, recupera los alumnos
  $envia=array();
  $cruce=array();
  $conjuntoalumnos=array();
  foreach ($conjuntoasignaciones['idasignacion'] as $valor) {
    // 2a.- obtiene el conjunto de alumnos de esa asignacion
    $conjuntoalumnos=obteneralumnosasignacion($basededatos,$valor);
    // 2b.- crúzalo con el array de alumnos
    $cruce=array_intersect($conjuntoalumnos['idalumno'],$alumnos);
    // 2c.- añade la asignacion a la variable envia si el cruce es positivo
    if (count($cruce)>0) { $envia[]=$valor;}
  } // fin del foreach
  $envia=array_unique($envia); //comprueba repetidos
  sort($envia); //ordena
  return $envia;
}

/* ********************************************************************
Esta función recupera los distintos cursos
********************************************************************** */
function obtenercursos($basededatos) {
	$conjunto=array();	
	$link=Conectarse($basededatos); // y me conecto. //dependiendo del tipo recupero uno u otro.
	$Sql='SELECT DISTINCT tb_alumno.unidad FROM tb_alumno ORDER BY tb_alumno.unidad';
	// echo $Sql;
	$ii=0;
	$result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result
	while ($row=mysql_fetch_array($result)) {
		$conjunto['unidad'][$ii]=$row['unidad'];
		// echo 'parametro: '.$ii.' '.$conjunto['idmateria'][$ii];
		$ii++;
		}
	mysql_free_result($result);
        if (!is_null($conjunto)) { // si no lo recupera, el valor por defecto)
	   return $conjunto; //envia el valor dado
	   } else {
	   return NULL;
	}

}

/* ********************************************************************
Esta función recupera los alumnos de una asignación
********************************************************************** */
function obteneralumnosasignacion($basededatos,$asignacion) {
        $unidad=obtenercursos($basededatos);
        $dato=dado_Id($basededatos,$asignacion,"datos","tb_asignaciones","idasignacion");
        $datos=explode("#",$dato); // consigue un vector con la asignación
        $clase=array();
        $resultado=array();
        foreach($datos as $key => $valor) {
            $k=array_search($valor,$unidad['unidad']);
            if($k>0) { // en caso que encuentre una clase
               $clase=obtenerclase($basededatos,$valor); //obtiene la clase
               $resultado=array_merge($resultado,$clase['idalumno']); //anexiona a resultado los valores de la clase
            } else { 
               $resultado[]=$valor;
            }
        } // fin del foreach
        // comprobar repetidos y ordenar
        $resultado=array_unique($resultado); //comprueba repetidos
        asort($resultado); //ordena
        //Reordena las claves del array
        $resultado2=array();
        $ii=0;
        foreach($resultado as $valor) {
           $resultado2['idalumno'][$ii]=$valor;
           $resultado2['alumno'][$ii]=dado_Id($basededatos,$valor,"alumno","tb_alumno","idalumno");
           $resultado2['unidad'][$ii]=dado_Id($basededatos,$valor,"unidad","tb_alumno","idalumno");
           $ii++;
        }
        unset($resultado); 
        // añade cadena de clases
	$clases=$resultado2['unidad']; // array con las clases
        $clases=array_unique($clases); //comprueba repetidos
        asort($clases); //ordena
        $retorna=""; // cadena donde se almacenarán los datos
        foreach ($clases as $valor) {
           $retorna.=$valor." - ";
        }
        $resultado2['cadenaclases']=substr($retorna,0,strlen($retorna)-3); // Quita tres últimos caracteres...      
        // enviar resultados
        return $resultado2;
}


/* ********************************************************************
Esta función recupera los datos de una asignacion
********************************************************************** */
function obtenerdatosasignacion($bd,$indice) {
   $profesor = dado_Id($bd,dado_Id($bd,$indice,"profesor","tb_asignaciones","idasignacion"),"Empleado","tb_profesores","idprofesor");
   $email=dado_Id($bd,dado_Id($bd,$indice,"profesor","tb_asignaciones","idasignacion"),"email","tb_profesores","idprofesor");
   $materia = dado_Id($bd,dado_Id($bd,$indice,"materia","tb_asignaciones","idasignacion"),"Materias","tb_asignaturas","idmateria");
   $descripcion = iconv("UTF-8","ISO-8859-1",dado_Id($bd,$indice,"descripcion","tb_asignaciones","idasignacion"));
   $tutorada=dado_Id($bd,$indice,"tutorada","tb_asignaciones","idasignacion"); 
   $cadena=array();
   $cadena["profesor"]=$profesor;
   $cadena["materia"]=$materia;
   $cadena["descripcion"]=$descripcion;
   $cadena["email"]=$email;
   $cadena["idprofesor"]=dado_Id($bd,$indice,"profesor","tb_asignaciones","idasignacion");
   $cadena["idmateria"]=dado_Id($bd,$indice,"materia","tb_asignaciones","idasignacion");
   if ($tutorada==1) {$cadena["tutorada"]=" (Tutoría) "; } else { $cadena["tutorada"]="";  }
   return $cadena;
} 


/* ****************************************************************
Esta función recupera los grupos de los items
****************************************************************** */
function obtenergrupos($basededatos,$pn) 
	{ // Recupera el valor de la base de datos
	$conjunto=array();
        $link=Conectarse($basededatos); // y me conecto. //dependiendo del tipo recupero uno u otro.
	if ($pn==0) 
            {$Sql='SELECT DISTINCT grupo FROM tb_itemsevaluacion WHERE positivo<=1 ORDER BY grupo';}
        else if ($pn>1) 
            {$Sql='SELECT DISTINCT grupo FROM tb_itemsevaluacion WHERE positivo>1 ORDER BY grupo';}
	// echo $Sql;
        $result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result
	$ii=0; // contador 
	while ($row=mysql_fetch_array($result)) {
		$conjunto['grupo'][$ii]=$row['grupo'];
		$ii++;
		}
        if (!is_null($conjunto)) { // si no lo recupera, el valor por defecto)
	   return $conjunto; //envia el valor dado
	   } else {
	   return NULL;
	}
	mysql_free_result($result);
	}

/* ****************************************************************
Esta función escribe o actualiza cada registro de evaluación
****************************************************************** */
function escribecadena($basededatos,$fecha,$asignacion,$alumno,$eval,$cadena,$obs) {
     $item=recuperacadena($basededatos,$asignacion,$alumno,$eval); // items
     $id=recuperacadenaid($basededatos,$asignacion,$alumno,$eval); // cadena 
     if (is_null($item)) { // escribe un nuevo dato INSERT
	$Sql="INSERT INTO tb_evaluacion (fecha, asignacion, alumno, eval, items, observaciones) VALUES (";
	if ($fecha<>'') {$Sql.="'".$fecha."', ";} else {$Sql.="'-',";}
	if ($asignacion<>'') {$Sql.="'".$asignacion."', ";} else {$Sql.="'-',";}
	if ($alumno<>'') {$Sql.="'".$alumno."', ";} else {$Sql.="'-',";}
	if ($eval<>'') {$Sql.="'".$eval."', ";} else {$Sql.="'-',";}
	if ($cadena<>'') {$Sql.="'".$cadena."', ";} else {$Sql.="'', ";}
	if ($obs<>'') {$Sql.="'".$obs."', ";} else {$Sql.="'', ";}
	$Sql=substr($Sql,0,strlen($Sql)-2); /* Quitar la última coma */
	$Sql.=")"; 	
     } else { // si no, hacemos un UPDATE
	$Sql="UPDATE tb_evaluacion SET ";
	$Sql.="fecha='".$fecha."', ";	
	$Sql.="asignacion='".$asignacion."', ";
	$Sql.="alumno='".$alumno."', ";
	$Sql.="eval='".$eval."', ";
	$Sql.="items='".$cadena."', ";
	$Sql.="observaciones='".$obs."', ";
	$Sql= substr($Sql,0,strlen($Sql)-2); /* Quitar la última coma */
	$Sql.=" WHERE id='".$id."'"; 
     } // fin del if
     if (!((is_null($cadena) || empty($cadena)) && is_null($item)) || !(is_null($obs) || empty($obs))) {
     // if (!((is_null($cadena) || empty($cadena)) && is_null($item))) {
        // echo $Sql;
	$link=Conectarse($basededatos);
	$result=mysql_query($Sql,$link); //ejecuta la consulta
	mysql_free_result($result);
     } // si ambos son vacios no escribir nada

} //fin de la función que escribe una cadena

/* ****************************************************************
Esta función recupera un item, dado el resto de los parámetros
****************************************************************** */
function recuperacadena($basededatos,$asignacion,$alumno,$eval) {
	$link=Conectarse($basededatos); // y me conecto. //dependiendo del tipo recupero uno u otro.
	$Sql='SELECT items FROM tb_evaluacion WHERE asignacion="'.$asignacion.'" AND alumno="'.$alumno.'" AND eval ="'.$eval.'"';
	$result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result
	$row=mysql_fetch_array($result);
	if (!is_null($row['items']) or !empty($row['items'])) { // si no lo recupera, el valor por defecto)
					  return $row['items']; //envia el valor dado
					  } else {
				      return NULL;
					  }
        mysql_free_result($result);
}

/* ****************************************************************
Esta función recupera una observacion, dado el resto de los parámetros
****************************************************************** */
function recuperaobservaciones($basededatos,$asignacion,$alumno,$eval) {
	$link=Conectarse($basededatos); // y me conecto. //dependiendo del tipo recupero uno u otro.
	$Sql='SELECT observaciones FROM tb_evaluacion WHERE asignacion="'.$asignacion.'" AND alumno="'.$alumno.'" AND eval ="'.$eval.'"';
	$result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result
	$row=mysql_fetch_array($result);
	if (!is_null($row['observaciones']) or !empty($row['observaciones'])) { // si no lo recupera, el valor por defecto)
					  return $row['observaciones']; //envia el valor dado
					  } else {
				      return NULL;
					  }
	mysql_free_result($result);
}
/* ****************************************************************
Esta función recupera un id de un item, dado el resto de los parámetros
****************************************************************** */
function recuperacadenaid($basededatos,$asignacion,$alumno,$eval) {
	$link=Conectarse($basededatos); // y me conecto. //dependiendo del tipo recupero uno u otro.
	$Sql='SELECT id FROM tb_evaluacion WHERE asignacion="'.$asignacion.'" AND alumno="'.$alumno.'" AND eval ="'.$eval.'"';
	$result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result
	$row=mysql_fetch_array($result);
	if (!is_null($row['id']) or !empty($row['id'])) { // si no lo recupera, el valor por defecto)
					  return $row['id']; //envia el valor dado
					  } else {
				      return NULL;
					  }
        mysql_free_result($result);
}

/* ****************************************************************
Esta función recupera un item formato array
****************************************************************** */
function recuperacadenaarray($basededatos,$asignacion,$alumno,$eval) {
	$item=recuperacadena($basededatos,$asignacion,$alumno,$eval);
	if (!(is_null($item))) {
	   // rompe la cadena
	   $caso = explode("#",$item); 	
	   return $caso;	
	} else {
	   return NULL;
	}
}

/* ***********************************************************************
Esta función escribe un array para pasar a javascript (positivo, negativo)
************************************************************************** */
function jsarray($basededatos,$agrupacion,$nombre,$positivo) {
	if ($positivo==1) {$p="p";} else {$p="n";}	
        $link=Conectarse($basededatos); // y me conecto. //dependiendo del tipo recupero uno u otro.
	$Sql='SELECT iditem FROM tb_itemsevaluacion WHERE grupo="'.$agrupacion.'" AND positivo="'.$positivo.'" ORDER BY iditem';
	$result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result	
	if (!is_null($result) && !empty($result)) {
		while ($row=mysql_fetch_array($result)) {
		   $script.='"'.$row['iditem'].'",';
		} // fin del while
		$script=strtolower($nombre.$p).'=Array('.substr($script,0,strlen($script)-1).");"; //quita última coma
		return $script;
        } else { return NULL;} 
}

/* ***********************************************************************
Esta función escribe un array para pasar a javascript todo o nada
************************************************************************** */
function jsarraytodonada($basededatos,$agrupacion,$nombre) {
        $link=Conectarse($basededatos); // y me conecto. //dependiendo del tipo recupero uno u otro.
	$Sql='SELECT iditem FROM tb_itemsevaluacion WHERE grupo="'.$agrupacion.'" ORDER BY iditem';
	$result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result	
	if (!is_null($result) && !empty($result)) {
		while ($row=mysql_fetch_array($result)) {
		   $script.='"'.$row['iditem'].'",';
		} // fin del while
		$script=strtolower($nombre).'=Array('.substr($script,0,strlen($script)-1).");"; //quita última coma
		return $script;
        } else { return NULL;} 
}

/* *****************************************************************************
Esta función cambiar el nombre del tipo Apll1 Apll2, Nombre a Nombre y apellidos
*********************************************************************************/
function cambiarnombre($nombre) {
    $palabras = preg_split('/,/', $nombre);
    if (count($palabras)==1) { return $nombre; }
    elseif (count($palabras)==2) {
       return trim($palabras[1]).' '.trim($palabras[0]);
    } else { return NULL; }
}


/* *****************************************************************************
Esta función permite leer el fichero config.txt
*********************************************************************************/
function leerconfig($nombre_fichero) {
$fichero_texto = fopen ($nombre_fichero, "r") or exit("No se encuentra el archivo");
//obtenemos de una sola vez todo el contenido del fichero
//OJO! Debido a filesize(), sólo funcionará con archivos de texto
$cadenas=array();
$resultado=array();
while(!feof($fichero_texto))
{
   $cadenas[]=fgets($fichero_texto);
}
fclose($fichero_texto);
// rompe la cadena si tiene un igual
$dato=array();
foreach ($cadenas as $valor) {
  if(strlen(strstr($valor,"="))>0) { // encuentra un igual
     $dato=explode("=",$valor);
     $resultado[$dato[0]]=$dato[1];
  }
}
return $resultado;
}

/* *****************************************************************************
Esta función permite escribir el fichero config.txt
*********************************************************************************/
function escribirconfig($nombre_fichero,$variable,$valor) {
if (!file_exists($nombre_fichero)) {
  die ('<p>Fichero no encontrado... ¿Dónde está?. Quizás tengas que regenerar el fichero de configuración.</p>');
  return;
}
$fichero_texto = fopen ($nombre_fichero, "r") or exit("No se encuentra el archivo");
// 1º) lo lee
$cadenas=array();
$resultado=array();
while(!feof($fichero_texto))
{
   $cadenas[]=fgets($fichero_texto);
}
fclose($fichero_texto);
// rompe la cadena si tiene un igual
$dato=array();
foreach ($cadenas as $loqueda) {
  if(strlen(strstr($loqueda,"="))>0) { // encuentra un igual
     // echo '<p>'.$loqueda.'</p>'; --> monitoriza lo que va dando
     $dato=explode("=",$loqueda);
     // foreach ($dato as $k => $v) {echo '<p>'.$k."-->".$v.'</p>'; } --> monitoriza lo que va dando
     if ($dato[0]==$variable) {$resultado[$dato[0]]=$valor;} else {$resultado[$dato[0]]=$dato[1];}
     // echo '<p>'.$dato[0]."=".$dato[1].'</p>';
  }
}
// 2º) lo elimina
chmod($nombre_fichero, 0777); // permisos totales para el fichero
unlink($nombre_fichero); // lo borra
// 3º) lo regenera de nuevo
$fichero_texto = fopen ($nombre_fichero, "x");
foreach ($resultado as $key => $valor) {
  // echo '<p>'.$key."=".$valor.'</p>'; --> monitoriza lo que va dando
  fwrite($fichero_texto,$key."=".$valor);
  fwrite($fichero_texto,"\n");
}
fclose($fichero_texto);
// chown($nombre_fichero,"root");
chmod($nombre_fichero, 0755); 
return 0; 
}

/* *****************************************************************************
Esta función permite borrar el fichero config.txt
*********************************************************************************/
function borraconfig($nombre_fichero) {
   // chmod($nombre_fichero, 0777); // permisos totales para el fichero
   unlink($nombre_fichero); // lo borra 
}


// ==========================================
// a partir de aquí, lo de la página anterior
// ==========================================

/* ****************************************************************
Esta función recupera el registro de una clase dado su Id
****************************************************************** */
function registroclase($basededatos,$Id) 
	{ // Recupera el valor de la base de datos
	$reg =NULL;
	$link=Conectarse($basededatos); // y me conecto. 
	$Sql="SELECT clases.Idclases,clases.curso,clases.letra,niveles.nivel,niveles.abr,ies.nombre,ies.ciudad,ies.provincia
		  FROM (clases INNER JOIN niveles ON clases.nivel = niveles.Idnivel)  INNER JOIN ies ON clases.ies = ies.Idies
		  WHERE clases.Idclases=".$Id;
    $result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result
    $row=mysql_fetch_array($result);
	$reg = $row['curso'].$row['abr'].$row['letra'].' - IES '.$row['nombre'].' ('.$row['ciudad'].' - '.$row['provincia'].')';
	return $reg;
	mysql_free_result($result);
	}
	
/* ****************************************************************
Esta función recupera el registro de una clase dado su Id
****************************************************************** */
function registroprofesor($basededatos,$Id) 
	{ // Recupera el valor de la base de datos
	$reg =NULL;
	$link=Conectarse($basededatos); // y me conecto. 
	$Sql="SELECT profesores.nombre,profesores.apll1,profesores.apll2,ies.nombre as iesnombre,ies.ciudad,ies.provincia
		  FROM profesores INNER JOIN ies ON profesores.ies=ies.Idies 
		  WHERE profesores.Idpro=".$Id;
    $result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result
    $row=mysql_fetch_array($result);
	$reg = $row['apll1'].' '.$row['apll2'].', '.$row['nombre'].' - IES '.$row['iesnombre'].' ('.$row['ciudad'].' - '.$row['provincia'].')';
	return $reg;
	mysql_free_result($result);
	}
	
// ************************************************
// buscar identificacion en las tablas...
// ************************************************

function maxdado($basededatos,$tabla,$nombreid) { // Recupera el último dato del indice de la tabla.
	$link=Conectarse($basededatos); // y me conecto. //dependiendo del tipo recupero uno u otro.
	$Sql="SELECT max(".$nombreid.") as valormaximo from ".$tabla;
	$result=mysql_query($Sql,$link);
	$row=mysql_fetch_array($result);
	return $row["valormaximo"];
	mysql_free_result($result);
}

function ultimo($basededatos,$tabla,$nombreid) { // El valor nuevo es el último del grupo.
	$link=Conectarse($basededatos); // y me conecto. //dependiendo del tipo recupero uno u otro.
	$Sql="SELECT max(".$nombreid.") as valormaximo from ".$tabla;
	$result=mysql_query($Sql,$link);
	$row=mysql_fetch_array($result);
	if (!is_null($row["valormaximo"])) {return $row["valormaximo"];} else {return 0;} 
	mysql_free_result($result);
}

function primero($basededatos,$tabla,$nombreid) { // El valor nuevo es el primero del grupo.
	$link=Conectarse($basededatos); // y me conecto. //dependiendo del tipo recupero uno u otro.
	$Sql="SELECT min(".$nombreid.") as valorminimo from ".$tabla;
	$result=mysql_query($Sql,$link);
	$row=mysql_fetch_array($result);
	if (!is_null($row["valorminimo"])) {return $row["valorminimo"];} else {return 0;} 
}

function siguiente($basededatos,$actual,$tabla,$nombreid) { // El valor nuevo es el siguiente
	$link=Conectarse($basededatos); // y me conecto. //dependiendo del tipo recupero uno u otro.
	$Sql="SELECT ".$nombreid." from ".$tabla." WHERE ".$nombreid.">".$actual;
	$result=mysql_query($Sql,$link);
	$row=mysql_fetch_array($result); // recupero la primera fila, que contiene el siguiente valor.
	if (!is_null($row[$nombreid])) {return $row[$nombreid];} else {return $actual;}
	mysql_free_result($result);
}

function anterior($basededatos,$actual,$tabla,$nombreid) { // El valor nuevo es el anterior
	$link=Conectarse($basededatos); // y me conecto. //dependiendo del tipo recupero uno u otro.
	$Sql="SELECT ".$nombreid." from ".$tabla." WHERE ".$nombreid."<".$actual." ORDER BY ".$nombreid." DESC";
	$result=mysql_query($Sql,$link);
	$row=mysql_fetch_array($result); // recupera la primera fila, que contiene el valor anterior.
	if (!is_null($row[$nombreid])) {return $row[$nombreid];} else {return $actual;} 
	mysql_free_result($result);
}

// ************************************************
// buscar identificacion en las tablas...
// Especial para UD, con filtro...
// ************************************************

function maxdado_fil($basededatos,$tabla,$nombreid,$filtro) { // Recupera el último dato del indice de la tabla.
	$link=Conectarse($basededatos); // y me conecto. //dependiendo del tipo recupero uno u otro.
	$Sql="SELECT max(".$nombreid.") as valormaximo from ".$tabla." ".$filtro;
	$result=mysql_query($Sql,$link);
	$row=mysql_fetch_array($result);
	return $row["valormaximo"];
	mysql_free_result($result);
}

function ultimo_fil($basededatos,$tabla,$nombreid,$filtro) { // El valor nuevo es el último del grupo.
	$link=Conectarse($basededatos); // y me conecto. //dependiendo del tipo recupero uno u otro.
	$Sql="SELECT max(".$nombreid.") as valormaximo from ".$tabla." ".$filtro;;
	$result=mysql_query($Sql,$link);
	$row=mysql_fetch_array($result);
	if (!is_null($row["valormaximo"])) {return $row["valormaximo"];} else {return 0;} 
	mysql_free_result($result);
}

function primero_fil($basededatos,$tabla,$nombreid,$filtro) { // El valor nuevo es el primero del grupo.
	$link=Conectarse($basededatos); // y me conecto. //dependiendo del tipo recupero uno u otro.
	$Sql="SELECT min(".$nombreid.") as valorminimo from ".$tabla." ".$filtro;;
	$result=mysql_query($Sql,$link);
	$row=mysql_fetch_array($result);
	if (!is_null($row["valorminimo"])) {return $row["valorminimo"];} else {return 0;} 
}

function siguiente_fil($basededatos,$actual,$tabla,$nombreid,$filtro) { // El valor nuevo es el siguiente
	$filtro=str_replace("WHERE "," and ",$filtro);
	$link=Conectarse($basededatos); // y me conecto. //dependiendo del tipo recupero uno u otro.
	$Sql="SELECT ".$nombreid." from ".$tabla." WHERE ".$nombreid.">".$actual.$filtro;
	$result=mysql_query($Sql,$link);
	$row=mysql_fetch_array($result); // recupero la primera fila, que contiene el siguiente valor.
	if (!is_null($row[$nombreid])) {return $row[$nombreid];} else {return $actual;}
	mysql_free_result($result);
}

function anterior_fil($basededatos,$actual,$tabla,$nombreid,$filtro) { // El valor nuevo es el anterior
	$filtro=str_replace("WHERE "," and ",$filtro);
	$link=Conectarse($basededatos); // y me conecto. //dependiendo del tipo recupero uno u otro.
	$Sql="SELECT ".$nombreid." from ".$tabla." WHERE ".$nombreid."<".$actual.$filtro." ORDER BY ".$nombreid." DESC";
	$result=mysql_query($Sql,$link);
	$row=mysql_fetch_array($result); // recupera la primera fila, que contiene el valor anterior.
	if (!is_null($row[$nombreid])) {return $row[$nombreid];} else {return $actual;} 
	mysql_free_result($result);
}

?>
