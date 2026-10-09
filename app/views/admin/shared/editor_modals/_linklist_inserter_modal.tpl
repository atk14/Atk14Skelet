<script>
  window.linklistinserterTexts = {
    copied: "{t escape=javascript}Copied!{/t}",
    insert: "{t escape=javascript}Insert{/t}",
    save: "{t escape=javascript}Save{/t}",
  }
</script>

<div class="modal fade" id="linklistinserter_modal" tabindex="-1" aria-labelledby="linklistinserter_label" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fs-5" id="linklistinserter_label">{t}Link List{/t}</h5>
        {if USING_BOOTSTRAP5}
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        {else}
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        {/if}
      </div>
      <div class="modal-body linklistinserter" id="linklistinserter">

        {assign linklists LinkList::FindAll()}
        <label for="linklistinserter_linklist" class="form-label">{t}Select a link list{/t}</label>
        <select class="form-control form-select mb-2" id="linklistinserter_linklist">
          <option value="">{t}Select a link list{/t}</option>
          {foreach from=$linklists item=linklist}
            {if $linklist->getCode()}
            <option value="{$linklist->getCode()}">{$linklist->getSystemName()} ({$linklist->getCode()})</option>
            {/if}
          {/foreach}
        </select>
        <label for="linklistinserter_style" class="form-label">{t}Style{/t}</label>
        <select class="form-control form-select mb-2" id="linklistinserter_style">
          <option value="">Default</option>
          <option value="with_images">Link list with images</option>
          <option value="cards">Cards</option>
        </select>
        <label for="linklistinserter_class" class="form-label">{t}CSS class{/t}</label>
        <input type="text" class="form-control" id="linklistinserter_class">
      </div>
      <div class="modal-footer d-flex justify-content-between">
        <div>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-dismiss="modal">{t}Close{/t}</button>
        </div>
        <div>
          <button type="button" class="btn btn-outline-secondary" id="linklistinserter_copy_btn">{t}Copy to clipboard{/t}</button>
          <button type="button" class="btn btn-primary" id="linklistinserter_save_btn">{t}Insert{/t}</button>
        </div>
      </div>
    </div>
  </div>
</div>
