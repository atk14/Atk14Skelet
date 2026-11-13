<?php
/**
 * A vertical space in DrInk Markdown content
 *
 *	[spacer height="2rem"]
 *
 * Renders an empty div.spacer of the given height (a number with px, em or rem; a bare number means px).
 * An invalid height gives the default one, so nothing can be injected into the style attribute.
 *
 * In the admin, the spacer is edited by public/admin/scripts/utils/spacer_editor.js.
 */
function smarty_function_drink_shortcode__spacer($params,$template){
	$params += [
		"height" => "",
	];

	$height = trim((string)$params["height"]);
	if(preg_match('/^(\d+(?:\.\d+)?|\.\d+)\s*(px|em|rem)?$/i',$height,$matches)){
		$height = $matches[1].(isset($matches[2]) && $matches[2]!=="" ? strtolower($matches[2]) : "px");
	}else{
		$height = "2rem";
	}

	$template->assign("height",$height);

	return trim($template->fetch("shared/helpers/drink_shortcodes/_spacer.tpl"));
}
