<tr>
	<td>{a action=edit id=$user}{$user->getId()}{/a}</td>
	<td>{$user->getLogin()}</td>
	<td>{$user->getName()}</td>
	<td>{$user->getEmail()}</td>
	<td>{$user->isAdmin()|display_bool}</td>
	<td>{$user->getCreatedAt()|format_datetime}</td>
	<td>{$user->getUpdatedAt()|format_datetime}</td>
	<td>{a action=login_as_user id=$user _method=post}{t}Sign in as this user{/t}{/a}</td>
</tr>
