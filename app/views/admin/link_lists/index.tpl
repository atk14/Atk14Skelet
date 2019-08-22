<h1>{button_create_new}{t}Nový seznam odkazů{/t}{/button_create_new} {$page_title}</h1>

{if $finder->isEmpty()}
	<p>{t}Seznam je prázdný{/t}</p>
{else}
	<table class="table table-striped">
	<thead>
		<tr>
			<th>#</th>
			<th>{t}Orientační název{/t}</th>
			<th>{t}Zobrazený text{/t}</th>
			<th>{t}URL{/t}</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		{foreach $finder->getRecords() as $link_list}
			<tr>
				<td>{$link_list->getId()}</td>
				<td>{$link_list->getName()}</td>
				<td>{$link_list->getTitle()|default:$mdash}</td>
				<td>{$link_list->getUrl()}</td>
				<td>
				{dropdown_menu}
					{a action="link_list_items/index" link_list_id=$link_list}{!"list"|icon} {t}Seznam odkazů{/t}{/a}
					{a action=edit id=$link_list}{!"edit"|icon} {t}Edit{/t}{/a}
					{a_destroy id=$link_list}{!"remove"|icon} {t}Smazat{/t}{/a_destroy}
				{/dropdown_menu}
				</td>
			</tr>
		{/foreach}
	</tbody>
	</table>
{/if}
