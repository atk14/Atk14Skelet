<div class="content-tabs{if $class} {$class}{/if}">
  <ul class="nav nav-pills" id="{$uniqid}-tabnav" role="tablist">
    {foreach $tab_names as $tab_name}
      <li class="nav-item" role="presentation">
        <button class="nav-link{if $tab_name@iteration==1} active{/if}" id="{$uniqid}-{!$tab_name|slugify}-tab" data-toggle="pill" data-target="#{$uniqid}-{!$tab_name|slugify}" type="button" role="tab" aria-controls="{$uniqid}-{!$tab_name|slugify}" aria-selected="{if $tab_name@iteration==1}true{else}false{/if}">{!$tab_name}</button>
      </li>
    {/foreach}
  </ul>
  <div class="tab-content" id="{$uniqid}-tabcontent">
    {!$content}
  </div>
</div>