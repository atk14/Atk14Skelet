<p>{a action=create_new _class="btn btn-primary"}<i class="glyphicon glyphicon-plus-sign"></i> {t}Nová stránka{/t}{/a}</p>

{if $finder->isEmpty()}
	<p>{t}The list is empty.{/t}</p>
{else}

	<ul class="list-group">
		{render partial="static_page_item" from=$finder->getRecords() item=static_page}
	</ul>
{/if}
