//<?php
/**
 * Blobfolio: Arrays
 *
 * Array helpers.
 *
 * @see {https://github.com/Blobfolio/blob-common}
 *
 * @package Blobfolio/Common
 * @author Blobfolio, LLC <hello@blobfolio.com>
 */

namespace Blobfolio;

final class Arrays {

	// -----------------------------------------------------------------
	// Formatting
	// -----------------------------------------------------------------

	/**
	 * Flatten Multi-Dimensional Array
	 *
	 * Like array_values(), but move child values into the single (main)
	 * level.
	 *
	 * @param array $arr Array.
	 * @param int $flags Flags.
	 * @return array Array.
	 */
	public static function flatten(array arr, const uint flags=0) -> array {
		if (!count(arr)) {
			return [];
		}

		array out = [];
		var v;
		for v in arr {
			// Recurse arrays.
			if (is_array(v)) {
				if (count(v)) {
					let out = array_merge(out, self::flatten(v, flags));
				}
			}
			else {
				let out[] = v;
			}
		}

		if (count(out)) {
			bool sort = (flags & globals_get("flag_sort"));
			bool unique = (flags & globals_get("flag_unique"));
			if (sort || unique) {
				if (unique) {
					let out = (array) array_unique(out);
				}
				if (sort) {
					sort(out);
				}
				else {
					let out = array_values(out);
				}
			}
			else {
				let out = array_values(out);
			}
		}

		return out;
	}

	/**
	 * Flatten Associative
	 *
	 * Like ::flatten() except keys are flattened too.
	 *
	 * @param mixed $data Data.
	 * @param string $stub Key stub.
	 * @return mixed Data.
	 */
	public static function flattenAssoc(array arr, const string stub="") -> array {
		if (!count(arr)) {
			return [];
		}

		array out = [];
		var k;
		var v;

		for k, v in arr {
			string key = stub ? strval(stub . "_" . k) : strval(k);

			if (is_array(v)) {
				array tmp = (array) self::flattenAssoc(v, key);
				var k2;
				var v2;
				for k2, v2 in tmp {
					let out[k2] = v2;
				}
			}
			else {
				let out[key] = v;
			}
		}

		return out;
	}

	/**
	 * List to Array
	 *
	 * Convert a delimited list into a proper array.
	 *
	 * @param mixed $list List.
	 * @param mixed $args Arguments or delimiter.
	 *
	 * @args string $delimiter Delimiter.
	 * @args bool $trim Trim.
	 * @args bool $unique Unique.
	 * @args bool $sort Sort output.
	 * @args string $cast Cast to type.
	 * @args mixed $min Minimum value.
	 * @args mixed $max Maximum value.
	 *
	 * @return void Nothing.
	 */
	public static function fromList(var list, var args=null) -> array {
		array defaults = [
			"cast": "string",
			"delimiter": ",",
			"max": null,
			"min": null,
			"sort": false,
			"trim": true,
			"unique": true
		];
		array data;

		// There's a weird Zephir casting bug requiring we run these
		// two alternate realities separately.
		if ("string" === typeof args) {
			let data = (array) \Blobfolio\Cast::parseArgs(
				["delimiter": args],
				defaults
			);
		}
		else {
			let data = (array) \Blobfolio\Cast::parseArgs(args, defaults);
		}

		// Make sure the cast type makes sense.
		array castTypes = [
			"array",
			"bool",
			"boolean",
			"double",
			"float",
			"int",
			"integer",
			"long",
			"number",
			"string"
		];
		let data["cast"] = strtolower(data["cast"]);
		if (!in_array(data["cast"], castTypes, true)) {
			let data["cast"] = "string";
		}

		bool argsMin = (!empty data["min"]);
		bool argsMax = (!empty data["max"]);

		// Sanitize min/max.
		if (argsMin && argsMax && data["min"] > data["max"]) {
			var tmp = data["min"];
			let data["min"] = data["max"];
			let data["max"] = tmp;
		}

		let list = (array) \Blobfolio\Cast::toArray(list);
		if (!count(list)) {
			return [];
		}

		array out = [];
		var k;
		var v;
		var k2;
		var v2;

		for k, v in list {
			if ("array" === typeof v) {
				let list[k] = self::fromList(v, data);
			}
			else {
				// We need to work with strings.
				let list[k] = \Blobfolio\Cast::toString(v, globals_get("flag_flatten"));

				if (data["delimiter"]) {
					let list[k] = (array) explode(data["delimiter"], list[k]);
				}
				else {
					let list[k] = \Blobfolio\Strings::split(list[k]);
				}

				// Trimming?
				if (data["trim"]) {
					for k2, v2 in list[k] {
						let list[k][k2] = \Blobfolio\Strings::trim(v2, globals_get("flag_trusted"));
					}
				}

				// Get rid of empties.
				let list[k] = array_filter(list[k], "strlen");

				// Cast back?
				if ("string" !== data["cast"]) {
					let list[k] = \Blobfolio\Cast::toType(list[k], data["cast"]);
				}
			}

			// Add whatever we've got to the running total.
			for v2 in list[k] {
				if (
					(!argsMin || v2 >= data["min"]) &&
					(!argsMax || v2 <= data["max"])
				) {
					let out[] = v2;
				}
			}
		}

		if (count(out) > 1) {
			// Unique?
			if (data["unique"]) {
				let out = array_unique(out);
				let out = array_values(out);
			}

			// Sort?
			if (data["sort"]) {
				sort(out);
			}
		}

		return out;
	}

