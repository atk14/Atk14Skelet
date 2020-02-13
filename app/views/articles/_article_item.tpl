{if $article->getImageUrl()}
	{capture assign="card_style"}background-image:url({$article->getImageUrl()|img_url:"!800x800"}){/capture}
	{assign var="card_class" "card card--bg-image"}
{else}
	{assign var="card_style" ""}
	{assign var="card_class" "card"}
{/if}
{a action="articles/detail" id=$article _class=$card_class _style=$card_style}
	<div class="card-body">
		<h2 class="card-title">{$article->getTitle()}</h2>
		{if $article->getTeaser()}
		<div class="card-teaser">{$article->getTeaser()|markdown|strip_tags|truncate:200}</div>
		{/if}
	</div>
	<div class="card-footer">
		<p class="card-meta">{t author=$article->getAuthor()|user_name|h date=$article->getPublishedAt() date_human=$article->getPublishedAt()|format_date escape=no}Posted by <em>%1</em> on <time datetime="%2">%3</time>{/t}</p>
	</div>
{/a}
