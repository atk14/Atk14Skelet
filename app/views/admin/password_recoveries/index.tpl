<h1>{$page_title}</h1>

{if $finder->isEmpty()}
	<p class="alert alert-info">No records found.</p>
{else}
	<table class="table">
		<thead>
			<tr>
				{sortable key=created_at}<th>{t}Created at{/t}</th>{/sortable}
				<th>{t}User{/t}</th>
				<th>{t}E-mail{/t}</th>
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
