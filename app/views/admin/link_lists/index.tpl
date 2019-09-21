<h1>{button_create_new}{t}Create new link list{/t}{/button_create_new} {$page_title}</h1>

{if !$link_lists}

	<p>{t}The list is empty.{/t}</p>

{else}

	<table class="table table-sm table-striped">
		<thead>
			<tr class="table-dark">
				<th>#</th>
				<th>{t}System name{/t}</th>
				<th>{t}Displayed title{/t}</th>
				<th>{t}Code{/t}</th>
				<th>{t}Count of links{/t}</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			{foreach $link_lists as $link_list}
				<tr>
					<td>{$link_list->getId()}</td>
					<td>{$link_list->getSystemName()}</td>
					<td>{$link_list->getTitle()|default:$mdash}</td>
					<td>{$link_list->getCode()|default:$mdash}</td>
					<td>{$link_list->getItems()|count}</td>
					<td>
					{dropdown_menu}
						{a action="link_list_items/index" link_list_id=$link_list}{!"list"|icon} {t}Link list{/t}{/a}
						{a action=edit id=$link_list}{!"edit"|icon} {t}Edit{/t}{/a}
						{a_destroy id=$link_list}{!"remove"|icon} {t}Delete{/t}{/a_destroy}
					{/dropdown_menu}
					</td>
				</tr>
			{/foreach}
		</tbody>
	</table>

{/if}
