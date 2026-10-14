{*
 * Modal for inserting objects (pictures, galleries, videos, files...) into Markdown text,
 * see public/admin/scripts/utils/iobject_inserter.js
 *
 * Objects linked to $iobjects_for are offered; without it (e.g. a record not saved yet) there is just a message.
 *
 *	{render partial="shared/editor_modals" iobjects_for=$page}
 *}
<script>
  window.iobjectinserterTexts = {
    insert: "{t escape=javascript}Insert{/t}",
    save: "{t escape=javascript}Save{/t}",
    loading: "{t escape=javascript}Loading{/t}...",
    error: "{t escape=javascript}The objects could not be loaded{/t}",
    empty: "{t escape=javascript}There are no objects yet. Add one by the buttons below.{/t}",
    notFound: "{t escape=javascript}No object matches the search{/t}",
    untitled: "{t escape=javascript}untitled{/t}"
  };
</script>

<div class="modal fade" id="iobjectinserter_modal" tabindex="-1" aria-labelledby="iobjectinserter_label" aria-hidden="true"{if isset($iobjects_for) && $iobjects_for} data-table_name="{$iobjects_for->getTableName()}" data-record_id="{$iobjects_for->getId()}"{/if}>
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fs-5" id="iobjectinserter_label">{t}Objects{/t}</h5>
        {if USING_BOOTSTRAP5}
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        {else}
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        {/if}
      </div>
      <div class="modal-body iobjectinserter">
        {if isset($iobjects_for) && $iobjects_for}
          <input type="search" class="form-control mb-3 js--filter" placeholder="{t}Search by title{/t}">
          <p class="iobjectinserter__message js--message"></p>
          <div class="iobjectinserter__list js--list"></div>

          <div class="iobjectinserter__add">
            {assign class "btn btn-sm btn-outline-primary"}
            {a action="pictures/create_new" table_name=$iobjects_for->getTableName() record_id=$iobjects_for _class=$class _target="_blank"}{!"camera"|icon} {t}Add image{/t}{/a}
            {a action="galleries/create_new" table_name=$iobjects_for->getTableName() record_id=$iobjects_for _class=$class _target="_blank"}{!"images"|icon} {t}Add photogallery{/t}{/a}
            {a action="files/create_new" table_name=$iobjects_for->getTableName() record_id=$iobjects_for _class=$class _target="_blank"}{!"file"|icon} {t}Add attachment{/t}{/a}
            {a action="videos/create_new" table_name=$iobjects_for->getTableName() record_id=$iobjects_for _class=$class _target="_blank"}{!"video"|icon} {t}Add video{/t}{/a}
            <div class="form-text text-muted small mt-1">{t}A new object is added in a new tab; it appears here when you come back.{/t}</div>
          </div>
        {else}
          <p>{t}Objects can be added after the record is saved for the first time.{/t}</p>
        {/if}
      </div>
      <div class="modal-footer d-flex justify-content-between">
        <div>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-dismiss="modal">{t}Close{/t}</button>
        </div>
        <div>
          <button type="button" class="btn btn-primary" id="iobjectinserter_save_btn" disabled>{t}Insert{/t}</button>
        </div>
      </div>
    </div>
  </div>
</div>
