<?php
/**
 * Sanitizing - By Reference
 *
 * Functions for sanitizing data.
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

namespace blobfolio\common\ref;

class sanitize {

	/**
	 * Strip Accents
	 *
	 * @param string $str String.
	 * @return string String.
	 */
	public static function accents(&$str) {
		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::accents($str[$k]);
			}
		}
		else {
			cast::string($str);

			if (preg_match('/[\x80-\xff]/', $str)) {
				$str = strtr($str, \blobfolio\common\constants::ACCENT_CHARS);
			}
		}

		return true;
	}

	/**
	 * Attribute Value
	 *
	 * This will decode entities, strip control
	 * characters, and trim outside whitespace.
	 *
	 * Note: this should not be used for safe
	 * insertion into HTML. For that, use the
	 * html() function.
	 *
	 * @param string $str String.
	 * @return bool True.
	 */
	public static function attribute_value(&$str='') {
		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::attribute_value($str[$k]);
			}
		}
		else {
			cast::string($str);
			static::control_characters($str);
			format::decode_entities($str);

			// And trim the edges while we're here.
			$str = preg_replace('/^\s+/u', '', $str);
			$str = preg_replace('/\s+$/u', '', $str);
		}

		return true;
	}

	/**
	 * Credit Card
	 *
	 * @param string $ccnum Card number.
	 * @return string|bool Card number or false.
	 */
	public static function cc(&$ccnum='') {
		// Digits only.
		cast::string($ccnum, true);
		$ccnum = preg_replace('/[^\d]/', '', $ccnum);
		$str = $ccnum;

		// Different cards have different length requirements.
		switch (\blobfolio\common\mb::substr($ccnum, 0, 1)) {
			// Amex.
			case 3:
				if (\blobfolio\common\mb::strlen($ccnum) !== 15 || !preg_match('/3[47]/', $ccnum)) {
					$ccnum = false;
					return false;
				}
				break;
			// Visa.
			case 4:
				if (!in_array(\blobfolio\common\mb::strlen($ccnum), array(13,16), true)) {
					$ccnum = false;
					return false;
				}
				break;
			// MC.
			case 5:
				if (\blobfolio\common\mb::strlen($ccnum) !== 16 || !preg_match('/5[1-5]/', $ccnum)) {
					$ccnum = false;
					return false;
				}
				break;
			// Disc.
			case 6:
				if (\blobfolio\common\mb::strlen($ccnum) !== 16 || intval(\blobfolio\common\mb::substr($ccnum, 0, 4)) !== 6011) {
					$ccnum = false;
					return false;
				}
				break;
			// There is nothing else...
			default:
				$ccnum = false;
				return false;
		}

		// MOD10 checks.
		$dig = \blobfolio\common\mb::str_split($ccnum);
		$numdig = count($dig);
		$j = 0;
		for ($i = ($numdig - 2); $i >= 0; $i -= 2) {
			$dbl[$j] = $dig[$i] * 2;
			$j++;
		}
		$dblsz = count($dbl);
		$validate = 0;
		for ($i = 0; $i < $dblsz; $i++) {
			$add = \blobfolio\common\mb::str_split($dbl[$i]);
			for ($j = 0; $j < count($add); $j++) {
				$validate += $add[$j];
			}
			$add = '';
		}
		for ($i = ($numdig - 1); $i >= 0; $i -= 2) {
			$validate += $dig[$i];
		}

		if (intval(\blobfolio\common\mb::substr($validate, -1, 1)) === 0) {
			$ccnum = $str;
		}
		else {
			$ccnum = false;
		}

		return true;
	}

	/**
	 * Control Characters
	 *
	 * @param string $str String.
	 * @return bool True.
	 */
	public static function control_characters(&$str='') {
		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::control_characters($str[$k]);
			}
		}
		else {
			cast::string($str);
			$str = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $str);
			$str = preg_replace('/\\\\+0+/', '', $str);
		}

		return true;
	}

	/**
	 * Country
	 *
	 * @param string $str Country.
	 * @return string ISO country code.
	 */
	public static function country(&$str='') {
		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::country($str[$k]);
			}
		}
		else {
			cast::string($str);
			static::whitespace($str);
			mb::strtoupper($str);
			if (!isset(\blobfolio\common\constants::COUNTRIES[$str])) {
				// Maybe a name?
				$found = false;
				foreach (\blobfolio\common\constants::COUNTRIES as $k=>$v) {
					if (\blobfolio\common\mb::strtoupper($v['name']) === $str) {
						$str = $k;
						$found = true;
						break;
					}
				}
				if (!$found) {
					$str = '';
				}
			}
		}

		return true;
	}

	/**
	 * CSV Cell Data
	 *
	 * @param string $str String.
	 * @return string String.
	 */
	public static function csv(&$str='') {
		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::csv($str[$k]);
			}
		}
		else {
			cast::string($str);
			static::quotes($str);
			static::whitespace($str);

			// Strip existing double quotes.
			while (false !== \blobfolio\common\mb::strpos($str, '""')) {
				$str = str_replace('""', '"', $str);
			}

			// Double quotes.
			$str = str_replace('"', '""', $str);
		}

		return true;
	}

	/**
	 * Datetime
	 *
	 * @param string|int $str Date or timestamp.
	 * @return string Date.
	 */
	public static function datetime(&$str='') {
		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::datetime($str[$k]);
			}
		}
		else {
			if (is_int($str)) {
				$str = date('Y-m-d H:i:s', $str);
			}

			cast::string($str);

			if (
				!\blobfolio\common\mb::strlen($str) ||
				\blobfolio\common\mb::substr($str, 0, 10) === '0000-00-00' ||
				false === $str = strtotime($str)
			) {
				$str = '0000-00-00 00:00:00';
			}
			else {
				$str = date('Y-m-d H:i:s', $str);
			}
		}

		return true;
	}

	/**
	 * Date
	 *
	 * @param string|int $str Date or timestamp.
	 * @return string Date.
	 */
	public static function date(&$str='') {
		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::date($str[$k]);
			}
		}
		else {
			static::datetime($str);
			$str = \blobfolio\common\mb::substr($str, 0, 10);
		}

		return true;
	}

	/**
	 * Domain Name.
	 *
	 * This locates the domain name portion of a URL,
	 * removes leading "www." subdomains, and ignores
	 * IP addresses.
	 *
	 * @param string $str Domain.
	 * @param bool $unicode Unicode.
	 * @return string Domain.
	 */
	public static function domain(&$str='', bool $unicode=false) {
		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::domain($str[$k]);
			}
		}
		else {
			$host = new \blobfolio\domain\domain($str);
			if ($host->is_fqdn() && !$host->is_ip()) {
				$str = $host->get_host($unicode);

				$subdomain = $host->get_subdomain();
				if (!is_null($subdomain)) {
					if ('www' === $subdomain || 'www.' === \blobfolio\common\mb::substr($subdomain, 0, 4)) {
						$str = preg_replace('/^www\./u', '', $str);
					}
				}
			}
			else {
				$str = '';
				return false;
			}
		}

		return true;
	}

	/**
	 * Email
	 *
	 * Converts the email to lowercase, strips
	 * invalid characters, quotes, and apostrophes.
	 *
	 * @param string $str Email.
	 * @return string Email.
	 */
	public static function email(&$str=null) {
		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::email($str[$k]);
			}
		}
		else {
			cast::string($str);
			static::quotes($str);
			mb::strtolower($str);
			$str = str_replace(array('"',"'"), '', filter_var($str, FILTER_SANITIZE_EMAIL));

			if (
				!filter_var($str, FILTER_VALIDATE_EMAIL) ||
				!preg_match('/^.+\@.+\..+$/', $str)
			) {
				$str = '';
			}
		}

		return true;
	}

	/**
	 * File Extension
	 *
	 * @param string $str Extension.
	 * @return string Extension.
	 */
	public static function file_extension(&$str='') {
		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::file_extension($str[$k]);
			}
		}
		else {
			cast::string($str);
			mb::strtolower($str);
			static::whitespace($str);
			$str = ltrim($str, '*. ');
			$str = preg_replace('/\s/u', '', $str);
		}

		return true;
	}

	/**
	 * HTML
	 *
	 * @param string $str HTML.
	 * @return string HTML.
	 */
	public static function html(&$str=null) {
		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::html($str[$k]);
			}
		}
		else {
			cast::string($str);
			$str = htmlspecialchars($str, ENT_QUOTES | ENT_HTML5, 'UTF-8');
		}

		return true;
	}

	/**
	 * Hostname
	 *
	 * @param string $domain Hostname.
	 * @param bool $www Keep leading www.
	 * @param bool $unicode Unicode.
	 * @return string|bool Hostname or false.
	 */
	public static function hostname(string &$domain, bool $www=false, bool $unicode=false) {
		$host = new \blobfolio\domain\domain($domain);
		if (!$host->is_valid()) {
			$domain = false;
			return false;
		}

		$domain = $host->get_host($unicode);

		// Strip leading www., but only if it is a subdomain.
		if (!$www) {
			$subdomain = $host->get_subdomain();
			if (!is_null($subdomain)) {
				if ('www' === $subdomain || 'www.' === \blobfolio\common\mb::substr($subdomain, 0, 4)) {
					$domain = preg_replace('/^www\./u', '', $domain);
				}
			}
		}

		return true;
	}

	/**
	 * IP Address
	 *
	 * @param string $str IP.
	 * @param bool $restricted Allow private/restricted values.
	 * @return string IP.
	 */
	public static function ip(&$str='', bool $restricted=false) {
		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::ip($str[$k], $restricted);
			}
		}
		else {
			cast::string($str);
			mb::strtolower($str);

			// Start by getting rid of obviously bad data.
			$str = preg_replace('/[^\d\.\:a-f]/', '', $str);

			// Try to compact IPv6.
			if (filter_var($str, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
				$str = inet_ntop(inet_pton($str));
			}
			elseif (!filter_var($str, FILTER_VALIDATE_IP)) {
				$str = '';
			}

			if (
				!$restricted &&
				\blobfolio\common\mb::strlen($str) &&
				!filter_var($str, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)
			) {
				$str = '';
			}
		}

		return true;
	}

	/**
	 * IRI Value
	 *
	 * @param string $str IRI value.
	 * @param array $protocols Allowed protocols.
	 * @param array $domains Allowed domains.
	 * @return bool True.
	 */
	public static function iri_value(&$str='', $protocols=null, $domains=null) {
		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::iri_value($str[$k]);
			}
		}
		else {
			cast::string($str);
			static::attribute_value($str);

			cast::array($protocols);
			$allowed_protocols = array_merge(\blobfolio\common\constants::SVG_WHITELIST_PROTOCOLS, $protocols);
			mb::strtolower($allowed_protocols);
			$allowed_protocols = array_map('trim', $allowed_protocols);
			$allowed_protocols = array_filter($allowed_protocols, 'strlen');
			$allowed_protocols = array_unique($allowed_protocols);
			sort($allowed_protocols);

			cast::array($domains);
			$allowed_domains = array_merge(\blobfolio\common\constants::SVG_WHITELIST_DOMAINS, $domains);
			static::domain($allowed_domains);
			$allowed_domains = array_filter($allowed_domains, 'strlen');
			$allowed_domains = array_unique($allowed_domains);
			sort($allowed_domains);

			// Assign a protocol.
			$str = preg_replace('/^\/\//', 'https://', $str);

			// Remove newlines.
			$str = preg_replace('/\v/u', '', $str);

			// Check protocols.
			$test = preg_replace('/\s/', '', $str);
			mb::strtolower($test);
			if (false !== \blobfolio\common\mb::strpos($test, ':')) {
				$test = explode(':', $test);
				if (!in_array($test[0], $allowed_protocols, true)) {
					$str = '';
					return true;
				}
			}

			// Is this at least a URLish thing?
			if (filter_var($str, FILTER_SANITIZE_URL) !== $str) {
				$str = '';
				return true;
			}

			// Check the domain, if applicable.
			if (preg_match('/^[\w\d]+:\/\//i', $str)) {
				$domain = \blobfolio\common\sanitize::domain($str);
				if (strlen($domain) && !in_array($domain, $allowed_domains, true)) {
					$str = '';
				}
			}
		}

		return true;
	}

	/**
	 * JS Variable
	 *
	 * @param string $str String.
	 * @param string $quote Quote type.
	 * @return string String.
	 */
	public static function js(&$str='', $quote="'") {
		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::js($str[$k], $quote);
			}
		}
		else {
			cast::string($str);
			sanitize::quotes($str);
			sanitize::whitespace($str);

			if ("'" === $quote) {
				$str = str_replace("'", "\'", $str);
			}
			elseif ('"' === $quote) {
				$str = str_replace('"', '\"', $str);
			}
		}

		return true;
	}

	/**
	 * IANA MIME Type
	 *
	 * @param string $str MIME.
	 * @return string MIME.
	 */
	public static function mime(&$str='') {
		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::mime($str[$k]);
			}
		}
		else {
			cast::string($str);
			mb::strtolower($str);
			$str = preg_replace('/[^-+*.a-z0-9\/]/', '', $str);
		}

		return true;
	}

	/**
	 * (Person's) Name
	 *
	 * A bit of a fool's goal, but this will attempt to
	 * strip out obviously bad data and convert to title
	 * casing.
	 *
	 * @param string $str Name.
	 * @return string Name.
	 */
	public static function name(&$str='') {
		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::name($str[$k]);
			}
		}
		else {
			cast::string($str);
			static::quotes($str);
			static::whitespace($str);
			$str = preg_replace('/[^\p{L}\p{Zs}\p{Pd}\d\'\"\,\.]/u', '', $str);
			static::whitespace($str);
			mb::ucwords($str);
		}

		return true;
	}

	/**
	 * Password
	 *
	 * This simply removes excess whitespace and
	 * non-printable characters, which are likely
	 * only present because of user error.
	 *
	 * @param string $str Password.
	 * @return string Password.
	 */
	public static function password(&$str='') {
		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::password($str[$k]);
			}
		}
		else {
			cast::string($str);
			static::printable($str);
			static::whitespace($str);
		}

		return true;
	}

	/**
	 * Printable
	 *
	 * Remove non-printable characters (except spaces).
	 *
	 * @param string $str String.
	 * @return string String.
	 */
	public static function printable(&$str='') {
		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::printable($str[$k]);
			}
		}
		else {
			cast::string($str);
			$str = preg_replace('/[^[:print:]]/u', '', $str);
		}

		return true;
	}

	/**
	 * Canadian Province
	 *
	 * @param string $str Province.
	 * @return string Province.
	 */
	public static function province(&$str='') {
		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::province($str[$k]);
			}
		}
		else {
			cast::string($str);
			static::whitespace($str);
			mb::strtoupper($str);

			if (!isset(\blobfolio\common\constants::PROVINCES[$str])) {
				if (false === $str = array_search($str, array_map('\blobfolio\common\mb::strtoupper', \blobfolio\common\constants::PROVINCES), true)) {
					$str = '';
				}
			}
		}

		return true;
	}

	/**
	 * Quotes
	 *
	 * Replace those damn curly quotes with the straight
	 * ones Athena intended!
	 *
	 * @param string $str String.
	 * @return string String.
	 */
	public static function quotes(&$str='') {
		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::quotes($str[$k]);
			}
		}
		else {
			cast::string($str);
			$from = array_keys(\blobfolio\common\constants::QUOTE_CHARS);
			$to = array_values(\blobfolio\common\constants::QUOTE_CHARS);
			$str = str_replace($from, $to, $str);
		}

		return true;
	}

	/**
	 * US State/Territory
	 *
	 * @param string $str State.
	 * @return string State.
	 */
	public static function state(&$str='') {
		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::state($str[$k]);
			}
		}
		else {
			cast::string($str);
			static::whitespace($str);
			mb::strtoupper($str);

			if (!isset(\blobfolio\common\constants::STATES[$str])) {
				if (false === $str = array_search($str, array_map('\blobfolio\common\mb::strtoupper', \blobfolio\common\constants::STATES), true)) {
					$str = '';
				}
			}
		}

		return true;
	}

	/**
	 * SVG
	 *
	 * @param string $str SVG code.
	 * @param array $tags Additional whitelist tags.
	 * @param array $attr Additional whitelist attributes.
	 * @param array $protocols Additional whitelist protocols.
	 * @param array $domains Additional whitelist domains.
	 * @return string SVG code.
	 */
	public static function svg(&$str='', $tags=null, $attr=null, $protocols=null, $domains=null) {
		// First, sanitize and build out function arguments!
		cast::string($str, true);
		cast::array($tags);
		cast::array($attr);
		cast::array($protocols);
		cast::array($domains);

		$allowed_tags = array_merge(\blobfolio\common\constants::SVG_WHITELIST_TAGS, $tags);
		mb::strtolower($allowed_tags);
		$allowed_tags = array_map('trim', $allowed_tags);
		$allowed_tags = array_filter($allowed_tags, 'strlen');
		$allowed_tags = array_unique($allowed_tags);
		sort($allowed_tags);

		$allowed_attributes = array_merge(\blobfolio\common\constants::SVG_WHITELIST_ATTR, $attr);
		mb::strtolower($allowed_attributes);
		$allowed_attributes = array_map('trim', $allowed_attributes);
		$allowed_attributes = array_filter($allowed_attributes, 'strlen');
		$allowed_attributes = array_unique($allowed_attributes);
		sort($allowed_attributes);

		$allowed_protocols = array_merge(\blobfolio\common\constants::SVG_WHITELIST_PROTOCOLS, $protocols);
		mb::strtolower($allowed_protocols);
		$allowed_protocols = array_map('trim', $allowed_protocols);
		$allowed_protocols = array_filter($allowed_protocols, 'strlen');
		$allowed_protocols = array_unique($allowed_protocols);
		sort($allowed_protocols);

		$allowed_domains = array_merge(\blobfolio\common\constants::SVG_WHITELIST_DOMAINS, $domains);
		static::domain($allowed_domains);
		$allowed_domains = array_filter($allowed_domains, 'strlen');
		$allowed_domains = array_unique($allowed_domains);
		sort($allowed_domains);

		$iri_attributes = \blobfolio\common\constants::SVG_IRI_ATTRIBUTES;

		// Load the SVG!
		$dom = \blobfolio\common\dom::load_svg($str);
		$svg = $dom->getElementsByTagName('svg');
		if (!$svg->length) {
			$str = '';
			return false;
		}
		$xpath = new \DOMXPath($dom);

		// Validate tags.
		$tags = $dom->getElementsByTagName('*');
		for ($x = $tags->length - 1; $x >= 0; $x--) {
			$tag = $tags->item($x);
			$tag_name = \blobfolio\common\mb::strtolower($tag->tagName);

			// The tag might be namespaced (ns:tag). We'll allow it if
			// the tag is allowed.
			if (
				false !== \blobfolio\common\mb::strpos($tag_name, ':') &&
				!in_array($tag_name, $allowed_tags, true)
			) {
				$tag_name = explode(':', $tag_name);
				$tag_name = $tag_name[1];
			}

			// Bad tag: not whitelisted.
			if (!in_array($tag_name, $allowed_tags, true)) {
				\blobfolio\common\dom::remove_node($tag);
				continue;
			}

			// If this is a <style> tag, we need to make sure all
			// entities are decoded. Thanks a lot, XML!
			if ('style' === $tag_name) {
				$style = strip_tags(\blobfolio\common\sanitize::attribute_value($tag->textContent));
				$tag->textContent = $style;
			}

			// Use XPath for attributes, as $tag->attributes will skip
			// anything namespaced. Note: We aren't focusing on
			// actual Namespaces here, that comes later.
			$attributes = $xpath->query('.//@*', $tag);
			for ($y = $attributes->length - 1; $y >= 0; $y--) {
				$attribute = $attributes->item($y);

				$attribute_name = \blobfolio\common\mb::strtolower($attribute->nodeName);

				// Could be namespaced.
				if (
					!in_array($attribute_name, $allowed_attributes, true) &&
					false !== ($start = \blobfolio\common\mb::strpos($attribute_name, ':'))
				) {
					$attribute_name = \blobfolio\common\mb::substr($attribute_name, $start + 1);
				}

				// Bad attribute: not whitelisted.
				// data-* is implicitly whitelisted.
				if (
					!preg_match('/^data\-/', $attribute_name) &&
					!in_array($attribute_name, $allowed_attributes, true)
				) {
					$tag->removeAttribute($attribute->nodeName);
					continue;
				}

				// Validate values.
				$attribute_value = \blobfolio\common\sanitize::attribute_value($attribute->value);

				// Validate protocols.
				// IRI attributes get the full treatment.
				$iri = false;
				if (in_array($attribute_name, $iri_attributes, true)) {
					$iri = true;
					static::iri_value($attribute_value, $allowed_protocols, $allowed_domains);
				}
				// For others, we are specifically interested in removing scripty bits.
				elseif (preg_match('/(?:\w+script):/xi', $attribute_value)) {
					$attribute_value = '';
				}

				// Update it.
				if ($attribute_value !== $attribute->value) {
					if ($iri) {
						$tag->removeAttribute($attribute->nodeName);
					} else {
						$tag->setAttribute($attribute->nodeName, $attribute_value);
					}
				}
			}
		} // Each tag.

		// Once more through the tags to find namespaces.
		$tags = $dom->getElementsByTagName('*');
		for ($x = 0; $x < $tags->length; $x++) {
			$tag = $tags->item($x);
			$nodes = $xpath->query('namespace::*', $tag);
			for ($y = 0; $y < $nodes->length; $y++) {
				$node = $nodes->item($y);

				$node_name = \blobfolio\common\mb::strtolower($node->nodeName);

				// Not xmlns?
				if (!preg_match('/^xmlns:/', $node_name)) {
					\blobfolio\common\dom::remove_namespace($dom, $node->localName);
					continue;
				}

				// Validate values.
				$node_value = \blobfolio\common\sanitize::iri_value($node->nodeValue, $allowed_protocols, $allowed_domains);

				// Remove invalid.
				if (!strlen($node_value)) {
					\blobfolio\common\dom::remove_namespace($dom, $node->localName);
				}
			}
		}

		// Back to string!
		$svg = \blobfolio\common\dom::save_svg($dom);

		// One more task, sanitize CSS values (e.g. foo="url(...)").
		$svg = preg_replace_callback(
			'/url\s*\((.*)\s*\)/Ui',
			function($match) use($allowed_protocols, $allowed_domains) {
				$str = \blobfolio\common\sanitize::attribute_value($match[1]);

				// Strip quotes.
				$str = ltrim($str, "'\"");
				$str = rtrim($str, "'\"");

				\blobfolio\common\ref\sanitize::iri_value($str, $allowed_protocols, $allowed_domains);

				if (strlen($str)) {
					return "url('$str')";
				}

				return 'none';
			},
			$svg
		);

		$str = $svg;

		return true;
	}

	/**
	 * Timezone
	 *
	 * @param string $str Timezone.
	 * @return string Timezone or UTC on failure.
	 */
	public static function timezone(&$str='') {
		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::timezone($str[$k]);
			}
		}
		else {
			try {
				mb::strtoupper($str);
				static::whitespace($str);

				$timezones = \DateTimeZone::listIdentifiers();

				$found = false;
				foreach ($timezones as $timezone) {
					if (\blobfolio\common\mb::strtoupper($timezone) === $str) {
						$str = $timezone;
						$found = true;
						break;
					}
				}

				if (!$found) {
					$str = 'UTC';
				}
			} catch (\Throwable $e) {
				$str = 'UTC';
			}
		}

		return true;
	}

	/**
	 * Confine a Value to a Range
	 *
	 * @param mixed $value Value.
	 * @param mixed $min Min.
	 * @param mixed $max Max.
	 * @return mixed Value.
	 */
	public static function to_range(&$value, $min=null, $max=null) {

		// Make sure min/max are in the right order.
		if (
			!is_null($min) &&
			!is_null($max) &&
			$min > $max
		) {
			\blobfolio\common\data::switcheroo($min, $max);
		}

		// Recursive.
		if (is_array($value)) {
			foreach ($value as $k=>$v) {
				static::to_range($v, $min, $max);
			}
		}
		else {
			$original = $value;

			try {
				if (!is_null($min) && $value < $min) {
					$value = $min;
				}
				if (!is_null($max) && $value > $max) {
					$value = $max;
				}
			} catch (\Throwable $e) {
				$value = $original;
			}
		}

		return true;
	}

	/**
	 * URL
	 *
	 * Validate URLishness and convert // schemas.
	 *
	 * @param string $str URL.
	 * @return string URL.
	 */
	public static function url(&$str='') {
		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::url($str[$k]);
			}
		}
		else {
			cast::string($str);
			$str = filter_var($str, FILTER_SANITIZE_URL);
			if (preg_match('/^\/\//', $str)) {
				$str = "https:$str";
			}
			if (!filter_var($str, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED | FILTER_FLAG_HOST_REQUIRED)) {
				$str = '';
			}
		}

		return true;
	}

	/**
	 * UTF-8
	 *
	 * Ensure string contains valid UTF-8 encoding.
	 *
	 * @param string $str String.
	 * @return string String.
	 */
	public static function utf8(&$str='') {
		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::utf8($str[$k]);
			}
		}
		elseif (!is_numeric($str) && !is_bool($str)) {
			try {
				$str = (string) $str;
			} catch (\Throwable $e) {
				$str = '';
			}

			$str = \ForceUTF8\Encoding::toUTF8($str);
			$str = (1 === @preg_match('/^./us', $str)) ? $str : '';
		}

		return true;
	}

	/**
	 * Whitespace
	 *
	 * Trim edges, replace all consecutive horizontal whitespace
	 * with a single space, and constrict consecutive newlines.
	 *
	 * @param string $str String.
	 * @param int $newlines Consecutive newlines allowed.
	 * @return string String.
	 */
	public static function whitespace(&$str='', int $newlines=0) {
		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::whitespace($str[$k], $newlines);
			}
		}
		else {
			cast::string($str);
			static::to_range($newlines, 0);

			if (!$newlines) {
				$str = preg_replace('/\s+/u', ' ', $str);
				$str = preg_replace('/^\s+/u', '', $str);
				$str = preg_replace('/\s+$/u', '', $str);
				return true;
			}

			// Sanitize newlines.
			$str = preg_replace('/^\s+/u', '', $str);
			$str = preg_replace('/\s+$/u', '', $str);
			$str = str_replace("\r\n", "\n", $str);
			$str = preg_replace('/\v/u', "\n", $str);

			// Now go through line by line.
			$str = explode("\n", $str);
			static::whitespace($str);
			$str = implode("\n", $str);

			$str = preg_replace('/\n{' . ($newlines + 1) . ',}/', str_repeat("\n", $newlines), $str);

			$str = trim($str);
		}

		return true;
	}

	/**
	 * Whitespace Multiline
	 *
	 * @param string $str String.
	 * @param int $newlines Consecutive newlines allowed.
	 * @return string String.
	 */
	public static function whitespace_multiline(&$str='', int $newlines=1) {
		static::to_range($newlines, 1);

		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::whitespace_multiline($str[$k], $newlines);
			}
		}
		else {
			static::whitespace($str, $newlines);
		}

		return true;
	}

	/**
	 * US ZIP5
	 *
	 * @param string $str ZIP Code.
	 * @return string ZIP Code.
	 */
	public static function zip5(&$str='') {
		if (is_array($str)) {
			foreach ($str as $k=>$v) {
				static::zip5($str[$k]);
			}
		}
		else {
			cast::string($zip5);
			$str = preg_replace('/[^\d]/', '', $str);

			if (\blobfolio\common\mb::strlen($str) < 5) {
				$str = sprintf('%05d', $str);
			}
			elseif (\blobfolio\common\mb::strlen($str) > 5) {
				$str = \blobfolio\common\mb::substr($str, 0, 5);
			}

			if ('00000' === $str) {
				$str = '';
			}
		}

		return true;
	}
}


