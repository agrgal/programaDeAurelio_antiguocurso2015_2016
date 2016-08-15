<?php 
/************************************************************
Calcula una cadena con los d�as no lectivos de forma general.
1 -> No lectivo y 0 -> lectivo.
$bug=1 ==> d�as de la semana (0,1,2..6)
$bug=2 ==> d�as lectivos formateados en cadena hexadecimal.
$bug=0 ==> d�as lectivos...
************************************************************/
function matrizanual ($anno,$bug) {
	// El a�o escolar empezar� el 1 de septiembre de un a�o y acaba 
	// al a�o siguiente el 31 de agosto.
	// 1�) Calculo el d�a de la semana del 1 de septiembre de ese a�o
	$diauno=adodb_gmmktime(0, 0, 0 ,9, 1, $anno); // timestamp del 1 de septiembre...
	$diadelasemana=adodb_date("w",$diauno); // dia de la semana...
	$diadelasemana=($diadelasemana==0)?6:--$diadelasemana;
	// 2�) �Es bisiesto? Calculo el n� de dias del a�o siguiente
	// Coincidir� con que 
	// N� de d�a del a�o del 31 de diciembre del a�o siguiente. +1 porque cuenta de 0 a 364...
	$diasdelanno=adodb_date("z",adodb_gmmktime(0, 0, 0 ,12, 31, $anno+1))+1;
	// 3�) Genera las cadenas con los datos correspondientes...
	// echo $diasdelanno;
	$j=$diadelasemana; // contador del d�a de la semana...
	$ds=""; // inicializo la cadena...
	$lectivos=""; //inicializo la cadena...
	for ($i=1;$i<=$diasdelanno;$i++) { // bucle que recorre los d�as...
		$ds.=$j; // a�ado directamente a la cadena 
		$lectivos.=($j>=5)?1:0; // Si es el d�a 5 (S�bado) o 6 (Domingo) un 1, y si no un 0
		$j=($j==6)?0:$j+1; 
	}
	// 4�) Cadena formateada en hexadecimal lista para guardar...
	$binario = New binario(); // llama a la clase binario...
	$formateada=$binario->binahex($lectivos); // la formatea en hexadecimal
	// $formateada=$binario->hexabin($formateada); // al rev�s para ver que acaba con m�s d�gitos...

	// elige lo que hay que retornar
	switch ($bug) {
		case 0: return $lectivos; break;
		case 1: return $ds; break;
		case 2: return $formateada; break;
	} // fin del switch.
}

/************************************************************************
Dada una clase, un �rea y un a�o, recupera la cadena de sus d�as h�biles.
// $bug = 1 -> retorna una cadena con las horas de cada d�a...
// $bug = 2 -> suma el n�mero de horas que existen.
// $bug = 3 -> retorna el horario...
*************************************************************************/
function rec_habiles ($bd,$clase,$area,$anno,$bug) {
	$Sql="SELECT habiles,diasemana,horario FROM temporizacion ";
	$Sql.=" WHERE clase='".$clase."' and area='".$area."' and anno='".$anno."'";
	//echo '<p>'.$Sql.'</p>';
	$link=Conectarse($bd);
	$result=mysql_query($Sql,$link);
	$row=mysql_fetch_array($result);
	// Llama a la clase binario...
	$binario = New binario(); // llama a la clase binario...
	//echo '<p>'.$binario->hexabin($row['habiles']).'</p>'; 
	//echo '<p>'.$row['diasemana'].'</p>'; 
	if ($bug<=2) {
		return $binario->numerohoras($binario->hexabin($row['habiles']),$row['diasemana'],$anno,$bug);
		} else {
		return $row['horario'];
	}
}

/************************************************************************
Dada una clase, un �rea , un a�o, y un profesor, recupera la cadena de sus d�as h�biles.
// $bug = 1 -> retorna una cadena con la distribucion de UD
// $bug = 2 -> retorna una cadena con la distribucion de actividades
*************************************************************************/
function rec_distribucion ($bd,$profesor, $clase,$area,$anno,$bug) {
	$Sql="SELECT distud,distact FROM distribucion ";
	$Sql.=" WHERE clase='".$clase."' and area='".$area."' and profesor='".$profesor."' and anno='".$anno."'";
	// echo '<p>'.$Sql.'</p>';
	$link=Conectarse($bd);
	$result=mysql_query($Sql,$link);
	$row=mysql_fetch_array($result);
	// Llama a la clase binario...
	// $binario = New binario(); // llama a la clase binario...
	//echo '<p>'.$binario->hexabin($row['habiles']).'</p>'; 
	//echo '<p>'.$row['diasemana'].'</p>'; 
	if ($bug==1) {return $row['distud'];}
	if ($bug==2) {return $row['distact'];}
}

?>