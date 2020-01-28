<h1>{$page_title}</h1>

{if $logged_user}

	<p>{t user=$logged_user|h escape=no}You have been successfully registered and now you are logged in as <em>%1</em>.{/t}</p>

	<p>
		{capture assign=url}{link_to action="main/index"}{/capture}
		{t url=$url escape=no}Now you can <a href="%1">go to the homepage</a> and enjoy our website.{/t}
	</p>

{else}
	
	<p>{t}You have been successfully registered.{/t}</p>

	<p>
		{capture assign=url}{link_to action="logins/create_new"}{/capture}
		{t url=$url escape=no}Now you can <a href="%1">sign in</a> and enjoy our website.{/t}
	</p>

{/if}


{content for=head}
	<meta name="robots" content="noindex,noarchive">
{/content}
