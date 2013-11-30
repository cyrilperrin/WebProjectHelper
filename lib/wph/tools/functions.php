<?php

// Function lcfirst
if (!function_exists('lcfirst')) {
    function lcfirst($str) {
    	return strtolower(substr($str,0,1)).substr($str,1);
    }
}

/**
 * Create new lines bloc (usefull for sql query) 
 * @param firstline string : first line
 * @param params[] string : other lines
 * @param $nbIndent int : number of indents
 * @return string : indented text
 */
function nlb($firstline) {
	// Get args
	$args = array();
	if (func_num_args() > 1) {
		for($i=1;$i<func_num_args()-1;$i++) {
			if (is_array(func_get_arg($i))) {
				$args = array_merge($args,func_get_arg($i));
			} else { $args[] = func_get_arg($i); }
		}
	}
	
	// Nb indents
	$nbIndents = func_get_arg(func_num_args()-1);
	
	// First line
	$string = nl($firstline.(count($args) ? $args[0] : ''),$nbIndents);
	
	// Whites
	$whites = rpt(' ',strlen($firstline));
	
	// Others
	for($i=1;$i<count($args);$i++) { $string .= nl($whites.$args[$i],$nbIndents); }
	
	// Return string
	return $string;
}

/**
 * Create a new line (usefull to indent code)
 * @param $text string : text
 * @param $nbIndent int : how many tabulation ?
 * @param $newLine : new line after text ?
 * @return string : indented text
 */
function nl($text='',$nbIndents=0,$newLine=true) {
	// Init string
	$string = '';
	
	// Generate tabulations
	$tabs = rpt(' ',$nbIndents*4);
	
	// Indent each lines
	if ($text != '') { $string .= $tabs.implode($tabs,explode("\n",$text)); }
	else { $string .= $tabs; }
	
	// New line ?
	if ($newLine) { $string .= "\n"; }
	
	// Return string
	return $string;
}

/**
 * Repeat a string
 * @param $pString string
 * @param $nbRepeat int
 * @return string
 */
function rpt($rpt,$nb) {
	$string = '';
	for($i=0;$i<$nb;$i++) { $string .= $rpt; }
	return $string;
}

/**
 * Puts an "s" at the end of word if necessary
 * @param $pString string
 * @return string 
 */
function svrl($string) {
	$lastCar = substr($string,-1);
	return in_array($lastCar,array('s','x')) ? $string : $string.'s';
}

/**
 * Generate a key
 * @param $nb int : key length
 * @param $alphabet string : used alphabet
 * @return string : result
 */
function generate_key($nb=32,$alphabet='1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz') {	
	$key = ''; $length = strlen($alphabet);
	for($i=0;$i<$nb;$i++) { $key .= $alphabet[rand(0,$length-1)]; }
	return $key;
}

/**
 * Extension of in_array() function
 * @param $element ? : element
 * @param $array ?[] : array
 * @return bool : element in array ?
 */
function in_array_equals($element,$array) {
	foreach ($array as $e) {
		if ($element->equals($e)) { return true; } 
	}
	return false;
}

/**
 * Same as array_merge() but keep keys 
 * @param $array1
 * @param $array2
 * @return array
 */
function array_merge_keys($array1,$array2) {
	foreach ($array2 as $key => $value) {
		$array1[$key] = $value;
	}
	return $array1;
}