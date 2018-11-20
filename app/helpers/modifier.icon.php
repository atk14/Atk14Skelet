<?php
definedef("USING_FONTAWESOME",USING_BOOTSTRAP4);

/**
 *	{!"edit"|icon}
 *	{!"remove"|icon}
 */
function smarty_modifier_icon($glyph){
	if(USING_FONTAWESOME){
		$tr_table = array(
			"remove" => "times",
		);
		$glyph = isset($tr_table[$glyph]) ? $tr_table[$glyph] : $glyph;
		return sprintf('<span class="fa fa-%s"></span>',$glyph);
	}

	return sprintf('<span class="glyphicon glyphicon-%s"></span>',$glyph);
}
