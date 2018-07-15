//<?php
/**
 * Blobfolio: Miscellaneous Geo Data
 *
 * @see {blobfolio\common\cast}
 * @see {blobfolio\common\ref\cast}
 *
 * @package Blobfolio/Common
 * @author Blobfolio, LLC <hello@blobfolio.com>
 */

namespace Blobfolio;

use \Throwable;

final class Geo {
	private static _au;
	private static _ca;
	private static _countries;
	private static _regions = ["Africa", "Asia", "Australia", "Europe", "North America", "South America"];
	private static _timezones;
	private static _us;



	// -----------------------------------------------------------------
	// Formatting
	// -----------------------------------------------------------------

	/**
	 * Nice Country
	 *
	 * @param string $country Country.
	 * @return string Country.
	 */
	public static function niceCountry(string country) -> string {
		let country = Strings::whitespace(country);
		if (empty country) {
			return "";
		}

		// Make sure the data is loaded.
		if (!globals_get("loaded_geo")) {
			self::loadData();
		}

		// Uppercase it.
		let country = Strings::strtoupper(country);

		// A direct hit!
		if (isset(self::_countries[country])) {
			return country;
		}

		// Check for common aliases.
		array aliases = [
			"BRITAIN": "GB",
			"GREAT BRITAIN": "GB",
			"U. S. A.": "US",
			"U. S. S. R.": "RU",
			"U.S.A.": "US",
			"U.S.S.R.": "RU",
			"UNITED STATES OF AMERICA": "US",
			"UNITED STATES": "US",
			"USSR": "RU"
		];
		if (isset(aliases[country])) {
			return aliases[country];
		}

		// Run through each country and see if the name matches.
		var k;
		var v;
		for k, v in self::_countries {
			let v["name"] = (string) Strings::strtoupper(v["name"]);
			if (country === v["name"]) {
				return (string) k;
			}
		}

		// Sadness.
		return "";
	}

	/**
	 * Nice AU State
	 *
	 * @param string $state State.
	 * @return string State.
	 */
	public static function niceAuState(string state) -> string {
		let state = Strings::whitespace(state);
		if (empty state) {
			return "";
		}

		// Make sure the data is loaded.
		if (!globals_get("loaded_geo")) {
			self::loadData();
		}

		// Uppercase it.
		let state = (string) strtoupper(state);

		// A direct hit!
		if (isset(self::_au[state])) {
			return state;
		}

		// Run through each state and see if the name matches.
		var k;
		var v;
		for k, v in self::_au {
			let v = (string) strtoupper(v);
			if (state === v) {
				return (string) k;
			}
		}

		// Sadness.
		return "";
	}

	/**
	 * Nice CA Province
	 *
	 * @param string $state State.
	 * @return string State.
	 */
	public static function niceCaProvince(string state) -> string {
		let state = Strings::whitespace(state);
		if (empty state) {
			return "";
		}

		// Make sure the data is loaded.
		if (!globals_get("loaded_geo")) {
			self::loadData();
		}

		// Uppercase it.
		let state = (string) strtoupper(state);

		// A direct hit!
		if (isset(self::_ca[state])) {
			return state;
		}

		// Run through each state and see if the name matches.
		var k;
		var v;
		for k, v in self::_ca {
			let v = (string) strtoupper(v);
			if (state === v) {
				return (string) k;
			}
		}

		// Sadness.
		return "";
	}

	/**
	 * Nice US State
	 *
	 * @param string $state State.
	 * @return string State.
	 */
	public static function niceUsState(string state) -> string {
		let state = Strings::whitespace(state);
		if (empty state) {
			return "";
		}

		// Make sure the data is loaded.
		if (!globals_get("loaded_geo")) {
			self::loadData();
		}

		// Uppercase it.
		let state = (string) strtoupper(state);

		// A direct hit!
		if (isset(self::_us[state])) {
			return state;
		}

		// Run through each state and see if the name matches.
		var k;
		var v;
		for k, v in self::_us {
			let v = (string) strtoupper(v);
			if (state === v) {
				return (string) k;
			}
		}

		// Sadness.
		return "";
	}

	/**
	 * Nice Timezone
	 *
	 * @param string $tz Timezone.
	 * @return string Timezone.
	 */
	public static function niceTimezone(string tz) -> string {
		let tz = (string) strtoupper(tz);
		let tz = (string) preg_replace("/\s/u", "", tz);

		// Make sure the data is loaded.
		if (!globals_get("loaded_geo")) {
			self::loadData();
		}

		if (empty tz || !isset(self::_timezones[tz])) {
			return "UTC";
		}

		return self::_timezones[tz];
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
		string json = (string) Blobfolio::getDataDir("geo.json");
		if (empty json) {
			throw new \Exception("Missing geo data.");
		}
		var tmp;
		let tmp = json_decode(json, true);
		if ("array" !== typeof tmp) {
			throw new \Exception("Could not parse geo data.");
		}

		// Split it out to make the data easier to access later.
		let self::_au = (array) tmp["au"];
		let self::_ca = (array) tmp["ca"];
		let self::_countries = (array) tmp["countries"];
		let self::_timezones = (array) tmp["timezones"];
		let self::_us = (array) tmp["us"];

		globals_set("loaded_geo", true);
	}
}
