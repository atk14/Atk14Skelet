<tr>
	<td class="item-id">{$error_redirection->getId()}</td>
	<td class="item-sourceurl">{$error_redirection->getSourceUrl()}</td>
	<td class="item-targeturl">{$error_redirection->getTargetUrl()}</td>
	<td class="item-created">{$error_redirection->getCreatedAt()|format_datetime}</td>
	<td class="item-lastaccess">{!$error_redirection->getLastAccessedAt()|format_datetime|default:"&mdash;"}</td>
	<td class="item-actions text-right">
		{dropdown_menu}
			{a action=edit id=$error_redirection}{t}Edit{/t}{/a}
			{a_destroy id=$error_redirection}{t}Delete{/t}{/a_destroy}
		{/dropdown_menu}
	</td>
</tr>
