<?php
header("Content-type: application/vnd.oasis.opendocument.spreadsheet; name='openoffice calc'");
header("Content-Disposition: filename=descargaficherohojadecalculo.ods");
header("Pragma: no-cache");
header("Expires: 0");

echo $_POST['datos_a_enviar'];
?>
