<?php
/**
 *
 *
 *	{!$card->getName()|h|highlight_search_query:"param_name=q,opening_tag=<mark>,closing_tag=</mark>"} 
 */
function smarty_modifier_highlight_search_query($content,$options = []){
	static $smarty;
	global $HTTP_REQUEST;

	$options = Atk14Utils::StringToOptions($options);

	Atk14Require::Helper("block.highlight_search_query");
	if(is_null($smarty)){
		$smarty = Atk14Utils::GetSmarty();
		$smarty->assign("params",new Dictionary($HTTP_REQUEST->getGetVars()));
	}
	$repeat = false;
	return smarty_block_highlight_search_query($options,$content,$smarty,$repeat);
}
