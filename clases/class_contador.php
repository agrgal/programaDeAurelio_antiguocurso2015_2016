<?php
//------------------------------------------------ 
class micontador {
//------------------------------------------------
/**
 * Autor: Aurelio Gallardo
 * Date : 20/11/2005
 *  
 * Description: Clase de PHP que permite hacer contadores
 * 
 */
 

	// Permite la construcción de un contador
	// usar dentro de un formulario...
	function obtencontador ($nombredecampo,$texto,$tipo,$valor,$min,$max) {
		
		echo '<p>'.$texto;

		// primero
		$this->onclick=$nombredecampo.".value=".$min."; refresh;"; // añade uno
		echo '<input type="button" class="botones" value="<<" name="'.$nombredecampo.'_primero" id="'.$nombredecampo.'_primero" 
		     OnClick="'.$this->onclick.'">';
		
		// atrás
		$this->onclick="if (".$nombredecampo.".value>".$min.") {".$nombredecampo.".value=".$nombredecampo.".value-1}; refresh;"; // añade uno
		echo '<input type="button" class="botones" value="<" name="'.$nombredecampo.'_atras" id="'.$nombredecampo.'_atras" 
		     OnClick="'.$this->onclick.'">';
		
		// valor del campo
		echo '&nbsp;';
		echo '<input style="width: 20px; text-align: center; 
		background-color: transparent; border: none; color: yellow;  
		font-weight: bold;	font-size: 1.3em;"
		name="'.$nombredecampo.'" value="'.$valor.'" type="'.$tipo.'">';
		
		 
		// adelante
		$this->onclick="if (".$nombredecampo.".value<".$max.") {".$nombredecampo.".value=1+(1*".$nombredecampo.".value)}; refresh;"; // añade uno
		echo '<input type="button" class="botones" value=">" name="'.$nombredecampo.'_adelante" id="'.$nombredecampo.'_adelante" 
		     OnClick="'.$this->onclick.'" size="1">';
		
		// ultimo
		$this->onclick=$nombredecampo.".value=".$max."; refresh;"; // añade uno
		echo '<input type="button" class="botones" value=">>" name="'.$nombredecampo.'_ultimo" id="'.$nombredecampo.'_ultimo" 
		     OnClick="'.$this->onclick.'">';
			 
	    echo '</p>';

	}

}?>