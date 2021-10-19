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
	);

	$float = USING_BOOTSTRAP4 ? "float" : "pull"; // float-right, pull-right

	$params += array(
		"class" => $params["align"]=="left" ? "$float-left" : "$float-right", //
	);

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
