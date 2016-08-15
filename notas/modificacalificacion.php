<?php
include_once("../funciones/seguridad/mysql_inc.php");
include_once("../funciones/adodb_time_inc.php");
include_once("../funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("../clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

   // Comprueba que el id de calificación es correcto 
   $idcalificacion = checkcalificacion($bd,$_POST["asignacion"],$_POST["alumno"],$_POST["evaluacion"]);

   if ($idcalificacion===$_POST["idcalificacion"] && !is_null($idcalificacion)){
     // Se supone que no es nulo, o sea, existe. Cadena de actualización...
     $Sql="UPDATE tb_miscalificaciones SET ";
     $Sql.="notaseneca='".$_POST["notaseneca"]."', ";	
     $Sql.="notarecuperacion='".$_POST["notarecuperacion"]."', ";	
     $Sql= substr($Sql,0,strlen($Sql)-2); 
     $Sql.=" WHERE idcalificacion='".trim($idcalificacion)."'"; // fin de la cadena SQL del UPDATE
     $link=Conectarse($bd);
     $result=mysql_query($Sql,$link); //ejecuta la consulta
     mysql_free_result($result); // guarda una calificación por defecto   
     // Envía comentario...
     $cadena = aprueba($_POST["notamedia"],$_POST["notaseneca"],$_POST["notarecuperacion"]);
     // echo $Sql;
     echo $cadena;
   } else {
     echo "Incongruencia en los datos. Nada se ha cambiado: ".$idcalificacion."===".$_POST["idcalificacion"];
   } 


// comprueba si existe una calificacion
function checkcalificacion($bd,$asignacion,$alumno,$evaluacion) {
  $Sql="SELECT idcalificacion FROM tb_miscalificaciones WHERE asignacion='".$asignacion."' AND alumno='".$alumno."' AND evaluacion='".$evaluacion."'";
  $link=Conectarse($bd);
  $result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result
  $row=mysql_fetch_array($result);
  mysql_free_result($result);
  if ($row["idcalificacion"]>0 && !empty($row["idcalificacion"]) && !is_null($row["idcalificacion"])) { // Existe el valor y no es cero...
     return $row["idcalificacion"];	
  } else { return NULL;}
} // Fin de la función 

// Retorna un valor de cadena con un recordatorio de cómo ha ido la evaluación
function aprueba($notamedia,$notaseneca,$notarecuperacion) {
  $cad = "";
  // $cad.= $notamedia." - ".$notaseneca." - ".$notarecuperacion;

  if (($notamedia<>$notaseneca) && ($notaseneca>=5)) { $cad.="Aprobaste al alumno/a con un ".$notaseneca.", pero modificaste la nota media (".$notamedia.") "; }

  if (($notamedia<>$notaseneca) && ($notaseneca<5)) { $cad.="Suspendiste al alumno/a con un ".$notaseneca.", pero modificaste la nota media (".$notamedia."). "; }

  if (($notaseneca>=5) && ($notarecuperacion==0)) { $cad.="Aprobaste definitivamente al alumno/a en convocatoria ordinaria con un ".$notaseneca; }

  if (($notaseneca<5) && ($notarecuperacion==0)) { $cad.="Suspendiste al alumno/a en convocatoria ordinaria con un ".$notaseneca.". O no ha recuperado o no se ha presentado ".$notarecuperacion; }

  if (($notaseneca<5) && ($notarecuperacion>=5)) { $cad.="Suspendiste al alumno/a en convocatoria ordinaria con un ".$notaseneca." y ha recuperado en convocatoria extraordinaria con un ".$notarecuperacion; }

  if (($notaseneca<5) && ($notarecuperacion<5) && ($notarecuperacion>0)) { $cad.="Suspendiste al alumno/a en convocatoria ordinaria con un ".$notaseneca." y no ha recuperado en convocatoria extraordinaria; ha sacado un ".$notarecuperacion; }
  return $cad;
}

?>
