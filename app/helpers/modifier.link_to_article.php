<?php
function smarty_modifier_link_to_article($article,$options = ""){
	$options = Atk14Utils::StringToOptions($options);

	if(!$article){ return; }

	$params = [
		"namespace" => "",
		"controller" => "articles",
		"action" => "detail",
		"id" => $article,
	];

	return Atk14Url::BuildLink($params,$options);
}
