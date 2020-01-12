<?php
/**
 * Blobfolio: Domain Suffixes
 *
 * Make Domain Validation Great Again.
 *
 * @see {https://github.com/Blobfolio/blob-common}
 *
 * @package Blobfolio/Common
 * @author Blobfolio, LLC <hello@blobfolio.com>
 */

namespace Blobfolio;

use Blobfolio\Blobfolio as Shim;
use Exception;



final class Domains {
	private static $_loaded_blob_domains = false;
	private static $_suffixes;

	private $host;
	private $subdomain;
	private $domain;
	private $suffix;

	private $dns;



	/**
	 * Construct
	 *
	 * @param string $host Host.
	 * @return void Nothing.
	 */
	public function __construct(string $host) {
		$parsed = self::parseHostParts($host);
		if (false === $parsed) {
			return;
		}

		$this->host = $parsed['host'];
		$this->subdomain = $parsed['subdomain'];
		$this->domain = $parsed['domain'];
		$this->suffix = $parsed['suffix'];
	}


	/**
	 * Parse Host
	 *
	 * Try to tease the hostname out of any arbitrary string, which
	 * might be the hostname, a URL, or something else.
	 *
	 * @param string $host Host.
	 * @return string|bool Host or false.
	 */
	private static function parseHost(string $host) {
		// Try to parse it the easy way.
		$tmp = self::parseUrl($host, \PHP_URL_HOST);
		if (! empty($tmp)) {
			$host = $tmp;
		}
		// Or the hard way?
		else {
			$host = \Blobfolio\Strings::trim($host);

			// Cut off the path, if any.
			$start = \mb_strpos($host, '/', 0, 'UTF-8');
			if (false !== $start) {
				$host = \mb_substr($host, 0, $start, 'UTF-8');
			}

			// Cut off the query, if any.
			$start = \mb_strpos($host, '?', 0, 'UTF-8');
			if (false !== $start) {
				$host = \mb_substr($host, 0, $start, 'UTF-8');
			}

			// Cut off credentials, if any.
			$start = \mb_strpos($host, '@', 0, 'UTF-8');
			if (false !== $start) {
				$host = \mb_substr($host, $start + 1, null, 'UTF-8');
			}

			// Is this an IPv6 address?
			if (\filter_var($host, \FILTER_VALIDATE_IP, \FILTER_FLAG_IPV6)) {
				$host = IPs::niceIp($host, Shim::IP_RESTRICTED | Shim::IP_CONDENSE);
			}
			else {
				// Pluck an IP out of brackets.
				$start = \strpos($host, '[');
				$end = \strpos($host, ']');
				if ((0 === $start) && false !== $end) {
					$host = \mb_substr($host, 1, $end - 1, 'UTF-8');
					$host = IPs::niceIp($host, Shim::IP_RESTRICTED | Shim::IP_CONDENSE);
				}
				// Chop off the port, if any.
				else {
					$start = \mb_strpos($host, ':', 0, 'UTF-8');
					if (false !== $start) {
						$host = \mb_substr($host, 0, $start, 'UTF-8');
					}
				}
			}

			// If it is empty($or) invalid, there is nothing we can do.
			if (empty($host)) {
				return false;
			}

			// Convert to ASCII if possible.
			$host = (string) self::toAscii($host);

			// Lowercase it.
			$host = \strtolower($host);

			// Get rid of trailing periods.
			$host = \ltrim($host, '.');
			$host = \rtrim($host, '.');
		}

		// Liberate IPv6 from its walls.
		if (0 === \strpos($host, '[')) {
			$host = \str_replace(array('[', ']'), '', $host);
			$host = IPs::niceIp($host, Shim::IP_RESTRICTED | Shim::IP_CONDENSE);
		}

		// Is this an IP address? If so, we're done!
		if (\filter_var($host, \FILTER_VALIDATE_IP)) {
			return $host;
		}

		// Look for illegal characters. At this point we should
		// only have nice and safe ASCII.
		if (\preg_match('/[^a-z\d\-\.]/u', $host)) {
			return false;
		}

		$parts = (array) \explode('.', $host);
		foreach ($parts as $v) {
			// Gotta have length, and can't start or end with a dash.
			if (
				empty($v) ||
				(0 === \strpos($v, '-')) ||
				('-' === \substr($v, -1))
			) {
				return false;
			}
		}

		return \implode('.', $parts);
	}

