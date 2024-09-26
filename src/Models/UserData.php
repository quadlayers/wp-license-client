<?php
namespace QuadLayers\WP_License_Client\Models;

/**
 * Model_User_Data Class
 * This class handles the input data of the user in the database.
 *
 * @since 1.0.0
 */
class UserData extends Base {

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
