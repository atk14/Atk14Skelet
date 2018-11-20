<?php
/**
 *	{icon glyph="plus"}
 */
function smarty_function_icon($params,$template){
	$params += array(
		"glyph" => "question-sign",
	);
	
	return sprintf('<span class="glyphicon glyphicon-%s"></span>',$params["glyph"]);
}
