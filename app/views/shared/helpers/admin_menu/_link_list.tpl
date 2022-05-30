{a namespace="admin" controller="link_list_items" action="index" link_list_id=$link_list}{!"list"|icon} {t}Edit links{/t} {if $link_list->getCode()|strlen}({$link_list->getCode()}){/if}{/a}
