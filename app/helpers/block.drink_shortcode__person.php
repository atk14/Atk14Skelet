<?php
function smarty_block_drink_shortcode__person($params,$content,$template,&$repeat){
	if($repeat){ return; }

	$params += [
		"class"			=> "",
		"name"			=> "",
		"email"			=> "",
		"phone"			=> "",
		"phone2"		=> "",
		"position"	=> "",
		"facebook"	=> "",
		"twitter"		=> "",
		"instagram" => "",
	];

	foreach($params as $k => $v){
		$template->assign($k,$v);
	}

	$template->assign("content",$content);
	$template->assign("uniqid",uniqid());

	return $template->fetch("shared/helpers/drink_shortcodes/_person.tpl");
}
