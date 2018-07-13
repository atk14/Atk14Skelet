<tr>
	<td>{$redirection->getId()}</td>
	<td>{$redirection->getSourceUrl()}</td>
	<td>{$redirection->getTargetUrl()}</td>
	<td>{$redirection->getCreatedAt()|format_datetime}</td>
	<td>{!$redirection->getLastAccessedAt()|format_datetime|default:"&mdash;"}</td>
	<td>
		{dropdown_menu}
			{a action=edit id=$redirection}{t}Edit{/t}{/a}
			{a_destroy id=$redirection}{t}Delete{/t}{/a_destroy}
		{/dropdown_menu}
	</td>
</tr>
