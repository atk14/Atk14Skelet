<h1>{a action="main/index"}{$namespace}{/a} &rarr; {a}{$page_title}{/a}</h1>

<p>HTTP Status Code: {$status_code}<br>
HTTP Status Message: {$status_message}</p>

<pre><code>{$data|print_r:true}</code></pre>
