<h1>{button_create_new}{t}Add new redirection{/t}{/button_create_new} {$page_title}</h1>

{render partial="shared/search_form"}

{if $finder->isEmpty()}

	<p>{t}The list is empty.{/t}</p>

{else}

	<table class="table">
		<thead>
			{sortable key=id}<th>#</th>{/sortable}
			{sortable key=source_url}<th>{t}Source URL{/t}</th>{/sortable}
			{sortable key=target_url}<th>{t}Target URL{/t}</th>{/sortable}
			{sortable key=created_at}<th>{t}Created at{/t}</th>{/sortable}
			{sortable key=last_accessed_at}<th>{t}Last access{/t}</th>{/sortable}
			<th></th>
		</thead>

		<tbody>
			{render partial="redirection_item" from=$finder->getRecords()}
		</tbody>
	</table>

	{paginator}

{/if}
