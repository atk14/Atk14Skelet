<div class="jumbotron row">
	<div class="col-sm-6">
		<h1>{t}Welcome!{/t}</h1>
		<p>{t escape=no}ATK14 Skelet is a very basic application written on top of an <a href="http://atk14.net">ATK14 framework</a>.{/t}</p>
		<h2>{t}Prepare to be be amazed by:{/t}</h2>
		<ul>
			<li>{a action="users/create_new"}{t}User registration{/t}{/a}</li>
			<li>{a action="password_recoveries/create_new"}{t}Blowfish passwords hashing{/t}{/a}</li>
			<li>{a action="news/index"}{t}"News" content type example{/t}{/a}</li>
			<li>{a namespace="admin"}Basic administration{/a}</li>
			<li>{a namespace="api"}RESTful API{/a}</li>
			<li>{t}Sitemap{/t} ({a action="sitemaps/detail"}HTML{/a}, {a action="sitemaps/index"}XML{/a})</li>
			<li>
				{t}Localization{/t}
				{capture assign=url_en}{link_to lang=en}{/capture}
				{capture assign=url_cs}{link_to lang=cs}{/capture}
				(<a href="{$url_en}">{t}English{/t}</a>, <a href="{$url_cz}">{t}Czech{/t}</a>)
			</li>
			<li>
				{t}Pages{/t}
				<ul>
					<li>{a action="main/about"}About page{/a}</li>
					<li>{a action="main/contact"}Contact page with fast contact form{/a}</li>
				</ul>
			</li>
		</ul>
	</div>

	<div class="col-sm-6">
		<img src="{$public}images/skelet.png" alt="ATK14 skelet" title="This is an ATK14 skelet." class="img-responsive pull-right" />
	</div>
</div>

<h2>{t}Further Reading{/t}</h2>
<div class="links-external btn-group btn-group-justified">
	<a href="http://www.atk14.net/" class="btn btn-info btn-lg">{t}ATK14 Project{/t}</a>
	<a href="http://book.atk14.net/" class="btn btn-info btn-lg">{t}ATK14 Book{/t}</a>
	<a href="http://api.atk14.net/" class="btn btn-info btn-lg">{t}API Reference{/t}</a>
	<a href="https://github.com/yarri/Atk14" class="btn btn-info btn-lg">{t}ATK14 on Github{/t}</a>
	<a href="https://github.com/yarri/Atk14Skelet" class="btn btn-info btn-lg">{t}ATK14 Skelet on Github{/t}</a>
</div>
