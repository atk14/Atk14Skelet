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

		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.css" type="text/css" media="all" />
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.structure.min.css" type="text/css" media="all" />
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.theme.min.css" type="text/css" media="all" />

		{stylesheet_link_tag file="../admin/dist/styles/vendor.min.css"}
		{stylesheet_link_tag file="../admin/dist/styles/application.min.css"}

		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
			<script src="{$public}admin/dist/scripts/html5shiv.min.js"></script>
			<script src="{$public}admin/dist/scripts/respond.min.js"></script>
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

		{javascript_script_tag file="{$public}/admin/dist/scripts/vendor.min.js"}
		{javascript_script_tag file="{$public}/admin/dist/scripts/application.min.js"}

		{* TODO: in DEVELOPMENT we need non-minified scripts
		{if $DEVELOPMENT}
			{javascript_script_tag file="{$public}/admin/dist/scripts/vendor.js"}
			{javascript_script_tag file="{$public}/admin/dist/scripts/application.js"}
		{else}
			{javascript_script_tag file="{$public}/admin/dist/scripts/vendor.min.js"}
			{javascript_script_tag file="{$public}/admin/dist/scripts/application.min.js"}
		{/if}
		*}
	</body>
</html>
