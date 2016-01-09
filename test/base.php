<?php

class PurgelyBase extends PHPUnit_Framework_TestCase {
	public function setUp() {
		\WP_Mock::setUp();

		// Ensure that the settings are in a default state
		Purgely_Settings::set_settings( array() );
	}

	public function tearDown() {
		\WP_Mock::tearDown();
	}
}