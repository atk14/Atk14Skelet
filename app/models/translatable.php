<?php
/**
 * Rozhrani pro modely s vicejazycnymi vlastnostmi
 *
 * Model, ktery toto rozhrani implementuje, musi obsahovat statickou metodu GetTranslatableFields
 * vracejici pole s nazvy db policek umoznujici pouzit vice jazyku.
 *
 * Priklad
 * 	class Category extends TableRecord implements Translatable {
 * 		static function GetTranslatableFields() {
 * 			return array("name", "description");
 * 		}
 * 	}
 */
interface Translatable {
	public static function GetTranslatableFields();
}
