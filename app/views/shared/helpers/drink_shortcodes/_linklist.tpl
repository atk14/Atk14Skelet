{if $code}
  {assign "link_list" LinkList::GetInstanceByCode($code)}
{/if}
{if $link_list}{admin_menu for=$link_list align=left}{/if}
{if $link_list && $link_list->getVisibleItems()}  
  <div class="drink-shortcode linklist--shortcode{if $class} {$class}{/if}"{if $style} style="{$style}"{/if}>
    {if $link_list->getTitle() && $show_title}
      <p class="h5">{$link_list->getTitle()}</p>
    {/if}
    {if $type=="with_images"}
      <p>with images</p>
      <div class="linklist linklist--images">
        <ul class="linklist__links">
          {foreach $link_list->getVisibleItems() as $item}
          <li{if $item->getCssClass()} class="{$item->getCssClass()}"{/if}>
            <a href="{$item->getUrl()}">
              {if $item->getImageUrl()}
                <div class="link__image">
                  <img src="{$item->getImageUrl()|img_url:'800x600xcrop'}" alt="{!$item->getTitle()}" width="800" height="600" loading="lazy" class="img-fluid">
                </div>
              {/if}
              <span class="link_title">{!$item->getTitle()}</span>
            </a>
          </li>
          {/foreach}
        </ul>
      </div>
    {else}
      <p>default</p>
      <div class="linklist">
        <ul class="linklist__links">
          {foreach $link_list->getVisibleItems() as $item}
          <li{if $item->getCssClass()} class="{$item->getCssClass()}"{/if}>
            <a href="{$item->getUrl()}">{!$item->getTitle()}</a>
          </li>
          {/foreach}
        </ul>
      </div>   
    {/if}
  </div>
{/if}