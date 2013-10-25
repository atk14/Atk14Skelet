<h1>{$page_title}</h1>

<table class="table">
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
			<th>{t}Your e-mail{/t}</th>
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
