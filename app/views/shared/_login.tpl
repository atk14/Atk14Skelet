<div class="clearfix">
	<ul class="nav nav-pills float-right">
		{if $logged_user}
			{capture assign=user_profile_url}{link_to namespace="" controller=users action="detail"}{/capture}

			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
					{t escape=no}{$logged_user->getLogin()}{/t}
					{if $logged_user->isAdmin()}
						({t}administrator{/t})
					{/if}
				</a>
				<div class="dropdown-menu">
					<a href="{$user_profile_url}" class="dropdown-item">{t}Profile{/t}</a>
					{if $logged_user->isAdmin()}
						{a action="main/index" namespace="admin" _class="dropdown-item"}{t}Administration{/t}{/a}
					{/if}
					<div class="dropdown-divider"></div>
					{a namespace="" action="logins/destroy" _method=post _class="dropdown-item"}{t}Sign out{/t}{/a}
				</div>
			</li>
		{else}
			<li class="nav-item"><a href="{link_to namespace="" action="logins/create_new"}" class="nav-link">{t}Sign in{/t}</a></li>
			<li class="nav-item"><a href="{link_to namespace="" action="users/create_new"}" class="nav-link">{t}Register{/t}</a></li>
		{/if}
	</ul>
</div>
