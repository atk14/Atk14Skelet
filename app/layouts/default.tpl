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
 * $context_menu
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
		{/if}

		{stylesheet_link_tag file="../assets/vendor/bootstrap/bootstrap/css/bootstrap.css" media="screen"}
		{stylesheet_link_tag file="../assets/vendor/bootstrap/bootstrap/css/bootstrap-responsive.css" media="screen"}
		{stylesheet_link_tag file="application.css" media="screen"}
	</head>

	<body id="body_{$controller}_{$action}">
		<div class="container">
			{render partial="shared/login"}

			<header>
				{if $controller=="main" && $action=="index" && $namespace==""}
					<h1 id="logo"><span>{"ATK14_APPLICATION_NAME"|dump_constant}</span></h1>
				{else}
					{capture assign=link_title}{t}Go to home page{/t}{/capture}
					<h1 id="logo">{a action="main/index" namespace="" _title=$link_title}<span>{"ATK14_APPLICATION_NAME"|dump_constant}</span>{/a}</h1>
				{/if}
			</header>

			<div class="main">
				{render partial="shared/context_menu"}
				{render partial="shared/layout/flash_message"}
				{placeholder}
			</div>

			<footer>
				{t escape=no}This site runs on <a href="http://www.atk14.net/">ATK14 Framework</a>, for now and ever after{/t}
			</footer>
		</div>

		{javascript_script_tag file="../assets/vendor/jquery/jquery.js"}
		{javascript_script_tag file="../assets/vendor/bootstrap/bootstrap/js/bootstrap.js"}
		{javascript_script_tag file="../assets/lib/atk14.js"}
		{javascript_script_tag file="application.js"}
	</body>
</html>
