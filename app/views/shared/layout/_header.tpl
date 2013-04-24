<header>
	{if $controller=="main" && $action=="index" && $namespace==""}
		<h1 id="logo"><span>{"ATK14_APPLICATION_NAME"|dump_constant}</span></h1>
	{else}
		{capture assign=link_title}{t}Go to home page{/t}{/capture}
		<h1 id="logo">{a action="main/index" namespace="" _title=$link_title}<span>{"ATK14_APPLICATION_NAME"|dump_constant}</span>{/a}</h1>
	{/if}
</header>
