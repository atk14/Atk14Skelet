<tr>
	<td>{$article->getId()}</td>
	<td>{a action=detail id=$article namespace=""}{$article->getTitle()}{/a}</td>
	<td>{$article->getAuthor()->getLogin()}</td>
	<td><time datetime="{$article->getPublishedAt()}">{$article->getPublishedAt()|format_date}</time></td>
	<td>{to_sentence var=$article->getTags() words_connector=" , " last_word_connector=" , "}</td>
	<td>
		{capture assign=confirm}{t title=$article->getTitle()|h escape=false}Are you sure to delete article item
%1?{/t}{/capture}

		{dropdown_menu}
			{a action=edit id=$article}<i class="glyphicon glyphicon-edit"></i> {t}Edit{/t}{/a}
			{a_destroy id=$article _confirm=$confirm}<i class="glyphicon glyphicon-remove"></i> {t}Delete article{/t}{/a_destroy}
		{/dropdown_menu}
	</td>
</tr>
