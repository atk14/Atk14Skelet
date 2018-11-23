<h1>{$page_title}</h1>

{if $finder->isEmpty()}

	<p>{t}The list is empty.{/t}</p>

{else}
	<table class="table table-striped table-sm table--pwdrecoveries">
		<thead>
			<tr class="table-dark">
				{sortable key=created_at}<th class="item-created">{t}Created at{/t}</th>{/sortable}
				<th class="item-login">{t}User{/t}</th>
				<th class="item-email">{t}Email{/t}</th>
				<th class="item-addresscreated">{t}Created from address{/t}</th>
				<th class="item-isrecovered">{t}Password recovered?{/t}</th>
				<th class="item-daterecovered">{t}Recovered at{/t}</th>
				<th class="item-addressrecovered">{t}Recovered from address{/t}</th>
			</tr>
		</thead>
		<body>
			{render partial=password_recovery_item from=$finder->getRecords() item=password_recovery}
		</body>
	</table>

	{paginator}
{/if}
