<?php

namespace QUADLAYERS\LicenseClient\Models;

use QUADLAYERS\LicenseClient\Models\Plugin as Model_Plugin;

/**
 * Model_Activation Class
 */

class Activation extends Base {

	protected $plugin;

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

	public function __construct( Model_Plugin $plugin ) {
		$this->plugin = $plugin;
	}

	protected function get_db_suffix() {
		return 'activation';
	}

	public function create( array $activation_data ) {
		$data = $this->save( $activation_data );
		// wp_clean_plugins_cache();
		return $data;
	}

	public function update( array $activation_data ) {
		$data = $this->save( $activation_data );
		// wp_clean_plugins_cache();
		return $data;
	}
}
