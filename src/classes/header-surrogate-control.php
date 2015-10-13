<?php

class Purgely_Surrogate_Control_Header extends Purgely_Header {
	/**
	 * The TTL for the resource.
	 *
	 * @var int    The TTL for the resource.
	 */
	private $_seconds = 0;

	/**
	 * Construct the object.
	 *
	 * @param  int                                 $seconds    The TTL for the object.
	 * @return Purgely_Surrogate_Control_Header
	 */
	public function __construct( $seconds ) {
		$this->set_seconds( $seconds );
		$this->set_header_name( 'Surrogate-Control' );
		$this->set_value( $this->prepare_value( $seconds ) );
	}

	/**
	 * Generate the full header value string.
	 *
	 * @param  int       $seconds    The number of seconds to cache the resource.
	 * @return string
	 */
	public function prepare_value( $seconds ) {
		return 'max-age=' . absint( $seconds );
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
	 * @param int    $seconds    The TTL for the object.
	 */
	public function set_seconds( $seconds ) {
		$this->_seconds = $seconds;
	}
}