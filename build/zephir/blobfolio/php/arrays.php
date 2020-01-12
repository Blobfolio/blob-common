<?php
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
	public static function flatten(array $arr, int $flags=0) : array {
		if (empty($arr)) {
			return [];
		}

		$out = [];
		foreach ($arr as $v) {
			// Recurse arrays.
			if (is_array($v)) {
				if (!empty($v)) {
					$out = array_merge($out, self::flatten($v, $flags));
				}
			}
			else {
				$out[] = $v;
			}
		}

		if (!empty($out)) {
			$sort = !! ($flags & globals_get("flag_sort"));
			$unique = !! ($flags & globals_get("flag_unique"));
			if ($sort || $unique) {
				if ($unique) {
					$out = (array) array_unique($out);
				}
				if ($sort) {
					sort($out);
				}
				else {
					$out = array_values($out);
				}
			}
			else {
				$out = array_values($out);
			}
		}

		return $out;
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
	public static function flattenAssoc(array $arr, string $stub="") : array {
		if (empty($arr)) {
			return [];
		}

		$out = [];
		foreach ($arr as $k=>$v) {
			$key = $stub ? strval($stub . "_" . $k) : strval($k);

			if (is_array($v)) {
				$tmp = (array) self::flattenAssoc($v, $key);
				foreach ($tmp as $k2=>$v2) {
					$out[$k2] = $v2;
				}
			}
			else {
				$out[$key] = $v;
			}
		}

		return $out;
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
	public static function fromList($list, $args=null) : array {
		$defaults = [
			"cast"=>"string",
			"delimiter"=>",",
			"max"=>null,
			"min"=>null,
			"sort"=>false,
			"trim"=>true,
			"unique"=>true
		];

		// There's a weird Zephir casting bug requiring we run these
		// two alternate realities separately.
		if ("string" === gettype($args)) {
			$data = (array) \Blobfolio\Cast::parseArgs(
				["delimiter"=>$args],
				$defaults
			);
		}
		else {
			$data = (array) \Blobfolio\Cast::parseArgs($args, $defaults);
		}

		// Make sure the cast type makes sense.
		$castTypes = [
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
		$data["cast"] = strtolower($data["cast"]);
		if (!in_array($data["cast"], $castTypes, true)) {
			$data["cast"] = "string";
		}

		$argsMin = !empty($data["min"]);
		$argsMax = !empty($data["max"]);

		// Sanitize min/max.
		if ($argsMin && $argsMax && $data["min"] > $data["max"]) {
			$tmp = $data["min"];
			$data["min"] = $data["max"];
			$data["max"] = $tmp;
		}

		$list = (array) \Blobfolio\Cast::toArray($list);
		if (empty($list)) {
			return [];
		}

		$out = [];
		foreach ($list as $k=>$v) {
			if ("array" === gettype($v)) {
				$list[$k] = self::fromList($v, $data);
			}
			else {
				// We need to work with strings.
				$list[$k] = \Blobfolio\Cast::toString($v, globals_get("flag_flatten"));

				if ($data["delimiter"]) {
					$list[$k] = (array) explode($data["delimiter"], $list[$k]);
				}
				else {
					$list[$k] = \Blobfolio\Strings::split($list[$k]);
				}

				// Trimming?
				if ($data["trim"]) {
					foreach ($list[$k] as $k2=>$v2) {
						$list[$k][$k2] = \Blobfolio\Strings::trim($v2, globals_get("flag_trusted"));
					}
				}

				// Get rid of empties.
				$list[$k] = array_filter($list[$k], "strlen");

				// Cast back?
				if ("string" !== $data["cast"]) {
					$list[$k] = \Blobfolio\Cast::toType($list[$k], $data["cast"]);
				}
			}

			// Add whatever we've got to the running total.
			foreach ($list[$k] as $v2) {
				if (
					(!$argsMin || $v2 >= $data["min"]) &&
					(!$argsMax || $v2 <= $data["max"])
				) {
					$out[] = $v2;
				}
			}
		}

		if (count($out) > 1) {
			// Unique?
			if ($data["unique"]) {
				$out = array_unique($out);
				$out = array_values($out);
			}

			// Sort?
			if ($data["sort"]) {
				sort($out);
			}
		}

		return $out;
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
	public static function otherize(array $arr, int $length=5, string $other="Other") {
		if ("associative" !== self::getType($arr)) {
			return false;
		}

		// Make sure everything is numeric.
		foreach ($arr as $k=>$v) {
			$type = gettype($v);
			if (("integer" !== $type) && ("double" !== $type)) {
				$arr[$k] = \Blobfolio\Cast::toFloat($v, globals_get("flag_flatten"));
			}
		}

		arsort($arr);

		// Make sure we have a sane length.
		if ($length < 1) {
			$length = 1;
		}

		// No need to otherize.
		if (count($arr) <= $length) {
			return $arr;
		}

		$other = \Blobfolio\Strings::utf8($other);
		if (empty($other)) {
			$other = "Other";
		}

		// Just sum it.
		if (1 === $length) {
			return [$other=>array_sum($arr)];
		}

		$out = (array) array_slice($arr, 0, $length - 1);
		$out[$other] = array_sum(array_slice($arr, $length - 1));

		return $out;
	}

	/**
	 * Oxford Join
	 *
	 * Join a list of elements the way english professors do.
	 *
	 * @param array $arr Array.
	 * @param string $separator Final separator.
	 * @return string Joined.
	 */
	public static function oxford_join(array $arr, string $separator="and") : string {
		if (empty($arr)) {
			return "";
		}

		// Clean up the separator.
		$separator = trim($separator);
		if (empty($separator)) {
			$separator = "and";
		}

		// Let's build a nice array.
		$out = [];
		foreach ($arr as $v) {
			if (! empty($v) && ("string" === gettype($v) || is_numeric($v))) {
				$out[] = (string) $v;
			}
		}

		if (count($out) <= 2) {
			return implode(" " . $separator . " ", $out);
		}

		$last = (string) array_pop($out);
		return implode(", ", $out) . ", " . $separator . " " . $last;
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
	public static function toCsv(array $data, $headers=null, string $delimiter=",", string $eol="\n") : string {
		$delimiter = "\"" . str_replace("\"", "", $delimiter) . "\"";

		// Data should be an array of arrays.
		$data = (array) array_values(array_filter($data, "is_array"));
		$assoc = count($data) && ("associative" === self::getType($data[0]));

		if ("array" === gettype($headers)) {
			$headers = self::flatten($headers);
		}
		elseif ($assoc) {
			$headers = array_keys($data[0]);
		}
		else {
			$headers = [];
		}

		$out = [];

		// Fix up headers.
		if (count($headers)) {
			foreach ($headers as $k=>$v) {
				$headers[$k] = self::csvCell($v);
			}

			$out[] = "\"" . implode($delimiter, $headers) . "\"";
		}

		if (count($data)) {
			$dataKeys = (array) array_keys($data[0]);
			foreach ($data as $k2=>$line) {
				// Make sure keys are in the right order.
				if ($assoc && $k2 > 0 && ("associative" === self::getType($line))) {
					$tmp = [];
					foreach ($dataKeys as $v) {
						if (isset($line[$v])) {
							$tmp[$v] = $line[$v];
						}
						else {
							$tmp[$v] = "";
						}
					}
					$line = $tmp;
				}

				foreach ($line as $k=>$v) {
					$line[$k] = self::csvCell($v);
				}

				$out[] = "\"" . implode($delimiter, $line) . "\"";
			}
		}

		return implode($eol, $out);
	}

	/**
	 * Sanitize CSV Cell
	 *
	 * @param mixed $str String.
	 * @return string String.
	 */
	private static function csvCell($str) : string {
		$str = \Blobfolio\Cast::toString($str, globals_get("flag_flatten"));
		$str = \Blobfolio\Strings::niceText($str, 0, globals_get("flag_trusted"));

		// Remove existing double quotes.
		while (false !== strpos($str, "\"\"")) {
			$str = str_replace("\"\"", "\"", $str);
		}

		// Redo double quotes.
		return str_replace("\"", "\"\"", $str);
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
	public static function toIndexed(array $arr, string $key="key", string $value="value") : array {
		$out = [];
		if (count($arr)) {
			foreach ($arr as $k=>$v) {
				$out[] = [
					$key=>$k,
					$value=>$v
				];
			}
		}

		return $out;
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
	public static function iDiff() : array {
		$targets = (array) func_get_args();
		$targetsLen = (int) count($targets);

		if ($targetsLen < 2) {
			return [];
		}

		// Make sure all arguments are arrays.
		foreach ($targets as $v) {
			if ("array" !== gettype($v)) {
				return [];
			}
		}

		// Can't do anything if the first is empty.
		if (empty($targets[0])) {
			return [];
		}

		// One more time through, actually check here.
		$common = [];
		$x = 1;
		$currentType = null;

		while ($x < $targetsLen) {
			// Skip empties.
			if (!count($targets[$x])) {
				$x++;
				continue;
			}

			// Lowercase values of current.
			foreach ($targets[$x] as $k=>$v) {
				$currentType = gettype($v);

				if ("string" === $currentType) {
					$targets[$x][$k] = \Blobfolio\Strings::toLower($v);
				}

				// Remove non-comparable entries.
				if (
					("array" === $currentType) ||
					(("object" === $currentType) && (null !== $v))
				) {
					unset($targets[$x][$k]);
				}
			}

			// Recount and move on if empty.
			if (!count($targets[$x])) {
				$x++;
				continue;
			}

			$common = [];

			// Run through the first.
			foreach ($targets[0] as $k=>$v) {
				$currentType = gettype($v);

				if (
					("array" === $currentType) ||
					(("object" === $currentType) && (null !== $v))
				) {
					continue;
				}

				if ("string" === $currentType) {
					$v = \Blobfolio\Strings::toLower(v);
				}

				// Save it (the original) if unique.
				if (!in_array($v, $targets[$x], true)) {
					$common[$k] = $targets[0][$k];
				}
			}

			if (!count($common)) {
				return [];
			}

			// Override the original.
			$targets[0] = $common;
			$x++;
		}

		return $targets[0];
	}

	/**
	 * Case-Insensitive in_array()
	 *
	 * @param mixed $needle Needle.
	 * @param array $haystack Haystack.
	 * @param bool $strict Strict.
	 * @return bool True/false.
	 */
	public static function iInArray($needle, array $haystack, bool $strict = true) : bool {
		return (false !== self::iSearch($needle, $haystack, $strict));
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
	public static function iIntersect() : array {
		$targets = (array) func_get_args();
		$targetsLen = (int) count($targets);

		if ($targetsLen < 2) {
			return [];
		}

		// Make sure all arguments are arrays.
		foreach ($targets as $v) {
			if ("array" !== gettype($v)) {
				return [];
			}
		}

		// Can't do anything if the first is empty.
		if (!count($targets[0])) {
			return [];
		}

		// One more time through, actually check here.
		$common = [];
		$x = 1;
		$currentType = null;

		while ($x < $targetsLen) {
			// Skip empties.
			if (!count($targets[$x])) {
				$x++;
				continue;
			}

			// Lowercase values of current.
			foreach ($targets[$x] as $k=>$v) {
				$currentType = gettype($v);

				if ("string" === $currentType) {
					$targets[$x][$k] = \Blobfolio\Strings::toLower($v);
				}

				// Remove non-comparable entries.
				if (
					("array" === $currentType) ||
					(("object" === $currentType) && (null !== $v))
				) {
					unset($targets[$x][$k]);
				}
			}

			// Recount and move on if empty.
			if (!count($targets[$x])) {
				$x++;
				continue;
			}

			$common = [];

			// Run through the first.
			foreach ($targets[0] as $k=>$v) {
				$currentType = gettype($v);

				if (
					("array" === $currentType) ||
					(("object" === $currentType) && (null !== $v))
				) {
					continue;
				}

				if ("string" === $currentType) {
					$v = \Blobfolio\Strings::toLower(v);
				}

				// Save it (the original) if unique.
				if (in_array($v, $targets[$x], true)) {
					$common[$k] = $targets[0][$k];
				}
			}

			if (!count($common)) {
				return [];
			}

			// Override the original.
			$targets[0] = $common;
			$x++;
		}

		return $targets[0];
	}

	/**
	 * Case-Insensitive array_key_exists()
	 *
	 * @param mixed $needle Needle.
	 * @param array $haystack Haystack.
	 * @return bool True/false.
	 */
	public static function iKeyExists($needle, array $haystack) : bool {
		$haystack = array_keys($haystack);
		return (false !== self::iSearch($needle, $haystack));
	}

	/**
	 * Case-Insensitive array_search()
	 *
	 * @param mixed $needle Needle.
	 * @param array $haystack Haystack.
	 * @param bool $strict Strict.
	 * @return mixed Key or false.
	 */
	public static function iSearch($needle, array $haystack, bool $strict=true) {
		if (!count($haystack)) {
			return false;
		}

		// Lowercase needle.
		if ("string" === gettype($needle)) {
			$needle = \Blobfolio\Strings::toLower($needle);

			// Lowercase haystack too.
			foreach ($haystack as $k=>$v) {
				if ("string" === gettype($v)) {
					$haystack[$k] = \Blobfolio\Strings::toLower($v);
				}
			}
		}

		return array_search($needle, $haystack, $strict);
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
	public static function compare(array $arr1, array $arr2) : bool {
		$arr1Len = (int) count($arr1);
		$arr2Len = (int) count($arr2);

		// If both are empty, they're equal.
		if (!$arr1Len && !$arr2Len) {
			return true;
		}
		// If the counts are different, so are they.
		elseif (
			!$arr1Len ||
			!$arr2Len ||
			($arr1Len !== $arr2Len) ||
			(count(array_intersect_key($arr1, $arr2)) !== $arr1Len)
		) {
			return false;
		}

		// What type of arrays are we dealing with?
		$arr1Type = (string) self::getType($arr1);
		$arr2Type = (string) self::getType($arr2);

		// If the types mismatch and one is associative, we're done.
		if (
			($arr1Type !== $arr2Type) &&
			(
				("associative" === $arr1Type) ||
				("associative" === $arr2Type)
			)
		) {
			return false;
		}

		// If neither are associative, just check the intersect.
		if (("associative" !== $arr1Type) && ("associative" !== $arr2Type)) {
			return (count(array_intersect($arr1, $arr2)) === $arr1Len);
		}

		// Go line by line looking for all the ways they might diverge.
		foreach ($arr1 as $k=>$v) {
			if (!isset($arr2[$k])) {
				return false;
			}

			$arr1Type = gettype($v);
			$arr2Type = gettype($arr2[$k]);
			if ($arr1Type !== $arr2Type) {
				return false;
			}

			// Recurse arrays.
			if ("array" === $arr1Type) {
				if (!self::compare($v, $arr2[$k])) {
					return false;
				}
			}
			elseif ($v !== $arr2[$k]) {
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
	public static function getType(array $arr) {
		if (empty($arr)) {
			return false;
		}

		$keys = (array) array_keys($arr);

		if (range(0, count($keys) - 1) === $keys) {
			return "sequential";
		}

		elseif (count($keys) === count(array_filter($keys, "is_numeric"))) {
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
	public static function pop(array $arr) {
		if (empty($arr)) {
			return false;
		}

		return end($arr);
	}

	/**
	 * Return a random array element.
	 *
	 * @param array $arr Array.
	 * @return mixed Value. False on error.
	 */
	public static function popRand(array $arr) {
		$length = (int) count($arr);

		switch ($length) {
			case 0:
				return false;
			case 1:
				return end($arr);
		}

		$keys = (array) array_keys($arr);
		$index = (int) random_int(0, $length - 1);

		return $arr[$keys[$index]];
	}

	/**
	 * Return the first value of an array.
	 *
	 * @param array $arr Array.
	 * @return mixed Value. False on error.
	 */
	public static function popTop(array $arr) {
		if (empty($arr)) {
			return false;
		}

		reset($arr);
		return $arr[key($arr)];
	}
}
