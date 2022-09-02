<?php
class TcPicture extends TcBase {

	function test(){
		$picture = Picture::CreateNewRecord([
			"url" => "http://i.pupiq.net/i/65/65/434/29434/1024x768/9TpGvk_800x600_cc49910967e8dc66.jpg",
			"title_visible" => true,
			"title_en" => "Arnold",
			"alt_en" => "",
		]);

		$this->assertEquals("Arnold",$picture->getAlt());

		$picture->s("title_visible",false);
		$this->assertEquals("",$picture->getAlt());

		$picture->s("alt_en","Profile photo of Arnold");
		$this->assertEquals("Profile photo of Arnold",$picture->getAlt());
	}
}