	/**
	 * Otherize
	 *
	 * Convert an indefinitely large array of totals to a fixed length
	 * with the chopped bits grouped under "other".
	 *
	 * @param array $arr Array.
	 * @param int $length Length.
	 * @param string $other Label.
	 * @return array|bool Array or false.
	 */
	public static function otherize(array arr, int length=5, string other="Other") -> array | bool {
		if ("associative" !== self::getType(arr)) {
			return false;
		}

		// Make sure everything is numeric.
		var k;
		var v;
		var type;

		for k, v in arr {
			let type = typeof v;
			if (("integer" !== type) && ("double" !== type)) {
				let arr[k] = \Blobfolio\Cast::toFloat(v, globals_get("flag_flatten"));
			}
		}

		arsort(arr);

		// Make sure we have a sane length.
		if (length < 1) {
			let length = 1;
		}

		// No need to otherize.
		if (count(arr) <= length) {
			return arr;
		}

		let other = \Blobfolio\Strings::utf8(other);
		if (empty other) {
			let other = "Other";
		}

		// Just sum it.
		if (1 === length) {
			return [other: array_sum(arr)];
		}

		array out = (array) array_slice(arr, 0, length - 1);
		let out[other] = array_sum(array_slice(arr, length - 1));

		return out;
	}

	/**
	 * To CSV
	 *
	 * Create a CSV from Data
	 *
	 * @param array $data Data.
	 * @param array $headers Headers.
	 * @param string $delimiter Delimiter.
	 * @param string $eol EOL.
	 * @return string CSV.
	 */
	public static function toCsv(array data, var headers=null, string delimiter=",", const string eol="\n") -> string {
		let delimiter = "\"" . str_replace("\"", "", delimiter) . "\"";

		// Data should be an array of arrays.
		let data = (array) array_values(array_filter(data, "is_array"));
		bool assoc = count(data) && ("associative" === self::getType(data[0]));

		if ("array" === typeof headers) {
			let headers = self::flatten(headers);
		}
		elseif (assoc) {
			let headers = array_keys(data[0]);
		}
		else {
			let headers = [];
		}

		array out = [];
		var k;
		var v;

		// Fix up headers.
		if (count(headers)) {
			for k, v in headers {
				let headers[k] = self::csvCell(v);
			}

			let out[] = "\"" . implode(delimiter, headers) . "\"";
		}

		if (count(data)) {
			array dataKeys = (array) array_keys(data[0]);
			var line;
			var k2;
			for k2, line in data {
				// Make sure keys are in the right order.
				if (assoc && k2 > 0 && ("associative" === self::getType(line))) {
					array tmp = [];
					for (v in dataKeys) {
						if (isset(line[v])) {
							let tmp[v] = line[v];
						}
						else {
							let tmp[v] = "";
						}
					}
					let line = tmp;
				}

				for k, v in line {
					let line[k] = self::csvCell(v);
				}

				let out[] = "\"" . implode(delimiter, line) . "\"";
			}
		}

		return implode(eol, out);
	}

	/**
	 * Sanitize CSV Cell
	 *
	 * @param mixed $str String.
	 * @return string String.
	 */
	private static function csvCell(var str) -> string {
		let str = \Blobfolio\Cast::toString(str, globals_get("flag_flatten"));
		let str = \Blobfolio\Strings::niceText(str, 0, globals_get("flag_trusted"));

		// Remove existing double quotes.
		while (false !== strpos(str, "\"\"")) {
			let str = str_replace("\"\"", "\"", str);
		}

		// Redo double quotes.
		return str_replace("\"", "\"\"", str);
	}

