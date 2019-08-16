{assign var=file value=$iobject->getObject()}

<div class="file-download">
    <div class="row">
        <div class="col-xs-2 col-sm-1 icon-container">
            <a href="{$file->getUrl()}" role="button">{icon glyph="download"}</a>
        </div>
        <div class="col-xs-10 col-sm-11">
            <a href="{$file->getUrl()}" role="button">
                <span class="file-name">{$file->getTitle()}</span>
                <span class="file-meta">(.{$file->getSuffix()}, {$file->getFilesize()|format_bytes})</span></a>
        </div>
    </div>
</div>
