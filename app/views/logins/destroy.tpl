<h1>{$page_title}</h1>

<p>
	{t escape=no name=$logged_user->getName()|h}You are signed in as <em>%1</em>.{/t}<br>
	{t}Click on the button to sign out.{/t}
</p>

{render partial="shared/form"}
