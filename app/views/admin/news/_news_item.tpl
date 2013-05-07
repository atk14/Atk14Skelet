<tr>
	<td>{a action=detail id=$news namespace=""}{$news->getTitle()}{/a}</td>
	<td>{$news->getAuthor()->getLogin()}</td>
	<td><time datetime="{$news->getPublishedAt()}">{$news->getPublishedAt()|format_date}</time></td>
	<td>
		<div class="btn-group">
			<button class="btn dropdown-toggle" data-toggle="dropdown" href="#">
				<i class="icon icon-cog"></i>
			</button>
			<ul class="dropdown-menu pull-right">
				<li>{a action=edit id=$news}<i class="icon icon-edit"></i> {t}Edit{/t}{/a}</li>
				<li>{a_remote action=destroy id=$news _method=post _confirm=$confirm}<i class="icon icon-remove"></i> {t}Delete news{/t}{/a_remote}</li>
			</ul>
		</div>
	</td>
</tr>
