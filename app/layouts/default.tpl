{*
 * The page Layout template
 *
 * Placeholders
 * ------------
 * head						 	located whithin the <head> tag
 * main							the main (or default) one
 * js_script_tags				place for javascript script tags
 * js							place for javascript code
 * domready						place for domready javascript code
 *
 * Variables
 * ------------
 * $lang
 * $controller
 * $action
 * $namespace
 * $logged_user
 * $page_description
 *
 * Constants
 * ------------
 * $DEVELOPMENT
 *}
<!DOCTYPE html>
<html lang="{$lang}">

	<head>
		<meta charset="utf-8">

		<title>{trim}
			{if $controller=="main" && $action=="index" && $namespace==""}
				{"ATK14_APPLICATION_NAME"|dump_constant}
			{else}
				{$page_title} | {"ATK14_APPLICATION_NAME"|dump_constant}
			{/if}
		{/trim}</title>

		<meta name="description" content="{$page_description}" />
		<meta name="viewport" content="width=device-width,initial-scale=1.0">

		{if $DEVELOPMENT}
			{render partial="shared/layout/dev_info"}

			{stylesheet_link_tag file="../assets/vendor/bootstrap/bootstrap/css/bootstrap.css" media="screen"}
			{stylesheet_link_tag file="../assets/vendor/bootstrap/bootstrap/css/bootstrap-responsive.css" media="screen"}
			{stylesheet_link_tag file="skelet.css" media="screen"}
		{else}
			{stylesheet_link_tag file="application.min.css" media="screen"}
		{/if}

	</head>

	<body class="body_{$controller}_{$action}" data-controller="{$controller}" data-action="{$action}">
		<div class="container-fluid">
			{render partial="shared/login"}
			{render partial="shared/layout/header"}

			<div class="row-fluid">
				{if $section_navigation}
					<div class="span3">
						{render partial="shared/layout/section_navigation"}
					</div>
				{/if}

				<div class="content{if $section_navigation} span9{/if}">
					{render partial="shared/layout/flash_message"}
					{placeholder}
				</div>
			</div>

			{render partial="shared/layout/footer"}
		</div>

		{if $DEVELOPMENT}
			{javascript_script_tag file="../assets/vendor/jquery/jquery.js"}
			{javascript_script_tag file="../assets/vendor/bootstrap/bootstrap/js/bootstrap.js"}
			{javascript_script_tag file="../assets/lib/atk14.js"}
			{javascript_script_tag file="skelet.js"}
		{else}
			{javascript_script_tag file="application.min.js"}
		{/if}
	</body>
</html>
