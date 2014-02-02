<tr>
	<td>{a action=detail id=$news namespace=""}{$news->getTitle()}{/a}</td>
	<td>{$news->getAuthor()->getLogin()}</td>
	<td><time datetime="{$news->getPublishedAt()}">{$news->getPublishedAt()|format_date}</time></td>
	<td>
		<div class="btn-group">
			<button class="btn dropdown-toggle" data-toggle="dropdown" href="#">
				<i class="glyphicon glyphicon-cog"></i>
			</button>
			<ul class="dropdown-menu pull-right">
				<li>{a action=edit id=$news}<i class="glyphicon glyphicon-edit"></i> {t}Edit{/t}{/a}</li>
				<li>
					{capture assign=confirm}{t title=$news->getTitle()|h escape=false}Are you sure to delete news item
%1?{/t}{/capture}
					{a_remote action=destroy id=$news _method=post _confirm=$confirm}<i class="glyphicon glyphicon-remove"></i> {t}Delete news{/t}{/a_remote}
				</li>
			</ul>
		</div>
	</td>
</tr>
