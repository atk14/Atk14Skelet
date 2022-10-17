<html>
<head>
	<meta charset="UTF-8">
</head>
<body>
	{placeholder}

	{capture assign=url}{link_to action="main/index" _with_hostname=true}{/capture}
	<p>
		{t}Best regards{/t}<br /><br />

		<b>{t name="ATK14_APPLICATION_NAME"|dump_constant|strip_tags}%1 Support Team{/t}</b><br />
		{t}web{/t}: <a href="{$url}">{$url}</a><br />
		{t}email{/t}: <a href="mailto:{"DEFAULT_EMAIL"|dump_constant}">{"DEFAULT_EMAIL"|dump_constant}</a>
	</p>
</body>
</html>
