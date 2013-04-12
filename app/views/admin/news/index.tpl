<h2>{$page_title}</h2>

{if $finder->isEmpty()}

	<p>{t}The list is empty{/t}</p>

{else}

	<table>
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