	/**
	 * Parse Host Parts
	 *
	 * Break a host down into subdomain, domain, and
	 * suffix parts.
	 *
	 * @param string $host Host.
	 * @return array|bool Parts or false.
	 */
	private static function parseHostParts(string $host) {
		// Tease out the hostname.
		$host = self::parseHost($host);
		if (empty($host)) {
			return false;
		}

		$out = array(
			'host'=>null,
			'subdomain'=>null,
			'domain'=>null,
			'suffix'=>null,
		);

		// If this is an IP, we don't have to do anything else.
		if (\filter_var($host, \FILTER_VALIDATE_IP)) {
			$out['host'] = $host;
			$out['domain'] = $host;
			return $out;
		}

		// Make sure the data is loaded.
		self::loadSuffixes();

		$suffixes = (array) self::$_suffixes;
		$suffix = array();
		$parts = (array) \explode('.', $host);
		$parts = \array_reverse($parts);

		foreach ($parts as $k=>$part) {
			// Override rule.
			if (isset($suffixes[$part]['!'])) {
				break;
			}

			// A match.
			if (isset($suffixes[$part])) {
				\array_unshift($suffix, $part);
				$suffixes = $suffixes[$part];
				unset($parts[$k]);
				continue;
			}

			// A wildcard.
			if (isset($suffixes['*'])) {
				\array_unshift($suffix, $part);
				$suffixes = $suffixes['*'];
				unset($parts[$k]);
				continue;
			}

			// We're done.
			break;
		}

		// The suffix can't be all there is.
		if (! \count($parts)) {
			return false;
		}

		// The domain.
		$parts = \array_reverse($parts);
		$out['domain'] = \array_pop($parts);

		// The subdomain.
		if (\count($parts)) {
			$out['subdomain'] = \implode('.', $parts);
		}

		// The suffix.
		if (\count($suffix)) {
			$out['suffix'] = \implode('.', $suffix);
		}

		$out['host'] = $host;
		return $out;
	}

	/**
	 * Strip Leading WWW
	 *
	 * The www. subdomain is evil. This removes it, but only if it is
	 * part of the subdomain.
	 *
	 * @return void Nothing.
	 */
	public function stripWww() : void {
		if (empty($this->subdomain) || ! $this->isValid()) {
			return;
		}

		if (
			('www' === $this->subdomain) ||
			(0 === \strpos($this->subdomain, 'www.'))
		) {
			$this->subdomain = \preg_replace('/^www\.?/u', '', $this->subdomain);
			if (empty($this->subdomain)) {
				$this->subdomain = null;
			}

			$this->host = \preg_replace('/^www\./u', '', $this->host);
		}
	}

	/**
	 * Add Leading WWW.
	 *
	 * This is evil and you shouldn't do it, but you can.
	 *
	 * @return void Nothing.
	 */
	public function addWww() : void {
		if (
			! $this->isValid() ||
			('www' === $this->subdomain) ||
			(! empty($this->subdomain) && (0 === \strpos($this->subdomain, 'www.'))) ||
			$this->isIp(true)
		) {
			return;
		}

		if (empty($this->subdomain)) {
			$this->subdomain = 'www';
		}
		else {
			$this->subdomain = 'www.' . $this->subdomain;
		}

		$this->host = 'www.' . $this->host;
	}



	// -----------------------------------------------------------------
	// Results
	// -----------------------------------------------------------------

	/**
	 * Is Valid
	 *
	 * @param bool $dns Has DNS.
	 * @return bool True/false.
	 */
	public function isValid(bool $dns=false) : bool {
		return (! empty($this->host) && (! $dns || $this->hasDns()));
	}

	/**
	 * Is Fully Qualified Domain Name
	 *
	 * @return bool True/false.
	 */
	public function isFqdn() : bool {
		return (
			$this->isValid() &&
			(! empty($this->suffix) || $this->isIp(false))
		);
	}

