<ul class="list--tree">

	<li>
		<h4>{t}Pages{/t}</h4>
		{render partial="pages" pages=$pages}
	</li>

	{*
	<li>
		<h4>{a action="users/create_new" _with_hostname=1}{t}New user registration{/t}{/a}</h4>
		<p>{t}If you don't have yet an account on this site, this is absolutely must to do procedure{/t}</p>
	</li>

	<li>
		<h4>{a action="logins/create_new" _with_hostname=1}{t}Sign in{/t}{/a}</h4>
		<p>{t}Sign in to our site{/t}</p>
	</li>

	<li>
		<h4>{a namespace="api" action="main/index" _with_hostname=1}{t}API{/t}{/a}</h4>
		<p>{t}We offer an awesome restful API{/t}</p>
	</li>
	*}


	{if $articles}
	<li>
		<h4>{t}Recent articles{/t}</h4>
		<ul class="list--tree">
			<li>{a action="articles/index" _with_hostname=1}{t}Articles{/t}{/a}</li>
		{foreach $articles as $article}
			<li>{a action="articles/detail" id=$article _with_hostname=1}{$article->getTitle()}{/a}</li>
		{/foreach}
		</ul>
	</li>
	{/if}
	
</ul>
