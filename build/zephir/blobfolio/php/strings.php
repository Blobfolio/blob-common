<?php
/**
 * Blobfolio: Strings
 *
 * String manipulation.
 *
 * @see {https://github.com/Blobfolio/blob-common}
 *
 * @package Blobfolio/Common
 * @author Blobfolio, LLC <hello@blobfolio.com>
 */

namespace Blobfolio;

use Blobfolio\Blobfolio as Shim;



final class Strings {
	/**
	 * Uppercase Unicode.
	 *
	 * @var array $case_char_upper
	 */
	private static $case_char_upper = array(
		"\xC7\x85", "\xC7\x88", "\xC7\x8B", "\xC7\xB2", "\xCF\xB7", "\xCF\xB9",
		"\xCF\xBA", "\xE1\xBE\x88", "\xE1\xBE\x89", "\xE1\xBE\x8A",
		"\xE1\xBE\x8B", "\xE1\xBE\x8C", "\xE1\xBE\x8D", "\xE1\xBE\x8E",
		"\xE1\xBE\x8F", "\xE1\xBE\x98", "\xE1\xBE\x99", "\xE1\xBE\x9A",
		"\xE1\xBE\x9B", "\xE1\xBE\x9C", "\xE1\xBE\x9D", "\xE1\xBE\x9E",
		"\xE1\xBE\x9F", "\xE1\xBE\xA8", "\xE1\xBE\xA9", "\xE1\xBE\xAA",
		"\xE1\xBE\xAB", "\xE1\xBE\xAC", "\xE1\xBE\xAD", "\xE1\xBE\xAE",
		"\xE1\xBE\xAF", "\xE1\xBE\xBC", "\xE1\xBF\x8C", "\xE1\xBF\xBC",
		"\xE2\x85\xA0", "\xE2\x85\xA1", "\xE2\x85\xA2", "\xE2\x85\xA3",
		"\xE2\x85\xA4", "\xE2\x85\xA5", "\xE2\x85\xA6", "\xE2\x85\xA7",
		"\xE2\x85\xA8", "\xE2\x85\xA9", "\xE2\x85\xAA", "\xE2\x85\xAB",
		"\xE2\x85\xAC", "\xE2\x85\xAD", "\xE2\x85\xAE", "\xE2\x85\xAF",
		"\xE2\x92\xB6", "\xE2\x92\xB7", "\xE2\x92\xB8", "\xE2\x92\xB9",
		"\xE2\x92\xBA", "\xE2\x92\xBB", "\xE2\x92\xBC", "\xE2\x92\xBD",
		"\xE2\x92\xBE", "\xE2\x92\xBF", "\xE2\x93\x80", "\xE2\x93\x81",
		"\xE2\x93\x82", "\xE2\x93\x83", "\xE2\x93\x84", "\xE2\x93\x85",
		"\xE2\x93\x86", "\xE2\x93\x87", "\xE2\x93\x88", "\xE2\x93\x89",
		"\xE2\x93\x8A", "\xE2\x93\x8B", "\xE2\x93\x8C", "\xE2\x93\x8D",
		"\xE2\x93\x8E", "\xE2\x93\x8F", "\xF0\x90\xA6", "\xF0\x90\xA7",
	);

	/**
	 * Lowercase Unicode.
	 *
	 * @var array $case_char_lower
	 */
	private static $case_char_lower = array(
		"\xC7\x86", "\xC7\x89", "\xC7\x8C", "\xC7\xB3", "\xCF\xB8", "\xCF\xB2",
		"\xCF\xBB", "\xE1\xBE\x80", "\xE1\xBE\x81", "\xE1\xBE\x82",
		"\xE1\xBE\x83", "\xE1\xBE\x84", "\xE1\xBE\x85", "\xE1\xBE\x86",
		"\xE1\xBE\x87", "\xE1\xBE\x90", "\xE1\xBE\x91", "\xE1\xBE\x92",
		"\xE1\xBE\x93", "\xE1\xBE\x94", "\xE1\xBE\x95", "\xE1\xBE\x96",
		"\xE1\xBE\x97", "\xE1\xBE\xA0", "\xE1\xBE\xA1", "\xE1\xBE\xA2",
		"\xE1\xBE\xA3", "\xE1\xBE\xA4", "\xE1\xBE\xA5", "\xE1\xBE\xA6",
		"\xE1\xBE\xA7", "\xE1\xBE\xB3", "\xE1\xBF\x83", "\xE1\xBF\xB3",
		"\xE2\x85\xB0", "\xE2\x85\xB1", "\xE2\x85\xB2", "\xE2\x85\xB3",
		"\xE2\x85\xB4", "\xE2\x85\xB5", "\xE2\x85\xB6", "\xE2\x85\xB7",
		"\xE2\x85\xB8", "\xE2\x85\xB9", "\xE2\x85\xBA", "\xE2\x85\xBB",
		"\xE2\x85\xBC", "\xE2\x85\xBD", "\xE2\x85\xBE", "\xE2\x85\xBF",
		"\xE2\x93\x90", "\xE2\x93\x91", "\xE2\x93\x92", "\xE2\x93\x93",
		"\xE2\x93\x94", "\xE2\x93\x95", "\xE2\x93\x96", "\xE2\x93\x97",
		"\xE2\x93\x98", "\xE2\x93\x99", "\xE2\x93\x9A", "\xE2\x93\x9B",
		"\xE2\x93\x9C", "\xE2\x93\x9D", "\xE2\x93\x9E", "\xE2\x93\x9F",
		"\xE2\x93\xA0", "\xE2\x93\xA1", "\xE2\x93\xA2", "\xE2\x93\xA3",
		"\xE2\x93\xA4", "\xE2\x93\xA5", "\xE2\x93\xA6", "\xE2\x93\xA7",
		"\xE2\x93\xA8", "\xE2\x93\xA9", "\xF0\x91\x8E", "\xF0\x91\x8F",
	);

