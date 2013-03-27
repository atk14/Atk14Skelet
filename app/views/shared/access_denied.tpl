<h2>{$page_title}</h2>

<p>{t}We are deeply sorry but you are not authorized to see this page!{/t}</p>

{if $logged_user}
	<p>{t}Currently you are not logged as an admin user.{/t}</p>
{else}
	<p>{a action="logins/create_new" namespace=""}{t}Please, sign in as an admin user{/t}{/a}</p>	
{/if}
