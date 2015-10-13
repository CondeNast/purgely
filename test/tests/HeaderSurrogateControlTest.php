<?php

class HeaderSurrogateControlTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		\WP_Mock::setUp();

		// Mock the remote request
		\WP_Mock::wpPassthruFunction( 'absint' );
	}

	public function test_object_is_constructed_correctly() {
		$seconds = 10;
		$header  = 'Surrogate-Control';

		$object =  new Purgely_Surrogate_Control_Header( $seconds );

		// Ensure that all properties are set correctly
		$this->assertEquals( $seconds, $object->get_seconds() );
		$this->assertEquals( $header, $object->get_header_name() );
		$this->assertEquals( 'max-age=' . $seconds, $object->get_value() );
	}

	/**
	 * Must run in separate process to avoid headers already sent errors.
	 *
	 * @runInSeparateProcess
	 */
	public function test_that_header_is_sent() {
		$seconds = 10;

		$object = new Purgely_Surrogate_Control_Header( $seconds );

		// Ensure that all properties are set correctly
		$object->send_header();

		// Get the headers that were sent
		$this->assertEquals( array( 'Surrogate-Control: max-age=' . $seconds ), xdebug_get_headers() );
	}

	public function tearDown() {
		\WP_Mock::tearDown();
	}
}