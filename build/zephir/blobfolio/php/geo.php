<?php
/**
 * Blobfolio: Spacetime Helpers
 *
 * @see {https://github.com/Blobfolio/blob-common}
 *
 * @package Blobfolio/Common
 * @author Blobfolio, LLC <hello@blobfolio.com>
 */

namespace Blobfolio;

use Blobfolio\Blobfolio as Shim;
use Exception;



final class Geo {
	private static $_loaded_geo = false;

	private static $_au;
	private static $_ca;
	private static $_country;
	private static $_countries;
	private static $_regions = array('Africa', 'Asia', 'Australia', 'Europe', 'North America', 'South America');
	private static $_timezones;
	private static $_us;



	// -----------------------------------------------------------------
	// Formatting
	// -----------------------------------------------------------------

	/**
	 * Nice Address
	 *
	 * @param array $parts Parts.
	 * @param int $flags Flags.
	 * @return array Parts.
	 */
	public static function niceAddress(array $parts, int $flags=7) : array {
		// Load data.
		self::loadData();

		// First figure out what fields we should be including.
		$flagsEmail = !! ($flags & Shim::ADDRESS_FIELD_EMAIL);
		$flagsPhone = !! ($flags & Shim::ADDRESS_FIELD_PHONE);
		$flagsCompany = !! ($flags & Shim::ADDRESS_FIELD_COMPANY);

		$template = array(
			'name'=>'',
			'street'=>'',
			'city'=>'',
			'state'=>'',
			'zip'=>'',
			'country'=>'',
		);

		// Add extra fields.
		if ($flagsCompany) {
			$template['company'] = '';
		}
		if ($flagsPhone) {
			$template['phone'] = '';
		}
		if ($flagsEmail) {
			$template['email'] = '';
		}

		// Pre-clean: Name.
		$aliases = array();
		if (! isset($parts['name']) || empty($parts['name'])) {
			$aliases = array(
				array('firstname', 'lastname'),
				array('first_name', 'last_name'),
				array('first', 'last'),
			);

			foreach ($aliases as $v) {
				if (
					isset($parts[$v[0]]) &&
					isset($parts[$v[1]]) &&
					(! empty($parts[$v[0]]) || ! empty($parts[$v[1]]))
				) {
					$parts['name'] = \trim(
						$parts[$v[0]] . ' ' . $parts[$v[1]]
					);
					break;
				}
			}
		}

		// Pre-clean: Address.
		if (! isset($parts['street']) || empty($parts['street'])) {
			$aliases = array(
				'street',
				'address',
				'address_line',
			);

			foreach ($aliases as $v) {
				if (
					(isset($parts[$v . '1']) && ! empty($parts[$v . '1'])) ||
					(isset($parts[$v . '2']) && ! empty($parts[$v . '2']))
				) {
					if (isset($parts[$v . '1'])) {
						$parts['street'] = $parts[$v . '1'];
					}
					if (isset($parts[$v . '2'])) {
						$parts['street'] .= ' ' . $parts[$v . '2'];
					}
					$parts['street'] = \trim($parts['street']);
					break;
				}
				elseif (
					(isset($parts[$v . '_1']) && ! empty($parts[$v . '_1'])) ||
					(isset($parts[$v . '_2']) && ! empty($parts[$v . '_2']))
				) {
					if (isset($parts[$v . '_1'])) {
						$parts['street'] = $parts[$v . '_1'];
					}
					if (isset($parts[$v . '_2'])) {
						$parts['street'] .= ' ' . $parts[$v . '_2'];
					}
					$parts['street'] = \trim($parts['street']);
					break;
				}
				elseif (
					('street' !== $v) &&
					isset($parts[$v]) &&
					! empty($parts[$v])
				) {
					$parts['street'] = \trim($parts[$v]);
					break;
				}
			}
		}

		// Company aliases.
		if (! isset($parts['company']) || empty($parts['company'])) {
			if (isset($parts['business']) && ! empty($parts['business'])) {
				$parts['company'] = $parts['business'];
			}
		}

		// Email alias.
		if (! isset($parts['email']) || empty($parts['email'])) {
			if (isset($parts['email_address']) && ! empty($parts['email_address'])) {
				$parts['email'] = $parts['email_address'];
			}
		}

		// Phone alias.
		if (! isset($parts['phone']) || empty($parts['phone'])) {
			$aliases = array(
				'telephone',
				'tel',
				'phone_number',
			);

			foreach ($aliases as $v) {
				if (
					isset($parts[$v]) &&
					! empty($parts[$v])
				) {
					$parts['phone'] = \trim($parts[$v]);
					break;
				}
			}
		}

		// Crunch the template.
		$out = (array) \Blobfolio\Cast::parseArgs($parts, $template);

		// Some formatting can be applied en masse.
		foreach ($out as $k=>$v) {
			// Everything should be nice.
			$out[$k] = (string) \Blobfolio\Strings::niceText(
				$v,
				0,
				Shim::TRUSTED
			);

			// Move on if we have nothing.
			if (empty($out[$k])) {
				// We can always set a country.
				if ('country' === $k) {
					$out['country'] = (string) self::$_country;
				}
				continue;
			}

			// Key-based changes.
			switch ($k) {
				case 'name':
					$out[$k] = (string) \Blobfolio\Retail::niceName($out[$k]);
					break;
				case 'country':
					$out['country'] = self::niceCountry($out[$k]);
					if (empty($out['country'])) {
						$out['country'] = (string) self::$_country;;
					}
					break;
				case 'email':
					$out[$k] = (string) \Blobfolio\Domains::niceEmail(
						$out[$k],
						Shim::TRUSTED
					);
					break;
				case 'phone':
					$out[$k] = (string) \Blobfolio\Phones::nicePhone(
						$out[$k],
						$out['country']
					);
					break;
				case 'company':
					break;
				default:
					// Uppercase everything else.
					$out[$k] = (string) \Blobfolio\Strings::toUpper(
						$out[$k],
						Shim::TRUSTED
					);
			}
		}

		// US.
		if ('US' === $out['country']) {
			$out['state'] = (string) self::niceUsState($out['state']);
			$out['zip'] = (string) self::niceZip5($out['zip']);
		}
		// Canada.
		elseif ('CA' === $out['country']) {
			$out['state'] = (string) self::niceCaProvince($out['state']);
			$out['zip'] = (string) self::niceCaPostalCode($out['zip']);
		}
		// Australia.
		elseif ('AU' === $out['country']) {
			$out['state'] = (string) self::niceAuState($out['state']);
		}

		return $out;
	}

