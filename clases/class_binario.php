<?php
//------------------------------------------------ 
class binario {
//------------------------------------------------
/**
 * Autor: Aurelio Gallardo
 * Día: 22/04/2006
 */
	
	// Pasa un número dado de binario a hexadecimal
	// EJEMPLO del uso de binahex
	// $binario=New binario();
	// $cadhex=$binario->binahex($cadena);
	function binahex ($cadena) {
		// trocea la cadena, construye otra en hexadecimal.
		$this->longitud=strlen($cadena);
		$this->tamanno=4*(floor($this->longitud/4)+1);
		for ($i=$this->longitud+1;$i<=$this->tamanno;$i++) {
			$cadena.="0"; //amplío el número de ceros de la cadena...
		}
		$this->longitud=strlen($cadena); // vuelve a calcular la longitud...
		// Divide la cadena en trozos iguales de 4, y va construyendo la cadena hexadecimal
		$i=0; //posiciónde la cadena a comprobar...
		$this->hexadecimal=""; //cadena en hexadecimal.
		while ($i<$this->longitud) {// mientras que el marcador de posición sea inferior a la longitud.
			$this->hexadecimal.=dechex(bindec(substr($cadena,$i,4))); //pasa de binario a decimal y después a hex
			$i=$i+4; // voy viendo el contador de 4 en 4.
		}
		// return $this->hexadecimal; //devuelve la cadena dada.
		return $this->hexadecimal; //devuelve la cadena dada.
		} // Fin de la función
		
