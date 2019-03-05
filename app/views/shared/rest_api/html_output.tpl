<header>
	<h1>{a action="main/index"}{$namespace}{/a} &rarr; {a}{$page_title}{/a}</h1>
</header>

<section>
	<p>HTTP Status Code: <strong>{$status_code}</strong><br>
	HTTP Status Message: <strong>{$status_message}</strong></p>

	<pre><code>{$data|print_r:true}</code></pre>
</section>