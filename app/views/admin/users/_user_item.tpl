<tr>
	<td>{a action=edit id=$user}{$user->getId()}{/a}</td>
	<td>{$user->getLogin()}</td>
	<td>{$user->isAdmin()|display_bool}</td>
	<td>{$user->getCreatedAt()|format_datetime}</td>
</tr>
