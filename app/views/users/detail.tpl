<header>
	<h1>{$page_title}</h1>
</header>

<table class="table table-auto">
	<tbody>
		<tr>
			<th>{t}Username (login){/t}</th>
			<td>{$logged_user->getLogin()}</td>
		</tr>
		<tr>
			<th>{t}Your name{/t}</th>
			<td>{$logged_user->getName()}</td>
		</tr>
		<tr>
			<th>{t}Your email{/t}</th>
			<td>{$logged_user->getEmail()}</td>
		</tr>
		{if $logged_user->isAdmin}
			<tr>
				<th>{t}Are you admin?{/t}</th>
				<td>{t}Yes, you are{/t}</td>
			</tr>
		{/if}
	</tbody>
</table>

<ul>
	<li>{a action="edit"}{t}Change your account data{/t}{/a}</li>
	<li>{a action="edit_password"}{t}Change your password{/t}{/a}</li>
</ul>
