<tr>
	<td class="item-id">{$newsletter_subscriber->getId()}</td>
	<td class="item-email">{$newsletter_subscriber->getEmail()}</td>
	<td class="item-title">{$newsletter_subscriber->getName()}</td>
	<td class="item-created">{$newsletter_subscriber->getCreatedAt()|format_datetime}</td>
	<td class="item-addresscreated">{$newsletter_subscriber->getCreatedFromAddr()}</td>
	<td class="item-actions text-right">
		{dropdown_menu}
			{a_destroy id=$newsletter_subscriber}{!"trash-alt"|icon} {t}Delete{/t}{/a_destroy}
		{/dropdown_menu}
	</td>
</tr>
