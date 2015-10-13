<?php

class Purgely_Cache_Control_Header extends Purgely_Header {
	/**
	 * The TTL for the resource.
	 *
	 * @var int    The TTL for the resource.
	 */
	private $_seconds = 0;

	/**
	 * The directive to set.
	 *
	 * @var int    The directive to set.
	 */
	private $_directive = '';

	/**
	 * Construct the object.
	 *
	 * @param  int    $seconds      The TTL for the object.
	 * @param  string $directive    The cache control directive to set.
	 * @return Purgely_Cache_Control_Header
	 */
	public function __construct( $seconds, $directive ) {
		$this->set_seconds( $seconds );
		$this->set_directive( $directive );
		$this->set_header_name( 'Cache-Control' );
		$this->set_value( $this->prepare_value( $seconds, $directive ) );
	}

	/**
	 * Generate the full header value string.
	 *
	 * @param  int    $seconds      The number of seconds to cache the resource.
	 * @param  string $directive    The cache control directive to set.
	 * @return string
	 */
	public function prepare_value( $seconds, $directive ) {
		return sanitize_key( $directive ) . '=' . absint( $seconds );
	}

	/**
	 * Get the TTL for an object.
	 *
	 * @return int    The TTL in seconds.
	 */
	public function get_seconds() {
		return $this->_seconds;
	}

	/**
	 * Set the TTL for the object.
	 *
	 * @param  int $seconds    The TTL for the object.
	 * @return void
	 */
	public function set_seconds( $seconds ) {
		$this->_seconds = $seconds;
	}

	/**
	 * Get the directive.
	 *
	 * @return int    The directive.
	 */
	public function get_directive() {
		return $this->_directive;
	}

	/**
	 * Set the directive.
	 *
	 * @param  int $directive    The directive.
	 * @return void
	 */
	public function set_directive( $directive ) {
		$this->_directive = $directive;
	}
}
