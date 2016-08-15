<?php
include_once("../funciones/seguridad/mysql_inc.php");
include_once("../funciones/adodb_time_inc.php");
include_once("../funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("../clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

     $idnota = $_POST["idnota"];
     $ce = $_POST["ce"]; // pasarlo a la base de datos
     $indicadores = $_POST["indicadores"];
     $alumno = $_POST["alumno"];
     $nota = $_POST["nota"];
     $modificadornota = trim($_POST["modificarnota"]);

     if (!empty($indicadores) && $indicadores==="-") {$indicadores=""; } // evita la inclusión de un guión sólo

     if (is_null($idnota) or empty($idnota)) {
        $idnota=checkit($ce,$alumno,$bd); // comprueba, por si acaso, si existe o no una id con esos datos
     }

     // Inserta o edita
     if (empty($ce) || empty($alumno) || (empty($nota) && empty($modificadornota)) ) {
        $devuelve = "Nota o alumno vacío. Introduce datos.";
     } else {
	     if (is_null($idnota) or empty($idnota)) {
		$Sql="INSERT INTO tb_notas (alumno, ce, nota, indicadores, modificadornota) VALUES (";
		if ($alumno<>'') {$Sql.="'".$alumno."', ";} else {$Sql.="'-',";}
		if ($ce<>'') {$Sql.="'".$ce."', ";} else {$Sql.="'-',";}
		if ($nota<>'') {$Sql.="'".$nota."', ";} else {$Sql.="'-',";}
		if ($indicadores<>'') {$Sql.="'".$indicadores."', ";} else {$Sql.="'-',";}
		if ($modificadornota<>'') {$Sql.="'".$modificadornota."', ";} else {$Sql.="'-',";} // N
		$Sql=substr($Sql,0,strlen($Sql)-2); 
		$Sql.=")"; 	
	     } else { // si no, hacemos un UPDATE
		$Sql="UPDATE tb_notas SET ";
		$Sql.="ce='".$ce."', ";	
		$Sql.="alumno='".$alumno."', ";
		$Sql.="indicadores='".$indicadores."', ";
		$Sql.="nota='".$nota."', ";	
		$Sql.="modificadornota='".$modificadornota."', ";
		$Sql= substr($Sql,0,strlen($Sql)-2); 
		$Sql.=" WHERE IDnota='".trim($idnota)."'"; 
	     } // fin del if

             $link=Conectarse($bd);
	     $result=mysql_query($Sql,$link); //ejecuta la consulta
	     mysql_free_result($result);

             $devuelve = "Nota introducida o cambiada."; 
     } // fin del if 

     // $devuelve="He llegado bien";

     echo $devuelve;

function checkit($ce,$alumno,$bd) {
   $Sql="SELECT idnota FROM tb_notas WHERE ce='".$ce."' AND alumno='".$alumno."'";
   $link=Conectarse($bd);
   $result=mysql_query($Sql,$link); //ejecuta la consulta
   $row=mysql_fetch_array($result);
   mysql_free_result($result);
	   if (is_null($row["nota"]) or empty($row["nota"])) {
		return NULL;
  	   } else {
		return $row["nota"];
           }
} // fin de la función de comprobacion

?>