	/**
	 * Is IP
	 *
	 * @param bool $restricted Allow restricted.
	 * @return bool True/false.
	 */
	public function isIp(bool $restricted=true) : bool {
		if (! $this->isValid()) {
			return false;
		}

		if ($restricted) {
			return !! \filter_var($this->host, \FILTER_VALIDATE_IP);
		}

		return !! \filter_var(
			$this->host,
			\FILTER_VALIDATE_IP,
			\FILTER_FLAG_NO_PRIV_RANGE | \FILTER_FLAG_NO_RES_RANGE
		);
	}

	/**
	 * Has DNS
	 *
	 * @return bool True/false.
	 */
	public function hasDns() : bool {
		// Have to set it first.
		if (null === $this->dns) {
			if (! $this->isFqdn()) {
				$this->dns = false;
			}
			elseif ($this->isIp()) {
				$this->dns = $this->isIp(false);
			}
			else {
				$this->dns = !! \filter_var(
					\gethostbyname($this->host . '.'),
					\FILTER_VALIDATE_IP,
					\FILTER_FLAG_NO_PRIV_RANGE | \FILTER_FLAG_NO_RES_RANGE
				);
			}
		}

		return $this->dns;
	}

	/**
	 * Is ASCII
	 *
	 * @return bool True/false.
	 */
	public function isAscii() : bool {
		if (! $this->isValid()) {
			return false;
		}

		return ! $this->isUnicode();
	}

	/**
	 * Is Unicode
	 *
	 * @return bool True/false.
	 */
	public function isUnicode() : bool {
		if (! $this->isValid() || $this->isIp()) {
			return false;
		}

		return (self::toUnicode($this->host) !== $this->host);
	}



	// -----------------------------------------------------------------
	// Getters
	// -----------------------------------------------------------------

	/**
	 * Cast to String
	 *
	 * @return string Phone number.
	 */
	public function __toString() {
		if ($this->isValid()) {
			return $this->host;
		}

		return '';
	}

	/**
	 * Get Data
	 *
	 * @param int $flags Flags.
	 * @return array|bool Host data or false.
	 */
	public function getData(int $flags=0) {
		if (! $this->isValid()) {
			return false;
		}

		return array(
			'host'=>$this->getHost($flags),
			'subdomain'=>$this->getSubdomain($flags),
			'domain'=>$this->getDomain($flags),
			'suffix'=>$this->getSuffix($flags),
		);
	}

	/**
	 * Get Host
	 *
	 * @param int $flags Flags.
	 * @return string|null Host.
	 */
	public function getHost(int $flags=0) : ?string {
		$unicode = !! ($flags & Shim::UNICODE);
		if ($unicode && ! empty($this->host)) {
			return self::toUnicode($this->host);
		}

		return $this->host;
	}

	/**
	 * Get Subdomain
	 *
	 * @param int $flags Flags.
	 * @return string|null Subdomain.
	 */
	public function getSubdomain(int $flags=0) : ?string {
		$unicode = !! ($flags & Shim::UNICODE);
		if ($unicode && ! empty($this->subdomain)) {
			return self::toUnicode($this->subdomain);
		}

		return $this->subdomain;
	}

	/**
	 * Get Domain
	 *
	 * @param int $flags Flags.
	 * @return string|null Domain.
	 */
	public function getDomain(int $flags=0) : ?string {
		$unicode = !! ($flags & Shim::UNICODE);
		if ($unicode && ! empty($this->domain)) {
			return self::toUnicode($this->domain);
		}

		return $this->domain;
	}

	/**
	 * Get Suffix
	 *
	 * @param int $flags Flags.
	 * @return string|null Suffix.
	 */
	public function getSuffix(int $flags=0) : ?string {
		$unicode = !! ($flags & Shim::UNICODE);
		if ($unicode && ! empty($this->suffix)) {
			return self::toUnicode($this->suffix);
		}

		return $this->suffix;
	}



	// -----------------------------------------------------------------
	// Formatting
	// -----------------------------------------------------------------

	/**
	 * Domain Name.
	 *
	 * This locates the domain name portion of a URL, removes leading
	 * "www" subdomains, and ignores IP addresses.
	 *
	 * @param string $str Domain.
	 * @param int $flags Flags.
	 * @return string Domain.
	 */
	public static function niceDomain(string $str, int $flags=0) : string {
		$host = new self($str);
		if ($host->isFqdn() && ! $host->isIp()) {
			$host->stripWww();
			return $host->getHost($flags);
		}

		return '';
	}