	/**
	 * Accents
	 *
	 * Convert accented to non-accented characters.
	 *
	 * @param string $str String.
	 * @param int $flags Flags.
	 * @return string String.
	 */
	public static function accents(string $str, int $flags=0) : string {
		$trusted = !! ($flags & Shim::TRUSTED);
		if (! $trusted) {
			$str = self::utf8($str);
		}

		if (\preg_match("/[\x80-\xff]/", $str)) {
			$accent_chars = array(
				'ª'=>'a', 'º'=>'o', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A',
				'Ä'=>'A', 'Å'=>'A', 'Æ'=>'AE', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
				'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I',
				'Ð'=>'D', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O',
				'Ö'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y',
				'Þ'=>'TH', 'ß'=>'s', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a',
				'ä'=>'a', 'å'=>'a', 'æ'=>'ae', 'ç'=>'c', 'è'=>'e', 'é'=>'e',
				'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i',
				'ð'=>'d', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
				'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ü'=>'u',
				'ý'=>'y', 'þ'=>'th', 'ÿ'=>'y', 'Ø'=>'O', 'Ā'=>'A', 'ā'=>'a',
				'Ă'=>'A', 'ă'=>'a', 'Ą'=>'A', 'ą'=>'a', 'Ć'=>'C', 'ć'=>'c',
				'Ĉ'=>'C', 'ĉ'=>'c', 'Ċ'=>'C', 'ċ'=>'c', 'Č'=>'C', 'č'=>'c',
				'Ď'=>'D', 'ď'=>'d', 'Đ'=>'D', 'đ'=>'d', 'Ē'=>'E', 'ē'=>'e',
				'Ĕ'=>'E', 'ĕ'=>'e', 'Ė'=>'E', 'ė'=>'e', 'Ę'=>'E', 'ę'=>'e',
				'Ě'=>'E', 'ě'=>'e', 'Ĝ'=>'G', 'ĝ'=>'g', 'Ğ'=>'G', 'ğ'=>'g',
				'Ġ'=>'G', 'ġ'=>'g', 'Ģ'=>'G', 'ģ'=>'g', 'Ĥ'=>'H', 'ĥ'=>'h',
				'Ħ'=>'H', 'ħ'=>'h', 'Ĩ'=>'I', 'ĩ'=>'i', 'Ī'=>'I', 'ī'=>'i',
				'Ĭ'=>'I', 'ĭ'=>'i', 'Į'=>'I', 'į'=>'i', 'İ'=>'I', 'ı'=>'i',
				'Ĳ'=>'IJ', 'ĳ'=>'ij', 'Ĵ'=>'J', 'ĵ'=>'j', 'Ķ'=>'K', 'ķ'=>'k',
				'ĸ'=>'k', 'Ĺ'=>'L', 'ĺ'=>'l', 'Ļ'=>'L', 'ļ'=>'l', 'Ľ'=>'L',
				'ľ'=>'l', 'Ŀ'=>'L', 'ŀ'=>'l', 'Ł'=>'L', 'ł'=>'l', 'Ń'=>'N',
				'ń'=>'n', 'Ņ'=>'N', 'ņ'=>'n', 'Ň'=>'N', 'ň'=>'n', 'ŉ'=>'N',
				'Ŋ'=>'n', 'ŋ'=>'N', 'Ō'=>'O', 'ō'=>'o', 'Ŏ'=>'O', 'ŏ'=>'o',
				'Ő'=>'O', 'ő'=>'o', 'Œ'=>'OE', 'œ'=>'oe', 'Ŕ'=>'R', 'ŕ'=>'r',
				'Ŗ'=>'R', 'ŗ'=>'r', 'Ř'=>'R', 'ř'=>'r', 'Ś'=>'S', 'ś'=>'s',
				'Ŝ'=>'S', 'ŝ'=>'s', 'Ş'=>'S', 'ş'=>'s', 'Š'=>'S', 'š'=>'s',
				'Ţ'=>'T', 'ţ'=>'t', 'Ť'=>'T', 'ť'=>'t', 'Ŧ'=>'T', 'ŧ'=>'t',
				'Ũ'=>'U', 'ũ'=>'u', 'Ū'=>'U', 'ū'=>'u', 'Ŭ'=>'U', 'ŭ'=>'u',
				'Ů'=>'U', 'ů'=>'u', 'Ű'=>'U', 'ű'=>'u', 'Ų'=>'U', 'ų'=>'u',
				'Ŵ'=>'W', 'ŵ'=>'w', 'Ŷ'=>'Y', 'ŷ'=>'y', 'Ÿ'=>'Y', 'Ź'=>'Z',
				'ź'=>'z', 'Ż'=>'Z', 'ż'=>'z', 'Ž'=>'Z', 'ž'=>'z', 'ſ'=>'s',
				'Ș'=>'S', 'ș'=>'s', 'Ț'=>'T', 'ț'=>'t', '€'=>'E', '£'=>'',
				'Ơ'=>'O', 'ơ'=>'o', 'Ư'=>'U', 'ư'=>'u', 'Ầ'=>'A', 'ầ'=>'a',
				'Ằ'=>'A', 'ằ'=>'a', 'Ề'=>'E', 'ề'=>'e', 'Ồ'=>'O', 'ồ'=>'o',
				'Ờ'=>'O', 'ờ'=>'o', 'Ừ'=>'U', 'ừ'=>'u', 'Ỳ'=>'Y', 'ỳ'=>'y',
				'Ả'=>'A', 'ả'=>'a', 'Ẩ'=>'A', 'ẩ'=>'a', 'Ẳ'=>'A', 'ẳ'=>'a',
				'Ẻ'=>'E', 'ẻ'=>'e', 'Ể'=>'E', 'ể'=>'e', 'Ỉ'=>'I', 'ỉ'=>'i',
				'Ỏ'=>'O', 'ỏ'=>'o', 'Ổ'=>'O', 'ổ'=>'o', 'Ở'=>'O', 'ở'=>'o',
				'Ủ'=>'U', 'ủ'=>'u', 'Ử'=>'U', 'ử'=>'u', 'Ỷ'=>'Y', 'ỷ'=>'y',
				'Ẫ'=>'A', 'ẫ'=>'a', 'Ẵ'=>'A', 'ẵ'=>'a', 'Ẽ'=>'E', 'ẽ'=>'e',
				'Ễ'=>'E', 'ễ'=>'e', 'Ỗ'=>'O', 'ỗ'=>'o', 'Ỡ'=>'O', 'ỡ'=>'o',
				'Ữ'=>'U', 'ữ'=>'u', 'Ỹ'=>'Y', 'ỹ'=>'y', 'Ấ'=>'A', 'ấ'=>'a',
				'Ắ'=>'A', 'ắ'=>'a', 'Ế'=>'E', 'ế'=>'e', 'Ố'=>'O', 'ố'=>'o',
				'Ớ'=>'O', 'ớ'=>'o', 'Ứ'=>'U', 'ứ'=>'u', 'Ạ'=>'A', 'ạ'=>'a',
				'Ậ'=>'A', 'ậ'=>'a', 'Ặ'=>'A', 'ặ'=>'a', 'Ẹ'=>'E', 'ẹ'=>'e',
				'Ệ'=>'E', 'ệ'=>'e', 'Ị'=>'I', 'ị'=>'i', 'Ọ'=>'O', 'ọ'=>'o',
				'Ộ'=>'O', 'ộ'=>'o', 'Ợ'=>'O', 'ợ'=>'o', 'Ụ'=>'U', 'ụ'=>'u',
				'Ự'=>'U', 'ự'=>'u', 'Ỵ'=>'Y', 'ỵ'=>'y', 'ɑ'=>'a', 'Ǖ'=>'U',
				'ǖ'=>'u', 'Ǘ'=>'U', 'ǘ'=>'u', 'Ǎ'=>'A', 'ǎ'=>'a', 'Ǐ'=>'I',
				'ǐ'=>'i', 'Ǒ'=>'O', 'ǒ'=>'o', 'Ǔ'=>'U', 'ǔ'=>'u', 'Ǚ'=>'U',
				'ǚ'=>'u', 'Ǜ'=>'U', 'ǜ'=>'u',
			);

			$str = \strtr($str, $accent_chars);

			// Remove combining accents too.
			$str = \str_replace(
				array("\xCC\x80", "\xCC\x81"),
				'',
				$str
			);
		}

		return $str;
	}

