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

		"USING_BOOTSTRAP4" => defined("USING_BOOTSTRAP4") && USING_BOOTSTRAP4,
	);

	if(!isset($params["clearfix"])){
		$params["clearfix"] = $params["pull"]=="right";
	}

	$smarty = atk14_get_smarty_from_template($template);
	$USING_BOOTSTRAP4 = $params["USING_BOOTSTRAP4"];

	// splitting up content according links
	$content = preg_replace('/(<\/a>)\s*(<a)/s','%a_end%%SEPARATOR%%a_begin%',$content);
	$content = preg_replace('/\s*(<a)/s','%SEPARATOR%%a_begin%',$content); // takes effect on lines without links
	$content = preg_replace('/(<\/a>)\s*/s','%a_end%%SEPARATOR%',$content);
	$content = str_replace("%a_begin%","<a",$content);
	$content = str_replace("%a_end%","</a>",$content);

	$lines = array();
	$first_line = "";
	foreach(explode('%SEPARATOR%',$content) as $line){
		$line = trim($line);
		if(!strlen($line)){ continue; }

		if(!$first_line){
			if($USING_BOOTSTRAP4){
				$line = _smarty_block_dropdown_menu_add_class_to_line($line,"btn btn-outline-primary btn-sm");
			}else{
				$line = _smarty_block_dropdown_menu_add_class_to_line($line,"btn btn-default");
			}
			$first_line = $line;
			continue;
		}

		if($USING_BOOTSTRAP4){
			$line = _smarty_block_dropdown_menu_add_class_to_line($line,"dropdown-item");
		}
		$lines[] = $line;
	}

	if(!strlen($first_line)){
		return "";
	}

	$original_smarty_vars = $smarty->getTemplateVars();
	$smarty->assign("first_line",$first_line);
	$smarty->assign("link_on_first_line",!!preg_match('/<a /',$first_line));
	$smarty->assign("lines",$lines);
	$smarty->assign("pull",$params["pull"]);
	$smarty->assign("clearfix",$params["clearfix"]);
	$smarty->assign("class",$params["class"]);
	$smarty->assign("USING_BOOTSTRAP4",$USING_BOOTSTRAP4);
	$template_name = "shared/helpers/_dropdown_menu.tpl";
	$out = $smarty->fetch($template_name);
	$smarty->clearAllAssign();
	$smarty->assign($original_smarty_vars);

	return $out;
}

/**
 * Hardocing the given CSS class into a <a> element
 *
 *	$line = _smarty_block_dropdown_menu_add_class_to_line($line,"dropdown-item"); // '<a href="/">Root</a>' -> '<a href="/" class="dropdown-item"></a>'
 *
 */
function _smarty_block_dropdown_menu_add_class_to_line($line,$class){
	if(preg_match('/<a\s[^>]*class="/',$line)){
		// <a href=".." class="bold">Detail</a> -> <a href=".." class="bold btn btn-default">Detail</a>
		$line = preg_replace('/(<a\s[^>]*\bclass="[^"]*)"/','\1 '.$class.'"',$line);
	}else{
		// <a href="..">Detail</a> -> <a href=".." class="btn btn-default">Detail</a>
		$line = preg_replace('/(<a\s[^>]*)>/','\1 class="'.$class.'">',$line);
	}
	return $line;
}
