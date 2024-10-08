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
	
		{!$head_tags}
		{render partial="shared/trackers/google/tag_manager_head"}
		{render partial="shared/trackers/google/analytics"}

		<title>{trim}
			{if $controller=="main" && $action=="index" && $namespace==""}
				{$page_title|strip_tags}
			{else}
				{$page_title|strip_tags} | {"ATK14_APPLICATION_NAME"|dump_constant}
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

		{stylesheet_link_tag file="$public/dist/styles/vendor.css" hide_when_file_not_found=true}
		{stylesheet_link_tag file="$public/dist/styles/application_styles.css"}

		
		{render partial="shared/layout/favicons"}

		{placeholder for=head} {* a place for <link rel="canonical" ...>, etc. *}
	</head>

	<body class="body_{$controller}_{$action}" data-namespace="{$namespace}" data-controller="{$controller}" data-action="{$action}">
		{render partial="shared/trackers/google/tag_manager_body"}
		{render partial="shared/layout/flash_message"}
		{render partial="shared/layout/header"}
		<div class="container-fluid{if $section_navigation} has-nav-section{/if}">
			
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
					{placeholder}
				</div>
			</div>
		</div>
		{render partial="shared/layout/footer"}
		{if $DEVELOPMENT}<!-- USING_BOOTSTRAP4: {USING_BOOTSTRAP4}, USING_BOOTSTRAP5 {USING_BOOTSTRAP5}/-->{/if}
		{render partial="shared/layout/devcssinfo"}

		{javascript_script_tag file="$public/dist/scripts/vendor.min.js"}
		{javascript_script_tag file="$public/dist/scripts/application.min.js"}
		{javascript_script_tag file="$public/dist/scripts/application_es6.min.js" type="module"}
		{javascript_tag}
			{placeholder for="js"}
		{/javascript_tag}
	</body>
</html>
