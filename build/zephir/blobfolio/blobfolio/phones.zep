//<?php
/**
 * Blobfolio: International Phone Formatting
 *
 * @see {https://github.com/Blobfolio/blob-common}
 *
 * @package Blobfolio/Common
 * @author Blobfolio, LLC <hello@blobfolio.com>
 */

namespace Blobfolio;

final class Phones {
	const MINLENGTH = 3;
	const MAXLENGTH = 30;

	private static _loaded_blob_phone = false;

	private static _country;
	private static _data;
	private static _prefixes;
	private static _regions;



	// -----------------------------------------------------------------
	// Formatting
	// -----------------------------------------------------------------

	/**
	 * Nice Phone
	 *
	 * @param string $phone Phone.
	 * @param string $country Country.
	 * @param array $types Types.
	 * @return string Phone.
	 */
	public static function nicePhone(string phone, const string country="", var types=null) -> string {
		var parsed = self::parsePhone(phone, country);
		if ("array" !== typeof parsed) {
			return "";
		}

		// Maybe restrict by type?
		if (("array" === typeof types) && count(types)) {
			array intersect = (array) array_intersect(types, parsed["types"]);
			if (count(intersect)) {
				return parsed["number"];
			}
		}
		else {
			return parsed["number"];
		}

		return "";
	}



	// -----------------------------------------------------------------
	// Other Helpers
	// -----------------------------------------------------------------

	/**
	 * Parse Phone
	 *
	 * @param string $phone Phone.
	 * @param string $country Country.
	 * @return array|bool Data or false.
	 */
	public static function parsePhone(string phone, string country="") -> array | bool {
		// Early phone sanitizing.
		let phone = preg_replace("/[^\d]/", "", phone);
		int phoneLength = (int) strlen(phone);
		if (phoneLength < self::MINLENGTH || phoneLength > self::MAXLENGTH) {
			return false;
		}

		// Make sure the data is loaded.
		self::loadData();

		// Sanitize default country.
		let country = \Blobfolio\Geo::niceCountry(country);
		if (empty country) {
			let country = (string) self::_country;
		}
		if (!isset(self::_data[country])) {
			return false;
		}

		array targets;
		var outPrefix;
		var v2;
		var v;

		// Build a list of targets.
		let targets = (array) \Blobfolio\Geo::getNeighborCountries(country, 50);
		if (!count(targets)) {
			let targets = (array) self::_regions[self::_data[country]["region"]];
			if (country !== targets[0]) {
				array_unshift(targets, country);
				let targets = array_unique(targets);
			}
		}

		// If the number begins with the target country's prefix, strip
		// it.
		let outPrefix = "" . self::_data[country]["prefix"];
		let v2 = (string) ltrim(phone, 0);
		if (0 === strpos(v2, outPrefix)) {
			let v2 = (string) substr(v2, strlen(outPrefix));
			let v = self::testPhone(v2, country);
			if (false !== v) {
				return v;
			}
		}

		// Pass One: the number as is.
		for v in targets {
			let v = self::testPhone(phone, v);
			if (false !== v) {
				return v;
			}
		}

		// Pass Two: try again without the prefix.
		for v in targets {
			let outPrefix = "" . self::_data[v]["prefix"];
			let v2 = (string) ltrim(phone, "0");
			if (0 === strpos(v2, outPrefix)) {
				let v2 = (string) substr(v2, strlen(outPrefix));
				let v = self::testPhone(v2, v);
				if (false !== v) {
					return v;
				}
			}
		}

		return false;
	}

	/**
	 * Test a phone against a country.
	 *
	 * @param string $phone Phone.
	 * @param string $country Country.
	 * @return array|bool Info for false.
	 */
	private static function testPhone(string phone, string country) -> array | bool {
		// Run through each test!
		if (!preg_match("#" . self::_data[country]["patterns"] . "#", phone)) {
			return false;
		}

		string outPrefix = (string) self::_data[country]["prefix"];
		array outTypes = [];
		var v3;

		// Loop through types.
		array keys = (array) array_keys(self::_data[country]["types"]);
		for v3 in keys {
			if (preg_match("#^(" . v3 . ")$#", phone)) {
				let outTypes = array_merge(
					outTypes,
					self::_data[country]["types"][v3]
				);
			}
		}

		// No types, no go.
		if (!count(outTypes)) {
			return false;
		}
		elseif (count(outTypes) > 1) {
			let outTypes = array_unique(outTypes);
			sort(outTypes);
		}

		// We found it! Now we just need to format the number.
		let keys = (array) array_keys(self::_data[country]["formats"]);
		for v3 in keys {
			if (preg_match("#^(" . v3 . ")$#", phone)) {
				let phone = (string) preg_replace(
					"#^" . v3 . "$#",
					self::_data[country]["formats"][v3],
					phone
				);
				return [
					"country": country,
					"prefix": (int) outPrefix,
					"region": self::_data[country]["region"],
					"types": outTypes,
					"number": "+" . outPrefix . " " . phone
				];
			}
		}

		// We have to build something generic.
		return [
			"country": country,
			"prefix": (int) outPrefix,
			"region": self::_data[country]["region"],
			"types": outTypes,
			"number": "+" . outPrefix . " " . phone
		];
	}




	// -----------------------------------------------------------------
	// Data
	// -----------------------------------------------------------------

	/**
	 * Load Data
	 *
	 * For performance reasons, "data", "prefixes", and "regions" are
	 * externally stored, loaded only if/when needed.
	 *
	 * @return void Nothing.
	 * @throws Exception Error.
	 */
	private static function loadData() -> void {
		// Don't allow accidental repeats.
		if (true === self::_loaded_blob_phone) {
			return;
		}

		string json = (string) \Blobfolio\Blobfolio::getDataDir("blob-phone.json");
		if (empty json) {
			throw new \Exception("Missing phone formatting data.");
		}
		var tmp;
		let tmp = json_decode(json, true);
		if ("array" !== typeof tmp) {
			throw new \Exception("Could not parse phone formatting data.");
		}

		// Split it out to make the data easier to access later.
		let self::_data = (array) tmp["data"];
		let self::_prefixes = (array) tmp["prefixes"];
		let self::_regions = (array) tmp["regions"];

		// While we're here, let's also set the default country.
		let self::_country = (string) ini_get("blobfolio.country");
		let self::_country = \Blobfolio\Geo::niceCountry(self::_country);
		if (empty self::_country) {
			let self::_country = "US";
		}

		let self::_loaded_blob_phone = true;
	}
}
