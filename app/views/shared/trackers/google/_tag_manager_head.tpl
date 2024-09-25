{assign container_id "GOOGLE_TAG_MANAGER_CONTAINER_ID"|dump_constant}
{if $container_id && $namespace==""}
<!-- Google Tag Manager -->
{literal}
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);{/literal}
})(window,document,'script','dataLayer','{$container_id}');</script>
<!-- End Google Tag Manager -->
{/if}
