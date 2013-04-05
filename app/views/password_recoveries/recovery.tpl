<h2>{$page_title}</h2>

<p>
	{t login=$password_recovery->getUser()->getLogin()|h escape=no}In the form bellow set the new password for user <em>%1</em>{/t}
</p>

{render partial="shared/generic_form"}