	/**
	 * Email
	 *
	 * Converts the email to lowercase, strips
	 * invalid characters, quotes, and apostrophes.
	 *
	 * @param string $str Email.
	 * @param int $flags Flags.
	 * @return string Email.
	 */
	public static function niceEmail(string $str, int $flags=0) : string {
		$str = (string) \Blobfolio\Strings::quotes($str, ($flags & Shim::TRUSTED));
		$str = \Blobfolio\Strings::toLower($str, Shim::TRUSTED);

		// Strip comments.
		$str = \preg_replace('/\([^)]*\)/u', '', $str);

		// Early bail: wrong number of "@".
		if (1 !== \substr_count($str, '@')) {
			return '';
		}

		// For backward-compatibility, strip quotes now.
		$str = \str_replace(array("'", '"'), '', $str);

		// Sanitize by part.
		$parts = (array) \explode('@', $str);

		// Sanitize local part.
		$parts[0] = \preg_replace(
			'/[^\.a-z0-9\!#$%&\*\+\-\=\?_~]/u',
			'',
			$parts[0]
		);
		$parts[0] = \ltrim($parts[0], '.');
		$parts[0] = \rtrim($parts[0], '.');

		// Another early bail, nothing local left.
		if (empty($parts[0])) {
			return '';
		}

		// Sanitize host.
		$host = new self($parts[1]);
		if (! $host->isValid() || ! $host->isFqdn() || $host->isIp()) {
			return '';
		}
		$parts[1] = $host->getHost();

		return \implode('@', $parts);
	}

	/**
	 * Hostname
	 *
	 * @param string $str Hostname.
	 * @param int $flags Flags.
	 * @return string|bool Host or false.
	 */
	public static function niceHost(string $str, int $flags=1) {
		$host = new self($str);
		if (! $host->isValid()) {
			return false;
		}

		$stripWww = !! ($flags & Shim::HOST_STRIP_WWW);
		if ($stripWww) {
			$host->stripWww();
		}

		return $host->getHost(($flags & Shim::UNICODE));
	}

	/**
	 * URL
	 *
	 * Validate URLishness and convert // schemas.
	 *
	 * @param string $str URL.
	 * @return string URL.
	 */
	public static function niceUrl(string $str) : string {
		$tmp = (array) self::parseUrl($str);

		// Validate the host, and ASCIIfy international bits
		// to keep PHP happy.
		if (! isset($tmp['host']) || empty($tmp['host'])) {
			return '';
		}

		$tmp['host'] = new self($tmp['host']);
		if (! $tmp['host']->isValid()) {
			return '';
		}
		$tmp['host'] = $tmp['host']->getHost();

		// Schemes can be lowercase.
		if (isset($tmp['scheme'])) {
			$tmp['scheme'] = \strtolower($tmp['scheme']);
		}

		// Put it back together.
		$str = (string) self::unparseUrl($tmp);

		$str = \filter_var($str, \FILTER_SANITIZE_URL);
		if (! \filter_var(
			$str,
			\FILTER_VALIDATE_URL
		)) {
			return '';
		}

		return $str;
	}



	// -----------------------------------------------------------------
	// Other Helpers
	// -----------------------------------------------------------------

