<h1>{$page_title}</h1>

{form _class="form-search"}
	{!$form.search}
	<button type="submit" class="btn">{t}Search articles{/t}</button>
{/form}

<p>{a action=create_new _class="btn btn-primary"}<i class="glyphicon glyphicon-plus-sign"></i> {t}Add New{/t}{/a}</p>

{if $finder->isEmpty()}

	<p>{t}The list is empty.{/t}</p>

{else}

	<table class="table table-striped">
		<thead>
			<tr>
				<th>#</th>
				<th>{t}Title{/t}</th>
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