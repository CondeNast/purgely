<?php

abstract class Purgely_Header {
	/**
	 * The header name that will be set.
	 *
	 * @var string    The header name.
	 */
	protected $_header_name = '';

	/**
	 * The surrogate key value.
	 *
	 * @var string    The surrogate key that will be set.
	 */
	protected $_value = '';

	/**
	 * Send the key by setting the header.
	 *
	 * @return void
	 */
	public function send_header() {
		header( $this->_header_name . ': ' . $this->get_value(), false );
	}

	/**
	 * Set the header name.
	 *
	 * @param  string $header_name    The header name.
	 * @return void
	 */
	public function set_header_name( $header_name ) {
		$this->_header_name = $header_name;
	}

	/**
	 * Return the header name.
	 *
	 * @return string    The header name.
	 */
	public function get_header_name() {
		return $this->_header_name;
	}

	/**
	 * Set the header value.
	 *
	 * @param  string $value    The header value.
	 * @return void
	 */
	public function set_value( $value ) {
		$this->_value = $value;
	}

	/**
	 * Return the value of the header.
	 *
	 * @return string    The header value.
	 */
	public function get_value() {
		return $this->_value;
	}
}
