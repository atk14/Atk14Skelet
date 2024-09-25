{assign container_id "GOOGLE_TAG_MANAGER_CONTAINER_ID"|dump_constant}
{if $container_id && $namespace==""}
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id={$container_id}" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
{/if}
