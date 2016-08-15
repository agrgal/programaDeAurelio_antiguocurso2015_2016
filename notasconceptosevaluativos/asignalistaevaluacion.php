<?php
include_once("../funciones/seguridad/mysql_inc.php");
include_once("../funciones/adodb_time_inc.php");
include_once("../funciones/funciones.php"); /* incluye el directorio de funciones */
include_once("../clases/class.micalendario.php");

        session_start(); /* empiezo una sesión */
        
        $_SESSION['listaevaluacion']=$_POST['listaevaluacion'];

        echo 'hecho'.$_SESSION['listaevaluacion'];

?>
