<mjml>
	<mj-head>
		// Custom fonts
		<mj-font href="Inter,Helvetica,Arial,sans-serif" name="Inter"></mj-font>
		// CSS styles for HTML (mostly used inside mjml-text tags)
		<mj-style inline="inline">
			a {
				color: #fc5130;
			}
			.bodycolor {
				color: #444;
				text-decoration:none;
			}
			a.bodycolor:hover {
				text-decoration:underline;
			}
			p {
				margin: 0 0 16px 0;
			}
			p.compact {
				margin-bottom: 4px;
			}
			p.small {
				font-size: 14px;
			}
			p.nomargin {
				margin-bottom: 0
			}
			p.item-price {
				text-align: right;
			}
			.currency-main {
				font-size: 16px;
				font-weight: bold;
			}
			.product-card .currency-main {
				color: #fc5130;
			}
		</mj-style>
		<mj-attributes>
			// General styles for MJML tags
			
			<mj-body background-color="#f6f6f6" />
			<mj-all font-family="Inter,Helvetica,Arial,sans-serif" font-size="16px" line-height="1.25"  color="#444" />
			<mj-spacer height="40px" />
			<mj-divider border-color="##$brand_color##" border-width="2px" />
			<mj-section padding-top="0" padding-bottom="0" />
			<mj-button background-color="##$primary_color##" color="##$button_color##"  font-weight="bold" />
			
			
			// for development
			<!--
			<mj-wrapper border="1px dotted fuchsia" />
			<mj-spacer container-background-color="beige" />
			<mj-section border="1px dotted aqua" />
			/-->
			
			// MJML classes for MJML tags with mj-class attributes
			
			// header
			<mj-class name="header" background-color="##$brand_color##" text-align="center" full-width="full-width" padding="0" />
			// main content
			<mj-class name="bodytext" line-height="1.5" />
			<mj-class name="smalltext" color="##$text_color##" font-size="14px" line-height="1.5" />
			<mj-class name="header__logo" width="103px" height="40px" />
			// footer
			<mj-class name="footer" full-width="full-width" background-color="##$footer_bgcolor##" font-size="16px" line-height="1.25" padding-top="16px" />
			<mj-class name="footer-bottom" full-width="full-width" background-color="gray" font-size="16px" line-height="1.25" />
			<mj-class name="footertext" color="##$footer_color##" font-size="14px" line-height="1.25" />
			<mj-class name="footersmalllink" color="#fff" font-size="12px" line-height="1.25" text-transform="none" />
			// divider
			<mj-class name="thin" border-color="#999" border-width="1px" padding="0 25px" />
			// buttons
			<mj-class name="button-cta--big" font-size="20px"/>
			<mj-class name="button-cta--small" padding="0 0 0 0"/>
			// order detail
			<mj-class name="order-overview" full-width="full-width" background-color="white" />
			<mj-class name="order-item" />
			<mj-class name="product-card" padding-bottom="20px" />
			// voucher
			<mj-class name="voucher" full-width="full-width" background-color="{$brand_color}" />
		</mj-attributes>
	</mj-head>
	<mj-body>
		<mj-raw><!-- header --></mj-raw>
			{* no header is available at the moment *}
		<mj-raw><!-- /header --></mj-raw>

		<mj-section padding-top="0" padding-bottom="0">
			<mj-column>
				<mj-text mj-class="bodytext">
					{placeholder}
				</mj-text>
			</mj-column>
		</mj-section>

		<mj-section padding-top="0" padding-bottom="0">
			<mj-column>
				<mj-text mj-class="bodytext">
					{capture assign=url}{link_to action="main/index" _with_hostname=true _ssl=false}{/capture}
					<p>
						{t}Best regards{/t}<br /><br />

						<b>{t name="ATK14_APPLICATION_NAME"|dump_constant|strip_tags}%1 Support Team{/t}</b><br />
						{t}web{/t}: <a href="{$url}">{$url}</a><br />
						{t}email{/t}: <a href="mailto:{"DEFAULT_EMAIL"|dump_constant}">{"DEFAULT_EMAIL"|dump_constant}</a>
					</p>
				</mj-text>
			</mj-column>
		</mj-section>

		<mj-raw><!-- footer --></mj-raw>
			{* no footer is available at the moment *}
		<mj-raw><!-- /footer --></mj-raw>
	</mj-body>
</mjml>
