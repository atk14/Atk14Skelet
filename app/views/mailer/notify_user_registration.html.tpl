<h2>{t appname="ATK14_APPLICATION_NAME"|dump_constant}Thanks for signing up for %1!{/t}</h2>

<p>
	{t}Here is your data summary{/t}
</p>

<ul>
	<li>{t}login{/t}: {$user->getLogin()}</li>
	<li>{t}email{/t}: {$user->getEmail()}</li>
	<li>{t}name{/t}: {$user->getName()}</li>
</ul>
