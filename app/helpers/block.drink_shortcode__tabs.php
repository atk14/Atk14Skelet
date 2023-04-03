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
	preg_match_all('/(!-- drink:tab .*?-->)/',trim($_content),$matches); // e.g. !-- drink:col class="alert-success" -->

  foreach($matches[1] as &$tag){
    preg_match('/name="([^"]*)"/', $tag, $matches);
    if (isset($matches[1])){
      $tab_names[] = $matches[1];
    }
  }
	$template->assign("content",$content);
  $template->assign("tab_names", $tab_names);
  $template->assign("uniqid", "tabs-".uniqid());

	return $template->fetch("shared/helpers/drink_shortcodes/_tabs.tpl");
}
