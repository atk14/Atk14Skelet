<?php
class TcHreflang extends TcBase {

	/**
	 * Extracts hreflang values from the controller's head_tags into a simple map:
	 *   ["en" => "http://…/en/", "cs" => "http://…/cs/", "x-default" => "http://…/en/", …]
	 */
	protected function _get_hreflang_map($ctrl) {
		$map = [];
		$link_tags = $ctrl->head_tags->getLinkTags();
		foreach($link_tags["alternate"] ?? [] as $link) {
			$data = $link->getData();
			$map[$data["hreflang"]] = $data["href"];
		}
		return $map;
	}

	/**
	 * Calls the protected _setup_hreflang_for_head_tags() on the given controller
	 * with a fresh HeadTags instance so that previous state doesn't interfere.
	 */
	protected function _invoke_setup_hreflang($ctrl) {
		$ctrl->head_tags = new HeadTags();
		$method = new ReflectionMethod($ctrl, "_setup_hreflang_for_head_tags");
		$method->setAccessible(true);
		$method->invoke($ctrl);
	}

	// --- happy-path integration tests ---

	function test_hreflang_tags_present_on_multilang_site() {
		$ctrl = $this->client->get("main/index");
		$map = $this->_get_hreflang_map($ctrl);

		// both short language codes
		$this->assertTrue(isset($map["en"]), "hreflang=en expected");
		$this->assertTrue(isset($map["cs"]), "hreflang=cs expected");

		// locale-country variants from locale.yml (en_US.UTF-8 → en-US, cs_CZ.UTF-8 → cs-CZ)
		$this->assertTrue(isset($map["en-US"]), "hreflang=en-US expected");
		$this->assertTrue(isset($map["cs-CZ"]), "hreflang=cs-CZ expected");

		// x-default must be present
		$this->assertTrue(isset($map["x-default"]), "hreflang=x-default expected");
	}

	function test_x_default_points_to_default_language() {
		$ctrl = $this->client->get("main/index");
		$map = $this->_get_hreflang_map($ctrl);

		// ATK14_DEFAULT_LANG is "en" (defined in test/controllers/initialize.php)
		$this->assertEquals($map["en"], $map["x-default"]);
	}

	function test_hreflang_urls_are_absolute() {
		$ctrl = $this->client->get("main/index");
		$map = $this->_get_hreflang_map($ctrl);

		foreach($map as $lang => $url) {
			$this->assertTrue(
				(bool)preg_match('#^https?://#', $url),
				"hreflang URL for '$lang' must be absolute, got: $url"
			);
		}
	}

	function test_hreflang_in_html_output() {
		$this->client->get("main/index");
		$html = $this->client->getContent();

		$this->assertStringContains('hreflang="en"', $html);
		$this->assertStringContains('hreflang="cs"', $html);
		$this->assertStringContains('hreflang="x-default"', $html);
	}

	// --- noindex suppresses hreflang ---

	function test_no_hreflang_when_noindex_is_set() {
		$ctrl = $this->client->get("main/index");
		$ctrl->head_tags = new HeadTags();
		$ctrl->head_tags->setMetaTag("robots", "noindex,noarchive");

		$method = new ReflectionMethod($ctrl, "_setup_hreflang_for_head_tags");
		$method->setAccessible(true);
		$method->invoke($ctrl);

		$link_tags = $ctrl->head_tags->getLinkTags();
		$this->assertFalse(isset($link_tags["alternate"]), "hreflang must not be added for noindex pages");
	}

	// --- _disable_hreflang() ---

	function test_disable_hreflang_completely() {
		$ctrl = $this->client->get("main/index");

		$ctrl->_hreflang_enabled = false;
		$this->_invoke_setup_hreflang($ctrl);

		$link_tags = $ctrl->head_tags->getLinkTags();
		$this->assertFalse(isset($link_tags["alternate"]), "hreflang must not be added after _disable_hreflang()");
	}

