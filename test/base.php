<?php

class PurgelyBase extends PHPUnit_Framework_TestCase {
	public function setUp() {
		// Ensure that the settings are in a default state
		Purgely_Settings::set_settings( array() );
	}
}