	/**
	 * Nice Country
	 *
	 * @param string $country Country.
	 * @return string Country.
	 */
	public static function niceCountry(string $country) : string {
		$country = \Blobfolio\Strings::whitespace($country);
		if (empty($country)) {
			return '';
		}

		// Make sure the data is loaded.
		self::loadData();

		// Uppercase it.
		$country = \Blobfolio\Strings::toUpper($country, Shim::TRUSTED);

		// A direct hit!
		if (isset(self::$_countries[$country])) {
			return $country;
		}

		// Check for common aliases.
		$aliases = array(
			'BRITAIN'=>'GB',
			'GREAT BRITAIN'=>'GB',
			'U. S. A.'=>'US',
			'U. S. S. R.'=>'RU',
			'U.S.A.'=>'US',
			'U.S.S.R.'=>'RU',
			'UNITED STATES OF AMERICA'=>'US',
			'UNITED STATES'=>'US',
			'USSR'=>'RU',
		);
		if (isset($aliases[$country])) {
			return $aliases[$country];
		}

		// Run through each country and see if the name matches.
		foreach (self::$_countries as $k=>$v) {
			$v['name'] = (string) \Blobfolio\Strings::toUpper($v['name'], Shim::TRUSTED);
			if ($country === $v['name']) {
				return (string) $k;
			}
		}

		// Sadness.
		return '';
	}

