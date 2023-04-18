<div class="iobject--contact-group contact-group--shortcode fullwidth{if $class} {$class}{/if}">
  {if $title}
    <{$title_tag|default:"h3"} class="iobject__heading">{$title}</{$title_tag|default:"h3"}>
  {/if}
  <div class="iobject__cards">
    {!$content}
  </div>
</div>
