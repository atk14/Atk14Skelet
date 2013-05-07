<tr>
	<td><time datetime="{$password_recovery->getCreatedAt()}">{$password_recovery->getCreatedAt()|format_datetime}</time></td>
	<td>{$password_recovery->getUser()->getLogin()}</td>
	<td>{$password_recovery->getEmail()}</td>
	<td>{$password_recovery->getCreatedFromAddr()}</td>
	<td>{$password_recovery->wasUsed()|display_bool}</td>
	<td><time datetime="{!$password_recovery->getRecoveredAt()}">{!$password_recovery->getRecoveredAt()|format_datetime|default:"&mdash;"}</time></td>
	<td>{!$password_recovery->getRecoveredFromAddr()|h|default:"&mdash;"}</td>
</tr>
