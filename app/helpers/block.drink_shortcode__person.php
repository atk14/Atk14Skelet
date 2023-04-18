<?php
function smarty_block_drink_shortcode__person($params,$content,$template,&$repeat){
	if($repeat){ return; }

	$params += [
		"class"			=> "",
		"image"			=> "",
		"name"			=> "",
		"position"	=> "",
		"email"			=> "",
		"phone"			=> "",
		"phone2"		=> "",
		"web"				=> "",
		"facebook"	=> "",
		"twitter"		=> "",
		"instagram" => "",
		"more_text"	=> _("More"),
		"more_link"	=> "",
		"show_qr"		=> "", // qr not implemented
	];

	foreach($params as $k => $v){
		$template->assign($k,$v);
	}

	$template->assign("content",$content);
	$template->assign("uniqid", uniqid());

	return $template->fetch("shared/helpers/drink_shortcodes/_person.tpl");
}
