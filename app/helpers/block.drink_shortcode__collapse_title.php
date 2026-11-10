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
 * class: (optional) CSS class of the title link (e.g. "btn btn-primary")
 * style: (optional) inline CSS style of the title link
 *
 * See:
 * - app/helpers/block.drink_shortcode__collapse.php
 */

function smarty_block_drink_shortcode__collapse_title($params,$content,$template,&$repeat){
	if($repeat){ return; }

	$params += [
		"class" => "",
		"style" => "",
	];

	foreach($params as $k => $v){
		$template->assign($k,$v);
	}

	// the title is inline, it's not a paragraph (Markdown makes one of a text on its own line)
	$content = trim($content);
	if(preg_match('/^<p>(.*)<\/p>$/s',$content,$matches) && strpos($matches[1],"<p>")===false){
		$content = trim($matches[1]);
	}

	$template->assign("content",$content);
	$template->assign("collapseID", $template->getTemplateVars("collapseID"));

	return $template->fetch("shared/helpers/drink_shortcodes/_collapse_title.tpl");
}
