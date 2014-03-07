<h1>{t}Error 403: Forbidden{/t}</h1>

<p class="alert alert-warning">
	{t escape=no uri=$request->getRequestUri()}You don`t have a permission to access <em>%1</em> on this server.{/t}<br />
</p>

{capture assign="go_to_homepage"}{a namespace="" controller=main action=index _class="btn btn-default"}{t}Go to the homepage{/t}{/a}{/capture}

{if $namespace=="admin" && $logged_user && !$logged_user->isAdmin()}

	<p>{t}Currently you are not logged as an admin user.{/t}</p>
	<p>{!$go_to_homepage}</p>

{elseif $namespace=="admin" && !$logged_user}

	<p>{!$go_to_homepage} {t}or{/t} {a action="logins/create_new" return_uri=$request->getUri() namespace="" _class="btn btn-default"}{t}Sign in as an admin user{/t}{/a}</p>

{elseif !$logged_user}

	<p>{!$go_to_homepage} {t}or{/t} {a action="logins/create_new" return_uri=$request->getUri() namespace="" _class="btn btn-default"}{t}Sign in{/t}{/a}</p>

{else}

	<p>{!$go_to_homepage}</p>

{/if}
