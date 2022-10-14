<?php

/**
 * Support pro <head> tagy.
 *
 * # meta tags <meta>
 *
 * ## Basic method
 * ```
 * $tags->addMetaTag("keywords", "Eshop, skelet, Atk14");
 * $tags->addMetaTag("google-site-verification", "googlekey1");
 * ```
 * Outputs:
 * ```
 * <meta name="keywords" content="Eshop, skelet, Atk14">
 * <meta name="google-site-verification" content="googlekey1">
 * ```
 *
 * ## Shortcut methods
 *
 * ```
 * $tags->addHttpEquiv("content-language", "en");
 * ```
 * equals to
 * ```
 * $tags->addMetaTag("content-language", "en", self::META_TYPE_HTTP_EQUIV);
 * ```
 * outputs
 * ```
 * <meta http-equiv="content-language" content="en">
 * ```
 *
 * ```
 * $tags->setCharsetMeta("utf-8");
 * ```
 * outputs
 * <meta charset="utf-8">
 * ```
 * ```
 *
 * Unofficial type 'property'
 * ```
 * $tags->addProperty("og:url", $this->request->getUrl());
 * ```
 *
 * # link tags <link>
 *
 * ## Basic method
 *
 * ```
 * $tags->addLinkTag("stylesheet", ["href" => "https://atk14eshop.com/stylesheets/main.css"]);
 * ```
 *
 *
 * ## Shortcut methods
 *
 * There are some shortcuts for canonical, preconnect ...
 * More to be added.
 *
 * ```
 * $tags->setCanonical("https://atk14eshop.net/product/neverending-story/");
 * ```
 * is the same as
 * ```
 * $tags->addLinkTag("canonical", ["href" => "https://atk14eshop.net/product/neverending-story/"]);
 * ```
 * outputs
 * ```
 * <link rel="canonical" href="https://atk14eshop.net/product/neverending-story/">
 * ```
 *
 * ```
 * $tags->addPreconnect("https://images.atk14eshop.net");
 */

class HeadTags {

	protected $meta_items = [];
	protected $link_items = [];

	const META_TYPE_CHARSET = "charset";
	const META_TYPE_HTTP_EQUIV = "http-equiv";
	const META_TYPE_NAME = "name";
	const META_TYPE_PROPERTY = "property";

	function __construct() {
	}

	function __toString() {
		$out = [];
		# name, property, http-equiv
		foreach($this->getMetaTags() as $type => $i) {
			# type name like google-site-verification in <meta name>, og:title in <meta property>
			foreach($i as $key => $elements) {
				# single type meta - make it an array so we can loop it
				if (!is_array($elements)) {
					$elements = [$elements];
				}

				foreach($elements as $element) {
					$out[] = (string)$element;
				}
			}
		}

		foreach($this->getLinkTags() as $link_type => $links) {
			if (!is_array($links)) {
				$links = [$links];
			}
			foreach($links as $link) {
				$out[] = (string)$link;
			}
		}

		$out = join("\n", $out);
		return $out;
	}

	/**
	 * Sets basic single type meta tag
	 *
	 * Only last value is print out.
	 *
	 * Example
	 * ```
	 * $meta14->setMetaTag("keywords", "keyword1, keyword2");
	 * $meta14->setMetaTag("keywords", "buzzword1, buzzword2");
	 * ```
	 * Result
	 * ```
	 * <meta name="keywords" content="buzzword1, buzzword2">
	 * ```
	 */
	function setMetaTag($key, $content, $type=self::META_TYPE_NAME) {
		if (!array_key_exists($type, $this->meta_items)) {
			$this->meta_items[$type] = [];
		}
		if (!array_key_exists($key, $this->meta_items[$type])) {
			$this->meta_items[$type][$key] = [];
		}
		$this->meta_items[$type][$key] = new MetaTag14($type, $key, $content);
	}

	/**
	 * Adds basic multiple type meta tag
	 *
	 * Example
	 * ```
	 * $meta14->addMetaTag("google-site-verification", "googlekey1");
	 * $meta14->addMetaTag("google-site-verification", "googlekey2");
	 * ```
	 * Result
	 * ```
	 * <meta name="google-site-verification" content="googlekey1">
	 * <meta name="google-site-verification" content="googlekey2">
	 * ```
	 * @param string $attribute_value
	 * @param string $content
	 */
	function addMetaTag($attribute_value, $content, $type=self::META_TYPE_NAME) {
		if (!array_key_exists($type, $this->meta_items)) {
			$this->meta_items[$type] = [];
		}
		if (!array_key_exists($attribute_value, $this->meta_items[$type])) {
			$this->meta_items[$type][$attribute_value] = [];
		}
		if ($this->meta_items[$type][$attribute_value] && !is_array($this->meta_items[$type][$attribute_value])) {
			settype($this->meta_items[$type][$attribute_value], "array");
		}
		$this->meta_items[$type][$attribute_value][] = new MetaTag14($type, $attribute_value, $content);
	}