	/**
	 * Control Characters
	 *
	 * @param string $str String.
	 * @param int $flags Flags.
	 * @return string String.
	 */
	public static function controlChars(string $str, int $flags=0) : string {
		$trusted = !! ($flags & Shim::TRUSTED);
		if (! $trusted) {
			$str = self::utf8($str);
		}

		$str = \preg_replace(
			array(
				'/[\x00-\x08\x0B\x0C\x0E-\x1F]/',
				'/\\\\+0+/',
			),
			'',
			$str
		);
		return $str;
	}

	/**
	 * Generate Text Excerpt
	 *
	 * @param string $str String.
	 * @param int $length Length.
	 * @param int $flags Flags.
	 *
	 * @arg int $length Length limit.
	 * @arg string $unit Unit to examine, "character" or "word".
	 * @arg string $suffix Suffix, e.g. ...
	 *
	 * @return string Excerpt.
	 */
	public static function excerpt(string $str, int $length = 200, int $flags = 5) : string {
		$str = self::whitespace($str, 0, ($flags & Shim::TRUSTED));
		$str = \strip_tags($str);

		$flagsEllipsis = !! ($flags & Shim::EXCERPT_ELLIPSIS);
		$flagsWord = !! ($flags & Shim::EXCERPT_BREAK_WORD);
		$flagsChar = ! $flagsWord;
		$flagsInclusive = $flagsEllipsis && $flagsChar && ($flags & Shim::EXCERPT_INCLUSIVE);

		if ($flagsInclusive) {
			$length -= 1;
		}

		if ($length < 1) {
			return '';
		}

		// Character limit.
		if ($flagsChar && \mb_strlen($str, 'UTF-8') > $length) {
			$str = (string) \trim(\mb_substr($str, 0, $length, 'UTF-8'));
			if ($flagsEllipsis) {
				$str .= '…';
			}
		}
		// Limit words.
		elseif ($flagsWord && \substr_count($str, ' ') > $length - 1) {
			$tmp = (array) \explode(' ', $str);
			$tmp = \array_slice($tmp, 0, $length);
			$str = (string) \implode(' ', $tmp);
			if ($flagsEllipsis) {
				$str .= '…';
			}
		}

		return $str;
	}

