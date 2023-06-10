<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
{foreach $langs as $l}
	<sitemap><loc>
		{link_to action="detail" lang=$l format=xml _with_hostname=1}
	</loc></sitemap>
{/foreach}	
</sitemapindex>
