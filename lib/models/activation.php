<?php

namespace QUADLAYERS\LicenseClient\Models;

use QUADLAYERS\LicenseClient\Models\Plugin as Model_Plugin;

/**
 * Model_Activation Class
 * This class handles fetched data of the activation in the database.
 *
 * @since 1.0.0
 */
class Activation extends Base {

	/**
	 * Plugin model
	 *
	 * @var Model_Plugin
	 */
	protected $plugin;

	/**
	 * Default attributes of the activation model.
	 *
	 * @var array
	 */
	protected $defaults = array(
		'message'              => null,
		'order_id'             => null,
		'license_key'          => null,
		'license_email'        => null,
		'license_limit'        => null,
		'license_updates'      => null,
		'license_support'      => null,
		'license_expiration'   => null,
		'license_created'      => null,
		'activation_limit'     => null,
		'activation_count'     => null,
		'activation_remaining' => null,
		'activation_instance'  => null,
		'activation_status'    => null,
		'activation_site'      => null,
		'activation_created'   => null,
	);

	/**
	 * Setup class
	 *
	 * @param Model_Plugin $plugin Model_Plugin instance.
	 */
	public function __construct( Model_Plugin $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Get database activation suffix
	 *
	 * @return string
	 */
	protected function get_db_suffix() {
		return 'activation';
	}

	/**
	 * Save activation data to the database.
	 *
	 * @param array $activation_data Activation data.
	 * @return array
	 */
	public function create( array $activation_data ) {
		$data = $this->save( $activation_data );
		return $data;
	}

	/**
	 * Update activation data in the database.
	 *
	 * @param array $activation_data Activation data.
	 * @return array
	 */
	public function update( array $activation_data ) {
		$data = $this->save( $activation_data );
		return $data;
	}
}