	/**
	 * Nice AU State
	 *
	 * @param string $state State.
	 * @return string State.
	 */
	public static function niceAuState(string $state) : string {
		$state = \Blobfolio\Strings::whitespace($state);
		if (empty($state)) {
			return '';
		}

		// Make sure the data is loaded.
		self::loadData();

		// Uppercase it.
		$state = (string) \strtoupper($state);

		// A direct hit!
		if (isset(self::$_au[$state])) {
			return $state;
		}

		// Run through each state and see if the name matches.
		foreach (self::$_au as $k=>$v) {
			$v = (string) \strtoupper($v);
			if ($state === $v) {
				return (string) $k;
			}
		}

		// Sadness.
		return '';
	}

	/**
	 * Nice CA Postal Code
	 *
	 * @param string $str Code.
	 * @return string Code.
	 */
	public static function niceCaPostalCode(string $str) : string {
		$str = \strtoupper($str);

		// Alphanumeric, minus D, F, I, O, Q, and U.
		$str = \preg_replace('/[^A-CEGHJ-NPR-TV-Z\d]/', '', $str);

		// W and Z are not allowed in the first slot, otherwise it
		// just alternates between letters and numbers.
		if (! \preg_match('/^[A-VXY][\d][A-Z][\d][A-Z][\d]$/', $str)) {
			return '';
		}

		// If it looks good, add a space in the middle.
		return \substr($str, 0, 3) . ' ' . \substr($str, -3);
	}

	/**
	 * Nice CA Province
	 *
	 * @param string $state State.
	 * @return string State.
	 */
	public static function niceCaProvince(string $state) : string {
		$state = \Blobfolio\Strings::whitespace($state);
		if (empty($state)) {
			return '';
		}

		// Make sure the data is loaded.
		self::loadData();

		// Uppercase it.
		$state = (string) \strtoupper($state);

		// A direct hit!
		if (isset(self::$_ca[$state])) {
			return $state;
		}

		// Run through each state and see if the name matches.
		foreach (self::$_ca as $k=>$v) {
			$v = (string) \strtoupper($v);
			if ($state === $v) {
				return (string) $k;
			}
		}

		// Sadness.
		return '';
	}

	/**
	 * Nice US State
	 *
	 * @param string $state State.
	 * @return string State.
	 */
	public static function niceUsState(string $state) : string {
		$state = \Blobfolio\Strings::whitespace($state);
		if (empty($state)) {
			return '';
		}

		// Make sure the data is loaded.
		self::loadData();

		// Uppercase it.
		$state = (string) \strtoupper($state);

		// A direct hit!
		if (isset(self::$_us[$state])) {
			return $state;
		}

		// Run through each state and see if the name matches.
		foreach (self::$_us as $k=>$v) {
			$v = (string) \strtoupper($v);
			if ($state === $v) {
				return (string) $k;
			}
		}

		// Sadness.
		return '';
	}

	/**
	 * Nice US ZIP5
	 *
	 * @param string $str ZIP.
	 * @return string ZIP.
	 */
	public static function niceZip5(string $str) : string {
		$str = \preg_replace('/[^\d]/', '', $str);
		if (\strlen($str) < 5) {
			$str = \sprintf('%05d', $str);
		}
		elseif (\strlen($str) > 5) {
			$str = \substr($str, 0, 5);
		}

		if ('00000' === $str) {
			return '';
		}

		return $str;
	}

	/**
	 * Nice Datetime
	 *
	 * @param mixed $str Date or timestamp.
	 * @return string Datetime.
	 */
	public static function niceDatetime($str) : string {
		// We don't need fancy casting.
		if ('string' !== \gettype($str)) {
			if (\is_numeric($str)) {
				$str = (string) $str;
			}
			else {
				return '0000-00-00 00:00:00';
			}
		}

		// Could be a timestamp.
		if (\preg_match('/^\d{9,}$/', $str)) {
			return \date('Y-m-d H:i:s', \intval($str));
		}

		$str = \trim($str);

		if (
			empty($str) ||
			(0 === \strpos($str, '0000-00-00'))
		) {
			return '0000-00-00 00:00:00';
		}

		$timestamp = \strtotime($str);
		if (false === $timestamp) {
			return '0000-00-00 00:00:00';
		}

		return \date('Y-m-d H:i:s', $timestamp);
	}

