<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
{foreach $langs as $l}
	<url><loc>
		{link_to action="detail" lang=$l format=xml _with_hostname=1}
	</loc></url>
{/foreach}	
</urlset>
