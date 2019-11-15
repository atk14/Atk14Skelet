<?php
/**
 * Interface pro modely, ktere pouzivaji slugy.
 */
interface iSlug {

	/**
	 * V pripade, ze nechceme segment urcit, at vraci ''
	 */
	public function getSlugPattern($lang);

	/**
	 * Pro objekty, ktere jsou setridene v nejake hierarchii.
	 * Umozni pouzit stejny slug pro stejne objekty v ruznych vetvich.
	 *
	 * V pripade, ze nechceme segment urcit, at vraci ''
	 */
	public function getSlugSegment();
}
