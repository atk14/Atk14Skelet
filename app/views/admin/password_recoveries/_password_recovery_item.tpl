<tr>
	<td class="item-id">{$password_recovery->getId()}</td>
	<td class="item-created"><time datetime="{$password_recovery->getCreatedAt()}">{$password_recovery->getCreatedAt()|format_datetime}</time></td>
	<td class="item-login">{$password_recovery->getUser()->getLogin()}</td>
	<td class="item-email">{$password_recovery->getEmail()}</td>
	<td class="item-addresscreated">{$password_recovery->getCreatedFromAddr()}</td>
	<td class="item-isrecovered">{$password_recovery->wasUsed()|display_bool}</td>
	<td class="item-daterecovered"><time datetime="{!$password_recovery->getRecoveredAt()}">{!$password_recovery->getRecoveredAt()|format_datetime|default:"&mdash;"}</time></td>
	<td class="item-addressrecovered">{!$password_recovery->getRecoveredFromAddr()|h|default:"&mdash;"}</td>
</tr>
