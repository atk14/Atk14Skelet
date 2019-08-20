<?php
/**
 * Rozhrani pro objekty, ktere jsou trideny podle policka rank.
 *
 * Metody jako Class::FindAll(), Class::FindFirst(), Class::FindBySomething().... budou defaultne tridit podle policka rank
 */
interface Rankable {
	public function setRank($rank);
}
