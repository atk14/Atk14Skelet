<?php
/**
 * Renders pulldown menu with links to administration of the given object
 *
 *	{admin_menu for=$book}
 *	{admin_menu for=$book align="left"}
 *
 *	{admin_menu for=$book only_edit=true edit_title="Edit this book"}
 */
function smarty_function_admin_menu($params,$template){
	global $ATK14_GLOBAL;
	$smarty = atk14_get_smarty_from_template($template);

	($logged_user = $smarty->getTemplateVars("logged_user")) ||
	($logged_user = $ATK14_GLOBAL->getValue("logged_user")); // in app/helpers/function.iobject_to_html.php, there is no info about the logged in user in $template
	
	if(!$logged_user || !$logged_user->isAdmin()){
		return;
	}

	$params += array(
		"for" => null, // a Book member
		"align" => "right", // "right", "left",
		"only_edit" => false,
		"edit_title" => "", // e.g. "Edit address"
		"opacity" => 100, // 100, 80, 50 (percent)
		"pull_down" => 0, // 1,2,3...
	);

	$float = USING_BOOTSTRAP3 ? "pull" : "float"; // pull-right, float-right,

	$params += array(
		"class" => $params["align"]=="left" ? "$float-left" : "$float-right", //
	);

	$opacity = (float)$params["opacity"];
	$pull_down = (float)$params["pull_down"];

	$style = [];
	if($opacity!==100.0){
		$style[] = sprintf("opacity: %.2f",$opacity)."%";
	}
	if($pull_down!==0.0){
		$style[] = sprintf("margin-top: %.2fem",3.0 * $pull_down);
	}
	$style = join("; ",$style);


	$object = $params["for"];
	if(!$object){ return; }
	$class_name = new String4(get_class($object)); // "Book"
	$controller = $class_name->underscore()->pluralize()->toString(); // "books"
	$object_name = $class_name->underscore()->toString(); // "book"

	$original_smarty_vars = $smarty->getTemplateVars();
	$smarty->assign("admin_controller",$controller);
	$smarty->assign("object",$object); // "object" => $book
	$smarty->assign($object_name,$object); // "book" => $book
	$smarty->assign("class",$params["class"]);
	$smarty->assign("only_edit",$params["only_edit"]);
	$smarty->assign("edit_title",$params["edit_title"]);
	$smarty->assign("style",$style);

	if($smarty->templateExists("shared/helpers/admin_menu/_$object_name.tpl")){ // e.g. "shared/helpers/admin_menu/_book.tpl"
		$subtemplate = "shared/helpers/admin_menu/$object_name";
	}else{
		$subtemplate = "shared/helpers/admin_menu/generic";
	}
	$smarty->assign("subtemplate",$subtemplate);

	$out = $smarty->fetch("shared/helpers/_admin_menu.tpl");
	$smarty->clearAllAssign();
	$smarty->assign($original_smarty_vars);

	return $out;
}
