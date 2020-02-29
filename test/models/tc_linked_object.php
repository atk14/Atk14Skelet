<?php
class TcLinkedObject extends TcBase {
	
	function test_DeleteInstancesFor(){
		$a1 = Article::CreateNewRecord(array());
		$a2 = Article::CreateNewRecord(array());
		$a3 = Article::CreateNewRecord(array());
		$a4 = Article::CreateNewRecord(array());

		$a1_image1 = Image::CreateNewFor($a1,array("url" => "https://i.pupiq.net/i/65/65/27c/2927c/1272x920/JuSG6C_800x578_0cecc732df82ad65.jpg"));
		$a1_image2 = Image::CreateNewFor($a1,array("url" => "https://i.pupiq.net/i/65/65/27e/2927e/1272x920/9cUpr1_800x578_26254b6a433fc4a9.jpg"));

		$a2_image1 = Image::CreateNewFor($a2,array("url" => "https://i.pupiq.net/i/65/65/a40/1a40/1489x1289/Tfmb1G_800x692_0a0a020dc3c31e42.jpg"));

		$a3_image2 = Image::CreateNewFor($a3,array("url" => "https://i.pupiq.net/i/65/65/a3f/1a3f/960x840/G6rbqH_800x700_e713550de0b44678.jpg"));

		$count = $this->dbmole->selectInt("SELECT COUNT(*) FROM images");
		$this->assertTrue($count>=4);

		$initial = $count;

		Image::DeleteInstancesFor($a1);
		$count = $this->dbmole->selectInt("SELECT COUNT(*) FROM images");
		$this->assertEquals($initial-2,$count);

		Image::DeleteInstancesFor($a4);
		$count = $this->dbmole->selectInt("SELECT COUNT(*) FROM images");
		$this->assertEquals($initial-2,$count);

		$a2->destroy();
		$count = $this->dbmole->selectInt("SELECT COUNT(*) FROM images");
		$this->assertEquals($initial-3,$count);

		$a4->destroy();
		$count = $this->dbmole->selectInt("SELECT COUNT(*) FROM images");
		$this->assertEquals($initial-3,$count);
	}
}
