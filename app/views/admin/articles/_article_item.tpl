<tr>
	<td>{$article->getId()}</td>
	<td>{a action=detail id=$article namespace=""}{$article->getTitle()}{/a}</td>
	<td>{$article->getAuthor()->getLogin()}</td>
	<td><time datetime="{$article->getPublishedAt()}">{$article->getPublishedAt()|format_date}</time></td>
	<td>{to_sentence var=$article->getTags() words_connector=" , " last_word_connector=" , "}</td>
	<td>
		<div class="btn-group">
			<button class="btn dropdown-toggle" data-toggle="dropdown" href="#">
				<i class="glyphicon glyphicon-cog"></i>
			</button>
			<ul class="dropdown-menu pull-right">
				<li>{a action=edit id=$article}<i class="glyphicon glyphicon-edit"></i> {t}Edit{/t}{/a}</li>
				<li>
					{capture assign=confirm}{t title=$article->getTitle()|h escape=false}Are you sure to delete article item
%1?{/t}{/capture}
					{a_remote action=destroy id=$article _method=post _confirm=$confirm}<i class="glyphicon glyphicon-remove"></i> {t}Delete article{/t}{/a_remote}
				</li>
			</ul>
		</div>
	</td>
</tr>
