<?php
/**
 * Class FormTests
 *
 * @package blob-common
 */

/**
 * Test functions-form.php.
 */
class FormTests extends WP_UnitTestCase {

	/**
	 * Get Form Timestamp
	 *
	 * @return void Nothing.
	 */
	function test_common_get_form_timestamp() {
		$timestamp = common_get_form_timestamp();
		$this->assertEquals(true, preg_match('/^\d+,[a-f\d]{32}$/', $timestamp));

		$timestamp = common_generate_form_timestamp();
		$this->assertEquals(true, preg_match('/^\d+,[a-f\d]{32}$/', $timestamp));

		sleep(1);
		$this->assertEquals(true, common_check_form_timestamp($timestamp, 1));
		$this->assertEquals(true, common_verify_form_timestamp($timestamp, 1));
		$this->assertEquals(false, common_check_form_timestamp($timestamp, 10));
	}
}
