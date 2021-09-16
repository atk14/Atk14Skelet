<?php
/**
 * Renders a plus button [+] linking to the create_new action of the given controller
 *
 * Commonly used in administration.
 *
 * See app/views/shared/helpers/_button_create_new.tpl
 *
 *	<h1>{button_create_new}Create new article{/button_create_new} Articles</h1>
 *	<h1>{button_create_new _class="btn btn-primary"}Create new article{/button_create_new} Articles</h1>
 *	...
 *	<h3>{button_create_new controller=attachments article_id=$article}Add attachment{/button_create_new} Attachments</h3>
 *
 *	<h3 id="images">{button_create_new return_to_anchor="images"}Add new image{/button_create_new} Images</h3>
 *
 *	<h1>{button_create_new action="upload" icon="file-import"}Import from CSV file{/button_create_new} Records</h1>
 */
function smarty_block_button_create_new($params,$content,$template,&$repeat){
	global $HTTP_REQUEST;

	if($repeat){ return; }
	$smarty = atk14_get_smarty_from_template($template);
	$request = $HTTP_REQUEST;

	$params += array(
		"action" => "create_new",
		"return_to_anchor" => "",
		"icon" => "plus-circle",
	);

	$return_to_anchor = $params["return_to_anchor"];
	unset($params["return_to_anchor"]);
	$return_to_anchor = preg_replace('/^#/','',$return_to_anchor); // "#images" -> "images"

	$icon = $params["icon"];
	unset($params["icon"]);

	$attrs = array();
	foreach($params as $k => $v){
		if(preg_match('/^_(.*)/',$k,$m)){
			$attrs[$m[1]] = $v;
			unset($params[$k]);
		}
	}

	$attrs += array(
		"class" => "btn btn-default",
	);

	$attrs = array();
	foreach($params as $k => $v){
		if(preg_match('/^_(.*)/',$k,$m)){
			$attrs[$m[1]] = $v;
			unset($params[$k]);
		}
	}

	$attrs += array(
		"class" => "btn btn-outline-primary",
	);

	$original_smarty_vars = $smarty->getTemplateVars();
	$smarty->assign("title",$content);
	if($return_to_anchor){
		$return_uri = $request->getUri()."#".$return_to_anchor;
		$params["_return_uri_"] = $return_uri; // see ApplicationBaseController::_redirect_back()
	}
	$smarty->assign("create_new_url",Atk14Url::BuildLink($params));
	$smarty->assign("attrs",$attrs);
	$smarty->assign("icon",$icon);
	$out = $smarty->fetch("shared/helpers/_button_create_new.tpl");
	$smarty->clearAllAssign();
	$smarty->assign($original_smarty_vars);

	return $out;
}
