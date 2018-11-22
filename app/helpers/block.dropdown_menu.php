<?php
/**
 * It takes a serie of links and converts it to a drop down menu
 *
 * Each link must be on a separate line. Empty lines are ignored.
 *
 * Usage:
 *
 *	{dropdown_menu pull=right clearfix=false}
 *		{a action="detail" id=$article}<i class="glyphicon glyphicon-eye-open"></i> Detail{/a}
 *		{a action="edit" id=$article}<i class="glyphicon glyphicon-edit"></i> Edit{/a}
 *		{a_destroy id=$article}<i class="glyphicon glyphicon-remove"></i> Delete{/a_destroy}
 *	{/dropdown_menu}
 *
 * HTML markup for this helper is in the template views/shared/helpers/_dropdown_menu.tpl
 */
function smarty_block_dropdown_menu($params,$content,$template,&$repeat){
	if($repeat){ return; }

	$params += array(
		"pull" => "right", // "right", "left", ""
		"clearfix" => null,
		"class" => "",
	);

	if(!isset($params["clearfix"])){
		$params["clearfix"] = $params["pull"]=="right";
	}

	$smarty = atk14_get_smarty_from_template($template);

	$content = preg_replace('/(<\/a>)\s*(<a)/s','\1%SEPARATOR%\2',$content);

	$lines = array();
	foreach(explode('%SEPARATOR%',$content) as $line){
		$line = trim($line);
		if(!strlen($line)){ continue; }
		$lines[] = $line;
	}

	// Herdcoding class="btn btn-default" to the first <a></a>
	$first_line = array_shift($lines);
	if(preg_match('/<a\s[^>]*class="/',$first_line)){
		// <a href=".." class="bold">Detail</a> -> <a href=".." class="bold btn btn-default">Detail</a>
		$first_line = preg_replace('/(<a\s[^>]*\bclass="[^"]*)"/','\1 btn btn-outline-primary btn-sm"',$first_line);
	}else{
		// <a href="..">Detail</a> -> <a href=".." class="btn btn-default">Detail</a>
		$first_line = preg_replace('/(<a\s[^>]*)>/','\1 class="btn btn-outline-primary btn-sm">',$first_line);
	}

	$original_smarty_vars = $smarty->getTemplateVars();
	$smarty->assign("first_line",$first_line);
	$smarty->assign("lines",$lines);
	$smarty->assign("pull",$params["pull"]);
	$smarty->assign("clearfix",$params["clearfix"]);
	$smarty->assign("class",$params["class"]);
	$out = $smarty->fetch("shared/helpers/_dropdown_menu.tpl");
	$smarty->clearAllAssign();
	$smarty->assign($original_smarty_vars);

	return $out;
}
