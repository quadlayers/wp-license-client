<?php

namespace QuadLayers\WP_License_Client\Models;

use QuadLayers\WP_License_Client\Models\Plugin as Model_Plugin;

/**
 * Abstract Base Class
 *
 * Implemented by classes using the same CRUD(s) pattern.
 *
 * @since  1.0.0
 */
abstract class Base {

	/**
	 * Plugin model
	 *
	 * @var Model_Plugin
	 */
	protected $plugin;

	/**
	 * Default attributes of the model.
	 *
	 * @var array
	 */
	protected $defaults = array();

	/**
	 * Setup class
	 *
	 * @param Model_Plugin $plugin Model_Plugin instance.
	 */
	public function __construct( Model_Plugin $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Get database model suffix.
	 *
	 * @return string
	 */
	abstract protected function get_db_suffix();

	/**
	 * Get model default attributes.
	 *
	 * @return array
	 */
	public function get_defaults() {
		return $this->defaults;
	}

	protected function get_valid_data_param( $param, $length = 1000 ) {

		if ( ! is_string( $param ) ) {
			return $param;
		}

		if ( strlen( $param ) > $length ) {
			$param = substr( $param, 0, $length );
		}
		return trim( $param );
	}

	protected function get_valid_data( array $array ) {
		$valid_data = array();
		foreach ( $this->get_defaults() as $key => $value ) {
			if ( array_key_exists( $key, $array ) ) {
				$valid_data[ $key ] = $this->get_valid_data_param( $array[ $key ] );
			} else {
				$valid_data[ $key ] = $value;
			}
		}
		return $valid_data;
	}

	protected function get_db_key() {

		if ( ! $this->plugin->is_valid() ) {
			return;
		}

		$plugin_slug = $this->plugin->get_slug();
		$db_suffix   = $this->get_db_suffix();

		return sanitize_key( "qlwlm_{$plugin_slug}_{$db_suffix}" );
	}

	abstract public function create( array $data );
	abstract public function update( array $data );

	public function get() {

		if ( ! $this->plugin->is_valid() ) {
			return $this->get_defaults();
		}

		$data = get_option( $this->get_db_key(), array() );

		if ( ! is_array( $data ) ) {
			return $this->get_defaults();
		}

		$valid_data = $this->get_valid_data( $data );

		return $valid_data;
	}

	public function delete() {

		if ( ! $this->plugin->is_valid() ) {
			return;
		}

		return delete_option( $this->get_db_key() );
	}

	public function save( array $data ) {

		if ( ! $this->plugin->is_valid() ) {
			return;
		}

		$valid_data = $this->get_valid_data( $data );

		$status = update_option( $this->get_db_key(), $valid_data );

		if ( $status ) {
			return $valid_data;
		}

		return false;
	}
}
