<?php
include_once("../funciones/seguridad/mysql_inc.php");
include_once("../funciones/adodb_time_inc.php");
include_once("../funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("../clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

if ($_POST['lee']<>'') {
      // $_POST['lee']-> en esta variable se guarda el dato de la clase escogida
      $ID=$_POST['lee'];
      // $alumno=obtenerinformacionalumno($bd,$ID) ;
      $nomapell=iconv("ISO-8859-1","UTF-8",dado_Id($bd,$ID,"alumno","tb_alumno","idalumno"));
      $palabras = preg_split('/,/', $nomapell);
      if (count($palabras)==1) { $nombre="$nomapell"; $apell=""; }
      elseif (count($palabras)==2) {
        $nombre=trim($palabras[1]);
        $apell=trim($palabras[0]);
      } else { $nombre=""; $apell=""; }
      $datos_json[]='"nombre":"'.$nombre.'"';
      $datos_json[]='"apellidos":"'.$apell.'"';
      $datos_json[]='"unidad":"'.dado_Id($bd,$ID,"unidad","tb_alumno","idalumno").'"';
      // echo '<p>'.iconv("ISO-8859-1", "UTF-8",  $cadena).'</p>';
      echo "{".implode(",", $datos_json)."}"; 
      // echo $ID;
} else {
  // echo 'No tienes nada';
}
?>

<?php 
// pequeña función que cue


?>