	/**
	 * Shortcut for unofficial property meta tag.
	 *
	 * ```
	 * <meta property="og:type" content="website">
	 * ```
	 */
	function setProperty($attribute_value, $content) {
		return $this->setMetaTag($attribute_value, $content, self::META_TYPE_PROPERTY);
	}

	function addProperty($attribute_value, $content) {
		return $this->addMetaTag($attribute_value, $content, self::META_TYPE_PROPERTY);
	}

	/**
	 * Shortcut for http-equiv meta tag.
	 *
	 * ```
	 * <meta http-equiv="content-language" content="en">
	 * ```
	 */
	function addHttpEquiv($attribute_value, $value) {
		return $this->addMetaTag($attribute_value, $value, self::META_TYPE_HTTP_EQUIV);
	}

	/**
	 * Shortcut for meta charset tag.
	 *
	 * ```
	 * <meta charset="utf-8">
	 * ```
	 */
	function setCharsetMeta($charset) {
		return $this->setMetaTag($charset, null, self::META_TYPE_CHARSET);
	}

	function getMetaTags() {
		return $this->meta_items;
	}

	/**
	 * ```
	 * $header14->addLinkTag("preconnect", ["href" => "//images.atk14eshop.net"]);
	 * $header14->addLinkTag("preconnect", ["href" => "//fonts.atk14eshop.net"]);
	 * ```
	 * Output:
	 * ```
	 * <link rel="preconnect" href="//images.atk14eshop.net">
	 * <link rel="preconnect" href="//fonts.atk14eshop.net">
	 * ```
	 */
	function addLinkTag($rel, $attributes) {
		if (!array_key_exists($rel, $this->link_items)) {
			$this->link_items[$rel] = [];
		}
		if (!is_array($this->link_items[$rel])) {
			settype($this->link_items[$rel], "array");
		}
		$this->link_items[$rel][] = new LinkTag14($rel, $attributes);
	}

	function getLinkTags() {
		return $this->link_items;
	}

	/**
	 * Special set methods to set important link tags.
	 */
	function setCanonical($canonical_url) {
		$this->link_items["canonical"] = [new LinkTag14("canonical", ["href" => $canonical_url])];
	}

	/**
	 * Add preconnect link element.
	 */
	function setPreconnect($preconnect_host) {
		return $this->addLinkTag("preconnect", ["href" => $preconnect_host]);
	}

	/**
	 * Alias to setPreconnect() method.
	 *
	 * In case of preconnect, addPreconnect or setPreconnect is the same as there can be more preconnect link elements present.
	 */
	function addPreconnect($preconnect_host) {
		return $this->setPreconnect($preconnect_host);
	}

	/**
	 * Add preload link element.
	 */
	function setPreload($preload_url, $attributes=[]) {
		$attributes["href"] = $preload_url;
		return $this->addLinkTag("preload", $attributes);
	}

	/**
	 * Alias to setPreload() method.
	 *
	 * In case of preload, addPreload or setPreload is the same as there can be more preload link elements present.
	 */
	function addPreload($preconnect_url, $attributes=[]) {
		return $this->setPreload($preconnect_url, $attributes);
	}
}

class Element14 {
	function getData() {
		return $this->data;
	}
}

class MetaTag14 extends Element14 {
	function __construct($meta_type, $key, $data) {
		$this->meta_type = $meta_type;
		$this->key = $key;
		$this->data = $data;
	}

	function __toString() {
		$_attrs = [];
		$_attrs[] = sprintf('%s="%s"', $this->meta_type, $this->key);
		if (!is_null($_data = $this->getData())) {
			$_attrs[] = sprintf('content="%s"', $_data);
		}
		return sprintf('<meta %s>', join(" ", $_attrs));
	}
}

class LinkTag14 extends Element14 {
	function __construct($rel_type,$attributes) {
		$this->rel_type = $rel_type;
		$this->data = $attributes;
	}

	function __toString() {
		$_attrs = [];
		$_attrs[] = sprintf('rel="%s"', $this->rel_type);
		foreach($this->getData() as $key => $value) {
			# attribute without value
			if (is_numeric($key)) {
				$_attrs[] = $value;
				continue;
			}
			$_attrs[] = sprintf('%s="%s"', $key, $value);
		}
		return sprintf('<link %s>', join(" ", $_attrs));
	}
}
