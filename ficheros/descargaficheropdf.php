<?php
$enlace=$_GET['fichero'];
// $ruta=$_GET['ruta'];
// $enlace='.'.chr(47).$ruta.chr(47).$nombre;
header("Content-Disposition: attachment; filename=\"".$enlace."\"");
header ("Content-Type: application/pdf");
// header ("Content-Type: application/octet-stream");
// header ("Content-Type: binary");
header ("Content-Length: ".filesize($enlace));
readfile($enlace);
unlink($enlace);
?>
