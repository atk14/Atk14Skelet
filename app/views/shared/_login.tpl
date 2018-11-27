<nav class="navbar navbar-dark bg-dark navbar-expand-md nav-login">
	<div class="container">
		{assign var=appname value="ATK14_APPLICATION_NAME"|dump_constant}
		{a action="main/index" namespace="" _title=$link_title _class="navbar-brand"}
			{if $controller=="main" && $action=="index" && $namespace==""}
				<h1>{$appname}</h1>
			{else}
				<span class="h1">{$appname}</span>
			{/if}
		{/a}		
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
			{!"bars"|icon}
		</button>
		<div class="collapse navbar-collapse justify-content-end" id="navbarNavDropdown">
			<ul class="navbar-nav">
				{if $logged_user}
					{capture assign=user_profile_url}{link_to namespace="" controller=users action="detail"}{/capture}
					<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
							{!"user"|icon} {$logged_user->getLogin()}
						</a>
						<div class="dropdown-menu">
							<a href="{$user_profile_url}" class="dropdown-item">{t}Profile{/t}</a>
							<div class="dropdown-divider"></div>
							{a namespace="" action="logins/destroy" _method=post _class="dropdown-item"}{t}Sign out{/t}{/a}
						</div>
					</li>
					{if $logged_user->isAdmin()}
						<li class="nav-item">
							{a action="main/index" namespace="admin" _class="nav-link"}{t}Administration{/t}{/a}
						</li>
					{/if}
			{else}
				<li class="nav-item"><a href="{link_to namespace="" action="logins/create_new"}" class="nav-link">{t}Sign in{/t}</a></li>
				<li class="nav-item"><a href="{link_to namespace="" action="users/create_new"}" class="nav-link">{t}Register{/t}</a></li>
			{/if}
			</ul>
		</div>
	</div>
</nav>
