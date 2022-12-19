<?php
namespace QuadLayers\WP_License_Client\Models;

use QuadLayers\WP_License_Client\Models\Plugin as Model_Plugin;

/**
 * Model_User_Data Class
 * This class handles the input data of the user in the database.
 *
 * @since 1.0.0
 */
class UserData extends Base {

	/**
	 * Plugin model
	 *
	 * @var Model_Plugin
	 */
	protected $plugin;

	/**
	 * Default attributes of the user data model.
	 *
	 * @var array
	 */
	protected $defaults = array(
		'license_key'    => null,
		'license_email'  => null,
		'license_market' => null,
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
	 * Get database user data suffix.
	 *
	 * @return string
	 */
	protected function get_db_suffix() {
		return 'user_data';
	}

	/**
	 * Save activation data to the database.
	 *
	 * @param array $user_data Activation data.
	 * @return array
	 */
	public function create( array $user_data ) {
		$data = $this->save( $user_data );
		return $data;
	}

	/**
	 * Update activation data in the database.
	 *
	 * @param array $user_data Activation data.
	 * @return array
	 */
	public function update( array $user_data ) {
		$data = $this->save( $user_data );
		return $data;
	}

}
