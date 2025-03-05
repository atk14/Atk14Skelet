<?php
function smarty_block_drink_shortcode__linklist($params,$content,$template,&$repeat){
	if($repeat){ return; }

	$params += [
		"code" => 0,
		"class" => ""
	];

	foreach($params as $k => $v){
		$template->assign($k,$v);
	}
	return $template->fetch("shared/helpers/drink_shortcodes/_linklist.tpl");
}
