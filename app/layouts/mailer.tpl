{placeholder}
{trim}
{capture assign=url}{link_to action="main/index" _with_hostname=true _ssl=false}{/capture}
{t
	name="ATK14_APPLICATION_NAME"|dump_constant|strip_tags
	url=$url
	email="DEFAULT_EMAIL"|dump_constant
	escape=no
}Best regards

%1 Support Team
web: %2
email: %3{/t}{/trim}
