{t
	appname="ATK14_APPLICATION_NAME"|dump_constant|strip_tags
	login=$user->getLogin()
	email=$user->getEmail()
	name=$user->getName()
	escape=no
}Thanks for signing up for %1!

Here is your data summary

login: %2
email: %3
name: %4
{/t}
