<tr>
	{highlight_search_query}
	<td class="item-id">{$user->getId()}</td>
	<td>
		{if !$user->isActive()}
			<span title="{t}inactive user{/t}" class="text-secondary">{!"user-times"|icon}</span>
		{elseif !$user->getPassword()|strlen}
			<span title="{t}user without password{/t}" class="text-secondary">{!"user-minus"|icon}</span>
		{elseif $user->isAdmin()}
			<span title="{t}administrator{/t}" class="text-danger">{!"user-shield"|icon}</span>
		{else}
			<span title="{t}regular user{/t}">{!"user"|icon:"regular"}</span>
		{/if}
	</td>
	<td class="item-login">{$user->getLogin()}{if !$user->isActive()} <em>({t}not active{/t})</em>{/if}</td>
	<td class="item-title">{$user->getName()}</td>
	<td class="item-email">{$user->getEmail()}</td>
	{/highlight_search_query}
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
