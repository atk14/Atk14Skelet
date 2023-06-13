<?php
/**
 * Vyrenderuje modalni okno.
 *
 * Renderuje sablonku views/helpers/modal/modal.tpl
 *
 *	{modal title="Kontaktní formulář" id="contactDialog"}
 *		<form>
 *			...
 *		</form>
 *
 *		<footer>
 *			<button type="submit">Odeslat</button>
 *		</footer>
 *	{/modal}
 *
 * 	Modaly testujeme na adresach:
 *		http://activacek_beta.localhost/cs/tests/modal/
 *		http://activa.localhost/cs/tests/modal/
 */
function smarty_block_modal($params,$content,$template,&$repeat){
	if($repeat){ return; }

	$params += array(
		"title" => "",
		"id" => "myModal".uniqid(),
		"open_on_load" => false,
		"vertically_centered" => false,
		"animation" => true,
		"close_button" => true,
		"closable_by_keyboard" => true,
		"closable_by_clicking_on_backdrop" => true,
	);

	$smarty = atk14_get_smarty_from_template($template);

	$footer = "";
	if(preg_match('/(<footer>(.*)<\/footer>)/s',$content,$matches)){
		$footer = $matches[2];
		$content = EasyReplace($content,array(
			$matches[1] => "",
		));
	}

	$original_smarty_vars = $smarty->getTemplateVars();

	$smarty->assign("title",$params["title"]);
	$smarty->assign("id",$params["id"]);
	$smarty->assign("footer",$footer);
	$smarty->assign("content",$content);
	$smarty->assign("open_on_load",$params["open_on_load"]);
	$smarty->assign("vertically_centered",$params["vertically_centered"]);
	$smarty->assign("animation",$params["animation"]);
	$smarty->assign("close_button",$params["close_button"]);
	$smarty->assign("closable_by_keyboard",$params["closable_by_keyboard"]);
	$smarty->assign("closable_by_clicking_on_backdrop",$params["closable_by_clicking_on_backdrop"]);

	$out = $smarty->fetch("shared/helpers/modal/".(USING_BOOTSTRAP4 || USING_BOOTSTRAP5 ? "_bootstrap4.tpl" : "_bootstrap3.tpl"));

	$smarty->clearAllAssign();
	$smarty->assign($original_smarty_vars);

	return $out;
}
