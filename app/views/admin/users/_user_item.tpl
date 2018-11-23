<tr>
	<td class="item-id">{$user->getId()}</td>
	<td class="item-login">{$user->getLogin()}</td>
	<td class="item-title">{$user->getName()}</td>
	<td class="item-email">{$user->getEmail()}</td>
	<td class="item-isadmin">{$user->isAdmin()|display_bool}</td>
	<td class="item-created">{$user->getCreatedAt()|format_datetime}</td>
	<td class="item-updated">{$user->getUpdatedAt()|format_datetime}</td>
	<td class="text-right item-actions">
			{dropdown_menu}
				{a action=edit id=$user}{!"user-edit"|icon} {t}Edit{/t}{/a}
				{a action=edit_password id=$user}{!"key"|icon} {t}Set new password{/t}{/a}
				{a action=login_as_user id=$user _method=post}{!"sign-in-alt"|icon} {t}Sign in as this user{/t}{/a}

				{if $user->isDeletable()}
					{capture assign="confirm"}{t login=$user->getLogin()|h escape=no}You are about to permanently delete user %1
	Are you sure about that?{/t}{/capture}
					{a_destroy id=$user _confirm=$confirm}{!"trash-alt"|icon} {t}Delete user{/t}{/a_destroy}
				{/if}
			{/dropdown_menu}
	</td>
</tr>
