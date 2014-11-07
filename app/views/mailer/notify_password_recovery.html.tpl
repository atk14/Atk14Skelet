<h2>{t user_name=$user->getName()}Hello %1!{/t}</h2>

<p>
	{t}Have you forgotten you password? To reset your password, click on the link below{/t}<br /><br />

	<a href="{$password_recovery->getUrl()}">{$password_recovery->getUrl()}</a><br /><br />

	{t}Please note that this link is valid for 2 hours only.{/t}
</p>
