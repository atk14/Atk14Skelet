<h1>{button_create_new}{t}Add new article{/t}{/button_create_new} {$page_title}</h1>

{render partial="shared/search_form"}

{if $finder->isEmpty()}

	<p>{t}The list is empty.{/t}</p>

{else}

	<table class="table table-striped">
		<thead>
			<tr>
				{sortable key=id}<th>#</th>{/sortable}
				{sortable key=title}<th>{t}Title{/t}</th>{/sortable}
				<th>{t}Author{/t}</th>
				{sortable key=published_at}<th>{t}Date{/t}</th>{/sortable}
				<th>{t}Tags{/t}</th>
				<th>{t}Actions{/t}</th>
			</tr>
		</thead>
		<tbody>
			{render partial="article_item" from=$finder->getRecords() item=article}
		</tbody>
	</table>

	{paginator}

{/if}
