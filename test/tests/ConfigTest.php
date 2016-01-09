<?php

class ConfigTest extends PHPUnit_Framework_TestCase {
	public function test_constants_are_set() {
		$this->assertTrue( defined( 'PURGELY_API_ENDPOINT' ) );
		$this->assertTrue( defined( 'PURGELY_FASTLY_KEY' ) );
		$this->assertTrue( defined( 'PURGELY_FASTLY_SERVICE_ID' ) );
		$this->assertTrue( defined( 'PURGELY_ALLOW_PURGE_ALL' ) );
		$this->assertTrue( defined( 'PURGELY_ENABLE_STALE_WHILE_REVALIDATE' ) );
		$this->assertTrue( defined( 'PURGELY_STALE_WHILE_REVALIDATE_TTL' ) );
		$this->assertTrue( defined( 'PURGELY_ENABLE_STALE_IF_ERROR' ) );
		$this->assertTrue( defined( 'PURGELY_STALE_IF_ERROR_TTL' ) );
		$this->assertTrue( defined( 'PURGELY_SURROGATE_CONTROL_TTL' ) );
		$this->assertTrue( defined( 'PURGELY_DEFAULT_PURGE_TYPE' ) );
	}
}