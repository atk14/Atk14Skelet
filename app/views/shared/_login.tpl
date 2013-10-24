<div class="row" id="row-login">
	<ul class="nav nav-pills pull-right">
		{if $logged_user}
			{capture assign=user_profile_url}{link_to namespace="" controller=users action="detail"}{/capture}

			<li class="dropdown">
				<a class="dropdown-toggle" data-toggle="dropdown" href="#">
					{t escape=no}{$logged_user->getLogin()}{/t}
					{if $logged_user->isAdmin()}
						({t}administrator{/t})
					{/if}
					<span class="caret"></span>
				</a>
				<ul class="dropdown-menu">
					<li><a href="{$user_profile_url}">{t}Profile{/t}</a></li>
					{if $logged_user->isAdmin()}
						<li>{a action="main/index" namespace="admin"}{t}Administration{/t}{/a}</li>
					{/if}
					<li class="divider"></li>
					<li>{a namespace="" action="logins/destroy" _method=post}{t}Sign out{/t}{/a}</li>
				</ul>
			</li>
		{else}
			<li>{a namespace="" action="logins/create_new"}{t}Sign in{/t}{/a}</li>
			<li class="divider-vertical"></li>
			<li>{a namespace="" action="users/create_new"}Register{/a}</li>
		{/if}
	</ul>
</div>
