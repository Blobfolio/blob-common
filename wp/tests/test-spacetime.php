<?php
/**
 * Class SpacetimeTests
 *
 * @package blob-common
 */

/**
 * Test functions-spacetime.php.
 */
class SpacetimeTests extends WP_UnitTestCase {

	const ASSETS = __DIR__ . '/assets/';

	/**
	 * Get US States
	 *
	 * @return void Nothing.
	 */
	function test_common_get_us_states() {
		$thing = common_get_us_states();
		$this->assertEquals(true, array_key_exists('PR', $thing));
		$this->assertEquals(true, in_array('TEXAS', $thing, true));

		$thing = common_get_us_states(false, false);
		$this->assertEquals(false, array_key_exists('PR', $thing));
		$this->assertEquals(false, in_array('TEXAS', $thing, true));
	}

	/**
	 * Get CA Provinces
	 *
	 * @return void Nothing.
	 */
	function test_common_get_ca_provinces() {
		$thing = common_get_ca_provinces();
		$this->assertEquals(true, array_key_exists('AB', $thing));
		$this->assertEquals(true, in_array('ALBERTA', $thing, true));

		$thing = common_get_ca_provinces(false);
		$this->assertEquals(false, in_array('ALBERTA', $thing, true));
	}

	/**
	 * Get Countries
	 *
	 * @return void Nothing.
	 */
	function test_common_get_countries() {
		$thing = common_get_countries();
		$this->assertEquals(true, in_array('Canada', $thing, true));

		$thing = common_get_countries(true);
		$this->assertEquals(true, in_array('CANADA', $thing, true));
	}

	/**
	 * Get Data URI
	 *
	 * @return void Nothing.
	 */
	function test_common_get_data_uri() {
		$svg = self::ASSETS . 'monogram.svg';
		$data = common_get_data_uri($svg);

		$this->assertEquals(true, false !== strpos($data, 'image/svg+xml'));
		$this->assertEquals(false, common_get_data_uri('does_not_exist.txt'));
	}

	/**
	 * Get MIME Type
	 *
	 * @return void Nothing.
	 */
	function test_common_get_mime_type() {
		$svg = self::ASSETS . 'monogram.svg';

		$this->assertEquals('image/svg+xml', common_get_mime_type($svg));
	}

	/**
	 * IP to Number
	 *
	 * @return void Nothing.
	 */
	function test_common_ip_to_number() {
		$thing = '50.116.18.174';
		$this->assertEquals(846467758, common_ip_to_number($thing));

		$thing = '2600:3c00::f03c:91ff:feae:0ff2';
		$this->assertEquals(50511880784403022287880976722111107058, common_ip_to_number($thing));
	}

	/**
	 * CIDR to Range
	 *
	 * @return void Nothing.
	 */
	function test_common_cidr_to_range() {
		$thing = '50.116.18.174/24';
		$match = array('min'=>'50.116.18.0', 'max'=>'50.116.18.255');
		$this->assertEquals($match, common_cidr_to_range($thing));

		$thing = '2600:3c00::f03c:91ff:feae:0ff2/64';
		$match = array('min'=>'2600:3c00::', 'max'=>'2600:3c00::ffff:ffff:ffff:ffff');
		$this->assertEquals($match, common_cidr_to_range($thing));
	}

	/**
	 * Is Dir Empty
	 *
	 * @return void Nothing.
	 */
	function test_common_is_empty_dir() {
		$this->assertEquals(false, common_is_empty_dir(self::ASSETS));

		$new = self::ASSETS . 'empty';
		mkdir($new);
		$this->assertEquals(true, common_is_empty_dir($new));
		rmdir($new);
	}

	/**
	 * Common Get Upload Path
	 *
	 * @return void Nothing.
	 */
	function test_common_upload_path() {
		$thing = common_upload_path('foobar');

		$this->assertEquals(true, false !== strpos($thing, 'uploads/foobar'));
	}

	/**
	 * Common Theme Path
	 *
	 * @return void Nothing.
	 */
	function test_common_theme_path() {
		$url = trailingslashit(get_stylesheet_directory_uri());
		if (!filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED)) {
			$this->markTestSkipped('The site cannot be locally hosted.');
		}
		else {
			$thing = common_theme_path('foobar');
			$this->assertEquals(true, false !== strpos($thing, 'themes/'));
			$this->assertEquals(true, false !== strpos($thing, '/foobar'));
		}
	}

	/**
	 * Datediff
	 *
	 * @return void Nothing.
	 */
	function test_common_datediff() {
		$date1 = '2015-01-15';
		$date2 = '2015-01-17';

		$this->assertEquals(2, common_datediff($date1, $date2));
		$this->assertEquals(2, common_datediff($date2, $date1));
		$this->assertEquals(2, common_datediff(strtotime($date2), $date1));
	}

	/**
	 * To/From Blogtime
	 *
	 * @return void Nothing.
	 */
	function test_common_blogtime() {
		$blogtime = common_get_blog_timezone();
		$othertime = 'America/Los_Angeles' === $blogtime ? 'America/Chicago' : 'America/Los_Angeles';
		$date = '2015-01-01 10:10:10';

		$transformed = common_from_blogtime($date, $othertime);
		$this->assertNotEquals($date, $transformed);

		$transformed = common_to_blogtime($transformed, $othertime);
		$this->assertEquals($date, $transformed);
	}

	/**
	 * Common Get Path By URL
	 *
	 * @return void Nothing.
	 */
	function test_common_get_path_by_url() {
		$thing = site_url('tmp.php');
		$result = common_get_path_by_url($thing);
		$this->assertNotEquals(false, $result);
		$this->assertEquals(0, strpos($thing, ABSPATH));
	}

	/**
	 * Common Get URL By PATH
	 *
	 * @return void Nothing.
	 */
	function test_common_get_url_by_path() {
		$thing = trailingslashit(ABSPATH) . 'tmp.php';
		$result = common_get_url_by_path($thing);
		$this->assertNotEquals(false, $result);
		$this->assertEquals(0, strpos($thing, site_url()));
	}
}
