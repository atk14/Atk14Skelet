<h1>{$page_title}</h1>

{render partial="shared/search_form"}

{if $finder->isEmpty()}

	<p>{t}No record has been found.{/t}</p>

{else}

	<table class="table table-striped table-sm table--newssubscribers">
		<thead>
			<tr class="table-dark">
				{sortable key=id}<th class="item-id">#</th>{/sortable}
				{sortable key=email}<th class="item-email">{t}Email{/t}</th>{/sortable}
				{sortable key=name}<th class="item-title">{t}Name{/t}</th>{/sortable}
				{sortable key=created_at}<th class="item-created">{t}Subscribed since{/t}</th>{/sortable}
				<th class="item-addresscreated">{t}IP address{/t}</th>
				<th class="item-actions"></th>
			</tr>
		</thead>
		<tbody>
			{render partial="newsletter_subscriber_item" from=$finder->getRecords() item=newsletter_subscriber}
		</tbody>		
	</table>
	{paginator}

	<p><a href="{$csv_export_url}">{t}Export emails in CSV{/t}</a></p>

{/if}
