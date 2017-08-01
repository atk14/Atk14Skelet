<?php
/**
 * Different functions
 */

/**
 * Alias for date("Y-m-d H:i:s")
 */
function now(){
	return date("Y-m-d H:i:s");
}

/**
 * Replacement for built-in function assert()
 *
 * Function assert() turned to language construct in PHP 7 and has no effect in production environment.
 */
function myAssert($expression,$message = ""){
	if(!$expression){
		$file = $line = "???";
		$ar = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT,1);
		if($ar){
			$file = $ar[0]["file"];
			$line = $ar[0]["line"];
		}

		$message = $message ? $message : "Assertion";
		$msg = sprintf("myAssert(): %s failed in %s on line %d",$message,$file,$line);
		throw new Exception($msg);
	}
}
