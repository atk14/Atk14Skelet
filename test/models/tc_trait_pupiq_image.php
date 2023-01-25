<?php
/**
 *
 * @fixture images
 * @fixture pictures
 * @fixture gallery_items
 */
class TcTraitPupiqImage extends TcBase {

  function test(){
		// class Image
		$this->_test($this->images["testing_article__astronaut"]);

		// class Picture
		$this->_test($this->pictures["astronaut"]);

		// class GalleryItem
		$this->_test($this->gallery_items["gallery__astronaut"]);
  }

  function _test($image){
		$this->assertEquals(min(ARTICLE_BODY_MAX_WIDTH,1272),$image->getWidth());
		$this->assertEquals(100,$image->getWidth("100x"));
		$this->assertEquals(1272,$image->getOriginalWidth());

		// we are not sure how PupiqClient calculates the image height
		$height = min(ARTICLE_BODY_MAX_WIDTH,1272) / 1.3826;
		$this->assertTrue(floor($height)<=$image->getHeight() && ceil($height)>=$image->getHeight(),"$height vs. ".$image->getHeight());
		$this->assertEquals(72,$image->getHeight("100x"));
		$this->assertEquals(920,$image->getOriginalHeight());

		$url = $image->getUrl();
		$url_100 = $image->getUrl("100x");

		$this->assertNotEquals($url,$url_100);
		$this->assertNotContains("100x72",$url);
		$this->assertContains("100x72",$url_100);
  }
}
