<?php
namespace QUADLAYERS\LicenseClient\Models;

use QUADLAYERS\LicenseClient\Models\Plugin as Model_Plugin;

/**
 * Model_User_Data Class
 */

class UserData extends Base {

	protected $plugin;

	protected $defaults = array(
		'license_key'    => null,
		'license_email'  => null,
		'license_market' => null,
	);

	public function __construct( Model_Plugin $plugin ) {
		$this->plugin = $plugin;
	}

	protected function get_db_suffix() {
		return 'user_data';
	}

	public function create( array $activation_data ) {
		$data = $this->save( $activation_data );
		return $data;
	}

	public function update( array $activation_data ) {
		$data = $this->save( $activation_data );
		return $data;
	}

}
