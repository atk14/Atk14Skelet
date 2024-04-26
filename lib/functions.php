<?php
/**
 * Different functions
 */

/**
 * Alias for date("Y-m-d H:i:s")
 */
function now($format = "Y-m-d H:i:s"){
	return date($format);
}

/**
 * Convert a multi-dimensional array into a single-dimensional array.
 *
 * https://gist.github.com/SeanCannon/6585889
 *
 * @author Sean Cannon, LitmusBox.com | seanc@litmusbox.com
 * @param  array $array The multi-dimensional array.
 * @return array
 */
function array_flatten($array) { 
	if (!is_array($array)) { 
		return false; 
	} 
	$result = array(); 
	foreach ($array as $key => $value) { 
		if (is_array($value)) { 
			$result = array_merge($result, array_flatten($value)); 
		} else { 
			$result = array_merge($result, array($key => $value));
		} 
	} 
	return $result; 
}
