<tr>
	<td>{$newsletter_subscriber->getId()}</td>
	<td>{$newsletter_subscriber->getEmail()}</td>
	<td>{$newsletter_subscriber->getName()}</td>
	<td>{$newsletter_subscriber->getCreatedAt()|format_datetime}</td>
	<td>{$newsletter_subscriber->getCreatedFromAddr()}</td>
	<td>{a_destroy id=$newsletter_subscriber}{t}Delete{/t}{/a_destroy}</td>
</tr>
