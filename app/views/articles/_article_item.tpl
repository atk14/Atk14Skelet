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
		<div class="card-teaser">{$article->getTeaser()|markdown|strip_html|truncate:200}</div>
		{/if}
	</div>
	<div class="card-footer">
		<p class="card-meta">{render partial="author_and_date"}</p>
	</div>
{/a}