	/**
	 * Create Index Array
	 *
	 * This will convert a {k:v} associative array into an indexed array
	 * with {key: k, value: v} as the values. Useful when exporting
	 * sorted data to Javascript, which doesn't preserve object key
	 * ordering.
	 *
	 * @param array $arr Array.
	 * @param string $key Key.
	 * @param string $value Value.
	 * @return array Array.
	 */
	public static function toIndexed(array arr, const string key="key", const string value="value") -> array {
		array out = [];

		if (count(arr)) {
			var k;
			var v;
			for k, v in arr {
				let out[] = [
					key: k,
					value: v
				];
			}
		}

		return out;
	}



	// -----------------------------------------------------------------
	// Case
	// -----------------------------------------------------------------

	/**
	 * Case-Insensitive array_diff()
	 *
	 * Compute the difference between two or more arrays.
	 *
	 * @param array $arr1 Array.
	 * @param array $arr2 Array.
	 * @return array Difference.
	 */
	public static function iDiff() -> array {
		array targets = (array) func_get_args();
		int targetsLen = (int) count(targets);

		if (targetsLen < 2) {
			return [];
		}

		var k;
		var v;

		// Make sure all arguments are arrays.
		for v in targets {
			if ("array" !== typeof v) {
				return [];
			}
		}

		// Can't do anything if the first is empty.
		if (!count(targets[0])) {
			return [];
		}

		// One more time through, actually check here.
		array common;
		int x = 1;
		string currentType;

		while x < targetsLen {
			// Skip empties.
			if (!count(targets[x])) {
				let x++;
				continue;
			}

			// Lowercase values of current.
			for k, v in targets[x] {
				let currentType = typeof v;

				if ("string" === currentType) {
					let targets[x][k] = \Blobfolio\Strings::toLower(v);
				}

				// Remove non-comparable entries.
				if (
					("array" === currentType) ||
					(("object" === currentType) && (null !== v))
				) {
					unset(targets[x][k]);
				}
			}

			// Recount and move on if empty.
			if (!count(targets[x])) {
				let x++;
				continue;
			}

			let common = [];

			// Run through the first.
			for k, v in targets[0] {
				let currentType = typeof v;

				if (
					("array" === currentType) ||
					(("object" === currentType) && (null !== v))
				) {
					continue;
				}

				if ("string" === currentType) {
					let v = \Blobfolio\Strings::toLower(v);
				}

				// Save it (the original) if unique.
				if (!in_array(v, targets[x], true)) {
					let common[k] = targets[0][k];
				}
			}

			if (!count(common)) {
				return [];
			}

			// Override the original.
			let targets[0] = common;
			let x++;
		}

		return targets[0];
	}

	/**
	 * Case-Insensitive in_array()
	 *
	 * @param mixed $needle Needle.
	 * @param array $haystack Haystack.
	 * @param bool $strict Strict.
	 * @return bool True/false.
	 */
	public static function iInArray(const var needle, const array haystack, const bool strict) -> bool {
		return (false !== self::iSearch(needle, haystack, strict));
	}

	/**
	 * Case-Insensitive array_intersect()
	 *
	 * Compute the union between two or more arrays.
	 *
	 * @param array $arr1 Array.
	 * @param array $arr2 Array.
	 * @return array Difference.
	 */
	public static function iIntersect() -> array {
		array targets = (array) func_get_args();
		int targetsLen = (int) count(targets);

		if (targetsLen < 2) {
			return [];
		}

		var k;
		var v;

		// Make sure all arguments are arrays.
		for v in targets {
			if ("array" !== typeof v) {
				return [];
			}
		}

		// Can't do anything if the first is empty.
		if (!count(targets[0])) {
			return [];
		}

		// One more time through, actually check here.
		array common;
		int x = 1;
		string currentType;

		while x < targetsLen {
			// Skip empties.
			if (!count(targets[x])) {
				let x++;
				continue;
			}

			// Lowercase values of current.
			for k, v in targets[x] {
				let currentType = typeof v;

				if ("string" === currentType) {
					let targets[x][k] = \Blobfolio\Strings::toLower(v);
				}

				// Remove non-comparable entries.
				if (
					("array" === currentType) ||
					(("object" === currentType) && (null !== v))
				) {
					unset(targets[x][k]);
				}
			}

			// Recount and move on if empty.
			if (!count(targets[x])) {
				let x++;
				continue;
			}

			let common = [];

			// Run through the first.
			for k, v in targets[0] {
				let currentType = typeof v;

				if (
					("array" === currentType) ||
					(("object" === currentType) && (null !== v))
				) {
					continue;
				}

				if ("string" === currentType) {
					let v = \Blobfolio\Strings::toLower(v);
				}

				// Save it (the original) if unique.
				if (in_array(v, targets[x], true)) {
					let common[k] = targets[0][k];
				}
			}

			if (!count(common)) {
				return [];
			}

			// Override the original.
			let targets[0] = common;
			let x++;
		}

		return targets[0];
	}