	/**
	 * Inflect
	 *
	 * Inflect a phrase given a count. `sprintf` formatting is
	 * supported. If an array is passed as $count, its size will be used
	 * for inflection.
	 *
	 * @param int|array $count Count.
	 * @param string $single Singular.
	 * @param string $plural Plural.
	 * @param int $flags Flags.
	 * @return string Inflected string.
	 */
	public static function inflect($count, string $single, string $plural, int $flags=0) : string {
		// Use the count() as $count for arrays.
		if ('array' === \gettype($count)) {
			$count = (int) \count($count);
		}
		// For everything else, there's floats.
		else {
			$count = \Blobfolio\Cast::toFloat($count);
		}

		// Figure out which phrase to use.
		$trusted = !! ($flags & Shim::TRUSTED);
		if (1 === $count || 1.0 === $count) {
			if (! $trusted) {
				$str = (string) \Blobfolio\Strings::utf8($single);
			}
			else {
				$str = $single;
			}
		}
		else {
			if (! $trusted) {
				$str = (string) \Blobfolio\Strings::utf8($plural);
			}
			else {
				$str = $plural;
			}
		}

		return \sprintf($str, $count);
	}

	/**
	 * Nice Text
	 *
	 * This runs UTF-8, control chars, quotes, printable, and whitespace
	 * against a string.
	 *
	 * @param string $str String.
	 * @param int $newlines Newlines.
	 * @param int $flags Flags.
	 * @return string String.
	 */
	public static function niceText(string $str, int $newlines=0, int $flags=0) : string {
		$str = self::printable($str, ($flags & Shim::TRUSTED));
		$str = self::controlChars($str, Shim::TRUSTED);
		$str = self::quotes($str, Shim::TRUSTED);
		$str = self::whitespace($str, $newlines, Shim::TRUSTED);

		return $str;
	}

	/**
	 * Printable
	 *
	 * Remove non-printable characters (except spaces).
	 *
	 * @param string $str String.
	 * @param int $flags Flags.
	 * @return string String.
	 */
	public static function printable(string $str, int $flags=0) : string {
		$trusted = !! ($flags & Shim::TRUSTED);
		if (! $trusted) {
			$str = self::utf8($str);
		}

		// Stripe zero-width chars.
		$str = \preg_replace("/[\x{200B}-\x{200D}\x{FEFF}]/u", '', $str);

		// Make whitespace consistent.
		$str = \str_replace("\r\n", "\n", $str);
		$str = \str_replace("\r", "\n", $str);
		$str = \preg_replace_callback(
			'/[^[:print:]]/u',
			array(static::class, 'printableCallback'),
			$str
		);

		return $str;
	}

	/**
	 * Printable Callback
	 *
	 * @param array $match Match.
	 * @return string Replacement.
	 */
	private static function printableCallback(array $match) : string {
		// Allow newlines and tabs, in case the OS considers
		// those non-printable.
		if (
			("\n" === $match[0]) ||
			("\t" === $match[0])
		) {
			return $match[0];
		}

		// Ignore everything else.
		return '';
	}

	/**
	 * Quotes
	 *
	 * Straighten out various forms of curly quotes and apostrophes.
	 *
	 * @param array|string $str String.
	 * @param int $flags Flags.
	 * @return array|string String.
	 */
	public static function quotes(string $str, int $flags=0) : string {
		$trusted = !! ($flags & Shim::TRUSTED);
		if (! $trusted) {
			$str = self::utf8($str);
		}

		// Curly quotes.
		$quote_char_keys = array(
			"\xC2\x82", "\xC2\x84", "\xC2\x8B", "\xC2\x91", "\xC2\x92",
			"\xC2\x93", "\xC2\x94", "\xC2\x9B", "\xC2\xAB", "\xC2\xBB",
			"\xE2\x80\x98", "\xE2\x80\x99", "\xE2\x80\x9A", "\xE2\x80\x9B",
			"\xE2\x80\x9C", "\xE2\x80\x9D", "\xE2\x80\x9E", "\xE2\x80\x9F",
			"\xE2\x80\xB9", "\xE2\x80\xBA",
		);

		$quote_char_values = array(
			"'", '"', "'", "'", "'", '"', '"', "'", '"', '"', "'",
			"'", "'", "'", '"', '"', '"', '"', "'", "'",
		);

		return \str_replace($quote_char_keys, $quote_char_values, $str);
	}

	/**
	 * Wrapper For strlen()
	 *
	 * @param string $str String.
	 * @return int String length.
	 */
	public static function length(string $str) : int {
		return (int) \mb_strlen($str, 'UTF-8');
	}

