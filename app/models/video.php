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

		if(preg_match("/youtube/",$out)){
			// Adding rel=0 to a youtube URL
			// Offering related videos is not a desired feature.
			$out = preg_replace('/(src=".*?)"/','\1&amp;rel=0"',$out);
		}

		// Privacy-enhanced mode
		$out = preg_replace('/\bsrc="https:\/\/www.youtube.com/','src="https://www.youtube-nocookie.com/',$out);

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