	/**
	 * Nice Date
	 *
	 * @param mixed $str Date.
	 * @return string Date.
	 */
	public static function niceDate($str) : string {
		$str = (string) self::niceDatetime($str);
		return \substr($str, 0, 10);
	}

	/**
	 * Nice Timezone
	 *
	 * @param string $tz Timezone.
	 * @return string Timezone.
	 */
	public static function niceTimezone(string $tz) : string {
		$tz = (string) \strtoupper($tz);
		$tz = (string) \preg_replace('/\s/u', '', $tz);

		// Make sure the data is loaded.
		self::loadData();

		if (empty($tz) || ! isset(self::$_timezones[$tz])) {
			return 'UTC';
		}

		return self::$_timezones[$tz];
	}

	/**
	 * Check Timezone For Daylight Saving
	 *
	 * @param string $tz Timezone.
	 * @return bool True/false.
	 */
	public static function isTimezoneDst(string $tz='') : bool {
		// If not specifying a specific timezone, just find the answer.
		if (empty($tz)) {
			$now = (array) \localtime(\time(), true);
			return (bool) $now['tm_isdst'];
		}

		// We have to temporarily swap PHP's timezone.
		$tz = self::niceTimezone($tz);
		$tz_old = (string) \date_default_timezone_get();
		if ($tz !== $tz_old) {
			\date_default_timezone_set($tz);
		}

		// Pull the data.
		$now = (array) \localtime(\time(), true);

		// Set it back to the original time.
		if ($tz !== $tz_old) {
			\date_default_timezone_set($tz_old);
		}

		return (bool) $now['tm_isdst'];
	}