	/**
	 * Parse URL
	 *
	 * @see {http://php.net/manual/en/function.parse-url.php#114817}
	 * @see {https://github.com/jeremykendall/php-domain-parser/}
	 *
	 * @param string $url URL.
	 * @param int $component Component.
	 * @return mixed Array, Component, or Null.
	 */
	public static function parseUrl(string $url, int $component = -1) {
		$url = \Blobfolio\Strings::trim($url);

		// Before we start, let's fix scheme-agnostic URLs.
		$url = \preg_replace('#^:?//#', 'https://', $url);

		// If an IPv6 address is passed on its own, we
		// need to shove it in brackets.
		if (\filter_var($url, \FILTER_VALIDATE_IP, \FILTER_FLAG_IPV6)) {
			$url = '[' . $url . ']';
		}

		// The trick is to urlencode (most) parts before passing
		// them to the real parse_url().
		$encoded = (string) \preg_replace_callback(
			'%([a-zA-Z][a-zA-Z0-9+\-.]*)?(:?//)?([^:/@?&=#\[\]]+)%usD',
			array(static::class, '_parseUrlCallback'),
			$url
		);

		// Before getting the real answer, make sure
		// there is a scheme, otherwise PHP will assume
		// all there is is a path, which is stupid.
		if (\PHP_URL_SCHEME !== $component) {
			$test = \parse_url($encoded, \PHP_URL_SCHEME);
			if (empty($test)) {
				$encoded = 'blobfolio://' . $encoded;
			}
		}

		$parts = \parse_url($encoded, $component);

		// And now decode what we've been giving. Let's also take a
		// moment to translate Unicode hosts to ASCII.
		if (('string' === \gettype($parts)) && (\PHP_URL_SCHEME !== $component)) {
			$parts = \str_replace(' ', '+', \urldecode($parts));

			if (\PHP_URL_HOST === $component) {
				// Fix Unicode.
				$parts = (string) self::toAscii($parts);

				// Lowercase it.
				$parts = \strtolower($parts);

				// Get rid of trailing periods.
				$parts = \ltrim($parts, '.');
				$parts = \rtrim($parts, '.');

				// Standardize IPv6 formatting.
				if (0 === \strpos($parts, '[')) {
					$parts = \str_replace(array('[', ']'), '', $parts);
					$parts = IPs::niceIp($parts, Shim::IP_RESTRICTED | Shim::IP_CONDENSE);
					$parts = '[' . $parts . ']';
				}
			}
		}
		elseif ('array' === \gettype($parts)) {
			foreach ($parts as $k=>$v) {
				if ('string' !== \gettype($v)) {
					continue;
				}

				if ('scheme' !== $k) {
					$parts[$k] = \str_replace(' ', '+', \urldecode($v));
				}
				// Remove our pretend scheme.
				elseif ('blobfolio' === $v) {
					unset($parts[$k]);
					continue;
				}

				if (('host' === $k) && ('string' === \gettype($parts[$k]))) {
					// Fix Unicode.
					$parts[$k] = (string) self::toAscii($parts[$k]);

					// Lowercase it.
					$parts[$k] = \strtolower($parts[$k]);

					// Get rid of trailing periods.
					$parts[$k] = \ltrim($parts[$k], '.');
					$parts[$k] = \rtrim($parts[$k], '.');

					// Standardize IPv6 formatting.
					if (0 === \strpos($parts[$k], '[')) {
						$parts[$k] = \str_replace(array('[', ']'), '', $parts[$k]);
						$parts[$k] = IPs::niceIp($parts[$k], Shim::IP_RESTRICTED | Shim::IP_CONDENSE);
						$parts[$k] = '[' . $parts[$k] . ']';
					}
				}
			}
		}

		return $parts;
	}

	/**
	 * Parse URL Callback.
	 *
	 * @param array $matches Matches.
	 * @return string Replacement.
	 */
	private static function _parseUrlCallback(array $matches) : string {
		return $matches[1] . $matches[2] . \urldecode($matches[3]);
	}

	/**
	 * Reverse `parse_url()`
	 *
	 * @param array $parsed Parsed data.
	 * @return string URL.
	 */
	public static function unparseUrl(array $parsed) {
		$url = '';
		$url_parts = array(
			'scheme'=>'',
			'host'=>'',
			'user'=>'',
			'pass'=>'',
			'port'=>'',
			'path'=>'',
			'query'=>'',
			'fragment'=>'',
		);

		$parsed = \Blobfolio\Cast::parseArgs($parsed, $url_parts);

		// To simplify, unset anything without length.
		foreach ($parsed as $k=>$v) {
			$parsed[$k] = \Blobfolio\Strings::trim($v);
			if (empty($parsed[$k])) {
				unset($parsed[$k]);
			}
		}

		// We don't really care about validating url integrity,
		// but if nothing at all was passed then it is trash.
		if (! \count($parsed)) {
			return false;
		}

		// The scheme.
		if (isset($parsed['scheme'])) {
			$url .= $parsed['scheme'] . ':';
		}

		// The host.
		if (isset($parsed['host'])) {
			if (! empty($url)) {
				$url .= '//';
			}

			// Is this a user:pass situation?
			if (isset($parsed['user'])) {
				$url .= $parsed['user'];
				if (isset($parsed['pass'])) {
					$url .= ':' . $parsed['pass'];
				}
				$url .= '@';
			}

			// Finally the host.
			if (\filter_var($parsed['host'], \FILTER_VALIDATE_IP, \FILTER_FLAG_IPV6)) {
				$url .= '[' . $parsed['host'] . ']';
			}
			else {
				$url .= $parsed['host'];
			}

			// The port.
			if (isset($parsed['port'])) {
				$url .= ':' . $parsed['port'];
			}

			// Prepare for a path by adding a trailing slash.
			if (isset($parsed['path']) && (0 !== \strpos($parsed['path'], '/'))) {
				$url .= '/';
			}
		}

		// Add the path.
		if (isset($parsed['path'])) {
			$url .= $parsed['path'];
		}

		// Add the query.
		if (isset($parsed['query'])) {
			$url .= '?' . $parsed['query'];
		}

		// And top it off with a fragment.
		if (isset($parsed['fragment'])) {
			$url .= '#' . $parsed['fragment'];
		}

		if (empty($url)) {
			return false;
		}

		return $url;
	}

