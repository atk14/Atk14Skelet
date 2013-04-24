<tr>
	<td>{a action=edit id=$user}{$user->getId()}{/a}</td>
	<td>{$user->getLogin()}</td>
	<td>{$user->getName()}</td>
	<td>{$user->getEmail()}</td>
	<td>{$user->isAdmin()|display_bool}</td>
	<td>{$user->getCreatedAt()|format_datetime}</td>
	<td>{$user->getUpdatedAt()|format_datetime}</td>
	<td>
	<div class="btn-group">
		<button class="btn dropdown-toggle" data-toggle="dropdown" href="#">
			<i class="icon icon-cog"></i>
		</button>
		<ul class="dropdown-menu pull-right">
			<li>{a action=edit id=$user}<i class="icon icon-edit"></i> {t}Edit{/t}{/a}</li>
			<li>{a action=login_as_user id=$user _method=post}<i class="icon icon-user"></i> {t}Sign in as this user{/t}{/a}</li>
		</ul>
		</div>
	</td>
</tr>
