//<?php
/**
 * Blobfolio: Domain Suffixes
 *
 * Make Domain Validation Great Again.
 *
 * @see {blobfolio\common\cast}
 * @see {blobfolio\common\ref\cast}
 *
 * @package Blobfolio/Common
 * @author Blobfolio, LLC <hello@blobfolio.com>
 */

namespace Blobfolio;

final class Domains {
	private static _suffixes;

	private host;
	private subdomain;
	private domain;
	private suffix;

	private dns;



	/**
	 * Construct
	 *
	 * @param string $host Host.
	 * @param bool $www Strip www.
	 * @return void Nothing.
	 */
	public function __construct(string host, const bool www=false) -> void {
		var parsed = self::parseHostParts(host);
		if (false === parsed) {
			return;
		}

		let this->host = parsed["host"];
		let this->subdomain = parsed["subdomain"];
		let this->domain = parsed["domain"];
		let this->suffix = parsed["suffix"];

		if (www) {
			this->stripWww();
		}
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
	public static function parseHost(string host) -> string | bool {
		// Try to parse it the easy way.
		var tmp = self::parseUrl(host, PHP_URL_HOST);
		if (!empty tmp) {
			let host = tmp;
		}
		// Or the hard way?
		else {
			let host = \Blobfolio\Strings::trim(host);

			// Cut off the path, if any.
			var start = mb_strpos(host, "/", 0, "UTF-8");
			if (false !== start) {
				let host = mb_substr(host, 0, start, "UTF-8");
			}

			// Cut off the query, if any.
			let start = mb_strpos(host, "?", 0, "UTF-8");
			if (false !== start) {
				let host = mb_substr(host, 0, start, "UTF-8");
			}

			// Cut off credentials, if any.
			let start = mb_strpos(host, "@", 0, "UTF-8");
			if (false !== start) {
				let host = mb_substr(host, start + 1, null, "UTF-8");
			}

			// Is this an IPv6 address?
			if (filter_var(host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
				let host = IPs::niceIp(host, true);
			}
			else {
				// Pluck an IP out of brackets.
				let start = strpos(host, "[");
				var end = strpos(host, "]");
				if ((0 === start) && false !== end) {
					let host = mb_substr(host, 1, end - 1, "UTF-8");
					let host = IPs::niceIp(host, true);
				}
				// Chop off the port, if any.
				else {
					let start = mb_strpos(host, ":", 0, "UTF-8");
					if (false !== start) {
						let host = mb_substr(host, 0, start, "UTF-8");
					}
				}
			}

			// If it is empty or invalid, there is nothing we can do.
			if (empty host) {
				return false;
			}

			// Convert to ASCII if possible.
			let host = (string) self::toAscii(host);

			// Lowercase it.
			let host = strtolower(host);

			// Get rid of trailing periods.
			let host = ltrim(host, ".");
			let host = rtrim(host, ".");
		}

		// Liberate IPv6 from its walls.
		if (0 === strpos(host, "[")) {
			let host = str_replace(["[", "]"], "", host);
			let host = IPs::niceIp(host, true);
		}

		// Is this an IP address? If so, we're done!
		if (filter_var(host, FILTER_VALIDATE_IP)) {
			return host;
		}

		// Look for illegal characters. At this point we should
		// only have nice and safe ASCII.
		if (preg_match("/[^a-z\d\-\.]/u", host)) {
			return false;
		}

		array parts = (array) explode(".", host);
		var v;
		for v in parts {
			// Gotta have length, and can't start or end with a dash.
			if (
				empty v ||
				(0 === strpos(v, "-")) ||
				("-" === substr(v, -1))
			) {
				return false;
			}
		}

		return implode(".", parts);
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
	public static function parseHostParts(string host) -> array | bool {
		// Tease out the hostname.
		let host = self::parseHost(host);
		if (empty host) {
			return false;
		}

		array out = [
			"host": null,
			"subdomain": null,
			"domain": null,
			"suffix": null
		];

		// If this is an IP, we don't have to do anything else.
		if (filter_var(host, FILTER_VALIDATE_IP)) {
			let out["host"] = host;
			let out["domain"] = host;
			return out;
		}

		// Now the hard part. See if any parts of the host
		// correspond to a registered suffix.
		if (!globals_get("loaded_blob_domains")) {
			self::loadSuffixes();
		}

		array suffixes = (array) self::_suffixes;
		array suffix = [];
		array parts = (array) explode(".", host);
		let parts = array_reverse(parts);

		var k, part;
		for k, part in parts {
			// Override rule.
			if (isset(suffixes[part]["!"])) {
				break;
			}

			// A match.
			if (isset(suffixes[part])) {
				array_unshift(suffix, part);
				let suffixes = suffixes[part];
				unset(parts[k]);
				continue;
			}

			// A wildcard.
			if (isset(suffixes["*"])) {
				array_unshift(suffix, part);
				let suffixes = suffixes["*"];
				unset(parts[k]);
				continue;
			}

			// We're done.
			break;
		}

		// The suffix can't be all there is.
		if (!count(parts)) {
			return false;
		}

		// The domain.
		let parts = array_reverse(parts);
		let out["domain"] = array_pop(parts);

		// The subdomain.
		if (count(parts)) {
			let out["subdomain"] = implode(".", parts);
		}

		// The suffix.
		if (count(suffix)) {
			let out["suffix"] = implode(".", suffix);
		}

		let out["host"] = host;
		return out;
	}

	/**
	 * Strip Leading WWW
	 *
	 * The www. subdomain is evil. This removes
	 * it, but only if it is part of the subdomain.
	 *
	 * @return void Nothing.
	 */
	public function stripWww() -> void {
		if (!this->isValid() || null === this->subdomain) {
			return;
		}

		if (
			("www" === this->subdomain) ||
			(0 === strpos(this->subdomain, "www."))
		) {
			let this->subdomain = preg_replace("/^www\.?/u", "", this->subdomain);
			if (empty this->subdomain) {
				let this->subdomain = null;
			}

			let this->host = preg_replace("/^www\./u", "", this->host);
		}
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
	public function isValid(const bool dns=false) -> bool {
		return (
			(null !== this->host) &&
			(!dns || this->hasDns())
		);
	}

	/**
	 * Is Fully Qualified Domain Name
	 *
	 * @return bool True/false.
	 */
	public function isFqdn() -> bool {
		return (
			this->isValid() &&
			(("string" === typeof this->suffix) || this->isIp(false))
		);
	}

	/**
	 * Is IP
	 *
	 * @param bool $restricted Allow restricted.
	 * @return bool True/false.
	 */
	public function isIp(const bool restricted=true) -> bool {
		if (!this->isValid()) {
			return false;
		}

		if (restricted) {
			return !!filter_var(this->host, FILTER_VALIDATE_IP);
		}

		return !!filter_var(
			this->host,
			FILTER_VALIDATE_IP,
			FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
		);
	}

	/**
	 * Has DNS
	 *
	 * @return bool True/false.
	 */
	public function hasDns() -> bool {
		if (null === this->dns) {
			if (!this->isFqdn()) {
				let this->dns = false;
			}
			elseif (this->isIp()) {
				let this->dns = this->isIp(false);
			}
			else {
				let this->dns = !!filter_var(
					gethostbyname(this->host . "."),
					FILTER_VALIDATE_IP,
					FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
				);
			}
		}

		return this->dns;
	}

	/**
	 * Is ASCII
	 *
	 * @return bool True/false.
	 */
	public function isAscii() -> bool {
		if (!this->isValid()) {
			return false;
		}

		return !this->isUnicode();
	}

	/**
	 * Is Unicode
	 *
	 * @return bool True/false.
	 */
	public function isUnicode() -> bool {
		if (
			!this->isValid() ||
			this->isIp()
		) {
			return false;
		}

		return (self::toUnicode(this->host) !== this->host);
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
		return this->isValid() ? this->host : "";
	}

	/**
	 * Get Data
	 *
	 * @param bool $unicode Unicode.
	 * @return array|bool Host data or false.
	 */
	public function getData(const bool unicode=false) -> array | bool {
		if (!this->isValid()) {
			return false;
		}

		return [
			"host": this->getHost(unicode),
			"subdomain": this->getSubdomain(unicode),
			"domain": this->getDomain(unicode),
			"suffix": this->getSuffix(unicode)
		];
	}

	/**
	 * Get Host
	 *
	 * @param bool $unicode Unicode.
	 * @return string|null Host.
	 */
	public function getHost(const bool unicode=false) -> string | null {
		if (
			unicode &&
			!empty this->host
		) {
			return self::toUnicode(this->host);
		}

		return this->host;
	}

	/**
	 * Get Subdomain
	 *
	 * @param bool $unicode Unicode.
	 * @return string|null Subdomain.
	 */
	public function getSubdomain(const bool unicode=false) -> string | null {
		if (
			unicode &&
			!empty this->subdomain
		) {
			return self::toUnicode(this->subdomain);
		}

		return this->subdomain;
	}

	/**
	 * Get Domain
	 *
	 * @param bool $unicode Unicode.
	 * @return string|null Domain.
	 */
	public function getDomain(const bool unicode=false) -> string | null {
		if (
			unicode &&
			!empty this->domain
		) {
			return self::toUnicode(this->domain);
		}

		return this->domain;
	}

	/**
	 * Get Suffix
	 *
	 * @param bool $unicode Unicode.
	 * @return string|null Suffix.
	 */
	public function getSuffix(const bool unicode=false) -> string | null {
		if (
			unicode &&
			!empty this->suffix
		) {
			return self::toUnicode(this->suffix);
		}

		return this->suffix;
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
	 * @param bool $unicode Unicode.
	 * @return bool True/false.
	 */
	public static function niceDomain(const string str, const bool unicode=false) -> string {
		var host;
		let host = new self(str, true);
		if (host->isFqdn() && !host->isIp()) {
			return host->getHost(unicode);
		}

		return "";
	}

	/**
	 * Email
	 *
	 * Converts the email to lowercase, strips
	 * invalid characters, quotes, and apostrophes.
	 *
	 * @param string $str Email.
	 * @return void Nothing.
	 */
	public static function niceEmail(string str) -> string {
		string str = (string) \Blobfolio\Strings::quotes(str);
		let str = \Blobfolio\Strings::toLower(str, true);

		// Strip comments.
		let str = preg_replace("/\([^)]*\)/u", "", str);

		// Early bail: wrong number of "@".
		if (1 !== substr_count(str, "@")) {
			return "";
		}

		// For backward-compatibility, strip quotes now.
		let str = str_replace(["'", "\""], "", str);

		// Sanitize by part.
		array parts = (array) explode("@", str);

		// Sanitize local part.
		let parts[0] = preg_replace(
			"/[^\.a-z0-9\!#\$%&\*\+\-\=\?_~]/u",
			"",
			parts[0]
		);
		let parts[0] = ltrim(parts[0], ".");
		let parts[0] = rtrim(parts[0], ".");

		// Another early bail, nothing local left.
		if (empty parts[0]) {
			return "";
		}

		// Sanitize host.
		var host;
		let host = new self(parts[1]);
		if (!host->isValid() || !host->isFqdn() || host->isIp()) {
			return "";
		}
		let parts[1] = host->getHost();

		return implode("@", parts);
	}

	/**
	 * Hostname
	 *
	 * @param string $str Hostname.
	 * @param bool $www Strip leading www.
	 * @param bool $unicode Unicode.
	 * @return bool True/false.
	 */
	public static function niceHost(const string str, const bool www=true, const bool unicode=false) -> string | bool {

		var host;
		let host = new self(str, www);
		if (!host->isValid()) {
			return false;
		}

		return host->getHost(unicode);
	}

	/**
	 * URL
	 *
	 * Validate URLishness and convert // schemas.
	 *
	 * @param string $str URL.
	 * @return bool True/false.
	 */
	public static function niceUrl(string str) -> string {
		array tmp = (array) self::parseUrl(str);

		// Validate the host, and ASCIIfy international bits
		// to keep PHP happy.
		if (!isset(tmp["host"]) || empty tmp["host"]) {
			return "";
		}

		let tmp["host"] = new self(tmp["host"]);
		if (!tmp["host"]->isValid()) {
			return "";
		}
		let tmp["host"] = tmp["host"]->getHost();

		// Schemes can be lowercase.
		if (isset(tmp["scheme"])) {
			let tmp["scheme"] = strtolower(tmp["scheme"]);
		}

		// Put it back together.
		let str = (string) self::unparseUrl(tmp);

		let str = filter_var(str, FILTER_SANITIZE_URL);
		if (!filter_var(
			str,
			FILTER_VALIDATE_URL,
			FILTER_FLAG_SCHEME_REQUIRED | FILTER_FLAG_HOST_REQUIRED
		)) {
			return "";
		}

		return str;
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
	public static function parseUrl(string url, const int $component = -1) -> array | string | null {
		let url = \Blobfolio\Strings::trim(url);

		// Before we start, let's fix scheme-agnostic URLs.
		let url = preg_replace("#^:?//#", "https://", url);

		// If an IPv6 address is passed on its own, we
		// need to shove it in brackets.
		if (filter_var(url, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
			let url = "[" . url . "]";
		}

		// The trick is to urlencode (most) parts before passing
		// them to the real parse_url().
		string encoded = (string) preg_replace_callback(
			"%([a-zA-Z][a-zA-Z0-9+\-.]*)?(:?//)?([^:/@?&=#\[\]]+)%usD",
			[__CLASS__, "_parseUrlCallback"],
			url
		);

		// Before getting the real answer, make sure
		// there is a scheme, otherwise PHP will assume
		// all there is is a path, which is stupid.
		if (PHP_URL_SCHEME !== component) {
			var test = parse_url(encoded, PHP_URL_SCHEME);
			if (empty test) {
				let encoded = "blobfolio://" . encoded;
			}
		}

		var parts = parse_url(encoded, component);

		// And now decode what we've been giving. Let's also take a
		// moment to translate Unicode hosts to ASCII.
		if (("string" === typeof parts) && (PHP_URL_SCHEME !== component)) {
			let parts = str_replace(" ", "+", urldecode(parts));

			if (PHP_URL_HOST === component) {
				// Fix Unicode.
				let parts = (string) self::toAscii(parts);

				// Lowercase it.
				let parts = strtolower(parts);

				// Get rid of trailing periods.
				let parts = ltrim(parts, ".");
				let parts = rtrim(parts, ".");

				// Standardize IPv6 formatting.
				if (0 === strpos(parts, "[")) {
					let parts = str_replace(["[", "]"], "", parts);
					let parts = IPs::niceIp(parts, true);
					let parts = "[" . parts . "]";
				}
			}
		}
		elseif ("array" === typeof parts) {
			var k, v;
			for k, v in parts {
				if ("string" !== typeof v) {
					continue;
				}

				if ("scheme" !== k) {
					let parts[k] = str_replace(" ", "+", urldecode(v));
				}
				// Remove our pretend scheme.
				elseif ("blobfolio" === v) {
					unset(parts[k]);
					continue;
				}

				if (("host" === k) && ("string" === typeof parts[k])) {
					// Fix Unicode.
					let parts[k] = (string) self::toAscii(parts[k]);

					// Lowercase it.
					let parts[k] = strtolower(parts[k]);

					// Get rid of trailing periods.
					let parts[k] = ltrim(parts[k], ".");
					let parts[k] = rtrim(parts[k], ".");

					// Standardize IPv6 formatting.
					if (0 === strpos(parts[k], "[")) {
						let parts[k] = str_replace(["[", "]"], "", parts[k]);
						let parts[k] = IPs::niceIp(parts[k], true);
						let parts[k] = "[" . parts[k] . "]";
					}
				}
			}
		}

		return parts;
	}

	/**
	 * Parse URL Callback.
	 *
	 * @param array $matches Matches.
	 * @return string Replacement.
	 */
	private static function _parseUrlCallback(array matches) -> string {
		return matches[1] . matches[2] . urldecode(matches[3]);
	}

	/**
	 * Reverse `parse_url()`
	 *
	 * @param array $parsed Parsed data.
	 * @return string URL.
	 */
	public static function unparseUrl(array parsed) -> string | bool {
		string url = "";
		array url_parts = [
			"scheme":"",
			"host":"",
			"user":"",
			"pass":"",
			"port":"",
			"path":"",
			"query":"",
			"fragment":""
		];

		let parsed = \Blobfolio\Cast::parseArgs(parsed, url_parts);

		// To simplify, unset anything without length.
		var k, v;
		for k, v in parsed {
			let parsed[k] = \Blobfolio\Strings::trim(v);
			if (empty parsed[k]) {
				unset(parsed[k]);
			}
		}

		// We don't really care about validating url integrity,
		// but if nothing at all was passed then it is trash.
		if (!count(parsed)) {
			return false;
		}

		// The scheme.
		if (isset($parsed["scheme"])) {
			let url .= parsed["scheme"] . ":";
		}

		// The host.
		if (isset(parsed["host"])) {
			if (!empty url) {
				let url .= "//";
			}

			// Is this a user:pass situation?
			if (isset(parsed["user"])) {
				let url .= parsed["user"];
				if (isset(parsed["pass"])) {
					let url .= ":" . parsed["pass"];
				}
				let url .= "@";
			}

			// Finally the host.
			if (filter_var(parsed["host"], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
				let url .= "[" . parsed["host"] . "]";
			}
			else {
				let url .= parsed["host"];
			}

			// The port.
			if (isset(parsed["port"])) {
				let url .= ":" . parsed["port"];
			}

			// Prepare for a path by adding a trailing slash.
			if (isset(parsed["path"]) && (0 !== strpos(parsed["path"], "/"))) {
				let url .= "/";
			}
		}

		// Add the path.
		if (isset(parsed["path"])) {
			let url .= parsed["path"];
		}

		// Add the query.
		if (isset(parsed["query"])) {
			let url .= "?" . parsed["query"];
		}

		// And top it off with a fragment.
		if (isset(parsed["fragment"])) {
			let url .= "#" . parsed["fragment"];
		}

		if (empty url) {
			return false;
		}

		return url;
	}

	/**
	 * Redirect
	 *
	 * @param string $to URL.
	 * @return void Nothing.
	 */
	public static function redirect(string to) -> void {
		let to = \Blobfolio\Domains::niceUrl(to);

		// Prevent stupid browser RELOAD warnings.
		let _POST = null;
		let _GET = null;
		let _REQUEST = null;

		if (!headers_sent()) {
			header("Location: " . to);
		}
		else {
			echo "<script>top.location.href='" . str_replace("'", "\'", to) . "';</script>";
		}

		exit(0);
	}

	/**
	 * To ASCII
	 *
	 * @param string $value Value.
	 * @return string|null Value.
	 */
	private static function toAscii(string value) -> string | null {
		if (!empty value) {
			array parts = (array) explode(".", value);
			var k, v;
			for k, v in parts {
				let parts[k] = (string) idn_to_ascii(v, 0, INTL_IDNA_VARIANT_UTS46);
			}
			return implode(".", parts);
		}

		return null;
	}

	/**
	 * To Unicode
	 *
	 * @param string $value Value.
	 * @return string|null Value.
	 */
	private static function toUnicode(string value) -> string | null {
		if (!empty value) {
			array parts = (array) explode(".", value);
			var k, v;
			for k, v in parts {
				let parts[k] = (string) idn_to_utf8(v, 0, INTL_IDNA_VARIANT_UTS46);
			}
			return implode(".", parts);
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
	public static function unsetcookie(string name, string path="", string domain="", bool secure=false, bool httponly=false) -> bool {

		if (!headers_sent()) {
			setcookie(name, false, -1, path, domain, secure, httponly);
			if (isset(_COOKIE[name])) {
				unset(_COOKIE[name]);
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
	private static function loadSuffixes() -> void {
		// Gotta load it!
		string json = (string) \Blobfolio\Blobfolio::getDataDir("blob-domains.json");
		if (empty json) {
			throw new \Exception("Missing domain suffix data.");
		}

		let self::_suffixes = json_decode(json, true);
		if ("array" !== typeof self::_suffixes) {
			throw new \Exception("Could not parse domain suffix data.");
		}

		globals_set("loaded_blob_domains", true);
	}
}
