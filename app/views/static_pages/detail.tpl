<h1>{$static_page->getTitle()}</h1>

<div>
{!$static_page->getTeaser()}
</div>

<div>
{!$static_page->getBody()|markdown}
</div>
