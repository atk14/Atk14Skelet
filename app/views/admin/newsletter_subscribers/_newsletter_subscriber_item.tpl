<tr>
	<td>{$newsletter_subscriber->getId()}</td>
	<td>{$newsletter_subscriber->getEmail()}</td>
	<td>{$newsletter_subscriber->getName()}</td>
	<td>{$newsletter_subscriber->getCreatedAt()|format_datetime}</td>
	<td>{$newsletter_subscriber->getCreatedFromAddr()}</td>
	<td>
		{dropdown_menu}
			{a_destroy id=$newsletter_subscriber}<i class="glyphicon glyphicon-remove"></i> {t}Delete{/t}{/a_destroy}
		{/dropdown_menu}
	</td>
</tr>
