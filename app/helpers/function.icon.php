<?php
require_once(__DIR__ . "/modifier.icon.php");

/**
 *	{icon glyph="plus"}
 */
function smarty_function_icon($params,$template){
	$params += array(
		"glyph" => "question-sign",
	);
	return smarty_modifier_icon($params["glyph"]);
}
