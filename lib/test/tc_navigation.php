<?php
class TcNavigation extends TcBase{
	function test(){
		$navi = new Navigation();
		$this->assertEquals(0,sizeof($navi));

		// adding items

		$navi[] = "Root";
		$this->assertEquals(1,sizeof($navi));
		$items = $navi->getItems();
		$this->assertEquals("Root",$items[0]->text);
		$this->assertEquals("Root",$navi[0]->text);

		$navi->add("Level_1");
		$this->assertEquals(2,sizeof($navi));
		$items = $navi->getItems();
		$this->assertEquals("Root",$items[0]->text);
		$this->assertEquals("Root",$navi[0]->text);
		$this->assertEquals("Level_1",$items[1]->text);
		$this->assertEquals("Level_1",$navi[1]->text);

		$navi[] = array("Level_2");
		$this->assertEquals(3,sizeof($navi));
		$this->assertEquals("Level_2",$navi[2]->text);

		$navi[] = array("text" => "Level_3");
		$this->assertEquals(4,sizeof($navi));
		$this->assertEquals("Level_3",$navi[3]->text);

		// iteration

		$ary = array();
		foreach($navi as $item){
			$ary[] = $item->text;
		}
		$this->assertEquals("Root,Level_1,Level_2,Level_3",join(",",$ary));
	}
}
