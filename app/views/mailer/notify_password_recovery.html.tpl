<h2>{t user_name=$user->getName()}Hello %1!{/t}</h2>

<p>
	{t}Have you forgotten your password? To reset your password, click on the following link{/t}<br /><br />

	<a href="{$password_recovery->getUrl()}">&gt;&gt; {t}recovery link{/t} &lt;&lt;</a><br /><br />

	{t}Please note that this link is valid for 2 hours only.{/t}
</p>
