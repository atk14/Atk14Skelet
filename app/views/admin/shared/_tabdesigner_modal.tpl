<script>
  window.tabdesignerTexts = {
    copied: "{t}Copied!{/t}",
  }
</script>

<div class="modal fade" id="tabdesigner_modal" tabindex="-1" aria-labelledby="fa_tabdesigner_label" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fs-5" id="fa_tabdesigner_label">{t}Tabs{/t}</h5>
        {if USING_BOOTSTRAP5}
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        {else}
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        {/if}
      </div>
      <div class="modal-body tabdesigner" id="tabdesigner">
        
      </div>
    </div>
  </div>
</div>
