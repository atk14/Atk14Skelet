<!DOCTYPE html>
<html lang="{$lang}" class="no-js">

	<head>
		<meta charset="utf-8">

		<title>{$page_title|strip_tags} | {"ATK14_APPLICATION_NAME"|dump_constant}</title>

		<meta name="description" content="{$page_description}">
		<meta name="viewport" content="width=device-width,initial-scale=1.0">

		{if $DEVELOPMENT}
			{render partial="shared/layout/dev_info"}
		{/if}

		{* Indication of active javascript *}
		{javascript_tag}
			document.documentElement.className = document.documentElement.className.replace( /\bno-js\b/, "js" );
		{/javascript_tag}

		{stylesheet_link_tag file="$public/dist/styles/vendor.min.css" hide_when_file_not_found=true}
		{stylesheet_link_tag file="$public/dist/styles/application.min.css"}

		<!-- HTML5 shiv and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
			{javascript_script_tag file="$public/dist/scripts/html5shiv.min.js"}
			{javascript_script_tag file="$public/dist/scripts/respond.min.js"}
		<![endif]-->
		
		{render partial="shared/layout/favicons"}

		{placeholder for=head} {* a place for <link rel="canonical" ...>, etc. *}

		<meta name="robots" content="noindex,noarchive">
	</head>

	<body class="body_{$controller}_{$action}" data-namespace="{$namespace}" data-controller="{$controller}" data-action="{$action}">

		<nav class="navbar navbar-dark bg-dark">
			<div class="container-fluid">
				<a href="{link_to namespace="" controller="main" action="index"}" class="navbar-brand">{"ATK14_APPLICATION_NAME"|dump_constant}</a>
			</div>
		</nav>

		<div class="container-fluid">
			{if $breadcrumbs && sizeof($breadcrumbs)>=2} {* It makes no sense to display breadcrumbs with just 1 or no element *}
				{render partial="shared/breadcrumbs"}
			{/if}

			<div class="body">
				<div class="content-main">
					{render partial="shared/layout/flash_message"}
					{placeholder}
				</div>
			</div>
		</div>
		
		{render partial="shared/layout/devcssinfo"}

		{javascript_script_tag file="$public/dist/scripts/vendor.min.js"}
		{javascript_script_tag file="$public/dist/scripts/application.min.js"}
	</body>
</html>
