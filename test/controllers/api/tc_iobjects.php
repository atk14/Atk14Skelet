<?php
/**
 * @fixture users
 * @fixture pages
 */
class TcIobjects extends TcBase{

	function test_index(){
		$page = Page::FindFirst(array("order_by" => "id"));
		$picture = Picture::CreateNewFor($page,array(
			"url" => "http://i.pupiq.net/i/65/65/434/29434/1024x768/9TpGvk_800x600_cc49910967e8dc66.jpg",
			"title_en" => "Arnold",
		));

		$params = array("table_name" => "pages", "record_id" => $page->getId());

		// administrators only
		$data = $this->_get("iobjects/index",$params,$status_code);
		$this->assertEquals(403,$status_code);

		$this->_post("logins/create_new",array("login" => "admin", "password" => "admin"),$status_code);
		$this->assertEquals(201,$status_code);

		$data = $this->_get("iobjects/index",$params,$status_code);
		$this->assertEquals(200,$status_code);
		$this->assertEquals(1,sizeof($data));
		$this->assertEquals($picture->getId(),$data[0]["id"]);
		$this->assertEquals("Picture",$data[0]["type"]);
		$this->assertEquals("Arnold",$data[0]["title"]);
		$this->assertEquals("[#".$picture->getId()." Picture: Arnold]",$data[0]["insert_mark"]);
		$this->assertStringContains("160x120xc",$data[0]["preview_image_url"]);

		// no such record
		$data = $this->_get("iobjects/index",array("table_name" => "pages", "record_id" => 999999),$status_code);
		$this->assertEquals(404,$status_code);
		$data = $this->_get("iobjects/index",array("table_name" => "nonsenses", "record_id" => 1),$status_code);
		$this->assertEquals(404,$status_code);

		// invalid params
		$data = $this->_get("iobjects/index",array("table_name" => "pages; DROP TABLE pages", "record_id" => 1),$status_code);
		$this->assertEquals(400,$status_code);
		$data = $this->_get("iobjects/index",array("table_name" => "pages"),$status_code);
		$this->assertEquals(400,$status_code);
	}
}
