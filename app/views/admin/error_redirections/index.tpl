<h1>{button_create_new}{t}Add new redirection{/t}{/button_create_new} {$page_title}</h1>

{render partial="shared/search_form"}

{if $finder->isEmpty()}

	<p>{t}The list is empty.{/t}</p>

{else}

	<table class="table table-striped table-sm table--redirections">
		<thead>
			<tr class="table-dark">
				{sortable key=id}<th class="item-id">#</th>{/sortable}
				{sortable key=source_url}<th class="item-sourceurl">{t}Source URL{/t}</th>{/sortable}
				{sortable key=target_url}<th class="item-targeturl">{t}Target URL{/t}</th>{/sortable}
				{sortable key=created_at}<th class="item-created">{t}Created at{/t}</th>{/sortable}
				{sortable key=last_accessed_at}<th class="item-lastaccess">{t}Last access{/t}</th>{/sortable}
				<th class="item-actions"></th>
			</tr>
		</thead>

		<tbody>
			{render partial="error_redirection_item" from=$finder->getRecords()}
		</tbody>
	</table>

	{paginator}

{/if}
