<header>
	{assign var=appname value="ATK14_APPLICATION_NAME"|dump_constant}

	<div id="logo">
		{if $controller=="main" && $action=="index" && $namespace==""}
			<h1>{$appname}</h1>
		{else}
			{capture assign=link_title}{t}Go to home page{/t}{/capture}
			{a action="main/index" namespace="" _title=$link_title}{$appname}{/a}
		{/if}
	</div>
</header>