	function test_disable_hreflang_for_one_lang_leaves_too_few() {
		// MiniSkelet has only en+cs. Disabling "cs" leaves 1 active locale
		// → not enough for hreflang to make sense → no tags at all.
		$ctrl = $this->client->get("main/index");

		$ctrl->_hreflang_disabled_langs = ["cs"];
		$this->_invoke_setup_hreflang($ctrl);

		$link_tags = $ctrl->head_tags->getLinkTags();
		$this->assertFalse(isset($link_tags["alternate"]), "hreflang must not be added when fewer than 2 active locales remain");
	}

	// --- _set_lang_noindex() — sibling language has noindex ---

	function test_set_lang_noindex_on_2lang_site_suppresses_all_hreflang() {
		// MiniSkelet has only en+cs. Marking cs as noindex leaves 1 active locale
		// → not enough for hreflang → no tags at all.
		$ctrl = $this->client->get("main/index");
		$ctrl->head_tags = new HeadTags();

		$method_noindex = new ReflectionMethod($ctrl, "_set_lang_noindex");
		$method_noindex->setAccessible(true);
		$method_noindex->invoke($ctrl, "cs");

		$method = new ReflectionMethod($ctrl, "_setup_hreflang_for_head_tags");
		$method->setAccessible(true);
		$method->invoke($ctrl);

		$link_tags = $ctrl->head_tags->getLinkTags();
		$this->assertFalse(isset($link_tags["alternate"]), "hreflang must not be added when only 1 active locale remains after excluding the noindex lang");
	}

	function test_set_lang_noindex_excludes_lang_not_in_active_locales() {
		// Marking a lang that isn't configured must not suppress hreflang for the remaining langs.
		$ctrl = $this->client->get("main/index");
		$ctrl->head_tags = new HeadTags();

		$method_noindex = new ReflectionMethod($ctrl, "_set_lang_noindex");
		$method_noindex->setAccessible(true);
		$method_noindex->invoke($ctrl, "sk"); // sk is not configured in this test suite

		$method = new ReflectionMethod($ctrl, "_setup_hreflang_for_head_tags");
		$method->setAccessible(true);
		$method->invoke($ctrl);

		$map = $this->_get_hreflang_map($ctrl);
		$this->assertTrue(isset($map["en"]), "hreflang=en must still be present when noindex lang is not active");
		$this->assertTrue(isset($map["cs"]), "hreflang=cs must still be present when noindex lang is not active");
	}

	// --- _set_hreflang_url() ---

	function test_set_hreflang_url_overrides_url_for_given_lang() {
		$ctrl = $this->client->get("main/index");
		$custom_url = "https://example.com/en/custom-page/";

		$ctrl->_hreflang_urls = ["en" => $custom_url];
		$this->_invoke_setup_hreflang($ctrl);

		$map = $this->_get_hreflang_map($ctrl);
		$this->assertEquals($custom_url, $map["en"], "Custom URL must be used for the specified language");
		$this->assertEquals($custom_url, $map["en-US"], "Custom URL must also appear for the locale-country variant");
		// x-default follows the default lang (en), so it must use the custom URL too
		$this->assertEquals($custom_url, $map["x-default"], "x-default must follow the overridden URL of the default language");
	}

	function test_set_hreflang_url_does_not_affect_other_langs() {
		$ctrl = $this->client->get("main/index");

		$ctrl->_hreflang_urls = ["en" => "https://example.com/en/custom/"];
		$this->_invoke_setup_hreflang($ctrl);

		$map = $this->_get_hreflang_map($ctrl);

		// "cs" URL must still be the auto-generated one (not the custom en URL)
		$this->assertTrue(isset($map["cs"]), "cs hreflang must still be present");
		$this->assertFalse($map["cs"] === "https://example.com/en/custom/", "cs URL must not be the custom en URL");
	}
}
