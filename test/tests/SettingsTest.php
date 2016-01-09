<?php

class SettingsTest extends PurgelyBase {
	public function setUp() {
		parent::setUp();
		\WP_Mock::setUp();
	}

	public function tearDown() {
		\WP_Mock::tearDown();
	}

	public function test_get_settings_returns_correct_object() {
		$settings = Purgely_Settings::get_registered_settings();
		$this->assertTrue( is_array( $settings ) );

		foreach ( $settings as $key => $setting ) {
			$this->assertTrue( is_string( $key ) );
			$this->assertTrue( isset( $setting['sanitize_callback'] ) );
			$this->assertTrue( isset( $setting['default'] ) );
		}
	}

	public function test_database_value_is_preferred() {
		\WP_Mock::wpFunction( 'get_option', array(
			'args'   => array(
				'purgely-settings',
				array()
			),
			'times'  => 1,
			'return' => array_merge( $this->get_defaults_in_option_form(), array(
				'fastly_key' => 'test' // Override the "fastly_key" value
			) )
		) );

		$settings = Purgely_Settings::get_settings();
		$this->assertEquals( 'test', $settings['fastly_key'] );
	}

	public function test_constants_are_used_when_database_options_are_empty() {
		\WP_Mock::wpFunction( 'get_option', array(
			'args'   => array(
				'purgely-settings',
				array()
			),
			'times'  => 1,
			'return' => array()
		) );

		$settings = Purgely_Settings::get_settings();
		$this->assertEquals( PURGELY_FASTLY_KEY, $settings['fastly_key'] );
	}

	public function test_database_value_is_preferred_when_getting_individual_setting() {
		\WP_Mock::wpFunction( 'get_option', array(
			'args'   => array(
				'purgely-settings',
				array()
			),
			'times'  => 1,
			'return' => array_merge( $this->get_defaults_in_option_form(), array(
				'fastly_key' => 'test' // Override the "fastly_key" value
			) )
		) );

		$fastly_key = Purgely_Settings::get_setting( 'fastly_key' );
		$this->assertEquals( 'test', $fastly_key );
	}

	public function test_constants_are_used_when_database_options_are_empty_when_getting_individual_setting() {
		\WP_Mock::wpFunction( 'get_option', array(
			'args'   => array(
				'purgely-settings',
				array()
			),
			'times'  => 1,
			'return' => array()
		) );

		$fastly_key = Purgely_Settings::get_setting( 'fastly_key' );
		$this->assertEquals( PURGELY_FASTLY_KEY, $fastly_key );
	}

	private function get_defaults_in_option_form() {
		return array(
			'fastly_key'                    => '',
			'fastly_service_id'             => '',
			'allow_purge_all'               => false,
			'api_endpoint'                  => 'https://api.fastly.com/',
			'enable_stale_while_revalidate' => true,
			'stale_while_revalidate_ttl'    => 60 * 60 * 24,
			'enable_stale_if_error'         => true,
			'stale_if_error_ttl'            => 60 * 60 * 24,
			'surrogate_control_ttl'         => 300,
			'default_purge_type'            => 'soft',
		);
	}
}