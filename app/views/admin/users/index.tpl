<h1>{button_create_new}{t}Create a new user{/t}{/button_create_new} {$page_title}</h1>

{render partial="shared/search_form"}

{if $finder->isEmpty()}

	<p>{t}No record has been found.{/t}</p>

{else}

	<table class="table table-striped table-sm table--users">
		<thead>
			<tr class="table-dark">
				{sortable key=id}<th class="item-id">#</th>{/sortable}
				{sortable key=role}<th>{!"user"|icon}</th>{/sortable}
				{sortable key=login}<th class="item-login">{t}Username{/t}</th>{/sortable}
				{sortable key=name}<th class="item-title">{t}Name{/t}</th>{/sortable}
				{sortable key=email}<th class="item-email">{t}Email address{/t}</th>{/sortable}
				{sortable key=is_admin}<th class="item-isadmin">{t}Is admin?{/t}</th>{/sortable}
				{sortable key=created_at}<th class="item-created">{t}Created at{/t}</th>{/sortable}
				{sortable key=updated_at}<th class="item-updated">{t}Updated at{/t}</th>{/sortable}
				<th class="item-actions"></th>
			</tr>
		</thead>

		<tbody>
			{render partial="user_item" from=$finder->getRecords() item=user}
		</tbody>
	</table>

	{paginator}

{/if}
