<header>
	<h1>{$page_title}</h1>
</header>

<p>
	{t login=$password_recovery->getUser()->getLogin()|h escape=no}In the form bellow set the new password for user <em>%1</em>{/t}
</p>

{render partial="shared/form"}
