<h1>{button_create_new}{t}Add new tag{/t}{/button_create_new} {$page_title}</h1>

{render partial="shared/search_form"}

{if $finder->isEmpty()}

	<p>{t}No tag has been found.{/t}</p>

{else}

	<table class="table table-striped table-sm table--tags">
		<thead>
			<tr class="table-dark">
				{sortable key=id}<th class="item-id">#</th>{/sortable}
				{sortable key=tag}<th class="item-title">{t}Tag{/t}</th>{/sortable}
				{sortable key=code}<th class="item-code">{t}Code{/t}</th>{/sortable}
				{sortable key=created_at}<th class="item-created">{t}Created at{/t}</th>{/sortable}
				<th class="item-actions"></th>
			</tr>
		</thead>
		<tbody>
			{render partial=tag_item from=$finder->getRecords() item=tag}
		</tbody>
	</table>

	{paginator}

{/if}
