<?php
include_once("../funciones/seguridad/mysql_inc.php");
include_once("../funciones/adodb_time_inc.php");
include_once("../funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("../clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

if ($_POST['lee']<>'') {
      // $_POST['lee']-> en esta variable se guarda el dato de la clase escogida
      $conjunto=array();
      $link=Conectarse($bd); // y me conecto. //dependiendo del tipo recupero uno u otro.
      $Sql='SELECT * FROM tb_profesores WHERE idprofesor>0 ORDER BY Empleado';
      $ii=0;
      $result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result
      while ($row=mysql_fetch_array($result)) {
	$conjunto['idprofesor'][$ii]=$row['idprofesor'];
	$conjunto['Empleado'][$ii]=$row['Empleado'];
	$conjunto['DNI'][$ii]=$row['DNI'];
	$conjunto['IDEA'][$ii]=$row['IDEA'];
	$conjunto['tutorde'][$ii]=$row['tutorde'];
		// echo 'parametro: '.$ii.' '.$conjunto['idmateria'][$ii];
	$ii++;
	}
      mysql_free_result($result);
      // Una vez obtenidas las paso a json_encode
      $i=0;
      foreach ($conjunto['idprofesor'] as $key => $valor) {
          $cadena['idprofesor'][$i]=$valor;
          $cadena['Empleado'][$i]=iconv("ISO-8859-1","UTF-8",$conjunto['Empleado'][$key]);
          $cadena['DNI'][$i]=$conjunto['DNI'][$key];
          $cadena['IDEA'][$i]=$conjunto['IDEA'][$key];
          $cadena['tutorde'][$i]=$conjunto['tutorde'][$key];
          $i++;
      }
      // echo '<p>'.iconv("ISO-8859-1", "UTF-8",  $cadena).'</p>';
      // echo "{".implode(",", $datos_json)."}";
      echo json_encode($cadena); 
      // echo $_POST['lee'];

} else {
  // echo 'No tienes nada';
}
?>
