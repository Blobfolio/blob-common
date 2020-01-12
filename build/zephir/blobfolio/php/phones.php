<?php
/**
 * Blobfolio: International Phone Formatting
 *
 * @see {https://github.com/Blobfolio/blob-common}
 *
 * @package Blobfolio/Common
 * @author Blobfolio, LLC <hello@blobfolio.com>
 */

namespace Blobfolio;

use Exception;



final class Phones {
	const MINLENGTH = 3;
	const MAXLENGTH = 30;

	private static $_loaded_blob_phone = false;
	private static $_country;
	private static $_data;
	private static $_prefixes;
	private static $_regions;



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
	public static function nicePhone(string $phone, string $country='', $types=null) : string {
		$parsed = self::parsePhone($phone, $country);
		if ('array' !== \gettype($parsed)) {
			return '';
		}

		// Maybe restrict by type?
		if (('array' === \gettype($types)) && \count($types)) {
			$intersect = (array) \array_intersect($types, $parsed['types']);
			if (\count($intersect)) {
				return $parsed['number'];
			}
		}
		else {
			return $parsed['number'];
		}

		return '';
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
	public static function parsePhone(string $phone, string $country='') {
		// Early phone sanitizing.
		$phone = \preg_replace('/[^\d]/', '', $phone);
		$phoneLength = (int) \strlen($phone);
		if ($phoneLength < self::MINLENGTH || $phoneLength > self::MAXLENGTH) {
			return false;
		}

		// Make sure the data is loaded.
		self::loadData();

		// Sanitize default country.
		$country = \Blobfolio\Geo::niceCountry($country);
		if (empty($country)) {
			$country = (string) self::$_country;
		}
		if (! isset(self::$_data[$country])) {
			return false;
		}

		// Build a list of targets.
		$targets = (array) \Blobfolio\Geo::getNeighborCountries($country, 50);
		if (! \count($targets)) {
			$targets = (array) self::$_regions[self::$_data[$country]['region']];
			if ($country !== $targets[0]) {
				\array_unshift($targets, $country);
				$targets = \array_unique($targets);
			}
		}

		// If the number begins with the target country's prefix, strip
		// it.
		$outPrefix = '' . self::$_data[$country]['prefix'];
		$v2 = (string) \ltrim($phone, 0);
		if (0 === \strpos($v2, $outPrefix)) {
			$v2 = (string) \substr($v2, \strlen($outPrefix));
			$v = self::testPhone($v2, $country);
			if (false !== $v) {
				return $v;
			}
		}

		// Pass One: the number as is.
		foreach ($targets as $v) {
			$v = self::testPhone($phone, $v);
			if (false !== $v) {
				return $v;
			}
		}

		// Pass Two: try again without the prefix.
		foreach ($targets as $v) {
			$outPrefix = '' . self::$_data[$v]['prefix'];
			$v2 = (string) \ltrim($phone, '0');
			if (0 === \strpos($v2, $outPrefix)) {
				$v2 = (string) \substr($v2, \strlen($outPrefix));
				$v = self::testPhone($v2, $v);
				if (false !== $v) {
					return $v;
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
	private static function testPhone(string $phone, string $country) {
		// Run through each test!
		if (! \preg_match('#' . self::$_data[$country]['patterns'] . '#', $phone)) {
			return false;
		}

		$outPrefix = (string) self::$_data[$country]['prefix'];
		$outTypes = array();

		// Loop through types.
		$keys = (array) \array_keys(self::$_data[$country]['types']);
		foreach ($keys as $v3) {
			if (\preg_match('#^(' . $v3 . ')$#', $phone)) {
				$outTypes = \array_merge(
					$outTypes,
					self::$_data[$country]['types'][$v3]
				);
			}
		}

		// No types, no go.
		if (! \count($outTypes)) {
			return false;
		}
		elseif (\count($outTypes) > 1) {
			$outTypes = \array_unique($outTypes);
			\sort($outTypes);
		}

		// We found it! Now we just need to format the number.
		$keys = (array) \array_keys(self::$_data[$country]['formats']);
		foreach ($keys as $v3) {
			if (\preg_match('#^(' . $v3 . ')$#', $phone)) {
				$phone = (string) \preg_replace(
					'#^' . $v3 . '$#',
					self::$_data[$country]['formats'][$v3],
					$phone
				);
				return array(
					'country'=>$country,
					'prefix'=>\intval($outPrefix),
					'region'=>self::$_data[$country]['region'],
					'types'=>$outTypes,
					'number'=>'+' . $outPrefix . ' ' . $phone,
				);
			}
		}

		// We have to build something generic.
		return array(
			'country'=>$country,
			'prefix'=>\intval($outPrefix),
			'region'=>self::$_data[$country]['region'],
			'types'=>$outTypes,
			'number'=>'+' . $outPrefix . ' ' . $phone,
		);
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
	private static function loadData() : void {
		// Don't allow accidental repeats.
		if (true === self::$_loaded_blob_phone) {
			return;
		}

		$json = (string) \Blobfolio\Blobfolio::getDataDir('blob-phone.json');
		if (empty($json)) {
			throw new Exception('Missing phone formatting data.');
		}
		$tmp = \json_decode($json, true);
		if ('array' !== \gettype($tmp)) {
			throw new Exception('Could not parse phone formatting data.');
		}

		// Split it out to make the data easier to access later.
		self::$_data = (array) $tmp['data'];
		self::$_prefixes = (array) $tmp['prefixes'];
		self::$_regions = (array) $tmp['regions'];

		// While we're here, let's also set the default country.
		self::$_country = (string) \ini_get('blobfolio.country');
		self::$_country = \Blobfolio\Geo::niceCountry(self::$_country);
		if (empty(self::$_country)) {
			self::$_country = 'US';
		}

		self::$_loaded_blob_phone = true;
	}
}
