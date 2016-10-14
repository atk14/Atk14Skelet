<?php
/**
 * Renders a plus button [+] linking to the create_new action of the given controller
 *
 * Commonly used in administration.
 *
 * See app/views/shared/helpers/_button_create_new.tpl
 *
 *	<h1>{button_create_new}Create new article{/button_create_new} Articles</h1>
 *	...
 *	<h3>{button_create_new controller=attachments article_id=$article}Add attachment{/button_create_new} Attachments</h3>
 */
function smarty_block_button_create_new($params,$content,$template,&$repeat){
	if($repeat){ return; }
	$smarty = atk14_get_smarty_from_template($template);

	$params += array(
		"action" => "create_new"
	);

	$original_smarty_vars = $smarty->getTemplateVars();
	$smarty->assign("title",$content);
	$smarty->assign("create_new_url",Atk14Url::BuildLink($params));
	$out = $smarty->fetch("shared/helpers/_button_create_new.tpl");
	$smarty->clearAllAssign();
	$smarty->assign($original_smarty_vars);

	return $out;
}
