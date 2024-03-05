<?php
/**
 * Shortcode to insert Bootstrap Collapse component
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
 * - https://getbootstrap.com/docs/4.6/components/collapse/
 */

function smarty_block_drink_shortcode__collapse($params,$content,$template,&$repeat){
	if($repeat){ return; }

	$params += [
		"class" => "",
	];

	foreach($params as $k => $v){
		$template->assign($k,$v);
	}

  $collapseID = "collapse_".uniqid();
  $template->assign( "collapseID", $collapseID );
	$template->assign( "content", $content );

  return $template->fetch("shared/helpers/drink_shortcodes/_collapse.tpl");
}
