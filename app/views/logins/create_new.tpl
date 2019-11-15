<header>
	<h1>{$page_title}</h1>
</header>

<section>
	{render partial="shared/form"}

	<p>{a action="password_recoveries/create_new"}{t}Have you forgotten password?{/t}{/a}</p>
</section>