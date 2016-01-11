<nav class="navbar navbar-light bg-faded">
	<button class="navbar-toggler hidden-sm-up" type="button" data-toggle="collapse" data-target="#collapsingNavbar">
		&#9776;
	</button>
	<div class="collapse navbar-toggleable-xs" id="collapsingNavbar">
		<a class="navbar-brand" href="/">{"ATK14_APPLICATION_NAME"|dump_constant}</a>
		<ul class="nav navbar-nav pull-xs-right">
		{if $logged_user}
		
			{capture assign=user_profile_url}{link_to namespace="" controller=users action="detail"}{/capture}
			
			<li class="dropdown">
				<a id="dLabel" type="button" class="btn btn-secondary dropdown-toggle" data-target="#" href="{$user_profile_url}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					{t escape=no}{$logged_user->getLogin()}{/t}
					{if $logged_user->isAdmin()}
						({t}administrator{/t})
					{/if}
					<span class="caret"></span>
				</a>

				<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dLabel">
					<li class="dropdown-item"><a href="{$user_profile_url}">{t}Profile{/t}</a></li>
					{if $logged_user->isAdmin()}
						<li class="dropdown-item">{a action="main/index" namespace="admin"}{t}Administration{/t}{/a}</li>
					{/if}
					<li class="dropdown-divider"></li>
					<li class="dropdown-item">{a namespace="" action="logins/destroy" _method=post}{t}Sign out{/t}{/a}</li>
				</ul>
			</li>

		{else}

			<li class="nav-item">
				{a _class="nav-link" namespace="" action="logins/create_new"}{t}Sign in{/t}{/a}
			</li>
			<li class="nav-item">
				{a _class="nav-link" namespace="" action="users/create_new"}{t}Register{/t}{/a}
			</li>
		{/if}
		</ul>
	</div>
</nav>
