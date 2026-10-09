<?php
function smarty_block_drink_shortcode__tabs($params,$content,$template,&$repeat){
	if($repeat){ return; }

	$params += [
		"class" => "",
	];

	foreach($params as $k => $v){
		$template->assign($k,$v);
	}

	// get [tab] elements
	$_content = trim($content);
	$_content = preg_replace('/<!-- drink:tabs -->.*?<!-- \/drink:tabs -->/s','',$_content);
	preg_match_all('/<!-- drink:tab(?<params>\s.*?)?-->/s',$_content,$matches); // e.g. <!-- drink:tab name="Contacts" -->

	// params are parsed the same way as DrInk Markdown does it, so escaped (name="Say \"hi\"") or single quoted (name='Say "hi"') values work
	$postfilter = new MarkdownShortcodesPostfilter();
	$tab_names = [];
	foreach($matches["params"] as $params_str){
		$tab_params = $postfilter->parseParams($params_str);
		if(isset($tab_params["name"])){
			$tab_names[] = $tab_params["name"];
		}
	}
	$template->assign("content",$content);
  $template->assign("tab_names", $tab_names);
  $template->assign("uniqid", "tabs-".uniqid());

	return $template->fetch("shared/helpers/drink_shortcodes/_tabs.tpl");
}
