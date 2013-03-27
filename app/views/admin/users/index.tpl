<h2>{$page_title}</h2>

<table>
	<thead>
		<tr>
			<th>Id</th>
			{sortable key=login}<th>{t}Username{/t}</th>{/sortable}
			<th>{t}Is admin?{/t}</th>
			{sortable key=created_at}<th>{t}Created at{/t}</th>{/sortable}
		</tr>
	</thead>

	<tbody>
		{render partial="user_item" from=$finder->getRecords() item=user}
	</tbody>
</table>
