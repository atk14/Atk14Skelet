<?php
/**
 * Shortcode for title link in Bootstrap Collapse component
 * 
 * Usage:
 *
 *	[collapse class="someclass"]
 *  	[collapse_title class="btn btn-primary"]Read More[/collapse_title]
 *  	[collapse_content class="someclass"]Hidden content to ne visible after click[/collapse_content]
 *  [/collapse]
 * 
 * class: (optional) CSS class for container containing both link
 *
 * See:
 * - app/helpers/block.drink_shortcode__collapse.php
 */

function smarty_block_drink_shortcode__collapse_title($params,$content,$template,&$repeat){
	if($repeat){ return; }

	$params += [
		"class" => "",
	];

	foreach($params as $k => $v){
		$template->assign($k,$v);
	}

	$template->assign("content",$content);
	$template->assign("collapseID", $template->getTemplateVars("collapseID"));

	return $template->fetch("shared/helpers/drink_shortcodes/_collapse_title.tpl");
}