	/**
	 * Case-Insensitive array_key_exists()
	 *
	 * @param mixed $needle Needle.
	 * @param array $haystack Haystack.
	 * @return bool True/false.
	 */
	public static function iKeyExists(const var needle, array haystack) -> bool {
		let haystack = array_keys(haystack);
		return (false !== self::iSearch(needle, haystack));
	}

	/**
	 * Case-Insensitive array_search()
	 *
	 * @param mixed $needle Needle.
	 * @param array $haystack Haystack.
	 * @param bool $strict Strict.
	 * @return mixed Key or false.
	 */
	public static function iSearch(var needle, array haystack, const bool strict=true) {
		if (!count(haystack)) {
			return false;
		}

		// Lowercase needle.
		if ("string" === typeof needle) {
			let needle = \Blobfolio\Strings::toLower(needle);

			// Lowercase haystack too.
			var k;
			var v;
			for k, v in haystack {
				if ("string" === typeof v) {
					let haystack[k] = \Blobfolio\Strings::toLower(v);
				}
			}
		}

		return array_search(needle, haystack, strict);
	}



	// -----------------------------------------------------------------
	// Helpers
	// -----------------------------------------------------------------

	/**
	 * Compare Two Arrays
	 *
	 * @param array $arr1 Array.
	 * @param array $arr2 Array.
	 * @return bool True/false.
	 */
	public static function compare(const array arr1, const array arr2) -> bool {
		int arr1Len = (int) count(arr1);
		int arr2Len = (int) count(arr2);

		// If both are empty, they're equal.
		if (!arr1Len && !arr2Len) {
			return true;
		}
		// If the counts are different, so are they.
		elseif (
			!arr1Len ||
			!arr2Len ||
			(arr1Len !== arr2Len) ||
			(count(array_intersect_key(arr1, arr2)) !== arr1Len)
		) {
			return false;
		}

		// What type of arrays are we dealing with?
		string arr1Type = (string) self::getType(arr1);
		string arr2Type = (string) self::getType(arr2);

		// If the types mismatch and one is associative, we're done.
		if (
			(arr1Type !== arr2Type) &&
			(
				("associative" === arr1Type) ||
				("associative" === arr2Type)
			)
		) {
			return false;
		}

		// If neither are associative, just check the intersect.
		if (("associative" !== arr1Type) && ("associative" !== arr2Type)) {
			return (count(array_intersect(arr1, arr2)) === arr1Len);
		}

		// Go line by line looking for all the ways they might diverge.
		var k;
		var v;
		for k, v in arr1 {
			if (!isset(arr2[k])) {
				return false;
			}

			let arr1Type = typeof v;
			let arr2Type = typeof arr2[k];
			if (arr1Type !== arr2Type) {
				return false;
			}

			// Recurse arrays.
			if ("array" === arr1Type) {
				if (!self::compare(v, arr2[k])) {
					return false;
				}
			}
			elseif (v !== arr2[k]) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Get Array Type
	 *
	 * "associative": If there are string keys.
	 * "sequential": If the keys are sequential numbers.
	 * "indexed": If the keys are at least numeric.
	 * FALSE: Any other condition.
	 *
	 * @param array $arr Array.
	 * @return string|bool Type. False on failure.
	 */
	public static function getType(const array arr) -> string | bool {
		if (!count(arr)) {
			return false;
		}

		array keys = (array) array_keys(arr);

		if (range(0, count(keys) - 1) === keys) {
			return "sequential";
		}

		elseif (count(keys) === count(array_filter(keys, "is_numeric"))) {
			return "indexed";
		}

		return "associative";
	}

	/**
	 * Return the last value of an array.
	 *
	 * This is like array_pop() but non-destructive.
	 *
	 * @param array $arr Array.
	 * @return mixed Value. False on error.
	 */
	public static function pop(array arr) {
		if (!count(arr)) {
			return false;
		}

		return end(arr);
	}

	/**
	 * Return a random array element.
	 *
	 * @param array $arr Array.
	 * @return mixed Value. False on error.
	 */
	public static function popRand(array arr) {
		int length = (int) count(arr);

		switch (length) {
			case 0:
				return false;
			case 1:
				return end(arr);
		}

		array keys = (array) array_keys(arr);
		int index = (int) random_int(0, length - 1);

		return arr[keys[index]];
	}

	/**
	 * Return the first value of an array.
	 *
	 * @param array $arr Array.
	 * @return mixed Value. False on error.
	 */
	public static function popTop(array arr) {
		if (!count(arr)) {
			return false;
		}

		reset(arr);
		return arr[key(arr)];
	}
}
