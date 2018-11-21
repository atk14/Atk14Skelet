<?php
/**
 *	{!"edit"|icon}
 *	{!"remove"|icon}
 *
 *	{!"twitter"|icon:"brands"}
 *	{!"envelope"|icon:"solid"}
 *	{!"envelope"|icon:"regular"}
 *	{!"envelope"|icon:"light"}
 */
function smarty_modifier_icon($glyph,$style = ""){
	$tr_table = array(
		"remove" => "times",
		"eye-open" => "eye",
	);
	$glyph = isset($tr_table[$glyph]) ? $tr_table[$glyph] : $glyph;

	$style_tr = [
		"brands" => [
			"twitter",
		]
	];

	if(!$style){
		foreach($style_tr as $s => $glyphs){
			if(in_array($glyph,$glyphs)){
				$style = $s;
				break;
			}
		}
	}

	if(!$style){
		$style = "solid"; // the default style
	}

	$s = $style[0]; // "style" -> "s"
	return sprintf('<span class="fa%s fa-%s"></span>',$s,$glyph);
}
