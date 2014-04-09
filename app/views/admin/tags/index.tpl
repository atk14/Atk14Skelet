<h1>{$page_title}</h1>

{form _class="form-search"}
	{!$form.search}
	<button type="submit" class="btn">{t}Search tags{/t}</button>
{/form}

<p>{a action=create_new _class="btn btn-primary"}<i class="glyphicon glyphicon-plus-sign"></i> {t}Add New{/t}{/a}</p>

{if $finder->isEmpty()}

	<p>{t}No tag has been found.{/t}</p>

{else}

	<table class="table table-striped">
		<thead>
			<tr>
				<th>#</th>
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