	/**
	 * Wrapper For str_pad()
	 *
	 * @param string $str String.
	 * @param int $pad_length Pad length.
	 * @param string $pad_string Pad string.
	 * @param int $pad_type Pad type.
	 * @return string String.
	 */
	public static function pad(string $str, int $pad_length, string $pad_string=' ', int $pad_type = 1) : string {
		$str = self::utf8($str);
		$pad_string = self::utf8($pad_string);

		$current_length = (int) \mb_strlen($str, 'UTF-8');
		$pad_string_length = (int) \mb_strlen($pad_string, 'UTF-8');
		$new_length = 0;

		if ($pad_length <= $current_length || ! $pad_string_length) {
			return $str;
		}

		// Pad left.
		if (\STR_PAD_LEFT === $pad_type) {
			$str = \str_repeat(
				$pad_string,
				\ceil(($pad_length - $current_length) / $pad_string_length)
			) . $str;
			$new_length = (int) \mb_strlen($str, 'UTF-8');
			if ($new_length > $pad_length) {
				$str = \mb_substr($str, $new_length - $pad_length, null, 'UTF-8');
			}
		}
		// Pad both.
		elseif (\STR_PAD_BOTH === $pad_type) {
			$leftright = 'right';
			while (\mb_strlen($str, 'UTF-8') < $pad_length) {
				$leftright = ('left' === $leftright) ? 'right' : 'left';
				if ('left' === $leftright) {
					$str = $pad_string . $str;
				}
				else {
					$str .= $pad_string;
				}
			}

			$new_length = (int) \mb_strlen($str, 'UTF-8');
			if ($new_length > $pad_length) {
				if ('left' === $leftright) {
					$str = \mb_substr($str, $new_length - $pad_length, null, 'UTF-8');
				}
				else {
					$str = \mb_substr($str, 0, $pad_length, 'UTF-8');
				}
			}
		}
		// Pad right.
		else {
			$str .= \str_repeat(
				$pad_string,
				\ceil(($pad_length - $current_length) / $pad_string_length)
			);
			$new_length = (int) \mb_strlen($str, 'UTF-8');
			if ($new_length > $pad_length) {
				$str = \mb_substr($str, 0, $pad_length, 'UTF-8');
			}
		}

		return $str;
	}

	/**
	 * Wrapper For str_split()
	 *
	 * @param string $str String.
	 * @param int $split_length Split length.
	 * @return bool True/false.
	 */
	public static function split(string $str, int $split_length=1) {
		if ($split_length < 1) {
			return false;
		}

		$str = self::utf8($str);

		$str_length = (int) \mb_strlen($str, 'UTF-8');
		$out = array();
		$x = 0;
		while ($x < $str_length) {
			$out[] = \mb_substr($str, $x, $split_length, 'UTF-8');
			$x += $split_length;
		}

		return $out;
	}

	/**
	 * Wrapper For strpos()
	 *
	 * @param string $haystack Haystack.
	 * @param string $needle Needle.
	 * @param int $offset Offset.
	 * @return int|bool First occurrence or false.
	 */
	public static function strpos(string $haystack, string $needle, int $offset=0) {
		return \mb_strpos($haystack, $needle, $offset, 'UTF-8');
	}

	/**
	 * Wrapper For strrev()
	 *
	 * @param string $str String.
	 * @return bool True/false.
	 */
	public static function strrev(string $str) : string {
		$str = self::utf8($str);

		if (! empty($str)) {
			$tmp = (array) self::split($str);
			$tmp = \array_reverse($tmp);
			return \implode('', $tmp);
		}

		return '';
	}

	/**
	 * Wrapper For strrpos()
	 *
	 * @param string $haystack Haystack.
	 * @param string $needle Needle.
	 * @param int $offset Offset.
	 * @return int|bool Last occurrence or false.
	 */
	public static function strrpos(string $haystack, string $needle, int $offset=0) {
		return \mb_strrpos($haystack, $needle, $offset, 'UTF-8');
	}

	/**
	 * Strtolower
	 *
	 * @param array|string $str String.
	 * @param int $flags Flags.
	 * @return string String.
	 */
	public static function toLower(string $str, int $flags=0) : string {
		// Proceed if we have a string, or don't care about type
		// conversion.
		$trusted = !! ($flags & Shim::TRUSTED);
		if (! $trusted) {
			$str = self::utf8($str);
		}

		if (! empty($str)) {
			if (! \mb_check_encoding($str, 'ASCII')) {
				// Hit the bulk of the conversion.
				$str = \mb_strtolower($str, 'UTF-8');

				// Replace some more.
				$str = \str_replace(
					self::$case_char_upper,
					self::$case_char_lower,
					$str
				);
			}
			else {
				$str = \strtolower($str);
			}
		}

		return $str;
	}

	/**
	 * Strtoupper
	 *
	 * @param array|string $str String.
	 * @param int $flags Flags.
	 */
	public static function toUpper(string $str, int $flags=0) : string {
		$trusted = !! ($flags & Shim::TRUSTED);
		if (! $trusted) {
			$str = self::utf8($str);
		}

		if (! empty($str)) {
			if (! \mb_check_encoding($str, 'ASCII')) {
				// Hit the bulk of the conversion.
				$str = \mb_strtoupper($str, 'UTF-8');

				// Replace some more.
				$str = \str_replace(
					self::$case_char_lower,
					self::$case_char_upper,
					$str
				);
			}
			else {
				$str = \strtoupper($str);
			}
		}

		return $str;
	}

	/**
	 * Wrapper For substr()
	 *
	 * @param string $str String.
	 * @param int $start Start.
	 * @param int $length Length.
	 * @return string String.
	 */
	public static function substr(string $str, int $start=0, $length=null) {
		return \mb_substr($str, $start, $length, 'UTF-8');
	}

	/**
	 * Wrapper For substr_count()
	 *
	 * @param string $haystack Haystack.
	 * @param string $needle Needle.
	 * @return int Count.
	 */
	public static function substrCount(string $haystack, string $needle) {
		return \mb_substr_count($haystack, $needle, 'UTF-8');
	}

	/**
	 * Trim
	 *
	 * @param string $str String.
	 * @param int $flags Flags.
	 * @return string String.
	 */
	public static function trim(string $str, int $flags=0) : string {
		$trusted = !! ($flags & Shim::TRUSTED);
		if (! $trusted) {
			$str = self::utf8($str);
		}

		return \preg_replace('/(^\s+|\s+$)/u', '', $str);
	}

