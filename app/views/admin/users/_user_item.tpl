<tr>
	<td>{$user->getId()}</td>
	<td>{$user->getLogin()}</td>
	<td>{$user->getName()}</td>
	<td>{$user->getEmail()}</td>
	<td>{$user->isAdmin()|display_bool}</td>
	<td>{$user->getCreatedAt()|format_datetime}</td>
	<td>{$user->getUpdatedAt()|format_datetime}</td>
	<td>
			{dropdown_menu}
				{a action=edit id=$user}<i class="glyphicon glyphicon-edit"></i> {t}Edit{/t}{/a}
				{a action=edit_password id=$user}<i class="glyphicon glyphicon-exclamation-sign"></i> {t}Set new password{/t}{/a}
				{a action=login_as_user id=$user _method=post}<i class="glyphicon glyphicon-user"></i> {t}Sign in as this user{/t}{/a}

				{if $user->isDeletable()}
					{capture assign="confirm"}{t login=$user->getLogin()|h escape=no}You are about to permanently delete user %1
	Are you sure about that?{/t}{/capture}
					{a_destroy id=$user _confirm=$confirm}<i class="glyphicon glyphicon-remove"></i> {t}Delete user{/t}{/a_destroy}
				{/if}
			{/dropdown_menu}
	</td>
</tr>
