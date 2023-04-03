<?php
function smarty_block_drink_shortcode__tab($params,$content,$template,&$repeat){
	if($repeat){ return; }

	$params += [
		"class" => "",
	];

	foreach($params as $k => $v){
		$template->assign($k,$v);
	}

	$template->assign("content",$content);
	$template->assign("uniqid", $template->getTemplateVars("uniqid"));
	$template->assign("tab_names", $template->getTemplateVars("tab_names"));

	return $template->fetch("shared/helpers/drink_shortcodes/_tab.tpl");
}
