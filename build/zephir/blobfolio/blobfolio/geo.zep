//<?php
/**
 * Blobfolio: Spacetime Helpers
 *
 * @see {https://github.com/Blobfolio/blob-common}
 *
 * @package Blobfolio/Common
 * @author Blobfolio, LLC <hello@blobfolio.com>
 */

namespace Blobfolio;

final class Geo {
	private static _au;
	private static _ca;
	private static _country;
	private static _countries;
	private static _regions = ["Africa", "Asia", "Australia", "Europe", "North America", "South America"];
	private static _timezones;
	private static _us;



	// -----------------------------------------------------------------
	// Formatting
	// -----------------------------------------------------------------

	/**
	 * Nice Address
	 *
	 * @param mixed $parts Parts.
	 * @param int $flags Flags.
	 * @return array Parts.
	 */
	public static function niceAddress(array parts, const uint flags=7) -> array {
		// Make sure the data is loaded.
		if (!globals_get("loaded_geo")) {
			self::loadData();
		}

		// First figure out what fields we should be including.
		bool flagsEmail = (flags & globals_get("flag_address_field_email"));
		bool flagsPhone = (flags & globals_get("flag_address_field_phone"));
		bool flagsCompany = (flags & globals_get("flag_address_field_company"));

		array template = [
			"name": "",
			"street": "",
			"city": "",
			"state": "",
			"zip": "",
			"country": ""
		];

		// Add extra fields.
		if (flagsCompany) {
			let template["company"] = "";
		}
		if (flagsPhone) {
			let template["phone"] = "";
		}
		if (flagsEmail) {
			let template["email"] = "";
		}

		var k, v;

		// Pre-clean: Name.
		array aliases;
		if (!isset(parts["name"]) || empty parts["name"]) {
			let aliases = [
				["firstname", "lastname"],
				["first_name", "last_name"],
				["first", "last"]
			];

			for v in aliases {
				if (
					isset(parts[v[0]]) &&
					isset(parts[v[1]]) &&
					(!empty parts[v[0]] || !empty parts[v[1]])
				) {
					let parts["name"] = trim(
						parts[v[0]] . " " . parts[v[1]]
					);
					break;
				}
			}
		}

		// Pre-clean: Address.
		if (!isset(parts["street"]) || empty parts["street"]) {
			let aliases = [
				"street",
				"address",
				"address_line"
			];

			for v in aliases {
				if (
					(isset(parts[v . "1"]) && !empty(parts[v . "1"])) ||
					(isset(parts[v . "2"]) && !empty(parts[v . "2"]))
				) {
					if (isset(parts[v . "1"])) {
						let parts["street"] = parts[v . "1"];
					}
					if (isset(parts[v . "2"])) {
						let parts["street"] .= " " . parts[v . "2"];
					}
					let parts["street"] = trim(parts["street"]);
					break;
				}
				elseif (
					(isset(parts[v . "_1"]) && !empty(parts[v . "_1"])) ||
					(isset(parts[v . "_2"]) && !empty(parts[v . "_2"]))
				) {
					if (isset(parts[v . "_1"])) {
						let parts["street"] = parts[v . "_1"];
					}
					if (isset(parts[v . "_2"])) {
						let parts["street"] .= " " . parts[v . "_2"];
					}
					let parts["street"] = trim(parts["street"]);
					break;
				}
				elseif (
					("street" !== v) &&
					isset(parts[v]) &&
					!empty parts[v]
				) {
					let parts["street"] = trim(parts[v]);
					break;
				}
			}
		}

		// Company aliases.
		if (!isset(parts["company"]) || empty parts["company"]) {
			if (isset(parts["business"]) && !empty parts["business"]) {
				let parts["company"] = parts["business"];
			}
		}

		// Email alias.
		if (!isset(parts["email"]) || empty parts["email"]) {
			if (isset(parts["email_address"]) && !empty parts["email_address"]) {
				let parts["email"] = parts["email_address"];
			}
		}

		// Phone alias.
		if (!isset(parts["phone"]) || empty parts["phone"]) {
			let aliases = [
				"telephone",
				"tel",
				"phone_number"
			];

			for v in aliases {
				if (
					isset(parts[v]) &&
					!empty parts[v]
				) {
					let parts["phone"] = trim(parts[v]);
					break;
				}
			}
		}

		// Crunch the template.
		array out = (array) \Blobfolio\Cast::parseArgs(parts, template);

		// Some formatting can be applied en masse.
		for k, v in out {
			// Everything should be nice.
			let out[k] = (string) \Blobfolio\Strings::niceText(
				v,
				0,
				globals_get("flag_trusted")
			);

			// Move on if we have nothing.
			if (empty out[k]) {
				// We can always set a country.
				if ("country" === k) {
					let out["country"] = (string) self::_country;
				}
				continue;
			}

			// Key-based changes.
			switch (k) {
				case "name":
					let out[k] = (string) \Blobfolio\Retail::niceName(out[k]);
					break;
				case "country":
					let out["country"] = self::niceCountry(out[k]);
					if (empty out["country"]) {
						let out["country"] = (string) self::_country;;
					}
					break;
				case "email":
					let out[k] = (string) \Blobfolio\Domains::niceEmail(
						out[k],
						globals_get("flag_trusted")
					);
					break;
				case "phone":
					let out[k] = (string) \Blobfolio\Phones::nicePhone(
						out[k],
						out["country"]
					);
					break;
				case "company":
					break;
				default:
					// Uppercase everything else.
					let out[k] = (string) \Blobfolio\Strings::toUpper(
						out[k],
						globals_get("flag_trusted")
					);
			}
		}

		// US.
		if ("US" === out["country"]) {
			let out["state"] = (string) self::niceUsState(out["state"]);
			let out["zip"] = (string) self::niceZip5(out["zip"]);
		}
		// Canada.
		elseif ("CA" === out["country"]) {
			let out["state"] = (string) self::niceCaProvince(out["state"]);
			let out["zip"] = (string) self::niceCaPostalCode(out["zip"]);
		}
		// Australia.
		elseif ("AU" === out["country"]) {
			let out["state"] = (string) self::niceAuState(out["state"]);
		}

		return out;
	}

