<div class="tab-pane fade{if $name==$tab_names[0]} show active{/if}{if $class} {$class}{/if}" id="{$uniqid}-{!$name|slugify}" role="tabpanel" aria-labelledby="{$uniqid}-{!$name|slugify}-tab">
{!$content}
</div>
