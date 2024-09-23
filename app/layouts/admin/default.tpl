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
<html lang="{$lang}" class="no-js">

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

		{* Indication of active javascript *}
		{javascript_tag}
			document.documentElement.className = document.documentElement.className.replace( /\bno-js\b/, "js" );
		{/javascript_tag}

		{stylesheet_link_tag file="$public/admin/dist/styles/vendor.css"}
		{stylesheet_link_tag file="$public/admin/dist/styles/application_styles.css"}

		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
			{javascript_script_tag file="$public/admin/dist/scripts/html5shiv.min.js"}
			{javascript_script_tag file="$public/admin/dist/scripts/respond.min.js"}
		<![endif]-->
		
		{render partial="shared/layout/favicons"}

		{placeholder for=head} {* a place for <link rel="canonical" ...>, etc. *}

		<meta name="robots" content="noindex,noarchive">
	</head>

	<body class="body_{$controller}_{$action}" data-namespace="{$namespace}" data-controller="{$controller}" data-action="{$action}" data-bs-theme="{if $request->getCookieVar("dark_mode")}dark{else}light{/if}">
		{render partial="shared/layout/flash_message"}
		<div class="body-wrap">
			{render partial="shared/layout/header"}
		
			{if $breadcrumbs && sizeof($breadcrumbs)>=2} {* It makes no sense to display breadcrumbs with just 1 or no element *}
				{render partial="shared/breadcrumbs"}
			{/if}
			<div class="body{if $section_navigation} has-nav-section{/if}">
					{if $section_navigation}
						<nav class="nav-section">
							{render partial="shared/layout/section_navigation"}
						</nav>
					{/if}

					<div class="content-main">
						{placeholder}
					</div>

			</div>
		</div>
		{if $DEVELOPMENT}<!-- USING_BOOTSTRAP4: {USING_BOOTSTRAP4}, USING_BOOTSTRAP5 {USING_BOOTSTRAP5}/-->{/if}
		<a href="#" id="js-scroll-to-top" title="{t}Nahoru{/t}">{!"arrow-up"|icon}</a>
		{javascript_script_tag file="$public/admin/dist/scripts/vendor.min.js"}
		{javascript_script_tag file="$public/admin/dist/scripts/application.min.js"}

		{javascript_tag}
			{placeholder for="js"}
		{/javascript_tag}
	</body>
</html>