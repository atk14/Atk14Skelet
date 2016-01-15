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

		<meta name="description" content="{$page_description}">
		<meta name="viewport" content="width=device-width,initial-scale=1.0">

		{if $DEVELOPMENT}
			{render partial="shared/layout/dev_info"}
		{/if}

		{stylesheet_link_tag file="$public/dist/styles/vendor.min.css" hide_when_file_not_found=true}
		{stylesheet_link_tag file="$public/dist/styles/application.min.css"}

		<!-- HTML5 shiv and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
			{javascript_script_tag file="$public/dist/scripts/html5shiv.min.js"}
			{javascript_script_tag file="$public/dist/scripts/respond.min.js"}
		<![endif]-->
	</head>

	<body class="body_{$controller}_{$action}" data-controller="{$controller}" data-action="{$action}">
		<div class="container{if $section_navigation} has-nav-section{/if}">
			{render partial="shared/login"}
			{render partial="shared/layout/header"}
			{if $breadcrumbs && sizeof($breadcrumbs)>=2} {* It makes no sense to display breadcrumbs with just 1 or no element *}
				{render partial="shared/breadcrumbs"}
			{/if}

			<div class="body">
				{if $section_navigation}
					<nav class="nav-section">
						{render partial="shared/layout/section_navigation"}
					</nav>
				{/if}

				<div class="content-main">
					{render partial="shared/layout/flash_message"}
					{placeholder}
				</div>
			</div>

			{render partial="shared/layout/footer"}
		</div>

		{javascript_script_tag file="$public/dist/scripts/vendor.min.js"}
		{javascript_script_tag file="$public/dist/scripts/application.min.js"}
	</body>
</html>
