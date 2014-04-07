<tr>
	<td>{$tag->getId()}</td>
	<td>{$tag}</td>
	<td>{$tag->getCreatedAt()|format_datetime}</td>
	<td>
		<div class="btn-group">
			<button class="btn dropdown-toggle" data-toggle="dropdown" href="#">
				<i class="glyphicon glyphicon-cog"></i>
			</button>
			<ul class="dropdown-menu pull-right">
				<li>{a action=edit id=$tag}<i class="glyphicon glyphicon-edit"></i> {t}Edit{/t}{/a}</li>
				{if $tag->isDeletable()}
					{capture assign="confirm"}{t tag=$tag|h escape=no}You are about to permanently delete tag %1
	Are you sure about that?{/t}{/capture}
					<li>{a_remote action=destroy id=$tag _method=post _confirm=$confirm}<i class="glyphicon glyphicon-remove"></i> {t}Delete tag{/t}{/a_remote}</li>
				{/if}
			</ul>
		</div>
	</td>
</tr>
