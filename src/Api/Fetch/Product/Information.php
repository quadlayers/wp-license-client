<?php

namespace QuadLayers\WP_License_Client\Api\Fetch\Product;

use QuadLayers\WP_License_Client\Api\Fetch\Base;

/**
 * API_Fetch_Product_Information Class
 *
 * @since 1.0.0
 */
class Information extends Base {

	/**
	 * Get rest route path
	 *
	 * @return string
	 */
	public function get_rest_path() {
		return 'product/information';
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
