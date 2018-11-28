{if !$title}
	{capture assign=title}{t}Create a new record{/t}{/capture}
{/if}
<a href="{$create_new_url}"{foreach $attrs as $attr_k => $attr_v} {$attr_k}="{$attr_v}"{/foreach}><i class="fas fa-plus-circle" title="{$title}"></i> <span class="sr-only">{$title}</span></a>
