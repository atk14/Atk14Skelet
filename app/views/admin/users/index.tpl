<h1>{button_create_new}{t}Create a new user{/t}{/button_create_new} {$page_title}</h1>

{render partial="shared/search_form"}

{if $finder->isEmpty()}

	<p>{t}No record has been found.{/t}</p>

{else}

	<table class="table table-striped table-sm">
		<thead>
			<tr class="table-dark">
				{sortable key=id}<th>#</th>{/sortable}
				{sortable key=login}<th>{t}Username{/t}</th>{/sortable}
				{sortable key=name}<th>{t}Name{/t}</th>{/sortable}
				{sortable key=email}<th>{t}Email address{/t}</th>{/sortable}
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
