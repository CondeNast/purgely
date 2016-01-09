<?php

class HeaderCacheControlTest extends PurgelyBase {
	public function setUp() {
		parent::setUp();

		// We are not testing escaping functions so let them passthrough
		\WP_Mock::wpPassthruFunction( 'absint' );
		\WP_Mock::wpPassthruFunction( 'esc_url' );
	}

	public function test_object_is_constructed_correctly() {
		$seconds   = 10;
		$directive = 'stale-while-revalidate';
		$header    = 'Cache-Control';

		// Mock the remote request
		\WP_Mock::wpFunction( 'sanitize_key', array(
			'args'   => $directive,
			'times'  => 1,
			'return' => $directive,
		) );

		$object =  new Purgely_Cache_Control_Header( $seconds, $directive );

		// Ensure that all properties are set correctly
		$this->assertEquals( $seconds, $object->get_seconds() );
		$this->assertEquals( $directive, $object->get_directive() );
		$this->assertEquals( $header, $object->get_header_name() );
		$this->assertEquals( $directive . '=' . $seconds, $object->get_value() );
	}

	/**
	 * Must run in separate process to avoid headers already sent errors.
	 *
	 * @runInSeparateProcess
	 */
	public function test_that_header_is_sent() {
		$seconds   = 10;
		$directive = 'stale-while-revalidate';

		// Mock the remote request
		\WP_Mock::wpFunction( 'sanitize_key', array(
			'args'   => $directive,
			'times'  => 1,
			'return' => $directive,
		) );

		$object =  new Purgely_Cache_Control_Header( $seconds, $directive );

		// Ensure that all properties are set correctly
		$object->send_header();

		// Get the headers that were sent
		if ( function_exists( 'xdebug_get_headers' ) ) {
			$this->assertEquals( array( 'Cache-Control: ' . $directive . '=' . $seconds ), xdebug_get_headers() );
		}
	}
}