	// Pasa un número dado de hexadecimal a binario
	// EJEMPLO del uso de hexabin
	// $binario=New binario();
	// $cadbin=$binario->hexabin($cadena);
	function hexabin($cadena) {
		$i=0; //contador 
		$this->binario=""; //inicializo la cadena
		while ($i<strlen($cadena)) { //si el contador es menor que la cadena
			$this->binario.=sprintf("%04d",decbin(hexdec(substr($cadena,$i,1))));
			// simplemente pasa a decimal cada letra. Se supone la cadena es válida.
			// Formatea el número a 4 digitos biarios. Recordar que en hexadecimal se va
			// de cero a 15. F-->15
			$i++; // aumenta el contador.
		}
		return $this->binario;
		} // Fin de la función


	
	// Damos una cadena binaria con los datos de ocupación...
	// Una fecha de referencia y otra fecha. Me dice si ese día está ocupado o no.
	// IMPORTANTE: Las binario están en formato MySQL (Año, mes, dia).
	// $binario=New binario();
	// $ocupado=$binario->ocupacion($cadena,$fecharef,$fecha,$que);
	// si $que=0 muestra la ocupacion de ese día. 
	// si $que=1 muestra el número de días entre fechas.
	function ocupacion($cadena,$fecharef,$fecha,$que) {
		//Primero, pasa el formato a mes, dia, año.
		$this->mes=substr($fecharef,5,2);
		$this->dia=substr($fecharef,8,2);
		$this->anno=substr($fecharef,0,4);
		$this->mes2=substr($fecha,5,2);
		$this->dia2=substr($fecha,8,2);
		$this->anno2=substr($fecha,0,4);
		
		$this->fecharef=adodb_mktime(0,0,0,$this->mes,$this->dia,$this->anno); // convierte a formato numérico la fecha de referencia
		$this->fechafin=adodb_mktime(0,0,0,$this->mes,$this->dia+strlen($cadena)-1,$this->anno); 
		// convierte a formato numérico la última fecha de la cadena...
		$this->fecha=adodb_mktime(0,0,0,$this->mes2,$this->dia2,$this->anno2); // convierte a formato numérico la fecha a ver la ocupación
		// $calen.= $this->fecharef.'<br>';
		// $calen.= $this->fechafin.'<br>';
		// $calen.= $this->fecha.'<br>';		
		
		// $this->numdias=date("d",$this->fecha-$this->fecharef);
		// Importante, redondear el número... Cuando hay deimales del tipo n.95.... parece que
		// no lo hace bien.
		$this->numdias=round(($this->fecha-$this->fecharef)/(24*3600)); //número de días que existen en la cadena.
		// por ejemplo, si $fecharef equivale al 15 del 12 de 1005, y fecha fin al 18 del 12
		// la distancia entre ellas será de tres días: 16, 17 y 18.
		// $calen.= $this->numdias.'<br>';		
		
		// si la fecha está en ese intervalo lee la posición de la cadena correspondiente.
		if ($this->fecha>=$this->fecharef and $this->fecha<=$this->fechafin) {
				$this->ocupado=substr($cadena,$this->numdias,1); //ocupacion de la posición $numdias
		} else { $this->ocupado=-1;}
		
		// $calen.= $this->numdias.'<br>';
		// $calen.= $this->ocupado.'<br>';
		if ($que==0) { return $this->ocupado;} else {return $this->numdias;}
		} // Fin de la función
		
	
	// Ocupa un período entre dos fechas...
	// parametro que= 0 -> si es día lectivo y 1 -> día no lectivo
	// Una fecha de referencia, y dos fechas. Me da los siguientes datos:
	// -1 --> esas fechas no existen
	// -2 --> Las fechas uno o dos se salen fuera del intervalo de la cadena
	// IMPORTANTE: Las fechas están en formato MySQL (Año, mes, dia).
	function entrar($cadena,$fecharef,$fechauno,$fechados,$que) {

		$this->devuelve=0; //valor devuelto por la función
		
		// comprueba que todas las fechas son válidas y si lo son ordena las dadas...
		$comprobarfechas=New micalendario();
		if ($comprobarfechas->fechavalida($fecharef)==0 
			or $comprobarfechas->fechavalida($fechauno)==0
			or $comprobarfechas->fechavalida($fechados)==0) {
			$this->devuelve=-1; // valor de binario inválidas.
		} else { // ordenar las fechas dadas.
			$this->desordenadas=$comprobarfechas->comprueba($fechauno,$fechados);
			// si la primera es mayor un 1 si no, es un -1
			if ($this->desordenadas==1) {
				$this->temporal=$fechados; // paso la segunda a una variable temporal.
				$fechados=$fechauno; // la segunda es ahora la primera.
				$fechauno=$this->temporal; // la primera es la variable temporal.
			}
		}
		
		if ($this->devuelve==0) { 
		// comprueba que las fechas están dentro del intervalo de la fecha de referencia en la cadena.
		// $binario=New binario(); //llama a la clase binario..
		// $comprueba=$binario->ocupacion($cadena,$fecharef,$fechauno,0);
		$this->comprueba=$this->ocupacion($cadena,$fecharef,$fechauno,0);
		if ($this->comprueba==-1) {$this->devuelve=-2;}//si no está la fecha en el intervalo.
		$this->comprueba=$this->ocupacion($cadena,$fecharef,$fechados,0);
		if ($this->comprueba==-1) {$this->devuelve=-2;} //si no está la fecha en el intervalo.
		}
		
		// en este paso las fechas son válidas y están dentro del intervalo de la cadena y están ordenadas
		if ($this->devuelve==0) {
			//posiciones en la cadena...
			$this->pos1=$this->ocupacion($cadena,$fecharef,$fechauno,1); //posicion de la primera
			$this->pos2=$this->ocupacion($cadena,$fecharef,$fechados,1); //posicion de la segunda
			$this->sustituto=str_repeat($que,$this->pos2-$this->pos1+1); // una cadena de sustitucion con unos del mismo tamaño.
			$this->devuelve=substr_replace($cadena,$this->sustituto,$this->pos1,$this->pos2-$this->pos1+1);
			}
		
		return $this->devuelve;
		} // Fin de la función
		
