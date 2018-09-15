<?php
/**
 * Class BehaviorTests
 *
 * @package blob-common
 */

/**
 * Test functions-behavior.php.
 */
class BehaviorTests extends WP_UnitTestCase {

	const ASSETS = __DIR__ . '/assets/';

	/**
	 * Cron Schedules
	 *
	 * @return void Nothing.
	 */
	function test_common_cron_schedules() {
		$thing = \wp_get_schedules();

		$this->assertEquals(true, \array_key_exists('oneminute', $thing));
		$this->assertEquals(true, \array_key_exists('twominutes', $thing));
		$this->assertEquals(true, \array_key_exists('fiveminutes', $thing));
		$this->assertEquals(true, \array_key_exists('tenminutes', $thing));
		$this->assertEquals(true, \array_key_exists('halfhour', $thing));
	}

	/**
	 * Upload MIMEs
	 *
	 * @return void Nothing.
	 */
	function test_common_upload_mimes() {
		$mimes = \get_allowed_mime_types();

		$this->assertEquals(true, \array_key_exists('svg', $mimes));
		$this->assertEquals(true, \array_key_exists('webp', $mimes));
	}

	/**
	 * Upload Real MIMEs
	 *
	 * @return void Nothing.
	 */
	function test_common_upload_real_mimes() {
		$svg = \file_get_contents(static::ASSETS . 'monogram.svg');

		// Save a copy.
		$upload = static::ASSETS . 'tmpfile';
		@\file_put_contents($upload, $svg);

		$checked = \apply_filters(
			'wp_check_filetype_and_ext',
			array(
				'type'=>false,
				'ext'=>false,
				'proper_filename'=>'',
			),
			$upload,
			'monogram.svg',
			\get_allowed_mime_types()
		);

		$this->assertEquals('image/svg+xml', $checked['type']);
		$this->assertEquals('svg', $checked['ext']);
		$this->assertEquals('monogram.svg', $checked['proper_filename']);

		// Remove test file.
		@\unlink($upload);
	}
}
