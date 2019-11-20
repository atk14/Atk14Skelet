{if !$edit_title}{assign edit_title "{t}Edit object{/t}"}{/if}

{if $only_edit}

	{a namespace="admin" controller=$admin_controller action="edit" id=$object}{!"edit"|icon} {$edit_title}{/a}

{else}

	{a namespace="admin" controller=$admin_controller action="edit" id=$object}{!"edit"|icon} {$edit_title}{/a}
	{a namespace="admin" controller=$admin_controller action="create_new"}{!"plus"|icon} {t}Create new object{/t}{/a}
	{a namespace="admin" controller=$admin_controller action="index"}{!"hammer"|icon} Objects administration{/a}

{/if}