	/**
	 * Wrapper For ucfirst()
	 *
	 * This will catch various case-able Unicode beyond the native PHP
	 * functions.
	 *
	 * @param string $str String.
	 * @param int $flags Flags.
	 * @return string String.
	 */
	public static function toSentence(string $str, int $flags=0) : string {
		$trusted = !! ($flags & Shim::TRUSTED);
		if (! $trusted) {
			$str = self::utf8($str);
		}

		if ($str) {
			if (! \mb_check_encoding($str, 'ASCII')) {
				$first = (string) \mb_substr($str, 0, 1, 'UTF-8');
				$first = self::toUpper($first, Shim::TRUSTED);
				$str = $first . \mb_substr($str, 1, null, 'UTF-8');
			}
			else {
				$str = \ucfirst($str);
			}
		}

		return $str;
	}

	/**
	 * Wrapper For ucwords()
	 *
	 * This will catch various case-able Unicode beyond the native PHP
	 * functions.
	 *
	 * @param string $str String.
	 * @param int $flags Flags.
	 * @return string String.
	 */
	public static function toTitle(string $str, int $flags=0) : string {
		$trusted = !! ($flags & Shim::TRUSTED);
		if (! $trusted) {
			$str = self::utf8($str);
		}

		if ($str) {
			// The first letter.
			$str = \preg_replace_callback(
				'/^(\p{L})/u',
				array(static::class, 'toTitleCallback1'),
				$str
			);

			// Any letter following a dash, space, or forward slash.
			$str = \preg_replace_callback(
				'/(\s|\p{Pd}|\/)(.)/u',
				array(static::class, 'toTitleCallback2'),
				$str
			);
		}

		return $str;
	}

	/**
	 * UCWords First Letter Callback
	 *
	 * @param array $matches Matches.
	 * @return string Replacement.
	 */
	private static function toTitleCallback1(array $matches) : string {
		return self::toUpper($matches[0]);
	}

	/**
	 * UCWords First Letter (after dash) Callback
	 *
	 * @param array $matches Matches.
	 * @return string Replacement.
	 */
	private static function toTitleCallback2(array $matches) : string {
		return $matches[1] . self::toUpper($matches[2]);
	}

	/**
	 * UTF-8
	 *
	 * Ensure string contains valid UTF-8 encoding.
	 *
	 * @see {https://github.com/neitanod/forceutf8}
	 *
	 * @param string $str String.
	 * @return string String.
	 */
	public static function utf8(string $str) : string {
		// Easy bypass.
		if (empty($str) || \is_numeric($str) || \mb_check_encoding($str, 'ASCII')) {
			return $str;
		}

		// Fix it up if we need to.
		$win1252_chars = array(
			128=>"\xe2\x82\xac", 130=>"\xe2\x80\x9a", 131=>"\xc6\x92",
			132=>"\xe2\x80\x9e", 133=>"\xe2\x80\xa6", 134=>"\xe2\x80\xa0",
			135=>"\xe2\x80\xa1", 136=>"\xcb\x86", 137=>"\xe2\x80\xb0",
			138=>"\xc5\xa0", 139=>"\xe2\x80\xb9", 140=>"\xc5\x92",
			142=>"\xc5\xbd", 145=>"\xe2\x80\x98", 146=>"\xe2\x80\x99",
			147=>"\xe2\x80\x9c", 148=>"\xe2\x80\x9d", 149=>"\xe2\x80\xa2",
			150=>"\xe2\x80\x93", 151=>"\xe2\x80\x94", 152=>"\xcb\x9c",
			153=>"\xe2\x84\xa2", 154=>"\xc5\xa1", 155=>"\xe2\x80\xba",
			156=>"\xc5\x93", 158=>"\xc5\xbe", 159=>"\xc5\xb8",
		);
		$length = (int) \mb_strlen($str, '8bit');
		$out = '';
		for ($x = 0; $x < $length; ++$x) {
			$c1 = $str[$x];

			// Should be converted to UTF-8 if not already.
			if ($c1 >= "\xc0") {
				$c2 = $x + 1 >= $length ? "\x00" : $str[$x + 1];
				$c3 = $x + 2 >= $length ? "\x00" : $str[$x + 2];
				$c4 = $x + 3 >= $length ? "\x00" : $str[$x + 3];

				// Probably 2-byte UTF-8.
				if ($c1 >= "\xc0" & $c1 <= "\xdf") {
					// Looks good.
					if ($c2 >= "\x80" && $c2 <= "\xbf") {
						$out .= $c1 . $c2;
						++$x;
					}
					// Invalid; convert it.
					else {
						$cc1 = (\chr(\ord($c1) / 64) | "\xc0");
						$cc2 = ($c1 & "\x3f") | "\x80";
						$out .= $cc1 . $cc2;
					}
				}
				// Probably 3-byte UTF-8.
				elseif ($c1 >= "\xe0" & $c1 <= "\xef") {
					// Looks good.
					if (
						$c2 >= "\x80" &&
						$c2 <= "\xbf" &&
						$c3 >= "\x80" &&
						$c3 <= "\xbf"
					) {
						$out .= $c1 . $c2 . $c3;
						$x += 2;
					}
					// Invalid; convert it.
					else {
						$cc1 = (\chr(\ord($c1) / 64) | "\xc0");
						$cc2 = ($c1 & "\x3f") | "\x80";
						$out .= $cc1 . $cc2;
					}
				}
				// Probably 4-byte UTF-8.
				elseif ($c1 >= "\xf0" & $c1 <= "\xf7") {
					// Looks good.
					if (
						$c2 >= "\x80" &&
						$c2 <= "\xbf" &&
						$c3 >= "\x80" &&
						$c3 <= "\xbf" &&
						$c4 >= "\x80" &&
						$c4 <= "\xbf"
					) {
						$out .= $c1 . $c2 . $c3 . $c4;
						$x += 3;
					}
					// Invalid; convert it.
					else {
						$cc1 = (\chr(\ord($c1) / 64) | "\xc0");
						$cc2 = ($c1 & "\x3f") | "\x80";
						$out .= $cc1 . $cc2;
					}
				}
				// Doesn't appear to be UTF-8; convert it.
				else {
					$cc1 = (\chr(\ord($c1) / 64) | "\xc0");
					$cc2 = (($c1 & "\x3f") | "\x80");
					$out .= $cc1 . $cc2;
				}
			}
			// Convert it.
			elseif (($c1 & "\xc0") === "\x80") {
				$o1 = \ord($c1);

				// Convert from Windows-1252.
				if (isset($win1252_chars[$o1])) {
					$out .= $win1252_chars[$o1];
				}
				else {
					$cc1 = (\chr($o1 / 64) | "\xc0");
					$cc2 = (($c1 & "\x3f") | "\x80");
					$out .= $cc1 . $cc2;
				}
			}
			// No change.
			else {
				$out .= $c1;
			}
		}

		// If it seems valid, return it, otherwise empty($it) out.
		return (1 === \preg_match('/^./us', $out)) ? $out : '';
	}

