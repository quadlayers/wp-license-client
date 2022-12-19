<?php

namespace QuadLayers\WP_License_Client\Api\Fetch\Product;

use QuadLayers\WP_License_Client\Api\Fetch\Base;

/**
 * API_Fetch_Product_Download Class
 *
 * @since 1.0.0
 */
class Download extends Base {

	/**
	 * Get rest route path
	 *
	 * @return string
	 */
	public function get_rest_path() {
		return 'product/download';
	}

	/**
	 * Get rest method
	 *
	 * @return string GET
	 */
	public static function get_rest_method() {
		return 'GET';
	}

}
