<?php
include_once("../funciones/seguridad/mysql_inc.php");
include_once("../funciones/adodb_time_inc.php");
include_once("../funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("../clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

if ($_POST['lee']<>'') {
      // $_POST['lee']-> en esta variable se guarda el dato de la clase escogida
      $ID=$_POST['lee'];
      // $datos=obtenerinformacionalumno($bd,$ID);
      $mostrar = iconv("ISO-8859-1","UTF-8",dado_Id($bd,$ID,"Materias","tb_asignaturas","idmateria"))." (".dado_Id($bd,$ID,"abr","tb_asignaturas","idmateria").")";
      // Borrado de ese dato
      $Sql='DELETE FROM tb_asignaturas WHERE idmateria="'.$ID.'"';
      $link=Conectarse($bd);
      $result=mysql_query($Sql,$link); //ejecuta la consulta
      mysql_free_result($result);
      // echo '<p>'.iconv("ISO-8859-1", "UTF-8",  $cadena).'</p>'; 
      echo "Dato borrado: ".$mostrar; 
      // echo $ID;
} else {
  // echo 'No tienes nada';
}
?>
