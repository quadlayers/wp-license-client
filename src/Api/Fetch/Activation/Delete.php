<?php

namespace QuadLayers\WP_License_Client\Api\Fetch\Activation;

use QuadLayers\WP_License_Client\Api\Fetch\Base;

/**
 * API_Fetch_Activation_Delete Class
 *
 * @since 1.0.0
 */
class Delete extends Base {

	/**
	 * Get rest route path
	 *
	 * @return string
	 */
	public function get_rest_path() {
		return 'activation';
	}

	/**
	 * Get rest method
	 *
	 * @return string POST
	 */
	public static function get_rest_method() {
		return 'DELETE';
	}
}
