<?php
// @codingStandardsIgnoreFile
/**
 * Type Handling - By Reference
 *
 * Functions for typecasting and type detection. This extends
 * the cast_base class. For PHP7 users, additional functions
 * named after the types are added.
 *
 * @package blobfolio/common
 * @author	Blobfolio, LLC <hello@blobfolio.com>
 */

namespace blobfolio\common\ref;

// Unfortunately PHP < 7 prohibits the use of reserved words
// in method names, and we can't just use the Magic overloader
// like we did with our by-value version since Magic/Reference
// is a no-go.
if (version_compare(PHP_VERSION, '7.0.0') >= 0) {
	class cast extends cast_full {

	}
}
else {
	class cast extends cast_base {

	}
}
