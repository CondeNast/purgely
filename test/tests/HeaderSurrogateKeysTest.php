<?php

class HeaderSurrogateKeysTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		\WP_Mock::setUp();

		// Mock the remote request
		\WP_Mock::wpPassthruFunction( 'absint' );
	}

	public function test_object_is_constructed_correctly() {
		$header = 'Surrogate-Key';
		$object =  new Purgely_Surrogate_Keys_Header();

		// Ensure that all properties are set correctly
		$this->assertEquals( $header, $object->get_header_name() );
	}

	public function test_keys_are_set() {
		$object =  new Purgely_Surrogate_Keys_Header();
		$keys   = array(
			'key-1',
			'key-2',
		);

		$object->set_keys( $keys );

		$this->assertEquals( $keys, $object->get_keys() );
	}

	public function test_keys_are_sanitized_correctly() {
		$object = new Purgely_Surrogate_Keys_Header();
		$this->assertEquals( 'key-1', $object->sanitize_key( 'key-1' ) );
		$this->assertEquals( 'KEY-1', $object->sanitize_key( 'KEY-1' ) );
		$this->assertEquals( 'key1', $object->sanitize_key( 'key 1' ) );
		$this->assertEquals( 'key1', $object->sanitize_key( 'key !@#$%^&*()+=~`<>?:"{}|\ 1' ) );
	}

	public function test_keys_being_prepared_turns_them_to_string() {
		$object = new Purgely_Surrogate_Keys_Header();
		$keys   = array(
			'key-1',
			'key-2',
		);

		$keys_string = 'key-1 key-2';

		$this->assertEquals( $keys_string, $object->prepare_keys( $keys ) );
	}

	public function test_keys_can_be_added() {
		$object = new Purgely_Surrogate_Keys_Header();

		$object->add_key( 'key-1' );
		$object->add_key( 'key-2' );

		$this->assertEquals( array( 'key-1', 'key-2' ), $object->get_keys() );
	}

	public function test_keys_setting_all_keys() {
		$object = new Purgely_Surrogate_Keys_Header();
		$keys   = array(
			'key-1',
			'key-2',
		);

		$object->set_keys( $keys );

		$this->assertEquals( $keys, $object->get_keys() );
	}

	/**
	 * Must run in separate process to avoid headers already sent errors.
	 *
	 * @runInSeparateProcess
	 */
	public function test_keys_are_send_correctly() {
		$object = new Purgely_Surrogate_Keys_Header();
		$keys   = array(
			'key-1',
			'key-2',
		);

		$keys_string = 'key-1 key-2';

		$object->set_keys( $keys );

		// Ensure that all properties are set correctly
		$object->send_header();

		// Get the headers that were sent
		$this->assertEquals( $keys_string, $object->get_value() );

		if ( function_exists( 'xdebug_get_headers' ) ) {
			$this->assertEquals( array( 'Surrogate-Key: ' . $keys_string ), xdebug_get_headers() );
		}
	}

	public function tearDown() {
		\WP_Mock::tearDown();
	}
}