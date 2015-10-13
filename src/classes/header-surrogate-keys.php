<?php

class Purgely_Surrogate_Keys_Header extends Purgely_Header {
	/**
	 * The lists that will compose the Surrogate-Keys header value.
	 *
	 * @var array    List of Surrogate Keys.
	 */
	private $_keys = array();

	/**
	 * Construct the new object.
	 *
	 * @return Purgely_Surrogate_Keys_Header
	 */
	public function __construct() {
		$this->set_header_name( 'Surrogate-Key' );
	}

	/**
	 * Send the key by setting the header.
	 *
	 * @return void
	 */
	public function send_header() {
		$this->set_value( $this->prepare_keys( $this->get_keys() ) );
		parent::send_header();
	}

	/**
	 * Prepare the keys into a header value string.
	 *
	 * @param  array     $keys    The keys for the header.
	 * @return string             Space delimited list of sanitized keys.
	 */
	public function prepare_keys( $keys ) {
		$keys = array_map( array( $this, 'sanitize_key' ), $keys );
		return implode( ' ', $keys );
	}

	/**
	 * Sanitize a surrogate key.
	 *
	 * @param  string    $key    The unsanitized key.
	 * @return string            The sanitized key.
	 */
	public function sanitize_key( $key ) {
		return purgely_sanitize_surrogate_key( $key );
	}

	/**
	 * Add a key to the list.
	 *
	 * @param  string    $key    The key to add to the list.
	 * @return array             The full list of keys.
	 */
	public function add_key( $key ) {
		$keys   = $this->get_keys();
		$keys[] = $key;

		$this->set_keys( $keys );
		return $keys;
	}

	/**
	 * Add multiple keys to the list.
	 *
	 * @param  string    $keys    The keys to add to the list.
	 * @return array              The full list of keys.
	 */
	public function add_keys( $keys ) {
		$current_keys = $this->get_keys();
		$keys         = array_merge( $current_keys, $keys );

		$this->set_keys( $keys );
		return $keys;
	}

	/**
	 * Set the keys for the Surrogate Keys header.
	 *
	 * @param  array    $keys    The keys for the header.
	 * @return void
	 */
	public function set_keys( $keys ) {
		$this->_keys = $keys;
	}

	/**
	 * Key the list of Surrogate Keys.
	 *
	 * @return array    The list of Surrogate Keys.
	 */
	public function get_keys() {
		return $this->_keys;
	}
}