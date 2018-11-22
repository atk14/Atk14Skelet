<h1>{$page_title}</h1>

{if $finder->isEmpty()}

	<p>{t}The list is empty.{/t}</p>

{else}
	<table class="table table-striped table-sm">
		<thead>
			<tr class="table-dark">
				{sortable key=created_at}<th>{t}Created at{/t}</th>{/sortable}
				<th>{t}User{/t}</th>
				<th>{t}Email{/t}</th>
				<th>{t}Created from address{/t}</th>
				<th>{t}Password recovered?{/t}</th>
				<th>{t}Recovered at{/t}</th>
				<th>{t}Recovered from address{/t}</th>
			</tr>
		</thead>
		<body>
			{render partial=password_recovery_item from=$finder->getRecords() item=password_recovery}
		</body>
	</table>

	{paginator}
{/if}