	// Damos una cadena y una cadena de referencia.
	// Una fecha de referencia, y dos fechas. Me da los siguientes datos:
	// cadena cruzada xor entre las dos fechas...
	// -1 --> esas fechas no existen
	// -2 --> Las fechas uno o dos se salen fuera del intervalo de la cadena
	// -3 --> Si las dos cadenas no son de igual tamaño...
	// IMPORTANTE: Las fechas están en formato MySQL (Año, mes, dia).
	function cruzar($cadena,$cadenaref,$fecharef,$fechauno,$fechados) {

		$this->devuelve=0; //valor devuelto por la función
		
		// comprueba que todas las fechas son válidas y si lo son ordena las dadas...
		$comprobarfechas=New micalendario();
		if ($comprobarfechas->fechavalida($fecharef)==0 
			or $comprobarfechas->fechavalida($fechauno)==0
			or $comprobarfechas->fechavalida($fechados)==0) {
			$this->devuelve=-1; // valor de binario inválidas.
		} else { // ordenar las fechas dadas.
			$this->desordenadas=$comprobarfechas->comprueba($fechauno,$fechados);
			// si la primera es mayor un 1 si no, es un -1
			if ($this->desordenadas==1) {
				$this->temporal=$fechados; // paso la segunda a una variable temporal.
				$fechados=$fechauno; // la segunda es ahora la primera.
				$fechauno=$this->temporal; // la primera es la variable temporal.
			}
		}
		
		if ($this->devuelve==0) { 
		// comprueba que las fechas están dentro del intervalo de la fecha de referencia en la cadena.
		// $binario=New binario(); //llama a la clase binario..
		// $comprueba=$binario->ocupacion($cadena,$fecharef,$fechauno,0);
		$this->comprueba=$this->ocupacion($cadena,$fecharef,$fechauno,0);
		if ($this->comprueba==-1) {$this->devuelve=-2;}//si no está la fecha en el intervalo.
		$this->comprueba=$this->ocupacion($cadena,$fecharef,$fechados,0);
		if ($this->comprueba==-1) {$this->devuelve=-2;} //si no está la fecha en el intervalo.
		}
		
		if ($this->devuelve==0) { 
		// comprueba que las cadenas tienen el mismo tamaño
		if (strlen($cadena)<>strlen($cadenaref)) {$this->devuelve=-3;}
		}
		
		// en este paso las fechas son válidas y están dentro del intervalo de la cadena y están ordenadas
		if ($this->devuelve==0) {
			//posiciones en la cadena...
			$this->pos1=$this->ocupacion($cadena,$fecharef,$fechauno,1); //posicion de la primera
			$this->pos2=$this->ocupacion($cadena,$fecharef,$fechados,1); //posicion de la segunda
			$this->sustituto="";
			for ($i=$this->pos1;$i<=$this->pos2;$i++) {
				$this->sustituto.=((int) $cadena{$i}) ^ ((int) $cadenaref{$i});
			}
			$this->devuelve=substr_replace($cadena,$this->sustituto,$this->pos1,$this->pos2-$this->pos1+1);
			}
		return $this->devuelve;
		} // Fin de la función
	