	/**
	 * UTF-8 Recursive
	 *
	 * Virtually every method that uses UTF-8 sanitizing is itself
	 * recursive, so to reduce overhead, the main method accepts only
	 * string data.
	 *
	 * This method exists for the odd cases.
	 *
	 * @param mixed $value Value.
	 * @return mixed Value.
	 */
	public static function utf8Recursive($value) {
		// Recurse.
		if ('array' === \gettype($value)) {
			foreach ($value as $k=>$v) {
				$value[$k] = self::utf8Recursive($v);
			}
			return $value;
		}
		elseif ('string' === \gettype($value)) {
			return self::utf8($value);
		}
		return $value;
	}

	/**
	 * Whitespace
	 *
	 * @param array|string $str String.
	 * @param int $newlines Newlines.
	 * @param int $flags Flags.
	 * @return array|string String.
	 */
	public static function whitespace(string $str, int $newlines=0, int $flags=0) : string {
		$trusted = !! ($flags & Shim::TRUSTED);
		if (! $trusted) {
			$str = self::utf8($str);
		}

		// If we aren't allowing new lines at all, we can do this
		// quickly.
		if ($newlines <= 0) {
			return \trim(\preg_replace('/\s+/u', ' ', $str));
		}

		// Convert different types of whitespace.
		$str = \str_replace("\r\n", "\n", $str);

		// Go through line by line.
		$lines = (array) \preg_split('/\v/u', $str);
		foreach ($lines as $k=>$v) {
			$lines[$k] = \trim(\preg_replace('/\s+/u', ' ', $v));
		}
		$str = (string) \implode("\n", $lines);
		$str = self::trim($str, Shim::TRUSTED);

		// Cap newlines.
		$str = \preg_replace(
			"/\n{" . ($newlines + 1) . ',}/',
			\str_repeat("\n", $newlines),
			$str
		);

		return $str;
	}

	/**
	 * Wrapper for wordwrap()
	 *
	 * Wrap text to specified line length. Unlike PHP's version, this
	 * will preferentially break long strings on any hypens or dashes
	 * they might have.
	 *
	 * @param string $str String.
	 * @param int $width Width.
	 * @param string $eol Break.
	 * @param bool $cut Cut.
	 * @return string String.
	 */
	public static function wordwrap(string $str, int $width=75, string $eol="\n", bool $cut=false) : string {

		$str = self::utf8($str);
		$eol = self::utf8($eol);

		// Bad data?
		if (empty($str) || $width <= 0) {
			return $str;
		}

		// First, split on horizontal whitespace.
		$chunks = (array) \preg_split(
			'/([\s$]+)/uS',
			\trim($str),
			-1,
			\PREG_SPLIT_DELIM_CAPTURE
		);

		$lines = array();
		$out = array();
		$breakLength = (int) \mb_strlen($eol, 'UTF-8');
		$lineLength = 0;
		$wordLength = 0;
		$line = '';
		$preg_eol = (string) \preg_quote($eol, '/');

		// Loop through chunks.
		foreach ($chunks as $v) {
			// Always start a new line with vertical whitespace.
			if (\preg_match('/\v/u', $v)) {
				$lines[] = $line;
				$lines[] = $v;
				$line = '';
				continue;
			}

			// Always append horizontal whitespace.
			if (\preg_match('/\h/u', $v)) {
				$line .= $v;
				continue;
			}

			// Start a new line?
			$lineLength = (int) \mb_strlen($line, 'UTF-8');

			if ($lineLength >= $width) {
				$lines[] = $line;
				$line = '';
				$lineLength = 0;
			}

			$wordLength = (int) \mb_strlen($v, 'UTF-8');

			// We can just add it.
			if ($wordLength + $lineLength <= $width) {
				$line .= $v;
				continue;
			}

			// We should make sure each chunk fits.
			if ($cut) {
				$v = self::split($v, $width);
				$v = \implode("\n", $v);
			}

			// Is this word hyphenated or dashed?
			$v = \preg_replace("/(\p{Pd})\n/u", '$1', $v);
			$v = \preg_replace('/(\p{Pd}+)/u', "$1\n", $v);
			$v = self::trim($v, Shim::TRUSTED);

			// Loop through word chunks to see what fits where.
			$tmp = (array) \explode("\n", $v);
			foreach ($tmp as $v2) {
				$lineLength = (int) \mb_strlen($line, 'UTF-8');
				$wordLength = (int) \mb_strlen($v2, 'UTF-8');

				// New line?
				if ($wordLength + $lineLength > $width) {
					$lines[] = $line;
					$line = '';
				}

				$line .= $v2;
			}
		}

		// Just in case anything was missed.
		if (! empty($line)) {
			$lines[] = $line;
		}

		// Okay, let's trim our lines real quick.
		foreach ($lines as $v) {
			// Ignore vertical space, unless it matches the breaker.
			if (! empty($preg_eol) && \preg_match('/\v/u', $v)) {
				// Don't need to double it.
				if ($v === $eol) {
					continue;
				}
				elseif (1 === $breakLength) {
					$out[] = self::trim(\ltrim($v, $eol), Shim::TRUSTED);
				}
				else {
					$out[] = self::trim(\preg_replace(
						'/^' . $preg_eol . '/ui',
						'',
						$v
					), Shim::TRUSTED);
				}

				continue;
			}

			$out[] = self::trim($v, Shim::TRUSTED);
		}

		// Finally, join our lines by the delimiter.
		return self::trim(\implode($eol, $out), Shim::TRUSTED);
	}

