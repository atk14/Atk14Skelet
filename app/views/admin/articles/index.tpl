<h1>{button_create_new}{t}Add new article{/t}{/button_create_new} {$page_title}</h1>

{render partial="shared/search_form"}

{if $finder->isEmpty()}

	<p>{t}The list is empty.{/t}</p>

{else}

	<table class="table table-sm table-striped table--articles">
		<thead>
			<tr class="table-dark">
				{sortable key=id}<th class="item-id">#</th>{/sortable}
				<th></th>
				{sortable key=title}<th class="item-title">{t}Title{/t}</th>{/sortable}
				<th class="item-author">{t}Author{/t}</th>
				{sortable key=published_at}<th class="item-published">{t}Date{/t}</th>{/sortable}
				<th class="item-tags">{t}Tags{/t}</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			{render partial="article_item" from=$finder->getRecords() item=article}
		</tbody>
	</table>

	{paginator}

{/if}
