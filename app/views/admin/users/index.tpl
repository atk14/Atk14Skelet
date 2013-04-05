<h2>{$page_title}</h2>

<table>
	<thead>
		<tr>
			<th>Id</th>
			{sortable key=login}<th>{t}Username{/t}</th>{/sortable}
			<th>{t}Name{/t}</th>
			<th>{t}E-mail address{/t}</th>
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
