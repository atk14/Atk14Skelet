<?php
/**
 * Renders pulldown menu with links to administration of the given object
 *
 *	{admin_menu for=$book}
 *
 */
function smarty_function_admin_menu($params,$template){
	$smarty = atk14_get_smarty_from_template($template);

	$logged_user = $smarty->getTemplateVars("logged_user");
	if(!$logged_user || !$logged_user->isAdmin()){
		return;
	}

	$params += array(
		"for" => null, // a Book member
		"class" => USING_BOOTSTRAP4 ? "float-right" : "pull-right", //
	);

	$object = $params["for"];
	$class_name = new String4(get_class($object)); // "Book"
	$controller = $class_name->underscore()->pluralize()->toString(); // "books"
	$object_name = $class_name->underscore()->toString(); // "book"

	$original_smarty_vars = $smarty->getTemplateVars();
	$smarty->assign("admin_controller",$controller);
	$smarty->assign("object",$object); // "object" => $book
	$smarty->assign($object_name,$object); // "book" => $book
	$smarty->assign("class",$params["class"]);

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
