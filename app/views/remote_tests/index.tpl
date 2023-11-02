<html>
	<head>
		<title>List of Remote Tests</title>
	</head>
	<body>
	<h1>List of Remote Tests</h1>
	<p>The list is rendered for automatization. You can remove index action in {$controller} controller to prevent displaying this page.</p>
	<ul>
		{foreach from=$tests item=test}
		<li><a href="{$test.url}">{$test.name}</a></li>
		{/foreach}
	</ul>
	<p>The list of all tests in JSON format is at {link_to action="index" format="json" _with_hostname=1}
	</body>
</html>