	/**
	 * Nice Country
	 *
	 * @param string $country Country.
	 * @return string Country.
	 */
	public static function niceCountry(string country) -> string {
		let country = \Blobfolio\Strings::whitespace(country);
		if (empty country) {
			return "";
		}

		// Make sure the data is loaded.
		if (!globals_get("loaded_geo")) {
			self::loadData();
		}

		// Uppercase it.
		let country = \Blobfolio\Strings::toUpper(country, globals_get("flag_trusted"));

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
			let v["name"] = (string) \Blobfolio\Strings::toUpper(v["name"], globals_get("flag_trusted"));
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
		let state = \Blobfolio\Strings::whitespace(state);
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
	 * Nice CA Postal Code
	 *
	 * @param string $str Code.
	 * @return string Code.
	 */
	public static function niceCaPostalCode(string str) -> string {
		let str = strtoupper(str);

		// Alphanumeric, minus D, F, I, O, Q, and U.
		let str = preg_replace("/[^A-CEGHJ-NPR-TV-Z\d]/", "", str);

		// W and Z are not allowed in the first slot, otherwise it
		// just alternates between letters and numbers.
		if (!preg_match("/^[A-VXY][\d][A-Z][\d][A-Z][\d]$/", str)) {
			return "";
		}

		// If it looks good, add a space in the middle.
		return substr(str, 0, 3) . " " . substr(str, -3);
	}

	/**
	 * Nice CA Province
	 *
	 * @param string $state State.
	 * @return string State.
	 */
	public static function niceCaProvince(string state) -> string {
		let state = \Blobfolio\Strings::whitespace(state);
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
		let state = \Blobfolio\Strings::whitespace(state);
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
	 * Nice US ZIP5
	 *
	 * @param string $str ZIP.
	 * @return string ZIP.
	 */
	public static function niceZip5(string str) -> string {
		let str = preg_replace("/[^\d]/", "", str);
		if (strlen(str) < 5) {
			let str = sprintf("%05d", str);
		}
		elseif (strlen(str) > 5) {
			let str = substr(str, 0, 5);
		}

		if ("00000" === str) {
			return "";
		}

		return str;
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
	 * Get Distance
	 *
	 * Calculate the distance between two sets of lat/lon in miles.
	 *
	 * @param float $lat1 Lat1.
	 * @param float $lon1 Lon1.
	 * @param float $lat2 Lat2.
	 * @param float $lon2 Lon2.
	 * @return float Miles.
	 */
	public static function getDistance(float lat1, float lon1, float lat2, float lon2) -> float {
		// The same?
		if ((lat1 === lat2) && (lon1 === lon2)) {
			return 0.0;
		}

		float theta = lon1 - lon2;
		float distance;
		let distance = sin(deg2rad(lat1)) * sin(deg2rad(lat2)) + cos(deg2rad(lat1)) * cos(deg2rad(lat2)) * cos(deg2rad(theta));
		let distance = acos(distance);
		return distance * 60 * 1.1515;
	}

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
	 * Get AU States
	 *
	 * @return array States.
	 */
	public static function getAuStates() -> array {
		// Make sure the data is loaded.
		if (!globals_get("loaded_geo")) {
			self::loadData();
		}

		return self::_au;
	}

	/**
	 * Get CA Provinces
	 *
	 * @return array Provinces.
	 */
	public static function getCaProvinces() -> array {
		// Make sure the data is loaded.
		if (!globals_get("loaded_geo")) {
			self::loadData();
		}

		return self::_ca;
	}

	/**
	 * Get Countries
	 *
	 * @return array States.
	 */
	public static function getCountries() -> array {
		// Make sure the data is loaded.
		if (!globals_get("loaded_geo")) {
			self::loadData();
		}

		return self::_countries;
	}

	/**
	 * Get Neighboring Countries
	 *
	 * Sort and return an array of countries by proximity to the target.
	 *
	 * @param string $country Country.
	 * @param int $limit Limit.
	 * @return array Countries.
	 */
	public static function getNeighborCountries(string country, const int limit = -1) -> array {
		let country = self::niceCountry(country);
		if (
			empty country ||
			(0 === self::_countries[country]["lat"]) ||
			(0 === self::_countries[country]["lon"])
		) {
			return [];
		}

		array countries = [];
		var k, v;
		for k, v in self::_countries {
			// The target is closest to itself.
			if (k === country) {
				let countries[k] = 0.0;
			}
			// If a country borders the target, consider that 1.0.
			elseif (in_array(k, self::_countries[country]["borders"], true)) {
				let countries[k] = 1.0;
			}
			// Otherwise calculate central distances.
			elseif (v["lat"] && v["lon"]) {
				let countries[k] = (float) self::getDistance(
					self::_countries[country]["lat"],
					self::_countries[country]["lon"],
					v["lat"],
					v["lon"]
				);
			}
		}

		// Sort by distance.
		asort(countries);

		// Chop long results.
		if (limit > 0 && count(countries) > limit) {
			array_splice(countries, limit);
		}

		return array_keys(countries);
	}

	/**
	 * Get Regions
	 *
	 * @return array Regions.
	 */
	public static function getRegions() -> array {
		return self::_regions;
	}

	/**
	 * Get US States
	 *
	 * @param int $flags Flags.
	 * @return array States.
	 */
	public static function getUsStates(const uint flags=1) -> array {
		// Make sure the data is loaded.
		if (!globals_get("loaded_geo")) {
			self::loadData();
		}

		// Strip the territories and military bases, but keep DC.
		bool extra = (flags & globals_get("flag_us_territories"));
		if (!extra) {
			array out = (array) self::_us;
			unset(out["AA"]);
			unset(out["AE"]);
			unset(out["AP"]);
			unset(out["AS"]);
			unset(out["FM"]);
			unset(out["GU"]);
			unset(out["MH"]);
			unset(out["MP"]);
			unset(out["PW"]);
			unset(out["PR"]);
			unset(out["VI"]);
			return out;
		}

		return self::_us;
	}

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
		string json = (string) \Blobfolio\Blobfolio::getDataDir("geo.json");
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

		// While we're here, let's also set the default country.
		let self::_country = (string) ini_get("blobfolio.country");
		let self::_country = \Blobfolio\Geo::niceCountry(self::_country);
		if (empty self::_country) {
			let self::_country = "US";
		}

		globals_set("loaded_geo", true);
	}
}