		// ***********************************************************************
		// Función de calendario...
		// dada una cadena binaria y una fecha inicial, pone un calendario largo...
		// ***********************************************************************
		function calendario($cadena,$cadenados,$fechaini) {
			
			$this->estilo='style="color: #FFFF00; background-color: #CC0000;"';
			$this->estilodos='style="color: #0000FF; background-color: #CCCCFF;"';
			
			$this->diasdelasemana=array( 0=> array("Domingo","Lunes","Martes","Miércoles","Jueves","Viernes","Sábado"), 
										 1=> array("Do","Lu","Ma","Mi","Ju","Vi","Sa"));
			
			$this->nombremes=array("Enero","Febrero","Marzo","Abril","Mayo","Junio",
			"Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
			
			// setlocale(LC_TIME,'es_ES'); // Locales en español
			// parece ser que no le afectan los locales a los datos de adobd_time
			
			$this->mes=substr($fechaini,5,2)+0;
			$this->anno=substr($fechaini,0,4)+0;
			$this->dia=substr($fechaini,8,2)+0;
			
			$calen= '<div style="position: absolute; width: auto; border: none; text-align: center;" id="calendario">'; // empieza a definir la variable calen...
			$calen.= '<table align="center">'; // empieza la tabla
			// encabezado
			$calen.= '<tr>';
				$calen.= '<th>Mes</th>';
				for ($i=1;$i<=31;$i++) {
					$calen.= '<td>'.$i.'</td>';
				}
			$calen.= '</tr>';
			// Cuerpo
			// empiezo un mes, una fila nueva
			$calen.= '<tr>';
			$mesini=$this->mes; $calen.= '<td>'.$this->nombremes[$this->mes-1].'</td>';
			$fecha=$fechaini;
			// espacios vacíos antes...
			$calen.= str_repeat('<td></td>',$this->dia-1);
			// generación de los datos a partir de esa fecha
			for ($i=1;$i<=strlen($cadena);$i++) {
				 $caracter=substr($cadena,$i-1,1); // extrae el caracter...
				 if ($cadenados<>"") {$caracterdos=substr($cadenados,$i-1,1);}
				 $dia=adodb_date("w",adodb_mktime(0,0,0,$this->mes,$this->dia+$i-1,$this->anno));
				 $mes=adodb_date("m",adodb_mktime(0,0,0,$this->mes,$this->dia+$i-1,$this->anno));
				 if ($mes<>$mesini) { // Si cambia el mes...
				 	 // 1º) cierra la fila y abre otra
					 $calen.= '</tr><tr>';
					 // 2º) añade un mes nuevo a la columna meses
					 $calen.= '<td>'.$this->nombremes[$mes-1].'</td>';
					 // 3º) El mes inicial es ahora el nuevo mes
					 $mesini=$mes;
				 }
				 if ($caracter==0) {$estilo="";} else {$estilo=$this->estilo;}
				 if ($cadenados<>"" and $caracter==0 and $caracterdos==1) {$estilo=$this->estilodos;}
				 $calen.= '<td '.$estilo.' >'.$this->diasdelasemana[1][$dia].'</td>'; // pone un día
			} 
			$calen.= '</tr>'; // Termina la fila...
			$calen.= '</table>'; // Termina la tabla
			// $calen.='</div>';
			return $calen;
		} // fin de la función
		
		
		// ***********************************************************************
		// Función de calendario mensual...
		// dada una cadena binaria , el mes y una fecha inicial, pone el calendario del mes
		// izquierda es la posición a la izquierda y top arriba.
		// $bug ->1, considera los ceros y unos (fechas lectivas o no)
		// ***********************************************************************
		function calendariodos($cadena,$fechaini,$mesref,$iz,$top,$bug) {
		
			$this->estilo='style="color: #FFFF00; background-color: #CC0000;"';
			
			$this->diasdelasemana=array( 0=> array("Domingo","Lunes","Martes","Miércoles","Jueves","Viernes","Sábado"), 
										 1=> array("Do","Lu","Ma","Mi","Ju","Vi","Sa"));
			
			$this->nombremes=array("Enero","Febrero","Marzo","Abril","Mayo","Junio",
			"Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
			
			// setlocale(LC_TIME,'es_ES'); // Locales en español
			// parece ser que no le afectan los locales a los datos de adobd_time
			
			$this->mes=substr($fechaini,5,2)+0;
			$this->anno=substr($fechaini,0,4)+0;
			$this->dia=substr($fechaini,8,2)+0;
			
			$calen= '<div style="position: relative; 
			         left: '.$iz.'; top: '.$top.'" id="calendario">'; // empieza a definir la variable calen...
			$calen.= '<table>'; // empieza la tabla
			// encabezado
			// $calen.='<tr><th colspan="7">Año escolar '.substr($this->anno,2,2).'/'.substr($this->anno+1,2,2).'</th></tr>';
			$anno=($mesref>=9)?$this->anno:($this->anno+1);
			$calen.='<tr><td colspan="7">'.$this->nombremes[$mesref-1].' '.$anno.'</td></tr>';	
			$calen.= '<tr>'; // Empiezan los días de la semana
				// $calen.= '<td>'.substr($this->anno,2,2).'/'.substr($this->anno+1,2,2).'</td>';
				for ($i=1;$i<=7;$i++) {
					$week=bcmod($i,7); // calendario empezando en lunes.
					$calen.= '<th>'.$this->diasdelasemana[1][$week].'</th>';
				}
			$calen.= '</tr>';
			// Cuerpo
			// empiezo un mes, una fila nueva
			$calen.= '<tr>';
			// generación de los datos a partir de esa fecha
			$semana=""; $si="";
			for ($i=1;$i<=strlen($cadena);$i++) {
				 $caracter=substr($cadena,$i-1,1); // extrae el caracter...
				 $dia=adodb_date("j",adodb_mktime(0,0,0,$this->mes,$this->dia+$i-1,$this->anno));
				 $week=adodb_date("w",adodb_mktime(0,0,0,$this->mes,$this->dia+$i-1,$this->anno));
				 $week=($week==0)?6:($week-1); // calendario empezando en lunes.
				 if ($i==1) { // para empezar el año, si no empieza en lunes...
				 	$semana.=str_repeat('<td>-</td>',$week); // pone guiones antes...
					$si=str_repeat('0',$week);  // inicializa la cadena de semana...
				 }
				 $mes=date("m",mktime(0,0,0,$this->mes,$this->dia+$i-1,$this->anno));
				 if ($mes==$mesref) {$poner=$dia; $si.="1";} else {$poner="-"; $si.="0"; }
				 // Si la cadena tiene un 1, genera un cambio de estilo...
				 if ($caracter==1 and $bug==1) {$semana.='<td '.$this->estilo.'>'.$poner.'</td>';} 
				 	else {$semana.='<td>'.$poner.'</td>';}
				 // comprueba que he acabado la semana y la imprime
				 if ($week==6 and $si<>'0000000') {
					 $semana='<tr>'.$semana.'</tr>';
					 $calen.= $semana;
					 $semana=""; $si=""; // inicializa variables
				 } elseif ($week==6 and $si=='0000000') 
				 	 {$semana=""; $si="";} // debo inicializarlas aunque no la escriba
				 } // fin del for...
			
			if ($mesref==8 and $semana<>"" and $week<>6) // Si estoy al final de la cadena...
				{ $semana.=str_repeat('<td>-</td>',6-$week); // pone guiones después--
				$calen.= '<tr>'.$semana.'</tr>'; // imprime la semana que queda
				}
				
				
			$calen.= '</table>'; // Termina la tabla
			$calen.= '</div>';
			
			return $calen;
			
		} // fin de la función
		
		// ***********************************************************************
		// Función numerohoras
		// $bug = 1 -> retorna una cadena con las horas de cada día...
		// $bug = 2 -> suma el número de horas que existen.
		// -1 -> Salida no valida
		// ***********************************************************************
		function numerohoras($habiles,$semana,$anno,$bug) {

			 $numdias=adodb_date("z",adodb_gmmktime(0, 0, 0 ,12, 31, $anno+1))+1;
			 
			 $habiles=substr(trim($habiles),0,$numdias); // quito espacios, por si acaso...
			 
			 $week=adodb_date("w",adodb_mktime(0,0,0,9,1,$anno)); // día de la semana del 1 de septiembre de ese año.
			 $week=($week==0)?6:($week-1); // calendario empezando en lunes.
			 
			 $ini=substr($semana,$week,7-$week); // cadena inicial...
			 // en esta cadena se recoge el número de horas cada día...
			 $cadena=substr($ini.str_repeat($semana,53),0,$numdias);
			 
			 $cadenados=""; $suma=0;
			 for ($i=1;$i<=$numdias;$i++) { // cruce de las dos cadenas...
			 	$a=substr($cadena,$i-1,1);
				$b=substr($habiles,$i-1,1);
					if ($b==0) { $cadenados.=$a; $suma=$suma+$a;} else {$cadenados.="0";}
			 }
			 
			 switch ($bug) {
			 	case 1: return $cadenados; break;
				case 2: return $suma; break;
				default: return $suma; break;
			 }
			 

		} // fin de función número de horas...
		
		// ***********************************************************************
		// Función obtengodia
		// paso de parametros la cadena con las horas, las horas de offset,
		// el día a partir del cual empieza a contar y el número de horas que debe contar...
		// Cuenta las horas de un intervalo y me dice que dia acaba
		// $bug = 1 -> retorna el dia cuando empieza
		// $bug = 2 -> retorna el dia cuando acaba
		// -1 -> Salida no valida
		// ***********************************************************************
		function obtengodia($horario,$offset,$anno,$num,$bug) {
		
		$i=0; $diauno=0; // contador a cero; primer día del intervalo...
		while ($diauno<$offset+1) {
			$diauno+=substr($horario,$i,1);
			$i++;
			// echo '<p>'.$diauno.' - '.$i.'</p>';
		}	
		$diauno=$i-1; // el día correspondiente...
		
		$i=0; $diados=0; // contador a cero; primer día del intervalo...
		while ($diados<$offset+$num) {
			$diados+=substr($horario,$i,1);
			$i++;
		}	
		$diados=$i-1;
		
		$this->fechaini=$anno.'-09-01'; // fecha inicial
		$this->mes=substr($this->fechaini,5,2)+0;
		$this->anno=substr($this->fechaini,0,4)+0;
		$this->dia=substr($this->fechaini,8,2)+0;
		
		$i=0;
		while ($i<=$diados) {
			$fecha=adodb_date("Y-m-d",adodb_mktime(0,0,0,$this->mes,$this->dia+$i,$this->anno));
			if ($i==$diauno) {$this->fechaini=$fecha;}
			if ($i==$diados) {$this->fechafin=$fecha;}
			$i++;
		}
		
		if ($bug==1) {return $this->fechaini;}
		if ($bug==2) {return $this->fechafin;}
			
		}

} //Fin de la clase.
?>
