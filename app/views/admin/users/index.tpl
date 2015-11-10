<h1>{$page_title}</h1>

{render partial="shared/search_form"}

{if $finder->isEmpty()}

	<p>{t}No record has been found.{/t}</p>

{else}

	<table class="table table-striped">
		<thead>
			<tr>
				<th>#</th>
				{sortable key=login}<th>{t}Username{/t}</th>{/sortable}
				<th>{t}Name{/t}</th>
				<th>{t}Email address{/t}</th>
				{sortable key=is_admin}<th>{t}Is admin?{/t}</th>{/sortable}
				{sortable key=created_at}<th>{t}Created at{/t}</th>{/sortable}
				{sortable key=updated_at}<th>{t}Updated at{/t}</th>{/sortable}
				<th></th>
			</tr>
		</thead>

		<tbody>
			{render partial="user_item" from=$finder->getRecords() item=user}
		</tbody>
	</table>

	{paginator}

{/if}
