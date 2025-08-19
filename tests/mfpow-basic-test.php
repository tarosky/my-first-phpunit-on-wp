<?php
/**
 * Function test
 */

/**
 * Sample test case.
 */
class Mfpow_Basic_Test extends WP_UnitTestCase {

	/**
	 * Test functions
	 */
	function test_hello() {
		// Check function.
		$this->assertEquals( mfpow_hello_world(), 'Hello, WordPress Unit Testing!' );
	}
}
