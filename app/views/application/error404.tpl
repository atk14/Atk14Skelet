{if $page}

	{admin_menu for=$page}

	<h1>{$page->getTitle()}</h1>

	{if $page->getTeaser()}
		<div class="lead">
			{!$page->getTeaser()|markdown}
		</div>
	{/if}

	{!$page->getBody()|markdown}

{else}

	<h1>{t}Error 404: Page not found{/t}</h1>

	<p>
		{t escape=no uri=$request->getRequestUri()}We are deeply sorry, but the page on the URI <em>%1</em> wasn't found.{/t}<br />
		{no_spam}{t email="DEFAULT_EMAIL"|dump_constant}Please, write us to %1 if you think this is our mistake.{/t}{/no_spam}
	</p>

	<p>{a namespace="" controller=main action=index}{t}Go to the homepage{/t}{/a}</p>

{/if}
