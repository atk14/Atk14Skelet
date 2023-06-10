{*
	Renders page H1 heading with optional teaser and small image
	{render partial="shared/layout/content_header" title=$page->getTitle()}
	{render partial="shared/layout/content_header" title=$page->getTitle() teaser=$page->getTeaser()|markdown image=$image tag="h1" tags=$tags meta=$meta colorbg=true}

	title: page title
	teaser: page teaser
	meta: typically author of the post, publish date...
	tag: heading html tag used (default "h1")
	tags: array of tags
*}
{if !$tag}
	{assign var=tag "h1"}
{/if}
<header class="content-header">
	<div class="content-header__text{if $colorbg} content-header__text--dark{/if}">
		{if $tags}
		<div class="tags">
			{!"tag"|icon}
			{foreach $tags as $tag}
				{if !$tag@first}/{/if}
				{a action="articles/index" tag_id=$tag _class="badge badge-primary"}{$tag->getTagLocalized()}{/a}
			{/foreach}
		</div>
		{/if}
		<{$tag} class="h1">{!$title}</{$tag}>
		{if $author|trim}
		<div class="author">{!$author}</div>
		{/if}
		{if $teaser|trim}
		<div class="lead">
			{!$teaser}
		</div>
		{if $meta|trim}
			<p class="text-muted">{!$meta}</p>
		{/if}
		{/if}
	</div>
</header>
