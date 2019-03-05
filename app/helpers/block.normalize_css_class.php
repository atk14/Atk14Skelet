<?php
/**
 * It makes list of css classes prettier
 *
 * It is used in shared/_form_field.tpl
 *
 * Usage:
 *
 *	{normalize_css_class}
 *		form-group
 *		form-group--checkbox
 *
 *		form-group--required
 *
 *	{/normalize_css_class}
 *
 *	form-group form-group--checkbox form-group--required
 */
function smarty_block_normalize_css_class($params,$content,$template,&$repeat){
	if($repeat){ return; }

	$content = trim($content);
	$content = preg_replace('/\s+/s',' ',$content);
	return $content;
}
