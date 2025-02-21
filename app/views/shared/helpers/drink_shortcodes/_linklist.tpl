{if $code}
  {assign "link_list" LinkList::GetInstanceByCode($code)}
{/if}
{if $link_list}{admin_menu for=$link_list align=left}{/if}
{if $link_list && $link_list->getVisibleItems()}  
  <div class="drink-shortcode linklist--shortcode">
    {if $link_list->getTitle()}
      <p class="h5">{$link_list->getTitle()}</p>
    {/if}

    <div class="linklist">
      <ul class="linklist__links">
        {foreach $link_list->getVisibleItems() as $item}
        <li{if $item->getCssClass()} class="{$item->getCssClass()}"{/if}>
          <a href="{$item->getUrl()}">{!$item->getTitle()}</a>
        </li>
        {/foreach}
      </ul>
    </div>
  </div>
{/if}