<h1 class="page-header">{$page_title}</h1>

{render partial="shared/context_menu"}

{if $finder->isEmpty()}

	<p>{t}The list is empty{/t}</p>

{else}

	<table class="table table-striped">
		<thead>
			<tr>
				<th>Id</th>
				<th>{t}Title{/t}</th>
			</tr>
		</thead>
		<tbody>
			{render partial="news_item" from=$finder->getRecords() item=news}
		</tbody>
	</table>

{/if}
