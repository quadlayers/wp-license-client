<?php

namespace QuadLayers\WP_License_Client\Models;

use QuadLayers\WP_License_Client\Traits\ActivationStatus;

/**
 * Model_Activation Class
 * This class handles fetched data of the activation in the database.
 *
 * @since 1.0.0
 */
class Activation extends Base {

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
	 * Activation status trait.
	 */
	use ActivationStatus;

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
