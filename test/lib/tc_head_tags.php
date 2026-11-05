<?php
class TcHeadTags extends TcBase {

	// HeadTags is a pure PHP class with no database dependency
	function _setUp() {}
	function _tearDown() {}

	function test_hasNoindex_false_by_default() {
		$tags = new HeadTags();
		$this->assertFalse($tags->hasNoindex());
	}

	function test_hasNoindex_false_when_only_noarchive() {
		$tags = new HeadTags();
		$tags->setMetaTag("robots", "noarchive");
		$this->assertFalse($tags->hasNoindex());
	}

	function test_hasNoindex_true_for_noindex() {
		$tags = new HeadTags();
		$tags->setMetaTag("robots", "noindex");
		$this->assertTrue($tags->hasNoindex());
	}

	function test_hasNoindex_true_for_noindex_noarchive() {
		$tags = new HeadTags();
		$tags->setMetaTag("robots", "noindex,noarchive");
		$this->assertTrue($tags->hasNoindex());
	}

	function test_hasNoindex_reflects_last_set_value() {
		$tags = new HeadTags();
		$tags->setMetaTag("robots", "noindex,noarchive");
		$this->assertTrue($tags->hasNoindex());

		// setMetaTag overwrites the previous value
		$tags->setMetaTag("robots", "noarchive");
		$this->assertFalse($tags->hasNoindex());
	}
}
