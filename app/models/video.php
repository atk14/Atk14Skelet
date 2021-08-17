<?php
/**
 * Hodnoty, ktere se posilaji v requestu jako maxwidth a maxheight.
 *
 * Server vrati embed kod s rozmery, kde ani jeden neprekroci uvedene hodnoty.
 * Pomer stran zustane zachovan.
 */
definedef("ARTICLE_BODY_MAX_WIDTH",825); 
definedef("VIDEO_DEFAULT_WIDTH",ARTICLE_BODY_MAX_WIDTH);
definedef("VIDEO_DEFAULT_HEIGHT",ARTICLE_BODY_MAX_WIDTH);

class Video extends Iobject{

	const DEFAULT_THUMBNAIL_WIDTH = 300;
	const DEFAULT_THUMBNAIL_HEIGHT = 200;

	/**
	 * Vraci html kod pro vlozeni do stranky.
	 *
	 * Pokud zadame v $options maxwidth a maxheight, rozmery ve vlozenem kodu se prepocitaji.
	 *
	 * @param array $options
	 * @return string
	 */
	function getHtml($options=array()) {
		$options += array(
			"maxwidth" => VIDEO_DEFAULT_WIDTH,
			"maxheight" => VIDEO_DEFAULT_HEIGHT,
		);

		if(is_null($options["maxwidth"]) || is_null($options["maxheight"])) {
			$out = $this->g("html");
		}else{
		$essence = new \Essence\Essence();
		$out =  $essence->replace($this->getUrl(), function($media) {
			return <<<HTML
$media->html
HTML;
		}, array(
			"maxheight" => $options["maxheight"],
			"maxwidth" => $options["maxwidth"],
		));
		}

		if(!preg_match('/<iframe[^>]* loading="/',$out)){
			$out = preg_replace('/(<iframe[^>]*)>/','\1 loading="lazy">',$out);
		}

		if($this->g("autoplay")){
			$out = preg_replace('/(src=".*?)"/','\1&amp;autoplay=1"',$out);
		}

		if($this->g("loop")){
			$video_id = "";
			if(preg_match('/src=".*embed\/(.*?)\?/',$out,$matches)){
				$video_id = $matches[1];
			}
			$out = preg_replace('/(src=".*?)"/','\1&amp;loop=1&amp;playlist='.$video_id.'"',$out);
		}

		// https://css-tricks.com/lazy-load-embedded-youtube-videos/
		if(!$this->g("autoplay") && !preg_match('/srcdoc=/') && preg_match('/src="(.*?)"/',$out,$matches)){
			$_url = $matches[1];
			$_url = $_url . (preg_match('/\?/',$_url) ? "&amp;autoplay=1" : "?autoplay=1");
			$p = new Pupiq($this->getImageUrl());
			$_image_url = $p->getUrl("800x800");
			$srcdoc = array();
			$srcdoc[] = "<style>";
			$srcdoc[] = "*{padding:0;margin:0;overflow:hidden}html,body{height:100%}";
			$srcdoc[] = "img,span{position:absolute;width:100%;top:0;bottom:0;margin:auto}";
			//$srcdoc[] = "img{filter: blur(3px)}";
			$srcdoc[] = "span{height:1.5em;text-align:center;font:48px/1.5 sans-serif;color:white;text-shadow:0 0 0.5em black}";
			$srcdoc[] = "</style>";
			//
			$srcdoc[] = "<a href=".$_url.">";
			$srcdoc[] = "<img src=".$_image_url." alt='".h($this->getTitle())."'>";
			$srcdoc[] = "<span>▶</span>";
			$srcdoc[] = "</a>";
			$srcdoc = join("",$srcdoc);

			$out = preg_replace('/src=".*?"/','\1 srcdoc="'.h($srcdoc).'"',$out);
		}

		return $out;
	}

	function getThumbnailHtml() {
		return $this->getHtml(array(
			"maxwidth" => self::DEFAULT_THUMBNAIL_WIDTH,
			"maxheight" => self::DEFAULT_THUMBNAIL_HEIGHT,
		));
	}

	function getPreviewImageUrl(){ return $this->getImageUrl(); }
}
