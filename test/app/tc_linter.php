<?php
class TcLinter extends TcBase {

	function test_php(){
		$suffixes = ["php","inc"];
		$forbidden_folders = [
			".git",
			"doc",
			"tmp",
			"log",
			"atk14",
			"vendor",
			"bower_components",
			"node_modules",
			"webpack",
			"public",
		];

		$files = Files::FindFiles(ATK14_DOCUMENT_ROOT,["pattern" => '/\.('.join('|',$suffixes).')$/']);

		foreach($files as $file){
			$_file = str_replace(ATK14_DOCUMENT_ROOT,"",$file);
			if(preg_match('#^('.join('|',$forbidden_folders).')/#',$_file)){
				continue;
			}
			//echo $_file,"\n"; continue;
			system("php -l ".escapeshellarg($file),$ret_val);
			if($ret_val){
				$this->fail("There is syntax error in file $_file");
			}
		}
	}

	function test_compile_all_templates(){
		system("ATK14_ENV=test ".ATK14_DOCUMENT_ROOT."/scripts/compile_all_templates",$ret_val);
		if($ret_val){
			$this->fail("./scripts/compile_all_templates failed");
		}
	}
}
