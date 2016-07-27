<h1>{$page_title}</h1>

{render partial="shared/search_form"}

<p>{a action=create_new _class="btn btn-primary"}<i class="glyphicon glyphicon-plus-sign"></i> {t}Add new tag{/t}{/a}</p>

{if $finder->isEmpty()}

	<p>{t}No tag has been found.{/t}</p>

{else}

	<table class="table table-striped">
		<thead>
			<tr>
				{sortable key=id}<th>#</th>{/sortable}
				{sortable key=tag}<th>{t}Tag{/t}</th>{/sortable}
				{sortable key=created_at}<th>{t}Created at{/t}</th>{/sortable}
				<th></th>
			</tr>
		</thead>
		<tbody>
			{render partial=tag_item from=$finder->getRecords() item=tag}
		</tbody>
	</table>

	{paginator}

{/if}
