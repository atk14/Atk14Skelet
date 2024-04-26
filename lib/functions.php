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
 * Keys can be optionally preserved.
 *
 * Originally inspired by Sean Cannon, LitmusBox.com | seanc@litmusbox.com:
 * https://gist.github.com/SeanCannon/6585889
 *
 * @param  array $array The multi-dimensional array.
 * @param  array $options
 * @return array
 */
function array_flatten($array,$options = array()) {
	if (!is_array($array)) { 
		return false;
	}
	$options += array(
		"preserve_keys" => false,
	);

	$result = array();
	foreach ($array as $key => $value) {
		if (is_array($value)) { 
			$result = array_merge($result, array_flatten($value, $options));
		} else {
			$key = $options["preserve_keys"] ? $key : 0;
			$result = array_merge($result, array($key => $value));
		} 
	} 
	return $result; 
}
