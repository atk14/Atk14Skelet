<div><img src="{$public}images/skelet.png" width="475" height="478" alt="ATK14 skelet" title="this is an ATK14 skelet" class="pull-right" /></div>

<div class="row-fluid">
	<div class="span5">
		<div class="well">
			<h2>{t}Features{/t}</h2>
			<ul class="nav nav-list">
				<li class="nav-header">{t}User management{/t}</li>
				<li>{a action="users/create_new"}{t}User registration{/t}{/a}</li>
				<li>{t}Blowfish passwords hashing{/t}</li>
				<li>{a action="password_recoveries/create_new"}Password recovery{/a}</li>
				<li class="nav-header">{t}Content type example{/t}</li>
				<li>{a action="news/index"}{t}News{/t}{/a}</li>
				<li class="nav-header">{t}Admin{/t}</li>
				<li>{a namespace="admin"}Basis of administration{/a}</li>
				<li class="nav-header">{t}API{/t}</li>
				<li>{a namespace="api"}Basis of RESTful API{/a}</li>
				<li class="nav-header">{t}Sitemap{/t}</li>
				<li>{a action="sitemaps/detail"}Sitemap (HTML){/a}</li>
				<li>{a action="sitemaps/index"}Sitemap (XML){/a}</li>
				<li class="nav-header">{t}Localization{/t}</li>
				{capture assign=url_en}{link_to lang=en}{/capture}
				{capture assign=url_cs}{link_to lang=cs}{/capture}
				<li><a href="{$url_en}">{t}English{/t}</a></li>
				<li><a href="{$url_cz}">{t}Czech{/t}</a></li>
				<li class="nav-header">{t}Pages{/t}</li>
				<li>{a action="main/about"}About page{/a}</li>
				<li>{a action="main/contact"}Contact page with fast contact form{/a}</li>
			</ul>
		</div>
	</div>
</div>

<div>
	<h3>{t}External links{/t}</h3>
	<ul>
		<li><a href="https://github.com/yarri/Atk14Skelet">{t}This Skelet on Github{/t}</a></li>
		<li><a href="http://www.atk14.net/">{t}ATK14 Project website{/t}</a></li>
		<li><a href="http://book.atk14.net/">{t}The ATK14 Book{/t}</a></li>
		<li><a href="http://api.atk14.net/">{t}The API reference{/t}</a></li>
		<li><a href="https://github.com/yarri/Atk14">{t}ATK14 project page on Github{/t}</a></li>
	</ul>
</div>
