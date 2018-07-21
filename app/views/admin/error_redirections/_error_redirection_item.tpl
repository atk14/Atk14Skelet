<tr>
	<td>{$error_redirection->getId()}</td>
	<td>{$error_redirection->getSourceUrl()}</td>
	<td>{$error_redirection->getTargetUrl()}</td>
	<td>{$error_redirection->getCreatedAt()|format_datetime}</td>
	<td>{!$error_redirection->getLastAccessedAt()|format_datetime|default:"&mdash;"}</td>
	<td>
		{dropdown_menu}
			{a action=edit id=$error_redirection}{t}Edit{/t}{/a}
			{a_destroy id=$error_redirection}{t}Delete{/t}{/a_destroy}
		{/dropdown_menu}
	</td>
</tr>