	/**
	 * Redirect
	 *
	 * Note: When using, be sure to issue an exit() as Zephir can't do
	 * that itself for some reason.
	 *
	 * @param string $to URL.
	 * @return void Nothing.
	 */
	public static function redirect(string $to) : void {
		$to = \Blobfolio\Domains::niceUrl($to);

		// Prevent stupid browser RELOAD warnings.
		// let _POST = null;
		// let _GET = null;
		// let _REQUEST = null;

		if (! \headers_sent()) {
			\header('Location: ' . $to);
		}
		else {
			echo "<script>top.location.href='" . \str_replace("'", "\'", $to) . "';</script>";
		}
	}

	/**
	 * To ASCII
	 *
	 * @param string $value Value.
	 * @return string|null Value.
	 */
	private static function toAscii(string $value) : ?string {
		if (! empty($value)) {
			$parts = (array) \explode('.', $value);
			foreach ($parts as $k=>$v) {
				$parts[$k] = (string) \idn_to_ascii($v, 0, \INTL_IDNA_VARIANT_UTS46);
			}
			return \implode('.', $parts);
		}

		return null;
	}

	/**
	 * To Unicode
	 *
	 * @param string $value Value.
	 * @return string|null Value.
	 */
	private static function toUnicode(string $value) : ?string {
		if (! empty($value)) {
			$parts = (array) \explode('.', $value);
			foreach ($parts as $k=>$v) {
				$parts[$k] = (string) \idn_to_utf8($v, 0, \INTL_IDNA_VARIANT_UTS46);
			}
			return \implode('.', $parts);
		}

		return null;
	}

	/**
	 * Delete a Cookie
	 *
	 * A companion to PHP's `setcookie()` function. It attempts to
	 * remove the cookie. The same path, etc., values should be passed
	 * as were used to first set it.
	 *
	 * @param string $name Name.
	 * @param string $path Path.
	 * @param string $domain Domain.
	 * @param bool $secure SSL only.
	 * @param bool $httponly HTTP only.
	 * @return bool True/false.
	 */
	public static function unsetcookie(string $name, string $path='', string $domain='', bool $secure=false, bool $httponly=false) : bool {

		if (! \headers_sent()) {
			\setcookie($name, false, -1, $path, $domain, $secure, $httponly);
			if (isset($_COOKIE[$name])) {
				unset($_COOKIE[$name]);
			}

			return true;
		}

		return false;
	}



	// -----------------------------------------------------------------
	// Data
	// -----------------------------------------------------------------

	/**
	 * Load Suffixes
	 *
	 * For performance reasons, this data stored in an external location
	 * and only loaded if/when needed.
	 *
	 * @return array Suffixes.
	 * @throws Exception Error.
	 */
	private static function loadSuffixes() : void {
		// Don't allow accidental repeats.
		if (true === self::$_loaded_blob_domains) {
			return;
		}

		// Gotta load it!
		$json = (string) \Blobfolio\Blobfolio::getDataDir('blob-domains.json');
		if (empty($json)) {
			throw new Exception('Missing domain suffix data.');
		}

		self::$_suffixes = \json_decode($json, true);
		if ('array' !== \gettype(self::$_suffixes)) {
			throw new Exception('Could not parse domain suffix data.');
		}

		self::$_loaded_blob_domains = true;
	}
}