	/**
	 * Yes/No
	 *
	 * Convert a mixed var into a Yes or No response based on its
	 * evaluation.
	 *
	 * @param mixed $value Value.
	 * @param string $yes Yes.
	 * @param string $no No.
	 * @return string Yes/No.
	 */
	public static function toYesNo($value, string $yes='Yes', string $no='No') : string {
		return $value ? $yes : $no;
	}



	// -----------------------------------------------------------------
	// Helpers
	// -----------------------------------------------------------------

	/**
	 * In Range
	 *
	 * Check if the value of a string falls between two points.
	 *
	 * @param string $str String.
	 * @param mixed $min Min.
	 * @param mixed $max Max.
	 * @return bool True/false.
	 */
	public static function inRange(string $str, $min=null, $max=null) : bool {
		$minString = !! (('string' === \gettype($min)) && ! empty($min));
		$maxString = !! (('string' === \gettype($max)) && ! empty($max));

		// Make sure they're in the right order.
		if (
			$minString &&
			$maxString &&
			$min > $max
		) {
			$tmp = $min;
			$min = (string) $max;
			$max = (string) $tmp;
		}

		if ($minString && $str < $min) {
			return false;
		}

		if ($maxString && $str > $max) {
			return false;
		}

		return true;
	}

	/**
	 * Length In Range
	 *
	 * Check if the length of a string is between two values.
	 *
	 * @param string $str String.
	 * @param mixed $min Min.
	 * @param mixed $max Max.
	 * @return bool True/false.
	 */
	public static function lengthInRange(string $str, $min=null, $max=null) : bool {
		$length = (int) \mb_strlen($str, 'UTF-8');
		return \Blobfolio\Numbers::inRange($length, $min, $max);
	}

	/**
	 * To Range
	 *
	 * @param string $str String.
	 * @param mixed $min Min.
	 * @param mixed $max Max.
	 * @return string String.
	 */
	public static function toRange(string $str, $min=null, $max=null) : string {
		$minString = !! (('string' === \gettype($min)) && ! empty($min));
		$maxString = !! (('string' === \gettype($max)) && ! empty($max));

		// Make sure they're in the right order.
		if (
			$minString &&
			$maxString &&
			$min > $max
		) {
			$tmp = $min;
			$min = (string) $max;
			$max = (string) $tmp;
		}

		if ($minString && $str < $min) {
			$str = $min;
		}

		if ($maxString && $str > $max) {
			$str = $max;
		}

		return $str;
	}

	/**
	 * Is Value Valid UTF-8?
	 *
	 * @param string $str String.
	 * @return bool True/false.
	 */
	public static function isUtf8($str) : bool {
		if (\is_numeric($str) || \is_bool($str)) {
			return true;
		}

		if (\is_string($str)) {
			return (bool) \preg_match('//u', $str);
		}

		return false;
	}

	/**
	 * Get Random String
	 *
	 * @param int $length Length.
	 * @param string $custom Alternate alphabet.
	 * @return string Random string.
	 */
	public static function random(int $length=10, string $custom='') : string {
		if ($length < 1) {
			return '';
		}

		// Build a custom soup.
		if (! empty($custom)) {
			$custom = self::printable($custom);
			$custom = \preg_replace('/\s/', '', $custom);
			$soup = (array) self::split($custom);
			$soup = \array_unique($soup);
			$soup = \array_values($soup);

			$max = \count($soup) - 1;
			if ($max < 0) {
				return '';
			}
			elseif (! $max) {
				// One does not random make.
				return \str_repeat($soup[0], $length);
			}
		}
		else {
			$soup = array(
				'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'J', 'K', 'L',
				'M', 'N', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X',
				'Y', 'Z', '2', '3', '4', '5', '6', '7', '8', '9',
			);

			$max = 31;
		}

		$out = '';
		$x = 0;

		while ($x < $length) {
			$index = (int) \random_int(0, $max);
			$out .= $soup[$index];
			++$x;
		}

		return $out;
	}
}
