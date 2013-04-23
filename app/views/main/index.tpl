<img src="{$public}images/skelet.png" width="475" height="478" alt="ATK14 skelet" title="this is an ATK14 skelet" />

<h3>{t}Skelet consists of{/t}</h3>

<ul>
	<li>{a action="users/create_new"}{t}User registration{/t}{/a}</li>
	<li>{t}Blowfish passwords hashing{/t}</li>
	<li>{a action="password_recoveries/create_new"}Password recovery{/a}</li>
	<li>{a action="news/index"}{t}News{/t}{/a}</li>
	<li>{a namespace="admin"}Bases of administration{/a}</li>
	<li>{a namespace="api"}Bases of Restful API{/a}</li>
	<li>Context menu</li>
	<li>{a action="sitemaps/detail"}Sitemap{/a} ({a action="sitemaps/index"}xml{/a})</li>
	<li>
	{capture assign=url_en}{link_to lang=en}{/capture}
	{capture assign=url_cs}{link_to lang=cs}{/capture}
	{t escape=no url_en=$url_en url_cs=$url_cs}<a href="%1">English</a> and <a href="%2">czech</a> localization{/t}<br />
	</li>
	<li>{a action="main/about"}About page{/a}</li>
	<li>{a action="main/contact"}Contact page with fast contact form{/a}</li>
</ul>

<h3>{t}External links{/t}</h3>
<ul>
	<li><a href="https://github.com/yarri/Atk14Skelet">{t}This Skelet on Github{/t}</a></li>
	<li><a href="http://www.atk14.net/">{t}ATK14 Project website{/t}</a></li>
	<li><a href="http://book.atk14.net/">{t}The ATK14 Book{/t}</a></li>
	<li><a href="http://api.atk14.net/">{t}The API reference{/t}</a></li>
	<li><a href="https://github.com/yarri/Atk14">{t}ATK14 project page on Github{/t}</a></li>
</ul>
