<h1>{$page_title}</h1>

<p>{t user=$user|h escape=no}On this page you can set a new password to the user <em>%1</em>.{/t}</p>

<p>
	{if $user->getPassword()|strlen==0}
		{t}At the moment the user has no password set. So he can't signed in.{/t}
	{else}
		{t}At the moment no one knows the actual user's password because passwords are not stored in plain text form.{/t}
	{/if}
</p>

{render partial="shared/form"}
