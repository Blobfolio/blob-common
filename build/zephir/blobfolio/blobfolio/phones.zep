//<?php
/**
 * Blobfolio: International Phone Formatting
 *
 * @see {blobfolio\common\cast}
 * @see {blobfolio\common\ref\cast}
 *
 * @package Blobfolio/Common
 * @author Blobfolio, LLC <hello@blobfolio.com>
 */

namespace Blobfolio;

final class Phones {
	const MINLENGTH = 3;
	const MAXLENGTH = 30;

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
		if (unlikely ("array" === typeof types) && count(types)) {
			var v;
			for v in types {
				if (in_array(v, parsed["types"], true)) {
					return parsed["number"];
				}
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
		if (!globals_get("loaded_blob_phone")) {
			self::loadData();
		}

		// Sanitize default country.
		let country = \Blobfolio\Geo::niceCountry(country);
		if (empty country) {
			let country = (string) self::_country;
		}
		if (!isset(self::_data[country])) {
			return false;
		}

		array keys;
		array targets;
		array tests;
		var outPrefix;
		var outTypes;
		var v2;
		var v3;
		var v;

		// Build a list of targets.
		let targets = (array) self::_regions[self::_data[country]["region"]];
		if (country !== targets[0]) {
			array_unshift(targets, country);
			let targets = array_unique(targets);
		}

		// Loop through each target until we find a match!
		for v in targets {
			let outPrefix = "" . self::_data[v]["prefix"];
			let outTypes = [];

			// We'll test the phone with and without a prefix.
			let tests = [phone];
			let v2 = (string) ltrim(phone, "0");
			if (0 === strpos(v2, outPrefix)) {
				let tests [] = (string) substr(v2, strlen(outPrefix));
			}

			// Run through each test!
			for v2 in tests {
				if (!preg_match("#" . self::_data[v]["patterns"] . "#", v2)) {
					continue;
				}

				// Loop through types.
				let keys = (array) array_keys(self::_data[v]["types"]);
				for v3 in keys {
					if (preg_match("#^(" . v3 . ")$#", v2)) {
						let outTypes = array_merge(
							outTypes,
							self::_data[v]["types"][v3]
						);
					}
				}

				// No types, no go.
				if (!count(outTypes)) {
					continue;
				}
				elseif (count(outTypes) > 1) {
					let outTypes = array_unique(outTypes);
					sort(outTypes);
				}

				// We found it! Now we just need to format the number.
				let keys = (array) array_keys(self::_data[v]["formats"]);
				for v3 in keys {
					if (preg_match("#^(" . v3 . ")$#", v2)) {
						let v2 = (string) preg_replace(
							"#^" . v3 . "$#",
							self::_data[v]["formats"][v3],
							v2
						);
						return [
							"country": v,
							"prefix": (int) outPrefix,
							"region": self::_data[v]["region"],
							"types": outTypes,
							"number": "+" . outPrefix . " " . v2
						];
					}
				}

				// We have to build something generic.
				return [
					"country": v,
					"prefix": (int) outPrefix,
					"region": self::_data[v]["region"],
					"types": outTypes,
					"number": "+" . outPrefix . " " . v2
				];
			}
		}

		return false;
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

		globals_set("loaded_blob_phone", true);
	}
}
