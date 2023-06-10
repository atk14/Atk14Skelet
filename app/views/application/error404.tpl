{capture assign="page_title"}{t}Error 404: Page not found{/t}{/capture}
{render partial="shared/layout/content_header" title=$page_title}
<p>
	{t escape=no uri=$request->getRequestUri()}We are deeply sorry, but the page on the URI <em>%1</em> wasn't found.{/t}
</p>

<p>{a namespace="" controller=main action=index}{t}Go to the homepage{/t}{/a}</p>
