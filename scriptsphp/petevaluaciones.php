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
      $Sql="SELECT DISTINCT ideval,nombreeval FROM tb_edicionevaluaciones ORDER BY ideval";
      $ii=0;
      $result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result
      while ($row=mysql_fetch_array($result)) {
	$conjunto['ideval'][$ii]=$row['ideval'];
	$conjunto['nombreeval'][$ii]=$row['nombreeval'];
		// echo 'parametro: '.$ii.' '.$conjunto['idmateria'][$ii];
	$ii++;
	}
      mysql_free_result($result);
      // Una vez obtenidas las paso a json_encode
      $i=0;
      foreach ($conjunto['ideval'] as $key => $valor) {
          $cadena['ideval'][$i]=$valor;
          $cadena['nombreeval'][$i]=iconv("ISO-8859-1","UTF-8",$conjunto['nombreeval'][$key]);
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
