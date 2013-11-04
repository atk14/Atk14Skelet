<h1>{$page_title}</h1>

<div class="alert alert-warning">
	<p>{t}We are deeply sorry but you are not authorized to see this page!{/t}</p>

	{if $logged_user}
		<p>{t}Currently you are not logged as an admin user.{/t}</p>
	{/if}
</div>

{if !$logged_user}
	<p>{a action="logins/create_new" namespace="" _class="btn btn-default"}{t}Sign in as an admin user{/t}{/a}</p>
{/if}
