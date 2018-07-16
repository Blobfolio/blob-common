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
		let country = Strings::toUpper(country);

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
			let v["name"] = (string) Strings::toUpper(v["name"]);
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
	 * Nice Datetime
	 *
	 * @param mixed str Date or timestamp.
	 * @return string Datetime.
	 */
	public static function niceDatetime(var str) -> string {
		// We don't need fancy casting.
		if ("string" !== typeof str) {
			if (is_numeric(str)) {
				let str = (string) str;
			}
			else {
				return "0000-00-00 00:00:00";
			}
		}

		// Could be a timestamp.
		if (preg_match("/^\d{9,}$/", str)) {
			return date("Y-m-d H:i:s", intval(str));
		}

		let str = trim(str);

		if (
			empty str ||
			(0 === strpos(str, "0000-00-00"))
		) {
			return "0000-00-00 00:00:00";
		}

		var timestamp;
		let timestamp = strtotime(str);
		if (false === timestamp) {
			return "0000-00-00 00:00:00";
		}

		return date("Y-m-d H:i:s", timestamp);
	}

	/**
	 * Nice Date
	 *
	 * @param mixed Date.
	 * @return string Date.
	 */
	public static function niceDate(var str) -> string {
		let str = (string) self::niceDatetime(str);
		return substr(str, 0, 10);
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
	// Helpers
	// -----------------------------------------------------------------

	/**
	 * Days Between Dates
	 *
	 * @param string $date1 Date.
	 * @param string $date2 Date.
	 * @return int Difference.
	 */
	public static function dateDiff(var date1, var date2) -> int {
		let date1 = self::niceDate(date1);
		let date2 = self::niceDate(date2);

		// Bad dates.
		if (
			(date1 === date2) ||
			("0000-00-00" === date1) ||
			("0000-00-00" === date2)
		) {
			return 0;
		}

		var dt1;
		var dt2;

		let dt1 = new \DateTime(strval(date1));
		let dt2 = new \DateTime(strval(date2));
		var diff = dt1->diff(dt2);

		return abs(diff->days);
	}

	/**
	 * To Timezone
	 *
	 * @param string $date Date.
	 * @param string $from From.
	 * @param string $to To.
	 * @return string Date.
	 */
	public static function toTimezone(string date, string from="UTC", string to="UTC") -> string {
		let date = self::niceDatetime(date);

		if ("UTC" !== from) {
			let from = self::niceTimezone(from);
		}

		if ("UTC" !== to) {
			let to = self::niceTimezone(to);
		}

		// Nothing to do.
		if (("0000-00-00 00:00:00" === date) || (from === to)) {
			return date;
		}

		var dateNew;
		let dateNew = new \DateTime(date, new \DateTimeZone(from));
		dateNew->setTimezone(new \DateTimeZone(to));
		return dateNew->format("Y-m-d H:i:s");
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