	/**
	 * Is Daylight Saving
	 *
	 * @return bool True/false.
	 */
	public static function isDst() : bool {
		return self::isTimezoneDst();
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
	public static function getDistance(float $lat1, float $lon1, float $lat2, float $lon2) : float {
		// The same?
		if (($lat1 === $lat2) && ($lon1 === $lon2)) {
			return 0.0;
		}

		$theta = $lon1 - $lon2;
		$distance = \sin(\deg2rad($lat1)) * \sin(\deg2rad($lat2)) + \cos(\deg2rad($lat1)) * \cos(\deg2rad($lat2)) * \cos(\deg2rad($theta));
		$distance = \acos($distance);
		return $distance * 60 * 1.1515;
	}

	/**
	 * Days Between Dates
	 *
	 * @param string $date1 Date.
	 * @param string $date2 Date.
	 * @return int Difference.
	 */
	public static function dateDiff($date1, $date2) : int {
		$date1 = self::niceDate($date1);
		$date2 = self::niceDate($date2);

		// Bad dates.
		if (
			($date1 === $date2) ||
			('0000-00-00' === $date1) ||
			('0000-00-00' === $date2)
		) {
			return 0;
		}

		$dt1 = new \DateTime(\strval($date1));
		$dt2 = new \DateTime(\strval($date2));
		$diff = $dt1->diff($dt2);

		return \abs($diff->days);
	}

	/**
	 * To Timezone
	 *
	 * @param string $date Date.
	 * @param string $from From.
	 * @param string $to To.
	 * @return string Date.
	 */
	public static function toTimezone(string $date, string $from='UTC', string $to='UTC') : string {
		$date = self::niceDatetime($date);

		if ('UTC' !== $from) {
			$from = self::niceTimezone($from);
		}

		if ('UTC' !== $to) {
			$to = self::niceTimezone($to);
		}

		// Nothing to do.
		if (('0000-00-00 00:00:00' === $date) || ($from === $to)) {
			return $date;
		}

		$dateNew = new \DateTime($date, new \DateTimeZone($from));
		$dateNew->setTimezone(new \DateTimeZone($to));
		return $dateNew->format('Y-m-d H:i:s');
	}



	// -----------------------------------------------------------------
	// Data
	// -----------------------------------------------------------------

	/**
	 * Get AU States
	 *
	 * @return array States.
	 */
	public static function getAuStates() : array {
		// Make sure the data is loaded.
		self::loadData();
		return self::$_au;
	}

	/**
	 * Get CA Provinces
	 *
	 * @return array Provinces.
	 */
	public static function getCaProvinces() : array {
		// Make sure the data is loaded.
		self::loadData();
		return self::$_ca;
	}

	/**
	 * Get Countries
	 *
	 * @return array States.
	 */
	public static function getCountries() : array {
		// Make sure the data is loaded.
		self::loadData();
		return self::$_countries;
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
	public static function getNeighborCountries(string $country, int $limit = -1) : array {
		$country = self::niceCountry($country);
		if (
			empty($country) ||
			(0 === self::$_countries[$country]['lat']) ||
			(0 === self::$_countries[$country]['lon'])
		) {
			return array();
		}

		$countries = array();
		foreach (self::$_countries as $k=>$v) {
			// The target is closest to itself.
			if ($k === $country) {
				$countries[$k] = 0.0;
			}
			// If a country borders the target, consider that 1.0.
			elseif (\in_array($k, self::$_countries[$country]['borders'], true)) {
				$countries[$k] = 1.0;
			}
			// Otherwise calculate central distances.
			elseif ($v['lat'] && $v['lon']) {
				$countries[$k] = (float) self::getDistance(
					self::$_countries[$country]['lat'],
					self::$_countries[$country]['lon'],
					$v['lat'],
					$v['lon']
				);
			}
		}

		// Sort by distance.
		\asort($countries);

		// Chop long results.
		if ($limit > 0 && \count($countries) > $limit) {
			\array_splice($countries, $limit);
		}

		return \array_keys($countries);
	}

	/**
	 * Get Regions
	 *
	 * @return array Regions.
	 */
	public static function getRegions() : array {
		return self::$_regions;
	}

	/**
	 * Get US States
	 *
	 * @param int $flags Flags.
	 * @return array States.
	 */
	public static function getUsStates(int $flags=1) : array {
		// Make sure the data is loaded.
		self::loadData();

		// Strip the territories and military bases, but keep DC.
		$extra = !! ($flags & Shim::US_TERRITORIES);
		if (! $extra) {
			$out = (array) self::$_us;
			unset($out['AA']);
			unset($out['AE']);
			unset($out['AP']);
			unset($out['AS']);
			unset($out['FM']);
			unset($out['GU']);
			unset($out['MH']);
			unset($out['MP']);
			unset($out['PW']);
			unset($out['PR']);
			unset($out['VI']);
			return $out;
		}

		return self::$_us;
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
	private static function loadData() : void {
		// Only load once.
		if (true === self::$_loaded_geo) {
			return;
		}

		$json = (string) \Blobfolio\Blobfolio::getDataDir('geo.json');
		if (empty($json)) {
			throw new Exception('Missing geo data.');
		}
		$tmp = \json_decode($json, true);
		if ('array' !== \gettype($tmp)) {
			throw new Exception('Could not parse geo data.');
		}

		// Split it out to make the data easier to access later.
		self::$_au = (array) $tmp['au'];
		self::$_ca = (array) $tmp['ca'];
		self::$_countries = (array) $tmp['countries'];
		self::$_timezones = (array) $tmp['timezones'];
		self::$_us = (array) $tmp['us'];

		// While we're here, let's also set the default country.
		self::$_country = (string) \ini_get('blobfolio.country');
		self::$_country = \Blobfolio\Geo::niceCountry(self::$_country);
		if (empty(self::$_country)) {
			self::$_country = 'US';
		}

		self::$_loaded_geo = true;
	}
}
