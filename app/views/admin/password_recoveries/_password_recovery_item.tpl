<tr>
	<td>{$password_recovery->getId()}</td>
	<td>{$password_recovery->getCreatedAt()|format_datetime}</td>
	<td>{$password_recovery->getUser()->getLogin()}</td>
	<td>{$password_recovery->getEmail()}</td>
	<td>{$password_recovery->getCreatedFromAddr()}</td>
	<td>{$password_recovery->wasUsed()|display_bool}</td>
	<td>{!$password_recovery->getRecoveredAt()|format_datetime|default:"&mdash;"}</td>
	<td>{!$password_recovery->getRecoveredFromAddr()|h|default:"&mdash;"}</td>
</tr>