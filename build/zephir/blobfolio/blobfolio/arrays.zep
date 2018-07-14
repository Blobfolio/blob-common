//<?php
/**
 * Blobfolio: Arrays
 *
 * Array helpers.
 *
 * @see {blobfolio\common\file}
 * @see {blobfolio\common\ref\file}
 *
 * @package Blobfolio/Common
 * @author Blobfolio, LLC <hello@blobfolio.com>
 */

namespace Blobfolio;

use \Throwable;

final class Arrays {
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
	 * Flatten Multi-Dimensional Array
	 *
	 * Like array_values(), but move child values into the single (main)
	 * level.
	 *
	 * @param array $arr Array.
	 * @return array Array.
	 */
	public static function flatten(array arr) -> array {
		array out = [];

		if (!count(arr)) {
			return out;
		}

		var v;
		for v in arr {
			// Recurse arrays.
			if ("array" === typeof v) {
				let v = self::flatten(v);
				var v2;
				for v2 in v {
					let out[] = v2;
				}
			}
			else {
				let out[] = v;
			}
		}

		return out;
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
					key: $k,
					value: $v
				];
			}
		}

		return out;
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
		if (count(arr)) {
			return false;
		}

		reset(arr);
		return arr[key(arr)];
	}
}
