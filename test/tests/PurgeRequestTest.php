<?php

class PurgeRequestTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		\WP_Mock::setUp();
	}

	public function test_successful_purge_request_for_individual_url() {
		$url             = 'http://www.example.org/2015/05/test-post';
		$expected_result = MockData::purge_url_response_200();

		// Mock the remote request
		\WP_Mock::wpFunction( 'wp_remote_request', array(
			'args'   => array(
				$url,
				array(
					'method' => 'PURGE',
				)
			),
			'times'  => 1,
			'return' => MockData::purge_url_response_200(),
		) );

		$purge         = new Purgely_Purge();
		$actual_result = $purge->purge( 'url', $url );

		$this->assertEquals( $expected_result, $actual_result );
		$this->assertEquals( $expected_result, $purge->get_response() );

		// The request is successful so ensure is_wp_error returns false
		\WP_Mock::wpFunction( 'is_wp_error', array(
			'args'   => array( $expected_result ),
			'times'  => 1,
			'return' => false,
		) );

		\WP_Mock::wpFunction( 'wp_remote_retrieve_response_code', array(
			'args'   => array( $expected_result ),
			'times'  => 1,
			'return' => '200',
		) );

		$this->assertEquals( 'success', $purge->get_result() );
	}

	public function test_successful_purge_request_for_individual_url_with_soft_purge() {
		$url             = 'http://www.example.org/2015/05/test-post';
		$expected_result = MockData::purge_url_response_200();

		// Mock the remote request
		\WP_Mock::wpFunction( 'wp_remote_request', array(
			'args'   => array(
				$url,
				array(
					'method'  => 'PURGE',
					'headers' => array(
						'Fastly-Soft-Purge' => 1,
					),
				),
			),
			'times'  => 1,
			'return' => MockData::purge_url_response_200(),
		) );

		$purge         = new Purgely_Purge();
		$actual_result = $purge->purge( 'url', $url, array( 'soft-purge' => true ) );

		$this->assertEquals( $expected_result, $actual_result );
		$this->assertEquals( $expected_result, $purge->get_response() );

		// The request is successful so ensure is_wp_error returns false
		\WP_Mock::wpFunction( 'is_wp_error', array(
			'args'   => array( $expected_result ),
			'times'  => 1,
			'return' => false,
		) );

		\WP_Mock::wpFunction( 'wp_remote_retrieve_response_code', array(
			'args'   => array( $expected_result ),
			'times'  => 1,
			'return' => '200',
		) );

		$this->assertEquals( 'success', $purge->get_result() );
	}

	public function test_failing_purge_request_for_individual_url() {
		$url             = 'http://www.example.org/2015/05/test-post';
		$expected_result = MockData::purge_url_response_405();

		// Mock the remote request
		\WP_Mock::wpFunction( 'wp_remote_request', array(
			'args'   => array(
				$url,
				array(
					'method' => 'PURGE',
				)
			),
			'times'  => 1,
			'return' => MockData::purge_url_response_405(),
		) );

		$purge         = new Purgely_Purge();
		$actual_result = $purge->purge( 'url', $url );

		$this->assertEquals( $expected_result, $actual_result );
		$this->assertEquals( $expected_result, $purge->get_response() );

		// The request is successful so ensure is_wp_error returns false
		\WP_Mock::wpFunction( 'is_wp_error', array(
			'args'   => array( $expected_result ),
			'times'  => 1,
			'return' => false,
		) );

		\WP_Mock::wpFunction( 'wp_remote_retrieve_response_code', array(
			'args'   => array( $expected_result ),
			'times'  => 1,
			'return' => '405',
		) );

		$this->assertEquals( 'failure', $purge->get_result() );
	}

	public function test_successful_purge_request_for_key() {
		$key             = 'test-key';
		$expected_result = MockData::purge_key_response_200();
		$request_url     = PURGELY_API_ENDPOINT . '/service/' . PURGELY_FASTLY_SERVICE_ID . '/purge/test-key';

		// Mock the remote request
		\WP_Mock::wpFunction( 'wp_remote_request', array(
			'args'   => array(
				$request_url,
				array(
					'method'  => 'POST',
					'headers' => array(
						'Fastly-Key' => PURGELY_FASTLY_KEY,
					),
				),
			),
			'times'  => 1,
			'return' => $expected_result,
		) );

		\WP_Mock::wpFunction( 'trailingslashit', array(
			'args'   => array( PURGELY_API_ENDPOINT ),
			'times'  => 1,
			'return' => PURGELY_API_ENDPOINT . '/',
		) );

		$purge         = new Purgely_Purge();
		$actual_result = $purge->purge( 'surrogate-key', $key );

		$this->assertEquals( $expected_result, $actual_result );
		$this->assertEquals( $expected_result, $purge->get_response() );

		// The request is successful so ensure is_wp_error returns false
		\WP_Mock::wpFunction( 'is_wp_error', array(
			'args'   => array( $expected_result ),
			'times'  => 1,
			'return' => false,
		) );

		\WP_Mock::wpFunction( 'wp_remote_retrieve_response_code', array(
			'args'   => array( $expected_result ),
			'times'  => 1,
			'return' => '200',
		) );

		$this->assertEquals( 'success', $purge->get_result() );
	}

	public function test_failing_purge_request_for_key() {
		$key             = 'test-key';
		$expected_result = MockData::purge_key_response_405();
		$request_url     = PURGELY_API_ENDPOINT . '/service/' . PURGELY_FASTLY_SERVICE_ID . '/purge/test-key';

		// Mock the remote request
		\WP_Mock::wpFunction( 'wp_remote_request', array(
			'args'   => array(
				$request_url,
				array(
					'method'  => 'POST',
					'headers' => array(
						'Fastly-Key' => PURGELY_FASTLY_KEY,
					),
				),
			),
			'times'  => 1,
			'return' => $expected_result,
		) );

		\WP_Mock::wpFunction( 'trailingslashit', array(
			'args'   => array( PURGELY_API_ENDPOINT ),
			'times'  => 1,
			'return' => PURGELY_API_ENDPOINT . '/',
		) );

		$purge         = new Purgely_Purge();
		$actual_result = $purge->purge( 'surrogate-key', $key );

		$this->assertEquals( $expected_result, $actual_result );
		$this->assertEquals( $expected_result, $purge->get_response() );

		// The request is successful so ensure is_wp_error returns false
		\WP_Mock::wpFunction( 'is_wp_error', array(
			'args'   => array( $expected_result ),
			'times'  => 1,
			'return' => false,
		) );

		\WP_Mock::wpFunction( 'wp_remote_retrieve_response_code', array(
			'args'   => array( $expected_result ),
			'times'  => 1,
			'return' => '405',
		) );

		$this->assertEquals( 'failure', $purge->get_result() );
	}

	public function test_successful_purge_request_for_all_items() {
		$expected_result = MockData::purge_all_response_200();
		$request_url     = PURGELY_API_ENDPOINT . '/service/' . PURGELY_FASTLY_SERVICE_ID . '/purge_all';

		// Mock the remote request
		\WP_Mock::wpFunction( 'wp_remote_request', array(
			'args'   => array(
				$request_url,
				array(
					'method'  => 'POST',
					'headers' => array(
						'Fastly-Key' => PURGELY_FASTLY_KEY,
					),
				),
			),
			'times'  => 1,
			'return' => $expected_result,
		) );

		\WP_Mock::wpFunction( 'trailingslashit', array(
			'args'   => array( PURGELY_API_ENDPOINT ),
			'times'  => 1,
			'return' => PURGELY_API_ENDPOINT . '/',
		) );

		$purge         = new Purgely_Purge();
		$actual_result = $purge->purge( 'all', '', array( 'allow-all' => true ) );

		$this->assertEquals( $expected_result, $actual_result );
		$this->assertEquals( $expected_result, $purge->get_response() );

		// The request is successful so ensure is_wp_error returns false
		\WP_Mock::wpFunction( 'is_wp_error', array(
			'args'   => array( $expected_result ),
			'times'  => 1,
			'return' => false,
		) );

		\WP_Mock::wpFunction( 'wp_remote_retrieve_response_code', array(
			'args'   => array( $expected_result ),
			'times'  => 1,
			'return' => '200',
		) );

		$this->assertEquals( 'success', $purge->get_result() );
	}

	public function test_failing_purge_request_for_all_items_when_not_allowed() {
		$purge         = new Purgely_Purge();
		$actual_result = $purge->purge( 'all' );

		$this->assertEquals( false, $actual_result );
	}

	public function tearDown() {
		\WP_Mock::tearDown();
	}
}