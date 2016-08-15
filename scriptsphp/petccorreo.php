<?php
include_once("../funciones/seguridad/mysql_inc.php");
include_once("../funciones/adodb_time_inc.php");
include_once("../funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("../clases/class.micalendario.php");
$calendario= New micalendario(); // variable de calendario.

if ($_POST['lee']<>'') {
      // $_POST['lee']-> en esta variable se guarda el dato de la clase escogida
      $datos=array();
      // echo $_POST['lee'];
      $datos=explode('******',$_POST['lee']); // 6 asteriscos. Más difícil que esté en un correo
      $profesor=$datos[0];
      $correo=trim($datos[1]); //quita espacios, por si acaso.

      $filtro=filter_var($correo, FILTER_VALIDATE_EMAIL);
      // echo $filtro;

      if ($filtro<>"") {

      $link=Conectarse($bd); // y me conecto. //dependiendo del tipo recupero uno u otro.
      $Sql="UPDATE tb_profesores SET ";
      $Sql.="email='".$correo."', ";
      $Sql= substr($Sql,0,strlen($Sql)-2); // Quitar la última coma
      $Sql.=" WHERE idprofesor='".$profesor."'"; 
      $result=mysql_query($Sql,$link); // ejecuta la cadena sql y almacena el resultado el $result

      if ($resultado) {
         echo "Lo siento. No se ha modificado el correo electrónico. Error.";
      } else {
         echo "Correo electrónico modificado con éxito.";
      } 

      } else {
	 echo "No has enviado un correo electrónico válido.";
      }
    

      // echo $_POST['lee']; 

} else {
  // echo 'No tienes nada';
}
?>
