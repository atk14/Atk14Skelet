<tr>
	<td class="item-id">{$tag->getId()}</td>
	<td class="item-title">
		{$tag}
		{if $tag->getTag()!=$tag->getTagLocalized()}
			<small>({$tag->getTagLocalized()})</small>
		{/if}
	</td>
	<td class="item-code">{$tag->getCode()|default:$mdash}</td>
	<td class="item-created">{$tag->getCreatedAt()|format_datetime}</td>
	<td class="text-right item-actions">
		{dropdown_menu}
			{a action=edit id=$tag}{!"pencil-alt"|icon} {t}Edit{/t}{/a}
			{if $tag->isDeletable()}
				{capture assign="confirm"}{t tag=$tag|h escape=no}You are about to permanently delete tag %1
	Are you sure about that?{/t}{/capture}
				{a_destroy id=$tag _confirm=$confirm}{!"trash-alt"|icon} {t}Delete tag{/t}{/a_destroy}
			{/if}
		{/dropdown_menu}	
	</td>
</tr>
