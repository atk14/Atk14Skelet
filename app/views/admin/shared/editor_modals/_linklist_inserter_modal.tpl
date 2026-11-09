<script>
  window.linklistinserterTexts = {
    copied: "{t escape=javascript}Copied!{/t}",
    insert: "{t escape=javascript}Insert{/t}",
    save: "{t escape=javascript}Save{/t}",
  }
</script>

{* fields of a [linklist], in the dialog and in the inspector of the block view (see UTILS.LinkListFields) *}
{assign linklists LinkList::FindAll()}
<template id="linklistinserter_fields">
  <label class="form-label js--linklist-label">{t}Select a link list{/t}</label>
  <select class="form-control form-select mb-2 js--linklist">
    <option value="">{t}Select a link list{/t}</option>
    {foreach from=$linklists item=linklist}
      {if $linklist->getCode()}
      <option value="{$linklist->getCode()}">{$linklist->getSystemName()} ({$linklist->getCode()})</option>
      {/if}
    {/foreach}
  </select>
  <label class="form-label js--type-label">{t}Type{/t}</label>
  <select class="form-control form-select mb-2 js--type">
    <option value="">{t}Default{/t}</option>
    <option value="with_images">{t}Link list with images{/t}</option>
  </select>
  <div class="form-check mb-3">
    <input type="checkbox" class="form-check-input js--show-title" checked>
    <label class="form-check-label js--show-title-label">{t}Show title of the link list{/t}</label>
  </div>
  <div class="form-label js--class-label">{t}CSS class{/t}</div>
  <div class="mb-2 js--class" role="group"></div>
  <div class="form-label js--style-label">{t}CSS style{/t}</div>
  <div class="js--style" role="group"></div>
</template>

<dialog class="dialog dialog-lg" id="linklistinserter_modal" aria-labelledby="linklistinserter_label" closedby="any">
  <div class="dialog-header">
    <h1 class="dialog-title" id="linklistinserter_label">{t}Link List{/t}</h1>
    <button type="button" class="btn-close" commandfor="linklistinserter_modal" command="close" aria-label="{t}Close{/t}"></button>
  </div>
  <div class="dialog-body linklistinserter" id="linklistinserter">
    {* the fields from #linklistinserter_fields *}
  </div>
  <div class="dialog-footer d-flex justify-content-between">
    <div>
      <button type="button" class="btn btn-secondary" commandfor="linklistinserter_modal" command="close">{t}Close{/t}</button>
    </div>
    <div>
      <button type="button" class="btn btn-outline-secondary" id="linklistinserter_copy_btn">{t}Copy to clipboard{/t}</button>
      <button type="button" class="btn btn-primary" id="linklistinserter_save_btn">{t}Insert{/t}</button>
    </div>
  </div>
</dialog>
