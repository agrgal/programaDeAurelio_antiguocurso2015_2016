<?php
include_once("../funciones/seguridad/mysql_inc.php");
include_once("../funciones/adodb_time_inc.php");
include_once("../funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("../clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

if ($_POST['lee']<>'') {
    $profesor=$_POST['lee']; // asigna profesor   
    $asignacion=array();
    $cadena=array();
    $link=Conectarse($bd);
    if ($profesor>0) {$Sql='SELECT * FROM tb_asignaciones WHERE profesor="'.$profesor.'"'; }  //caso normal
    if ($profesor==0) {$Sql='SELECT * FROM tb_asignaciones ORDER BY profesor,descripcion'; }  // caso de administración 
    $result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result
	$ii=0; // contador 
	while ($row=mysql_fetch_array($result)) {
		$asignacion['idasignacion'][$ii]=$row['idasignacion'];		
                $asignacion['materia'][$ii]=$row['materia'];
		$asignacion['descripcion'][$ii]=$row['descripcion'];
		$asignacion['datos'][$ii]=$row['datos'];
		$asignacion['tutorada'][$ii]=$row['tutorada'];
                $asignacion['profesor'][$ii]=$row['profesor'];
                $ii++;
		}
    mysql_free_result($result); 
    // 2º paso, codificar json_encode
    $ii=0;
    foreach ($asignacion['idasignacion'] as $key => $valor) {
       $cadena[$ii]['idasignacion']=$valor;       
       $mat=iconv("ISO-8859-1", "UTF-8",dado_Id($bd,$asignacion['materia'][$key],'Materias','tb_asignaturas','idmateria'));
       $cadena[$ii]['materia']=$mat;
       $cadena[$ii]['idmateria']=$asignacion['materia'][$key];
       $cadena[$ii]['descripcion']=$asignacion['descripcion'][$key];
       $cadena[$ii]['datos']=convierte($bd,$asignacion['datos'][$key]);
       // $cadena[$ii]['datos']=$asignacion['datos'][$key];
       if ($asignacion['tutorada'][$key]==1) {$tut="SI";} else {{$tut="NO";}}       
       $cadena[$ii]['tutorada']=$tut;
       // profesor
       $pro=iconv("ISO-8859-1", "UTF-8",dado_Id($bd,$asignacion['profesor'][$key],'Empleado','tb_profesores','idprofesor'));
       $cadena[$ii]['profesor']=$pro;  
       $cadena[$ii]['idprofesor']=$asignacion['profesor'][$key];  
       unset($tut); unset($mat); unset($pro);
       $ii++;
    }

   // dado_Id($bd,'8','Materias','tb_asignaturas','idmateria');
    if (!is_null($cadena)) { // si no lo recupera, el valor por defecto)
	   echo json_encode($cadena);
           // echo $mira; //envia el valor dado
           } else {
	   echo "";
    }
    
     
} else {
  // echo 'No tienes nada';
}


function convierte($bd,$datos) {
   $cadadato=explode("#",$datos);
   $unidad=obtenercursos($bd);
   $devuelve="";
   foreach($cadadato as $valor) {
     $k=in_array(trim($valor),$unidad['unidad'],true);
     // $devuelve.=$valor.'---'.$k.'#';
     if($k) {
        $devuelve.=$valor."---".$valor."#";
        // $devuelve.="cadena".$valor."#";        
     } else {
        $nm=iconv("ISO-8859-1", "UTF-8",dado_Id($bd,$valor,'alumno','tb_alumno','idalumno'));
	$unidad2=iconv("ISO-8859-1", "UTF-8",dado_Id($bd,$valor,'unidad','tb_alumno','idalumno'));
        $devuelve.=$nm." (".$unidad2.") [".$valor."]---".$valor."#";
        unset($nombre); unset($unidad2);
        // $devuelve.="numero".$valor."#";
     }
   } // fin del for 
   
   $devuelve=substr($devuelve,0,strlen($devuelve)-1);
   return $devuelve;
}


