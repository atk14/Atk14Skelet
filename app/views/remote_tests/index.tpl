<html>
	<head>
		<title>List of Remote Tests</title>
	</head>
	<body>
	<h1>List of Remote Tests</h1>
	<p>The list is rendered for automatization. You can remove index action in {$controller} controller to prevent to display this page.</p>
	<ul>
		{foreach from=$tests item=test}
		<li>{a action=$test _with_hostname=true}{$test}{/a}</li>
		{/foreach}
	</ul>
	</body>
</html>
